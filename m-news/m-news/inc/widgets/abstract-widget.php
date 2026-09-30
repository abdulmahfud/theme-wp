<?php
/**
 * Base class for M-News widgets: schema-driven form/update, output cache, selective refresh.
 *
 * A child class defines fields() and render(). Everything else is shared.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

/**
 * Base class for M-News widgets (schema-driven form, sanitising update, cached output).
 */
abstract class MNews_Widget extends WP_Widget {

	/**
	 * Cache lifetime in seconds. 0 disables caching.
	 *
	 * @var int
	 */
	protected $ttl = 300;

	/**
	 * Constructor.
	 *
	 * @param string $id_base     Widget ID base.
	 * @param string $name        Widget name.
	 * @param string $description Description.
	 */
	public function __construct( $id_base, $name, $description ) {
		parent::__construct(
			$id_base,
			$name,
			array(
				'classname'                   => 'mnw-w-' . str_replace( 'mnews_', '', $id_base ),
				'description'                 => $description,
				'customize_selective_refresh' => true,
			)
		);
	}

	/**
	 * Field schema: key => array( type, label, default, choices|min|max, desc ).
	 * Types: text, slug, url, color, html, image, wilayah, number, select, checkbox, category.
	 *
	 * @return array<string,array>
	 */
	abstract protected function fields();

	/**
	 * Print the widget body (already inside the wrapper is NOT assumed; print before/after yourself).
	 *
	 * @param array $args     Sidebar args.
	 * @param array $instance Parsed instance.
	 */
	abstract protected function render( $args, $instance );

	/**
	 * Hook to enqueue assets. Runs on every request, also on cache hits.
	 *
	 * @param array $instance Parsed instance.
	 */
	protected function assets( $instance ) {} // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter

	/**
	 * Extra values that make the cached output differ (e.g. the page number).
	 *
	 * @param array $instance Parsed instance.
	 * @return array
	 */
	protected function cache_vars( $instance ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
		return array();
	}

	/**
	 * Merge instance with defaults.
	 *
	 * @param array $instance Saved values.
	 * @return array
	 */
	protected function parse( $instance ) {
		$out = array();
		foreach ( $this->fields() as $key => $f ) {
			$out[ $key ] = isset( $instance[ $key ] ) ? $instance[ $key ] : $f['default'];
		}
		return $out;
	}

