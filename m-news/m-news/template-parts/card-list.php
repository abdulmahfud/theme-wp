<?php
/**
 * Card: thumbnail left, category + title + time on the right.
 *
 * @package M_News
 *
 * @var array $args index, show_cat, show_date, lcp (passed by mnews_render_posts()).
 */

defined( 'ABSPATH' ) || exit;

$mnews_show_cat  = ! isset( $args['show_cat'] ) || $args['show_cat'];
$mnews_show_date = ! isset( $args['show_date'] ) || $args['show_date'];
$mnews_cat       = $mnews_show_cat ? mnews_category_label() : '';
?>
<article class="mnw-card">
	<a class="mnw-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php mnews_thumb( 'mnews-thumb', ! empty( $args['lcp'] ), '(min-width:768px) 240px, 96px' ); ?>
		<?php mnews_video_badge(); ?>
	</a>
	<div class="mnw-card__body">
		<?php if ( $mnews_cat ) : ?>
			<span class="mnw-card__cat"><?php echo $mnews_cat; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in mnews_category_label(). ?></span>
		<?php endif; ?>
		<h2 class="mnw-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<?php if ( $mnews_show_date ) : ?>
			<div class="mnw-card__meta"><?php mnews_posted_on(); ?></div>
		<?php endif; ?>
	</div>
</article>
