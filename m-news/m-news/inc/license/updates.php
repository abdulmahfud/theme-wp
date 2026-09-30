<?php
/**
 * Theme updates from the licence server (only for an active licence).
 *
 * Flow: WordPress' normal update check -> we ask POST {api}/products/m-news/update -> the answer is Ed25519-signed like every
 * licence answer -> the theme shows "update available" in Appearance > Themes -> the download is verified against the
 * signed SHA-256 before WordPress installs it. Nothing here runs without an activated licence.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

/**
 * Hosts a package may be downloaded from (the API host and its own sub-domains).
 *
 * @return string[]
 */
function mnews_update_hosts() {
	$api   = (string) wp_parse_url( MNEWS_LICENSE_API, PHP_URL_HOST );
	$hosts = array( $api );
	$parts = explode( '.', $api );
	if ( count( $parts ) >= 2 ) {
		$hosts[] = implode( '.', array_slice( $parts, -2 ) ); // Registrable domain, e.g. m-onetech.id.
	}
	return (array) apply_filters( 'mnews_update_hosts', array_unique( $hosts ) );
}

/**
 * Whether a package URL is acceptable (https, allowed host).
 *
 * @param string $url URL.
 * @return bool
 */
function mnews_update_package_ok( $url ) {
	$parts = wp_parse_url( $url );
	if ( empty( $parts['host'] ) || empty( $parts['scheme'] ) ) {
		return false;
	}
	$https_ok = 'https' === $parts['scheme'] || ( 'http' === $parts['scheme'] && mnews_license_is_dev_host() ) || ( defined( 'MNEWS_LICENSE_FORCE' ) && MNEWS_LICENSE_FORCE );
	if ( ! $https_ok ) {
		return false;
	}
	$host = strtolower( $parts['host'] );
	foreach ( mnews_update_hosts() as $allowed ) {
		if ( $host === $allowed || substr( $host, -strlen( '.' . $allowed ) ) === '.' . $allowed ) {
			return true;
		}
	}
	// Local test servers (127.0.0.1, localhost) when enforcement testing is on.
	return defined( 'MNEWS_LICENSE_FORCE' ) && MNEWS_LICENSE_FORCE && in_array( $host, array( '127.0.0.1', 'localhost' ), true );
}

/**
 * Update information for this site: array (version, package, sha256, ...) or an empty array when up to date / not licensed.
 * Cached for 12 hours.
 *
 * @param bool $force Ignore the cache.
 * @return array
 */
function mnews_update_info( $force = false ) {
	$state = mnews_license_state();
	if ( ! $state || empty( $state['key'] ) || 'active' !== mnews_license_status() ) {
		return array();
	}
	if ( ! $force ) {
		$cached = get_transient( 'mnews_update_info' );
		if ( is_array( $cached ) ) {
			return $cached;
		}
	}

	$nonce = bin2hex( random_bytes( 12 ) );
	$res   = mnews_license_request( 'products/' . MNEWS_LICENSE_PRODUCT . '/update', $state['key'], $nonce );
	$info  = array();

	if ( $res['ok'] && 200 === $res['http'] ) {
		$payload = mnews_license_read_payload( $res['body'], $nonce );
		if ( ! is_wp_error( $payload ) && 'active' === $payload['status'] && ! empty( $payload['update'] ) && is_array( $payload['update'] ) ) {
			$u = $payload['update'];
			if ( isset( $u['version'], $u['package'], $u['sha256'] )
				&& preg_match( '/^\d+\.\d+\.\d+([\-+][A-Za-z0-9.\-]+)?$/', $u['version'] )
				&& preg_match( '/^[a-f0-9]{64}$/', $u['sha256'] )
				&& version_compare( $u['version'], MNEWS_VERSION, '>' )
				&& mnews_update_package_ok( $u['package'] )
			) {
				$info = array(
					'version'       => $u['version'],
					'package'       => esc_url_raw( $u['package'] ),
					'sha256'        => $u['sha256'],
					'requires'      => isset( $u['requires'] ) ? sanitize_text_field( $u['requires'] ) : '',
					'requires_php'  => isset( $u['requires_php'] ) ? sanitize_text_field( $u['requires_php'] ) : '',
					'tested'        => isset( $u['tested'] ) ? sanitize_text_field( $u['tested'] ) : '',
					'changelog_url' => isset( $u['changelog_url'] ) ? esc_url_raw( $u['changelog_url'] ) : '',
					'released_at'   => isset( $u['released_at'] ) ? sanitize_text_field( $u['released_at'] ) : '',
				);
			}
		}
	}

	// Failed or empty answers are cached briefly so a down server is not hit on every admin page.
	set_transient( 'mnews_update_info', $info, $info || ( $res['ok'] && 200 === $res['http'] ) ? 12 * HOUR_IN_SECONDS : HOUR_IN_SECONDS );
	return $info;
}

