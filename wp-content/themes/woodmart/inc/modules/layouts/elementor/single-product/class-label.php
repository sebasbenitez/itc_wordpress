<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Product label map.
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
class Product_Label extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'wd_single_product_label';
	}

	/**
	 * Get widget content.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Product label', 'woodmart' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'wd-icon-sp-label';
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
			'general_section',
			array(
				'label' => esc_html__( 'General', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'css_classes',
			array(
				'type'         => 'wd_css_class',
				'default'      => 'wd-single-product-label',
				'prefix_class' => '',
			)
		);

		$this->add_control(
			'label_id',
			array(
				'label'       => esc_html__( 'Label', 'woodmart' ),
				'type'        => 'wd_autocomplete',
				'search'      => 'woodmart_get_posts_by_query',
				'render'      => 'woodmart_get_posts_title_by_id',
				'post_type'   => 'wd_custom_label',
				'query_type'  => 'product_label',
				'multiple'    => false,
				'label_block' => true,
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

		$this->add_responsive_control(
			'align',
			array(
				'label'     => esc_html__( 'Alignment', 'woodmart' ),
				'type'      => 'wd_buttons',
				'options'   => array(
					'var(--wd-start)'  => array(
						'title' => esc_html__( 'Left', 'woodmart' ),
						'image' => WOODMART_ASSETS_IMAGES . '/settings/align/left.jpg',
					),
					'var(--wd-center)' => array(
						'title' => esc_html__( 'Center', 'woodmart' ),
						'image' => WOODMART_ASSETS_IMAGES . '/settings/align/center.jpg',
					),
					'var(--wd-end)'    => array(
						'title' => esc_html__( 'Right', 'woodmart' ),
						'image' => WOODMART_ASSETS_IMAGES . '/settings/align/right.jpg',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .wd-single-prod-label' => 'justify-content: {{VALUE}};',
				),
				'default'   => 'left',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 */
	protected function render() {
		$settings = wp_parse_args(
			$this->get_settings_for_display(),
			array(
				'label_id' => false,
			)
		);

		if ( ! woodmart_woocommerce_installed() ) {
			return;
		}

		Main::setup_preview();

		$content       = '';
		$shape         = woodmart_get_opt( 'label_shape' );
		$label_classes = ' product-label';

		if ( 'rounded-sm' === $shape ) {
			$label_classes .= ' wd-shape-round-sm';
		} elseif ( 'rectangular' === $shape ) {
			$label_classes .= ' wd-shape-rect-sm';
		} elseif ( 'rounded' === $shape ) {
			$label_classes .= ' wd-shape-round';
		}

		$content .= Labels_Frontend::get_instance()->get_default_label_html( $settings['label_id'], $label_classes );

		if ( woodmart_get_opt( 'custom_labels' ) && ! in_array( $settings['label_id'], array( 'sale', 'out-of-stock', 'hot', 'new' ), true ) ) {
			$label_id = apply_filters( 'wpml_object_id', $settings['label_id'], 'wd_custom_label', true );
			$label    = get_post( $label_id );

			if ( ! $label || ! $label_id || 'publish' !== $label->post_status ) {
				return;
			}

			$ids_to_show = Labels_Frontend::get_instance()->manager->get_current_custom_labels_ids();
			if ( ! in_array( (int) $label_id, $ids_to_show, true ) ) {
				return;
			}

			$label_classes = Labels_Frontend::get_instance()->get_classes( $label_id );

			$content  = '<div class="' . esc_attr( $label_classes ) . '">';
			$content .= Labels_Frontend::get_instance()->get_content( $label_id );
			$content .= '</div>';
		}

		if ( ! $content ) {
			Main::restore_preview();

			return;
		}

		woodmart_enqueue_inline_style( 'woo-mod-product-labels' );
		woodmart_enqueue_inline_style( 'woo-mod-product-labels-builder' );
		woodmart_enqueue_inline_style( 'woo-single-prod-el-labels' );

		if ( 'rounded' === $shape ) {
			woodmart_enqueue_inline_style( 'woo-mod-product-labels-round' );
		}

		?>
		<div class="wd-single-prod-label">
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<?php

		Main::restore_preview();
	}
}

Plugin::instance()->widgets_manager->register( new Product_Label() );
