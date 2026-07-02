<?php
/**
 * Woodmart Variation Gallery module
 *
 * @package woodmart
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

if ( 'old' !== woodmart_get_opt( 'variation_gallery_storage_method', 'new' ) ) {
	return;
}

// -------------------------------------------------------------------------------
// Print admin variation gallery html
// -------------------------------------------------------------------------------
if ( ! function_exists( 'woodmart_vg_admin_html' ) ) {
	/**
	 * Output variation gallery html in admin variation settings.
	 *
	 * @param int                  $loop            Variation loop index.
	 * @param array                $variation_data Variation data.
	 * @param WC_Product_Variation $variation      Variation object.
	 */
	function woodmart_vg_admin_html( $loop, $variation_data, $variation ) {
		global $post;

		if ( ! woodmart_get_opt( 'variation_gallery' ) ) {
			return;
		}

		$attachments            = '';
		$variation_gallery_data = get_post_meta( $post->ID, 'woodmart_variation_gallery_data', true ) ? get_post_meta( $post->ID, 'woodmart_variation_gallery_data', true ) : array();

		foreach ( $variation_gallery_data as $variation_id => $image_ids ) {
			if ( intval( $variation_id ) === intval( $variation->ID ) ) {
				$attachments = array_filter( explode( ',', $image_ids ) );
			}
		}

		echo '<div class="woodmart-variation-gallery-wrapper">';
			echo '<h4>' . esc_html__( 'Variation Image Gallery', 'woodmart' ) . '</h4>';

			echo '<ul class="woodmart-variation-gallery-images">';

		if ( $attachments && is_array( $attachments ) ) {
			foreach ( $attachments as $attachment_id ) {
				$image = wp_get_attachment_image_src( $attachment_id );

				echo '<li class="image" data-attachment_id="' . esc_attr( $attachment_id ) . '">';
					echo '<img src="' . esc_url( $image[0] ) . '">';
					echo '<a href="#" class="delete woodmart-remove-variation-gallery-image" aria-label="' . esc_attr_e( 'Remove variation gallery image', 'woodmart' ) . '"><span class="xts-i-close"></span></a>';
				echo '</li>';
			}
		}

			echo '</ul>';

		if ( $attachments ) {
			$attachments = implode( ',', $attachments );
		}

			echo '<input type="hidden" class="variation-gallery-ids" name="woodmart_variation_gallery[' . esc_attr( $variation->ID ) . ']" value="' . esc_attr( $attachments ) . '">';

			echo '<a href="#" class="button woodmart-add-variation-gallery-image">' . esc_html__( 'Add Gallery Images', 'woodmart' ) . '</a>';
		echo '</div>';
	}

	add_action( 'woocommerce_variation_options', 'woodmart_vg_admin_html', 10, 3 );
}

