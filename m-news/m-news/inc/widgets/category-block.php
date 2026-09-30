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
				'choices' => array_merge(
					mnews_post_layout_choices(),
					array(
						'carousel' => __( 'Carousel (geser)', 'm-news' ),
						'news'     => __( '1 besar + Terbaru & Trending (2 kolom)', 'm-news' ),
					)
				),
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

		if ( 'news' === $i['layout'] ) {
			$this->render_news_block( $args, $i, $class, $style, $title, $link );
			return;
		}

		$query = mnews_run_query( mnews_query_args( $i ) );
		if ( ! $query->have_posts() ) {
			return;
		}

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

	/**
	 * "news" layout: one big card (the top trending item) plus two lists side by side — "Terbaru" (latest) and
	 * "Trending" (most commented). Two small queries (one per list; the hero re-uses the trending list's first
	 * item, no query of its own) instead of the single query every other layout uses.
	 *
	 * @param array  $args  Sidebar args.
	 * @param array  $i     Instance.
	 * @param string $wrap_class Wrapper class (background style already resolved by render()).
	 * @param string $style      Inline style for $wrap_class.
	 * @param string $title      Block title.
	 * @param string $link       Category link.
	 */
	private function render_news_block( $args, $i, $wrap_class, $style, $title, $link ) {
		$n        = max( 3, min( 6, (int) $i['count'] ) );
		$base     = array(
			'category' => (int) $i['category'],
			'tag'      => '',
			'count'    => $n,
			'show_cat' => $i['show_cat'],
		);
		$trending = mnews_run_query( mnews_query_args( array_merge( $base, array( 'order' => 'popular' ) ) ) );
		$latest   = mnews_run_query( mnews_query_args( array_merge( $base, array( 'order' => 'latest' ) ) ) );
		if ( ! $trending->have_posts() && ! $latest->have_posts() ) {
			return;
		}

		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- registered sidebar markup.
		printf( '<div class="%1$s"%2$s>', esc_attr( $wrap_class ), $style ? ' style="' . esc_attr( $style ) . '"' : '' );
		echo '<div class="mnw-block__head">';
		printf( '<h2 class="mnw-block-title"><a href="%1$s">%2$s</a></h2>', esc_url( $link ), esc_html( $title ) );
		if ( $i['more'] ) {
			printf( '<a class="mnw-block__more" href="%1$s">%2$s</a>', esc_url( $link ), esc_html__( 'Lihat semua', 'm-news' ) );
		}
		echo '</div>';

		echo '<div class="mnw-nb">';
		if ( $trending->have_posts() ) {
			// Hero = the trending list's own first item (rewound below so the column still shows it as row 1,
			// exactly like the reference). $query->the_post()/have_posts() — not manual setup_postdata() — is
			// what correctly binds template tags to *this* query instead of the page's main $wp_query.
			$trending->the_post();
			$cat = $i['show_cat'] ? mnews_category_label() : '';
			?>
			<article class="mnw-overlay mnw-nb-hero">
				<a class="mnw-overlay__link" href="<?php the_permalink(); ?>">
					<?php mnews_thumb( 'mnews-card', false, '(min-width:1300px) 320px, (min-width:768px) 700px, 100vw' ); ?>
					<?php mnews_video_badge(); ?>
					<span class="mnw-slide__cap">
						<?php if ( $cat ) : ?>
							<span class="mnw-slide__cat"><?php echo $cat; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in mnews_category_label(). ?></span>
						<?php endif; ?>
						<span class="mnw-slide__title"><?php the_title(); ?></span>
						<span class="mnw-nb-hero__date"><?php mnews_posted_on(); ?></span>
					</span>
				</a>
			</article>
			<?php
			$trending->rewind_posts();
		}
		$this->render_news_col( __( 'Terbaru di', 'm-news' ) . ' ' . $title, $latest );
		$this->render_news_col( __( 'Trending di', 'm-news' ) . ' ' . $title, $trending );
		echo '</div>';

		wp_reset_postdata();
		echo '</div>';
		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * One "Terbaru"/"Trending" column: small heading + a list of title-left/thumbnail-right rows.
	 *
	 * @param string   $heading Column heading.
	 * @param WP_Query $query   Query (already run).
	 */
	private function render_news_col( $heading, $query ) {
		if ( ! $query->have_posts() ) {
			return;
		}
		echo '<div class="mnw-nb-col">';
		printf( '<p class="mnw-nb-col__heading">%s</p>', esc_html( $heading ) );
		echo '<div class="mnw-nb-col__list">';
		while ( $query->have_posts() ) {
			$query->the_post();
			?>
			<article class="mnw-nb-row">
				<a class="mnw-nb-row__body" href="<?php the_permalink(); ?>">
					<span class="mnw-nb-row__title"><?php the_title(); ?></span>
					<span class="mnw-card__meta"><?php mnews_posted_on(); ?></span>
				</a>
				<a class="mnw-nb-row__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
					<?php mnews_thumb( 'mnews-square', false, '64px' ); ?>
					<?php mnews_video_badge(); ?>
				</a>
			</article>
			<?php
		}
		echo '</div></div>';
	}
}
