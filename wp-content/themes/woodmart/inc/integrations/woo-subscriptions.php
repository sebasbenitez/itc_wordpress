<?php
/**
 * WooCommerce Subscriptions integration.
 *
 * @package woodmart
 */

if ( ! class_exists( 'WC_Subscriptions' ) ) {
	return;
}

if ( ! function_exists( 'woodmart_wc_subscriptions_add_variable_types' ) ) {
	/**
	 * Adds variable subscription product type to WoodMart variable product types list.
	 *
	 * @param array $types List of variable product types.
	 * @return array Modified list of product types.
	 */
	function woodmart_wc_subscriptions_add_variable_types( $types ) {
		$types[] = 'variable-subscription';
		$types[] = 'subscription_variation';

		return $types;
	}

	add_filter( 'woodmart_variable_product_types', 'woodmart_wc_subscriptions_add_variable_types' );
}

if ( ! function_exists( 'woodmart_wc_subscriptions_add_supported_types' ) ) {
	/**
	 * Adds subscription product types to waitlist and price tracker features.
	 *
	 * @param array $types List of allowed product types.
	 * @return array Modified list of product types.
	 */
	function woodmart_wc_subscriptions_add_supported_types( $types ) {
		$types[] = 'subscription';
		$types[] = 'variable-subscription';
		$types[] = 'subscription_variation';

		return $types;
	}

	add_filter( 'woodmart_waitlist_allowed_product_types', 'woodmart_wc_subscriptions_add_supported_types' );
	add_filter( 'woodmart_price_tracker_allowed_product_types', 'woodmart_wc_subscriptions_add_supported_types' );
}
