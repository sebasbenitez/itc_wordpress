<?php
/**
 * Main class for WoodMart Images Optimizer plugin.
 *
 * @package WoodMart\ImagesOptimizer
 */

namespace WoodMart\ImagesOptimizer;

use WP_Post;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main class for image optimizer functionality.
 */
class Main {
	
	/**
	 * Optimizer instance.
	 *
	 * @var Optimizer
	 */
	private $optimizer = null;

	/**
	 * UI Components instance.
	 *
	 * @var UI_Components
	 */
	private $ui_components = null;

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->optimizer = new Optimizer();

		// Initialize UI components.
		$this->ui_components = new UI_Components( $this->optimizer );

		// Initialize the picture display for WebP replacement.
		new Picture_Display();

		// Initialize WebP replacement for direct image URL replacement.
		new WebP_Replace();

		add_action( 'init', array( $this, 'init' ) );

		// Register the scheduled optimization action.
		add_action( 'xts_auto_optimize_image', array( $this, 'scheduled_optimize_image' ), 10, 1 );

		// Add admin notices for optimization results.
		add_action( 'admin_notices', array( $this, 'display_optimization_notices' ) );

		// Clean up backup files when attachment is deleted.
		add_action( 'delete_attachment', array( $this, 'cleanup_backup_on_delete' ) );
	}

	/**
	 * Initialize the module.
	 */
	public function init() {
		// Add button to the media library list in a new column before data.
		add_filter( 'manage_media_columns', array( $this, 'add_column' ) );
		add_filter( 'manage_media_custom_column', array( $this, 'add_column_content' ), 10, 2 );

		// Add optimization buttons to media modal.
		add_filter( 'attachment_fields_to_edit', array( $this, 'add_attachment_fields_modal' ), 10, 2 );

		// Add optimization buttons to attachment edit page.
		add_action( 'add_meta_boxes_attachment', array( $this, 'add_attachment_meta_box' ) );

		// Register ajax action to optimize image.
		add_action( 'wp_ajax_xts_optimizer_run', array( $this, 'optimize_image' ) );

		// Register ajax action to restore backup.
		add_action( 'wp_ajax_xts_optimizer_restore', array( $this, 'restore_image' ) );

		// Register ajax action for bulk optimization.
		add_action( 'wp_ajax_xts_optimizer_bulk', array( $this, 'bulk_optimize_images' ) );

		// Add bulk actions to media library.
		add_filter( 'bulk_actions-upload', array( $this, 'add_bulk_actions' ) );
		add_filter( 'handle_bulk_actions-upload', array( $this, 'handle_bulk_actions' ), 10, 3 );

		// Load script on media library page only.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Auto-optimize newly uploaded images (use add_attachment instead of wp_generate_attachment_metadata to avoid triggering during bulk operations).
		add_action( 'add_attachment', array( $this, 'auto_optimize_uploaded_image' ), 10, 1 );
	}

	/**
	 * Enqueue scripts for admin pages.
	 */
	public function enqueue_scripts() {
		// Enqueue CSS for enhanced styling
		wp_enqueue_style(
			'xts-imgopt-styles',
			WOODMART_IMAGES_OPTIMIZER_PLUGIN_URL . 'assets/css/image-optimizer-styles.css',
			array(),
			WOODMART_IMAGES_OPTIMIZER_VERSION
		);

		wp_enqueue_script(
			'xts-optimizer',
			WOODMART_IMAGES_OPTIMIZER_PLUGIN_URL . 'assets/js/scripts.js',
			array( 'jquery' ),
			WOODMART_IMAGES_OPTIMIZER_VERSION,
			true
		);
		wp_localize_script(
			'xts-optimizer',
			'xts_optimizer',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'xts_optimizer_nonce' ),
			)
		);
	}

	/**
	 * Add optimizer column to media library.
	 *
	 * @param array $columns Existing columns.
	 * @return array Modified columns.
	 */
	public function add_column( $columns ) {
		$columns['xts_optimizer'] = esc_html__( 'WoodMart optimizer', 'woodmart' );
		return $columns;
	}

	/**
	 * Add content to optimizer column.
	 *
	 * @param string $column_name Column name.
	 * @param int    $post_id Post ID.
	 */
	public function add_column_content( $column_name, $post_id ) {
		if ( 'xts_optimizer' === $column_name ) {
			echo $this->ui_components->get_optimization_buttons_html( $post_id, 'list' );
		}
	}

	/**
	 * Add optimization fields to media modal.
	 *
	 * @param array   $form_fields Array of form fields.
	 * @param WP_Post $post        Post object.
	 * @return array Modified form fields.
	 */
	public function add_attachment_fields_modal( $form_fields, $post ) {
		// Only add for image attachments.
		if ( ! wp_attachment_is_image( $post->ID ) ) {
			return $form_fields;
		}

		// Check if we're in the modal context (not on attachment edit page).
		// On the edit page, we use a meta box instead.
		$screen = get_current_screen();
		if ( $screen && 'attachment' === $screen->id ) {
			return $form_fields;
		}

		$optimization_buttons = $this->ui_components->get_optimization_buttons_html( $post->ID, 'modal' );

		$form_fields['woodmart_optimizer'] = array(
			'label' => esc_html__( 'WoodMart Image Optimizer', 'woodmart-images-optimizer' ),
			'input' => 'html',
			'html'  => $optimization_buttons,
		);

		return $form_fields;
	}

	/**
	 * Add meta box to attachment edit page.
	 *
	 * @param WP_Post $post Post object.
	 */
	public function add_attachment_meta_box( $post ) {
		// Only add for image attachments.
		if ( ! wp_attachment_is_image( $post->ID ) ) {
			return;
		}

		add_meta_box(
			'woodmart-optimizer',
			esc_html__( 'WoodMart Image Optimizer', 'woodmart-images-optimizer' ),
			array( $this, 'render_attachment_meta_box' ),
			'attachment',
			'side',
			'default'
		);
	}

	/**
	 * Render the meta box content for attachment edit page.
	 *
	 * @param WP_Post $post Post object.
	 */
	public function render_attachment_meta_box( $post ) {
		$optimization_buttons = $this->ui_components->get_optimization_buttons_html( $post->ID, 'edit' );

		echo $optimization_buttons;
	}

	/**
	 * Handle AJAX request to optimize image.
	 */
	public function optimize_image() {
		// Check nonce and permissions.
		check_ajax_referer( 'xts_optimizer_nonce', 'nonce' );
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'You do not have permission to perform this action.', 'woodmart' ) ) );
		}

		// Check if token is available.
		if ( ! $this->optimizer->is_token_available() ) {
			wp_send_json_error( array( 'message' => esc_html__( 'WoodMart theme token is required for image optimization. Please ensure WoodMart theme is activated.', 'woodmart-images-optimizer' ) ) );
		}

		$image_id = isset( $_POST['image_id'] ) ? intval( $_POST['image_id'] ) : 0;

		if ( ! $image_id ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Invalid image ID.', 'woodmart' ) ) );
		}

		// Get the server file path of the image.
		$file_path = get_attached_file( $image_id );

		if ( ! $file_path || ! file_exists( $file_path ) ) {
			wp_send_json_error( array(
				'message' => esc_html__( 'Could not find the image file.', 'woodmart' ),
				'html'    => $this->ui_components->get_optimization_buttons_html( $image_id, 'list' ),
			) );
		}

		// Check if this is a supported image type.
		if ( ! Helpers::is_supported_image_type( $file_path, $image_id ) ) {
			wp_send_json_error( array(
				'message' => esc_html__( 'Unsupported image format. Only JPEG, PNG, and WebP images are supported.', 'woodmart' ),
				'html'    => $this->ui_components->get_optimization_buttons_html( $image_id, 'list' ),
			) );
		}

		$result = $this->optimizer->optimize( $file_path, $image_id );

		// Always return HTML for UI refresh.
		$response = array(
			'message' => $result['message'] ?? esc_html__( 'Optimization request processed.', 'woodmart' ),
			'result'  => $result,
			'html'    => $this->ui_components->get_optimization_buttons_html( $image_id, 'list' ),
		);

		if ( isset( $result['error'] ) && $result['error'] ) {
			wp_send_json_error( $response );
		}

		wp_send_json_success( $response );
	}

	/**
	 * Handle AJAX request to restore image from backup.
	 */
	public function restore_image() {
		// Check nonce and permissions.
		check_ajax_referer( 'xts_optimizer_nonce', 'nonce' );
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'You do not have permission to perform this action.', 'woodmart' ) ) );
		}

		$image_id = isset( $_POST['image_id'] ) ? intval( $_POST['image_id'] ) : 0;

		if ( ! $image_id ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Invalid image ID.', 'woodmart' ) ) );
		}

		// Get the server file path of the image.
		$file_path = get_attached_file( $image_id );

		if ( ! $file_path || ! file_exists( $file_path ) ) {
			wp_send_json_error( array(
				'message' => esc_html__( 'Could not find the image file.', 'woodmart' ),
				'html'    => $this->ui_components->get_optimization_buttons_html( $image_id, 'list' ),
			) );
		}

		$result = $this->optimizer->restore_from_backup( $file_path, $image_id );

		// Clear the scheduled flag if it exists.
		delete_post_meta( $image_id, '_xts_optimizer_scheduled' );

		// Always return HTML for UI refresh.
		$response = array(
			'message' => $result['message'],
			'result'  => $result,
			'html'    => $this->ui_components->get_optimization_buttons_html( $image_id, 'list' ),
		);

		if ( $result['error'] ) {
			wp_send_json_error( $response );
		}

		wp_send_json_success( $response );
	}

	/**
	 * Auto-optimize newly uploaded images.
	 *
	 * @param int $attachment_id Attachment ID.
	 */
	public function auto_optimize_uploaded_image( $attachment_id ) {
		// Check if auto-optimization is enabled via theme settings.
		if ( ! woodmart_get_opt( 'woodmart_optimizer_auto_optimize', false ) ) {
			return;
		}

		// Only process images.
		if ( ! wp_attachment_is_image( $attachment_id ) ) {
			return;
		}

		// Get the full file path.
		$file_path = get_attached_file( $attachment_id );

		if ( ! $file_path || ! file_exists( $file_path ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'Auto-optimize skipped for attachment ID ' . $attachment_id . ': File path not found or does not exist' );
			}
			return;
		}

		// Check if this is a supported image type.
		if ( ! Helpers::is_supported_image_type( $file_path, $attachment_id ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'Auto-optimize skipped for attachment ID ' . $attachment_id . ': Unsupported image type' );
			}
			return;
		}

		// Check if this image was already optimized (prevent duplicate optimization).
		$optimization_meta = $this->optimizer->get_optimization_meta( $attachment_id );
		if ( $optimization_meta && ( ! empty( $optimization_meta['compression_ratio'] ) || ! empty( $optimization_meta['backup_exists'] ) ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'Auto-optimize skipped for attachment ID ' . $attachment_id . ': Image already optimized' );
			}
			return;
		}

		// Check if we've already scheduled this image (use post meta to prevent race conditions).
		$scheduled_timestamp = get_post_meta( $attachment_id, '_xts_optimizer_scheduled', true );
		
		// Check if the schedule is stale (older than 30 minutes).
		$is_stale = false;
		if ( $scheduled_timestamp && is_numeric( $scheduled_timestamp ) ) {
			if ( ( time() - intval( $scheduled_timestamp ) ) > 30 * MINUTE_IN_SECONDS ) {
				$is_stale = true;
			}
		} elseif ( $scheduled_timestamp ) {
			// Handle legacy boolean 'true' value or other non-numeric values as stale if they exist.
			// If it's just '1', we can assume it might be stuck from before.
			$is_stale = true;
		}

		if ( $scheduled_timestamp && ! $is_stale ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'Auto-optimize skipped for attachment ID ' . $attachment_id . ': Already scheduled (flag set)' );
			}
			return;
		}

		// Mark as scheduled with current timestamp to prevent duplicate scheduling and allow staleness checks.
		update_post_meta( $attachment_id, '_xts_optimizer_scheduled', time() );

		// Schedule optimization in background to avoid blocking the upload process.
		wp_schedule_single_event( current_time( 'timestamp', true ) + 5, 'xts_auto_optimize_image', array( $attachment_id ) );

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'Auto-optimize scheduled for attachment ID ' . $attachment_id . ' - will run in 5 seconds' );
		}
	}

	/**
	 * Perform scheduled image optimization.
	 *
	 * @param int $attachment_id Attachment ID.
	 */
	public function scheduled_optimize_image( $attachment_id ) {
		// Get the file path from attachment ID.
		$file_path = get_attached_file( $attachment_id );
		
		// Double-check the file still exists.
		if ( ! $file_path || ! file_exists( $file_path ) ) {
			// Clean up the scheduled flag.
			delete_post_meta( $attachment_id, '_xts_optimizer_scheduled' );
			return;
		}

		// Run the optimization.
		$result = $this->optimizer->optimize( $file_path, $attachment_id );

		// Clean up the scheduled flag now that optimization is complete.
		delete_post_meta( $attachment_id, '_xts_optimizer_scheduled' );

		// Log the result (optional, for debugging).
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			if ( $result['error'] ) {
				error_log( 'Auto-optimization failed for attachment ID ' . $attachment_id . ': ' . $result['message'] );
			} else {
				error_log( 'Auto-optimization succeeded for attachment ID ' . $attachment_id );
			}
		}

		// Store optimization result for admin notice display.
		if ( ! $result['error'] ) {
			$this->add_optimization_notice( $attachment_id, 'success', 'Image optimized successfully in the background.' );
		} elseif ( isset( $result['minimal_optimization'] ) && $result['minimal_optimization'] ) {
			$this->add_optimization_notice( $attachment_id, 'warning', 'Image is already well-optimized (less than 0.5% reduction possible).' );
		} else {
			$this->add_optimization_notice( $attachment_id, 'error', 'Auto-optimization failed: ' . $result['message'] );
		}
	}

	/**
	 * Add optimization notice to display in admin.
	 *
	 * @param int    $attachment_id Attachment ID.
	 * @param string $type          Notice type (success, error, warning).
	 * @param string $message       Notice message.
	 */
	private function add_optimization_notice( $attachment_id, $type, $message ) {
		$notices = get_transient( 'xts_optimizer_notices' );
		if ( ! is_array( $notices ) ) {
			$notices = array();
		}

		$notices[] = array(
			'attachment_id' => $attachment_id,
			'type'          => $type,
			'message'       => $message,
			'timestamp'     => time(),
		);

		// Keep only the last 10 notices to avoid memory issues.
		$notices = array_slice( $notices, -10 );

		set_transient( 'xts_optimizer_notices', $notices, 24 * HOUR_IN_SECONDS );
	}

	/**
	 * Display optimization notices in admin.
	 */
	public function display_optimization_notices() {
		$screen = get_current_screen();
		
		// Only show notices on upload or edit media screens.
		if ( ! $screen || ! in_array( $screen->base, array( 'upload', 'post' ), true ) ) {
			return;
		}

		// Display bulk optimization error (no token)
		if ( isset( $_GET['bulk_optimize_error'] ) && 'no_token' === $_GET['bulk_optimize_error'] ) {
			printf(
				'<div class="notice notice-error is-dismissible"><p><strong>%s</strong> %s</p></div>',
				esc_html__( 'Bulk Optimization Failed:', 'woodmart-images-optimizer' ),
				esc_html__( 'WoodMart theme token is required for image optimization. Please ensure WoodMart theme is activated.', 'woodmart-images-optimizer' )
			);
		}

		// Display bulk optimization notices
		if ( isset( $_GET['bulk_optimize'] ) ) {
			$batch_id = sanitize_text_field( $_GET['bulk_optimize'] );
			$image_count = isset( $_GET['image_count'] ) ? intval( $_GET['image_count'] ) : 0;
			
			if ( $image_count > 0 ) {
				printf(
					'<div class="notice notice-info is-dismissible"><p><strong>%s</strong> %s <span id="bulk-progress">0</span>/%d</p><div id="bulk-progress-bar"><div style="width: 0;"></div></div></div>',
					esc_html__( 'Bulk Optimization in Progress:', 'woodmart' ),
					esc_html__( 'Processing image', 'woodmart' ),
					$image_count
				);
				
				// Add hidden field with batch info for JavaScript
				printf( '<script type="text/javascript">window.woodmartBulkOptimize = {batch_id: "%s", total: %d};</script>', esc_js( $batch_id ), $image_count );
			}
		}

		// Display bulk restore results
		if ( isset( $_GET['bulk_restored'] ) ) {
			$restored_count = intval( $_GET['bulk_restored'] );
			$error_count = isset( $_GET['bulk_errors'] ) ? intval( $_GET['bulk_errors'] ) : 0;
			
			if ( $restored_count > 0 ) {
				printf(
					'<div class="notice notice-success is-dismissible"><p><strong>%s</strong> %s</p></div>',
					esc_html__( 'Bulk Restore Complete:', 'woodmart' ),
					sprintf( esc_html( _n( '%d image restored successfully.', '%d images restored successfully.', $restored_count, 'woodmart' ) ), $restored_count )
				);
			}
			
			if ( $error_count > 0 ) {
				printf(
					'<div class="notice notice-warning is-dismissible"><p><strong>%s</strong> %s</p></div>',
					esc_html__( 'Bulk Restore Warnings:', 'woodmart' ),
					sprintf( esc_html( _n( '%d image could not be restored.', '%d images could not be restored.', $error_count, 'woodmart' ) ), $error_count )
				);
			}
		}

		$notices = get_transient( 'xts_optimizer_notices' );
		if ( ! is_array( $notices ) || empty( $notices ) ) {
			return;
		}

		// Clear old notices (older than 1 hour).
		$notices = array_filter( $notices, function( $notice ) {
			return ( time() - $notice['timestamp'] ) < HOUR_IN_SECONDS;
		});

		foreach ( $notices as $notice ) {
			$notice_type = $notice['type'];
			$class = 'notice notice-' . ( 'success' === $notice_type ? 'success' : ( 'warning' === $notice_type ? 'warning' : 'error' ) );
			$image_title = get_the_title( $notice['attachment_id'] );
			
			printf(
				'<div class="%1$s is-dismissible"><p><strong>%2$s:</strong> %3$s</p></div>',
				esc_attr( $class ),
				esc_html( $image_title ? $image_title : 'Image #' . $notice['attachment_id'] ),
				esc_html( $notice['message'] )
			);
		}

		// Clear notices after display.
		delete_transient( 'xts_optimizer_notices' );
	}

	/**
	 * Add bulk actions to media library.
	 *
	 * @param array $bulk_actions Existing bulk actions.
	 * @return array Modified bulk actions.
	 */
	public function add_bulk_actions( $bulk_actions ) {
		// Only add optimization action if token is available
		if ( $this->optimizer->is_token_available() ) {
			$bulk_actions['woodmart_optimize'] = esc_html__( 'Optimize with WoodMart', 'woodmart' );
		}
		$bulk_actions['woodmart_restore'] = esc_html__( 'Restore from optimizer backup', 'woodmart' );
		return $bulk_actions;
	}

	/**
	 * Handle bulk actions.
	 *
	 * @param string $redirect_to Redirect URL.
	 * @param string $doaction The action being taken.
	 * @param array  $post_ids Array of post IDs.
	 * @return string Modified redirect URL.
	 */
	public function handle_bulk_actions( $redirect_to, $doaction, $post_ids ) {
		if ( 'woodmart_optimize' === $doaction ) {
			// Check if token is available before processing
			if ( ! $this->optimizer->is_token_available() ) {
				$redirect_to = add_query_arg( array(
					'bulk_optimize_error' => 'no_token',
				), $redirect_to );
				return $redirect_to;
			}

			// Filter to only include supported image types
			$image_ids = array();
			foreach ( $post_ids as $post_id ) {
				$file_path = get_attached_file( $post_id );
				if ( $file_path && file_exists( $file_path ) && Helpers::is_supported_image_type( $file_path, $post_id ) ) {
					$image_ids[] = $post_id;
				}
			}

			if ( ! empty( $image_ids ) ) {
				// Store the IDs in a transient for AJAX processing
				$batch_id = uniqid( 'woodmart_bulk_' );
				set_transient( $batch_id, $image_ids, HOUR_IN_SECONDS );
				
				$redirect_to = add_query_arg( array(
					'bulk_optimize' => $batch_id,
					'image_count' => count( $image_ids ),
				), $redirect_to );
			}
		} elseif ( 'woodmart_restore' === $doaction ) {
			$restored_count = 0;
			$error_count = 0;

			foreach ( $post_ids as $post_id ) {
				$file_path = get_attached_file( $post_id );
				if ( $file_path && file_exists( $file_path ) ) {
					$restore_result = $this->optimizer->restore_from_backup( $file_path, $post_id );
					if ( ! $restore_result['error'] ) {
						$restored_count++;
					} else {
						$error_count++;
					}
				}
			}

			$redirect_to = add_query_arg( array(
				'bulk_restored' => $restored_count,
				'bulk_errors' => $error_count,
			), $redirect_to );
		}

		return $redirect_to;
	}

	/**
	 * Handle AJAX bulk optimization.
	 */
	public function bulk_optimize_images() {
		// Check nonce and permissions.
		check_ajax_referer( 'xts_optimizer_nonce', 'nonce' );
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( esc_html__( 'You do not have permission to perform this action.', 'woodmart' ) );
		}

		// Check if token is available.
		if ( ! $this->optimizer->is_token_available() ) {
			wp_send_json_error( esc_html__( 'WoodMart theme token is required for bulk image optimization. Please ensure WoodMart theme is activated.', 'woodmart-images-optimizer' ) );
		}

		$batch_id = isset( $_POST['batch_id'] ) ? sanitize_text_field( $_POST['batch_id'] ) : '';
		$offset = isset( $_POST['offset'] ) ? intval( $_POST['offset'] ) : 0;
		$batch_size = 5; // Process 5 images at a time.

		if ( ! $batch_id ) {
			wp_send_json_error( esc_html__( 'Invalid batch ID.', 'woodmart' ) );
		}

		// Get the image IDs from transient.
		$image_ids = get_transient( $batch_id );
		if ( false === $image_ids ) {
			wp_send_json_error( esc_html__( 'Batch not found or expired.', 'woodmart' ) );
		}

		$total_images = count( $image_ids );
		$batch_images = array_slice( $image_ids, $offset, $batch_size );
		$results = array();

		foreach ( $batch_images as $image_id ) {
			$file_path = get_attached_file( $image_id );
			if ( ! $file_path || ! file_exists( $file_path ) ) {
				$results[] = array(
					'id' => $image_id,
					'success' => false,
					'message' => esc_html__( 'File not found', 'woodmart' ),
				);
				continue;
			}

			$result = $this->optimizer->optimize( $file_path, $image_id );
			
			$results[] = array(
				'id' => $image_id,
				'success' => ! $result['error'],
				'message' => $result['message'] ?? ( $result['error'] ? esc_html__( 'Optimization failed', 'woodmart' ) : esc_html__( 'Optimized successfully', 'woodmart' ) ),
				'minimal_optimization' => isset( $result['minimal_optimization'] ) ? $result['minimal_optimization'] : false,
				'compression_percentage' => $result['compression_percentage'] ?? null,
			);
		}

		$processed = $offset + count( $batch_images );
		$is_complete = $processed >= $total_images;

		// Clean up transient if complete
		if ( $is_complete ) {
			delete_transient( $batch_id );
		}

		wp_send_json_success( array(
			'results' => $results,
			'processed' => $processed,
			'total' => $total_images,
			'complete' => $is_complete,
			'progress_percentage' => round( ( $processed / $total_images ) * 100, 1 ),
		) );
	}

	/**
	 * Clean up backup files when an attachment is permanently deleted.
	 *
	 * @param int $attachment_id The attachment ID being deleted.
	 */
	public function cleanup_backup_on_delete( $attachment_id ) {
		// Only process images.
		if ( ! wp_attachment_is_image( $attachment_id ) ) {
			return;
		}

		// Get the original file path.
		$original_path = get_attached_file( $attachment_id );
		if ( ! $original_path ) {
			return;
		}

		// Check if backup exists using the optimizer's method.
		$backup_status = $this->optimizer->has_backup( $original_path );
		if ( ! $backup_status['has_backup'] ) {
			return;
		}

		// Delete the backup file.
		$backup_path = $backup_status['backup_path'];
		if ( file_exists( $backup_path ) ) {
			$deleted = unlink( $backup_path );
			
			// Log the result for debugging.
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				if ( $deleted ) {
					error_log( 'Images Optimizer: Backup file deleted for attachment ID ' . $attachment_id . ': ' . basename( $backup_path ) );
				} else {
					error_log( 'Images Optimizer: Failed to delete backup file for attachment ID ' . $attachment_id . ': ' . basename( $backup_path ) );
				}
			}
		}

		// Delete WebP file for main image if it exists.
		$meta_data = $this->optimizer->get_optimization_meta( $attachment_id );
		if ( $meta_data && ! empty( $meta_data['webp_created'] ) && ! empty( $meta_data['webp_filename'] ) ) {
			$webp_path = $original_path . '.webp';
			
			if ( file_exists( $webp_path ) ) {
				$webp_deleted = unlink( $webp_path );
				
				// Log the result for debugging.
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					if ( $webp_deleted ) {
						error_log( 'Images Optimizer: WebP file deleted for attachment ID ' . $attachment_id . ': ' . basename( $webp_path ) );
					} else {
						error_log( 'Images Optimizer: Failed to delete WebP file for attachment ID ' . $attachment_id . ': ' . basename( $webp_path ) );
					}
				}
			}
		}

		// Delete WebP files for all thumbnail sizes.
		$metadata = wp_get_attachment_metadata( $attachment_id );
		if ( $metadata && ! empty( $metadata['sizes'] ) ) {
			$file_dir = dirname( $original_path );
			$webp_count = 0;

			foreach ( $metadata['sizes'] as $size_name => $size_data ) {
				if ( ! empty( $size_data['file'] ) ) {
					$thumbnail_path = $file_dir . '/' . $size_data['file'];
					$webp_thumbnail_path = $thumbnail_path . '.webp';
					
					if ( file_exists( $webp_thumbnail_path ) ) {
						if ( unlink( $webp_thumbnail_path ) ) {
							$webp_count++;
						}
					}
				}
			}

			// Log the result for debugging.
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG && $webp_count > 0 ) {
				error_log( 'Images Optimizer: Deleted ' . $webp_count . ' WebP thumbnail files for attachment ID ' . $attachment_id );
			}
		}

		// Clean up optimization metadata.
		$this->optimizer->clear_optimization_meta( $attachment_id );

		// Clean up scheduling flag.
		delete_post_meta( $attachment_id, '_xts_optimizer_scheduled' );
	}
}