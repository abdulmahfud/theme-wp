<?php
/**
 * Card: big number + title. For "Trending" style lists.
 *
 * @package M_News
 *
 * @var array $args index, show_cat, show_date.
 */

defined( 'ABSPATH' ) || exit;

$mnews_show_cat  = ! empty( $args['show_cat'] );
$mnews_show_date = ! empty( $args['show_date'] );
$mnews_cat       = $mnews_show_cat ? mnews_category_label() : '';
?>
<article class="mnw-num">
	<span class="mnw-num__n" aria-hidden="true"><?php echo (int) ( isset( $args['index'] ) ? $args['index'] : 1 ); ?></span>
	<div class="mnw-num__body">
		<?php if ( $mnews_cat ) : ?>
			<span class="mnw-card__cat"><?php echo $mnews_cat; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in mnews_category_label(). ?></span>
		<?php endif; ?>
		<h3 class="mnw-num__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<?php if ( $mnews_show_date ) : ?>
			<div class="mnw-card__meta"><?php mnews_posted_on(); ?></div>
		<?php endif; ?>
	</div>
</article>
