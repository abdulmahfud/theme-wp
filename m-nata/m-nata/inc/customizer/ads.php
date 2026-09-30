<?php
/**
 * Customizer: global banner options. The banners themselves are "Banner Iklan" widgets.
 *
 * @package M_Nata
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register ad settings.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function mnata_customize_ads( $wp_customize ) {
	$wp_customize->get_section( 'mnata_ads' )->description = sprintf(
		/* translators: %s: list of sizes and ratios. */
		__( 'Pasang banner lewat Tampilan > Widget memakai widget "M-Nata: Banner Iklan" di area yang diinginkan (header, samping 160×600, sidebar, dalam artikel, setelah artikel). Ukuran standar (rasio): %s.', 'm-nata' ),
		mnata_ad_sizes_help()
	);

	$wp_customize->add_setting(
		'mnata_ad_label_on',
		array(
			'default'           => true,
			'sanitize_callback' => 'mnata_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'mnata_ad_label_on',
		array(
			'label'   => __( 'Tampilkan label di atas banner', 'm-nata' ),
			'section' => 'mnata_ads',
			'type'    => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'mnata_ad_label',
		array(
			'default'           => __( 'Iklan', 'm-nata' ),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'mnata_ad_label',
		array(
			'label'   => __( 'Teks label banner', 'm-nata' ),
			'section' => 'mnata_ads',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'mnata_side_sticky',
		array(
			'default'           => true,
			'sanitize_callback' => 'mnata_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'mnata_side_sticky',
		array(
			'label'       => __( 'Banner samping kiri/kanan tetap tampil saat halaman digulir (sticky)', 'm-nata' ),
			'description' => __( 'Hanya di layar sangat lebar (≥ 1560 px), tempat banner 160×600 tampil.', 'm-nata' ),
			'section'     => 'mnata_ads',
			'type'        => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'mnata_ad_in_article_after',
		array(
			'default'           => 3,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		'mnata_ad_in_article_after',
		array(
			'label'       => __( 'Sisipkan banner dalam artikel setelah paragraf ke-', 'm-nata' ),
			'description' => __( 'Untuk widget di area "Artikel – Di Dalam Konten".', 'm-nata' ),
			'section'     => 'mnata_ads',
			'type'        => 'number',
			'input_attrs' => array(
				'min' => 1,
				'max' => 20,
			),
		)
	);
}
add_action( 'customize_register', 'mnata_customize_ads' );
