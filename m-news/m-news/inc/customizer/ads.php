<?php
/**
 * Customizer: global banner options. The banners themselves are "Banner Iklan" widgets.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register ad settings.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function mnews_customize_ads( $wp_customize ) {
	$wp_customize->get_section( 'mnews_ads' )->description = sprintf(
		/* translators: %s: list of sizes and ratios. */
		__( 'Pasang banner lewat Tampilan > Widget memakai widget "M-News: Banner Iklan" di area yang diinginkan (header, samping 160×600, sidebar, dalam artikel, setelah artikel). Ukuran standar (rasio): %s.', 'm-news' ),
		mnews_ad_sizes_help()
	);

	$wp_customize->add_setting(
		'mnews_ad_label_on',
		array(
			'default'           => true,
			'sanitize_callback' => 'mnews_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'mnews_ad_label_on',
		array(
			'label'   => __( 'Tampilkan label di atas banner', 'm-news' ),
			'section' => 'mnews_ads',
			'type'    => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'mnews_ad_label',
		array(
			'default'           => __( 'Iklan', 'm-news' ),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'mnews_ad_label',
		array(
			'label'   => __( 'Teks label banner', 'm-news' ),
			'section' => 'mnews_ads',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'mnews_side_sticky',
		array(
			'default'           => true,
			'sanitize_callback' => 'mnews_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'mnews_side_sticky',
		array(
			'label'       => __( 'Banner samping kiri/kanan tetap tampil saat halaman digulir (sticky)', 'm-news' ),
			'description' => __( 'Hanya di layar sangat lebar (≥ 1560 px), tempat banner 160×600 tampil.', 'm-news' ),
			'section'     => 'mnews_ads',
			'type'        => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'mnews_ad_in_article_after',
		array(
			'default'           => 3,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		'mnews_ad_in_article_after',
		array(
			'label'       => __( 'Sisipkan banner dalam artikel setelah paragraf ke-', 'm-news' ),
			'description' => __( 'Untuk widget di area "Artikel – Di Dalam Konten".', 'm-news' ),
			'section'     => 'mnews_ads',
			'type'        => 'number',
			'input_attrs' => array(
				'min' => 1,
				'max' => 20,
			),
		)
	);
}
add_action( 'customize_register', 'mnews_customize_ads' );
