<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * The template for displaying all xts templates.
 *
 * @package woodmart
 */

use XTS\Modules\Layouts\Loop_Item;
use XTS\Modules\Layouts\Main as Builder;
use XTS\Modules\Layouts\Single_Product;

$layout_type           = get_post_meta( get_the_ID(), 'wd_layout_type', true );
$wc_layouts            = array( 'single_product', 'shop_archive', 'cart', 'empty_cart', 'checkout_content', 'checkout_form', 'thank_you_page', 'my_account_page', 'my_account_auth', 'my_account_lost_password' );
$checkout_form_id      = Builder::get_instance()->get_layout_id( 'checkout_form' );
$checkout_content_id   = Builder::get_instance()->get_layout_id( 'checkout_content' );
$checkout_form_post    = get_post( $checkout_form_id );
$checkout_content_post = get_post( $checkout_content_id );

if ( 'checkout_form' === $layout_type && $checkout_form_post && has_blocks( $checkout_form_post->post_content ) ) {
	$checkout_content_id = false;
} elseif ( 'checkout_content' === $layout_type && $checkout_content_post && has_blocks( $checkout_content_post->post_content ) ) {
	$checkout_form_id = false;
}
?>

<?php get_header(); ?>

<?php if ( woodmart_woocommerce_installed() && in_array( $layout_type, $wc_layouts, true ) ) : ?>
	<?php do_action( 'woocommerce_before_main_content' ); ?>
<?php endif; ?>

<?php if ( 'checkout_content' === $layout_type ) : ?>
<div class="woocommerce-checkout">
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<?php the_content(); ?>
	<?php endwhile; ?>

	<?php if ( $checkout_form_id ) : ?>
		<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">
			<?php if ( woodmart_is_elementor_installed() && Elementor\Plugin::$instance->documents->get( $checkout_form_post->ID )->is_built_with_elementor() ) : ?>
				<?php echo woodmart_elementor_get_content( $checkout_form_post->ID ); // phpcs:ignore ?>
			<?php else : ?>
				<?php
				$shortcodes_custom_css          = get_post_meta( $checkout_form_post->ID, '_wpb_shortcodes_custom_css', true );
				$woodmart_shortcodes_custom_css = get_post_meta( $checkout_form_post->ID, 'woodmart_shortcodes_custom_css', true );

				$content = '<style data-type="vc_shortcodes-custom-css">';
				if ( ! empty( $shortcodes_custom_css ) ) {
					$content .= $shortcodes_custom_css;
				}

				if ( ! empty( $woodmart_shortcodes_custom_css ) ) {
					$content .= $woodmart_shortcodes_custom_css;
				}
				$content .= '</style>';

				$content .= apply_filters( 'the_content', $checkout_form_post->post_content );

				echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			<?php endif; ?>
		</form>
	<?php endif; ?>
