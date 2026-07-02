<?php

use XTS\Modules\Layouts\Main;
use XTS\Modules\Visitor_Counter\Main as Counter_Visitors;

if ( ! function_exists( 'wd_gutenberg_single_product_visitor_counter' ) ) {
	function wd_gutenberg_single_product_visitor_counter( $block_attributes, $content ) {
		$el_id            = wd_get_gutenberg_element_id( $block_attributes );
		$wrapper_classes  = wd_get_gutenberg_element_classes( $block_attributes );
		$wrapper_classes .= ' wd-style-' . $block_attributes['style'];

		if ( isset( $block_attributes['iconType'] ) && 'icon' === $block_attributes['iconType'] && $content ) {
			$wrapper_classes .= ' wd-with-icon';
		}

		ob_start();

		Main::setup_preview();

		?>
		<div <?php echo $el_id ? 'id="' . esc_attr( $el_id ) . '" ' : ''; ?>class="wd-single-visitor-count<?php echo esc_attr( wd_get_gutenberg_element_classes( $block_attributes ) ); ?>">
			<?php Counter_Visitors::get_instance()->output_count_visitors( $wrapper_classes, do_shortcode( $content ) ); ?>
		</div>
		<?php

		Main::restore_preview();

		return ob_get_clean();
	}
}
