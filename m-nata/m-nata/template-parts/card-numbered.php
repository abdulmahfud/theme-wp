<?php
/**
 * Card: big number + title. For "Trending" style lists.
 *
 * @package M_Nata
 *
 * @var array $args index, show_cat, show_date.
 */

defined( 'ABSPATH' ) || exit;

$mnata_show_cat  = ! empty( $args['show_cat'] );
$mnata_show_date = ! empty( $args['show_date'] );
$mnata_cat       = $mnata_show_cat ? mnata_category_label() : '';
?>
<article class="mn-num">
	<span class="mn-num__n" aria-hidden="true"><?php echo (int) ( isset( $args['index'] ) ? $args['index'] : 1 ); ?></span>
	<div class="mn-num__body">
		<?php if ( $mnata_cat ) : ?>
			<span class="mn-card__cat"><?php echo $mnata_cat; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in mnata_category_label(). ?></span>
		<?php endif; ?>
		<h3 class="mn-num__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<?php if ( $mnata_show_date ) : ?>
			<div class="mn-card__meta"><?php mnata_posted_on(); ?></div>
		<?php endif; ?>
	</div>
</article>
