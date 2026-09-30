<?php
/**
 * Customizer: colour scheme (gradient presets + custom stops).
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register colour settings and controls.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function mnews_customize_colors( $wp_customize ) {
	$wp_customize->add_setting(
		'mnews_grad_preset',
		array(
			'default'           => 'ungu-magenta',
			'sanitize_callback' => 'mnews_sanitize_gradient_preset',
		)
	);
	$wp_customize->add_control(
		new MNews_Gradient_Preset_Control(
			$wp_customize,
			'mnews_grad_preset',
			array(
				'label'       => __( 'Kombinasi gradient', 'm-news' ),
				'description' => __( 'Pilih salah satu, atau "Kustom" untuk memilih 2–4 warna sendiri.', 'm-news' ),
				'section'     => 'mnews_colors',
			)
		)
	);

	$is_custom = static function () use ( $wp_customize ) {
		return 'custom' === $wp_customize->get_setting( 'mnews_grad_preset' )->value();
	};

	$defaults = array(
		1 => '#4a1d8f',
		2 => '#b5179e',
		3 => '',
		4 => '',
	);
	foreach ( $defaults as $i => $default ) {
		/* translators: %d: colour number. */
		$label_optional = sprintf( __( 'Warna %d (opsional)', 'm-news' ), $i );
		/* translators: %d: colour number. */
		$label_required = sprintf( __( 'Warna %d', 'm-news' ), $i );

		$wp_customize->add_setting(
			"mnews_grad_c{$i}",
			array(
				'default'           => $default,
				'sanitize_callback' => 'mnews_sanitize_optional_hex',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				"mnews_grad_c{$i}",
				array(
					'label'           => $i > 2 ? $label_optional : $label_required,
					'section'         => 'mnews_colors',
					'active_callback' => $is_custom,
				)
			)
		);
	}

	$wp_customize->add_setting(
		'mnews_grad_dir',
		array(
			'default'           => 'to right',
			'sanitize_callback' => 'mnews_sanitize_gradient_dir',
		)
	);
	$wp_customize->add_control(
		'mnews_grad_dir',
		array(
			'label'           => __( 'Arah gradient', 'm-news' ),
			'section'         => 'mnews_colors',
			'type'            => 'select',
			'choices'         => mnews_gradient_directions(),
			'active_callback' => $is_custom,
		)
	);

	$targets = array(
		'nav'    => array( __( 'Bar menu utama', 'm-news' ), true ),
		'footer' => array( __( 'Footer', 'm-news' ), true ),
		'button' => array( __( 'Tombol & label', 'm-news' ), true ),
		'block'  => array( __( 'Latar Blok Kategori', 'm-news' ), true ),
		'title'  => array( __( 'Garis judul blok', 'm-news' ), false ),
	);
	foreach ( $targets as $key => $cfg ) {
		$wp_customize->add_setting(
			"mnews_grad_{$key}",
			array(
				'default'           => $cfg[1],
				'sanitize_callback' => 'mnews_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			"mnews_grad_{$key}",
			array(
				/* translators: %s: element name. */
				'label'   => sprintf( __( 'Gradient pada: %s', 'm-news' ), $cfg[0] ),
				'section' => 'mnews_colors',
				'type'    => 'checkbox',
			)
		);
	}
}
add_action( 'customize_register', 'mnews_customize_colors' );
