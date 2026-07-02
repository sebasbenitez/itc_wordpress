<?php
/**
 * FunnelKit Funnel Builder integration.
 *
 * @package woodmart
 */

if ( ! class_exists( 'WFFN_Core' ) ) {
	return;
}

if ( ! function_exists( 'woodmart_get_fkcart_fragments' ) ) {
	/**
	 * Get cart fragments for FunnelKit.
	 *
	 * @param array $fragments Cart fragments.
	 *
	 * @return array
	 */
	function woodmart_get_fkcart_fragments( $fragments ) {
		ob_start();
		woodmart_cart_count();
		$count = ob_get_clean();

		ob_start();
		woodmart_cart_subtotal();
		$subtotal = ob_get_clean();

		$fragments['span.wd-cart-number']   = $count;
		$fragments['span.wd-cart-subtotal'] = $subtotal;

		return $fragments;
	}
}
