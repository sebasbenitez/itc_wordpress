<?php
/**
 * Loop Product Title block attributes.
 *
 * @package woodmart
 */

use XTS\Gutenberg\Block_Attributes;

if ( ! function_exists( 'wd_get_loop_builder_product_title_attrs' ) ) {
	/**
	 * Get Loop Product Title block attributes.
	 *
	 * @return array[]
	 */
	function wd_get_loop_builder_product_title_attrs() {
		$attr = new Block_Attributes();

		$attr->add_attr(
			array(
				'textAlign'  => array(
					'type'       => 'string',
					'responsive' => true,
				),
				'htmlTag'    => array(
					'type'    => 'string',
					'default' => 'h3',
				),
				'linesLimit' => array(
					'type' => 'boolean',
				),
				'linesClamp' => array(
					'type'       => 'number',
					'default'    => 1,
					'responsive' => true,
				),
			)
		);

		$attr->add_attr( wd_get_color_control_attrs( 'color' ) );
		$attr->add_attr( wd_get_color_control_attrs( 'colorHover' ) );
		$attr->add_attr( wd_get_color_control_attrs( 'colorParentHover' ) );
		$attr->add_attr( wd_get_typography_control_attrs(), 'tp' );

		wd_get_advanced_tab_attrs( $attr );

		return $attr->get_attr();
	}
}
