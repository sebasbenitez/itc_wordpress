<?php
/**
 * Loop Product SKU block attributes.
 *
 * @package woodmart
 */

use XTS\Gutenberg\Block_Attributes;

if ( ! function_exists( 'wd_get_loop_builder_product_sku_attrs' ) ) {
	/**
	 * Get Loop Product SKU block attributes.
	 *
	 * @return array[]
	 */
	function wd_get_loop_builder_product_sku_attrs() {
		$attr = new Block_Attributes();

		$attr->add_attr(
			array(
				'showTitle' => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'layout'    => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'textAlign' => array(
					'type'       => 'string',
					'responsive' => true,
				),
			)
		);

		$attr->add_attr( wd_get_color_control_attrs( 'valueColor' ) );
		$attr->add_attr( wd_get_typography_control_attrs(), 'valueTp' );

		wd_get_advanced_tab_attrs( $attr );

		return $attr->get_attr();
	}
}
