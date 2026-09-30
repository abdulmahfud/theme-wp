<?php
/**
 * Widget: Blok Kategori. A titled block for one category, optionally on a coloured background.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

/**
 * Category block widget (optionally on a coloured background).
 */
class MNews_Widget_Category_Block extends MNews_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'mnews_category_block',
			__( 'M-News: Blok Kategori', 'm-news' ),
			__( 'Blok berita satu kategori dengan judul + tautan "Lihat semua", bisa berlatar gradient tema atau warna sendiri.', 'm-news' )
		);
	}

	/**
	 * Fields.
	 *
	 * @return array
	 */
	protected function fields() {
		return array(
			'category'    => array(
				'type'    => 'category',
				'label'   => __( 'Kategori', 'm-news' ),
				'default' => 0,
			),
			'title'       => array(
				'type'    => 'text',
				'label'   => __( 'Judul (kosong = nama kategori)', 'm-news' ),
				'default' => '',
			),
			'layout'      => array(
				'type'    => 'select',
				'label'   => __( 'Layout', 'm-news' ),
				'default' => 'grid-3',
				'choices' => array_merge( mnews_post_layout_choices(), array( 'carousel' => __( 'Carousel (geser)', 'm-news' ) ) ),
			),
			'count'       => array(
				'type'    => 'number',
				'label'   => __( 'Jumlah berita', 'm-news' ),
				'default' => 6,
				'min'     => 2,
				'max'     => 12,
			),
			'style'       => array(
				'type'    => 'select',
				'label'   => __( 'Latar blok', 'm-news' ),
				'default' => 'plain',
				'choices' => array(
					'plain' => __( 'Tanpa latar', 'm-news' ),
					'theme' => __( 'Gradient tema (atur di Customizer)', 'm-news' ),
					'solid' => __( 'Warna sendiri', 'm-news' ),
				),
			),
			'color'       => array(
				'type'    => 'color',
				'label'   => __( 'Warna (bila "Warna sendiri")', 'm-news' ),
				'default' => '#4a1d8f',
			),
			'title_lines' => array(
				'type'    => 'number',
				'label'   => __( 'Batas baris judul (0 = otomatis)', 'm-news' ),
				'default' => 0,
				'min'     => 0,
				'max'     => 6,
			),
			'show_cat'    => array(
				'type'    => 'checkbox',
				'label'   => __( 'Tampilkan label kategori pada tiap berita', 'm-news' ),
				'default' => 0,
			),
			'show_date'   => array(
				'type'    => 'checkbox',
				'label'   => __( 'Tampilkan waktu terbit', 'm-news' ),
				'default' => 1,
			),
			'more'        => array(
				'type'    => 'checkbox',
				'label'   => __( 'Tampilkan tautan "Lihat semua"', 'm-news' ),
				'default' => 1,
			),
		);
	}

	/**
	 * Carousel layout needs the slider script.
	 *
	 * @param array $i Instance.
	 */
	protected function assets( $i ) {
		if ( 'carousel' === $i['layout'] ) {
			mnews_enqueue_slider();
		}
	}

	/**
	 * Print.
	 *
	 * @param array $args Sidebar args.
	 * @param array $i    Instance.
	 */
	protected function render( $args, $i ) {
		$term = $i['category'] ? get_category( (int) $i['category'] ) : null;
		if ( ! $term || is_wp_error( $term ) ) {
			return;
		}

		$query = mnews_run_query( mnews_query_args( $i ) );
		if ( ! $query->have_posts() ) {
			return;
		}

		$style = '';
		$class = 'mnw-block';
		if ( 'theme' === $i['style'] ) {
			$class .= ' mnw-block--filled';
			$style  = 'background:var(--mnw-block-bg);color:var(--mnw-on-grad)';
		} elseif ( 'solid' === $i['style'] ) {
			$hex    = sanitize_hex_color( $i['color'] ) ? $i['color'] : '#4a1d8f';
			$fg     = ( mnews_contrast( $hex, '#ffffff' ) >= mnews_contrast( $hex, '#111111' ) ) ? '#ffffff' : '#111111';
			$class .= ' mnw-block--filled';
			$style  = 'background:' . $hex . ';color:' . $fg;
		}

		$title = $i['title'] ? $i['title'] : $term->name;
		$link  = get_category_link( $term );

		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- registered sidebar markup.
		printf( '<div class="%1$s"%2$s>', esc_attr( $class ), $style ? ' style="' . esc_attr( $style ) . '"' : '' );
		echo '<div class="mnw-block__head">';
		printf( '<h2 class="mnw-block-title"><a href="%1$s">%2$s</a></h2>', esc_url( $link ), esc_html( $title ) );
		if ( $i['more'] ) {
			printf( '<a class="mnw-block__more" href="%1$s">%2$s</a>', esc_url( $link ), esc_html__( 'Lihat semua', 'm-news' ) );
		}
		echo '</div>';

		if ( 'carousel' === $i['layout'] ) {
			mnews_render_slider(
				$query,
				array(
					'model'       => 'carousel',
					'show_cat'    => $i['show_cat'],
					'autoplay'    => 0,
					'title_lines' => $i['title_lines'] ? $i['title_lines'] : 2,
				)
			);
		} else {
			mnews_render_posts(
				$query,
				array(
					'layout'      => $i['layout'],
					'show_cat'    => $i['show_cat'],
					'show_date'   => $i['show_date'],
					'title_lines' => $i['title_lines'],
				)
			);
		}

		echo '</div>';
		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
