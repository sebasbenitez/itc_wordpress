<?php
/**
 * Single Product Labels block render.
 *
 * @package woodmart
 */

if ( ! function_exists( 'wd_gutenberg_single_product_labels' ) ) {
	/**
	 * Render Single Product Labels block.
	 *
	 * @param array $block_attributes Block attributes.
	 * @return string
	 */
	function wd_gutenberg_single_product_labels( $block_attributes ) {
		$block_attributes['is_wpb']          = false;
		$block_attributes['wrapper_classes'] = wd_get_gutenberg_element_classes( $block_attributes );

		return woodmart_shortcode_single_product_labels( $block_attributes );
	}
}
