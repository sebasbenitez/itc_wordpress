<?php
/**
 * Author area map.
 */

namespace XTS\Elementor;

use Elementor\Group_Control_Image_Size;
use Elementor\Utils;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Elementor widget that inserts an embeddable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class Author_Area extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'wd_author_area';
	}

	/**
	 * Get widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Author area', 'woodmart' );
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
		return 'wd-icon-author-area';
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
				'default'   => 'thumbnail',
				'separator' => 'none',
			)
		);

		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'woodmart' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Title example',
			)
		);

		$this->add_control(
			'author_name',
			array(
				'label'   => esc_html__( 'Author name', 'woodmart' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Nicolas Wood',
			)
		);

		$this->add_control(
			'content',
			array(
				'label'   => esc_html__( 'Author bio', 'woodmart' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Web Developer',
			)
		);

		$this->add_control(
			'link',
			array(
				'label'       => esc_html__( 'Link', 'woodmart' ),
				'description' => esc_html__( 'Enter URL if you want this banner to have a link.', 'woodmart' ),
				'type'        => Controls_Manager::URL,
				'default'     => array(
					'url'         => '#',
					'is_external' => false,
					'nofollow'    => false,
				),
			)
		);

		$this->add_control(
			'link_text',
			array(
				'label' => esc_html__( 'Link text', 'woodmart' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$this->end_controls_section();

		/**
		 * Style tab.
		 */

		/**
		 * General settings.
		 */
		$this->start_controls_section(
			'general_style_section',
			array(
				'label' => esc_html__( 'General', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'alignment',
			array(
				'label'   => esc_html__( 'Align', 'woodmart' ),
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
			'woodmart_color_scheme',
			array(
				'label'   => esc_html__( 'Color Scheme', 'woodmart' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					''      => esc_html__( 'Inherit', 'woodmart' ),
					'light' => esc_html__( 'Light', 'woodmart' ),
					'dark'  => esc_html__( 'Dark', 'woodmart' ),
				),
				'default' => '',
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
			'title'                 => '',
			'author_name'           => '',
			'image'                 => '',
			'link'                  => '',
			'link_text'             => '',
			'alignment'             => 'left',
			'woodmart_color_scheme' => 'dark',
		);

		$settings = wp_parse_args( $this->get_settings_for_display(), $default_settings );

		$this->add_render_attribute(
			array(
				'wrapper'     => array(
					'class' => array(
						'author-area',
						'text-' . $settings['alignment'],
						'color-scheme-' . $settings['woodmart_color_scheme'],
						'wd-set-mb',
						'reset-last-child',
					),
				),
				'author_name' => array(
					'class' => array(
						'author-name',
						'title',
					),
				),
				'title'       => array(
					'class' => array(
						'author-title',
						'title',
					),
				),
				'content'     => array(
					'class' => array(
						'author-area-info',
					),
				),
			),
		);

		$this->add_inline_editing_attributes( 'title' );
		$this->add_inline_editing_attributes( 'author_name' );
		$this->add_inline_editing_attributes( 'content' );

		// Image settings.
		$image_output      = '';
		$custom_image_size = isset( $settings['image_custom_dimension']['width'] ) && $settings['image_custom_dimension']['width'] ? $settings['image_custom_dimension'] : array(
			'width'  => 128,
			'height' => 128,
		);

		if ( isset( $settings['image']['id'] ) && $settings['image']['id'] ) {
			$image_output = woodmart_otf_get_image_html( $settings['image']['id'], $settings['image_size'], $custom_image_size, array( 'class' => 'author-area-image' ) );

			if ( woodmart_is_svg( wp_get_attachment_image_url( $settings['image']['id'] ) ) ) {
				$custom_image_size = 'custom' !== $settings['image_size'] ? $settings['image_size'] : $custom_image_size;
				$image_output      = woodmart_get_svg_html( $settings['image']['id'], $custom_image_size );
			}
		}

		$link_attrs = woodmart_get_link_attrs( $settings['link'] );

		woodmart_enqueue_inline_style( 'el-author-area' );

		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php if ( $settings['title'] ) : ?>
				<h3 <?php echo $this->get_render_attribute_string( 'title' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<?php echo esc_html( $settings['title'] ); ?>
				</h3>
			<?php endif ?>

			<?php if ( $image_output ) : ?>
				<div class="author-avatar">
					<?php echo wp_kses( $image_output, true ); ?>
				</div>
			<?php endif; ?>

			<?php if ( $settings['author_name'] ) : ?>
				<h4 <?php echo $this->get_render_attribute_string( 'author_name' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<?php echo esc_html( $settings['author_name'] ); ?>
				</h4>
			<?php endif ?>

			<?php if ( $settings['content'] ) : ?>
				<div <?php echo $this->get_render_attribute_string( 'content' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<?php echo do_shortcode( $settings['content'] ); ?>
				</div>
			<?php endif ?>

			<?php if ( ! empty( $settings['link_text'] ) ) : ?>
				<a <?php echo $link_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> class="btn">
					<?php echo esc_html( $settings['link_text'] ); ?>
				</a>
			<?php endif; ?>
		</div>
		<?php
	}
}

Plugin::instance()->widgets_manager->register( new Author_Area() );
