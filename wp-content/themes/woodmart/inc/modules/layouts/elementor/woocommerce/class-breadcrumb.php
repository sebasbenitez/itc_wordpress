<?php
/**
 * Breadcrumb map.
 *
 * @package woodmart
 */

namespace XTS\Modules\Layouts;

use Elementor\Group_Control_Typography;
use Elementor\Controls_Manager;
use Elementor\Plugin;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Elementor widget that inserts an embeddable content into the page, from any given URL.
 */
class Breadcrumb extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'wd_wc_breadcrumb';
	}

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'WooCommerce breadcrumbs', 'woodmart' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'wd-icon-sp-breadcrumb';
	}

	/**
	 * Get widget categories.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'wd-site-elements' );
	}

	/**
	 * Show in panel.
	 *
	 * @return bool Whether to show the widget in the panel or not.
	 */
	public function show_in_panel() {
		return Main::is_layout_type( 'single_product' ) || Main::is_layout_type( 'shop_archive' ) || Main::is_layout_type( 'checkout_form' ) || Main::is_layout_type( 'cart' ) || Main::is_layout_type( 'checkout_content' ) || Main::is_layout_type( 'thank_you_page' ) || Main::is_layout_type( 'my_account_page' ) || Main::is_layout_type( 'my_account_auth' ) || Main::is_layout_type( 'my_account_lost_password' );
	}

	/**
	 * Register the widget controls.
	 */
	protected function register_controls() {
		$breadcrumbs_selector = '{{WRAPPER}} .wd-breadcrumbs';

		/**
		 * Content tab
		 */

		/**
		 * General settings
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
				'default'      => 'wd-el-breadcrumbs',
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
			'nowrap_md',
			array(
				'label'        => esc_html__( 'No wrap on mobile devices', 'woodmart' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'md',
				'prefix_class' => 'wd-nowrap-',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Typography', 'woodmart' ),
				'selector' => $breadcrumbs_selector,
			)
		);

		$this->start_controls_tabs( 'text_color_tabs' );

		$this->start_controls_tab(
			'text_color_tab',
			array(
				'label' => esc_html__( 'Idle', 'woodmart' ),
			)
		);

		$this->add_control(
			'text_idle_color',
			array(
				'label'     => esc_html__( 'Color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$breadcrumbs_selector => '--wd-link-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'text_hover_color_tab',
			array(
				'label' => esc_html__( 'Hover', 'woodmart' ),
			)
		);

		$this->add_control(
			'text_hover_color',
			array(
				'label'     => esc_html__( 'Color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$breadcrumbs_selector => '--wd-link-color-hover: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'active_item_heading',
			array(
				'label'     => esc_html__( 'Current item', 'woodmart' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'active_item_typography',
				'label'    => esc_html__( 'Typography', 'woodmart' ),
				'selector' => '{{WRAPPER}} :is(.wd-last, .breadcrumb_last, .last, .aioseo-breadcrumbs span:last-child, .active)',
			)
		);

		$this->add_control(
			'text_active_color',
			array(
				'label'     => esc_html__( 'Color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$breadcrumbs_selector => '--wd-bcrumb-color-active: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'delimiter_heading',
			array(
				'label'     => esc_html__( 'Delimiter', 'woodmart' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'delimiter_color',
			array(
				'label'     => esc_html__( 'Color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$breadcrumbs_selector => '--wd-bcrumb-delim-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render breadcrumbs preview in the editor.
	 *
	 * Used to generate the control HTML in the editor using Underscore JS
	 * template. The variables for the class are available using `settings` JS
	 * object.
	 *
	 * @return void
	 */
	protected function content_template() {
		$separator = woodmart_get_opt( 'breadcrumbs_separator', '/' );
		?>
		<nav class="wd-breadcrumbs woocommerce-breadcrumb" aria-label="Breadcrumb">
			<a href="#">
				<?php echo esc_html__( 'Home', 'woodmart' ); ?>
			</a>
			<span class="wd-delimiter"><?php echo esc_html( $separator ); ?></span>
			<a href="#" class="wd-last-link">
				<?php echo esc_html__( 'Post', 'woodmart' ); ?>
			</a>
			<span class="wd-delimiter"><?php echo esc_html( $separator ); ?></span>
			<span class="wd-last">
				<?php echo esc_html__( 'Current Post', 'woodmart' ); ?>
			</span>
		</nav>
		<?php
	}

	/**
	 * Render the widget output on the frontend.
	 */
	protected function render() {
		$settings = wp_parse_args(
			$this->get_settings_for_display(),
			array(
				'nowrap_md' => '',
			)
		);

		if ( ! empty( $settings['nowrap_md'] ) ) {
			woodmart_enqueue_inline_style( 'mod-breadcrumbs-no-wrap' );
		}

		if ( woodmart_elementor_is_edit_mode() ) {
			return;
		}

		Main::setup_preview();
		woodmart_current_breadcrumbs( 'shop' );
		Main::restore_preview();
	}
}

Plugin::instance()->widgets_manager->register( new Breadcrumb() );
