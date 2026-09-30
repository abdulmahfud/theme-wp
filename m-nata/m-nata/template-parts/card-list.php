<?php
/**
 * Card: thumbnail left, category + title + time on the right.
 *
 * @package M_Nata
 *
 * @var array $args index, show_cat, show_date, lcp (passed by mnata_render_posts()).
 */

defined( 'ABSPATH' ) || exit;

$mnata_show_cat  = ! isset( $args['show_cat'] ) || $args['show_cat'];
$mnata_show_date = ! isset( $args['show_date'] ) || $args['show_date'];
$mnata_cat       = $mnata_show_cat ? mnata_category_label() : '';
?>
<article class="mn-card">
	<a class="mn-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php mnata_thumb( 'mnata-thumb', ! empty( $args['lcp'] ), '(min-width:768px) 240px, 96px' ); ?>
	</a>
	<div class="mn-card__body">
		<?php if ( $mnata_cat ) : ?>
			<span class="mn-card__cat"><?php echo $mnata_cat; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in mnata_category_label(). ?></span>
		<?php endif; ?>
		<h2 class="mn-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<?php if ( $mnata_show_date ) : ?>
			<div class="mn-card__meta"><?php mnata_posted_on(); ?></div>
		<?php endif; ?>
	</div>
</article>
