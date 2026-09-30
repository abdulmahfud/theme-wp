<?php
/**
 * Licence enforcement. Without a valid licence the public site shows a "licence required" page (HTTP 503) instead of the
 * theme, and the theme's features stay off. Logged-in administrators still see the site so they can activate the licence,
 * and wp-admin / wp-login / REST / AJAX / cron are never blocked.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

/**
 * Replace the public front end with the locked page while the licence is not valid.
 */
function mnews_license_gate() {
	if ( mnews_license_ok() || is_robots() ) {
		return;
	}

	if ( current_user_can( 'manage_options' ) ) {
		add_action( 'wp_body_open', 'mnews_license_admin_bar_notice' );
		return;
	}

	$status = mnews_license_status();
	status_header( 503 );
	nocache_headers();
	header( 'Retry-After: 3600' );
	header( 'X-Robots-Tag: noindex, nofollow' );

	// The template is self-contained on purpose: it must not depend on the theme's stylesheet or scripts.
	include MNEWS_DIR . '/template-parts/license-locked.php';
	exit;
}
add_action( 'template_redirect', 'mnews_license_gate', 1 );

/**
 * Banner for administrators viewing the front end of a locked site.
 */
function mnews_license_admin_bar_notice() {
	printf(
		'<div style="background:#b32d2e;color:#fff;padding:10px 16px;font:14px/1.4 sans-serif;text-align:center">%1$s <a style="color:#fff;font-weight:700" href="%2$s">%3$s</a></div>',
		esc_html__( 'Lisensi M-News belum valid — pengunjung melihat halaman "lisensi diperlukan".', 'm-news' ),
		esc_url( admin_url( 'themes.php?page=mnews-license' ) ),
		esc_html__( 'Aktifkan sekarang', 'm-news' )
	);
}
