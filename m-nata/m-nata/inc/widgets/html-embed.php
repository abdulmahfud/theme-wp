<?php
/**
 * Widget: HTML / Embed. Free HTML for third-party widgets (scores, weather, prices), loaded lazily.
 *
 * @package M_Nata
 */

defined( 'ABSPATH' ) || exit;

/**
 * Free HTML / third-party embed widget, loaded lazily.
 */
class MNata_Widget_Html_Embed extends MNata_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'mnata_html_embed',
			__( 'M-Nata: HTML / Embed', 'm-nata' ),
			__( 'HTML bebas untuk widget pihak ketiga (skor bola, cuaca, harga emas, dsb.).', 'm-nata' )
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
				'label'   => __( 'Judul', 'm-nata' ),
				'default' => '',
			),
			'code'   => array(
				'type'    => 'html',
				'label'   => __( 'Kode HTML', 'm-nata' ),
				'default' => '',
				'desc'    => __( 'Skrip hanya tersimpan utuh untuk pengguna dengan izin unfiltered_html.', 'm-nata' ),
			),
			'height' => array(
				'type'    => 'number',
				'label'   => __( 'Tinggi cadangan (px)', 'm-nata' ),
				'default' => 0,
				'min'     => 0,
				'max'     => 2000,
				'desc'    => __( 'Ruang yang dicadangkan agar halaman tidak bergeser saat embed dimuat.', 'm-nata' ),
			),
			'lazy'   => array(
				'type'    => 'checkbox',
				'label'   => __( 'Muat saat mendekati layar (disarankan)', 'm-nata' ),
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
		mnata_enqueue_slot();
	}

	/**
	 * Print.
	 *
	 * @param array $args Sidebar args.
	 * @param array $i    Instance.
	 */
	protected function render( $args, $i ) {
		if ( '' === trim( $i['code'] ) || mnata_is_amp() ) {
			return;
		}
		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- registered sidebar markup.
		$this->title( $args, $i['title'] );
		mnata_render_slot( $i['code'], $i['height'] ? 'min-height:' . (int) $i['height'] . 'px' : '', '', (bool) $i['lazy'] );
		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
