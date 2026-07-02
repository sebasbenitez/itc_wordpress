<?php
/**
 * Dokan integration.
 *
 * @package woodmart
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

if ( ! function_exists( 'woodmart_dokan_wrap_dashboard_start' ) ) {
	/**
	 * Outputs opening wrapper for Dokan dashboard pages.
	 *
	 * @return void
	 */
	function woodmart_dokan_wrap_dashboard_start() {
		echo '<div class="wd-content-area site-content col-12">';
	}

	add_action( 'dokan_dashboard_wrap_before', 'woodmart_dokan_wrap_dashboard_start', 10 );
}

if ( ! function_exists( 'woodmart_dokan_wrap_dashboard_end' ) ) {
	/**
	 * Outputs closing wrapper for Dokan dashboard pages.
	 *
	 * @return void
	 */
	function woodmart_dokan_wrap_dashboard_end() {
		echo '</div>';
	}

	add_action( 'dokan_dashboard_wrap_after', 'woodmart_dokan_wrap_dashboard_end', 10 );
}

if ( ! function_exists( 'woodmart_dokan_add_lazy_loading_attributes' ) ) {
	/**
	 * Adds lazy loading attributes to Dokan product images.
	 *
	 * @return array Allowed HTML attributes for product images.
	 */
	function woodmart_dokan_add_lazy_loading_attributes() {
		return array(
			'img' => array(
				'alt'         => array(),
				'class'       => array(),
				'height'      => array(),
				'src'         => array(),
				'width'       => array(),
				'data-src'    => array(),
				'data-srcset' => array(),
			),
		);
	}

	add_filter( 'dokan_product_image_attributes', 'woodmart_dokan_add_lazy_loading_attributes', 10 );
}

if ( ! function_exists( 'woodmart_dokan_remove_geolocation_map_from_shop' ) ) {
	/**
	 * Removes Dokan geolocation map from shop page when using Mapbox.
	 *
	 * @return void
	 */
	function woodmart_dokan_remove_geolocation_map_from_shop() {
		if ( ! function_exists( 'Dokan_Pro' ) || ! function_exists( 'dokan_get_option' ) || ! function_exists( 'dokan_remove_hook_for_anonymous_class' ) ) {
			return;
		}

		$source = dokan_get_option( 'map_api_source', 'dokan_appearance', 'google_maps' );

		if ( 'mapbox' === $source ) {
			dokan_remove_hook_for_anonymous_class( 'woocommerce_before_shop_loop', 'Dokan_Geolocation_Product_View', 'before_shop_loop', 10 );
			dokan_remove_hook_for_anonymous_class( 'woocommerce_no_products_found', 'Dokan_Geolocation_Product_View', 'before_shop_loop', 9 );
		}
	}

	add_action( 'init', 'woodmart_dokan_remove_geolocation_map_from_shop' );
}

if ( ! function_exists( 'woodmart_dokan_render_geolocation_map' ) ) {
	/**
	 * Renders Dokan geolocation map and filters before shop content.
	 *
	 * @return void
	 */
	function woodmart_dokan_render_geolocation_map() {
		if ( ! class_exists( 'Dokan_Pro' ) || ! function_exists( 'dokan_get_option' ) || ! function_exists( 'dokan_geo_filter_form' ) || ! function_exists( 'dokan_geo_get_template' ) ) {
			return;
		}

		$show_filters   = dokan_get_option( 'show_filters_before_locations_map', 'dokan_geolocation', 'on' );
		$map_location   = dokan_get_option( 'show_locations_map', 'dokan_geolocation', 'top' );
		$source         = dokan_get_option( 'map_api_source', 'dokan_appearance', 'google_maps' );
		$show_map_pages = dokan_get_option( 'show_location_map_pages', 'dokan_geolocation', 'shop' );

		if ( 'store_listing' === $show_map_pages ) {
			return;
		}

		?>
		<div class="wd-dokan-geo">
			<?php if ( 'on' === $show_filters && 'mapbox' === $source ) : ?>
				<?php dokan_geo_filter_form( 'product' ); ?>
			<?php endif; ?>

			<?php if ( 'top' === $map_location && 'mapbox' === $source ) : ?>
				<?php dokan_geo_get_template( 'map', array( 'layout' => 'top' ) ); ?>
			<?php endif; ?>
		</div>
		<?php
	}

	add_action( 'woocommerce_before_main_content', 'woodmart_dokan_render_geolocation_map' );
}

if ( ! function_exists( 'woodmart_dokan_get_shipping_package_index' ) ) {
	/**
	 * Gets the shipping package index for a product in a Dokan multi-vendor setup.
	 *
	 * @param int        $package_index Current package index.
	 * @param WC_Product $product       Instance of WC_Product class.
	 *
	 * @return int Updated package index.
	 */
	function woodmart_dokan_get_shipping_package_index( $package_index, $product ) {
		if ( $product instanceof WC_Product && function_exists( 'dokan' ) ) {
			$product_id = $product->get_id();
			$vendor_id  = get_post_field( 'post_author', $product_id );
			$packages   = WC()->shipping()->get_packages();

			foreach ( $packages as $i => $package ) {
				if ( isset( $package['seller_id'] ) && (int) $package['seller_id'] === (int) $vendor_id ) {
					$package_index = $i;
					break;
				}
			}
		}

		return $package_index;
	}

	add_filter( 'woodmart_get_shipping_package_index', 'woodmart_dokan_get_shipping_package_index', 10, 2 );
}
