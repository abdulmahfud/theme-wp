<?php
/**
 * Licence screen (Appearance > Lisensi M-Nata) and admin notice.
 *
 * @package M_Nata
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the screen.
 */
function mnata_license_menu_page() {
	add_theme_page(
		__( 'Lisensi M-Nata', 'm-nata' ),
		__( 'Lisensi', 'm-nata' ),
		'manage_options',
		'mnata-license',
		'mnata_license_render_page'
	);
}
add_action( 'admin_menu', 'mnata_license_menu_page' );

/**
 * Mask a key for display: MNATA-XXXX-****-****-1234.
 *
 * @param string $key Key.
 * @return string
 */
function mnata_license_mask( $key ) {
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
function mnata_license_flash( $type, $message ) {
	set_transient(
		'mnata_license_msg_' . get_current_user_id(),
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
function mnata_license_render_page() {
	$status = mnata_license_status();
	$state  = mnata_license_state();
	$flash  = get_transient( 'mnata_license_msg_' . get_current_user_id() );
	if ( $flash ) {
		delete_transient( 'mnata_license_msg_' . get_current_user_id() );
	}
	$fmt = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Lisensi M-Nata', 'm-nata' ); ?></h1>

		<?php if ( $flash ) : ?>
			<div class="notice notice-<?php echo 'success' === $flash['type'] ? 'success' : 'error'; ?> is-dismissible"><p><?php echo esc_html( $flash['message'] ); ?></p></div>
		<?php endif; ?>

		<div class="notice notice-<?php echo mnata_license_ok() ? 'success' : 'error'; ?> inline">
			<p>
				<strong><?php esc_html_e( 'Status:', 'm-nata' ); ?></strong>
				<?php echo esc_html( 'active' === $status ? __( 'Aktif', 'm-nata' ) : mnata_license_status_label( $status ) ); ?>
			</p>
		</div>

		<?php if ( 'dev' === $status ) : ?>
			<p><?php esc_html_e( 'Situs ini berjalan di host lokal/pengembangan, sehingga tema tidak dikunci. Lisensi tetap diperlukan pada domain produksi.', 'm-nata' ); ?></p>
		<?php endif; ?>

		<?php if ( $state ) : ?>
			<table class="widefat striped" style="max-width:720px">
				<tbody>
				<tr><th><?php esc_html_e( 'Kunci lisensi', 'm-nata' ); ?></th><td><code><?php echo esc_html( mnata_license_mask( $state['key'] ) ); ?></code></td></tr>
				<tr><th><?php esc_html_e( 'Domain', 'm-nata' ); ?></th><td><?php echo esc_html( $state['domain'] ); ?></td></tr>
				<?php if ( ! empty( $state['plan'] ) ) : ?>
					<tr><th><?php esc_html_e( 'Paket', 'm-nata' ); ?></th><td><?php echo esc_html( $state['plan'] ); ?></td></tr>
				<?php endif; ?>
				<tr>
					<th><?php esc_html_e( 'Berlaku sampai', 'm-nata' ); ?></th>
					<td><?php echo $state['expires_at'] ? esc_html( wp_date( $fmt, (int) $state['expires_at'] ) ) : esc_html__( 'Seumur hidup', 'm-nata' ); ?></td>
				</tr>
				<tr><th><?php esc_html_e( 'Terakhir terverifikasi', 'm-nata' ); ?></th><td><?php echo $state['last_ok'] ? esc_html( wp_date( $fmt, (int) $state['last_ok'] ) ) : '—'; ?></td></tr>
				<tr><th><?php esc_html_e( 'Pemeriksaan terakhir', 'm-nata' ); ?></th><td><?php echo esc_html( wp_date( $fmt, (int) $state['last_check'] ) ); ?>
					<?php if ( ! empty( $state['last_error'] ) ) : ?>
						<em>(<?php echo esc_html( $state['last_error'] ); ?>)</em>
					<?php endif; ?></td></tr>
				</tbody>
			</table>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline-block;margin-right:8px">
				<input type="hidden" name="action" value="mnata_license_check">
				<?php wp_nonce_field( 'mnata_license_check' ); ?>
				<button class="button button-secondary"><?php esc_html_e( 'Periksa ulang sekarang', 'm-nata' ); ?></button>
			</form>
			<p class="description"><?php esc_html_e( 'Lisensi ini terkunci ke domain ini. Untuk memindahkannya ke domain lain, hubungi dukungan M-Onetech — pemindahan tidak bisa dilakukan sendiri dari layar ini.', 'm-nata' ); ?></p>
		<?php endif; ?>

		<h2><?php esc_html_e( 'Pembaruan tema', 'm-nata' ); ?></h2>
		<?php $mnata_update = 'active' === $status ? mnata_update_info() : array(); ?>
		<p>
			<?php
			/* translators: %s: installed version. */
			echo esc_html( sprintf( __( 'Versi terpasang: %s.', 'm-nata' ), MNATA_VERSION ) );
			echo ' ';
			if ( $mnata_update ) {
				/* translators: %s: new version. */
				echo '<strong>' . esc_html( sprintf( __( 'Pembaruan tersedia: %s.', 'm-nata' ), $mnata_update['version'] ) ) . '</strong> ';
				echo '<a href="' . esc_url( admin_url( 'themes.php' ) ) . '">' . esc_html__( 'Perbarui di Tampilan > Tema', 'm-nata' ) . '</a>';
			} elseif ( 'active' === $status ) {
				esc_html_e( 'Anda memakai versi terbaru.', 'm-nata' );
			} else {
				esc_html_e( 'Aktifkan lisensi untuk menerima pembaruan otomatis.', 'm-nata' );
			}
			?>
		</p>
		<?php if ( 'active' === $status ) : ?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-bottom:20px">
				<input type="hidden" name="action" value="mnata_license_updates">
				<?php wp_nonce_field( 'mnata_license_updates' ); ?>
				<button class="button"><?php esc_html_e( 'Cek pembaruan sekarang', 'm-nata' ); ?></button>
			</form>
		<?php endif; ?>

		<h2><?php echo $state ? esc_html__( 'Ganti kunci lisensi', 'm-nata' ) : esc_html__( 'Aktifkan lisensi', 'm-nata' ); ?></h2>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="mnata_license_activate">
			<?php wp_nonce_field( 'mnata_license_activate' ); ?>
			<p>
				<input type="text" class="regular-text code" name="license_key" placeholder="MNATA-XXXX-XXXX-XXXX-XXXX" autocomplete="off" required>
				<button class="button button-primary"><?php esc_html_e( 'Aktifkan', 'm-nata' ); ?></button>
			</p>
			<p class="description"><?php esc_html_e( 'Kunci lisensi dikirim ke server lisensi M-Onetech bersama nama domain situs ini. Satu lisensi memiliki batas jumlah domain.', 'm-nata' ); ?></p>
		</form>
	</div>
	<?php
}

/**
 * Common guard for the admin-post handlers.
 *
 * @param string $action Action / nonce name.
 */
function mnata_license_guard( $action ) {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Anda tidak punya izin.', 'm-nata' ), '', array( 'response' => 403 ) );
	}
	check_admin_referer( $action );
}

/**
 * Handler: activate.
 */
function mnata_license_handle_activate() {
	mnata_license_guard( 'mnata_license_activate' );
	$key    = isset( $_POST['license_key'] ) ? sanitize_text_field( wp_unslash( $_POST['license_key'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce and capability are checked in mnata_license_guard().
	$result = mnata_license_activate( $key );
	if ( is_wp_error( $result ) ) {
		mnata_license_flash( 'error', $result->get_error_message() );
	} else {
		mnata_license_flash( 'success', __( 'Lisensi berhasil diaktifkan. Terima kasih!', 'm-nata' ) );
	}
	wp_safe_redirect( admin_url( 'themes.php?page=mnata-license' ) );
	exit;
}
add_action( 'admin_post_mnata_license_activate', 'mnata_license_handle_activate' );

/**
 * Handler: re-check.
 */
function mnata_license_handle_check() {
	mnata_license_guard( 'mnata_license_check' );
	$status = mnata_license_check();
	mnata_license_flash( 'active' === $status ? 'success' : 'error', 'active' === $status ? __( 'Lisensi terverifikasi.', 'm-nata' ) : mnata_license_status_label( $status ) );
	wp_safe_redirect( admin_url( 'themes.php?page=mnata-license' ) );
	exit;
}
add_action( 'admin_post_mnata_license_check', 'mnata_license_handle_check' );

/**
 * Handler: check for theme updates now.
 */
function mnata_license_handle_updates() {
	mnata_license_guard( 'mnata_license_updates' );
	mnata_update_reset();
	wp_update_themes();
	$info = mnata_update_info();
	mnata_license_flash(
		'success',
		$info
			/* translators: %s: version. */
			? sprintf( __( 'Pembaruan tersedia: versi %s.', 'm-nata' ), $info['version'] )
			: __( 'Tidak ada pembaruan; Anda memakai versi terbaru.', 'm-nata' )
	);
	wp_safe_redirect( admin_url( 'themes.php?page=mnata-license' ) );
	exit;
}
add_action( 'admin_post_mnata_license_updates', 'mnata_license_handle_updates' );

/**
 * Red notice on every admin screen while the licence is not valid.
 */
function mnata_license_admin_notice() {
	if ( mnata_license_ok() || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( $screen && 'appearance_page_mnata-license' === $screen->id ) {
		return;
	}
	?>
	<div class="notice notice-error">
		<p><strong><?php esc_html_e( 'Lisensi M-Nata belum valid.', 'm-nata' ); ?></strong>
			<?php echo esc_html( mnata_license_status_label( mnata_license_status() ) ); ?>
			<?php esc_html_e( 'Pengunjung saat ini melihat halaman "lisensi diperlukan".', 'm-nata' ); ?>
			<a href="<?php echo esc_url( admin_url( 'themes.php?page=mnata-license' ) ); ?>"><?php esc_html_e( 'Aktifkan lisensi', 'm-nata' ); ?></a></p>
	</div>
	<?php
}
add_action( 'admin_notices', 'mnata_license_admin_notice' );
