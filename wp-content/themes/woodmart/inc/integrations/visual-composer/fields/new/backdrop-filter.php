<?php
/**
 * Backdrop filter.
 *
 * @package woodmart
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

if ( ! function_exists( 'woodmart_get_backdrop_filter_param' ) ) {
	/**
	 * Get backdrop filter control html.
	 *
	 * @param array  $settings Control settings.
	 * @param string $value   Control value.
	 *
	 * @return string Control HTML.
	 */
	function woodmart_get_backdrop_filter_param( $settings, $value ) {
		$value_string = $value;
		$value        = json_decode( woodmart_decompress( $value ), true );
		$value        = is_array( $value ) ? $value['devices']['desktop'] : array();
		$value        = shortcode_atts(
			array(
				'blur'       => 0,
				'brightness' => 1,
				'contrast'   => 100,
				'grayscale'  => 0,
				'hue-rotate' => 0,
				'invert'     => 0,
				'opacity'    => 100,
				'saturate'   => 100,
				'sepia'      => 0,
			),
			$value
		);
		ob_start();
		?>
		<button class="xts-backdrop-filter-opener xts-dropdown-opener xts-btn xts-i-cog">
			<?php echo esc_html__( 'Edit', 'woodmart' ); ?>
		</button>
		<div class="xts-backdrop-filter-content xts-dropdown-content xts-dropdown-col-2 xts-hidden">
			<div class="xts-backdrop-filter-reset xts-dropdown-btn-reset xts-i-round-left">
				<div class="xts-tooltip xts-right">
					<?php esc_html_e( 'Reset', 'woodmart' ); ?>
				</div>
			</div>
			<div class="xts-backdrop-presets xts-swatches-set">
				<div class="xts-btns-set">
					<?php
						woodmart_render_backdrop_filter_preset(
							array(
								'id'    => 'blur',
								'label' => esc_html__( 'Blur', 'woodmart' ),
								'value' => 5,
							)
						);

						woodmart_render_backdrop_filter_preset(
							array(
								'id'    => 'brightness',
								'label' => esc_html__( 'Brightness', 'woodmart' ),
								'value' => 1.35,
							)
						);

						woodmart_render_backdrop_filter_preset(
							array(
								'id'    => 'grayscale',
								'label' => esc_html__( 'Grayscale', 'woodmart' ),
								'value' => 100,
							)
						);

						woodmart_render_backdrop_filter_preset(
							array(
								'id'    => 'saturate',
								'label' => esc_html__( 'Saturate', 'woodmart' ),
								'value' => 160,
							)
						);

						woodmart_render_backdrop_filter_preset(
							array(
								'id'    => 'hue-rotate',
								'label' => esc_html__( 'Hue Rotate', 'woodmart' ),
								'value' => 45,
							)
						);

						woodmart_render_backdrop_filter_preset(
							array(
								'id'    => 'invert',
								'label' => esc_html__( 'Invert', 'woodmart' ),
								'value' => 80,
							)
						);
					?>
				</div>
			</div>
			<div class="xts-backdrop-filter xts-dropdown-controls-wrap">
				<?php
					woodmart_render_backdrop_filter_slider(
						array(
							'id'      => 'blur',
							'label'   => esc_html__( 'Blur', 'woodmart' ),
							'min'     => 0,
							'max'     => 50,
							'default' => 0,
							'unit'    => 'px',
						),
						$value['blur']
					);

					woodmart_render_backdrop_filter_slider(
						array(
							'id'      => 'brightness',
							'label'   => esc_html__( 'Brightness', 'woodmart' ),
							'min'     => 0,
							'max'     => 3,
							'default' => 1,
							'step'    => 0.01,
						),
						$value['brightness']
					);

					woodmart_render_backdrop_filter_slider(
						array(
							'id'      => 'contrast',
							'label'   => esc_html__( 'Contrast', 'woodmart' ),
							'min'     => 0,
							'max'     => 300,
							'default' => 100,
							'unit'    => '%',
						),
						$value['contrast']
					);

					woodmart_render_backdrop_filter_slider(
						array(
							'id'      => 'grayscale',
							'label'   => esc_html__( 'Grayscale', 'woodmart' ),
							'min'     => 0,
							'max'     => 100,
							'default' => 0,
							'unit'    => '%',
						),
						$value['grayscale']
					);

					woodmart_render_backdrop_filter_slider(
						array(
							'id'      => 'hue-rotate',
							'label'   => esc_html__( 'Hue Rotate', 'woodmart' ),
							'min'     => 0,
							'max'     => 360,
							'default' => 0,
							'unit'    => 'deg',
						),
						$value['hue-rotate']
					);

					woodmart_render_backdrop_filter_slider(
						array(
							'id'      => 'invert',
							'label'   => esc_html__( 'Invert', 'woodmart' ),
							'min'     => 0,
							'max'     => 100,
							'default' => 0,
							'unit'    => '%',
						),
						$value['invert']
					);

					woodmart_render_backdrop_filter_slider(
						array(
							'id'      => 'opacity',
							'label'   => esc_html__( 'Opacity', 'woodmart' ),
							'min'     => 0,
							'max'     => 100,
							'default' => 100,
							'unit'    => '%',
						),
						$value['opacity']
					);

					woodmart_render_backdrop_filter_slider(
						array(
							'id'      => 'saturate',
							'label'   => esc_html__( 'Saturate', 'woodmart' ),
							'min'     => 0,
							'max'     => 300,
							'default' => 100,
							'unit'    => '%',
						),
						$value['saturate']
					);

					woodmart_render_backdrop_filter_slider(
						array(
							'id'      => 'sepia',
							'label'   => esc_html__( 'Sepia', 'woodmart' ),
							'min'     => 0,
							'max'     => 100,
							'default' => 0,
							'unit'    => '%',
						),
						$value['sepia']
					);
				?>
			</div>
		</div>
		<input type="hidden" class="xts-backdrop-filter-value wpb_vc_param_value" name="<?php echo esc_attr( $settings['param_name'] ); ?>" id="<?php echo esc_attr( $settings['param_name'] ); ?>" value="<?php echo esc_attr( $value_string ); ?>">
		<?php

		return ob_get_clean();
	}
}

