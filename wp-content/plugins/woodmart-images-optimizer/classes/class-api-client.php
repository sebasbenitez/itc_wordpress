<?php
/**
 * API Client class for external image optimization service.
 *
 * @package WoodMart\ImagesOptimizer
 */

namespace WoodMart\ImagesOptimizer;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WP_Error;

/**
 * API Client class for handling external image optimization service requests.
 *
 * @package WoodMart\ImagesOptimizer
 */
class Api_Client {

	// Constants for magic values.
	private const QUOTA_OPTION_KEY = 'woodmart_images_optimizer_quota';
	private const TOKEN_OPTION_KEY = 'woodmart_token';
	private const DEV_DOMAIN = 'https://wood.local';
	private const DEV_API_URL = 'http://localhost:8080/api';
	private const PROD_API_URL = 'https://img-optim.xtemos.com/api';

	/**
	 * Base API URL.
	 *
	 * @var string
	 */
	private $base_api_url;

	/**
	 * WoodMart theme token.
	 *
	 * @var string|null
	 */
	private $token;

	/**
	 * Request timeout in seconds.
	 *
	 * @var int
	 */
	private $timeout;

	// ========================================
	// CONSTRUCTOR & INITIALIZATION
	// ========================================

	/**
	 * Get the appropriate API URL based on the current site domain.
	 *
	 * @return string API URL to use.
	 */
	public static function get_default_api_url() {
		$site_url = get_site_url();
		
		if ( strpos( $site_url, self::DEV_DOMAIN ) === 0 && defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			return self::DEV_API_URL;
		}
		
		return self::PROD_API_URL;
	}

	/**
	 * Constructor.
	 *
	 * @param int $timeout Request timeout in seconds (default: 30).
	 */
	public function __construct( int $timeout = 30 ) {
		$this->base_api_url = rtrim( self::get_default_api_url(), '/' );
		$this->token = $this->get_woodmart_token();
		$this->timeout = $timeout;
	}

	// ========================================
	// PRIVATE HELPER METHODS
	// ========================================

	/**
	 * Check if token is available for API calls.
	 *
	 * @return bool
	 */
	private function has_token() {
		return ! empty( $this->token );
	}

	/**
	 * Get error array for missing token.
	 *
	 * @return array Error response for missing token.
	 */
	private function get_token_error() {
		return array(
			'error' => true,
			'message' => 'WoodMart theme token is required. Please ensure WoodMart theme is activated.',
		);
	}

	/**
	 * Get the submit endpoint URL.
	 *
	 * @return string Full submit endpoint URL.
	 */
	private function get_submit_url() {
		return $this->base_api_url . '/optimize/submit';
	}

	/**
	 * Add token parameter to URL for GET requests.
	 *
	 * @param string $url Base URL.
	 * @param string $token Authentication token.
	 * @return string URL with token parameter.
	 */
	private function add_token_to_url( string $url, string $token ) {
		return add_query_arg( 'token', $token, $url );
	}

	/**
	 * Add token parameter to request body for POST requests.
	 *
	 * @param array  $additional_data Existing form data.
	 * @param string $token Authentication token.
	 * @return array Form data with token added.
	 */
	private function add_token_to_body( array $additional_data, string $token ) {
		$additional_data['token'] = $token;
		return $additional_data;
	}

	/**
	 * Get the status endpoint URL with token.
	 *
	 * @param string $task_id Task ID.
	 * @param string $token Authentication token.
	 * @return string Full status endpoint URL with token.
	 */
	private function get_status_url( string $task_id, string $token ) {
		$base_url = $this->base_api_url . '/optimize/status/' . $task_id;
		return $this->add_token_to_url( $base_url, $token );
	}

	/**
	 * Validate image file.
	 *
	 * @param string $image_path Path to the image file.
	 * @return array|false Error information or false if valid.
	 */
	private function validate_image_file( string $image_path ) {
		if ( ! file_exists( $image_path ) ) {
			return array(
				'error' => true,
				'message' => 'Image file not found: ' . $image_path,
			);
		}

		if ( ! is_readable( $image_path ) ) {
			return array(
				'error' => true,
				'message' => 'Image file is not readable: ' . $image_path,
			);
		}

		return false;
	}

