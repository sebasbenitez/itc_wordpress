<?php
/**
 * Page settings class.
 *
 * @package woodmart
 */

use Elementor\Controls_Manager;
use Elementor\Core\Base\Document;
use Elementor\Core\DocumentTypes\PageBase;
use Elementor\Group_Control_Image_Size;

if ( ! function_exists( 'woodmart_register_page_settings_controls' ) ) {
	/**
	 * Register page settings controls.
	 *
	 * @param Document $document The document instance.
	 */
	function woodmart_register_page_settings_controls( $document ) {
		if ( ! $document instanceof PageBase ) {
			return;
		}

		$post_id   = $document->get_main_id();
		$post_type = get_post_type( $post_id );

		if ( ! in_array( $post_type, array( 'page', 'post', 'portfolio' ), true ) ) {
			return;
		}

		$woodmart_prefix = '_woodmart_';

		// Header section.
		$document->start_controls_section(
			$woodmart_prefix . 'header_section',
			array(
				'label' => esc_html__( 'Header', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_SETTINGS,
			)
		);

		$whb_header_meta   = get_post_meta( $post_id, $woodmart_prefix . 'whb_header', true );
		$headers_array_raw = function_exists( 'woodmart_get_theme_settings_headers_array' ) ? woodmart_get_theme_settings_headers_array() : array();
		$headers_array     = array();
		foreach ( $headers_array_raw as $key => $value ) {
			if ( is_array( $value ) && isset( $value['name'] ) ) {
				$headers_array[ $key ] = $value['name'];
			} else {
				$headers_array[ $key ] = $value;
			}
		}
		$document->add_control(
			'wd_' . $woodmart_prefix . 'whb_header',
			array(
				'label'             => esc_html__( 'Custom header', 'woodmart' ),
				'type'              => Controls_Manager::SELECT,
				'default'           => $whb_header_meta ? $whb_header_meta : 'none',
				'options'           => $headers_array,
				'description'       => esc_html__( 'If you are using our header builder for your header configuration you can select different layout from the list for this particular page.', 'woodmart' ),
				'wd_reload_preview' => true,
			)
		);

		$open_categories_meta = get_post_meta( $post_id, $woodmart_prefix . 'open_categories', true );
		$document->add_control(
			'wd_' . $woodmart_prefix . 'open_categories',
			array(
				'label'             => esc_html__( 'Open categories menu', 'woodmart' ),
				'type'              => Controls_Manager::SWITCHER,
				'default'           => $open_categories_meta ? '1' : '',
				'label_on'          => esc_html__( 'Yes', 'woodmart' ),
				'label_off'         => esc_html__( 'No', 'woodmart' ),
				'return_value'      => '1',
				'description'       => esc_html__( 'Always shows categories menu on this page', 'woodmart' ),
				'wd_reload_preview' => true,
			)
		);

		$document->end_controls_section();

		// Page title section.
		$document->start_controls_section(
			$woodmart_prefix . 'page_title_section',
			array(
				'label' => esc_html__( 'Page title', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_SETTINGS,
			)
		);

		$title_off_meta = get_post_meta( $post_id, $woodmart_prefix . 'title_off', true );
		$document->add_control(
			'wd_' . $woodmart_prefix . 'title_off',
			array(
				'label'             => esc_html__( 'Disable page title', 'woodmart' ),
				'type'              => Controls_Manager::SWITCHER,
				'default'           => $title_off_meta ? '1' : '',
				'label_on'          => esc_html__( 'Yes', 'woodmart' ),
				'label_off'         => esc_html__( 'No', 'woodmart' ),
				'return_value'      => '1',
				'wd_reload_preview' => true,
			)
		);

		$page_title_size_meta = get_post_meta( $post_id, $woodmart_prefix . 'page-title-size', true );
		$document->add_control(
			'wd_' . $woodmart_prefix . 'page-title-size',
			array(
				'label'             => esc_html__( 'Size', 'woodmart' ),
				'type'              => Controls_Manager::SELECT,
				'default'           => $page_title_size_meta ? $page_title_size_meta : 'inherit',
				'options'           => array(
					'inherit' => esc_html__( 'Inherit from Theme Settings', 'woodmart' ),
					'default' => esc_html__( 'Default', 'woodmart' ),
					'small'   => esc_html__( 'Small', 'woodmart' ),
					'large'   => esc_html__( 'Large', 'woodmart' ),
				),
				'condition'         => array(
					'wd_' . $woodmart_prefix . 'title_off!' => '1',
				),
				'wd_reload_preview' => true,
			)
		);

		$title_bg_color_meta = get_post_meta( $post_id, $woodmart_prefix . 'title_bg_color', true );
		$document->add_control(
			'wd_' . $woodmart_prefix . 'title_bg_color',
			array(
				'label'             => esc_html__( 'Background color', 'woodmart' ),
				'type'              => Controls_Manager::COLOR,
				'default'           => $title_bg_color_meta ? $title_bg_color_meta : '',
				'selectors'         => array(
					'body .wd-page-title' => 'background-color: {{VALUE}};',
				),
				'condition'         => array(
					'wd_' . $woodmart_prefix . 'title_off!' => '1',
				),
				'wd_reload_preview' => true,
			)
		);

		$title_image_meta = get_post_meta( $post_id, $woodmart_prefix . 'title_image', true );

		$document->add_control(
			'wd_' . $woodmart_prefix . 'title_image',
			array(
				'label'             => esc_html__( 'Image', 'woodmart' ),
				'type'              => Controls_Manager::MEDIA,
				'default'           => $title_image_meta && is_array( $title_image_meta ) ? $title_image_meta : array(),
				'condition'         => array(
					'wd_' . $woodmart_prefix . 'title_off!' => '1',
				),
				'wd_reload_preview' => true,
			)
		);

		$image_size = get_post_meta( $post_id, $woodmart_prefix . 'title_image_size', true );
		$document->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'           => 'wd_' . $woodmart_prefix . 'title_image',
				'default'        => $image_size ? $image_size : 'full',
				'separator'      => 'none',
				'fields_options' => array(
					'size'             => array(
						'wd_reload_preview' => true,
					),
					'custom_dimension' => array(
						'wd_reload_preview' => true,
					),
				),
				'condition'      => array(
					'wd_' . $woodmart_prefix . 'title_off!' => '1',
					'wd_' . $woodmart_prefix . 'title_image[id]!' => '',
				),
			)
		);

		$title_color_meta = get_post_meta( $post_id, $woodmart_prefix . 'title_color', true );
		$document->add_control(
			'wd_' . $woodmart_prefix . 'title_color',
			array(
				'label'             => esc_html__( 'Color scheme', 'woodmart' ),
				'type'              => Controls_Manager::SELECT,
				'default'           => $title_color_meta ? $title_color_meta : 'default',
				'options'           => array(
					'default' => esc_html__( 'Inherit from Theme Settings', 'woodmart' ),
					'light'   => esc_html__( 'Light', 'woodmart' ),
					'dark'    => esc_html__( 'Dark', 'woodmart' ),
				),
				'condition'         => array(
					'wd_' . $woodmart_prefix . 'title_off!' => '1',
				),
				'wd_reload_preview' => true,
			)
		);

		$document->end_controls_section();

		// Sidebar section.
		$document->start_controls_section(
			$woodmart_prefix . 'sidebar_section',
			array(
				'label' => esc_html__( 'Sidebar', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_SETTINGS,
			)
		);

		$main_layout_meta = get_post_meta( $post_id, $woodmart_prefix . 'main_layout', true );
		$document->add_control(
			'wd_' . $woodmart_prefix . 'main_layout',
			array(
				'label'             => esc_html__( 'Position', 'woodmart' ),
				'type'              => Controls_Manager::SELECT,
				'default'           => $main_layout_meta ? $main_layout_meta : 'default',
				'options'           => array(
					'default'       => esc_html__( 'Inherit from Theme Settings', 'woodmart' ),
					'full-width'    => esc_html__( 'Without', 'woodmart' ),
					'sidebar-left'  => esc_html__( 'Left', 'woodmart' ),
					'sidebar-right' => esc_html__( 'Right', 'woodmart' ),
				),
				'wd_reload_preview' => true,
			)
		);

		$sidebar_width_meta = get_post_meta( $post_id, $woodmart_prefix . 'sidebar_width', true );
		$document->add_control(
			'wd_' . $woodmart_prefix . 'sidebar_width',
			array(
				'label'             => esc_html__( 'Size', 'woodmart' ),
				'type'              => Controls_Manager::SELECT,
				'default'           => $sidebar_width_meta ? $sidebar_width_meta : 'default',
				'options'           => array(
					'default' => esc_html__( 'Inherit from Theme Settings', 'woodmart' ),
					2         => esc_html__( 'Small', 'woodmart' ),
					3         => esc_html__( 'Medium', 'woodmart' ),
					4         => esc_html__( 'Large', 'woodmart' ),
				),
				'wd_reload_preview' => true,
			)
		);

		$custom_sidebar_meta = woodmart_get_post_meta_value( $post_id, $woodmart_prefix . 'custom_sidebar' );
		$sidebars_array_raw  = function_exists( 'woodmart_get_theme_settings_sidebars_array' ) ? woodmart_get_theme_settings_sidebars_array() : array();
		$sidebars_array      = array();
		foreach ( $sidebars_array_raw as $key => $value ) {
			if ( is_array( $value ) && isset( $value['name'] ) ) {
				$sidebars_array[ $key ] = $value['name'];
			} else {
				$sidebars_array[ $key ] = $value;
			}
		}
		$document->add_control(
			'wd_' . $woodmart_prefix . 'custom_sidebar',
			array(
				'label'             => esc_html__( 'Custom sidebar', 'woodmart' ),
				'type'              => Controls_Manager::SELECT,
				'default'           => $custom_sidebar_meta ? $custom_sidebar_meta : 'none',
				'options'           => $sidebars_array,
				'wd_reload_preview' => true,
			)
		);

		$document->end_controls_section();

		// Footer section.
		$document->start_controls_section(
			$woodmart_prefix . 'footer_section',
			array(
				'label' => esc_html__( 'Footer', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_SETTINGS,
			)
		);

		$footer_off_meta = get_post_meta( $post_id, $woodmart_prefix . 'footer_off', true );
		$document->add_control(
			'wd_' . $woodmart_prefix . 'footer_off',
			array(
				'label'             => esc_html__( 'Disable footer', 'woodmart' ),
				'type'              => Controls_Manager::SWITCHER,
				'default'           => $footer_off_meta ? '1' : '',
				'label_on'          => esc_html__( 'Yes', 'woodmart' ),
				'label_off'         => esc_html__( 'No', 'woodmart' ),
				'return_value'      => '1',
				'wd_reload_preview' => true,
			)
		);

		$prefooter_off_meta = get_post_meta( $post_id, $woodmart_prefix . 'prefooter_off', true );
		$document->add_control(
			'wd_' . $woodmart_prefix . 'prefooter_off',
			array(
				'label'             => esc_html__( 'Disable prefooter', 'woodmart' ),
				'type'              => Controls_Manager::SWITCHER,
				'default'           => $prefooter_off_meta ? '1' : '',
				'label_on'          => esc_html__( 'Yes', 'woodmart' ),
				'label_off'         => esc_html__( 'No', 'woodmart' ),
				'return_value'      => '1',
				'wd_reload_preview' => true,
			)
		);

		$copyrights_off_meta = get_post_meta( $post_id, $woodmart_prefix . 'copyrights_off', true );
		$document->add_control(
			'wd_' . $woodmart_prefix . 'copyrights_off',
			array(
				'label'             => esc_html__( 'Disable copyrights', 'woodmart' ),
				'type'              => Controls_Manager::SWITCHER,
				'default'           => $copyrights_off_meta ? '1' : '',
				'label_on'          => esc_html__( 'Yes', 'woodmart' ),
				'label_off'         => esc_html__( 'No', 'woodmart' ),
				'return_value'      => '1',
				'wd_reload_preview' => true,
			)
		);

		$document->end_controls_section();

		// Preload image section (only if option enabled and only for pages).
		if ( woodmart_get_opt( 'preload_lcp_image' ) && 'page' === $post_type ) {
			$document->start_controls_section(
				$woodmart_prefix . 'preload_section',
				array(
					'label' => esc_html__( 'Preload image', 'woodmart' ),
					'tab'   => Controls_Manager::TAB_SETTINGS,
				)
			);

			$preload_image_desktop_meta = get_post_meta( $post_id, $woodmart_prefix . 'preload_image', true );
			$preload_image_mobile_meta  = get_post_meta( $post_id, $woodmart_prefix . 'preload_image_mobile', true );

			$preload_image_desktop_size = get_post_meta( $post_id, $woodmart_prefix . 'preload_image_size', true );
			$preload_image_mobile_size  = get_post_meta( $post_id, $woodmart_prefix . 'preload_image_mobile_size', true );

			$preload_image_type_desktop_meta = get_post_meta( $post_id, $woodmart_prefix . 'preload_image_type', true );
			$preload_image_type_mobile_meta  = get_post_meta( $post_id, $woodmart_prefix . 'preload_image_mobile_type', true );

			$document->start_controls_tabs(
				'preload_image_tabs',
			);

			$document->start_controls_tab(
				'preload_image_tabs_desktop',
				array(
					'label' => esc_html__( 'Desktop', 'woodmart' ),
				)
			);

			$document->add_control(
				'wd_' . $woodmart_prefix . 'preload_image',
				array(
					'label'       => esc_html__( 'Image', 'woodmart' ),
					'type'        => Controls_Manager::MEDIA,
					'default'     => $preload_image_desktop_meta ? $preload_image_desktop_meta : array(),
					'description' => esc_html__( 'Upload an image for desktop', 'woodmart' ),
				)
			);

			$document->add_group_control(
				Group_Control_Image_Size::get_type(),
				array(
					'name'      => 'wd_' . $woodmart_prefix . 'preload_image',
					'default'   => $preload_image_desktop_size ? $preload_image_desktop_size : 'full',
					'separator' => 'none',
					'condition' => array(
						'wd_' . $woodmart_prefix . 'preload_image[id]!' => '',
					),
				)
			);

			$document->add_control(
				'wd_' . $woodmart_prefix . 'preload_image_type',
				array(
					'label'       => esc_html__( 'Image type', 'woodmart' ),
					'type'        => Controls_Manager::SELECT,
					'default'     => $preload_image_type_desktop_meta ? $preload_image_type_desktop_meta : 'image',
					'options'     => array(
						'image'      => esc_html__( 'Image tag (<img>)', 'woodmart' ),
						'background' => esc_html__( 'Background image (CSS)', 'woodmart' ),
					),
					'description' => esc_html__( 'Choose whether your image is added to the page using an "img" tag or as a background via CSS. Selecting the correct placement type will help determine whether srcsets are used for the image, ensuring each of them is considered in the LCP option. If you set the image using "Find" function, this value will be selected automatically.', 'woodmart' ),
				)
			);

			$document->end_controls_tab();

			$document->start_controls_tab(
				'preload_image_tabs_mobile',
				array(
					'label' => esc_html__( 'Mobile', 'woodmart' ),
				)
			);

			$document->add_control(
				'wd_' . $woodmart_prefix . 'preload_image_mobile',
				array(
					'label'       => esc_html__( 'Image', 'woodmart' ),
					'type'        => Controls_Manager::MEDIA,
					'default'     => $preload_image_mobile_meta ? $preload_image_mobile_meta : array(),
					'description' => esc_html__( 'Upload an image for mobile', 'woodmart' ),
				)
			);

			$document->add_group_control(
				Group_Control_Image_Size::get_type(),
				array(
					'name'      => 'wd_' . $woodmart_prefix . 'preload_image_mobile',
					'default'   => $preload_image_mobile_size ? $preload_image_mobile_size : 'full',
					'separator' => 'none',
					'condition' => array(
						'wd_' . $woodmart_prefix . 'preload_image_mobile[id]!' => '',
					),
				)
			);

			$document->add_control(
				'wd_' . $woodmart_prefix . 'preload_image_type_mobile',
				array(
					'label'       => esc_html__( 'Image type', 'woodmart' ),
					'type'        => Controls_Manager::SELECT,
					'default'     => $preload_image_type_mobile_meta ? $preload_image_type_mobile_meta : 'image',
					'options'     => array(
						'image'      => esc_html__( 'Image tag (<img>)', 'woodmart' ),
						'background' => esc_html__( 'Background image (CSS)', 'woodmart' ),
					),
					'description' => esc_html__( 'Choose whether your image is added to the page using an "img" tag or as a background via CSS. Selecting the correct placement type will help determine whether srcsets are used for the image, ensuring each of them is considered in the LCP option. If you set the image using "Find" function, this value will be selected automatically.', 'woodmart' ),
				)
			);

			$document->end_controls_tab();
			$document->end_controls_tabs();

			$document->end_controls_section();
		}

		// Mobile version section (only for pages).
		if ( 'page' === $post_type ) {
			$document->start_controls_section(
				$woodmart_prefix . 'mobile_section',
				array(
					'label' => esc_html__( 'Mobile version', 'woodmart' ),
					'tab'   => Controls_Manager::TAB_SETTINGS,
				)
			);

			$mobile_content_meta    = get_post_meta( $post_id, $woodmart_prefix . 'mobile_content', true );
			$mobile_content_default = '';

			if ( ! empty( $mobile_content_meta ) ) {
				$mobile_content_default = is_array( $mobile_content_meta ) ? $mobile_content_meta : array( absint( $mobile_content_meta ) );
			}

			$document->add_control(
				'wd_' . $woodmart_prefix . 'mobile_content',
				array(
					'label'       => esc_html__( 'Choose HTML block (experimental)', 'woodmart' ),
					'type'        => 'wd_autocomplete',
					'default'     => $mobile_content_default,
					'description' => esc_html__( 'You can create a separate content that will be displayed on mobile devices to optimize the performance.', 'woodmart' ),
					'post_type'   => 'cms_block',
					'search'      => 'woodmart_get_posts_by_query',
					'render'      => 'woodmart_get_posts_title_by_id',
				)
			);
			$document->end_controls_section();
		}
	}

	add_action( 'elementor/documents/register_controls', 'woodmart_register_page_settings_controls', 10, 1 );
}
