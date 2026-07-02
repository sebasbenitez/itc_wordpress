<?php

use XTS\Modules\Layouts\Main;

if ( ! function_exists( 'wd_gutenberg_woo_notices' ) ) {
	function wd_gutenberg_woo_notices( $block_attributes ) {
		if ( ! woodmart_woocommerce_installed() ) {
			return '';
		}

		$el_id = wd_get_gutenberg_element_id( $block_attributes );

		Main::setup_preview();

		ob_start();

		woodmart_enqueue_inline_style( 'woo-el-notices-builder' );

		?>
			<div <?php echo $el_id ? 'id="' . esc_attr( $el_id ) . '" ' : ''; ?>class="wd-wc-notices<?php echo esc_attr( wd_get_gutenberg_element_classes( $block_attributes ) ); ?>">
				<?php woocommerce_output_all_notices(); ?>
			</div>
		<?php
		Main::restore_preview();

		return ob_get_clean();
	}
}
