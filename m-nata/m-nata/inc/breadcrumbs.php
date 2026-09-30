<?php
/**
 * Breadcrumb trail for single posts: Beranda / Kategori.
 *
 * @package M_Nata
 */

defined( 'ABSPATH' ) || exit;

/**
 * Trail items for the current post: array of [label, url|null].
 *
 * @param bool $with_title Include the post title as the last, unlinked item (used by JSON-LD).
 * @return array<int,array{0:string,1:?string}>
 */
function mnata_breadcrumb_items( $with_title = true ) {
	$items = array( array( __( 'Beranda', 'm-nata' ), home_url( '/' ) ) );

	if ( is_singular( 'post' ) ) {
		$cats = get_the_category();
		if ( $cats ) {
			$items[] = array( $cats[0]->name, get_category_link( $cats[0] ) );
		}
	}
	if ( $with_title && is_singular() ) {
		$items[] = array( get_the_title(), null );
	}
	return $items;
}

/**
 * Echo the breadcrumb navigation.
 */
function mnata_breadcrumb() {
	$items = mnata_breadcrumb_items( false );

	echo '<nav class="mn-breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'm-nata' ) . '"><ol>';
	foreach ( $items as $item ) {
		if ( ! $item[1] ) {
			printf( '<li aria-current="page"><span>%s</span></li>', esc_html( $item[0] ) );
		} else {
			printf( '<li><a href="%1$s">%2$s</a></li>', esc_url( $item[1] ), esc_html( $item[0] ) );
		}
	}
	echo '</ol></nav>';
}
