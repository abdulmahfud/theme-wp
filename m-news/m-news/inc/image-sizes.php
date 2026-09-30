<?php
/**
 * Image sizes. Keep this list short (budget: <= 6 custom sizes). All are 16:9 except the square.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register image sizes.
 */
function mnews_image_sizes() {
	add_image_size( 'mnews-square', 160, 160, true ); // Compact / numbered lists.
	add_image_size( 'mnews-thumb', 320, 180, true );  // List cards.
	add_image_size( 'mnews-card', 640, 360, true );   // Grid / carousel cards.
	add_image_size( 'mnews-hero', 960, 540, true );   // Slider, overlay cards (LCP).
}
add_action( 'after_setup_theme', 'mnews_image_sizes' );
