<?php
/**
 * Loop Product Label block render.
 *
 * @package woodmart
 */

use XTS\Modules\Layouts\Loop_Item;
use XTS\Modules\Custom_Labels\Frontend as Labels_Frontend;

if ( ! function_exists( 'wd_gutenberg_loop_builder_product_label' ) ) {
	/**
	 * Render Loop Product Label block.
	 *
	 * @param array $block_attributes Block attributes.
	 * @return false|string
	 */
	function wd_gutenberg_loop_builder_product_label( $block_attributes ) {
		if ( ! woodmart_woocommerce_installed() ) {
			return '';
		}

		Loop_Item::setup_postdata();

		$content       = '';
		$shape         = woodmart_get_opt( 'label_shape' );
		$label_classes = ' product-label';

		if ( 'rounded-sm' === $shape ) {
			$label_classes .= ' wd-shape-round-sm';
		} elseif ( 'rectangular' === $shape ) {
			$label_classes .= ' wd-shape-rect-sm';
		} elseif ( 'rounded' === $shape ) {
			$label_classes .= ' wd-shape-round';
		}

		$content .= Labels_Frontend::get_instance()->get_default_label_html( $block_attributes['type'], $label_classes );

		if ( woodmart_get_opt( 'custom_labels' ) && ! in_array( $block_attributes['type'], array( 'sale', 'out-of-stock', 'hot', 'new' ), true ) ) {
			$label_id = apply_filters( 'wpml_object_id', $block_attributes['type'], 'wd_custom_label', true );
			$label    = get_post( $label_id );

			if ( ! $label || ! $label_id || 'publish' !== $label->post_status ) {
				return '';
			}

			$ids_to_show = Labels_Frontend::get_instance()->manager->get_current_custom_labels_ids();
			if ( ! in_array( (int) $label_id, $ids_to_show, true ) ) {
				return '';
			}

			$label_classes = Labels_Frontend::get_instance()->get_classes( $label_id );

			$content  = '<div class="' . esc_attr( $label_classes ) . '">';
			$content .= Labels_Frontend::get_instance()->get_content( $label_id );
			$content .= '</div>';
		}

		if ( ! $content ) {
			Loop_Item::reset_postdata();

			return '';
		}

		ob_start();

		woodmart_enqueue_inline_style( 'woo-mod-product-labels' );

		if ( 'rounded' === $shape ) {
			woodmart_enqueue_inline_style( 'woo-mod-product-labels-round' );
		}

		$classes = wd_get_gutenberg_element_classes( $block_attributes );

		?>
		<div class="wd-loop-prod-label<?php echo esc_attr( $classes ); ?>">
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<?php

		Loop_Item::reset_postdata();

		return ob_get_clean();
	}
}
