<?php
/**
 * Widget: HTML / Embed. Free HTML for third-party widgets (scores, weather, prices), loaded lazily.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

/**
 * Free HTML / third-party embed widget, loaded lazily.
 */
class MNews_Widget_Html_Embed extends MNews_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'mnews_html_embed',
			__( 'M-News: HTML / Embed', 'm-news' ),
			__( 'HTML bebas untuk widget pihak ketiga (skor bola, cuaca, harga emas, dsb.).', 'm-news' )
		);
	}

	/**
	 * Fields.
	 *
	 * @return array
	 */
	protected function fields() {
		return array(
			'title'  => array(
				'type'    => 'text',
				'label'   => __( 'Judul', 'm-news' ),
				'default' => '',
			),
			'code'   => array(
				'type'    => 'html',
				'label'   => __( 'Kode HTML', 'm-news' ),
				'default' => '',
				'desc'    => __( 'Skrip hanya tersimpan utuh untuk pengguna dengan izin unfiltered_html.', 'm-news' ),
			),
			'height' => array(
				'type'    => 'number',
				'label'   => __( 'Tinggi cadangan (px)', 'm-news' ),
				'default' => 0,
				'min'     => 0,
				'max'     => 2000,
				'desc'    => __( 'Ruang yang dicadangkan agar halaman tidak bergeser saat embed dimuat.', 'm-news' ),
			),
			'lazy'   => array(
				'type'    => 'checkbox',
				'label'   => __( 'Muat saat mendekati layar (disarankan)', 'm-news' ),
				'default' => 1,
			),
		);
	}

	/**
	 * Slot loader.
	 *
	 * @param array $i Instance.
	 */
	protected function assets( $i ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
		mnews_enqueue_slot();
	}

	/**
	 * Print.
	 *
	 * @param array $args Sidebar args.
	 * @param array $i    Instance.
	 */
	protected function render( $args, $i ) {
		if ( '' === trim( $i['code'] ) || mnews_is_amp() ) {
			return;
		}
		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- registered sidebar markup.
		$this->title( $args, $i['title'] );
		mnews_render_slot( $i['code'], $i['height'] ? 'min-height:' . (int) $i['height'] . 'px' : '', '', (bool) $i['lazy'] );
		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
