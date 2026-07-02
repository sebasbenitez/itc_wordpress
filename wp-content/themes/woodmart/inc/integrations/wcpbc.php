<?php
/**
 * WooCommerce Price Based on Country.
 *
 * @package woodmart
 */

if ( ! defined( 'WCPBC_PLUGIN_FILE' ) ) {
	return;
}

add_filter( 'woodmart_do_not_recalulate_total_on_get_refreshed_fragments', '__return_true' );

if ( ! function_exists( 'woodmart_wcpbc_convert_price' ) ) {
	/**
	 * Convert product price based on the current pricing zone.
	 *
	 * @param float $price Product price.
	 * @return float|int
	 */
	function woodmart_wcpbc_convert_price( $price ) {
		if ( function_exists( 'wcpbc_the_zone' ) ) {
			$zone = wcpbc_the_zone();

			if ( $zone && is_object( $zone ) && method_exists( $zone, 'get_exchange_rate_price' ) ) {
				return $zone->get_exchange_rate_price( $price, false );
			}
		}

		return $price;
	}

	add_filter( 'woodmart_pricing_amount_discounts_value', 'woodmart_wcpbc_convert_price', 10, 1 );
	add_filter( 'woodmart_product_pricing_amount_discounts_value', 'woodmart_wcpbc_convert_price', 10, 1 );
	add_filter( 'woodmart_fbt_set_product_price_cart', 'woodmart_wcpbc_convert_price' );
	add_filter( 'woodmart_free_gift_price', 'woodmart_wcpbc_convert_price' );
	add_filter( 'woodmart_shipping_progress_bar_amount', 'woodmart_wcpbc_convert_price' );
}
