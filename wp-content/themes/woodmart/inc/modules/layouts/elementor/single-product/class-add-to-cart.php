<?php
/**
 * Add to cart map.
 *
 * @package woodmart
 */

namespace XTS\Modules\Layouts;

use WC_Product;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Plugin;
use XTS\Modules\Layouts\Global_Data as Builder;
use XTS\Modules\Waitlist\Frontend as Waitlist_Frontend;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Elementor widget that inserts an embeddable content into the page, from any given URL.
 */
class Add_To_Cart extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'wd_single_product_add_to_cart';
	}

	/**
	 * Get widget content.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Product add to cart', 'woodmart' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'wd-icon-sp-add-to-cart';
	}

	/**
	 * Get widget categories.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'wd-single-product-elements' );
	}

	/**
	 * Show in panel.
	 *
	 * @return bool Whether to show the widget in the panel or not.
	 */
	public function show_in_panel() {
		return Main::is_layout_type( 'single_product' );
	}

	/**
	 * Register the widget controls.
	 */
	protected function register_controls() {
		/**
		 * General styles.
		 */
		$this->start_controls_section(
			'general_style_section',
			array(
				'label' => esc_html__( 'General', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'css_classes',
			array(
				'type'         => 'wd_css_class',
				'default'      => 'wd-single-add-cart',
				'prefix_class' => '',
			)
		);

		$this->add_control(
			'alignment',
			array(
				'label'        => esc_html__( 'Alignment', 'woodmart' ),
				'type'         => 'wd_buttons',
				'options'      => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'woodmart' ),
						'image' => WOODMART_ASSETS_IMAGES . '/settings/align/left.jpg',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'woodmart' ),
						'image' => WOODMART_ASSETS_IMAGES . '/settings/align/center.jpg',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'woodmart' ),
						'image' => WOODMART_ASSETS_IMAGES . '/settings/align/right.jpg',
					),
				),
				'prefix_class' => 'text-',
				'default'      => 'left',
			)
		);

		$this->add_control(
			'button_design',
			array(
				'label'        => esc_html__( 'Stretch', 'woodmart' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'woodmart' ),
				'label_off'    => esc_html__( 'No', 'woodmart' ),
				'default'      => 'default',
				'return_value' => 'full',
				'prefix_class' => 'wd-btn-design-',
			)
		);

		$this->end_controls_section();

		/**
		 * Add to cart button styles.
		 */
		$this->start_controls_section(
			'add_to_cart_button_style_section',
			array(
				'label' => esc_html__( 'Add to cart button', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'add_to_cart_design',
			array(
				'label'        => esc_html__( 'Design', 'woodmart' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => array(
					'default'  => esc_html__( 'Inherit from Theme Settings', 'woodmart' ),
					'bordered' => esc_html__( 'Bordered', 'woodmart' ),
					'flat'     => esc_html__( 'Flat', 'woodmart' ),
				),
				'default'      => 'default',
				'prefix_class' => 'wd-atc-btn-style-',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'label'    => esc_html__( 'Typography', 'woodmart' ),
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .single_add_to_cart_button',
			)
		);

		$this->start_controls_tabs( 'add_to_cart_tabs' );

		$this->start_controls_tab(
			'add_to_cart_normal_tab',
			array(
				'label' => esc_html__( 'Normal', 'woodmart' ),
			)
		);

		$this->add_control(
			'add_to_cart_color_normal',
			array(
				'label'     => esc_html__( 'Text color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .single_add_to_cart_button' => '--btn-accented-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'add_to_cart_bg_color_normal',
			array(
				'label'     => esc_html__( 'Background color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .single_add_to_cart_button' => '--btn-accented-bgcolor: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'add_to_cart_border',
				'label'     => esc_html__( 'Border', 'woodmart' ),
				'selector'  => '{{WRAPPER}} .single_add_to_cart_button',
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'add_to_cart_border_radius',
			array(
				'label'      => esc_html__( 'Border radius', 'woodmart' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .single_add_to_cart_button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'add_to_cart_border_border!' => array( '', 'none' ),
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'add_to_cart_hover_tab',
			array(
				'label' => esc_html__( 'Hover', 'woodmart' ),
			)
		);

		$this->add_control(
			'add_to_cart_color_hover',
			array(
				'label'     => esc_html__( 'Text color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .single_add_to_cart_button' => '--btn-accented-color-hover: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'add_to_cart_bg_color_hover',
			array(
				'label'     => esc_html__( 'Background color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .single_add_to_cart_button' => '--btn-accented-bgcolor-hover: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'add_to_cart_border_hover',
				'label'     => esc_html__( 'Border', 'woodmart' ),
				'selector'  => '{{WRAPPER}} .single_add_to_cart_button:hover',
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'add_to_cart_border_radius_hover',
			array(
				'label'      => esc_html__( 'Border radius', 'woodmart' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .single_add_to_cart_button:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'add_to_cart_border_hover_border!' => array( '', 'none' ),
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		/**
		 * Buy now button styles.
		 */
		$this->start_controls_section(
			'buy_now_button_style_section',
			array(
				'label' => esc_html__( 'Buy now button', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'buy_now_design',
			array(
				'label'        => esc_html__( 'Design', 'woodmart' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => array(
					'default'  => esc_html__( 'Inherit from Theme Settings', 'woodmart' ),
					'bordered' => esc_html__( 'Bordered', 'woodmart' ),
					'flat'     => esc_html__( 'Flat', 'woodmart' ),
				),
				'default'      => 'default',
				'prefix_class' => 'wd-bn-btn-style-',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'label'    => esc_html__( 'Typography', 'woodmart' ),
				'name'     => 'buy_now_typography',
				'selector' => '{{WRAPPER}} .wd-buy-now-btn',
			)
		);

		$this->start_controls_tabs( 'buy_now_tabs' );

		$this->start_controls_tab(
			'buy_now_normal_tab',
			array(
				'label' => esc_html__( 'Normal', 'woodmart' ),
			)
		);

		$this->add_control(
			'buy_now_color_normal',
			array(
				'label'     => esc_html__( 'Text color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wd-buy-now-btn' => '--btn-accented-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'buy_now_bg_color_normal',
			array(
				'label'     => esc_html__( 'Background color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wd-buy-now-btn' => '--btn-accented-bgcolor: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'buy_now_border',
				'label'     => esc_html__( 'Border', 'woodmart' ),
				'selector'  => '{{WRAPPER}} .wd-buy-now-btn',
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'buy_now_border_radius',
			array(
				'label'      => esc_html__( 'Border radius', 'woodmart' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .wd-buy-now-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'buy_now_border_border!' => array( '', 'none' ),
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'buy_now_hover_tab',
			array(
				'label' => esc_html__( 'Hover', 'woodmart' ),
			)
		);

		$this->add_control(
			'buy_now_color_hover',
			array(
				'label'     => esc_html__( 'Text color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wd-buy-now-btn' => '--btn-accented-color-hover: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'buy_now_bg_color_hover',
			array(
				'label'     => esc_html__( 'Background color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wd-buy-now-btn' => '--btn-accented-bgcolor-hover: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'buy_now_border_hover',
				'label'     => esc_html__( 'Border', 'woodmart' ),
				'selector'  => '{{WRAPPER}} .wd-buy-now-btn:hover',
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'buy_now_border_radius_hover`',
			array(
				'label'      => esc_html__( 'Border radius', 'woodmart' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .wd-buy-now-btn:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'buy_now_border_hover_border!' => array( '', 'none' ),
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		/**
		 * Variable product styles.
		 */
		$this->start_controls_section(
			'variable_product_style_section',
			array(
				'label' => esc_html__( 'Variable product', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'design',
			array(
				'label'        => esc_html__( 'Design', 'woodmart' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => array(
					'default' => esc_html__( 'Default', 'woodmart' ),
					'justify' => esc_html__( 'Justify', 'woodmart' ),
				),
				'prefix_class' => 'wd-design-',
				'default'      => 'default',
			)
		);

		$this->add_control(
			'swatch_layout',
			array(
				'label'        => esc_html__( 'Swatches layout', 'woodmart' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => array(
					'default' => esc_html__( 'Default', 'woodmart' ),
					'inline'  => esc_html__( 'Inline', 'woodmart' ),
				),
				'prefix_class' => 'wd-swatch-layout-',
				'default'      => 'default',
			)
		);

		$this->add_responsive_control(
			'reset_button_position',
			array(
				'label'          => esc_html__( 'Clear button position', 'woodmart' ),
				'type'           => Controls_Manager::SELECT,
				'options'        => array(
					'side'   => esc_html__( 'Side', 'woodmart' ),
					'bottom' => esc_html__( 'Bottom', 'woodmart' ),
				),
				'devices'        => array( 'desktop', 'mobile' ),
				'classes'        => 'wd-hide-custom-breakpoints wd-hide-tablet-breakpoint',
				'default'        => 'side',
				'mobile_default' => 'side',
			)
		);

		$this->add_responsive_control(
			'label_position',
			array(
				'label'          => esc_html__( 'Swatch label position', 'woodmart' ),
				'type'           => Controls_Manager::SELECT,
				'options'        => array(
					'side' => esc_html__( 'Side', 'woodmart' ),
					'top'  => esc_html__( 'Top', 'woodmart' ),
					'hide' => esc_html__( 'Hide', 'woodmart' ),
				),
				'devices'        => array( 'desktop', 'mobile' ),
				'classes'        => 'wd-hide-custom-breakpoints wd-hide-tablet-breakpoint',
				'default'        => 'side',
				'mobile_default' => 'side',
				'render_type'    => 'template',
			)
		);

		$this->start_controls_tabs( 'price_settings_tabs' );

		$this->start_controls_tab(
			'main_price_tab',
			array(
				'label' => esc_html__( 'Price', 'woodmart' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'label'    => esc_html__( 'Typography', 'woodmart' ),
				'name'     => 'main_price_typography',
				'selector' => '{{WRAPPER}} .variations_form .woocommerce-variation-price .price',
			)
		);

		$this->add_control(
			'main_price_text_color',
			array(
				'label'     => esc_html__( 'Text color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .variations_form .woocommerce-variation-price :is(.price, del)' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'old_price_tab',
			array(
				'label' => esc_html__( 'Old price', 'woodmart' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'label'    => esc_html__( 'Typography', 'woodmart' ),
				'name'     => 'old_price_typography',
				'selector' => '{{WRAPPER}} .variations_form .woocommerce-variation-price .price del',
			)
		);

		$this->add_control(
			'old_price_text_color',
			array(
				'label'     => esc_html__( 'Text color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .variations_form .woocommerce-variation-price .price del' => 'color: {{VALUE}}',
				),
			)
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'suffix_tab',
			array(
				'label' => esc_html__( 'Suffix', 'woodmart' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'label'    => esc_html__( 'Typography', 'woodmart' ),
				'name'     => 'suffix_typography',
				'selector' => '{{WRAPPER}} .woocommerce-price-suffix',
			)
		);

		$this->add_control(
			'suffix_text_color',
			array(
				'label'     => esc_html__( 'Text color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-price-suffix' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		/**
		 * Stock status styles.
		 */
		$this->start_controls_section(
			'stock_status_style_section',
			array(
				'label' => esc_html__( 'Stock status', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'enable_stock_status',
			array(
				'label'        => esc_html__( 'Enable stock status', 'woodmart' ),
				'description'  => esc_html__( 'If "NO" stock status will be removed.', 'woodmart' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => esc_html__( 'Yes', 'woodmart' ),
				'label_off'    => esc_html__( 'No', 'woodmart' ),
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'stock_status_css_classes',
			array(
				'type'         => 'wd_css_class',
				'default'      => 'wd-stock-status-off',
				'prefix_class' => '',
				'condition'    => array(
					'enable_stock_status!' => array( 'yes' ),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 */
	protected function render() {
		$default_settings = array(
			'design'                       => 'default',
			'reset_button_position'        => 'side',
			'reset_button_position_mobile' => 'side',
			'label_position'               => 'side',
			'label_position_mobile'        => 'side',
			'product_id'                   => false,
			'enable_stock_status'          => 'yes',
		);

		$settings = wp_parse_args( $this->get_settings_for_display(), $default_settings );

		if ( ! is_user_logged_in() && woodmart_get_opt( 'login_prices' ) ) {
			return;
		}

		$product = wc_get_product( $settings['product_id'] );

		if (
			woodmart_get_opt( 'catalog_mode' ) &&
			(
				$product instanceof WC_Product &&
				'variable' !== $product->get_type()
			)
		) {
			return;
		}

		$form_classes  = ' wd-reset-' . $settings['reset_button_position'] . '-lg';
		$form_classes .= ' wd-reset-' . $settings['reset_button_position_mobile'] . '-md';

		$form_classes .= ' wd-label-' . $settings['label_position'] . '-lg';
		$form_classes .= ' wd-label-' . $settings['label_position_mobile'] . '-md';

		if ( 'justify' === $settings['design'] ) {
			woodmart_enqueue_inline_style( 'woo-single-prod-el-add-to-cart-opt-design-justify-builder' );

			remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation' );
			add_action( 'woocommerce_before_variations_form', 'woocommerce_single_variation' );
		}

		Builder::get_instance()->set_data( 'form_classes', $form_classes );
		Builder::get_instance()->set_data( 'layout_id', get_the_ID() );

		Main::setup_preview( array(), $settings['product_id'] );

		woocommerce_template_single_add_to_cart();

		if ( ! woodmart_get_opt( 'catalog_mode' ) && woodmart_get_opt( 'waitlist_enabled' ) && ( ! woodmart_get_opt( 'waitlist_for_loggined' ) || is_user_logged_in() ) ) {
			$waitlist_frontend = Waitlist_Frontend::get_instance();

			if ( woodmart_elementor_is_edit_mode() || woodmart_elementor_is_preview_page() || woodmart_elementor_is_preview_mode() ) {
				$waitlist_frontend->render_waitlist_subscribe_form_on_elementor_edit_page();
			} else {
				$waitlist_frontend->render_waitlist_subscribe_form();
				$waitlist_frontend->render_template_subscribe_form();
			}
		}

		Main::restore_preview( $settings['product_id'] );
	}
}

Plugin::instance()->widgets_manager->register( new Add_To_Cart() );
