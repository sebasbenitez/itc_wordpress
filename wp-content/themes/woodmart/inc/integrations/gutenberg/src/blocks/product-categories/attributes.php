<?php
/**
 * Gutenberg Product Categories Block Attributes.
 *
 * @package woodmart
 */

use XTS\Gutenberg\Block_Attributes;

if ( ! function_exists( 'wd_get_block_product_categories_attrs' ) ) {
	/**
	 * Get Product Categories Block Attributes.
	 *
	 * @return array
	 */
	function wd_get_block_product_categories_attrs() {
		$attr = new Block_Attributes();

		$attr->add_attr(
			array(
				'data_source'                              => array(
					'type'    => 'string',
					'default' => 'custom_query',
				),
				'type'                                     => array(
					'type'    => 'string',
					'default' => 'grid',
				),
				'images'                                   => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'product_count'                            => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'mobile_accordion'                         => array(
					'type'    => 'string',
					'default' => 'no',
				),
				'shop_categories_ancestors'                => array(
					'type' => 'boolean',
				),
				'show_categories_neighbors'                => array(
					'type' => 'boolean',
				),
				'mobile_categories_menu_layout'            => array(
					'type'    => 'string',
					'default' => 'dropdown',
				),
				'mobile_categories_drilldown_animation'    => array(
					'type'    => 'string',
					'default' => 'slide',
				),
				'mobile_categories_submenu_opening_action' => array(
					'type'    => 'string',
					'default' => 'only_arrow',
				),
				'mobile_categories_position'               => array(
					'type'    => 'string',
					'default' => 'left',
				),
				'mobile_categories_color_scheme'           => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'mobile_categories_close_btn'              => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'number'                                   => array(
					'type' => 'string',
				),
				'orderby'                                  => array(
					'type' => 'string',
				),
				'order'                                    => array(
					'type' => 'string',
				),
				'ids'                                      => array(
					'type' => 'string',
				),
				'hide_empty'                               => array(
					'type'    => 'boolean',
					'default' => true,
				),

				'categories_design'                        => array(
					'type'    => 'string',
					'default' => '',
				),
				'image_container_width'                    => array(
					'type'       => 'string',
					'responsive' => true,
					'units'      => 'px',
				),
				'color_scheme'                             => array(
					'type' => 'string',
				),
				'categories_with_shadow'                   => array(
					'type'    => 'string',
					'default' => '',
				),
				'navAlignment'                             => array(
					'type'       => 'string',
					'responsive' => true,
				),
				'title_idle_color'                         => array(
					'type' => 'string',
				),
				'title_hover_color'                        => array(
					'type' => 'string',
				),
				'custom_rounding_size'                     => array(
					'type'  => 'string',
					'units' => 'px',
				),

				'style'                                    => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'grid_different_sizes'                     => array(
					'type' => 'string',
				),
				'masonry_grid'                             => array(
					'type' => 'boolean',
				),
				'columns'                                  => array(
					'type'       => 'number',
					'default'    => 4,
					'responsive' => true,
				),
				'spacing'                                  => array(
					'type'       => 'string',
					'default'    => '20',
					'responsive' => true,
				),
				'img_size'                                 => array(
					'type'    => 'string',
					'default' => 'woocommerce_thumbnail',
				),
				'imgSizeCustomWidth'                       => array(
					'type' => 'string',
				),
				'imgSizeCustomHeight'                      => array(
					'type' => 'string',
				),
				'categories_bordered_grid'                 => array(
					'type' => 'boolean',
				),
				'categories_bordered_grid_style'           => array(
					'type'    => 'string',
					'default' => 'outside',
				),
				'categories_with_background'               => array(
					'type' => 'boolean',
				),
				'subcategories'                            => array(
					'type' => 'string',
				),
				'grid_product_count'                       => array(
					'type' => 'string',
				),
				'icon_alignment'                           => array(
					'type' => 'string',
				),
				'iconWidth'                                => array(
					'type'       => 'string',
					'responsive' => true,
				),
				'iconHeight'                               => array(
					'type'       => 'string',
					'responsive' => true,
				),
				'navStyle'                                 => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'navItemsGap'                              => array(
					'type'    => 'string',
					'default' => 's',
				),
				'navCustomItemsGap'                        => array(
					'type'       => 'number',
					'responsive' => true,
				),
				'navItemsBorderWidthLock'                  => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'navItemsBorderHoverWidthLock'             => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'navItemsBorderActiveWidthLock'            => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'navDisableActiveStyle'                    => array(
					'type' => 'boolean',
				),
			)
		);

		$attr->add_attr( wd_get_color_control_attrs( 'categoriesBorderColor' ) );
		$attr->add_attr( wd_get_color_control_attrs( 'categoriesBackground' ) );
		$attr->add_attr( wd_get_typography_control_attrs(), 'title' );

		$attr->add_attr( wd_get_typography_control_attrs(), 'navItemTp' );

		$attr->add_attr( wd_get_color_control_attrs( 'titleIdleColor' ) );
		$attr->add_attr( wd_get_color_control_attrs( 'titleHoverColor' ) );
		$attr->add_attr( wd_get_color_control_attrs( 'titleActiveColor' ) );

		$attr->add_attr( wd_get_color_control_attrs( 'countIdleColor' ) );
		$attr->add_attr( wd_get_color_control_attrs( 'countHoverColor' ) );
		$attr->add_attr( wd_get_color_control_attrs( 'countActiveColor' ) );

		$attr->add_attr( wd_get_color_control_attrs( 'navItemsBgColor' ) );
		$attr->add_attr( wd_get_color_control_attrs( 'navItemsBgHoverColor' ) );
		$attr->add_attr( wd_get_color_control_attrs( 'navItemsBgActiveColor' ) );

		wd_get_box_shadow_control_attrs( $attr, 'navItemsBoxShadow' );
		wd_get_box_shadow_control_attrs( $attr, 'navItemsBoxShadowHover' );
		wd_get_box_shadow_control_attrs( $attr, 'navItemsBoxShadowActive' );

		wd_get_border_control_attrs( $attr, 'navItemsBorder' );
		wd_get_border_control_attrs( $attr, 'navItemsBorderHover' );
		wd_get_border_control_attrs( $attr, 'navItemsBorderActive' );

		wd_get_padding_control_attrs( $attr, 'navItemsPadding' );

		wd_get_carousel_settings_attrs( $attr );
		wd_get_advanced_tab_attrs( $attr );

		return $attr->get_attr();
	}
}
