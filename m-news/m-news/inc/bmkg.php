<?php
/**
 * BMKG data layer (weather forecast + earthquakes).
 *
 * Rules: visitors never wait on BMKG when any earlier copy exists. Data lives in transients,
 * a stale copy is kept as a fallback, failures are remembered for a minute, and WP-Cron
 * refreshes everything in the background.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

const MNEWS_BMKG_QUAKE_URL   = 'https://data.bmkg.go.id/DataMKG/TEWS/';
const MNEWS_BMKG_WEATHER_URL = 'https://api.bmkg.go.id/publik/prakiraan-cuaca?adm4=';
const MNEWS_BMKG_QUAKE_TTL   = 10 * MINUTE_IN_SECONDS;
const MNEWS_BMKG_WEATHER_TTL = HOUR_IN_SECONDS;

/**
 * Fetch and decode JSON from an allowed BMKG URL. Returns null on any problem.
 *
 * @param string $url URL (must start with one of the BMKG bases).
 * @return array|null
 */
function mnews_bmkg_remote( $url ) {
	if ( 0 !== strpos( $url, MNEWS_BMKG_QUAKE_URL ) && 0 !== strpos( $url, MNEWS_BMKG_WEATHER_URL ) ) {
		return null;
	}
	$res = wp_safe_remote_get(
		$url,
		array(
			'timeout'     => 3,
			'redirection' => 2,
			'headers'     => array( 'Accept' => 'application/json' ),
			'user-agent'  => 'M-News/' . MNEWS_VERSION . '; ' . home_url( '/' ),
		)
	);
	if ( is_wp_error( $res ) || 200 !== (int) wp_remote_retrieve_response_code( $res ) ) {
		return null;
	}
	$data = json_decode( wp_remote_retrieve_body( $res ), true );
	return is_array( $data ) ? $data : null;
}

/**
 * Cached BMKG JSON with stale fallback.
 *
 * @param string $url   BMKG URL.
 * @param int    $ttl   Fresh lifetime in seconds.
 * @param bool   $force Skip the fresh copy (used by cron).
 * @return array|null
 */
function mnews_bmkg_get( $url, $ttl, $force = false ) {
	$key = 'mnews_bmkg_' . md5( $url );

	if ( ! $force ) {
		$fresh = get_transient( $key );
		if ( is_array( $fresh ) ) {
			return $fresh;
		}
		if ( get_transient( $key . '_f' ) ) {
			$stale = get_option( $key . '_s' );
			return is_array( $stale ) ? $stale : null;
		}
	}

	$data = mnews_bmkg_remote( $url );
	if ( null !== $data ) {
		set_transient( $key, $data, $ttl );
		update_option( $key . '_s', $data, false );
		delete_transient( $key . '_f' );
		return $data;
	}

	set_transient( $key . '_f', 1, MINUTE_IN_SECONDS );
	$stale = get_option( $key . '_s' );
	return is_array( $stale ) ? $stale : null;
}

/**
 * Whether a string is a BMKG adm4 (kelurahan/desa) code, e.g. 31.71.03.1001.
 *
 * @param string $code Code.
 * @return bool
 */
function mnews_is_adm4( $code ) {
	return (bool) preg_match( '/^\d{2}\.\d{2}\.\d{2}\.\d{4}$/', (string) $code );
}

/**
 * Remember which adm4 codes are in use so cron can keep them warm.
 *
 * @param string $code adm4 code.
 */
function mnews_bmkg_track( $code ) {
	$codes = get_option( 'mnews_bmkg_codes', array() );
	if ( ! is_array( $codes ) ) {
		$codes = array();
	}
	if ( ! in_array( $code, $codes, true ) && count( $codes ) < 20 ) {
		$codes[] = $code;
		update_option( 'mnews_bmkg_codes', $codes, false );
	}
}

