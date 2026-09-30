<?php
/**
 * Licence client: activation, signed verification, tamper-proof local state, daily re-check.
 *
 * Rules:
 * - A definitive, correctly signed answer from the server ("active", "expired", "revoked", ...) is applied at once.
 * - A network problem, HTTP error or bad signature never locks the site by itself: the last good state keeps working for
 *   MNATA_LICENSE_GRACE_DAYS (+1 day), then the theme reports "unverified".
 * - Local development hosts (.test, .local, localhost, ...) are never locked, so developers are not blocked.
 *
 * @package M_Nata
 */

defined( 'ABSPATH' ) || exit;

/**
 * Normalised host of this site (lower case, without "www.").
 *
 * @return string
 */
function mnata_license_host() {
	$host = strtolower( (string) wp_parse_url( home_url(), PHP_URL_HOST ) );
	return preg_replace( '/^www\./', '', $host );
}

/**
 * Whether this is a local development host that is exempt from the licence check.
 *
 * @return bool
 */
function mnata_license_is_dev_host() {
	if ( defined( 'MNATA_LICENSE_FORCE' ) && MNATA_LICENSE_FORCE ) {
		return false;
	}
	$host = mnata_license_host();
	if ( 'localhost' === $host || preg_match( '/^(127\.\d+\.\d+\.\d+|\[::1\])$/', $host ) ) {
		return true;
	}
	$suffixes = (array) apply_filters( 'mnata_license_dev_suffixes', array( '.test', '.local', '.localhost', '.invalid', '.example' ) );
	foreach ( $suffixes as $suffix ) {
		if ( substr( $host, -strlen( $suffix ) ) === $suffix ) {
			return true;
		}
	}
	return false;
}

/**
 * MAC over a state array (keeps the stored state tamper-proof without a network call on every request).
 *
 * @param array $state State without the "mac" key.
 * @return string
 */
function mnata_license_mac( $state ) {
	ksort( $state );
	return hash_hmac( 'sha256', wp_json_encode( $state ), wp_salt( 'auth' ) . '|mnata-license' );
}

/**
 * Stored state, or null when missing / modified by hand.
 *
 * @param bool $refresh Re-read from the database (after a save).
 * @return array|null
 */
function mnata_license_state( $refresh = false ) {
	static $cache = false;
	if ( false !== $cache && ! $refresh ) {
		return $cache;
	}
	$raw   = get_option( 'mnata_license', array() );
	$cache = null;
	if ( is_array( $raw ) && isset( $raw['mac'] ) ) {
		$mac = $raw['mac'];
		unset( $raw['mac'] );
		if ( hash_equals( mnata_license_mac( $raw ), (string) $mac ) ) {
			$cache = $raw;
		}
	}
	return $cache;
}

/**
 * Save state (adds the MAC).
 *
 * @param array $state State.
 */
function mnata_license_save_state( $state ) {
	unset( $state['mac'] );
	$state['mac'] = mnata_license_mac( $state );
	update_option( 'mnata_license', $state, false );
	mnata_license_state( true );
}

/**
 * Current licence status code: dev | active | none | expired | revoked | suspended | invalid | domain_mismatch | unverified.
 *
 * @return string
 */
function mnata_license_status() {
	if ( mnata_license_is_dev_host() ) {
		return 'dev';
	}
	$state = mnata_license_state();
	if ( ! $state || empty( $state['status'] ) ) {
		return 'none';
	}
	if ( 'active' !== $state['status'] ) {
		return (string) $state['status'];
	}
	if ( ! empty( $state['expires_at'] ) && time() > (int) $state['expires_at'] ) {
		return 'expired';
	}
	$grace = ( ! empty( $state['grace_days'] ) ? (int) $state['grace_days'] : (int) MNATA_LICENSE_GRACE_DAYS ) * DAY_IN_SECONDS + DAY_IN_SECONDS;
	if ( time() - (int) $state['last_ok'] > $grace ) {
		return 'unverified';
	}
	return 'active';
}

/**
 * Whether the theme may run.
 *
 * @return bool
 */
function mnata_license_ok() {
	$status = mnata_license_status();
	return 'active' === $status || 'dev' === $status;
}

/**
 * Talk to the licence server.
 *
 * @param string $endpoint activate | verify (the theme never calls deactivate; a licence is locked to its first domain — see wp-be/permintaan-ke-dev-theme.md §5).
 * @param string $key      Licence key.
 * @param string $nonce    Random value the server must echo inside the signed payload.
 * @return array{ok:bool,http:int,body:?array,error:string}
 */