	/**
	 * Consolidated download method for both optimized and WebP images.
	 *
	 * @param string $download_url The download URL.
	 * @param string $type File type for error messages.
	 * @return array Downloaded file data or error information.
	 */
	private function download_file( string $download_url, string $type ) {
		if ( empty( $download_url ) ) {
			return array(
				'error' => true,
				'message' => ucfirst( $type ) . ' download URL is required',
			);
		}

		if ( ! $this->has_token() ) {
			return $this->get_token_error();
		}

		$download_url_with_token = $this->add_token_to_url( $download_url, $this->token );
		$args = $this->build_request( 'GET', $download_url_with_token, [
			'headers' => [ 'Accept' => 'application/octet-stream' ],
		]);

		$response = \wp_remote_request( $download_url_with_token, $args );

		if ( \is_wp_error( $response ) ) {
			return array(
				'error'   => true,
				'message' => ucfirst( $type ) . ' download failed: ' . $response->get_error_message(),
				'code'    => $response->get_error_code(),
			);
		}

		$response_code = \wp_remote_retrieve_response_code( $response );
		$response_body = \wp_remote_retrieve_body( $response );

		if ( $response_code < 200 || $response_code >= 300 ) {
			return array(
				'error'   => true,
				'message' => 'HTTP error during ' . $type . ' download: ' . $response_code,
				'code'    => $response_code,
			);
		}

		return array(
			'error'    => false,
			'content'  => $response_body,
			'size'     => strlen( $response_body ),
			'headers'  => \wp_remote_retrieve_headers( $response ),
		);
	}

	/**
	 * Build request arguments for wp_remote_request.
	 *
	 * @param string $method HTTP method.
	 * @param string $url Request URL.
	 * @param array  $args Request arguments.
	 * @return array
	 */
	private function build_request( string $method, string $url, array $args = [] ) {
		$defaults = [
			'method'      => $method,
			'timeout'     => $this->timeout,
			'sslverify'   => true,
		];

		return \wp_parse_args( $args, $defaults );
	}

	// ========================================
	// UTILITY METHODS
	// ========================================

	/**
	 * Prepare file data for upload.
	 *
	 * @param string $image_path Path to the image file.
	 * @return array File data or error information.
	 */
	private function prepare_file_data( string $image_path ) {
		$file_info = pathinfo( $image_path );
		$mime_type = \wp_check_filetype( $image_path );

		if ( ! $mime_type['type'] ) {
			return array(
				'error' => true,
				'message' => 'Could not determine file MIME type for: ' . $image_path,
			);
		}

		if ( strpos( $mime_type['type'], 'image/' ) !== 0 ) {
			return array(
				'error' => true,
				'message' => 'File is not an image: ' . $image_path,
			);
		}

		return array(
			'name'      => $file_info['basename'],
			'type'      => $mime_type['type'],
			'content'   => file_get_contents( $image_path ),
			'size'      => filesize( $image_path ),
		);
	}

	/**
	 * Build multipart/form-data body for the request.
	 *
	 * @param array  $file_data File information.
	 * @param array  $additional_data Additional form fields.
	 * @param string $boundary Multipart boundary string.
	 * @return string Formatted multipart body.
	 */
	private function build_multipart_body( array $file_data, array $additional_data, string $boundary ) {
		$body = '';

		foreach ( $additional_data as $key => $value ) {
			$body .= "--{$boundary}\r\n";
			$body .= "Content-Disposition: form-data; name=\"{$key}\"\r\n\r\n";
			$body .= "{$value}\r\n";
		}

		$body .= "--{$boundary}\r\n";
		$body .= "Content-Disposition: form-data; name=\"file\"; filename=\"{$file_data['name']}\"\r\n";
		$body .= "Content-Type: {$file_data['type']}\r\n\r\n";
		$body .= $file_data['content'] . "\r\n";
		$body .= "--{$boundary}--\r\n";

		return $body;
	}

	/**
	 * Process the API response.
	 *
	 * @param array|WP_Error $response WordPress HTTP API response.
	 * @return array Processed response data.
	 */
	private function process_response( $response ) {
		if ( is_wp_error( $response ) ) {
			return array(
				'error'   => true,
				'message' => 'Request failed: ' . $response->get_error_message(),
				'code'    => $response->get_error_code(),
			);
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );

		if ( $response_code < 200 || $response_code >= 300 ) {
			return array(
				'error'   => true,
				'message' => 'HTTP error: ' . $response_code,
				'code'    => $response_code,
				'body'    => $response_body,
			);
		}

		$decoded_response = json_decode( $response_body, true );

		if ( json_last_error() !== JSON_ERROR_NONE ) {
			return array(
				'error'   => true,
				'message' => 'Invalid JSON response from API',
				'body'    => $response_body,
			);
		}

		if ( isset( $decoded_response['data']['quota_info'] ) ) {
			$this->store_quota_info( $decoded_response['data']['quota_info'] );
		}

		return array(
			'error'    => false,
			'data'     => $decoded_response,
			'code'     => $response_code,
			'headers'  => wp_remote_retrieve_headers( $response ),
		);
	}

	/**
	 * Get optimization quality from WoodMart theme settings.
	 *
	 * @return int Quality value (10-100).
	 */
	private function get_optimization_quality() {
		return (int) woodmart_get_opt( 'woodmart_optimizer_quality', 75 );
	}

