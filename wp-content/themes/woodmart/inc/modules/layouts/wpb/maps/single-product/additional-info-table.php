<?php
/**
 * Additional info table map.
 *
 * @package woodmart
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_get_vc_map_single_product_additional_info_table' ) ) {
	/**
	 * Additional info table map.
	 */
	function woodmart_get_vc_map_single_product_additional_info_table() {
		$typography = woodmart_get_typography_map(
			array(
				'key'      => 'title',
				'selector' => '{{WRAPPER}} .title-text',
			)
		);

		$name_typography = woodmart_get_typography_map(
			array(
				'title'      => esc_html__( 'Typography', 'woodmart' ),
				'group'      => esc_html__( 'Style', 'woodmart' ),
				'key'        => 'attr_name',
				'selector'   => '{{WRAPPER}} .shop_attributes th',
				'dependency' => array(
					'element' => 'attr_hide_name',
					'value'   => 'no',
				),
			)
		);
		$term_typography = woodmart_get_typography_map(
			array(
				'title'      => esc_html__( 'Typography', 'woodmart' ),
				'group'      => esc_html__( 'Style', 'woodmart' ),
				'key'        => 'attr_term',
				'selector'   => '{{WRAPPER}} .shop_attributes td',
				'dependency' => array(
					'element' => 'hide_term_label',
					'value'   => 'no',
				),
			)
		);

		return array(
			'base'        => 'woodmart_single_product_additional_info_table',
			'name'        => esc_html__( 'Product additional information', 'woodmart' ),
			'category'    => woodmart_get_tab_title_category_for_wpb( esc_html__( 'Single product elements', 'woodmart' ), 'single_product' ),
			'description' => esc_html__( 'Attributes, dimensions, and weight', 'woodmart' ),
			'icon'        => WOODMART_ASSETS . '/images/vc-icon/sp-icons/sp-additional-information-table.svg',
			'params'      => array(
				array(
					'type'       => 'woodmart_css_id',
					'param_name' => 'woodmart_css_id',
				),

				array(
					'title'      => esc_html__( 'Title', 'woodmart' ),
					'type'       => 'woodmart_title_divider',
					'param_name' => 'title_divider',
				),

				array(
					'type'       => 'textfield',
					'heading'    => esc_html__( 'Element title', 'woodmart' ),
					'param_name' => 'title',
					'holder'     => 'div',
				),

				array(
					'heading'          => esc_html__( 'Color', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'param_name'       => 'title_color',
					'selectors'        => array(
						'{{WRAPPER}} .title-text' => array(
							'color: {{VALUE}};',
						),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				$typography['font_family'],
				$typography['font_size'],
				$typography['font_weight'],
				$typography['text_transform'],
				$typography['font_style'],
				$typography['text_decoration'],
				$typography['line_height'],

				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Icon type', 'woodmart' ),
					'param_name'       => 'icon_type',
					'value'            => array(
						esc_html__( 'Without icon', 'woodmart' ) => 'without',
						esc_html__( 'With icon', 'woodmart' ) => 'icon',
						esc_html__( 'With image', 'woodmart' ) => 'image',
					),
					'std'              => 'without',
					'edit_field_class' => 'vc_col-sm-12 vc_column',
				),
				array(
					'type'             => 'attach_image',
					'heading'          => esc_html__( 'Image', 'woodmart' ),
					'param_name'       => 'image',
					'value'            => '',
					'hint'             => esc_html__( 'Select image from media library.', 'woodmart' ),
					'dependency'       => array(
						'element' => 'icon_type',
						'value'   => 'image',
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'textfield',
					'heading'          => esc_html__( 'Image resolution', 'woodmart' ),
					'param_name'       => 'img_size',
					'hint'             => esc_html__( 'Enter image resolution. Example: \'thumbnail\', \'medium\', \'large\', \'full\' or other sizes defined by current theme. Alternatively enter image size in pixels: 200x50 (Width x Height). Leave empty to use \'thumbnail\' size.', 'woodmart' ),
					'dependency'       => array(
						'element' => 'icon_type',
						'value'   => 'image',
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Icon library', 'woodmart' ),
					'param_name'       => 'icon_library',
					'value'            => array(
						esc_html__( 'Font Awesome', 'woodmart' ) => 'fontawesome',
						esc_html__( 'Open Iconic', 'woodmart' ) => 'openiconic',
						esc_html__( 'Typicons', 'woodmart' ) => 'typicons',
						esc_html__( 'Entypo', 'woodmart' ) => 'entypo',
						esc_html__( 'Linecons', 'woodmart' ) => 'linecons',
						esc_html__( 'Mono Social', 'woodmart' ) => 'monosocial',
						esc_html__( 'Material', 'woodmart' ) => 'material',
					),
					'hint'             => esc_html__( 'Select icon library.', 'woodmart' ),
					'dependency'       => array(
						'element' => 'icon_type',
						'value'   => 'icon',
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'iconpicker',
					'heading'          => esc_html__( 'Icon', 'woodmart' ),
					'param_name'       => 'icon_fontawesome',
					'value'            => 'fa fa-regular fa-bell',
					'settings'         => array(
						'emptyIcon'    => false,
						'iconsPerPage' => 50,
					),
					'dependency'       => array(
						'element' => 'icon_library',
						'value'   => 'fontawesome',
					),
					'hint'             => esc_html__( 'Select icon from library.', 'woodmart' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'iconpicker',
					'heading'          => esc_html__( 'Icon', 'woodmart' ),
					'param_name'       => 'icon_openiconic',
					'settings'         => array(
						'emptyIcon'    => false,
						'type'         => 'openiconic',
						'iconsPerPage' => 50,
					),
					'dependency'       => array(
						'element' => 'icon_library',
						'value'   => 'openiconic',
					),
					'hint'             => esc_html__( 'Select icon from library.', 'woodmart' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'iconpicker',
					'heading'          => esc_html__( 'Icon', 'woodmart' ),
					'param_name'       => 'icon_typicons',
					'settings'         => array(
						'emptyIcon'    => false,
						'type'         => 'typicons',
						'iconsPerPage' => 50,
					),
					'dependency'       => array(
						'element' => 'icon_library',
						'value'   => 'typicons',
					),
					'hint'             => esc_html__( 'Select icon from library.', 'woodmart' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'iconpicker',
					'heading'          => esc_html__( 'Icon', 'woodmart' ),
					'param_name'       => 'icon_entypo',
					'settings'         => array(
						'emptyIcon'    => false,
						'type'         => 'entypo',
						'iconsPerPage' => 50,
					),
					'dependency'       => array(
						'element' => 'icon_library',
						'value'   => 'entypo',
					),
					'hint'             => esc_html__( 'Select icon from library.', 'woodmart' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'iconpicker',
					'heading'          => esc_html__( 'Icon', 'woodmart' ),
					'param_name'       => 'icon_linecons',
					'settings'         => array(
						'emptyIcon'    => false,
						'type'         => 'linecons',
						'iconsPerPage' => 50,
					),
					'dependency'       => array(
						'element' => 'icon_library',
						'value'   => 'linecons',
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'hint'             => esc_html__( 'Select icon from library.', 'woodmart' ),
				),
				array(
					'type'             => 'iconpicker',
					'heading'          => esc_html__( 'Icon', 'woodmart' ),
					'param_name'       => 'icon_monosocial',
					'settings'         => array(
						'emptyIcon'    => false,
						'type'         => 'monosocial',
						'iconsPerPage' => 50,
					),
					'dependency'       => array(
						'element' => 'icon_library',
						'value'   => 'monosocial',
					),
					'hint'             => esc_html__( 'Select icon from library.', 'woodmart' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'iconpicker',
					'heading'          => esc_html__( 'Icon', 'woodmart' ),
					'param_name'       => 'icon_material',
					'settings'         => array(
						'emptyIcon'    => false,
						'type'         => 'material',
						'iconsPerPage' => 50,
					),
					'dependency'       => array(
						'element' => 'icon_library',
						'value'   => 'material',
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'hint'             => esc_html__( 'Select icon from library.', 'woodmart' ),
				),
				array(
					'heading'          => esc_html__( 'Icons color', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'param_name'       => 'icons_color',
					'selectors'        => array(
						'{{WRAPPER}} .title-icon' => array(
							'color: {{VALUE}};',
						),
					),
					'dependency'       => array(
						'element' => 'icon_type',
						'value'   => array( 'icon' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'heading'          => esc_html__( 'Icon size', 'woodmart' ),
					'type'             => 'wd_slider',
					'param_name'       => 'icon_size',
					'selectors'        => array(
						'{{WRAPPER}} .title-icon' => array(
							'font-size: {{VALUE}}{{UNIT}};',
						),
					),
					'devices'          => array(
						'desktop' => array(
							'value' => '',
							'unit'  => 'px',
						),
					),
					'range'            => array(
						'px' => array(
							'min'  => 1,
							'max'  => 100,
							'step' => 1,
						),
					),
					'dependency'       => array(
						'element' => 'icon_type',
						'value'   => array( 'icon' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'title'      => esc_html__( 'Data source', 'woodmart' ),
					'type'       => 'woodmart_title_divider',
					'param_name' => 'general_divider',
				),

				array(
					'type'             => 'woodmart_button_set',
					'param_name'       => 'data_source',
					'tabs'             => true,
					'value'            => array(
						esc_html__( 'All', 'woodmart' ) => 'all',
						esc_html__( 'Include', 'woodmart' ) => 'include',
						esc_html__( 'Exclude', 'woodmart' ) => 'exclude',
					),
					'default'          => 'all',
					'edit_field_class' => 'vc_col-sm-12 vc_column',
				),

				array(
					'type'          => 'autocomplete',
					'heading'       => esc_html__( 'Include', 'woodmart' ),
					'param_name'    => 'include',
					'settings'      => array(
						'multiple'   => true,
						'sortable'   => true,
						'min_length' => 1,
					),
					'wd_dependency' => array(
						'element' => 'data_source',
						'value'   => array( 'include' ),
					),
				),

				array(
					'type'          => 'autocomplete',
					'heading'       => esc_html__( 'Exclude', 'woodmart' ),
					'param_name'    => 'exclude',
					'settings'      => array(
						'multiple'   => true,
						'sortable'   => true,
						'min_length' => 1,
					),
					'wd_dependency' => array(
						'element' => 'data_source',
						'value'   => array( 'exclude' ),
					),
				),

				// General.
				array(
					'title'      => esc_html__( 'General', 'woodmart' ),
					'group'      => esc_html__( 'Style', 'woodmart' ),
					'type'       => 'woodmart_title_divider',
					'param_name' => 'general_divider',
				),

				array(
					'heading'          => esc_html__( 'Layout', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'type'             => 'dropdown',
					'param_name'       => 'layout',
					'value'            => array(
						esc_html__( 'Default', 'woodmart' ) => 'grid',
						esc_html__( 'Justify', 'woodmart' ) => 'list',
						esc_html__( 'Inline', 'woodmart' ) => 'inline',
					),
					'std'              => 'list',
					'wood_tooltip'     => true,
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'heading'          => esc_html__( 'Style', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'type'             => 'dropdown',
					'param_name'       => 'style',
					'value'            => array(
						esc_html__( 'Default', 'woodmart' )  => 'default',
						esc_html__( 'Bordered', 'woodmart' ) => 'bordered',
					),
					'std'              => 'bordered',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'heading'    => esc_html__( 'Border type', 'woodmart' ),
					'group'      => esc_html__( 'Style', 'woodmart' ),
					'type'       => 'wd_select',
					'param_name' => 'items_border_type',
					'style'      => 'select',
					'selectors'  => array(
						'{{WRAPPER}}.wd-style-bordered .shop_attributes' => array(
							'--wd-attr-brd-style: {{VALUE}};',
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
						'element'            => 'style',
						'value_not_equal_to' => 'default',
					),
				),
				array(
					'heading'          => esc_html__( 'Border width', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'type'             => 'wd_slider',
					'param_name'       => 'items_border_width',
					'devices'          => array(
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
					'range'            => array(
						'px' => array(
							'min'  => 0,
							'max'  => 20,
							'step' => 1,
						),
					),
					'selectors'        => array(
						'{{WRAPPER}}.wd-style-bordered .shop_attributes' => array(
							'--wd-attr-brd-width: {{VALUE}}{{UNIT}};',
						),
					),
					'dependency'       => array(
						'element'            => 'items_border_type',
						'value_not_equal_to' => array( '', 'eyJkZXZpY2VzIjp7ImRlc2t0b3AiOnsidmFsdWUiOiJub25lIn19fQ==' ),
					),
					'wd_dependency'    => array(
						'element'            => 'style',
						'value_not_equal_to' => 'default',
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'heading'          => esc_html__( 'Border color', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'param_name'       => 'items_border_color',
					'selectors'        => array(
						'{{WRAPPER}}.wd-style-bordered .shop_attributes' => array(
							'--wd-attr-brd-color: {{VALUE}};',
						),
					),
					'dependency'       => array(
						'element'            => 'items_border_type',
						'value_not_equal_to' => array( '', 'eyJkZXZpY2VzIjp7ImRlc2t0b3AiOnsidmFsdWUiOiJub25lIn19fQ==' ),
					),
					'wd_dependency'    => array(
						'element'            => 'style',
						'value_not_equal_to' => 'default',
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'heading'    => esc_html__( 'Columns', 'woodmart' ),
					'group'      => esc_html__( 'Style', 'woodmart' ),
					'type'       => 'wd_slider',
					'param_name' => 'columns',
					'selectors'  => array(
						'{{WRAPPER}} .shop_attributes' => array(
							'--wd-attr-col: {{VALUE}};',
						),
					),
					'devices'    => array(
						'desktop' => array(
							'value' => '',
							'unit'  => '-',
						),
						'tablet'  => array(
							'value' => '',
							'unit'  => '-',
						),
						'mobile'  => array(
							'value' => '',
							'unit'  => '-',
						),
					),
					'range'      => array(
						'-' => array(
							'min'  => 1,
							'max'  => 6,
							'step' => 1,
						),
					),
				),

				array(
					'heading'    => esc_html__( 'Vertical spacing', 'woodmart' ),
					'group'      => esc_html__( 'Style', 'woodmart' ),
					'type'       => 'wd_slider',
					'param_name' => 'vertical_gap',
					'selectors'  => array(
						'{{WRAPPER}} .shop_attributes' => array(
							'--wd-attr-v-gap: {{VALUE}}{{UNIT}};',
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
							'max'  => 150,
							'step' => 1,
						),
					),
				),

				array(
					'heading'    => esc_html__( 'Horizontal spacing', 'woodmart' ),
					'group'      => esc_html__( 'Style', 'woodmart' ),
					'type'       => 'wd_slider',
					'param_name' => 'horizontal_gap',
					'selectors'  => array(
						'{{WRAPPER}} .shop_attributes' => array(
							'--wd-attr-h-gap: {{VALUE}}{{UNIT}};',
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
							'max'  => 150,
							'step' => 1,
						),
					),
				),

				// Attributes.
				array(
					'title'      => esc_html__( 'Attribute names', 'woodmart' ),
					'group'      => esc_html__( 'Style', 'woodmart' ),
					'type'       => 'woodmart_title_divider',
					'param_name' => 'attributes_divider',
				),

				array(
					'heading'     => esc_html__( 'Hide name', 'woodmart' ),
					'group'       => esc_html__( 'Style', 'woodmart' ),
					'type'        => 'woodmart_switch',
					'param_name'  => 'attr_hide_name',
					'true_state'  => 'yes',
					'false_state' => 'no',
					'default'     => 'no',
				),

				array(
					'heading'       => esc_html__( 'Width', 'woodmart' ),
					'group'         => esc_html__( 'Style', 'woodmart' ),
					'type'          => 'wd_slider',
					'param_name'    => 'attr_name_column_width',
					'selectors'     => array(
						'{{WRAPPER}} .shop_attributes th' => array(
							'width: {{VALUE}}{{UNIT}};',
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
							'min'  => 0,
							'max'  => 300,
							'step' => 1,
						),
						'%'  => array(
							'min'  => 1,
							'max'  => 100,
							'step' => 1,
						),
					),
					'wd_dependency' => array(
						'element' => 'attr_hide_name',
						'value'   => 'no',
					),
					'dependency'    => array(
						'element' => 'layout',
						'value'   => 'inline',
					),
				),

				$name_typography['font_family'],
				$name_typography['font_size'],
				$name_typography['font_weight'],
				$name_typography['text_transform'],
				$name_typography['font_style'],
				$name_typography['text_decoration'],
				$name_typography['line_height'],

				array(
					'heading'          => esc_html__( 'Color', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'param_name'       => 'attr_name_color',
					'selectors'        => array(
						'{{WRAPPER}} .shop_attributes th' => array(
							'color: {{VALUE}};',
						),
					),
					'dependency'       => array(
						'element' => 'attr_hide_name',
						'value'   => 'no',
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'heading'          => esc_html__( 'Hide image', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'type'             => 'woodmart_switch',
					'param_name'       => 'attr_hide_image',
					'true_state'       => 'yes',
					'false_state'      => 'no',
					'default'          => 'no',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'heading'    => esc_html__( 'Image width', 'woodmart' ),
					'group'      => esc_html__( 'Style', 'woodmart' ),
					'type'       => 'wd_slider',
					'param_name' => 'image_width',
					'selectors'  => array(
						'{{WRAPPER}} .shop_attributes' => array(
							'--wd-attr-img-width: {{VALUE}}{{UNIT}};',
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
							'max'  => 300,
							'step' => 1,
						),
					),
					'dependency' => array(
						'element' => 'attr_hide_image',
						'value'   => array( 'no' ),
					),
				),

				array(
					'title'      => esc_html__( 'Attribute terms', 'woodmart' ),
					'group'      => esc_html__( 'Style', 'woodmart' ),
					'type'       => 'woodmart_title_divider',
					'param_name' => 'terms_divider',
				),

				array(
					'heading'     => esc_html__( 'Hide name', 'woodmart' ),
					'group'       => esc_html__( 'Style', 'woodmart' ),
					'type'        => 'woodmart_switch',
					'param_name'  => 'hide_term_label',
					'true_state'  => 'yes',
					'false_state' => 'no',
					'default'     => 'no',
				),

				$term_typography['font_family'],
				$term_typography['font_size'],
				$term_typography['font_weight'],
				$term_typography['text_transform'],
				$term_typography['font_style'],
				$term_typography['text_decoration'],
				$term_typography['line_height'],

				array(
					'heading'          => esc_html__( 'Color', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'param_name'       => 'attr_term_color',
					'selectors'        => array(
						'{{WRAPPER}} .shop_attributes td' => array(
							'color: {{VALUE}};',
						),
					),
					'dependency'       => array(
						'element' => 'hide_term_label',
						'value'   => 'no',
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'heading'          => esc_html__( 'Link color', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'param_name'       => 'term_link_color',
					'selectors'        => array(
						'{{WRAPPER}} .shop_attributes td' => array(
							'--wd-link-color: {{VALUE}};',
						),
					),
					'dependency'       => array(
						'element' => 'hide_term_label',
						'value'   => 'no',
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'heading'          => esc_html__( 'Link color hover', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'type'             => 'wd_colorpicker',
					'param_name'       => 'term_link_color_hover',
					'selectors'        => array(
						'{{WRAPPER}} .shop_attributes td' => array(
							'--wd-link-color-hover: {{VALUE}};',
						),
					),
					'dependency'       => array(
						'element' => 'hide_term_label',
						'value'   => 'no',
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'heading'          => esc_html__( 'Hide image', 'woodmart' ),
					'group'            => esc_html__( 'Style', 'woodmart' ),
					'type'             => 'woodmart_switch',
					'param_name'       => 'term_hide_image',
					'true_state'       => 'yes',
					'false_state'      => 'no',
					'default'          => 'no',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'heading'    => esc_html__( 'Image width', 'woodmart' ),
					'group'      => esc_html__( 'Style', 'woodmart' ),
					'type'       => 'wd_slider',
					'param_name' => 'term_image_width',
					'selectors'  => array(
						'{{WRAPPER}} .shop_attributes' => array(
							'--wd-term-img-width: {{VALUE}}{{UNIT}};',
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
							'max'  => 300,
							'step' => 1,
						),
					),
					'dependency' => array(
						'element' => 'term_hide_image',
						'value'   => array( 'no' ),
					),
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
			),
		);
	}
}

if ( ! function_exists( 'woodmart_autocomplete_products_attributes_field_search' ) ) {
	/**
	 * Output search autocomplete results.
	 *
	 * @param string $search Search attribute.
	 *
	 * @return array
	 */
	function woodmart_autocomplete_products_attributes_field_search( $search ) {
		$data       = array();
		$attributes = woodmart_get_products_attributes();

		if ( $attributes ) {
			foreach ( $attributes as $key => $attribute ) {
				if ( false === strpos( strtolower( $attribute ), strtolower( $search ) ) ) {
					continue;
				}

				$data[] = array(
					'value' => $key,
					'label' => $attribute,
				);
			}
		}

		return $data;
	}

	add_filter( 'vc_autocomplete_woodmart_single_product_additional_info_table_include_callback', 'woodmart_autocomplete_products_attributes_field_search', 10, 1 );
	add_filter( 'vc_autocomplete_woodmart_single_product_additional_info_table_exclude_callback', 'woodmart_autocomplete_products_attributes_field_search', 10, 1 );
}

if ( ! function_exists( 'woodmart_autocomplete_products_attributes_field_render' ) ) {
	/**
	 * Render controls field.
	 *
	 * @param array $value Save value.
	 *
	 * @return array|bool
	 */
	function woodmart_autocomplete_products_attributes_field_render( $value ) {
		if ( empty( $value['value'] ) ) {
			return false;
		}

		$wc_attributes  = wc_get_attribute_taxonomy_labels();
		$all_attributes = array();

		$all_attributes['weight']     = esc_html__( 'Weight', 'woocommerce' );
		$all_attributes['dimensions'] = esc_html__( 'Dimensions', 'woocommerce' );

		if ( $wc_attributes ) {
			foreach ( $wc_attributes as $key => $attribute ) {
				$all_attributes[ 'pa_' . $key ] = $attribute . ' (pa_' . $key . ')';
			}
		}

		return empty( $all_attributes[ $value['value'] ] ) ? false : array(
			'label' => $all_attributes[ $value['value'] ],
			'value' => $value['value'],
		);
	}

	add_filter( 'vc_autocomplete_woodmart_single_product_additional_info_table_include_render', 'woodmart_autocomplete_products_attributes_field_render', 10, 1 );
	add_filter( 'vc_autocomplete_woodmart_single_product_additional_info_table_exclude_render', 'woodmart_autocomplete_products_attributes_field_render', 10, 1 );
}
