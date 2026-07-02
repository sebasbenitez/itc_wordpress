<?php
/**
 * Compatibility with WPC Composite Products for WooCommerce.
 *
 * @package woodmart
 */

if ( ! class_exists( 'WPCleverWooco' ) ) {
	return;
}

if ( ! function_exists( 'woodmart_wooco_add_custom_product_types' ) ) {
	/**
	 * Adds support for the waitlist module for products of type "composite".
	 *
	 * @param array $types Product types.
	 *
	 * @return array
	 */
	function woodmart_wooco_add_custom_product_types( $types ) {
		$types[] = 'composite';

		return $types;
	}

	add_filter( 'woodmart_waitlist_allowed_product_types', 'woodmart_wooco_add_custom_product_types' );
}
