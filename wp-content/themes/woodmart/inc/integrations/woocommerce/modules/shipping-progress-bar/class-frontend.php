<?php
/**
 * Shipping progress bar frontend class.
 *
 * @package woodmart
 */

namespace XTS\Modules\Shipping_Progress_Bar;

use XTS\Singleton;
use XTS\Modules\Layouts\Main as Builder;

/**
 * Shipping progress bar frontend class.
 */
class Frontend extends Singleton {
	/**
	 * Init.
	 */
	public function init() {
		if ( woodmart_get_opt( 'shipping_progress_bar_enabled' ) && woodmart_woocommerce_installed() ) {
			add_action( 'wp', array( $this, 'output_shipping_progress_bar' ), 100 );
			add_action( 'init', array( $this, 'output_shipping_progress_bar_in_mini_cart' ), 100 );
		}
	}

	/**
	 * Output shipping progress bar.
	 */
	public function output_shipping_progress_bar() {
		if ( ! woodmart_get_opt( 'shipping_progress_bar_enabled' ) ) {
			return;
		}

		if ( woodmart_get_opt( 'shipping_progress_bar_location_card_page' ) && ! Builder::get_instance()->has_custom_layout( 'cart' ) ) {
			add_action( 'woocommerce_before_cart_table', array( $this, 'render_shipping_progress_bar_with_wrapper' ) );
		}

		if ( woodmart_get_opt( 'shipping_progress_bar_location_single_product' ) ) {
			add_action( 'woocommerce_single_product_summary', array( $this, 'render_shipping_progress_bar_with_wrapper' ), 29 );
		}

		if ( woodmart_get_opt( 'shipping_progress_bar_location_checkout' ) ) {
			add_action( 'woocommerce_checkout_billing', array( $this, 'render_shipping_progress_bar_with_wrapper' ) );
		}
	}

	/**
	 * Update fragments shipping progress bar.
	 *
	 * @return void
	 */
	public function output_shipping_progress_bar_in_mini_cart() {
		if ( ! woodmart_get_opt( 'shipping_progress_bar_enabled' ) ) {
			return;
		}

		if ( woodmart_get_opt( 'shipping_progress_bar_location_mini_cart' ) ) {
			add_action( 'woocommerce_widget_shopping_cart_before_buttons', array( $this, 'render_shipping_progress_bar' ) );
		}

		add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'get_shipping_progress_bar_fragments' ), 40 );
		add_filter( 'woocommerce_update_order_review_fragments', array( $this, 'get_shipping_progress_bar_checkout_fragments' ), 10 );
	}

	/**
	 * Get shipping progress bar content.
	 *
	 * @codeCoverageIgnore
	 * @return void
	 */
	public function render_shipping_progress_bar_with_wrapper() {
		?>
		<div class="wd-shipping-progress-bar wd-style-bordered">
			<?php $this->render_shipping_progress_bar(); ?>
		</div>
		<?php
	}

	/**
	 * Add shipping progress bar fragment.
	 *
	 * @param array $fragments Fragments.
	 *
	 * @return array
	 */
	public function get_shipping_progress_bar_checkout_fragments( $fragments ) {
		ob_start();

		$this->render_shipping_progress_bar();

		$content = ob_get_clean();

		$fragments['div.wd-free-progress-bar'] = $content;

		return $fragments;
	}

	/**
	 * Add shipping progress bar fragment.
	 *
	 * @param array $fragments Fragments.
	 *
	 * @return array
	 */
	public function get_shipping_progress_bar_fragments( $fragments ) {
		ob_start();

		$this->render_shipping_progress_bar();

		$content = ob_get_clean();

		if ( apply_filters( 'woodmart_update_fragments_fix', true ) ) {
			$fragments['div.wd-free-progress-bar_wd'] = $content;
		} else {
			$fragments['div.wd-free-progress-bar'] = $content;
		}

		return $fragments;
	}

	/**
	 * Render free shipping progress bar.
	 *
	 * @codeCoverageIgnore
	 */
	public function render_shipping_progress_bar() {
		if ( ! woodmart_get_opt( 'shipping_progress_bar_enabled' ) ) {
			return;
		}

		$calculation     = woodmart_get_opt( 'shipping_progress_bar_calculation', 'custom' );
		$wrapper_classes = '';
		$percent         = 100;
		$limit           = 0;
		$free_shipping   = false;

		if ( ! is_object( WC() ) || ! property_exists( WC(), 'cart' ) || ! is_object( WC()->cart ) ) {
			$cart_price  = 0;
			$calculation = 'custom';
		} else {
			$cart_object = WC()->cart;
			$totals      = $cart_object->get_totals();

			switch ( woodmart_get_opt( 'shipping_progress_bar_base_price', 'displayed_subtotal' ) ) {
				case 'subtotal':
					$cart_price = floatval( $totals['subtotal'] );
					break;
				case 'total':
					$cart_price = floatval( $totals['total'] );
					break;
				default:
					$cart_price = floatval( WC()->cart->get_displayed_subtotal() );
					break;
			}

			$cart_price = apply_filters( 'woodmart_shipping_progress_bar_cart_price', $cart_price, $cart_object );
		}

		if ( 'wc' === $calculation ) {
			$packages = WC()->cart->get_shipping_packages();
			$package  = reset( $packages );
			$zone     = wc_get_shipping_zone( $package );

			foreach ( $zone->get_shipping_methods( true ) as $method ) {
				if ( 'free_shipping' === $method->id ) {
					$limit = wc_format_decimal( $method->get_option( 'min_amount' ) );
				}
			}
		} elseif ( 'custom' === $calculation ) {
			$limit = woodmart_get_opt( 'shipping_progress_bar_amount' );
		}

		if ( $cart_price && 'include' === woodmart_get_opt( 'shipping_progress_bar_include_coupon' ) && WC()->cart->get_coupons() ) {
			foreach ( WC()->cart->get_coupons() as $coupon ) {
				$cart_price -= WC()->cart->get_coupon_discount_amount( $coupon->get_code(), WC()->cart->display_cart_ex_tax );

				if ( $coupon->get_free_shipping() ) {
					$free_shipping = true;
					break;
				}
			}
		}

		$limit = floatval( apply_filters( 'woodmart_shipping_progress_bar_amount', $limit ) );

		if ( ! $limit ) {
			return;
		}

		if ( $cart_price < $limit && ! $free_shipping ) {
			$percent = floor( ( $cart_price / $limit ) * 100 );
			$message = str_replace( '[remainder]', wc_price( $limit - $cart_price ), woodmart_get_opt( 'shipping_progress_bar_message_initial' ) );
		} else {
			$message = woodmart_get_opt( 'shipping_progress_bar_message_success' );
		}

		if ( 0 === (int) $cart_price || $percent < 0 ) {
			$wrapper_classes .= ' wd-progress-hide';
		}

		?>
		<div class="wd-progress-bar wd-free-progress-bar<?php echo esc_attr( $wrapper_classes ); ?>">
			<div class="progress-msg">
				<?php echo do_shortcode( $message ); ?>
			</div>
			<div class="progress-area">
				<div class="progress-bar" style="width: <?php echo esc_attr( $percent ); ?>%"></div>
			</div>
		</div>
		<?php
	}
}

Frontend::get_instance();
