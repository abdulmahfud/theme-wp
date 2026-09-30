<?php
/**
 * Licence screen (Appearance > Lisensi M-News) and admin notice.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the screen.
 */
function mnews_license_menu_page() {
	add_theme_page(
		__( 'Lisensi M-News', 'm-news' ),
		__( 'Lisensi', 'm-news' ),
		'manage_options',
		'mnews-license',
		'mnews_license_render_page'
	);
}
add_action( 'admin_menu', 'mnews_license_menu_page' );

/**
 * Mask a key for display: MNEWS-XXXX-****-****-1234.
 *
 * @param string $key Key.
 * @return string
 */
function mnews_license_mask( $key ) {
	$key = (string) $key;
	if ( strlen( $key ) <= 10 ) {
		return str_repeat( '•', strlen( $key ) );
	}
	return substr( $key, 0, 6 ) . str_repeat( '•', max( 4, strlen( $key ) - 10 ) ) . substr( $key, -4 );
}

/**
 * Remember a one-off message for the next page load.
 *
 * @param string $type    success | error.
 * @param string $message Text.
 */
function mnews_license_flash( $type, $message ) {
	set_transient(
		'mnews_license_msg_' . get_current_user_id(),
		array(
			'type'    => $type,
			'message' => $message,
		),
		MINUTE_IN_SECONDS
	);
}

/**
 * Render the screen.
 */
function mnews_license_render_page() {
	$status = mnews_license_status();
	$state  = mnews_license_state();
	$flash  = get_transient( 'mnews_license_msg_' . get_current_user_id() );
	if ( $flash ) {
		delete_transient( 'mnews_license_msg_' . get_current_user_id() );
	}
	$fmt = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Lisensi M-News', 'm-news' ); ?></h1>

		<?php if ( $flash ) : ?>
			<div class="notice notice-<?php echo 'success' === $flash['type'] ? 'success' : 'error'; ?> is-dismissible"><p><?php echo esc_html( $flash['message'] ); ?></p></div>
		<?php endif; ?>

		<div class="notice notice-<?php echo mnews_license_ok() ? 'success' : 'error'; ?> inline">
			<p>
				<strong><?php esc_html_e( 'Status:', 'm-news' ); ?></strong>
				<?php echo esc_html( 'active' === $status ? __( 'Aktif', 'm-news' ) : mnews_license_status_label( $status ) ); ?>
			</p>
		</div>

		<?php if ( 'dev' === $status ) : ?>
			<p><?php esc_html_e( 'Situs ini berjalan di host lokal/pengembangan, sehingga tema tidak dikunci. Lisensi tetap diperlukan pada domain produksi.', 'm-news' ); ?></p>
		<?php endif; ?>

		<?php if ( $state ) : ?>
			<table class="widefat striped" style="max-width:720px">
				<tbody>
				<tr><th><?php esc_html_e( 'Kunci lisensi', 'm-news' ); ?></th><td><code><?php echo esc_html( mnews_license_mask( $state['key'] ) ); ?></code></td></tr>
				<tr><th><?php esc_html_e( 'Domain', 'm-news' ); ?></th><td><?php echo esc_html( $state['domain'] ); ?></td></tr>
				<?php if ( ! empty( $state['plan'] ) ) : ?>
					<tr><th><?php esc_html_e( 'Paket', 'm-news' ); ?></th><td><?php echo esc_html( $state['plan'] ); ?></td></tr>
				<?php endif; ?>
				<tr>
					<th><?php esc_html_e( 'Berlaku sampai', 'm-news' ); ?></th>
					<td><?php echo $state['expires_at'] ? esc_html( wp_date( $fmt, (int) $state['expires_at'] ) ) : esc_html__( 'Seumur hidup', 'm-news' ); ?></td>
				</tr>
				<tr><th><?php esc_html_e( 'Terakhir terverifikasi', 'm-news' ); ?></th><td><?php echo $state['last_ok'] ? esc_html( wp_date( $fmt, (int) $state['last_ok'] ) ) : '—'; ?></td></tr>
				<tr><th><?php esc_html_e( 'Pemeriksaan terakhir', 'm-news' ); ?></th><td><?php echo esc_html( wp_date( $fmt, (int) $state['last_check'] ) ); ?>
					<?php if ( ! empty( $state['last_error'] ) ) : ?>
						<em>(<?php echo esc_html( $state['last_error'] ); ?>)</em>
					<?php endif; ?></td></tr>
				</tbody>
			</table>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline-block;margin-right:8px">
				<input type="hidden" name="action" value="mnews_license_check">
				<?php wp_nonce_field( 'mnews_license_check' ); ?>
				<button class="button button-secondary"><?php esc_html_e( 'Periksa ulang sekarang', 'm-news' ); ?></button>
			</form>
			<p class="description"><?php esc_html_e( 'Lisensi ini terkunci ke domain ini. Untuk memindahkannya ke domain lain, hubungi dukungan M-Onetech — pemindahan tidak bisa dilakukan sendiri dari layar ini.', 'm-news' ); ?></p>
		<?php endif; ?>

		<h2><?php esc_html_e( 'Pembaruan tema', 'm-news' ); ?></h2>
		<?php $mnews_update = 'active' === $status ? mnews_update_info() : array(); ?>
		<p>
			<?php
			/* translators: %s: installed version. */
			echo esc_html( sprintf( __( 'Versi terpasang: %s.', 'm-news' ), MNEWS_VERSION ) );
			echo ' ';
			if ( $mnews_update ) {
				/* translators: %s: new version. */
				echo '<strong>' . esc_html( sprintf( __( 'Pembaruan tersedia: %s.', 'm-news' ), $mnews_update['version'] ) ) . '</strong> ';
				echo '<a href="' . esc_url( admin_url( 'themes.php' ) ) . '">' . esc_html__( 'Perbarui di Tampilan > Tema', 'm-news' ) . '</a>';
			} elseif ( 'active' === $status ) {
				esc_html_e( 'Anda memakai versi terbaru.', 'm-news' );
			} else {
				esc_html_e( 'Aktifkan lisensi untuk menerima pembaruan otomatis.', 'm-news' );
			}
			?>
		</p>
		<?php if ( 'active' === $status ) : ?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-bottom:20px">
				<input type="hidden" name="action" value="mnews_license_updates">
				<?php wp_nonce_field( 'mnews_license_updates' ); ?>
				<button class="button"><?php esc_html_e( 'Cek pembaruan sekarang', 'm-news' ); ?></button>
			</form>
		<?php endif; ?>

		<h2><?php echo $state ? esc_html__( 'Ganti kunci lisensi', 'm-news' ) : esc_html__( 'Aktifkan lisensi', 'm-news' ); ?></h2>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="mnews_license_activate">
			<?php wp_nonce_field( 'mnews_license_activate' ); ?>
			<p>
				<input type="text" class="regular-text code" name="license_key" placeholder="MNEWS-XXXX-XXXX-XXXX-XXXX" autocomplete="off" required>
				<button class="button button-primary"><?php esc_html_e( 'Aktifkan', 'm-news' ); ?></button>
			</p>
			<p class="description"><?php esc_html_e( 'Kunci lisensi dikirim ke server lisensi M-Onetech bersama nama domain situs ini. Satu lisensi memiliki batas jumlah domain.', 'm-news' ); ?></p>
		</form>
	</div>
	<?php
}

