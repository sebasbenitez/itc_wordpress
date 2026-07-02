<?php
/**
 * Text block map.
 *
 * @package woodmart
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' ); // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_get_vc_map_text_block' ) ) {
	/**
	 * Get VC map for text block.
	 *
	 * @return array
	 */
	function woodmart_get_vc_map_text_block() {
		[
			'primary-font' => $primary_font_label,
			'secondary-font' => $secondary_font_label
		] = woodmart_get_current_theme_settings_fonts_labels(
			array(
				'primary-font',
				'secondary-font',
			)
		);

		$typography = woodmart_get_typography_map(
			array(
				'key'      => 'title_decoration_typography',
				'group'    => esc_html__( 'Highlight', 'woodmart' ),
				'selector' => '{{WRAPPER}} u',
			)
		);

		return array(
			'name'        => esc_html__( 'Text block', 'woodmart' ),
			'base'        => 'woodmart_text_block',
			'category'    => woodmart_get_tab_title_category_for_wpb( esc_html__( 'Theme elements', 'woodmart' ) ),
			'description' => esc_html__( 'A block of text', 'woodmart' ),
			'icon'        => WOODMART_ASSETS . '/images/vc-icon/text-block.svg',
			'params'      => array(
				array(
					'type'       => 'woodmart_css_id',
					'param_name' => 'woodmart_css_id',
				),
				/**
				 * Content.
				 */
				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Content', 'woodmart' ),
					'param_name' => 'content_divider',
				),
				array(
					'type'       => 'textarea_html',
					'holder'     => 'div',
					'heading'    => esc_html__( 'Text', 'woodmart' ),
					'param_name' => 'content',
					'std'        => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.',
				),
				array(
					'type'             => 'woodmart_button_set',
					'heading'          => esc_html__( 'Color Scheme', 'woodmart' ),
					'param_name'       => 'text_color_scheme',
					'value'            => array(
						esc_html__( 'Inherit', 'woodmart' ) => 'inherit',
						esc_html__( 'Light', 'woodmart' ) => 'light',
						esc_html__( 'Dark', 'woodmart' )  => 'dark',
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				/**
				 * Paragraph.
				 */
				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Paragraph', 'woodmart' ),
					'param_name' => 'paragraph_divider',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Text font', 'woodmart' ),
					'param_name'       => 'text_font_family',
					'value'            => array(
						esc_html__( 'Default', 'woodmart' ) => 'default',
						$primary_font_label   => 'primary',
						$secondary_font_label => 'alt',
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Font size', 'woodmart' ),
					'param_name'       => 'text_font_size',
					'value'            => array(
						esc_html__( 'Default', 'woodmart' ) => 'default',
						esc_html__( 'Extra Small (14px)', 'woodmart' ) => 'xs',
						esc_html__( 'Small (16px)', 'woodmart' ) => 's',
						esc_html__( 'Medium (18px)', 'woodmart' ) => 'm',
						esc_html__( 'Large (22px)', 'woodmart' ) => 'l',
						esc_html__( 'Extra Large (26px)', 'woodmart' ) => 'xl',
						esc_html__( 'XXL (36px)', 'woodmart' ) => 'xxl',
						esc_html__( 'XXXL (46px)', 'woodmart' ) => 'xxxl',
						esc_html__( 'Custom', 'woodmart' ) => 'custom',
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'woodmart_responsive_size',
					'heading'          => esc_html__( 'Size', 'woodmart' ),
					'param_name'       => 'text_font_size_custom',
					'css_args'         => array(
						'font-size' => array(
							'.wd-text-block',
						),
					),
					'dependency'       => array(
						'element' => 'text_font_size',
						'value'   => array( 'custom' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'woodmart_responsive_size',
					'heading'          => esc_html__( 'Line height', 'woodmart' ),
					'param_name'       => 'text_line_height_custom',
					'css_args'         => array(
						'line-height' => array(
							'.wd-text-block',
						),
					),
					'dependency'       => array(
						'element' => 'text_font_size',
						'value'   => array( 'custom' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'       => 'woodmart_empty_space',
					'param_name' => 'woodmart_empty_space',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Font weight', 'woodmart' ),
					'param_name'       => 'text_font_weight',
					'value'            => array(
						esc_html__( 'Default', 'woodmart' ) => 'default',
						esc_html__( 'Ultra-Light 100', 'woodmart' ) => 100,
						esc_html__( 'Light 200', 'woodmart' ) => 200,
						esc_html__( 'Book 300', 'woodmart' ) => 300,
						esc_html__( 'Normal 400', 'woodmart' ) => 400,
						esc_html__( 'Medium 500', 'woodmart' ) => 500,
						esc_html__( 'Semi-Bold 600', 'woodmart' ) => 600,
						esc_html__( 'Bold 700', 'woodmart' ) => 700,
						esc_html__( 'Extra-Bold 800', 'woodmart' ) => 800,
						esc_html__( 'Ultra-Bold 900', 'woodmart' ) => 900,
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'woodmart_dropdown',
					'heading'          => esc_html__( 'Color', 'woodmart' ),
					'param_name'       => 'text_color',
					'value'            => array(
						esc_html__( 'Default', 'woodmart' ) => 'default',
						esc_html__( 'Title', 'woodmart' )  => 'title',
						esc_html__( 'Primary', 'woodmart' ) => 'primary',
						esc_html__( 'Alternative', 'woodmart' ) => 'alt',
						esc_html__( 'Custom', 'woodmart' ) => 'custom',
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'       => 'woodmart_colorpicker',
					'heading'    => esc_html__( 'Custom Color', 'woodmart' ),
					'param_name' => 'text_color_custom',
					'css_args'   => array(
						'color' => array(
							'.wd-text-block',
						),
					),
					'dependency' => array(
						'element' => 'text_color',
						'value'   => array( 'custom' ),
					),
				),
				/**
				 * Layout
				 */
				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Layout', 'woodmart' ),
					'param_name' => 'layout_divider',
				),
				array(
					'type'             => 'woodmart_image_select',
					'heading'          => esc_html__( 'Text align', 'woodmart' ),
					'param_name'       => 'text_align',
					'value'            => array(
						esc_html__( 'Left', 'woodmart' )   => 'left',
						esc_html__( 'Center', 'woodmart' ) => 'center',
						esc_html__( 'Right', 'woodmart' )  => 'right',
					),
					'images_value'     => array(
						'center' => WOODMART_ASSETS_IMAGES . '/settings/align/center.jpg',
						'left'   => WOODMART_ASSETS_IMAGES . '/settings/align/left.jpg',
						'right'  => WOODMART_ASSETS_IMAGES . '/settings/align/right.jpg',
					),
					'wood_tooltip'     => true,
					'edit_field_class' => 'vc_col-sm-6 vc_column title-align',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Text width', 'woodmart' ),
					'param_name'       => 'content_width',
					'value'            => array(
						'100%'                             => '100',
						'90%'                              => '90',
						'80%'                              => '80',
						'70%'                              => '70',
						'60%'                              => '60',
						'50%'                              => '50',
						'40%'                              => '40',
						'30%'                              => '30',
						'20%'                              => '20',
						'10%'                              => '10',
						esc_html__( 'Custom', 'woodmart' ) => 'custom',
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'woodmart_button_set',
					'heading'          => esc_html__( 'Custom text width', 'woodmart' ),
					'param_name'       => 'custom_content_width',
					'tabs'             => true,
					'value'            => array(
						esc_html__( 'Desktop', 'woodmart' ) => 'desktop',
						esc_html__( 'Tablet', 'woodmart' ) => 'tablet',
						esc_html__( 'Mobile', 'woodmart' ) => 'mobile',
					),
					'default'          => 'desktop',
					'edit_field_class' => 'wd-res-control wd-custom-width vc_col-sm-12 vc_column',
					'dependency'       => array(
						'element' => 'content_width',
						'value'   => array( 'custom' ),
					),
				),
				array(
					'type'             => 'woodmart_slider',
					'param_name'       => 'content_desktop_width',
					'min'              => '0',
					'max'              => '1000',
					'step'             => '1',
					'default'          => '600',
					'units'            => 'px',
					'edit_field_class' => 'wd-res-item vc_col-sm-12 vc_column',
					'css_args'         => array(
						'--wd-max-width' => array(
							'',
						),
					),
					'css_params'       => array(
						'device' => 'desktop',
					),
					'wd_dependency'    => array(
						'element' => 'custom_content_width',
						'value'   => array( 'desktop' ),
					),
					'dependency'       => array(
						'element' => 'content_width',
						'value'   => array( 'custom' ),
					),
				),
				array(
					'type'             => 'woodmart_slider',
					'param_name'       => 'content_tablet_width',
					'min'              => '0',
					'max'              => '1000',
					'step'             => '1',
					'default'          => '0',
					'units'            => 'px',
					'edit_field_class' => 'wd-res-item vc_col-sm-12 vc_column',
					'css_args'         => array(
						'--wd-max-width' => array(
							'',
						),
					),
					'css_params'       => array(
						'device' => 'tablet',
					),
					'wd_dependency'    => array(
						'element' => 'custom_content_width',
						'value'   => array( 'tablet' ),
					),
					'dependency'       => array(
						'element' => 'content_width',
						'value'   => array( 'custom' ),
					),
				),
				array(
					'type'             => 'woodmart_slider',
					'param_name'       => 'content_mobile_width',
					'min'              => '0',
					'max'              => '1000',
					'step'             => '1',
					'default'          => '0',
					'units'            => 'px',
					'edit_field_class' => 'wd-res-item vc_col-sm-12 vc_column',
					'css_args'         => array(
						'--wd-max-width' => array(
							'',
						),
					),
					'css_params'       => array(
						'device' => 'mobile',
					),
					'wd_dependency'    => array(
						'element' => 'custom_content_width',
						'value'   => array( 'mobile' ),
					),
					'dependency'       => array(
						'element' => 'content_width',
						'value'   => array( 'custom' ),
					),
				),
				woodmart_get_vc_display_inline_map(),

				/**
				 * Advanced highlight.
				 */
				array(
					'group'      => esc_html__( 'Highlight', 'woodmart' ),
					'type'       => 'woodmart_empty_space',
					'param_name' => 'title_decoration_empty_space',
				),

				$typography['font_family'],
				$typography['font_size'],
				$typography['font_weight'],
				$typography['text_transform'],
				$typography['font_style'],
				$typography['text_decoration'],
				$typography['line_height'],

				array(
					'group'      => esc_html__( 'Highlight', 'woodmart' ),
					'type'       => 'woodmart_empty_space',
					'param_name' => 'title_decoration_empty_space_color_type',
				),

				array(
					'type'             => 'woodmart_button_set',
					'heading'          => esc_html__( 'Color type', 'woodmart' ),
					'param_name'       => 'title_decoration_color_type',
					'value'            => array(
						esc_html__( 'Default', 'woodmart' ) => '',
						esc_html__( 'Classic', 'woodmart' ) => 'classic',
						esc_html__( 'Gradient', 'woodmart' ) => 'gradient',
					),
					'std'              => '',
					'group'            => esc_html__( 'Highlight', 'woodmart' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column',
				),
				array(
					'type'             => 'wd_colorpicker',
					'heading'          => esc_html__( 'Color', 'woodmart' ),
					'group'            => esc_html__( 'Highlight', 'woodmart' ),
					'param_name'       => 'title_decoration_color',
					'selectors'        => array(
						'{{WRAPPER}} u' => array(
							'color: {{VALUE}};',
						),
					),
					'dependency'       => array(
						'element' => 'title_decoration_color_type',
						'value'   => array( 'classic' ),
					),
					'edit_field_class' => 'vc_col-sm-12 vc_column',
				),
				array(
					'type'       => 'woodmart_gradient',
					'heading'    => esc_html__( 'Highlight text gradient', 'woodmart' ),
					'group'      => esc_html__( 'Highlight', 'woodmart' ),
					'param_name' => 'title_decoration_gradient_text',
					'selectors'  => array(
						'{{WRAPPER}} u' => array(
							'background-image: {{VALUE}};',
							'background-clip: text;',
							'background-color: currentColor;',
							'-webkit-background-clip: text;',
							'-webkit-text-fill-color: transparent;',
						),
					),
					'dependency' => array(
						'element' => 'title_decoration_color_type',
						'value'   => array( 'gradient' ),
					),
				),
				array(
					'type'             => 'woodmart_button_set',
					'heading'          => esc_html__( 'Background type', 'woodmart' ),
					'param_name'       => 'title_decoration_bg_type',
					'group'            => esc_html__( 'Highlight', 'woodmart' ),
					'value'            => array(
						esc_html__( 'Default', 'woodmart' ) => '',
						esc_html__( 'Classic', 'woodmart' ) => 'classic',
						esc_html__( 'Gradient', 'woodmart' ) => 'gradient',
					),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-12 vc_column',
				),
				array(
					'group'      => esc_html__( 'Highlight', 'woodmart' ),
					'type'       => 'woodmart_empty_space',
					'param_name' => 'title_decoration_empty_space',
				),
				array(
					'type'             => 'wd_colorpicker',
					'heading'          => esc_html__( 'Background color', 'woodmart' ),
					'group'            => esc_html__( 'Highlight', 'woodmart' ),
					'param_name'       => 'title_decoration_background_color',
					'selectors'        => array(
						'{{WRAPPER}} u' => array(
							'background-color: {{VALUE}};',
						),
					),
					'dependency'       => array(
						'element' => 'title_decoration_bg_type',
						'value'   => array( 'classic' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'attach_image',
					'heading'          => esc_html__( 'Background image', 'woodmart' ),
					'param_name'       => 'title_decoration_bg_image',
					'hint'             => esc_html__( 'Select images from media library.', 'woodmart' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'group'            => esc_html__( 'Highlight', 'woodmart' ),
					'dependency'       => array(
						'element' => 'title_decoration_bg_type',
						'value'   => array( 'classic' ),
					),
				),
				array(
					'type'             => 'textfield',
					'heading'          => esc_html__( 'Background image resolution', 'woodmart' ),
					'param_name'       => 'title_decoration_bg_image_size',
					'hint'             => esc_html__( 'Enter image resolution. Example: \'thumbnail\', \'medium\', \'large\', \'full\' or other sizes defined by current theme. Alternatively enter image size in pixels: 200x100 (Width x Height). Leave empty to use \'thumbnail\' size.', 'woodmart' ),
					'group'            => esc_html__( 'Highlight', 'woodmart' ),
					'dependency'       => array(
						'element' => 'title_decoration_bg_type',
						'value'   => array( 'classic' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Background position', 'woodmart' ),
					'param_name'       => 'title_decoration_bg_image_position',
					'group'            => esc_html__( 'Highlight', 'woodmart' ),
					'value'            => array(
						esc_html__( 'Default', 'woodmart' ) => '',
						esc_html__( 'Center Center', 'woodmart' ) => 'center center',
						esc_html__( 'Center Left', 'woodmart' ) => 'center left',
						esc_html__( 'Center Right', 'woodmart' ) => 'center right',
						esc_html__( 'Top Center', 'woodmart' ) => 'top center',
						esc_html__( 'Top Left', 'woodmart' ) => 'top left',
						esc_html__( 'Top Right', 'woodmart' ) => 'top right',
						esc_html__( 'Bottom Center', 'woodmart' ) => 'bottom center',
						esc_html__( 'Bottom Left', 'woodmart' ) => 'bottom left',
						esc_html__( 'Bottom Right', 'woodmart' ) => 'bottom right',
					),
					'dependency'       => array(
						'element' => 'title_decoration_bg_type',
						'value'   => array( 'classic' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Background repeat', 'woodmart' ),
					'param_name'       => 'title_decoration_bg_image_repeat',
					'group'            => esc_html__( 'Highlight', 'woodmart' ),
					'value'            => array(
						esc_html__( 'Default', 'woodmart' ) => '',
						esc_html__( 'No-repeat', 'woodmart' ) => 'no-repeat',
						esc_html__( 'Repeat', 'woodmart' ) => 'repeat',
						esc_html__( 'Repeat-x', 'woodmart' ) => 'repeat-x',
						esc_html__( 'Repeat-y', 'woodmart' ) => 'repeat-y',
					),
					'dependency'       => array(
						'element' => 'title_decoration_bg_type',
						'value'   => array( 'classic' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Background size', 'woodmart' ),
					'param_name'       => 'title_decoration_bg_image_sizes',
					'group'            => esc_html__( 'Highlight', 'woodmart' ),
					'value'            => array(
						esc_html__( 'Default', 'woodmart' ) => '',
						esc_html__( 'Cover', 'woodmart' ) => 'cover',
						esc_html__( 'Contain', 'woodmart' ) => 'contain',
					),
					'dependency'       => array(
						'element' => 'title_decoration_bg_type',
						'value'   => array( 'classic' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'       => 'woodmart_gradient',
					'heading'    => esc_html__( 'Background gradient', 'woodmart' ),
					'group'      => esc_html__( 'Highlight', 'woodmart' ),
					'param_name' => 'title_decoration_gradient_bg',
					'selectors'  => array(
						'{{WRAPPER}} u' => array(
							'background-image: {{VALUE}};',
						),
					),
					'dependency' => array(
						'element' => 'title_decoration_bg_type',
						'value'   => array( 'gradient' ),
					),
				),

				array(
					'heading'    => esc_html__( 'Border type', 'woodmart' ),
					'group'      => esc_html__( 'Highlight', 'woodmart' ),
					'type'       => 'wd_select',
					'param_name' => 'title_decoration_border_type',
					'style'      => 'select',
					'selectors'  => array(
						'{{WRAPPER}} u' => array(
							'border-style: {{VALUE}};',
						),
					),
					'devices'    => array(
						'desktop' => array(
							'value' => '',
						),
					),
					'value'      => array(
						esc_html__( 'Default', 'woodmart' ) => '',
						esc_html__( 'None', 'woodmart' )   => 'none',
						esc_html__( 'Solid', 'woodmart' )  => 'solid',
						esc_html__( 'Dotted', 'woodmart' ) => 'dotted',
						esc_html__( 'Double', 'woodmart' ) => 'double',
						esc_html__( 'Dashed', 'woodmart' ) => 'dashed',
						esc_html__( 'Groove', 'woodmart' ) => 'groove',
					),
				),
				array(
					'heading'          => esc_html__( 'Border width', 'woodmart' ),
					'group'            => esc_html__( 'Highlight', 'woodmart' ),
					'type'             => 'wd_dimensions',
					'param_name'       => 'title_decoration_border_width',
					'selectors'        => array(
						'{{WRAPPER}} u' => array(
							'border-top-width: {{TOP}}px;',
							'border-right-width: {{RIGHT}}px;',
							'border-bottom-width: {{BOTTOM}}px;',
							'border-left-width: {{LEFT}}px;',
						),
					),
					'devices'          => array(
						'desktop' => array(
							'unit' => 'px',
						),
					),
					'range'            => array(
						'px' => array(),
					),
					'dependency'       => array(
						'element'            => 'title_decoration_border_type',
						'value_not_equal_to' => array( '', 'eyJkZXZpY2VzIjp7ImRlc2t0b3AiOnsidmFsdWUiOiJub25lIn19fQ==' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'heading'          => esc_html__( 'Border color', 'woodmart' ),
					'group'            => esc_html__( 'Highlight', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'param_name'       => 'title_decoration_border_color',
					'selectors'        => array(
						'{{WRAPPER}} u' => array(
							'border-color: {{VALUE}};',
						),
					),
					'dependency'       => array(
						'element'            => 'title_decoration_border_type',
						'value_not_equal_to' => array( '', 'eyJkZXZpY2VzIjp7ImRlc2t0b3AiOnsidmFsdWUiOiJub25lIn19fQ==' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'heading'    => esc_html__( 'Border radius', 'woodmart' ),
					'group'      => esc_html__( 'Highlight', 'woodmart' ),
					'type'       => 'wd_dimensions',
					'param_name' => 'title_decoration_border_radius',
					'selectors'  => array(
						'{{WRAPPER}} u' => array(
							'border-top-left-radius: {{TOP}}{{UNIT}};',
							'border-top-right-radius: {{RIGHT}}{{UNIT}};',
							'border-bottom-right-radius: {{BOTTOM}}{{UNIT}};',
							'border-bottom-left-radius: {{LEFT}}{{UNIT}};',
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
					'type'             => 'wd_box_shadow',
					'heading'          => esc_html__( 'Box shadow', 'woodmart' ),
					'group'            => esc_html__( 'Highlight', 'woodmart' ),
					'param_name'       => 'title_decoration_box_shadow',
					'selectors'        => array(
						'{{WRAPPER}} u' => array(
							'box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}};',
						),
					),
					'edit_field_class' => 'vc_col-sm-12 vc_column',
					'default'          => array(
						'horizontal' => '0',
						'vertical'   => '0',
						'blur'       => '0',
						'spread'     => '0',
						'color'      => '',
					),
				),
				array(
					'heading'    => esc_html__( 'Padding', 'woodmart' ),
					'group'      => esc_html__( 'Highlight', 'woodmart' ),
					'type'       => 'wd_dimensions',
					'param_name' => 'title_decoration_padding',
					'selectors'  => array(
						'{{WRAPPER}} u' => array(
							'padding-top: {{TOP}}{{UNIT}};',
							'padding-left: {{LEFT}}{{UNIT}};',
							'padding-right: {{RIGHT}}{{UNIT}};',
							'padding-bottom: {{BOTTOM}}{{UNIT}};',
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

				woodmart_parallax_scroll_map( 'parallax_scroll' ),
				woodmart_parallax_scroll_map( 'scroll_x' ),
				woodmart_parallax_scroll_map( 'scroll_y' ),
				woodmart_parallax_scroll_map( 'scroll_z' ),
				woodmart_parallax_scroll_map( 'scroll_smooth' ),

				woodmart_get_vc_animation_map( 'wd_animation' ),
				woodmart_get_vc_animation_map( 'wd_animation_delay' ),
				woodmart_get_vc_animation_map( 'wd_animation_duration' ),

				array(
					'type'       => 'textfield',
					'heading'    => esc_html__( 'Extra class name', 'woodmart' ),
					'param_name' => 'extra_classes',
					'hint'       => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'woodmart' ),
				),

				woodmart_get_vc_responsive_visible_map( 'responsive_tabs_hide' ),
				woodmart_get_vc_responsive_visible_map( 'wd_hide_on_desktop' ),
				woodmart_get_vc_responsive_visible_map( 'wd_hide_on_tablet' ),
				woodmart_get_vc_responsive_visible_map( 'wd_hide_on_mobile' ),
			),
		);
	}
}
