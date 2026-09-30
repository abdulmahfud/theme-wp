<?php
/**
 * 404: friendly message, search box and latest news.
 *
 * @package M_Nata
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="mn-main" class="mn-main">
	<div class="mn-container mn-layout">
		<div class="mn-layout__main">
			<header class="mn-archive-head">
				<span class="mn-archive-kicker">404</span>
				<h1 class="mn-archive-title"><?php esc_html_e( 'Halaman tidak ditemukan', 'm-nata' ); ?></h1>
				<p class="mn-archive-desc"><?php esc_html_e( 'Tautan yang Anda buka mungkin salah atau beritanya sudah dipindahkan. Coba cari berita yang Anda maksud:', 'm-nata' ); ?></p>
				<?php get_search_form(); ?>
			</header>
			<?php
			the_widget(
				'MNata_Widget_Post_List',
				array(
					'title'  => __( 'Berita Terbaru', 'm-nata' ),
					'layout' => 'list',
					'count'  => 6,
				),
				mnata_widget_args( true )
			);
			?>
		</div>

		<?php mnata_sidebar( 'home-sidebar' ); ?>
	</div>
</main>
<?php
get_footer();
