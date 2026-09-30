<?php
/**
 * "Info Berita" meta box: sub-judul, penulis, editor, sumber.
 *
 * @package M_Nata
 */

defined( 'ABSPATH' ) || exit;

/**
 * Fields: meta key => label.
 *
 * @return array<string,string>
 */
function mnata_meta_fields() {
	return array(
		'_mnata_subtitle' => __( 'Sub-judul (tampil di bawah judul)', 'm-nata' ),
		'_mnata_writer'   => __( 'Penulis / reporter', 'm-nata' ),
		'_mnata_editor'   => __( 'Editor', 'm-nata' ),
		'_mnata_source'   => __( 'Sumber berita', 'm-nata' ),
	);
}

/**
 * Register the box.
 */
function mnata_add_meta_box() {
	add_meta_box( 'mnata_info', __( 'Info Berita (M-Nata)', 'm-nata' ), 'mnata_render_meta_box', 'post', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'mnata_add_meta_box' );

/**
 * Render the box.
 *
 * @param WP_Post $post Post.
 */
function mnata_render_meta_box( $post ) {
	wp_nonce_field( 'mnata_meta', 'mnata_meta_nonce' );
	foreach ( mnata_meta_fields() as $key => $label ) {
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
function mnata_save_meta_box( $post_id ) {
	if ( ! isset( $_POST['mnata_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mnata_meta_nonce'] ) ), 'mnata_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	foreach ( array_keys( mnata_meta_fields() ) as $key ) {
		$value = isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '';
		if ( '' === $value ) {
			delete_post_meta( $post_id, $key );
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}
}
add_action( 'save_post_post', 'mnata_save_meta_box' );
