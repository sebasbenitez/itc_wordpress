<?php
/**
 * Widgets.
 *
 * @package woodmart
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_widgets_init' ) ) {
	/**
	 * Register theme widgets.
	 */
	function woodmart_widgets_init() {
		if ( ! is_blog_installed() || ! class_exists( 'WOODMART_WP_Nav_Menu_Widget' ) ) {
			return;
		}

		register_widget( 'WOODMART_WP_Nav_Menu_Widget' );
		register_widget( 'WOODMART_Banner_Widget' );
		register_widget( 'WOODMART_Author_Area_Widget' );
		register_widget( 'WOODMART_Instagram_Widget' );
		register_widget( 'WOODMART_Static_Block_Widget' );
		register_widget( 'WOODMART_Recent_Posts' );
		register_widget( 'WOODMART_Twitter' );
		register_widget( 'WOODMART_Widget_Mailchimp' );

		if ( woodmart_woocommerce_installed() ) {
			register_widget( 'WOODMART_User_Panel_Widget' );
			register_widget( 'WOODMART_Widget_Layered_Nav' );
			register_widget( 'WOODMART_Widget_Sorting' );
			register_widget( 'WOODMART_Widget_Price_Filter' );
			register_widget( 'WOODMART_Widget_Search' );
			register_widget( 'WOODMART_Stock_Status' );

			if ( class_exists( 'WOODMART_Product_Category_Filter' ) ) {
				register_widget( 'WOODMART_Product_Category_Filter' );
			}
		}
	}

	add_action( 'widgets_init', 'woodmart_widgets_init' );
}
