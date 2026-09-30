<?php
/**
 * Customizer: header, footer and performance options.
 *
 * @package M_Nata
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register header/footer/performance settings.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function mnata_customize_layout( $wp_customize ) {
	// Header.
	$wp_customize->add_setting(
		'mnata_sticky_header',
		array(
			'default'           => true,
			'sanitize_callback' => 'mnata_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'mnata_sticky_header',
		array(
			'label'   => __( 'Header menempel di atas saat scroll', 'm-nata' ),
			'section' => 'mnata_header',
			'type'    => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'mnata_ticker_on',
		array(
			'default'           => false,
			'sanitize_callback' => 'mnata_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'mnata_ticker_on',
		array(
			'label'   => __( 'Tampilkan ticker breaking news', 'm-nata' ),
			'section' => 'mnata_header',
			'type'    => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'mnata_ticker_label',
		array(
			'default'           => __( 'Terbaru', 'm-nata' ),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'mnata_ticker_label',
		array(
			'label'   => __( 'Label ticker', 'm-nata' ),
			'section' => 'mnata_header',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'mnata_ticker_tag',
		array(
			'default'           => '',
			'sanitize_callback' => 'mnata_sanitize_slug',
		)
	);
	$wp_customize->add_control(
		'mnata_ticker_tag',
		array(
			'label'       => __( 'Slug tag sumber ticker', 'm-nata' ),
			'description' => __( 'Contoh: breaking-news. Kosongkan untuk memakai berita terbaru.', 'm-nata' ),
			'section'     => 'mnata_header',
			'type'        => 'text',
		)
	);

	$wp_customize->add_setting(
		'mnata_ticker_count',
		array(
			'default'           => 5,
			'sanitize_callback' => 'mnata_sanitize_ticker_count',
		)
	);
	$wp_customize->add_control(
		'mnata_ticker_count',
		array(
			'label'       => __( 'Jumlah berita di ticker', 'm-nata' ),
			'section'     => 'mnata_header',
			'type'        => 'number',
			'input_attrs' => array(
				'min' => 1,
				'max' => 10,
			),
		)
	);

	$wp_customize->add_setting(
		'mnata_tz_label',
		array(
			'default'           => 'WIB',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'mnata_tz_label',
		array(
			'label'       => __( 'Keterangan zona waktu', 'm-nata' ),
			'description' => __( 'Ditampilkan setelah jam terbit, mis. WIB / WITA / WIT. Kosongkan untuk menyembunyikan.', 'm-nata' ),
			'section'     => 'mnata_header',
			'type'        => 'text',
		)
	);

	// Footer.
	$wp_customize->add_setting(
		'mnata_footer_address',
		array(
			'default'           => '',
			'sanitize_callback' => 'wp_kses_post',
		)
	);
	$wp_customize->add_control(
		'mnata_footer_address',
		array(
			'label'   => __( 'Alamat / info redaksi', 'm-nata' ),
			'section' => 'mnata_footer',
			'type'    => 'textarea',
		)
	);

	$wp_customize->add_setting(
		'mnata_network_title',
		array(
			'default'           => __( 'MEDIA NETWORK', 'm-nata' ),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'mnata_network_title',
		array(
			'label'   => __( 'Judul menu media network', 'm-nata' ),
			'section' => 'mnata_footer',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'mnata_copyright',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'mnata_copyright',
		array(
			'label'       => __( 'Teks copyright', 'm-nata' ),
			'description' => __( 'Kosongkan untuk teks otomatis: © tahun + nama situs.', 'm-nata' ),
			'section'     => 'mnata_footer',
			'type'        => 'text',
		)
	);

	// Performance.
	$wp_customize->add_setting(
		'mnata_clean_wp',
		array(
			'default'           => true,
			'sanitize_callback' => 'mnata_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'mnata_clean_wp',
		array(
			'label'       => __( 'Bersihkan bawaan WordPress yang tidak perlu', 'm-nata' ),
			'description' => __( 'Hapus emoji, wp-embed, meta generator, dan CSS blok di halaman non-artikel agar lebih cepat. Matikan jika sebuah plugin bermasalah.', 'm-nata' ),
			'section'     => 'mnata_perf',
			'type'        => 'checkbox',
		)
	);
}
add_action( 'customize_register', 'mnata_customize_layout' );
