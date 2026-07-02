<?php
/**
 * Elementor CSS_Class controls
 *
 * @package woodmart
 */

namespace XTS\Elementor\Controls;

use Elementor\Control_Base_Multiple;

/**
 * Elementor Backdrop_Filter control.
 */
class Backdrop_Filter extends Control_Base_Multiple {
	/**
	 * Get backdrop filter control type.
	 *
	 * Retrieve the control type, in this case `wd_backdrop_filter`.
	 *
	 * @return string Control type.
	 */
	public function get_type() {
		return 'wd_backdrop_filter';
	}

	/**
	 * Enqueue control scripts and styles.
	 */
	public function enqueue() {
		wp_enqueue_script( 'wd-backdrop-filter-control', WOODMART_THEME_DIR . '/inc/integrations/elementor/assets/js/backdrop-filter.js', array( 'jquery' ), woodmart_get_theme_info( 'Version' ), false );
	}

	/**
	 * Render backdrop filter control output in the editor.
	 *
	 * Used to generate the control HTML in the editor using Underscore JS
	 * template. The variables for the class are available using `data` JS
	 * object.
	 */
	public function content_template() {
		?>
		<div class="elementor-backdrop-filter-presets-box">
			<label class="elementor-control-title">
				<?php echo esc_html__( 'Presets', 'woodmart' ); ?>
			</label>
			<div class="elementor-control-input-wrapper xts-swatches-set">
				<div class="xts-btns-set">
					<?php
						$this->render_preset(
							array(
								'id'    => 'blur',
								'label' => esc_html__( 'Blur', 'woodmart' ),
								'value' => 5,
							)
						);

						$this->render_preset(
							array(
								'id'    => 'brightness',
								'label' => esc_html__( 'Brightness', 'woodmart' ),
								'value' => 1.35,
							)
						);

						$this->render_preset(
							array(
								'id'    => 'grayscale',
								'label' => esc_html__( 'Grayscale', 'woodmart' ),
								'value' => 100,
							)
						);

						$this->render_preset(
							array(
								'id'    => 'saturate',
								'label' => esc_html__( 'Saturate', 'woodmart' ),
								'value' => 160,
							)
						);

						$this->render_preset(
							array(
								'id'    => 'hue-rotate',
								'label' => esc_html__( 'Hue Rotate', 'woodmart' ),
								'value' => 45,
							)
						);

						$this->render_preset(
							array(
								'id'    => 'invert',
								'label' => esc_html__( 'Invert', 'woodmart' ),
								'value' => 80,
							)
						);
					?>
				</div>
			</div>
		</div>

		<div class="elementor-backdrop-filter-box">
			<?php
			$this->render_slider(
				array(
					'id'      => 'blur',
					'label'   => esc_html__( 'Blur', 'woodmart' ),
					'min'     => 0,
					'max'     => 50,
					'default' => 0,
					'unit'    => 'px',
				)
			);

			$this->render_slider(
				array(
					'id'      => 'brightness',
					'label'   => esc_html__( 'Brightness', 'woodmart' ),
					'min'     => 0,
					'max'     => 3,
					'default' => 1,
					'step'    => 0.01,
				)
			);

			$this->render_slider(
				array(
					'id'      => 'contrast',
					'label'   => esc_html__( 'Contrast', 'woodmart' ),
					'min'     => 0,
					'max'     => 300,
					'default' => 100,
					'unit'    => '%',
				)
			);

			$this->render_slider(
				array(
					'id'      => 'grayscale',
					'label'   => esc_html__( 'Grayscale', 'woodmart' ),
					'min'     => 0,
					'max'     => 100,
					'default' => 0,
					'unit'    => '%',
				)
			);

			$this->render_slider(
				array(
					'id'      => 'hue-rotate',
					'label'   => esc_html__( 'Hue Rotate', 'woodmart' ),
					'min'     => 0,
					'max'     => 360,
					'default' => 0,
					'unit'    => 'deg',
				)
			);

			$this->render_slider(
				array(
					'id'      => 'invert',
					'label'   => esc_html__( 'Invert', 'woodmart' ),
					'min'     => 0,
					'max'     => 100,
					'default' => 0,
					'unit'    => '%',
				)
			);

			$this->render_slider(
				array(
					'id'      => 'opacity',
					'label'   => esc_html__( 'Opacity', 'woodmart' ),
					'min'     => 0,
					'max'     => 100,
					'default' => 100,
					'unit'    => '%',
				)
			);

			$this->render_slider(
				array(
					'id'      => 'saturate',
					'label'   => esc_html__( 'Saturate', 'woodmart' ),
					'min'     => 0,
					'max'     => 300,
					'default' => 100,
					'unit'    => '%',
				)
			);

			$this->render_slider(
				array(
					'id'      => 'sepia',
					'label'   => esc_html__( 'Sepia', 'woodmart' ),
					'min'     => 0,
					'max'     => 100,
					'default' => 0,
					'unit'    => '%',
				)
			);
			?>
		</div>
		<# if ( data.description ) { #>
		<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
	}

