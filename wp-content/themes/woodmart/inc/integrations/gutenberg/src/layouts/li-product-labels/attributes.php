<?php
/**
 * Loop Product Labels block attributes.
 *
 * @package woodmart
 */

use XTS\Gutenberg\Block_Attributes;

if ( ! function_exists( 'wd_get_loop_builder_product_labels_attrs' ) ) {
	/**
	 * Get Loop Product Labels block attributes.
	 *
	 * @return array[]
	 */
	function wd_get_loop_builder_product_labels_attrs() {
		$attr = new Block_Attributes();

		$attr->add_attr(
			array(
				'textAlign'           => array(
					'type' => 'string',
				),
				'textAlignTablet'     => array(
					'type' => 'string',
				),
				'htextAlignMobile'    => array(
					'type' => 'string',
				),
				'verticalAlign'       => array(
					'type' => 'string',
				),
				'verticalAlignTablet' => array(
					'type' => 'string',
				),
				'verticalAlignMobile' => array(
					'type' => 'string',
				),
				'orientation'         => array(
					'type'    => 'string',
					'default' => 'vertical',
				),
				'source'              => array(
					'type'    => 'string',
					'default' => 'all',
				),
				'include'             => array(
					'type'    => 'string',
					'default' => '',
				),
				'exclude'             => array(
					'type'    => 'string',
					'default' => '',
				),
				'labelsGap'           => array(
					'type' => 'number',
				),
				'labelsGapTablet'     => array(
					'type' => 'number',
				),
				'labelsGapMobile'     => array(
					'type' => 'number',
				),
			)
		);

		wd_get_advanced_tab_attrs( $attr );

		return $attr->get_attr();
	}
}
