<?php
/**
 * Template tags used by header, footer and cards.
 *
 * @package M_Nata
 */

defined( 'ABSPATH' ) || exit;

/**
 * Social networks supported for follow links (id => label).
 *
 * @return array<string,string>
 */
function mnata_social_networks() {
	return array(
		'facebook'  => 'Facebook',
		'x'         => 'X',
		'instagram' => 'Instagram',
		'youtube'   => 'YouTube',
		'tiktok'    => 'TikTok',
		'linkedin'  => 'LinkedIn',
		'pinterest' => 'Pinterest',
		'whatsapp'  => 'WhatsApp',
		'telegram'  => 'Telegram',
	);
}

/**
 * Echo the follow links that have a URL in the Customizer.
 *
 * @param string $classes Extra classes for the list.
 */
function mnata_social_links( $classes = 'mn-social' ) {
	$items = '';
	foreach ( mnata_social_networks() as $id => $label ) {
		$url = get_theme_mod( 'mnata_social_' . $id, '' );
		if ( ! $url ) {
			continue;
		}
		ob_start();
		mnata_icon( $id );
		$icon   = ob_get_clean();
		$items .= sprintf(
			'<li><a href="%1$s" target="_blank" rel="noopener noreferrer" aria-label="%2$s">%3$s</a></li>',
			esc_url( $url ),
			esc_attr( $label ),
			$icon // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built by mnata_icon().
		);
	}
	if ( '' === $items ) {
		return;
	}
	printf( '<ul class="%s">%s</ul>', esc_attr( $classes ), $items ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
}

/**
 * Echo the site logo, or the site title as a fallback. On the front page (header only) the
 * wrapper is the page's <h1>, since the home page has no other heading.
 *
 * @param bool $heading Whether this instance may be the <h1> (false in the footer).
 */
function mnata_logo( $heading = true ) {
	$tag = ( $heading && is_front_page() ) ? 'h1' : 'div';
	printf( '<%s class="mn-logo">', esc_attr( $tag ) );
	if ( has_custom_logo() ) {
		the_custom_logo();
		if ( 'h1' === $tag ) {
			printf( '<span class="screen-reader-text">%s</span>', esc_html( get_bloginfo( 'name' ) ) );
		}
	} else {
		printf(
			'<a class="mn-site-title" href="%1$s" rel="home">%2$s</a>',
			esc_url( home_url( '/' ) ),
			esc_html( get_bloginfo( 'name' ) )
		);
	}
	printf( '</%s>', esc_attr( $tag ) );
}

/**
 * Breaking-news ticker (cached). Disabled by default; see Customizer > M-Nata > Header.
 */
function mnata_ticker() {
	if ( ! get_theme_mod( 'mnata_ticker_on', false ) ) {
		return;
	}

	$tag   = sanitize_title( (string) get_theme_mod( 'mnata_ticker_tag', '' ) );
	$count = max( 1, min( 10, absint( get_theme_mod( 'mnata_ticker_count', 5 ) ) ) );

	$items = mnata_cache_remember(
		"ticker|{$tag}|{$count}",
		5 * MINUTE_IN_SECONDS,
		static function () use ( $tag, $count ) {
			$args = array(
				'posts_per_page'         => $count,
				'ignore_sticky_posts'    => true,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			);
			if ( $tag ) {
				$args['tag'] = $tag;
			}
			$q   = new WP_Query( $args );
			$out = array();
			foreach ( $q->posts as $p ) {
				$out[] = array(
					'title' => get_the_title( $p ),
					'url'   => get_permalink( $p ),
				);
			}
			return $out;
		}
	);

	if ( empty( $items ) ) {
		return;
	}

	$label = get_theme_mod( 'mnata_ticker_label', __( 'Terbaru', 'm-nata' ) );
	?>
	<div class="mn-ticker">
		<div class="mn-container mn-ticker__in">
			<span class="mn-ticker__label"><?php echo esc_html( $label ); ?></span>
			<div class="mn-ticker__track">
				<div class="mn-ticker__list">
					<?php foreach ( $items as $item ) : ?>
						<a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['title'] ); ?></a>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
	<?php
}

/**
 * First category of the current post as a text label.
 *
 * @param int|null $post_id Post ID.
 * @return string Escaped label or empty string.
 */
function mnata_category_label( $post_id = null ) {
	$cats = get_the_category( $post_id );
	return $cats ? esc_html( $cats[0]->name ) : '';
}

/**
 * Echo publish date/time with the site timezone label.
 */
function mnata_posted_on() {
	$label = get_theme_mod( 'mnata_tz_label', 'WIB' );
	$time  = sprintf(
		'<time datetime="%1$s">%2$s</time>',
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date( 'j M Y' ) . ', ' . get_the_time( 'H:i' ) . ( $label ? ' ' . $label : '' ) )
	);
	echo $time; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
}

/**
 * Echo the sidebar column: the given widget area, or a default "Trending" list when it is empty.
 *
 * @param string $area Widget area id.
 */
function mnata_sidebar( $area = 'home-sidebar' ) {
	echo '<aside class="mn-layout__side" aria-label="' . esc_attr__( 'Sidebar', 'm-nata' ) . '">';
	// Article sidebar falls back to the home sidebar, so a new site shows the same widgets everywhere.
	if ( ! mnata_area( $area ) && ( 'home-sidebar' === $area || ! mnata_area( 'home-sidebar' ) ) ) {
		the_widget(
			'MNata_Widget_Post_List',
			array(
				'title'     => __( 'Trending', 'm-nata' ),
				'layout'    => 'numbered',
				'order'     => 'popular',
				'count'     => 10,
				'show_cat'  => 0,
				'show_date' => 0,
			),
			mnata_widget_args( true )
		);
	}
	echo '</aside>';
}
