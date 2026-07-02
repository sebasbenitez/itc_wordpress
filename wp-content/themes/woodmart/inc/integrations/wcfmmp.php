<?php
/**
 * WCFM Marketplace integration.
 *
 * @package woodmart
 */

if ( ! function_exists( 'woodmart_wcfmmp_set_vendor_shop_page_link' ) ) {
	/**
	 * Sets vendor store URL as shop page link on vendor product pages.
	 *
	 * @param int $user_id Vendor user ID.
	 * @return void
	 */
	function woodmart_wcfmmp_set_vendor_shop_page_link( $user_id ) {
		add_filter(
			'woodmart_shop_page_link',
			function ( $url ) use ( $user_id ) {
				if ( function_exists( 'wcfmmp_get_store_url' ) ) {
					$url = wcfmmp_get_store_url( $user_id );
				}

				return $url;
			}
		);
	}

	add_action( 'wcfmmp_before_store_product', 'woodmart_wcfmmp_set_vendor_shop_page_link' );
}

if ( ! function_exists( 'woodmart_wcfmmp_check_per_page_expression' ) ) {
	/**
	 * Checks if per page filter should be applied for WCFM Marketplace requests.
	 *
	 * @return bool True if per page filter should apply, false otherwise.
	 */
	function woodmart_wcfmmp_check_per_page_expression() {
		return ( ( isset( $_POST['action'] ) && 'wcfmmp_stores_list_map_markers' !== $_POST['action'] ) || ! isset( $_POST['action'] ) ); // phpcs:ignore
	}

	add_action( 'woodmart_per_page_custom_expression', 'woodmart_wcfmmp_check_per_page_expression' );
}

if ( ! function_exists( 'woodmart_wcfmmp_apply_custom_per_page' ) ) {
	/**
	 * Applies custom per page value to WCFM Marketplace store listings.
	 *
	 * @return void
	 */
	function woodmart_wcfmmp_apply_custom_per_page() {
		add_filter(
			'wcfmmp_store_ppp',
			function ( $post_per_page ) {
				if ( isset( $_REQUEST['per_page'] ) && 1 != $_REQUEST['per_page'] && ! isset( $_REQUEST['_locale'] ) && apply_filters( 'woodmart_per_page_custom_expression', true ) ) { // phpcs:ignore
					$post_per_page = $_REQUEST['per_page']; // phpcs:ignore
				}

				return $post_per_page;
			},
			50
		);
	}

	add_action( 'init', 'woodmart_wcfmmp_apply_custom_per_page' );
}
