<?php
/**
 * Customizer panel and sections.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the M-News panel and its sections. Runs before the section files add settings.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function mnews_customize_panel( $wp_customize ) {
	$wp_customize->add_panel(
		'mnews_panel',
		array(
			'title'    => __( 'M-News', 'm-news' ),
			'priority' => 30,
		)
	);

	$sections = array(
		'mnews_colors'  => __( 'Warna & Gradient', 'm-news' ),
		'mnews_header'  => __( 'Header', 'm-news' ),
		'mnews_social'  => __( 'Social Media Follow', 'm-news' ),
		'mnews_share'   => __( 'Social Media Share', 'm-news' ),
		'mnews_ads'     => __( 'Banner Iklan', 'm-news' ),
		'mnews_article' => __( 'Artikel', 'm-news' ),
		'mnews_footer'  => __( 'Footer', 'm-news' ),
		'mnews_perf'    => __( 'Performa', 'm-news' ),
	);

	$priority = 10;
	foreach ( $sections as $id => $title ) {
		$wp_customize->add_section(
			$id,
			array(
				'title'    => $title,
				'panel'    => 'mnews_panel',
				'priority' => $priority,
			)
		);
		$priority += 10;
	}
}
add_action( 'customize_register', 'mnews_customize_panel', 5 );
