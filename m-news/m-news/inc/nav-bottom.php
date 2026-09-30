<?php
/**
 * Mobile bottom navigation bar: Beranda, Trending, Cari, Bagikan, Polling.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

/**
 * URL of the first published page using the "Trending" page template, cached (invalidated on any post/page
 * change like everything else in inc/cache.php). Falls back to the home page if no such page exists yet.
 *
 * @return string
 */
function mnews_trending_url() {
	return mnews_cache_remember(
		'trending_url',
		DAY_IN_SECONDS,
		static function () {
			$pages = get_posts(
				array(
					'post_type'              => 'page',
					'posts_per_page'         => 1,
					'no_found_rows'          => true,
					'update_post_term_cache' => false,
					'meta_key'               => '_wp_page_template', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- tiny admin-configured lookup, cached a full day.
					'meta_value'             => 'template-trending.php', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
				)
			);
			return $pages ? get_permalink( $pages[0] ) : home_url( '/' );
		}
	);
}

/**
 * Print the bar. Mobile only (CSS), fixed to the bottom, above everything else.
 */
function mnews_nav_bottom() {
	if ( mnews_is_amp() ) {
		return; // Keep it simple on AMP; the header search/menu already covers navigation there.
	}
	$items = array(
		array( 'home', __( 'Beranda', 'm-news' ), home_url( '/' ), false ),
		array( 'trending', __( 'Trending', 'm-news' ), mnews_trending_url(), false ),
		array( 'search', __( 'Cari', 'm-news' ), '#mnw-nav', true ),
		array( 'share', __( 'Bagikan', 'm-news' ), '', false ),
		array( 'poll', __( 'Polling', 'm-news' ), get_post_type_archive_link( 'mnews_poll' ), false ),
	);
	echo '<nav class="mnw-navbottom" aria-label="' . esc_attr__( 'Navigasi bawah', 'm-news' ) . '">';
	foreach ( $items as $item ) {
		list( $icon, $label, $url, $is_toggle ) = $item;
		if ( 'share' === $icon ) {
			printf(
				'<button type="button" class="mnw-navbottom__item" data-mnw-share aria-label="%1$s">',
				esc_attr( $label )
			);
		} else {
			printf(
				'<a class="mnw-navbottom__item" href="%1$s"%2$s aria-label="%3$s">',
				esc_url( $url ),
				$is_toggle ? ' data-mnw-nav-toggle aria-controls="mnw-nav" aria-expanded="false"' : '',
				esc_attr( $label )
			);
		}
		mnews_icon( $icon, 24 );
		echo '<span class="mnw-navbottom__label">' . esc_html( $label ) . '</span>';
		echo ( 'share' === $icon ) ? '</button>' : '</a>';
	}
	echo '</nav>';
}
add_action( 'wp_footer', 'mnews_nav_bottom', 4 ); // Before mnews_print_sprite() (priority 5, inc/icons.php) so its icons make it into the sprite.

/**
 * Reserve space at the bottom of the viewport for the bar (mobile only, done in CSS via this body class).
 *
 * @param string[] $classes Classes.
 * @return string[]
 */
function mnews_nav_bottom_body_class( $classes ) {
	if ( ! mnews_is_amp() ) {
		$classes[] = 'mnw-has-navbottom';
	}
	return $classes;
}
add_filter( 'body_class', 'mnews_nav_bottom_body_class' );
