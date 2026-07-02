<?php
/**
 * Single Product Label block render.
 *
 * @package woodmart
 */

if ( ! function_exists( 'wd_gutenberg_single_product_label' ) ) {
	/**
	 * Render Single Product Label block.
	 *
	 * @param array $block_attributes Block attributes.
	 * @return false|string
	 */
	function wd_gutenberg_single_product_label( $block_attributes ) {
		$block_attributes['is_wpb']          = false;
		$block_attributes['label_id']        = $block_attributes['type'];
		$block_attributes['wrapper_classes'] = wd_get_gutenberg_element_classes( $block_attributes );

		return woodmart_shortcode_single_product_label( $block_attributes );
	}
}
