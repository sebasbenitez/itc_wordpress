<?php
/**
 * Loop Product Wishlist block render.
 *
 * @package woodmart
 */

use XTS\Modules\Layouts\Loop_Item;
use XTS\WC_Wishlist\Ui as Wishlist;

if ( ! function_exists( 'wd_gutenberg_loop_builder_product_wishlist' ) ) {
	/**
	 * Render Loop Product Wishlist block.
	 *
	 * @param array $block_attributes Block attributes.
	 * @return false|string
	 */
	function wd_gutenberg_loop_builder_product_wishlist( $block_attributes ) {
		if ( ! woodmart_woocommerce_installed() || ! class_exists( 'XTS\WC_Wishlist\Ui' ) || ! woodmart_get_opt( 'product_loop_wishlist' ) || ( woodmart_get_opt( 'wishlist_logged' ) && ! is_user_logged_in() ) ) {
			return '';
		}

		$link_classes     = '';
		$wrapper_classes  = 'wd-loop-prod-btn wd-wishlist-icon';
		$wrapper_classes .= wd_get_gutenberg_element_classes( $block_attributes );

		if ( 'button' === $block_attributes['style'] ) {
			$wrapper_classes .= ' wd-add-btn-replace';
			$link_classes    .= ' button';

			if ( ! empty( $block_attributes['stretch'] ) ) {
				$wrapper_classes .= ' wd-stretched';
			}
		} else {
			$wrapper_classes .= ' wd-action-btn';

			if ( 'icon' === $block_attributes['style'] ) {
				$wrapper_classes .= ' wd-style-icon';
				$link_classes    .= ' wd-tooltip';
				$link_classes    .= ' wd-tooltip-' . esc_attr( $block_attributes['tooltip_position'] );
			} elseif ( 'icon_with_text' === $block_attributes['style'] ) {
				$wrapper_classes .= ' wd-style-text';
			}
		}

		ob_start();

		if ( 'icon' === $block_attributes['style'] ) {
			woodmart_enqueue_js_library( 'tooltips' );
			woodmart_enqueue_js_script( 'btns-tooltips' );
		}

		Loop_Item::setup_postdata();

		Wishlist::get_instance()->add_to_wishlist_btn( $wrapper_classes, $link_classes );

		Loop_Item::reset_postdata();

		return ob_get_clean();
	}
}
