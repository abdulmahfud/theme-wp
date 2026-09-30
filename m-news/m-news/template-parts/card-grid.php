<?php
/**
 * Card: 16:9 image on top, category + title + time below. Used by grid layouts.
 *
 * @package M_News
 *
 * @var array $args show_cat, show_date, lcp.
 */

defined( 'ABSPATH' ) || exit;

$mnews_show_cat  = ! isset( $args['show_cat'] ) || $args['show_cat'];
$mnews_show_date = ! isset( $args['show_date'] ) || $args['show_date'];
$mnews_cat       = $mnews_show_cat ? mnews_category_label() : '';
?>
<article class="mnw-gcard">
	<a class="mnw-gcard__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php mnews_thumb( 'mnews-card', ! empty( $args['lcp'] ), '(min-width:992px) 260px, 50vw' ); ?>
		<?php mnews_video_badge(); ?>
	</a>
	<?php if ( $mnews_cat ) : ?>
		<span class="mnw-card__cat"><?php echo $mnews_cat; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in mnews_category_label(). ?></span>
	<?php endif; ?>
	<h3 class="mnw-gcard__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
	<?php if ( $mnews_show_date ) : ?>
		<div class="mnw-card__meta"><?php mnews_posted_on(); ?></div>
	<?php endif; ?>
</article>
