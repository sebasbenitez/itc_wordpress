<?php
/**
 * Slider map.
 *
 * @package woodmart
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

if ( ! function_exists( 'woodmart_get_vc_map_slider' ) ) {
	/**
	 * Get map for slider shortcode.
	 */
	function woodmart_get_vc_map_slider() {
		return array(
			'name'        => esc_html__( 'Slider', 'woodmart' ),
			'base'        => 'woodmart_slider',
			'category'    => woodmart_get_tab_title_category_for_wpb( esc_html__( 'Theme elements', 'woodmart' ) ),
			'description' => esc_html__( 'WoodMart theme slider', 'woodmart' ),
			'icon'        => WOODMART_ASSETS . '/images/vc-icon/slider.svg',
			'params'      => array(
				array(
					'type'       => 'woodmart_dropdown',
					'heading'    => esc_html__( 'Slider', 'woodmart' ),
					'param_name' => 'slider',
					'callback'   => 'woodmart_get_sliders_for_vc',
				),
				array(
					'param_name'       => 'carousel_sync',
					'type'             => 'woodmart_dropdown',
					'heading'          => esc_html__( 'Synchronization', 'woodmart' ),
					'hint'             => esc_html__( 'Links carousels to navigate together. Use Parent/Child mode to pair two carousels, or Equal group mode to synchronize multiple carousels.', 'woodmart' ),
					'value'            => array(
						esc_html__( 'Disabled', 'woodmart' )  => '',
						esc_html__( 'As parent', 'woodmart' ) => 'parent',
						esc_html__( 'As child', 'woodmart' )  => 'child',
						esc_html__( 'Equal group', 'woodmart' )  => 'group',
					),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'textfield',
					'heading'          => esc_html__( 'ID', 'woodmart' ),
					'param_name'       => 'sync_parent_id',
					'hint'             => esc_html__( 'Copy this ID and paste it into the "ID" field of the child carousel.', 'woodmart' ),
					'std'              => 'wd_' . uniqid(),
					'save_always'      => true,
					'wd_dependency'    => array(
						'element' => 'carousel_sync',
						'value'   => array( 'parent' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'textfield',
					'heading'          => esc_html__( 'ID', 'woodmart' ),
					'param_name'       => 'sync_child_id',
					'hint'             => esc_html__( 'Copy the ID from the parent carousel and paste it into this field.', 'woodmart' ),
					'wd_dependency'    => array(
						'element' => 'carousel_sync',
						'value'   => array( 'child' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'textfield',
					'heading'          => esc_html__( 'ID', 'woodmart' ),
					'param_name'       => 'sync_group_id',
					'hint'             => esc_html__( 'All carousels with the same ID will be synchronized. Use different IDs for different groups.', 'woodmart' ),
					'std'              => 'group-1',
					'value'            => 'group-1',
					'save_always'      => true,
					'wd_dependency'    => array(
						'element' => 'carousel_sync',
						'value'   => array( 'group' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'       => 'textfield',
					'heading'    => esc_html__( 'Extra class name', 'woodmart' ),
					'param_name' => 'el_class',
					'hint'       => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'woodmart' ),
				),
			),
		);
	}
}

if ( ! function_exists( 'woodmart_get_sliders_for_vc' ) ) {
	/**
	 * Get sliders for Visual Composer.
	 */
	function woodmart_get_sliders_for_vc() {
		$args    = array(
			'taxonomy'   => 'woodmart_slider',
			'hide_empty' => false,
		);
		$sliders = get_terms( $args );

		if ( is_wp_error( $sliders ) || empty( $sliders ) ) {
			return array();
		}

		$data = array();

		foreach ( $sliders as $slider ) {
			$data[ $slider->name ] = $slider->slug;
		}

		return $data;
	}
}
