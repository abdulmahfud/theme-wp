<?php
/**
 * Visitor statistics (privacy-friendly, cache-friendly).
 *
 * Counting happens in a tiny beacon request (`/wp-json/mnews/v1/hit`), never while a page renders, so it works
 * behind page caches and on AMP (<amp-pixel>). No IP address is stored: a visitor is a random id from the browser,
 * kept only as a one-way hash in a short-lived transient. Counters are atomic SQL increments on non-autoloaded options.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether the visitor widget is placed anywhere (only then is anything counted or loaded).
 *
 * @return bool
 */
function mnews_stats_active() {
	return is_active_widget( false, false, 'mnews_visitors', false ) ? true : false;
}

/**
 * Atomically add one to a counter option (created on first use, never autoloaded).
 *
 * @param string $name Option name.
 */
function mnews_stats_incr( $name ) {
	global $wpdb;
	// phpcs:disable WordPress.DB.DirectDatabaseQuery -- atomic counter, see file header.
	$updated = $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->options} SET option_value = option_value + 1 WHERE option_name = %s", $name ) );
	if ( ! $updated && ! add_option( $name, 1, '', false ) ) {
		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->options} SET option_value = option_value + 1 WHERE option_name = %s", $name ) );
	}
	// phpcs:enable
}

/**
 * Crawler / bot detection by user agent.
 *
 * @return bool
 */
function mnews_stats_is_bot() {
	$ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? strtolower( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) ) : '';
	return '' === $ua || (bool) preg_match( '/bot|crawl|spider|slurp|headless|lighthouse|pagespeed|curl|wget|python|monitor|preview/', $ua );
}

/**
 * Record one visit. Called from the REST endpoint.
 *
 * @param string $visitor_id Random id from the browser (a-z0-9, 8-40 chars).
 */
function mnews_stats_record( $visitor_id ) {
	$today = wp_date( 'Ymd' );
	$month = wp_date( 'Ym' );
	$hash  = md5( $visitor_id . '|' . wp_salt( 'auth' ) );

	mnews_stats_incr( 'mnews_c_pv_total' );
	mnews_stats_incr( 'mnews_c_pv_' . $today );

	// Unique visitor per day.
	$seen = 'mnews_seen_' . md5( $hash . $today );
	if ( false === get_transient( $seen ) ) {
		set_transient( $seen, 1, DAY_IN_SECONDS );
		mnews_stats_incr( 'mnews_c_v_total' );
		mnews_stats_incr( 'mnews_c_v_' . $today );
		mnews_stats_incr( 'mnews_c_vm_' . $month );
	}

	// Online now: seen within the last 5 minutes; refreshed at most once a minute per visitor.
	$now    = time();
	$online = get_transient( 'mnews_online' );
	$online = is_array( $online ) ? $online : array();
	if ( ! isset( $online[ $hash ] ) || $now - $online[ $hash ] > 60 ) {
		$online[ $hash ] = $now;
		foreach ( $online as $key => $ts ) {
			if ( $now - $ts > 300 ) {
				unset( $online[ $key ] );
			}
		}
		set_transient( 'mnews_online', $online, 10 * MINUTE_IN_SECONDS );
	}

	mnews_stats_maybe_prune( $today );
}

/**
 * Once a day, delete per-day counters older than ~60 days.
 *
 * @param string $today Y-m-d as Ymd.
 */
