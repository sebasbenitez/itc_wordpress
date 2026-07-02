<?php
/**
 * Functions for the Woodmart Core plugin.
 *
 * @package woodmart
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_get_core_path' ) ) {
	/**
	 * Get the path to the Woodmart Core plugin.
	 *
	 * @return string The path to the Woodmart Core plugin.
	 */
	function woodmart_get_core_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}
}

if ( ! function_exists( 'woodmart_compress' ) ) {
	/**
	 * Compress a variable using base64 encoding.
	 *
	 * @param mixed $variable The variable to compress.
	 * @return string The compressed variable.
	 */
	function woodmart_compress( $variable ) {
		return base64_encode( $variable ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
	}
}

if ( ! function_exists( 'woodmart_get_file' ) ) {
	/**
	 * Get the contents of a file.
	 *
	 * @param string $variable The file path.
	 * @return string The file contents.
	 */
	function woodmart_get_file( $variable ) {
		return file_get_contents( $variable ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	}
}

if ( ! function_exists( 'woodmart_decompress' ) ) {
	/**
	 * Decompress a variable using base64 decoding.
	 *
	 * @param string $variable The variable to decompress.
	 * @return string The decompressed variable.
	 */
	function woodmart_decompress( $variable ) {
		return $variable ? base64_decode( $variable ) : ''; // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
	}
}

if ( ! function_exists( 'woodmart_get_svg' ) ) {
	/**
	 * Get the contents of an SVG file, with optional caching.
	 *
	 * @param string $file The path to the SVG file.
	 * @return string The contents of the SVG file.
	 */
	function woodmart_get_svg( $file ) {
		if ( ! apply_filters( 'woodmart_svg_cache', true ) ) {
			return file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		}

		$file_path = array_reverse( explode( '/', $file ) );
		$slug      = 'wdm-svg-' . $file_path[2] . '-' . $file_path[1] . '-' . $file_path[0];
		$content   = get_transient( $slug );

		if ( ! $content ) {
			$file_get_contents = file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

			if ( strstr( $file_get_contents, '<svg' ) ) {
				$content = woodmart_compress( $file_get_contents );
				set_transient( $slug, $content, apply_filters( 'woodmart_svg_cache_time', 60 * 60 * 24 * 7 ) );
			}
		}

		return woodmart_decompress( $content );
	}
}

if ( ! function_exists( 'getallheaders' ) ) {
	/**
	 * Get all HTTP headers.
	 *
	 * @return array An associative array of HTTP headers.
	 */
	function getallheaders() { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
		$headers = array();

		foreach ( $_SERVER as $name => $value ) {
			if ( 'HTTP_' === substr( $name, 0, 5 ) ) {
				$headers[ str_replace( ' ', '-', ucwords( strtolower( str_replace( '_', ' ', substr( $name, 5 ) ) ) ) ) ] = $value;
			}
		}

		return $headers;
	}
}

if ( ! function_exists( 'woodmart_get_importer' ) ) {
	/**
	 * Get an instance of the Woodmart importer.
	 *
	 * @return WOODMART_CORE\Importer\Import An instance of the Woodmart importer.
	 */
	function woodmart_get_importer() {
		require_once ABSPATH . 'wp-admin/includes/import.php';

		if ( ! class_exists( 'WP_Importer', false ) ) {
			$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';

			if ( file_exists( $class_wp_importer ) ) {
				require $class_wp_importer;
			}
		}

		if ( ! class_exists( 'WOODMART_CORE\Importer\Import', false ) ) {
			require_once woodmart_get_core_path() . '/importer/parsers/class-wxr-parser.php';
			require_once woodmart_get_core_path() . '/importer/parsers/class-wxr-parser-simplexml.php';
			require_once woodmart_get_core_path() . '/importer/parsers/class-wxr-parser-xml.php';
			require_once woodmart_get_core_path() . '/importer/parsers/class-wxr-parser-regex.php';

			require_once woodmart_get_core_path() . '/importer/class-import.php';
		}

		return new WOODMART_CORE\Importer\Import();
	}
}

add_filter( 'widget_text', 'do_shortcode' );
