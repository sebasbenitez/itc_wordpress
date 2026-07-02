<?php
/**
 * Loop Product Labels block render.
 *
 * @package woodmart
 */

use XTS\Modules\Layouts\Loop_Item;
use XTS\Modules\Custom_Labels\Frontend as Labels_Frontend;

if ( ! function_exists( 'wd_gutenberg_loop_builder_product_labels' ) ) {
	/**
	 * Render Loop Product Labels block.
	 *
	 * @param array $block_attributes Block attributes.
	 * @return string
	 */
	function wd_gutenberg_loop_builder_product_labels( $block_attributes ) {
		if ( ! woodmart_woocommerce_installed() ) {
			return '';
		}

		$classes = wd_get_gutenberg_element_classes( $block_attributes );

		Loop_Item::setup_postdata();

		$output = array();

		$shape         = woodmart_get_opt( 'label_shape', 'rounded' );
		$label_classes = ' product-label';

		if ( 'rounded-sm' === $shape ) {
			$label_classes .= ' wd-shape-round-sm';
		} elseif ( 'rectangular' === $shape ) {
			$label_classes .= ' wd-shape-rect-sm';
		} elseif ( 'rounded' === $shape ) {
			$label_classes .= ' wd-shape-round';
		}

		$default_labels = Labels_Frontend::get_instance()->get_default_labels_output( $label_classes );
		$output         = array_merge( $output, $default_labels );

		$output = apply_filters(
			'woodmart_product_label_output',
			$output,
			array(
				'source'  => isset( $block_attributes['source'] ) ? $block_attributes['source'] : 'all',
				'include' => ! empty( $block_attributes['include'] ) ? explode( ',', $block_attributes['include'] ) : array(),
				'exclude' => ! empty( $block_attributes['exclude'] ) ? explode( ',', $block_attributes['exclude'] ) : array(),
			)
		);

		if ( ! $output ) {
			Loop_Item::reset_postdata();
			return '';
		}

		ob_start();

		woodmart_enqueue_inline_style( 'woo-mod-product-labels' );
		woodmart_enqueue_inline_style( 'woo-mod-product-labels-builder' );

		if ( 'rounded' === $shape ) {
			woodmart_enqueue_inline_style( 'woo-mod-product-labels-round' );
		}

		?>
		<div class="wd-loop-prod-labels product-labels<?php echo esc_attr( $classes ); ?>">
			<?php echo implode( ' ', $output ); // phpcs:ignore ?>
		</div>
		<?php

		Loop_Item::reset_postdata();

		return ob_get_clean();
	}
}
