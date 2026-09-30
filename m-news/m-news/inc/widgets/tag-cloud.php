<?php
/**
 * Widget: Tag / Topik. Most used tags as chips.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

/**
 * Popular tags widget.
 */
class MNews_Widget_Tag_Cloud extends MNews_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'mnews_tag_cloud',
			__( 'M-News: Tag / Topik', 'm-news' ),
			__( 'Tag terpopuler sebagai tombol kecil ("Topik Terkini").', 'm-news' )
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
				'default' => __( 'Topik Terkini', 'm-news' ),
			),
			'count' => array(
				'type'    => 'number',
				'label'   => __( 'Jumlah tag', 'm-news' ),
				'default' => 12,
				'min'     => 1,
				'max'     => 50,
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
		$tags = get_tags(
			array(
				'orderby'    => 'count',
				'order'      => 'DESC',
				'number'     => (int) $i['count'],
				'hide_empty' => true,
			)
		);
		if ( empty( $tags ) || is_wp_error( $tags ) ) {
			return;
		}
		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- registered sidebar markup.
		$this->title( $args, $i['title'] );
		echo '<ul class="mnw-tags">';
		foreach ( $tags as $tag ) {
			printf( '<li><a href="%1$s">#%2$s</a></li>', esc_url( get_tag_link( $tag ) ), esc_html( $tag->name ) );
		}
		echo '</ul>';
		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
