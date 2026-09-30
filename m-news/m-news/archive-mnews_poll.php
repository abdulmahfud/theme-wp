<?php
/**
 * Polling archive: grid of poll cards.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

get_header();
mnews_enqueue_poll();
?>
<main id="mnw-main" class="mnw-main">
	<div class="mnw-container mnw-layout">
		<div class="mnw-layout__main">
			<header class="mnw-archive-head">
				<span class="mnw-archive-kicker"><?php esc_html_e( 'Polling', 'm-news' ); ?></span>
				<h1 class="mnw-archive-title"><?php esc_html_e( 'Polling', 'm-news' ); ?></h1>
			</header>

			<?php if ( have_posts() ) : ?>
				<div class="mnw-poll-grid">
					<?php
					while ( have_posts() ) :
						the_post();
						mnews_render_poll_card( get_the_ID() );
					endwhile;
					?>
				</div>
				<?php
				the_posts_pagination(
					array(
						'mid_size'  => 1,
						'prev_text' => __( 'Sebelumnya', 'm-news' ),
						'next_text' => __( 'Selanjutnya', 'm-news' ),
					)
				);
				?>
			<?php else : ?>
				<?php get_template_part( 'template-parts/content', 'none' ); ?>
			<?php endif; ?>
		</div>

		<?php mnews_sidebar( 'home-sidebar' ); ?>
	</div>
</main>
<?php
get_footer();
