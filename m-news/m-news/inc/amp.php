<?php
/**
 * AMP compatibility (official AMP plugin, Transitional or Standard mode).
 *
 * The theme's own JavaScript is not allowed on AMP pages, so on AMP the theme simply stops emitting what cannot
 * work there (scripts, ad code, "copy link", floating bar, slider buttons) and lets CSS scroll-snap, the AMP
 * plugin's menu toggle and <amp-pixel> do the job. The result validates without any sanitising.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether the current request is an AMP page.
 *
 * @return bool
 */
function mnews_is_amp() {
	return function_exists( 'amp_is_request' ) && amp_is_request();
}

/**
 * Declare AMP support. Transitional ("paired") keeps the full experience for normal visitors
 * and serves AMP to search/social traffic; Standard can also be chosen in the plugin settings.
 */
function mnews_amp_support() {
	add_theme_support(
		'amp',
		array(
			'paired'          => true,
			'nav_menu_toggle' => array(
				'nav_container_id'           => 'mnw-nav',
				'nav_container_toggle_class' => 'is-open',
				'menu_button_id'             => 'mnw-toggle',
				'menu_button_toggle_class'   => 'is-open',
			),
		)
	);
}
add_action( 'after_setup_theme', 'mnews_amp_support' );

/**
 * Body class that switches on the AMP-only CSS.
 *
 * @param string[] $classes Classes.
 * @return string[]
 */
function mnews_amp_body_class( $classes ) {
	if ( mnews_is_amp() ) {
		$classes[] = 'mnw-amp';
	}
	return $classes;
}
add_filter( 'body_class', 'mnews_amp_body_class' );

/**
 * Images: the `loading` attribute is not valid on AMP images (the plugin lazy-loads them itself).
 *
 * @param array $attr Image attributes.
 * @return array
 */
function mnews_amp_image_attributes( $attr ) {
	if ( mnews_is_amp() ) {
		unset( $attr['loading'] );
	}
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'mnews_amp_image_attributes', 99 );
add_filter(
	'wp_lazy_loading_enabled',
	static function ( $enabled ) {
		return mnews_is_amp() ? false : $enabled;
	}
);

/**
 * Core's speculation rules <script> is not allowed on AMP.
 *
 * @param mixed $config Configuration.
 * @return mixed
 */
function mnews_amp_no_speculation( $config ) {
	return mnews_is_amp() ? null : $config;
}
add_filter( 'wp_speculation_rules_configuration', 'mnews_amp_no_speculation' );