function mnews_stats_maybe_prune( $today ) {
	if ( get_option( 'mnews_c_pruned' ) === $today ) {
		return;
	}
	global $wpdb;
	$cut = wp_date( 'Ymd', time() - 60 * DAY_IN_SECONDS );
	foreach ( array( 'mnews_c_pv_', 'mnews_c_v_' ) as $prefix ) {
		$rows = $wpdb->get_col( $wpdb->prepare( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s", $wpdb->esc_like( $prefix ) . '2%' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		foreach ( (array) $rows as $name ) {
			if ( preg_match( '/_(\d{8})$/', $name, $m ) && $m[1] < $cut ) {
				delete_option( $name );
			}
		}
	}
	update_option( 'mnews_c_pruned', $today, false );
}

/**
 * Current numbers (one query, cached for a minute).
 *
 * @return array{online:int,today:int,yesterday:int,month:int,total:int,pv_today:int,pv_total:int}
 */
function mnews_stats_snapshot() {
	$cached = get_transient( 'mnews_stats_snapshot' );
	if ( is_array( $cached ) ) {
		return $cached;
	}

	global $wpdb;
	$today = wp_date( 'Ymd' );
	$yest  = wp_date( 'Ymd', time() - DAY_IN_SECONDS );
	$month = wp_date( 'Ym' );
	$names = array( 'mnews_c_v_total', 'mnews_c_pv_total', 'mnews_c_v_' . $today, 'mnews_c_v_' . $yest, 'mnews_c_vm_' . $month, 'mnews_c_pv_' . $today );

	$placeholders = implode( ',', array_fill( 0, count( $names ), '%s' ) );
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare -- placeholders built above.
	$rows = $wpdb->get_results( $wpdb->prepare( "SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name IN ($placeholders)", $names ), OBJECT_K );
	$val  = static function ( $name ) use ( $rows ) {
		return isset( $rows[ $name ] ) ? (int) $rows[ $name ]->option_value : 0;
	};

	$online = get_transient( 'mnews_online' );
	$now    = time();
	$count  = 0;
	foreach ( is_array( $online ) ? $online : array() as $ts ) {
		if ( $now - $ts <= 300 ) {
			++$count;
		}
	}

	$snap = array(
		'online'    => $count,
		'today'     => $val( 'mnews_c_v_' . $today ),
		'yesterday' => $val( 'mnews_c_v_' . $yest ),
		'month'     => $val( 'mnews_c_vm_' . $month ),
		'total'     => $val( 'mnews_c_v_total' ),
		'pv_today'  => $val( 'mnews_c_pv_' . $today ),
		'pv_total'  => $val( 'mnews_c_pv_total' ),
	);
	set_transient( 'mnews_stats_snapshot', $snap, MINUTE_IN_SECONDS );
	return $snap;
}

/**
 * REST route for the beacon.
 */
function mnews_stats_register_route() {
	register_rest_route(
		'mnews/v1',
		'/hit',
		array(
			'methods'             => array( 'GET', 'POST' ),
			'permission_callback' => '__return_true',
			'callback'            => static function ( WP_REST_Request $request ) {
				$id = strtolower( preg_replace( '/[^A-Za-z0-9]/', '', (string) $request->get_param( 'v' ) ) );
				if ( strlen( $id ) >= 8 && strlen( $id ) <= 64 && ! mnews_stats_is_bot() && mnews_stats_active() ) {
					mnews_stats_record( substr( $id, 0, 40 ) );
				}
				$response = new WP_REST_Response( null, 204 );
				$response->header( 'Cache-Control', 'no-store' );
				return $response;
			},
		)
	);
}
add_action( 'rest_api_init', 'mnews_stats_register_route' );

/**
 * Load the beacon on normal pages, or print <amp-pixel> on AMP pages, only while the widget is in use.
 */
function mnews_stats_tracker() {
	if ( ! mnews_stats_active() || ( is_user_logged_in() && current_user_can( 'edit_posts' ) ) ) {
		return;
	}
	$url = rest_url( 'mnews/v1/hit' );

	if ( mnews_is_amp() ) {
		add_action(
			'wp_footer',
			static function () use ( $url ) {
				// amp-pixel needs https; CLIENT_ID and RANDOM are AMP substitutions.
				printf(
					'<amp-pixel src="%s" layout="nodisplay"></amp-pixel>',
					esc_url( set_url_scheme( $url, 'https' ) . '?v=CLIENT_ID(mnews_v)&r=RANDOM' )
				);
			}
		);
		return;
	}

	wp_enqueue_script(
		'mnews-visit',
		mnews_asset( 'assets/js/visit.js' )[0],
		array(),
		mnews_asset( 'assets/js/visit.js' )[1],
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);
	wp_add_inline_script( 'mnews-visit', 'window.mnewsHit=' . wp_json_encode( esc_url_raw( $url ) ) . ';', 'before' );
}
add_action( 'wp_enqueue_scripts', 'mnews_stats_tracker' );
