<?php
/**
 * Empty state: nothing found. Offers a search box and the latest posts.
 *
 * @package M_Nata
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="mn-none">
	<p>
		<?php
		echo esc_html(
			is_search()
				? __( 'Maaf, tidak ada berita yang cocok dengan kata kunci Anda. Coba kata kunci lain.', 'm-nata' )
				: __( 'Belum ada berita di bagian ini.', 'm-nata' )
		);
		?>
	</p>
	<?php if ( ! is_search() ) : ?>
		<?php get_search_form(); ?>
	<?php endif; ?>
	<?php
	the_widget(
		'MNata_Widget_Post_List',
		array(
			'title'  => __( 'Berita Terbaru', 'm-nata' ),
			'layout' => 'list',
			'count'  => 5,
		),
		mnata_widget_args( true )
	);
	?>
</section>
