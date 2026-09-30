<?php
/**
 * Theme setup: supports, menus.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register theme features.
 */
function mnews_setup() {
	load_theme_textdomain( 'm-news', MNEWS_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 60,
			'width'       => 240,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'Menu Utama', 'm-news' ),
			'footer'  => __( 'Menu Footer', 'm-news' ),
			'network' => __( 'Menu Media Network (footer)', 'm-news' ),
		)
	);
}
add_action( 'after_setup_theme', 'mnews_setup' );

/**
 * Content width for embeds.
 */
function mnews_content_width() {
	$GLOBALS['content_width'] = 800;
}
add_action( 'after_setup_theme', 'mnews_content_width', 0 );

/**
 * The header logo is above the fold: never lazy-load it.
 *
 * @param array $attr Image attributes.
 * @return array
 */
function mnews_logo_attributes( $attr ) {
	$attr['loading']       = 'eager';
	$attr['fetchpriority'] = 'high';
	return $attr;
}
add_filter( 'get_custom_logo_image_attributes', 'mnews_logo_attributes' );

/**
 * Body classes used by the stylesheet.
 *
 * @param string[] $classes Classes.
 * @return string[]
 */
function mnews_body_classes( $classes ) {
	if ( get_theme_mod( 'mnews_sticky_header', true ) ) {
		$classes[] = 'mnw-sticky';
	}
	if ( get_theme_mod( 'mnews_side_sticky', true ) ) {
		$classes[] = 'mnw-side-sticky';
	}
	return $classes;
}
add_filter( 'body_class', 'mnews_body_classes' );

/**
 * Archive titles without the "Category:" / "Tag:" prefix (the template adds its own label).
 */
add_filter( 'get_the_archive_title_prefix', '__return_empty_string' );

/**
 * Sites without a Site Icon would make browsers request /favicon.ico (a 404 that shows up as a console error).
 * An empty inline icon avoids that request; setting a Site Icon in the Customizer replaces it.
 */
function mnews_default_favicon() {
	if ( ! has_site_icon() ) {
		echo "<link rel=\"icon\" href=\"data:,\">\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
add_action( 'wp_head', 'mnews_default_favicon', 2 );
