<?php
/**
 * Loop Product Stock Progress Bar block attributes.
 *
 * @package woodmart
 */

use XTS\Gutenberg\Block_Attributes;

if ( ! function_exists( 'wd_get_loop_builder_product_stock_progress_bar_attrs' ) ) {
	/**
	 * Get Loop Product Stock Progress Bar block attributes.
	 *
	 * @return array[]
	 */
	function wd_get_loop_builder_product_stock_progress_bar_attrs() {
		$attr = new Block_Attributes();

		wd_get_advanced_tab_attrs( $attr );

		return $attr->get_attr();
	}
}
