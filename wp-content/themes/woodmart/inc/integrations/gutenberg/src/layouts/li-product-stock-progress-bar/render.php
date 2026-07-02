<?php
/**
 * Loop Product Stock Progress Bar block render.
 *
 * @package woodmart
 */

use XTS\Modules\Layouts\Loop_Item;

if ( ! function_exists( 'wd_gutenberg_loop_builder_product_stock_progress_bar' ) ) {
	/**
	 * Render Loop Product Stock Progress Bar block.
	 *
	 * @param array $block_attributes Block attributes.
	 * @return false|string
	 */
	function wd_gutenberg_loop_builder_product_stock_progress_bar( $block_attributes ) {
		if ( ! woodmart_woocommerce_installed() ) {
			return '';
		}

		Loop_Item::setup_postdata();

		global $product;

		if ( ! $product ) {
			Loop_Item::reset_postdata();
			return '';
		}

		ob_start();

		woodmart_stock_progress_bar( ' wd-loop-prod-stock-progress-bar' . wd_get_gutenberg_element_classes( $block_attributes ) );

		Loop_Item::reset_postdata();

		return ob_get_clean();
	}
}
