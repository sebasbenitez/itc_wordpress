<?php

use XTS\Modules\Layouts\Main;
use XTS\Modules\Estimate_Delivery\Frontend as Estimate_Delivery_Frontend;

if ( ! function_exists( 'wd_gutenberg_single_product_estimate_delivery' ) ) {
	function wd_gutenberg_single_product_estimate_delivery( $block_attributes, $content ) {
		if ( ! woodmart_get_opt( 'estimate_delivery_enabled' ) ) {
			return '';
		}

		$wrapper_classes = ' wd-style-' . $block_attributes['style'];
		$el_id           = wd_get_gutenberg_element_id( $block_attributes );

		if ( isset( $block_attributes['iconType'] ) && 'icon' === $block_attributes['iconType'] && $content ) {
			$wrapper_classes .= ' wd-with-icon';
		}

		ob_start();

		Main::setup_preview();
		?>
		<div <?php echo $el_id ? 'id="' . esc_attr( $el_id ) . '" ' : ''; ?>class="wd-single-est-del<?php echo esc_attr( wd_get_gutenberg_element_classes( $block_attributes ) ); ?>">
			<?php Estimate_Delivery_Frontend::get_instance()->render_on_single_product( $wrapper_classes, do_shortcode( $content ) ); ?>
		</div>
		<?php
		Main::restore_preview();

		return ob_get_clean();
	}
}
