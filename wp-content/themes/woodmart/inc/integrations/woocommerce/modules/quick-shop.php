<?php
/**
 * Quick shop module.
 *
 * @package woodmart
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

if ( ! function_exists( 'woodmart_quick_shop' ) ) {
	/**
	 * Quick shop ajax handler.
	 *
	 * @param int $id Product ID.
	 * @return void
	 */
	function woodmart_quick_shop( $id = false ) {
		if ( isset( $_GET['id'] ) ) { // phpcs:ignore WordPress.Security
			$id = sanitize_text_field( (int) $_GET['id'] );  // phpcs:ignore WordPress.Security
		}

		if ( ! $id || ! woodmart_woocommerce_installed() || post_password_required( $id ) ) {
			return;
		}

		$args = array(
			'post__in'  => array( $id ),
			'post_type' => 'product',
		);

		$quick_posts = get_posts( $args );

		woodmart_enqueue_inline_style( 'woo-opt-quick-shop' );
		woodmart_enqueue_inline_style( 'woo-mod-stock-status' );

		foreach ( $quick_posts as $quick_post ) :
			setup_postdata( $quick_post );
			?>
			<div class="quick-shop-wrapper wd-quantity-overlap wd-fill wd-scroll">
				<div class="quick-shop-close wd-action-btn wd-style-text wd-cross-icon">
					<a href="#" rel="nofollow noopener">
						<span class="wd-action-icon"></span>
						<span class="wd-action-text">
						<?php esc_html_e( 'Close', 'woodmart' ); ?></span>
					</a>
				</div>
				<div class="quick-shop-form text-center wd-scroll-content">
					<?php woocommerce_template_single_add_to_cart(); ?>
				</div>
			</div>
			<?php
		endforeach;

		wp_reset_postdata();

		die();
	}

	add_action( 'wp_ajax_woodmart_quick_shop', 'woodmart_quick_shop' );
	add_action( 'wp_ajax_nopriv_woodmart_quick_shop', 'woodmart_quick_shop' );
}

if ( ! function_exists( 'woodmart_quick_shop_wrapper' ) ) {
	/**
	 * Quick shop wrapper.
	 *
	 * @return void
	 */
	function woodmart_quick_shop_wrapper() {
		if ( ! woodmart_get_opt( 'quick_shop_variable' ) ) {
			return;
		}
		?>
			<div class="quick-shop-wrapper wd-quantity-overlap wd-fill wd-scroll">
				<div class="quick-shop-close wd-action-btn wd-style-text wd-cross-icon">
					<a href="#" rel="nofollow noopener">
						<span class="wd-action-icon"></span>
						<span class="wd-action-text">
							<?php esc_html_e( 'Close', 'woodmart' ); ?>
						</span>
					</a>
				</div>
				<div class="quick-shop-form text-center wd-scroll-content">
				</div>
			</div>
		<?php
	}
}

if ( ! function_exists( 'woodmart_load_available_variations' ) ) {
	/**
	 * Load available variations for quick shop.
	 *
	 * @return void
	 */
	function woodmart_load_available_variations() {
		if ( empty( $_GET['id'] ) || ! woodmart_woocommerce_installed() ) { // phpcs:ignore
			return;
		}

		$product = wc_get_product( absint( $_GET['id'] ) ); // phpcs:ignore

		if ( ! $product || ! $product->is_type( 'variable' ) || 'publish' !== $product->get_status() || 'hidden' === $product->get_catalog_visibility() || post_password_required( $product->get_id() ) ) {
			return;
		}

		$cache          = apply_filters( 'woodmart_swatches_cache', true );
		$transient_name = 'woodmart_swatches_cache_' . $product->get_id();

		if ( $cache ) {
			$available_variations = get_transient( $transient_name );
		} else {
			$available_variations = array();
		}

		if ( ! $available_variations ) {
			$available_variations = $product->get_available_variations();

			if ( $cache ) {
				set_transient( $transient_name, $available_variations, apply_filters( 'woodmart_swatches_cache_time', WEEK_IN_SECONDS ) );
			}
		}

		wp_send_json( $available_variations );
	}

	add_action( 'wp_ajax_woodmart_load_available_variations', 'woodmart_load_available_variations' );
	add_action( 'wp_ajax_nopriv_woodmart_load_available_variations', 'woodmart_load_available_variations' );
}
