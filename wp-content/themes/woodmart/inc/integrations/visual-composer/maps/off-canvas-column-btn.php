<?php
/**
 * Maps for off canvas button element.
 *
 * @package woodmart.
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

if ( ! function_exists( 'woodmart_get_vc_map_off_canvas_btn' ) ) {
	/**
	 * Displays the shortcode settings fields in the admin.
	 */
	function woodmart_get_vc_map_off_canvas_btn() {
		$typography = woodmart_get_typography_map(
			array(
				'key'      => 'button_typography',
				'selector' => '{{WRAPPER}} .wd-action-text',
				'group'    => esc_html__( 'Style', 'woodmart' ),
			)
		);

		return array(
			'base'        => 'woodmart_off_canvas_btn',
			'name'        => esc_html__( 'Off canvas column button', 'woodmart' ),
			'description' => esc_html__( 'Button for off canvas column', 'woodmart' ),
			'category'    => woodmart_get_tab_title_category_for_wpb( esc_html__( 'Theme elements', 'woodmart' ) ),
			'icon'        => WOODMART_ASSETS . '/images/vc-icon/off-canvas-column-button.svg',
			'params'      => array(
				array(
					'type'       => 'woodmart_css_id',
					'param_name' => 'woodmart_css_id',
				),
				/**
				 * General settings.
				 */
				array(
					'param_name' => 'button_text',
					'type'       => 'textfield',
					'heading'    => esc_html__( 'Button text', 'woodmart' ),
					'std'        => 'Show column',
				),
				array(
					'param_name'       => 'sticky',
					'type'             => 'woodmart_switch',
					'heading'          => esc_html__( 'Sticky', 'woodmart' ),
					'hint'             => esc_html__( 'Add an additional sticky button.', 'woodmart' ),
					'true_state'       => 'yes',
					'false_state'      => 'no',
					'std'              => 'no',
					'edit_field_class' => 'vc_col-sm-12 vc_column',
				),
				array(
					'param_name'       => 'only_sticky_button',
					'type'             => 'woodmart_switch',
					'heading'          => esc_html__( 'Only sticky button', 'woodmart' ),
					'hint'             => esc_html__( 'Hide the static button and show only the sticky one.', 'woodmart' ),
					'true_state'       => 'yes',
					'false_state'      => 'no',
					'std'              => 'no',
					'dependency'       => array(
						'element' => 'sticky',
						'value'   => array( 'yes' ),
					),
					'edit_field_class' => 'vc_col-sm-12 vc_column',
				),

				/**
				 * Text style settings
				 */
				array(
					'param_name' => 'text_style_section',
					'type'       => 'woodmart_title_divider',
					'title'      => esc_html__( 'Text', 'woodmart' ),
					'group'      => esc_html__( 'Style', 'woodmart' ),
				),

				$typography['font_family'],
				$typography['font_size'],
				$typography['font_weight'],
				$typography['text_transform'],
				$typography['font_style'],
				$typography['text_decoration'],
				$typography['line_height'],

				array(
					'type'       => 'woodmart_empty_space',
					'param_name' => 'woodmart_empty_space',
					'group'      => esc_html__( 'Style', 'woodmart' ),
				),

				array(
					'heading'          => esc_html__( 'Idle color', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'param_name'       => 'btn_text_color',
					'selectors'        => array(
						'{{WRAPPER}} .wd-action-btn' => array(
							'--wd-action-text-color: {{VALUE}};',
						),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'heading'          => esc_html__( 'Hover color', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'param_name'       => 'btn_text_hover_color',
					'selectors'        => array(
						'{{WRAPPER}} .wd-action-btn' => array(
							'--wd-action-text-color-hover: {{VALUE}};',
						),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				/**
				 * Icon settings.
				 */
				array(
					'param_name' => 'icon_style_section',
					'type'       => 'woodmart_title_divider',
					'title'      => esc_html__( 'Icon', 'woodmart' ),
					'group'      => esc_html__( 'Style', 'woodmart' ),
				),
				array(
					'param_name'       => 'icon_type',
					'type'             => 'woodmart_dropdown',
					'heading'          => esc_html__( 'Type', 'woodmart' ),
					'std'              => 'default',
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column',
					'value'            => array(
						esc_html__( 'Without icon', 'woodmart' ) => 'without',
						esc_html__( 'Default', 'woodmart' ) => 'default',
						esc_html__( 'Custom image', 'woodmart' ) => 'custom',
					),
				),
				array(
					'heading'    => esc_html__( 'Icon size', 'woodmart' ),
					'group'      => esc_html__( 'Style', 'woodmart' ),
					'type'       => 'wd_slider',
					'param_name' => 'icon_size',
					'selectors'  => array(
						'{{WRAPPER}} .wd-action-btn' => array(
							'--wd-action-icon-size: {{VALUE}}px;',
						),
					),
					'devices'    => array(
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
					'range'      => array(
						'px' => array(
							'min'  => 0,
							'max'  => 50,
							'step' => 1,
						),
					),
					'dependency' => array(
						'element' => 'icon_type',
						'value'   => array( 'default' ),
					),
				),
				array(
					'heading'          => esc_html__( 'Idle color', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'param_name'       => 'icon_color',
					'selectors'        => array(
						'{{WRAPPER}} .wd-action-btn' => array(
							'--wd-action-icon-color: {{VALUE}};',
						),
					),
					'dependency'       => array(
						'element' => 'icon_type',
						'value'   => array( 'default' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'heading'          => esc_html__( 'Hover color', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'param_name'       => 'icon_color_hover',
					'selectors'        => array(
						'{{WRAPPER}} .wd-action-btn' => array(
							'--wd-action-icon-color-hover: {{VALUE}};',
						),
					),
					'dependency'       => array(
						'element' => 'icon_type',
						'value'   => array( 'default' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'param_name'       => 'img_id',
					'type'             => 'attach_image',
					'heading'          => esc_html__( 'Image', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column',
					'dependency'       => array(
						'element' => 'icon_type',
						'value'   => array( 'custom' ),
					),
				),
				array(
					'param_name'       => 'img_size',
					'type'             => 'textfield',
					'heading'          => esc_html__( 'Image resolution', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column',
					'hint'             => esc_html__( 'Enter image size. Example: \'thumbnail\', \'medium\', \'large\', \'full\' or other sizes defined by current theme. Alternatively enter image size in pixels: 200x100 (Width x Height). Leave empty to use \'20x20\' size.', 'woodmart' ),
					'dependency'       => array(
						'element' => 'icon_type',
						'value'   => array( 'custom' ),
					),
				),
				// Design Options tab.
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
				 * Advanced tab.
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
