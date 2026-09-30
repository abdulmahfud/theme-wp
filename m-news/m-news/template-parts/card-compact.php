<?php
/**
 * Card: small square thumbnail + title (+ time). For sidebars and "featured" lists.
 *
 * @package M_News
 *
 * @var array $args show_date.
 */

defined( 'ABSPATH' ) || exit;

$mnews_show_date = ! isset( $args['show_date'] ) || $args['show_date'];
?>
<article class="mnw-compact">
	<a class="mnw-compact__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php mnews_thumb( 'mnews-square', false, '72px' ); ?>
		<?php mnews_video_badge(); ?>
	</a>
	<div class="mnw-compact__body">
		<h3 class="mnw-compact__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<?php if ( $mnews_show_date ) : ?>
			<div class="mnw-card__meta"><?php mnews_posted_on(); ?></div>
		<?php endif; ?>
	</div>
</article>
