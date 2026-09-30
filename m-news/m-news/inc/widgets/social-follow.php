<?php
/**
 * Widget: Sosial Follow. Uses the URLs from Customizer > M-News > Social Media Follow.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

/**
 * Social follow icons widget.
 */
class MNews_Widget_Social_Follow extends MNews_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'mnews_social_follow',
			__( 'M-News: Sosial Follow', 'm-news' ),
			__( 'Ikon akun media sosial (diisi di Customizer > M-News > Social Media Follow).', 'm-news' )
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
				'label'   => __( 'Judul', 'm-news' ),
				'default' => __( 'Ikuti Kami', 'm-news' ),
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
		mnews_social_links( 'mnw-social mnw-social--widget' );
		$links = ob_get_clean();
		if ( '' === $links ) {
			return;
		}
		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- registered sidebar markup.
		$this->title( $args, $i['title'] );
		echo $links; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in mnews_social_links().
		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
