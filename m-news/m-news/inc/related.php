<?php
/**
 * Related posts: shared tag/category first, topped up with the latest posts. IDs are cached.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

/**
 * IDs of related posts.
 *
 * @param int $post_id Current post.
 * @param int $limit   How many.
 * @return int[]
 */
function mnews_related_ids( $post_id, $limit = 6 ) {
	$post_id = (int) $post_id;
	$limit   = max( 1, (int) $limit );

	return mnews_cache_remember(
		"related|{$post_id}|{$limit}",
		30 * MINUTE_IN_SECONDS,
		static function () use ( $post_id, $limit ) {
			$base = array(
				'post_type'              => 'post',
				'post_status'            => 'publish',
				'posts_per_page'         => $limit,
				'fields'                 => 'ids',
				'ignore_sticky_posts'    => true,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			);

			$cats = wp_get_post_categories( $post_id, array( 'fields' => 'ids' ) );
			$tags = wp_get_post_tags( $post_id, array( 'fields' => 'ids' ) );

			$tax = array();
			if ( $tags ) {
				$tax[] = array(
					'taxonomy' => 'post_tag',
					'field'    => 'term_id',
					'terms'    => $tags,
				);
			}
			if ( $cats ) {
				$tax[] = array(
					'taxonomy' => 'category',
					'field'    => 'term_id',
					'terms'    => $cats,
				);
			}

			$ids = array();
			if ( $tax ) {
				$q   = new WP_Query(
					array_merge(
						$base,
						array(
							'post__not_in' => array( $post_id ),
							'tax_query'    => array_merge( array( 'relation' => 'OR' ), $tax ), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
						)
					)
				);
				$ids = array_map( 'intval', $q->posts );
			}

			if ( count( $ids ) < $limit ) {
				$q   = new WP_Query(
					array_merge(
						$base,
						array(
							'posts_per_page' => $limit - count( $ids ),
							'post__not_in'   => array_merge( array( $post_id ), $ids ),
						)
					)
				);
				$ids = array_merge( $ids, array_map( 'intval', $q->posts ) );
			}

			return $ids;
		}
	);
}

/**
 * "Baca Juga" box HTML (one related headline), or an empty string.
 *
 * @return string
 */
function mnews_readmore_html() {
	$ids = mnews_related_ids( get_the_ID(), 3 );
	if ( empty( $ids ) ) {
		return '';
	}
	$id = $ids[0];
	return sprintf(
		'<p class="mnw-readmore"><strong>%1$s</strong> <a href="%2$s">%3$s</a></p>',
		esc_html__( 'Baca Juga:', 'm-news' ),
		esc_url( get_permalink( $id ) ),
		esc_html( get_the_title( $id ) )
	);
}

/**
 * Echo the "Berita Terkait" grid under an article (cached as HTML).
 */
function mnews_related_section() {
	if ( ! get_theme_mod( 'mnews_related_on', true ) ) {
		return;
	}
	$count = max( 3, min( 12, (int) get_theme_mod( 'mnews_related_count', 6 ) ) );
	$title = (string) get_theme_mod( 'mnews_related_title', __( 'Berita Terkait', 'm-news' ) );
	$post  = get_the_ID();

	mnews_cache_fragment(
		"related_html|{$post}|{$count}|{$title}",
		30 * MINUTE_IN_SECONDS,
		static function () use ( $post, $count, $title ) {
			mnews_related_section_html( $post, $count, $title );
		}
	);
}

/**
 * Print the related grid.
 *
 * @param int    $post_id Current post.
 * @param int    $count   Number of posts.
 * @param string $title   Block title.
 */
function mnews_related_section_html( $post_id, $count, $title ) {
	$ids = mnews_related_ids( $post_id, $count );
	if ( empty( $ids ) ) {
		return;
	}

	$query = mnews_run_query(
		array(
			'post_type'              => 'post',
			'post__in'               => $ids,
			'orderby'                => 'post__in',
			'posts_per_page'         => $count,
			'ignore_sticky_posts'    => true,
			'no_found_rows'          => true,
			'update_post_meta_cache' => true,
			'update_post_term_cache' => true,
		)
	);
	if ( ! $query->have_posts() ) {
		return;
	}

	echo '<section class="mnw-related">';
	printf( '<h2 class="mnw-block-title">%s</h2>', esc_html( $title ) );
	mnews_render_posts(
		$query,
		array(
			'layout'    => 'grid-3',
			'show_cat'  => true,
			'show_date' => true,
		)
	);
	echo '</section>';
}
