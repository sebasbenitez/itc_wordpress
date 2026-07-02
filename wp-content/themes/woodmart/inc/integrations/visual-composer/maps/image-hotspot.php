<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Image Hotspot element.
 *
 * @package woodmart.
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

if ( ! function_exists( 'woodmart_get_vc_map_image_hotspot' ) ) {
	/**
	 * Image Hotspot map
	 *
	 * @return array
	 */
	function woodmart_get_vc_map_image_hotspot() {
		return array(
			'name'                    => esc_html__( 'Image Hotspot', 'woodmart' ),
			'base'                    => 'woodmart_image_hotspot',
			'class'                   => '',
			'category'                => woodmart_get_tab_title_category_for_wpb( esc_html__( 'Theme elements', 'woodmart' ) ),
			'description'             => esc_html__( 'Add hotspots with products to the image', 'woodmart' ),
			'icon'                    => WOODMART_ASSETS . '/images/vc-icon/image-map.svg',
			'as_parent'               => array( 'only' => 'woodmart_hotspot' ),
			'content_element'         => true,
			'show_settings_on_create' => true,
			'params'                  => array(
				array(
					'type'       => 'woodmart_css_id',
					'param_name' => 'woodmart_css_id',
				),
				/**
				 * Image
				 */
				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Background', 'woodmart' ),
					'param_name' => 'image_divider',
				),
				array(
					'type'       => 'woodmart_button_set',
					'heading'    => esc_html__( 'Source', 'woodmart' ),
					'param_name' => 'source_type',
					'value'      => array(
						esc_html__( 'Image', 'woodmart' ) => 'image',
						esc_html__( 'Video', 'woodmart' ) => 'video',
					),
					'default'    => 'image',
				),
				array(
					'type'            => 'wd_upload',
					'heading'         => esc_html__( 'Video', 'woodmart' ),
					'param_name'      => 'video',
					'attachment_type' => 'video',
					'value'           => '',
					'hint'            => esc_html__( 'Select video from media library.', 'woodmart' ),
					'dependency'      => array(
						'element' => 'source_type',
						'value'   => array( 'video' ),
					),
				),
				array(
					'type'             => 'attach_image',
					'heading'          => esc_html__( 'Fallback image', 'woodmart' ),
					'param_name'       => 'video_poster',
					'holder'           => 'img',
					'value'            => '',
					'hint'             => esc_html__( 'Select images from media library.', 'woodmart' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'dependency'       => array(
						'element' => 'source_type',
						'value'   => array( 'video' ),
					),
				),
				array(
					'type'             => 'textfield',
					'heading'          => esc_html__( 'Fallback image resolution', 'woodmart' ),
					'param_name'       => 'video_poster_size',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'description'      => esc_html__( 'Example: \'thumbnail\', \'medium\', \'large\', \'full\' or enter image size in pixels: \'200x100\'.', 'woodmart' ),
					'dependency'       => array(
						'element' => 'source_type',
						'value'   => array( 'video' ),
					),
				),
				array(
					'type'             => 'attach_image',
					'heading'          => esc_html__( 'Image', 'woodmart' ),
					'param_name'       => 'img',
					'holder'           => 'img',
					'value'            => '',
					'hint'             => esc_html__( 'Select images from media library.', 'woodmart' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'dependency'       => array(
						'element' => 'source_type',
						'value'   => array( 'image' ),
					),
				),
				array(
					'type'             => 'textfield',
					'heading'          => esc_html__( 'Image resolution', 'woodmart' ),
					'param_name'       => 'img_size',
					'hint'             => esc_html__( 'Enter image resolution. Example: \'thumbnail\', \'medium\', \'large\', \'full\' or other sizes defined by current theme. Alternatively enter image size in pixels: 200x100 (Width x Height). Leave empty to use \'thumbnail\' size.', 'woodmart' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'dependency'       => array(
						'element' => 'source_type',
						'value'   => array( 'image' ),
					),
				),
				/**
				 * Icon
				 */
				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Icon', 'woodmart' ),
					'param_name' => 'icon_divider',
				),
				array(
					'type'             => 'woodmart_image_select',
					'heading'          => esc_html__( 'Icon style', 'woodmart' ),
					'param_name'       => 'icon',
					'value'            => array(
						esc_html__( 'Default', 'woodmart' ) => 'default',
						esc_html__( 'Alternative', 'woodmart' ) => 'alt',
					),
					'images_value'     => array(
						'default' => WOODMART_ASSETS_IMAGES . '/settings/image-hotspot/default.jpg',
						'alt'     => WOODMART_ASSETS_IMAGES . '/settings/image-hotspot/alt.jpg',
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Icon position', 'woodmart' ),
					'param_name'       => 'icon_position',
					'value'            => array(
						esc_html__( 'Static', 'woodmart' ) => 'static',
						esc_html__( 'On hover', 'woodmart' ) => 'hover',
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'       => 'dropdown',
					'heading'    => esc_html__( 'Hotspot action', 'woodmart' ),
					'param_name' => 'action',
					'value'      => array(
						esc_html__( 'Hover', 'woodmart' ) => 'hover',
						esc_html__( 'Click', 'woodmart' ) => 'click',
					),
					'hint'       => esc_html__( 'Open hotspot content on click or hover', 'woodmart' ),
				),
				array(
					'heading'          => esc_html__( 'Primary color', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'param_name'       => 'primary_color',
					'selectors'        => array(
						'{{WRAPPER}} .wd-image-hotspot' => array(
							'--hotspot-primary: {{VALUE}};',
						),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'heading'          => esc_html__( 'Secondary color', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'param_name'       => 'secondary_color',
					'selectors'        => array(
						'{{WRAPPER}} .wd-image-hotspot' => array(
							'--hotspot-secondary: {{VALUE}};',
						),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				/**
				 * Extra
				 */
				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Extra options', 'woodmart' ),
					'param_name' => 'extra_divider',
				),
				array(
					'type'       => 'woodmart_button_set',
					'heading'    => esc_html__( 'Color Scheme', 'woodmart' ),
					'param_name' => 'woodmart_color_scheme',
					'value'      => array(
						esc_html__( 'Inherit', 'woodmart' ) => '',
						esc_html__( 'Light', 'woodmart' ) => 'light',
						esc_html__( 'Dark', 'woodmart' )  => 'dark',
					),
				),
				array(
					'type'       => 'textfield',
					'heading'    => esc_html__( 'Extra class name', 'woodmart' ),
					'param_name' => 'el_class',
					'hint'       => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'woodmart' ),
				),
				/**
				 * Design Options
				 */
				array(
					'type'       => 'css_editor',
					'heading'    => esc_html__( 'CSS box', 'woodmart' ),
					'param_name' => 'css',
					'group'      => esc_html__( 'Design Options', 'js_composer' ),
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
			),
			'js_view'                 => 'VcColumnView',
		);
	}
}

if ( ! function_exists( 'woodmart_get_vc_map_hotspot' ) ) {
	/**
	 * Hotspot map.
	 *
	 * @return array
	 */
	function woodmart_get_vc_map_hotspot() {
		return array(
			'name'            => esc_html__( 'Hotspot', 'woodmart' ),
			'base'            => 'woodmart_hotspot',
			'as_child'        => array( 'only' => 'woodmart_image_hotspot' ),
			'content_element' => true,
			'category'        => woodmart_get_tab_title_category_for_wpb( esc_html__( 'Theme elements', 'woodmart' ) ),
			'icon'            => WOODMART_ASSETS . '/images/vc-icon/image-map-hotspot.svg',
			'params'          => array(
				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Position', 'woodmart' ),
					'param_name' => 'title_divider',
				),
				array(
					'type'       => 'woodmart_image_hotspot',
					'heading'    => esc_html__( 'Hotspot position', 'woodmart' ),
					'param_name' => 'hotspot',
				),
				/**
				 * Content
				 */
				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Content', 'woodmart' ),
					'param_name' => 'content_divider',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Hotspot content', 'woodmart' ),
					'param_name'       => 'hotspot_type',
					'value'            => array(
						esc_html__( 'Product', 'woodmart' ) => 'product',
						esc_html__( 'Text', 'woodmart' ) => 'text',
					),
					'hint'             => esc_html__( 'You can display any product or custom text in the hotspot content.', 'woodmart' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Hotspot dropdown side', 'woodmart' ),
					'param_name'       => 'hotspot_dropdown_side',
					'value'            => array(
						esc_html__( 'Left', 'woodmart' )   => 'left',
						esc_html__( 'Right', 'woodmart' )  => 'right',
						esc_html__( 'Top', 'woodmart' )    => 'top',
						esc_html__( 'Bottom', 'woodmart' ) => 'bottom',
					),
					'hint'             => esc_html__( 'Show the content on left or right side, top or bottom.', 'woodmart' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				/**
				 * Product
				 */
				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Product', 'woodmart' ),
					'param_name' => 'product_divider',
					'dependency' => array(
						'element' => 'hotspot_type',
						'value'   => array( 'product' ),
					),
				),
				array(
					'type'       => 'autocomplete',
					'heading'    => esc_html__( 'Select product', 'woodmart' ),
					'param_name' => 'product_id',
					'hint'       => esc_html__( 'Add products by title.', 'woodmart' ),
					'settings'   => array(
						'multiple' => false,
						'sortable' => false,
						'groups'   => true,
					),
					'dependency' => array(
						'element' => 'hotspot_type',
						'value'   => array( 'product' ),
					),
				),
				/**
				 * Text
				 */
				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Text', 'woodmart' ),
					'param_name' => 'text_divider',
					'dependency' => array(
						'element' => 'hotspot_type',
						'value'   => array( 'text' ),
					),
				),
				array(
					'type'       => 'textfield',
					'holder'     => 'div',
					'heading'    => esc_html__( 'Title', 'woodmart' ),
					'param_name' => 'title',
					'dependency' => array(
						'element' => 'hotspot_type',
						'value'   => array( 'text' ),
					),
				),
				array(
					'type'             => 'attach_image',
					'heading'          => esc_html__( 'Image', 'woodmart' ),
					'param_name'       => 'img',
					'value'            => '',
					'hint'             => esc_html__( 'Select images from media library.', 'woodmart' ),
					'dependency'       => array(
						'element' => 'hotspot_type',
						'value'   => array( 'text' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'textfield',
					'heading'          => esc_html__( 'Image resolution', 'woodmart' ),
					'param_name'       => 'img_size',
					'hint'             => esc_html__( 'Enter image resolution. Example: \'thumbnail\', \'medium\', \'large\', \'full\' or other sizes defined by current theme. Alternatively enter image size in pixels: 200x100 (Width x Height). Leave empty to use \'thumbnail\' size.', 'woodmart' ),
					'dependency'       => array(
						'element' => 'hotspot_type',
						'value'   => array( 'text' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'textfield',
					'heading'          => esc_html__( 'Link text', 'woodmart' ),
					'param_name'       => 'link_text',
					'dependency'       => array(
						'element' => 'hotspot_type',
						'value'   => array( 'text' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'vc_link',
					'heading'          => esc_html__( 'Link', 'woodmart' ),
					'param_name'       => 'link',
					'dependency'       => array(
						'element' => 'hotspot_type',
						'value'   => array( 'text' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'       => 'textarea_html',
					'holder'     => 'div',
					'heading'    => esc_html__( 'Content', 'woodmart' ),
					'param_name' => 'content',
					'dependency' => array(
						'element' => 'hotspot_type',
						'value'   => array( 'text' ),
					),
				),
				/**
				 * Extra
				 */
				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Extra options', 'woodmart' ),
					'param_name' => 'extra_divider',
				),
				array(
					'type'       => 'textfield',
					'heading'    => esc_html__( 'Extra class name', 'woodmart' ),
					'param_name' => 'el_class',
					'hint'       => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'woodmart' ),
				),
			),
		);
	}
}

// A must for container functionality, replace Wbc_Item with your base name from mapping for parent container
if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
	class WPBakeryShortCode_woodmart_image_hotspot extends WPBakeryShortCodesContainer {} // phpcs:ignore
}

// Replace Wbc_Inner_Item with your base name from mapping for nested element
if ( class_exists( 'WPBakeryShortCode' ) ) {
	class WPBakeryShortCode_woodmart_hotspot extends WPBakeryShortCode {} // phpcs:ignore
}

// WC 3.6.0
if ( function_exists( 'WC' ) && version_compare( WC()->version, '3.6.0', '<' ) ) {
	add_filter( 'vc_autocomplete_woodmart_hotspot_product_id_callback', 'woodmart_product_id_autocomplete_suggester', 10, 1 );
	add_filter( 'vc_autocomplete_woodmart_hotspot_product_id_render', 'woodmart_product_id_autocomplete_render', 10, 1 );
} else {
	add_filter( 'vc_autocomplete_woodmart_hotspot_product_id_callback', 'woodmart_product_id_autocomplete_suggester_new', 10, 1 );
	add_filter( 'vc_autocomplete_woodmart_hotspot_product_id_render', 'woodmart_product_id_autocomplete_render', 10, 1 );
}