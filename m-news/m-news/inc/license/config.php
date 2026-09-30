<?php
/**
 * Licence configuration.
 *
 * MNEWS_LICENSE_API can be overridden in wp-config.php (development only). The public key below verifies the
 * signature on every response from the licence server (Ed25519), so a fake server cannot unlock the theme.
 *
 * !! The key below is the DEVELOPMENT key that belongs to wp-be/mock. Before shipping, ask the backend team to
 * !! register the "m-news" product (it uses the same signing keypair as every other M-Onetech theme, see
 * !! permintaan-ke-backend.md) and paste that PUBLIC key here.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'MNEWS_LICENSE_API' ) ) {
	define( 'MNEWS_LICENSE_API', 'https://api.m-onetech.id/v1' );
}
if ( ! defined( 'MNEWS_LICENSE_PRODUCT' ) ) {
	define( 'MNEWS_LICENSE_PRODUCT', 'm-news' );
}
if ( ! defined( 'MNEWS_LICENSE_PUBKEY' ) ) {
	define( 'MNEWS_LICENSE_PUBKEY', 's0xcf+xtJWFLNCC56QBtyaoXf3r4QGrE099UfCQdNmI=' );
}
// How long the theme keeps working when the licence server cannot be reached (network outage, maintenance).
if ( ! defined( 'MNEWS_LICENSE_GRACE_DAYS' ) ) {
	define( 'MNEWS_LICENSE_GRACE_DAYS', 7 );
}