/**
 * Common guard for the admin-post handlers.
 *
 * @param string $action Action / nonce name.
 */
function mnews_license_guard( $action ) {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Anda tidak punya izin.', 'm-news' ), '', array( 'response' => 403 ) );
	}
	check_admin_referer( $action );
}

/**
 * Handler: activate.
 */
function mnews_license_handle_activate() {
	mnews_license_guard( 'mnews_license_activate' );
	$key    = isset( $_POST['license_key'] ) ? sanitize_text_field( wp_unslash( $_POST['license_key'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce and capability are checked in mnews_license_guard().
	$result = mnews_license_activate( $key );
	if ( is_wp_error( $result ) ) {
		mnews_license_flash( 'error', $result->get_error_message() );
	} else {
		mnews_license_flash( 'success', __( 'Lisensi berhasil diaktifkan. Terima kasih!', 'm-news' ) );
	}
	wp_safe_redirect( admin_url( 'themes.php?page=mnews-license' ) );
	exit;
}
add_action( 'admin_post_mnews_license_activate', 'mnews_license_handle_activate' );

/**
 * Handler: re-check.
 */
function mnews_license_handle_check() {
	mnews_license_guard( 'mnews_license_check' );
	$status = mnews_license_check();
	mnews_license_flash( 'active' === $status ? 'success' : 'error', 'active' === $status ? __( 'Lisensi terverifikasi.', 'm-news' ) : mnews_license_status_label( $status ) );
	wp_safe_redirect( admin_url( 'themes.php?page=mnews-license' ) );
	exit;
}
add_action( 'admin_post_mnews_license_check', 'mnews_license_handle_check' );

/**
 * Handler: check for theme updates now.
 */
function mnews_license_handle_updates() {
	mnews_license_guard( 'mnews_license_updates' );
	mnews_update_reset();
	wp_update_themes();
	$info = mnews_update_info();
	mnews_license_flash(
		'success',
		$info
			/* translators: %s: version. */
			? sprintf( __( 'Pembaruan tersedia: versi %s.', 'm-news' ), $info['version'] )
			: __( 'Tidak ada pembaruan; Anda memakai versi terbaru.', 'm-news' )
	);
	wp_safe_redirect( admin_url( 'themes.php?page=mnews-license' ) );
	exit;
}
add_action( 'admin_post_mnews_license_updates', 'mnews_license_handle_updates' );

/**
 * Red notice on every admin screen while the licence is not valid.
 */
function mnews_license_admin_notice() {
	if ( mnews_license_ok() || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( $screen && 'appearance_page_mnews-license' === $screen->id ) {
		return;
	}
	?>
	<div class="notice notice-error">
		<p><strong><?php esc_html_e( 'Lisensi M-News belum valid.', 'm-news' ); ?></strong>
			<?php echo esc_html( mnews_license_status_label( mnews_license_status() ) ); ?>
			<?php esc_html_e( 'Pengunjung saat ini melihat halaman "lisensi diperlukan".', 'm-news' ); ?>
			<a href="<?php echo esc_url( admin_url( 'themes.php?page=mnews-license' ) ); ?>"><?php esc_html_e( 'Aktifkan lisensi', 'm-news' ); ?></a></p>
	</div>
	<?php
}
add_action( 'admin_notices', 'mnews_license_admin_notice' );
