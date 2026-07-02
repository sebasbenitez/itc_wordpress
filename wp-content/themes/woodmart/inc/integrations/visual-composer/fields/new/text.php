<?php
/**
 * Woodmart slider responsive param.
 *
 * @package woodmart
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

if ( ! function_exists( 'woodmart_get_text_responsive_param' ) ) {
	/**
	 * Woodmart text param.
	 *
	 * @param array  $settings Settings.
	 * @param string $value    Value.
	 *
	 * @return string
	 */
	function woodmart_get_text_responsive_param( $settings, $value ) {
		$param_name   = $settings['param_name'];
		$devices_maps = array(
			'desktop'         => esc_html__( 'Desktop', 'woodmart' ),
			'tablet'          => esc_html__( 'Tablet', 'woodmart' ),
			'tablet_vertical' => esc_html__( 'Tablet', 'woodmart' ),
			'mobile'          => esc_html__( 'Mobile', 'woodmart' ),
		);

		if ( ! empty( $value ) ) {
			if ( woodmart_is_compressed_data( $value ) ) {
				$data = json_decode( woodmart_decompress( $value ), true );
			} elseif ( ! woodmart_is_compressed_data( $value ) ) {
				$data = array(
					'devices' => array(
						'desktop' => array(
							'value' => $value,
						),
					),
				);
			}
		}

		if ( isset( $data['devices'] ) ) {
			$settings['default'] = $settings['devices'];

			foreach ( $data['devices'] as $device => $device_settings ) {
				if ( ! isset( $settings['devices'][ $device ] ) ) {
					continue;
				}

				$settings['devices'][ $device ] = wp_parse_args( $device_settings, $settings['devices'][ $device ] );
			}
		}

		ob_start();
		?>
		<div class="xts-text-inputs">
			<?php if ( 1 < count( $settings['devices'] ) ) : ?>
				<div class="wd-field-devices">
					<?php foreach ( $settings['devices'] as $device => $device_settings ) : ?>
						<?php
						$device_classes = ' wd-' . $device;

						if ( array_key_first( $settings['devices'] ) === $device ) {
							$device_classes .= ' xts-active';
						}
						?>

						<span class="wd-device<?php echo esc_attr( $device_classes ); ?>" data-value="<?php echo esc_attr( $device ); ?>" title="<?php echo esc_attr( ucfirst( $devices_maps[ $device ] ) ); ?>">
						<span><?php echo esc_html( $devices_maps[ $device ] ); ?></span>
					</span>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php foreach ( $settings['devices'] as $device => $device_settings ) : ?>
				<?php
				$text_classes = '';

				if ( array_key_first( $settings['devices'] ) === $device ) {
					$text_classes = ' xts-active';
				}
				?>

				<div class="xts-text-input<?php echo esc_attr( $text_classes ); ?>" data-device="<?php echo esc_attr( $device ); ?>">
					<input type="text" class="xts-text-value-preview" placeholder="<?php echo isset( $device_settings['placeholder'] ) ? esc_attr( $device_settings['placeholder'] ) : ''; ?>" value="<?php echo isset( $device_settings['value'] ) ? esc_attr( $device_settings['value'] ) : ''; ?>">
				</div>
			<?php endforeach; ?>

			<input type="hidden" class="wpb_vc_param_value" name="<?php echo esc_attr( $param_name ); ?>" id="<?php echo esc_attr( $param_name ); ?>" value="<?php echo esc_attr( $value ); ?>" data-settings="<?php echo esc_attr( wp_json_encode( $settings ) ); ?>">
		</div>
		<?php
		return ob_get_clean();
	}
}
