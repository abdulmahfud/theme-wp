<?php
/**
 * Widget: Polling. Horizontal scroll-snap row of poll cards (reuses the slider's track markup/JS as-is).
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

/**
 * Polling carousel widget.
 */
class MNews_Widget_Polling extends MNews_Widget {

	/**
	 * No fragment cache: aggregate vote counts change on every vote, and stale bars would confuse readers.
	 * (Still fine behind a page cache: nothing here is per-visitor, see inc/polling.php's file header.)
	 *
	 * @var int
	 */
	protected $ttl = 0;

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'mnews_polling',
			__( 'M-News: Polling', 'm-news' ),
			__( 'Baris polling yang bisa digeser. Buat pertanyaan dan pilihannya di Polling > Tambah Polling.', 'm-news' )
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
				'default' => __( 'Polling', 'm-news' ),
			),
			'count' => array(
				'type'    => 'number',
				'label'   => __( 'Jumlah polling', 'm-news' ),
				'default' => 6,
				'min'     => 1,
				'max'     => 12,
			),
		);
	}

	/**
	 * Load the voting script (also when the HTML comes from cache).
	 *
	 * @param array $i Instance.
	 */
	protected function assets( $i ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
		mnews_enqueue_poll();
		mnews_enqueue_slider(); // Same track/prev-next/dots behaviour as the headline slider.
	}

	/**
	 * Print.
	 *
	 * @param array $args Sidebar args.
	 * @param array $i    Instance.
	 */
	protected function render( $args, $i ) {
		$query = mnews_run_query(
			array(
				'post_type'              => 'mnews_poll',
				'posts_per_page'         => max( 1, (int) $i['count'] ),
				'ignore_sticky_posts'    => true,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false, // Options/votes are fetched per poll on purpose (small, deliberate reads).
				'update_post_term_cache' => false,
			)
		);
		if ( ! $query->have_posts() ) {
			return;
		}

		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- registered sidebar markup.
		echo '<div class="mnw-block__head">';
		$this->title( $args, $i['title'] );
		printf(
			'<a class="mnw-block__more" href="%1$s">%2$s</a>',
			esc_url( get_post_type_archive_link( 'mnews_poll' ) ),
			esc_html__( 'Lihat Lainnya', 'm-news' )
		);
		echo '</div>';

		echo '<div class="mnw-slider mnw-slider--polling" data-mnw-slider data-autoplay="0" role="region" aria-label="' . esc_attr__( 'Polling', 'm-news' ) . '">';
		echo '<div class="mnw-slider__stage"><div class="mnw-slider__track">';
		while ( $query->have_posts() ) {
			$query->the_post();
			mnews_render_poll_card( get_the_ID() );
		}
		wp_reset_postdata();
		echo '</div>';
		foreach ( array(
			-1 => 'chevron-left',
			1  => 'chevron-right',
		) as $dir => $icon ) {
			printf(
				'<button type="button" class="mnw-slider__nav mnw-slider__nav--%1$s" data-dir="%2$d" aria-label="%3$s">',
				$dir < 0 ? 'prev' : 'next',
				(int) $dir,
				$dir < 0 ? esc_attr__( 'Sebelumnya', 'm-news' ) : esc_attr__( 'Selanjutnya', 'm-news' )
			);
			mnews_icon( $icon, 22 );
			echo '</button>';
		}
		echo '</div></div>';
		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
