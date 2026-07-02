<?php
/**
 * Image map.
 *
 * @package woodmart
 */

namespace XTS\Elementor;

use Elementor\Utils;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Plugin;
use Elementor\Group_Control_Image_Size;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Elementor widget that inserts an embeddable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class Image extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'wd_image_or_svg';
	}

	/**
	 * Get widget Text block.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget Text block.
	 */
	public function get_title() {
		return esc_html__( 'Image or SVG', 'woodmart' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'wd-icon-image-or-svg';
	}

	/**
	 * Get widget categories.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'wd-elements' );
	}

	/**
	 * Register the widget controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		/**
		 * Content tab.
		 */

		/**
		 * General settings.
		 */
		$this->start_controls_section(
			'general_content_section',
			array(
				'label' => esc_html__( 'General', 'woodmart' ),
			)
		);

		$this->add_control(
			'image',
			array(
				'label'   => esc_html__( 'Choose image', 'woodmart' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'      => 'image',
				'default'   => 'full',
				'separator' => 'none',
			)
		);

		$this->add_control(
			'size',
			array(
				'label'       => esc_html__( 'Size', 'woodmart' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					''             => esc_html__( 'Default', 'woodmart' ),
					'aspect_ratio' => esc_html__( 'Aspect ratio', 'woodmart' ),
					'custom'       => esc_html__( 'Custom', 'woodmart' ),
				),
				'render_type' => 'ui',
				'default'     => '',
			)
		);

		$this->add_responsive_control(
			'height',
			array(
				'label'     => esc_html__( 'Height', 'woodmart' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(),
				'range'     => array(
					'px' => array(
						'min'  => 1,
						'max'  => 2000,
						'step' => 1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} img' => 'height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'size' => 'custom',
				),
			)
		);

		$this->add_responsive_control(
			'aspect_ratio',
			array(
				'label'     => esc_html__( 'Aspect ratio', 'woodmart' ),
				'type'      => Controls_Manager::SELECT,
				'selectors' => array(
					'{{WRAPPER}} img' => '--wd-aspect-ratio: {{VALUE}};',
				),
				'options'   => array(
					'revert-layer' => esc_html__( 'As image', 'woodmart' ),
					'16/9'         => '16:9',
					'16/10'        => '16:10',
					'21/9'         => '21:9',
					'4/3'          => '4:3',
					'3/2'          => '3:2',
					'1/1'          => '1:1',
					'9/16'         => '9:16',
					'initial'      => esc_html__( 'Custom', 'woodmart' ),
				),
				'default'   => 'revert-layer',
				'condition' => array(
					'size' => 'aspect_ratio',
				),
			)
		);

		$this->add_responsive_control(
			'custom_aspect_ratio',
			array(
				'label'       => esc_html__( 'Custom aspect ratio', 'woodmart' ),
				'type'        => Controls_Manager::TEXT,
				'selectors'   => array(
					'{{WRAPPER}} img' => '--wd-aspect-ratio: {{VALUE}};',
				),
				'placeholder' => '16/9',
				'ai'          => array(
					'active' => false,
				),
				'condition'   => array(
					'size'         => 'aspect_ratio',
					'aspect_ratio' => 'initial',
				),
			)
		);

		$this->add_responsive_control(
			'object_fit',
			array(
				'label'      => esc_html__( 'Object-fit', 'woodmart' ),
				'type'       => Controls_Manager::SELECT,
				'default'    => '',
				'options'    => array(
					''        => esc_html__( 'Default', 'woodmart' ),
					'cover'   => esc_html__( 'Cover', 'woodmart' ),
					'contain' => esc_html__( 'Contain', 'woodmart' ),
					'fill'    => esc_html__( 'Fill', 'woodmart' ),
					'none'    => esc_html__( 'None', 'woodmart' ),
				),
				'selectors'  => array(
					'{{WRAPPER}} img' => 'object-fit: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'size',
							'operator' => '===',
							'value'    => 'custom',
						),
						array(
							'relation' => 'and',
							'terms'    => array(
								array(
									'name'     => 'size',
									'operator' => '===',
									'value'    => 'aspect_ratio',
								),
								array(
									'name'     => 'aspect_ratio',
									'operator' => '!==',
									'value'    => 'revert-layer',
								),
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'object_position',
			array(
				'label'      => esc_html__( 'Object position', 'woodmart' ),
				'type'       => Controls_Manager::SELECT,
				'default'    => '',
				'options'    => array(
					''              => esc_html__( 'Default', 'woodmart' ),
					'left top'      => esc_html__( 'Left Top', 'woodmart' ),
					'left center'   => esc_html__( 'Left Center', 'woodmart' ),
					'left bottom'   => esc_html__( 'Left Bottom', 'woodmart' ),
					'center top'    => esc_html__( 'Center Top', 'woodmart' ),
					'center center' => esc_html__( 'Center Center', 'woodmart' ),
					'center bottom' => esc_html__( 'Center Bottom', 'woodmart' ),
					'right top'     => esc_html__( 'Right Top', 'woodmart' ),
					'right center'  => esc_html__( 'Right Center', 'woodmart' ),
					'right bottom'  => esc_html__( 'Right Bottom', 'woodmart' ),
					'initial'       => esc_html__( 'Custom', 'woodmart' ),
				),
				'selectors'  => array(
					'{{WRAPPER}} img' => 'object-position: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'size',
							'operator' => '===',
							'value'    => 'custom',
						),
						array(
							'relation' => 'and',
							'terms'    => array(
								array(
									'name'     => 'size',
									'operator' => '===',
									'value'    => 'aspect_ratio',
								),
								array(
									'name'     => 'aspect_ratio',
									'operator' => '!==',
									'value'    => 'revert-layer',
								),
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'object_position_x',
			array(
				'label'      => esc_html__( 'X Position', 'woodmart' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => -800,
						'max'  => 800,
						'step' => 1,
					),
				),
				'default'    => array(),
				'selectors'  => array(
					'{{WRAPPER}} img' => 'object-position: {{SIZE}}{{UNIT}} {{object_position_y.SIZE}}{{object_position_y.UNIT}}',
				),
				'condition'  => array(
					'object_position' => 'initial',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'size',
							'operator' => '===',
							'value'    => 'custom',
						),
						array(
							'relation' => 'and',
							'terms'    => array(
								array(
									'name'     => 'size',
									'operator' => '===',
									'value'    => 'aspect_ratio',
								),
								array(
									'name'     => 'aspect_ratio',
									'operator' => '!==',
									'value'    => 'revert-layer',
								),
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'object_position_y',
			array(
				'label'       => esc_html__( 'Y Position', 'woodmart' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'range'       => array(
					'px' => array(
						'min'  => -800,
						'max'  => 800,
						'step' => 1,
					),
				),
				'render_type' => 'ui',
				'default'     => array(),
				'condition'   => array(
					'object_position' => 'initial',
				),
				'conditions'  => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'size',
							'operator' => '===',
							'value'    => 'custom',
						),
						array(
							'relation' => 'and',
							'terms'    => array(
								array(
									'name'     => 'size',
									'operator' => '===',
									'value'    => 'aspect_ratio',
								),
								array(
									'name'     => 'aspect_ratio',
									'operator' => '!==',
									'value'    => 'revert-layer',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'on_click_action',
			array(
				'label'     => esc_html__( 'Click action', 'woodmart' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'none'     => esc_html__( 'None', 'woodmart' ),
					'lightbox' => esc_html__( 'Lightbox', 'woodmart' ),
					'link'     => esc_html__( 'Custom link', 'woodmart' ),
				),
				'separator' => 'before',
				'default'   => 'none',
			)
		);

		$this->add_control(
			'link',
			array(
				'label'       => esc_html__( 'Link', 'woodmart' ),
				'description' => esc_html__( 'Enter URL if you want this image to have a link.', 'woodmart' ),
				'type'        => Controls_Manager::URL,
				'default'     => array(
					'url'         => '',
					'is_external' => false,
					'nofollow'    => false,
				),
				'condition'   => array(
					'on_click_action' => array( 'link' ),
				),
			)
		);

		$this->end_controls_section();

		/**
		 * Style tab.
		 */

		/**
		 * Design settings.
		 */
		$this->start_controls_section(
			'style_section',
			[
				'label' => esc_html__( 'Style', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'alignment',
			array(
				'label'   => esc_html__( 'Alignment', 'woodmart' ),
				'type'    => 'wd_buttons',
				'options' => array(
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
				'default' => 'left',
			)
		);

		$this->add_control(
			'rounding_size',
			array(
				'label'     => esc_html__( 'Rounding', 'woodmart' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					''       => esc_html__( 'Inherit', 'woodmart' ),
					'0'      => esc_html__( '0', 'woodmart' ),
					'5'      => esc_html__( '5', 'woodmart' ),
					'8'      => esc_html__( '8', 'woodmart' ),
					'12'     => esc_html__( '12', 'woodmart' ),
					'custom' => esc_html__( 'Custom', 'woodmart' ),
				),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--wd-brd-radius: {{VALUE}}px;',
				),
			)
		);

		$this->add_control(
			'custom_rounding_size',
			array(
				'label'      => esc_html__( 'Custom rounding', 'woodmart' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 1,
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
					'{{WRAPPER}}' => '--wd-brd-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'rounding_size' => array( 'custom' ),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
		$default_settings = array(
			'image'           => array(),
			'alignment'       => 'left',
			'on_click_action' => 'none',
			'link'            => array(),
		);
		$settings         = wp_parse_args( $this->get_settings_for_display(), $default_settings );
		$link_attrs       = woodmart_get_link_attrs( $settings['link'] );

		$this->add_render_attribute(
			array(
				'wrapper' => array(
					'class' => array(
						'wd-image',
						'text-' . $settings['alignment'],
					),
				),
			)
		);

		if ( 'lightbox' === $settings['on_click_action'] ) {
			$image_data = wp_get_attachment_image_src( $settings['image']['id'], 'full' );

			woodmart_enqueue_js_library( 'photoswipe-bundle' );
			woodmart_enqueue_inline_style( 'photoswipe' );
			woodmart_enqueue_js_script( 'photoswipe-images' );

			$this->add_render_attribute( 'wrapper', 'class', 'photoswipe-images' );

			$link_attrs = woodmart_get_link_attrs(
				array(
					'url'  => $settings['image']['url'],
					'data' => 'data-width="' . esc_attr( $image_data[1] ) . '" data-height="' . esc_attr( $image_data[2] ) . '" data-elementor-open-lightbox="no"',
				)
			);
		}

		$icon_output = '';

		if ( 'custom' !== $settings['image_size'] ) {
			$image_size = $settings['image_size'];
		} elseif ( ! empty( $settings['image_custom_dimension']['width'] ) ) {
			$image_size = $settings['image_custom_dimension'];
		} else {
			$image_size = array(
				'width'  => 128,
				'height' => 128,
			);
		}

		if ( ! empty( $settings['image']['id'] ) ) {
			$icon_output = woodmart_otf_get_image_html(
				$settings['image']['id'],
				$settings['image_size'],
				$settings['image_custom_dimension']
			);

			if ( woodmart_is_svg( $settings['image']['url'] ) ) {
				$icon_output = woodmart_get_svg_html(
					$settings['image']['id'],
					$image_size
				);
			}
		} elseif ( ! empty( $settings['image']['url'] ) ) {
			$icon_output = apply_filters( 'woodmart_image', '<img src="' . esc_url( $settings['image']['url'] ) . '">' );
		}
		?>

		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); // phpcs:ignore ?>>
			<?php if ( 'none' !== $settings['on_click_action'] ) : ?>
				<a <?php echo $link_attrs; // phpcs:ignore ?> aria-label="<?php esc_html_e('Image link', 'woodmart' ); ?>">
			<?php endif; ?>
					<?php echo $icon_output; // phpcs:ignore  ?>
			<?php if ( 'none' !== $settings['on_click_action'] ) : ?>
				</a>
			<?php endif; ?>
		</div>

		<?php
	}
}

Plugin::instance()->widgets_manager->register( new Image() );
