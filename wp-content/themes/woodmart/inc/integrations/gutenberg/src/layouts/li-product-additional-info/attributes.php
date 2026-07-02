<?php
/**
 * Loop Product Additional Info block attributes.
 *
 * @package woodmart
 */

use XTS\Gutenberg\Block_Attributes;

if ( ! function_exists( 'wd_get_loop_builder_product_additional_info_attrs' ) ) {
	/**
	 * Get Loop Product Additional Info block attributes.
	 *
	 * @return array[]
	 */
	function wd_get_loop_builder_product_additional_info_attrs() {
		$attr = new Block_Attributes();

		$attr->add_attr(
			array(
				'showTitle'            => array(
					'type' => 'boolean',
				),
				'source'               => array(
					'type'    => 'string',
					'default' => 'all',
				),
				'include'              => array(
					'type' => 'string',
				),
				'exclude'              => array(
					'type' => 'string',
				),
				'layout'               => array(
					'type'    => 'string',
					'default' => 'list',
				),
				'style'                => array(
					'type'    => 'string',
					'default' => 'bordered',
				),
				'columns'              => array(
					'type'       => 'number',
					'responsive' => true,
				),
				'columnGap'            => array(
					'type'       => 'number',
					'responsive' => true,
				),
				'horizontalGap'        => array(
					'type'       => 'number',
					'responsive' => true,
				),
				'attrImage'            => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'imageWidth'           => array(
					'type'       => 'number',
					'responsive' => true,
				),
				'attrName'             => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'attrNameColumnWidth'  => array(
					'type'       => 'string',
					'responsive' => true,
					'units'      => 'px',
				),
				'termImage'            => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'termImageWidth'       => array(
					'type'       => 'number',
					'responsive' => true,
				),
				'termLabel'            => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'itemsBorderWidthLock' => array(
					'type'    => 'boolean',
					'default' => true,
				),
			)
		);

		$attr->add_attr( wd_get_color_control_attrs( 'attrNameColor' ) );
		$attr->add_attr( wd_get_typography_control_attrs(), 'attrNameTp' );

		$attr->add_attr( wd_get_color_control_attrs( 'attrTermColor' ) );
		$attr->add_attr( wd_get_typography_control_attrs(), 'attrTermTp' );

		$attr->add_attr( wd_get_color_control_attrs( 'termLinkColor' ) );
		$attr->add_attr( wd_get_color_control_attrs( 'termLinkColorHover' ) );

		wd_get_border_control_attrs( $attr, 'itemsBorder' );

		wd_get_advanced_tab_attrs( $attr );

		return $attr->get_attr();
	}
}
