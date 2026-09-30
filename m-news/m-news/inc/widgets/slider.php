<?php
/**
 * Widget: Slider Headline. CSS scroll-snap track + a tiny deferred script (assets/js/slider.js).
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

/**
 * Headline slider / carousel widget.
 */
class MNews_Widget_Slider extends MNews_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'mnews_slider',
			__( 'M-News: Slider Headline', 'm-news' ),
			__( 'Slider berita utama. Model: slider + thumbnail, slider penuh, atau carousel 3 kolom.', 'm-news' )
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
				'label'   => __( 'Judul (opsional)', 'm-news' ),
				'default' => '',
			),
			'model'       => array(
				'type'    => 'select',
				'label'   => __( 'Model', 'm-news' ),
				'default' => 'thumbs',
				'choices' => array(
					'thumbs'   => __( 'Slider + thumbnail di bawah', 'm-news' ),
					'full'     => __( 'Slider penuh (titik navigasi)', 'm-news' ),
					'carousel' => __( 'Carousel 3 kolom', 'm-news' ),
					'hero'     => __( 'Hero: 1 besar + bubble bulat penanda slide', 'm-news' ),
				),
			),
			'category'    => array(
				'type'    => 'category',
				'label'   => __( 'Kategori', 'm-news' ),
				'default' => 0,
			),
			'tag'         => array(
				'type'    => 'slug',
				'label'   => __( 'Slug tag (opsional)', 'm-news' ),
				'default' => '',
				'desc'    => __( 'Contoh: headline. Kosongkan untuk berita terbaru.', 'm-news' ),
			),
			'count'       => array(
				'type'    => 'number',
				'label'   => __( 'Jumlah slide', 'm-news' ),
				'default' => 5,
				'min'     => 2,
				'max'     => 10,
			),
			'title_lines' => array(
				'type'    => 'number',
				'label'   => __( 'Batas baris judul', 'm-news' ),
				'default' => 2,
				'min'     => 1,
				'max'     => 6,
				'desc'    => __( 'Judul yang lebih panjang dipotong dengan "…" agar caption tidak menutupi gambar.', 'm-news' ),
			),
			'show_cat'    => array(
				'type'    => 'checkbox',
				'label'   => __( 'Tampilkan label kategori', 'm-news' ),
				'default' => 1,
			),
			'autoplay'    => array(
				'type'    => 'checkbox',
				'label'   => __( 'Geser otomatis', 'm-news' ),
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
		mnews_enqueue_slider();
	}

	/**
	 * Print.
	 *
	 * @param array $args Sidebar args.
	 * @param array $i    Instance.
	 */
	protected function render( $args, $i ) {
		$query = mnews_run_query( mnews_query_args( $i ) );
		if ( ! $query->have_posts() ) {
			return;
		}

		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- registered sidebar markup.
		$this->title( $args, $i['title'] );
		mnews_render_slider(
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
