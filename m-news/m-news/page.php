<?php
/**
 * Static pages (Tentang Kami, Redaksi, Pedoman Media Siber, ...): a single reading column.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="mnw-main" class="mnw-main">
	<div class="mnw-container mnw-page">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article id="post-<?php the_ID(); ?>" class="mnw-article">
				<h1 class="mnw-article__title"><?php the_title(); ?></h1>

				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="mnw-article__figure">
						<?php
						the_post_thumbnail(
							'large',
							array(
								'fetchpriority' => 'high',
								'loading'       => 'eager',
								'decoding'      => 'async',
							)
						);
						?>
					</figure>
				<?php endif; ?>

				<div class="mnw-content entry-content">
					<?php
					the_content();
					wp_link_pages(
						array(
							'before' => '<nav class="mnw-page-links" aria-label="' . esc_attr__( 'Halaman', 'm-news' ) . '"><span>' . esc_html__( 'Halaman:', 'm-news' ) . '</span>',
							'after'  => '</nav>',
						)
					);
					?>
				</div>
			</article>

			<?php
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
		endwhile;
		?>
	</div>
</main>
<?php
get_footer();
