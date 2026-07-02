<?php
/**
 * Abandoned cart functions.
 *
 * @package woodmart
 */

if ( ! function_exists( 'woodmart_get_abandoned_cart_object_from_db' ) ) {
	/**
	 * Get abandoned cart object from database.
	 * Update old serialized data to new format.
	 *
	 * @param int $cart_id Cart ID.
	 *
	 * @return WC_Cart|null
	 */
	function woodmart_get_abandoned_cart_object_from_db( $cart_id ) {
		$cart_data = get_post_meta( $cart_id, '_cart', true );
		$cart      = null;

		if ( is_array( $cart_data ) && isset( $cart_data['cart'] ) ) {
			$cart = $cart_data['cart'];
		} elseif ( is_string( $cart_data ) ) {
			$cart = maybe_unserialize( $cart_data );

			if ( $cart instanceof \WC_Cart ) {
				update_post_meta( $cart_id, '_cart', array( 'cart' => $cart ) );
			} else {
				delete_post_meta( $cart_id, '_cart' );
			}
		}

		return $cart;
	}
}
