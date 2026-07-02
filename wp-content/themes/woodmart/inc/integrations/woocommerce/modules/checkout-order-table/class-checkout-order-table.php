<?php
/**
 * Product order on checkout page.
 *
 * @package woodmart
 */

namespace XTS\Modules;

use XTS\Admin\Modules\Options;
use XTS\Singleton;

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * Checkout order table class.
 */
class Checkout_Order_Table extends Singleton {
	/**
	 * Init.
	 */
	public function init() {
		$this->hooks();
	}

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'add_options' ) );
		add_action( 'woocommerce_review_order_before_cart_contents', array( $this, 'checkout_table_content_replacement' ) );
		add_filter( 'woocommerce_get_cart_url', array( $this, 'restore_checkout_undo' ), 10, 1 );
	}

	/**
	 * Appends removed_item to cart URL when redirecting from checkout with empty cart,
	 * so Undo link continues to work after redirect.
	 *
	 * @param string $url The cart URL.
	 * @return string The cart URL with the removed_item param.
	 */
	public function restore_checkout_undo( $url ) {
		if ( is_checkout() && isset( $_GET['removed_item'] ) && woodmart_get_opt( 'checkout_remove_button' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$url = add_query_arg( 'removed_item', '1', $url );
		}
		return $url;
	}

	/**
	 * Add options
	 */
	public function add_options() {
		Options::add_field(
			array(
				'id'       => 'checkout_show_product_image',
				'name'     => esc_html__( 'Product image', 'woodmart' ),
				'hint'     => '<video data-src="' . WOODMART_TOOLTIP_URL . 'checkout-show-product-image.mp4" autoplay loop muted></video>',
				'type'     => 'switcher',
				'section'  => 'checkout_section',
				'default'  => false,
				'tags'     => array(
					esc_html__( 'cart thumbnail', 'woodmart' ),
					esc_html__( 'checkout image', 'woodmart' ),
				),
				'priority' => 10,
			)
		);

		Options::add_field(
			array(
				'id'       => 'checkout_product_quantity',
				'name'     => esc_html__( 'Quantity', 'woodmart' ),
				'hint'     => '<video data-src="' . WOODMART_TOOLTIP_URL . 'checkout-product-quantity.mp4" autoplay loop muted></video>',
				'type'     => 'switcher',
				'section'  => 'checkout_section',
				'default'  => false,
				'priority' => 20,
			)
		);

		Options::add_field(
			array(
				'id'       => 'checkout_remove_button',
				'name'     => esc_html__( 'Remove button', 'woodmart' ),
				'hint'     => '<video data-src="' . WOODMART_TOOLTIP_URL . 'checkout-remove-button.mp4" autoplay loop muted></video>',
				'type'     => 'switcher',
				'section'  => 'checkout_section',
				'default'  => false,
				'priority' => 30,
			)
		);

		Options::add_field(
			array(
				'id'          => 'checkout_link_to_product',
				'name'        => esc_html__( 'Link to product', 'woodmart' ),
				'description' => esc_html__( 'Enable the ability to go to the product page from the order table at checkout.', 'woodmart' ),
				'type'        => 'switcher',
				'section'     => 'checkout_section',
				'default'     => false,
				'priority'    => 40,
			)
		);
	}

	/**
	 * Check whether you need to rewrite the default review-order.php product table.
	 *
	 * @return bool
	 */
	public function is_enable_woodmart_product_table_template() {
		$condition = woodmart_get_opt( 'checkout_show_product_image' ) || woodmart_get_opt( 'checkout_product_quantity' ) || woodmart_get_opt( 'checkout_remove_button' ) || woodmart_get_opt( 'checkout_link_to_product' ) || woodmart_get_opt( 'show_sku_in_checkout_page' ) || woodmart_get_opt( 'estimate_delivery_show_on_checkout_page' );

		return apply_filters( 'woodmart_replace_checkout_template_condition', $condition );
	}

	/**
	 * Replaces default review-order.php product table by woodmart product table template (checkout/review-order-product-table.php).
	 * Adds filter to hide default review order product table output.
	 *
	 * @codeCoverageIgnore
	 */
	public function checkout_table_content_replacement() {
		if ( ! is_checkout() || ! $this->is_enable_woodmart_product_table_template() ) {
			return;
		}

		require_once WOODMART_THEMEROOT . '/inc/integrations/woocommerce/modules/checkout-order-table/templates/review-order-product-table.php';
		add_filter( 'woocommerce_checkout_cart_item_visible', '__return_false' );
	}
}

Checkout_Order_Table::get_instance();
