<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * The template for displaying single custom label preview.
 *
 * @package woodmart
 */

use XTS\Modules\Custom_Labels\Frontend as Labels_Frontend;

get_header();

if ( ! current_user_can( apply_filters( 'woodmart_wd_custom_label_access', 'edit_posts' ) ) ) {
	wp_die( 'You do not have access.', '', array( 'back_link' => true ) );
}

if ( ! woodmart_woocommerce_installed() ) {
	get_footer();
	return;
}

$products_atts = array(
	'layout'         => 'grid',
	'columns'        => 4,
	'items_per_page' => 4,
);

$hover_type = woodmart_loop_prop( 'product_hover_type', 'predefined' );

?>
<div class="entry-content">
	<?php
	global $product, $post;
	remove_action( 'woodmart_shop_loop_product_thumbnail', 'woocommerce_show_product_loop_sale_flash', 10 );
	add_action( 'woodmart_shop_loop_product_thumbnail', array( Labels_Frontend::get_instance(), 'print_custom_label_preview' ), 10 );

	echo woodmart_shortcode_products( $products_atts ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	remove_action( 'woodmart_shop_loop_product_thumbnail', array( Labels_Frontend::get_instance(), 'print_custom_label_preview' ), 10 );
	add_action( 'woodmart_shop_loop_product_thumbnail', 'woocommerce_show_product_loop_sale_flash', 10 );
	?>
</div>
<?php

get_footer();