// -------------------------------------------------------------------------------
// Save variation gallery images
// -------------------------------------------------------------------------------
if ( ! function_exists( 'woodmart_save_vg_images' ) ) {
	/**
	 * Save variation gallery images.
	 *
	 * @param int $variation_id Variation ID.
	 * @param int $i            Variation loop index.
	 */
	function woodmart_save_vg_images( $variation_id, $i ) { // phpcs:ignore.
		$product_id             = wp_get_post_parent_id( $variation_id );
		$variation_gallery_data = get_post_meta( $product_id, 'woodmart_variation_gallery_data', true );
		$output                 = $variation_gallery_data ? $variation_gallery_data : array();
		$ids                    = isset( $_POST['woodmart_variation_gallery'] ) && array_key_exists( $variation_id, $_POST['woodmart_variation_gallery'] ) ? sanitize_text_field( wp_unslash( $_POST['woodmart_variation_gallery'][ $variation_id ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification

		if ( ! empty( $ids ) ) {
			$output[ $variation_id ] = $ids;
			$output                  = array_filter( $output );
		}

		update_post_meta( $product_id, 'woodmart_variation_gallery_data', $output );
	}

	add_action( 'woocommerce_save_product_variation', 'woodmart_save_vg_images', 10, 2 );
}

// -------------------------------------------------------------------------------
// Remove unnecessary variation gallery data
// -------------------------------------------------------------------------------

if ( ! function_exists( 'woodmart_remove_unnecessary_vg_data' ) ) {
	/**
	 * Remove unnecessary variation gallery data when variation is deleted.
	 *
	 * @param int $post_id Post ID.
	 */
	function woodmart_remove_unnecessary_vg_data( $post_id ) {
		if ( ! woodmart_woocommerce_installed() ) {
			return;
		}

		$product = wc_get_product( $post_id );

		if ( ! $product || 'variable' !== $product->get_type() ) {
			return;
		}

		$available_variations = array();

		foreach ( $product->get_children() as $child_id ) {
			$available_variations[] = $product->get_available_variation( wc_get_product( $child_id ) );
		}

		$available_variations = array_values( array_filter( $available_variations ) );

		$variations             = 'variable' === $product->get_type() ? $available_variations : '';
		$variations_ids         = array();
		$variation_gallery_data = get_post_meta( $post_id, 'woodmart_variation_gallery_data', true );

		if ( ! $variations || ! $variation_gallery_data ) {
			return;
		}

		foreach ( $variations as $variation ) {
			$variations_ids[] = $variation['variation_id'];
		}

		foreach ( $variation_gallery_data as $key => $data ) {
			if ( ! in_array( $key, $variations_ids, true ) && isset( $variation_gallery_data[ $key ] ) ) {
				unset( $variation_gallery_data[ $key ] );
			}
		}

		update_post_meta( $post_id, 'woodmart_variation_gallery_data', $variation_gallery_data );
	}

	add_action( 'save_post', 'woodmart_remove_unnecessary_vg_data' );
}

// -------------------------------------------------------------------------------
// Get variation gallery images data
// -------------------------------------------------------------------------------
if ( ! function_exists( 'woodmart_get_vg_data' ) ) {
	/**
	 * Get variation gallery images data.
	 *
	 * @return array
	 */
	function woodmart_get_vg_data() {
		if ( ! woodmart_woocommerce_installed() ) {
			return array();
		}

		$product_id = get_the_ID();
		$product    = wc_get_product( $product_id );

		if ( ! $product || $product->get_type() !== 'variable' ) {
			return array();
		}

		$variation_gallery_data = get_post_meta( $product_id, 'woodmart_variation_gallery_data', true );
		$default_images_data    = woodmart_get_default_vg_data( $product_id );
		$data                   = array();

		if ( ! $variation_gallery_data || ! is_array( $variation_gallery_data ) ) {
			return array();
		}

		foreach ( $variation_gallery_data as $variation_id => $image_ids ) {
			$ids = array_filter( explode( ',', $image_ids ) );

			if ( has_post_thumbnail( $variation_id ) ) {
				array_unshift( $ids, get_post_thumbnail_id( $variation_id ) );
			}

			foreach ( $ids as $id ) {
				$data[ $variation_id ][] = woodmart_get_vg_image_data( $id );
			}
		}

		if ( $default_images_data ) {
			$data['default'] = $default_images_data;
		}

		return $data;
	}
}

// -------------------------------------------------------------------------------
// Get default gallery images data
// -------------------------------------------------------------------------------
if ( ! function_exists( 'woodmart_get_default_vg_data' ) ) {
	/**
	 * Get default gallery images data.
	 *
	 * @param int $product_id Product ID.
	 *
	 * @return array|void
	 */
	function woodmart_get_default_vg_data( $product_id ) {
		if ( ! woodmart_woocommerce_installed() ) {
			return;
		}

		$product = wc_get_product( $product_id );

		if ( ! $product ) {
			return;
		}

		$default_image_ids = $product->get_gallery_image_ids();

		$images = array();

		if ( has_post_thumbnail( $product_id ) ) {
			array_unshift( $default_image_ids, get_post_thumbnail_id( $product_id ) );
		}

		if ( $default_image_ids && is_array( $default_image_ids ) ) {
			foreach ( $default_image_ids as $id ) {
				$images[] = woodmart_get_vg_image_data( $id );
			}
		}

		return $images;
	}
}

// -------------------------------------------------------------------------------
// Get gallery images data
// -------------------------------------------------------------------------------
if ( ! function_exists( 'woodmart_get_vg_image_data' ) ) {
	/**
	 * Get gallery image data.
	 *
	 * @param int $attachment_id Attachment ID.
	 */
	function woodmart_get_vg_image_data( $attachment_id ) {
		woodmart_lazy_loading_deinit( true );

		$full_size_image = wp_get_attachment_image_src( $attachment_id, 'full' );
		$thumbnail       = wp_get_attachment_image_src( $attachment_id, 'woocommerce_thumbnail' );
		$thumbnail_size  = apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' );
		$full_size_image = wp_get_attachment_image_src( $attachment_id, $thumbnail_size );

		$attributes = array(
			'title'                   => get_post_field( 'post_title', $attachment_id ),
			'data-caption'            => get_post_field( 'post_excerpt', $attachment_id ),
			'data-src'                => isset( $full_size_image[0] ) ? $full_size_image[0] : '',
			'data-large_image'        => isset( $full_size_image[0] ) ? $full_size_image[0] : '',
			'data-large_image_width'  => isset( $full_size_image[1] ) ? $full_size_image[1] : '',
			'data-large_image_height' => isset( $full_size_image[2] ) ? $full_size_image[2] : '',
			'class'                   => 'wp-post-image',
		);

		$output = array(
			'image'      => wp_get_attachment_image( $attachment_id, 'woocommerce_single', false, $attributes ),
			'data_thumb' => isset( $thumbnail[0] ) ? $thumbnail[0] : '',
			'href'       => isset( $full_size_image[0] ) ? $full_size_image[0] : '',
		);

		woodmart_lazy_loading_init();

		return apply_filters( 'woodmart_get_single_product_image_data', $output, $attachment_id );
	}
}

// -------------------------------------------------------------------------------
// Single product object with gallery data
// -------------------------------------------------------------------------------
if ( ! function_exists( 'woodmart_single_product_vg_data' ) ) {
	/**
	 * Add variation gallery data to single product page.
	 */
	function woodmart_single_product_vg_data() {
		if ( ! woodmart_get_opt( 'variation_gallery' ) || ! woodmart_woocommerce_installed() || ! is_singular( 'product' ) ) {
			return;
		}

		$images_data = woodmart_get_vg_data();

		wp_add_inline_script( 'woodmart-functions', 'var woodmart_variation_gallery_data = ' . wp_json_encode( $images_data ) . ';' );
		wp_add_inline_script( 'woodmart-theme', 'var woodmart_variation_gallery_data = ' . wp_json_encode( $images_data ) . ';' );
	}

	add_action( 'wp_footer', 'woodmart_single_product_vg_data' );
}

// -------------------------------------------------------------------------------
// Quick view object with gallery data
// -------------------------------------------------------------------------------
if ( ! function_exists( 'woodmart_quick_view_vg_data' ) ) {
	/**
	 * Add variation gallery data to quick view.
	 *
	 * @param bool $is_quick_view Is quick view.
	 */
	function woodmart_quick_view_vg_data( $is_quick_view ) {
		if ( ! woodmart_get_opt( 'variation_gallery' ) ) {
			return;
		}

		$name = $is_quick_view ? 'woodmart_qv_variation_gallery_data' : 'woodmart_variation_gallery_data';

		echo '<script>';
			echo 'var ' . esc_attr( $name ) . ' = ' . wp_json_encode( woodmart_get_vg_data() );
		echo '</script>';
	}
}
