<?php
/**
 * Shared post-list renderer used by every widget that shows news.
 * Adding a layout = one template part + one entry in mnata_post_layouts().
 *
 * @package M_Nata
 */

defined( 'ABSPATH' ) || exit;

/**
 * Available layouts: slug => array( label, card template suffix ).
 *
 * @return array<string,array{0:string,1:string}>
 */
function mnata_post_layouts() {
	return array(
		'list'     => array( __( 'Daftar (gambar kiri)', 'm-nata' ), 'list' ),
		'compact'  => array( __( 'Ringkas (gambar kecil)', 'm-nata' ), 'compact' ),
		'numbered' => array( __( 'Bernomor / Trending', 'm-nata' ), 'numbered' ),
		'grid-2'   => array( __( 'Grid 2 kolom', 'm-nata' ), 'grid' ),
		'grid-3'   => array( __( 'Grid 3 kolom', 'm-nata' ), 'grid' ),
		'featured' => array( __( '1 besar + daftar ringkas', 'm-nata' ), 'compact' ),
	);
}

/**
 * Layout choices for a select field.
 *
 * @return array<string,string>
 */
function mnata_post_layout_choices() {
	$out = array();
	foreach ( mnata_post_layouts() as $slug => $data ) {
		$out[ $slug ] = $data[0];
	}
	return $out;
}

/**
 * Echo <img> for the post thumbnail (or nothing).
 *
 * @param string $size  Image size.
 * @param bool   $lcp   Whether this is the likely LCP image (eager + high priority).
 * @param string $sizes Value for the sizes attribute.
 */
function mnata_thumb( $size, $lcp = false, $sizes = '' ) {
	if ( ! has_post_thumbnail() ) {
		return;
	}
	// Card/slide images always sit next to the headline text, so they are decorative (empty alt avoids a screen reader reading the title twice).
	$attr = array(
		'alt'      => '',
		'decoding' => 'async',
		'loading'  => $lcp ? 'eager' : 'lazy',
	);
	if ( $lcp ) {
		$attr['fetchpriority'] = 'high';
	}
	if ( $sizes ) {
		$attr['sizes'] = $sizes;
	}
	the_post_thumbnail( $size, $attr );
}

/**
 * Print the posts of a query in a layout.
 *
 * @param WP_Query $query Query.
 * @param array    $args  layout, show_cat, show_date, lcp (first image is LCP).
 */
function mnata_render_posts( $query, $args = array() ) {
	$a = wp_parse_args(
		$args,
		array(
			'layout'      => 'list',
			'show_cat'    => true,
			'show_date'   => true,
			'lcp'         => false,
			'title_lines' => 0,
		)
	);

	$layouts = mnata_post_layouts();
	$layout  = isset( $layouts[ $a['layout'] ] ) ? $a['layout'] : 'list';
	$card    = $layouts[ $layout ][1];

	// Title length limit (visual, CSS line-clamp). 0 = the layout's default.
	$defaults = array(
		'list'     => 3,
		'compact'  => 2,
		'numbered' => 2,
		'grid-2'   => 2,
		'grid-3'   => 2,
		'featured' => 2,
	);
	$lines    = (int) $a['title_lines'] > 0 ? min( 6, (int) $a['title_lines'] ) : $defaults[ $layout ];

	printf( '<div class="mn-posts mn-posts--%1$s" style="--mn-lines:%2$d">', esc_attr( $layout ), (int) $lines );

	$i = 0;
	while ( $query->have_posts() ) {
		$query->the_post();
		++$i;

		$slug = ( 'featured' === $layout && 1 === $i ) ? 'overlay' : $card;
		get_template_part(
			'template-parts/card',
			$slug,
			array(
				'index'     => $i,
				'show_cat'  => (bool) $a['show_cat'],
				'show_date' => (bool) $a['show_date'],
				'lcp'       => $a['lcp'] && 1 === $i,
			)
		);
	}
	wp_reset_postdata();

	echo '</div>';
}

/**
 * Run a widget query and prime thumbnail caches in one batch (avoids 2+ queries per image).
 *
 * @param array $args WP_Query args.
 * @return WP_Query
 */
function mnata_run_query( $args ) {
	$query = new WP_Query( $args );
	if ( $query->have_posts() ) {
		update_post_thumbnail_cache( $query );
	}
	return $query;
}

/**
 * Build WP_Query args from widget settings.
 *
 * @param array $s Settings: count, category, tag, order, paginate, show_cat.
 * @return array
 */
function mnata_query_args( $s ) {
	$args = array(
		'post_type'              => 'post',
		'post_status'            => 'publish',
		'posts_per_page'         => max( 1, (int) $s['count'] ),
		'ignore_sticky_posts'    => true,
		'no_found_rows'          => empty( $s['paginate'] ),
		'update_post_meta_cache' => true, // One query for all posts; avoids a meta query per thumbnail.
		'update_post_term_cache' => ! empty( $s['show_cat'] ),
	);

	if ( ! empty( $s['category'] ) ) {
		$args['cat'] = (int) $s['category'];
	}
	if ( ! empty( $s['tag'] ) ) {
		$args['tag'] = $s['tag'];
	}

	$order = isset( $s['order'] ) ? $s['order'] : 'latest';
	if ( 'popular' === $order ) {
		$args['orderby'] = array(
			'comment_count' => 'DESC',
			'date'          => 'DESC',
		);
	} elseif ( 'random' === $order ) {
		$args['orderby'] = 'rand';
	}

	if ( ! empty( $s['paginate'] ) ) {
		$args['paged'] = max( 1, (int) get_query_var( 'paged' ) );
	}

	return $args;
}

