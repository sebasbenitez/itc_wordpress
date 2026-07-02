<?php
/**
 * Portfolio Archive block render.
 *
 * @package woodmart
 */

if ( ! function_exists( 'wd_gutenberg_portfolio_archive' ) ) {
	/**
	 * Render Portfolio Archive block.
	 *
	 * @param array $block_attributes Block attributes.
	 * @return string
	 */
	function wd_gutenberg_portfolio_archive( $block_attributes ) {
		$block_attributes['is_wpb']          = false;
		$block_attributes['el_id']           = wd_get_gutenberg_element_id( $block_attributes );
		$block_attributes['wrapper_classes'] = wd_get_gutenberg_element_classes( $block_attributes );

		if ( ! empty( $block_attributes['columns'] ) && 'inherit' !== $block_attributes['portfolio_style'] ) {
			$block_attributes['portfolio_column'] = $block_attributes['columns'];
		}

		if ( ! empty( $block_attributes['columnsTablet'] ) && 'inherit' !== $block_attributes['portfolio_style'] ) {
			$block_attributes['portfolio_columns_tablet'] = $block_attributes['columnsTablet'];
		}

		if ( ! empty( $block_attributes['columnsMobile'] ) && 'inherit' !== $block_attributes['portfolio_style'] ) {
			$block_attributes['portfolio_columns_mobile'] = $block_attributes['columnsMobile'];
		}

		if ( isset( $block_attributes['spacing'] ) && 'inherit' !== $block_attributes['portfolio_style'] ) {
			$block_attributes['portfolio_spacing'] = $block_attributes['spacing'];
		}

		if ( isset( $block_attributes['spacingTablet'] ) && 'inherit' !== $block_attributes['portfolio_style'] ) {
			$block_attributes['portfolio_spacing_tablet'] = $block_attributes['spacingTablet'];
		}

		if ( isset( $block_attributes['spacingMobile'] ) && 'inherit' !== $block_attributes['portfolio_style'] ) {
			$block_attributes['portfolio_spacing_mobile'] = $block_attributes['spacingMobile'];
		}

		if ( ! empty( $block_attributes['portfolio_image_size'] ) && 'custom' === $block_attributes['portfolio_image_size'] && ( ! empty( $block_attributes['imgSizeCustomHeight'] ) || ! empty( $block_attributes['imgSizeCustomWidth'] ) ) ) {
			$block_attributes['portfolio_image_size_custom'] = array(
				'width'  => $block_attributes['imgSizeCustomWidth'],
				'height' => $block_attributes['imgSizeCustomHeight'],
			);
			woodmart_set_loop_prop( 'portfolio_image_size_custom', $block_attributes['portfolio_image_size_custom'] );
		}

		return woodmart_shortcode_portfolio_archive_loop( $block_attributes );
	}
}
