<?php
/**
 * Customizer: social media follow links.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register one URL setting per network.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function mnews_customize_social( $wp_customize ) {
	foreach ( mnews_social_networks() as $id => $label ) {
		$wp_customize->add_setting(
			'mnews_social_' . $id,
			array(
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
			)
		);
		$wp_customize->add_control(
			'mnews_social_' . $id,
			array(
				'label'       => $label,
				'section'     => 'mnews_social',
				'type'        => 'url',
				'input_attrs' => array( 'placeholder' => 'https://' ),
			)
		);
	}
}
add_action( 'customize_register', 'mnews_customize_social' );