/**
 * Echo numbered pagination for a widget query.
 *
 * @param WP_Query $query Query.
 */
function mnata_render_pagination( $query ) {
	$total = (int) $query->max_num_pages;
	if ( $total < 2 ) {
		return;
	}
	$links = paginate_links(
		array(
			'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
			'format'    => '',
			'current'   => max( 1, (int) get_query_var( 'paged' ) ),
			'total'     => $total,
			'mid_size'  => 1,
			'prev_text' => __( 'Sebelumnya', 'm-nata' ),
			'next_text' => __( 'Selanjutnya', 'm-nata' ),
		)
	);
	if ( $links ) {
		printf(
			'<nav class="navigation pagination" aria-label="%1$s"><div class="nav-links">%2$s</div></nav>',
			esc_attr__( 'Halaman berita', 'm-nata' ),
			$links // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- generated by paginate_links().
		);
	}
}

/**
 * Enqueue the slider script (call from a widget's assets() so it also runs on cache hits).
 */
function mnata_enqueue_slider() {
	if ( mnata_is_amp() ) {
		return;
	}
	wp_enqueue_script(
		'mnata-slider',
		mnata_asset( 'assets/js/slider.js' )[0],
		array(),
		mnata_asset( 'assets/js/slider.js' )[1],
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);
}

/**
 * Print a slider/carousel for the posts of a query.
 *
 * @param WP_Query $query Query (thumbnail cache already primed).
 * @param array    $a     model (thumbs|full|carousel), show_cat, autoplay.
 */
function mnata_render_slider( $query, $a = array() ) {
	$a = wp_parse_args(
		$a,
		array(
			'model'       => 'thumbs',
			'show_cat'    => true,
			'autoplay'    => true,
			'title_lines' => 2,
		)
	);

	$model  = $a['model'];
	$slide  = ( 'carousel' === $model ) ? 'mnata-card' : 'mnata-hero';
	$sizes  = ( 'carousel' === $model ) ? '(min-width:768px) 33vw, 80vw' : '(min-width:992px) 780px, 100vw';
	$thumbs = array();

	printf(
		'<div class="mn-slider mn-slider--%1$s" style="--mn-lines:%4$d" data-mn-slider data-autoplay="%2$d" role="region"%5$s aria-label="%3$s">',
		esc_attr( $model ),
		$a['autoplay'] ? 5000 : 0,
		esc_attr__( 'Berita utama', 'm-nata' ),
		absint( max( 1, min( 6, (int) $a['title_lines'] ) ) ),
		mnata_is_amp() ? '' : ' aria-roledescription="carousel"' // Not allowed on AMP pages.
	);
	echo '<div class="mn-slider__stage"><div class="mn-slider__track">';

	$n = 0;
	while ( $query->have_posts() ) {
		$query->the_post();
		++$n;
		$cat = $a['show_cat'] ? mnata_category_label() : '';
		?>
		<article class="mn-slide">
			<a class="mn-slide__link" href="<?php the_permalink(); ?>">
				<?php mnata_thumb( $slide, 1 === $n && 'carousel' !== $model, $sizes ); ?>
				<span class="mn-slide__cap">
					<?php if ( $cat ) : ?>
						<span class="mn-slide__cat"><?php echo $cat; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in mnata_category_label(). ?></span>
					<?php endif; ?>
					<span class="mn-slide__title"><?php the_title(); ?></span>
				</span>
			</a>
		</article>
		<?php
		if ( 'thumbs' === $model ) {
			ob_start();
			mnata_thumb( 'mnata-thumb' );
			$thumbs[] = array(
				'img'   => ob_get_clean(),
				'title' => get_the_title(),
			);
		}
	}
	wp_reset_postdata();
	echo '</div>';

	if ( $n > 1 ) {
		foreach ( array(
			-1 => 'chevron-left',
			1  => 'chevron-right',
		) as $dir => $icon ) {
			printf(
				'<button type="button" class="mn-slider__nav mn-slider__nav--%1$s" data-dir="%2$d" aria-label="%3$s">',
				$dir < 0 ? 'prev' : 'next',
				(int) $dir,
				$dir < 0 ? esc_attr__( 'Sebelumnya', 'm-nata' ) : esc_attr__( 'Selanjutnya', 'm-nata' )
			);
			mnata_icon( $icon, 22 );
			echo '</button>';
		}
	}
	echo '</div>'; // .mn-slider__stage

	if ( $n > 1 && 'carousel' !== $model ) {
		echo '<div class="mn-slider__dots">';
		for ( $d = 0; $d < $n; $d++ ) {
			/* translators: %d: slide number. */
			printf( '<button type="button" data-slide="%1$d" aria-label="%2$s"></button>', (int) $d, esc_attr( sprintf( __( 'Slide %d', 'm-nata' ), $d + 1 ) ) );
		}
		echo '</div>';
	}

	if ( 'thumbs' === $model && $thumbs ) {
		echo '<div class="mn-slider__thumbs">';
		foreach ( $thumbs as $d => $t ) {
			printf(
				'<button type="button" data-slide="%1$d"><span class="mn-slider__thumb">%2$s</span><span class="mn-slider__thumbtitle">%3$s</span></button>',
				(int) $d,
				$t['img'], // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-generated <img>.
				esc_html( $t['title'] )
			);
		}
		echo '</div>';
	}

	echo '</div>';
}
