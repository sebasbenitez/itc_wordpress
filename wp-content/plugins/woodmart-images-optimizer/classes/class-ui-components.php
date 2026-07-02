<?php
/**
 * UI Components class for displaying optimization buttons and interfaces.
 *
 * @package WoodMart\ImagesOptimizer
 */

namespace WoodMart\ImagesOptimizer;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class for handling UI components across different WordPress media contexts.
 */
class UI_Components {

	/**
	 * Optimizer instance.
	 *
	 * @var Optimizer
	 */
	private $optimizer;

	/**
	 * Constructor.
	 *
	 * @param Optimizer $optimizer Optimizer instance.
	 */
	public function __construct( $optimizer ) {
		$this->optimizer = $optimizer;
	}

	/**
	 * Generate the optimization buttons HTML for a given attachment.
	 *
	 * @param int    $attachment_id Attachment ID.
	 * @param string $context       Context where buttons are displayed (list|modal|edit).
	 * @return string HTML content.
	 */
	public function get_optimization_buttons_html( $attachment_id, $context = 'list' ) {
		// Check if WoodMart token is available.
		if ( ! $this->optimizer->is_token_available() ) {
			return $this->get_no_token_html( $context );
		}

		// Get the server file path of the image.
		$file_path = get_attached_file( $attachment_id );

		if ( ! $file_path || ! file_exists( $file_path ) ) {
			return $this->get_file_not_found_html( $context );
		}

		// Check if this is a supported image type.
		if ( ! Helpers::is_supported_image_type( $file_path, $attachment_id ) ) {
			return $this->get_unsupported_format_html( $context );
		}

		// Get optimization meta data.
		$optimization_meta = $this->optimizer->get_optimization_meta( $attachment_id );
		$backup_status     = $this->optimizer->has_backup_by_id( $attachment_id );
		$is_scheduled      = get_post_meta( $attachment_id, '_xts_optimizer_scheduled', true );

		return $this->build_buttons_html( $attachment_id, $optimization_meta, $backup_status, $is_scheduled, $context );
	}

	/**
	 * Build the buttons HTML structure.
	 *
	 * @param int    $attachment_id     Attachment ID.
	 * @param array  $optimization_meta Optimization metadata.
	 * @param array  $backup_status     Backup status.
	 * @param bool   $is_scheduled      Whether optimization is scheduled (flag check).
	 * @param string $context           Display context.
	 * @return string HTML content.
	 */
	private function build_buttons_html( $attachment_id, $optimization_meta, $backup_status, $is_scheduled, $context ) {
		$html = '';

		$html .= '<div class="xts-imgopt-container xts-theme-style">';

		// Display optimization status if available.
		if ( $optimization_meta ) {
			$html .= $this->get_optimization_status_html( $optimization_meta, $context );
		} elseif ( $is_scheduled ) {
			// Check for stale schedule (older than 30 minutes).
			$is_stale = false;
			if ( is_numeric( $is_scheduled ) ) {
				if ( ( time() - intval( $is_scheduled ) ) > 30 * MINUTE_IN_SECONDS ) {
					$is_stale = true;
				}
			} elseif ( $is_scheduled ) {
				// Handle legacy boolean 'true' value or other non-numeric values as stale if they exist.
				$is_stale = true;
			}

			if ( $is_stale ) {
				// Auto-heal: remove the stale flag so the user can try again.
				delete_post_meta( $attachment_id, '_xts_optimizer_scheduled' );
				$is_scheduled = false;
			} else {
				$html .= $this->get_scheduled_optimization_html( $context );
			}
		}

		// Only show Optimize button if image is not already optimized and not scheduled.
		$is_optimized = $optimization_meta && ( ! empty( $optimization_meta['compression_ratio'] ) || ! empty( $optimization_meta['backup_exists'] ) );
		if ( ! $is_optimized && ! $is_scheduled ) {
			$html .= $this->get_optimize_button_html( $attachment_id, $context );
		}

		// Show restore button if backup exists.
		if ( $backup_status['has_backup'] ) {
			$html .= $this->get_restore_button_html( $attachment_id, $backup_status['backup_filename'], $context );
		}

		$html .= '</div>'; // .xts-imgopt-container

		return $html;
	}

