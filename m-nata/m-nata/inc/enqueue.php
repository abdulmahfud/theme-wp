<?php
/**
 * Front-end assets: one CSS file, one deferred JS file, colour tokens inline.
 *
 * @package M_Nata
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue theme assets.
 */
function mnata_enqueue() {
	wp_enqueue_style( 'mnata-main', mnata_asset( 'assets/css/main.css' )[0], array(), mnata_asset( 'assets/css/main.css' )[1] );
	wp_add_inline_style( 'mnata-main', mnata_tokens_css() );

	// AMP pages may not run author JavaScript: CSS only.
	if ( mnata_is_amp() ) {
		return;
	}

	wp_enqueue_script(
		'mnata-main',
		mnata_asset( 'assets/js/main.js' )[0],
		array(),
		mnata_asset( 'assets/js/main.js' )[1],
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'mnata_enqueue' );

/**
 * Flag JS support before first paint so CSS can hide the mobile menu only when JS can open it.
 */
function mnata_js_flag() {
	if ( mnata_is_amp() ) {
		return;
	}
	echo "<script>document.documentElement.className+=' js';</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'wp_head', 'mnata_js_flag', 1 );
