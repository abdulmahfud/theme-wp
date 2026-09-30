<?php
/**
 * Customizer: social share buttons and article options.
 *
 * @package M_Nata
 */

defined( 'ABSPATH' ) || exit;

/**
 * Sanitize the share order string: keep known ids only, unique, in the given order.
 *
 * @param string $value Comma separated ids.
 * @return string
 */
function mnata_sanitize_share_order( $value ) {
	$nets = mnata_share_networks();
	$ids  = array();
	foreach ( explode( ',', (string) $value ) as $id ) {
		$id = strtolower( trim( $id ) );
		if ( isset( $nets[ $id ] ) && ! in_array( $id, $ids, true ) ) {
			$ids[] = $id;
		}
	}
	return $ids ? implode( ',', $ids ) : mnata_share_default_order();
}

/**
 * Sanitize a value against a list of allowed keys.
 *
 * @param string $value   Raw.
 * @param array  $allowed Allowed keys.
 * @param string $fallback Fallback.
 * @return string
 */
function mnata_sanitize_choice( $value, $allowed, $fallback ) {
	return in_array( $value, $allowed, true ) ? $value : $fallback;
}

/**
 * Register share + article settings.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function mnata_customize_share( $wp_customize ) {
	$wp_customize->get_section( 'mnata_share' )->description = __( 'Tombol bagikan di halaman artikel. Berupa tautan biasa ke tiap jaringan (tanpa skrip pihak ketiga); hanya "Salin Link" memakai JavaScript kecil.', 'm-nata' );

	// Which networks.
	foreach ( mnata_share_networks() as $id => $net ) {
		$wp_customize->add_setting(
			'mnata_share_' . $id,
			array(
				'default'           => $net[3],
				'sanitize_callback' => 'mnata_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			'mnata_share_' . $id,
			array(
				/* translators: %s: network name. */
				'label'   => sprintf( __( 'Tampilkan: %s', 'm-nata' ), $net[0] ),
				'section' => 'mnata_share',
				'type'    => 'checkbox',
			)
		);
	}

	$wp_customize->add_setting(
		'mnata_share_order',
		array(
			'default'           => mnata_share_default_order(),
			'sanitize_callback' => 'mnata_sanitize_share_order',
		)
	);
	$wp_customize->add_control(
		'mnata_share_order',
		array(
			'label'       => __( 'Urutan tombol', 'm-nata' ),
			'description' => __( 'Tulis id dipisah koma: ', 'm-nata' ) . mnata_share_default_order(),
			'section'     => 'mnata_share',
			'type'        => 'text',
		)
	);

	$wp_customize->add_setting(
		'mnata_share_position',
		array(
			'default'           => 'top',
			'sanitize_callback' => static function ( $v ) {
				return mnata_sanitize_choice( $v, array( 'top', 'bottom', 'both', 'none' ), 'top' );
			},
		)
	);
	$wp_customize->add_control(
		'mnata_share_position',
		array(
			'label'   => __( 'Posisi', 'm-nata' ),
			'section' => 'mnata_share',
			'type'    => 'select',
			'choices' => array(
				'top'    => __( 'Di atas artikel (bawah judul)', 'm-nata' ),
				'bottom' => __( 'Di bawah artikel', 'm-nata' ),
				'both'   => __( 'Atas dan bawah', 'm-nata' ),
				'none'   => __( 'Sembunyikan', 'm-nata' ),
			),
		)
	);

	$wp_customize->add_setting(
		'mnata_share_float',
		array(
			'default'           => false,
			'sanitize_callback' => 'mnata_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'mnata_share_float',
		array(
			'label'   => __( 'Bar bagikan melayang di bawah layar (khusus mobile)', 'm-nata' ),
			'section' => 'mnata_share',
			'type'    => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'mnata_share_style',
		array(
			'default'           => 'brand',
			'sanitize_callback' => static function ( $v ) {
				return mnata_sanitize_choice( $v, array( 'brand', 'theme', 'outline' ), 'brand' );
			},
		)
	);
	$wp_customize->add_control(
		'mnata_share_style',
		array(
			'label'   => __( 'Gaya ikon', 'm-nata' ),
			'section' => 'mnata_share',
			'type'    => 'select',
			'choices' => array(
				'brand'   => __( 'Warna tiap jaringan', 'm-nata' ),
				'theme'   => __( 'Gradient tema', 'm-nata' ),
				'outline' => __( 'Garis tepi', 'm-nata' ),
			),
		)
	);

	$wp_customize->add_setting(
		'mnata_share_labels',
		array(
			'default'           => false,
			'sanitize_callback' => 'mnata_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'mnata_share_labels',
		array(
			'label'   => __( 'Tampilkan nama jaringan di samping ikon', 'm-nata' ),
			'section' => 'mnata_share',
			'type'    => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'mnata_share_text',
		array(
			'default'           => '{title} - {url}',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'mnata_share_text',
		array(
			'label'       => __( 'Template teks bagikan', 'm-nata' ),
			'description' => __( 'Pakai {title} dan {url}. Dipakai oleh WhatsApp, X, Telegram, Pinterest, dan Email.', 'm-nata' ),
			'section'     => 'mnata_share',
			'type'        => 'text',
		)
	);

	// Article options.
	$article = array(
		'mnata_show_author'  => array( __( 'Tampilkan nama penulis (by-line)', 'm-nata' ), true ),
		'mnata_show_credits' => array( __( 'Tampilkan Penulis / Editor / Sumber di akhir artikel', 'm-nata' ), true ),
		'mnata_show_tags'    => array( __( 'Tampilkan tag di akhir artikel', 'm-nata' ), true ),
		'mnata_related_on'   => array( __( 'Tampilkan "Berita Terkait" di bawah artikel', 'm-nata' ), true ),
	);
	foreach ( $article as $key => $cfg ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => $cfg[1],
				'sanitize_callback' => 'mnata_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			$key,
			array(
				'label'   => $cfg[0],
				'section' => 'mnata_article',
				'type'    => 'checkbox',
			)
		);
	}

	$wp_customize->add_setting(
		'mnata_related_title',
		array(
			'default'           => __( 'Berita Terkait', 'm-nata' ),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'mnata_related_title',
		array(
			'label'   => __( 'Judul blok berita terkait', 'm-nata' ),
			'section' => 'mnata_article',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'mnata_related_count',
		array(
			'default'           => 6,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		'mnata_related_count',
		array(
			'label'       => __( 'Jumlah berita terkait', 'm-nata' ),
			'section'     => 'mnata_article',
			'type'        => 'number',
			'input_attrs' => array(
				'min' => 3,
				'max' => 12,
			),
		)
	);

	$wp_customize->add_setting(
		'mnata_readmore_after',
		array(
			'default'           => 2,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		'mnata_readmore_after',
		array(
			'label'       => __( 'Sisipkan "Baca Juga" setelah paragraf ke- (0 = matikan)', 'm-nata' ),
			'section'     => 'mnata_article',
			'type'        => 'number',
			'input_attrs' => array(
				'min' => 0,
				'max' => 20,
			),
		)
	);
}
add_action( 'customize_register', 'mnata_customize_share' );
