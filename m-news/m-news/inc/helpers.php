<?php
/**
 * Generic helpers: gradient presets, colour maths, tokens.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gradient presets shown as swatches in the Customizer.
 *
 * @return array<string,array{label:string,colors:string[],dir:string}>
 */
function mnews_gradient_presets() {
	return array(
		'ungu-magenta'   => array(
			'label'  => __( 'Ungu – Magenta', 'm-news' ),
			'colors' => array( '#4a1d8f', '#b5179e' ),
			'dir'    => 'to right',
		),
		'biru-laut'      => array(
			'label'  => __( 'Biru Laut', 'm-news' ),
			'colors' => array( '#0b3d91', '#1e88e5', '#4fc3f7' ),
			'dir'    => 'to right',
		),
		'biru-ungu'      => array(
			'label'  => __( 'Biru – Ungu', 'm-news' ),
			'colors' => array( '#1b5daf', '#562b77' ),
			'dir'    => 'to right',
		),
		'merah-jingga'   => array(
			'label'  => __( 'Merah – Jingga', 'm-news' ),
			'colors' => array( '#c72026', '#ff6a00' ),
			'dir'    => 'to right',
		),
		'jingga-biru'    => array(
			'label'  => __( 'Jingga – Merah – Biru', 'm-news' ),
			'colors' => array( '#e08a00', '#b21f1f', '#1a2a6c' ),
			'dir'    => '135deg',
		),
		'hijau-tosca'    => array(
			'label'  => __( 'Hijau Tosca', 'm-news' ),
			'colors' => array( '#00796b', '#26c6a0' ),
			'dir'    => 'to right',
		),
		'hijau-kuning'   => array(
			'label'  => __( 'Hijau – Kuning', 'm-news' ),
			'colors' => array( '#2e7d32', '#9e9d24' ),
			'dir'    => 'to right',
		),
		'merah-marun'    => array(
			'label'  => __( 'Merah Marun', 'm-news' ),
			'colors' => array( '#6a0000', '#c62828' ),
			'dir'    => 'to right',
		),
		'pink-ungu-biru' => array(
			'label'  => __( 'Pink – Ungu – Biru', 'm-news' ),
			'colors' => array( '#d81b7a', '#7c3aed', '#2563eb' ),
			'dir'    => '135deg',
		),
		'teal-jingga'    => array(
			'label'  => __( 'Teal – Jingga', 'm-news' ),
			'colors' => array( '#0a7f8c', '#e65100' ),
			'dir'    => 'to right',
		),
		'biru-gelap'     => array(
			'label'  => __( 'Biru Gelap', 'm-news' ),
			'colors' => array( '#0f1b2d', '#2a4365' ),
			'dir'    => 'to right',
		),
		'hitam-elegan'   => array(
			'label'  => __( 'Hitam Elegan', 'm-news' ),
			'colors' => array( '#0d0d0d', '#3b3b3b' ),
			'dir'    => 'to right',
		),
	);
}

/**
 * Allowed gradient directions.
 *
 * @return array<string,string>
 */
function mnews_gradient_directions() {
	return array(
		'to right'  => __( 'Kiri ke kanan', 'm-news' ),
		'to bottom' => __( 'Atas ke bawah', 'm-news' ),
		'135deg'    => __( 'Diagonal (kiri atas ke kanan bawah)', 'm-news' ),
		'45deg'     => __( 'Diagonal (kiri bawah ke kanan atas)', 'm-news' ),
	);
}

/**
 * Convert #rrggbb to an [r,g,b] array (0-255).
 *
 * @param string $hex Colour.
 * @return int[]
 */
