<?php
/**
 * Widget areas and helpers to print them.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

/**
 * Shared wrapper markup for widgets.
 *
 * @param bool $standalone True for the_widget(), which passes only the class name to sprintf().
 * @return array
 */
function mnews_widget_args( $standalone = false ) {
	return array(
		'before_widget' => $standalone ? '<section class="mnw-widget %s">' : '<section id="%1$s" class="mnw-widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="mnw-block-title">',
		'after_title'   => '</h2>',
	);
}

/**
 * Register all widget areas.
 */
function mnews_register_widget_areas() {
	$areas = array(
		'header-banner'        => array(
			__( 'Banner Header', 'm-news' ),
			__( 'Di bawah menu, lebar penuh. Ukuran disarankan: 970×90 atau 728×90 (desktop) dan 320×100 (mobile).', 'm-news' ),
		),
		'side-banner-left'     => array(
			__( 'Banner Samping Kiri', 'm-news' ),
			__( 'Di sisi kiri konten, hanya layar sangat lebar (≥1560 px). Ukuran disarankan: 160×600 (rasio 4:15).', 'm-news' ),
		),
		'side-banner-right'    => array(
			__( 'Banner Samping Kanan', 'm-news' ),
			__( 'Di sisi kanan konten, hanya layar sangat lebar (≥1560 px). Ukuran disarankan: 160×600 (rasio 4:15).', 'm-news' ),
		),
		'home-top'             => array(
			__( 'Home – Atas', 'm-news' ),
			__( 'Lebar penuh di atas konten home, biasanya Slider Headline.', 'm-news' ),
		),
		'home-main'            => array(
			__( 'Home – Konten Utama', 'm-news' ),
			__( 'Kolom kiri halaman depan. Urutan widget = urutan tampil. Isi dengan Daftar Berita, Blok Kategori, dan Banner Iklan (mis. 300×250 / 728×90).', 'm-news' ),
		),
		'home-sidebar'         => array(
			__( 'Home – Sidebar', 'm-news' ),
			__( 'Sidebar kanan halaman depan (lebar 300 px). Banner disarankan 300×250 atau 300×600.', 'm-news' ),
		),
		'single-sidebar'       => array(
			__( 'Artikel – Sidebar', 'm-news' ),
			__( 'Sidebar kanan halaman artikel (lebar 300 px). Bila dikosongkan, memakai widget dari "Home – Sidebar". Banner disarankan 300×250 atau 300×600.', 'm-news' ),
		),
		'single-in-article'    => array(
			__( 'Artikel – Di Dalam Konten', 'm-news' ),
			__( 'Disisipkan setelah paragraf tertentu di dalam artikel. Ukuran disarankan: 300×250 atau 336×280.', 'm-news' ),
		),
		'single-after-content' => array(
			__( 'Artikel – Setelah Konten', 'm-news' ),
			__( 'Di bawah isi artikel. Ukuran disarankan: 728×90 atau 300×250.', 'm-news' ),
		),
		'footer-1'             => array( __( 'Footer 1', 'm-news' ), __( 'Kolom footer pertama. Jumlah kolom yang tampil diatur di Sesuaikan > Footer.', 'm-news' ) ),
		'footer-2'             => array( __( 'Footer 2', 'm-news' ), __( 'Kolom footer kedua.', 'm-news' ) ),
		'footer-3'             => array( __( 'Footer 3', 'm-news' ), __( 'Kolom footer ketiga.', 'm-news' ) ),
		'footer-4'             => array( __( 'Footer 4', 'm-news' ), __( 'Kolom footer keempat (hanya tampil bila "Jumlah kolom footer" diatur ke 4).', 'm-news' ) ),
	);

	foreach ( $areas as $id => $data ) {
		register_sidebar(
			array_merge(
				mnews_widget_args(),
				array(
					'id'          => $id,
					'name'        => $data[0],
					'description' => $data[1],
				)
			)
		);
	}
}
add_action( 'widgets_init', 'mnews_register_widget_areas' );

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
function mnews_area( $id, $classes = '' ) {
	if ( ! is_active_sidebar( $id ) ) {
		return false;
	}
	echo '<div class="' . esc_attr( trim( 'mnw-area mnw-area--' . $id . ' ' . $classes ) ) . '">';
	dynamic_sidebar( $id );
	echo '</div>';
	return true;
}

/**
 * Left/right 160x600 banners (wide screens only). Call once inside a position:relative container.
 * Each side is a full-height column; the inner box sticks while scrolling (Customizer can turn that off).
 */
function mnews_side_banners() {
	foreach ( array( 'left', 'right' ) as $side ) {
		$area = 'side-banner-' . $side;
		if ( ! is_active_sidebar( $area ) ) {
			continue;
		}
		echo '<div class="mnw-side mnw-side--' . esc_attr( $side ) . '"><div class="mnw-side__in">';
		mnews_area( $area );
		echo '</div></div>';
	}
}

/**
 * Footer widget columns. The number of columns (and so how many of footer-1..4 are even eligible to show)
 * is chosen in Customizer > M-News > Footer > "Jumlah kolom footer" — an empty column never renders regardless
 * (is_active_sidebar()), this only caps how many are considered.
 */
function mnews_footer_widgets() {
	$max  = max( 2, min( 4, absint( get_theme_mod( 'mnews_footer_cols', 3 ) ) ) );
	$ids  = array_slice( array( 'footer-1', 'footer-2', 'footer-3', 'footer-4' ), 0, $max );
	$cols = array_filter( $ids, 'is_active_sidebar' );
	if ( ! $cols ) {
		return;
	}
	printf( '<div class="mnw-footer__widgets" style="--mnw-footer-cols:%d">', (int) $max );
	foreach ( $cols as $id ) {
		mnews_area( $id, 'mnw-footer__col' );
	}
	echo '</div>';
}

/**
 * On a "latest posts" front page the widgets run their own queries, so shrink
 * the unused main query to one cheap post (no counting, no meta/term priming).
 *
 * @param WP_Query $query Query.
 */
function mnews_lean_home_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_home() || ! $query->is_front_page() || $query->is_feed() ) {
		return;
	}
	$query->set( 'posts_per_page', 1 );
	$query->set( 'no_found_rows', true );
	$query->set( 'update_post_meta_cache', false );
	$query->set( 'update_post_term_cache', false );
}
add_action( 'pre_get_posts', 'mnews_lean_home_query' );
