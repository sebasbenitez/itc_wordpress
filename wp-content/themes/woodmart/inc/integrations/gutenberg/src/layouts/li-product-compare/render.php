<?php
/**
 * Loop Product Compare block rendering.
 *
 * @package woodmart
 */

use XTS\Modules\Compare\Ui as Compare;
use XTS\Modules\Layouts\Loop_Item;

if ( ! function_exists( 'wd_gutenberg_loop_builder_product_compare' ) ) {
	/**
	 * Render Product Compare button in Loop Builder.
	 *
	 * @param array $block_attributes Block attributes.
	 * @return false|string
	 */
	function wd_gutenberg_loop_builder_product_compare( $block_attributes ) {
		if ( ! woodmart_woocommerce_installed() || ! class_exists( 'XTS\Modules\Compare\Ui' ) || ! woodmart_get_opt( 'compare' ) || ! woodmart_get_opt( 'compare_on_grid' ) ) {
			return '';
		}

		$link_classes     = '';
		$wrapper_classes  = 'wd-loop-prod-btn wd-compare-icon';
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

		Compare::get_instance()->add_to_compare_btn( $wrapper_classes, $link_classes );

		Loop_Item::reset_postdata();

		return ob_get_clean();
	}
}
