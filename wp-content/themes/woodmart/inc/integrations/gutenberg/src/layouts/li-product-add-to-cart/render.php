<?php
/**
 * Loop Product Add to Cart Block Render.
 *
 * @package woodmart
 */

use XTS\Modules\Layouts\Loop_Item;

if ( ! function_exists( 'wd_gutenberg_loop_builder_product_add_to_cart' ) ) {
	/**
	 * Render Loop Product Add to Cart Block.
	 *
	 * @param array $block_attributes Block attributes.
	 * @return string
	 */
	function wd_gutenberg_loop_builder_product_add_to_cart( $block_attributes ) {
		if ( ! woodmart_woocommerce_installed() ) {
			return '';
		}

		$classes         = ' wd-loop-prod-btn';
		$wrapper_classes = wd_get_gutenberg_element_classes( $block_attributes );

		if ( 'button' === $block_attributes['style'] ) {
			$classes .= ' wd-add-btn-replace';

			if ( ! empty( $block_attributes['stretch'] ) ) {
				$classes .= ' wd-stretched';
			}
		} else {
			$classes .= ' wd-action-btn';

			if ( 'icon' === $block_attributes['style'] ) {
				$classes .= ' wd-style-icon';

				if ( empty( $block_attributes['show_quantity'] ) ) {
					$classes .= ' wd-tooltip';

					$classes .= ' wd-tooltip-' . esc_attr( $block_attributes['tooltip_position'] );
				}
			} elseif ( 'icon_with_text' === $block_attributes['style'] ) {
				$classes .= ' wd-style-text';
			}
		}

		if ( ! empty( $block_attributes['show_quantity'] ) && ! empty( $block_attributes['quantity_overlap'] ) && 'button' === $block_attributes['style'] ) {
			$wrapper_classes .= ' wd-quantity-overlap';
		}

		Loop_Item::setup_postdata();

		global $product;

		if ( ! has_action( 'woodmart_add_loop_btn' ) ) {
			Loop_Item::reset_postdata();
			return '';
		}

		ob_start();

		?>
		<div class="wd-add-btn-wrapp<?php echo esc_attr( $wrapper_classes ); ?>">
			<div class="wd-add-btn<?php echo esc_attr( $classes ); ?>">
				<?php if ( ! empty( $block_attributes['show_quantity'] ) ) : ?>
					<?php woodmart_product_quantity( $product, true ); ?>
				<?php endif; ?>

				<?php do_action( 'woodmart_add_loop_btn' ); ?>
			</div>
		</div>

		<?php
		Loop_Item::reset_postdata();

		return ob_get_clean();
	}
}
