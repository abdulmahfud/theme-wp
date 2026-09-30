<?php
/**
 * Card: large image with the title over a dark gradient. First item of the "featured" layout.
 *
 * @package M_News
 *
 * @var array $args show_cat, lcp.
 */

defined( 'ABSPATH' ) || exit;

$mnews_show_cat = ! isset( $args['show_cat'] ) || $args['show_cat'];
$mnews_cat      = $mnews_show_cat ? mnews_category_label() : '';
?>
<article class="mnw-overlay">
	<a class="mnw-overlay__link" href="<?php the_permalink(); ?>">
		<?php mnews_thumb( 'mnews-card', ! empty( $args['lcp'] ), '(min-width:992px) 320px, 100vw' ); ?>
		<?php mnews_video_badge(); ?>
		<span class="mnw-slide__cap">
			<?php if ( $mnews_cat ) : ?>
				<span class="mnw-slide__cat"><?php echo $mnews_cat; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in mnews_category_label(). ?></span>
			<?php endif; ?>
			<span class="mnw-slide__title"><?php the_title(); ?></span>
		</span>
	</a>
</article>
