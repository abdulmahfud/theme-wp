<?php
/**
 * Customizer: social media follow links.
 *
 * @package M_Nata
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register one URL setting per network.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function mnata_customize_social( $wp_customize ) {
	foreach ( mnata_social_networks() as $id => $label ) {
		$wp_customize->add_setting(
			'mnata_social_' . $id,
			array(
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
			)
		);
		$wp_customize->add_control(
			'mnata_social_' . $id,
			array(
				'label'       => $label,
				'section'     => 'mnata_social',
				'type'        => 'url',
				'input_attrs' => array( 'placeholder' => 'https://' ),
			)
		);
	}
}
add_action( 'customize_register', 'mnata_customize_social' );
