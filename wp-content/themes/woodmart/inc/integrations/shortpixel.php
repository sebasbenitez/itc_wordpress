<?php
/**
 * ShortPixel Image Optimizer integration.
 *
 * @package woodmart
 */

if ( ! defined( 'SHORTPIXEL_IMAGE_OPTIMISER_VERSION' ) ) {
	return;
}

if ( ! function_exists( 'woodmart_shortpixel_convert_srcset_to_webp' ) ) {
	/**
	 * Converts product thumbnail srcset URLs to WebP or AVIF format.
	 *
	 * @param string $image_srcset Image srcset attribute value.
	 * @param int    $attachment_id Attachment ID.
	 * @return string Modified srcset with WebP/AVIF URLs.
	 */
	function woodmart_shortpixel_convert_srcset_to_webp( $image_srcset, $attachment_id ) {
		if ( ! function_exists( 'wpSPIO' ) || ! $image_srcset ) {
			return $image_srcset;
		}

		$image_path = wp_get_original_image_path( $attachment_id );

		if ( ! $image_path ) {
			return $image_srcset;
		}

		$image_srcset_array = explode( ',', $image_srcset );

		foreach ( $image_srcset_array as $key => $srcset_line ) {
			$srcset_line_array = explode( ' ', trim( $srcset_line ) );

			if ( false === strpos( $srcset_line_array[0], '.webp' ) && false === strpos( $srcset_line_array[0], '.avif' ) ) {
				$parsed_url = wp_parse_url( $srcset_line_array[0] );

				if ( ! isset( $parsed_url['path'] ) ) {
					continue;
				}

				$webp_path = woodmart_shortpixel_get_converted_image_path( $parsed_url['path'], 'webp' );
				$webp_file = ABSPATH . ltrim( $webp_path, '/' );

				if ( file_exists( $webp_file ) ) {
					$srcset_line_array[0] = woodmart_shortpixel_build_image_url( $parsed_url, $webp_path );
				} else {
					$avif_path = woodmart_shortpixel_get_converted_image_path( $parsed_url['path'], 'avif' );
					$avif_file = ABSPATH . ltrim( $avif_path, '/' );

					if ( file_exists( $avif_file ) ) {
						$srcset_line_array[0] = woodmart_shortpixel_build_image_url( $parsed_url, $avif_path );
					}
				}
			}

			$image_srcset_array[ $key ] = implode( ' ', $srcset_line_array );
		}

		return implode( ',', $image_srcset_array );
	}

	add_filter( 'woodmart_product_thumbnails_urls_image_srcset', 'woodmart_shortpixel_convert_srcset_to_webp', 10, 2 );
	add_filter( 'woodmart_get_webp_image_srcset', 'woodmart_shortpixel_convert_srcset_to_webp', 10, 2 );
}

if ( ! function_exists( 'woodmart_shortpixel_get_converted_image_path' ) ) {
	/**
	 * Get converted image path for WebP or AVIF.
	 *
	 * @param string $path Original image path.
	 * @param string $type Image type (webp or avif).
	 * @return string Converted image path.
	 */
	function woodmart_shortpixel_get_converted_image_path( $path, $type ) {
		$is_double = ( 'webp' === $type )
			? \wpSPIO()->env()->useDoubleWebpExtension()
			: \wpSPIO()->env()->useDoubleAvifExtension();

		if ( $is_double ) {
			return $path . '.' . $type;
		} else {
			return substr( $path, 0, strrpos( $path, '.' ) ) . '.' . $type;
		}
	}
}

if ( ! function_exists( 'woodmart_shortpixel_build_image_url' ) ) {
	/**
	 * Build image URL from parsed URL and new path.
	 *
	 * @param array  $parsed_url Parsed URL array.
	 * @param string $new_path New image path.
	 * @return string Complete image URL.
	 */
	function woodmart_shortpixel_build_image_url( $parsed_url, $new_path ) {
		$new_url = $parsed_url['scheme'] . '://' . $parsed_url['host'];

		if ( isset( $parsed_url['port'] ) ) {
			$new_url .= ':' . $parsed_url['port'];
		}

		$new_url .= $new_path;

		return $new_url;
	}
}
