<?php
/**
 * WooCommerce Breadcrumbs block render.
 *
 * @package woodmart
 */

use XTS\Modules\Layouts\Main;

if ( ! function_exists( 'wd_gutenberg_woo_breadcrumbs' ) ) {
	/**
	 * Render Woo breadcrumbs block.
	 *
	 * @param array $block_attributes Block attributes.
	 * @return string
	 */
	function wd_gutenberg_woo_breadcrumbs( $block_attributes ) {
		$classes = wd_get_gutenberg_element_classes( $block_attributes );
		$el_id   = wd_get_gutenberg_element_id( $block_attributes );

		if ( ! empty( $block_attributes['textAlign'] ) || ! empty( $block_attributes['textAlignTablet'] ) || ! empty( $block_attributes['textAlignMobile'] ) ) {
			$classes .= ' wd-align';
		}

		Main::setup_preview();

		ob_start();

		if ( ! empty( $block_attributes['nowrapMd'] ) ) {
			woodmart_enqueue_inline_style( 'mod-breadcrumbs-no-wrap' );
			$classes .= ' wd-nowrap-md';
		}

		?>
			<div <?php echo $el_id ? 'id="' . esc_attr( $el_id ) . '" ' : ''; ?>class="wd-el-breadcrumbs<?php echo esc_attr( $classes ); ?>">
				<?php
				woodmart_current_breadcrumbs( 'shop' );
				?>
			</div>
		<?php
		Main::restore_preview();

		return ob_get_clean();
	}
}
