<?php
/**
 * Single Product Block Price Tracker button attributes.
 *
 * @package woodmart
 */

use XTS\Gutenberg\Block_Attributes;

if ( ! function_exists( 'wd_get_single_product_block_price_tracker_btn_attrs' ) ) {
	/**
	 * Get Single Product Block Price Tracker button attributes.
	 *
	 * @return array[]
	 */
	function wd_get_single_product_block_price_tracker_btn_attrs() {
		$attr = new Block_Attributes();

		$attr->add_attr(
			array(
				'style'                 => array(
					'type'    => 'string',
					'default' => 'text',
				),
				'iconSize'              => array(
					'type'       => 'number',
					'responsive' => true,
				),
				'linkPaddingLock'       => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'linkPaddingLockTablet' => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'linkPaddingLockMobile' => array(
					'type'    => 'boolean',
					'default' => true,
				),
			)
		);

		$attr->add_attr( wd_get_typography_control_attrs(), 'textTp' );

		$attr->add_attr( wd_get_color_control_attrs( 'textColor' ) );
		$attr->add_attr( wd_get_color_control_attrs( 'iconColor' ) );

		$attr->add_attr( wd_get_color_control_attrs( 'textColorHover' ) );
		$attr->add_attr( wd_get_color_control_attrs( 'iconColorHover' ) );

		wd_get_advanced_tab_attrs( $attr );

		return $attr->get_attr();
	}
}