	/**
	 * Get WebP generation setting from WoodMart theme settings.
	 *
	 * @return bool Whether to generate WebP images.
	 */
	private function get_webp_generation_setting() {
		return (bool) woodmart_get_opt( 'woodmart_optimizer_generate_webp', false );
	}

	/**
	 * Get WoodMart theme token.
	 *
	 * @return string|false
	 */
	private function get_woodmart_token() {
		$token = get_option( self::TOKEN_OPTION_KEY );
		
		if ( ! empty( $token ) ) {
			return $token;
		}
		
		return false;
	}

	/**
	 * Store quota information from API response.
	 *
	 * @param array $quota_info Quota information from API response.
	 */
	private function store_quota_info( array $quota_info ) {
		// Store in wp_option for persistent storage
		update_option( self::QUOTA_OPTION_KEY, $quota_info, false );
	}

	// ========================================
	// PUBLIC API METHODS
	// ========================================

	/**
	 * Submit an image for optimization via API.
	 *
	 * @param string $image_path Full path to the image file on the server.
	 * @param array  $additional_data Optional additional data to send with the request.
	 * @return array API response or error information.
	 */
	public function submit_image( string $image_path, array $additional_data = array() ) {
		if ( ! $this->has_token() ) {
			return $this->get_token_error();
		}

		// Validate image file
		$validation_error = $this->validate_image_file( $image_path );
		if ( $validation_error ) {
			return $validation_error;
		}

		// Prepare the file for upload
		$file_data = $this->prepare_file_data( $image_path );
		if ( isset( $file_data['error'] ) ) {
			return $file_data;
		}

		// Add configuration parameters
		$additional_data['quality'] = $this->get_optimization_quality();
		$additional_data['generate_webp'] = $this->get_webp_generation_setting() ? '1' : '0';
		$additional_data = $this->add_token_to_body( $additional_data, $this->token );

		// Build and send request
		$boundary = wp_generate_password( 24, false );
		$body = $this->build_multipart_body( $file_data, $additional_data, $boundary );
		
		$args = $this->build_request( 'POST', $this->get_submit_url(), [
			'headers' => [
				'Content-Type' => 'multipart/form-data; boundary=' . $boundary,
				'Accept' => 'application/json',
			],
			'body' => $body,
		]);

		$response = wp_remote_request( $this->get_submit_url(), $args );
		return $this->process_response( $response );
	}

	/**
	 * Download optimized image from the API.
	 *
	 * @param string $download_url The download URL for the optimized image.
	 * @return array Downloaded file data or error information.
	 */
	public function download_optimized_image( string $download_url ) {
		return $this->download_file( $download_url, 'optimized' );
	}

	/**
	 * Download WebP version of optimized image from the API.
	 *
	 * @param string $webp_download_url The download URL for the WebP version.
	 * @return array Downloaded WebP file data or error information.
	 */
	public function download_webp_image( string $webp_download_url ) {
		return $this->download_file( $webp_download_url, 'webp' );
	}

	/**
	 * Check the status of an optimization task.
	 *
	 * @param string $task_id The task ID to check.
	 * @return array API response or error information.
	 */
	public function check_task_status( string $task_id ) {
		if ( empty( $task_id ) ) {
			return array(
				'error' => true,
				'message' => 'Task ID is required',
			);
		}

		if ( ! $this->has_token() ) {
			return $this->get_token_error();
		}

		$status_url = $this->get_status_url( $task_id, $this->token );
		$args = $this->build_request( 'GET', $status_url, [
			'headers' => [ 'Accept' => 'application/json' ],
		]);

		$response = wp_remote_request( $status_url, $args );
		return $this->process_response( $response );
	}

	/**
	 * Check if WoodMart theme token is available.
	 *
	 * @return bool
	 */
	public function is_token_available() {
		return $this->has_token();
	}

	/**
	 * Get current quota information.
	 *
	 * @return array|false Quota information or false if not available.
	 */
	public function get_quota_info() {
		return get_option( self::QUOTA_OPTION_KEY, false );
	}

	/**
	 * Format quota information for display.
	 *
	 * @return string|false Formatted quota string or false if no quota info available.
	 */
	public function get_formatted_quota() {
		$quota_info = $this->get_quota_info();
		
		if ( ! $quota_info || ! isset( $quota_info['remaining_mb'], $quota_info['limit_mb'] ) ) {
			return false;
		}

		$remaining = round( $quota_info['remaining_mb'], 1 );
		$limit = round( $quota_info['limit_mb'], 1 );
		$used_percentage = round( ( ( $limit - $remaining ) / $limit ) * 100, 1 );

		return sprintf(
			/* translators: %1$s: remaining MB, %2$s: total limit MB, %3$s: used percentage */
			esc_html__( 'Quota: %1$s MB remaining of %2$s MB (%3$s%% used)', 'woodmart-images-optimizer' ),
			$remaining,
			$limit,
			$used_percentage
		);
	}
}