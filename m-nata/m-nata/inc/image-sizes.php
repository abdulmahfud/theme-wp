<?php
/**
 * Image sizes. Keep this list short (budget: <= 6 custom sizes). All are 16:9 except the square.
 *
 * @package M_Nata
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register image sizes.
 */
function mnata_image_sizes() {
	add_image_size( 'mnata-square', 160, 160, true ); // Compact / numbered lists.
	add_image_size( 'mnata-thumb', 320, 180, true );  // List cards.
	add_image_size( 'mnata-card', 640, 360, true );   // Grid / carousel cards.
	add_image_size( 'mnata-hero', 960, 540, true );   // Slider, overlay cards (LCP).
}
add_action( 'after_setup_theme', 'mnata_image_sizes' );
