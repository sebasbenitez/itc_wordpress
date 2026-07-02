<?php
/**
 * Compatibility with WPC Product Bundles for WooCommerce.
 *
 * @package woodmart
 */

if ( ! class_exists( 'WPCleverWoosb' ) ) {
	return;
}

if ( ! function_exists( 'woodmart_woosb_add_custom_product_types' ) ) {
	/**
	 * Adds support for the waitlist module for products of type "woosb".
	 *
	 * @param array $types Product types.
	 *
	 * @return array
	 */
	function woodmart_woosb_add_custom_product_types( $types ) {
		$types[] = 'woosb';

		return $types;
	}

	add_filter( 'woodmart_waitlist_allowed_product_types', 'woodmart_woosb_add_custom_product_types' );
}

if ( ! function_exists( 'woodmart_woosb_send_waitlist_instock_email_emails' ) ) {
	/**
	 * Send waitlist emails for bundled products when they are back in stock.
	 *
	 * @param integer $product_id Product ID.
	 * @param string  $stock_status Stock status product.
	 * @param object  $product Data product.
	 *
	 * @return void
	 */
	function woodmart_woosb_send_waitlist_instock_email_emails( $product_id, $stock_status, $product ) {
		if (
			! woodmart_get_opt( 'waitlist_enabled' ) ||
			'instock' !== $stock_status ||
			! function_exists( 'WPCleverWoosb' ) ||
			$product->is_type( 'woosb' )
		) {
			return;
		}

		$bundles    = WPCleverWoosb()->get_bundles( $product_id, 500, 0, 'edit' );
		$bundle_ids = array();

		if ( ! empty( $bundles ) ) {
			foreach ( $bundles as $bundle ) {
				if ( $bundle->is_in_stock() ) {
					$bundle_ids[] = $bundle->get_id();
				}
			}
		}

		if ( ! empty( $bundle_ids ) ) {
			$waitlist_db_storage = XTS\Modules\Waitlist\DB_Storage::get_instance();

			foreach ( $bundle_ids as $bundle_id ) {
				$bundle_product = wc_get_product( $bundle_id );

				$waitlists       = $waitlist_db_storage->get_subscriptions_by_product( $bundle_product );
				$waitlists_chunk = array_chunk( $waitlists, apply_filters( 'woodmart_waitlist_scheduled_email_chunk', 50 ) );
				$schedule_time   = time() + 10;

				foreach ( $waitlists_chunk as $waitlist_chunk ) {
					if ( ! wp_next_scheduled( 'woodmart_waitlist_send_in_stock', array( $waitlist_chunk ) ) ) {
						wp_schedule_single_event(
							$schedule_time,
							'woodmart_waitlist_send_in_stock',
							array( $waitlist_chunk )
						);
					}

					$schedule_time += apply_filters( 'woodmart_waitlist_schedule_time', intval( woodmart_get_opt( 'waitlist_wait_interval', HOUR_IN_SECONDS ) ) ) + 1;
				}
			}
		}
	}

	add_action( 'woocommerce_product_set_stock_status', 'woodmart_woosb_send_waitlist_instock_email_emails', 20, 3 );
	add_action( 'woocommerce_variation_set_stock_status', 'woodmart_woosb_send_waitlist_instock_email_emails', 20, 3 );
}
