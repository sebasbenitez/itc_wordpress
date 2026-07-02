<?php
/**
 * Render file for 'Small' product design.
 * Products(grid or carousel) element.
 *
 * @package woodmart
 */

global $product;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

?>

<?php do_action( 'woodmart_before_shop_loop_thumbnail' ); ?>


<div class="wd-product-wrapper product-wrapper">
	<div class="wd-product-thumb product-element-top">
		<a href="<?php echo esc_url( get_permalink() ); ?>" class="wd-product-img-link product-image-link" aria-label="<?php echo esc_attr( get_the_title() ); ?>">
			<?php do_action( 'woocommerce_before_shop_loop_item_title' ); ?>
		</a>
		<?php do_action( 'woodmart_shop_loop_product_thumbnail' ); ?>
	</div>

	<div class="product-element-bottom">
	<?php
		do_action( 'woocommerce_shop_loop_item_title' );
		echo wp_kses_post( woodmart_get_product_rating() );
		do_action( 'woocommerce_after_shop_loop_item_title' );
	?>
	</div>
</div>

