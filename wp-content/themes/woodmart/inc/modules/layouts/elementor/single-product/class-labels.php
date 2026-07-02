<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Single product labels map.
 *
 * @package woodmart
 */

namespace XTS\Modules\Layouts;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Plugin;
use XTS\Modules\Custom_Labels\Frontend as Labels_Frontend;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Elementor widget that inserts an embeddable content into the page, from any given URL.
 */
class Product_Labels extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'wd_single_product_labels';
	}

	/**
	 * Get widget content.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Product labels', 'woodmart' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'wd-icon-sp-labels';
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
				'label' => esc_html__( 'Data source', 'woodmart' ),
			)
		);

		$this->add_control(
			'source',
			array(
				'label'   => esc_html__( 'Source', 'woodmart' ),
				'type'    => 'select',
				'options' => array(
					'all'     => array(
						'title' => esc_html__( 'All', 'woodmart' ),
					),
					'include' => array(
						'title' => esc_html__( 'Include only', 'woodmart' ),
					),
				),
				'default' => 'all',
			)
		);

		$this->add_control(
			'exclude',
			array(
				'label'       => esc_html__( 'Exclude', 'woodmart' ),
				'type'        => 'wd_autocomplete',
				'search'      => 'woodmart_get_posts_by_query',
				'render'      => 'woodmart_get_posts_title_by_id',
				'post_type'   => 'wd_custom_label',
				'query_type'  => 'product_labels',
				'multiple'    => true,
				'label_block' => true,
				'condition'   => array(
					'source' => array( 'all' ),
				),
			)
		);

		$this->add_control(
			'include',
			array(
				'label'       => esc_html__( 'Include', 'woodmart' ),
				'type'        => 'wd_autocomplete',
				'search'      => 'woodmart_get_posts_by_query',
				'render'      => 'woodmart_get_posts_title_by_id',
				'post_type'   => 'wd_custom_label',
				'query_type'  => 'product_labels',
				'multiple'    => true,
				'label_block' => true,
				'condition'   => array(
					'source' => array( 'include' ),
				),
			)
		);

		$this->end_controls_section();

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
				'default'      => 'wd-single-product-labels',
				'prefix_class' => '',
			)
		);

		$this->add_control(
			'orientation',
			array(
				'label'     => esc_html__( 'Orientation', 'woodmart' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'column' => esc_html__( 'Vertical', 'woodmart' ),
					'row'    => esc_html__( 'Horizontal', 'woodmart' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .wd-single-prod-labels' => 'flex-direction: {{VALUE}};',
				),
				'default'   => 'column',
			)
		);

		$this->add_responsive_control(
			'horizontal_align_v',
			array(
				'label'     => esc_html__( 'Horizontal alignment', 'woodmart' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'var(--wd-start)'  => array(
						'title' => esc_html__( 'Left', 'woodmart' ),
						'icon'  => 'eicon-h-align-left',
					),
					'var(--wd-center)' => array(
						'title' => esc_html__( 'Center', 'woodmart' ),
						'icon'  => 'eicon-h-align-center',
					),
					'var(--wd-end)'    => array(
						'title' => esc_html__( 'Right', 'woodmart' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default'   => '',
				'condition' => array(
					'orientation' => 'column',
				),
				'selectors' => array(
					'{{WRAPPER}} .wd-single-prod-labels' => 'align-items: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'horizontal_align_h',
			array(
				'label'     => esc_html__( 'Horizontal alignment', 'woodmart' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'var(--wd-start)'  => array(
						'title' => esc_html__( 'Left', 'woodmart' ),
						'icon'  => 'eicon-h-align-left',
					),
					'var(--wd-center)' => array(
						'title' => esc_html__( 'Center', 'woodmart' ),
						'icon'  => 'eicon-h-align-center',
					),
					'var(--wd-end)'    => array(
						'title' => esc_html__( 'Right', 'woodmart' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default'   => '',
				'condition' => array(
					'orientation' => 'row',
				),
				'selectors' => array(
					'{{WRAPPER}} .wd-single-prod-labels' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'vertical_align',
			array(
				'label'     => esc_html__( 'Vertical alignment', 'woodmart' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => esc_html__( 'Top', 'woodmart' ),
						'icon'  => 'eicon-v-align-top',
					),
					'center'     => array(
						'title' => esc_html__( 'Middle', 'woodmart' ),
						'icon'  => 'eicon-v-align-middle',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'Bottom', 'woodmart' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
				'default'   => '',
				'condition' => array(
					'orientation' => 'row',
				),
				'selectors' => array(
					'{{WRAPPER}} .wd-single-prod-labels' => 'align-items: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'labels_gap',
			array(
				'label'      => esc_html__( 'Gap', 'woodmart' ),
				'type'       => Controls_Manager::SLIDER,
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'size_units' => array( 'px' ),
				'default'    => array(
					'unit' => 'px',
					'size' => '',
				),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .wd-single-prod-labels' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( ! woodmart_woocommerce_installed() ) {
			return '';
		}

		Main::setup_preview();

		$output = array();

		$shape         = woodmart_get_opt( 'label_shape', 'rounded' );
		$label_classes = ' product-label';

		if ( 'rounded-sm' === $shape ) {
			$label_classes .= ' wd-shape-round-sm';
		} elseif ( 'rectangular' === $shape ) {
			$label_classes .= ' wd-shape-rect-sm';
		} elseif ( 'rounded' === $shape ) {
			$label_classes .= ' wd-shape-round';
		}

		$default_labels = Labels_Frontend::get_instance()->get_default_labels_output( $label_classes );
		$output         = array_merge( $output, $default_labels );

		$output = apply_filters(
			'woodmart_product_label_output',
			$output,
			array(
				'source'  => isset( $settings['source'] ) ? $settings['source'] : 'all',
				'include' => ! empty( $settings['include'] ) ? $settings['include'] : array(),
				'exclude' => ! empty( $settings['exclude'] ) ? $settings['exclude'] : array(),
			)
		);

		if ( ! $output ) {
			Main::restore_preview();
			return '';
		}

		woodmart_enqueue_inline_style( 'woo-mod-product-labels' );
		woodmart_enqueue_inline_style( 'woo-mod-product-labels-builder' );
		woodmart_enqueue_inline_style( 'woo-single-prod-el-labels' );

		if ( 'rounded' === $shape ) {
			woodmart_enqueue_inline_style( 'woo-mod-product-labels-round' );
		}

		?>
		<div class="wd-single-prod-labels product-labels">
			<?php echo implode( ' ', $output ); // phpcs:ignore ?>
		</div>
		<?php

		Main::restore_preview();
	}
}

Plugin::instance()->widgets_manager->register( new Product_Labels() );
