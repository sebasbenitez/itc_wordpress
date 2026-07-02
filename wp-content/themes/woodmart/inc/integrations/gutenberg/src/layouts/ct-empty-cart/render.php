<?php

use XTS\Modules\Layouts\Main;

if ( ! function_exists( 'wd_gutenberg_empty_cart' ) ) {
	function wd_gutenberg_empty_cart( $block_attributes ) {
		if ( ! woodmart_woocommerce_installed() ) {
			return '';
		}

		$el_id = wd_get_gutenberg_element_id( $block_attributes );

		Main::setup_preview();

		ob_start();

		?>
		<div <?php echo $el_id ? 'id="' . esc_attr( $el_id ) . '" ' : ''; ?>class="<?php echo esc_attr( wd_get_gutenberg_element_classes( $block_attributes ) ); ?>">
			<?php wc_get_template( 'cart/cart-empty.php' ); ?>
		</div>
		<?php
		Main::restore_preview();

		return ob_get_clean();
	}
}
