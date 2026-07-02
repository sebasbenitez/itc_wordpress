<?php
/**
 * RevSlider integration.
 *
 * @package woodmart
 */

if ( ! defined( 'RS_REVISION' ) ) {
	return;
}

if ( ! function_exists( 'woodmart_revslider_disable_post_saving_on_cart_register' ) ) {
	/**
	 * Disables RevSlider post saving action during cart registration.
	 *
	 * @param bool $skip Whether to skip cart registration.
	 * @return bool Original skip value.
	 */
	function woodmart_revslider_disable_post_saving_on_cart_register( $skip ) {
		if ( class_exists( 'RevSliderFront' ) ) {
			remove_action( 'save_post', array( 'RevSliderFront', 'set_post_saving' ) );
		}

		return $skip;
	}

	add_filter( 'woodmart_skip_register_cart', 'woodmart_revslider_disable_post_saving_on_cart_register' );
}