</div>
<?php elseif ( 'checkout_form' === $layout_type ) : ?>
	<div class="woocommerce-checkout">
		<?php if ( $checkout_content_id ) : ?>
			<?php if ( woodmart_is_elementor_installed() && Elementor\Plugin::$instance->documents->get( $checkout_content_post->ID )->is_built_with_elementor() ) : ?>
			<?php echo woodmart_elementor_get_content( $checkout_content_post->ID ); // phpcs:ignore ?>
		<?php elseif ( has_blocks( $checkout_content_post->post_content ) ) : ?>
			<?php echo apply_filters( 'the_content', $checkout_content_post->post_content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php else : ?>
			<?php
			$shortcodes_custom_css          = get_post_meta( $checkout_content_post->ID, '_wpb_shortcodes_custom_css', true );
			$woodmart_shortcodes_custom_css = get_post_meta( $checkout_content_post->ID, 'woodmart_shortcodes_custom_css', true );

			$content = '<style data-type="vc_shortcodes-custom-css">';
			if ( ! empty( $shortcodes_custom_css ) ) {
				$content .= $shortcodes_custom_css;
			}

			if ( ! empty( $woodmart_shortcodes_custom_css ) ) {
				$content .= $woodmart_shortcodes_custom_css;
			}
			$content .= '</style>';

			$content .= apply_filters( 'the_content', $checkout_content_post->post_content );

			echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		<?php endif; ?>
	<?php endif; ?>

		<?php if ( has_blocks( $checkout_form_post->post_content ) ) : ?>
			<?php while ( have_posts() ) : ?>
				<?php the_post(); ?>
				<?php the_content(); ?>
		<?php endwhile; ?>
	<?php else : ?>
		<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">
			<?php while ( have_posts() ) : ?>
				<?php the_post(); ?>
				<?php the_content(); ?>
			<?php endwhile; ?>
		</form>
	<?php endif; ?>
</div>
<?php elseif ( 'single_product' === $layout_type ) : ?>
<div <?php wc_product_class( 'entry-content wd-entry-content' ); ?>>
		<?php while ( have_posts() ) : ?>
			<?php the_post(); ?>
			<?php if ( Single_Product::get_instance()::get_preview_product_id() ) : ?>
				<?php the_content(); ?>
		<?php endif; ?>
	<?php endwhile; ?>
</div>
<?php elseif ( 'product_loop_item' === $layout_type ) : ?>
	<?php
	$loop_id              = get_the_ID();
	$classes              = ' wd-loop-item-wrap-' . $loop_id;
	$inner_classes        = '';
	$preview_width        = get_post_meta( $loop_id, 'wd_preview_width', true );
	$preview_width_tablet = get_post_meta( $loop_id, 'wd_preview_widthTablet', true );
	$preview_width_mobile = get_post_meta( $loop_id, 'wd_preview_widthMobile', true );

	if ( get_post_meta( $loop_id, 'wd_bordered_grid', true ) ) {
		woodmart_enqueue_inline_style( 'bordered-product' );

		$classes .= ' products-bordered-grid';
	}

	if ( get_post_meta( $loop_id, 'wd_stretch_product', true ) ) {
		woodmart_enqueue_inline_style( 'woo-opt-stretch-cont' );

		$classes .= ' wd-stretch-cont-lg';
	}
	if ( get_post_meta( $loop_id, 'wd_stretch_productTablet', true ) ) {
		woodmart_enqueue_inline_style( 'woo-opt-stretch-cont' );

		$classes .= ' wd-stretch-cont-md';
	}
	if ( get_post_meta( $loop_id, 'wd_stretch_productMobile', true ) ) {
		woodmart_enqueue_inline_style( 'woo-opt-stretch-cont' );

		$classes .= ' wd-stretch-cont-sm';
	}

	if ( get_post_meta( $loop_id, 'wd_transform', true ) ) {
		$inner_classes .= ' wd-transform';
	}

	$products = wc_get_products(
		array(
			'status' => 'publish',
			'limit'  => 4,
		)
	);

	if ( empty( $products ) ) {
		return;
	}

	$product_ids = array_map(
		function ( $product ) {
			return $product->get_id();
		},
		$products
	);

	?>
	<style>
		.wd-products .wd-product {
			max-width: <?php echo esc_html( $preview_width ? $preview_width . 'px' : '320px' ); ?>;
		}

		<?php if ( $preview_width_tablet ) : ?>
		@media (max-width: 1024px) {
			.wd-products .wd-product {
				max-width: <?php echo esc_html( $preview_width_tablet . 'px' ); ?>;
			}
		}
		<?php endif; ?>
		<?php if ( $preview_width_mobile ) : ?>
		@media (max-width: 767.98px) {
			.wd-products .wd-product {
				max-width: <?php echo esc_html( $preview_width_mobile . 'px' ); ?>;
			}
		}
		<?php endif; ?>
	</style>
	<div class="entry-content">
		<div class="wd-products wd-grid-g elements-grid wd-loop-builder-on<?php echo esc_attr( $classes ); ?>" style="--wd-col-lg:4;--wd-col-md:3;--wd-col-sm:2;--wd-gap-lg:20px;--wd-gap-sm:10px;">
			<?php while ( have_posts() ) : ?>
				<?php the_post(); ?>
				<?php foreach ( $product_ids as $product_id ) : ?>
					<?php Loop_Item::set_preview_product( $product_id ); ?>
					<div class="wd-product wd-col wd-hover-parent wd-loop-item-<?php echo esc_attr( $loop_id ); ?>">
						<div class="wd-product-wrapper wd-entry-content<?php echo esc_attr( $inner_classes ); ?>">
							<?php the_content(); ?>
						</div>
					</div>
				<?php endforeach; ?>
			<?php endwhile; ?>
		</div>
	</div>
<?php else : ?>
	<div class="entry-content">
		<?php while ( have_posts() ) : ?>
			<?php the_post(); ?>
			<?php the_content(); ?>
		<?php endwhile; ?>
	</div>
<?php endif; ?>

<?php if ( woodmart_woocommerce_installed() && in_array( $layout_type, $wc_layouts, true ) ) : ?>
	<?php do_action( 'woocommerce_after_main_content' ); ?>
<?php endif; ?>

<?php
get_footer();
