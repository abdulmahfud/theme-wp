<?php
/**
 * Widget: Sosial Follow. Uses the URLs from Customizer > M-Nata > Social Media Follow.
 *
 * @package M_Nata
 */

defined( 'ABSPATH' ) || exit;

/**
 * Social follow icons widget.
 */
class MNata_Widget_Social_Follow extends MNata_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'mnata_social_follow',
			__( 'M-Nata: Sosial Follow', 'm-nata' ),
			__( 'Ikon akun media sosial (diisi di Customizer > M-Nata > Social Media Follow).', 'm-nata' )
		);
	}

	/**
	 * Fields.
	 *
	 * @return array
	 */
	protected function fields() {
		return array(
			'title' => array(
				'type'    => 'text',
				'label'   => __( 'Judul', 'm-nata' ),
				'default' => __( 'Ikuti Kami', 'm-nata' ),
			),
		);
	}

	/**
	 * Print.
	 *
	 * @param array $args Sidebar args.
	 * @param array $i    Instance.
	 */
	protected function render( $args, $i ) {
		ob_start();
		mnata_social_links( 'mn-social mn-social--widget' );
		$links = ob_get_clean();
		if ( '' === $links ) {
			return;
		}
		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- registered sidebar markup.
		$this->title( $args, $i['title'] );
		echo $links; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in mnata_social_links().
		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
