<?php
/**
 * Main navigation menu element for Header Builder
 *
 * @package woodmart
 */

namespace XTS\Modules\Header_Builder\Elements;

use XTS\Modules\Header_Builder\Element;

/**
 *  Main navigation menu element
 */
class Mainmenu extends Element {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();

		$this->template_name = 'main-menu';
	}

	/**
	 * Map element settings
	 *
	 * @return void
	 */
	public function map() {
		$this->args = array(
			'type'            => 'mainmenu',
			'title'           => esc_html__( 'Main menu', 'woodmart' ),
			'text'            => esc_html__( 'Main navigation', 'woodmart' ),
			'icon'            => 'xts-i-main-menu',
			'editable'        => true,
			'container'       => false,
			'drg'             => false,
			'drag_target_for' => array(),
			'drag_source'     => 'content_element',
			'edit_on_create'  => true,
			'removable'       => true,
			'desktop'         => true,
			'addable'         => true,
			'params'          => array(
				'menu_id'                  => array(
					'id'          => 'menu_id',
					'title'       => esc_html__( 'Choose menu', 'woodmart' ),
					'type'        => 'select',
					'tab'         => esc_html__( 'General', 'woodmart' ),
					'value'       => '',
					'callback'    => 'get_menu_options_with_empty',
					'description' => esc_html__( 'Choose which menu to display in the header.', 'woodmart' ),
				),
				'full_screen'              => array(
					'id'          => 'full_screen',
					'title'       => esc_html__( 'Full screen menu', 'woodmart' ),
					'hint'        => '<video src="' . WOODMART_TOOLTIP_URL . 'hb_full_screen.mp4" autoplay loop muted></video>',
					'type'        => 'switcher',
					'tab'         => esc_html__( 'General', 'woodmart' ),
					'value'       => false,
					'description' => esc_html__( 'Enable to show your menu in full screen style on burger icon click.', 'woodmart' ),
				),
				'style'                    => array(
					'id'          => 'style',
					'title'       => esc_html__( 'Display', 'woodmart' ),
					'type'        => 'selector',
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Button', 'woodmart' ),
					'value'       => 'text',
					'options'     => array(
						'text-only' => array(
							'value' => 'text-only',
							'label' => esc_html__( 'Text', 'woodmart' ),
						),
						'icon'      => array(
							'value' => 'icon',
							'label' => esc_html__( 'Icon', 'woodmart' ),
						),
						'text'      => array(
							'value' => 'text',
							'label' => esc_html__( 'Icon with text', 'woodmart' ),
						),
					),
					'description' => esc_html__( 'Select whether to display only the icon, only the text, or both together.', 'woodmart' ),
					'condition'   => array(
						'full_screen' => array(
							'comparison' => 'equal',
							'value'      => true,
						),
					),
				),
				'icon_design'              => array(
					'id'        => 'icon_design',
					'title'     => esc_html__( 'Design', 'woodmart' ),
					'type'      => 'selector',
					'tab'       => esc_html__( 'Style', 'woodmart' ),
					'group'     => esc_html__( 'Button', 'woodmart' ),
					'value'     => '1',
					'options'   => array(
						'1' => array(
							'value' => '1',
							'label' => esc_html__( 'First', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/mobile-menu-icons/first.jpg',
						),
						'6' => array(
							'value' => '6',
							'label' => esc_html__( 'Second', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/mobile-menu-icons/second.jpg',
						),
						'7' => array(
							'value' => '7',
							'label' => esc_html__( 'Third', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/mobile-menu-icons/third.jpg',
						),
						'8' => array(
							'value' => '8',
							'label' => esc_html__( 'Fourth', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/mobile-menu-icons/fourth.jpg',
						),
					),
					'condition' => array(
						'full_screen' => array(
							'comparison' => 'equal',
							'value'      => true,
						),
						'style'       => array(
							'comparison' => 'not_equal',
							'value'      => array( 'text-only' ),
						),
					),
				),
				'text_design'              => array(
					'id'        => 'text_design',
					'title'     => esc_html__( 'Design', 'woodmart' ),
					'type'      => 'selector',
					'tab'       => esc_html__( 'Style', 'woodmart' ),
					'group'     => esc_html__( 'Button', 'woodmart' ),
					'value'     => '1',
					'options'   => array(
						'1' => array(
							'value' => '1',
							'label' => esc_html__( 'First', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/mobile-menu-icons/text-first.jpg',
						),
						'6' => array(
							'value' => '6',
							'label' => esc_html__( 'Second', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/mobile-menu-icons/text-second.jpg',
						),
						'7' => array(
							'value' => '7',
							'label' => esc_html__( 'Third', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/mobile-menu-icons/text-third.jpg',
						),
					),
					'condition' => array(
						'full_screen' => array(
							'comparison' => 'equal',
							'value'      => true,
						),
						'style'       => array(
							'comparison' => 'equal',
							'value'      => array( 'text-only' ),
						),
					),
				),
				'text_color'               => array(
					'id'          => 'text_color',
					'title'       => esc_html__( 'Color', 'woodmart' ),
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Button', 'woodmart' ),
					'type'        => 'color',
					'value'       => '',
					'selectors'   => array(
						'whb-row .{{WRAPPER}}.wd-tools-element .wd-tools-inner' => array(
							'color: {{VALUE}};',
						),
					),
					'condition'   => array(
						'style'       => array(
							'comparison' => 'equal',
							'value'      => array( 'text-only' ),
						),
						'text_design' => array(
							'comparison' => 'equal',
							'value'      => array( '7' ),
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'text_hover_color'         => array(
					'id'          => 'text_hover_color',
					'title'       => esc_html__( 'Hover color', 'woodmart' ),
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Button', 'woodmart' ),
					'type'        => 'color',
					'value'       => '',
					'selectors'   => array(
						'whb-row .{{WRAPPER}}.wd-tools-element:hover .wd-tools-inner' => array(
							'color: {{VALUE}};',
						),
					),
					'condition'   => array(
						'style'       => array(
							'comparison' => 'equal',
							'value'      => array( 'text-only' ),
						),
						'text_design' => array(
							'comparison' => 'equal',
							'value'      => array( '7' ),
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'text_bg_color'            => array(
					'id'          => 'text_bg_color',
					'title'       => esc_html__( 'Background color', 'woodmart' ),
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Button', 'woodmart' ),
					'type'        => 'color',
					'value'       => '',
					'selectors'   => array(
						'whb-row .{{WRAPPER}}.wd-tools-element .wd-tools-inner' => array(
							'background-color: {{VALUE}};',
						),
					),
					'condition'   => array(
						'style'       => array(
							'comparison' => 'equal',
							'value'      => array( 'text-only' ),
						),
						'text_design' => array(
							'comparison' => 'equal',
							'value'      => array( '7' ),
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'text_bg_hover_color'      => array(
					'id'          => 'text_bg_hover_color',
					'title'       => esc_html__( 'Hover background color', 'woodmart' ),
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Button', 'woodmart' ),
					'type'        => 'color',
					'value'       => '',
					'selectors'   => array(
						'whb-row .{{WRAPPER}}.wd-tools-element:hover .wd-tools-inner' => array(
							'background-color: {{VALUE}};',
						),
					),
					'condition'   => array(
						'style'       => array(
							'comparison' => 'equal',
							'value'      => array( 'text-only' ),
						),
						'text_design' => array(
							'comparison' => 'equal',
							'value'      => array( '7' ),
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'wrap_type'                => array(
					'id'        => 'wrap_type',
					'title'     => esc_html__( 'Background wrap type', 'woodmart' ),
					'type'      => 'selector',
					'tab'       => esc_html__( 'Style', 'woodmart' ),
					'group'     => esc_html__( 'Button', 'woodmart' ),
					'value'     => 'icon_only',
					'options'   => array(
						'icon_only'     => array(
							'value' => 'icon_only',
							'label' => esc_html__( 'Icon only', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/bg-wrap-type/menu-wrap-icon.jpg',
						),
						'icon_and_text' => array(
							'value' => 'icon_and_text',
							'label' => esc_html__( 'Icon and text', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/bg-wrap-type/menu-wrap-icon-and-text.jpg',
						),
					),
					'condition' => array(
						'full_screen' => array(
							'comparison' => 'equal',
							'value'      => true,
						),
						'style'       => array(
							'comparison' => 'equal',
							'value'      => 'text',
						),
						'icon_design' => array(
							'comparison' => 'equal',
							'value'      => array( '6', '7' ),
						),
					),
				),
				'color'                    => array(
					'id'          => 'color',
					'title'       => esc_html__( 'Color', 'woodmart' ),
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Button', 'woodmart' ),
					'type'        => 'color',
					'value'       => '',
					'selectors'   => array(
						'whb-row .{{WRAPPER}}.wd-tools-element .wd-tools-inner, .whb-row .{{WRAPPER}}.wd-tools-element > a > .wd-tools-icon' => array(
							'color: {{VALUE}};',
						),
					),
					'conditions'  => array(
						'relation' => 'or',
						'terms'    => array(
							array(
								'relation' => 'and',
								'terms'    => array(
									array(
										'field'      => 'style',
										'comparison' => 'not_equal',
										'value'      => array( 'text-only' ),
									),
									array(
										'field'      => 'icon_design',
										'comparison' => 'equal',
										'value'      => array( '7' ),
									),
								),
							),
							array(
								'relation' => 'and',
								'terms'    => array(
									array(
										'field'      => 'style',
										'comparison' => 'equal',
										'value'      => array( 'text' ),
									),
									array(
										'field'      => 'icon_design',
										'comparison' => 'equal',
										'value'      => array( '8' ),
									),
								),
							),
						),
					),
					'condition'   => array(
						'full_screen' => array(
							'comparison' => 'equal',
							'value'      => true,
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'hover_color'              => array(
					'id'          => 'hover_color',
					'title'       => esc_html__( 'Hover color', 'woodmart' ),
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Button', 'woodmart' ),
					'type'        => 'color',
					'value'       => '',
					'selectors'   => array(
						'whb-row .{{WRAPPER}}.wd-tools-element:hover .wd-tools-inner, .whb-row .{{WRAPPER}}.wd-tools-element:hover > a > .wd-tools-icon' => array(
							'color: {{VALUE}};',
						),
					),
					'conditions'  => array(
						'relation' => 'or',
						'terms'    => array(
							array(
								'relation' => 'and',
								'terms'    => array(
									array(
										'field'      => 'style',
										'comparison' => 'not_equal',
										'value'      => array( 'text-only' ),
									),
									array(
										'field'      => 'icon_design',
										'comparison' => 'equal',
										'value'      => array( '7' ),
									),
								),
							),
							array(
								'relation' => 'and',
								'terms'    => array(
									array(
										'field'      => 'style',
										'comparison' => 'equal',
										'value'      => array( 'text' ),
									),
									array(
										'field'      => 'icon_design',
										'comparison' => 'equal',
										'value'      => array( '8' ),
									),
								),
							),
						),
					),
					'condition'   => array(
						'full_screen' => array(
							'comparison' => 'equal',
							'value'      => true,
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'bg_color'                 => array(
					'id'          => 'bg_color',
					'title'       => esc_html__( 'Background color', 'woodmart' ),
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Button', 'woodmart' ),
					'type'        => 'color',
					'value'       => '',
					'selectors'   => array(
						'whb-row .{{WRAPPER}}.wd-tools-element .wd-tools-inner, .whb-row .{{WRAPPER}}.wd-tools-element > a > .wd-tools-icon' => array(
							'background-color: {{VALUE}};',
						),
					),
					'condition'   => array(
						'full_screen' => array(
							'comparison' => 'equal',
							'value'      => true,
						),
						'style'       => array(
							'comparison' => 'not_equal',
							'value'      => array( 'text-only' ),
						),
						'icon_design' => array(
							'comparison' => 'equal',
							'value'      => array( '7', '8' ),
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'bg_hover_color'           => array(
					'id'          => 'bg_hover_color',
					'title'       => esc_html__( 'Hover background color', 'woodmart' ),
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Button', 'woodmart' ),
					'type'        => 'color',
					'value'       => '',
					'selectors'   => array(
						'whb-row .{{WRAPPER}}.wd-tools-element:hover .wd-tools-inner, .whb-row .{{WRAPPER}}.wd-tools-element:hover > a > .wd-tools-icon' => array(
							'background-color: {{VALUE}};',
						),
					),
					'condition'   => array(
						'full_screen' => array(
							'comparison' => 'equal',
							'value'      => true,
						),
						'style'       => array(
							'comparison' => 'not_equal',
							'value'      => array( 'text-only' ),
						),
						'icon_design' => array(
							'comparison' => 'equal',
							'value'      => array( '7', '8' ),
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'icon_color'               => array(
					'id'          => 'icon_color',
					'title'       => esc_html__( 'Icon color', 'woodmart' ),
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Button', 'woodmart' ),
					'type'        => 'color',
					'value'       => '',
					'selectors'   => array(
						'{{WRAPPER}}.wd-tools-element.wd-design-8 .wd-tools-icon' => array(
							'color: {{VALUE}};',
						),
					),
					'condition'   => array(
						'full_screen' => array(
							'comparison' => 'equal',
							'value'      => true,
						),
						'style'       => array(
							'comparison' => 'not_equal',
							'value'      => array( 'text-only' ),
						),
						'icon_design' => array(
							'comparison' => 'equal',
							'value'      => '8',
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'icon_hover_color'         => array(
					'id'          => 'icon_hover_color',
					'title'       => esc_html__( 'Hover icon color', 'woodmart' ),
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Button', 'woodmart' ),
					'type'        => 'color',
					'value'       => '',
					'selectors'   => array(
						'{{WRAPPER}}.wd-tools-element.wd-design-8:hover .wd-tools-icon' => array(
							'color: {{VALUE}};',
						),
					),
					'condition'   => array(
						'full_screen' => array(
							'comparison' => 'equal',
							'value'      => true,
						),
						'style'       => array(
							'comparison' => 'not_equal',
							'value'      => array( 'text-only' ),
						),
						'icon_design' => array(
							'comparison' => 'equal',
							'value'      => '8',
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'icon_bg_color'            => array(
					'id'          => 'icon_bg_color',
					'title'       => esc_html__( 'Icon background color', 'woodmart' ),
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Button', 'woodmart' ),
					'type'        => 'color',
					'value'       => '',
					'selectors'   => array(
						'{{WRAPPER}}.wd-tools-element.wd-design-8 .wd-tools-icon' => array(
							'background-color: {{VALUE}};',
						),
					),
					'condition'   => array(
						'full_screen' => array(
							'comparison' => 'equal',
							'value'      => true,
						),
						'style'       => array(
							'comparison' => 'not_equal',
							'value'      => array( 'text-only' ),
						),
						'icon_design' => array(
							'comparison' => 'equal',
							'value'      => '8',
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'icon_bg_hover_color'      => array(
					'id'          => 'icon_bg_hover_color',
					'title'       => esc_html__( 'Hover icon background color', 'woodmart' ),
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Button', 'woodmart' ),
					'type'        => 'color',
					'value'       => '',
					'selectors'   => array(
						'{{WRAPPER}}.wd-tools-element.wd-design-8:hover .wd-tools-icon' => array(
							'background-color: {{VALUE}};',
						),
					),
					'condition'   => array(
						'full_screen' => array(
							'comparison' => 'equal',
							'value'      => true,
						),
						'style'       => array(
							'comparison' => 'not_equal',
							'value'      => array( 'text-only' ),
						),
						'icon_design' => array(
							'comparison' => 'equal',
							'value'      => '8',
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'icon_type'                => array(
					'id'        => 'icon_type',
					'title'     => esc_html__( 'Icon type', 'woodmart' ),
					'type'      => 'selector',
					'tab'       => esc_html__( 'Style', 'woodmart' ),
					'group'     => esc_html__( 'Button', 'woodmart' ),
					'value'     => 'default',
					'options'   => array(
						'default' => array(
							'value' => 'default',
							'label' => esc_html__( 'Default', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/default-icons/burger-default.jpg',
						),
						'custom'  => array(
							'value' => 'custom',
							'label' => esc_html__( 'Custom', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/upload.jpg',
						),
					),
					'condition' => array(
						'full_screen' => array(
							'comparison' => 'equal',
							'value'      => true,
						),
						'style'       => array(
							'comparison' => 'not_equal',
							'value'      => array( 'text-only' ),
						),
					),
				),
				'custom_icon'              => array(
					'id'          => 'custom_icon',
					'title'       => esc_html__( 'Upload an image', 'woodmart' ),
					'type'        => 'image',
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Button', 'woodmart' ),
					'value'       => '',
					'description' => '',
					'condition'   => array(
						'icon_type' => array(
							'comparison' => 'equal',
							'value'      => 'custom',
						),
						'style'     => array(
							'comparison' => 'not_equal',
							'value'      => array( 'text-only' ),
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'custom_icon_width'        => array(
					'id'          => 'custom_icon_width',
					'title'       => esc_html__( 'Icon width', 'woodmart' ),
					'type'        => 'slider',
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Button', 'woodmart' ),
					'from'        => 0,
					'to'          => 60,
					'value'       => 0,
					'units'       => 'px',
					'selectors'   => array(
						'{{WRAPPER}}' => array(
							'--wd-tools-icon-width: {{VALUE}}px;',
						),
					),
					'condition'   => array(
						'icon_type' => array(
							'comparison' => 'equal',
							'value'      => 'custom',
						),
						'style'     => array(
							'comparison' => 'not_equal',
							'value'      => array( 'text-only' ),
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'menu_style'               => array(
					'id'          => 'menu_style',
					'title'       => esc_html__( 'Style', 'woodmart' ),
					'type'        => 'selector',
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Items', 'woodmart' ),
					'value'       => 'default',
					'options'     => array(
						'default'   => array(
							'value' => 'default',
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/menu-style/default.jpg',
							'label' => esc_html__( 'Default', 'woodmart' ),
						),
						'underline' => array(
							'value' => 'underline',
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/menu-style/underline.jpg',
							'label' => esc_html__( 'Underline', 'woodmart' ),
						),
						'bordered'  => array(
							'value' => 'bordered',
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/menu-style/bordered.jpg',
							'label' => esc_html__( 'Bordered', 'woodmart' ),
						),
						'separated' => array(
							'value' => 'separated',
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/menu-style/separated.jpg',
							'label' => esc_html__( 'Separated', 'woodmart' ),
						),
						'bg'        => array(
							'value' => 'bg',
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/menu-style/background.jpg',
							'label' => esc_html__( 'Background', 'woodmart' ),
						),
					),
					'description' => esc_html__( 'You can change menu style in the header.', 'woodmart' ),
					'condition'   => array(
						'full_screen' => array(
							'comparison' => 'equal',
							'value'      => false,
						),
					),
				),
				'menu_align'               => array(
					'id'          => 'menu_align',
					'title'       => esc_html__( 'Alignment', 'woodmart' ),
					'type'        => 'selector',
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Items', 'woodmart' ),
					'value'       => 'left',
					'options'     => array(
						'left'   => array(
							'value' => 'left',
							'label' => esc_html__( 'Left', 'woodmart' ),
						),
						'center' => array(
							'value' => 'center',
							'label' => esc_html__( 'Center', 'woodmart' ),
						),
						'right'  => array(
							'value' => 'right',
							'label' => esc_html__( 'Right', 'woodmart' ),
						),
					),
					'description' => esc_html__( 'Set the menu items text align.', 'woodmart' ),
					'condition'   => array(
						'full_screen' => array(
							'comparison' => 'equal',
							'value'      => false,
						),
					),
				),
				'items_gap'                => array(
					'id'          => 'items_gap',
					'title'       => esc_html__( 'Gap', 'woodmart' ),
					'type'        => 'selector',
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Items', 'woodmart' ),
					'value'       => 's',
					'options'     => array(
						's'      => array(
							'value' => 's',
							'label' => esc_html__( 'Small', 'woodmart' ),
						),
						'm'      => array(
							'value' => 'm',
							'label' => esc_html__( 'Medium', 'woodmart' ),
						),
						'l'      => array(
							'value' => 'l',
							'label' => esc_html__( 'Large', 'woodmart' ),
						),
						'custom' => array(
							'value' => 'custom',
							'label' => esc_html__( 'Custom', 'woodmart' ),
						),
					),
					'description' => esc_html__( 'Set the items gap.', 'woodmart' ),
					'condition'   => array(
						'full_screen' => array(
							'comparison' => 'equal',
							'value'      => false,
						),
					),
				),
				'items_custom_gap'         => array(
					'id'          => 'items_custom_gap',
					'title'       => esc_html__( 'Custom gap', 'woodmart' ),
					'type'        => 'slider',
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Items', 'woodmart' ),
					'from'        => 0,
					'to'          => 100,
					'value'       => '',
					'units'       => 'px',
					'selectors'   => array(
						'{{WRAPPER}} > .wd-nav' => array(
							'--nav-gap: {{VALUE}}px;',
						),
					),
					'conditions'  => array(
						'relation' => 'and',
						'terms'    => array(
							array(
								'field'      => 'full_screen',
								'comparison' => 'equal',
								'value'      => array( false ),
							),
							array(
								'field'      => 'items_gap',
								'comparison' => 'equal',
								'value'      => array( 'custom' ),
							),
						),
					),
					'extra_class' => 'xts-col-12',
				),
				'items_color'              => array(
					'id'          => 'items_color',
					'title'       => esc_html__( 'Color', 'woodmart' ),
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Items', 'woodmart' ),
					'type'        => 'color',
					'value'       => '',
					'selectors'   => array(
						'{{WRAPPER}}.wd-header-nav .wd-nav' => array(
							'--nav-color: {{VALUE}};',
						),
					),
					'condition'   => array(
						'full_screen' => array(
							'comparison' => 'equal',
							'value'      => false,
						),
					),
					'extra_class' => 'xts-col-4',
				),
				'items_color_hover'        => array(
					'id'          => 'items_color_hover',
					'title'       => esc_html__( 'Hover color', 'woodmart' ),
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Items', 'woodmart' ),
					'type'        => 'color',
					'value'       => '',
					'selectors'   => array(
						'{{WRAPPER}}.wd-header-nav .wd-nav' => array(
							'--nav-color-hover: {{VALUE}};',
						),
					),
					'condition'   => array(
						'full_screen' => array(
							'comparison' => 'equal',
							'value'      => false,
						),
					),
					'extra_class' => 'xts-col-4',
				),
				'items_color_active'       => array(
					'id'          => 'items_color_active',
					'title'       => esc_html__( 'Active color', 'woodmart' ),
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Items', 'woodmart' ),
					'type'        => 'color',
					'value'       => '',
					'selectors'   => array(
						'{{WRAPPER}}.wd-header-nav .wd-nav' => array(
							'--nav-color-active: {{VALUE}};',
						),
					),
					'condition'   => array(
						'full_screen' => array(
							'comparison' => 'equal',
							'value'      => false,
						),
					),
					'extra_class' => 'xts-col-4',
				),
				'items_bg_color'           => array(
					'id'          => 'items_bg_color',
					'title'       => esc_html__( 'Background color', 'woodmart' ),
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Items', 'woodmart' ),
					'type'        => 'color',
					'value'       => '',
					'selectors'   => array(
						'{{WRAPPER}}.wd-header-nav .wd-nav' => array(
							'--nav-bg: {{VALUE}};',
						),
					),
					'condition'   => array(
						'full_screen' => array(
							'comparison' => 'equal',
							'value'      => false,
						),
					),
					'extra_class' => 'xts-col-4',
				),
				'items_bg_color_hover'     => array(
					'id'          => 'items_bg_color_hover',
					'title'       => esc_html__( 'Hover background color', 'woodmart' ),
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Items', 'woodmart' ),
					'type'        => 'color',
					'value'       => '',
					'selectors'   => array(
						'{{WRAPPER}}.wd-header-nav .wd-nav' => array(
							'--nav-bg-hover: {{VALUE}};',
						),
					),
					'condition'   => array(
						'full_screen' => array(
							'comparison' => 'equal',
							'value'      => false,
						),
					),
					'extra_class' => 'xts-col-4',
				),
				'items_bg_color_active'    => array(
					'id'          => 'items_bg_color_active',
					'title'       => esc_html__( 'Active background color', 'woodmart' ),
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Items', 'woodmart' ),
					'type'        => 'color',
					'value'       => '',
					'selectors'   => array(
						'{{WRAPPER}}.wd-header-nav .wd-nav' => array(
							'--nav-bg-active: {{VALUE}};',
						),
					),
					'condition'   => array(
						'full_screen' => array(
							'comparison' => 'equal',
							'value'      => false,
						),
					),
					'extra_class' => 'xts-col-4',
				),
				'dropdown_indicator_color' => array(
					'id'        => 'dropdown_indicator_color',
					'title'     => esc_html__( 'Dropdown indicator color', 'woodmart' ),
					'tab'       => esc_html__( 'Style', 'woodmart' ),
					'group'     => esc_html__( 'Items', 'woodmart' ),
					'type'      => 'color',
					'value'     => '',
					'selectors' => array(
						'{{WRAPPER}}.wd-header-nav .wd-nav' => array(
							'--nav-chevron-color: {{VALUE}};',
						),
					),
				),
				'icon_width'               => array(
					'id'          => 'icon_width',
					'title'       => esc_html__( 'Width', 'woodmart' ),
					'hint'        => '<video src="' . WOODMART_TOOLTIP_URL . 'hb_icon_width.mp4" autoplay loop muted></video>',
					'type'        => 'slider',
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Items icon', 'woodmart' ),
					'from'        => 0,
					'to'          => 60,
					'value'       => '',
					'units'       => 'px',
					'selectors'   => array(
						'{{WRAPPER}} > .wd-nav > li > a .wd-nav-img, .wd-fs-menu .wd-nav-fs > li > a .wd-nav-img' => array(
							'--nav-img-width: {{VALUE}}px;',
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'icon_height'              => array(
					'id'          => 'icon_height',
					'title'       => esc_html__( 'Height', 'woodmart' ),
					'hint'        => '<video src="' . WOODMART_TOOLTIP_URL . 'hb_icon_height.mp4" autoplay loop muted></video>',
					'type'        => 'slider',
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Items icon', 'woodmart' ),
					'from'        => 0,
					'to'          => 60,
					'value'       => '',
					'units'       => 'px',
					'selectors'   => array(
						'{{WRAPPER}} > .wd-nav > li > a .wd-nav-img, .wd-fs-menu .wd-nav-fs > li > a .wd-nav-img' => array(
							'--nav-img-height: {{VALUE}}px;',
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'bg_dropdown_color'        => array(
					'id'        => 'bg_dropdown_color',
					'title'     => esc_html__( 'Background color', 'woodmart' ),
					'type'      => 'color',
					'tab'       => esc_html__( 'Style', 'woodmart' ),
					'group'     => esc_html__( 'Dropdown', 'woodmart' ),
					'value'     => '',
					'selectors' => array(
						'{{WRAPPER}} > ul > li > .wd-dropdown-menu, .{{WRAPPER}} .wd-design-default .wd-dropdown' => array(
							'--wd-dropdown-bg-color: {{VALUE}};',
						),
					),
				),
				'box_shadow_dropdown'      => array(
					'id'        => 'box_shadow_dropdown',
					'title'     => esc_html__( 'Box shadow', 'woodmart' ),
					'tab'       => esc_html__( 'Style', 'woodmart' ),
					'group'     => esc_html__( 'Dropdown', 'woodmart' ),
					'type'      => 'group',
					'style'     => 'dropdown',
					'selectors' => array(
						'{{WRAPPER}} > ul > li > .wd-dropdown-menu, .{{WRAPPER}} .wd-design-default .wd-dropdown' => array(
							'--wd-dropdown-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{BORDER_TYPE}};',
						),
					),
					'value'     => array(),
					'fields'    => array(
						'color'       => array(
							'id'          => 'color',
							'title'       => esc_html__( 'Color', 'woodmart' ),
							'type'        => 'color',
							'value'       => '',
							'extra_class' => 'xts-col-6',
						),
						'border_type' => array(
							'id'                 => 'border_type',
							'title'              => esc_html__( 'Border type', 'woodmart' ),
							'type'               => 'selector',
							'value'              => 'outline',
							'options'            => array(
								'outline' => array(
									'value' => 'outline',
									'label' => esc_html__( 'Outline', 'woodmart' ),
								),
								'inset'   => array(
									'value' => 'inset',
									'label' => esc_html__( 'Inset', 'woodmart' ),
								),
							),
							'allowed_css_values' => array( 'inset' ),
							'extra_class'        => 'xts-col-6',
						),
						'horizontal'  => array(
							'id'            => 'horizontal',
							'title'         => esc_html__( 'Horizontal offset', 'woodmart' ),
							'type'          => 'slider',
							'from'          => -100,
							'to'            => 100,
							'value'         => '0',
							'generate_zero' => true,
							'units'         => 'px',
							'extra_class'   => 'xts-col-6',
						),
						'vertical'    => array(
							'id'            => 'vertical',
							'title'         => esc_html__( 'Vertical offset', 'woodmart' ),
							'type'          => 'slider',
							'from'          => -100,
							'to'            => 100,
							'value'         => '0',
							'generate_zero' => true,
							'units'         => 'px',
							'extra_class'   => 'xts-col-6',
						),
						'blur'        => array(
							'id'            => 'blur',
							'title'         => esc_html__( 'Blur', 'woodmart' ),
							'type'          => 'slider',
							'from'          => 0,
							'to'            => 100,
							'value'         => 10,
							'generate_zero' => true,
							'units'         => 'px',
							'extra_class'   => 'xts-col-6',
						),
						'spread'      => array(
							'id'            => 'spread',
							'title'         => esc_html__( 'Spread', 'woodmart' ),
							'type'          => 'slider',
							'from'          => -100,
							'to'            => 100,
							'value'         => '0',
							'generate_zero' => true,
							'units'         => 'px',
							'extra_class'   => 'xts-col-6',
						),
					),
				),
				'bg_overlay'               => array(
					'id'          => 'bg_overlay',
					'title'       => esc_html__( 'Background overlay', 'woodmart' ),
					'hint'        => '<video src="' . WOODMART_TOOLTIP_URL . 'hb_bg_overlay.mp4" autoplay loop muted></video>',
					'description' => __( 'Highlight dropdowns by darkening the background behind.', 'woodmart' ),
					'type'        => 'switcher',
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Dropdown', 'woodmart' ),
					'value'       => false,
					'condition'   => array(
						'full_screen' => array(
							'comparison' => 'equal',
							'value'      => false,
						),
					),
				),
				'inline'                   => array(
					'id'          => 'inline',
					'title'       => esc_html__( 'Display inline', 'woodmart' ),
					'type'        => 'switcher',
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Extra', 'woodmart' ),
					'value'       => false,
					'description' => esc_html__( 'The width of the element will depend on its content', 'woodmart' ),
					'condition'   => array(
						'full_screen' => array(
							'comparison' => 'equal',
							'value'      => false,
						),
					),
				),
			),
		);
	}
}