if ( ! function_exists( 'woodmart_render_backdrop_filter_preset' ) ) {
	/**
	 * Render preset control.
	 *
	 * @param array $data Preset data.
	 *
	 * @return void
	 */
	function woodmart_render_backdrop_filter_preset( $data ) {
		?>
			<div class="xts-backdrop-preset xts-set-item" data-id="<?php echo esc_attr( $data['id'] ); ?>" data-value="<?php echo esc_attr( $data['value'] ); ?>" title="<?php echo esc_attr( $data['label'] ); ?>">
				<?php echo esc_html( $data['label'] ); ?>
			</div>
		<?php
	}
}

if ( ! function_exists( 'woodmart_render_backdrop_filter_slider' ) ) {
	/**
	 * Render slider control.
	 *
	 * @param array            $data     Slider data.
	 * @param string|int|float $value Slider value.
	 *
	 * @return void
	 */
	function woodmart_render_backdrop_filter_slider( $data, $value ) {
		$slider_id = 'backdrop_filter_' . $data['id'] . '_' . uniqid();
		$has_units = ! empty( $data['unit'] );
		?>
		<div class="xts-dropdown-control">
			<div class="wpb-param-heading">
				<div class="wpb_element_label">
					<?php echo esc_html( $data['label'] ); ?>
				</div>
			</div>
			<div class="edit_form_line">
				<div class="wd-slider-field"></div>

				<span class="xts-range-field-value-input">
					<input type="number" min="<?php echo esc_attr( $data['min'] ); ?>" max="<?php echo esc_attr( $data['max'] ); ?>" step="<?php echo isset( $data['step'] ) ? esc_attr( $data['step'] ) : '1'; ?>" value="<?php echo esc_attr( $value ); ?>" class="wd-slider-value-preview" aria-label="Preview">
				</span>

				<?php if ( $has_units ) : ?>
					<span class="xts-slider-units">
						<span class="wd-slider-unit-control xts-active"><?php echo esc_html( $data['unit'] ); ?></span>
					</span>
				<?php endif; ?>

				<input type="hidden" class="wd-slider-field-value" name="<?php echo esc_attr( $data['id'] ); ?>" id="<?php echo esc_attr( $slider_id ); ?>" value="<?php echo esc_attr( $value ); ?>" min="<?php echo esc_attr( $data['min'] ); ?>" max="<?php echo esc_attr( $data['max'] ); ?>" step="<?php echo isset( $data['step'] ) ? esc_attr( $data['step'] ) : '1'; ?>" data-default-value="<?php echo esc_attr( isset( $data['default'] ) ? $data['default'] : '' ); ?>">
			</div>
		</div>
		<?php
	}
}
