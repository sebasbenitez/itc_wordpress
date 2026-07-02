<?php
/**
 * Loop Product price block render.
 *
 * @package woodmart
 */

use XTS\Modules\Layouts\Loop_Item;

if ( ! function_exists( 'wd_gutenberg_loop_builder_product_price' ) ) {
	/**
	 * Render Loop Product Price block.
	 *
	 * @param array $block_attributes Block attributes.
	 * @return string
	 */
	function wd_gutenberg_loop_builder_product_price( $block_attributes ) {
		if ( ! woodmart_woocommerce_installed() ) {
			return '';
		}

		$classes = wd_get_gutenberg_element_classes( $block_attributes );

		if ( ! empty( $block_attributes['align'] ) || ! empty( $block_attributes['alignTablet'] ) || ! empty( $block_attributes['alignMobile'] ) ) {
			$classes .= ' wd-align';
		}

		Loop_Item::setup_postdata();

		ob_start();

		woocommerce_template_loop_price();

		$content = ob_get_clean();

		if ( ! trim( $content ) ) {
			Loop_Item::reset_postdata();
			return '';
		}

		ob_start();

		?>
		<div class="wd-loop-prod-price<?php echo esc_attr( $classes ); ?>">
			<?php echo $content; //phpcs:ignore ?>
		</div>
		<?php

		Loop_Item::reset_postdata();

		return ob_get_clean();
	}
}
