<?php
/**
 * WP Rocket integration.
 *
 * @package woodmart
 */

if ( ! defined( 'WP_ROCKET_VERSION' ) ) {
	return;
}

if ( ! function_exists( 'woodmart_rocket_remove_elementor_css_from_exclusions' ) ) {
	/**
	 * Removes Elementor CSS files from WP Rocket CSS exclusions.
	 *
	 * @param array $excluded_files List of excluded CSS files.
	 * @return array Modified list of excluded files.
	 */
	function woodmart_rocket_remove_elementor_css_from_exclusions( $excluded_files ) {
		$upload   = wp_get_upload_dir();
		$basepath = wp_parse_url( $upload['baseurl'], PHP_URL_PATH );

		if ( empty( $basepath ) ) {
			return $excluded_files;
		}

		$key = array_search( $basepath . '/elementor/css/(.*).css', $excluded_files, true );

		if ( false !== $key ) {
			unset( $excluded_files[ $key ] );
		}

		return $excluded_files;
	}

	add_action( 'rocket_exclude_css', 'woodmart_rocket_remove_elementor_css_from_exclusions' );
}

if ( ! function_exists( 'woodmart_rocket_add_delay_js_exclusions' ) ) {
	/**
	 * Adds WoodMart JS files to WP Rocket delay JS exclusions.
	 *
	 * @param array $exclude_delay_js List of excluded JS files.
	 * @return array Modified list of excluded JS files.
	 */
	function woodmart_rocket_add_delay_js_exclusions( $exclude_delay_js ) {
		if ( ! woodmart_get_opt( 'rocket_delay_js_exclusions' ) ) {
			return $exclude_delay_js;
		}

		return wp_parse_args(
			$exclude_delay_js,
			array(
				'/jquery-?[0-9.](.*)(.min|.slim|.slim.min)?.js',
				'helpers',
				'scrollBar',
				'clickOnScrollButton',
				'searchFullScreen',
				'menuOffsets',
				'menuStickyOffsets',
				'menuOverlay',
				'menuDropdowns',
				'clearSearch',
				'cartWidget',
				'cart-fragments',
				'mobileNavigation',
				'loginSidebar',
				'menuSetUp',
				'productImages',
				'cookie.min',
				'imagesLoaded',
				'ageVerify',
				'magnific-popup',
				'headerBuilder',
				'swiper',
				'swiperInit',
				'trackProductViewed',
				'lazyLoading',
			)
		);
	}

	add_filter( 'rocket_delay_js_exclusions', 'woodmart_rocket_add_delay_js_exclusions' );
}

if ( ! function_exists( 'woodmart_rocket_add_defer_js_exclusions' ) ) {
	/**
	 * Adds WoodMart JS files to WP Rocket defer JS exclusions.
	 *
	 * @param array $excluded_files List of excluded JS files.
	 * @return array Modified list of excluded files.
	 */
	function woodmart_rocket_add_defer_js_exclusions( $excluded_files ) {
		if ( ! is_array( $excluded_files ) ) {
			$excluded_files = array();
		}

		$excluded_files[] = 'scrollBar';

		return $excluded_files;
	}

	add_filter( 'rocket_exclude_defer_js', 'woodmart_rocket_add_defer_js_exclusions' );
}

if ( ! function_exists( 'woodmart_rocket_add_rejected_uri_exclusions' ) ) {
	/**
	 * Adds WoodMart pages to WP Rocket rejected URI cache exclusions.
	 *
	 * @param array $uris List of rejected URIs.
	 * @return array Modified list of rejected URIs.
	 */
	function woodmart_rocket_add_rejected_uri_exclusions( $uris ) {
		$urls = array();

		if ( woodmart_get_opt( 'wishlist' ) && woodmart_get_opt( 'wishlist_page' ) ) {
			$urls[] = woodmart_get_wishlist_page_url();
		}
		if ( woodmart_get_opt( 'compare' ) && woodmart_get_opt( 'compare_page' ) ) {
			$urls[] = woodmart_get_compare_page_url();
		}

		if ( $urls ) {
			foreach ( $urls as $url ) {
				$uri = wp_parse_url( $url, PHP_URL_PATH );

				if ( $uri ) {
					$uris[] = $uri;
				}
			}
		}

		return $uris;
	}

	add_filter( 'rocket_cache_reject_uri', 'woodmart_rocket_add_rejected_uri_exclusions' );
}