function mnata_license_request( $endpoint, $key, $nonce ) {
	global $wp_version;

	$base = untrailingslashit( MNATA_LICENSE_API );
	// Plain http is only accepted for local development servers.
	if ( 0 !== strpos( $base, 'https://' ) && ! mnata_license_is_dev_host() && ! ( defined( 'MNATA_LICENSE_FORCE' ) && MNATA_LICENSE_FORCE ) ) {
		return array(
			'ok'    => false,
			'http'  => 0,
			'body'  => null,
			'error' => 'insecure_api',
		);
	}

	$res = wp_remote_post(
		$base . '/' . ( false === strpos( $endpoint, '/' ) ? 'licenses/' : '' ) . $endpoint,
		array(
			'timeout'    => 10,
			'headers'    => array(
				'Accept'       => 'application/json',
				'Content-Type' => 'application/json',
			),
			'user-agent' => 'M-Nata/' . MNATA_VERSION . '; ' . home_url( '/' ),
			'body'       => wp_json_encode(
				array(
					'license_key' => $key,
					'product'     => MNATA_LICENSE_PRODUCT,
					'domain'      => mnata_license_host(),
					'site_url'    => home_url( '/' ),
					'version'     => MNATA_VERSION,
					'wp_version'  => $wp_version,
					'php_version' => PHP_VERSION,
					'locale'      => get_locale(),
					'nonce'       => $nonce,
				)
			),
		)
	);

	if ( is_wp_error( $res ) ) {
		return array(
			'ok'    => false,
			'http'  => 0,
			'body'  => null,
			'error' => $res->get_error_message(),
		);
	}
	$body = json_decode( wp_remote_retrieve_body( $res ), true );
	return array(
		'ok'    => true,
		'http'  => (int) wp_remote_retrieve_response_code( $res ),
		'body'  => is_array( $body ) ? $body : null,
		'error' => '',
	);
}

/**
 * Check the signature and the contents of a server payload.
 *
 * @param array  $body  Decoded response body.
 * @param string $nonce Nonce that was sent.
 * @return array|WP_Error Payload array when authentic.
 */
