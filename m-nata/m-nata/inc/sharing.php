<?php
/**
 * Social share buttons. Plain links (no SDK, no third-party JS); JS is only used for "Salin Link".
 * Configured in Customizer > M-Nata > Social Media Share.
 *
 * @package M_Nata
 */

defined( 'ABSPATH' ) || exit;

/**
 * Share networks: id => [ label, icon, brand colour, enabled by default ].
 *
 * @return array<string,array{0:string,1:string,2:string,3:bool}>
 */
function mnata_share_networks() {
	return array(
		'facebook'  => array( 'Facebook', 'facebook', '#1877f2', true ),
		'x'         => array( 'X', 'x', '#111111', true ),
		'whatsapp'  => array( 'WhatsApp', 'whatsapp', '#1fa855', true ),
		'telegram'  => array( 'Telegram', 'telegram', '#229ed9', true ),
		'line'      => array( 'LINE', 'line', '#06a94d', false ),
		'linkedin'  => array( 'LinkedIn', 'linkedin', '#0a66c2', false ),
		'pinterest' => array( 'Pinterest', 'pinterest', '#e60023', false ),
		'email'     => array( __( 'Email', 'm-nata' ), 'mail', '#666666', false ),
		'copy'      => array( __( 'Salin Link', 'm-nata' ), 'link', '#555555', true ),
	);
}

/**
 * Default order string.
 *
 * @return string
 */
function mnata_share_default_order() {
	return implode( ',', array_keys( mnata_share_networks() ) );
}

/**
 * Networks to show, in the configured order.
 *
 * @return string[]
 */
function mnata_share_active() {
	$nets  = mnata_share_networks();
	$order = array_filter( array_map( 'trim', explode( ',', (string) get_theme_mod( 'mnata_share_order', mnata_share_default_order() ) ) ) );
	// Networks missing from the order string go last, so enabling one never makes it vanish.
	$order = array_unique( array_merge( $order, array_keys( $nets ) ) );

	$out = array();
	foreach ( $order as $id ) {
		if ( isset( $nets[ $id ] ) && get_theme_mod( 'mnata_share_' . $id, $nets[ $id ][3] ) ) {
			$out[] = $id;
		}
	}
	return $out;
}

/**
 * Build the share URL for a network.
 *
 * @param string $id    Network id.
 * @param int    $post_id Post ID.
 * @return string
 */
function mnata_share_url( $id, $post_id ) {
	$url   = get_permalink( $post_id );
	$title = html_entity_decode( wp_strip_all_tags( get_the_title( $post_id ) ), ENT_QUOTES, get_bloginfo( 'charset' ) );

	$tpl    = (string) get_theme_mod( 'mnata_share_text', '{title} - {url}' );
	$titled = str_replace( '{title}', $title, $tpl );
	$full   = trim( str_replace( '{url}', $url, $titled ) );
	$plain  = trim( rtrim( trim( str_replace( '{url}', '', $titled ) ), ' -–|:' ) );

	$u = rawurlencode( $url );

	switch ( $id ) {
		case 'facebook':
			return 'https://www.facebook.com/sharer/sharer.php?u=' . $u;
		case 'x':
			return 'https://twitter.com/intent/tweet?text=' . rawurlencode( $plain ) . '&url=' . $u;
		case 'whatsapp':
			return 'https://wa.me/?text=' . rawurlencode( $full );
		case 'telegram':
			return 'https://t.me/share/url?url=' . $u . '&text=' . rawurlencode( $plain );
		case 'line':
			return 'https://social-plugins.line.me/lineit/share?url=' . $u;
		case 'linkedin':
			return 'https://www.linkedin.com/sharing/share-offsite/?url=' . $u;
		case 'pinterest':
			$img = get_the_post_thumbnail_url( $post_id, 'large' );
			return 'https://pinterest.com/pin/create/button/?url=' . $u . '&description=' . rawurlencode( $plain ) . ( $img ? '&media=' . rawurlencode( $img ) : '' );
		case 'email':
			return 'mailto:?subject=' . rawurlencode( $title ) . '&body=' . rawurlencode( $full );
	}
	return $url;
}

/**
 * Echo the share bar.
 *
 * @param string $position top|bottom|float (only changes the CSS class).
 */
function mnata_share_buttons( $position = 'top' ) {
	$active = mnata_share_active();
	if ( ! $active || ! is_singular() ) {
		return;
	}

	$nets    = mnata_share_networks();
	$style   = get_theme_mod( 'mnata_share_style', 'brand' );
	$labels  = get_theme_mod( 'mnata_share_labels', false );
	$post_id = get_the_ID();

	printf(
		'<div class="mn-share mn-share--%1$s mn-share--%2$s%3$s" role="group" aria-label="%4$s">',
		esc_attr( in_array( $style, array( 'brand', 'theme', 'outline' ), true ) ? $style : 'brand' ),
		esc_attr( $position ),
		$labels ? ' mn-share--labels' : '',
		esc_attr__( 'Bagikan artikel', 'm-nata' )
	);
	printf( '<span class="mn-share__title">%s</span><ul>', esc_html__( 'Bagikan', 'm-nata' ) );

	foreach ( $active as $id ) {
		// "Copy link" needs JavaScript (not available on AMP).
		if ( 'copy' === $id && mnata_is_amp() ) {
			continue;
		}
		list( $label, $icon, $color ) = $nets[ $id ];

		ob_start();
		mnata_icon( $icon, 18 );
		$svg  = ob_get_clean();
		$text = $labels ? '<span class="mn-share__text">' . esc_html( $label ) . '</span>' : '';
		/* translators: %s: network name. */
		$aria = ( 'copy' === $id ) ? esc_attr__( 'Salin tautan artikel', 'm-nata' ) : esc_attr( sprintf( __( 'Bagikan ke %s', 'm-nata' ), $label ) );

		if ( 'copy' === $id ) {
			printf(
				'<li><button type="button" class="mn-share__btn" style="--c:%1$s" data-mn-copy="%2$s" data-copied="%3$s" aria-label="%4$s">%5$s%6$s</button></li>',
				esc_attr( $color ),
				esc_url( get_permalink( $post_id ) ),
				esc_attr__( 'Tersalin!', 'm-nata' ),
				$aria, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
				$svg, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built by mnata_icon().
				$text // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
			);
		} else {
			printf(
				'<li><a class="mn-share__btn" style="--c:%1$s" href="%2$s" target="_blank" rel="noopener noreferrer" aria-label="%3$s">%4$s%5$s</a></li>',
				esc_attr( $color ),
				esc_url( mnata_share_url( $id, $post_id ) ),
				$aria, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
				$svg, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built by mnata_icon().
				$text // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
			);
		}
	}
	echo '</ul></div>';
}

/**
 * Add a body class when the floating mobile bar is on (reserves space at the bottom).
 *
 * @param string[] $classes Classes.
 * @return string[]
 */
function mnata_share_body_class( $classes ) {
	if ( ! mnata_is_amp() && is_singular( 'post' ) && get_theme_mod( 'mnata_share_float', false ) && 'none' !== get_theme_mod( 'mnata_share_position', 'top' ) ) {
		$classes[] = 'mn-has-share-float';
	}
	return $classes;
}
add_filter( 'body_class', 'mnata_share_body_class' );