function mnews_hex_to_rgb( $hex ) {
	$hex = ltrim( (string) $hex, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	return array(
		hexdec( substr( $hex, 0, 2 ) ),
		hexdec( substr( $hex, 2, 2 ) ),
		hexdec( substr( $hex, 4, 2 ) ),
	);
}

/**
 * WCAG relative luminance of a hex colour.
 *
 * @param string $hex Colour.
 * @return float
 */
function mnews_luminance( $hex ) {
	$rgb = array_map(
		static function ( $c ) {
			$c = $c / 255;
			return ( $c <= 0.03928 ) ? $c / 12.92 : pow( ( $c + 0.055 ) / 1.055, 2.4 );
		},
		mnews_hex_to_rgb( $hex )
	);
	return 0.2126 * $rgb[0] + 0.7152 * $rgb[1] + 0.0722 * $rgb[2];
}

/**
 * WCAG contrast ratio between two hex colours.
 *
 * @param string $a Colour.
 * @param string $b Colour.
 * @return float
 */
function mnews_contrast( $a, $b ) {
	$la = mnews_luminance( $a );
	$lb = mnews_luminance( $b );
	return ( max( $la, $lb ) + 0.05 ) / ( min( $la, $lb ) + 0.05 );
}

/**
 * Darken a hex colour by mixing it with black.
 *
 * @param string $hex    Colour.
 * @param float  $amount 0-1.
 * @return string
 */
function mnews_darken( $hex, $amount ) {
	$rgb = mnews_hex_to_rgb( $hex );
	return sprintf(
		'#%02x%02x%02x',
		(int) round( $rgb[0] * ( 1 - $amount ) ),
		(int) round( $rgb[1] * ( 1 - $amount ) ),
		(int) round( $rgb[2] * ( 1 - $amount ) )
	);
}

/**
 * Resolve the active colour scheme (preset or custom) into ready-to-print values.
 *
 * @return array{colors:string[],direction:string,css:string,on:string,accent_text:string}
 */
function mnews_get_gradient() {
	$presets = mnews_gradient_presets();
	$key     = get_theme_mod( 'mnews_grad_preset', 'ungu-magenta' );
	$colors  = array();
	$dir     = 'to right';

	if ( 'custom' === $key ) {
		foreach ( array( 1, 2, 3, 4 ) as $i ) {
			$c = sanitize_hex_color( (string) get_theme_mod( "mnews_grad_c{$i}", '' ) );
			if ( $c ) {
				$colors[] = $c;
			}
		}
		$dirs = mnews_gradient_directions();
		$dir  = get_theme_mod( 'mnews_grad_dir', 'to right' );
		$dir  = isset( $dirs[ $dir ] ) ? $dir : 'to right';
	}

	if ( empty( $colors ) ) {
		$preset = isset( $presets[ $key ] ) ? $presets[ $key ] : $presets['ungu-magenta'];
		$colors = $preset['colors'];
		$dir    = $preset['dir'];
	}

	$stops = 1 === count( $colors ) ? array( $colors[0], $colors[0] ) : $colors;

	// Text colour: whichever of white / near-black has the better worst-case contrast over all stops.
	$min_white = 21;
	$min_dark  = 21;
	foreach ( $colors as $c ) {
		$min_white = min( $min_white, mnews_contrast( $c, '#ffffff' ) );
		$min_dark  = min( $min_dark, mnews_contrast( $c, '#111111' ) );
	}

	// Accent as text on white: darken until it reaches 4.5:1.
	$accent = $colors[0];
	$steps  = 0;
	while ( $steps < 20 && mnews_contrast( $accent, '#ffffff' ) < 4.5 ) {
		$accent = mnews_darken( $accent, 0.1 );
		++$steps;
	}

	return array(
		'colors'      => $colors,
		'direction'   => $dir,
		'css'         => 'linear-gradient(' . $dir . ',' . implode( ',', $stops ) . ')',
		'on'          => ( $min_white >= $min_dark ) ? '#ffffff' : '#111111',
		'accent_text' => $accent,
	);
}

/**
 * CSS custom properties for the active colour scheme. Printed once as inline CSS.
 *
 * @return string
 */
function mnews_tokens_css() {
	$g   = mnews_get_gradient();
	$out = ':root{';

	foreach ( $g['colors'] as $i => $c ) {
		$out .= '--mnw-c' . ( $i + 1 ) . ':' . $c . ';';
	}
	$out .= '--mnw-grad:' . $g['css'] . ';';
	$out .= '--mnw-accent:' . $g['colors'][0] . ';';
	$out .= '--mnw-accent-text:' . $g['accent_text'] . ';';
	$out .= '--mnw-on-grad:' . $g['on'] . ';';

	$targets = array(
		'nav'    => array( 'mnews_grad_nav', true ),
		'footer' => array( 'mnews_grad_footer', true ),
		'btn'    => array( 'mnews_grad_button', true ),
		'block'  => array( 'mnews_grad_block', true ),
		'title'  => array( 'mnews_grad_title', false ),
	);
	foreach ( $targets as $name => $cfg ) {
		$use  = (bool) get_theme_mod( $cfg[0], $cfg[1] );
		$out .= '--mnw-' . $name . ( 'title' === $name ? '-line' : '-bg' ) . ':' . ( $use ? 'var(--mnw-grad)' : 'var(--mnw-c1)' ) . ';';
	}

	return $out . '}';
}

/**
 * Cache-busting version for a theme asset.
 *
 * @param string $rel Path relative to the theme root.
 * @return string
 */
function mnews_asset_ver( $rel ) {
	$path = MNEWS_DIR . '/' . ltrim( $rel, '/' );
	return file_exists( $path ) ? (string) filemtime( $path ) : MNEWS_VERSION;
}

/**
 * URL and cache-busting version of a theme asset. Serves the minified twin (x.min.css / x.min.js, built by
 * tools/build-assets.js) when it exists, unless SCRIPT_DEBUG is on.
 *
 * @param string $rel Path relative to the theme root, e.g. assets/css/main.css.
 * @return array{0:string,1:string} URL and version.
 */
function mnews_asset( $rel ) {
	$rel = ltrim( $rel, '/' );
	$min = preg_replace( '/\.(css|js)$/', '.min.$1', $rel );
	if ( ! ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) && file_exists( MNEWS_DIR . '/' . $min ) ) {
		$rel = $min;
	}
	return array( MNEWS_URI . '/' . $rel, mnews_asset_ver( $rel ) );
}
