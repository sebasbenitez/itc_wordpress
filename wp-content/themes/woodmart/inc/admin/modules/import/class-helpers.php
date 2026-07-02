<?php
/**
 * Import helpers.
 *
 * @package woodmart
 */

namespace XTS\Admin\Modules\Import;

use XTS\Singleton;

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * Import helpers.
 */
class Helpers extends Singleton {
	/**
	 * Current page builder.
	 *
	 * @var string
	 */
	public $page_builder;

	/**
	 * Init.
	 */
	public function init() {}

	/**
	 * Send error.
	 *
	 * @param string $message Message.
	 */
	public function send_error_message( $message ) {
		$this->send_message( 'error', $message );
	}

	/**
	 * Send success.
	 *
	 * @param string $message Message.
	 */
	public function send_success_message( $message ) {
		$this->send_message( 'success', $message );
	}

	/**
	 * Send message.
	 *
	 * @param string $status  Status.
	 * @param string $message Message.
	 */
	public function send_message( $status, $message ) {
		echo wp_json_encode(
			array(
				'status'  => $status,
				'message' => $message,
			)
		);
	}

	/**
	 * Get file data.
	 *
	 * @param string $path File path.
	 *
	 * @return false|string
	 */
	public function get_local_file_content( $path ) {
		ob_start();
		include $path;

		return ob_get_clean();
	}

	/**
	 * Get file path.
	 *
	 * @param string $file_name File name.
	 * @param string $version   Version name.
	 *
	 * @return false|string
	 */
	public function get_file_path( $file_name, $version ) {
		$file = $this->get_version_folder_path( $version ) . $file_name;

		if ( ! file_exists( $file ) ) {
			return false;
		}

		return $file;
	}

	/**
	 * Get all links for current page builder only.
	 *
	 * @return array
	 */
	private function get_links() {
		$links           = array(
			'uploads' => array(),
			'simple'  => array(),
		);
		$current_builder = $this->get_page_builder();
		$versions        = woodmart_get_config( 'versions' );

		if ( ! is_array( $versions ) ) {
			return $links;
		}

		foreach ( $versions as $version_data ) {
			if ( ! is_array( $version_data ) ) {
				continue;
			}

			if ( isset( $version_data['links'] ) && is_array( $version_data['links'] ) ) {
				$link = $version_data['links'][ $current_builder ] ?? null;
				if ( $link ) {
					$this->push_link( $links['simple'], $link );
				}
			}

			if ( isset( $version_data['uploads_link'] ) && is_array( $version_data['uploads_link'] ) ) {
				$link = $version_data['uploads_link'][ $current_builder ] ?? null;
				if ( $link ) {
					$this->push_link( $links['uploads'], $link );
				}
			}
		}

		return $links;
	}

	/**
	 * Get links for a specific version only (fast path) for current page builder.
	 *
	 * @param string $version Version key.
	 * @return array
	 */
	public function get_links_for_version( $version ) {
		$links           = array(
			'uploads' => array(),
			'simple'  => array(),
		);
		$current_builder = $this->get_page_builder();
		$versions        = woodmart_get_config( 'versions' );

		if ( ! is_array( $versions ) ) {
			return $links;
		}

		$keys = array( $version );

		if ( ! str_ends_with( $version, '_base' ) ) {
			$keys[] = get_option( 'wd_import_current_base' );
		}

		foreach ( $keys as $key ) {
			if ( ! isset( $versions[ $key ] ) || ! is_array( $versions[ $key ] ) ) {
				continue;
			}

			$version_data = $versions[ $key ];

			if ( isset( $version_data['links'] ) && is_array( $version_data['links'] ) ) {
				$link = $version_data['links'][ $current_builder ] ?? null;
				if ( $link ) {
					$this->push_link( $links['simple'], $link );
				}
			}

			if ( isset( $version_data['uploads_link'] ) && is_array( $version_data['uploads_link'] ) ) {
				$link = $version_data['uploads_link'][ $current_builder ] ?? null;
				if ( $link ) {
					$this->push_link( $links['uploads'], $link );
				}
			}
		}

		return $links;
	}

	/**
	 * Add unique link with http/https variants in guaranteed order.
	 *
	 * @param array  $links Links.
	 * @param string $link  Link.
	 *
	 * @return void
	 */
	private function push_link( &$links, $link ) {
		if ( ! is_string( $link ) ) {
			return;
		}

		$link = trim( $link );

		if ( '' === $link ) {
			return;
		}

		$link = rtrim( $link, '/' ) . '/';

		// Always add in consistent order: https first, then http
		$variants = array();
		$parsed   = wp_parse_url( $link );

		if ( ! empty( $parsed['scheme'] ) ) {
			$host_and_path = str_replace( $parsed['scheme'] . '://', '', $link );
			// Always https first, then http
			$variants[] = 'https://' . $host_and_path;
			$variants[] = 'http://' . $host_and_path;
		} else {
			// No scheme - add with https first, then http
			$variants[] = 'https://' . ltrim( $link, '/' );
			$variants[] = 'http://' . ltrim( $link, '/' );
		}

		foreach ( $variants as $v ) {
			if ( is_string( $v ) && '' !== $v && ! in_array( $v, $links, true ) ) {
				$links[] = $v;
			}
		}
	}

