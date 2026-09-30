<?php
/**
 * Widget: Banner Iklan. Image + link, or ad code (AdSense/HTML), with a reserved size so the page never shifts.
 *
 * @package M_Nata
 */

defined( 'ABSPATH' ) || exit;

/**
 * Banner ad widget (image or ad code, fixed reserved size).
 */
class MNata_Widget_Banner_Ad extends MNata_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'mnata_banner_ad',
			__( 'M-Nata: Banner Iklan', 'm-nata' ),
			__( 'Banner gambar + tautan, atau kode iklan (AdSense/HTML). Pilih ukuran standar; rasio ditampilkan di pilihan.', 'm-nata' )
		);
	}

	/**
	 * Fields.
	 *
	 * @return array
	 */
	protected function fields() {
		return array(
			'size'    => array(
				'type'    => 'select',
				'label'   => __( 'Ukuran banner (ukuran · rasio)', 'm-nata' ),
				'default' => '300x250',
				'choices' => mnata_ad_size_choices(),
			),
			'w'       => array(
				'type'    => 'number',
				'label'   => __( 'Lebar kustom (px)', 'm-nata' ),
				'default' => 300,
				'min'     => 1,
				'max'     => 2000,
			),
			'h'       => array(
				'type'    => 'number',
				'label'   => __( 'Tinggi kustom (px)', 'm-nata' ),
				'default' => 250,
				'min'     => 1,
				'max'     => 2000,
			),
			'type'    => array(
				'type'    => 'select',
				'label'   => __( 'Jenis', 'm-nata' ),
				'default' => 'image',
				'choices' => array(
					'image' => __( 'Gambar banner', 'm-nata' ),
					'code'  => __( 'Kode iklan (AdSense / HTML)', 'm-nata' ),
				),
			),
			'image'   => array(
				'type'    => 'image',
				'label'   => __( 'Gambar (jenis Gambar)', 'm-nata' ),
				'default' => 0,
			),
			'url'     => array(
				'type'    => 'url',
				'label'   => __( 'Tautan tujuan (jenis Gambar)', 'm-nata' ),
				'default' => '',
			),
			'alt'     => array(
				'type'    => 'text',
				'label'   => __( 'Teks alternatif gambar', 'm-nata' ),
				'default' => '',
			),
			'new_tab' => array(
				'type'    => 'checkbox',
				'label'   => __( 'Buka tautan di tab baru', 'm-nata' ),
				'default' => 1,
			),
			'code'    => array(
				'type'    => 'html',
				'label'   => __( 'Kode iklan (jenis Kode)', 'm-nata' ),
				'default' => '',
				'desc'    => __( 'Dimuat setelah halaman tampil dan saat mendekati layar, supaya tidak memperlambat. Skrip hanya tersimpan utuh untuk pengguna dengan izin unfiltered_html.', 'm-nata' ),
			),
			'sticky'  => array(
				'type'    => 'checkbox',
				'label'   => __( 'Tempel saat halaman digulir (sticky, khusus sidebar)', 'm-nata' ),
				'default' => 0,
				'desc'    => __( 'Banner tetap terlihat saat pembaca menggulir ke bawah. Hanya berlaku bila widget ini paling bawah di sidebar, supaya tidak menutupi widget lain. Cocok untuk 300×600 atau 300×250.', 'm-nata' ),
			),
			'device'  => array(
				'type'    => 'select',
				'label'   => __( 'Tampil di', 'm-nata' ),
				'default' => 'all',
				'choices' => array(
					'all'     => __( 'Semua perangkat', 'm-nata' ),
					'desktop' => __( 'Desktop saja (≥ 768 px)', 'm-nata' ),
					'mobile'  => __( 'Mobile saja (< 768 px)', 'm-nata' ),
				),
			),
		);
	}

	/**
	 * Ad code needs the slot loader.
	 *
	 * @param array $i Instance.
	 */
	protected function assets( $i ) {
		if ( 'code' === $i['type'] ) {
			mnata_enqueue_slot();
		}
	}

	/**
	 * Print.
	 *
	 * @param array $args Sidebar args.
	 * @param array $i    Instance.
	 */
	protected function render( $args, $i ) {
		if ( 'image' === $i['type'] && ! $i['image'] ) {
			return;
		}
		if ( 'code' === $i['type'] && ( '' === trim( $i['code'] ) || mnata_is_amp() ) ) {
			return;
		}

		list( $w, $h ) = mnata_ad_dimensions( $i['size'], $i['w'], $i['h'] );

		$hide    = array(
			'desktop' => 'mn-hide-mobile',
			'mobile'  => 'mn-hide-desktop',
		);
		$before  = $args['before_widget'];
		$classes = array();
		if ( isset( $hide[ $i['device'] ] ) ) {
			$classes[] = $hide[ $i['device'] ];
		}
		if ( ! empty( $i['sticky'] ) ) {
			$classes[] = 'mn-sticky-ad';
		}
		if ( $classes ) {
			$before = preg_replace( '/class="/', 'class="' . implode( ' ', $classes ) . ' ', $before, 1 );
		}

		echo $before; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- registered sidebar markup.
		echo '<div class="mn-ad">';

		if ( get_theme_mod( 'mnata_ad_label_on', true ) ) {
			printf( '<span class="mn-ad__label">%s</span>', esc_html( get_theme_mod( 'mnata_ad_label', __( 'Iklan', 'm-nata' ) ) ) );
		}

		if ( 'code' === $i['type'] ) {
			$media = array(
				'desktop' => '(min-width:768px)',
				'mobile'  => '(max-width:767px)',
			);
			mnata_render_slot( $i['code'], sprintf( 'max-width:%dpx;min-height:%dpx', $w, $h ), isset( $media[ $i['device'] ] ) ? $media[ $i['device'] ] : '' );
		} else {
			$above_fold = isset( $args['id'] ) && 'header-banner' === $args['id'];
			$img        = wp_get_attachment_image(
				(int) $i['image'],
				'full',
				false,
				array(
					'class'    => 'mn-ad__img',
					'alt'      => $i['alt'],
					'loading'  => $above_fold ? 'eager' : 'lazy',
					'decoding' => 'async',
					'sizes'    => sprintf( '(min-width:%1$dpx) %1$dpx, 100vw', $w ),
				)
			);
			if ( $img ) {
				$inner = $img;
				if ( $i['url'] ) {
					$inner = sprintf(
						'<a href="%1$s" rel="sponsored noopener"%2$s>%3$s</a>',
						esc_url( $i['url'] ),
						$i['new_tab'] ? ' target="_blank"' : '',
						$img
					);
				}
				printf(
					'<div class="mn-ad__box" style="max-width:%1$dpx;aspect-ratio:%1$d/%2$d">%3$s</div>',
					absint( $w ),
					absint( $h ),
					$inner // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core <img> and an escaped link.
				);
			}
		}

		echo '</div>';
		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
