<?php
/**
 * Single poll: the same card, just alone and a bit wider.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

get_header();
mnews_enqueue_poll();
?>
<main id="mnw-main" class="mnw-main">
	<div class="mnw-container mnw-page">
		<?php
		while ( have_posts() ) :
			the_post();
			mnews_render_poll_card( get_the_ID() );
		endwhile;
		?>
	</div>
</main>
<?php
get_footer();
