<?php
/**
 * Widget: Statistik Pengunjung.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

/**
 * Visitor statistics widget (online now, today, yesterday, this month, total).
 */
class MNews_Widget_Visitors extends MNews_Widget {

	/**
	 * Numbers are cached by the stats layer; refresh the fragment every minute.
	 *
	 * @var int
	 */
	protected $ttl = 60;

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'mnews_visitors',
			__( 'M-News: Statistik Pengunjung', 'm-news' ),
			__( 'Pengunjung online, hari ini, kemarin, bulan ini, dan total. Dihitung lewat permintaan latar belakang kecil (tetap cepat dengan page cache, bisa dipakai di AMP), tanpa menyimpan alamat IP.', 'm-news' )
		);
	}

	/**
	 * Fields.
	 *
	 * @return array
	 */
	protected function fields() {
		return array(
			'title'     => array(
				'type'    => 'text',
				'label'   => __( 'Judul', 'm-news' ),
				'default' => __( 'Statistik Pengunjung', 'm-news' ),
			),
			'online'    => array(
				'type'    => 'checkbox',
				'label'   => __( 'Tampilkan pengunjung online (5 menit terakhir)', 'm-news' ),
				'default' => 1,
			),
			'today'     => array(
				'type'    => 'checkbox',
				'label'   => __( 'Tampilkan pengunjung hari ini', 'm-news' ),
				'default' => 1,
			),
			'yesterday' => array(
				'type'    => 'checkbox',
				'label'   => __( 'Tampilkan pengunjung kemarin', 'm-news' ),
				'default' => 1,
			),
			'month'     => array(
				'type'    => 'checkbox',
				'label'   => __( 'Tampilkan pengunjung bulan ini', 'm-news' ),
				'default' => 1,
			),
			'total'     => array(
				'type'    => 'checkbox',
				'label'   => __( 'Tampilkan total pengunjung', 'm-news' ),
				'default' => 1,
			),
			'pageviews' => array(
				'type'    => 'checkbox',
				'label'   => __( 'Tampilkan total tayangan halaman', 'm-news' ),
				'default' => 0,
			),
			'offset'    => array(
				'type'    => 'number',
				'label'   => __( 'Angka awal total pengunjung', 'm-news' ),
				'default' => 0,
				'min'     => 0,
				'max'     => 1000000000,
				'desc'    => __( 'Ditambahkan ke total, berguna bila situs ini pindahan dan Anda ingin melanjutkan hitungan lama.', 'm-news' ),
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
		$s    = mnews_stats_snapshot();
		$rows = array();
		if ( $i['online'] ) {
			$rows[] = array( __( 'Online sekarang', 'm-news' ), $s['online'], true );
		}
		if ( $i['today'] ) {
			$rows[] = array( __( 'Hari ini', 'm-news' ), $s['today'], false );
		}
		if ( $i['yesterday'] ) {
			$rows[] = array( __( 'Kemarin', 'm-news' ), $s['yesterday'], false );
		}
		if ( $i['month'] ) {
			$rows[] = array( __( 'Bulan ini', 'm-news' ), $s['month'], false );
		}
		if ( $i['total'] ) {
			$rows[] = array( __( 'Total pengunjung', 'm-news' ), $s['total'] + (int) $i['offset'], false );
		}
		if ( $i['pageviews'] ) {
			$rows[] = array( __( 'Total tayangan', 'm-news' ), $s['pv_total'], false );
		}
		if ( ! $rows ) {
			return;
		}

		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- registered sidebar markup.
		$this->title( $args, $i['title'] );
		echo '<ul class="mnw-stats">';
		foreach ( $rows as $row ) {
			printf(
				'<li%1$s><span>%2$s</span><strong>%3$s</strong></li>',
				$row[2] ? ' class="is-live"' : '',
				esc_html( $row[0] ),
				esc_html( number_format_i18n( $row[1] ) )
			);
		}
		echo '</ul>';
		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
