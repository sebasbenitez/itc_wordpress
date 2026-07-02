<?php
/**
 * Loop Product Stock Status block attributes.
 *
 * @package woodmart
 */

use XTS\Gutenberg\Block_Attributes;

if ( ! function_exists( 'wd_get_loop_builder_product_stock_status_attrs' ) ) {
	/**
	 * Get Loop Product Stock Status block attributes.
	 *
	 * @return array[]
	 */
	function wd_get_loop_builder_product_stock_status_attrs() {
		$attr = new Block_Attributes();

		$attr->add_attr(
			array(
				'textAlign' => array(
					'type'       => 'string',
					'responsive' => true,
				),
			)
		);

		$attr->add_attr( wd_get_typography_control_attrs(), 'tp' );

		wd_get_advanced_tab_attrs( $attr );

		return $attr->get_attr();
	}
}
