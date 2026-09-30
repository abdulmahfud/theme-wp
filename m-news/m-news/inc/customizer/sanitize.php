<?php
/**
 * Customizer sanitizers.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

/**
 * Checkbox.
 *
 * @param mixed $value Raw value.
 * @return bool
 */
function mnews_sanitize_checkbox( $value ) {
	return (bool) $value;
}

/**
 * Gradient preset key (or "custom").
 *
 * @param string $value Raw value.
 * @return string
 */
function mnews_sanitize_gradient_preset( $value ) {
	$presets = mnews_gradient_presets();
	return ( 'custom' === $value || isset( $presets[ $value ] ) ) ? $value : 'ungu-magenta';
}

/**
 * Gradient direction.
 *
 * @param string $value Raw value.
 * @return string
 */
function mnews_sanitize_gradient_dir( $value ) {
	$dirs = mnews_gradient_directions();
	return isset( $dirs[ $value ] ) ? $value : 'to right';
}

/**
 * Hex colour that may be empty (optional stops).
 *
 * @param string $value Raw value.
 * @return string
 */
function mnews_sanitize_optional_hex( $value ) {
	$value = (string) $value;
	if ( '' === $value ) {
		return '';
	}
	$hex = sanitize_hex_color( $value );
	return $hex ? $hex : '';
}

/**
 * Integer clamped between 1 and 10 (ticker count).
 *
 * @param mixed $value Raw value.
 * @return int
 */
function mnews_sanitize_ticker_count( $value ) {
	return max( 1, min( 10, absint( $value ) ) );
}

/**
 * Tag slug.
 *
 * @param string $value Raw value.
 * @return string
 */
function mnews_sanitize_slug( $value ) {
	return sanitize_title( (string) $value );
}
