<?php
/**
 * Picture Display class for WoodMart Images Optimizer.
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
 * Display WebP images on the site with <picture> tags.
 *
 * @since 1.0.0
 */
class Picture_Display {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'template_redirect', array( $this, 'start_content_process' ) );
		// Add compatibility for woodmart-no-webp class.
		add_filter( 'woodmart_single_product_gallery_image_class', array( $this, 'add_no_webp_class' ) );

		add_action( 'woodmart_get_webp_image_srcset', array( $this, 'get_webp_images' ) );
		add_action( 'woodmart_get_webp_image_src', array( $this, 'get_webp_image' ) );
	}

	/**
	 * Add woodmart-no-webp class to single product gallery images.
	 *
	 * @param string|array $classes Existing classes.
	 * @return string|array Modified classes.
	 */
	public function add_no_webp_class( $classes ) {
		if ( ! apply_filters( 'woodmart_optimizer_allow_picture_tags', false ) ) {
			return $classes;
		}

		if ( is_string( $classes ) ) {
			$classes .= ' woodmart-no-webp';
		} elseif ( is_array( $classes ) ) {
			$classes[] = 'woodmart-no-webp';
		}
		return $classes;
	}

	/**
	 * Start buffering the page content.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function start_content_process() {
		// Only process on frontend
		if ( is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
			return;
		}

		// Check if WoodMart theme is active
		if ( ! function_exists( 'woodmart_get_opt' ) ) {
			return;
		}



		/**
		 * Filter to allow/prevent the replacement of <img> tags with <picture> tags.
		 *
		 * @since 1.0.0
		 * @param bool $allow True to allow picture tags (default). False to prevent.
		 */
		$allow = apply_filters( 'woodmart_optimizer_allow_picture_tags', false );

		if ( ! $allow ) {
			return;
		}

		ob_start( array( $this, 'maybe_process_buffer' ) );
	}

	/**
	 * Maybe process the page content.
	 *
	 * @since 1.0.0
	 * @param string $buffer The buffer content.
	 * @return string
	 */
	public function maybe_process_buffer( $buffer ) {
		if ( ! $this->is_html( $buffer ) ) {
			return $buffer;
		}

		if ( strlen( $buffer ) <= 255 ) {
			// Buffer length must be > 255 (IE does not read pages under 255 chars).
			return $buffer;
		}

		$buffer = $this->process_content( $buffer );

		/**
		 * Filter the page content after WoodMart Images Optimizer processing.
		 *
		 * @since 1.0.0
		 * @param string $buffer The page content.
		 */
		$buffer = (string) apply_filters( 'woodmart_optimizer_buffer', $buffer );

		return $buffer;
	}

	/**
	 * Process the content to replace img tags with picture tags.
	 *
	 * @since 1.0.0
	 * @param string $content The content.
	 * @return string
	 */
	public function process_content( $content ) {
		// Remove existing picture tags to avoid nested processing
		$html_no_picture_tags = $this->remove_picture_tags( $content );
		$images = $this->get_images( $html_no_picture_tags );

		if ( ! $images ) {
			return $content;
		}

		foreach ( $images as $image ) {
			if ( $this->has_webp_version( $image ) ) {
				$picture_tag = $this->build_picture_tag( $image );
				$content = str_replace( $image['tag'], $picture_tag, $content );
			}
		}

		return $content;
	}

	/**
	 * Remove pre-existing <picture> tags to avoid nested processing.
	 *
	 * @since 1.0.0
	 * @param string $html Content of the page.
	 * @return string HTML content without pre-existing <picture> tags.
	 */
	private function remove_picture_tags( $html ) {
		$replace = preg_replace( '#<picture[^>]*>.*?<\/picture\s*>#mis', '', $html );

		if ( null === $replace ) {
			return $html;
		}

		return $replace;
	}

	/**
	 * Get a list of images in content.
	 *
	 * @since 1.0.0
	 * @param string $content The content.
	 * @return array
	 */
	protected function get_images( $content ) {
		// Remove HTML comments
		$content = preg_replace( '/<!--(.*)-->/Uis', '', $content );

		if ( ! preg_match_all( '/<img\s[^>]*>/isU', $content, $matches ) ) {
			return array();
		}

		$images = array_map( array( $this, 'process_image' ), $matches[0] );
		$images = array_filter( $images );

		/**
		 * Filter the images to display with a <picture> tag.
		 *
		 * @since 1.0.0
		 * @param array  $images A list of processed image data arrays.
		 * @param string $content The page content.
		 */
		$images = apply_filters( 'woodmart_optimizer_picture_images_to_display', $images, $content );

		if ( ! $images || ! is_array( $images ) ) {
			return array();
		}

		return $images;
	}

	/**
	 * Process an image tag and extract data.
	 *
	 * @since 1.0.0
	 * @param string $image An image HTML tag.
	 * @return array|false Array of data if processable, false otherwise.
	 */
	protected function process_image( $image ) {
		// Simplified regex pattern to match HTML attributes
		$atts_pattern = '/([\w-]+)\s*=\s*["\']([^"\']*)["\']?/';

		if ( ! preg_match_all( $atts_pattern, $image, $matches, PREG_SET_ORDER ) ) {
			return false;
		}

		$attributes = array();
		foreach ( $matches as $match ) {
			$attributes[ $match[1] ] = $match[2];
		}

		// Skip images with no-webp class
		if ( ! empty( $attributes['class'] ) && strpos( $attributes['class'], 'woodmart-no-webp' ) !== false ) {
			return false;
		}

		// Find src attribute
		$src_source = false;
		foreach ( array( 'data-lazy-src', 'data-src', 'src' ) as $src_attr ) {
			if ( ! empty( $attributes[ $src_attr ] ) ) {
				$src_source = $src_attr;
				break;
			}
		}

		if ( ! $src_source ) {
			return false;
		}

		// Check if it's a supported image format
		$supported_extensions = 'jpg|jpeg|png|webp';
		if ( ! preg_match( '@^(?<src>(?:(?:https?:)?//|/).+\.(?<extension>' . $supported_extensions . '))(?<query>\?.*)?$@i', $attributes[ $src_source ], $src_match ) ) {
			return false;
		}

		$data = array(
			'tag'              => $image,
			'attributes'       => $attributes,
			'src_attribute'    => $src_source,
			'src'              => array(
				'url'  => $attributes[ $src_source ],
				'path' => $this->url_to_path( $attributes[ $src_source ] ),
			),
			'srcset_attribute' => false,
			'srcset'           => array(),
		);

		// Check for WebP version of main image
		if ( $data['src']['path'] ) {
			$webp_path = $data['src']['path'] . '.webp';
			$data['src']['webp_exists'] = file_exists( $webp_path );
			$data['src']['webp_url'] = $data['src']['webp_exists'] ? $this->path_to_url( $webp_path ) : '';
		}

		// Handle srcset attribute
		$srcset_source = false;
		foreach ( array( 'data-lazy-srcset', 'data-srcset', 'srcset' ) as $srcset_attr ) {
			if ( ! empty( $attributes[ $srcset_attr ] ) ) {
				$srcset_source = $srcset_attr;
				break;
			}
		}

		if ( $srcset_source ) {
			$data['srcset_attribute'] = $srcset_source;
			$data['srcset']           = $this->generate_webp_images( $attributes[ $srcset_source ] );
		}

		/**
		 * Filter a processed image tag.
		 *
		 * @since 1.0.0
		 * @param array  $data  An array of data for this image.
		 * @param string $image An image HTML tag.
		 */
		$data = apply_filters( 'woodmart_optimizer_picture_process_image', $data, $image );

		return $data;
	}

	/**
	 * Check if an image has WebP versions available.
	 *
	 * @since 1.0.0
	 * @param array $image Image data array.
	 * @return bool
	 */
	protected function has_webp_version( $image ) {
		// Check main src
		if ( ! empty( $image['src']['webp_exists'] ) ) {
			return true;
		}

		// Check srcset items
		if ( ! empty( $image['srcset'] ) ) {
			foreach ( $image['srcset'] as $srcset_item ) {
				if ( ! empty( $srcset_item['webp_exists'] ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Build the <picture> tag.
	 *
	 * @since 1.0.0
	 * @param array $image Image data array.
	 * @return string
	 */
	protected function build_picture_tag( $image ) {
		$to_remove = array(
			'alt'              => '',
			'height'           => '',
			'width'            => '',
			'data-lazy-src'    => '',
			'data-src'         => '',
			'src'              => '',
			'data-lazy-srcset' => '',
			'data-srcset'      => '',
			'srcset'           => '',
			'data-lazy-sizes'  => '',
			'data-sizes'       => '',
			'sizes'            => '',
		);

		$picture_attributes = array_diff_key( $image['attributes'], $to_remove );

		/**
		 * Filter the attributes for the <picture> tag.
		 *
		 * @since 1.0.0
		 * @param array $picture_attributes Picture tag attributes.
		 * @param array $image Image data.
		 */
		$picture_attributes = apply_filters( 'woodmart_optimizer_picture_attributes', $picture_attributes, $image );

		$output = '<picture' . $this->build_attributes( $picture_attributes ) . ">\n";
		$output .= $this->build_source_tag( $image );
		$output .= $this->build_img_tag( $image );
		$output .= "</picture>\n";

		return $output;
	}

	/**
	 * Build the <source> tag for WebP.
	 *
	 * @since 1.0.0
	 * @param array $image Image data array.
	 * @return string
	 */
	protected function build_source_tag( $image ) {
		$srcset_source = ! empty( $image['srcset_attribute'] ) ? $image['srcset_attribute'] : $image['src_attribute'] . 'set';
		
		$attributes = array(
			'type' => 'image/webp',
			$srcset_source => array(),
		);

		// Build WebP srcset
		if ( ! empty( $image['srcset'] ) ) {
			foreach ( $image['srcset'] as $srcset_item ) {
				if ( ! empty( $srcset_item['webp_exists'] ) && ! empty( $srcset_item['webp_url'] ) ) {
					$attributes[ $srcset_source ][] = $srcset_item['webp_url'] . ' ' . $srcset_item['descriptor'];
				}
			}
		}

		// Fallback to main image WebP if no srcset WebP found
		if ( empty( $attributes[ $srcset_source ] ) && ! empty( $image['src']['webp_exists'] ) && ! empty( $image['src']['webp_url'] ) ) {
			$attributes[ $srcset_source ][] = $image['src']['webp_url'];
		}

		if ( empty( $attributes[ $srcset_source ] ) ) {
			return '';
		}

		$attributes[ $srcset_source ] = implode( ', ', $attributes[ $srcset_source ] );

		// Copy sizes attributes
		foreach ( array( 'data-lazy-sizes', 'data-sizes', 'sizes' ) as $sizes_attr ) {
			if ( ! empty( $image['attributes'][ $sizes_attr ] ) ) {
				$attributes[ $sizes_attr ] = $image['attributes'][ $sizes_attr ];
			}
		}

		/**
		 * Filter the attributes for the <source> tag.
		 *
		 * @since 1.0.0
		 * @param array $attributes Source tag attributes.
		 * @param array $image Image data.
		 */
		$attributes = apply_filters( 'woodmart_optimizer_picture_source_attributes', $attributes, $image );

		return '<source' . $this->build_attributes( $attributes ) . "/>\n";
	}

	/**
	 * Build the <img> tag for the picture.
	 *
	 * @since 1.0.0
	 * @param array $image Image data array.
	 * @return string
	 */
	protected function build_img_tag( $image ) {
		$to_remove = array(
			'class' => '',
			'id'    => '',
			'style' => '',
			'title' => '',
		);

		$img_attributes = array_diff_key( $image['attributes'], $to_remove );

		/**
		 * Filter the attributes for the <img> tag inside <picture>.
		 *
		 * @since 1.0.0
		 * @param array $img_attributes Image tag attributes.
		 * @param array $image Image data.
		 */
		$img_attributes = apply_filters( 'woodmart_optimizer_picture_img_attributes', $img_attributes, $image );

		return '<img' . $this->build_attributes( $img_attributes ) . "/>\n";
	}

	/**
	 * Create HTML attributes from an array.
	 *
	 * @since 1.0.0
	 * @param array $attributes Attribute pairs.
	 * @return string
	 */
	protected function build_attributes( $attributes ) {
		if ( ! $attributes || ! is_array( $attributes ) ) {
			return '';
		}

		$output = '';
		foreach ( $attributes as $attribute => $value ) {
			if ( is_array( $value ) ) {
				$value = implode( ' ', $value );
			}
			$output .= ' ' . $attribute . '="' . esc_attr( $value ) . '"';
		}

		return $output;
	}

	/**
	 * Check if content is HTML.
	 *
	 * @since 1.0.0
	 * @param string $content The content.
	 * @return bool
	 */
	protected function is_html( $content ) {
		return preg_match( '/<\/html>/i', $content );
	}

	/**
	 * Convert a file URL to an absolute path.
	 *
	 * @since 1.0.0
	 * @param string $url File URL.
	 * @return string|false File path or false on failure.
	 */
	protected function url_to_path( $url ) {
		static $uploads_url;
		static $uploads_dir;
		static $site_url;
		static $abspath;

		if ( ! isset( $uploads_url ) ) {
			$upload_dir = wp_upload_dir();
			$uploads_url = set_url_scheme( $upload_dir['baseurl'] );
			$uploads_dir = $upload_dir['basedir'];
			$site_url = set_url_scheme( get_site_url() );
			$abspath = ABSPATH;
		}

		$url = set_url_scheme( $url );

		// Handle uploads directory
		if ( stripos( $url, $uploads_url ) === 0 ) {
			return str_ireplace( $uploads_url, $uploads_dir, $url );
		}

		// Handle site root
		if ( stripos( $url, $site_url ) === 0 ) {
			return str_ireplace( $site_url, rtrim( $abspath, '/' ), $url );
		}

		// Handle protocol-relative and absolute path URLs
		if ( strpos( $url, '/' ) === 0 ) {
			return rtrim( $abspath, '/' ) . $url;
		}

		return false;
	}

	/**
	 * Convert a file path to URL.
	 *
	 * @since 1.0.0
	 * @param string $path File path.
	 * @return string|false File URL or false on failure.
	 */
	protected function path_to_url( $path ) {
		static $uploads_url;
		static $uploads_dir;
		static $site_url;
		static $abspath;

		if ( ! isset( $uploads_url ) ) {
			$upload_dir = wp_upload_dir();
			$uploads_url = $upload_dir['baseurl'];
			$uploads_dir = $upload_dir['basedir'];
			$site_url = get_site_url();
			$abspath = ABSPATH;
		}

		// Handle uploads directory
		if ( stripos( $path, $uploads_dir ) === 0 ) {
			return str_ireplace( $uploads_dir, $uploads_url, $path );
		}

		// Handle site root
		if ( stripos( $path, $abspath ) === 0 ) {
			return str_ireplace( rtrim( $abspath, '/' ), $site_url, $path );
		}

		return false;
	}

	/**
	 * Get WebP image URL if it exists.
	 *
	 * @param string $src The original image URL.
	 * @return string
	 */
	public function get_webp_image( $src ) {
		$src_path = $this->url_to_path( $src );

		if ( $src_path ) {
			$webp_src_path = $src_path . '.webp';

			if ( file_exists( $webp_src_path ) ) {
				return $this->path_to_url( $webp_src_path );
			}
		}

		return $src;
	}

	/**
	 * Get WebP images for srcset attribute.
	 *
	 * @param string $srcset The srcset attribute value.
	 * @return string
	 */
	public function get_webp_images( $srcset ) {
		$generated_webp_images = $this->generate_webp_images( $srcset );

		if ( $generated_webp_images ) {
			$webp_images = array();

			foreach ( $generated_webp_images as $srcset_item ) {
				if ( $srcset_item['webp_exists'] && $srcset_item['webp_url'] ) {
					$webp_images[] = $srcset_item['webp_url'] . ' ' . $srcset_item['descriptor'];
				} else {
					// If no WebP version, keep original srcset item
					$webp_images[] = $srcset_item['url'] . ' ' . $srcset_item['descriptor'];
				}
			}

			return implode( ', ', $webp_images );
		}

		return $srcset;
	}

	/**
	 * Generate WebP image data for srcset.
	 *
	 * @param string $srcset The srcset attribute value.
	 * @return array
	 */
	protected function generate_webp_images( $srcset ) {
		$srcset_items = explode( ',', $srcset );
		$srcset_data  = array();

		if ( $srcset_items ) {
			foreach ( $srcset_items as $index => $srcset_item ) {
				$srcset_parts = preg_split( '/\s+/', trim( $srcset_item ) );

				if ( count( $srcset_parts ) > 2 ) {
					$descriptor   = array_pop( $srcset_parts );
					$srcset_parts = array( implode( ' ', $srcset_parts ), $descriptor );
				}

				if ( empty( $srcset_parts[1] ) ) {
					$srcset_parts[1] = '1x';
				}

				$srcset_url  = $srcset_parts[0];
				$srcset_path = $this->url_to_path( $srcset_url );

				$srcset_data[ $index ] = array(
					'url'         => $srcset_url,
					'path'        => $srcset_path,
					'descriptor'  => $srcset_parts[1],
					'webp_exists' => false,
					'webp_url'    => '',
				);

				// Check for WebP version of srcset image.
				if ( $srcset_path ) {
					$webp_srcset_path = $srcset_path . '.webp';

					$srcset_data[ $index ]['webp_exists'] = file_exists( $webp_srcset_path );
					$srcset_data[ $index ]['webp_url']    = $srcset_data[ $index ]['webp_exists'] ? $this->path_to_url( $webp_srcset_path ) : '';
				}
			}
		}

		return $srcset_data;
	}
}
