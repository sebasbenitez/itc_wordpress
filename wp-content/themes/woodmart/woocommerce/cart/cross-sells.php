<?php
/**
 * Cross-sells
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cross-sells.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce/Templates
 * @version     9.6.0
 */

defined( 'ABSPATH' ) || exit;

if ( $cross_sells ) {
	$product_ids = array();

	foreach ( $cross_sells as $cross_sell ) {
		$product_ids[] = $cross_sell->get_id();
	}

	$products_atts = apply_filters(
		'woodmart_cross_sells_products_args',
		array(
			'element_title'                => apply_filters( 'woocommerce_product_cross_sells_products_heading', __( 'You may be interested in&hellip;', 'woocommerce' ) ),
			'layout'                       => 'carousel',
			'post_type'                    => 'ids',
			'include'                      => implode( ',', $product_ids ),
			'slides_per_view'              => apply_filters( 'woodmart_cross_sells_products_per_view', 4 ),
			'slides_per_view_tablet'       => 'auto',
			'slides_per_view_mobile'       => 'auto',
			'img_size'                     => 'woocommerce_thumbnail',
			'custom_sizes'                 => apply_filters( 'woodmart_cross_sells_custom_sizes', false ),
			'hide_pagination_control'      => true,
			'hide_prev_next_buttons'       => true,
			'product_quantity'             => woodmart_get_opt( 'product_quantity' ),
			'products_bordered_grid'       => woodmart_get_opt( 'products_bordered_grid' ),
			'products_bordered_grid_style' => woodmart_get_opt( 'products_bordered_grid_style' ),
			'products_with_background'     => woodmart_get_opt( 'products_with_background' ),
			'products_shadow'              => woodmart_get_opt( 'products_shadow' ),
			'products_color_scheme'        => woodmart_get_opt( 'products_color_scheme' ),
			'spacing'                      => woodmart_get_opt( 'products_spacing' ),
			'spacing_tablet'               => woodmart_get_opt( 'products_spacing_tablet', '' ),
			'spacing_mobile'               => woodmart_get_opt( 'products_spacing_mobile', '' ),
			'wrapper_classes'              => ' cross-sells',
			'query_post_type'              => array( 'product', 'product_variation' ),
			'items_per_page'               => count( $product_ids ),
		)
	);

	echo woodmart_shortcode_products( $products_atts ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
