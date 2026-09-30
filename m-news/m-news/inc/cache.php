<?php
/**
 * Tiny versioned transient cache. Bumping the version invalidates every entry at once.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

/**
 * Current cache generation (autoloaded option, so no extra query per request).
 *
 * @return string
 */
function mnews_cache_version() {
	$v = get_option( 'mnews_cache_v' );
	return $v ? (string) $v : '1';
}

/**
 * Invalidate all cached theme output.
 */
function mnews_cache_flush() {
	update_option( 'mnews_cache_v', (string) microtime( true ), true );
}

/**
 * Return a cached value or compute and store it.
 *
 * @param string   $key      Unique key (will be hashed with args).
 * @param int      $ttl      Seconds.
 * @param callable $callback Produces the value when missing.
 * @return mixed
 */
function mnews_cache_remember( $key, $ttl, $callback ) {
	if ( is_customize_preview() ) {
		return call_user_func( $callback );
	}

	$name  = 'mnews_' . md5( $key . '|' . mnews_cache_version() );
	$value = get_transient( $name );

	if ( false === $value ) {
		$value = call_user_func( $callback );
		set_transient( $name, $value, $ttl );
	}

	return $value;
}

/**
 * Cache a chunk of printed HTML (with the icons it used, so the sprite stays complete on a hit).
 *
 * @param string   $key      Unique key.
 * @param int      $ttl      Seconds.
 * @param callable $callback Prints the fragment.
 */
function mnews_cache_fragment( $key, $ttl, $callback ) {
	$cache = mnews_cache_remember(
		$key,
		$ttl,
		static function () use ( $callback ) {
			mnews_icon_log( null, true );
			ob_start();
			call_user_func( $callback );
			return array(
				'html'  => ob_get_clean(),
				'icons' => mnews_icon_log( null, true ),
			);
		}
	);
	foreach ( $cache['icons'] as $icon ) {
		mnews_icons_used( $icon );
	}
	echo $cache['html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped where it was generated.
}

/**
 * Flush on content changes (not on autosaves/revisions).
 *
 * @param int $post_id Post ID.
 */
function mnews_cache_on_post_change( $post_id ) {
	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}
	mnews_cache_flush();
}
add_action( 'save_post', 'mnews_cache_on_post_change' );
add_action( 'deleted_post', 'mnews_cache_on_post_change' );
add_action( 'trashed_post', 'mnews_cache_on_post_change' );
add_action( 'edited_term', 'mnews_cache_flush' );
add_action( 'delete_term', 'mnews_cache_flush' );
add_action( 'switch_theme', 'mnews_cache_flush' );
add_action( 'customize_save_after', 'mnews_cache_flush' );
