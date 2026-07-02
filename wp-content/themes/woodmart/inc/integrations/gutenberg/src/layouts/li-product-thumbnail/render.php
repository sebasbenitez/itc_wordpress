<?php
/**
 * Loop Builder Product Thumbnail block rendering.
 *
 * @package woodmart
 */

use XTS\Modules\Layouts\Loop_Item;

if ( ! function_exists( 'wd_gutenberg_loop_builder_product_thumbnail' ) ) {
	/**
	 * Render product thumbnail block for Loop Builder layout.
	 *
	 * @param array  $block_attributes Block attributes.
	 * @param string $block_content Inner block content.
	 * @return string
	 */
	function wd_gutenberg_loop_builder_product_thumbnail( $block_attributes, $block_content ) {
		if ( ! woodmart_woocommerce_installed() ) {
			return '';
		}

		ob_start();

		Loop_Item::setup_postdata();

		$classes = wd_get_gutenberg_element_classes( $block_attributes );

		if ( ! empty( $block_attributes['image_on_hover'] ) && empty( $block_attributes['product_gallery'] ) ) {
			$classes .= ' wd-hover-' . esc_attr( $block_attributes['hover_image_effect'] );
		}

		woodmart_set_loop_prop( 'grid_gallery', ! empty( $block_attributes['product_gallery'] ) ? 'yes' : 'no' );

		if ( ! empty( $block_attributes['product_gallery'] ) ) {
			woodmart_set_loop_prop( 'grid_gallery_control', $block_attributes['grid_gallery_control'] );
			woodmart_set_loop_prop( 'grid_gallery_enable_arrows', $block_attributes['grid_gallery_enable_arrows'] );
		}

		?>
		<div class="wd-loop-prod-thumb wd-product-thumb<?php echo esc_attr( $classes ); ?>">
			<a href="<?php echo esc_url( get_permalink() ); ?>" class="wd-product-img-link" tabindex="-1" aria-label="<?php echo esc_attr( get_the_title() ); ?>">
				<?php
					woodmart_template_loop_product_thumbnails_gallery();
					woodmart_template_loop_product_thumbnail();
				?>
			</a>

			<?php
			if ( ! empty( $block_attributes['image_on_hover'] ) && empty( $block_attributes['product_gallery'] ) ) {
				woodmart_hover_image( true );
			}
			?>

			<?php echo $block_content; // phpcs:ignore ?>
		</div>
		<?php

		Loop_Item::reset_postdata();

		return ob_get_clean();
	}
}
