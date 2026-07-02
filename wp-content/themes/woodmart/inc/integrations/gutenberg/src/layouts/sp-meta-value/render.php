<?php

use XTS\Modules\Layouts\Main;

if ( ! function_exists( 'wd_gutenberg_single_product_meta_value' ) ) {
	function wd_gutenberg_single_product_meta_value( $block_attributes ) {
		if ( empty( $block_attributes['metaKey'] ) ) {
			return '';
		}

		ob_start();

		Main::setup_preview();

		$value = get_post_meta( get_the_ID(), $block_attributes['metaKey'], true );
		$el_id = wd_get_gutenberg_element_id( $block_attributes );

		if ( '' === $value ) {
			Main::restore_preview();

			return '';
		}

		?>
			<div <?php echo $el_id ? 'id="' . esc_attr( $el_id ) . '" ' : ''; ?>class="wd-single-meta-value<?php echo esc_attr( wd_get_gutenberg_element_classes( $block_attributes ) ); ?>">
				<?php
					echo wp_kses( $value, true );
				?>
			</div>
		<?php
		Main::restore_preview();

		return ob_get_clean();
	}
}
