<?php
/**
 * Stock status map.
 *
 * @package woodmart
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_get_vc_map_single_product_stock_status' ) ) {
	/**
	 * Content map.
	 */
	function woodmart_get_vc_map_single_product_stock_status() {
		$title_typography = woodmart_get_typography_map(
			array(
				'key'      => 'typography',
				'group'    => esc_html__( 'Style', 'woodmart' ),
				'selector' => '{{WRAPPER}}.wd-single-stock-status p',
			)
		);

		return array(
			'base'        => 'woodmart_single_product_stock_status',
			'name'        => esc_html__( 'Product stock status', 'woodmart' ),
			'category'    => woodmart_get_tab_title_category_for_wpb( esc_html__( 'Single product elements', 'woodmart' ), 'single_product' ),
			'description' => esc_html__( 'Show product stock status', 'woodmart' ),
			'icon'        => WOODMART_ASSETS . '/images/vc-icon/sp-icons/sp-stock-status.svg',
			'params'      => array(
				array(
					'group'      => esc_html__( 'Style', 'js_composer' ),
					'type'       => 'woodmart_css_id',
					'param_name' => 'woodmart_css_id',
				),

				$title_typography['font_family'],
				$title_typography['font_size'],
				$title_typography['font_weight'],
				$title_typography['text_transform'],
				$title_typography['font_style'],
				$title_typography['text_decoration'],
				$title_typography['line_height'],

				/**
				 * Design options Tab.
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

				/**
				 * Advanced Tab.
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
