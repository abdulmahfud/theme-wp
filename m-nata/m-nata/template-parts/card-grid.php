<?php
/**
 * Card: 16:9 image on top, category + title + time below. Used by grid layouts.
 *
 * @package M_Nata
 *
 * @var array $args show_cat, show_date, lcp.
 */

defined( 'ABSPATH' ) || exit;

$mnata_show_cat  = ! isset( $args['show_cat'] ) || $args['show_cat'];
$mnata_show_date = ! isset( $args['show_date'] ) || $args['show_date'];
$mnata_cat       = $mnata_show_cat ? mnata_category_label() : '';
?>
<article class="mn-gcard">
	<a class="mn-gcard__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php mnata_thumb( 'mnata-card', ! empty( $args['lcp'] ), '(min-width:992px) 260px, 50vw' ); ?>
	</a>
	<?php if ( $mnata_cat ) : ?>
		<span class="mn-card__cat"><?php echo $mnata_cat; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in mnata_category_label(). ?></span>
	<?php endif; ?>
	<h3 class="mn-gcard__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
	<?php if ( $mnata_show_date ) : ?>
		<div class="mn-card__meta"><?php mnata_posted_on(); ?></div>
	<?php endif; ?>
</article>
