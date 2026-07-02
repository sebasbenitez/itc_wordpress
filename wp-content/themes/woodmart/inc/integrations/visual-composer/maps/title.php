<?php
/**
 * Section title element map
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}


if ( ! function_exists( 'woodmart_get_vc_map_title' ) ) {
	/**
	 * Get VC map for title
	 *
	 * @return array
	 */
	function woodmart_get_vc_map_title() {
		[
			'text-font'      => $text_font_label,
			'secondary-font' => $secondary_font_label,
		] = woodmart_get_current_theme_settings_fonts_labels(
			array(
				'secondary-font',
				'text-font',
			)
		);

		$typography = woodmart_get_typography_map(
			array(
				'key'        => 'title_decoration_typography',
				'group'      => esc_html__( 'Title', 'woodmart' ),
				'selector'   => '{{WRAPPER}} u',
				'dependency' => array(
					'element' => 'title_decoration_style',
					'value'   => array( 'default' ),
				),
			)
		);

		return array(
			'name'        => esc_html__( 'Section title', 'woodmart' ),
			'base'        => 'woodmart_title',
			'category'    => woodmart_get_tab_title_category_for_wpb( esc_html__( 'Theme elements', 'woodmart' ) ),
			'description' => esc_html__( 'Styled title for sections', 'woodmart' ),
			'icon'        => WOODMART_ASSETS . '/images/vc-icon/section-title.svg',
			'params'      => array(
				array(
					'type'       => 'woodmart_css_id',
					'param_name' => 'woodmart_css_id',
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
					'heading'          => esc_html__( 'Align', 'woodmart' ),
					'param_name'       => 'align',
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
					'std'              => 'center',
					'wood_tooltip'     => true,
					'edit_field_class' => 'vc_col-sm-6 vc_column title-align',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Width', 'woodmart' ),
					'param_name'       => 'title_width',
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
					'heading'          => esc_html__( 'Custom title width', 'woodmart' ),
					'param_name'       => 'custom_title_width',
					'tabs'             => true,
					'value'            => array(
						esc_html__( 'Desktop', 'woodmart' ) => 'desktop',
						esc_html__( 'Tablet', 'woodmart' ) => 'tablet',
						esc_html__( 'Mobile', 'woodmart' ) => 'mobile',
					),
					'default'          => 'desktop',
					'edit_field_class' => 'wd-res-control vc_col-sm-12 vc_column wd-custom-width',
					'dependency'       => array(
						'element' => 'title_width',
						'value'   => array( 'custom' ),
					),
				),
				array(
					'type'             => 'woodmart_slider',
					'param_name'       => 'title_desktop_width',
					'min'              => '0',
					'max'              => '1000',
					'step'             => '1',
					'default'          => '600',
					'units'            => 'px',
					'edit_field_class' => 'wd-res-control vc_col-sm-12 vc_column',
					'css_args'         => array(
						'--wd-max-width' => array(
							'',
						),
					),
					'css_params'       => array(
						'device' => 'desktop',
					),
					'wd_dependency'    => array(
						'element' => 'custom_title_width',
						'value'   => array( 'desktop' ),
					),
					'dependency'       => array(
						'element' => 'title_width',
						'value'   => array( 'custom' ),
					),
				),
				array(
					'type'             => 'woodmart_slider',
					'param_name'       => 'title_tablet_width',
					'min'              => '0',
					'max'              => '1000',
					'step'             => '1',
					'default'          => '0',
					'units'            => 'px',
					'edit_field_class' => 'wd-res-control vc_col-sm-12 vc_column',
					'css_args'         => array(
						'--wd-max-width' => array(
							'',
						),
					),
					'css_params'       => array(
						'device' => 'tablet',
					),
					'wd_dependency'    => array(
						'element' => 'custom_title_width',
						'value'   => array( 'tablet' ),
					),
					'dependency'       => array(
						'element' => 'title_width',
						'value'   => array( 'custom' ),
					),
				),
				array(
					'type'             => 'woodmart_slider',
					'param_name'       => 'title_mobile_width',
					'min'              => '0',
					'max'              => '1000',
					'step'             => '1',
					'default'          => '0',
					'units'            => 'px',
					'edit_field_class' => 'wd-res-control vc_col-sm-12 vc_column',
					'css_args'         => array(
						'--wd-max-width' => array(
							'',
						),
					),
					'css_params'       => array(
						'device' => 'mobile',
					),
					'wd_dependency'    => array(
						'element' => 'custom_title_width',
						'value'   => array( 'mobile' ),
					),
					'dependency'       => array(
						'element' => 'title_width',
						'value'   => array( 'custom' ),
					),
				),
				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Style', 'woodmart' ),
					'param_name' => 'layout_divider',
				),
				array(
					'type'         => 'woodmart_image_select',
					'heading'      => esc_html__( 'Style', 'woodmart' ),
					'param_name'   => 'style',
					'value'        => array(
						esc_html__( 'Default', 'woodmart' ) => 'default',
						esc_html__( 'Simple', 'woodmart' ) => 'simple',
						esc_html__( 'Bordered', 'woodmart' ) => 'bordered',
						esc_html__( 'Underline', 'woodmart' ) => 'underlined',
						esc_html__( 'Underline 2', 'woodmart' ) => 'underlined-2',
						esc_html__( 'Overlined', 'woodmart' ) => 'overlined',
						esc_html__( 'Shadow', 'woodmart' ) => 'shadow',
						esc_html__( 'With image', 'woodmart' ) => 'image',
					),
					'images_value' => array(
						'default'      => WOODMART_ASSETS_IMAGES . '/settings/title-style/default.png',
						'simple'       => WOODMART_ASSETS_IMAGES . '/settings/title-style/simple.png',
						'bordered'     => WOODMART_ASSETS_IMAGES . '/settings/title-style/bordered.png',
						'underlined'   => WOODMART_ASSETS_IMAGES . '/settings/title-style/underlined.png',
						'underlined-2' => WOODMART_ASSETS_IMAGES . '/settings/title-style/underlined-2.png',
						'overlined'    => WOODMART_ASSETS_IMAGES . '/settings/title-style/overlined.png',
						'shadow'       => WOODMART_ASSETS_IMAGES . '/settings/title-style/shadow.png',
						'image'        => WOODMART_ASSETS_IMAGES . '/settings/title-style/image.png',
					),
				),
				array(
					'type'             => 'woodmart_dropdown',
					'heading'          => esc_html__( 'Predefined color scheme', 'woodmart' ),
					'param_name'       => 'color',
					'value'            => woodmart_section_title_color_variation(),
					'style'            => array(
						'default' => '#989898',
						'primary' => woodmart_get_color_value( 'primary-color', '#7eb934' ),
						'alt'     => woodmart_get_color_value( 'secondary-color', '#fbbc34' ),
						'black'   => '#2d2a2a',
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				woodmart_title_gradient_picker(),
				/**
				 * Image
				 */
				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Image', 'woodmart' ),
					'param_name' => 'image_divider',
					'dependency' => array(
						'element' => 'style',
						'value'   => array( 'image' ),
					),
				),
				array(
					'type'             => 'attach_image',
					'heading'          => esc_html__( 'Image', 'woodmart' ),
					'param_name'       => 'image',
					'value'            => '',
					'hint'             => esc_html__( 'Select image from media library.', 'woodmart' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'dependency'       => array(
						'element' => 'style',
						'value'   => array( 'image' ),
					),
				),
				array(
					'type'             => 'textfield',
					'heading'          => esc_html__( 'Image resolution', 'woodmart' ),
					'param_name'       => 'img_size',
					'hint'             => esc_html__( 'Enter image resolution. Example: \'thumbnail\', \'medium\', \'large\', \'full\' or other sizes defined by current theme. Alternatively enter image size in pixels: 200x100 (Width x Height). Leave empty to use \'thumbnail\' size.', 'woodmart' ),
					'dependency'       => array(
						'element' => 'style',
						'value'   => array( 'image' ),
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
				( function_exists( 'vc_map_add_css_animation' ) ) ? vc_map_add_css_animation( true ) : '',

				woodmart_get_vc_animation_map( 'wd_animation' ),
				woodmart_get_vc_animation_map( 'wd_animation_delay' ),
				woodmart_get_vc_animation_map( 'wd_animation_duration' ),

				array(
					'type'       => 'textfield',
					'heading'    => esc_html__( 'Extra class name', 'woodmart' ),
					'param_name' => 'el_class',
					'hint'       => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'woodmart' ),
				),
				/**
				 * Title
				 */
				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Title', 'woodmart' ),
					'group'      => esc_html__( 'Title', 'woodmart' ),
					'param_name' => 'title_divider',
				),
				array(
					'type'       => 'textarea',
					'holder'     => 'div',
					'heading'    => esc_html__( 'Title', 'woodmart' ),
					'group'      => esc_html__( 'Title', 'woodmart' ),
					'param_name' => 'title',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Predefined size', 'woodmart' ),
					'group'            => esc_html__( 'Title', 'woodmart' ),
					'param_name'       => 'size',
					'value'            => array(
						esc_html__( 'Default (22px)', 'woodmart' ) => 'default',
						esc_html__( 'Small (18px)', 'woodmart' ) => 'small',
						esc_html__( 'Medium (26px)', 'woodmart' ) => 'medium',
						esc_html__( 'Large (36px)', 'woodmart' ) => 'large',
						esc_html__( 'Extra Large (46px)', 'woodmart' ) => 'extra-large',
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Font weight', 'woodmart' ),
					'group'            => esc_html__( 'Title', 'woodmart' ),
					'param_name'       => 'font_weight',
					'value'            => array(
						'' => '',
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
					'type'             => 'woodmart_responsive_size',
					'heading'          => esc_html__( 'Custom size', 'woodmart' ),
					'group'            => esc_html__( 'Title', 'woodmart' ),
					'param_name'       => 'title_font_size',
					'css_args'         => array(
						'font-size' => array(
							' .woodmart-title-container',
						),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'woodmart_responsive_size',
					'heading'          => esc_html__( 'Custom line height', 'woodmart' ),
					'group'            => esc_html__( 'Title', 'woodmart' ),
					'param_name'       => 'title_line_height',
					'css_args'         => array(
						'line-height' => array(
							' .woodmart-title-container',
						),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'       => 'woodmart_empty_space',
					'param_name' => 'woodmart_empty_space',
					'group'      => esc_html__( 'Title', 'woodmart' ),
				),
				array(
					'type'             => 'woodmart_colorpicker',
					'heading'          => esc_html__( 'Custom color', 'woodmart' ),
					'group'            => esc_html__( 'Title', 'woodmart' ),
					'param_name'       => 'title_custom_color',
					'css_args'         => array(
						'color' => array(
							' .woodmart-title-container',
						),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Tag', 'woodmart' ),
					'group'            => esc_html__( 'Title', 'woodmart' ),
					'param_name'       => 'tag',
					'value'            => array(
						'h1'   => 'h1',
						'h2'   => 'h2',
						'h3'   => 'h3',
						'h4'   => 'h4',
						'h5'   => 'h5',
						'h6'   => 'h6',
						'p'    => 'p',
						'div'  => 'div',
						'span' => 'span',
					),
					'std'              => 'h4',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				/**
				 * Advanced highlight.
				 */
				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Highlight', 'woodmart' ),
					'group'      => esc_html__( 'Title', 'woodmart' ),
					'param_name' => 'highlight_divider',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Highlight text style', 'woodmart' ),
					'hint'             => esc_html__( 'The text must be wrapped with the <u></u> tag to highlight it.', 'woodmart' ),
					'group'            => esc_html__( 'Title', 'woodmart' ),
					'param_name'       => 'title_decoration_style',
					'value'            => array(
						esc_html__( 'Default', 'woodmart' )  => 'default',
						esc_html__( 'Primary color', 'woodmart' )  => 'colored',
						esc_html__( 'Primary color + secondary font', 'woodmart' ) => 'colored-alt',
						esc_html__( 'Bordered', 'woodmart' ) => 'bordered',
						esc_html__( 'Gradient', 'woodmart' ) => 'gradient',
					),
					'std'              => 'colored',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'       => 'woodmart_gradient',
					'heading'    => esc_html__( 'Highlight text gradient', 'woodmart' ),
					'group'      => esc_html__( 'Title', 'woodmart' ),
					'param_name' => 'title_decoration_gradient',
					'selectors'  => array(
						'{{WRAPPER}}.wd-underline-gradient u' => array(
							'background-image: {{VALUE}};',
						),
					),
					'dependency' => array(
						'element' => 'title_decoration_style',
						'value'   => array( 'gradient' ),
					),
				),
				array(
					'group'      => esc_html__( 'Title', 'woodmart' ),
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
					'group'      => esc_html__( 'Title', 'woodmart' ),
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
					'group'            => esc_html__( 'Title', 'woodmart' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column',
					'dependency'       => array(
						'element' => 'title_decoration_style',
						'value'   => array( 'default' ),
					),
				),
				array(
					'type'             => 'wd_colorpicker',
					'heading'          => esc_html__( 'Color', 'woodmart' ),
					'group'            => esc_html__( 'Title', 'woodmart' ),
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
					'wd_dependency'    => array(
						'element' => 'title_decoration_style',
						'value'   => array( 'default' ),
					),
					'edit_field_class' => 'vc_col-sm-12 vc_column',
				),
				array(
					'type'          => 'woodmart_gradient',
					'heading'       => esc_html__( 'Highlight text gradient', 'woodmart' ),
					'group'         => esc_html__( 'Title', 'woodmart' ),
					'param_name'    => 'title_decoration_gradient_text',
					'selectors'     => array(
						'{{WRAPPER}} u' => array(
							'background-image: {{VALUE}};',
							'background-clip: text;',
							'background-color: currentColor;',
							'-webkit-background-clip: text;',
							'-webkit-text-fill-color: transparent;',
						),
					),
					'dependency'    => array(
						'element' => 'title_decoration_color_type',
						'value'   => array( 'gradient' ),
					),
					'wd_dependency' => array(
						'element' => 'title_decoration_style',
						'value'   => array( 'default' ),
					),
				),
				array(
					'type'             => 'woodmart_button_set',
					'heading'          => esc_html__( 'Background type', 'woodmart' ),
					'param_name'       => 'title_decoration_bg_type',
					'group'            => esc_html__( 'Title', 'woodmart' ),
					'value'            => array(
						esc_html__( 'Default', 'woodmart' ) => '',
						esc_html__( 'Classic', 'woodmart' ) => 'classic',
						esc_html__( 'Gradient', 'woodmart' ) => 'gradient',
					),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-12 vc_column',
					'dependency'       => array(
						'element' => 'title_decoration_style',
						'value'   => array( 'default' ),
					),
				),
				array(
					'group'      => esc_html__( 'Title', 'woodmart' ),
					'type'       => 'woodmart_empty_space',
					'param_name' => 'title_decoration_empty_space',
				),
				array(
					'type'             => 'wd_colorpicker',
					'heading'          => esc_html__( 'Background color', 'woodmart' ),
					'group'            => esc_html__( 'Title', 'woodmart' ),
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
					'wd_dependency'    => array(
						'element' => 'title_decoration_style',
						'value'   => array( 'default' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'attach_image',
					'heading'          => esc_html__( 'Background image', 'woodmart' ),
					'param_name'       => 'title_decoration_bg_image',
					'hint'             => esc_html__( 'Select images from media library.', 'woodmart' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'group'            => esc_html__( 'Title', 'woodmart' ),
					'dependency'       => array(
						'element' => 'title_decoration_bg_type',
						'value'   => array( 'classic' ),
					),
					'wd_dependency'    => array(
						'element' => 'title_decoration_style',
						'value'   => array( 'default' ),
					),
				),
				array(
					'type'             => 'textfield',
					'heading'          => esc_html__( 'Background image resolution', 'woodmart' ),
					'param_name'       => 'title_decoration_bg_image_size',
					'hint'             => esc_html__( 'Enter image resolution. Example: \'thumbnail\', \'medium\', \'large\', \'full\' or other sizes defined by current theme. Alternatively enter image size in pixels: 200x100 (Width x Height). Leave empty to use \'thumbnail\' size.', 'woodmart' ),
					'group'            => esc_html__( 'Title', 'woodmart' ),
					'dependency'       => array(
						'element' => 'title_decoration_bg_type',
						'value'   => array( 'classic' ),
					),
					'wd_dependency'    => array(
						'element' => 'title_decoration_style',
						'value'   => array( 'default' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Background position', 'woodmart' ),
					'param_name'       => 'title_decoration_bg_image_position',
					'group'            => esc_html__( 'Title', 'woodmart' ),
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
					'wd_dependency'    => array(
						'element' => 'title_decoration_style',
						'value'   => array( 'default' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Background repeat', 'woodmart' ),
					'param_name'       => 'title_decoration_bg_image_repeat',
					'group'            => esc_html__( 'Title', 'woodmart' ),
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
					'wd_dependency'    => array(
						'element' => 'title_decoration_style',
						'value'   => array( 'default' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Background size', 'woodmart' ),
					'param_name'       => 'title_decoration_bg_image_sizes',
					'group'            => esc_html__( 'Title', 'woodmart' ),
					'value'            => array(
						esc_html__( 'Default', 'woodmart' ) => '',
						esc_html__( 'Cover', 'woodmart' ) => 'cover',
						esc_html__( 'Contain', 'woodmart' ) => 'contain',
					),
					'dependency'       => array(
						'element' => 'title_decoration_bg_type',
						'value'   => array( 'classic' ),
					),
					'wd_dependency'    => array(
						'element' => 'title_decoration_style',
						'value'   => array( 'default' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'          => 'woodmart_gradient',
					'heading'       => esc_html__( 'Background gradient', 'woodmart' ),
					'group'         => esc_html__( 'Title', 'woodmart' ),
					'param_name'    => 'title_decoration_gradient_bg',
					'selectors'     => array(
						'{{WRAPPER}} u' => array(
							'background-image: {{VALUE}};',
						),
					),
					'dependency'    => array(
						'element' => 'title_decoration_bg_type',
						'value'   => array( 'gradient' ),
					),
					'wd_dependency' => array(
						'element' => 'title_decoration_style',
						'value'   => array( 'default' ),
					),
				),

				array(
					'heading'    => esc_html__( 'Border type', 'woodmart' ),
					'group'      => esc_html__( 'Title', 'woodmart' ),
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
					'dependency' => array(
						'element' => 'title_decoration_style',
						'value'   => array( 'default' ),
					),
				),
				array(
					'heading'          => esc_html__( 'Border width', 'woodmart' ),
					'group'            => esc_html__( 'Title', 'woodmart' ),
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
					'wd_dependency'    => array(
						'element' => 'title_decoration_style',
						'value'   => array( 'default' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'heading'          => esc_html__( 'Border color', 'woodmart' ),
					'group'            => esc_html__( 'Title', 'woodmart' ),
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
					'wd_dependency'    => array(
						'element' => 'title_decoration_style',
						'value'   => array( 'default' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'heading'    => esc_html__( 'Border radius', 'woodmart' ),
					'group'      => esc_html__( 'Title', 'woodmart' ),
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
					'dependency' => array(
						'element' => 'title_decoration_style',
						'value'   => array( 'default' ),
					),
				),
				array(
					'type'             => 'wd_box_shadow',
					'heading'          => esc_html__( 'Box shadow', 'woodmart' ),
					'group'            => esc_html__( 'Title', 'woodmart' ),
					'param_name'       => 'title_decoration_box_shadow',
					'selectors'        => array(
						'{{WRAPPER}} u' => array(
							'box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}};',
						),
					),
					'edit_field_class' => 'vc_col-sm-12 vc_column',
					'dependency'       => array(
						'element' => 'title_decoration_style',
						'value'   => array( 'default' ),
					),
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
					'group'      => esc_html__( 'Title', 'woodmart' ),
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
					'dependency' => array(
						'element' => 'title_decoration_style',
						'value'   => array( 'default' ),
					),
				),
				/**
				 * Subtitle
				 */
				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Subtitle', 'woodmart' ),
					'group'      => esc_html__( 'Subtitle', 'woodmart' ),
					'param_name' => 'subtitle_divider',
				),
				array(
					'type'       => 'textarea',
					'heading'    => esc_html__( 'Subtitle', 'woodmart' ),
					'group'      => esc_html__( 'Subtitle', 'woodmart' ),
					'param_name' => 'subtitle',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Font', 'woodmart' ),
					'group'            => esc_html__( 'Subtitle', 'woodmart' ),
					'param_name'       => 'subtitle_font',
					'value'            => array(
						$text_font_label      => 'default',
						$secondary_font_label => 'alt',
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Font weight', 'woodmart' ),
					'group'            => esc_html__( 'Subtitle', 'woodmart' ),
					'param_name'       => 'subtitle_font_weight',
					'value'            => array(
						'' => '',
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
					'type'             => 'woodmart_responsive_size',
					'heading'          => esc_html__( 'Font Size', 'woodmart' ),
					'group'            => esc_html__( 'Subtitle', 'woodmart' ),
					'param_name'       => 'subtitle_font_size',
					'css_args'         => array(
						'font-size' => array(
							' .title-subtitle',
						),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'woodmart_responsive_size',
					'heading'          => esc_html__( 'Line height', 'woodmart' ),
					'group'            => esc_html__( 'Subtitle', 'woodmart' ),
					'param_name'       => 'subtitle_line_height_size',
					'css_args'         => array(
						'line-height' => array(
							' .title-subtitle',
						),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'woodmart_colorpicker',
					'heading'          => esc_html__( 'Custom color', 'woodmart' ),
					'group'            => esc_html__( 'Subtitle', 'woodmart' ),
					'param_name'       => 'subtitle_color',
					'css_args'         => array(
						'color' => array(
							' .title-subtitle',
						),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'woodmart_image_select',
					'heading'          => esc_html__( 'Style', 'woodmart' ),
					'group'            => esc_html__( 'Subtitle', 'woodmart' ),
					'param_name'       => 'subtitle_style',
					'value'            => array(
						esc_html__( 'Default', 'woodmart' ) => 'default',
						esc_html__( 'Background', 'woodmart' ) => 'background',
					),
					'images_value'     => array(
						'default'    => WOODMART_ASSETS_IMAGES . '/settings/subtitle-style/default.png',
						'background' => WOODMART_ASSETS_IMAGES . '/settings/subtitle-style/background.png',
					),
					'edit_field_class' => 'vc_col-sm-12 vc_column',
				),
				array(
					'type'             => 'woodmart_colorpicker',
					'heading'          => esc_html__( 'Background color', 'woodmart' ),
					'group'            => esc_html__( 'Subtitle', 'woodmart' ),
					'param_name'       => 'subtitle_bg_color',
					'css_args'         => array(
						'background-color' => array(
							' .title-subtitle',
						),
					),
					'dependency'       => array(
						'element' => 'subtitle_style',
						'value'   => array( 'background' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				/**
				 * Text after title
				 */
				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Text', 'woodmart' ),
					'group'      => esc_html__( 'Text', 'woodmart' ),
					'param_name' => 'text_divider',
				),
				array(
					'type'       => 'textarea',
					'heading'    => esc_html__( 'Text after title', 'woodmart' ),
					'group'      => esc_html__( 'Text', 'woodmart' ),
					'param_name' => 'after_title',
				),
				array(
					'type'             => 'woodmart_responsive_size',
					'heading'          => esc_html__( 'Size', 'woodmart' ),
					'group'            => esc_html__( 'Text', 'woodmart' ),
					'param_name'       => 'after_font_size',
					'css_args'         => array(
						'font-size' => array(
							' .title-after_title',
						),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'woodmart_responsive_size',
					'heading'          => esc_html__( 'Line height', 'woodmart' ),
					'group'            => esc_html__( 'Text', 'woodmart' ),
					'param_name'       => 'after_line_height_size',
					'css_args'         => array(
						'line-height' => array(
							' .title-after_title',
						),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'       => 'woodmart_colorpicker',
					'heading'    => esc_html__( 'Color', 'woodmart' ),
					'group'      => esc_html__( 'Text', 'woodmart' ),
					'param_name' => 'after_color',
					'css_args'   => array(
						'color' => array(
							' .title-after_title',
						),
					),
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
				/**
				 * Advanced.
				 */

				woodmart_get_vc_responsive_visible_map( 'responsive_tabs_hide' ),
				woodmart_get_vc_responsive_visible_map( 'wd_hide_on_desktop' ),
				woodmart_get_vc_responsive_visible_map( 'wd_hide_on_tablet' ),
				woodmart_get_vc_responsive_visible_map( 'wd_hide_on_mobile' ),

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


if ( ! function_exists( 'woodmart_section_title_color_variation' ) ) {
	/**
	 * Get Section Title Color.
	 *
	 * @return array List of title color variation.
	 */
	function woodmart_section_title_color_variation() {

		$variation = array(
			esc_html__( 'Default', 'woodmart' )           => 'default',
			esc_html__( 'Primary color', 'woodmart' )     => 'primary',
			esc_html__( 'Alternative color', 'woodmart' ) => 'alt',
			esc_html__( 'Black', 'woodmart' )             => 'black',
			esc_html__( 'White', 'woodmart' )             => 'white',
		);

		if ( apply_filters( 'woodmart_gradients_enabled', true ) ) {
			$variation = array_merge(
				$variation,
				array(
					esc_html__( 'Gradient', 'woodmart' ) => 'gradient',
				)
			);
		}

		return $variation;
	}
}

if ( ! function_exists( 'woodmart_title_gradient_picker' ) ) {
	/**
	 * Get Gradient Section Title Color Picker.
	 *
	 * @return array List of title color variation.
	 */
	function woodmart_title_gradient_picker() {

		$title_color = array(
			'type'       => 'woodmart_gradient',
			'param_name' => 'woodmart_color_gradient',
			'heading'    => esc_html__( 'Gradient title color', 'woodmart' ),
			'dependency' => array(
				'element' => 'color',
				'value'   => array( 'gradient' ),
			),
		);

		if ( ! apply_filters( 'woodmart_gradients_enabled', true ) ) {
			$title_color = false;
		}

		return $title_color;
	}
}
