<?php
/**
 * Loop Product Title block render.
 *
 * @package woodmart
 */

use XTS\Modules\Layouts\Loop_Item;

if ( ! function_exists( 'wd_gutenberg_loop_builder_product_title' ) ) {
	/**
	 * Render Loop Product Title block.
	 *
	 * @param array $block_attributes Block attributes.
	 * @return false|string
	 */
	function wd_gutenberg_loop_builder_product_title( $block_attributes ) {
		if ( ! woodmart_woocommerce_installed() ) {
			return '';
		}

		$classes = wd_get_gutenberg_element_classes( $block_attributes );

		if ( ! empty( $block_attributes['textAlign'] ) || ! empty( $block_attributes['textAlignTablet'] ) || ! empty( $block_attributes['textAlignMobile'] ) ) {
			$classes .= ' wd-align';
		}

		if ( ! empty( $block_attributes['linesLimit'] ) ) {
			$classes .= ' wd-line-clamp';
		}

		ob_start();

		Loop_Item::setup_postdata();

		?>
		<<?php echo esc_attr( $block_attributes['htmlTag'] ); ?> class="wd-loop-prod-title wd-entities-title<?php echo esc_attr( $classes ); ?>">
			<a href="<?php the_permalink(); ?>">
				<?php the_title(); ?>
			</a>
		</<?php echo esc_attr( $block_attributes['htmlTag'] ); ?> >

		<?php

		Loop_Item::reset_postdata();

		return ob_get_clean();
	}
}
