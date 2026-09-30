<?php
/**
 * Front page: three widget areas (top, main, sidebar). Empty areas get sensible defaults
 * so a fresh install already looks complete.
 *
 * @package M_Nata
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="mn-main" class="mn-main">
	<?php mnata_side_banners(); ?>

	<?php if ( is_active_sidebar( 'home-top' ) ) : ?>
		<div class="mn-container mn-home-top">
			<?php mnata_area( 'home-top' ); ?>
		</div>
	<?php endif; ?>

	<div class="mn-container mn-layout">
		<div class="mn-layout__main">
			<?php
			if ( ! mnata_area( 'home-main' ) ) {
				the_widget(
					'MNata_Widget_Slider',
					array(
						'model' => 'thumbs',
						'count' => 4,
					),
					mnata_widget_args( true )
				);
				the_widget(
					'MNata_Widget_Post_List',
					array(
						'title'    => __( 'Berita Terkini', 'm-nata' ),
						'layout'   => 'list',
						'count'    => 10,
						'paginate' => 1,
					),
					mnata_widget_args( true )
				);
			}
			?>
		</div>

		<?php mnata_sidebar( 'home-sidebar' ); ?>
	</div>
</main>
<?php
get_footer();
