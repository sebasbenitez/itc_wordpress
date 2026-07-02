<?php
/**
 * Loop Product Rating block render.
 *
 * @package woodmart
 */

use XTS\Modules\Layouts\Loop_Item;

if ( ! function_exists( 'wd_gutenberg_loop_builder_product_rating' ) ) {
	/**
	 * Render Loop Product Rating block.
	 *
	 * @param array $block_attributes Block attributes.
	 * @return string
	 */
	function wd_gutenberg_loop_builder_product_rating( $block_attributes ) {
		if ( ! woodmart_woocommerce_installed() || ! wc_review_ratings_enabled() ) {
			return '';
		}

		Loop_Item::setup_postdata();

		global $product;

		if ( ! $product ) {
			Loop_Item::reset_postdata();
			return '';
		}

		if ( 'variation' === $product->get_type() ) {
			$rating = wc_get_product( $product->get_parent_id() )->get_average_rating();
		} else {
			$rating = $product->get_average_rating();
		}

		if ( 0 >= $rating && empty( $block_attributes['show_empty_rating'] ) ) {
			Loop_Item::reset_postdata();
			return '';
		}

		ob_start();

		if ( 'compact' === $block_attributes['design'] ) {
			woodmart_enqueue_inline_style( 'mod-star-rating-style-simple' );
		}

		$classes = wd_get_gutenberg_element_classes( $block_attributes );

		if ( ! empty( $block_attributes['textAlign'] ) || ! empty( $block_attributes['textAlignTablet'] ) || ! empty( $block_attributes['textAlignMobile'] ) ) {
			$classes .= ' wd-align';
		}

		?>
		<div class="wd-loop-prod-rating<?php echo esc_attr( $classes ); ?>">
			<?php if ( ! empty( $block_attributes['show_count'] ) ) : ?>
				<div class="wd-star-rating">
			<?php endif; ?>

			<?php /* translators: %s: average rating */ ?>
			<div class="star-rating<?php echo 'compact' === $block_attributes['design'] ? ' wd-style-simple' : ''; ?>" role="img" aria-label="<?php echo esc_attr( sprintf( __( 'Rated %s out of 5', 'woocommerce' ), $rating ) ); ?>">
				<?php if ( 'compact' === $block_attributes['design'] ) : ?>
					<?php echo wp_kses( woodmart_get_simple_star_rating_html( $rating ), true ); ?>
				<?php else : ?>
					<?php echo wp_kses( woodmart_get_star_rating_html( $rating ), true ); ?>
				<?php endif; ?>
			</div>

			<?php if ( ! empty( $block_attributes['show_count'] ) ) : ?>
					<a href="<?php echo esc_url( get_permalink( $product->get_id() ) . '#reviews' ); ?>" class="woocommerce-review-link" rel="nofollow">
						(<?php echo esc_html( $product->get_review_count() ); ?>)
					</a>
				</div>
			<?php endif; ?>
		</div>
		<?php

		Loop_Item::reset_postdata();

		return apply_filters( 'woocommerce_product_get_rating_html', ob_get_clean(), $rating, 0 );
	}
}
