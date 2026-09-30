<?php
/**
 * Customizer: social share buttons and article options.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

/**
 * Sanitize the share order string: keep known ids only, unique, in the given order.
 *
 * @param string $value Comma separated ids.
 * @return string
 */
function mnews_sanitize_share_order( $value ) {
	$nets = mnews_share_networks();
	$ids  = array();
	foreach ( explode( ',', (string) $value ) as $id ) {
		$id = strtolower( trim( $id ) );
		if ( isset( $nets[ $id ] ) && ! in_array( $id, $ids, true ) ) {
			$ids[] = $id;
		}
	}
	return $ids ? implode( ',', $ids ) : mnews_share_default_order();
}

/**
 * Sanitize a value against a list of allowed keys.
 *
 * @param string $value   Raw.
 * @param array  $allowed Allowed keys.
 * @param string $fallback Fallback.
 * @return string
 */
function mnews_sanitize_choice( $value, $allowed, $fallback ) {
	return in_array( $value, $allowed, true ) ? $value : $fallback;
}

/**
 * Register share + article settings.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function mnews_customize_share( $wp_customize ) {
	$wp_customize->get_section( 'mnews_share' )->description = __( 'Tombol bagikan di halaman artikel. Berupa tautan biasa ke tiap jaringan (tanpa skrip pihak ketiga); hanya "Salin Link" memakai JavaScript kecil.', 'm-news' );

	// Which networks.
	foreach ( mnews_share_networks() as $id => $net ) {
		$wp_customize->add_setting(
			'mnews_share_' . $id,
			array(
				'default'           => $net[3],
				'sanitize_callback' => 'mnews_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			'mnews_share_' . $id,
			array(
				/* translators: %s: network name. */
				'label'   => sprintf( __( 'Tampilkan: %s', 'm-news' ), $net[0] ),
				'section' => 'mnews_share',
				'type'    => 'checkbox',
			)
		);
	}

	$wp_customize->add_setting(
		'mnews_share_order',
		array(
			'default'           => mnews_share_default_order(),
			'sanitize_callback' => 'mnews_sanitize_share_order',
		)
	);
	$wp_customize->add_control(
		'mnews_share_order',
		array(
			'label'       => __( 'Urutan tombol', 'm-news' ),
			'description' => __( 'Tulis id dipisah koma: ', 'm-news' ) . mnews_share_default_order(),
			'section'     => 'mnews_share',
			'type'        => 'text',
		)
	);

	$wp_customize->add_setting(
		'mnews_share_position',
		array(
			'default'           => 'top',
			'sanitize_callback' => static function ( $v ) {
				return mnews_sanitize_choice( $v, array( 'top', 'bottom', 'both', 'none' ), 'top' );
			},
		)
	);
	$wp_customize->add_control(
		'mnews_share_position',
		array(
			'label'   => __( 'Posisi', 'm-news' ),
			'section' => 'mnews_share',
			'type'    => 'select',
			'choices' => array(
				'top'    => __( 'Di atas artikel (bawah judul)', 'm-news' ),
				'bottom' => __( 'Di bawah artikel', 'm-news' ),
				'both'   => __( 'Atas dan bawah', 'm-news' ),
				'none'   => __( 'Sembunyikan', 'm-news' ),
			),
		)
	);

	$wp_customize->add_setting(
		'mnews_share_float',
		array(
			'default'           => false,
			'sanitize_callback' => 'mnews_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'mnews_share_float',
		array(
			'label'   => __( 'Bar bagikan melayang di bawah layar (khusus mobile)', 'm-news' ),
			'section' => 'mnews_share',
			'type'    => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'mnews_share_style',
		array(
			'default'           => 'brand',
			'sanitize_callback' => static function ( $v ) {
				return mnews_sanitize_choice( $v, array( 'brand', 'theme', 'outline' ), 'brand' );
			},
		)
	);
	$wp_customize->add_control(
		'mnews_share_style',
		array(
			'label'   => __( 'Gaya ikon', 'm-news' ),
			'section' => 'mnews_share',
			'type'    => 'select',
			'choices' => array(
				'brand'   => __( 'Warna tiap jaringan', 'm-news' ),
				'theme'   => __( 'Gradient tema', 'm-news' ),
				'outline' => __( 'Garis tepi', 'm-news' ),
			),
		)
	);

	$wp_customize->add_setting(
		'mnews_share_labels',
		array(
			'default'           => false,
			'sanitize_callback' => 'mnews_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'mnews_share_labels',
		array(
			'label'   => __( 'Tampilkan nama jaringan di samping ikon', 'm-news' ),
			'section' => 'mnews_share',
			'type'    => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'mnews_share_text',
		array(
			'default'           => '{title} - {url}',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'mnews_share_text',
		array(
			'label'       => __( 'Template teks bagikan', 'm-news' ),
			'description' => __( 'Pakai {title} dan {url}. Dipakai oleh WhatsApp, X, Telegram, Pinterest, dan Email.', 'm-news' ),
			'section'     => 'mnews_share',
			'type'        => 'text',
		)
	);

	// Article options.
	$article = array(
		'mnews_show_author'  => array( __( 'Tampilkan nama penulis (by-line)', 'm-news' ), true ),
		'mnews_show_credits' => array( __( 'Tampilkan Penulis / Editor / Sumber di akhir artikel', 'm-news' ), true ),
		'mnews_show_tags'    => array( __( 'Tampilkan tag di akhir artikel', 'm-news' ), true ),
		'mnews_related_on'   => array( __( 'Tampilkan "Berita Terkait" di bawah artikel', 'm-news' ), true ),
	);
	foreach ( $article as $key => $cfg ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => $cfg[1],
				'sanitize_callback' => 'mnews_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			$key,
			array(
				'label'   => $cfg[0],
				'section' => 'mnews_article',
				'type'    => 'checkbox',
			)
		);
	}

	$wp_customize->add_setting(
		'mnews_related_title',
		array(
			'default'           => __( 'Berita Terkait', 'm-news' ),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'mnews_related_title',
		array(
			'label'   => __( 'Judul blok berita terkait', 'm-news' ),
			'section' => 'mnews_article',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'mnews_related_count',
		array(
			'default'           => 6,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		'mnews_related_count',
		array(
			'label'       => __( 'Jumlah berita terkait', 'm-news' ),
			'section'     => 'mnews_article',
			'type'        => 'number',
			'input_attrs' => array(
				'min' => 3,
				'max' => 12,
			),
		)
	);

	$wp_customize->add_setting(
		'mnews_readmore_after',
		array(
			'default'           => 2,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		'mnews_readmore_after',
		array(
			'label'       => __( 'Sisipkan "Baca Juga" setelah paragraf ke- (0 = matikan)', 'm-news' ),
			'section'     => 'mnews_article',
			'type'        => 'number',
			'input_attrs' => array(
				'min' => 0,
				'max' => 20,
			),
		)
	);
}
add_action( 'customize_register', 'mnews_customize_share' );
