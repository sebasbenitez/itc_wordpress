<?php
/**
 * Categories element map.
 *
 * @package woodmart
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

if ( ! function_exists( 'woodmart_get_vc_shortcode_categories' ) ) {
	/**
	 * Get Visual Composer map configuration for Categories element.
	 *
	 * @return array
	 */
	function woodmart_get_vc_shortcode_categories() {
		$order_by_values = array(
			'',
			esc_html__( 'Date', 'woodmart' )       => 'date',
			esc_html__( 'ID', 'woodmart' )         => 'ID',
			esc_html__( 'Title', 'woodmart' )      => 'title',
			esc_html__( 'Modified', 'woodmart' )   => 'modified',
			esc_html__( 'Menu order', 'woodmart' ) => 'menu_order',
			esc_html__( 'As IDs or slugs provided order', 'woodmart' ) => 'include',
		);

		$order_way_values = array(
			esc_html__( 'Inherit', 'woodmart' )    => '',
			esc_html__( 'Descending', 'woodmart' ) => 'DESC',
			esc_html__( 'Ascending', 'woodmart' )  => 'ASC',
		);

		$title_typography = woodmart_get_typography_map(
			array(
				'title'      => esc_html__( 'Title typography', 'woodmart' ),
				'key'        => 'title_typography',
				'selector'   => '{{WRAPPER}} div.product-category .wd-entities-title',
				'group'      => esc_html__( 'Style', 'woodmart' ),
				'dependency' => array(
					'element' => 'type',
					'value'   => array( 'grid' ),
				),
			)
		);

		$nav_items_typography = woodmart_get_typography_map(
			array(
				'title'      => esc_html__( 'Typography', 'woodmart' ),
				'key'        => 'nav_item_tp',
				'selector'   => '.wd {{WRAPPER}} .wd-nav-product-cat > li > a',
				'group'      => esc_html__( 'Style', 'woodmart' ),
				'dependency' => array(
					'element' => 'type',
					'value'   => array( 'navigation' ),
				),
			)
		);

		return array(
			'name'        => esc_html__( 'Product categories', 'woodmart' ),
			'base'        => 'woodmart_categories',
			'category'    => woodmart_get_tab_title_category_for_wpb( esc_html__( 'Theme elements', 'woodmart' ) ),
			'description' => esc_html__( 'Product categories grid', 'woodmart' ),
			'icon'        => WOODMART_ASSETS . '/images/vc-icon/product-categories.svg',
			'params'      => array(
				/**
				 * Content - General
				 */
				array(
					'group'      => esc_html__( 'Content', 'woodmart' ),
					'type'       => 'woodmart_css_id',
					'param_name' => 'woodmart_css_id',
				),

				array(
					'title'      => esc_html__( 'General', 'woodmart' ),
					'group'      => esc_html__( 'Content', 'woodmart' ),
					'type'       => 'woodmart_title_divider',
					'param_name' => 'data_divider',
				),

				array(
					'heading'          => esc_html__( 'Type', 'woodmart' ),
					'group'            => esc_html__( 'Content', 'woodmart' ),
					'type'             => 'dropdown',
					'param_name'       => 'type',
					'value'            => array(
						esc_html__( 'Navigation', 'woodmart' ) => 'navigation',
						esc_html__( 'Grid', 'woodmart' ) => 'grid',
					),
					'std'              => 'grid',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'heading'          => esc_html__( 'Data source', 'woodmart' ),
					'group'            => esc_html__( 'Content', 'woodmart' ),
					'hint'             => esc_html__( 'Use WooCommerce query when you display this element as a part of the shop page in WoodMart Layouts builder.', 'woodmart' ),
					'type'             => 'dropdown',
					'param_name'       => 'data_source',
					'value'            => array(
						esc_html__( 'Custom query', 'woodmart' ) => 'custom_query',
						esc_html__( 'WooCommerce query', 'woodmart' ) => 'wc_query',
					),
					'std'              => 'custom_query',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'heading'          => esc_html__( 'Order by', 'woodmart' ),
					'group'            => esc_html__( 'Content', 'woodmart' ),
					'type'             => 'dropdown',
					'param_name'       => 'orderby',
					'value'            => $order_by_values,
					'save_always'      => true,
					'hint'             => sprintf(
						wp_kses(
							// translators: %s: WordPress codex page.
							__( 'Select how to sort retrieved categories. More at %s.', 'woodmart' ),
							array(
								'a' => array(
									'href'   => array(),
									'target' => array(),
								),
							)
						),
						'<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>'
					),
					'dependency'       => array(
						'element' => 'data_source',
						'value'   => array( 'custom_query' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'heading'          => esc_html__( 'Sort order', 'woodmart' ),
					'group'            => esc_html__( 'Content', 'woodmart' ),
					'type'             => 'woodmart_button_set',
					'param_name'       => 'order',
					'value'            => $order_way_values,
					'save_always'      => true,
					'hint'             => sprintf(
						wp_kses(
							// translators: %s: WordPress codex page.
							__( 'Designates the ascending or descending order. More at %s.', 'woodmart' ),
							array(
								'a' => array(
									'href'   => array(),
									'target' => array(),
								),
							)
						),
						'<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>'
					),
					'dependency'       => array(
						'element' => 'data_source',
						'value'   => array( 'custom_query' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'heading'          => esc_html__( 'Categories', 'woodmart' ),
					'group'            => esc_html__( 'Content', 'woodmart' ),
					'type'             => 'autocomplete',
					'param_name'       => 'ids',
					'settings'         => array(
						'multiple' => true,
						'sortable' => true,
					),
					'save_always'      => true,
					'hint'             => esc_html__( 'List of product categories', 'woodmart' ),
					'dependency'       => array(
						'element' => 'data_source',
						'value'   => array( 'custom_query' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'heading'          => esc_html__( 'Show current category ancestors', 'woodmart' ),
					'group'            => esc_html__( 'Content', 'woodmart' ),
					'descriptions'     => esc_html__( 'This option works with WooCommerce query Data source only. They are dedicated to the shop page layout.', 'woodmart' ),
					'type'             => 'woodmart_switch',
					'param_name'       => 'shop_categories_ancestors',
					'hint'             => esc_html__( 'If you visit category Man, for example, only man\'s subcategories will be shown in the page title like T-shirts, Coats, Shoes etc.', 'woodmart' ),
					'true_state'       => 'yes',
					'false_state'      => 'no',
					'default'          => 'no',
					'dependency'       => array(
						'element' => 'type',
						'value'   => array( 'navigation' ),
					),
					'wd_dependency'    => array(
						'element' => 'data_source',
						'value'   => array( 'wc_query' ),
					),
					'edit_field_class' => 'vc_col-sm-12 vc_column',
				),

				array(
					'heading'          => esc_html__( 'Show category neighbors if there is no children', 'woodmart' ),
					'group'            => esc_html__( 'Content', 'woodmart' ),
					'descriptions'     => esc_html__( 'This option works with WooCommerce query Data source only. They are dedicated to the shop page layout.', 'woodmart' ),
					'type'             => 'woodmart_switch',
					'param_name'       => 'show_categories_neighbors',
					'hint'             => esc_html__( 'If the category you visit doesn\'t contain any subcategories, the page title menu will display this category\'s neighbors categories.', 'woodmart' ),
					'true_state'       => 'yes',
					'false_state'      => 'no',
					'default'          => 'no',
					'dependency'       => array(
						'element' => 'type',
						'value'   => array( 'navigation' ),
					),
					'wd_dependency'    => array(
						'element' => 'data_source',
						'value'   => array( 'wc_query' ),
					),
					'edit_field_class' => 'vc_col-sm-12 vc_column',
				),

				array(
					'heading'          => esc_html__( 'Hide empty', 'woodmart' ),
					'group'            => esc_html__( 'Content', 'woodmart' ),
					'type'             => 'woodmart_switch',
					'param_name'       => 'hide_empty',
					'hint'             => esc_html__( 'Don\'t display categories that don\'t have any products assigned.', 'woodmart' ),
					'true_state'       => 'yes',
					'false_state'      => 'no',
					'default'          => 'yes',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				/**
				 * Content - Layout
				 */
				array(
					'title'      => esc_html__( 'Layout', 'woodmart' ),
					'group'      => esc_html__( 'Content', 'woodmart' ),
					'type'       => 'woodmart_title_divider',
					'param_name' => 'layout_divider',
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'grid' ),
					),
				),

				array(
					'heading'          => esc_html__( 'Layout', 'woodmart' ),
					'group'            => esc_html__( 'Content', 'woodmart' ),
					'type'             => 'dropdown',
					'param_name'       => 'style',
					'save_always'      => true,
					'hint'             => esc_html__( 'Try out our creative styles for categories block', 'woodmart' ),
					'value'            => array(
						esc_html__( 'Grid', 'woodmart' ) => 'default',
						esc_html__( 'Masonry', 'woodmart' ) => 'masonry',
						esc_html__( 'Masonry (with first wide)', 'woodmart' ) => 'masonry-first',
						esc_html__( 'Carousel', 'woodmart' ) => 'carousel',
					),
					'dependency'       => array(
						'element' => 'type',
						'value'   => array( 'grid' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'heading'          => esc_html__( 'Columns', 'woodmart' ),
					'hint'             => esc_html__( 'Number of columns in the grid.', 'woodmart' ),
					'group'            => esc_html__( 'Content', 'woodmart' ),
					'type'             => 'wd_slider',
					'param_name'       => 'columns',
					'devices'          => array(
						'desktop' => array(
							'unit'  => '-',
							'value' => 4,
						),
						'tablet'  => array(
							'unit'  => '-',
							'value' => '',
						),
						'mobile'  => array(
							'unit'  => '-',
							'value' => '',
						),
					),
					'range'            => array(
						'-' => array(
							'min'  => 1,
							'max'  => 12,
							'step' => 1,
						),
					),
					'selectors'        => array(),
					'dependency'       => array(
						'element' => 'style',
						'value'   => array( 'masonry', 'default' ),
					),
					'wd_dependency'    => array(
						'element' => 'type',
						'value'   => array( 'grid' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'heading'       => esc_html__( 'Grid items with different sizes', 'woodmart' ),
					'group'         => esc_html__( 'Content', 'woodmart' ),
					'hint'          => esc_html__( 'Specify certain grid items to be doubled in size. Example: “1,3,5', 'woodmart' ),
					'type'          => 'textfield',
					'param_name'    => 'grid_different_sizes',
					'value'         => '',
					'dependency'    => array(
						'element' => 'style',
						'value'   => array( 'default' ),
					),
					'wd_dependency' => array(
						'element' => 'type',
						'value'   => array( 'grid' ),
					),
				),

				array(
					'type'             => 'woodmart_button_set',
					'heading'          => esc_html__( 'Space between categories', 'woodmart' ),
					'group'            => esc_html__( 'Content', 'woodmart' ),
					'param_name'       => 'spacing_tabs',
					'tabs'             => true,
					'value'            => array(
						esc_html__( 'Desktop', 'woodmart' ) => 'desktop',
						esc_html__( 'Tablet', 'woodmart' ) => 'tablet',
						esc_html__( 'Mobile', 'woodmart' ) => 'mobile',
					),
					'default'          => 'desktop',
					'edit_field_class' => 'wd-res-control wd-custom-width vc_col-sm-12 vc_column',
					'dependency'       => array(
						'element' => 'type',
						'value'   => array( 'grid' ),
					),
				),

				array(
					'group'            => esc_html__( 'Content', 'woodmart' ),
					'type'             => 'dropdown',
					'param_name'       => 'spacing',
					'value'            => array(
						esc_html__( 'Inherit from Theme Settings', 'woodmart' ) => '',
						0  => 0,
						2  => 2,
						6  => 6,
						10 => 10,
						20 => 20,
						30 => 30,
					),
					'std'              => '',
					'dependency'       => array(
						'element' => 'type',
						'value'   => array( 'grid' ),
					),
					'wd_dependency'    => array(
						'element' => 'spacing_tabs',
						'value'   => array( 'desktop' ),
					),
					'edit_field_class' => 'wd-res-item vc_col-sm-12 vc_column',
				),

				array(
					'group'            => esc_html__( 'Content', 'woodmart' ),
					'type'             => 'dropdown',
					'param_name'       => 'spacing_tablet',
					'value'            => array(
						esc_html__( 'Inherit', 'woodmart' ) => '',
						0  => 0,
						2  => 2,
						6  => 6,
						10 => 10,
						20 => 20,
						30 => 30,
					),
					'std'              => '',
					'dependency'       => array(
						'element' => 'type',
						'value'   => array( 'grid' ),
					),
					'wd_dependency'    => array(
						'element' => 'spacing_tabs',
						'value'   => array( 'tablet' ),
					),
					'edit_field_class' => 'wd-res-item vc_col-sm-12 vc_column',
				),

				array(
					'group'            => esc_html__( 'Content', 'woodmart' ),
					'type'             => 'dropdown',
					'param_name'       => 'spacing_mobile',
					'value'            => array(
						esc_html__( 'Inherit', 'woodmart' ) => '',
						0  => 0,
						2  => 2,
						6  => 6,
						10 => 10,
						20 => 20,
						30 => 30,
					),
					'std'              => '',
					'dependency'       => array(
						'element' => 'type',
						'value'   => array( 'grid' ),
					),
					'wd_dependency'    => array(
						'element' => 'spacing_tabs',
						'value'   => array( 'mobile' ),
					),
					'edit_field_class' => 'wd-res-item vc_col-sm-12 vc_column',
				),

				array(
					'heading'          => esc_html__( 'Items per page', 'woodmart' ),
					'group'            => esc_html__( 'Content', 'woodmart' ),
					'type'             => 'textfield',
					'param_name'       => 'number',
					'hint'             => esc_html__( 'Enter the number of categories to display for this element.', 'woodmart' ),
					'dependency'       => array(
						'element' => 'data_source',
						'value'   => array( 'custom_query' ),
					),
					'wd_dependency'    => array(
						'element' => 'type',
						'value'   => array( 'grid' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				/**
				 * Content - Mobile layout
				 */
				array(
					'title'      => esc_html__( 'Mobile', 'woodmart' ),
					'group'      => esc_html__( 'Content', 'woodmart' ),
					'type'       => 'woodmart_title_divider',
					'param_name' => 'mobile_divider',
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'navigation' ),
					),
				),

				array(
					'heading'          => esc_html__( 'Layout', 'woodmart' ),
					'group'            => esc_html__( 'Content', 'woodmart' ),
					'hint'             => esc_html__( 'Turn categories navigation into accordion or hidden sidebar on mobile devices', 'woodmart' ),
					'type'             => 'dropdown',
					'param_name'       => 'mobile_accordion',
					'value'            => array(
						esc_html__( 'Default', 'woodmart' ) => 'no',
						esc_html__( 'Accordion', 'woodmart' ) => 'yes',
						esc_html__( 'Hidden sidebar', 'woodmart' ) => 'side-hidden',
					),
					'std'              => 'yes',
					'dependency'       => array(
						'element' => 'type',
						'value'   => array( 'navigation' ),
					),
					'edit_field_class' => 'vc_col-sm-12 vc_column',
				),

				array(
					'param_name'       => 'mobile_categories_menu_layout',
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Menu layout', 'woodmart' ),
					'group'            => esc_html__( 'Content', 'woodmart' ),
					'value'            => array(
						esc_html__( 'Dropdown', 'woodmart' )  => 'dropdown',
						esc_html__( 'Drilldown', 'woodmart' ) => 'drilldown',
					),
					'std'              => 'dropdown',
					'dependency'       => array(
						'element' => 'mobile_accordion',
						'value'   => array( 'side-hidden' ),
					),
					'wd_dependency'    => array(
						'element' => 'type',
						'value'   => array( 'navigation' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'param_name'       => 'mobile_categories_drilldown_animation',
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Drilldown animation', 'woodmart' ),
					'group'            => esc_html__( 'Content', 'woodmart' ),
					'value'            => array(
						esc_html__( 'Slide', 'woodmart' ) => 'slide',
						esc_html__( 'Fade in', 'woodmart' ) => 'fade-in',
					),
					'std'              => 'slide',
					'dependency'       => array(
						'element' => 'mobile_accordion',
						'value'   => array( 'side-hidden' ),
					),
					'wd_dependency'    => array(
						'element' => 'mobile_categories_menu_layout',
						'value'   => array( 'drilldown' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'param_name'       => 'mobile_categories_submenu_opening_action',
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Opening action', 'woodmart' ),
					'group'            => esc_html__( 'Content', 'woodmart' ),
					'value'            => array(
						esc_html__( 'Arrow', 'woodmart' ) => 'only_arrow',
						esc_html__( 'Label and arrow', 'woodmart' ) => 'item_and_arrow',
					),
					'std'              => 'only_arrow',
					'dependency'       => array(
						'element' => 'mobile_accordion',
						'value'   => array( 'side-hidden' ),
					),
					'wd_dependency'    => array(
						'element' => 'type',
						'value'   => array( 'navigation' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'param_name'       => 'mobile_categories_position',
					'type'             => 'woodmart_image_select',
					'heading'          => esc_html__( 'Position', 'woodmart' ),
					'group'            => esc_html__( 'Content', 'woodmart' ),
					'value'            => array(
						esc_html__( 'Left', 'woodmart' )  => 'left',
						esc_html__( 'Right', 'woodmart' ) => 'right',
					),
					'images_value'     => array(
						'left'  => WOODMART_ASSETS_IMAGES . '/settings/sidebar-layout/left.png',
						'right' => WOODMART_ASSETS_IMAGES . '/settings/sidebar-layout/right.png',
					),
					'std'              => 'left',
					'dependency'       => array(
						'element' => 'mobile_accordion',
						'value'   => array( 'side-hidden' ),
					),
					'wd_dependency'    => array(
						'element' => 'type',
						'value'   => array( 'navigation' ),
					),
					'wood_tooltip'     => true,
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'param_name'       => 'mobile_categories_close_btn',
					'type'             => 'woodmart_switch',
					'heading'          => esc_html__( 'Close button', 'woodmart' ),
					'group'            => esc_html__( 'Content', 'woodmart' ),
					'true_state'       => 'yes',
					'false_state'      => 'no',
					'default'          => 'no',
					'dependency'       => array(
						'element' => 'mobile_accordion',
						'value'   => array( 'side-hidden' ),
					),
					'wd_dependency'    => array(
						'element' => 'type',
						'value'   => array( 'navigation' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'param_name'       => 'mobile_categories_color_scheme',
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Color scheme', 'woodmart' ),
					'group'            => esc_html__( 'Content', 'woodmart' ),
					'value'            => array(
						esc_html__( 'Default', 'woodmart' ) => 'default',
						esc_html__( 'Dark', 'woodmart' )  => 'dark',
						esc_html__( 'Light', 'woodmart' ) => 'light',
					),
					'std'              => 'default',
					'dependency'       => array(
						'element' => 'mobile_accordion',
						'value'   => array( 'side-hidden' ),
					),
					'wd_dependency'    => array(
						'element' => 'type',
						'value'   => array( 'navigation' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				/**
				 * Content - Elements
				 */
				array(
					'title'      => esc_html__( 'Elements', 'woodmart' ),
					'group'      => esc_html__( 'Content', 'woodmart' ),
					'type'       => 'woodmart_title_divider',
					'param_name' => 'elements_divider',
				),

				array(
					'heading'          => esc_html__( 'Enable icons', 'woodmart' ),
					'group'            => esc_html__( 'Content', 'woodmart' ),
					'type'             => 'woodmart_switch',
					'param_name'       => 'images',
					'true_state'       => 'yes',
					'false_state'      => 'no',
					'default'          => 'yes',
					'dependency'       => array(
						'element' => 'type',
						'value'   => array( 'navigation' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'heading'          => esc_html__( 'Enable product count', 'woodmart' ),
					'group'            => esc_html__( 'Content', 'woodmart' ),
					'type'             => 'woodmart_switch',
					'param_name'       => 'product_count',
					'true_state'       => 'yes',
					'false_state'      => 'no',
					'default'          => 'yes',
					'dependency'       => array(
						'element' => 'type',
						'value'   => array( 'navigation' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'type'       => 'woodmart_button_set',
					'heading'    => esc_html__( 'Product count', 'woodmart' ),
					'param_name' => 'grid_product_count',
					'value'      => array(
						esc_html__( 'Inherit', 'woodmart' ) => '',
						esc_html__( 'Enable', 'woodmart' ) => 'enable',
						esc_html__( 'Disable', 'woodmart' ) => 'disable',
					),
					'group'      => esc_html__( 'Content', 'woodmart' ),
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'grid' ),
					),
				),

				/**
				 * Style - Design
				 */
				array(
					'title'      => esc_html__( 'Design', 'woodmart' ),
					'group'      => esc_html__( 'Style', 'woodmart' ),
					'type'       => 'woodmart_title_divider',
					'param_name' => 'design_divider',
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'grid' ),
					),
				),

				array(
					'heading'      => esc_html__( 'Categories design', 'woodmart' ),
					'group'        => esc_html__( 'Style', 'woodmart' ),
					'type'         => 'woodmart_image_select',
					'param_name'   => 'categories_design',
					'value'        => array(
						esc_html__( 'Inherit from Theme Settings', 'woodmart' ) => 'inherit',
						esc_html__( 'Default', 'woodmart' ) => 'default',
						esc_html__( 'Alternative', 'woodmart' ) => 'alt',
						esc_html__( 'Center title', 'woodmart' ) => 'center',
						esc_html__( 'Replace title', 'woodmart' ) => 'replace-title',
						esc_html__( 'Mask', 'woodmart' ) => 'mask-subcat',
						esc_html__( 'Side', 'woodmart' ) => 'side',
						esc_html__( 'Zoom out', 'woodmart' ) => 'zoom-out',
					),
					'images_value' => array(
						'inherit'       => WOODMART_ASSETS_IMAGES . '/settings/empty.jpg',
						'default'       => WOODMART_ASSETS_IMAGES . '/settings/categories/default.jpg',
						'alt'           => WOODMART_ASSETS_IMAGES . '/settings/categories/alt.jpg',
						'center'        => WOODMART_ASSETS_IMAGES . '/settings/categories/center.jpg',
						'replace-title' => WOODMART_ASSETS_IMAGES . '/settings/categories/replace-title.jpg',
						'mask-subcat'   => WOODMART_ASSETS_IMAGES . '/settings/categories/subcat.jpg',
						'side'          => WOODMART_ASSETS_IMAGES . '/settings/categories/side.jpg',
						'zoom-out'      => WOODMART_ASSETS_IMAGES . '/settings/categories/zoom-out.jpg',
					),
					'hint'         => esc_html__( 'Overrides option from Theme Settings -> Shop', 'woodmart' ),
					'dependency'   => array(
						'element' => 'type',
						'value'   => array( 'grid' ),
					),
				),

				array(
					'heading'    => esc_html__( 'Image resolution', 'woodmart' ),
					'group'      => esc_html__( 'Style', 'woodmart' ),
					'type'       => 'textfield',
					'param_name' => 'img_size',
					'hint'       => esc_html__( 'Enter image resolution. Example: \'thumbnail\', \'medium\', \'large\', \'full\' or other sizes defined by current theme. Alternatively enter image size in pixels: 200x100 (Width x Height). Leave empty to use \'thumbnail\' size.', 'woodmart' ),
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'grid' ),
					),
				),

				array(
					'heading'    => esc_html__( 'Image width', 'woodmart' ),
					'group'      => esc_html__( 'Style', 'woodmart' ),
					'type'       => 'wd_slider',
					'param_name' => 'image_container_width',
					'selectors'  => array(
						'{{WRAPPER}}' => array(
							'--wd-cat-img-width: {{VALUE}}{{UNIT}};',
						),
					),
					'devices'    => array(
						'desktop' => array(
							'unit'  => 'px',
							'value' => '',
						),
						'tablet'  => array(
							'unit'  => 'px',
							'value' => '',
						),
						'mobile'  => array(
							'unit'  => 'px',
							'value' => '',
						),
					),
					'range'      => array(
						'px' => array(
							'min'  => 0,
							'max'  => 1000,
							'step' => 1,
						),
						'%'  => array(
							'min'  => 0,
							'max'  => 100,
							'step' => 1,
						),
					),
					'dependency' => array(
						'element' => 'categories_design',
						'value'   => array( 'alt', 'side' ),
					),
				),

				array(
					'heading'          => esc_html__( 'Color scheme', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'type'             => 'woodmart_dropdown',
					'param_name'       => 'color_scheme',
					'value'            => array(
						esc_html__( 'Inherit', 'woodmart' ) => 'inherit',
						esc_html__( 'Dark', 'woodmart' )  => 'dark',
						esc_html__( 'Light', 'woodmart' ) => 'light',
					),
					'style'            => array(
						'dark' => '#2d2a2a',
					),
					'std'              => '',
					'dependency'       => array(
						'element' => 'categories_design',
						'value'   => array( 'default', 'mask-subcat' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'heading'    => esc_html__( 'Categories with shadow', 'woodmart' ),
					'group'      => esc_html__( 'Style', 'woodmart' ),
					'type'       => 'woodmart_button_set',
					'param_name' => 'categories_with_shadow',
					'value'      => array(
						esc_html__( 'Inherit from Theme Settings', 'woodmart' ) => '',
						esc_html__( 'Enable', 'woodmart' ) => 'enable',
						esc_html__( 'Disable', 'woodmart' ) => 'disable',
					),
					'dependency' => array(
						'element' => 'categories_design',
						'value'   => array( 'alt', 'default' ),
					),
				),

				array(
					'heading'       => esc_html__( 'Rounding', 'woodmart' ),
					'group'         => esc_html__( 'Style', 'woodmart' ),
					'type'          => 'wd_select',
					'param_name'    => 'rounding_size',
					'style'         => 'select',
					'selectors'     => array(
						'{{WRAPPER}}' => array(
							'--wd-cat-brd-radius: {{VALUE}}px;',
						),
					),
					'devices'       => array(
						'desktop' => array(
							'value' => '',
						),
					),
					'value'         => array(
						esc_html__( 'Inherit', 'woodmart' ) => '',
						esc_html__( '0', 'woodmart' )      => '0',
						esc_html__( '5', 'woodmart' )      => '5',
						esc_html__( '8', 'woodmart' )      => '8',
						esc_html__( '12', 'woodmart' )     => '12',
						esc_html__( 'Custom', 'woodmart' ) => 'custom',
					),
					'dependency'    => array(
						'element' => 'type',
						'value'   => array( 'grid' ),
					),
					'generate_zero' => true,
				),

				array(
					'heading'       => esc_html__( 'Custom rounding', 'woodmart' ),
					'group'         => esc_html__( 'Style', 'woodmart' ),
					'type'          => 'wd_slider',
					'param_name'    => 'custom_rounding_size',
					'selectors'     => array(
						'{{WRAPPER}}' => array(
							'--wd-cat-brd-radius: {{VALUE}}{{UNIT}};',
						),
					),
					'devices'       => array(
						'desktop' => array(
							'value' => '',
							'unit'  => 'px',
						),
					),
					'range'         => array(
						'px' => array(
							'min'  => 0,
							'max'  => 300,
							'step' => 1,
						),
						'%'  => array(
							'min'  => 0,
							'max'  => 100,
							'step' => 1,
						),
					),
					'dependency'    => array(
						'element' => 'rounding_size',
						'value'   => function_exists( 'woodmart_compress' ) ? woodmart_compress(
							wp_json_encode(
								array(
									'devices' => array(
										'desktop' => array(
											'value' => 'custom',
										),
									),
								)
							)
						) : '',
					),
					'generate_zero' => true,
				),

				array(
					'type'        => 'woodmart_switch',
					'heading'     => esc_html__( 'Bordered grid', 'woodmart' ),
					'hint'        => esc_html__( 'Add borders between the categories in your grid', 'woodmart' ),
					'group'       => esc_html__( 'Style', 'woodmart' ),
					'param_name'  => 'categories_bordered_grid',
					'true_state'  => 1,
					'false_state' => 0,
					'default'     => 0,
					'dependency'  => array(
						'element' => 'type',
						'value'   => array( 'grid' ),
					),
				),

				array(
					'type'          => 'woodmart_button_set',
					'heading'       => esc_html__( 'Bordered grid style', 'woodmart' ),
					'param_name'    => 'categories_bordered_grid_style',
					'value'         => array(
						esc_html__( 'Outside', 'woodmart' ) => 'outside',
						esc_html__( 'Inside', 'woodmart' ) => 'inside',
					),
					'group'         => esc_html__( 'Style', 'woodmart' ),
					'dependency'    => array(
						'element' => 'type',
						'value'   => array( 'grid' ),
					),
					'wd_dependency' => array(
						'element' => 'categories_bordered_grid',
						'value'   => '1',
					),
				),

				array(
					'heading'          => esc_html__( 'Custom category border color', 'woodmart' ),
					'hint'             => esc_html__( 'Set custom border color for category.', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'param_name'       => 'category_border_color',
					'selectors'        => array(
						'{{WRAPPER}} [class*="products-bordered-grid"], {{WRAPPER}} [class*="products-bordered-grid"] .wd-cat, {{WRAPPER}}[class*="products-bordered-grid"], {{WRAPPER}}[class*="products-bordered-grid"] .wd-cat' => array(
							'--wd-bordered-brd:{{VALUE}};',
						),
					),
					'dependency'       => array(
						'element' => 'categories_bordered_grid',
						'value'   => '1',
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'heading'     => esc_html__( 'Categories background', 'woodmart' ),
					'hint'        => esc_html__( 'Add a background to the categories in your grid.', 'woodmart' ),
					'group'       => esc_html__( 'Style', 'woodmart' ),
					'type'        => 'woodmart_switch',
					'param_name'  => 'categories_with_background',
					'true_state'  => 1,
					'false_state' => 0,
					'default'     => 0,
					'dependency'  => array(
						'element' => 'type',
						'value'   => array( 'grid' ),
					),
				),

				array(
					'heading'    => esc_html__( 'Custom background color', 'woodmart' ),
					'hint'       => esc_html__( 'Set custom background color for categories.', 'woodmart' ),
					'group'      => esc_html__( 'Style', 'woodmart' ),
					'type'       => 'wd_colorpicker',
					'param_name' => 'categories_background',
					'selectors'  => array(
						'{{WRAPPER}} .wd-products-with-bg, {{WRAPPER}}.wd-products-with-bg, {{WRAPPER}} .wd-products-with-bg .wd-cat, {{WRAPPER}}.wd-products-with-bg .wd-cat' => array(
							'--wd-prod-bg:{{VALUE}}; --wd-bordered-bg:{{VALUE}};',
						),
					),
					'dependency' => array(
						'element' => 'categories_with_background',
						'value'   => array( '1' ),
					),
				),

				array(
					'type'          => 'woodmart_button_set',
					'heading'       => esc_html__( 'Subcategories', 'woodmart' ),
					'param_name'    => 'subcategories',
					'value'         => array(
						esc_html__( 'Inherit', 'woodmart' ) => '',
						esc_html__( 'Enable', 'woodmart' ) => 'enable',
						esc_html__( 'Disable', 'woodmart' ) => 'disable',
					),
					'group'         => esc_html__( 'Style', 'woodmart' ),
					'dependency'    => array(
						'element' => 'categories_design',
						'value'   => array( 'mask-subcat', 'side' ),
					),
					'wd_dependency' => array(
						'element' => 'type',
						'value'   => array( 'grid' ),
					),
				),

				$title_typography['font_family'],
				$title_typography['font_size'],
				$title_typography['font_weight'],
				$title_typography['text_transform'],
				$title_typography['font_style'],
				$title_typography['text_decoration'],
				$title_typography['line_height'],

				/**
				 * Style - Items
				 */
				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Items', 'woodmart' ),
					'group'      => esc_html__( 'Style', 'woodmart' ),
					'param_name' => 'nav_items_divider',
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'navigation' ),
					),
				),

				array(
					'type'       => 'dropdown',
					'heading'    => esc_html__( 'Style', 'woodmart' ),
					'group'      => esc_html__( 'Style', 'woodmart' ),
					'param_name' => 'nav_style',
					'value'      => array(
						esc_html__( 'Default', 'woodmart' )    => 'default',
						esc_html__( 'Underline', 'woodmart' )  => 'underline',
					),
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'navigation' ),
					),
					'std'        => 'default',
				),

				array(
					'type'       => 'dropdown',
					'heading'    => esc_html__( 'Gap', 'woodmart' ),
					'group'      => esc_html__( 'Style', 'woodmart' ),
					'param_name' => 'nav_items_gap',
					'value'      => array(
						esc_html__( 'Small', 'woodmart' )  => 's',
						esc_html__( 'Medium', 'woodmart' ) => 'm',
						esc_html__( 'Large', 'woodmart' )  => 'l',
						esc_html__( 'Custom', 'woodmart' ) => 'custom',
					),
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'navigation' ),
					),
				),

				array(
					'type'          => 'wd_slider',
					'group'         => esc_html__( 'Style', 'woodmart' ),
					'heading'       => esc_html__( 'Custom gap', 'woodmart' ),
					'param_name'    => 'nav_custom_items_gap',
					'selectors'     => array(
						'.wd {{WRAPPER}} .wd-nav-product-cat' => array(
							'--nav-gap: {{VALUE}}{{UNIT}};',
						),
					),
					'devices'       => array(
						'desktop' => array(
							'value' => '',
							'unit'  => 'px',
						),
						'tablet'  => array(
							'value' => '',
							'unit'  => 'px',
						),
						'mobile'  => array(
							'value' => '',
							'unit'  => 'px',
						),
					),
					'range'         => array(
						'px' => array(
							'min'  => 1,
							'max'  => 100,
							'step' => 1,
						),
					),
					'dependency'    => array(
						'element' => 'type',
						'value'   => array( 'navigation' ),
					),
					'wd_dependency' => array(
						'element' => 'nav_items_gap',
						'value'   => array( 'custom' ),
					),
				),

				$nav_items_typography['font_family'],
				$nav_items_typography['font_size'],
				$nav_items_typography['font_weight'],
				$nav_items_typography['text_transform'],
				$nav_items_typography['font_style'],
				$nav_items_typography['text_decoration'],
				$nav_items_typography['line_height'],

				array(
					'heading'          => esc_html__( 'Color scheme', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'type'             => 'woodmart_dropdown',
					'param_name'       => 'nav_color_scheme',
					'value'            => array(
						esc_html__( 'Inherit from Theme Settings', 'woodmart' ) => 'inherit',
						esc_html__( 'Dark', 'woodmart' )  => 'dark',
						esc_html__( 'Light', 'woodmart' ) => 'light',
					),
					'dependency'       => array(
						'element' => 'type',
						'value'   => array( 'navigation' ),
					),
					'edit_field_class' => 'vc_col-sm-12 vc_column',
				),

				// Style - Items tabs.
				array(
					'type'       => 'woodmart_button_set',
					'group'      => esc_html__( 'Style', 'woodmart' ),
					'param_name' => 'nav_items_color_tabs',
					'tabs'       => true,
					'value'      => array(
						esc_html__( 'Idle', 'woodmart' )   => 'idle',
						esc_html__( 'Hover', 'woodmart' )  => 'hover',
						esc_html__( 'Active', 'woodmart' ) => 'active',
					),
					'default'    => 'idle',
				),

				array(
					'type'             => 'woodmart_switch',
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'heading'          => esc_html__( 'Disable active style', 'woodmart' ),
					'param_name'       => 'nav_disable_active_style',
					'true_state'       => 'yes',
					'false_state'      => 'no',
					'default'          => 'no',
					'wd_dependency'    => array(
						'element' => 'nav_items_color_tabs',
						'value'   => array( 'active' ),
					),
					'edit_field_class' => 'vc_col-sm-12 vc_column',
				),

				// Color.
				array(
					'heading'          => esc_html__( 'Color', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'param_name'       => 'title_idle_color',
					'selectors'        => array(
						'.wd {{WRAPPER}} .wd-nav-product-cat' => array(
							'--nav-color: {{VALUE}};',
						),
					),
					'wd_dependency'    => array(
						'element' => 'nav_items_color_tabs',
						'value'   => array( 'idle' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'heading'          => esc_html__( 'Color', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'param_name'       => 'title_hover_color',
					'selectors'        => array(
						'.wd {{WRAPPER}} .wd-nav-product-cat' => array(
							'--nav-color-hover: {{VALUE}};',
						),
					),
					'wd_dependency'    => array(
						'element' => 'nav_items_color_tabs',
						'value'   => array( 'hover' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'heading'          => esc_html__( 'Color', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'param_name'       => 'nav_items_active_color',
					'selectors'        => array(
						'.wd {{WRAPPER}} .wd-nav-product-cat' => array(
							'--nav-color-active: {{VALUE}};',
						),
					),
					'wd_dependency'    => array(
						'element' => 'nav_items_color_tabs',
						'value'   => array( 'active' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				// Product count color.
				array(
					'heading'          => esc_html__( 'Count color', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'param_name'       => 'count_idle_color',
					'selectors'        => array(
						'{{WRAPPER}} .wd-nav-product-cat > li > a .nav-link-count' => array(
							'color: {{VALUE}};',
						),
					),
					'wd_dependency'    => array(
						'element' => 'nav_items_color_tabs',
						'value'   => array( 'idle' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'heading'          => esc_html__( 'Count color', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'param_name'       => 'count_hover_color',
					'selectors'        => array(
						'{{WRAPPER}} .wd-nav-product-cat > li:hover > a .nav-link-count' => array(
							'color: {{VALUE}};',
						),
					),
					'wd_dependency'    => array(
						'element' => 'nav_items_color_tabs',
						'value'   => array( 'hover' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'heading'          => esc_html__( 'Count color', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'param_name'       => 'count_active_color',
					'selectors'        => array(
						'{{WRAPPER}} .wd-nav-product-cat:where(:not(.wd-dis-act)) > li.wd-active > a .nav-link-count' => array(
							'color: {{VALUE}};',
						),
					),
					'wd_dependency'    => array(
						'element' => 'nav_items_color_tabs',
						'value'   => array( 'active' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				// Background color.
				array(
					'type'             => 'woodmart_switch',
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'heading'          => esc_html__( 'Background color', 'woodmart' ),
					'param_name'       => 'enable_nav_items_background',
					'true_state'       => 'yes',
					'false_state'      => 'no',
					'default'          => 'no',
					'wd_dependency'    => array(
						'element' => 'nav_items_color_tabs',
						'value'   => array( 'idle' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'heading'          => esc_html__( 'Background color', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'param_name'       => 'nav_items_bg_color',
					'selectors'        => array(
						'.wd {{WRAPPER}} .wd-nav-product-cat' => array(
							'--nav-bg: {{VALUE}};',
						),
					),
					'wd_dependency'    => array(
						'element' => 'nav_items_color_tabs',
						'value'   => array( 'idle' ),
					),
					'dependency'       => array(
						'element' => 'enable_nav_items_background',
						'value'   => array( 'yes' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'type'             => 'woodmart_switch',
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'heading'          => esc_html__( 'Background color', 'woodmart' ),
					'param_name'       => 'enable_nav_items_background_hover',
					'true_state'       => 'yes',
					'false_state'      => 'no',
					'default'          => 'no',
					'wd_dependency'    => array(
						'element' => 'nav_items_color_tabs',
						'value'   => array( 'hover' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'heading'          => esc_html__( 'Background color', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'param_name'       => 'nav_items_bg_hover_color',
					'selectors'        => array(
						'.wd {{WRAPPER}} .wd-nav-product-cat' => array(
							'--nav-bg-hover: {{VALUE}};',
						),
					),
					'wd_dependency'    => array(
						'element' => 'nav_items_color_tabs',
						'value'   => array( 'hover' ),
					),
					'dependency'       => array(
						'element' => 'enable_nav_items_background_hover',
						'value'   => array( 'yes' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'type'             => 'woodmart_switch',
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'heading'          => esc_html__( 'Background color', 'woodmart' ),
					'param_name'       => 'enable_nav_items_background_active',
					'true_state'       => 'yes',
					'false_state'      => 'no',
					'default'          => 'no',
					'wd_dependency'    => array(
						'element' => 'nav_items_color_tabs',
						'value'   => array( 'active' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'heading'          => esc_html__( 'Background color', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'param_name'       => 'nav_items_bg_active_color',
					'selectors'        => array(
						'.wd {{WRAPPER}} .wd-nav-product-cat' => array(
							'--nav-bg-active: {{VALUE}};',
						),
					),
					'wd_dependency'    => array(
						'element' => 'nav_items_color_tabs',
						'value'   => array( 'active' ),
					),
					'dependency'       => array(
						'element' => 'enable_nav_items_background_active',
						'value'   => array( 'yes' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'group'      => esc_html__( 'Style', 'woodmart' ),
					'type'       => 'woodmart_empty_space',
					'param_name' => 'woodmart_empty_space_bg',
				),

				// Border idle.
				array(
					'type'             => 'woodmart_switch',
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'heading'          => esc_html__( 'Border', 'woodmart' ),
					'param_name'       => 'enable_nav_items_border',
					'true_state'       => 'yes',
					'false_state'      => 'no',
					'default'          => 'no',
					'wd_dependency'    => array(
						'element' => 'nav_items_color_tabs',
						'value'   => array( 'idle' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'heading'          => esc_html__( 'Border type', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'type'             => 'wd_select',
					'param_name'       => 'nav_items_border_type',
					'style'            => 'select',
					'selectors'        => array(
						'.wd {{WRAPPER}} .wd-nav-product-cat > li > a' => array(
							'border-style: {{VALUE}};',
						),
					),
					'devices'          => array(
						'desktop' => array(
							'value' => '',
						),
					),
					'value'            => array(
						esc_html__( 'Default', 'woodmart' ) => '',
						esc_html__( 'None', 'woodmart' )   => 'none',
						esc_html__( 'Solid', 'woodmart' )  => 'solid',
						esc_html__( 'Dotted', 'woodmart' ) => 'dotted',
						esc_html__( 'Double', 'woodmart' ) => 'double',
						esc_html__( 'Dashed', 'woodmart' ) => 'dashed',
						esc_html__( 'Groove', 'woodmart' ) => 'groove',
					),
					'wd_dependency'    => array(
						'element' => 'nav_items_color_tabs',
						'value'   => array( 'idle' ),
					),
					'dependency'       => array(
						'element' => 'enable_nav_items_border',
						'value'   => array( 'yes' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'type'          => 'wd_slider',
					'group'         => esc_html__( 'Style', 'woodmart' ),
					'heading'       => esc_html__( 'Border width', 'woodmart' ),
					'param_name'    => 'nav_items_border_width',
					'selectors'     => array(
						'.wd {{WRAPPER}} .wd-nav-product-cat > li > a' => array(
							'border-width: {{VALUE}}{{UNIT}};',
						),
					),
					'devices'       => array(
						'desktop' => array(
							'unit' => 'px',
						),
					),
					'range'         => array(
						'px' => array(
							'min'  => 0,
							'max'  => 50,
							'step' => 1,
						),
					),
					'wd_dependency' => array(
						'element' => 'nav_items_color_tabs',
						'value'   => array( 'idle' ),
					),
					'dependency'    => array(
						'element' => 'enable_nav_items_border',
						'value'   => array( 'yes' ),
					),
				),

				array(
					'heading'       => esc_html__( 'Border color', 'woodmart' ),
					'group'         => esc_html__( 'Style', 'woodmart' ),
					'type'          => 'wd_colorpicker',
					'param_name'    => 'nav_items_border_color',
					'selectors'     => array(
						'.wd {{WRAPPER}} .wd-nav-product-cat > li > a' => array(
							'border-color: {{VALUE}};',
						),
					),
					'wd_dependency' => array(
						'element' => 'nav_items_color_tabs',
						'value'   => array( 'idle' ),
					),
					'dependency'    => array(
						'element' => 'enable_nav_items_border',
						'value'   => array( 'yes' ),
					),
				),

				array(
					'type'          => 'wd_slider',
					'group'         => esc_html__( 'Style', 'woodmart' ),
					'heading'       => esc_html__( 'Border radius', 'woodmart' ),
					'param_name'    => 'nav_items_border_radius',
					'selectors'     => array(
						'.wd {{WRAPPER}} .wd-nav-product-cat' => array(
							'--nav-radius: {{VALUE}}{{UNIT}};',
						),
					),
					'devices'       => array(
						'desktop' => array(
							'unit' => 'px',
						),
					),
					'range'         => array(
						'px' => array(
							'min'  => 0,
							'max'  => 50,
							'step' => 1,
						),
					),
					'wd_dependency' => array(
						'element' => 'nav_items_color_tabs',
						'value'   => array( 'idle' ),
					),
					'dependency'    => array(
						'element' => 'enable_nav_items_border',
						'value'   => array( 'yes' ),
					),
				),

				// Border hover.
				array(
					'type'             => 'woodmart_switch',
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'heading'          => esc_html__( 'Border', 'woodmart' ),
					'param_name'       => 'enable_nav_items_border_hover',
					'true_state'       => 'yes',
					'false_state'      => 'no',
					'default'          => 'no',
					'wd_dependency'    => array(
						'element' => 'nav_items_color_tabs',
						'value'   => array( 'hover' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'heading'          => esc_html__( 'Border type', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'type'             => 'wd_select',
					'param_name'       => 'nav_items_border_hover_type',
					'style'            => 'select',
					'selectors'        => array(
						'.wd {{WRAPPER}} .wd-nav-product-cat > li:hover > a' => array(
							'border-style: {{VALUE}};',
						),
					),
					'devices'          => array(
						'desktop' => array(
							'value' => '',
						),
					),
					'value'            => array(
						esc_html__( 'Default', 'woodmart' ) => '',
						esc_html__( 'None', 'woodmart' )   => 'none',
						esc_html__( 'Solid', 'woodmart' )  => 'solid',
						esc_html__( 'Dotted', 'woodmart' ) => 'dotted',
						esc_html__( 'Double', 'woodmart' ) => 'double',
						esc_html__( 'Dashed', 'woodmart' ) => 'dashed',
						esc_html__( 'Groove', 'woodmart' ) => 'groove',
					),
					'wd_dependency'    => array(
						'element' => 'nav_items_color_tabs',
						'value'   => array( 'hover' ),
					),
					'dependency'       => array(
						'element' => 'enable_nav_items_border_hover',
						'value'   => array( 'yes' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'type'          => 'wd_slider',
					'group'         => esc_html__( 'Style', 'woodmart' ),
					'heading'       => esc_html__( 'Border width', 'woodmart' ),
					'param_name'    => 'nav_items_border_hover_width',
					'selectors'     => array(
						'.wd {{WRAPPER}} .wd-nav-product-cat > li:hover > a' => array(
							'border-width: {{VALUE}}{{UNIT}};',
						),
					),
					'devices'       => array(
						'desktop' => array(
							'unit' => 'px',
						),
					),
					'range'         => array(
						'px' => array(
							'min'  => 0,
							'max'  => 50,
							'step' => 1,
						),
					),
					'wd_dependency' => array(
						'element' => 'nav_items_color_tabs',
						'value'   => array( 'hover' ),
					),
					'dependency'    => array(
						'element' => 'enable_nav_items_border_hover',
						'value'   => array( 'yes' ),
					),
				),

				array(
					'heading'       => esc_html__( 'Border color', 'woodmart' ),
					'group'         => esc_html__( 'Style', 'woodmart' ),
					'type'          => 'wd_colorpicker',
					'param_name'    => 'nav_items_border_hover_color',
					'selectors'     => array(
						'.wd {{WRAPPER}} .wd-nav-product-cat > li:hover > a' => array(
							'border-color: {{VALUE}};',
						),
					),
					'wd_dependency' => array(
						'element' => 'nav_items_color_tabs',
						'value'   => array( 'hover' ),
					),
					'dependency'    => array(
						'element' => 'enable_nav_items_border_hover',
						'value'   => array( 'yes' ),
					),
				),

				// Border active.
				array(
					'type'             => 'woodmart_switch',
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'heading'          => esc_html__( 'Border', 'woodmart' ),
					'param_name'       => 'enable_nav_items_border_active',
					'true_state'       => 'yes',
					'false_state'      => 'no',
					'default'          => 'no',
					'wd_dependency'    => array(
						'element' => 'nav_items_color_tabs',
						'value'   => array( 'active' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'heading'          => esc_html__( 'Border type', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'type'             => 'wd_select',
					'param_name'       => 'nav_items_border_active_type',
					'style'            => 'select',
					'selectors'        => array(
						'.wd {{WRAPPER}} .wd-nav-product-cat:where(:not(.wd-dis-act)) > li.wd-active > a' => array(
							'border-style: {{VALUE}};',
						),
					),
					'devices'          => array(
						'desktop' => array(
							'value' => '',
						),
					),
					'value'            => array(
						esc_html__( 'Default', 'woodmart' ) => '',
						esc_html__( 'None', 'woodmart' )   => 'none',
						esc_html__( 'Solid', 'woodmart' )  => 'solid',
						esc_html__( 'Dotted', 'woodmart' ) => 'dotted',
						esc_html__( 'Double', 'woodmart' ) => 'double',
						esc_html__( 'Dashed', 'woodmart' ) => 'dashed',
						esc_html__( 'Groove', 'woodmart' ) => 'groove',
					),
					'wd_dependency'    => array(
						'element' => 'nav_items_color_tabs',
						'value'   => array( 'active' ),
					),
					'dependency'       => array(
						'element' => 'enable_nav_items_border_active',
						'value'   => array( 'yes' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'type'          => 'wd_slider',
					'group'         => esc_html__( 'Style', 'woodmart' ),
					'heading'       => esc_html__( 'Border width', 'woodmart' ),
					'param_name'    => 'nav_items_border_active_width',
					'selectors'     => array(
						'.wd {{WRAPPER}} .wd-nav-product-cat:where(:not(.wd-dis-act)) > li.wd-active > a' => array(
							'border-width: {{VALUE}}{{UNIT}};',
						),
					),
					'devices'       => array(
						'desktop' => array(
							'unit' => 'px',
						),
					),
					'range'         => array(
						'px' => array(
							'min'  => 0,
							'max'  => 50,
							'step' => 1,
						),
					),
					'wd_dependency' => array(
						'element' => 'nav_items_color_tabs',
						'value'   => array( 'active' ),
					),
					'dependency'    => array(
						'element' => 'enable_nav_items_border_active',
						'value'   => array( 'yes' ),
					),
				),

				array(
					'heading'       => esc_html__( 'Border color', 'woodmart' ),
					'group'         => esc_html__( 'Style', 'woodmart' ),
					'type'          => 'wd_colorpicker',
					'param_name'    => 'nav_items_border_active_color',
					'selectors'     => array(
						'.wd {{WRAPPER}} .wd-nav-product-cat:where(:not(.wd-dis-act)) > li.wd-active > a' => array(
							'border-color: {{VALUE}};',
						),
					),
					'wd_dependency' => array(
						'element' => 'nav_items_color_tabs',
						'value'   => array( 'active' ),
					),
					'dependency'    => array(
						'element' => 'enable_nav_items_border_active',
						'value'   => array( 'yes' ),
					),
				),

				// Box shadow idle.
				array(
					'type'          => 'woodmart_switch',
					'group'         => esc_html__( 'Style', 'woodmart' ),
					'heading'       => esc_html__( 'Box shadow', 'woodmart' ),
					'param_name'    => 'enable_nav_items_box_shadow',
					'true_state'    => 'yes',
					'false_state'   => 'no',
					'default'       => 'no',
					'wd_dependency' => array(
						'element' => 'nav_items_color_tabs',
						'value'   => array( 'idle' ),
					),
				),

				array(
					'type'             => 'wd_box_shadow',
					'param_name'       => 'nav_items_box_shadow',
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'heading'          => esc_html__( 'Box shadow', 'woodmart' ),
					'selectors'        => array(
						'.wd {{WRAPPER}} .wd-nav-product-cat > li > a' => array(
							'box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}};',
						),
					),
					'edit_field_class' => 'vc_col-sm-12 vc_column',
					'wd_dependency'    => array(
						'element' => 'nav_items_color_tabs',
						'value'   => array( 'idle' ),
					),
					'dependency'       => array(
						'element' => 'enable_nav_items_box_shadow',
						'value'   => array( 'yes' ),
					),
					'default'          => array(
						'horizontal' => '0',
						'vertical'   => '0',
						'blur'       => '9',
						'spread'     => '0',
						'color'      => 'rgba(0, 0, 0, .15)',
					),
				),

				// Box shadow hover.
				array(
					'type'          => 'woodmart_switch',
					'group'         => esc_html__( 'Style', 'woodmart' ),
					'heading'       => esc_html__( 'Box shadow', 'woodmart' ),
					'param_name'    => 'enable_nav_items_box_shadow_hover',
					'true_state'    => 'yes',
					'false_state'   => 'no',
					'default'       => 'no',
					'wd_dependency' => array(
						'element' => 'nav_items_color_tabs',
						'value'   => array( 'hover' ),
					),
				),

				array(
					'type'             => 'wd_box_shadow',
					'param_name'       => 'nav_items_box_shadow_hover',
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'heading'          => esc_html__( 'Box shadow', 'woodmart' ),
					'selectors'        => array(
						'.wd {{WRAPPER}} .wd-nav-product-cat > li:hover > a' => array(
							'box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}};',
						),
					),
					'edit_field_class' => 'vc_col-sm-12 vc_column',
					'wd_dependency'    => array(
						'element' => 'nav_items_color_tabs',
						'value'   => array( 'hover' ),
					),
					'dependency'       => array(
						'element' => 'enable_nav_items_box_shadow_hover',
						'value'   => array( 'yes' ),
					),
					'default'          => array(
						'horizontal' => '0',
						'vertical'   => '0',
						'blur'       => '9',
						'spread'     => '0',
						'color'      => 'rgba(0, 0, 0, .15)',
					),
				),

				// Box shadow active.
				array(
					'type'          => 'woodmart_switch',
					'group'         => esc_html__( 'Style', 'woodmart' ),
					'heading'       => esc_html__( 'Box shadow', 'woodmart' ),
					'param_name'    => 'enable_nav_items_box_shadow_active',
					'true_state'    => 'yes',
					'false_state'   => 'no',
					'default'       => 'no',
					'wd_dependency' => array(
						'element' => 'nav_items_color_tabs',
						'value'   => array( 'active' ),
					),
				),

				array(
					'type'             => 'wd_box_shadow',
					'param_name'       => 'nav_items_box_shadow_active',
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'heading'          => esc_html__( 'Box shadow', 'woodmart' ),
					'selectors'        => array(
						'.wd {{WRAPPER}} .wd-nav-product-cat:where(:not(.wd-dis-act)) > li.wd-active > a' => array(
							'box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}};',
						),
					),
					'edit_field_class' => 'vc_col-sm-12 vc_column',
					'wd_dependency'    => array(
						'element' => 'nav_items_color_tabs',
						'value'   => array( 'active' ),
					),
					'dependency'       => array(
						'element' => 'enable_nav_items_box_shadow_active',
						'value'   => array( 'yes' ),
					),
					'default'          => array(
						'horizontal' => '0',
						'vertical'   => '0',
						'blur'       => '9',
						'spread'     => '0',
						'color'      => 'rgba(0, 0, 0, .15)',
					),
				),

				array(
					'heading'    => esc_html__( 'Padding', 'woodmart' ),
					'group'      => esc_html__( 'Style', 'woodmart' ),
					'type'       => 'wd_dimensions',
					'param_name' => 'nav_items_padding',
					'selectors'  => array(
						'.wd {{WRAPPER}} .wd-nav-product-cat' => array(
							'--nav-pd: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					),
					'devices'    => array(
						'desktop' => array(
							'unit' => 'px',
						),
						'tablet'  => array(
							'unit' => 'px',
						),
						'mobile'  => array(
							'unit' => 'px',
						),
					),
					'range'      => array(
						'px' => array(),
					),
				),

				array(
					'heading'          => esc_html__( 'Alignment', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'type'             => 'wd_select',
					'param_name'       => 'nav_alignment',
					'style'            => 'images',
					'selectors'        => array(),
					'devices'          => array(
						'desktop' => array(
							'value' => 'left',
						),
					),
					'value'            => array(
						esc_html__( 'Left', 'woodmart' )   => 'left',
						esc_html__( 'Center', 'woodmart' ) => 'center',
						esc_html__( 'Right', 'woodmart' )  => 'right',
					),
					'images'           => array(
						'left'   => WOODMART_ASSETS_IMAGES . '/settings/align/left.jpg',
						'center' => WOODMART_ASSETS_IMAGES . '/settings/align/center.jpg',
						'right'  => WOODMART_ASSETS_IMAGES . '/settings/align/right.jpg',
					),
					'dependency'       => array(
						'element' => 'type',
						'value'   => array( 'navigation' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				/**
				 * Style - Icon
				 */
				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Icon', 'woodmart' ),
					'group'      => esc_html__( 'Style', 'woodmart' ),
					'param_name' => 'icon_divider',
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'navigation' ),
					),
				),

				array(
					'type'       => 'dropdown',
					'heading'    => esc_html__( 'Alignment', 'woodmart' ),
					'group'      => esc_html__( 'Style', 'woodmart' ),
					'param_name' => 'icon_alignment',
					'value'      => array(
						esc_html__( 'Default', 'woodmart' ) => 'inherit',
						esc_html__( 'Left', 'woodmart' )  => 'left',
						esc_html__( 'Right', 'woodmart' ) => 'right',
					),
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'navigation' ),
					),
				),

				array(
					'type'             => 'wd_slider',
					'heading'          => esc_html__( 'Height', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'param_name'       => 'icon_height',
					'selectors'        => array(
						'.wd {{WRAPPER}} .wd-nav-product-cat > li > a .wd-nav-img' => array(
							'--nav-img-height: {{VALUE}}{{UNIT}};',
						),
					),
					'devices'          => array(
						'desktop' => array(
							'value' => '',
							'unit'  => 'px',
						),
						'tablet'  => array(
							'value' => '',
							'unit'  => 'px',
						),
						'mobile'  => array(
							'value' => '',
							'unit'  => 'px',
						),
					),
					'range'            => array(
						'px' => array(
							'min'  => 0,
							'max'  => 50,
							'step' => 1,
						),
					),
					'dependency'       => array(
						'element' => 'type',
						'value'   => array( 'navigation' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'type'             => 'wd_slider',
					'heading'          => esc_html__( 'Width', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'param_name'       => 'icon_width',
					'selectors'        => array(
						'.wd {{WRAPPER}} .wd-nav-product-cat > li > a .wd-nav-img' => array(
							'--nav-img-width: {{VALUE}}{{UNIT}};',
						),
					),
					'devices'          => array(
						'desktop' => array(
							'value' => '',
							'unit'  => 'px',
						),
						'tablet'  => array(
							'value' => '',
							'unit'  => 'px',
						),
						'mobile'  => array(
							'value' => '',
							'unit'  => 'px',
						),
					),
					'range'            => array(
						'px' => array(
							'min'  => 0,
							'max'  => 50,
							'step' => 1,
						),
					),
					'dependency'       => array(
						'element' => 'type',
						'value'   => array( 'navigation' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				/**
				 * Style - Extra options
				 */
				array(
					'title'      => esc_html__( 'Extra options', 'woodmart' ),
					'group'      => esc_html__( 'Style', 'woodmart' ),
					'type'       => 'woodmart_title_divider',
					'param_name' => 'extra_divider',
				),

				array(
					'heading'          => esc_html__( 'Lazy loading for images', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'type'             => 'woodmart_switch',
					'param_name'       => 'lazy_loading',
					'hint'             => esc_html__( 'Enable lazy loading for images for this element.', 'woodmart' ),
					'true_state'       => 'yes',
					'false_state'      => 'no',
					'default'          => 'no',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'heading'    => esc_html__( 'Extra class name', 'woodmart' ),
					'group'      => esc_html__( 'Style', 'woodmart' ),
					'type'       => 'textfield',
					'param_name' => 'el_class',
					'hint'       => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'woodmart' ),
				),

				/**
				 * Carousel
				 */
				array(
					'title'      => esc_html__( 'Carousel', 'woodmart' ),
					'group'      => esc_html__( 'Carousel', 'woodmart' ),
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'param_name' => 'carousel_divider',
					'dependency' => array(
						'element' => 'style',
						'value'   => array( 'carousel' ),
					),
				),

				/**
				 * Design Options
				 */
				array(
					'heading'    => esc_html__( 'CSS box', 'woodmart' ),
					'group'      => esc_html__( 'Design Options', 'js_composer' ),
					'type'       => 'css_editor',
					'param_name' => 'css',
				),

				woodmart_get_vc_responsive_spacing_map(),

				array(
					'param_name' => 'wd_backdrop_filter',
					'heading'    => esc_html__( 'Backdrop filter', 'woodmart' ),
					'group'      => esc_html__( 'Design Options', 'woodmart' ),
					'type'       => 'wd_backdrop_filter',
					'selectors'  => array(
						'{{WRAPPER}}' => array(
							'backdrop-filter: {{VALUE}};',
							'-webkit-backdrop-filter: {{VALUE}};',
						),
					),
					'class'      => 'xts-col-6',
				),

				// Width option (with dependency Columns option, responsive).
				woodmart_get_responsive_dependency_width_map( 'responsive_tabs' ),
				woodmart_get_responsive_dependency_width_map( 'width_desktop' ),
				woodmart_get_responsive_dependency_width_map( 'custom_width_desktop' ),
				woodmart_get_responsive_dependency_width_map( 'width_tablet' ),
				woodmart_get_responsive_dependency_width_map( 'custom_width_tablet' ),
				woodmart_get_responsive_dependency_width_map( 'width_mobile' ),
				woodmart_get_responsive_dependency_width_map( 'custom_width_mobile' ),
			),
		);
	}
}

// Filters For autocomplete param:
// For suggestion: vc_autocomplete_[shortcode_name]_[param_name]_callback
add_filter( 'vc_autocomplete_woodmart_categories_ids_callback', 'woodmart_product_category_autocomplete_suggester', 10, 1 ); // Get suggestion(find). Must return an array
add_filter( 'vc_autocomplete_woodmart_categories_ids_render', 'woodmart_product_category_render_by_id_exact', 10, 1 );

if ( ! function_exists( 'woodmart_product_category_autocomplete_suggester' ) ) {
	/**
	 * Get Visual Composer map configuration for Categories element.
	 *
	 * @param string $query The query string.
	 * @param bool   $slug Whether to return the slug or the ID.
	 * @return array
	 */
	function woodmart_product_category_autocomplete_suggester( $query, $slug = false ) {
		global $wpdb;
		$cat_id          = (int) $query;
		$query           = trim( $query );
		$post_meta_infos = $wpdb->get_results( // phpcs:ignore. WordPress.DB
			$wpdb->prepare(
				"SELECT a.term_id AS id, b.name as name, b.slug AS slug
						FROM {$wpdb->term_taxonomy} AS a
						INNER JOIN {$wpdb->terms} AS b ON b.term_id = a.term_id
						WHERE a.taxonomy = 'product_cat' AND (a.term_id = '%d' OR b.slug LIKE '%%%s%%' OR b.name LIKE '%%%s%%' )", // phpcs:ignore. WordPress.DB
				$cat_id > 0 ? $cat_id : - 1,
				stripslashes( $query ),
				stripslashes( $query )
			),
			ARRAY_A
		);

		$result = array();
		if ( is_array( $post_meta_infos ) && ! empty( $post_meta_infos ) ) {
			foreach ( $post_meta_infos as $value ) {
				$data          = array();
				$data['value'] = $slug ? $value['slug'] : $value['id'];
				$data['label'] = esc_html__( 'Id', 'woodmart' ) . ': ' .
				$value['id'] .
				( ( strlen( $value['name'] ) > 0 ) ? ' - ' . esc_html__( 'Name', 'woodmart' ) . ': ' .
					$value['name'] : '' ) .
				( ( strlen( $value['slug'] ) > 0 ) ? ' - ' . esc_html__( 'Slug', 'woodmart' ) . ': ' .
					$value['slug'] : '' );
				$result[]      = $data;
			}
		}

		return $result;
	}
}
if ( ! function_exists( 'woodmart_product_category_render_by_id_exact' ) ) {
	/**
	 * Render exact category by ID.
	 *
	 * @param array $query The query array.
	 * @return array
	 */
	function woodmart_product_category_render_by_id_exact( $query ) {
		global $wpdb;
		$query  = $query['value'];
		$cat_id = (int) $query;
		$term   = get_term( $cat_id, 'product_cat' );

		return woodmart_product_category_term_output( $term );
	}
}

if ( ! function_exists( 'woodmart_product_category_term_output' ) ) {
	/**
	 * Render exact category by ID.
	 *
	 * @param object $term The term object.
	 * @return array
	 */
	function woodmart_product_category_term_output( $term ) {
		if ( ! $term || ! is_object( $term ) ) {
			return false;
		}

		$term_slug  = $term->slug;
		$term_title = $term->name;
		$term_id    = $term->term_id;

		$term_slug_display = '';
		if ( ! empty( $term_slug ) ) {
			$term_slug_display = ' - ' . esc_html__( 'Slug', 'woodmart' ) . ': ' . $term_slug;
		}

		$term_title_display = '';
		if ( ! empty( $term_title ) ) {
			$term_title_display = ' - ' . esc_html__( 'Name', 'woodmart' ) . ': ' . $term_title;
		}

		$term_id_display = esc_html__( 'Id', 'woodmart' ) . ': ' . $term_id;

		$data          = array();
		$data['value'] = $term_id;
		$data['label'] = $term_id_display . $term_title_display . $term_slug_display;

		return ! empty( $data ) ? $data : false;
	}
}
