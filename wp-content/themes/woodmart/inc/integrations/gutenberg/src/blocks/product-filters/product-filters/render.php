<?php
/**
 * Gutenberg Product Filters block render.
 *
 * @package woodmart
 */

if ( ! function_exists( 'wd_gutenberg_product_filters' ) ) {
	/**
	 * Gutenberg Product Filters block render function.
	 *
	 * @param array  $block_attributes Block attributes.
	 * @param string $content Block content.
	 * @return string
	 */
	function wd_gutenberg_product_filters( $block_attributes, $content ) {
		if ( ! woodmart_woocommerce_installed() ) {
			return '';
		}

		woodmart_replace_boolean_to_yes_no( array( 'show_selected_values' ), $block_attributes );

		$block_attributes['display_grid_col_desktop'] = $block_attributes['display_grid_col'];
		$block_attributes['display_grid_col_tablet']  = $block_attributes['display_grid_colTablet'];
		$block_attributes['display_grid_col_mobile']  = $block_attributes['display_grid_colMobile'];

		$block_attributes['space_between_tablet'] = isset( $block_attributes['space_betweenTablet'] ) ? $block_attributes['space_betweenTablet'] : '';
		$block_attributes['space_between_mobile'] = isset( $block_attributes['space_betweenMobile'] ) ? $block_attributes['space_betweenMobile'] : '';

		$block_attributes['el_class'] = wd_get_gutenberg_element_classes( $block_attributes );
		$block_attributes['el_id']    = wd_get_gutenberg_element_id( $block_attributes );
		$block_attributes['is_wpb']   = false;

		return woodmart_product_filters_shortcode( $block_attributes, $content );
	}
}
