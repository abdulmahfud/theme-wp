<?php
/**
 * Empty state: nothing found. Offers a search box and the latest posts.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="mnw-none">
	<p>
		<?php
		echo esc_html(
			is_search()
				? __( 'Maaf, tidak ada berita yang cocok dengan kata kunci Anda. Coba kata kunci lain.', 'm-news' )
				: __( 'Belum ada berita di bagian ini.', 'm-news' )
		);
		?>
	</p>
	<?php if ( ! is_search() ) : ?>
		<?php get_search_form(); ?>
	<?php endif; ?>
	<?php
	the_widget(
		'MNews_Widget_Post_List',
		array(
			'title'  => __( 'Berita Terbaru', 'm-news' ),
			'layout' => 'list',
			'count'  => 5,
		),
		mnews_widget_args( true )
	);
	?>
</section>
