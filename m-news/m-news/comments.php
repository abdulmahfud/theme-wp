<?php
/**
 * Comments. Avatars are off on purpose (each Gravatar is an extra third-party request).
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="mnw-comments">
	<?php if ( have_comments() ) : ?>
		<h2 class="mnw-block-title">
			<?php
			/* translators: %s: number of comments. */
			echo esc_html( sprintf( _n( '%s Komentar', '%s Komentar', get_comments_number(), 'm-news' ), number_format_i18n( get_comments_number() ) ) );
			?>
		</h2>
		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 0,
				)
			);
			?>
		</ol>
		<?php the_comments_navigation(); ?>
	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() ) : ?>
		<p class="mnw-comments__closed"><?php esc_html_e( 'Komentar ditutup.', 'm-news' ); ?></p>
	<?php endif; ?>

	<?php
	comment_form(
		array(
			'title_reply'          => esc_html__( 'Tinggalkan komentar', 'm-news' ),
			'title_reply_before'   => '<h2 id="reply-title" class="mnw-block-title">',
			'title_reply_after'    => '</h2>',
			'label_submit'         => esc_html__( 'Kirim Komentar', 'm-news' ),
			'class_submit'         => 'submit mnw-btn',
			'comment_notes_before' => '',
		)
	);
	?>
</section>
