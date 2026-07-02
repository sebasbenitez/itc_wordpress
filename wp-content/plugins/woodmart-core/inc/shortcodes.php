<?php
/**
 * Shortcodes.
 *
 * @package woodmart
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_add_shortcodes' ) ) {
	/**
	 * Register theme shortcodes.
	 */
	function woodmart_add_shortcodes() {
		if ( function_exists( 'woodmart_get_current_page_builder' ) && 'wpb' === woodmart_get_current_page_builder() ) {
			// Single product.
			add_shortcode( 'woodmart_single_product_add_to_cart', 'woodmart_shortcode_single_product_add_to_cart' );
			add_shortcode( 'woodmart_single_product_additional_info_table', 'woodmart_shortcode_single_product_additional_info_table' );
			add_shortcode( 'woodmart_single_product_brand_information', 'woodmart_shortcode_single_product_brand_information' );
			add_shortcode( 'woodmart_single_product_brands', 'woodmart_shortcode_single_product_brands' );
			add_shortcode( 'woodmart_single_product_compare_button', 'woodmart_shortcode_single_product_compare_button' );
			add_shortcode( 'woodmart_single_product_content', 'woodmart_shortcode_single_product_content' );
			add_shortcode( 'woodmart_single_product_countdown', 'woodmart_shortcode_single_product_countdown' );
			add_shortcode( 'woodmart_single_product_extra_content', 'woodmart_shortcode_single_product_extra_content' );
			add_shortcode( 'woodmart_single_product_gallery', 'woodmart_shortcode_single_product_gallery' );
			add_shortcode( 'woodmart_single_product_label', 'woodmart_shortcode_single_product_label' );
			add_shortcode( 'woodmart_single_product_labels', 'woodmart_shortcode_single_product_labels' );
			add_shortcode( 'woodmart_single_product_meta', 'woodmart_shortcode_single_product_meta' );
			add_shortcode( 'woodmart_single_product_meta_value', 'woodmart_shortcode_single_product_meta_value' );
			add_shortcode( 'woodmart_single_product_nav', 'woodmart_shortcode_single_product_nav' );
			add_shortcode( 'woodmart_single_product_price', 'woodmart_shortcode_single_product_price' );
			add_shortcode( 'woodmart_single_product_rating', 'woodmart_shortcode_single_product_rating' );
			add_shortcode( 'woodmart_single_product_reviews', 'woodmart_shortcode_single_product_reviews' );
			add_shortcode( 'woodmart_single_product_short_description', 'woodmart_shortcode_single_product_short_description' );
			add_shortcode( 'woodmart_single_product_size_guide_button', 'woodmart_shortcode_single_product_size_guide_button' );
			add_shortcode( 'woodmart_single_product_stock_progress_bar', 'woodmart_shortcode_single_product_stock_progress_bar' );
			add_shortcode( 'woodmart_single_product_tabs', 'woodmart_shortcode_single_product_tabs' );
			add_shortcode( 'woodmart_single_product_title', 'woodmart_shortcode_single_product_title' );
			add_shortcode( 'woodmart_single_product_wishlist_button', 'woodmart_shortcode_single_product_wishlist_button' );
			add_shortcode( 'woodmart_single_product_visitor_counter', 'woodmart_shortcode_single_product_visitor_counter' );
			add_shortcode( 'woodmart_single_product_linked_variations', 'woodmart_shortcode_single_product_linked_variations' );
			add_shortcode( 'woodmart_single_product_fbt_products', 'woodmart_shortcode_single_product_fbt_products' );
			add_shortcode( 'woodmart_single_product_stock_status', 'woodmart_shortcode_single_product_stock_status' );
			add_shortcode( 'woodmart_single_product_sold_counter', 'woodmart_shortcode_single_product_sold_counter' );
			add_shortcode( 'woodmart_single_product_estimate_delivery', 'woodmart_shortcode_single_product_estimate_delivery' );
			add_shortcode( 'woodmart_single_product_dynamic_discounts_table', 'woodmart_shortcode_single_product_dynamic_discounts_table' );
			add_shortcode( 'woodmart_single_product_price_tracker', 'woodmart_shortcode_single_product_price_tracker' );

			// Single post.
			add_shortcode( 'woodmart_single_post_author_meta', 'woodmart_shortcode_single_post_author_meta' );
			add_shortcode( 'woodmart_single_post_categories', 'woodmart_shortcode_single_post_categories' );
			add_shortcode( 'woodmart_single_post_comment_form', 'woodmart_shortcode_single_post_comment_form' );
			add_shortcode( 'woodmart_single_post_comments', 'woodmart_shortcode_single_post_comments' );
			add_shortcode( 'woodmart_single_post_comments_button', 'woodmart_shortcode_single_post_comments_button' );
			add_shortcode( 'woodmart_single_post_content', 'woodmart_shortcode_single_post_content' );
			add_shortcode( 'woodmart_single_post_date_meta', 'woodmart_shortcode_single_post_date_meta' );
			add_shortcode( 'woodmart_single_post_excerpt', 'woodmart_shortcode_single_post_excerpt' );
			add_shortcode( 'woodmart_single_post_image', 'woodmart_shortcode_single_post_image' );
			add_shortcode( 'woodmart_single_post_meta_value', 'woodmart_shortcode_single_post_meta_value' );
			add_shortcode( 'woodmart_single_post_navigation', 'woodmart_shortcode_single_post_navigation' );
			add_shortcode( 'woodmart_single_post_tags', 'woodmart_shortcode_single_post_tags' );
			add_shortcode( 'woodmart_single_post_title', 'woodmart_shortcode_single_post_title' );

			// Blog and single post.
			add_shortcode( 'woodmart_post_author_bio', 'woodmart_shortcode_post_author_bio' );

			// Archive loop.
			add_shortcode( 'woodmart_blog_archive_loop', 'woodmart_shortcode_blog_archive_loop' );
			add_shortcode( 'woodmart_portfolio_archive_loop', 'woodmart_shortcode_portfolio_archive_loop' );
			add_shortcode( 'woodmart_portfolio_archive_categories', 'woodmart_shortcode_portfolio_archive_categories' );

			// Shop archive.
			add_shortcode( 'woodmart_shop_archive_active_filters', 'woodmart_shortcode_shop_archive_active_filters' );
			add_shortcode( 'woodmart_shop_archive_description', 'woodmart_shortcode_shop_archive_description' );
			add_shortcode( 'woodmart_shop_archive_extra_description', 'woodmart_shortcode_shop_category_extra_description' );
			add_shortcode( 'woodmart_shop_archive_products', 'woodmart_shortcode_shop_archive_products' );
			add_shortcode( 'woodmart_shop_archive_filters_area', 'woodmart_shortcode_shop_archive_filters_area' );
			add_shortcode( 'woodmart_shop_archive_filters_area_btn', 'woodmart_shortcode_shop_archive_filters_area_btn' );
			add_shortcode( 'woodmart_shop_archive_orderby', 'woodmart_shortcode_shop_archive_orderby' );
			add_shortcode( 'woodmart_shop_archive_orderby', 'woodmart_shortcode_shop_archive_orderby' );
			add_shortcode( 'woodmart_shop_archive_per_page', 'woodmart_shortcode_shop_archive_per_page' );
			add_shortcode( 'woodmart_shop_archive_result_count', 'woodmart_shortcode_shop_archive_result_count' );
			add_shortcode( 'woodmart_sidebar', 'woodmart_shortcode_sidebar' );
			add_shortcode( 'woodmart_shop_archive_view', 'woodmart_shortcode_shop_archive_view' );
			add_shortcode( 'woodmart_shop_archive_woocommerce_title', 'woodmart_shortcode_shop_archive_woocommerce_title' );

			// Cart.
			add_shortcode( 'woodmart_cart_table', 'woodmart_shortcode_cart_table' );
			add_shortcode( 'woodmart_cart_totals', 'woodmart_shortcode_cart_totals' );
			add_shortcode( 'woodmart_cart_free_gifts', 'woodmart_shortcode_cart_free_gifts' );
			add_shortcode( 'woodmart_empty_cart', 'woodmart_shortcode_empty_cart' );

			// Checkout.
			add_shortcode( 'woodmart_checkout_billing_details_form', 'woodmart_shortcode_checkout_billing_details_form' );
			add_shortcode( 'woodmart_checkout_coupon_form', 'woodmart_shortcode_checkout_coupon_form' );
			add_shortcode( 'woodmart_checkout_login_form', 'woodmart_shortcode_checkout_login_form' );
			add_shortcode( 'woodmart_checkout_order_review', 'woodmart_shortcode_checkout_order_review' );
			add_shortcode( 'woodmart_checkout_payment_methods', 'woodmart_shortcode_checkout_payment_methods' );
			add_shortcode( 'woodmart_checkout_shipping_details_form', 'woodmart_shortcode_checkout_shipping_details_form' );

			// Thank you page.
			add_shortcode( 'woodmart_tp_customer_details', 'woodmart_shortcode_tp_customer_details' );
			add_shortcode( 'woodmart_tp_order_details', 'woodmart_shortcode_tp_order_details' );
			add_shortcode( 'woodmart_tp_order_overview', 'woodmart_shortcode_tp_order_overview' );
			add_shortcode( 'woodmart_tp_order_message', 'woodmart_shortcode_tp_order_message' );
			add_shortcode( 'woodmart_tp_payment_instructions', 'woodmart_shortcode_tp_payment_instructions' );
			add_shortcode( 'woodmart_tp_order_meta', 'woodmart_shortcode_tp_order_meta' );

			// My account page.
			add_shortcode( 'woodmart_my_account_content', 'woodmart_shortcode_my_account_content' );
			add_shortcode( 'woodmart_my_account_nav', 'woodmart_shortcode_my_account_nav' );

			// My account auth.
			add_shortcode( 'woodmart_my_account_login', 'woodmart_shortcode_my_account_login' );
			add_shortcode( 'woodmart_my_account_register', 'woodmart_shortcode_my_account_register' );

			// My account lost password.
			add_shortcode( 'woodmart_my_account_lost_pass', 'woodmart_shortcode_my_account_lost_pass' );
		}

		// WooCommerce.
		add_shortcode( 'woodmart_woocommerce_breadcrumb', 'woodmart_shortcode_woocommerce_breadcrumb' );
		add_shortcode( 'woodmart_woocommerce_checkout_steps', 'woodmart_shortcode_woocommerce_checkout_steps' );
		add_shortcode( 'woodmart_woocommerce_hook', 'woodmart_shortcode_woocommerce_hook' );
		add_shortcode( 'woodmart_woocommerce_notices', 'woodmart_shortcode_woocommerce_notices' );
		add_shortcode( 'woodmart_page_title', 'woodmart_shortcode_page_title' );
		add_shortcode( 'woodmart_shipping_progress_bar', 'woodmart_shortcode_shipping_progress_bar' );

		add_shortcode( 'html_block', 'woodmart_html_block_shortcode' );
		add_shortcode( 'social_buttons', 'woodmart_shortcode_social' );
		add_shortcode( 'woodmart_info_box', 'woodmart_shortcode_info_box' );
		add_shortcode( 'woodmart_info_box_carousel', 'woodmart_shortcode_info_box_carousel' );
		add_shortcode( 'woodmart_button', 'woodmart_shortcode_button' );
		add_shortcode( 'author_area', 'woodmart_shortcode_author_area' );
		add_shortcode( 'promo_banner', 'woodmart_shortcode_promo_banner' );
		add_shortcode( 'banners_carousel', 'woodmart_shortcode_banners_carousel' );
		add_shortcode( 'woodmart_instagram', 'woodmart_shortcode_instagram' );
		add_shortcode( 'user_panel', 'woodmart_shortcode_user_panel' );
		add_shortcode( 'woodmart_size_guide', 'woodmart_size_guide_shortcode' );
		add_shortcode( 'woodmart_gallery', 'woodmart_images_gallery_shortcode' );
		add_shortcode( 'woodmart_blog', 'woodmart_shortcode_blog' );
		add_shortcode( 'woodmart_shortcode_products_widget', 'woodmart_shortcode_products_widget' );

		if ( class_exists( 'XTS\Modules\Compare\Ui' ) ) {
			add_shortcode( 'woodmart_compare', array( XTS\Modules\Compare\Ui::get_instance(), 'compare_page' ) );
		}

		if ( class_exists( 'XTS\WC_Wishlist\Ui' ) ) {
			add_shortcode( 'woodmart_wishlist', array( XTS\WC_Wishlist\Ui::get_instance(), 'wishlist_page' ) );
		}

		if ( function_exists( 'woodmart_get_current_page_builder' ) && 'wpb' === woodmart_get_current_page_builder() ) {
			add_shortcode( 'woodmart_3d_view', 'woodmart_shortcode_3d_view' );
			add_shortcode( 'woodmart_ajax_search', 'woodmart_ajax_search' );
			add_shortcode( 'woodmart_countdown_timer', 'woodmart_shortcode_countdown_timer' );
			add_shortcode( 'woodmart_counter', 'woodmart_shortcode_animated_counter' );
			add_shortcode( 'extra_menu', 'woodmart_shortcode_extra_menu' );
			add_shortcode( 'extra_menu_list', 'woodmart_shortcode_extra_menu_list' );
			add_shortcode( 'woodmart_google_map', 'woodmart_shortcode_google_map' );
			add_shortcode( 'woodmart_image_hotspot', 'woodmart_image_hotspot_shortcode' );
			add_shortcode( 'woodmart_hotspot', 'woodmart_hotspot_shortcode' );
			add_shortcode( 'woodmart_list', 'woodmart_list_shortcode' );
			add_shortcode( 'woodmart_mega_menu', 'woodmart_shortcode_mega_menu' );
			add_shortcode( 'woodmart_menu_price', 'woodmart_shortcode_menu_price' );
			add_shortcode( 'woodmart_popup', 'woodmart_shortcode_popup' );
			add_shortcode( 'woodmart_portfolio', 'woodmart_shortcode_portfolio' );
			add_shortcode( 'pricing_tables', 'woodmart_shortcode_pricing_tables' );
			add_shortcode( 'pricing_plan', 'woodmart_shortcode_pricing_plan' );
			add_shortcode( 'woodmart_responsive_text_block', 'woodmart_shortcode_responsive_text_block' );
			add_shortcode( 'woodmart_text_block', 'woodmart_shortcode_text_block' );
			add_shortcode( 'woodmart_marquee', 'woodmart_shortcode_marquee' );
			add_shortcode( 'woodmart_contact_form_7', 'woodmart_shortcode_contact_form_7' );
			add_shortcode( 'woodmart_nested_carousel', 'woodmart_shortcode_nested_carousel' );
			add_shortcode( 'woodmart_nested_carousel_item', 'woodmart_shortcode_nested_carousel_item' );
			add_shortcode( 'woodmart_image', 'woodmart_shortcode_image' );
			add_shortcode( 'woodmart_mailchimp', 'woodmart_shortcode_mailchimp' );
			add_shortcode( 'woodmart_row_divider', 'woodmart_row_divider' );
			add_shortcode( 'woodmart_slider', 'woodmart_shortcode_slider' );
			add_shortcode( 'team_member', 'woodmart_shortcode_team_member' );
			add_shortcode( 'testimonials', 'woodmart_shortcode_testimonials' );
			add_shortcode( 'testimonial', 'woodmart_shortcode_testimonial' );
			add_shortcode( 'woodmart_timeline', 'woodmart_timeline_shortcode' );
			add_shortcode( 'woodmart_timeline_item', 'woodmart_timeline_item_shortcode' );
			add_shortcode( 'woodmart_timeline_breakpoint', 'woodmart_timeline_breakpoint_shortcode' );
			add_shortcode( 'woodmart_title', 'woodmart_shortcode_title' );
			add_shortcode( 'woodmart_twitter', 'woodmart_twitter' );
			add_shortcode( 'woodmart_tabs', 'woodmart_shortcode_tabs' );
			add_shortcode( 'woodmart_tab', 'woodmart_shortcode_tab' );
			add_shortcode( 'woodmart_accordion', 'woodmart_shortcode_accordion' );
			add_shortcode( 'woodmart_accordion_item', 'woodmart_shortcode_accordion_item' );
			add_shortcode( 'woodmart_off_canvas_btn', 'woodmart_shortcode_off_canvas_btn' );
			add_shortcode( 'woodmart_open_street_map', 'woodmart_shortcode_open_street_map' );
			add_shortcode( 'woodmart_table', 'woodmart_shortcode_table' );
			add_shortcode( 'woodmart_table_row', 'woodmart_shortcode_table_row' );
			add_shortcode( 'woodmart_video', 'woodmart_shortcode_video' );
			add_shortcode( 'woodmart_compare_images', 'woodmart_shortcode_compare_images' );
			add_shortcode( 'woodmart_el_breadcrumbs', 'woodmart_shortcode_el_breadcrumbs' );
			add_shortcode( 'woodmart_page_heading', 'woodmart_shortcode_page_heading' );
			add_shortcode( 'woodmart_toggle', 'woodmart_shortcode_toggle' );

			require_once ABSPATH . 'wp-admin/includes/plugin.php';

			if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
				add_shortcode( 'products_tabs', 'woodmart_shortcode_products_tabs' );
				add_shortcode( 'products_tab', 'woodmart_shortcode_products_tab' );
				add_shortcode( 'woodmart_brands', 'woodmart_shortcode_brands' );
				add_shortcode( 'woodmart_categories', 'woodmart_shortcode_categories' );
				add_shortcode( 'woodmart_product_filters', 'woodmart_product_filters_shortcode' );
				add_shortcode( 'woodmart_filter_categories', 'woodmart_filters_categories_shortcode' );
				add_shortcode( 'woodmart_filters_attribute', 'woodmart_filters_attribute_shortcode' );
				add_shortcode( 'woodmart_filters_orderby', 'woodmart_orderby_filter_template' );
				add_shortcode( 'woodmart_filters_price_slider', 'woodmart_filters_price_slider_shortcode' );
				add_shortcode( 'woodmart_stock_status', 'woodmart_stock_status_shortcode' );
				add_shortcode( 'woodmart_products', 'woodmart_shortcode_products' );
			}

			if ( function_exists( 'vc_add_shortcode_param' ) ) {
				vc_add_shortcode_param( 'woodmart_datepicker', 'woodmart_get_datepicker_param' );
				vc_add_shortcode_param( 'woodmart_button_set', 'woodmart_get_button_set_param' );
				vc_add_shortcode_param( 'woodmart_colorpicker', 'woodmart_get_colorpicker_param' );
				vc_add_shortcode_param( 'woodmart_css_id', 'woodmart_get_css_id_param' );
				vc_add_shortcode_param( 'woodmart_dropdown', 'woodmart_get_dropdown_param' );
				vc_add_shortcode_param( 'woodmart_empty_space', 'woodmart_get_empty_space_param' );
				vc_add_shortcode_param( 'woodmart_gradient', 'woodmart_add_gradient_type' );
				vc_add_shortcode_param( 'woodmart_image_hotspot', 'woodmart_image_hotspot' );
				vc_add_shortcode_param( 'woodmart_image_select', 'woodmart_add_image_select_type' );
				vc_add_shortcode_param( 'woodmart_responsive_size', 'woodmart_get_responsive_size_param' );
				vc_add_shortcode_param( 'woodmart_responsive_spacing', 'woodmart_get_responsive_spacing_param' );
				vc_add_shortcode_param( 'woodmart_slider', 'woodmart_get_slider_param' );
				vc_add_shortcode_param( 'woodmart_switch', 'woodmart_get_switch_param' );
				vc_add_shortcode_param( 'woodmart_title_divider', 'woodmart_get_title_divider_param' );

				vc_add_shortcode_param( 'wd_slider', 'woodmart_get_slider_responsive_param' );
				vc_add_shortcode_param( 'wd_text', 'woodmart_get_text_responsive_param' );
				vc_add_shortcode_param( 'wd_number', 'woodmart_get_number_param' );
				vc_add_shortcode_param( 'wd_colorpicker', 'woodmart_get_wd_colorpicker_param' );
				vc_add_shortcode_param( 'wd_box_shadow', 'woodmart_get_box_shadow_param' );
				vc_add_shortcode_param( 'wd_select', 'woodmart_get_select_param' );
				vc_add_shortcode_param( 'wd_notice', 'woodmart_get_notice_param' );
				vc_add_shortcode_param( 'wd_dimensions', 'woodmart_get_dimensions_responsive_param' );
				vc_add_shortcode_param( 'wd_fonts', 'woodmart_get_fonts_param' );
				vc_add_shortcode_param( 'wd_upload', 'woodmart_get_upload_param' );
				vc_add_shortcode_param( 'wd_backdrop_filter', 'woodmart_get_backdrop_filter_param' );
			}
		}

		if ( function_exists( 'woodmart_get_opt' ) && woodmart_get_opt( 'single_post_justified_gallery' ) ) {
			remove_shortcode( 'gallery' );
			add_shortcode( 'gallery', 'woodmart_gallery_shortcode' );
		}
	}

	add_action( 'init', 'woodmart_add_shortcodes' );
}
