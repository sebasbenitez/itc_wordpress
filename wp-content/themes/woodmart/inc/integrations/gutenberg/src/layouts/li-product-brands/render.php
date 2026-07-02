<?php
/**
 * Loop Product Brands block render.
 *
 * @package woodmart
 */

use XTS\Modules\Layouts\Loop_Item;

if ( ! function_exists( 'wd_gutenberg_loop_builder_product_brands' ) ) {
	/**
	 * Render Loop Product Brands block.
	 *
	 * @param array  $block_attributes Block attributes.
	 * @param string $content Inner block content.
	 * @return false|string
	 */
	function wd_gutenberg_loop_builder_product_brands( $block_attributes, $content ) {
		if ( ! woodmart_woocommerce_installed() ) {
			return '';
		}

		$classes  = ' wd-loop-prod-meta';
		$classes .= wd_get_gutenberg_element_classes( $block_attributes );

		if ( ! empty( $block_attributes['showTitle'] ) ) {
			$classes .= ' wd-layout-' . $block_attributes['layout'];
		}

		if ( ( empty( $block_attributes['showTitle'] ) || 'justify' !== $block_attributes['layout'] ) && ( ! empty( $block_attributes['textAlign'] ) || ! empty( $block_attributes['textAlignTablet'] ) || ! empty( $block_attributes['textAlignMobile'] ) ) ) {
			$classes .= ' wd-align';
		}

		Loop_Item::setup_postdata();

		ob_start();

		woodmart_product_brands_links( $classes, true, $content );

		Loop_Item::reset_postdata();

		return ob_get_clean();
	}
}