/**
 * Add our update to WordPress' theme update transient.
 *
 * @param mixed $transient The update_themes site transient.
 * @return mixed
 */
function mnews_update_inject( $transient ) {
	if ( ! is_object( $transient ) ) {
		return $transient;
	}
	$slug = get_template();
	$info = mnews_update_info();
	if ( $info ) {
		$transient->response[ $slug ] = array(
			'theme'        => $slug,
			'new_version'  => $info['version'],
			'url'          => $info['changelog_url'],
			'package'      => $info['package'],
			'requires'     => $info['requires'],
			'requires_php' => $info['requires_php'],
		);
		unset( $transient->no_update[ $slug ] );
	} elseif ( isset( $transient->response[ $slug ] ) ) {
		unset( $transient->response[ $slug ] );
	}
	return $transient;
}
add_filter( 'pre_set_site_transient_update_themes', 'mnews_update_inject' );

/**
 * Never let WordPress.org answer for this (private) theme.
 *
 * @param array  $args HTTP args.
 * @param string $url  URL.
 * @return array
 */
function mnews_update_hide_from_wporg( $args, $url ) {
	if ( 0 !== strpos( $url, 'https://api.wordpress.org/themes/update-check/' ) || empty( $args['body']['themes'] ) ) {
		return $args;
	}
	$themes = json_decode( $args['body']['themes'], true );
	if ( is_array( $themes ) && isset( $themes['themes'][ get_template() ] ) ) {
		unset( $themes['themes'][ get_template() ] );
		$args['body']['themes'] = wp_json_encode( $themes );
	}
	return $args;
}
add_filter( 'http_request_args', 'mnews_update_hide_from_wporg', 10, 2 );

/**
 * Verify the downloaded zip against the signed SHA-256 before WordPress unpacks it.
 *
 * @param mixed  $reply   Short-circuit value.
 * @param string $package Package URL.
 * @return mixed
 */
function mnews_update_verify_download( $reply, $package ) {
	$info = get_transient( 'mnews_update_info' );
	if ( ! is_array( $info ) || empty( $info['package'] ) || $package !== $info['package'] ) {
		return $reply;
	}
	if ( ! mnews_update_package_ok( $package ) ) {
		return new WP_Error( 'mnews_bad_package', __( 'Alamat paket pembaruan tidak dipercaya.', 'm-news' ) );
	}
	$file = download_url( $package, 300 );
	if ( is_wp_error( $file ) ) {
		return $file;
	}
	if ( ! hash_equals( $info['sha256'], (string) hash_file( 'sha256', $file ) ) ) {
		wp_delete_file( $file );
		return new WP_Error( 'mnews_bad_checksum', __( 'Paket pembaruan gagal diverifikasi (checksum tidak cocok). Pembaruan dibatalkan.', 'm-news' ) );
	}
	return $file;
}
add_filter( 'upgrader_pre_download', 'mnews_update_verify_download', 10, 2 );

/**
 * Forget the cached answer (licence changed / manual re-check) and let WordPress ask again.
 */
function mnews_update_reset() {
	delete_transient( 'mnews_update_info' );
	delete_site_transient( 'update_themes' );
}
