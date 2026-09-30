<?php
/**
 * Licence configuration.
 *
 * MNATA_LICENSE_API can be overridden in wp-config.php (development only). The public key below verifies the
 * signature on every response from the licence server (Ed25519), so a fake server cannot unlock the theme.
 *
 * The key below is the PRODUCTION public key from api.m-onetech.id (confirmed 2026-09-29, see
 * wp-be/permintaan-ke-dev-theme.md §7). It will NOT verify answers from the local mock server (wp-be/mock/), which
 * signs with its own separate development keypair (wp-be/mock/dev-keys.json) — that is expected, not a bug: the
 * mock is only for exercising the activate/verify/deactivate request flow during development, never for signature
 * testing against the real production key. If this key is ever rotated, coordinate with the backend team first
 * (see permintaan-ke-backend.md §5) — pasting a mismatched key locks out every already-active licence.
 *
 * @package M_Nata
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'MNATA_LICENSE_API' ) ) {
	define( 'MNATA_LICENSE_API', 'https://api.m-onetech.id/v1' );
}
if ( ! defined( 'MNATA_LICENSE_PRODUCT' ) ) {
	define( 'MNATA_LICENSE_PRODUCT', 'm-nata' );
}
if ( ! defined( 'MNATA_LICENSE_PUBKEY' ) ) {
	define( 'MNATA_LICENSE_PUBKEY', '0aOnUW0LHdHqLW9WVlHCKHuKkMtqWn3U8leOvmAB9+U=' );
}
// How long the theme keeps working when the licence server cannot be reached (network outage, maintenance).
if ( ! defined( 'MNATA_LICENSE_GRACE_DAYS' ) ) {
	define( 'MNATA_LICENSE_GRACE_DAYS', 7 );
}
