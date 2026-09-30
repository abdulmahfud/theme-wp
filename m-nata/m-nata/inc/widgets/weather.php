<?php
/**
 * Widget: Prakiraan Cuaca (BMKG).
 *
 * @package M_Nata
 */

defined( 'ABSPATH' ) || exit;

/**
 * Weather forecast widget. The region is chosen in the widget form (provinsi > kab/kota > kecamatan > desa).
 */
class MNata_Widget_Weather extends MNata_Widget {

	/**
	 * Data is cached by the BMKG layer, so no HTML fragment cache (avoids caching an error state).
	 *
	 * @var int
	 */
	protected $ttl = 0;

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'mnata_weather',
			__( 'M-Nata: Prakiraan Cuaca (BMKG)', 'm-nata' ),
			__( 'Prakiraan cuaca dari BMKG untuk satu wilayah (desa/kelurahan) yang Anda pilih. Data di-cache dan diperbarui otomatis.', 'm-nata' )
		);
	}

	/**
	 * Fields.
	 *
	 * @return array
	 */
	protected function fields() {
		return array(
			'title'       => array(
				'type'    => 'text',
				'label'   => __( 'Judul', 'm-nata' ),
				'default' => __( 'Prakiraan Cuaca', 'm-nata' ),
			),
			'region'      => array(
				'type'    => 'wilayah',
				'label'   => __( 'Wilayah (kode adm4 BMKG)', 'm-nata' ),
				'default' => '',
				'desc'    => __( 'Pilih dari daftar di bawah, atau tempel kode wilayah tingkat IV (contoh: 31.71.03.1001).', 'm-nata' ),
			),
			'slots'       => array(
				'type'    => 'number',
				'label'   => __( 'Jumlah jam berikutnya', 'm-nata' ),
				'default' => 5,
				'min'     => 2,
				'max'     => 8,
			),
			'show_detail' => array(
				'type'    => 'checkbox',
				'label'   => __( 'Tampilkan kelembapan dan angin', 'm-nata' ),
				'default' => 1,
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
		if ( ! mnata_is_adm4( $i['region'] ) ) {
			if ( current_user_can( 'edit_theme_options' ) ) {
				echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- registered sidebar markup.
				$this->title( $args, $i['title'] );
				echo '<p class="mn-wx__note">' . esc_html__( 'Pilih wilayah di pengaturan widget ini (hanya terlihat oleh admin).', 'm-nata' ) . '</p>';
				echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			return;
		}

		mnata_bmkg_track( $i['region'] );
		$wx = mnata_bmkg_weather( $i['region'] );
		if ( ! $wx ) {
			return;
		}

		$now  = time();
		$idx  = 0;
		$last = count( $wx['slots'] ) - 1;
		foreach ( $wx['slots'] as $n => $slot ) {
			if ( $slot['ts'] <= $now ) {
				$idx = $n;
			}
		}
		$cur   = $wx['slots'][ $idx ];
		$next  = array_slice( $wx['slots'], min( $idx + 1, $last ), (int) $i['slots'] );
		$loc   = $wx['loc'];
		$place = implode( ', ', array_unique( array_filter( array( isset( $loc['desa'] ) ? $loc['desa'] : '', isset( $loc['kecamatan'] ) ? $loc['kecamatan'] : '', isset( $loc['kotkab'] ) ? $loc['kotkab'] : '' ) ) ) );

		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- registered sidebar markup.
		$this->title( $args, $i['title'] );
		?>
		<div class="mn-wx">
			<p class="mn-wx__place"><?php echo esc_html( $place ); ?></p>
			<div class="mn-wx__now">
				<?php mnata_icon( mnata_weather_icon( $cur['weather'], self::is_night( $cur ) ), 44 ); ?>
				<div>
					<span class="mn-wx__temp"><?php echo esc_html( round( (float) $cur['t'] ) ); ?>°C</span>
					<span class="mn-wx__desc"><?php echo esc_html( isset( $cur['weather_desc'] ) ? $cur['weather_desc'] : '' ); ?></span>
				</div>
			</div>
			<?php if ( $i['show_detail'] ) : ?>
				<ul class="mn-wx__detail">
					<?php if ( isset( $cur['hu'] ) ) : ?>
						<li><?php echo esc_html( sprintf( /* translators: %d: humidity percent. */ __( 'Kelembapan %d%%', 'm-nata' ), (int) $cur['hu'] ) ); ?></li>
					<?php endif; ?>
					<?php if ( isset( $cur['ws'] ) ) : ?>
						<li><?php echo esc_html( sprintf( /* translators: 1: wind speed, 2: wind direction. */ __( 'Angin %1$s km/j %2$s', 'm-nata' ), round( (float) $cur['ws'] ), isset( $cur['wd'] ) ? $cur['wd'] : '' ) ); ?></li>
					<?php endif; ?>
				</ul>
			<?php endif; ?>
			<?php if ( $next ) : ?>
				<ul class="mn-wx__next">
					<?php foreach ( $next as $slot ) : ?>
						<li>
							<span class="mn-wx__time"><?php echo esc_html( substr( $slot['local_datetime'], 11, 5 ) ); ?></span>
							<?php mnata_icon( mnata_weather_icon( $slot['weather'], self::is_night( $slot ) ), 26 ); ?>
							<span class="mn-wx__t"><?php echo esc_html( round( (float) $slot['t'] ) ); ?>°</span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<p class="mn-wx__src"><?php esc_html_e( 'Sumber:', 'm-nata' ); ?> <a href="https://www.bmkg.go.id/" target="_blank" rel="noopener">BMKG</a></p>
		</div>
		<?php
		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Whether a slot falls at night (local time 18:00-05:59).
	 *
	 * @param array $slot Forecast slot.
	 * @return bool
	 */
	private static function is_night( $slot ) {
		$hour = isset( $slot['local_datetime'] ) ? (int) substr( $slot['local_datetime'], 11, 2 ) : 12;
		return $hour >= 18 || $hour < 6;
	}
}
