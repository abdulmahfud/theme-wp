<?php
/**
 * Admin screen for the demo import: Appearance > Impor Demo. Steps run one AJAX call at a time,
 * so the import works on shared hosting without hitting time limits.
 *
 * @package M_Nata
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the screen.
 */
function mnata_demo_menu_page() {
	add_theme_page(
		__( 'Impor Demo M-Nata', 'm-nata' ),
		__( 'Impor Demo', 'm-nata' ),
		'manage_options',
		'mnata-demo',
		'mnata_demo_render_page'
	);
}
add_action( 'admin_menu', 'mnata_demo_menu_page' );

/**
 * Load the script only on our screen.
 *
 * @param string $hook Admin page hook.
 */
function mnata_demo_enqueue( $hook ) {
	if ( 'appearance_page_mnata-demo' !== $hook ) {
		return;
	}
	wp_enqueue_script( 'mnata-demo', mnata_asset( 'assets/js/demo-import.js' )[0], array(), mnata_asset( 'assets/js/demo-import.js' )[1], true );
	wp_localize_script(
		'mnata-demo',
		'mnataDemo',
		array(
			'ajax'  => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'mnata_demo' ),
			'text'  => array(
				'media'     => __( 'Menyiapkan gambar…', 'm-nata' ),
				'structure' => __( 'Membuat kategori, halaman, dan menu…', 'm-nata' ),
				'post'      => __( 'Mengimpor artikel', 'm-nata' ),
				'widgets'   => __( 'Memasang widget…', 'm-nata' ),
				'settings'  => __( 'Menerapkan pengaturan…', 'm-nata' ),
				'finish'    => __( 'Menyelesaikan…', 'm-nata' ),
				'remove'    => __( 'Menghapus konten demo…', 'm-nata' ),
				'done'      => __( 'Impor selesai! Situs Anda kini tampil seperti demo.', 'm-nata' ),
				'removed'   => __( 'Konten demo sudah dihapus.', 'm-nata' ),
				'failed'    => __( 'Terjadi kesalahan:', 'm-nata' ),
				'confirm'   => __( 'Hapus semua konten demo (artikel, halaman, gambar, dan widget yang dibuat impor)? Konten Anda sendiri tidak disentuh.', 'm-nata' ),
			),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'mnata_demo_enqueue' );

/**
 * Render the screen.
 */
function mnata_demo_render_page() {
	$imported = (int) get_option( 'mnata_demo_imported', 0 );
	$labels   = array(
		'content'   => array( __( 'Artikel, halaman, dan menu demo', 'm-nata' ), __( '30 artikel (6 kategori × 5) dengan gambar unggulan, 5 halaman, dan menu utama/footer.', 'm-nata' ) ),
		'widgets'   => array( __( 'Widget dan tata letak', 'm-nata' ), __( 'Slider, blok kategori, banner iklan (termasuk sticky), cuaca dan gempa BMKG, trending, topik, sosial.', 'm-nata' ) ),
		'settings'  => array( __( 'Pengaturan tampilan', 'm-nata' ), __( 'Gradient warna, ticker, tombol bagikan, tautan sosial, footer.', 'm-nata' ) ),
		'logo'      => array( __( 'Logo M-Nata', 'm-nata' ), __( 'Memasang logo contoh sebagai logo situs.', 'm-nata' ) ),
		'identity'  => array( __( 'Judul dan slogan situs', 'm-nata' ), __( 'Menjadi "Portal Berita M-Nata".', 'm-nata' ) ),
		'permalink' => array( __( 'Permalink dan zona waktu', 'm-nata' ), __( 'Permalink /nama-artikel/ dan zona waktu Asia/Jakarta (disarankan untuk portal berita).', 'm-nata' ) ),
		'cleanup'   => array( __( 'Hapus konten bawaan WordPress', 'm-nata' ), __( '"Hello world!" dan "Sample Page".', 'm-nata' ) ),
	);
	?>
	<div class="wrap" id="mnata-demo">
		<h1><?php esc_html_e( 'Impor Demo M-Nata', 'm-nata' ); ?></h1>

		<?php if ( $imported ) : ?>
			<div class="notice notice-success inline">
				<p>
					<?php
					/* translators: %s: date and time of the last import. */
					echo esc_html( sprintf( __( 'Demo sudah diimpor pada %s.', 'm-nata' ), wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $imported ) ) );
					?>
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Lihat situs', 'm-nata' ); ?></a>
				</p>
			</div>
		<?php endif; ?>

		<p><?php esc_html_e( 'Satu klik untuk membuat situs Anda tampil seperti demo tema. Impor bersifat menambah: konten Anda sendiri tidak dihapus (kecuali "Hello world!" dan "Sample Page" bila dipilih). Aman diulang; artikel yang sudah ada dilewati.', 'm-nata' ); ?></p>

		<form id="mnata-demo-form" onsubmit="return false;">
			<table class="form-table" role="presentation">
				<tbody>
				<?php foreach ( $labels as $key => $text ) : ?>
					<tr>
						<th scope="row"><?php echo esc_html( $text[0] ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="opts[<?php echo esc_attr( $key ); ?>]" value="1" checked>
								<?php echo esc_html( $text[1] ); ?>
							</label>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>

			<p class="submit">
				<button type="button" class="button button-primary button-hero" id="mnata-demo-run"><?php esc_html_e( 'Impor Demo Sekarang', 'm-nata' ); ?></button>
				<?php if ( $imported ) : ?>
					<button type="button" class="button button-link-delete" id="mnata-demo-remove"><?php esc_html_e( 'Hapus konten demo', 'm-nata' ); ?></button>
				<?php endif; ?>
			</p>
		</form>

		<div id="mnata-demo-progress" hidden>
			<div class="mnata-demo__bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><span></span></div>
			<p id="mnata-demo-status" aria-live="polite"></p>
			<ul id="mnata-demo-log"></ul>
		</div>

		<p class="description">
			<?php esc_html_e( 'Catatan: seluruh artikel demo bersifat fiktif. Cuaca dan gempa memakai data BMKG langsung (butuh koneksi internet); ganti wilayah cuaca di Tampilan > Widget.', 'm-nata' ); ?>
		</p>

		<style>
			.mnata-demo__bar{height:14px;max-width:560px;background:#dcdcde;border-radius:7px;overflow:hidden}
			.mnata-demo__bar span{display:block;height:100%;width:0;background:linear-gradient(90deg,#4a1d8f,#b5179e);transition:width .25s}
			#mnata-demo-log{max-height:260px;max-width:720px;overflow:auto;margin-top:12px;padding:8px 12px;background:#fff;border:1px solid #dcdcde;font-size:12px}
			#mnata-demo-log li{margin:0 0 3px}
			#mnata-demo-log li.is-error{color:#b32d2e;font-weight:600}
			#mnata-demo-remove{margin-left:12px}
		</style>
	</div>
	<?php
}

/**
 * AJAX: run one import step.
 */
function mnata_demo_ajax() {
	check_ajax_referer( 'mnata_demo' );
	if ( ! mnata_license_ok() ) {
		wp_send_json_error( array( 'message' => __( 'Aktifkan lisensi M-Nata terlebih dahulu (Tampilan > Lisensi).', 'm-nata' ) ), 402 );
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'Anda tidak punya izin.', 'm-nata' ) ), 403 );
	}

	// phpcs:disable WordPress.Security.NonceVerification.Missing -- nonce checked above.
	$step = isset( $_POST['step'] ) ? sanitize_key( wp_unslash( $_POST['step'] ) ) : '';
	$raw  = isset( $_POST['opts'] ) && is_array( $_POST['opts'] ) ? array_map( 'absint', wp_unslash( $_POST['opts'] ) ) : array();
	$idx  = isset( $_POST['index'] ) ? absint( $_POST['index'] ) : 0;
	// phpcs:enable

	$opts = mnata_demo_options( $raw );

	if ( function_exists( 'set_time_limit' ) ) {
		set_time_limit( 120 ); // phpcs:ignore Squiz.PHP.DiscouragedFunctions.Discouraged, WordPress.PHP.NoSilencedErrors.Discouraged
	}
	mnata_demo_load_admin_includes();

	switch ( $step ) {
		case 'init':
			wp_send_json_success( array( 'total' => count( mnata_demo_articles() ) ) );
			break;
		case 'media':
			wp_send_json_success( array( 'message' => mnata_demo_step_media( $opts ) ) );
			break;
		case 'structure':
			wp_send_json_success( array( 'message' => mnata_demo_step_structure( $opts ) ) );
			break;
		case 'post':
			wp_send_json_success( mnata_demo_step_post( $idx ) );
			break;
		case 'widgets':
			wp_send_json_success( array( 'message' => mnata_demo_step_widgets() ) );
			break;
		case 'settings':
			wp_send_json_success( array( 'message' => mnata_demo_step_settings( $opts ) ) );
			break;
		case 'finish':
			wp_send_json_success( array( 'message' => mnata_demo_step_finish() ) );
			break;
		case 'remove':
			wp_send_json_success( array( 'remaining' => mnata_demo_step_remove() ) );
			break;
	}
	wp_send_json_error( array( 'message' => __( 'Langkah tidak dikenal.', 'm-nata' ) ), 400 );
}
add_action( 'wp_ajax_mnata_demo', 'mnata_demo_ajax' );

/**
 * Invitation shown on the dashboard and the themes screen until the demo is imported or dismissed.
 */
function mnata_demo_notice() {
	if ( get_option( 'mnata_demo_imported' ) || get_option( 'mnata_demo_dismissed' ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || ! in_array( $screen->id, array( 'dashboard', 'themes' ), true ) ) {
		return;
	}
	$dismiss = wp_nonce_url( add_query_arg( 'mnata_dismiss_demo', '1' ), 'mnata_dismiss_demo' );
	?>
	<div class="notice notice-info">
		<p><strong><?php esc_html_e( 'Terima kasih telah memakai M-Nata!', 'm-nata' ); ?></strong>
			<?php esc_html_e( 'Ingin situs Anda langsung tampil seperti demo?', 'm-nata' ); ?></p>
		<p>
			<a class="button button-primary" href="<?php echo esc_url( admin_url( 'themes.php?page=mnata-demo' ) ); ?>"><?php esc_html_e( 'Impor Demo', 'm-nata' ); ?></a>
			<a class="button" href="<?php echo esc_url( $dismiss ); ?>"><?php esc_html_e( 'Tidak, terima kasih', 'm-nata' ); ?></a>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'mnata_demo_notice' );

/**
 * Handle "Tidak, terima kasih".
 */
function mnata_demo_dismiss() {
	if ( isset( $_GET['mnata_dismiss_demo'] ) && current_user_can( 'manage_options' ) && check_admin_referer( 'mnata_dismiss_demo' ) ) {
		update_option( 'mnata_demo_dismissed', 1, false );
		wp_safe_redirect( remove_query_arg( array( 'mnata_dismiss_demo', '_wpnonce' ) ) );
		exit;
	}
}
add_action( 'admin_init', 'mnata_demo_dismiss' );
