<?php
/**
 * Shared body for index, category, tag, date, author and search results:
 * heading + list of posts + pagination + sidebar.
 *
 * @package M_Nata
 */

defined( 'ABSPATH' ) || exit;

global $wp_query;

$mnata_kicker = '';
$mnata_title  = '';
$mnata_desc   = '';

if ( is_category() ) {
	$mnata_kicker = __( 'Kategori', 'm-nata' );
	$mnata_title  = single_cat_title( '', false );
	$mnata_desc   = wp_kses_post( category_description() );
} elseif ( is_tag() ) {
	$mnata_kicker = __( 'Topik', 'm-nata' );
	$mnata_title  = '#' . single_tag_title( '', false );
	$mnata_desc   = wp_kses_post( tag_description() );
} elseif ( is_author() ) {
	$mnata_kicker = __( 'Penulis', 'm-nata' );
	$mnata_title  = get_the_author_meta( 'display_name', (int) get_queried_object_id() );
	$mnata_desc   = esc_html( (string) get_the_author_meta( 'description', (int) get_queried_object_id() ) );
} elseif ( is_search() ) {
	$mnata_kicker = __( 'Pencarian', 'm-nata' );
	/* translators: %s: search query. */
	$mnata_title = sprintf( __( 'Hasil untuk "%s"', 'm-nata' ), get_search_query() );
} elseif ( is_archive() ) {
	$mnata_kicker = __( 'Arsip', 'm-nata' );
	$mnata_title  = wp_strip_all_tags( get_the_archive_title() );
} elseif ( is_home() && ! is_front_page() ) {
	$mnata_title = single_post_title( '', false );
}
?>
<main id="mn-main" class="mn-main">
	<?php mnata_side_banners(); ?>

	<div class="mn-container mn-layout">
		<div class="mn-layout__main">
			<?php if ( $mnata_title ) : ?>
				<header class="mn-archive-head">
					<?php if ( $mnata_kicker ) : ?>
						<span class="mn-archive-kicker"><?php echo esc_html( $mnata_kicker ); ?></span>
					<?php endif; ?>
					<h1 class="mn-archive-title"><?php echo esc_html( $mnata_title ); ?></h1>
					<?php if ( $mnata_desc ) : ?>
						<div class="mn-archive-desc"><?php echo $mnata_desc; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped/kses'd above. ?></div>
					<?php endif; ?>
					<?php if ( is_search() ) : ?>
						<?php get_search_form(); ?>
						<p class="mn-archive-count">
							<?php
							/* translators: %s: number of results. */
							echo esc_html( sprintf( _n( '%s berita ditemukan', '%s berita ditemukan', (int) $wp_query->found_posts, 'm-nata' ), number_format_i18n( (int) $wp_query->found_posts ) ) );
							?>
						</p>
					<?php endif; ?>
				</header>
			<?php endif; ?>

			<?php if ( have_posts() ) : ?>
				<?php
				update_post_thumbnail_cache( $wp_query );
				$mnata_i = 0;
				?>
				<div class="mn-feed">
					<?php
					while ( have_posts() ) :
						the_post();
						++$mnata_i;
						get_template_part(
							'template-parts/card',
							'list',
							array(
								'index'     => $mnata_i,
								'show_cat'  => true,
								'show_date' => true,
								'lcp'       => 1 === $mnata_i,
							)
						);
					endwhile;
					?>
				</div>

				<?php
				the_posts_pagination(
					array(
						'mid_size'  => 1,
						'prev_text' => __( 'Sebelumnya', 'm-nata' ),
						'next_text' => __( 'Selanjutnya', 'm-nata' ),
					)
				);
				?>
			<?php else : ?>
				<?php get_template_part( 'template-parts/content', 'none' ); ?>
			<?php endif; ?>
		</div>

		<?php mnata_sidebar( 'home-sidebar' ); ?>
	</div>
</main>
