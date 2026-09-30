<?php
/**
 * Widget: Info Gempa (BMKG).
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

/**
 * Earthquake info widget: latest quake, M5.0+ list, or felt-quake list.
 */
class MNews_Widget_Earthquake extends MNews_Widget {

	/**
	 * Data is cached by the BMKG layer, so no HTML fragment cache.
	 *
	 * @var int
	 */
	protected $ttl = 0;

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'mnews_earthquake',
			__( 'M-News: Info Gempa (BMKG)', 'm-news' ),
			__( 'Gempa terkini, gempa M 5.0+, atau gempa dirasakan dari BMKG. Data di-cache dan diperbarui otomatis.', 'm-news' )
		);
	}

	/**
	 * Fields.
	 *
	 * @return array
	 */
	protected function fields() {
		return array(
			'title'    => array(
				'type'    => 'text',
				'label'   => __( 'Judul', 'm-news' ),
				'default' => __( 'Info Gempa', 'm-news' ),
			),
			'mode'     => array(
				'type'    => 'select',
				'label'   => __( 'Data', 'm-news' ),
				'default' => 'latest',
				'choices' => array(
					'latest' => __( 'Gempa terkini (1 kejadian, lengkap)', 'm-news' ),
					'm5'     => __( 'Daftar gempa M 5.0+', 'm-news' ),
					'felt'   => __( 'Daftar gempa dirasakan', 'm-news' ),
				),
			),
			'count'    => array(
				'type'    => 'number',
				'label'   => __( 'Jumlah kejadian (untuk daftar)', 'm-news' ),
				'default' => 5,
				'min'     => 1,
				'max'     => 15,
			),
			'show_map' => array(
				'type'    => 'checkbox',
				'label'   => __( 'Tampilkan peta guncangan (± 230 KB, hanya mode "terkini")', 'm-news' ),
				'default' => 0,
				'desc'    => __( 'Gambar dari BMKG dimuat lazy. Nonaktif secara bawaan demi kecepatan.', 'm-news' ),
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
		$kinds = array(
			'latest' => 'autogempa',
			'm5'     => 'gempaterkini',
			'felt'   => 'gempadirasakan',
		);
		$kind  = $kinds[ $i['mode'] ];
		mnews_bmkg_track_quake( $kind );

		$quakes = mnews_bmkg_quakes( $kind );
		if ( ! $quakes ) {
			return;
		}

		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- registered sidebar markup.
		$this->title( $args, $i['title'] );

		if ( 'latest' === $i['mode'] ) {
			$this->render_latest( $quakes[0], (bool) $i['show_map'] );
		} else {
			echo '<ul class="mnw-eq__list">';
			foreach ( array_slice( $quakes, 0, (int) $i['count'] ) as $q ) {
				$this->render_row( $q );
			}
			echo '</ul>';
		}

		printf(
			'<p class="mnw-wx__src">%1$s <a href="%2$s" target="_blank" rel="noopener">BMKG</a></p>',
			esc_html__( 'Sumber:', 'm-news' ),
			esc_url( 'https://www.bmkg.go.id/gempabumi/gempabumi-terkini.html' )
		);
		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * CSS class for a magnitude badge.
	 *
	 * @param string $mag Magnitude.
	 * @return string
	 */
	private static function mag_class( $mag ) {
		$m = (float) $mag;
		if ( $m >= 5 ) {
			return 'is-high';
		}
		return $m >= 4 ? 'is-mid' : 'is-low';
	}

	/**
	 * The single latest quake with details.
	 *
	 * @param array $q   Quake.
	 * @param bool  $map Show shakemap.
	 */
	private function render_latest( $q, $map ) {
		$mag = isset( $q['Magnitude'] ) ? $q['Magnitude'] : '';
		?>
		<div class="mnw-eq">
			<div class="mnw-eq__head">
				<span class="mnw-eq__mag <?php echo esc_attr( self::mag_class( $mag ) ); ?>"><?php echo esc_html( $mag ); ?><small>M</small></span>
				<p class="mnw-eq__where"><?php echo esc_html( isset( $q['Wilayah'] ) ? $q['Wilayah'] : '' ); ?></p>
			</div>
			<p class="mnw-eq__when"><?php echo esc_html( trim( ( isset( $q['Tanggal'] ) ? $q['Tanggal'] : '' ) . ', ' . ( isset( $q['Jam'] ) ? $q['Jam'] : '' ), ', ' ) ); ?></p>
			<ul class="mnw-eq__facts">
				<li><?php echo esc_html( sprintf( /* translators: %s: depth. */ __( 'Kedalaman: %s', 'm-news' ), isset( $q['Kedalaman'] ) ? $q['Kedalaman'] : '-' ) ); ?></li>
				<li><?php echo esc_html( sprintf( /* translators: 1: latitude, 2: longitude. */ __( 'Lokasi: %1$s, %2$s', 'm-news' ), isset( $q['Lintang'] ) ? $q['Lintang'] : '-', isset( $q['Bujur'] ) ? $q['Bujur'] : '-' ) ); ?></li>
				<?php if ( ! empty( $q['Dirasakan'] ) ) : ?>
					<li><?php echo esc_html( sprintf( /* translators: %s: felt in areas. */ __( 'Dirasakan: %s', 'm-news' ), $q['Dirasakan'] ) ); ?></li>
				<?php endif; ?>
			</ul>
			<?php if ( ! empty( $q['Potensi'] ) ) : ?>
				<p class="mnw-eq__note"><?php echo esc_html( $q['Potensi'] ); ?></p>
			<?php endif; ?>
			<?php if ( $map && ! empty( $q['Shakemap'] ) && preg_match( '/^[\w.\-]+$/', $q['Shakemap'] ) ) : ?>
				<img class="mnw-eq__map" src="<?php echo esc_url( MNEWS_BMKG_QUAKE_URL . $q['Shakemap'] ); ?>" alt="<?php esc_attr_e( 'Peta guncangan gempa (BMKG)', 'm-news' ); ?>" width="612" height="717" loading="lazy" decoding="async" referrerpolicy="no-referrer">
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * One row of a list.
	 *
	 * @param array $q Quake.
	 */
	private function render_row( $q ) {
		$mag  = isset( $q['Magnitude'] ) ? $q['Magnitude'] : '';
		$when = trim( ( isset( $q['Tanggal'] ) ? $q['Tanggal'] : '' ) . ', ' . ( isset( $q['Jam'] ) ? $q['Jam'] : '' ), ', ' );
		?>
		<li class="mnw-eq__row">
			<span class="mnw-eq__mag mnw-eq__mag--sm <?php echo esc_attr( self::mag_class( $mag ) ); ?>"><?php echo esc_html( $mag ); ?></span>
			<div>
				<p class="mnw-eq__where"><?php echo esc_html( isset( $q['Wilayah'] ) ? $q['Wilayah'] : '' ); ?></p>
				<p class="mnw-eq__when">
					<?php echo esc_html( $when ); ?>
					<?php if ( ! empty( $q['Kedalaman'] ) ) : ?>
						· <?php echo esc_html( $q['Kedalaman'] ); ?>
					<?php endif; ?>
				</p>
				<?php if ( ! empty( $q['Dirasakan'] ) ) : ?>
					<p class="mnw-eq__note"><?php echo esc_html( $q['Dirasakan'] ); ?></p>
				<?php endif; ?>
			</div>
		</li>
		<?php
	}
}
