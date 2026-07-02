<?php
/**
 * Image Optimizer class for handling API-based image optimization.
 *
 * @package WoodMart\ImagesOptimizer
 */

namespace WoodMart\ImagesOptimizer;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Optimizer {

	// Constants for magic values
	private const MAX_POLLING_ATTEMPTS = 30;
	private const POLLING_INTERVAL = 2; // seconds
	private const THUMBNAIL_MAX_ATTEMPTS = 15;
	private const MIN_COMPRESSION_THRESHOLD = 0.5; // 0.5% minimum improvement
	private const OPTIMIZATION_META_KEY = '_xts_image_optimization';
	private const BACKUP_SUFFIX = '-backup';

	/**
	 * API client instance for external optimization.
	 *
	 * @var Api_Client
	 */
	private $api_client;

	// ========================================
	// CONSTRUCTOR & INITIALIZATION
	// ========================================

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Initialize API client with default API URL.
		$this->api_client = new Api_Client();
	}

	// ========================================
	// PUBLIC API METHODS
	// ========================================

	/**
	 * Check if WoodMart token is available.
	 *
	 * @return bool
	 */
	public function is_token_available() {
		return $this->api_client->is_token_available();
	}

	/**
	 * Get formatted quota information.
	 *
	 * @return string|false Formatted quota string or false if not available.
	 */
	public function get_formatted_quota() {
		return $this->api_client->get_formatted_quota();
	}

	/**
	 * Optimize an image file.
	 *
	 * @param string $url Path to the image file.
	 * @param int    $attachment_id WordPress attachment ID.
	 * @return array Optimization results including original and optimized file information, or error details.
	 */
	public function optimize( string $url, int $attachment_id = 0 ) {
		// Check if image is already optimized (if attachment ID provided)
		if ( $attachment_id > 0 ) {
			$existing_meta = $this->get_optimization_meta( $attachment_id );

			if ( $existing_meta && ! empty( $existing_meta['backup_exists'] ) ) {
				return array(
					'error' => true,
					'message' => 'Image has already been optimized',
					'already_optimized' => true,
					'optimization_data' => array(
						'timestamp' => $existing_meta['timestamp'] ?? null,
						'compression_ratio' => $existing_meta['compression_ratio'] ?? null,
						'file_size_before' => $existing_meta['file_size_before'] ?? null,
						'file_size_after' => $existing_meta['file_size_after'] ?? null,
						'size_reduction' => $existing_meta['size_reduction'] ?? null,
						'backup_filename' => $existing_meta['backup_filename'] ?? null,
					),
				);
			}
		}

		// Submit image for API optimization.
		$api_result = $this->api_client->submit_image( $url );
		
		if ( $api_result['error'] ) {
			// Return API error (don't save meta for API failures).
			return array(
				'error' => true,
				'message' => $api_result['message'] ?? 'API optimization failed'
			);
		}

		// API request successful - task created.
		$response_data = $api_result['data'];
		$task_id = $response_data['data']['task_id'] ?? null;

		if ( ! $task_id ) {
			return array(
				'error' => true,
				'message' => 'No task ID received from API',
			);
		}

		// Poll for task completion
		$polling_result = $this->poll_task_completion( $task_id, $url, $attachment_id );
		
		if ( $polling_result['error'] ) {
			// Save meta only for minimal optimization case
			if ( $attachment_id > 0 && ! empty( $polling_result['minimal_optimization'] ) ) {
				$this->save_optimization_meta( $attachment_id, $polling_result );
			}
			return $polling_result;
		}

		// Save successful optimization meta if attachment ID provided
		if ( $attachment_id > 0 ) {
			$this->save_optimization_meta( $attachment_id, $polling_result );
		}

		// Return completed task result
		return $polling_result;
	}

	/**
	 * Restore image from backup.
	 *
	 * @param string $original_path Path to the current image file.
	 * @param int    $attachment_id WordPress attachment ID.
	 * @return array Restoration result.
	 */
	public function restore_from_backup( string $original_path, int $attachment_id = 0 ) {
		// Validate original file exists
		if ( ! file_exists( $original_path ) ) {
			return $this->create_error_response( 'Current image file not found: ' . $original_path );
		}

		// Create backup filename
		$path_info = pathinfo( $original_path );
		$backup_path = $path_info['dirname'] . '/' . $path_info['filename'] . self::BACKUP_SUFFIX . '.' . $path_info['extension'];

		// Check if backup exists
		if ( ! file_exists( $backup_path ) ) {
			return $this->create_error_response( 'Backup file not found: ' . basename( $backup_path ) );
		}

		// Replace current file with backup
		if ( ! copy( $backup_path, $original_path ) ) {
			return $this->create_error_response( 'Failed to restore from backup' );
		}

		// Remove the backup file after successful restoration
		if ( ! unlink( $backup_path ) ) {
			// Log warning but don't fail the operation
			error_log( 'Warning: Could not remove backup file after restoration: ' . $backup_path );
		}

		// Remove WebP file if it exists
		$webp_result = array();
		if ( $attachment_id > 0 ) {
			$meta_data = $this->get_optimization_meta( $attachment_id );
			if ( $meta_data && ! empty( $meta_data['webp_created'] ) && ! empty( $meta_data['webp_filename'] ) ) {
				$webp_path = $original_path . '.webp';
				
				if ( file_exists( $webp_path ) ) {
					if ( unlink( $webp_path ) ) {
						$webp_result = array(
							'webp_removed' => true,
							'webp_filename' => basename( $webp_path ),
						);
					} else {
						$webp_result = array(
							'webp_removed' => false,
							'webp_error' => 'Failed to remove WebP file',
						);
					}
				}
			}
		}

		// Clear optimization meta data since image is restored to original
		if ( $attachment_id > 0 ) {
			$this->clear_optimization_meta( $attachment_id );
		}

		// Delete and regenerate thumbnails if attachment ID provided
		if ( $attachment_id > 0 ) {
			$this->rebuild_thumbnails( $attachment_id );
		}

		return $this->create_success_response( 'Image successfully restored from backup', [
			'backup_filename' => basename( $backup_path ),
		]);
	}

	// ========================================
	// BACKUP & META DATA METHODS
	// ========================================

	/**
	 * Check if backup exists for an image using meta data.
	 *
	 * @param int $attachment_id WordPress attachment ID.
	 * @return array Backup status information.
	 */
	public function has_backup_by_id( int $attachment_id ) {
		$meta_data = $this->get_optimization_meta( $attachment_id );
		
		if ( ! $meta_data ) {
			return array(
				'has_backup' => false,
				'message' => 'No optimization data found',
			);
		}

		$has_backup = ! empty( $meta_data['backup_exists'] );

		return array(
			'has_backup' => $has_backup,
			'backup_filename' => $has_backup ? $meta_data['backup_filename'] : null,
		);
	}

	/**
	 * Check if backup exists for an image.
	 *
	 * @param string $original_path Path to the image file.
	 * @return array Backup status information.
	 */
	public function has_backup( string $original_path ) {
		if ( ! file_exists( $original_path ) ) {
			return array(
				'has_backup' => false,
				'message' => 'Original file not found',
			);
		}

		$path_info = pathinfo( $original_path );
		$backup_path = $path_info['dirname'] . '/' . $path_info['filename'] . self::BACKUP_SUFFIX . '.' . $path_info['extension'];

		$has_backup = file_exists( $backup_path );

		return array(
			'has_backup' => $has_backup,
			'backup_path' => $has_backup ? $backup_path : null,
			'backup_filename' => $has_backup ? basename( $backup_path ) : null,
		);
	}

	/**
	 * Get optimization meta data for an attachment.
	 *
	 * @param int $attachment_id WordPress attachment ID.
	 * @return array|false Optimization meta data or false if not found.
	 */
	public function get_optimization_meta( int $attachment_id ) {
		$meta_data = get_post_meta( $attachment_id, self::OPTIMIZATION_META_KEY, true );
		return is_array( $meta_data ) ? $meta_data : false;
	}

	/**
	 * Clear optimization meta data for an attachment.
	 *
	 * @param int $attachment_id WordPress attachment ID.
	 */
	public function clear_optimization_meta( int $attachment_id ) {
		delete_post_meta( $attachment_id, self::OPTIMIZATION_META_KEY );
	}

	// ========================================
	// PRIVATE CORE OPTIMIZATION METHODS
	// ========================================

	/**
	 * Poll for task completion with short polling.
	 *
	 * @param string $task_id Task ID to poll.
	 * @param string $original_path Path to the original image file.
	 * @param int    $attachment_id WordPress attachment ID.
	 * @return array Task completion result or error.
	 */
	private function poll_task_completion( string $task_id, string $original_path, int $attachment_id = 0 ) {
		$result = $this->poll_task_generic( $task_id, self::MAX_POLLING_ATTEMPTS );
		
		if ( $result['error'] ) {
			return $result;
		}
		
		$task_data = $result['task_data'];
		$completion_result = array(
			'error' => false,
			'message' => 'Image optimized successfully',
		);

		// Replace original image with optimized version
		if ( ! empty( $task_data['download_url'] ) ) {
			$replacement_result = $this->replace_original_image( $original_path, $task_data, $attachment_id );
			$completion_result = array_merge( $completion_result, $replacement_result );
		}

		return $completion_result;
	}

	/**
	 * Generic polling method for task completion.
	 *
	 * @param string $task_id Task ID to poll.
	 * @param int    $max_attempts Maximum number of polling attempts.
	 * @return array Task completion result with task_data on success, or error.
	 */
	private function poll_task_generic( string $task_id, int $max_attempts ) {
		$poll_interval = self::POLLING_INTERVAL;
		
		for ( $attempt = 1; $attempt <= $max_attempts; $attempt++ ) {
			// Check task status
			$status_result = $this->api_client->check_task_status( $task_id );
			
			if ( $status_result['error'] ) {
				return $this->create_error_response( 'Failed to check task status: ' . $status_result['message'] );
			}

			$task_data = $status_result['data']['data'] ?? array();
			$status = $task_data['status'] ?? 'unknown';

			// Check if task is completed
			if ( 'completed' === $status ) {
				return array(
					'error' => false,
					'task_data' => $task_data,
				);
			}

			// Check if task failed
			if ( 'error' === $status || 'failed' === $status ) {
				return $this->create_error_response( 'Optimization task failed' );
			}

			// Sleep for poll interval (except on last attempt)
			if ( $attempt < $max_attempts ) {
				sleep( $poll_interval );
			}
		}

		// Timeout reached
		$timeout_seconds = $max_attempts * $poll_interval;
		return $this->create_error_response( "Optimization task timed out after {$timeout_seconds} seconds ({$max_attempts} attempts, {$poll_interval} second intervals)" );
	}

	/**
	 * Replace the original image with the optimized one.
	 *
	 * @param string $original_path Path to the original image.
	 * @param array  $task_data Complete optimization task data including WebP info.
	 * @param int    $attachment_id WordPress attachment ID.
	 * @return array Replacement result including backup and replacement status.
	 */
	private function replace_original_image( string $original_path, array $task_data, int $attachment_id = 0 ) {
		// Validate original file exists
		if ( ! file_exists( $original_path ) ) {
			return $this->create_error_response( 'Original image file not found: ' . $original_path );
		}

		// Process WebP image
		$webp_result = $this->process_webp_image( $task_data, $original_path );

		// Create backup
		$backup_result = $this->create_backup( $original_path );
		if ( $backup_result['error'] ) {
			return $backup_result;
		}
		$backup_path = $backup_result['backup_path'];

		// Download and replace main image
		$replacement_result = $this->download_and_replace_image( $original_path, $task_data, $backup_path );
		if ( $replacement_result['error'] ) {
			return $replacement_result;
		}

		// Handle minimal optimization case
		$minimal_check = $this->handle_minimal_optimization( $replacement_result, $backup_path );
		if ( $minimal_check['error'] ) {
			// Even if main image optimization is minimal, we should still process thumbnails
			// to generate WebP versions if WebP was successfully created for the main image
			if ( $attachment_id > 0 && ! empty( $webp_result['webp_created'] ) ) {
				$thumbnail_result = $this->rebuild_and_optimize_thumbnails( $attachment_id );
				
				// Add WebP and thumbnail results to the minimal optimization response
				$minimal_check = array_merge( $minimal_check, $webp_result );
				
				if ( ! empty( $thumbnail_result ) && ! $thumbnail_result['error'] ) {
					$minimal_check['thumbnails_regenerated'] = true;
					$minimal_check['thumbnails_message'] = $thumbnail_result['message'];
					$minimal_check['thumbnails_generated_count'] = $thumbnail_result['generated_count'];
					$minimal_check['thumbnails_generated_sizes'] = $thumbnail_result['generated_sizes'];
					$minimal_check['thumbnails_optimized'] = $thumbnail_result['thumbnails_optimized'] ?? false;
					$minimal_check['optimized_count'] = $thumbnail_result['optimized_count'] ?? 0;
					$minimal_check['optimization_results'] = $thumbnail_result['optimization_results'] ?? [];
				}
			}
			return $minimal_check;
		}

		// Handle thumbnails if attachment ID provided
		$thumbnail_result = [];
		if ( $attachment_id > 0 ) {
			$thumbnail_result = $this->rebuild_and_optimize_thumbnails( $attachment_id );
		}

		// Build final result
		$result = $this->create_success_response( 'Image successfully replaced with optimized version', [
			'backup_created' => true,
			'backup_path' => $backup_path,
			'backup_filename' => basename( $backup_path ),
			'file_size_before' => $replacement_result['file_size_before'],
			'file_size_after' => $replacement_result['file_size_after'],
			'size_reduction' => $replacement_result['size_reduction'],
			'compression_percentage' => $replacement_result['compression_percentage'],
			'download_size' => $replacement_result['download_size'],
		]);

		// Add WebP results if available
		if ( ! empty( $webp_result ) ) {
			$result = array_merge( $result, $webp_result );
		}

		// Add thumbnail regeneration results
		if ( ! empty( $thumbnail_result ) ) {
			$result['thumbnails_regenerated'] = ! $thumbnail_result['error'];
			$result['thumbnails_message'] = $thumbnail_result['message'];
			if ( ! $thumbnail_result['error'] ) {
				$result['thumbnails_generated_count'] = $thumbnail_result['generated_count'];
				$result['thumbnails_generated_sizes'] = $thumbnail_result['generated_sizes'];
			}
		}

		return $result;
	}

	/**
	 * Create backup of original image.
	 *
	 * @param string $original_path Path to the original image.
	 * @return array Backup creation result.
	 */
	private function create_backup( string $original_path ) {
		$path_info = pathinfo( $original_path );
		$backup_path = $path_info['dirname'] . '/' . $path_info['filename'] . self::BACKUP_SUFFIX . '.' . $path_info['extension'];

		// Check if backup already exists
		if ( file_exists( $backup_path ) ) {
			// If backup exists, we assume it's a valid backup from a previous attempt
			// and allow the process to continue using it as the original source.
			return $this->create_success_response( 'Backup file already exists', [
				'backup_path' => $backup_path,
			]);
		}

		// Create backup of original file
		if ( ! copy( $original_path, $backup_path ) ) {
			return $this->create_error_response( 'Failed to create backup file' );
		}

		return $this->create_success_response( 'Backup created successfully', [
			'backup_path' => $backup_path,
		]);
	}

	/**
	 * Download optimized image and replace original.
	 *
	 * @param string $original_path Path to the original image.
	 * @param array  $task_data Task data with download URL.
	 * @param string $backup_path Path to backup file.
	 * @return array Download and replacement result.
	 */
	private function download_and_replace_image( string $original_path, array $task_data, string $backup_path ) {
		$download_url = $task_data['download_url'] ?? '';
		
		// Download the optimized main image
		$download_result = $this->api_client->download_optimized_image( $download_url );
		
		if ( $download_result['error'] ) {
			return $this->create_error_response( 'Failed to download optimized image: ' . $download_result['message'] );
		}

		// Write optimized image to original location
		$write_result = file_put_contents( $original_path, $download_result['content'] );
		
		if ( false === $write_result ) {
			// Restore from backup if replacement failed
			copy( $backup_path, $original_path );
			unlink( $backup_path );
			
			return $this->create_error_response( 'Failed to write optimized image file' );
		}

		// Calculate file sizes and compression
		$original_size = filesize( $backup_path );
		$optimized_size = filesize( $original_path );
		$size_reduction = $original_size - $optimized_size;
		$compression_percentage = $original_size > 0 ? ( $size_reduction / $original_size ) * 100 : 0;

		// Update file permissions to match original
		$original_perms = fileperms( $backup_path );
		if ( $original_perms !== false ) {
			chmod( $original_path, $original_perms );
		}

		return $this->create_success_response( 'Image replacement successful', [
			'file_size_before' => Helpers::format_file_size( $original_size ),
			'file_size_after' => Helpers::format_file_size( $optimized_size ),
			'size_reduction' => Helpers::format_file_size( $size_reduction ),
			'compression_percentage' => round( $compression_percentage, 2 ),
			'download_size' => Helpers::format_file_size( $download_result['size'] ),
		]);
	}

	/**
	 * Handle minimal optimization case.
	 *
	 * @param array  $replacement_result Result from image replacement.
	 * @param string $backup_path Path to backup file.
	 * @return array Result of minimal optimization check.
	 */
	private function handle_minimal_optimization( array $replacement_result, string $backup_path ) {
		$compression_percentage = $replacement_result['compression_percentage'];

		// Check if optimization result is minimal
		if ( $compression_percentage < self::MIN_COMPRESSION_THRESHOLD ) {
			// Remove the backup since optimization didn't provide significant savings
			if ( file_exists( $backup_path ) ) {
				unlink( $backup_path );
			}
			
			return $this->create_error_response( 
				'Image is already well-optimized. No significant compression achieved (less than ' . self::MIN_COMPRESSION_THRESHOLD . '% reduction).', 
				[
					'compression_percentage' => $compression_percentage,
					'file_size_before' => $replacement_result['file_size_before'],
					'file_size_after' => $replacement_result['file_size_after'],
					'size_reduction' => $replacement_result['size_reduction'],
					'minimal_optimization' => true,
				]
			);
		}

		return $this->create_success_response( 'Optimization threshold met' );
	}

	/**
	 * Save optimization meta data for an attachment.
	 *
	 * @param int   $attachment_id WordPress attachment ID.
	 * @param array $optimization_data Optimization result data.
	 */
	private function save_optimization_meta( int $attachment_id, array $optimization_data ) {
		$meta_data = array(
			'timestamp' => current_time( 'mysql' ),
			'backup_exists' => isset( $optimization_data['backup_created'] ) ? $optimization_data['backup_created'] : false,
			'backup_filename' => $optimization_data['backup_filename'] ?? null,
			'compression_ratio' => $optimization_data['compression_percentage'] ?? ( $optimization_data['optimization']['compression_ratio'] ?? null ),
			'file_size_before' => $optimization_data['file_size_before'] ?? null,
			'file_size_after' => $optimization_data['file_size_after'] ?? null,
			'size_reduction' => $optimization_data['size_reduction'] ?? null,
			'minimal_optimization' => $optimization_data['minimal_optimization'] ?? false,
			'thumbnails_regenerated' => $optimization_data['thumbnails_regenerated'] ?? false,
			'thumbnails_count' => $optimization_data['thumbnails_generated_count'] ?? 0,
			'thumbnails_sizes' => $optimization_data['thumbnails_generated_sizes'] ?? array(),
			'thumbnails_optimized' => $optimization_data['thumbnails_optimized'] ?? false,
			'thumbnails_optimized_count' => $optimization_data['optimized_count'] ?? 0,
			'thumbnails_optimization_results' => $optimization_data['optimization_results'] ?? array(),
			'webp_created' => $optimization_data['webp_created'] ?? false,
			'webp_filename' => $optimization_data['webp_filename'] ?? null,
			'webp_size' => $optimization_data['webp_size'] ?? null,
			'webp_compression_ratio' => $optimization_data['webp_compression_ratio'] ?? null,
			'webp_size_reduction' => $optimization_data['webp_size_reduction'] ?? null,
		);

		update_post_meta( $attachment_id, self::OPTIMIZATION_META_KEY, $meta_data );
	}

	/**
	 * Process WebP image creation/download.
	 *
	 * @param array  $task_data Complete optimization task data.
	 * @param string $base_path Base path for the main image.
	 * @return array WebP processing result.
	 */
	private function process_webp_image( array $task_data, string $base_path ) {
		$webp_download_url = $task_data['webp_download_url'] ?? '';
		$webp_data = $task_data['webp'] ?? array();
		$webp_enabled = (bool) woodmart_get_opt( 'woodmart_optimizer_generate_webp', false );

		// Return early if no WebP URL
		if ( empty( $webp_download_url ) ) {
			return [];
		}

		// Skip if WebP generation is disabled
		if ( ! $webp_enabled ) {
			return [
				'webp_created' => false,
				'webp_skipped' => true,
				'webp_message' => 'WebP generation is disabled in settings',
			];
		}

		// Download WebP image
		$webp_download_result = $this->api_client->download_webp_image( $webp_download_url );
		
		if ( $webp_download_result['error'] ) {
			return [
				'webp_created' => false,
				'webp_error' => 'Failed to download WebP: ' . $webp_download_result['message'],
			];
		}

		// Create WebP file path
		$webp_path = $base_path . '.webp';
		
		// Save WebP file
		$webp_write_result = file_put_contents( $webp_path, $webp_download_result['content'] );
		
		if ( false === $webp_write_result ) {
			return [
				'webp_created' => false,
				'webp_error' => 'Failed to save WebP file',
			];
		}

		return [
			'webp_created' => true,
			'webp_path' => $webp_path,
			'webp_filename' => basename( $webp_path ),
			'webp_size' => Helpers::format_file_size( $webp_download_result['size'] ),
			'webp_compression_ratio' => $webp_data['compression_ratio'] ?? null,
			'webp_size_reduction' => isset( $webp_data['size_reduction'] ) ? Helpers::format_file_size( $webp_data['size_reduction'] ) : null,
		];
	}

	// ========================================
	// THUMBNAIL OPTIMIZATION METHODS
	// ========================================

	/**
	 * Rebuild and optimize thumbnails for an attachment.
	 *
	 * @param int $attachment_id WordPress attachment ID.
	 * @return array Result of thumbnail rebuild and optimization.
	 */
	private function rebuild_and_optimize_thumbnails( int $attachment_id ) {
		$validation_error = $this->validate_attachment_id( $attachment_id );
		if ( $validation_error ) {
			return $validation_error;
		}

		// Delete existing thumbnails
		$delete_result = $this->delete_existing_thumbnails( $attachment_id );
		if ( $delete_result['error'] ) {
			return $delete_result;
		}

		// Regenerate thumbnails
		$regenerate_result = $this->regenerate_thumbnails( $attachment_id );
		if ( $regenerate_result['error'] ) {
			return $regenerate_result;
		}

		// Optimize the newly generated thumbnails
		$optimize_result = $this->optimize_thumbnails( $attachment_id );

		$result = $this->create_success_response( 
			"Deleted and regenerated {$regenerate_result['generated_count']} thumbnail files", 
			[
				'generated_count' => $regenerate_result['generated_count'],
				'generated_sizes' => $regenerate_result['generated_sizes'],
				'metadata' => $regenerate_result['metadata'],
			]
		);

		// Add thumbnail optimization results
		if ( ! $optimize_result['error'] ) {
			$result['thumbnails_optimized'] = true;
			$result['optimized_count'] = $optimize_result['optimized_count'];
			$result['optimization_results'] = $optimize_result['optimization_results'] ?? [];
			$result['message'] .= " and optimized {$optimize_result['optimized_count']} thumbnails";
			
			if ( ! empty( $optimize_result['errors'] ) ) {
				$result['optimization_errors'] = $optimize_result['errors'];
				$result['message'] .= ' (with ' . count( $optimize_result['errors'] ) . ' optimization errors)';
			}
		} else {
			$result['thumbnails_optimized'] = false;
			$result['optimization_error'] = $optimize_result['message'];
			$result['message'] .= ' but failed to optimize thumbnails: ' . $optimize_result['message'];
		}

		return $result;
	}

	/**
	 * Rebuild thumbnails for an attachment (delete old, generate new, no optimization).
	 *
	 * @param int $attachment_id WordPress attachment ID.
	 * @return array Result of thumbnail rebuild.
	 */
	private function rebuild_thumbnails( int $attachment_id ) {
		$validation_error = $this->validate_attachment_id( $attachment_id );
		if ( $validation_error ) {
			return $validation_error;
		}

		// Delete existing thumbnails
		$delete_result = $this->delete_existing_thumbnails( $attachment_id );
		if ( $delete_result['error'] ) {
			return $delete_result;
		}

		// Regenerate thumbnails (without optimization)
		$regenerate_result = $this->regenerate_thumbnails( $attachment_id );
		if ( $regenerate_result['error'] ) {
			return $regenerate_result;
		}

		return $this->create_success_response( 
			"Deleted and regenerated {$regenerate_result['generated_count']} thumbnail files (not optimized)", 
			[
				'generated_count' => $regenerate_result['generated_count'],
				'generated_sizes' => $regenerate_result['generated_sizes'],
				'metadata' => $regenerate_result['metadata'],
			]
		);
	}

	/**
	 * Delete all existing thumbnails for an attachment.
	 *
	 * @param int $attachment_id WordPress attachment ID.
	 * @return array Result of thumbnail deletion.
	 */
	private function delete_existing_thumbnails( int $attachment_id ) {
		$validation_error = $this->validate_attachment_id( $attachment_id );
		if ( $validation_error ) {
			return $validation_error;
		}

		$file_info = $this->get_attachment_file_info( $attachment_id );
		
		if ( ! $file_info['metadata'] || empty( $file_info['metadata']['sizes'] ) ) {
			return $this->create_success_response( 'No thumbnails found to delete', [
				'deleted_count' => 0,
				'webp_deleted_count' => 0,
			]);
		}

		$deleted_count = 0;
		$deleted_sizes = [];
		$webp_deleted_count = 0;

		// Process each thumbnail
		foreach ( $file_info['metadata']['sizes'] as $size_name => $size_data ) {
			if ( ! empty( $size_data['file'] ) ) {
				$thumbnail_path = $file_info['file_dir'] . '/' . $size_data['file'];
				$dimensions = $size_data['width'] . 'x' . $size_data['height'];
				$result = $this->delete_single_thumbnail_file( $thumbnail_path, $size_name, $dimensions );
				
				if ( ! $result['error'] ) {
					if ( $result['deleted_main'] ) {
						$deleted_count++;
						$deleted_sizes[] = $size_name . ' (' . $result['dimensions'] . ')';
					}
					if ( $result['deleted_webp'] ) {
						$webp_deleted_count++;
					}
				}
			}
		}

		$message = "Deleted {$deleted_count} thumbnail files";
		if ( $webp_deleted_count > 0 ) {
			$message .= " and {$webp_deleted_count} WebP thumbnail files";
		}

		return $this->create_success_response( $message, [
			'deleted_count' => $deleted_count,
			'deleted_sizes' => $deleted_sizes,
			'webp_deleted_count' => $webp_deleted_count,
		]);
	}

	/**
	 * Regenerate thumbnails for an attachment.
	 *
	 * @param int $attachment_id WordPress attachment ID.
	 * @return array Result of thumbnail regeneration.
	 */
	private function regenerate_thumbnails( int $attachment_id ) {
		$validation_error = $this->validate_attachment_id( $attachment_id );
		if ( $validation_error ) {
			return $validation_error;
		}

		// Include required WordPress functions
		if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
		}

		$file_info = $this->get_attachment_file_info( $attachment_id );
		
		if ( ! $file_info['file_path'] || ! file_exists( $file_info['file_path'] ) ) {
			return $this->create_error_response( 'Attachment file not found' );
		}

		// Generate new metadata and thumbnails
		$metadata = wp_generate_attachment_metadata( $attachment_id, $file_info['file_path'] );
		
		if ( is_wp_error( $metadata ) ) {
			return $this->create_error_response( 'Failed to generate thumbnails: ' . $metadata->get_error_message() );
		}

		// Update the attachment metadata
		wp_update_attachment_metadata( $attachment_id, $metadata );

		// Count generated thumbnails
		$generated_count = 0;
		$generated_sizes = [];
		
		if ( ! empty( $metadata['sizes'] ) ) {
			foreach ( $metadata['sizes'] as $size_name => $size_data ) {
				$generated_count++;
				$generated_sizes[] = $size_name . ' (' . $size_data['width'] . 'x' . $size_data['height'] . ')';
			}
		}

		return $this->create_success_response( "Generated {$generated_count} thumbnail files", [
			'generated_count' => $generated_count,
			'generated_sizes' => $generated_sizes,
			'metadata' => $metadata,
		]);
	}

	/**
	 * Optimize all thumbnails for an attachment (without creating backups).
	 *
	 * @param int $attachment_id WordPress attachment ID.
	 * @return array Result of thumbnail optimization.
	 */
	private function optimize_thumbnails( int $attachment_id ) {
		$validation_error = $this->validate_attachment_id( $attachment_id );
		if ( $validation_error ) {
			return $validation_error;
		}

		$file_info = $this->get_attachment_file_info( $attachment_id );
		
		if ( ! $file_info['metadata'] || empty( $file_info['metadata']['sizes'] ) ) {
			return $this->create_success_response( 'No thumbnails found to optimize', [
				'optimized_count' => 0,
				'optimization_results' => [],
			]);
		}

		$optimized_count = 0;
		$optimization_results = [];
		$errors = [];

		// Process each thumbnail
		foreach ( $file_info['metadata']['sizes'] as $size_name => $size_data ) {
			if ( ! empty( $size_data['file'] ) ) {
				$thumbnail_path = $file_info['file_dir'] . '/' . $size_data['file'];
				$result = $this->optimize_single_thumbnail( $thumbnail_path, $size_name );
				
				if ( $result['error'] ) {
					$errors[] = $size_name . ': ' . $result['message'];
				} else {
					$optimized_count++;
					$optimization_results[] = [
						'size_name' => $size_name,
						'file' => $size_data['file'],
						'dimensions' => $size_data['width'] . 'x' . $size_data['height'],
						'compression_ratio' => $result['compression_percentage'] ?? null,
						'size_reduction' => $result['size_reduction'] ?? null,
					];
				}
			}
		}

		$message = "Optimized {$optimized_count} thumbnail files";
		if ( ! empty( $errors ) ) {
			$message .= ' (with ' . count( $errors ) . ' errors)';
		}

		return $this->create_success_response( $message, [
			'optimized_count' => $optimized_count,
			'optimization_results' => $optimization_results,
			'errors' => $errors,
		]);
	}

	/**
	 * Optimize a single thumbnail file.
	 *
	 * @param string $thumbnail_path Path to the thumbnail file.
	 * @param string $size_name Size name for logging.
	 * @return array Optimization result.
	 */
	private function optimize_single_thumbnail( string $thumbnail_path, string $size_name ) {
		// Submit thumbnail for API optimization (no attachment ID to prevent backup)
		$api_result = $this->api_client->submit_image( $thumbnail_path );
		
		if ( $api_result['error'] ) {
			return $this->create_error_response( $api_result['message'] ?? 'API optimization failed' );
		}

		// API request successful - task created
		$response_data = $api_result['data'];
		$task_id = $response_data['data']['task_id'] ?? null;

		if ( ! $task_id ) {
			return $this->create_error_response( 'No task ID received from API' );
		}

		// Poll for task completion
		return $this->poll_thumbnail_completion( $task_id, $thumbnail_path, $size_name );
	}

	/**
	 * Poll for thumbnail optimization completion.
	 *
	 * @param string $task_id Task ID to poll.
	 * @param string $thumbnail_path Path to the thumbnail file.
	 * @param string $size_name Size name for logging.
	 * @return array Task completion result.
	 */
	private function poll_thumbnail_completion( string $task_id, string $thumbnail_path, string $size_name ) {
		$result = $this->poll_task_generic( $task_id, self::THUMBNAIL_MAX_ATTEMPTS );
		
		if ( $result['error'] ) {
			return $result;
		}
		
		$task_data = $result['task_data'];
		
		if ( ! empty( $task_data['download_url'] ) ) {
			$replacement_result = $this->replace_thumbnail_file( $thumbnail_path, $task_data );
			
			if ( $replacement_result['error'] ) {
				return $replacement_result;
			}

			return $this->create_success_response( "Thumbnail {$size_name} optimized successfully", [
				'compression_ratio' => $replacement_result['compression_percentage'],
				'size_reduction' => $replacement_result['size_reduction'],
			]);
		}

		return $this->create_error_response( 'No download URL provided' );
	}

	/**
	 * Replace a thumbnail file with optimized version (no backup).
	 *
	 * @param string $thumbnail_path Path to the thumbnail file.
	 * @param array  $task_data Complete optimization task data including WebP info.
	 * @return array Replacement result.
	 */
	private function replace_thumbnail_file( string $thumbnail_path, array $task_data ) {
		// Validate thumbnail file exists
		if ( ! file_exists( $thumbnail_path ) ) {
			return $this->create_error_response( 'Thumbnail file not found: ' . $thumbnail_path );
		}

		// Process WebP image
		$webp_result = $this->process_webp_image( $task_data, $thumbnail_path );

		// Download the optimized thumbnail
		$download_url = $task_data['download_url'] ?? '';
		$download_result = $this->api_client->download_optimized_image( $download_url );
		
		if ( $download_result['error'] ) {
			return $this->create_error_response( 'Failed to download optimized thumbnail: ' . $download_result['message'] );
		}

		// Get original file size
		$original_size = filesize( $thumbnail_path );

		// Write optimized thumbnail directly (no backup)
		$write_result = file_put_contents( $thumbnail_path, $download_result['content'] );
		
		if ( false === $write_result ) {
			return $this->create_error_response( 'Failed to write optimized thumbnail file' );
		}

		// Calculate compression metrics
		$optimized_size = filesize( $thumbnail_path );
		$size_reduction = $original_size - $optimized_size;
		$compression_percentage = $original_size > 0 ? ( $size_reduction / $original_size ) * 100 : 0;

		$result = $this->create_success_response( 'Thumbnail successfully replaced with optimized version', [
			'file_size_before' => Helpers::format_file_size( $original_size ),
			'file_size_after' => Helpers::format_file_size( $optimized_size ),
			'size_reduction' => Helpers::format_file_size( $size_reduction ),
			'compression_percentage' => round( $compression_percentage, 2 ),
		]);

		// Add WebP results if available
		if ( ! empty( $webp_result ) ) {
			$result = array_merge( $result, $webp_result );
		}

		return $result;
	}

	/**
	 * Delete a single thumbnail file and its WebP version.
	 *
	 * @param string $thumbnail_path Path to thumbnail file.
	 * @param string $size_name Thumbnail size name.
	 * @param string $dimensions Thumbnail dimensions string.
	 * @return array Deletion result.
	 */
	private function delete_single_thumbnail_file( string $thumbnail_path, string $size_name, string $dimensions ) {
		$deleted_main = false;
		$deleted_webp = false;

		// Delete main thumbnail
		if ( unlink( $thumbnail_path ) ) {
			$deleted_main = true;
		}

		// Delete WebP version if exists
		$webp_thumbnail_path = $thumbnail_path . '.webp';
		if ( file_exists( $webp_thumbnail_path ) && unlink( $webp_thumbnail_path ) ) {
			$deleted_webp = true;
		}

		return $this->create_success_response( "Deleted thumbnail {$size_name}", [
			'size_name' => $size_name,
			'dimensions' => $dimensions,
			'deleted_main' => $deleted_main,
			'deleted_webp' => $deleted_webp,
		]);
	}

	// ========================================
	// HELPER & UTILITY METHODS
	// ========================================

	/**
	 * Validate attachment ID for thumbnail operations.
	 *
	 * @param int $attachment_id WordPress attachment ID.
	 * @return array|false Error response or false if valid.
	 */
	private function validate_attachment_id( int $attachment_id ) {
		if ( $attachment_id <= 0 ) {
			return $this->create_error_response( 'Invalid attachment ID' );
		}
		return false;
	}

	/**
	 * Get attachment metadata and file info.
	 *
	 * @param int $attachment_id WordPress attachment ID.
	 * @return array File info with metadata, file_path, and file_dir.
	 */
	private function get_attachment_file_info( int $attachment_id ) {
		$metadata = wp_get_attachment_metadata( $attachment_id );
		$file_path = get_attached_file( $attachment_id );
		$file_dir = $file_path ? dirname( $file_path ) : '';

		return [
			'metadata' => $metadata,
			'file_path' => $file_path,
			'file_dir' => $file_dir,
		];
	}

	/**
	 * Create standardized error response.
	 *
	 * @param string $message Error message.
	 * @param array  $context Additional context data.
	 * @return array Standardized error response.
	 */
	private function create_error_response( string $message, array $context = [] ) {
		return array_merge(
			[
				'error' => true,
				'message' => $message,
			],
			$context
		);
	}

	/**
	 * Create standardized success response.
	 *
	 * @param string $message Success message.
	 * @param array  $data Additional data.
	 * @return array Standardized success response.
	 */
	private function create_success_response( string $message, array $data = [] ) {
		return array_merge(
			[
				'error' => false,
				'message' => $message,
			],
			$data
		);
	}
}