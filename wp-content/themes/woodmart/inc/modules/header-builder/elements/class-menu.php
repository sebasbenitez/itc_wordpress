<?php
/**
 * Menu element class.
 *
 * @package woodmart
 */

namespace XTS\Modules\Header_Builder\Elements;

use XTS\Modules\Header_Builder\Element;

/**
 *  Secondary menu element
 */
class Menu extends Element {
	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->template_name = 'menu';
	}

	/**
	 * Map element settings.
	 *
	 * @return void
	 */
	public function map() {
		$this->args = array(
			'type'            => 'menu',
			'title'           => esc_html__( 'Menu', 'woodmart' ),
			'text'            => esc_html__( 'Secondary menu', 'woodmart' ),
			'icon'            => 'xts-i-menu',
			'editable'        => true,
			'container'       => false,
			'drg'             => false,
			'drag_target_for' => array(),
			'drag_source'     => 'content_element',
			'edit_on_create'  => true,
			'removable'       => true,
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
						'{{WRAPPER}} > .wd-nav > li > a .wd-nav-img' => array(
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
						'{{WRAPPER}} > .wd-nav > li > a .wd-nav-img' => array(
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
				),
				'inline'                   => array(
					'id'          => 'inline',
					'title'       => esc_html__( 'Display inline', 'woodmart' ),
					'type'        => 'switcher',
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Extra', 'woodmart' ),
					'value'       => false,
					'description' => esc_html__( 'The width of the element will depend on its content', 'woodmart' ),
				),
			),
		);
	}
}