	/**
	 * Get optimization status HTML.
	 *
	 * @param array  $optimization_meta Optimization metadata.
	 * @param string $context          Display context.
	 * @return string HTML content.
	 */
	private function get_optimization_status_html( $optimization_meta, $context ) {
		// Determine if optimized based on having compression ratio or backup.
		$is_optimized_meta = ! empty( $optimization_meta['compression_ratio'] ) || ! empty( $optimization_meta['backup_exists'] );
		$is_minimal_optimization = ! empty( $optimization_meta['minimal_optimization'] );

		$info_class = 'xts-imgopt-info xts-notice';
		if ( $is_optimized_meta ) {
			$info_class .= ' xts-success';
		} elseif ( $is_minimal_optimization ) {
			$info_class .= ' xts-info';
		} else {
			$info_class .= ' xts-error';
		}

		$html = '<div class="' . esc_attr( $info_class ) . '">';

		if ( $is_optimized_meta ) {
			$html .= '<strong class="xts-status-label">' . esc_html__( 'Optimized', 'woodmart-images-optimizer' ) . '</strong>';
			if ( ! empty( $optimization_meta['compression_ratio'] ) ) {
				$html .= ' <span>(' . esc_html( $optimization_meta['compression_ratio'] ) . '% ' . esc_html__( 'smaller', 'woodmart-images-optimizer' ) . ')</span>';
			}
			if ( ! empty( $optimization_meta['timestamp'] ) && 'edit' === $context ) {
				$html .= '<br><span class="xts-timestamp">' . sprintf(
					/* translators: %s: human readable time difference */
					esc_html__( 'Optimized %s ago', 'woodmart-images-optimizer' ),
					human_time_diff( strtotime( $optimization_meta['timestamp'] ) )
				) . '</span>';
			}
		} else {
			// Failed optimization
			$html .= '<strong class="xts-status-label">' . esc_html__( 'Not optimized', 'woodmart-images-optimizer' ) . '</strong>';
			
			// Display failure reason if available
			if ( $is_minimal_optimization ) {
				$failure_message = esc_html__( 'Image is already well-optimized', 'woodmart-images-optimizer' );
				
				if ( 'modal' === $context || 'edit' === $context ) {
					$html .= '<br><span>' . esc_html( $failure_message );
					if ( ! empty( $optimization_meta['compression_ratio'] ) ) {
						$html .= ' (' . esc_html( $optimization_meta['compression_ratio'] ) . '% ' . esc_html__( 'compression achieved', 'woodmart-images-optimizer' ) . ')';
					}
					$html .= '</span>';
				} else {
					$html .= '<br><span>' . esc_html( $failure_message ) . '</span>';
				}
			}
			
			if ( ! empty( $optimization_meta['timestamp'] ) && 'edit' === $context ) {
				$html .= '<br><span class="xts-timestamp">' . sprintf(
					/* translators: %s: human readable time difference */
					esc_html__( 'Attempted %s ago', 'woodmart-images-optimizer' ),
					human_time_diff( strtotime( $optimization_meta['timestamp'] ) )
				) . '</span>';
			}
		}

		$html .= '</div>';

		return $html;
	}

	/**
	 * Get scheduled optimization HTML.
	 *
	 * @param string $context Display context.
	 * @return string HTML content.
	 */
	private function get_scheduled_optimization_html( $context ) {
		$html  = '<div class="xts-imgopt-info xts-notice xts-info">';
		$html .= '<strong class="xts-status-label">' . esc_html__( 'Auto-optimization scheduled', 'woodmart-images-optimizer' ) . '</strong>';
		$html .= '</div>';

		return $html;
	}

	/**
	 * Get optimize button HTML.
	 *
	 * @param int    $attachment_id Attachment ID.
	 * @param string $context       Display context.
	 * @return string HTML content.
	 */
	private function get_optimize_button_html( $attachment_id, $context ) {
		$button_class = 'xts-imgopt-optimize-btn xts-bordered-btn xts-color-primary';

		return sprintf(
			'<a href="#" class="%s" data-id="%d">%s</a>',
			esc_attr( $button_class ),
			esc_attr( $attachment_id ),
			esc_html__( 'Optimize', 'woodmart-images-optimizer' )
		);
	}

