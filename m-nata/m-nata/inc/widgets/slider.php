<?php
/**
 * Widget: Slider Headline. CSS scroll-snap track + a tiny deferred script (assets/js/slider.js).
 *
 * @package M_Nata
 */

defined( 'ABSPATH' ) || exit;

/**
 * Headline slider / carousel widget.
 */
class MNata_Widget_Slider extends MNata_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'mnata_slider',
			__( 'M-Nata: Slider Headline', 'm-nata' ),
			__( 'Slider berita utama. Model: slider + thumbnail, slider penuh, atau carousel 3 kolom.', 'm-nata' )
		);
	}

	/**
	 * Fields.
	 *
	 * @return array
	 */
	protected function fields() {
		return array(
			'title'       => array(
				'type'    => 'text',
				'label'   => __( 'Judul (opsional)', 'm-nata' ),
				'default' => '',
			),
			'model'       => array(
				'type'    => 'select',
				'label'   => __( 'Model', 'm-nata' ),
				'default' => 'thumbs',
				'choices' => array(
					'thumbs'   => __( 'Slider + thumbnail di bawah', 'm-nata' ),
					'full'     => __( 'Slider penuh (titik navigasi)', 'm-nata' ),
					'carousel' => __( 'Carousel 3 kolom', 'm-nata' ),
				),
			),
			'category'    => array(
				'type'    => 'category',
				'label'   => __( 'Kategori', 'm-nata' ),
				'default' => 0,
			),
			'tag'         => array(
				'type'    => 'slug',
				'label'   => __( 'Slug tag (opsional)', 'm-nata' ),
				'default' => '',
				'desc'    => __( 'Contoh: headline. Kosongkan untuk berita terbaru.', 'm-nata' ),
			),
			'count'       => array(
				'type'    => 'number',
				'label'   => __( 'Jumlah slide', 'm-nata' ),
				'default' => 5,
				'min'     => 2,
				'max'     => 10,
			),
			'title_lines' => array(
				'type'    => 'number',
				'label'   => __( 'Batas baris judul', 'm-nata' ),
				'default' => 2,
				'min'     => 1,
				'max'     => 6,
				'desc'    => __( 'Judul yang lebih panjang dipotong dengan "…" agar caption tidak menutupi gambar.', 'm-nata' ),
			),
			'show_cat'    => array(
				'type'    => 'checkbox',
				'label'   => __( 'Tampilkan label kategori', 'm-nata' ),
				'default' => 1,
			),
			'autoplay'    => array(
				'type'    => 'checkbox',
				'label'   => __( 'Geser otomatis', 'm-nata' ),
				'default' => 1,
			),
		);
	}

	/**
	 * Load the slider script (also when the HTML comes from cache).
	 *
	 * @param array $i Instance.
	 */
	protected function assets( $i ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
		mnata_enqueue_slider();
	}

	/**
	 * Print.
	 *
	 * @param array $args Sidebar args.
	 * @param array $i    Instance.
	 */
	protected function render( $args, $i ) {
		$query = mnata_run_query( mnata_query_args( $i ) );
		if ( ! $query->have_posts() ) {
			return;
		}

		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- registered sidebar markup.
		$this->title( $args, $i['title'] );
		mnata_render_slider(
			$query,
			array(
				'model'       => $i['model'],
				'show_cat'    => $i['show_cat'],
				'autoplay'    => $i['autoplay'],
				'title_lines' => $i['title_lines'],
			)
		);
		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
