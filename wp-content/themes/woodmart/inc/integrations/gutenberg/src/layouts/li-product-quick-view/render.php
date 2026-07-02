<?php
/**
 * Loop Product price block assets.
 *
 * @package woodmart
 */

use XTS\Modules\Layouts\Loop_Item;

if ( ! function_exists( 'wd_gutenberg_loop_builder_product_quick_view' ) ) {
	/**
	 * Render Loop Product Quick View block.
	 *
	 * @param array $block_attributes Block attributes.
	 * @return false|string
	 */
	function wd_gutenberg_loop_builder_product_quick_view( $block_attributes ) {
		if ( ! woodmart_woocommerce_installed() ) {
			return '';
		}

		$link_classes     = '';
		$wrapper_classes  = ' wd-loop-prod-btn';
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

		Loop_Item::setup_postdata();
		woodmart_quick_view_btn( get_the_ID(), $wrapper_classes, $link_classes );
		Loop_Item::reset_postdata();

		return ob_get_clean();
	}
}