	/**
	 * Front-end output with fragment cache.
	 *
	 * @param array $args     Sidebar args.
	 * @param array $instance Saved values.
	 */
	public function widget( $args, $instance ) {
		$i = $this->parse( $instance );
		$this->assets( $i );

		$render = function () use ( $args, $i ) {
			mnews_icon_log( null, true );
			ob_start();
			$this->render( $args, $i );
			return array(
				'html'  => ob_get_clean(),
				'icons' => mnews_icon_log( null, true ),
			);
		};

		if ( $this->ttl > 0 ) {
			$key   = implode(
				'|',
				array( $this->id, isset( $args['id'] ) ? $args['id'] : '', md5( wp_json_encode( $i ) ), wp_json_encode( $this->cache_vars( $i ) ) )
			);
			$cache = mnews_cache_remember( $key, $this->ttl, $render );
		} else {
			$cache = $render();
		}

		foreach ( $cache['icons'] as $icon ) {
			mnews_icons_used( $icon );
		}
		echo $cache['html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside render().
	}

	/**
	 * Print the widget title using the sidebar's markup.
	 *
	 * @param array  $args  Sidebar args.
	 * @param string $title Title.
	 */
	protected function title( $args, $title ) {
		$title = apply_filters( 'widget_title', $title, array(), $this->id_base );
		if ( '' !== $title ) {
			echo $args['before_title'] . esc_html( $title ) . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sidebar markup + escaped title.
		}
	}

	/**
	 * Sanitise on save according to the schema.
	 *
	 * @param array $new_instance Submitted.
	 * @param array $old_instance Previous.
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
		$out = array();
		foreach ( $this->fields() as $key => $f ) {
			$raw = isset( $new_instance[ $key ] ) ? $new_instance[ $key ] : '';
			switch ( $f['type'] ) {
				case 'checkbox':
					$out[ $key ] = ! empty( $new_instance[ $key ] ) ? 1 : 0;
					break;
				case 'number':
					$out[ $key ] = max( $f['min'], min( $f['max'], (int) $raw ) );
					break;
				case 'category':
				case 'image':
					$out[ $key ] = absint( $raw );
					break;
				case 'url':
					$out[ $key ] = esc_url_raw( $raw );
					break;
				case 'wilayah':
					$out[ $key ] = mnews_is_adm4( trim( (string) $raw ) ) ? trim( (string) $raw ) : '';
					break;
				case 'color':
					$hex         = sanitize_hex_color( (string) $raw );
					$out[ $key ] = $hex ? $hex : $f['default'];
					break;
				case 'html':
					// Raw code (AdSense, scripts) only for users allowed to post unfiltered HTML.
					$out[ $key ] = current_user_can( 'unfiltered_html' ) ? (string) $raw : wp_kses_post( (string) $raw );
					break;
				case 'slug':
					$out[ $key ] = sanitize_title( $raw );
					break;
				case 'select':
					$out[ $key ] = isset( $f['choices'][ $raw ] ) ? $raw : $f['default'];
					break;
				default:
					$out[ $key ] = sanitize_text_field( $raw );
			}
		}
		// Deferred: the Customizer rejects a widget update that writes any option besides its own
		// ("widget_setting_too_many_options"), and the flush writes an option.
		add_action( 'shutdown', 'mnews_cache_flush' );
		return $out;
	}

	/**
	 * Admin form generated from the schema.
	 *
	 * @param array $instance Saved values.
	 * @return string
	 */
	public function form( $instance ) {
		$i = $this->parse( $instance );

		foreach ( $this->fields() as $key => $f ) {
			$id   = $this->get_field_id( $key );
			$name = $this->get_field_name( $key );
			$val  = $i[ $key ];

			echo '<p>';
			if ( 'checkbox' === $f['type'] ) {
				printf(
					'<input class="checkbox" type="checkbox" id="%1$s" name="%2$s" value="1" %3$s> <label for="%1$s">%4$s</label>',
					esc_attr( $id ),
					esc_attr( $name ),
					checked( ! empty( $val ), true, false ),
					esc_html( $f['label'] )
				);
			} else {
				printf( '<label for="%s">%s</label>', esc_attr( $id ), esc_html( $f['label'] ) );
				if ( 'select' === $f['type'] || 'category' === $f['type'] ) {
					$choices = isset( $f['choices'] ) ? $f['choices'] : array();
					if ( 'category' === $f['type'] ) {
						$choices = array( 0 => __( '— Semua kategori —', 'm-news' ) );
						foreach ( get_categories( array( 'hide_empty' => false ) ) as $cat ) {
							$choices[ $cat->term_id ] = $cat->name;
						}
					}
					printf( '<select class="widefat" id="%s" name="%s">', esc_attr( $id ), esc_attr( $name ) );
					foreach ( $choices as $v => $label ) {
						printf( '<option value="%s" %s>%s</option>', esc_attr( $v ), selected( (string) $val, (string) $v, false ), esc_html( $label ) );
					}
					echo '</select>';
				} elseif ( 'wilayah' === $f['type'] ) {
					printf(
						'<input class="widefat mnews-wilayah-code" type="text" id="%1$s" name="%2$s" value="%3$s" placeholder="31.71.03.1001" pattern="\d{2}\.\d{2}\.\d{2}\.\d{4}">',
						esc_attr( $id ),
						esc_attr( $name ),
						esc_attr( $val )
					);
					echo '<span class="mnews-wilayah">';
					foreach ( array(
						'province' => __( 'Provinsi', 'm-news' ),
						'regency'  => __( 'Kabupaten / Kota', 'm-news' ),
						'district' => __( 'Kecamatan', 'm-news' ),
						'village'  => __( 'Desa / Kelurahan', 'm-news' ),
					) as $level => $level_label ) {
						printf( '<select class="widefat mnews-wil-%1$s" data-level="%1$s" aria-label="%2$s"><option value="">%2$s</option></select>', esc_attr( $level ), esc_html( $level_label ) );
					}
					echo '</span>';
				} elseif ( 'html' === $f['type'] ) {
					printf( '<textarea class="widefat code" rows="6" id="%s" name="%s">%s</textarea>', esc_attr( $id ), esc_attr( $name ), esc_textarea( $val ) );
				} elseif ( 'color' === $f['type'] ) {
					printf( '<br><input type="color" id="%s" name="%s" value="%s">', esc_attr( $id ), esc_attr( $name ), esc_attr( $val ) );
				} elseif ( 'image' === $f['type'] ) {
					printf(
						'<span class="mnews-image-field"><span class="mnews-image-preview">%1$s</span><input type="hidden" class="mnews-image-id" id="%2$s" name="%3$s" value="%4$d"><button type="button" class="button mnews-image-pick">%5$s</button> <button type="button" class="button-link mnews-image-clear">%6$s</button></span>',
						$val ? wp_get_attachment_image( (int) $val, 'thumbnail' ) : '', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-generated <img>.
						esc_attr( $id ),
						esc_attr( $name ),
						(int) $val,
						esc_html__( 'Pilih gambar', 'm-news' ),
						esc_html__( 'Hapus', 'm-news' )
					);
				} elseif ( 'number' === $f['type'] ) {
					printf(
						'<input class="tiny-text" type="number" id="%s" name="%s" value="%d" min="%d" max="%d">',
						esc_attr( $id ),
						esc_attr( $name ),
						(int) $val,
						(int) $f['min'],
						(int) $f['max']
					);
				} else {
					printf( '<input class="widefat" type="%s" id="%s" name="%s" value="%s">', 'url' === $f['type'] ? 'url' : 'text', esc_attr( $id ), esc_attr( $name ), esc_attr( $val ) );
				}
			}
			if ( ! empty( $f['desc'] ) ) {
				printf( '<br><small>%s</small>', esc_html( $f['desc'] ) );
			}
			echo '</p>';
		}
		return '';
	}
}