	/**
	 * Get restore button HTML.
	 *
	 * @param int    $attachment_id  Attachment ID.
	 * @param string $backup_filename Backup filename.
	 * @param string $context        Display context.
	 * @return string HTML content.
	 */
	private function get_restore_button_html( $attachment_id, $backup_filename, $context ) {
		$button_class = 'xts-imgopt-restore-btn xts-bordered-btn xts-color-warning';

		$title = sprintf(
			/* translators: %s: backup filename */
			esc_html__( 'Restore from: %s', 'woodmart-images-optimizer' ),
			esc_html( $backup_filename )
		);

		return sprintf(
			'<a href="#" class="%s" data-id="%d" title="%s">%s</a>',
			esc_attr( $button_class ),
			esc_attr( $attachment_id ),
			esc_attr( $title ),
			esc_html__( 'Restore backup', 'woodmart-images-optimizer' )
		);
	}

	/**
	 * Get no token HTML.
	 *
	 * @param string $context Display context.
	 * @return string HTML content.
	 */
	private function get_no_token_html( $context ) {
		$container_class = 'xts-imgopt-no-token';
		if ( 'modal' === $context || 'edit' === $context ) {
			$container_class .= ' xts-theme-style';
		}

		$html = '<div class="' . esc_attr( $container_class ) . '">';

		if ( 'modal' === $context || 'edit' === $context ) {
			$html          .= '<div class="xts-notice xts-error">';
			$html          .= '<strong>' . esc_html__( 'Activation Required', 'woodmart-images-optimizer' ) . '</strong>';
			$html          .= '<p>' . esc_html__( 'WoodMart theme must be activated to use image optimization.', 'woodmart-images-optimizer' ) . '</p>';

			$activation_url = admin_url( 'admin.php?page=xts_license' );
			$html          .= '<a href="' . esc_url( $activation_url ) . '" class="xts-bordered-btn xts-color-warning">' . esc_html__( 'Activate WoodMart', 'woodmart-images-optimizer' ) . '</a>';
			$html          .= '</div>';
		} else {
			$html .= '<strong>' . esc_html__( 'Activation required', 'woodmart-images-optimizer' ) . '</strong>';

			$activation_url = admin_url( 'admin.php?page=xts_license' );
			$html          .= '<span>';
			$html          .= sprintf(
				/* translators: %s: link to WoodMart activation page */
				wp_kses_post( __( 'Please <a href="%s">activate WoodMart theme</a>', 'woodmart-images-optimizer' ) ),
				esc_url( $activation_url )
			);
			$html          .= '</span>';
		}

		$html .= '</div>';

		return $html;
	}

	/**
	 * Get file not found HTML.
	 *
	 * @param string $context Display context.
	 * @return string HTML content.
	 */
	private function get_file_not_found_html( $context ) {
		if ( 'modal' === $context || 'edit' === $context ) {
			return '<div class="xts-imgopt-error xts-theme-style"><div class="xts-notice xts-error">' . esc_html__( 'Image file not found', 'woodmart-images-optimizer' ) . '</div></div>';
		} else {
			return '<span class="xts-imgopt-error">' . esc_html__( 'File not found', 'woodmart-images-optimizer' ) . '</span>';
		}
	}

	/**
	 * Get unsupported format HTML.
	 *
	 * @param string $context Display context.
	 * @return string HTML content.
	 */
	private function get_unsupported_format_html( $context ) {
		if ( 'modal' === $context || 'edit' === $context ) {
			return '<div class="xts-imgopt-unsupported xts-theme-style"><div class="xts-notice xts-info">' . esc_html__( 'Unsupported image format. Only JPEG and PNG images are supported.', 'woodmart-images-optimizer' ) . '</div></div>';
		} else {
			return '<span class="xts-imgopt-unsupported">' . esc_html__( 'Unsupported format', 'woodmart-images-optimizer' ) . '</span>';
		}
	}
}
