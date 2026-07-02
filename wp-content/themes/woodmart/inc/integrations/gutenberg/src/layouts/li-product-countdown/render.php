<?php
/**
 * Loop Product Countdown block render.
 *
 * @package woodmart
 */

use XTS\Modules\Layouts\Loop_Item;

if ( ! function_exists( 'wd_gutenberg_loop_builder_product_countdown' ) ) {
	/**
	 * Render Loop Product Countdown block.
	 *
	 * @param array  $block_attributes Block attributes.
	 * @param string $content Inner block content.
	 * @return false|string
	 */
	function wd_gutenberg_loop_builder_product_countdown( $block_attributes, $content ) {
		if ( ! woodmart_woocommerce_installed() ) {
			return '';
		}

		$block_attributes['wrapper_classes']  = ' wd-loop-prod-countdown';
		$block_attributes['wrapper_classes'] .= wd_get_gutenberg_element_classes( $block_attributes );

		if ( ! empty( $block_attributes['textAlign'] ) || ! empty( $block_attributes['textAlignTablet'] ) || ! empty( $block_attributes['textAlignMobile'] ) ) {
			$block_attributes['wrapper_classes'] .= ' wd-align';
		}

		Loop_Item::setup_postdata();

		global $product;

		if ( ! $product ) {
			Loop_Item::reset_postdata();
			return '';
		}

		ob_start();

		woodmart_product_sale_countdown( $block_attributes, $content );

		Loop_Item::reset_postdata();

		return ob_get_clean();
	}
}
