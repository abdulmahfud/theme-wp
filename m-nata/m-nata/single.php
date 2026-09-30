<?php
/**
 * Single post.
 *
 * @package M_Nata
 */

defined( 'ABSPATH' ) || exit;

get_header();

$mnata_position = get_theme_mod( 'mnata_share_position', 'top' );
?>
<main id="mn-main" class="mn-main">
	<?php mnata_side_banners(); ?>

	<div class="mn-container mn-layout">
		<div class="mn-layout__main">
			<?php
			while ( have_posts() ) :
				the_post();
				$mnata_subtitle = (string) get_post_meta( get_the_ID(), '_mnata_subtitle', true );
				$mnata_writer   = (string) get_post_meta( get_the_ID(), '_mnata_writer', true );
				$mnata_editor   = (string) get_post_meta( get_the_ID(), '_mnata_editor', true );
				$mnata_source   = (string) get_post_meta( get_the_ID(), '_mnata_source', true );
				?>
				<article id="post-<?php the_ID(); ?>" class="mn-article">
					<?php mnata_breadcrumb(); ?>

					<h1 class="mn-article__title"><?php the_title(); ?></h1>
					<?php if ( $mnata_subtitle ) : ?>
						<p class="mn-article__dek"><?php echo esc_html( $mnata_subtitle ); ?></p>
					<?php endif; ?>

					<div class="mn-article__meta">
						<?php if ( get_theme_mod( 'mnata_show_author', true ) && get_the_author_meta( 'ID' ) ) : ?>
							<span class="mn-article__by">
								<?php esc_html_e( 'Oleh', 'm-nata' ); ?>
								<a href="<?php echo esc_url( get_author_posts_url( (int) get_the_author_meta( 'ID' ) ) ); ?>"><?php the_author(); ?></a>
							</span>
						<?php endif; ?>
						<span class="mn-article__time"><?php mnata_posted_on(); ?></span>
					</div>

					<?php
					if ( in_array( $mnata_position, array( 'top', 'both' ), true ) ) {
						mnata_share_buttons( 'top' );
					}
					?>

					<?php if ( has_post_thumbnail() ) : ?>
						<figure class="mn-article__figure">
							<?php
							the_post_thumbnail(
								'large',
								array(
									'loading'       => 'eager',
									'fetchpriority' => 'high',
									'decoding'      => 'async',
									'sizes'         => '(min-width:992px) 780px, 100vw',
								)
							);
							?>
							<?php $mnata_caption = get_the_post_thumbnail_caption(); ?>
							<?php if ( $mnata_caption ) : ?>
								<figcaption><?php echo esc_html( $mnata_caption ); ?></figcaption>
							<?php endif; ?>
						</figure>
					<?php endif; ?>

					<div class="mn-content entry-content">
						<?php
						the_content();
						wp_link_pages(
							array(
								'before' => '<nav class="mn-page-links" aria-label="' . esc_attr__( 'Halaman artikel', 'm-nata' ) . '"><span>' . esc_html__( 'Halaman:', 'm-nata' ) . '</span>',
								'after'  => '</nav>',
							)
						);
						?>
					</div>

					<?php if ( get_theme_mod( 'mnata_show_credits', true ) && ( $mnata_writer || $mnata_editor || $mnata_source ) ) : ?>
						<ul class="mn-credits">
							<?php if ( $mnata_writer ) : ?>
								<li><strong><?php esc_html_e( 'Penulis:', 'm-nata' ); ?></strong> <?php echo esc_html( $mnata_writer ); ?></li>
							<?php endif; ?>
							<?php if ( $mnata_editor ) : ?>
								<li><strong><?php esc_html_e( 'Editor:', 'm-nata' ); ?></strong> <?php echo esc_html( $mnata_editor ); ?></li>
							<?php endif; ?>
							<?php if ( $mnata_source ) : ?>
								<li><strong><?php esc_html_e( 'Sumber:', 'm-nata' ); ?></strong> <?php echo esc_html( $mnata_source ); ?></li>
							<?php endif; ?>
						</ul>
					<?php endif; ?>

					<?php $mnata_tags = get_theme_mod( 'mnata_show_tags', true ) ? get_the_tags() : false; ?>
					<?php if ( $mnata_tags ) : ?>
						<ul class="mn-tags mn-tags--article">
							<?php foreach ( $mnata_tags as $mnata_tag ) : ?>
								<li><a href="<?php echo esc_url( get_tag_link( $mnata_tag ) ); ?>">#<?php echo esc_html( $mnata_tag->name ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<?php
					if ( in_array( $mnata_position, array( 'bottom', 'both' ), true ) ) {
						mnata_share_buttons( 'bottom' );
					}
					?>
				</article>

				<?php
				mnata_area( 'single-after-content', 'mn-after-content' );
				mnata_related_section();

				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
			endwhile;
			?>
		</div>

		<?php mnata_sidebar( 'single-sidebar' ); ?>
	</div>

	<?php
	if ( ! mnata_is_amp() && get_theme_mod( 'mnata_share_float', false ) && 'none' !== $mnata_position ) {
		mnata_share_buttons( 'float' );
	}
	?>
</main>
<?php
get_footer();
