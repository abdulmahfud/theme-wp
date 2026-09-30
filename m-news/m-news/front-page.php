<?php
/**
 * Front page: three widget areas (top, main, sidebar). An empty "home-main" gets a sensible default (slider +
 * latest news) so a fresh install already looks complete; an empty "home-sidebar" simply means no sidebar column
 * at all (full width) — unlike single/archive pages, home has no forced fallback here on purpose.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="mnw-main" class="mnw-main">
	<?php mnews_side_banners(); ?>

	<?php if ( is_active_sidebar( 'home-top' ) ) : ?>
		<div class="mnw-container mnw-home-top">
			<?php mnews_area( 'home-top' ); ?>
		</div>
	<?php endif; ?>

	<div class="mnw-container mnw-layout">
		<div class="mnw-layout__main">
			<?php
			if ( ! mnews_area( 'home-main' ) ) {
				the_widget(
					'MNews_Widget_Slider',
					array(
						'model' => 'thumbs',
						'count' => 4,
					),
					mnews_widget_args( true )
				);
				the_widget(
					'MNews_Widget_Post_List',
					array(
						'title'    => __( 'Berita Terkini', 'm-news' ),
						'layout'   => 'list',
						'count'    => 10,
						'paginate' => 1,
					),
					mnews_widget_args( true )
				);
			}
			?>
		</div>

		<?php if ( is_active_sidebar( 'home-sidebar' ) ) : ?>
			<aside class="mnw-layout__side" aria-label="<?php esc_attr_e( 'Sidebar', 'm-news' ); ?>">
				<?php mnews_area( 'home-sidebar' ); ?>
			</aside>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
