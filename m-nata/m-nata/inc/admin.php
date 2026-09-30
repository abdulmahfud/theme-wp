<?php
/**
 * Admin-only assets: media picker for the image field in widget forms.
 *
 * @package M_Nata
 */

defined( 'ABSPATH' ) || exit;

/**
 * Load the picker on the Widgets screen and inside the Customizer.
 */
function mnata_admin_widget_assets() {
	wp_enqueue_media();
	wp_enqueue_script( 'mnata-admin', mnata_asset( 'assets/js/admin.js' )[0], array(), mnata_asset( 'assets/js/admin.js' )[1], true );
	wp_localize_script(
		'mnata-admin',
		'mnataAdmin',
		array(
			'title'   => __( 'Pilih gambar banner', 'm-nata' ),
			'button'  => __( 'Gunakan gambar ini', 'm-nata' ),
			'ajax'    => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'mnata_wilayah' ),
			'loading' => __( 'Memuat…', 'm-nata' ),
			'failed'  => __( 'Gagal memuat daftar wilayah. Isi kode manual.', 'm-nata' ),
		)
	);
	wp_add_inline_style( 'wp-admin', '.mnata-image-preview img{display:block;max-width:100%;height:auto;margin:6px 0}.mnata-image-clear{margin-left:6px;color:#b32d2e}.mnata-wilayah{display:block;margin-top:6px}.mnata-wilayah select{margin-bottom:4px}.mnata-wilayah[data-error]::after{content:attr(data-error);display:block;color:#b32d2e;font-size:12px}' );
}
add_action(
	'admin_enqueue_scripts',
	static function ( $hook ) {
		if ( 'widgets.php' === $hook ) {
			mnata_admin_widget_assets();
		}
	}
);
add_action( 'customize_controls_enqueue_scripts', 'mnata_admin_widget_assets' );
