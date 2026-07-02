<?php
/**
 * Single product label map.
 *
 * @package woodmart
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_get_vc_map_single_product_label' ) ) {
	/**
	 * Single product label map.
	 *
	 * @return array
	 */
	function woodmart_get_vc_map_single_product_label() {
		return array(
			'base'        => 'woodmart_single_product_label',
			'name'        => esc_html__( 'Product label', 'woodmart' ),
			'category'    => woodmart_get_tab_title_category_for_wpb( esc_html__( 'Single product elements', 'woodmart' ), 'single_product' ),
			'description' => esc_html__( 'Show a single product label', 'woodmart' ),
			'icon'        => WOODMART_ASSETS . '/images/vc-icon/sp-icons/sp-label.svg',
			'params'      => array(
				array(
					'type'       => 'woodmart_css_id',
					'param_name' => 'woodmart_css_id',
				),

				array(
					'type'               => 'autocomplete',
					'heading'            => esc_html__( 'Label', 'woodmart' ),
					'param_name'         => 'label_id',
					'settings'           => array(
						'multiple'       => false,
						'min_length'     => 1,
						'groups'         => true,
						'unique_values'  => true,
						'display_inline' => true,
						'delay'          => 500,
						'auto_focus'     => true,
					),
					'param_holder_class' => 'vc_not-for-custom',
				),

				array(
					'heading'          => esc_html__( 'Alignment', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'type'             => 'wd_select',
					'param_name'       => 'alignment',
					'style'            => 'images',
					'selectors'        => array(
						'{{WRAPPER}}' => array(
							'justify-content: {{VALUE}};',
						),
					),
					'devices'          => array(
						'desktop' => array(
							'value' => 'var(--wd-start)',
						),
						'tablet'  => array(
							'value' => '',
						),
						'mobile'  => array(
							'value' => '',
						),
					),
					'value'            => array(
						esc_html__( 'Left', 'woodmart' )   => 'var(--wd-start)',
						esc_html__( 'Center', 'woodmart' ) => 'var(--wd-center)',
						esc_html__( 'Right', 'woodmart' )  => 'var(--wd-end)',
					),
					'images'           => array(
						'var(--wd-start)'  => WOODMART_ASSETS_IMAGES . '/settings/align/left.jpg',
						'var(--wd-center)' => WOODMART_ASSETS_IMAGES . '/settings/align/center.jpg',
						'var(--wd-end)'    => WOODMART_ASSETS_IMAGES . '/settings/align/right.jpg',
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

				array(
					'type'             => 'wd_select',
					'heading'          => esc_html__( 'Position', 'woodmart' ),
					'param_name'       => 'wd_position',
					'group'            => esc_html__( 'Advanced', 'woodmart' ),
					'style'            => 'select',
					'selectors'        => array(
						'{{WRAPPER}}' => array(
							'position: {{VALUE}};',
						),
					),
					'devices'          => array(
						'desktop' => array(
							'value' => '',
						),
					),
					'value'            => array(
						esc_html__( 'Default', 'woodmart' ) => '',
						esc_html__( 'Absolute', 'woodmart' ) => 'absolute',
					),
					'edit_field_class' => 'vc_col-sm-12 vc_column',
				),

				array(
					'type'             => 'woodmart_switch',
					'param_name'       => 'wd_z_index',
					'heading'          => esc_html__( 'Z Index', 'woodmart' ),
					'hint'             => esc_html__( 'Enable this option if you would like to display this element above other elements on the page. You can specify a custom value as well.', 'woodmart' ),
					'group'            => esc_html__( 'Advanced', 'woodmart' ),
					'true_state'       => 'yes',
					'false_state'      => 'no',
					'default'          => 'no',
					'edit_field_class' => 'vc_col-sm-12 vc_column',
				),
				array(
					'type'             => 'wd_number',
					'param_name'       => 'wd_z_index_custom',
					'group'            => esc_html__( 'Advanced', 'woodmart' ),
					'devices'          => array(
						'desktop' => array(
							'value' => '',
						),
					),
					'min'              => -1,
					'max'              => 1000,
					'step'             => 1,
					'selectors'        => array(
						'{{WRAPPER}}' => array(
							'z-index: {{VALUE}};',
						),
					),
					'dependency'       => array(
						'element' => 'wd_z_index',
						'value'   => array( 'yes' ),
					),
					'edit_field_class' => 'vc_col-sm-12 vc_column',
				),

				array(
					'heading'    => esc_html__( 'Offset', 'woodmart' ),
					'group'      => esc_html__( 'Advanced', 'woodmart' ),
					'type'       => 'wd_dimensions',
					'param_name' => 'wd_position_offsets',
					'selectors'  => array(
						'{{WRAPPER}}' => array(
							'top: {{TOP}}{{UNIT}};',
							'right: {{RIGHT}}{{UNIT}};',
							'bottom: {{BOTTOM}}{{UNIT}};',
							'left: {{LEFT}}{{UNIT}};',
						),
					),
					'range'      => array(
						'px'     => array(),
						'custom' => array(),
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
					'dependency' => array(
						'element' => 'wd_position',
						'value'   => woodmart_compress(
							wp_json_encode(
								array(
									'devices' => array(
										'desktop' => array(
											'value' => 'absolute',
										),
									),
								)
							)
						),
					),
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

// Label autocomplete fields
add_filter( 'vc_autocomplete_woodmart_single_product_label_label_id_callback', 'woodmart_vc_autocomplete_label_field_search', 10, 1 );
add_filter( 'vc_autocomplete_woodmart_single_product_label_label_id_render', 'woodmart_vc_autocomplete_label_field_render', 10, 1 );

if ( ! function_exists( 'woodmart_vc_autocomplete_label_field_search' ) ) {
	/**
	 * Search labels for autocomplete labels field.
	 *
	 * @param string $query Query string.
	 * @return array
	 */
	function woodmart_vc_autocomplete_label_field_search( $query ) {
		$results      = array();
		$extra_labels = array(
			'sale'         => esc_html__( 'Sale', 'woodmart' ),
			'out-of-stock' => esc_html__( 'Out of stock', 'woodmart' ),
			'hot'          => esc_html__( 'Hot', 'woodmart' ),
			'new'          => esc_html__( 'New', 'woodmart' ),
		);
		foreach ( $extra_labels as $value => $label ) {
			if ( '' === $query || false !== stripos( $label, $query ) ) {
				$results[] = array(
					'value' => $value,
					'label' => $label,
					'group' => esc_html__( 'Default labels', 'woodmart' ),
				);
			}
		}
		$labels = get_posts(
			array(
				'post_type'      => 'wd_custom_label',
				'posts_per_page' => 50,
				'post_status'    => array( 'publish', 'private', 'draft' ),
				's'              => $query,
			)
		);
		if ( $labels ) {
			foreach ( $labels as $post ) {
				$results[] = array(
					'value' => $post->ID,
					'label' => $post->post_title,
					'group' => esc_html__( 'Custom labels', 'woodmart' ),
				);
			}
		}
		return $results;
	}
}

if ( ! function_exists( 'woodmart_vc_autocomplete_label_field_render' ) ) {
	/**
	 * Render exact label for autocomplete labels field.
	 *
	 * @param array $query Query.
	 * @return array|false
	 */
	function woodmart_vc_autocomplete_label_field_render( $query ) {
		$value = isset( $query['value'] ) ? trim( $query['value'] ) : '';
		if ( '' === $value ) {
			return false;
		}
		$extra_labels = array(
			'sale'         => esc_html__( 'Sale', 'woodmart' ),
			'out-of-stock' => esc_html__( 'Out of stock', 'woodmart' ),
			'hot'          => esc_html__( 'Hot', 'woodmart' ),
			'new'          => esc_html__( 'New', 'woodmart' ),
		);
		if ( isset( $extra_labels[ $value ] ) ) {
			return array(
				'value' => $value,
				'label' => $extra_labels[ $value ],
			);
		}
		if ( ctype_digit( $value ) ) {
			$label_object = get_post( (int) $value );
			if ( $label_object && 'wd_custom_label' === $label_object->post_type ) {
				return array(
					'value' => $label_object->ID,
					'label' => $label_object->post_title,
				);
			}
		}
		return false;
	}
}
