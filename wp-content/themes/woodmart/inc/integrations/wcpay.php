<?php
/**
 * WooPayments integration.
 *
 * @package woodmart
 */

use WCPay\MultiCurrency\MultiCurrency;

if ( ! defined( 'WCPAY_VERSION_NUMBER' ) ) {
	return;
}

if ( ! function_exists( 'woodmart_wcpay_convert_shipping_progress_bar_limit' ) ) {
	/**
	 * Converts shipping progress bar limit to selected currency rate.
	 *
	 * @param float $limit Original price limit.
	 * @return float Converted price limit based on selected currency rate.
	 */
	function woodmart_wcpay_convert_shipping_progress_bar_limit( $limit ) {
		if ( 'wc' === woodmart_get_opt( 'shipping_progress_bar_calculation', 'custom' ) ) {
			return $limit;
		}

		$limit *= MultiCurrency::instance()->get_selected_currency()->get_rate();

		return $limit;
	}

	add_action( 'woodmart_shipping_progress_bar_amount', 'woodmart_wcpay_convert_shipping_progress_bar_limit' );
}
