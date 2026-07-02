<?php
/**
 * Single Product Label block attributes.
 *
 * @package woodmart
 */

use XTS\Gutenberg\Block_Attributes;

if ( ! function_exists( 'wd_get_single_product_label_attrs' ) ) {
	/**
	 * Get Single Product Label block attributes.
	 *
	 * @return array[]
	 */
	function wd_get_single_product_label_attrs() {
		$attr = new Block_Attributes();

		$attr->add_attr(
			array(
				'type'      => array(
					'type'    => 'string',
					'default' => 'sale',
				),
				'textAlign' => array(
					'type'       => 'string',
					'responsive' => true,
				),
			)
		);

		wd_get_advanced_tab_attrs( $attr );

		return $attr->get_attr();
	}
}
