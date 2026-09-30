<?php
/**
 * Card: large image with the title over a dark gradient. First item of the "featured" layout.
 *
 * @package M_Nata
 *
 * @var array $args show_cat, lcp.
 */

defined( 'ABSPATH' ) || exit;

$mnata_show_cat = ! isset( $args['show_cat'] ) || $args['show_cat'];
$mnata_cat      = $mnata_show_cat ? mnata_category_label() : '';
?>
<article class="mn-overlay">
	<a class="mn-overlay__link" href="<?php the_permalink(); ?>">
		<?php mnata_thumb( 'mnata-card', ! empty( $args['lcp'] ), '(min-width:992px) 320px, 100vw' ); ?>
		<span class="mn-slide__cap">
			<?php if ( $mnata_cat ) : ?>
				<span class="mn-slide__cat"><?php echo $mnata_cat; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in mnata_category_label(). ?></span>
			<?php endif; ?>
			<span class="mn-slide__title"><?php the_title(); ?></span>
		</span>
	</a>
</article>
