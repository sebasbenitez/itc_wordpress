<?php
/**
 * Imagify integration.
 *
 * @package woodmart
 */

if ( ! defined( 'IMAGIFY_VERSION' ) ) {
	return;
}

if ( ! function_exists( 'woodmart_imagify_disable_webp_for_gallery_images' ) ) {
	/**
	 * Disables Imagify WebP conversion for product gallery images.
	 *
	 * @param string $classes CSS class for gallery image.
	 * @return string Modified CSS class with no-webp flag.
	 */
	function woodmart_imagify_disable_webp_for_gallery_images( $classes ) {
		$classes .= ' imagify-no-webp';

		return $classes;
	}

	add_filter( 'woodmart_single_product_gallery_image_class', 'woodmart_imagify_disable_webp_for_gallery_images' );
}

if ( ! function_exists( 'woodmart_imagify_convert_srcset_to_webp' ) ) {
	/**
	 * Converts product thumbnail srcset URLs to WebP format.
	 *
	 * @param string $image_srcset Image srcset attribute value.
	 * @param int    $attachment_id Attachment ID.
	 * @return string Modified srcset with WebP URLs.
	 */
	function woodmart_imagify_convert_srcset_to_webp( $image_srcset, $attachment_id ) {
		if ( ! function_exists( 'imagify_path_to_nextgen' ) ) {
			return $image_srcset;
		}

		$image_path = wp_get_original_image_path( $attachment_id );

		if ( $image_path ) {
			$image_srcset_array = explode( ',', $image_srcset );

			foreach ( $image_srcset_array as $key => $srcset_line ) {
				$srcset_line_array = explode( ' ', trim( $srcset_line ) );

				if ( false === strpos( $srcset_line_array[0], '.webp' ) && woodmart_attachment_url_to_path( $srcset_line_array[0] . '.webp' ) ) {
					$srcset_line_array[0] = imagify_path_to_nextgen( $srcset_line_array[0], 'webp' );
				}

				$image_srcset_array[ $key ] = implode( ' ', $srcset_line_array );
			}

			$image_srcset = implode( ',', $image_srcset_array );
		}

		return $image_srcset;
	}

	add_filter( 'woodmart_product_thumbnails_urls_image_srcset', 'woodmart_imagify_convert_srcset_to_webp', 10, 2 );
	add_filter( 'woodmart_get_webp_image_srcset', 'woodmart_imagify_convert_srcset_to_webp', 10, 2 );
}
