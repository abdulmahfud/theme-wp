<?php
/**
 * Widget: Info Gempa (BMKG).
 *
 * @package M_Nata
 */

defined( 'ABSPATH' ) || exit;

/**
 * Earthquake info widget: latest quake, M5.0+ list, or felt-quake list.
 */
class MNata_Widget_Earthquake extends MNata_Widget {

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
			'mnata_earthquake',
			__( 'M-Nata: Info Gempa (BMKG)', 'm-nata' ),
			__( 'Gempa terkini, gempa M 5.0+, atau gempa dirasakan dari BMKG. Data di-cache dan diperbarui otomatis.', 'm-nata' )
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
				'label'   => __( 'Judul', 'm-nata' ),
				'default' => __( 'Info Gempa', 'm-nata' ),
			),
			'mode'     => array(
				'type'    => 'select',
				'label'   => __( 'Data', 'm-nata' ),
				'default' => 'latest',
				'choices' => array(
					'latest' => __( 'Gempa terkini (1 kejadian, lengkap)', 'm-nata' ),
					'm5'     => __( 'Daftar gempa M 5.0+', 'm-nata' ),
					'felt'   => __( 'Daftar gempa dirasakan', 'm-nata' ),
				),
			),
			'count'    => array(
				'type'    => 'number',
				'label'   => __( 'Jumlah kejadian (untuk daftar)', 'm-nata' ),
				'default' => 5,
				'min'     => 1,
				'max'     => 15,
			),
			'show_map' => array(
				'type'    => 'checkbox',
				'label'   => __( 'Tampilkan peta guncangan (± 230 KB, hanya mode "terkini")', 'm-nata' ),
				'default' => 0,
				'desc'    => __( 'Gambar dari BMKG dimuat lazy. Nonaktif secara bawaan demi kecepatan.', 'm-nata' ),
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
		mnata_bmkg_track_quake( $kind );

		$quakes = mnata_bmkg_quakes( $kind );
		if ( ! $quakes ) {
			return;
		}

		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- registered sidebar markup.
		$this->title( $args, $i['title'] );

		if ( 'latest' === $i['mode'] ) {
			$this->render_latest( $quakes[0], (bool) $i['show_map'] );
		} else {
			echo '<ul class="mn-eq__list">';
			foreach ( array_slice( $quakes, 0, (int) $i['count'] ) as $q ) {
				$this->render_row( $q );
			}
			echo '</ul>';
		}

		printf(
			'<p class="mn-wx__src">%1$s <a href="%2$s" target="_blank" rel="noopener">BMKG</a></p>',
			esc_html__( 'Sumber:', 'm-nata' ),
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
		<div class="mn-eq">
			<div class="mn-eq__head">
				<span class="mn-eq__mag <?php echo esc_attr( self::mag_class( $mag ) ); ?>"><?php echo esc_html( $mag ); ?><small>M</small></span>
				<p class="mn-eq__where"><?php echo esc_html( isset( $q['Wilayah'] ) ? $q['Wilayah'] : '' ); ?></p>
			</div>
			<p class="mn-eq__when"><?php echo esc_html( trim( ( isset( $q['Tanggal'] ) ? $q['Tanggal'] : '' ) . ', ' . ( isset( $q['Jam'] ) ? $q['Jam'] : '' ), ', ' ) ); ?></p>
			<ul class="mn-eq__facts">
				<li><?php echo esc_html( sprintf( /* translators: %s: depth. */ __( 'Kedalaman: %s', 'm-nata' ), isset( $q['Kedalaman'] ) ? $q['Kedalaman'] : '-' ) ); ?></li>
				<li><?php echo esc_html( sprintf( /* translators: 1: latitude, 2: longitude. */ __( 'Lokasi: %1$s, %2$s', 'm-nata' ), isset( $q['Lintang'] ) ? $q['Lintang'] : '-', isset( $q['Bujur'] ) ? $q['Bujur'] : '-' ) ); ?></li>
				<?php if ( ! empty( $q['Dirasakan'] ) ) : ?>
					<li><?php echo esc_html( sprintf( /* translators: %s: felt in areas. */ __( 'Dirasakan: %s', 'm-nata' ), $q['Dirasakan'] ) ); ?></li>
				<?php endif; ?>
			</ul>
			<?php if ( ! empty( $q['Potensi'] ) ) : ?>
				<p class="mn-eq__note"><?php echo esc_html( $q['Potensi'] ); ?></p>
			<?php endif; ?>
			<?php if ( $map && ! empty( $q['Shakemap'] ) && preg_match( '/^[\w.\-]+$/', $q['Shakemap'] ) ) : ?>
				<img class="mn-eq__map" src="<?php echo esc_url( MNATA_BMKG_QUAKE_URL . $q['Shakemap'] ); ?>" alt="<?php esc_attr_e( 'Peta guncangan gempa (BMKG)', 'm-nata' ); ?>" width="612" height="717" loading="lazy" decoding="async" referrerpolicy="no-referrer">
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
		<li class="mn-eq__row">
			<span class="mn-eq__mag mn-eq__mag--sm <?php echo esc_attr( self::mag_class( $mag ) ); ?>"><?php echo esc_html( $mag ); ?></span>
			<div>
				<p class="mn-eq__where"><?php echo esc_html( isset( $q['Wilayah'] ) ? $q['Wilayah'] : '' ); ?></p>
				<p class="mn-eq__when">
					<?php echo esc_html( $when ); ?>
					<?php if ( ! empty( $q['Kedalaman'] ) ) : ?>
						· <?php echo esc_html( $q['Kedalaman'] ); ?>
					<?php endif; ?>
				</p>
				<?php if ( ! empty( $q['Dirasakan'] ) ) : ?>
					<p class="mn-eq__note"><?php echo esc_html( $q['Dirasakan'] ); ?></p>
				<?php endif; ?>
			</div>
		</li>
		<?php
	}
}
