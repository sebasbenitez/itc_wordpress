<?php
/**
 * Product content template for buttons on hover style.
 */

global $product;

do_action( 'woocommerce_before_shop_loop_item' );

woodmart_enqueue_js_script( 'btns-tooltip' );
?>

<div class="wd-product-wrapper product-wrapper">
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
			<?php do_action( 'woodmart_product_action_buttons' ); ?>
		</div>
	</div>

	<div class="product-element-bottom">

		<div class="wd-product-header">
			<?php
			/**
			 * Trigger woocommerce_shop_loop_item_title hook.
			 *
			 * @hooked woocommerce_template_loop_product_title - 10
			 */
			do_action( 'woocommerce_shop_loop_item_title' );
			?>

			<?php if ( 0 < $product->get_average_rating() || woodmart_get_opt( 'show_empty_star_rating' ) ) : ?>
				<?php echo wp_kses_post( woodmart_get_product_rating( 'simple' ) ); ?>
			<?php endif; ?>
		</div>

		<?php
		woodmart_product_categories();
		woodmart_product_brands_links();
		?>

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

		<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>

		<?php
		woodmart_product_sku();
		?>

		<?php if ( woodmart_loop_prop( 'progress_bar' ) ) : ?>
			<?php woodmart_stock_progress_bar(); ?>
		<?php endif ?>

		<?php if ( woodmart_loop_prop( 'timer' ) ) : ?>
			<?php woodmart_product_sale_countdown(); ?>
		<?php endif ?>

		<div class="wd-product-footer">
			<div class="wd-add-btn wd-add-btn-replace">
				<?php do_action( 'woodmart_add_loop_btn' ); ?>
			</div>

			<?php if ( ( woodmart_get_opt( 'compare' ) && woodmart_get_opt( 'compare_on_grid' ) ) || woodmart_get_opt( 'quick_view' ) ) : ?>
			<div class="wd-action-buttons">
				<?php woodmart_add_to_compare_loop_btn(); ?>
				<?php woodmart_quick_view_btn( get_the_ID(), ' wd-action-btn wd-style-icon wd-tooltip' ); ?>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>
