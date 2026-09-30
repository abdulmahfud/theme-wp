<?php
/**
 * M-Nata theme bootstrap. Keep this file as a loader only.
 *
 * @package M_Nata
 */

defined( 'ABSPATH' ) || exit;

define( 'MNATA_VERSION', wp_get_theme()->get( 'Version' ) );
define( 'MNATA_DIR', get_template_directory() );
define( 'MNATA_URI', get_template_directory_uri() );

$mnata_includes = array(
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

foreach ( $mnata_includes as $mnata_file ) {
	require_once MNATA_DIR . '/' . $mnata_file;
}

// Licence screen (Appearance > Lisensi M-Nata).
if ( is_admin() ) {
	require_once MNATA_DIR . '/inc/license/admin.php';
}

// Demo import (Appearance > Impor Demo): admin screens and WP-CLI only, never on the front end.
if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
	require_once MNATA_DIR . '/inc/demo/data.php';
	require_once MNATA_DIR . '/inc/demo/importer.php';
	if ( is_admin() ) {
		require_once MNATA_DIR . '/inc/demo/admin.php';
	}
}

unset( $mnata_includes, $mnata_file );
