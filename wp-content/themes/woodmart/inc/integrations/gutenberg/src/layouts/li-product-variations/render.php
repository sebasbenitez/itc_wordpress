<?php
/**
 * Loop Product Variations block render.
 *
 * @package woodmart
 */

use XTS\Modules\Layouts\Loop_Item;

if ( ! function_exists( 'wd_gutenberg_loop_builder_product_variations' ) ) {
	/**
	 * Render Loop Product Variations block.
	 *
	 * @param array $block_attributes Block attributes.
	 * @return false|string
	 */
	function wd_gutenberg_loop_builder_product_variations( $block_attributes ) {
		if ( ! woodmart_woocommerce_installed() ) {
			return '';
		}

		Loop_Item::setup_postdata();

		global $product;

		if ( ! $product || ! $product->is_type( 'variable' ) ) {
			Loop_Item::reset_postdata();

			return '';
		}

		$classes              = wd_get_gutenberg_element_classes( $block_attributes );
		$cache                = apply_filters( 'woodmart_swatches_cache', true );
		$transient_name       = 'woodmart_swatches_cache_' . $product->get_id();
		$available_variations = array();

		if ( $cache ) {
			$available_variations = get_transient( $transient_name );
		}

		if ( ! $available_variations ) {
			$available_variations = $product->get_available_variations();

			if ( $cache ) {
				set_transient( $transient_name, $available_variations, apply_filters( 'woodmart_swatches_cache_time', WEEK_IN_SECONDS ) );
			}
		}

		if ( empty( $available_variations ) ) {
			Loop_Item::reset_postdata();

			return '';
		}

		if ( ! empty( $block_attributes['align'] ) || ! empty( $block_attributes['alignTablet'] ) || ! empty( $block_attributes['alignMobile'] ) ) {
			$classes .= ' wd-align';
		}

		ob_start();

		?>
		<div class="wd-loop-prod-variations<?php echo esc_attr( $classes ); ?>">
			<?php echo woodmart_swatches_form_grid_template( $available_variations ); // phpcs:ignore ?>
		</div>
		<?php
		Loop_Item::reset_postdata();

		return ob_get_clean();
	}
}
