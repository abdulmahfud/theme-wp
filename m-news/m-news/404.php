<?php
/**
 * 404: friendly message, search box and latest news.
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
				<span class="mnw-archive-kicker">404</span>
				<h1 class="mnw-archive-title"><?php esc_html_e( 'Halaman tidak ditemukan', 'm-news' ); ?></h1>
				<p class="mnw-archive-desc"><?php esc_html_e( 'Tautan yang Anda buka mungkin salah atau beritanya sudah dipindahkan. Coba cari berita yang Anda maksud:', 'm-news' ); ?></p>
				<?php get_search_form(); ?>
			</header>
			<?php
			the_widget(
				'MNews_Widget_Post_List',
				array(
					'title'  => __( 'Berita Terbaru', 'm-news' ),
					'layout' => 'list',
					'count'  => 6,
				),
				mnews_widget_args( true )
			);
			?>
		</div>

		<?php mnews_sidebar( 'home-sidebar' ); ?>
	</div>
</main>
<?php
get_footer();
