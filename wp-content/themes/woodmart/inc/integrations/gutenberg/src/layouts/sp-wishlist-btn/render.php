<?php
/**
 * Single product block wishlist button render.
 *
 * @package woodmart
 */

use XTS\WC_Wishlist\Ui as Wishlist;
use XTS\Modules\Layouts\Main;

if ( ! function_exists( 'wd_gutenberg_single_product_wishlist_btn' ) ) {
	/**
	 * Render single product block wishlist button.
	 *
	 * @param array $block_attributes Block attributes.
	 *
	 * @return string
	 */
	function wd_gutenberg_single_product_wishlist_btn( $block_attributes ) {
		if ( ! woodmart_get_opt( 'wishlist' ) || ( ! is_user_logged_in() && woodmart_get_opt( 'wishlist_logged' ) ) ) {
			return '';
		}

		$el_id        = wd_get_gutenberg_element_id( $block_attributes );
		$btn_classes  = 'wd-action-btn wd-wishlist-icon';
		$btn_classes .= ' wd-style-' . $block_attributes['style'];

		ob_start();

		Main::setup_preview();

		if ( 'icon' === $block_attributes['style'] ) {
			$btn_classes .= ' wd-tooltip';

			woodmart_enqueue_js_library( 'tooltips' );
			woodmart_enqueue_js_script( 'btns-tooltips' );
		}

		?>
		<div <?php echo $el_id ? 'id="' . esc_attr( $el_id ) . '" ' : ''; ?>class="wd-single-action-btn wd-single-wishlist-btn<?php echo esc_attr( wd_get_gutenberg_element_classes( $block_attributes ) ); ?>">
			<?php Wishlist::get_instance()->add_to_wishlist_btn( $btn_classes ); ?>
		</div>
		<?php

		Main::restore_preview();

		return ob_get_clean();
	}
}
