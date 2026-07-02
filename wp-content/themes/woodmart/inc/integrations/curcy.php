<?php
/**
 * CURCY - WooCommerce Multi Currency Premium integration.
 *
 * @package woodmart
 */

if ( ! defined( 'WOOMULTI_CURRENCY_VERSION' ) ) {
	return;
}

if ( ! function_exists( 'woodmart_curcy_convert_shipping_progress_bar_limit' ) ) {
	/**
	 * Converts shipping progress bar limit to current currency.
	 *
	 * @param float $limit The shipping progress bar limit.
	 * @return float Converted limit in current currency.
	 */
	function woodmart_curcy_convert_shipping_progress_bar_limit( $limit ) {
		if ( 'wc' === woodmart_get_opt( 'shipping_progress_bar_calculation', 'custom' ) ) {
			return $limit;
		}

		$currency_data    = WOOMULTI_CURRENCY_Data::get_ins();
		$currencies       = $currency_data->get_list_currencies();
		$current_currency = $currency_data->get_current_currency();

		if ( array_key_exists( $current_currency, $currencies ) ) {
			$limit *= $currencies[ $current_currency ]['rate'];
		}

		return $limit;
	}

	add_filter( 'woodmart_shipping_progress_bar_amount', 'woodmart_curcy_convert_shipping_progress_bar_limit' );
}
