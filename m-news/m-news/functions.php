<?php
/**
 * M-News theme bootstrap. Keep this file as a loader only.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

define( 'MNEWS_VERSION', wp_get_theme()->get( 'Version' ) );
define( 'MNEWS_DIR', get_template_directory() );
define( 'MNEWS_URI', get_template_directory_uri() );

$mnews_includes = array(
	'inc/helpers.php',
	'inc/cache.php',
	'inc/icons.php',
	'inc/license/config.php',
	'inc/license/license.php',
	'inc/license/gate.php',
	'inc/license/updates.php',
	'inc/setup.php',
	'inc/amp.php',
	'inc/image-sizes.php',
	'inc/enqueue.php',
	'inc/performance.php',
	'inc/template-tags.php',
	'inc/widget-areas.php',
	'inc/render.php',
	'inc/ads.php',
	'inc/bmkg.php',
	'inc/meta.php',
	'inc/related.php',
	'inc/breadcrumbs.php',
	'inc/sharing.php',
	'inc/content.php',
	'inc/seo.php',
	'inc/admin.php',
	'inc/stats.php',
	'inc/polling.php',
	'inc/nav-bottom.php',
	'inc/widgets/init.php',
	'inc/customizer/sanitize.php',
	'inc/customizer/controls.php',
	'inc/customizer/panel.php',
	'inc/customizer/colors.php',
	'inc/customizer/social.php',
	'inc/customizer/layout.php',
	'inc/customizer/ads.php',
	'inc/customizer/share.php',
);

foreach ( $mnews_includes as $mnews_file ) {
	require_once MNEWS_DIR . '/' . $mnews_file;
}

// Licence screen (Appearance > Lisensi M-News).
if ( is_admin() ) {
	require_once MNEWS_DIR . '/inc/license/admin.php';
}

// Demo import (Appearance > Impor Demo): admin screens and WP-CLI only, never on the front end.
if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
	require_once MNEWS_DIR . '/inc/demo/data.php';
	require_once MNEWS_DIR . '/inc/demo/importer.php';
	if ( is_admin() ) {
		require_once MNEWS_DIR . '/inc/demo/admin.php';
	}
}

unset( $mnews_includes, $mnews_file );
