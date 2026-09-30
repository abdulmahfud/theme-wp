<?php
/**
 * Remove WordPress front-end bloat. Toggle: Customizer > M-News > Performa.
 *
 * Block styles are kept on singular pages (article content may use blocks),
 * and removed on home/archive/search where only theme markup is rendered.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether cleanup is enabled.
 *
 * @return bool
 */
function mnews_clean_enabled() {
	return (bool) get_theme_mod( 'mnews_clean_wp', true );
}

/**
 * Detach head/footer extras.
 */
function mnews_performance_init() {
	if ( ! mnews_clean_enabled() ) {
		return;
	}

	// Emoji.
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'emoji_svg_url', '__return_false' );

	// Head clutter.
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head', 10 );
	remove_action( 'wp_head', 'feed_links_extra', 3 );
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
}
add_action( 'init', 'mnews_performance_init' );

/**
 * Dequeue assets late so we win over plugins that enqueue on the default priority.
 */
function mnews_dequeue_assets() {
	if ( ! mnews_clean_enabled() ) {
		return;
	}

	wp_dequeue_script( 'wp-embed' );

	if ( ! is_user_logged_in() ) {
		wp_dequeue_style( 'dashicons' );
	}

	if ( ! is_singular() ) {
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'wp-block-library-theme' );
		wp_dequeue_style( 'global-styles' );
		wp_dequeue_style( 'classic-theme-styles' );
	}
}
add_action( 'wp_enqueue_scripts', 'mnews_dequeue_assets', 100 );

/**
 * Drop global styles / SVG filters on non-singular pages.
 */
function mnews_drop_global_styles() {
	if ( ! mnews_clean_enabled() || is_singular() ) {
		return;
	}
	remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
	remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );
	remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );
}
add_action( 'wp', 'mnews_drop_global_styles' );
