<?php
/**
 * My Account Content Gutenberg block render.
 *
 * @package woodmart
 */

use XTS\Modules\Layouts\Main;
use XTS\WC_Wishlist\Ui;

if ( ! function_exists( 'wd_gutenberg_my_account_content' ) ) {
	/**
	 * My Account Content block render.
	 *
	 * @param array $block_attributes Block attributes.
	 * @return false|string
	 */
	function wd_gutenberg_my_account_content( $block_attributes ) {
		$wrapper_classes  = wd_get_gutenberg_element_classes( $block_attributes );
		$wrapper_classes .= ' woocommerce-MyAccount-content';
		$el_id            = wd_get_gutenberg_element_id( $block_attributes );

		Main::setup_preview();

		ob_start();
		?>
		<div <?php echo $el_id ? 'id="' . esc_attr( $el_id ) . '" ' : ''; ?>class="wd-el-my-acc-content<?php echo esc_attr( $wrapper_classes ); ?>">
			<?php
			/**
			 * Hook: woocommerce_account_content.
			 */
			if ( (int) woodmart_get_opt( 'wishlist_page' ) === get_the_ID() && class_exists( 'XTS\WC_Wishlist\Ui' ) ) {
				$ui_instance = Ui::get_instance();
				if ( $ui_instance->is_editable() ) {
					add_action( 'woocommerce_before_shop_loop_item', array( $ui_instance, 'output_settings_btn' ) );
					add_action( 'woodmart_loop_item_content', array( $ui_instance, 'output_settings_btn' ), 5 );
				}

				echo $ui_instance->wishlist_page_content(); // phpcs:ignore.

				if ( $ui_instance->is_editable() ) {
					remove_action( 'woocommerce_before_shop_loop_item', array( $ui_instance, 'output_settings_btn' ) );
					remove_action( 'woodmart_loop_item_content', array( $ui_instance, 'output_settings_btn' ), 5 );
				}
			} else {
				remove_action( 'woocommerce_account_dashboard', 'woodmart_my_account_links', 10 );
				do_action( 'woocommerce_account_content' ); // phpcs:ignore.
			}
			?>
		</div>
		<?php

		Main::restore_preview();
		return ob_get_clean();
	}
}
