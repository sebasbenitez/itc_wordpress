<?php
/**
 * Widgets.
 *
 * @package woodmart
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_product_360_view_meta' ) ) {
	/**
	 * Add the 360 product view images metabox to the product edit screen.
	 */
	function woodmart_product_360_view_meta() {
		if ( ! function_exists( 'woodmart_get_opt' ) ) {
			return;
		}

		add_meta_box( 'woocommerce-product-360-images', esc_html__( 'Product 360 View Gallery (optional)', 'woodmart' ), 'woodmart_360_metabox_output', 'product', 'side', 'low' );
	}

	add_action( 'add_meta_boxes', 'woodmart_product_360_view_meta', 50 );
}

if ( ! function_exists( 'woodmart_sguide_add_metaboxes' ) ) {
	/**
	 * Add size guide metaboxes.
	 */
	function woodmart_sguide_add_metaboxes() {
		if ( ! function_exists( 'woodmart_get_opt' ) || ! woodmart_get_opt( 'size_guides' ) ) {
			return;
		}

		// Add table metaboxes to size guide
		add_meta_box( 'woodmart_sguide_metaboxes', esc_html__( 'Create/modify size guide table', 'woodmart' ), 'woodmart_sguide_metaboxes', 'woodmart_size_guide', 'normal', 'default' );

		// Add metaboxes to product
		add_meta_box( 'woodmart_sguide_dropdown_template', esc_html__( 'Choose size guide', 'woodmart' ), 'woodmart_sguide_dropdown_template', 'product', 'side' );

		// Add category metaboxes to size guide
		add_meta_box( 'woodmart_sguide_category_template', esc_html__( 'Choose product categories', 'woodmart' ), 'woodmart_sguide_category_template', 'woodmart_size_guide', 'side' );

		// Add hide table checkbox to size guide
		add_meta_box( 'woodmart_sguide_hide_table_template', esc_html__( 'Hide size guide table', 'woodmart' ), 'woodmart_sguide_hide_table_template', 'woodmart_size_guide', 'side' );
	}

	add_action( 'add_meta_boxes', 'woodmart_sguide_add_metaboxes' );
}
