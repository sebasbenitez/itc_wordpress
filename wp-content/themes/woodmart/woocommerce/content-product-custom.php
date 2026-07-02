<?php
/**
 * Product content template for custom hover effects.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product-custom.php.
 *
 * @package woodmart
 */

$classes  = '';
$hover_id = 'list' === woodmart_loop_prop( 'products_view' ) ? woodmart_loop_prop( 'product_custom_list' ) : woodmart_loop_prop( 'product_custom_hover' );

if ( 1 === woodmart_loop_prop( 'woocommerce_loop' ) ) {
	woodmart_add_editable_post_to_admin_bar( $hover_id );
}

if ( get_post_meta( $hover_id, 'wd_transform', true ) ) {
	$classes .= ' wd-transform';
}

?>
<div class="wd-product-wrapper wd-entry-content<?php echo esc_attr( $classes ); ?>">
	<?php do_action( 'woodmart_loop_item_content', $hover_id ); ?>
</div>