	/**
	 * Get version folder path.
	 *
	 * @param string $version Version name.
	 *
	 * @return string
	 */
	public function get_version_folder_path( $version ) {
		return WOODMART_THEMEROOT . '/inc/admin/modules/import/dummy-data/' . $version . '/';
	}

	/**
	 * Replace links in imported data.
	 *
	 * @since 1.0.0
	 *
	 * @param string      $data    Data.
	 * @param string      $replace Replace.
	 * @param string|null $version Version key to limit processed links (optional).
	 *
	 * @return string|string[]
	 */
	public function links_replace( $data, $replace = '\\/', $version = null ) {
		if ( $version ) {
			$links = $this->get_links_for_version( $version );
		} else {
			$links = $this->get_links();
		}

		$url_data = wp_upload_dir();
		foreach ( $links['uploads'] as $link ) {
			$data = str_replace( str_replace( '/', $replace, $link ), str_replace( '/', $replace, $url_data['baseurl'] . '/' ), $data );
		}

		foreach ( $links['simple'] as $link ) {
			$data = str_replace( str_replace( '/', $replace, $link ), str_replace( '/', $replace, get_home_url() . '/' ), $data );
		}

		// If there are still remote demo links left in content, apply fallback replacements as a second pass.
		if ( str_contains( $data, 'dummy.xtemos.com' ) || str_contains( $data, 'woodmart.xtemos.com' ) ) {
			$fallbacks = $this->get_fallback_links();
			foreach ( $fallbacks['uploads'] as $link ) {
				$data = str_replace( str_replace( '/', $replace, $link ), str_replace( '/', $replace, $url_data['baseurl'] . '/' ), $data );
			}

			foreach ( $fallbacks['simple'] as $link ) {
				$data = str_replace( str_replace( '/', $replace, $link ), str_replace( '/', $replace, get_home_url() . '/' ), $data );
			}
		}

		return $data;
	}

	/**
	 * Return fallback links array (simple + uploads) without merging into demo links.
	 *
	 * @return array
	 */
	private function get_fallback_links() {
		$links = array(
			'uploads' => array(),
			'simple'  => array(),
		);

		$fallback_hosts = array(
			'dummy.xtemos.com/woodmart2/',
			'woodmart.xtemos.com/wp-content/uploads/',
			'dummy.xtemos.com/',
			'woodmart.xtemos.com/',
		);

		foreach ( $fallback_hosts as $host ) {
			$host = rtrim( $host, '/' ) . '/';

			$links['simple'][] = 'https://' . $host;
			$links['simple'][] = 'http://' . $host;

			if ( str_contains( $host, 'wp-content' ) ) {
				$links['uploads'][] = 'https://' . $host;
				$links['uploads'][] = 'http://' . $host;
			}
		}

		return $links;
	}

	/**
	 * Get imported data.
	 *
	 * @since 1.0.0
	 *
	 * @param string $version Version name.
	 *
	 * @return array
	 */
	public function get_imported_data( $version ) {
		if ( in_array( $version . '_base', $this->get_base_version(), true ) ) {
			$base = get_option( 'wd_imported_data_' . $version . '_base' );
		} else {
			$base = get_option( 'wd_imported_data_base' );
		}

		$demo = get_option( 'wd_imported_data_' . $version );

		if ( in_array( $version, $this->get_base_version(), true ) || str_starts_with( $version, 'floating-block-' ) || str_starts_with( $version, 'popup-' ) || str_starts_with( $version, 'layout-' ) || str_starts_with( $version, 'custom_label-' ) ) {
			return $demo;
		}

		if ( $demo && $base ) {
			return array_replace_recursive( $base, $demo );
		} else {
			return array();
		}
	}

	/**
	 * Get current builder.
	 *
	 * @return string
	 */
	public function get_page_builder() {
		if ( ! $this->page_builder ) {
			$this->set_page_builder( 'native' === woodmart_get_opt( 'current_builder' ) ? 'gutenberg' : woodmart_get_current_page_builder() );
		}

		return $this->page_builder;
	}

	/**
	 * Set current builder.
	 *
	 * @param string $builder Builder.
	 * @return void
	 */
	public function set_page_builder( $builder ) {
		$this->page_builder = $builder;
	}

	/**
	 * Get base version for import.
	 *
	 * @return array
	 */
	public function get_base_version() {
		return array( 'base', 'megamarket_base', 'accessories_base', 'mega-electronics_base', 'furniture2_base', 'plants_base', 'kids_base', 'games_base-light', 'games_base-dark', 'organic-farm_base', 'pills_base', 'pottery_base', 'vegetables_base', 'makeup_base', 'marketplace2_base', 't-shirts_base', 'handmade-bags_base', 'vinyls_base', 'pets_base', 'christmas-2_base', 'merchandise_base', 'perfumes_base', 'fashion-2_base', 'electronics-3_base', 'keyboards_base', 'edc_base', 'jewellery-2_base' );
	}
}
