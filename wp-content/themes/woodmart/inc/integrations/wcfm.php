<?php
/**
 * WCFM integration.
 *
 * @package woodmart
 */

if ( ! function_exists( 'woodmart_wcfm_add_stock_progress_bar_field' ) ) {
	/**
	 * Adds stock progress bar field to WCFM product stock settings.
	 *
	 * @param array $fields List of stock fields.
	 * @param int   $product_id Product ID.
	 * @return array Modified list of fields.
	 */
	function woodmart_wcfm_add_stock_progress_bar_field( $fields, $product_id ) {
		$value = get_post_meta( $product_id, 'woodmart_total_stock_quantity', true );

		$fields['woodmart_total_stock_quantity'] = array(
			'label'       => esc_html__( 'Initial number in stock', 'woodmart' ),
			'type'        => 'text',
			'class'       => 'wcfm-text',
			'label_class' => 'wcfm_title',
			'value'       => $value,
			'hints'       => esc_html__( 'Required for stock progress bar option.', 'woodmart' ),
		);

		return $fields;
	}

	add_filter( 'wcfm_product_fields_stock', 'woodmart_wcfm_add_stock_progress_bar_field', 10, 2 );
}

if ( ! function_exists( 'woodmart_wcfm_save_stock_progress_bar_field' ) ) {
	/**
	 * Saves total stock quantity meta when WCFM product is updated.
	 *
	 * @param int   $post_id Product post ID.
	 * @param array $form_data Form data from WCFM.
	 * @return void
	 */
	function woodmart_wcfm_save_stock_progress_bar_field( $post_id, $form_data ) {
		update_post_meta( $post_id, 'woodmart_total_stock_quantity', $form_data['woodmart_total_stock_quantity'] );
	}

	add_action( 'after_wcfm_products_manage_meta_save', 'woodmart_wcfm_save_stock_progress_bar_field', 10, 2 );
}


if ( ! function_exists( 'woodmart_wcfm_exclude_show_single_variation' ) ) {
	/**
	 * Excludes Show Single Variations from WCFM product queries.
	 *
	 * @return void
	 */
	function woodmart_wcfm_exclude_show_single_variation() {
		if ( class_exists( 'XTS\Modules\Show_Single_Variations\Query' ) ) {
			remove_action( 'pre_get_posts', array( XTS\Modules\Show_Single_Variations\Query::get_instance(), 'add_variations_to_product_query' ) );
		}
	}

	add_action( 'after_wcfm_ajax_controller', 'woodmart_wcfm_exclude_show_single_variation' );
}
