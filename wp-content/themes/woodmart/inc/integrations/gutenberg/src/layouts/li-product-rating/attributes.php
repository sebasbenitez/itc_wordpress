<?php
/**
 * Loop Product Rating block attributes.
 *
 * @package woodmart
 */

use XTS\Gutenberg\Block_Attributes;

if ( ! function_exists( 'wd_get_loop_builder_product_rating_attrs' ) ) {
	/**
	 * Get Loop Product Rating block attributes.
	 *
	 * @return array[]
	 */
	function wd_get_loop_builder_product_rating_attrs() {
		$attr = new Block_Attributes();

		$attr->add_attr(
			array(
				'design'            => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'show_count'        => array(
					'type' => 'boolean',
				),
				'show_empty_rating' => array(
					'type' => 'boolean',
				),
				'textAlign'         => array(
					'type'       => 'string',
					'responsive' => true,
				),
				'size'              => array(
					'type'       => 'string',
					'responsive' => true,
				),
			)
		);

		$attr->add_attr( wd_get_color_control_attrs( 'textColor' ) );
		$attr->add_attr( wd_get_typography_control_attrs(), 'textTp' );

		$attr->add_attr( wd_get_color_control_attrs( 'emptyColor' ) );
		$attr->add_attr( wd_get_color_control_attrs( 'filledColor' ) );

		$attr->add_attr( wd_get_color_control_attrs( 'countColor' ) );
		$attr->add_attr( wd_get_color_control_attrs( 'countColorHover' ) );
		$attr->add_attr( wd_get_typography_control_attrs(), 'countTp' );

		wd_get_advanced_tab_attrs( $attr );

		return $attr->get_attr();
	}
}
