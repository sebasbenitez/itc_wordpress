<?php

use XTS\Modules\Layouts\Main;
use XTS\Modules\Sold_Counter\Main as Sold_Counter_Module;

if ( ! function_exists( 'wd_gutenberg_single_product_sold_counter' ) ) {
	function wd_gutenberg_single_product_sold_counter( $block_attributes, $inner_content ) {
		$wrapper_classes = ' wd-style-' . $block_attributes['style'];
		$el_id           = wd_get_gutenberg_element_id( $block_attributes );

		if ( isset( $block_attributes['iconType'] ) && 'icon' === $block_attributes['iconType'] && $inner_content ) {
			$wrapper_classes .= ' wd-with-icon';
		}

		ob_start();

		Main::setup_preview();

		Sold_Counter_Module::get_instance()->render( $wrapper_classes, do_shortcode( $inner_content ) );

		$content = ob_get_clean();

		if ( ! $content ) {
			Main::restore_preview();

			return '';
		}

		ob_start();

		?>
		<div <?php echo $el_id ? 'id="' . esc_attr( $el_id ) . '" ' : ''; ?>class="wd-single-sold-count<?php echo esc_attr( wd_get_gutenberg_element_classes( $block_attributes ) ); ?>">
			<?php echo do_shortcode( $content ); ?>
		</div>
		<?php
		Main::restore_preview();

		return ob_get_clean();
	}
}
