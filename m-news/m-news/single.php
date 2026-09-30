<?php
/**
 * Single post.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

get_header();

$mnews_position = get_theme_mod( 'mnews_share_position', 'top' );
?>
<main id="mnw-main" class="mnw-main">
	<?php mnews_side_banners(); ?>

	<div class="mnw-container mnw-layout">
		<div class="mnw-layout__main">
			<?php
			while ( have_posts() ) :
				the_post();
				$mnews_subtitle = (string) get_post_meta( get_the_ID(), '_mnews_subtitle', true );
				$mnews_writer   = (string) get_post_meta( get_the_ID(), '_mnews_writer', true );
				$mnews_editor   = (string) get_post_meta( get_the_ID(), '_mnews_editor', true );
				$mnews_source   = (string) get_post_meta( get_the_ID(), '_mnews_source', true );
				$mnews_video    = mnews_video_url();
				$mnews_embed    = $mnews_video ? wp_oembed_get( $mnews_video, array( 'width' => 780 ) ) : false;
				?>
				<article id="post-<?php the_ID(); ?>" class="mnw-article">
					<?php mnews_breadcrumb(); ?>

					<h1 class="mnw-article__title"><?php the_title(); ?></h1>
					<?php if ( $mnews_subtitle ) : ?>
						<p class="mnw-article__dek"><?php echo esc_html( $mnews_subtitle ); ?></p>
					<?php endif; ?>

					<div class="mnw-article__meta">
						<?php if ( get_theme_mod( 'mnews_show_author', true ) && get_the_author_meta( 'ID' ) ) : ?>
							<span class="mnw-article__by">
								<?php esc_html_e( 'Oleh', 'm-news' ); ?>
								<a href="<?php echo esc_url( get_author_posts_url( (int) get_the_author_meta( 'ID' ) ) ); ?>"><?php the_author(); ?></a>
							</span>
						<?php endif; ?>
						<span class="mnw-article__time"><?php mnews_posted_on(); ?></span>
					</div>

					<?php
					if ( in_array( $mnews_position, array( 'top', 'both' ), true ) ) {
						mnews_share_buttons( 'top' );
					}
					?>

					<?php if ( $mnews_embed ) : ?>
						<figure class="mnw-article__figure mnw-article__figure--video">
							<span class="mnw-video-label"><?php esc_html_e( 'Video', 'm-news' ); ?></span>
							<div class="mnw-embed"><?php echo $mnews_embed; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- from wp_oembed_get(), core-sanitised. ?></div>
						</figure>
					<?php elseif ( has_post_thumbnail() ) : ?>
						<figure class="mnw-article__figure">
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
							<?php $mnews_caption = get_the_post_thumbnail_caption(); ?>
							<?php if ( $mnews_caption ) : ?>
								<figcaption><?php echo esc_html( $mnews_caption ); ?></figcaption>
							<?php endif; ?>
						</figure>
					<?php endif; ?>

					<div class="mnw-content entry-content">
						<?php
						the_content();
						wp_link_pages(
							array(
								'before' => '<nav class="mnw-page-links" aria-label="' . esc_attr__( 'Halaman artikel', 'm-news' ) . '"><span>' . esc_html__( 'Halaman:', 'm-news' ) . '</span>',
								'after'  => '</nav>',
							)
						);
						?>
					</div>

					<?php if ( get_theme_mod( 'mnews_show_credits', true ) && ( $mnews_writer || $mnews_editor || $mnews_source ) ) : ?>
						<ul class="mnw-credits">
							<?php if ( $mnews_writer ) : ?>
								<li><strong><?php esc_html_e( 'Penulis:', 'm-news' ); ?></strong> <?php echo esc_html( $mnews_writer ); ?></li>
							<?php endif; ?>
							<?php if ( $mnews_editor ) : ?>
								<li><strong><?php esc_html_e( 'Editor:', 'm-news' ); ?></strong> <?php echo esc_html( $mnews_editor ); ?></li>
							<?php endif; ?>
							<?php if ( $mnews_source ) : ?>
								<li><strong><?php esc_html_e( 'Sumber:', 'm-news' ); ?></strong> <?php echo esc_html( $mnews_source ); ?></li>
							<?php endif; ?>
						</ul>
					<?php endif; ?>

					<?php $mnews_tags = get_theme_mod( 'mnews_show_tags', true ) ? get_the_tags() : false; ?>
					<?php if ( $mnews_tags ) : ?>
						<ul class="mnw-tags mnw-tags--article">
							<?php foreach ( $mnews_tags as $mnews_tag ) : ?>
								<li><a href="<?php echo esc_url( get_tag_link( $mnews_tag ) ); ?>">#<?php echo esc_html( $mnews_tag->name ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<?php
					if ( in_array( $mnews_position, array( 'bottom', 'both' ), true ) ) {
						mnews_share_buttons( 'bottom' );
					}
					?>
				</article>

				<?php
				mnews_area( 'single-after-content', 'mnw-after-content' );
				mnews_related_section();

				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
			endwhile;
			?>
		</div>

		<?php mnews_sidebar( 'single-sidebar' ); ?>
	</div>

	<?php
	if ( ! mnews_is_amp() && get_theme_mod( 'mnews_share_float', false ) && 'none' !== $mnews_position ) {
		mnews_share_buttons( 'float' );
	}
	?>
</main>
<?php
get_footer();