/**
 * Earthquake list/latest. $kind: autogempa | gempaterkini | gempadirasakan.
 *
 * @param string $kind Dataset name.
 * @return array[] List of quake arrays (autogempa returns a single-item list).
 */
function mnews_bmkg_quakes( $kind ) {
	if ( ! in_array( $kind, array( 'autogempa', 'gempaterkini', 'gempadirasakan' ), true ) ) {
		return array();
	}
	$data = mnews_bmkg_get( MNEWS_BMKG_QUAKE_URL . $kind . '.json', MNEWS_BMKG_QUAKE_TTL );
	if ( ! isset( $data['Infogempa']['gempa'] ) ) {
		return array();
	}
	$g = $data['Infogempa']['gempa'];
	return isset( $g['Magnitude'] ) ? array( $g ) : array_values( (array) $g );
}

/**
 * Weather forecast for an adm4 code: location names + flat, time-sorted slots.
 *
 * @param string $adm4 Code.
 * @return array{loc:array,slots:array[]}|null
 */
function mnews_bmkg_weather( $adm4 ) {
	if ( ! mnews_is_adm4( $adm4 ) ) {
		return null;
	}
	$data = mnews_bmkg_get( MNEWS_BMKG_WEATHER_URL . rawurlencode( $adm4 ), MNEWS_BMKG_WEATHER_TTL );
	if ( empty( $data['data'][0]['cuaca'] ) || ! is_array( $data['data'][0]['cuaca'] ) ) {
		return null;
	}

	$slots = array();
	foreach ( $data['data'][0]['cuaca'] as $day ) {
		foreach ( (array) $day as $slot ) {
			if ( isset( $slot['utc_datetime'], $slot['t'] ) ) {
				$slot['ts'] = (int) strtotime( $slot['utc_datetime'] . ' UTC' );
				$slots[]    = $slot;
			}
		}
	}
	if ( ! $slots ) {
		return null;
	}
	usort(
		$slots,
		static function ( $a, $b ) {
			return $a['ts'] - $b['ts'];
		}
	);

	return array(
		'loc'   => isset( $data['lokasi'] ) ? $data['lokasi'] : $data['data'][0]['lokasi'],
		'slots' => $slots,
	);
}

/**
 * Map a BMKG weather code to an icon key (night variants after 18:00 / before 06:00 local).
 *
 * @param int  $code  BMKG code.
 * @param bool $night Whether it is night at the location.
 * @return string
 */
function mnews_weather_icon( $code, $night = false ) {
	$code = (int) $code;
	if ( in_array( $code, array( 95, 97 ), true ) ) {
		return 'w-storm';
	}
	if ( in_array( $code, array( 60, 61, 63, 80 ), true ) ) {
		return 'w-rain';
	}
	if ( in_array( $code, array( 5, 10, 45 ), true ) ) {
		return 'w-fog';
	}
	if ( in_array( $code, array( 3, 4 ), true ) ) {
		return 'w-cloud';
	}
	if ( 2 === $code ) {
		return $night ? 'w-partly-night' : 'w-partly';
	}
	return $night ? 'w-moon' : 'w-sun';
}

/**
 * Background refresh of everything the site uses.
 */
function mnews_bmkg_refresh() {
	foreach ( array( 'autogempa', 'gempaterkini', 'gempadirasakan' ) as $kind ) {
		if ( get_option( 'mnews_bmkg_used_' . $kind ) ) {
			mnews_bmkg_get( MNEWS_BMKG_QUAKE_URL . $kind . '.json', MNEWS_BMKG_QUAKE_TTL, true );
		}
	}
	$codes = get_option( 'mnews_bmkg_codes', array() );
	foreach ( is_array( $codes ) ? $codes : array() as $code ) {
		if ( mnews_is_adm4( $code ) ) {
			mnews_bmkg_get( MNEWS_BMKG_WEATHER_URL . rawurlencode( $code ), MNEWS_BMKG_WEATHER_TTL, true );
		}
	}
}
add_action( 'mnews_bmkg_cron', 'mnews_bmkg_refresh' );

