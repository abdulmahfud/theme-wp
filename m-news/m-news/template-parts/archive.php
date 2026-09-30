<?php
/**
 * Shared body for index, category, tag, date, author and search results:
 * heading + list of posts + pagination + sidebar.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

global $wp_query;

$mnews_kicker = '';
$mnews_title  = '';
$mnews_desc   = '';

if ( is_category() ) {
	$mnews_kicker = __( 'Kategori', 'm-news' );
	$mnews_title  = single_cat_title( '', false );
	$mnews_desc   = wp_kses_post( category_description() );
} elseif ( is_tag() ) {
	$mnews_kicker = __( 'Topik', 'm-news' );
	$mnews_title  = '#' . single_tag_title( '', false );
	$mnews_desc   = wp_kses_post( tag_description() );
} elseif ( is_author() ) {
	$mnews_kicker = __( 'Penulis', 'm-news' );
	$mnews_title  = get_the_author_meta( 'display_name', (int) get_queried_object_id() );
	$mnews_desc   = esc_html( (string) get_the_author_meta( 'description', (int) get_queried_object_id() ) );
} elseif ( is_search() ) {
	$mnews_kicker = __( 'Pencarian', 'm-news' );
	/* translators: %s: search query. */
	$mnews_title = sprintf( __( 'Hasil untuk "%s"', 'm-news' ), get_search_query() );
} elseif ( is_archive() ) {
	$mnews_kicker = __( 'Arsip', 'm-news' );
	$mnews_title  = wp_strip_all_tags( get_the_archive_title() );
} elseif ( is_home() && ! is_front_page() ) {
	$mnews_title = single_post_title( '', false );
}
?>
<main id="mnw-main" class="mnw-main">
	<?php mnews_side_banners(); ?>

	<div class="mnw-container mnw-layout">
		<div class="mnw-layout__main">
			<?php if ( $mnews_title ) : ?>
				<header class="mnw-archive-head">
					<?php if ( $mnews_kicker ) : ?>
						<span class="mnw-archive-kicker"><?php echo esc_html( $mnews_kicker ); ?></span>
					<?php endif; ?>
					<h1 class="mnw-archive-title"><?php echo esc_html( $mnews_title ); ?></h1>
					<?php if ( $mnews_desc ) : ?>
						<div class="mnw-archive-desc"><?php echo $mnews_desc; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped/kses'd above. ?></div>
					<?php endif; ?>
					<?php if ( is_search() ) : ?>
						<?php get_search_form(); ?>
						<p class="mnw-archive-count">
							<?php
							/* translators: %s: number of results. */
							echo esc_html( sprintf( _n( '%s berita ditemukan', '%s berita ditemukan', (int) $wp_query->found_posts, 'm-news' ), number_format_i18n( (int) $wp_query->found_posts ) ) );
							?>
						</p>
					<?php endif; ?>
				</header>
			<?php endif; ?>

			<?php if ( have_posts() ) : ?>
				<?php
				update_post_thumbnail_cache( $wp_query );
				$mnews_i = 0;
				?>
				<div class="mnw-feed">
					<?php
					while ( have_posts() ) :
						the_post();
						++$mnews_i;
						get_template_part(
							'template-parts/card',
							'list',
							array(
								'index'     => $mnews_i,
								'show_cat'  => true,
								'show_date' => true,
								'lcp'       => 1 === $mnews_i,
							)
						);
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
