<?php
/**
 * Loop Product Description block rendering.
 *
 * @package woodmart
 */

use XTS\Modules\Layouts\Loop_Item;

if ( ! function_exists( 'wd_gutenberg_loop_builder_product_description' ) ) {
	/**
	 * Render Loop Product Description block.
	 *
	 * @param array $block_attributes Block attributes.
	 * @return false|string
	 */
	function wd_gutenberg_loop_builder_product_description( $block_attributes ) {
		if ( ! woodmart_woocommerce_installed() ) {
			return '';
		}

		$classes = wd_get_gutenberg_element_classes( $block_attributes );

		if ( ! empty( $block_attributes['textAlign'] ) || ! empty( $block_attributes['textAlignTablet'] ) || ! empty( $block_attributes['textAlignMobile'] ) ) {
			$classes .= ' wd-align';
		}

		Loop_Item::setup_postdata();

		$content = get_the_excerpt();

		if ( ! $content ) {
			Loop_Item::reset_postdata();
			return '';
		}

		ob_start();
		?>
		<div class="wd-loop-prod-short-desc<?php echo esc_attr( $classes ); ?>">
			<?php echo do_shortcode( wpautop( $content ) ); ?>
		</div>
		<?php
		Loop_Item::reset_postdata();

		return ob_get_clean();
	}
}
