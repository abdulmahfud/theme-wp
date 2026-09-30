<?php
/**
 * Page shown to visitors while the theme licence is not valid. Self-contained (no theme CSS/JS).
 *
 * @package M_Nata
 */

defined( 'ABSPATH' ) || exit;

$mnata_status = isset( $status ) ? $status : mnata_license_status();
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?php echo esc_html( get_bloginfo( 'name' ) . ' — ' . __( 'Lisensi diperlukan', 'm-nata' ) ); ?></title>
<style>
	body{margin:0;min-height:100vh;display:grid;place-items:center;background:#f4f4f6;color:#1a1a1a;font:16px/1.6 system-ui,-apple-system,"Segoe UI",Roboto,Arial,sans-serif}
	main{max-width:520px;margin:24px;padding:32px;background:#fff;border-radius:10px;box-shadow:0 6px 24px rgba(0,0,0,.08);text-align:center}
	h1{margin:0 0 8px;font-size:1.4rem}
	p{margin:0 0 12px;color:#555}
	small{color:#888}
</style>
</head>
<body>
<main>
	<h1><?php esc_html_e( 'Situs sedang tidak dapat ditampilkan', 'm-nata' ); ?></h1>
	<p><?php esc_html_e( 'Lisensi tema pada situs ini belum aktif atau tidak dapat diverifikasi.', 'm-nata' ); ?></p>
	<p><strong><?php echo esc_html( mnata_license_status_label( $mnata_status ) ); ?></strong></p>
	<p><small><?php esc_html_e( 'Pemilik situs: masuk ke wp-admin lalu buka Tampilan > Lisensi M-Nata untuk mengaktifkan atau memperbarui lisensi.', 'm-nata' ); ?></small></p>
</main>
</body>
</html>
