<?php
/**
 * Card: small square thumbnail + title (+ time). For sidebars and "featured" lists.
 *
 * @package M_Nata
 *
 * @var array $args show_date.
 */

defined( 'ABSPATH' ) || exit;

$mnata_show_date = ! isset( $args['show_date'] ) || $args['show_date'];
?>
<article class="mn-compact">
	<a class="mn-compact__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php mnata_thumb( 'mnata-square', false, '72px' ); ?>
	</a>
	<div class="mn-compact__body">
		<h3 class="mn-compact__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<?php if ( $mnata_show_date ) : ?>
			<div class="mn-card__meta"><?php mnata_posted_on(); ?></div>
		<?php endif; ?>
	</div>
</article>
