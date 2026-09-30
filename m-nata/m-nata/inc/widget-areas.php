<?php
/**
 * Widget areas and helpers to print them.
 *
 * @package M_Nata
 */

defined( 'ABSPATH' ) || exit;

/**
 * Shared wrapper markup for widgets.
 *
 * @param bool $standalone True for the_widget(), which passes only the class name to sprintf().
 * @return array
 */
function mnata_widget_args( $standalone = false ) {
	return array(
		'before_widget' => $standalone ? '<section class="mn-widget %s">' : '<section id="%1$s" class="mn-widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="mn-block-title">',
		'after_title'   => '</h2>',
	);
}

/**
 * Register all widget areas.
 */
function mnata_register_widget_areas() {
	$areas = array(
		'header-banner'        => array(
			__( 'Banner Header', 'm-nata' ),
			__( 'Di bawah menu, lebar penuh. Ukuran disarankan: 970×90 atau 728×90 (desktop) dan 320×100 (mobile).', 'm-nata' ),
		),
		'side-banner-left'     => array(
			__( 'Banner Samping Kiri', 'm-nata' ),
			__( 'Di sisi kiri konten, hanya layar sangat lebar (≥1560 px). Ukuran disarankan: 160×600 (rasio 4:15).', 'm-nata' ),
		),
		'side-banner-right'    => array(
			__( 'Banner Samping Kanan', 'm-nata' ),
			__( 'Di sisi kanan konten, hanya layar sangat lebar (≥1560 px). Ukuran disarankan: 160×600 (rasio 4:15).', 'm-nata' ),
		),
		'home-top'             => array(
			__( 'Home – Atas', 'm-nata' ),
			__( 'Lebar penuh di atas konten home, biasanya Slider Headline.', 'm-nata' ),
		),
		'home-main'            => array(
			__( 'Home – Konten Utama', 'm-nata' ),
			__( 'Kolom kiri halaman depan. Urutan widget = urutan tampil. Isi dengan Daftar Berita, Blok Kategori, dan Banner Iklan (mis. 300×250 / 728×90).', 'm-nata' ),
		),
		'home-sidebar'         => array(
			__( 'Home – Sidebar', 'm-nata' ),
			__( 'Sidebar kanan halaman depan (lebar 300 px). Banner disarankan 300×250 atau 300×600.', 'm-nata' ),
		),
		'single-sidebar'       => array(
			__( 'Artikel – Sidebar', 'm-nata' ),
			__( 'Sidebar kanan halaman artikel (lebar 300 px). Bila dikosongkan, memakai widget dari "Home – Sidebar". Banner disarankan 300×250 atau 300×600.', 'm-nata' ),
		),
		'single-in-article'    => array(
			__( 'Artikel – Di Dalam Konten', 'm-nata' ),
			__( 'Disisipkan setelah paragraf tertentu di dalam artikel. Ukuran disarankan: 300×250 atau 336×280.', 'm-nata' ),
		),
		'single-after-content' => array(
			__( 'Artikel – Setelah Konten', 'm-nata' ),
			__( 'Di bawah isi artikel. Ukuran disarankan: 728×90 atau 300×250.', 'm-nata' ),
		),
		'footer-1'             => array( __( 'Footer 1', 'm-nata' ), __( 'Kolom footer pertama.', 'm-nata' ) ),
		'footer-2'             => array( __( 'Footer 2', 'm-nata' ), __( 'Kolom footer kedua.', 'm-nata' ) ),
		'footer-3'             => array( __( 'Footer 3', 'm-nata' ), __( 'Kolom footer ketiga.', 'm-nata' ) ),
	);

	foreach ( $areas as $id => $data ) {
		register_sidebar(
			array_merge(
				mnata_widget_args(),
				array(
					'id'          => $id,
					'name'        => $data[0],
					'description' => $data[1],
				)
			)
		);
	}
}
add_action( 'widgets_init', 'mnata_register_widget_areas' );

/**
 * Classic widgets screen: the theme's widgets use the classic Widget API.
 */
add_filter( 'use_widgets_block_editor', '__return_false' );

/**
 * Print a widget area inside a wrapper, only when it has widgets.
 *
 * @param string $id    Sidebar ID.
 * @param string $classes Wrapper classes.
 * @return bool Whether anything was printed.
 */
function mnata_area( $id, $classes = '' ) {
	if ( ! is_active_sidebar( $id ) ) {
		return false;
	}
	echo '<div class="' . esc_attr( trim( 'mn-area mn-area--' . $id . ' ' . $classes ) ) . '">';
	dynamic_sidebar( $id );
	echo '</div>';
	return true;
}

/**
 * Left/right 160x600 banners (wide screens only). Call once inside a position:relative container.
 * Each side is a full-height column; the inner box sticks while scrolling (Customizer can turn that off).
 */
function mnata_side_banners() {
	foreach ( array( 'left', 'right' ) as $side ) {
		$area = 'side-banner-' . $side;
		if ( ! is_active_sidebar( $area ) ) {
			continue;
		}
		echo '<div class="mn-side mn-side--' . esc_attr( $side ) . '"><div class="mn-side__in">';
		mnata_area( $area );
		echo '</div></div>';
	}
}

/**
 * Footer widget columns.
 */
function mnata_footer_widgets() {
	$cols = array_filter(
		array( 'footer-1', 'footer-2', 'footer-3' ),
		'is_active_sidebar'
	);
	if ( ! $cols ) {
		return;
	}
	echo '<div class="mn-footer__widgets">';
	foreach ( $cols as $id ) {
		mnata_area( $id, 'mn-footer__col' );
	}
	echo '</div>';
}

/**
 * On a "latest posts" front page the widgets run their own queries, so shrink
 * the unused main query to one cheap post (no counting, no meta/term priming).
 *
 * @param WP_Query $query Query.
 */
function mnata_lean_home_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_home() || ! $query->is_front_page() || $query->is_feed() ) {
		return;
	}
	$query->set( 'posts_per_page', 1 );
	$query->set( 'no_found_rows', true );
	$query->set( 'update_post_meta_cache', false );
	$query->set( 'update_post_term_cache', false );
}
add_action( 'pre_get_posts', 'mnata_lean_home_query' );
