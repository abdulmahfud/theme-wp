<?php
/**
 * Static pages (Tentang Kami, Redaksi, Pedoman Media Siber, ...): a single reading column.
 *
 * @package M_Nata
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="mn-main" class="mn-main">
	<div class="mn-container mn-page">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article id="post-<?php the_ID(); ?>" class="mn-article">
				<h1 class="mn-article__title"><?php the_title(); ?></h1>

				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="mn-article__figure">
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

				<div class="mn-content entry-content">
					<?php
					the_content();
					wp_link_pages(
						array(
							'before' => '<nav class="mn-page-links" aria-label="' . esc_attr__( 'Halaman', 'm-nata' ) . '"><span>' . esc_html__( 'Halaman:', 'm-nata' ) . '</span>',
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
