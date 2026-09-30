<?php
/**
 * Customizer: header, footer and performance options.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register header/footer/performance settings.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function mnews_customize_layout( $wp_customize ) {
	// Header.
	$wp_customize->add_setting(
		'mnews_sticky_header',
		array(
			'default'           => true,
			'sanitize_callback' => 'mnews_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'mnews_sticky_header',
		array(
			'label'   => __( 'Header menempel di atas saat scroll', 'm-news' ),
			'section' => 'mnews_header',
			'type'    => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'mnews_ticker_on',
		array(
			'default'           => false,
			'sanitize_callback' => 'mnews_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'mnews_ticker_on',
		array(
			'label'   => __( 'Tampilkan ticker breaking news', 'm-news' ),
			'section' => 'mnews_header',
			'type'    => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'mnews_ticker_label',
		array(
			'default'           => __( 'Terbaru', 'm-news' ),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'mnews_ticker_label',
		array(
			'label'   => __( 'Label ticker', 'm-news' ),
			'section' => 'mnews_header',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'mnews_ticker_tag',
		array(
			'default'           => '',
			'sanitize_callback' => 'mnews_sanitize_slug',
		)
	);
	$wp_customize->add_control(
		'mnews_ticker_tag',
		array(
			'label'       => __( 'Slug tag sumber ticker', 'm-news' ),
			'description' => __( 'Contoh: breaking-news. Kosongkan untuk memakai berita terbaru.', 'm-news' ),
			'section'     => 'mnews_header',
			'type'        => 'text',
		)
	);

	$wp_customize->add_setting(
		'mnews_ticker_count',
		array(
			'default'           => 5,
			'sanitize_callback' => 'mnews_sanitize_ticker_count',
		)
	);
	$wp_customize->add_control(
		'mnews_ticker_count',
		array(
			'label'       => __( 'Jumlah berita di ticker', 'm-news' ),
			'section'     => 'mnews_header',
			'type'        => 'number',
			'input_attrs' => array(
				'min' => 1,
				'max' => 10,
			),
		)
	);

	$wp_customize->add_setting(
		'mnews_navpanel_network',
		array(
			'default'           => true,
			'sanitize_callback' => 'mnews_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'mnews_navpanel_network',
		array(
			'label'       => __( 'Tampilkan menu Jaringan di panel menu mobile', 'm-news' ),
			'description' => __( 'Menu ini diisi di Tampilan > Menu (lokasi Jaringan Media) — sama seperti yang tampil di footer.', 'm-news' ),
			'section'     => 'mnews_header',
			'type'        => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'mnews_navpanel_social',
		array(
			'default'           => true,
			'sanitize_callback' => 'mnews_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'mnews_navpanel_social',
		array(
			'label'       => __( 'Tampilkan ikon sosial media di panel menu mobile', 'm-news' ),
			'description' => __( 'URL akun diisi di Sesuaikan > M-News > Social Media Follow.', 'm-news' ),
			'section'     => 'mnews_header',
			'type'        => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'mnews_tz_label',
		array(
			'default'           => 'WIB',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'mnews_tz_label',
		array(
			'label'       => __( 'Keterangan zona waktu', 'm-news' ),
			'description' => __( 'Ditampilkan setelah jam terbit, mis. WIB / WITA / WIT. Kosongkan untuk menyembunyikan.', 'm-news' ),
			'section'     => 'mnews_header',
			'type'        => 'text',
		)
	);

	// Footer.
	$wp_customize->add_setting(
		'mnews_footer_address',
		array(
			'default'           => '',
			'sanitize_callback' => 'wp_kses_post',
		)
	);
	$wp_customize->add_control(
		'mnews_footer_address',
		array(
			'label'   => __( 'Alamat / info redaksi', 'm-news' ),
			'section' => 'mnews_footer',
			'type'    => 'textarea',
		)
	);

	$wp_customize->add_setting(
		'mnews_footer_cols',
		array(
			'default'           => 3,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		'mnews_footer_cols',
		array(
			'label'       => __( 'Jumlah kolom footer', 'm-news' ),
			'description' => __( 'Susunan widget footer (isi tiap kolom di Tampilan > Widget > Footer 1-4).', 'm-news' ),
			'section'     => 'mnews_footer',
			'type'        => 'select',
			'choices'     => array(
				2 => __( '2 kolom', 'm-news' ),
				3 => __( '3 kolom', 'm-news' ),
				4 => __( '4 kolom', 'm-news' ),
			),
		)
	);

	$wp_customize->add_setting(
		'mnews_network_title',
		array(
			'default'           => __( 'MEDIA NETWORK', 'm-news' ),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'mnews_network_title',
		array(
			'label'   => __( 'Judul menu media network', 'm-news' ),
			'section' => 'mnews_footer',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'mnews_copyright',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'mnews_copyright',
		array(
			'label'       => __( 'Teks copyright', 'm-news' ),
			'description' => __( 'Kosongkan untuk teks otomatis: © tahun + nama situs.', 'm-news' ),
			'section'     => 'mnews_footer',
			'type'        => 'text',
		)
	);

	// Performance.
	$wp_customize->add_setting(
		'mnews_clean_wp',
		array(
			'default'           => true,
			'sanitize_callback' => 'mnews_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'mnews_clean_wp',
		array(
			'label'       => __( 'Bersihkan bawaan WordPress yang tidak perlu', 'm-news' ),
			'description' => __( 'Hapus emoji, wp-embed, meta generator, dan CSS blok di halaman non-artikel agar lebih cepat. Matikan jika sebuah plugin bermasalah.', 'm-news' ),
			'section'     => 'mnews_perf',
			'type'        => 'checkbox',
		)
	);
}
add_action( 'customize_register', 'mnews_customize_layout' );
