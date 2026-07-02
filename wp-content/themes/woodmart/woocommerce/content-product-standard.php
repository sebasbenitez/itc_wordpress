<?php
/**
 * The template for displaying product content in the standard loop
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product-standard.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

global $product;

do_action( 'woocommerce_before_shop_loop_item' );
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

		if ( 'no' === woodmart_loop_prop( 'grid_gallery' ) || ! woodmart_loop_prop( 'grid_gallery' ) ) {
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

	<?php if ( woodmart_loop_prop( 'stretch_product_desktop' ) || woodmart_loop_prop( 'stretch_product_tablet' ) || woodmart_loop_prop( 'stretch_product_mobile' ) ) : ?>
	<div class="product-element-bottom">
	<?php endif; ?>

	<?php
		echo woodmart_swatches_list(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	?>

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
		woodmart_product_sku();
	?>
	<?php if ( 0 < $product->get_average_rating() || woodmart_get_opt( 'show_empty_star_rating' ) ) : ?>
		<?php echo wp_kses_post( woodmart_get_product_rating() ); ?>
	<?php endif; ?>
	<?php
		woodmart_stock_status_after_title();
	?>

	<?php
		/**
		 * Trigger woocommerce_after_shop_loop_item_title hook.
		 *
		 * @hooked woocommerce_template_loop_rating - 5
		 * @hooked woocommerce_template_loop_price - 10
		 */
		do_action( 'woocommerce_after_shop_loop_item_title' );
	?>

	<div class="wd-add-btn wd-add-btn-replace">
		<?php if ( woodmart_loop_prop( 'product_quantity' ) ) : ?>
			<?php woodmart_product_quantity( $product ); ?>
		<?php endif ?>

		<?php do_action( 'woodmart_add_loop_btn' ); ?>
	</div>

	<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>

	<?php if ( woodmart_loop_prop( 'progress_bar' ) ) : ?>
		<?php woodmart_stock_progress_bar(); ?>
	<?php endif ?>

	<?php if ( woodmart_loop_prop( 'timer' ) ) : ?>
		<?php woodmart_product_sale_countdown( array( 'products_hover' => 'standard' ) ); ?>
	<?php endif ?>
	<?php if ( woodmart_loop_prop( 'stretch_product_desktop' ) || woodmart_loop_prop( 'stretch_product_tablet' ) || woodmart_loop_prop( 'stretch_product_mobile' ) ) : ?>
	</div>
	<?php endif; ?>
</div>
