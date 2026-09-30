<?php
/**
 * Admin-only assets: media picker for the image field in widget forms.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

/**
 * Load the picker on the Widgets screen and inside the Customizer.
 */
function mnews_admin_widget_assets() {
	wp_enqueue_media();
	wp_enqueue_script( 'mnews-admin', mnews_asset( 'assets/js/admin.js' )[0], array(), mnews_asset( 'assets/js/admin.js' )[1], true );
	wp_localize_script(
		'mnews-admin',
		'mnewsAdmin',
		array(
			'title'   => __( 'Pilih gambar banner', 'm-news' ),
			'button'  => __( 'Gunakan gambar ini', 'm-news' ),
			'ajax'    => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'mnews_wilayah' ),
			'loading' => __( 'Memuat…', 'm-news' ),
			'failed'  => __( 'Gagal memuat daftar wilayah. Isi kode manual.', 'm-news' ),
		)
	);
	wp_add_inline_style( 'wp-admin', '.mnews-image-preview img{display:block;max-width:100%;height:auto;margin:6px 0}.mnews-image-clear{margin-left:6px;color:#b32d2e}.mnews-wilayah{display:block;margin-top:6px}.mnews-wilayah select{margin-bottom:4px}.mnews-wilayah[data-error]::after{content:attr(data-error);display:block;color:#b32d2e;font-size:12px}' );
}
add_action(
	'admin_enqueue_scripts',
	static function ( $hook ) {
		if ( 'widgets.php' === $hook ) {
			mnews_admin_widget_assets();
		}
	}
);
add_action( 'customize_controls_enqueue_scripts', 'mnews_admin_widget_assets' );
