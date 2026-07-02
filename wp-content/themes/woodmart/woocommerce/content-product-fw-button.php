<?php
/**
 * The template for displaying product content within fw button loop
 */

global $product;

do_action( 'woocommerce_before_shop_loop_item' );
?>

<div class="wd-product-wrapper product-wrapper">
	<div class="wd-product-card-bg content-product-imagin"></div>
	<div class="wd-product-thumb product-element-top wd-quick-shop">
		<a href="<?php echo esc_url( get_permalink() ); ?>" class="wd-product-img-link product-image-link" tabindex="-1" aria-label="<?php echo esc_attr( get_the_title() ); ?>">
			<?php
			/**
			 * Hook woocommerce_before_shop_loop_item_title.
			 *
			 * @hooked woodmart_template_loop_product_thumbnails_gallery - 5
			 * @hooked woocommerce_show_product_loop_sale_flash - 10
			 * @hooked woodmart_template_loop_product_thumbnail - 10
			 */
			do_action( 'woocommerce_before_shop_loop_item_title' );
			?>
		</a>

		<?php
		do_action( 'woodmart_shop_loop_product_thumbnail' );

		if ( ! woodmart_loop_prop( 'grid_gallery' ) || ( ! woodmart_get_opt( 'grid_gallery' ) && empty( woodmart_loop_prop( 'grid_gallery_control', 'hover' ) ) && empty( woodmart_loop_prop( 'grid_gallery_enable_arrows', 'none' ) ) ) ) {
			woodmart_hover_image();
		}
		?>

		<div class="wd-buttons wd-pos-r-t">
			<?php woodmart_enqueue_js_script( 'btns-tooltip' ); ?>
			<?php woodmart_add_to_compare_loop_btn(); ?>
			<?php woodmart_quick_view_btn( get_the_ID() ); ?>
			<?php do_action( 'woodmart_product_action_buttons' ); ?>
		</div>
	</div>

	<div class="product-element-bottom">

		<?php
		/**
		 * Trigger woocommerce_shop_loop_item_title hook.
		 *
		 * @hooked woocommerce_template_loop_product_title - 10
		 */
		do_action( 'woocommerce_shop_loop_item_title' );
		?>

		<?php
		woodmart_product_categories();
		woodmart_product_brands_links();
		?>
		<?php if ( 0 < $product->get_average_rating() || woodmart_get_opt( 'show_empty_star_rating' ) ) : ?>
			<?php echo wp_kses_post( woodmart_get_product_rating() ); ?>
		<?php endif; ?>

		<?php woodmart_stock_status_after_title(); ?>

		<div class="wrap-price">
			<?php
			/**
			 * Trigger woocommerce_after_shop_loop_item_title hook.
			 *
			 * @hooked woocommerce_template_loop_rating - 5
			 * @hooked woocommerce_template_loop_price - 10
			 */
			do_action( 'woocommerce_after_shop_loop_item_title' );
			?>

			<?php echo woodmart_swatches_list(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>

		<div class="wd-add-btn wd-add-btn-replace">
			<?php if ( woodmart_loop_prop( 'product_quantity' ) ) : ?>
				<?php woodmart_product_quantity( $product ); ?>
			<?php endif ?>

			<?php do_action( 'woodmart_add_loop_btn' ); ?>
		</div>

		<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>

		<?php
		woodmart_product_sku();
		?>
		<div class="wd-product-card-hover fade-in-block wd-scroll">
			<?php if ( 'none' !== woodmart_get_opt( 'base_hover_content' ) ) : ?>
				<div class="hover-content-wrap">
					<div class="hover-content wd-more-desc">
						<div class="hover-content-inner wd-more-desc-inner">
							<?php
							if ( 'excerpt' === woodmart_get_opt( 'base_hover_content' ) ) {
								echo do_shortcode( wpautop( get_the_excerpt() ) );
							} elseif ( 'additional_info' === woodmart_get_opt( 'base_hover_content' ) ) {
								wc_display_product_attributes( $product );
							}
							?>
						</div>
						<a href="#" rel="nofollow" class="wd-more-desc-btn" aria-label="<?php esc_attr_e( 'Read more description', 'woodmart' ); ?>"></a>
					</div>
				</div>
			<?php endif; ?>
			<?php if ( woodmart_loop_prop( 'progress_bar' ) ) : ?>
				<?php woodmart_stock_progress_bar(); ?>
			<?php endif ?>

			<?php if ( woodmart_loop_prop( 'timer' ) ) : ?>
				<?php woodmart_product_sale_countdown(); ?>
			<?php endif ?>
		</div>
	</div>
</div>
