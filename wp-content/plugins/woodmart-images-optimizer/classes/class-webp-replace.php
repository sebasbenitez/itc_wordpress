<?php
/**
 * WebP Replace class for WoodMart Images Optimizer.
 *
 * @package WoodMart\ImagesOptimizer
 * @since 1.0.0
 */

namespace WoodMart\ImagesOptimizer;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Replace images with WebP versions if they exist.
 *
 * @since 1.0.0
 */
class WebP_Replace {

	/**
	 * Cache for upload directory info to avoid repeated wp_upload_dir() calls.
	 *
	 * @var array|null
	 */
	private $upload_dir_cache = null;

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Hook into WordPress image filters.
		add_action( 'init', array( $this, 'init' ), 120 );
	}

	/**
	 * Initialize WebP replacement hooks.
	 *
	 * @return void
	 */
	public function init() {
		// Check if WoodMart theme is active.
		if ( ! function_exists( 'woodmart_get_opt' ) ) {
			return;
		}

		// Enable WebP replacement if WebP generation is enabled.
		$enable_webp_generation = woodmart_get_opt( 'woodmart_optimizer_generate_webp', false );

		/**
		 * Filter to allow/prevent WebP replacement.
		 * By default, WebP replacement is enabled when WebP generation is enabled.
		 *
		 * @since 1.0.0
		 * @param bool $allow True to allow WebP replacement (default based on WebP generation setting).
		 */
		$allow = apply_filters( 'woodmart_optimizer_allow_webp_replace', $enable_webp_generation );

		if ( ! $allow || is_admin() ) {
			return;
		}

		// Avatar images.
		add_filter( 'get_avatar', array( $this, 'replace_image_html' ), 10 );

		// Instagram and custom images.
		add_filter( 'woodmart_image', array( $this, 'replace_image_html' ), 10, 1 );

		// WPBakery generated images.
		add_filter( 'vc_wpb_getimagesize', array( $this, 'replace_wpbakery_image' ), 10, 3 );

		// Products, blog, standard WordPress images.
		add_filter( 'wp_get_attachment_image_attributes', array( $this, 'replace_image_attributes' ), 10, 3 );

		// Elementor images.
		add_filter( 'elementor/image_size/get_attachment_image_html', array( $this, 'replace_image_html' ), 10, 4 );

		// Gutenberg images.
		add_filter( 'wp_content_img_tag', array( $this, 'replace_image_html' ), 20, 3 );

		// Post thumbnail.
		add_filter( 'post_thumbnail_html', array( $this, 'replace_image_html' ), 10, 5 );

		// Image downsize for custom sizes.
		add_filter( 'image_downsize', array( $this, 'replace_image_downsize' ), 10, 3 );
	}

	/**
	 * Replace image HTML with WebP version if exists.
	 *
	 * @param string $html Image HTML.
	 * @return string Modified HTML.
	 */
	public function replace_image_html( $html ) {
		if ( empty( $html ) || ! is_string( $html ) ) {
			return $html;
		}

		// Skip if already processed or no img tag found.
		if ( strpos( $html, 'data-webp-processed' ) !== false || strpos( $html, '<img' ) === false ) {
			return $html;
		}

		// Skip images with no-webp class.
		if ( strpos( $html, 'woodmart-no-webp' ) !== false ) {
			return $html;
		}

		// Find and replace src attributes.
		$modified_html = $this->process_html_images( $html );

		return $modified_html;
	}

	/**
	 * Process HTML to replace image URLs with WebP versions.
	 *
	 * @param string $html HTML content.
	 * @return string Modified HTML.
	 */
	protected function process_html_images( $html ) {
		// Single combined pattern for better performance - matches src, data-src, and data-lazy-src in one pass.
		$html = preg_replace_callback(
			'/(<img[^>]*?\s)((?:data-lazy-)?(?:data-)?src=(["\'])([^"\']+)\3)([^>]*?>)/i',
			array( $this, 'replace_src_callback' ),
			$html
		);

		// Single combined pattern for srcset attributes.
		$html = preg_replace_callback(
			'/(<img[^>]*?\s)((?:data-lazy-)?(?:data-)?srcset=(["\'])([^"\']+)\3)([^>]*?>)/i',
			array( $this, 'replace_srcset_callback' ),
			$html
		);

		return $html;
	}

	/**
	 * Callback to replace src attribute with WebP version.
	 *
	 * @param array $matches Regex matches.
	 * @return string Modified img tag.
	 */
	protected function replace_src_callback( $matches ) {
		// phpcs:disable WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter
		$before_attr = $matches[1];
		$full_attr   = $matches[2];
		$quote       = $matches[3];
		$url         = $matches[4];
		$after_attr  = $matches[5];
		// phpcs:enable WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter

		// Get WebP URL if exists.
		$webp_url = $this->get_webp_url( $url );

		if ( $webp_url ) {
			// Replace the URL in the attribute.
			$new_attr = str_replace( $url, $webp_url, $full_attr );
			return $before_attr . $new_attr . $after_attr;
		}

		return $matches[0];
	}

	/**
	 * Callback to replace srcset attribute with WebP versions.
	 *
	 * @param array $matches Regex matches.
	 * @return string Modified img tag.
	 */
	protected function replace_srcset_callback( $matches ) {
		// phpcs:disable WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter
		$before_attr = $matches[1];
		$full_attr   = $matches[2];
		$quote       = $matches[3];
		$srcset      = $matches[4];
		$after_attr  = $matches[5];
		// phpcs:enable WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter

		// Process srcset.
		$new_srcset = $this->replace_srcset( $srcset );

		if ( $new_srcset !== $srcset ) {
			$new_attr = str_replace( $srcset, $new_srcset, $full_attr );
			return $before_attr . $new_attr . $after_attr;
		}

		return $matches[0];
	}

	/**
	 * Replace URLs in srcset with WebP versions.
	 *
	 * @param string $srcset Srcset attribute value.
	 * @return string Modified srcset.
	 */
	protected function replace_srcset( $srcset ) {
		// phpcs:disable WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter
		$srcset_items   = explode( ',', $srcset );
		$modified_items = array();
		$has_changes    = false;
		// phpcs:enable WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter

		foreach ( $srcset_items as $item ) {
			// phpcs:disable WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter
			$item  = trim( $item );
			$parts = preg_split( '/\s+/', $item );
			// phpcs:enable WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter

			if ( ! empty( $parts[0] ) ) {
				// phpcs:disable WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter
				$url        = $parts[0];
				$descriptor = isset( $parts[1] ) ? $parts[1] : '';
				// phpcs:enable WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter

				$webp_url = $this->get_webp_url( $url );

				if ( $webp_url ) {
					// phpcs:disable WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter
					$modified_items[] = $webp_url . ( $descriptor ? ' ' . $descriptor : '' );
					$has_changes      = true;
					// phpcs:enable WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter
				} else {
					$modified_items[] = $item;
				}
			}
		}

		return $has_changes ? implode( ', ', $modified_items ) : $srcset;
	}

	/**
	 * Replace image attributes with WebP version.
	 *
	 * @param array        $attr Attributes.
	 * @param object|array $attachment Attachment object.
	 * @param string       $size Image size.
	 * @return array Modified attributes.
	 */
	public function replace_image_attributes( $attr, $attachment, $size ) {
		// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		unset( $attachment, $size );
		// Skip if REST request or no-webp class exists.
		if ( wp_is_serving_rest_request() || ( ! empty( $attr['class'] ) && strpos( $attr['class'], 'woodmart-no-webp' ) !== false ) ) {
			return $attr;
		}

		// Replace src.
		if ( ! empty( $attr['src'] ) ) {
			$webp_url = $this->get_webp_url( $attr['src'] );
			if ( $webp_url ) {
				$attr['src'] = $webp_url;
			}
		}

		// Replace srcset.
		if ( ! empty( $attr['srcset'] ) ) {
			$new_srcset = $this->replace_srcset( $attr['srcset'] );
			if ( $new_srcset !== $attr['srcset'] ) {
				$attr['srcset'] = $new_srcset;
			}
		}

		return $attr;
	}

	/**
	 * Replace WPBakery image with WebP version.
	 *
	 * @param array   $img Image data.
	 * @param integer $attach_id Attachment ID.
	 * @param array   $params Parameters.
	 * @return array Modified image data.
	 */
	public function replace_wpbakery_image( $img, $attach_id, $params ) {
		// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		unset( $attach_id, $params );
		if ( ! empty( $img['thumbnail'] ) ) {
			$img['thumbnail'] = $this->replace_image_html( $img['thumbnail'] );
		}

		return $img;
	}

	/**
	 * Replace image downsize with WebP version.
	 *
	 * @param false|array $downsize Array of image data, or false.
	 * @param int         $id Attachment ID.
	 * @param string      $size Image size.
	 * @return false|array Modified image data.
	 */
	public function replace_image_downsize( $downsize, $id, $size ) {
		// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		unset( $id, $size );
		// Only process if downsize is an array with URL.
		// phpcs:disable WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter
		if ( ! is_array( $downsize ) || empty( $downsize[0] ) ) {
			return $downsize;
		}

		$url      = $downsize[0];
		$webp_url = $this->get_webp_url( $url );
		// phpcs:enable WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter

		if ( $webp_url ) {
			$downsize[0] = $webp_url;
		}

		return $downsize;
	}

	/**
	 * Get WebP URL if WebP version exists.
	 *
	 * @param string $url Original image URL.
	 * @return string|false WebP URL or false if not exists.
	 */
	protected function get_webp_url( $url ) {
		if ( empty( $url ) ) {
			return false;
		}

		// Skip if already WebP.
		if ( preg_match( '/\.webp$/i', $url ) ) {
			return false;
		}

		// Check if it's a supported image format.
		if ( ! preg_match( '/\.(jpg|jpeg|png)$/i', $url ) ) {
			return false;
		}

		// Skip external URLs unless filter allows them.
		if ( ! $this->is_local_url( $url ) && ! apply_filters( 'woodmart_optimizer_webp_replace_external', false ) ) {
			return false;
		}

		// Convert URL to path.
		$path = $this->url_to_path( $url );

		if ( ! $path ) {
			return false;
		}

		// Check if WebP version exists.
		$webp_path = $path . '.webp';

		if ( ! file_exists( $webp_path ) ) {
			return false;
		}

		// Convert path back to URL.
		$webp_url = $this->path_to_url( $webp_path );

		/**
		 * Filter the WebP URL before returning.
		 *
		 * @since 1.0.0
		 * @param string $webp_url WebP URL.
		 * @param string $url Original URL.
		 * @param string $webp_path WebP file path.
		 */
		return apply_filters( 'woodmart_optimizer_webp_url', $webp_url, $url, $webp_path );
	}

	/**
	 * Check if URL is local to the site.
	 *
	 * @param string $url URL to check.
	 * @return bool True if local, false otherwise.
	 */
	protected function is_local_url( $url ) {
		// Initialize upload directory cache once per request.
		if ( null === $this->upload_dir_cache ) {
			$this->upload_dir_cache = array(
				'site_url'    => get_site_url(),
				'uploads_url' => wp_upload_dir()['baseurl'],
			);
		}

		$site_url    = $this->upload_dir_cache['site_url'];
		$uploads_url = $this->upload_dir_cache['uploads_url'];

		// Check if URL starts with site URL or uploads URL.
		if ( strpos( $url, $site_url ) === 0 || strpos( $url, $uploads_url ) === 0 ) {
			return true;
		}

		// Check if it's a relative URL.
		if ( strpos( $url, '/' ) === 0 && strpos( $url, '//' ) !== 0 ) {
			return true;
		}

		return false;
	}

	/**
	 * Convert a file URL to an absolute path.
	 *
	 * @param string $url File URL.
	 * @return string|false File path or false on failure.
	 */
	protected function url_to_path( $url ) {
		// phpcs:disable WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter
		static $uploads_url;
		static $uploads_dir;
		static $site_url;
		static $abspath;
		// phpcs:enable WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter

		if ( ! isset( $uploads_url ) ) {
			$upload_dir = wp_upload_dir();
			$uploads_url = set_url_scheme( $upload_dir['baseurl'] );
			$uploads_dir = $upload_dir['basedir'];
			$site_url = set_url_scheme( get_site_url() );
			$abspath = ABSPATH;
		}

		$url = set_url_scheme( $url );

		// Handle uploads directory.
		if ( stripos( $url, $uploads_url ) === 0 ) {
			return str_ireplace( $uploads_url, $uploads_dir, $url );
		}

		// Handle site root.
		if ( stripos( $url, $site_url ) === 0 ) {
			return str_ireplace( $site_url, rtrim( $abspath, '/' ), $url );
		}

		// Handle protocol-relative and absolute path URLs.
		if ( strpos( $url, '/' ) === 0 ) {
			return rtrim( $abspath, '/' ) . $url;
		}

		return false;
	}

	/**
	 * Convert a file path to URL.
	 *
	 * @param string $path File path.
	 * @return string|false File URL or false on failure.
	 */
	protected function path_to_url( $path ) {
		// phpcs:disable WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter
		static $uploads_url;
		static $uploads_dir;
		static $site_url;
		static $abspath;
		// phpcs:enable WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter

		if ( ! isset( $uploads_url ) ) {
			$upload_dir = wp_upload_dir();
			$uploads_url = $upload_dir['baseurl'];
			$uploads_dir = $upload_dir['basedir'];
			$site_url = get_site_url();
			$abspath = ABSPATH;
		}

		// Handle uploads directory.
		if ( stripos( $path, $uploads_dir ) === 0 ) {
			return str_ireplace( $uploads_dir, $uploads_url, $path );
		}

		// Handle site root.
		if ( stripos( $path, $abspath ) === 0 ) {
			return str_ireplace( rtrim( $abspath, '/' ), $site_url, $path );
		}

		return false;
	}
}
