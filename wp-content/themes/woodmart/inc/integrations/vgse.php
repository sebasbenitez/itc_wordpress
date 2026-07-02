<?php
/**
 * WP Sheet Editor integration.
 *
 * @package woodmart
 */

if ( ! defined( 'VGSE_MAIN_FILE' ) ) {
	return;
}

if ( ! function_exists( 'woodmart_vgse_trigger_waitlist_emails_on_stock_update' ) ) {
	/**
	 * Triggers waitlist in-stock email notifications when product stock status is updated via sheet editor.
	 *
	 * @param string $post_type Post type being edited.
	 * @param int    $post_id Post ID.
	 * @param string $key Field key being updated.
	 * @param mixed  $new_value New field value.
	 * @return void
	 */
	function woodmart_vgse_trigger_waitlist_emails_on_stock_update( $post_type, $post_id, $key, $new_value ) {
		if ( ! woodmart_get_opt( 'waitlist_enabled' ) || 'product' !== $post_type || '_stock_status' !== $key || 'instock' !== $new_value ) {
			return;
		}

		$db_storage = XTS\Modules\Waitlist\DB_Storage::get_instance();
		$product    = wc_get_product( $post_id );

		$waitlists       = $db_storage->get_subscriptions_by_product( $product );
		$waitlists_chunk = array_chunk( $waitlists, apply_filters( 'woodmart_waitlist_scheduled_email_chunk', 50 ) );
		$schedule_time   = time();

		foreach ( $waitlists_chunk as $waitlist_chunk ) {
			wp_schedule_single_event(
				$schedule_time,
				'woodmart_waitlist_send_in_stock',
				array( $waitlist_chunk )
			);

			$schedule_time += apply_filters( 'woodmart_waitlist_schedule_time', intval( woodmart_get_opt( 'waitlist_wait_interval', HOUR_IN_SECONDS ) ) );
		}
	}

	add_action( 'vg_sheet_editor/save_rows/after_saving_cell', 'woodmart_vgse_trigger_waitlist_emails_on_stock_update', 10, 4 );
}
