<?php
/**
 * Custom Customizer controls. Loaded only when the Customizer is running.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the control classes once WP_Customize_Control exists.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function mnews_register_controls( $wp_customize ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found -- required hook signature.
	if ( ! class_exists( 'MNews_Gradient_Preset_Control' ) ) {
		/**
		 * Radio swatches: each option previews its own gradient.
		 */
		class MNews_Gradient_Preset_Control extends WP_Customize_Control {
			/**
			 * Control type.
			 *
			 * @var string
			 */
			public $type = 'mnews-gradient-preset';

			/**
			 * Render the swatch list.
			 */
			public function render_content() {
				$presets = mnews_gradient_presets();
				?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php if ( $this->description ) : ?>
					<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
				<?php endif; ?>
				<style>
					.mnews-swatches{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:8px}
					.mnews-swatches label{display:block;cursor:pointer}
					.mnews-swatches input{position:absolute;opacity:0;pointer-events:none}
					.mnews-swatches .sw{display:block;height:34px;border-radius:4px;border:2px solid transparent;box-shadow:inset 0 0 0 1px rgba(0,0,0,.12)}
					.mnews-swatches .nm{display:block;font-size:11px;margin-top:3px;line-height:1.2}
					.mnews-swatches input:checked+.sw{border-color:#2271b1;box-shadow:0 0 0 1px #2271b1}
					.mnews-swatches input:focus-visible+.sw{outline:2px solid #2271b1;outline-offset:2px}
				</style>
				<div class="mnews-swatches">
					<?php foreach ( $presets as $key => $preset ) : ?>
						<label>
							<input type="radio" name="<?php echo esc_attr( '_customize-radio-' . $this->id ); ?>" value="<?php echo esc_attr( $key ); ?>" <?php $this->link(); ?> <?php checked( $this->value(), $key ); ?>>
							<span class="sw" style="background:linear-gradient(<?php echo esc_attr( $preset['dir'] . ',' . implode( ',', $preset['colors'] ) ); ?>)"></span>
							<span class="nm"><?php echo esc_html( $preset['label'] ); ?></span>
						</label>
					<?php endforeach; ?>
					<label>
						<input type="radio" name="<?php echo esc_attr( '_customize-radio-' . $this->id ); ?>" value="custom" <?php $this->link(); ?> <?php checked( $this->value(), 'custom' ); ?>>
						<span class="sw" style="background:repeating-linear-gradient(45deg,#eee,#eee 6px,#ddd 6px,#ddd 12px)"></span>
						<span class="nm"><?php esc_html_e( 'Kustom (pilih sendiri)', 'm-news' ); ?></span>
					</label>
				</div>
				<?php
			}
		}
	}
}
add_action( 'customize_register', 'mnews_register_controls', 1 );
