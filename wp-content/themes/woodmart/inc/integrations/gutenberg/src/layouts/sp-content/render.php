<?php

use XTS\Modules\Layouts\Main;

if ( ! function_exists( 'wd_gutenberg_single_product_content' ) ) {
	function wd_gutenberg_single_product_content( $block_attributes ) {
		if ( wp_is_serving_rest_request() ) {
			return '';
		}

		$el_id = wd_get_gutenberg_element_id( $block_attributes );

		ob_start();

		Main::setup_preview();

		?>
			<div <?php echo $el_id ? 'id="' . esc_attr( $el_id ) . '" ' : ''; ?>class="wd-single-content wd-entry-content<?php echo esc_attr( wd_get_gutenberg_element_classes( $block_attributes ) ); ?>">
				<?php the_content(); ?>
			</div>
		<?php
		Main::restore_preview();

		return ob_get_clean();
	}
}
