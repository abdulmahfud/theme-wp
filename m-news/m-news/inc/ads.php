<?php
/**
 * Ad slot helpers: standard sizes with their aspect ratio.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

/**
 * Standard banner sizes (key => width, height, name).
 *
 * @return array<string,array{0:int,1:int,2:string}>
 */
function mnews_ad_sizes() {
	return array(
		'300x250' => array( 300, 250, __( 'Medium Rectangle', 'm-news' ) ),
		'336x280' => array( 336, 280, __( 'Large Rectangle', 'm-news' ) ),
		'300x600' => array( 300, 600, __( 'Half Page', 'm-news' ) ),
		'160x600' => array( 160, 600, __( 'Wide Skyscraper', 'm-news' ) ),
		'728x90'  => array( 728, 90, __( 'Leaderboard', 'm-news' ) ),
		'970x90'  => array( 970, 90, __( 'Large Leaderboard', 'm-news' ) ),
		'970x250' => array( 970, 250, __( 'Billboard', 'm-news' ) ),
		'468x60'  => array( 468, 60, __( 'Banner', 'm-news' ) ),
		'320x100' => array( 320, 100, __( 'Large Mobile Banner', 'm-news' ) ),
		'320x50'  => array( 320, 50, __( 'Mobile Banner', 'm-news' ) ),
	);
}

/**
 * Human readable ratio: reduced fraction when small (6:5), otherwise decimal (8.09:1).
 *
 * @param int $w Width.
 * @param int $h Height.
 * @return string
 */
function mnews_ad_ratio( $w, $h ) {
	$a = $w;
	$b = $h;
	while ( $b ) {
		$t = $b;
		$b = $a % $b;
		$a = $t;
	}
	$rw = $w / $a;
	$rh = $h / $a;
	if ( $rw <= 20 && $rh <= 20 ) {
		return $rw . ':' . $rh;
	}
	return rtrim( rtrim( number_format( $w / $h, 2, '.', '' ), '0' ), '.' ) . ':1';
}

/**
 * Select choices: "300×250 · 6:5 — Medium Rectangle".
 *
 * @return array<string,string>
 */
function mnews_ad_size_choices() {
	$out = array();
	foreach ( mnews_ad_sizes() as $key => $s ) {
		$out[ $key ] = sprintf( '%1$d×%2$d · %3$s — %4$s', $s[0], $s[1], mnews_ad_ratio( $s[0], $s[1] ), $s[2] );
	}
	$out['custom'] = __( 'Ukuran kustom (isi lebar & tinggi)', 'm-news' );
	return $out;
}

/**
 * Resolve [width, height] from a preset key or custom values.
 *
 * @param string $key Preset key or "custom".
 * @param int    $w   Custom width.
 * @param int    $h   Custom height.
 * @return int[]
 */
function mnews_ad_dimensions( $key, $w, $h ) {
	$sizes = mnews_ad_sizes();
	if ( isset( $sizes[ $key ] ) ) {
		return array( $sizes[ $key ][0], $sizes[ $key ][1] );
	}
	return array( max( 1, (int) $w ), max( 1, (int) $h ) );
}

/**
 * Text listing every size with its ratio (used in Customizer help text).
 *
 * @return string
 */
function mnews_ad_sizes_help() {
	$lines = array();
	foreach ( mnews_ad_sizes() as $s ) {
		$lines[] = sprintf( '%1$d×%2$d (%3$s)', $s[0], $s[1], mnews_ad_ratio( $s[0], $s[1] ) );
	}
	return implode( ' · ', $lines );
}

/**
 * Enqueue the slot script (call from a widget's assets() so it also runs on cache hits).
 */
function mnews_enqueue_slot() {
	if ( mnews_is_amp() ) {
		return;
	}
	wp_enqueue_script(
		'mnews-slot',
		mnews_asset( 'assets/js/slot.js' )[0],
		array(),
		mnews_asset( 'assets/js/slot.js' )[1],
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);
}

/**
 * Print code (ad/embed) inside a lazy slot. The code stays in a <template> until the slot is
 * near the viewport, and is skipped entirely on devices that do not match $media.
 *
 * @param string $code  Already sanitised HTML/script.
 * @param string $style Inline style for the reserved space (max-width / min-height).
 * @param string $media Media query the slot is for, or empty for all devices.
 * @param bool   $lazy  Whether to wait until visible.
 */
function mnews_render_slot( $code, $style = '', $media = '', $lazy = true ) {
	// Ad/embed code needs JavaScript, which AMP pages do not allow.
	if ( mnews_is_amp() ) {
		return;
	}
	printf(
		'<div class="mnw-ad__box mnw-ad__box--code" data-mnw-slot data-lazy="%1$d"%2$s style="%3$s"><template>%4$s</template></div>',
		$lazy ? 1 : 0,
		$media ? ' data-media="' . esc_attr( $media ) . '"' : '',
		esc_attr( $style ),
		$code // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sanitised when the widget was saved (unfiltered_html or wp_kses_post).
	);
}
