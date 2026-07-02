<?php
/**
 * Breadcrumbs element map.
 *
 * @package woodmart
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}


if ( ! function_exists( 'woodmart_get_vc_map_breadcrumbs' ) ) {
	/**
	 * Get breadcrumbs element map.
	 *
	 * @return array
	 */
	function woodmart_get_vc_map_breadcrumbs() {
		return array(
			'name'        => esc_html__( 'Breadcrumbs', 'woodmart' ),
			'base'        => 'woodmart_el_breadcrumbs',
			'category'    => woodmart_get_tab_title_category_for_wpb( esc_html__( 'Site', 'woodmart' ) ),
			'description' => esc_html__( 'Current page breadcrumbs', 'woodmart' ),
			'icon'        => WOODMART_ASSETS . '/images/vc-icon/breadcrumbs.svg',
			'params'      => woodmart_get_breadcrumbs_params(),
		);
	}
}

if ( ! function_exists( 'woodmart_get_breadcrumbs_params' ) ) {
	/**
	 * Get breadcrumbs params.
	 *
	 * @return array
	 */
	function woodmart_get_breadcrumbs_params() {
		$breadcrumbs_selector = '{{WRAPPER}} .wd-breadcrumbs';
		$typography           = woodmart_get_typography_map(
			array(
				'key'      => 'typography',
				'selector' => $breadcrumbs_selector,
				'group'    => esc_html__( 'Style', 'woodmart' ),
			)
		);

		$active_typography = woodmart_get_typography_map(
			array(
				'key'      => 'active_item',
				'selector' => '{{WRAPPER}} :is(.wd-last, .breadcrumb_last, .last, .aioseo-breadcrumbs span:last-child, .active)',
				'group'    => esc_html__( 'Style', 'woodmart' ),
			)
		);

		return array(
			array(
				'group'      => esc_html__( 'Style', 'woodmart' ),
				'type'       => 'woodmart_css_id',
				'param_name' => 'woodmart_css_id',
			),

			array(
				'type'       => 'woodmart_title_divider',
				'holder'     => 'div',
				'group'      => esc_html__( 'Style', 'woodmart' ),
				'title'      => esc_html__( 'General', 'woodmart' ),
				'param_name' => 'general_style_divider',
			),

			array(
				'heading'          => esc_html__( 'Alignment', 'woodmart' ),
				'group'            => esc_html__( 'Style', 'woodmart' ),
				'type'             => 'wd_select',
				'param_name'       => 'alignment',
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
				'edit_field_class' => 'vc_col-sm-6 vc_column',
			),

			array(
				'heading'          => esc_html__( 'No wrap on mobile devices', 'woodmart' ),
				'group'            => esc_html__( 'Style', 'woodmart' ),
				'type'             => 'woodmart_switch',
				'param_name'       => 'nowrap_md',
				'true_state'       => 'yes',
				'false_state'      => 'no',
				'default'          => 'no',
				'edit_field_class' => 'vc_col-sm-6 vc_column',
			),

			array(
				'type'       => 'woodmart_title_divider',
				'holder'     => 'div',
				'group'      => esc_html__( 'Style', 'woodmart' ),
				'title'      => esc_html__( 'Items', 'woodmart' ),
				'param_name' => 'items_style_divider',
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
				'group'            => esc_html__( 'Style', 'woodmart' ),
				'type'             => 'wd_colorpicker',
				'param_name'       => 'text_color',
				'selectors'        => array(
					$breadcrumbs_selector => array(
						'--wd-link-color: {{VALUE}};',
					),
				),
				'edit_field_class' => 'vc_col-sm-6 vc_column',
			),

			array(
				'heading'          => esc_html__( 'Hover color', 'woodmart' ),
				'group'            => esc_html__( 'Style', 'woodmart' ),
				'type'             => 'wd_colorpicker',
				'param_name'       => 'text_color_hover',
				'selectors'        => array(
					$breadcrumbs_selector => array(
						'--wd-link-color-hover: {{VALUE}};',
					),
				),
				'edit_field_class' => 'vc_col-sm-6 vc_column',
			),

			array(
				'type'       => 'woodmart_title_divider',
				'holder'     => 'div',
				'group'      => esc_html__( 'Style', 'woodmart' ),
				'title'      => esc_html__( 'Current item', 'woodmart' ),
				'param_name' => 'current_item_style_divider',
			),

			$active_typography['font_family'],
			$active_typography['font_size'],
			$active_typography['font_weight'],
			$active_typography['text_transform'],
			$active_typography['font_style'],
			$active_typography['text_decoration'],
			$active_typography['line_height'],

			array(
				'heading'          => esc_html__( 'Color', 'woodmart' ),
				'group'            => esc_html__( 'Style', 'woodmart' ),
				'type'             => 'wd_colorpicker',
				'param_name'       => 'text_color_active',
				'selectors'        => array(
					$breadcrumbs_selector => array(
						'--wd-bcrumb-color-active: {{VALUE}};',
					),
				),
				'edit_field_class' => 'vc_col-sm-6 vc_column',
			),

			array(
				'type'       => 'woodmart_title_divider',
				'holder'     => 'div',
				'group'      => esc_html__( 'Style', 'woodmart' ),
				'title'      => esc_html__( 'Delimiter', 'woodmart' ),
				'param_name' => 'delimiter_style_divider',
			),

			array(
				'heading'          => esc_html__( 'Color', 'woodmart' ),
				'group'            => esc_html__( 'Style', 'woodmart' ),
				'type'             => 'wd_colorpicker',
				'param_name'       => 'delimiter_color',
				'selectors'        => array(
					$breadcrumbs_selector => array(
						'--wd-bcrumb-delim-color: {{VALUE}};',
					),
				),
				'edit_field_class' => 'vc_col-sm-6 vc_column',
			),

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
		);
	}
}
