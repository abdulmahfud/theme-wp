<?php
/**
 * Customizer panel and sections.
 *
 * @package M_Nata
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the M-Nata panel and its sections. Runs before the section files add settings.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function mnata_customize_panel( $wp_customize ) {
	$wp_customize->add_panel(
		'mnata_panel',
		array(
			'title'    => __( 'M-Nata', 'm-nata' ),
			'priority' => 30,
		)
	);

	$sections = array(
		'mnata_colors'  => __( 'Warna & Gradient', 'm-nata' ),
		'mnata_header'  => __( 'Header', 'm-nata' ),
		'mnata_social'  => __( 'Social Media Follow', 'm-nata' ),
		'mnata_share'   => __( 'Social Media Share', 'm-nata' ),
		'mnata_ads'     => __( 'Banner Iklan', 'm-nata' ),
		'mnata_article' => __( 'Artikel', 'm-nata' ),
		'mnata_footer'  => __( 'Footer', 'm-nata' ),
		'mnata_perf'    => __( 'Performa', 'm-nata' ),
	);

	$priority = 10;
	foreach ( $sections as $id => $title ) {
		$wp_customize->add_section(
			$id,
			array(
				'title'    => $title,
				'panel'    => 'mnata_panel',
				'priority' => $priority,
			)
		);
		$priority += 10;
	}
}
add_action( 'customize_register', 'mnata_customize_panel', 5 );
