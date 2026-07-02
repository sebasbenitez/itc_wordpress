<?php
/**
 * Additional information table map.
 *
 * @package woodmart
 */

namespace XTS\Modules\Layouts;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;
use Elementor\Widget_Base;
use XTS\Modules\Layouts\Global_Data as Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Elementor widget that inserts an embeddable content into the page, from any given URL.
 */
class Additional_Info_Table extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'wd_single_product_additional_info_table';
	}

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Product additional information', 'woodmart' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'wd-icon-sp-additional-information-table';
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
		 * Content tab.
		 */

		/**
		 * General settings.
		 */
		$this->start_controls_section(
			'general_section',
			array(
				'label' => esc_html__( 'Title', 'woodmart' ),
			)
		);

		$this->add_control(
			'title',
			array(
				'label' => esc_html__( 'Element title', 'woodmart' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'icon_type',
			array(
				'label'   => esc_html__( 'Icon type', 'woodmart' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'without' => esc_html__( 'Without icon', 'woodmart' ),
					'icon'    => esc_html__( 'With icon', 'woodmart' ),
					'image'   => esc_html__( 'With image', 'woodmart' ),
				),
				'default' => 'without',
			)
		);

		$this->add_control(
			'icon',
			array(
				'label'     => esc_html__( 'Icon', 'woodmart' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => array(
					'icon_type' => array( 'icon' ),
				),
			)
		);

		$this->add_control(
			'image',
			array(
				'label'     => esc_html__( 'Choose image', 'woodmart' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => array(
					'icon_type' => array( 'image' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'      => 'image',
				'default'   => 'thumbnail',
				'separator' => 'none',
				'condition' => array(
					'icon_type' => array( 'image' ),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'data_source_general_section',
			array(
				'label' => esc_html__( 'Data source', 'woodmart' ),
			)
		);

		$this->add_control(
			'data_source_type',
			array(
				'label'   => '',
				'type'    => 'wd_buttons',
				'options' => array(
					'all'     => array(
						'title' => esc_html__( 'All', 'woodmart' ),
					),
					'include' => array(
						'title' => esc_html__( 'Include', 'woodmart' ),
					),
					'exclude' => array(
						'title' => esc_html__( 'Exclude', 'woodmart' ),
					),
				),
				'default' => 'all',
			)
		);

		$this->add_control(
			'include',
			array(
				'label'       => esc_html__( 'Include', 'woodmart' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'default'     => array(),
				'options'     => woodmart_get_products_attributes(),
				'label_block' => true,
				'condition'   => array(
					'data_source_type' => array( 'include' ),
				),
			)
		);

		$this->add_control(
			'exclude',
			array(
				'label'       => esc_html__( 'Exclude', 'woodmart' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'default'     => array(),
				'options'     => woodmart_get_products_attributes(),
				'label_block' => true,
				'condition'   => array(
					'data_source_type' => array( 'exclude' ),
				),
			)
		);

		$this->end_controls_section();
		/**
		 * Layout settings.
		 */
		$this->start_controls_section(
			'layout_style_section',
			array(
				'label' => esc_html__( 'Layout', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'css_classes',
			array(
				'type'         => 'wd_css_class',
				'default'      => 'wd-single-attrs',
				'prefix_class' => '',
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'        => esc_html__( 'Layout', 'woodmart' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => array(
					'grid'   => esc_html__( 'Default', 'woodmart' ),
					'list'   => esc_html__( 'Justify', 'woodmart' ),
					'inline' => esc_html__( 'Inline', 'woodmart' ),
				),
				'prefix_class' => 'wd-layout-',
				'default'      => 'list',
			)
		);

		$this->add_control(
			'style',
			array(
				'label'        => esc_html__( 'Style', 'woodmart' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => array(
					'default'  => esc_html__( 'Default', 'woodmart' ),
					'bordered' => esc_html__( 'Bordered', 'woodmart' ),
				),
				'prefix_class' => 'wd-style-',
				'default'      => 'bordered',
			)
		);

		$this->add_control(
			'items_border_popover',
			array(
				'label'     => esc_html__( 'Border', 'woodmart' ),
				'type'      => Controls_Manager::POPOVER_TOGGLE,
				'condition' => array(
					'style' => 'bordered',
				),
			)
		);

		$this->start_popover();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'items_border',
				'fields_options' => array(
					'border' => array(
						'label'     => esc_html__( 'Type', 'woodmart' ),
						'selectors' => array(
							'{{WRAPPER}}.wd-style-bordered .shop_attributes' => '--wd-attr-brd-style: {{VALUE}}',
						),
					),
					'color'  => array(
						'label'     => esc_html__( 'Color', 'woodmart' ),
						'selectors' => array(
							'{{WRAPPER}}.wd-style-bordered .shop_attributes' => '--wd-attr-brd-color: {{VALUE}}',
						),
						'condition' => array(),
					),
					'width'  => array(
						'label'      => esc_html__( 'Width', 'woodmart' ),
						'type'       => Controls_Manager::SLIDER,
						'size_units' => array( 'px' ),
						'range'      => array(
							'px' => array(
								'min'  => 1,
								'max'  => 20,
								'step' => 1,
							),
						),
						'selectors'  => array(
							'{{WRAPPER}}.wd-style-bordered .shop_attributes' => '--wd-attr-brd-width: {{SIZE}}{{UNIT}};',
						),
						'condition'  => array(),
					),
				),
				'condition'      => array(
					'style' => 'bordered',
				),
			)
		);

		$this->end_popover();

		$this->add_responsive_control(
			'columns',
			array(
				'label'     => esc_html__( 'Columns', 'woodmart' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 1,
						'max'  => 6,
						'step' => 1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .shop_attributes' => '--wd-attr-col: {{SIZE}};',
				),
			)
		);

		$this->add_responsive_control(
			'vertical_gap',
			array(
				'label'     => esc_html__( 'Vertical spacing', 'woodmart' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 1,
						'max'  => 150,
						'step' => 1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .shop_attributes' => '--wd-attr-v-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'horizontal_gap',
			array(
				'label'     => esc_html__( 'Horizontal spacing', 'woodmart' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 1,
						'max'  => 150,
						'step' => 1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .shop_attributes' => '--wd-attr-h-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		/**
		 * Attributes settings.
		 */
		$this->start_controls_section(
			'attributes_style_section',
			array(
				'label' => esc_html__( 'Attribute names', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'attr_hide_name',
			array(
				'label'        => esc_html__( 'Hide name', 'woodmart' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => esc_html__( 'Yes', 'woodmart' ),
				'label_off'    => esc_html__( 'No', 'woodmart' ),
				'return_value' => 'yes',
			)
		);

		$this->add_responsive_control(
			'attr_name_column_width',
			array(
				'label'      => esc_html__( 'Width', 'woodmart' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'range'      => array(
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
				'selectors'  => array(
					'{{WRAPPER}} .shop_attributes th' => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'attr_hide_name!' => 'yes',
					'layout'          => 'inline',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'attr_name_typography',
				'label'     => esc_html__( 'Typography', 'woodmart' ),
				'selector'  => '{{WRAPPER}} .shop_attributes th',
				'condition' => array(
					'attr_hide_name!' => 'yes',
				),
			)
		);

		$this->add_control(
			'attr_name_color',
			array(
				'label'     => esc_html__( 'Color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .shop_attributes th' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'attr_hide_name!' => 'yes',
				),
			)
		);

		$this->add_control(
			'attr_divider',
			array(
				'type'  => Controls_Manager::DIVIDER,
				'style' => 'solid',
			)
		);

		$this->add_control(
			'attr_hide_image',
			array(
				'label'        => esc_html__( 'Hide image', 'woodmart' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => esc_html__( 'Yes', 'woodmart' ),
				'label_off'    => esc_html__( 'No', 'woodmart' ),
				'return_value' => 'yes',
			)
		);

		$this->add_responsive_control(
			'image_width',
			array(
				'label'     => esc_html__( 'Image width', 'woodmart' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 300,
						'step' => 1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .shop_attributes' => '--wd-attr-img-width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'attr_hide_image!' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		/**
		 * Terms settings.
		 */
		$this->start_controls_section(
			'terms_style_section',
			array(
				'label' => esc_html__( 'Attribute terms', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'hide_term_label',
			array(
				'label'        => esc_html__( 'Hide name', 'woodmart' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => esc_html__( 'Yes', 'woodmart' ),
				'label_off'    => esc_html__( 'No', 'woodmart' ),
				'return_value' => 'yes',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'attr_term_typography',
				'label'     => esc_html__( 'Typography', 'woodmart' ),
				'selector'  => '{{WRAPPER}} .shop_attributes td',
				'condition' => array(
					'hide_term_label!' => 'yes',
				),
			)
		);

		$this->add_control(
			'attr_term_color',
			array(
				'label'     => esc_html__( 'Color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .shop_attributes td' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'hide_term_label!' => 'yes',
				),
			)
		);

		$this->start_controls_tabs(
			'term_link_color_tabs',
			array(
				'condition' => array(
					'hide_term_label!' => 'yes',
				),
			)
		);

		$this->start_controls_tab(
			'term_link_color_tab',
			array(
				'label' => esc_html__( 'Idle', 'woodmart' ),
			)
		);

		$this->add_control(
			'term_link_color',
			array(
				'label'     => esc_html__( 'Link color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .shop_attributes td' => '--wd-link-color: {{VALUE}}',
				),
				'condition' => array(
					'hide_term_label!' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'term_link_color_hover_tab',
			array(
				'label' => esc_html__( 'Hover', 'woodmart' ),
			)
		);

		$this->add_control(
			'term_link_color_hover',
			array(
				'label'     => esc_html__( 'Link color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .shop_attributes td' => '--wd-link-color-hover: {{VALUE}}',
				),
				'condition' => array(
					'hide_term_label!' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'term_divider',
			array(
				'type'  => Controls_Manager::DIVIDER,
				'style' => 'solid',
			)
		);

		$this->add_control(
			'term_hide_image',
			array(
				'label'        => esc_html__( 'Hide image', 'woodmart' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => esc_html__( 'Yes', 'woodmart' ),
				'label_off'    => esc_html__( 'No', 'woodmart' ),
				'return_value' => 'yes',
			)
		);

		$this->add_responsive_control(
			'term_image_width',
			array(
				'label'     => esc_html__( 'Image width', 'woodmart' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 300,
						'step' => 1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .shop_attributes' => '--wd-term-img-width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'term_hide_image!' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'title_style_section',
			array(
				'label' => esc_html__( 'Title', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .title-text' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Typography', 'woodmart' ),
				'selector' => '{{WRAPPER}} .title-text',
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => esc_html__( 'Icon color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .img-wrapper' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'icon_type' => array( 'icon' ),
				),
			)
		);

		$this->add_control(
			'icon_size',
			array(
				'label'     => esc_html__( 'Icon size', 'woodmart' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 1,
						'max'  => 100,
						'step' => 1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .img-wrapper' => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'icon_type' => array( 'icon' ),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 */
	protected function render() {
		$settings    = $this->get_settings_for_display();
		$icon_output = '';

		if ( 'image' === $settings['icon_type'] && isset( $settings['image']['id'] ) && $settings['image']['id'] ) {
			$icon_output = woodmart_otf_get_image_html( $settings['image']['id'], $settings['image_size'], $settings['image_custom_dimension'] );

			if ( woodmart_is_svg( $settings['image']['url'] ) ) {
				if ( 'custom' === $settings['image_size'] && ! empty( $settings['image_custom_dimension'] ) ) {
					$icon_output = woodmart_get_svg_html( $settings['image']['id'], $settings['image_custom_dimension'] );
				} else {
					$icon_output = woodmart_get_svg_html( $settings['image']['id'], $settings['image_size'] );
				}
			}
		} elseif ( 'icon' === $settings['icon_type'] && $settings['icon'] ) {
			$icon_output = woodmart_elementor_get_render_icon( $settings['icon'] );
		}

		Main::setup_preview();

		global $product;

		$display_dimensions = apply_filters( 'wc_product_enable_dimensions_display', $product->has_weight() || $product->has_dimensions() );
		$attributes         = array_keys( array_filter( $product->get_attributes(), 'wc_attributes_array_filter_visible' ) );

		if ( $display_dimensions && $product->has_weight() ) {
			$attributes[] = 'weight';
		}

		if ( $display_dimensions && $product->has_dimensions() ) {
			$attributes[] = 'dimensions';
		}

		if ( $settings['include'] ) {
			if ( $settings['include'] === $settings['exclude'] || ! array_intersect( $attributes, $settings['include'] ) ) {
				Main::restore_preview();
				return;
			}

			Builder::get_instance()->set_data( 'wd_product_attributes_include', $settings['include'] );
		}

		if ( $settings['exclude'] ) {
			if ( ! array_diff( $attributes, $settings['exclude'] ) ) {
				Builder::get_instance()->set_data( 'wd_product_attributes_include', array() );
				Main::restore_preview();
				return;
			}

			Builder::get_instance()->set_data( 'wd_product_attributes_exclude', $settings['exclude'] );
		}

		if ( ! empty( $settings['title'] ) ) {
			?>
			<h4 class="wd-el-title title element-title">
				<?php if ( $icon_output ) : ?>
					<span class="img-wrapper">
						<?php echo $icon_output; // phpcs:ignore. ?>
					</span>
				<?php endif; ?>
				<span class="title-text">
					<?php echo esc_html( $settings['title'] ); ?>
				</span>
			</h4>
			<?php
		}

		Global_Data::get_instance()->set_data(
			'wd_additional_info_table_args',
			array(
				// Attributes.
				'attr_image' => isset( $settings['attr_hide_image'] ) && 'yes' !== $settings['attr_hide_image'],
				'attr_name'  => isset( $settings['attr_hide_name'] ) && 'yes' !== $settings['attr_hide_name'],
				// Terms.
				'term_label' => isset( $settings['hide_term_label'] ) && 'yes' !== $settings['hide_term_label'],
				'term_image' => isset( $settings['term_hide_image'] ) && 'yes' !== $settings['term_hide_image'],
			)
		);

		woodmart_enqueue_inline_style( 'woo-mod-shop-attributes-builder' );

		do_action( 'woocommerce_product_additional_information', $product );

		Global_Data::get_instance()->set_data( 'wd_additional_info_table_args', array() );

		Builder::get_instance()->set_data( 'wd_product_attributes_include', array() );
		Builder::get_instance()->set_data( 'wd_product_attributes_exclude', array() );

		Main::restore_preview();
	}
}

Plugin::instance()->widgets_manager->register( new Additional_Info_Table() );