/**
 * Note that a quake dataset is used by a widget (so cron refreshes it).
 *
 * @param string $kind Dataset name.
 */
function mnews_bmkg_track_quake( $kind ) {
	if ( ! get_option( 'mnews_bmkg_used_' . $kind ) ) {
		update_option( 'mnews_bmkg_used_' . $kind, 1, false );
	}
}

/**
 * Cron interval and schedule.
 *
 * @param array $schedules Schedules.
 * @return array
 */
function mnews_bmkg_cron_schedules( $schedules ) {
	$schedules['mnews_10min'] = array(
		'interval' => 10 * MINUTE_IN_SECONDS,
		'display'  => __( 'Setiap 10 menit', 'm-news' ),
	);
	return $schedules;
}
add_filter( 'cron_schedules', 'mnews_bmkg_cron_schedules' ); // phpcs:ignore WordPress.WP.CronInterval.CronSchedulesInterval -- 10 minutes is intentional for earthquake data.

/**
 * Make sure the event exists; remove it when the theme is switched away.
 */
function mnews_bmkg_schedule() {
	if ( ! wp_next_scheduled( 'mnews_bmkg_cron' ) ) {
		wp_schedule_event( time() + MINUTE_IN_SECONDS, 'mnews_10min', 'mnews_bmkg_cron' );
	}
}
add_action( 'init', 'mnews_bmkg_schedule' );

/**
 * Unschedule on theme switch.
 */
function mnews_bmkg_unschedule() {
	wp_clear_scheduled_hook( 'mnews_bmkg_cron' );
}
add_action( 'switch_theme', 'mnews_bmkg_unschedule' );

/**
 * Admin AJAX: region lists (provinsi > kab/kota > kecamatan > desa/kelurahan) for the weather widget.
 * Proxied and cached for 30 days so the admin's browser never calls a third-party site.
 */
function mnews_ajax_wilayah() {
	check_ajax_referer( 'mnews_wilayah', '_wpnonce' );
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_send_json_error( null, 403 );
	}

	$levels = array(
		'provinces' => '',
		'regencies' => '/^\d{2}$/',
		'districts' => '/^\d{2}\.\d{2}$/',
		'villages'  => '/^\d{2}\.\d{2}\.\d{2}$/',
	);
	$level  = isset( $_GET['level'] ) ? sanitize_key( wp_unslash( $_GET['level'] ) ) : '';
	$parent = isset( $_GET['parent'] ) ? sanitize_text_field( wp_unslash( $_GET['parent'] ) ) : '';

	if ( ! isset( $levels[ $level ] ) || ( $levels[ $level ] && ! preg_match( $levels[ $level ], $parent ) ) ) {
		wp_send_json_error( null, 400 );
	}

	$key  = 'mnews_wil_' . md5( $level . '|' . $parent );
	$list = get_transient( $key );
	if ( false === $list ) {
		$url = 'https://wilayah.id/api/' . $level . ( $parent ? '/' . $parent : '' ) . '.json';
		$res = wp_safe_remote_get( $url, array( 'timeout' => 8 ) );
		if ( is_wp_error( $res ) || 200 !== (int) wp_remote_retrieve_response_code( $res ) ) {
			wp_send_json_error( null, 502 );
		}
		$body = json_decode( wp_remote_retrieve_body( $res ), true );
		$list = array();
		foreach ( isset( $body['data'] ) ? (array) $body['data'] : array() as $row ) {
			if ( isset( $row['code'], $row['name'] ) ) {
				$list[] = array(
					'code' => sanitize_text_field( $row['code'] ),
					'name' => trim( sanitize_text_field( $row['name'] ) ),
				);
			}
		}
		set_transient( $key, $list, 30 * DAY_IN_SECONDS );
	}
	wp_send_json_success( $list );
}
add_action( 'wp_ajax_mnews_wilayah', 'mnews_ajax_wilayah' );
