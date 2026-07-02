<?php
/**
 * CartFlows integration.
 *
 * @package woodmart
 */

if ( ! defined( 'CARTFLOWS_FILE' ) ) {
	return;
}

if ( ! function_exists( 'woodmart_cartflows_is_checkout_override_enabled' ) ) {
	/**
	 * Checks if CartFlows global checkout override is enabled.
	 *
	 * @return bool True if override is enabled, false otherwise.
	 */
	function woodmart_cartflows_is_checkout_override_enabled() {
		$common_settings = get_option( '_cartflows_common', false );

		return ! empty( $common_settings ) && isset( $common_settings['override_global_checkout'] ) && 'enable' === $common_settings['override_global_checkout'];
	}
}

if ( ! function_exists( 'woodmart_cartflows_disable_checkout_template_override' ) ) {
	/**
	 * Disables theme checkout template override when CartFlows is active.
	 *
	 * @param bool $should_override Whether the theme should override the template.
	 *
	 * @return bool False if CartFlows overrides checkout, original value otherwise.
	 */
	function woodmart_cartflows_disable_checkout_template_override( $should_override ) {
		if ( woodmart_cartflows_is_checkout_override_enabled() ) {
			return false;
		}

		return $should_override;
	}

	add_filter( 'woodmart_replace_checkout_template_condition', 'woodmart_cartflows_disable_checkout_template_override' );
}

if ( ! function_exists( 'woodmart_cartflows_enqueue_checkout_styles' ) ) {
	/**
	 * Enqueues styles for CartFlows checkout pages.
	 *
	 * @return void
	 */
	function woodmart_cartflows_enqueue_checkout_styles() {
		wp_enqueue_style( 'wd-int-woo-cartflows-checkout', WOODMART_THEME_DIR . '/css/parts/int-woo-cartflows-checkout.css', array(), WOODMART_VERSION );
	}

	add_action( 'wp_enqueue_scripts', 'woodmart_cartflows_enqueue_checkout_styles', 10001 );
}

if ( ! function_exists( 'woodmart_cartflows_disable_lazy_loading' ) ) {
	/**
	 * Disables lazy loading on CartFlows checkout and order-received pages.
	 *
	 * @return void
	 */
	function woodmart_cartflows_disable_lazy_loading() {
		if ( ! is_checkout() && ! is_wc_endpoint_url( 'order-received' ) && ! ( is_ajax() && isset( $_GET['wc-ajax'] ) ) ) { // phpcs:ignore WordPress.Security
			return;
		}

		if ( woodmart_cartflows_is_checkout_override_enabled() || ( isset( $_GET['wc-ajax'] ) && 'update_order_review' === $_GET['wc-ajax'] ) ) { // phpcs:ignore WordPress.Security
			woodmart_lazy_loading_deinit( true );
		}
	}

	add_action( 'wp', 'woodmart_cartflows_disable_lazy_loading' );
}

if ( ! function_exists( 'woodmart_cartflows_remove_sticky_toolbar' ) ) {
	/**
	 * Removes sticky toolbar on CartFlows checkout and order-received pages.
	 *
	 * @return void
	 */
	function woodmart_cartflows_remove_sticky_toolbar() {
		if ( ! is_checkout() && ! is_wc_endpoint_url( 'order-received' ) ) {
			return;
		}

		if ( woodmart_cartflows_is_checkout_override_enabled() ) {
			remove_action( 'woodmart_before_wp_footer', 'woodmart_sticky_toolbar_template', 250 );
		}
	}

	add_action( 'wp', 'woodmart_cartflows_remove_sticky_toolbar' );
}

if ( ! function_exists( 'woodmart_cartflows_remove_skip_to_content_button' ) ) {
	/**
	 * Removes skip to content button on CartFlows checkout and order-received pages.
	 *
	 * @return void
	 */
	function woodmart_cartflows_remove_skip_to_content_button() {
		if ( ! is_checkout() && ! is_wc_endpoint_url( 'order-received' ) ) {
			return;
		}

		if ( woodmart_cartflows_is_checkout_override_enabled() ) {
			remove_action( 'wp_body_open', 'woodmart_get_skip_main_content_button' );
		}
	}

	add_action( 'wp', 'woodmart_cartflows_remove_skip_to_content_button' );
}
