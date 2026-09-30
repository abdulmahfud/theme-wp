<?php
/**
 * Theme setup: supports, menus.
 *
 * @package M_Nata
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register theme features.
 */
function mnata_setup() {
	load_theme_textdomain( 'm-nata', MNATA_DIR . '/languages' );

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
			'primary' => __( 'Menu Utama', 'm-nata' ),
			'footer'  => __( 'Menu Footer', 'm-nata' ),
			'network' => __( 'Menu Media Network (footer)', 'm-nata' ),
		)
	);
}
add_action( 'after_setup_theme', 'mnata_setup' );

/**
 * Content width for embeds.
 */
function mnata_content_width() {
	$GLOBALS['content_width'] = 800;
}
add_action( 'after_setup_theme', 'mnata_content_width', 0 );

/**
 * The header logo is above the fold: never lazy-load it.
 *
 * @param array $attr Image attributes.
 * @return array
 */
function mnata_logo_attributes( $attr ) {
	$attr['loading']       = 'eager';
	$attr['fetchpriority'] = 'high';
	return $attr;
}
add_filter( 'get_custom_logo_image_attributes', 'mnata_logo_attributes' );

/**
 * Body classes used by the stylesheet.
 *
 * @param string[] $classes Classes.
 * @return string[]
 */
function mnata_body_classes( $classes ) {
	if ( get_theme_mod( 'mnata_sticky_header', true ) ) {
		$classes[] = 'mn-sticky';
	}
	if ( get_theme_mod( 'mnata_side_sticky', true ) ) {
		$classes[] = 'mn-side-sticky';
	}
	return $classes;
}
add_filter( 'body_class', 'mnata_body_classes' );

/**
 * Archive titles without the "Category:" / "Tag:" prefix (the template adds its own label).
 */
add_filter( 'get_the_archive_title_prefix', '__return_empty_string' );

/**
 * Sites without a Site Icon would make browsers request /favicon.ico (a 404 that shows up as a console error).
 * An empty inline icon avoids that request; setting a Site Icon in the Customizer replaces it.
 */
function mnata_default_favicon() {
	if ( ! has_site_icon() ) {
		echo "<link rel=\"icon\" href=\"data:,\">\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
add_action( 'wp_head', 'mnata_default_favicon', 2 );
