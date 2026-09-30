<?php
/**
 * "Info Berita" meta box: sub-judul, penulis, editor, sumber.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

/**
 * Fields: meta key => label.
 *
 * @return array<string,string>
 */
function mnews_meta_fields() {
	return array(
		'_mnews_subtitle' => __( 'Sub-judul (tampil di bawah judul)', 'm-news' ),
		'_mnews_writer'   => __( 'Penulis / reporter', 'm-news' ),
		'_mnews_editor'   => __( 'Editor', 'm-news' ),
		'_mnews_source'   => __( 'Sumber berita', 'm-news' ),
		'_mnews_video'    => __( 'URL Video (YouTube/Vimeo, opsional)', 'm-news' ),
	);
}

/**
 * The post's video URL, or '' when it has none.
 *
 * @param int|null $post_id Post ID (current post if null).
 * @return string
 */
function mnews_video_url( $post_id = null ) {
	return (string) get_post_meta( $post_id ? $post_id : get_the_ID(), '_mnews_video', true );
}

/**
 * Register the box.
 */
function mnews_add_meta_box() {
	add_meta_box( 'mnews_info', __( 'Info Berita (M-News)', 'm-news' ), 'mnews_render_meta_box', 'post', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'mnews_add_meta_box' );

/**
 * Render the box.
 *
 * @param WP_Post $post Post.
 */
function mnews_render_meta_box( $post ) {
	wp_nonce_field( 'mnews_meta', 'mnews_meta_nonce' );
	foreach ( mnews_meta_fields() as $key => $label ) {
		printf(
			'<p><label for="%1$s"><strong>%2$s</strong></label><br><input type="text" class="widefat" id="%1$s" name="%1$s" value="%3$s"></p>',
			esc_attr( $key ),
			esc_html( $label ),
			esc_attr( (string) get_post_meta( $post->ID, $key, true ) )
		);
	}
}

/**
 * Save the box.
 *
 * @param int $post_id Post ID.
 */
function mnews_save_meta_box( $post_id ) {
	if ( ! isset( $_POST['mnews_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mnews_meta_nonce'] ) ), 'mnews_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	foreach ( array_keys( mnews_meta_fields() ) as $key ) {
		$raw   = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : '';
		$value = ( '_mnews_video' === $key ) ? esc_url_raw( trim( $raw ) ) : sanitize_text_field( $raw );
		if ( '' === $value ) {
			delete_post_meta( $post_id, $key );
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}
}
add_action( 'save_post_post', 'mnews_save_meta_box' );
