<?php
/**
 * MOCK licence server for testing the M-Nata theme (NOT for production).
 *
 * It implements the same contract as the real backend (see ../permintaan-ke-backend.md) so the theme can be tested without Laravel:
 *   php -S 127.0.0.1:8090 wp-be/mock/server.php
 *   (then in wp-config.php:  define('MNATA_LICENSE_FORCE', true); define('MNATA_LICENSE_API', 'http://127.0.0.1:8090/v1');)
 *
 * Test keys:
 *   MNATA-TEST-ACTIVE-0001    pro, 1 year, 1 domain
 *   MNATA-TEST-LIFETIME-0002  lifetime, 3 domains
 *   MNATA-TEST-EXPIRED-0003   expired
 *   MNATA-TEST-REVOKED-0004   revoked
 *   MNATA-TEST-LIMIT-0005     already used by another domain (limit reached)
 *
 * Test controls:  POST /_test/status  key=...&status=revoked|active|...   change a licence status (simulates revocation)
 *                 POST /_test/mode    mode=ok|down|badsig                 outage / wrong-signature simulation
 *                 POST /_test/reset                                       forget all activations
 *                 POST /_test/release version=1.0.1&file=/abs/path/theme.zip[&tamper=1]   publish a theme release (tamper = wrong checksum)
 *
 * @package M_Nata
 */

$dir   = __DIR__;
$keys  = json_decode( (string) file_get_contents( $dir . '/dev-keys.json' ), true );
$file  = $dir . '/state.json';
$state = is_file( $file ) ? json_decode( (string) file_get_contents( $file ), true ) : array();
$state = is_array( $state ) ? $state : array();

$state += array(
	'mode'     => 'ok',
	'licenses' => array(
		'MNATA-TEST-ACTIVE-0001'   => array( 'plan' => 'pro', 'status' => 'active', 'expires' => '+1 year', 'max' => 1, 'domains' => array() ),
		'MNATA-TEST-LIFETIME-0002' => array( 'plan' => 'lifetime', 'status' => 'active', 'expires' => null, 'max' => 3, 'domains' => array() ),
		'MNATA-TEST-EXPIRED-0003'  => array( 'plan' => 'pro', 'status' => 'expired', 'expires' => '-1 day', 'max' => 1, 'domains' => array() ),
		'MNATA-TEST-REVOKED-0004'  => array( 'plan' => 'pro', 'status' => 'revoked', 'expires' => '+1 year', 'max' => 1, 'domains' => array() ),
		'MNATA-TEST-LIMIT-0005'    => array( 'plan' => 'pro', 'status' => 'active', 'expires' => '+1 year', 'max' => 1, 'domains' => array( 'other-site.com' ) ),
	),
);

/**
 * Persist state.
 */
function mock_save( $file, $state ) {
	file_put_contents( $file, json_encode( $state, JSON_PRETTY_PRINT ) );
}

/**
 * JSON response.
 */
function mock_json( $code, $data ) {
	http_response_code( $code );
	header( 'Content-Type: application/json' );
	echo json_encode( $data );
	exit;
}

/**
 * Signed payload envelope.
 */
function mock_signed( $keys, $state, $payload ) {
	$raw = json_encode( $payload, JSON_UNESCAPED_SLASHES );
	$sec = base64_decode( $keys['secret'] );
	if ( 'badsig' === $state['mode'] ) {
		$sec = sodium_crypto_sign_secretkey( sodium_crypto_sign_keypair() ); // A different key: the theme must reject this.
	}
	$sig = sodium_crypto_sign_detached( $raw, $sec );
	return array(
		'success' => true,
		'data'    => array(
			'payload'   => base64_encode( $raw ),
			'signature' => base64_encode( $sig ),
		),
	);
}

$path = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
$body = json_decode( (string) file_get_contents( 'php://input' ), true );
$body = is_array( $body ) ? $body : $_POST;

// ---- test controls
if ( 0 === strpos( $path, '/_test/' ) ) {
	if ( '/_test/mode' === $path ) {
		$state['mode'] = $_POST['mode'] ?? 'ok';
	} elseif ( '/_test/status' === $path && isset( $state['licenses'][ $_POST['key'] ?? '' ] ) ) {
		$state['licenses'][ $_POST['key'] ]['status'] = $_POST['status'] ?? 'active';
	} elseif ( '/_test/release' === $path ) {
		$state['release'] = array(
			'version' => $_POST['version'] ?? '',
			'file'    => $_POST['file'] ?? '',
			'tamper'  => ! empty( $_POST['tamper'] ),
		);
	} elseif ( '/_test/reset' === $path ) {
		foreach ( $state['licenses'] as $k => $l ) {
			$state['licenses'][ $k ]['domains'] = ( 'MNATA-TEST-LIMIT-0005' === $k ) ? array( 'other-site.com' ) : array();
		}
		$state['mode'] = 'ok';
	}
	mock_save( $file, $state );
	mock_json( 200, array( 'ok' => true, 'mode' => $state['mode'] ) );
}

if ( 'down' === $state['mode'] ) {
	mock_json( 503, array( 'success' => false, 'message' => 'Service unavailable' ) );
}

