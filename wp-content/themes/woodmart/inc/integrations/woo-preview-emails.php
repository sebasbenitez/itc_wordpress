<?php
/**
 * Preview E-mails for WooCommerce integration.
 *
 * @package woodmart
 */

use XTS\WC_Wishlist\Ui;

if ( ! defined( 'WOO_PREVIEW_EMAILS_DIR' ) ) {
	return;
}

if ( ! function_exists( 'woodmart_woo_preview_skip_wishlist_emails' ) ) {
	/**
	 * Skips custom wishlist emails from default order preview handling.
	 *
	 * @param mixed  $additional_data Additional data.
	 * @param string $index Email index.
	 *
	 * @return bool|mixed Returns true to skip order creation, otherwise returns original data.
	 */
	function woodmart_woo_preview_skip_wishlist_emails( $additional_data, $index ) {
		$emails_list = array(
			'XTS_Email_Wishlist_Back_In_Stock',
			'XTS_Email_Wishlist_On_Sale_Products',
			'XTS_Email_Wishlist_Promotional',
		);

		if ( in_array( $index, $emails_list, true ) ) {
			return true;
		}

		return $additional_data;
	}

	add_filter( 'woo_preview_additional_orderID', 'woodmart_woo_preview_skip_wishlist_emails', 10, 2 );
}

if ( ! function_exists( 'woodmart_woo_preview_trigger_wishlist_emails' ) ) {
	/**
	 * Triggers custom wishlist email content for preview.
	 *
	 * @param object $current_email Current email object.
	 * @return void
	 */
	function woodmart_woo_preview_trigger_wishlist_emails( $current_email ) {
		$user_id = get_current_user_id();

		if ( 'woodmart_back_in_stock_email' === $current_email->id ) {
			$products_back_in_stock = get_option( 'woodmart_wishlist_products_back_in_stock', array() );

			if ( ! $products_back_in_stock || empty( $products_back_in_stock[ $user_id ] ) ) {
				$args  = array(
					'posts_per_page' => 4,
					'post_type'      => 'product',
					'orderby'        => 'rand',
				);
				$query = new WP_Query( $args );

				if ( ! isset( $query->posts ) ) {
					return;
				}

				foreach ( $query->posts as $post ) {
					$products_back_in_stock[ $user_id ][] = $post->ID;
				}
			}

			do_action( 'woodmart_send_back_in_stock_mail', $user_id, $products_back_in_stock[ $user_id ] );
		} elseif ( 'woodmart_on_sale_products_email' === $current_email->id ) {
			$ui                  = Ui::get_instance();
			$products_on_sales   = array();
			$product_ids_on_sale = wc_get_product_ids_on_sale();

			if ( $ui->get_wishlist() ) {
				$wishlist_products = $ui->get_wishlist()->get_all();

				foreach ( $wishlist_products as $product_data ) {
					if ( in_array( $product_data['product_id'], $product_ids_on_sale ) ) { //phpcs:ignore
						$products_on_sales[ $product_data['product_id'] ] = $product_data['product_id'];
					}
				}
			}

			if ( ! $products_on_sales ) {
				$args  = array(
					'posts_per_page' => 4,
					'post_type'      => 'product',
					'orderby'        => 'rand',
				);
				$query = new WP_Query( $args );

				if ( ! isset( $query->posts ) ) {
					return;
				}

				foreach ( $query->posts as $post ) {
					$products_on_sales[] = $post->ID;
				}
			}

			do_action( 'woodmart_send_on_sale_products_mail', $user_id, $products_on_sales );
		} elseif ( 'woodmart_promotional_email' === $current_email->id ) {
			$products_for_promotion = array();
			$args                   = array(
				'posts_per_page' => 4,
				'post_type'      => 'product',
				'orderby'        => 'rand',
			);
			$query                  = new WP_Query( $args );

			if ( ! isset( $query->posts ) ) {
				return;
			}

			foreach ( $query->posts as $post ) {
				$products_for_promotion[ $user_id ][] = $post->ID;
			}

			do_action( 'woodmart_send_promotional_mail', $user_id, $products_for_promotion[ $user_id ], '', '' );
		}
	}

	add_action( 'woo_preview_additional_order_trigger', 'woodmart_woo_preview_trigger_wishlist_emails', 10 );
}
