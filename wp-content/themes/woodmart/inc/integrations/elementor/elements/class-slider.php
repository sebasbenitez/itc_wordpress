<?php
/**
 * Slider map.
 *
 * @package woodmart
 */

namespace XTS\Elementor;

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
class Slider extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'wd_slider';
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
		return esc_html__( 'Slider', 'woodmart' );
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
		return 'wd-icon-slider';
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
	 * Get widget categories.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_sliders() {
		$sliders = get_terms(
			array(
				'taxonomy'   => 'woodmart_slider',
				'hide_empty' => false,
			)
		);

		$output = array(
			'0' => esc_html__( 'Select', 'woodmart' ),
		);

		if ( is_wp_error( $sliders ) || ! $sliders ) {
			return $output;
		}

		foreach ( $sliders as $slider ) {
			$output[ $slider->slug ] = $slider->name;
		}

		return $output;
	}

	/**
	 * Register the widget controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		/**
		 * Content tab
		 */

		/**
		 * General settings
		 */
		$this->start_controls_section(
			'general_content_section',
			array(
				'label' => esc_html__( 'General', 'woodmart' ),
			)
		);

		$this->add_control(
			'slider',
			array(
				'label'   => esc_html__( 'Slider', 'woodmart' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $this->get_sliders(),
				'default' => '0',
			)
		);

		$this->add_control(
			'carousel_sync',
			array(
				'label'       => esc_html__( 'Synchronization', 'woodmart' ),
				'type'        => Controls_Manager::SELECT,
				'description' => esc_html__( 'Links carousels to navigate together. Use Parent/Child mode to pair two carousels, or Equal group mode to synchronize multiple carousels.', 'woodmart' ),
				'options'     => array(
					''       => esc_html__( 'Disabled', 'woodmart' ),
					'parent' => esc_html__( 'As parent', 'woodmart' ),
					'child'  => esc_html__( 'As child', 'woodmart' ),
					'group'  => esc_html__( 'Equal group', 'woodmart' ),
				),
				'default'     => '',
			)
		);

		$this->add_control(
			'sync_parent_id',
			array(
				'label'       => esc_html__( 'ID', 'woodmart' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'Copy this ID and paste it into the "ID" field of the child carousel.', 'woodmart' ),
				'default'     => 'wd_' . uniqid(),
				'ai'          => array(
					'active' => false,
				),
				'condition'   => array(
					'carousel_sync' => array( 'parent' ),
				),
			)
		);

		$this->add_control(
			'sync_child_id',
			array(
				'label'       => esc_html__( 'ID', 'woodmart' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'Copy the ID from the parent carousel and paste it into this field.', 'woodmart' ),
				'ai'          => array(
					'active' => false,
				),
				'condition'   => array(
					'carousel_sync' => array( 'child' ),
				),
			)
		);

		$this->add_control(
			'sync_group_id',
			array(
				'label'       => esc_html__( 'ID', 'woodmart' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'All carousels with the same ID will be synchronized. Use different IDs for different groups.', 'woodmart' ),
				'default'     => 'group-1',
				'ai'          => array(
					'active' => false,
				),
				'condition'   => array(
					'carousel_sync' => array( 'group' ),
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
		$settings              = $this->get_settings_for_display();
		$settings['elementor'] = true;
		woodmart_shortcode_slider( $settings );
	}
}

Plugin::instance()->widgets_manager->register( new Slider() );
