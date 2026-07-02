<?php
/**
 * Loop Product Stock Status block render.
 *
 * @package woodmart
 */

use XTS\Modules\Layouts\Loop_Item;

if ( ! function_exists( 'wd_gutenberg_loop_builder_product_stock_status' ) ) {
	/**
	 * Render Loop Product Stock Status block.
	 *
	 * @param array $block_attributes Block attributes.
	 * @return false|string
	 */
	function wd_gutenberg_loop_builder_product_stock_status( $block_attributes ) {
		if ( ! woodmart_woocommerce_installed() ) {
			return '';
		}

		Loop_Item::setup_postdata();

		global $product;

		if ( ! $product ) {
			Loop_Item::reset_postdata();
			return '';
		}

		$classes = wd_get_gutenberg_element_classes( $block_attributes );

		if ( ! empty( $block_attributes['textAlign'] ) || ! empty( $block_attributes['textAlignTablet'] ) || ! empty( $block_attributes['textAlignMobile'] ) ) {
			$classes .= ' wd-align';
		}

		ob_start();

		?>
		<div class="wd-loop-prod-stock-status<?php echo esc_attr( $classes ); ?>">
			<?php woodmart_stock_status_after_title( true ); ?>
		</div>
		<?php
		Loop_Item::reset_postdata();

		return ob_get_clean();
	}
}
