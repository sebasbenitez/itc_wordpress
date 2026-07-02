<?php
/**
 * Cart element class file.
 *
 * @package woodmart
 */

namespace XTS\Modules\Header_Builder\Elements;

use XTS\Modules\Header_Builder\Element;

/**
 *  Shopping cart widget element.
 */
class Cart extends Element {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();

		$this->template_name = 'cart';
	}

	/**
	 * Map element.
	 *
	 * @return void
	 */
	public function map() {
		$this->args = array(
			'type'            => 'cart',
			'title'           => esc_html__( 'Cart', 'woodmart' ),
			'text'            => esc_html__( 'Shopping widget', 'woodmart' ),
			'icon'            => 'xts-i-cart',
			'editable'        => true,
			'container'       => false,
			'edit_on_create'  => true,
			'drag_target_for' => array(),
			'drag_source'     => 'content_element',
			'removable'       => true,
			'addable'         => true,
			'params'          => array(
				'position'            => array(
					'id'      => 'position',
					'title'   => esc_html__( 'Position', 'woodmart' ),
					'type'    => 'selector',
					'tab'     => esc_html__( 'Style', 'woodmart' ),
					'group'   => esc_html__( 'General', 'woodmart' ),
					'value'   => 'side',
					'options' => array(
						'side'     => array(
							'value' => 'side',
							'label' => esc_html__( 'Hidden sidebar', 'woodmart' ),
							'hint'  => '<img src="' . WOODMART_TOOLTIP_URL . 'hb_cart_hidden_sidebar.jpg" alt="">',
						),
						'dropdown' => array(
							'value' => 'dropdown',
							'label' => esc_html__( 'Dropdown', 'woodmart' ),
							'hint'  => '<img src="' . WOODMART_TOOLTIP_URL . 'hb_cart_dropdown.jpg" alt="">',
						),
						'without'  => array(
							'value' => 'without',
							'label' => esc_html__( 'Without', 'woodmart' ),
						),
					),
				),
				'bg_dropdown_color'   => array(
					'id'        => 'bg_dropdown_color',
					'title'     => esc_html__( 'Background color', 'woodmart' ),
					'type'      => 'color',
					'tab'       => esc_html__( 'Style', 'woodmart' ),
					'group'     => esc_html__( 'Dropdown', 'woodmart' ),
					'value'     => '',
					'selectors' => array(
						'{{WRAPPER}} .wd-dropdown' => array(
							'--wd-dropdown-bg-color: {{VALUE}};',
						),
					),
					'condition' => array(
						'position' => array(
							'comparison' => 'equal',
							'value'      => 'dropdown',
						),
					),
				),
				'box_shadow_dropdown' => array(
					'id'        => 'box_shadow_dropdown',
					'title'     => esc_html__( 'Box shadow', 'woodmart' ),
					'tab'       => esc_html__( 'Style', 'woodmart' ),
					'group'     => esc_html__( 'Dropdown', 'woodmart' ),
					'type'      => 'group',
					'style'     => 'dropdown',
					'selectors' => array(
						'{{WRAPPER}} .wd-dropdown' => array(
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
					'condition' => array(
						'position' => array(
							'comparison' => 'equal',
							'value'      => 'dropdown',
						),
					),
				),
				'bg_overlay'          => array(
					'id'          => 'bg_overlay',
					'title'       => esc_html__( 'Background overlay', 'woodmart' ),
					'hint'        => '<img src="' . WOODMART_TOOLTIP_URL . 'hb_cart_bg_overlay.jpg" alt="">',
					'description' => __( 'Highlight dropdowns by darkening the background behind.', 'woodmart' ),
					'type'        => 'switcher',
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Dropdown', 'woodmart' ),
					'value'       => false,
					'condition'   => array(
						'position' => array(
							'comparison' => 'equal',
							'value'      => 'dropdown',
						),
					),
				),
				'design'              => array(
					'id'          => 'design',
					'title'       => esc_html__( 'Display', 'woodmart' ),
					'type'        => 'selector',
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Button', 'woodmart' ),
					'value'       => '',
					'options'     => array(
						'text-only'     => array(
							'value' => 'text-only',
							'label' => esc_html__( 'Text', 'woodmart' ),
						),
						'text-only-sub' => array(
							'value' => 'text-only-sub',
							'label' => esc_html__( 'Text with subtotal', 'woodmart' ),
						),
						'icon'          => array(
							'value' => 'icon',
							'label' => esc_html__( 'Icon', 'woodmart' ),
						),
						'text'          => array(
							'value' => 'text',
							'label' => esc_html__( 'Icon with subtotal', 'woodmart' ),
						),
					),
					'description' => esc_html__( 'Select whether to display the text, the icon, or include both along with the cart subtotal.', 'woodmart' ),
				),
				'style'               => array(
					'id'        => 'style',
					'title'     => esc_html__( 'Design', 'woodmart' ),
					'type'      => 'selector',
					'tab'       => esc_html__( 'Style', 'woodmart' ),
					'group'     => esc_html__( 'Button', 'woodmart' ),
					'value'     => '1',
					'options'   => array(
						'1' => array(
							'value' => '1',
							'label' => esc_html__( 'First', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/cart-icons/first.jpg',
						),
						'2' => array(
							'value' => '2',
							'label' => esc_html__( 'Second', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/cart-icons/second.jpg',
						),
						'3' => array(
							'value' => '3',
							'label' => esc_html__( 'Third', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/cart-icons/third.jpg',
						),
						'4' => array(
							'value' => '4',
							'label' => esc_html__( 'Fourth', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/cart-icons/fourth.jpg',
						),
						'6' => array(
							'value' => '6',
							'label' => esc_html__( 'Fifths', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/cart-icons/six.jpg',
						),
						'7' => array(
							'value' => '7',
							'label' => esc_html__( 'Sixth', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/cart-icons/seventh.jpg',
						),
						'8' => array(
							'value' => '8',
							'label' => esc_html__( 'Seventh', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/cart-icons/eighth.jpg',
						),
					),
					'condition' => array(
						'design' => array(
							'comparison' => 'not_equal',
							'value'      => array( 'text-only', 'text-only-sub' ),
						),
					),
				),
				'text_design'         => array(
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
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/cart-icons/text-first.jpg',
						),
						'4' => array(
							'value' => '4',
							'label' => esc_html__( 'Fourth', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/cart-icons/text-second.jpg',
						),
						'6' => array(
							'value' => '6',
							'label' => esc_html__( 'Sixth', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/cart-icons/text-third.jpg',
						),
						'7' => array(
							'value' => '7',
							'label' => esc_html__( 'Seventh', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/cart-icons/text-fourth.jpg',
						),
					),
					'condition' => array(
						'design' => array(
							'comparison' => 'equal',
							'value'      => array( 'text-only', 'text-only-sub' ),
						),
					),
				),
				'text_wrap_type'      => array(
					'id'        => 'text_wrap_type',
					'title'     => esc_html__( 'Background wrap type', 'woodmart' ),
					'type'      => 'selector',
					'tab'       => esc_html__( 'Style', 'woodmart' ),
					'group'     => esc_html__( 'Button', 'woodmart' ),
					'value'     => 'icon_only',
					'options'   => array(
						'icon_only'     => array(
							'value' => 'icon_only',
							'label' => esc_html__( 'Icon only', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/bg-wrap-type/cart-bg-wrap-icon.jpg',
						),
						'icon_and_text' => array(
							'value' => 'icon_and_text',
							'label' => esc_html__( 'Icon and text', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/bg-wrap-type/cart-bg-wrap-icon-and-text.jpg',
						),
					),
					'condition' => array(
						'design'      => array(
							'comparison' => 'equal',
							'value'      => array( 'text-only-sub' ),
						),
						'text_design' => array(
							'comparison' => 'equal',
							'value'      => array( '6', '7' ),
						),
					),
				),
				'text_color'          => array(
					'id'          => 'text_color',
					'title'       => esc_html__( 'Color', 'woodmart' ),
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Button', 'woodmart' ),
					'type'        => 'color',
					'value'       => '',
					'selectors'   => array(
						'whb-row .{{WRAPPER}}.wd-tools-element .wd-tools-inner, .whb-row .{{WRAPPER}}.wd-tools-element > a > .wd-tools-text-cart' => array(
							'color: {{VALUE}};',
						),
					),
					'condition'   => array(
						'design'      => array(
							'comparison' => 'equal',
							'value'      => array( 'text-only', 'text-only-sub' ),
						),
						'text_design' => array(
							'comparison' => 'equal',
							'value'      => array( '7' ),
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'text_hover_color'    => array(
					'id'          => 'text_hover_color',
					'title'       => esc_html__( 'Hover color', 'woodmart' ),
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Button', 'woodmart' ),
					'type'        => 'color',
					'value'       => '',
					'selectors'   => array(
						'whb-row .{{WRAPPER}}.wd-tools-element:hover .wd-tools-inner, .whb-row .{{WRAPPER}}.wd-tools-element:hover > a > .wd-tools-text-cart' => array(
							'color: {{VALUE}};',
						),
					),
					'condition'   => array(
						'design'      => array(
							'comparison' => 'equal',
							'value'      => array( 'text-only', 'text-only-sub' ),
						),
						'text_design' => array(
							'comparison' => 'equal',
							'value'      => array( '7' ),
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'text_bg_color'       => array(
					'id'          => 'text_bg_color',
					'title'       => esc_html__( 'Background color', 'woodmart' ),
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Button', 'woodmart' ),
					'type'        => 'color',
					'value'       => '',
					'selectors'   => array(
						'whb-row .{{WRAPPER}}.wd-tools-element .wd-tools-inner, .whb-row .{{WRAPPER}}.wd-tools-element > a > .wd-tools-text-cart' => array(
							'background-color: {{VALUE}};',
						),
					),
					'condition'   => array(
						'design'      => array(
							'comparison' => 'equal',
							'value'      => array( 'text-only', 'text-only-sub' ),
						),
						'text_design' => array(
							'comparison' => 'equal',
							'value'      => array( '7' ),
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'text_bg_hover_color' => array(
					'id'          => 'text_bg_hover_color',
					'title'       => esc_html__( 'Hover background color', 'woodmart' ),
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Button', 'woodmart' ),
					'type'        => 'color',
					'value'       => '',
					'selectors'   => array(
						'whb-row .{{WRAPPER}}.wd-tools-element:hover .wd-tools-inner, .whb-row .{{WRAPPER}}.wd-tools-element:hover > a > .wd-tools-text-cart' => array(
							'background-color: {{VALUE}};',
						),
					),
					'condition'   => array(
						'design'      => array(
							'comparison' => 'equal',
							'value'      => array( 'text-only', 'text-only-sub' ),
						),
						'text_design' => array(
							'comparison' => 'equal',
							'value'      => array( '7' ),
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'wrap_type'           => array(
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
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/bg-wrap-type/cart-bg-wrap-icon.jpg',
						),
						'icon_and_text' => array(
							'value' => 'icon_and_text',
							'label' => esc_html__( 'Icon and text', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/bg-wrap-type/cart-bg-wrap-icon-and-text.jpg',
						),
					),
					'condition' => array(
						'design' => array(
							'comparison' => 'equal',
							'value'      => 'text',
						),
						'style'  => array(
							'comparison' => 'equal',
							'value'      => array( '6', '7' ),
						),
					),
				),
				'color'               => array(
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
										'field'      => 'design',
										'comparison' => 'not_equal',
										'value'      => array( 'text-only' ),
									),
									array(
										'field'      => 'style',
										'comparison' => 'equal',
										'value'      => array( '7' ),
									),
								),
							),
							array(
								'relation' => 'and',
								'terms'    => array(
									array(
										'field'      => 'design',
										'comparison' => 'equal',
										'value'      => array( 'text' ),
									),
									array(
										'field'      => 'style',
										'comparison' => 'equal',
										'value'      => array( '8' ),
									),
								),
							),
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'hover_color'         => array(
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
										'field'      => 'design',
										'comparison' => 'not_equal',
										'value'      => array( 'text-only' ),
									),
									array(
										'field'      => 'style',
										'comparison' => 'equal',
										'value'      => array( '7' ),
									),
								),
							),
							array(
								'relation' => 'and',
								'terms'    => array(
									array(
										'field'      => 'design',
										'comparison' => 'equal',
										'value'      => array( 'text' ),
									),
									array(
										'field'      => 'style',
										'comparison' => 'equal',
										'value'      => array( '8' ),
									),
								),
							),
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'bg_color'            => array(
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
						'design' => array(
							'comparison' => 'not_equal',
							'value'      => array( 'text-only', 'text-only-sub' ),
						),
						'style'  => array(
							'comparison' => 'equal',
							'value'      => array( '7', '8' ),
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'bg_hover_color'      => array(
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
						'design' => array(
							'comparison' => 'not_equal',
							'value'      => array( 'text-only', 'text-only-sub' ),
						),
						'style'  => array(
							'comparison' => 'equal',
							'value'      => array( '7', '8' ),
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'icon_color'          => array(
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
						'design' => array(
							'comparison' => 'not_equal',
							'value'      => array( 'text-only', 'text-only-sub' ),
						),
						'style'  => array(
							'comparison' => 'equal',
							'value'      => '8',
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'icon_hover_color'    => array(
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
						'design' => array(
							'comparison' => 'not_equal',
							'value'      => array( 'text-only', 'text-only-sub' ),
						),
						'style'  => array(
							'comparison' => 'equal',
							'value'      => '8',
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'icon_bg_color'       => array(
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
						'design' => array(
							'comparison' => 'not_equal',
							'value'      => array( 'text-only', 'text-only-sub' ),
						),
						'style'  => array(
							'comparison' => 'equal',
							'value'      => '8',
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'icon_bg_hover_color' => array(
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
						'design' => array(
							'comparison' => 'not_equal',
							'value'      => array( 'text-only', 'text-only-sub' ),
						),
						'style'  => array(
							'comparison' => 'equal',
							'value'      => '8',
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'icon_type'           => array(
					'id'        => 'icon_type',
					'title'     => esc_html__( 'Icon type', 'woodmart' ),
					'type'      => 'selector',
					'tab'       => esc_html__( 'Style', 'woodmart' ),
					'group'     => esc_html__( 'Button', 'woodmart' ),
					'value'     => 'cart',
					'options'   => array(
						'cart'   => array(
							'value' => 'cart',
							'label' => esc_html__( 'Cart', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/cart-icons/cart.jpg',
						),
						'bag'    => array(
							'value' => 'bag',
							'label' => esc_html__( 'Bag', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/cart-icons/bag.jpg',
						),
						'custom' => array(
							'value' => 'custom',
							'label' => esc_html__( 'Custom', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/upload.jpg',
						),
					),
					'condition' => array(
						'design' => array(
							'comparison' => 'not_equal',
							'value'      => array( 'text-only', 'text-only-sub' ),
						),
					),
				),
				'custom_icon'         => array(
					'id'          => 'custom_icon',
					'title'       => esc_html__( 'Upload an image', 'woodmart' ),
					'type'        => 'image',
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Button', 'woodmart' ),
					'value'       => '',
					'description' => '',
					'condition'   => array(
						'design'    => array(
							'comparison' => 'not_equal',
							'value'      => array( 'text-only', 'text-only-sub' ),
						),
						'icon_type' => array(
							'comparison' => 'equal',
							'value'      => 'custom',
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'custom_icon_width'   => array(
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
						'design'    => array(
							'comparison' => 'not_equal',
							'value'      => array( 'text-only', 'text-only-sub' ),
						),
						'icon_type' => array(
							'comparison' => 'equal',
							'value'      => 'custom',
						),
					),
					'extra_class' => 'xts-col-6',
				),
			),
		);
	}
}
