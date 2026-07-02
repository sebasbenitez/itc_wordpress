<?php
/**
 * Loop Product Thumbnail block assets.
 *
 * @package woodmart
 */

use XTS\Gutenberg\Block_Attributes;

if ( ! function_exists( 'wd_get_loop_builder_product_thumbnail_attrs' ) ) {
	/**
	 * Get Loop Product Thumbnail block attributes.
	 *
	 * @return array[]
	 */
	function wd_get_loop_builder_product_thumbnail_attrs() {
		$attr = new Block_Attributes();

		$attr->add_attr(
			array(
				'image_on_hover'             => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'hover_image_effect'         => array(
					'type'    => 'string',
					'default' => 'zoom',
				),
				'product_gallery'            => array(
					'type' => 'boolean',
				),
				'grid_gallery_control'       => array(
					'type'    => 'string',
					'default' => 'arrows',
				),
				'grid_gallery_enable_arrows' => array(
					'type'    => 'string',
					'default' => 'none',
				),
			)
		);

		wd_get_advanced_tab_attrs( $attr );

		return $attr->get_attr();
	}
}