	/**
	 * Render preset control.
	 *
	 * Render the preset control HTML. Used in the `content_template` method to generate the preset controls. The `$data` argument contains the preset data like label and value.
	 *
	 * @param array $data Preset data. Contains the preset label and value.
	 *
	 * @return void
	 */
	public function render_preset( $data ) {
		?>
		<div class="xts-set-item xts-set-btn" data-id="<?php echo esc_attr( $data['id'] ); ?>" data-value="<?php echo esc_attr( $data['value'] ); ?>" title="<?php echo esc_attr( $data['label'] ); ?>">
			<?php echo esc_html( $data['label'] ); ?>
		</div>
		<?php
	}

	/**
	 * Render slider control.
	 *
	 * Render the slider control HTML. Used in the `content_template` method to generate the slider control for each filter. The `$data` argument contains the slider data like label, min, max, default value, unit, etc.
	 *
	 * @param array $data Slider data. Contains the slider label, min, max, default value, unit, etc.
	 *
	 * @return void
	 */
	public function render_slider( $data ) {
		?>
		<div class="elementor-control-field elementor-backdrop-filter-slider elementor-control-type-slider">
			<label for="<?php $this->print_control_uid( $data['id'] ); ?>" class="elementor-control-title">
			<?php
				echo $data['label']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
			</label>

			<?php if ( ! empty( $data['unit'] ) ) : ?>
				<div class="e-units-wrapper">
					<div class="e-units-switcher" data-selected="<?php echo esc_attr( $data['unit'] ); ?>">
						<span><?php echo esc_html( $data['unit'] ); ?></span>
					</div>
				</div>
			<?php endif; ?>
			<div class="elementor-control-input-wrapper">
				<div class="elementor-slider" data-input="<?php echo esc_attr( $data['id'] ); ?>"></div>
				<div class="elementor-slider-input">
					<input id="<?php $this->print_control_uid( $data['id'] ); ?>" type="number" min="<?php echo esc_attr( $data['min'] ); ?>" max="<?php echo esc_attr( $data['max'] ); ?>" step="<?php echo esc_attr( isset( $data['step'] ) ? $data['step'] : 1 ); ?>" data-setting="<?php echo esc_attr( $data['id'] ); ?>" data-default-value="<?php echo isset( $data['default'] ) ? esc_attr( $data['default'] ) : ''; ?>"/>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Get multiple control style value.
	 *
	 * Retrieve the style of the control. Used when adding CSS rules to the control
	 * while extracting CSS from the `selectors` data argument.
	 *
	 * @param string $css_property  CSS property.
	 * @param array  $control_value Control value.
	 * @param array  $control_data Control Data.
	 *
	 * @return array Control style value.
	 */
	public function get_style_value( $css_property, $control_value, array $control_data ) {
		$parts = woodmart_get_backdrop_filter_css_parts( $control_value );

		if ( empty( $parts ) ) {
			return '__EMPTY__';
		}

		return implode( ' ', $parts );
	}
}
