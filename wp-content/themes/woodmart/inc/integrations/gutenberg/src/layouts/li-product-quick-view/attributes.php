<?php
/**
 * Loop Product price block assets.
 *
 * @package woodmart
 */

use XTS\Gutenberg\Block_Attributes;

if ( ! function_exists( 'wd_get_loop_builder_product_quick_view_attrs' ) ) {
	/**
	 * Get Loop Product Quick View block attributes.
	 *
	 * @return array[]
	 */
	function wd_get_loop_builder_product_quick_view_attrs() {
		$attr = new Block_Attributes();

		$attr->add_attr(
			array(
				'style'                 => array(
					'type'    => 'string',
					'default' => 'icon',
				),
				'align'                 => array(
					'type'       => 'string',
					'responsive' => true,
				),
				'stretch'               => array(
					'type' => 'boolean',
				),
				'iconSize'              => array(
					'type'       => 'number',
					'responsive' => true,
				),
				'tooltip_position'      => array(
					'type'    => 'string',
					'default' => 'top',
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
				'btnBorderRadius'       => array(
					'type'       => 'string',
					'responsive' => true,
					'units'      => 'px',
				),
			)
		);

		$attr->add_attr( wd_get_typography_control_attrs(), 'textTp' );

		$attr->add_attr( wd_get_color_control_attrs( 'textColor' ) );
		$attr->add_attr( wd_get_color_control_attrs( 'iconColor' ) );

		$attr->add_attr( wd_get_color_control_attrs( 'textColorHover' ) );
		$attr->add_attr( wd_get_color_control_attrs( 'iconColorHover' ) );

		wd_get_padding_control_attrs( $attr, 'linkPadding' );

		wd_get_advanced_tab_attrs( $attr );

		return $attr->get_attr();
	}
}