function mnata_license_read_payload( $body, $nonce ) {
	if ( empty( $body['data']['payload'] ) || empty( $body['data']['signature'] ) ) {
		return new WP_Error( 'no_payload' );
	}
	$raw = base64_decode( (string) $body['data']['payload'], true ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode -- decoding a signed licence payload, not code.
	$sig = base64_decode( (string) $body['data']['signature'], true ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode -- decoding a signed licence payload, not code.
	$pub = base64_decode( MNATA_LICENSE_PUBKEY, true ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode -- decoding a signed licence payload, not code.
	if ( false === $raw || false === $sig || false === $pub || ! function_exists( 'sodium_crypto_sign_verify_detached' ) ) {
		return new WP_Error( 'bad_encoding' );
	}
	try {
		$valid = sodium_crypto_sign_verify_detached( $sig, $raw, $pub );
	} catch ( Exception $e ) {
		$valid = false;
	}
	if ( ! $valid ) {
		return new WP_Error( 'bad_signature' );
	}

	$p = json_decode( $raw, true );
	if ( ! is_array( $p )
		|| ( isset( $p['product'] ) ? $p['product'] : '' ) !== MNATA_LICENSE_PRODUCT
		|| ( isset( $p['domain'] ) ? $p['domain'] : '' ) !== mnata_license_host()
		|| ( isset( $p['nonce'] ) ? $p['nonce'] : '' ) !== $nonce
		|| empty( $p['status'] )
		|| abs( time() - (int) strtotime( isset( $p['issued_at'] ) ? $p['issued_at'] : '' ) ) > DAY_IN_SECONDS
	) {
		return new WP_Error( 'bad_payload' );
	}
	return $p;
}

/**
 * Apply an authentic payload to the stored state.
 *
 * @param array  $payload Payload.
 * @param string $key     Licence key.
 * @return string Status.
 */
function mnata_license_apply( $payload, $key ) {
	$now   = time();
	$state = array(
		'key'          => $key,
		'status'       => sanitize_key( $payload['status'] ),
		'plan'         => isset( $payload['plan'] ) ? sanitize_text_field( $payload['plan'] ) : '',
		'expires_at'   => ! empty( $payload['expires_at'] ) ? (int) strtotime( $payload['expires_at'] ) : 0,
		'grace_days'   => isset( $payload['grace_days'] ) ? max( 1, min( 60, (int) $payload['grace_days'] ) ) : (int) MNATA_LICENSE_GRACE_DAYS,
		'message'      => isset( $payload['message'] ) ? sanitize_text_field( $payload['message'] ) : '',
		'domain'       => mnata_license_host(),
		'last_check'   => $now,
		'last_ok'      => 'active' === $payload['status'] ? $now : ( ( mnata_license_state()['last_ok'] ?? 0 ) ),
		'last_error'   => '',
		'activated_at' => ( mnata_license_state()['activated_at'] ?? $now ),
	);
	mnata_license_save_state( $state );
	return $state['status'];
}

/**
 * Activate a licence key for this domain.
 *
 * @param string $key Licence key.
 * @return string|WP_Error Status or an error with a user-facing message.
 */
function mnata_license_activate( $key ) {
	$key = strtoupper( trim( preg_replace( '/\s+/', '', (string) $key ) ) );
	if ( strlen( $key ) < 8 || strlen( $key ) > 80 || ! preg_match( '/^[A-Z0-9\-]+$/', $key ) ) {
		return new WP_Error( 'format', __( 'Format kunci lisensi tidak valid.', 'm-nata' ) );
	}

	$nonce = bin2hex( random_bytes( 12 ) );
	$res   = mnata_license_request( 'activate', $key, $nonce );

	if ( ! $res['ok'] ) {
		return new WP_Error( 'network', __( 'Server lisensi tidak dapat dihubungi. Periksa koneksi internet server Anda lalu coba lagi.', 'm-nata' ) );
	}
	if ( 200 !== $res['http'] ) {
		$message = isset( $res['body']['message'] ) ? sanitize_text_field( $res['body']['message'] ) : __( 'Aktivasi ditolak oleh server lisensi.', 'm-nata' );
		return new WP_Error( 'rejected', $message );
	}
	$payload = mnata_license_read_payload( $res['body'], $nonce );
	if ( is_wp_error( $payload ) ) {
		return new WP_Error( 'signature', __( 'Jawaban server lisensi tidak dapat diverifikasi (tanda tangan tidak cocok).', 'm-nata' ) );
	}
	$status = mnata_license_apply( $payload, $key );
	if ( 'active' !== $status ) {
		return new WP_Error( 'inactive', mnata_license_status_label( $status ) );
	}
	delete_transient( 'mnata_license_lock' );
	mnata_update_reset();
	return $status;
}

/**
 * Re-verify the stored key (cron / "Periksa ulang").
 *
 * @return string Status after the check.
 */
function mnata_license_check() {
	$state = mnata_license_state();
	if ( ! $state || empty( $state['key'] ) ) {
		return mnata_license_status();
	}

	$nonce = bin2hex( random_bytes( 12 ) );
	$res   = mnata_license_request( 'verify', $state['key'], $nonce );

	if ( $res['ok'] && 200 === $res['http'] ) {
		$payload = mnata_license_read_payload( $res['body'], $nonce );
		if ( ! is_wp_error( $payload ) ) {
			return mnata_license_apply( $payload, $state['key'] );
		}
		$error = $payload->get_error_code();
	} else {
		$error = $res['ok'] ? 'http_' . $res['http'] : $res['error'];
	}

	// Unreachable / unauthentic answer: keep the last good state (grace period), only remember the attempt.
	$state['last_check'] = time();
	$state['last_error'] = sanitize_text_field( substr( (string) $error, 0, 120 ) );
	mnata_license_save_state( $state );
	return mnata_license_status();
}

/**
 * Human readable label for a status code.
 *
 * @param string $status Status code.
 * @return string
 */
function mnata_license_status_label( $status ) {
	$labels = array(
		'dev'             => __( 'Mode pengembangan (host lokal) — tidak perlu lisensi.', 'm-nata' ),
		'active'          => __( 'Aktif', 'm-nata' ),
		'none'            => __( 'Belum diaktifkan.', 'm-nata' ),
		'expired'         => __( 'Lisensi sudah kedaluwarsa.', 'm-nata' ),
		'revoked'         => __( 'Lisensi dicabut.', 'm-nata' ),
		'suspended'       => __( 'Lisensi dibekukan.', 'm-nata' ),
		'invalid'         => __( 'Kunci lisensi tidak valid.', 'm-nata' ),
		'domain_mismatch' => __( 'Lisensi ini terdaftar untuk domain lain.', 'm-nata' ),
		'unverified'      => __( 'Lisensi tidak dapat diverifikasi ulang (server lisensi tidak terjangkau melebihi masa toleransi).', 'm-nata' ),
	);
	return isset( $labels[ $status ] ) ? $labels[ $status ] : $labels['invalid'];
}

/**
 * Daily re-check.
 */
function mnata_license_schedule() {
	if ( ! wp_next_scheduled( 'mnata_license_cron' ) ) {
		wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'mnata_license_cron' );
	}
}
add_action( 'init', 'mnata_license_schedule' );
add_action( 'mnata_license_cron', 'mnata_license_check' );
add_action(
	'switch_theme',
	static function () {
		wp_clear_scheduled_hook( 'mnata_license_cron' );
	}
);
