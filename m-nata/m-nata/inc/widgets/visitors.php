<?php
/**
 * Widget: Statistik Pengunjung.
 *
 * @package M_Nata
 */

defined( 'ABSPATH' ) || exit;

/**
 * Visitor statistics widget (online now, today, yesterday, this month, total).
 */
class MNata_Widget_Visitors extends MNata_Widget {

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
			'mnata_visitors',
			__( 'M-Nata: Statistik Pengunjung', 'm-nata' ),
			__( 'Pengunjung online, hari ini, kemarin, bulan ini, dan total. Dihitung lewat permintaan latar belakang kecil (tetap cepat dengan page cache, bisa dipakai di AMP), tanpa menyimpan alamat IP.', 'm-nata' )
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
				'label'   => __( 'Judul', 'm-nata' ),
				'default' => __( 'Statistik Pengunjung', 'm-nata' ),
			),
			'online'    => array(
				'type'    => 'checkbox',
				'label'   => __( 'Tampilkan pengunjung online (5 menit terakhir)', 'm-nata' ),
				'default' => 1,
			),
			'today'     => array(
				'type'    => 'checkbox',
				'label'   => __( 'Tampilkan pengunjung hari ini', 'm-nata' ),
				'default' => 1,
			),
			'yesterday' => array(
				'type'    => 'checkbox',
				'label'   => __( 'Tampilkan pengunjung kemarin', 'm-nata' ),
				'default' => 1,
			),
			'month'     => array(
				'type'    => 'checkbox',
				'label'   => __( 'Tampilkan pengunjung bulan ini', 'm-nata' ),
				'default' => 1,
			),
			'total'     => array(
				'type'    => 'checkbox',
				'label'   => __( 'Tampilkan total pengunjung', 'm-nata' ),
				'default' => 1,
			),
			'pageviews' => array(
				'type'    => 'checkbox',
				'label'   => __( 'Tampilkan total tayangan halaman', 'm-nata' ),
				'default' => 0,
			),
			'offset'    => array(
				'type'    => 'number',
				'label'   => __( 'Angka awal total pengunjung', 'm-nata' ),
				'default' => 0,
				'min'     => 0,
				'max'     => 1000000000,
				'desc'    => __( 'Ditambahkan ke total, berguna bila situs ini pindahan dan Anda ingin melanjutkan hitungan lama.', 'm-nata' ),
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
		$s    = mnata_stats_snapshot();
		$rows = array();
		if ( $i['online'] ) {
			$rows[] = array( __( 'Online sekarang', 'm-nata' ), $s['online'], true );
		}
		if ( $i['today'] ) {
			$rows[] = array( __( 'Hari ini', 'm-nata' ), $s['today'], false );
		}
		if ( $i['yesterday'] ) {
			$rows[] = array( __( 'Kemarin', 'm-nata' ), $s['yesterday'], false );
		}
		if ( $i['month'] ) {
			$rows[] = array( __( 'Bulan ini', 'm-nata' ), $s['month'], false );
		}
		if ( $i['total'] ) {
			$rows[] = array( __( 'Total pengunjung', 'm-nata' ), $s['total'] + (int) $i['offset'], false );
		}
		if ( $i['pageviews'] ) {
			$rows[] = array( __( 'Total tayangan', 'm-nata' ), $s['pv_total'], false );
		}
		if ( ! $rows ) {
			return;
		}

		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- registered sidebar markup.
		$this->title( $args, $i['title'] );
		echo '<ul class="mn-stats">';
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
