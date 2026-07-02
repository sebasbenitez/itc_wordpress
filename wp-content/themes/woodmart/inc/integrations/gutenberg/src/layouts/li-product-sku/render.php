<?php
/**
 * Loop Product SKU block render.
 *
 * @package woodmart
 */

use XTS\Modules\Layouts\Loop_Item;

if ( ! function_exists( 'wd_gutenberg_loop_builder_product_sku' ) ) {
	/**
	 * Render Loop Product SKU block.
	 *
	 * @param array  $block_attributes Block attributes.
	 * @param string $content Inner block content.
	 * @return false|string
	 */
	function wd_gutenberg_loop_builder_product_sku( $block_attributes, $content ) {
		if ( ! woodmart_woocommerce_installed() ) {
			return '';
		}

		Loop_Item::setup_postdata();

		global $product;

		if ( ! $product || ! $product->get_sku() ) {
			Loop_Item::reset_postdata();
			return '';
		}

		$classes = wd_get_gutenberg_element_classes( $block_attributes );

		if ( ! empty( $block_attributes['showTitle'] ) ) {
			$classes .= ' wd-layout-' . $block_attributes['layout'];
		}

		if ( ( empty( $block_attributes['showTitle'] ) || 'justify' !== $block_attributes['layout'] ) && ( ! empty( $block_attributes['textAlign'] ) || ! empty( $block_attributes['textAlignTablet'] ) || ! empty( $block_attributes['textAlignMobile'] ) ) ) {
			$classes .= ' wd-align';
		}

		ob_start();

		?>
		<div class="wd-product-sku wd-loop-prod-meta<?php echo esc_attr( $classes ); ?>">
			<?php echo wp_kses( $content, true ); ?>
			<span class="wd-sku">
				<?php echo esc_html( $product->get_sku() ); ?>
			</span>
		</div>
		<?php

		Loop_Item::reset_postdata();

		return ob_get_clean();
	}
}
