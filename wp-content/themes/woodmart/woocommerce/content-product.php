<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 9.4.0
 */

use XTS\Modules\Layouts\Main;
use XTS\Modules\Custom_Labels\Frontend as Labels_Frontend;

defined( 'ABSPATH' ) || exit;

global $product;

$is_slider       = woodmart_loop_prop( 'is_slider' );
$is_shortcode    = woodmart_loop_prop( 'is_shortcode' );
$different_sizes = woodmart_loop_prop( 'products_different_sizes' );
$hover           = woodmart_loop_prop( 'product_hover' );
$hover_type      = woodmart_loop_prop( 'product_hover_type', 'predefined' );
$current_view    = woodmart_loop_prop( 'products_view' );
$shop_view       = woodmart_get_opt( 'shop_view' );

// Ensure visibility.
if ( ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) && ! woodmart_loop_prop( 'is_wishlist' ) ) {
	return;
}

// Increase loop count.
wc_set_loop_prop( 'loop', woodmart_loop_prop( 'woocommerce_loop' ) + 1 );
woodmart_set_loop_prop( 'woocommerce_loop', woodmart_loop_prop( 'woocommerce_loop' ) + 1 );
$woocommerce_loop = woodmart_loop_prop( 'woocommerce_loop' );

// Grid or list style.
if ( ( 'grid' === $shop_view || 'list' === $shop_view ) && ! Main::get_instance()->has_custom_layout( 'shop_archive' ) ) {
	$current_view = $shop_view;
}

if ( $is_shortcode ) {
	$current_view = woodmart_loop_prop( 'products_view' );
}

if ( $is_slider ) {
	$current_view = 'grid';
}

// Extra post classes.
$classes = array( 'wd-product' );

if ( ! $is_slider ) {
	$grid_different_sizes = woodmart_loop_prop( 'grid_items_different_sizes' );
	$grid_different_sizes = is_array( $grid_different_sizes ) ? array_map( 'absint', $grid_different_sizes ) : $grid_different_sizes;

	if ( ! isset( $different_sizes ) ) {
		$different_sizes = false;
	}

	if ( ( 'grid' === $current_view && $different_sizes && ( in_array( $woocommerce_loop, woodmart_get_wide_items_array( $different_sizes ), true ) ) ) || ( $grid_different_sizes && in_array( $woocommerce_loop, $grid_different_sizes, true ) ) ) {
		$classes[] = 'wd-wider';
	}

	$classes[] = 'wd-col';
}

if ( ( 'custom' === $hover_type && ( 'grid' === $current_view && woodmart_loop_prop( 'product_custom_hover' ) && 'publish' === get_post_status( woodmart_loop_prop( 'product_custom_hover' ) ) ) ) || ( 'list' === $current_view && woodmart_loop_prop( 'product_custom_list' ) && 'publish' === get_post_status( woodmart_loop_prop( 'product_custom_list' ) ) ) ) {
	$hover = 'custom';

	if ( 'list' === $current_view && woodmart_loop_prop( 'product_custom_list' ) ) {
		$classes[] = 'wd-loop-item-' . woodmart_loop_prop( 'product_custom_list' );
	} else {
		$classes[] = 'wd-loop-item-' . woodmart_loop_prop( 'product_custom_hover' );
	}

	$classes[] = 'wd-hover-parent';
} else {
	if (
		'info' === $hover &&
		(
			( woodmart_get_opt( 'new_label' ) && woodmart_is_new_label_needed( $product->get_id() ) ) ||
			( woodmart_get_opt( 'attribute_label' ) && woodmart_get_product_attributes_label() ) ||
			( woodmart_get_opt( 'sale_label' ) && $product->is_on_sale() ) ||
			( woodmart_get_opt( 'hot_label' ) && $product->is_featured() ) ||
			( woodmart_get_opt( 'stock_label' ) && ! $product->is_in_stock() ) ||
			(
				woodmart_get_opt( 'custom_labels' ) && Labels_Frontend::get_instance()->manager->get_current_custom_labels_ids()
			)
		)
	) {
		$classes[] = 'wd-with-labels';
	}

	if ( 'base' === $hover || 'fw-button' === $hover ) {
		wp_enqueue_script( 'imagesloaded' );
		woodmart_enqueue_js_script( 'product-hover' );
		woodmart_enqueue_js_script( 'product-more-description' );
	}

	if ( 'list' === $current_view ) {
		$hover     = 'list';
		$classes[] = 'product-list-item';
		woodmart_set_loop_prop( 'products_columns', 1 );
	} else {
		$classes[] = 'wd-hover-' . $hover;

		if ( 'base' === $hover || 'fw-button' === $hover ) {
			$classes[] = 'wd-hover-with-fade';
		}
	}

	if ( woodmart_loop_prop( 'product_quantity' ) && ( 'quick' === $hover || 'standard' === $hover || 'fw-button' === $hover || 'list' === $hover ) && ! $product->is_sold_individually() && ( 'variable' !== $product->get_type() || 'variation_form' === woodmart_get_opt( 'quick_shop_variable_type' ) ) && $product->is_purchasable() && $product->is_in_stock() ) {
		if ( 'quick' === $hover || 'fw-button' === $hover ) {
			$classes[] = 'wd-quantity-overlap';
		} else {
			$classes[] = 'wd-quantity';
		}
	}

	if ( ! empty( $different_sizes ) && in_array( $woocommerce_loop, woodmart_get_wide_items_array( $different_sizes ), true ) ) {
		woodmart_set_loop_prop( 'double_size', true );
	}

	$desktop_columns = woodmart_loop_prop( 'products_columns' );
	$tablet_columns  = woodmart_loop_prop( 'products_columns_tablet' );
	$mobile_columns  = woodmart_loop_prop( 'products_columns_mobile' );

	if ( $is_slider && ( 'base' === $hover || 'fw-button' === $hover ) ) {
		$classes[] = 'wd-fade-off';
	}

	if ( 'yes' === get_option( 'woocommerce_enable_reviews' ) && 'yes' === get_option( 'woocommerce_enable_review_rating' ) && 'base' === $hover && ( $product->get_rating_count() > 0 || woodmart_get_opt( 'show_empty_star_rating' ) ) ) {
		$classes[] = 'has-stars';
	}

	if ( 'base' === $hover && ! woodmart_have_product_swatches_template() ) {
		$classes[] = 'product-no-swatches';
	}

	if ( 'no' !== woodmart_loop_prop( 'grid_gallery' ) || ( ! woodmart_loop_prop( 'grid_gallery' ) && woodmart_get_opt( 'grid_gallery' ) ) ) {
		add_action( 'woocommerce_before_shop_loop_item_title', 'woodmart_template_loop_product_thumbnails_gallery', 5 );
	}

	$classes[] = 'product-grid-item';
}

if ( 'default' !== woodmart_loop_prop( 'products_color_scheme' ) ) {
	$classes[] = 'color-scheme-' . woodmart_loop_prop( 'products_color_scheme' );
}

$classes[] = 'product';

?>
<div <?php wc_product_class( $classes, $product ); ?> data-loop="<?php echo esc_attr( $woocommerce_loop ); ?>" data-id="<?php echo esc_attr( $product->get_id() ); ?>">
	<?php wc_get_template_part( 'content', 'product-' . $hover ); ?>
</div>
