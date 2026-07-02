<?php
/**
 * Shortcodes for HTML block element.
 *
 * @package woodmart.
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

if ( ! function_exists( 'woodmart_html_block_shortcode' ) ) {
	/**
	 * HTML block shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 *
	 * @return string
	 */
	function woodmart_html_block_shortcode( $atts ) {
		extract( // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
			shortcode_atts(
				array(
					'id' => 0,
				),
				$atts
			)
		);

		return woodmart_get_html_block( $id );
	}
}