// Package download (a real backend would use a short-lived signed URL).
if ( '/v1/downloads/m-nata.zip' === $path && 'GET' === $_SERVER['REQUEST_METHOD'] ) {
	$rel = $state['release'] ?? array();
	if ( empty( $rel['file'] ) || ! is_file( $rel['file'] ) ) {
		mock_json( 404, array( 'success' => false, 'code' => 'not_found', 'message' => 'No release' ) );
	}
	header( 'Content-Type: application/zip' );
	header( 'Content-Length: ' . filesize( $rel['file'] ) );
	readfile( $rel['file'] );
	exit;
}

if ( ! preg_match( '#^/v1/(?:licenses/(activate|verify|deactivate)|products/m-nata/(update))$#', $path, $m ) || 'POST' !== $_SERVER['REQUEST_METHOD'] ) {
	mock_json( 404, array( 'success' => false, 'code' => 'not_found', 'message' => 'Not found' ) );
}
$action = $m[1] ?: $m[2];

$key    = strtoupper( trim( (string) ( $body['license_key'] ?? '' ) ) );
$domain = strtolower( (string) ( $body['domain'] ?? '' ) );
$nonce  = (string) ( $body['nonce'] ?? '' );
$prod   = (string) ( $body['product'] ?? '' );

if ( 'm-nata' !== $prod ) {
	mock_json( 422, array( 'success' => false, 'code' => 'product_mismatch', 'message' => 'Lisensi ini bukan untuk produk M-Nata.' ) );
}
$lic = $state['licenses'][ $key ] ?? null;

$make = static function ( $status, $lic, $message = '' ) use ( $domain, $nonce, $key ) {
	return array(
		'license'    => substr( $key, 0, 6 ) . '****' . substr( $key, -4 ),
		'product'    => 'm-nata',
		'domain'     => $domain,
		'status'     => $status,
		'plan'       => $lic['plan'] ?? '',
		'expires_at' => ( $lic && $lic['expires'] ) ? gmdate( 'c', strtotime( $lic['expires'] ) ) : null,
		'issued_at'  => gmdate( 'c' ),
		'grace_days' => 7,
		'message'    => $message,
		'nonce'      => $nonce,
	);
};

if ( 'deactivate' === $action ) {
	if ( $lic ) {
		$state['licenses'][ $key ]['domains'] = array_values( array_diff( $lic['domains'], array( $domain ) ) );
		mock_save( $file, $state );
	}
	mock_json( 200, array( 'success' => true ) );
}

if ( 'update' === $action ) {
	$status = $lic ? $lic['status'] : 'invalid';
	if ( $lic && 'active' === $status && ! in_array( $domain, $lic['domains'], true ) ) {
		$status = 'domain_mismatch';
	}
	$payload = $make( $status, $lic );
	$rel     = $state['release'] ?? array();
	if ( 'active' === $status && ! empty( $rel['version'] ) && is_file( $rel['file'] ) ) {
		$host              = $_SERVER['HTTP_HOST'] ?? '127.0.0.1:8090';
		$payload['update'] = array(
			'version'       => $rel['version'],
			'package'       => 'http://' . $host . '/v1/downloads/m-nata.zip?token=' . bin2hex( random_bytes( 8 ) ),
			'sha256'        => ! empty( $rel['tamper'] ) ? str_repeat( '0', 64 ) : hash_file( 'sha256', $rel['file'] ),
			'requires'      => '6.3',
			'requires_php'  => '7.4',
			'tested'        => '7.1',
			'released_at'   => gmdate( 'c' ),
			'changelog_url' => 'https://m-onetech.id/m-nata/changelog',
		);
	} else {
		$payload['update'] = null;
	}
	mock_json( 200, mock_signed( $keys, $state, $payload ) );
}

if ( 'verify' === $action ) {
	if ( ! $lic ) {
		mock_json( 200, mock_signed( $keys, $state, $make( 'invalid', null ) ) );
	}
	$status = $lic['status'];
	if ( 'active' === $status && ! in_array( $domain, $lic['domains'], true ) ) {
		$status = 'domain_mismatch';
	}
	mock_json( 200, mock_signed( $keys, $state, $make( $status, $lic ) ) );
}

// ---- activate
if ( ! $lic ) {
	mock_json( 404, array( 'success' => false, 'code' => 'invalid_key', 'message' => 'Kunci lisensi tidak ditemukan.' ) );
}
if ( 'active' !== $lic['status'] ) {
	mock_json( 403, array( 'success' => false, 'code' => $lic['status'], 'message' => 'Lisensi ini ' . ( 'expired' === $lic['status'] ? 'sudah kedaluwarsa.' : 'tidak dapat digunakan (' . $lic['status'] . ').' ) ) );
}
if ( ! in_array( $domain, $lic['domains'], true ) ) {
	if ( count( $lic['domains'] ) >= $lic['max'] ) {
		mock_json( 409, array( 'success' => false, 'code' => 'limit_reached', 'message' => 'Batas domain untuk lisensi ini sudah tercapai. Nonaktifkan salah satu domain lebih dulu.' ) );
	}
	$state['licenses'][ $key ]['domains'][] = $domain;
	mock_save( $file, $state );
}
mock_json( 200, mock_signed( $keys, $state, $make( 'active', $lic ) ) );
