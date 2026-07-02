<?php
/**
 * Render Woo Hook block for Gutenberg.
 *
 * @package woodmart
 */

use XTS\Modules\Compare\Ui as Compare;
use XTS\Modules\Layouts\Loop_Item;
use XTS\Modules\Layouts\Main;
use XTS\Modules\Linked_Variations\Frontend as Linked_Variations;
use XTS\Modules\Shipping_Progress_Bar\Frontend as Shipping_Progress_Bar;
use XTS\WC_Wishlist\Ui as Wishlist;
use XTS\Modules\Visitor_Counter\Main as Visitor_Counter;
use XTS\Modules\Sold_Counter\Main as Sold_Counter;
use XTS\Modules\Estimate_Delivery\Frontend as Estimate_Delivery_Frontend;
use XTS\Modules\Dynamic_Discounts\Frontend as Dynamic_Discounts_Frontend;
use Automattic\WooCommerce\Internal\Orders\OrderAttributionController;

if ( ! function_exists( 'wd_gutenberg_woo_hook' ) ) {
	/**
	 * Render Woo Hook block.
	 *
	 * @param array $block_attributes Block attributes.
	 * @return false|string
	 */
	function wd_gutenberg_woo_hook( $block_attributes ) {
		if ( empty( $block_attributes['hook'] ) ) {
			return '';
		}

		$shop_loop_hooks   = array(
			'woocommerce_before_shop_loop_item',
			'woocommerce_after_shop_loop_item',
			'woocommerce_before_shop_loop_item_title',
			'woocommerce_after_shop_loop_item_title',
			'woocommerce_shop_loop_item_title',
		);
		$is_shop_loop_hook = in_array( $block_attributes['hook'], $shop_loop_hooks, true );

		if ( $is_shop_loop_hook ) {
			if ( ! class_exists( 'XTS\Modules\Layouts\Loop_Item' ) ) {
				return '';
			}

			Loop_Item::setup_postdata();
		} else {
			Main::setup_preview();
		}

		if ( ! empty( $block_attributes['cleanActions'] ) ) {
			$order_attribution_class = OrderAttributionController::class;

			if ( 'woocommerce_checkout_billing' === $block_attributes['hook'] ) {
				remove_action( 'woocommerce_checkout_billing', array( WC()->checkout(), 'checkout_form_billing' ) );

				if ( woodmart_get_opt( 'shipping_progress_bar_enabled' ) ) {
					remove_action( 'woocommerce_checkout_billing', array( Shipping_Progress_Bar::get_instance(), 'render_shipping_progress_bar_with_wrapper' ) );
				}

				$controller = wc_get_container()->get( $order_attribution_class );
				remove_action( 'woocommerce_checkout_billing', array( $controller, 'stamp_html_element' ) );
			} elseif ( 'woocommerce_checkout_shipping' === $block_attributes['hook'] ) {
				remove_action( 'woocommerce_checkout_shipping', array( WC()->checkout(), 'checkout_form_shipping' ) );

				$controller = wc_get_container()->get( $order_attribution_class );
				remove_action( 'woocommerce_checkout_shipping', array( $controller, 'stamp_html_element' ) );
			} elseif ( 'woocommerce_checkout_before_customer_details' === $block_attributes['hook'] ) {
				remove_action( 'woocommerce_checkout_before_customer_details', 'wc_get_pay_buttons', 30 );
			} elseif ( 'woocommerce_before_checkout_form' === $block_attributes['hook'] ) {
				remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10 );
				remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
				remove_action( 'woocommerce_before_checkout_form', 'woocommerce_output_all_notices', 10 );
			} elseif ( 'woocommerce_checkout_after_customer_details' === $block_attributes['hook'] ) {
				$controller = wc_get_container()->get( $order_attribution_class );
				remove_action( 'woocommerce_checkout_after_customer_details', array( $controller, 'stamp_html_element' ) );
			} elseif ( 'woocommerce_cart_collaterals' === $block_attributes['hook'] ) {
				remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
				remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display', 20 );
				remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cart_totals', 10 );
			} elseif ( 'woocommerce_before_cart' === $block_attributes['hook'] ) {
				remove_action( 'woocommerce_before_cart', 'woocommerce_output_all_notices', 10 );
			} elseif ( 'woocommerce_before_single_product' === $block_attributes['hook'] ) {
				remove_action( 'woocommerce_before_single_product', 'woocommerce_output_all_notices' );
				remove_action( 'woocommerce_before_single_product', 'wc_print_notices' );
				remove_action( 'woocommerce_before_single_product', 'woodmart_product_extra_content', 20 );
			} elseif ( 'woocommerce_before_single_product_summary' === $block_attributes['hook'] ) {
				remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash' );
				remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
			} elseif ( 'woocommerce_product_thumbnails' === $block_attributes['hook'] ) {
				remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );
			} elseif ( 'woocommerce_single_product_summary' === $block_attributes['hook'] ) {
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
				remove_action( 'woocommerce_single_product_summary', 'woodmart_single_product_countdown', 15 );
				remove_action( 'woocommerce_single_product_summary', 'woodmart_stock_progress_bar', 16 );
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_output_product_data_tabs', 60 );
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating' );
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price' );
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
				remove_action( 'woocommerce_single_product_summary', 'woodmart_product_brand', 3 );
				remove_action( 'woocommerce_single_product_summary', 'woodmart_product_brand', 8 );
				remove_action( 'woocommerce_single_product_summary', 'woodmart_product_share_buttons', 62 );
				remove_action( 'woocommerce_single_product_summary', 'woodmart_display_product_attributes', 21 );
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_loop_add_to_cart', 30 );
				remove_action( 'woocommerce_single_product_summary', 'woodmart_sguide_display', 38 );
				remove_action( 'woocommerce_single_product_summary', 'woodmart_before_add_to_cart_area', 25 );
				remove_action( 'woocommerce_single_product_summary', 'woodmart_after_add_to_cart_area', 31 );
				remove_action( 'woocommerce_single_product_summary', array( $GLOBALS['woocommerce']->structured_data, 'generate_product_data' ), 60 );

				if ( woodmart_get_opt( 'linked_variations' ) ) {
					remove_action( 'woocommerce_single_product_summary', array( Linked_Variations::get_instance(), 'output' ), 25 );
				}
				if ( woodmart_get_opt( 'wishlist' ) ) {
					remove_action( 'woocommerce_single_product_summary', array( Wishlist::get_instance(), 'add_to_wishlist_single_btn' ), 33 );
				}
				if ( woodmart_get_opt( 'compare' ) ) {
					remove_action( 'woocommerce_single_product_summary', array( Compare::get_instance(), 'add_to_compare_single_btn' ), 33 );
				}
				if ( woodmart_get_opt( 'counter_visitor_enabled' ) ) {
					remove_action( 'woocommerce_single_product_summary', array( Visitor_Counter::get_instance(), 'output_count_visitors' ), 39 );
				}
				if ( woodmart_get_opt( 'sold_counter_enabled' ) ) {
					remove_action( 'woocommerce_single_product_summary', array( Sold_Counter::get_instance(), 'render' ), 25 );
				}
				if ( woodmart_get_opt( 'estimate_delivery_enabled' ) && woodmart_get_opt( 'estimate_delivery_show_on_single_product' ) ) {
					remove_action( 'woocommerce_single_product_summary', array( Estimate_Delivery_Frontend::get_instance(), 'render_on_single_product' ), 39 );
				}
				if ( woodmart_get_opt( 'discounts_enabled' ) && woodmart_get_opt( 'show_discounts_table' ) ) {
					remove_action( 'woocommerce_single_product_summary', array( Dynamic_Discounts_Frontend::get_instance(), 'render_dynamic_discounts_table' ), 25 );
				}
			} elseif ( 'woocommerce_before_add_to_cart_form' === $block_attributes['hook'] ) {
				remove_action( 'woocommerce_before_add_to_cart_form', 'woodmart_single_product_add_to_cart_scripts' );
			} elseif ( 'woocommerce_before_variations_form' === $block_attributes['hook'] ) {
				remove_action( 'woocommerce_before_variations_form', 'woocommerce_single_variation' );
			} elseif ( 'woocommerce_single_variation' === $block_attributes['hook'] ) {
				remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation' );
				remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
				remove_action( 'woocommerce_before_variations_form', 'woocommerce_single_variation' );
			} elseif ( 'woocommerce_after_single_product_summary' === $block_attributes['hook'] ) {
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs' );
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
				remove_action( 'woocommerce_after_single_product_summary', 'woodmart_wc_comments_template', 50 );
			} elseif ( 'woocommerce_checkout_order_review' === $block_attributes['hook'] ) {
				remove_action( 'woocommerce_checkout_order_review', 'woodmart_open_table_wrapper_div', 7 );
				remove_action( 'woocommerce_checkout_order_review', 'woodmart_close_table_wrapper_div', 13 );
				remove_action( 'woocommerce_checkout_order_review', 'woocommerce_order_review', 10 );
				remove_action( 'woocommerce_checkout_order_review', 'woocommerce_order_review', 20 );
				remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );
				remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 10 );
			} elseif ( 'woocommerce_order_details_after_order_table' === $block_attributes['hook'] ) {
				remove_action( 'woocommerce_order_details_after_order_table', 'woocommerce_order_again_button' );
			} elseif ( 'woocommerce_thankyou' === $block_attributes['hook'] ) {
				remove_action( 'woocommerce_thankyou', 'woocommerce_order_details_table' );
			} elseif ( 'woocommerce_before_customer_login_form' === $block_attributes['hook'] ) {
				remove_action( 'woocommerce_before_customer_login_form', 'woocommerce_output_all_notices' );
			} elseif ( 'woocommerce_register_form' === $block_attributes['hook'] ) {
				remove_action( 'woocommerce_register_form', 'wc_registration_privacy_policy_text', 20 );
			} elseif ( 'woocommerce_before_lost_password_form' === $block_attributes['hook'] ) {
				remove_action( 'woocommerce_before_lost_password_form', 'woocommerce_output_all_notices' );
			} elseif ( 'woocommerce_before_shop_loop_item' === $block_attributes['hook'] ) {
				if ( woodmart_get_opt( 'wishlist' ) ) {
					remove_action( 'woocommerce_before_shop_loop_item', array( Wishlist::get_instance(), 'output_settings_btn' ) );
				}
			} elseif ( 'woocommerce_before_shop_loop_item_title' === $block_attributes['hook'] ) {
				remove_action( 'woocommerce_before_shop_loop_item_title', 'woodmart_template_loop_product_thumbnails_gallery', 5 );
				remove_action( 'woocommerce_before_shop_loop_item_title', 'woodmart_template_loop_product_thumbnail', 10 );
				remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
			} elseif ( 'woocommerce_shop_loop_item_title' === $block_attributes['hook'] ) {
				remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title' );
			} elseif ( 'woocommerce_after_shop_loop_item_title' === $block_attributes['hook'] ) {
				remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
			}
		}

		ob_start();

		if ( has_action( $block_attributes['hook'] ) ) {
			if ( 'woocommerce_before_checkout_form' === $block_attributes['hook'] || 'woocommerce_after_checkout_form' === $block_attributes['hook'] ) {
				do_action( $block_attributes['hook'], WC()->checkout() ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
			} elseif ( in_array( $block_attributes['hook'], array( 'woocommerce_thankyou', 'woocommerce_before_thankyou', 'woocommerce_order_details_after_order_table' ), true ) ) {
				$order_id = (int) get_query_var( 'order-received' );
				$order    = $order_id ? wc_get_order( $order_id ) : '';
				if ( $order ) {
					if ( 'woocommerce_order_details_after_order_table' === $block_attributes['hook'] ) {
						do_action( $block_attributes['hook'], $order ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
					} else {
						do_action( $block_attributes['hook'], $order_id ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
					}
				}
			} else {
				do_action( $block_attributes['hook'] ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
			}
		}

		$hook_data = ob_get_clean();

		if ( ! empty( $block_attributes['cleanActions'] ) ) {
			if ( 'woocommerce_before_shop_loop_item' === $block_attributes['hook'] ) {
				$wishlist_page = function_exists( 'wpml_object_id_filter' ) ? wpml_object_id_filter( woodmart_get_opt( 'wishlist_page' ), 'page', true ) : woodmart_get_opt( 'wishlist_page' );

				if ( woodmart_get_opt( 'wishlist' ) && $wishlist_page && (int) get_the_ID() === (int) $wishlist_page ) {
					add_action( 'woocommerce_before_shop_loop_item', array( Wishlist::get_instance(), 'output_settings_btn' ) );
				}
			} elseif ( 'woocommerce_before_shop_loop_item_title' === $block_attributes['hook'] ) {
				add_action( 'woocommerce_before_shop_loop_item_title', 'woodmart_template_loop_product_thumbnails_gallery', 5 );
				add_action( 'woocommerce_before_shop_loop_item_title', 'woodmart_template_loop_product_thumbnail', 10 );
				add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
			} elseif ( 'woocommerce_shop_loop_item_title' === $block_attributes['hook'] ) {
				add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title' );
			} elseif ( 'woocommerce_after_shop_loop_item_title' === $block_attributes['hook'] ) {
				add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
			}
		}

		$classes = wd_get_gutenberg_element_classes( $block_attributes );

		if ( ! trim( $hook_data ) ) {
			if ( ! empty( $block_attributes['hideEmpty'] ) || wp_is_serving_rest_request() ) {
				if ( $is_shop_loop_hook ) {
					Loop_Item::reset_postdata();
				} else {
					Main::restore_preview();
				}

				return '';
			}

			$classes .= ' wd-hide';
		}

		$el_id = wd_get_gutenberg_element_id( $block_attributes );

		ob_start();
		?>
		<div <?php echo $el_id ? 'id="' . esc_attr( $el_id ) . '" ' : ''; ?>class="wd-el-hook<?php echo esc_attr( $classes ); ?>">
			<?php echo $hook_data; // phpcs:ignore ?>
		</div>
		<?php

		if ( $is_shop_loop_hook ) {
			Loop_Item::reset_postdata();
		} else {
			Main::restore_preview();
		}

		return ob_get_clean();
	}
}
