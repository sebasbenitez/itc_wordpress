<?php
/**
 * Helper utilities for WoodMart Images Optimizer plugin.
 *
 * @package WoodMart\ImagesOptimizer
 */

namespace WoodMart\ImagesOptimizer;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class for common helper functions used across the plugin.
 */
class Helpers {

	/**
	 * Check if the image type is supported for optimization.
	 *
	 * @param string $file_path     Path to the image file.
	 * @param int    $attachment_id Attachment ID.
	 * @return bool True if supported, false otherwise.
	 */
	public static function is_supported_image_type( $file_path, $attachment_id ) {
		// Check if this is actually an image attachment.
		if ( ! \wp_attachment_is_image( $attachment_id ) ) {
			return false;
		}

		// Get file extension.
		$file_extension = strtolower( pathinfo( $file_path, PATHINFO_EXTENSION ) );

		// Define supported image formats.
		$supported_formats = array(
			'jpg',
			'jpeg',
			'png',
		);

		// Check if the file extension is supported.
		if ( ! in_array( $file_extension, $supported_formats, true ) ) {
			return false;
		}

		// Additional check using WordPress function to get mime type.
		$mime_type = \get_post_mime_type( $attachment_id );
		$supported_mime_types = array(
			'image/jpeg',
			'image/jpg',
			'image/png',
		);

		return in_array( $mime_type, $supported_mime_types, true );
	}

	/**
	 * Format file size in human-readable format.
	 *
	 * @param int $bytes File size in bytes.
	 * @return string Formatted file size.
	 */
	public static function format_file_size( $bytes ) {
		$units = array( 'B', 'KB', 'MB', 'GB' );
		$bytes = max( $bytes, 0 );
		$pow   = floor( ( $bytes ? log( $bytes ) : 0 ) / log( 1024 ) );
		$pow   = min( $pow, count( $units ) - 1 );
		$bytes /= pow( 1024, $pow );
		return round( $bytes, 2 ) . ' ' . $units[ $pow ];
	}
}
