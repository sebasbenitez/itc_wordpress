<?php
/**
 * Loop Product Variations block attributes.
 *
 * @package woodmart
 */

use XTS\Gutenberg\Block_Attributes;

if ( ! function_exists( 'wd_get_loop_builder_product_variations_attrs' ) ) {
	/**
	 * Get Loop Product Variations block attributes.
	 *
	 * @return array[]
	 */
	function wd_get_loop_builder_product_variations_attrs() {
		$attr = new Block_Attributes();

		$attr->add_attr(
			array(
				'align' => array(
					'type'       => 'string',
					'responsive' => true,
				),
			)
		);

		wd_get_advanced_tab_attrs( $attr );

		return $attr->get_attr();
	}
}
