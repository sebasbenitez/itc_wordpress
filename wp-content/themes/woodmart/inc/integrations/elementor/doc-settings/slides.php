<?php
/**
 * Slide settings class.
 *
 * @package woodmart
 */

use Elementor\Controls_Manager;
use Elementor\Core\Base\Document;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Image_Size;

if ( ! function_exists( 'woodmart_register_slide_settings_controls' ) ) {
	/**
	 * Register slide settings controls.
	 *
	 * @param Document $document The document instance.
	 */
	function woodmart_register_slide_settings_controls( $document ) {
		if ( ! method_exists( $document, 'get_main_id' ) ) {
			return;
		}

		$post_id = $document->get_main_id();

		if ( ! $post_id ) {
			return;
		}

		$post_type = get_post_type( $post_id );

		if ( 'woodmart_slide' !== $post_type ) {
			return;
		}

		$document->remove_control( 'padding' );
		$document->remove_control( 'margin' );
		$document->remove_control( 'template' );
		$document->remove_control( 'template_default_description' );
		$document->remove_control( 'template_theme_description' );
		$document->remove_control( 'template_canvas_description' );
		$document->remove_control( 'template_header_footer_description' );
		$document->remove_control( 'reload_preview_description' );

		$document->start_injection(
			array(
				'of'       => 'post_status',
				'fallback' => array(
					'of' => 'post_title',
				),
			)
		);

		$current_slider_terms = wp_get_post_terms( $post_id, 'woodmart_slider', array( 'fields' => 'ids' ) );
		$current_slider_id    = ! empty( $current_slider_terms ) ? $current_slider_terms : array();

		$document->add_control(
			'wd_slider',
			array(
				'label'       => esc_html__( 'Slider', 'woodmart' ),
				'type'        => 'wd_autocomplete',
				'default'     => $current_slider_id ? (array) $current_slider_id : array(),
				'description' => esc_html__( 'Select one or more sliders for this slide', 'woodmart' ),
				'taxonomy'    => 'woodmart_slider',
				'multiple'    => true,
				'search'      => 'woodmart_get_taxonomies_by_query',
				'render'      => 'woodmart_get_taxonomies_title_by_id',
			)
		);

		$link_meta = get_post_meta( $post_id, 'link', true );
		$document->add_control(
			'wd_link',
			array(
				'label'       => esc_html__( 'Link', 'woodmart' ),
				'type'        => Controls_Manager::URL,
				'options'     => false,
				'default'     => array(
					'url' => $link_meta ? esc_url( $link_meta ) : '',
				),
				'description' => esc_html__( 'Add URL to make whole slide clickable. Placing a link over the slide content will make this content not selectable.', 'woodmart' ),
			)
		);

		$link_target_blank_meta = get_post_meta( $post_id, 'link_target_blank', true );
		$document->add_control(
			'wd_link_target_blank',
			array(
				'label'        => esc_html__( 'Open link in new tab', 'woodmart' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => $link_target_blank_meta ? '1' : '',
				'label_on'     => esc_html__( 'Yes', 'woodmart' ),
				'label_off'    => esc_html__( 'No', 'woodmart' ),
				'return_value' => '1',
				'condition'    => array(
					'wd_link[url]!' => '',
				),
			)
		);

		$document->end_injection();

		$document->start_controls_section(
			'wd_slide_content_section',
			array(
				'label' => esc_html__( 'Content', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_SETTINGS,
			)
		);

		$document->add_responsive_control(
			'wd_padding',
			array(
				'label'      => esc_html__( 'Padding', 'woodmart' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'#slide-' . $post_id . ' .wd-slide-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$content_width_meta        = get_post_meta( $post_id, 'content_width', true );
		$content_width_meta_tablet = get_post_meta( $post_id, 'content_width_tablet', true );
		$content_width_meta_mobile = get_post_meta( $post_id, 'content_width_mobile', true );
		$document->add_responsive_control(
			'wd_content_width',
			array(
				'label'          => esc_html__( 'Width', 'woodmart' ),
				'type'           => Controls_Manager::SLIDER,
				'selectors'      => array(
					'#slide-' . $post_id . ' .wd-slide-inner' => 'max-width: {{SIZE}}{{UNIT}};',
				),
				'size_units'     => array( 'px', '%' ),
				'range'          => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1200,
						'step' => 1,
					),
					'%'  => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'default'        => array(
					'unit' => 'px',
					'size' => $content_width_meta ? $content_width_meta : '',
				),
				'tablet_default' => array(
					'unit' => 'px',
					'size' => $content_width_meta_tablet ? $content_width_meta_tablet : '',
				),
				'mobile_default' => array(
					'unit' => 'px',
					'size' => $content_width_meta_mobile ? $content_width_meta_mobile : '',
				),
				'responsive'     => true,
			)
		);

		$vertical_align_meta        = get_post_meta( $post_id, 'vertical_align', true );
		$vertical_align_meta_tablet = get_post_meta( $post_id, 'vertical_align_tablet', true );
		$vertical_align_meta_mobile = get_post_meta( $post_id, 'vertical_align_mobile', true );

		$v_align_value = array(
			'top'    => 'flex-start',
			'middle' => 'center',
			'bottom' => 'flex-end',
		);

		$document->add_responsive_control(
			'wd_vertical_align',
			array(
				'label'          => esc_html__( 'Vertical align', 'elementor' ),
				'type'           => Controls_Manager::CHOOSE,
				'options'        => array(
					'flex-start' => array(
						'title' => esc_html__( 'Top', 'woodmart' ),
						'icon'  => 'eicon-align-start-v',
					),
					'center'     => array(
						'title' => esc_html__( 'Middle', 'woodmart' ),
						'icon'  => 'eicon-align-center-v',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'End', 'woodmart' ),
						'icon'  => 'eicon-align-end-v',
					),
				),
				'selectors'      => array(
					'#slide-' . $post_id . ' .wd-slide-container' => '--wd-align-items: {{VALUE}};',
				),
				'default'        => isset( $v_align_value[ $vertical_align_meta ] ) ? $v_align_value[ $vertical_align_meta ] : 'center',
				'tablet_default' => isset( $v_align_value[ $vertical_align_meta_tablet ] ) ? $v_align_value[ $vertical_align_meta_tablet ] : 'center',
				'mobile_default' => isset( $v_align_value[ $vertical_align_meta_mobile ] ) ? $v_align_value[ $vertical_align_meta_mobile ] : 'center',
				'toggle'         => false,
			)
		);

		$horizontal_align_meta        = get_post_meta( $post_id, 'horizontal_align', true );
		$horizontal_align_meta_tablet = get_post_meta( $post_id, 'horizontal_align_tablet', true );
		$horizontal_align_meta_mobile = get_post_meta( $post_id, 'horizontal_align_mobile', true );
		$document->add_responsive_control(
			'wd_horizontal_align',
			array(
				'label'          => esc_html__( 'Horizontal align', 'woodmart' ),
				'type'           => Controls_Manager::CHOOSE,
				'options'        => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'woodmart' ),
						'icon'  => 'eicon-align-start-h',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'woodmart' ),
						'icon'  => 'eicon-align-center-h',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'woodmart' ),
						'icon'  => 'eicon-align-end-h',
					),
				),
				'selectors'      => array(
					'#slide-' . $post_id . ' .wd-slide-container' => '--wd-justify-content: {{VALUE}};',
				),
				'default'        => $horizontal_align_meta ? $horizontal_align_meta : 'left',
				'tablet_default' => $horizontal_align_meta_tablet ? $horizontal_align_meta_tablet : 'left',
				'mobile_default' => $horizontal_align_meta_mobile ? $horizontal_align_meta_mobile : 'left',
				'toggle'         => false,
			)
		);

		$slide_animation_meta = get_post_meta( $post_id, 'slide_animation', true );
		$document->add_control(
			'wd_slide_animation',
			array(
				'label'             => esc_html__( 'Animation', 'woodmart' ),
				'type'              => Controls_Manager::SELECT,
				'default'           => $slide_animation_meta ? $slide_animation_meta : 'none',
				'options'           => array(
					'none'              => esc_html__( 'None', 'woodmart' ),
					'slide-from-top'    => esc_html__( 'Slide from top', 'woodmart' ),
					'slide-from-bottom' => esc_html__( 'Slide from bottom', 'woodmart' ),
					'slide-from-right'  => esc_html__( 'Slide from right', 'woodmart' ),
					'slide-from-left'   => esc_html__( 'Slide from left', 'woodmart' ),
					'top-flip-x'        => esc_html__( 'Top flip X', 'woodmart' ),
					'bottom-flip-x'     => esc_html__( 'Bottom flip X', 'woodmart' ),
					'right-flip-y'      => esc_html__( 'Right flip Y', 'woodmart' ),
					'left-flip-y'       => esc_html__( 'Left flip Y', 'woodmart' ),
					'zoom-in'           => esc_html__( 'Zoom in', 'woodmart' ),
				),
				'description'       => esc_html__( 'Select a content appearance animation.', 'woodmart' ),
				'wd_reload_preview' => true,
			)
		);

		$document->end_controls_section();

		// Image section.
		$document->start_controls_section(
			'wd_slide_image_section',
			array(
				'label' => esc_html__( 'Image', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_SETTINGS,
			)
		);

		$image_meta = get_post_meta( $post_id, 'image', true );

		$document->add_control(
			'wd_image',
			array(
				'label'             => esc_html__( 'Image', 'woodmart' ),
				'type'              => Controls_Manager::MEDIA,
				'default'           => $image_meta && is_array( $image_meta ) ? $image_meta : array(),
				'wd_reload_preview' => true,
			)
		);

		$image_size = get_post_meta( $post_id, 'image_size', true );
		$document->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'           => 'wd_image',
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
					'wd_image[id]!' => '',
				),
			)
		);

		$image_object_fit_meta        = get_post_meta( $post_id, 'image_object_fit', true );
		$image_object_fit_meta_tablet = get_post_meta( $post_id, 'image_object_fit_tablet', true );
		$image_object_fit_meta_mobile = get_post_meta( $post_id, 'image_object_fit_mobile', true );
		$document->add_responsive_control(
			'wd_image_object_fit',
			array(
				'label'          => esc_html__( 'Object-fit', 'woodmart' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => $image_object_fit_meta ? $image_object_fit_meta : '',
				'tablet_default' => $image_object_fit_meta_tablet ? $image_object_fit_meta_tablet : '',
				'mobile_default' => $image_object_fit_meta_mobile ? $image_object_fit_meta_mobile : '',
				'options'        => array(
					''        => esc_html__( 'Default', 'woodmart' ),
					'cover'   => esc_html__( 'Cover', 'woodmart' ),
					'contain' => esc_html__( 'Contain', 'woodmart' ),
					'fill'    => esc_html__( 'Fill', 'woodmart' ),
					'none'    => esc_html__( 'None', 'woodmart' ),
				),
				'selectors'      => array(
					'#slide-' . $post_id . ' .wd-slide-bg img' => 'object-fit: {{VALUE}};',
				),
				'condition'      => array(
					'wd_image[id]!' => '',
				),
			)
		);

		$image_object_position_meta        = get_post_meta( $post_id, 'image_object_position', true );
		$image_object_position_meta_tablet = get_post_meta( $post_id, 'image_object_position_tablet', true );
		$image_object_position_meta_mobile = get_post_meta( $post_id, 'image_object_position_mobile', true );
		$document->add_responsive_control(
			'wd_image_object_position',
			array(
				'label'          => esc_html__( 'Object position', 'woodmart' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => $image_object_position_meta ? $image_object_position_meta : '',
				'tablet_default' => $image_object_position_meta_tablet ? $image_object_position_meta_tablet : '',
				'mobile_default' => $image_object_position_meta_mobile ? $image_object_position_meta_mobile : '',
				'options'        => array(
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
				'selectors'      => array(
					'#slide-' . $post_id . ' .wd-slide-bg img' => 'object-position: {{VALUE}};',
				),
				'condition'      => array(
					'wd_image[id]!' => '',
				),
			)
		);

		$image_object_position_x_meta        = get_post_meta( $post_id, 'image_object_position_x', true );
		$image_object_position_x_meta_tablet = get_post_meta( $post_id, 'image_object_position_x_tablet', true );
		$image_object_position_x_meta_mobile = get_post_meta( $post_id, 'image_object_position_x_mobile', true );
		$document->add_responsive_control(
			'wd_image_object_position_x',
			array(
				'label'          => esc_html__( 'X Position', 'woodmart' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => array( 'px' ),
				'range'          => array(
					'px' => array(
						'min'  => -800,
						'max'  => 800,
						'step' => 1,
					),
				),
				'default'        => array(
					'unit' => 'px',
					'size' => $image_object_position_x_meta ? $image_object_position_x_meta : 0,
				),
				'tablet_default' => array(
					'unit' => 'px',
					'size' => $image_object_position_x_meta_tablet ? $image_object_position_x_meta_tablet : 0,
				),
				'mobile_default' => array(
					'unit' => 'px',
					'size' => $image_object_position_x_meta_mobile ? $image_object_position_x_meta_mobile : 0,
				),
				'selectors'      => array(
					'#slide-' . $post_id . ' .wd-slide-bg img' => 'object-position: {{SIZE}}{{UNIT}} {{wd_image_object_position_y.SIZE}}{{wd_image_object_position_y.UNIT}}',
				),
				'condition'      => array(
					'wd_image_object_position' => 'initial',
					'wd_image[id]!'            => '',
				),
			)
		);

		$image_object_position_y_meta        = get_post_meta( $post_id, 'image_object_position_y', true );
		$image_object_position_y_meta_tablet = get_post_meta( $post_id, 'image_object_position_y_tablet', true );
		$image_object_position_y_meta_mobile = get_post_meta( $post_id, 'image_object_position_y_mobile', true );
		$document->add_responsive_control(
			'wd_image_object_position_y',
			array(
				'label'          => esc_html__( 'Y Position', 'woodmart' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => array( 'px' ),
				'range'          => array(
					'px' => array(
						'min'  => -800,
						'max'  => 800,
						'step' => 1,
					),
				),
				'default'        => array(
					'unit' => 'px',
					'size' => $image_object_position_y_meta ? $image_object_position_y_meta : 0,
				),
				'tablet_default' => array(
					'unit' => 'px',
					'size' => $image_object_position_y_meta_tablet ? $image_object_position_y_meta_tablet : 0,
				),
				'mobile_default' => array(
					'unit' => 'px',
					'size' => $image_object_position_y_meta_mobile ? $image_object_position_y_meta_mobile : 0,
				),
				'condition'      => array(
					'wd_image_object_position' => 'initial',
					'wd_image[id]!'            => '',
				),
			)
		);

		$document->end_controls_section();

		// Background section.
		$document->start_controls_section(
			'wd_slide_background_section',
			array(
				'label' => esc_html__( 'Background', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_SETTINGS,
			)
		);

		$bg_color_meta = get_post_meta( $post_id, 'bg_color', true );

		$bg_image_desktop_meta = get_post_meta( $post_id, 'bg_image_desktop', true );
		$bg_image_tablet_meta  = get_post_meta( $post_id, 'bg_image_tablet', true );
		$bg_image_mobile_meta  = get_post_meta( $post_id, 'bg_image_mobile', true );

		$bg_image_size        = get_post_meta( $post_id, 'bg_image_desktop_size', true );
		$bg_image_size_tablet = get_post_meta( $post_id, 'bg_image_tablet_size', true );
		$bg_image_size_mobile = get_post_meta( $post_id, 'bg_image_mobile_size', true );

		$bg_image_size_meta        = get_post_meta( $post_id, 'bg_image_size_desktop', true );
		$bg_image_size_meta_tablet = get_post_meta( $post_id, 'bg_image_size_tablet', true );
		$bg_image_size_meta_mobile = get_post_meta( $post_id, 'bg_image_size_mobile', true );

		$bg_image_position_meta        = get_post_meta( $post_id, 'bg_image_position_desktop', true );
		$bg_image_position_meta_tablet = get_post_meta( $post_id, 'bg_image_position_tablet', true );
		$bg_image_position_meta_mobile = get_post_meta( $post_id, 'bg_image_position_mobile', true );

		$bg_image_position_x_meta        = get_post_meta( $post_id, 'bg_image_position_x_desktop', true );
		$bg_image_position_x_meta_tablet = get_post_meta( $post_id, 'bg_image_position_x_tablet', true );
		$bg_image_position_x_meta_mobile = get_post_meta( $post_id, 'bg_image_position_x_mobile', true );

		$bg_image_position_y_meta        = get_post_meta( $post_id, 'bg_image_position_y_desktop', true );
		$bg_image_position_y_meta_tablet = get_post_meta( $post_id, 'bg_image_position_y_tablet', true );
		$bg_image_position_y_meta_mobile = get_post_meta( $post_id, 'bg_image_position_y_mobile', true );

		$document->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'           => 'wd_bg',
				'selector'       => '#slide-' . $post_id . ' .wd-slide-bg',
				'exclude'        => array( 'video', 'attachment', 'repeat' ),
				'fields_options' => array(
					'background' => array(
						'default' => 'classic',
					),
					'color'      => array(
						'default' => $bg_color_meta ? $bg_color_meta : '#fefefe',
					),
					'image'      => array(
						'default'        => $bg_image_desktop_meta ? $bg_image_desktop_meta : array(),
						'tablet_default' => $bg_image_tablet_meta ? $bg_image_tablet_meta : array(),
						'mobile_default' => $bg_image_mobile_meta ? $bg_image_mobile_meta : array(),
					),
					'image_size' => array(
						'default'        => $bg_image_size ? $bg_image_size : '',
						'tablet_default' => $bg_image_size_tablet ? $bg_image_size_tablet : '',
						'mobile_default' => $bg_image_size_mobile ? $bg_image_size_mobile : '',
					),
					'position'   => array(
						'default'        => $bg_image_position_meta ? $bg_image_position_meta : '',
						'tablet_default' => $bg_image_position_meta_tablet ? $bg_image_position_meta_tablet : '',
						'mobile_default' => $bg_image_position_meta_mobile ? $bg_image_position_meta_mobile : '',
					),
					'xpos'       => array(
						'default'        => array(
							'size' => $bg_image_position_x_meta ? $bg_image_position_x_meta : '',
						),
						'tablet_default' => array(
							'size' => $bg_image_position_x_meta_tablet ? $bg_image_position_x_meta_tablet : '',
						),
						'mobile_default' => array(
							'size' => $bg_image_position_x_meta_mobile ? $bg_image_position_x_meta_mobile : '',
						),
					),
					'ypos'       => array(
						'default'        => array(
							'size' => $bg_image_position_y_meta ? $bg_image_position_y_meta : '',
						),
						'tablet_default' => array(
							'size' => $bg_image_position_y_meta_tablet ? $bg_image_position_y_meta_tablet : '',
						),
						'mobile_default' => array(
							'size' => $bg_image_position_y_meta_mobile ? $bg_image_position_y_meta_mobile : '',
						),
					),
					'size'       => array(
						'options'        => array(
							''        => esc_html__( 'Default', 'elementor' ),
							'cover'   => esc_html__( 'Cover', 'elementor' ),
							'contain' => esc_html__( 'Contain', 'elementor' ),
						),
						'default'        => $bg_image_size_meta ? $bg_image_size_meta : '',
						'tablet_default' => $bg_image_size_meta_tablet ? $bg_image_size_meta_tablet : '',
						'mobile_default' => $bg_image_size_meta_mobile ? $bg_image_size_meta_mobile : '',
					),
				),
			)
		);

		$document->end_controls_section();
	}

	add_action( 'elementor/documents/register_controls', 'woodmart_register_slide_settings_controls', 50, 1 );
}

if ( ! function_exists( 'woodmart_save_slide_slider_from_elementor' ) ) {
	/**
	 * Save slider selection from Elementor settings.
	 *
	 * @param \Elementor\Core\Base\Document $document The document instance.
	 * @param array                         $data     The document data.
	 */
	function woodmart_save_slide_slider_from_elementor( $document, $data ) {
		$post_id = $document->get_main_id();

		if ( 'woodmart_slide' !== get_post_type( $post_id ) || ! isset( $data['settings']['wd_slider'] ) ) {
			return;
		}

		if ( ! empty( $data['settings']['wd_slider'] ) ) {
			$meta_keys = array(
				'link',
				'link_target_blank',
				'content_width',
				'content_width_tablet',
				'content_width_mobile',
				'vertical_align',
				'vertical_align_tablet',
				'vertical_align_mobile',
				'horizontal_align',
				'horizontal_align_tablet',
				'horizontal_align_mobile',
				'slide_animation',
				'image',
				'image_size',
				'image_object_fit',
				'image_object_fit_tablet',
				'image_object_fit_mobile',
				'image_object_position',
				'image_object_position_tablet',
				'image_object_position_mobile',
				'image_object_position_x',
				'image_object_position_x_tablet',
				'image_object_position_x_mobile',
				'image_object_position_y',
				'image_object_position_y_tablet',
				'image_object_position_y_mobile',
				'bg_color',
				'bg_image_desktop',
				'bg_image_tablet',
				'bg_image_mobile',
				'bg_image_desktop_size',
				'bg_image_tablet_size',
				'bg_image_mobile_size',
				'bg_image_size_desktop',
				'bg_image_size_tablet',
				'bg_image_size_mobile',
				'bg_image_position_desktop',
				'bg_image_position_tablet',
				'bg_image_position_mobile',
				'bg_image_position_x_desktop',
				'bg_image_position_x_tablet',
				'bg_image_position_x_mobile',
				'bg_image_position_y_desktop',
				'bg_image_position_y_tablet',
				'bg_image_position_y_mobile',
			);

			foreach ( $meta_keys as $meta_key ) {
				if ( metadata_exists( 'post', $post_id, $meta_key ) ) {
					delete_post_meta( $post_id, $meta_key );
				}
			}
		}

		$slider_ids = $data['settings']['wd_slider'];
		$slider_ids = $slider_ids && is_array( $slider_ids ) ? array_map( 'absint', $slider_ids ) : array();

		wp_set_post_terms( $post_id, $slider_ids, 'woodmart_slider', false );
	}

	add_action( 'elementor/document/after_save', 'woodmart_save_slide_slider_from_elementor', 10, 2 );
}

if ( ! function_exists( 'woodmart_update_slide_settings_elementor_controls' ) ) {
	/**
	 * Update slide settings Elementor controls.
	 *
	 * @param array $client_env Editor configuration.
	 * @return array
	 */
	function woodmart_update_slide_settings_elementor_controls( $client_env ) {
		if ( ! isset( $client_env['initial_document']['id'] ) || 'woodmart_slide' !== get_post_type( $client_env['initial_document']['id'] ) ) {
			return $client_env;
		}

		$client_env['initial_document']['settings']['panelPage']['title'] = esc_html__( 'Slide Settings', 'woodmart' );

		if ( isset( $client_env['initial_document']['settings']['controls']['hide_title'] ) ) {
			unset( $client_env['initial_document']['settings']['controls']['hide_title'] );
		}

		if ( isset( $client_env['initial_document']['settings']['controls']['post_featured_image'] ) ) {
			unset( $client_env['initial_document']['settings']['controls']['post_featured_image'] );
		}

		if ( isset( $client_env['initial_document']['settings']['controls']['margin'] ) ) {
			unset( $client_env['initial_document']['settings']['controls']['margin'] );
			unset( $client_env['initial_document']['settings']['controls']['padding'] );
			unset( $client_env['initial_document']['settings']['controls']['background_background'] );
		}

		if ( isset( $client_env['initial_document']['settings']['tabs']['style'] ) ) {
			unset( $client_env['initial_document']['settings']['tabs']['style'] );
			unset( $client_env['initial_document']['settings']['tabs']['advanced'] );
		}

		return $client_env;
	}

	add_action( 'elementor/editor/localize_settings', 'woodmart_update_slide_settings_elementor_controls' );
}
