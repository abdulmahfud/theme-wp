<?php
/**
 * Template Name: Trending
 *
 * Assign this to any page (Halaman > Atribut Halaman > Templat) to give the bottom nav's "Trending"
 * item somewhere real to go: a paginated feed ordered by popularity (see mnews_query_args()'s "popular" order).
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="mnw-main" class="mnw-main">
	<div class="mnw-container mnw-layout">
		<div class="mnw-layout__main">
			<header class="mnw-archive-head">
				<h1 class="mnw-archive-title"><?php the_title(); ?></h1>
			</header>
			<?php
			$mnews_trending = mnews_run_query(
				mnews_query_args(
					array(
						'count'    => 12,
						'order'    => 'popular',
						'paginate' => true,
						'show_cat' => true,
					)
				)
			);
			if ( $mnews_trending->have_posts() ) :
				mnews_render_posts( $mnews_trending, array( 'layout' => 'list' ) );
				mnews_render_pagination( $mnews_trending );
			else :
				get_template_part( 'template-parts/content', 'none' );
			endif;
			wp_reset_postdata();
			?>
		</div>

		<?php mnews_sidebar( 'home-sidebar' ); ?>
	</div>
</main>
<?php
get_footer();
