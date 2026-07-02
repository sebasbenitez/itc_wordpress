<?php
/**
 * Manage local google fonts.
 *
 * @package woodmart
 */

namespace XTS\Admin\Modules\Options\Google_Fonts;

use XTS\Singleton;
use XTS\Admin\Modules\Options;
use XTS\Modules\Styles_Storage;

/**
 * Class to manage local google fonts.
 */
class Local extends Singleton {
	/**
	 * Instance of local google fonts data manager.
	 *
	 * @var Local_Data|null
	 */
	private $fonts_data = null;

	/**
	 * Google Fonts Utils instance.
	 *
	 * @var Utils|null
	 */
	private $fonts_utils = null;

	/**
	 * Styles storage instance for managing local font files.
	 *
	 * @var Styles_Storage|null
	 */
	private $fonts_storage = null;

	/**
	 * User agent string for Google Fonts API requests.
	 *
	 * @var string
	 */
	const UA_STRING = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36';

	/**
	 * Constructor.
	 */
	public function init() {
		$this->fonts_data    = Local_Data::get_instance();
		$this->fonts_utils   = Utils::get_instance();
		$this->fonts_storage = new Styles_Storage( 'local-google-fonts', 'option', '', false );

		add_action( 'xts_theme_settings_save', array( $this, 'download_fonts_after_settings_save' ) );

		add_action( 'init', array( $this, 'reload_local_google_fonts_on_status_page' ) );
		add_action( 'woodmart_updated', array( $this, 'reload_local_google_fonts' ) );
	}

	/**
	 * Enqueue local font style.
	 *
	 * Attempts to enqueue a locally stored Google font CSS file.
	 *
	 * @return bool True if font was successfully enqueued, false otherwise.
	 */
	public function enqueue_styles() {
		if ( ! $this->fonts_data->check_is_current_display() ) {
			return false;
		}

		$this->fonts_storage->print_styles();

		return true;
	}

	/**
	 * Download all required fonts after theme settings save.
	 *
	 * Checks if local Google fonts option is enabled and downloads any fonts
	 * that haven't been cached locally yet.
	 *
	 * @return void
	 */
	public function download_fonts_after_settings_save() {
		if ( ! woodmart_get_opt( 'local_google_fonts' ) ) {
			$this->delete_all_fonts();
			return;
		}

		if ( isset( $_GET['preset'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		$fonts = $this->get_necessary_fonts();

		$this->prune_unused_fonts( $fonts );

		$this->fonts_data->update_setting( 'display', woodmart_get_opt( 'google_font_display' ) );
		$this->fonts_data->update_setting( 'time_updated', time() );

		if ( empty( $fonts ) ) {
			return;
		}

		$this->fetch_local_fonts( $fonts );
	}

	/**
	 * Get list of fonts to download.
	 */
	private function get_necessary_fonts() {
		$options = Options::get_options();
		$fonts   = array();

		if ( ! woodmart_get_opt( 'local_google_fonts' ) || empty( $options ) || ! is_array( $options ) ) {
			return array();
		}

		foreach ( $options as $option_id => $option_value ) {
			if ( ! is_array( $option_value ) ) {
				continue;
			}

			foreach ( $option_value as $value ) {
				if ( ! is_array( $value ) ) {
					continue;
				}

				if ( isset( $value['font-family'] ) && ! empty( $value['font-family'] ) ) {
					$font_family      = $value['font-family'];
					$font_data        = Google_Fonts::get_font( $value );
					$new_variants     = array();
					$current_variants = ( $fonts[ $font_family ] ?? array() );

					if ( empty( $font_data['font-weight'] ) && empty( $font_data['variants'] ) ) {
						continue;
					}

					if ( $font_data && isset( $font_data['variants'] ) ) {
						$new_variants = array_column( $font_data['variants'], 'id' );
					}

					if ( 'text-font' === $option_id ) {
						$default_font_weights = Google_Fonts::get_default_font_weights( $font_family );

						if ( $default_font_weights ) {
							$new_variants = array_merge( $default_font_weights, $new_variants );
						}
					}

					if ( ! empty( $new_variants ) ) {
						$fonts[ $font_family ] = array_values(
							array_unique(
								array_merge( $new_variants, $current_variants )
							)
						);
					}
				}
			}
		}

		return $fonts;
	}

	/**
	 * Delete fonts that are not in the active list.
	 *
	 * Compares currently used fonts with cached fonts in database:
	 * - Removes fonts that are no longer used
	 * - Updates variants for fonts that have variant changes and removes old files
	 *
	 * @param array $fonts Active fonts in format [ font-family => [variants] ].
	 * @return void
	 */
	public function prune_unused_fonts( $fonts ) {
		$fonts           = array_filter( $fonts );
		$available_fonts = $this->fonts_data->get_raw_fonts_data();

		if ( empty( $available_fonts ) ) {
			return;
		}

		$active_font_names    = array_keys( $fonts );
		$available_font_names = array_keys( $available_fonts );
		$stored_display       = $this->fonts_data->get_settings( 'display' );
		$current_display      = woodmart_get_opt( 'google_font_display' );
		$new_fonts_to_load    = array_diff( $active_font_names, $available_font_names );

		if ( $stored_display !== $current_display || $new_fonts_to_load ) {
			$this->delete_css_fonts_data();
		}

		foreach ( $available_fonts as $font_name => $stored_variants ) {
			if ( ! in_array( $font_name, $active_font_names, true ) || ! isset( Google_Fonts::$all_google_fonts[ $font_name ] ) ) {
				$this->delete_font( $font_name );
				continue;
			}

			$current_variants = (array) $fonts[ $font_name ];
			$stored_variants  = (array) $stored_variants;

			if ( $this->fonts_utils::variants_differ( $stored_variants, $current_variants ) ) {
				$this->delete_font( $font_name );
			}
		}
	}

	/**
	 * Fetch multiple fonts and save them locally in a single CSS file.
	 *
	 * Downloads CSS for all fonts in one request, processes and localizes
	 * all font files, and saves them with a single CSS file.
	 *
	 * @param array $fonts Array of font families and their variants.
	 *
	 * @return bool True if fonts were successfully fetched, false otherwise.
	 */
	public function fetch_local_fonts( $fonts ) {
		if ( empty( $fonts ) ) {
			return false;
		}

		if ( $this->is_fonts_cached_and_valid( $fonts ) ) {
			return true;
		}

		$css_content = $this->fetch_and_process_css( $fonts );

		if ( empty( $css_content ) ) {
			return false;
		}

		return $this->save_all_fonts( $fonts, $css_content );
	}

	/**
	 * Check if fonts are cached and valid.
	 *
	 * Verifies if the cached font CSS file corresponds to the current font selection
	 * and if the associated font files exist in local storage.
	 *
	 * @param array $fonts Array of font families and their variants.
	 *
	 * @return bool True if fonts are cached and valid, false otherwise.
	 */
	private function is_fonts_cached_and_valid( $fonts ) {
		$font_url        = $this->fonts_utils::get_google_fonts_remote_url( $fonts );
		$unique_font_id  = $this->fonts_utils::get_unique_font_id( $font_url );
		$is_cached_url   = $unique_font_id === $this->fonts_data->get_settings( 'last_valid_font_id' );
		$css_file_exists = $this->fonts_storage->is_css_exists();

		return $is_cached_url && $css_file_exists;
	}

	/**
	 * Fetch CSS and process it with localized font files for all fonts.
	 *
	 * Retrieves the raw CSS from Google Fonts API for all fonts and processes it
	 * to download and localize all font files into a single CSS with local URLs.
	 *
	 * @param array $fonts Array of font families and their variants.
	 *
	 * @return string Processed CSS content with local font URLs, or empty string on failure.
	 */
	public function fetch_and_process_css( $fonts ) {
		$css_content = $this->fetch_css_from_google( $fonts );

		if ( empty( $css_content ) ) {
			return '';
		}

		$css_content = $this->minify_css( $css_content );

		return $this->process_and_localize_font_files( $css_content );
	}

	/**
	 * Get raw CSS content from Google Fonts API.
	 *
	 * @param array $fonts Array of font families and their variants.
	 *
	 * @return string Raw CSS content from Google Fonts, or empty string on failure.
	 */
	public function fetch_css_from_google( $fonts ) {
		$font_url = $this->fonts_utils::get_google_fonts_remote_url( $fonts );

		$css_content_response = wp_remote_get(
			$font_url,
			array(
				'headers' => array(
					'User-Agent' => self::UA_STRING,
				),
				'timeout' => 15,
			)
		);

		if ( is_wp_error( $css_content_response ) || 200 !== (int) wp_remote_retrieve_response_code( $css_content_response ) ) {
			return '';
		}

		return wp_remote_retrieve_body( $css_content_response );
	}

	/**
	 * Minify CSS content by removing whitespace and comments.
	 *
	 * @param string $css_content Raw CSS content to minify.
	 *
	 * @return string Minified CSS content.
	 */
	public function minify_css( $css_content ) {
		// Remove CSS comments.
		$css_content = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css_content );

		// Remove whitespace around special characters.
		$css_content = preg_replace( '/\s*([{}:;,])\s*/', '$1', $css_content );

		// Remove newlines and multiple spaces.
		$css_content = preg_replace( '/\s+/', ' ', $css_content );

		// Trim leading and trailing whitespace.
		$css_content = trim( $css_content );

		return $css_content;
	}

	/**
	 * Process font files and localize URLs in CSS.
	 *
	 * Parses CSS content for font file URLs, downloads them to local storage,
	 * and replaces remote URLs with local paths in the CSS.
	 *
	 * @param string $css_content Raw CSS content with remote font URLs.
	 *
	 * @return string Modified CSS content with local font URLs, or empty string on failure.
	 */
	public function process_and_localize_font_files( $css_content ) {
		$font_data = $this->parse_font_urls_from_css( $css_content );

		if ( empty( $font_data ) ) {
			return $css_content;
		}

		return $this->download_and_replace_font_urls( $font_data, $css_content );
	}

	/**
	 * Extract all font URLs from CSS content with their font families.
	 *
	 * @param string $css_content CSS content to parse.
	 *
	 * @return array Array of arrays with 'font_family' and 'url' keys.
	 */
	private function parse_font_urls_from_css( $css_content ) {
		$font_data = array();

		// Split by @font-face blocks.
		preg_match_all( '/@font-face\s*\{([^}]+)\}/s', $css_content, $font_face_matches );

		if ( empty( $font_face_matches[1] ) ) {
			return array();
		}

		foreach ( $font_face_matches[1] as $font_face_block ) {
			// Extract font-family.
			if ( preg_match( '/font-family:\s*[\'"](.*?)[\'"]/', $font_face_block, $family_match ) ) {
				$font_family = $family_match[1];

				// Extract URL.
				if ( preg_match( '/url\(([^)]+)\)/', $font_face_block, $url_match ) ) {
					$font_data[] = array(
						'font_family' => $font_family,
						'url'         => $url_match[1],
					);
				}
			}
		}

		return $font_data;
	}

	/**
	 * Download font files and replace URLs in CSS.
	 *
	 * @param array  $font_data Array of font data with 'font_family' and 'url' keys.
	 * @param string $css_content CSS content to update.
	 *
	 * @return string Modified CSS content with local font URLs, or empty string on failure.
	 */
	private function download_and_replace_font_urls( $font_data, $css_content ) {
		if ( ! function_exists( 'download_url' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		$failed_fonts = array();
		$failed_urls  = array();

		foreach ( $font_data as $font_item ) {
			$font_family      = $font_item['font_family'];
			$current_font_url = $font_item['url'];

			$result = $this->download_single_font_file( $font_family, $current_font_url );

			if ( is_wp_error( $result ) ) {
				$failed_fonts[] = $font_family;
				$failed_urls[]  = $current_font_url;
				continue;
			}

			$css_content = str_replace( $current_font_url, $result, $css_content );
		}

		$failed_fonts = array_unique( $failed_fonts );
		$this->fonts_data->update_failed_fonts( $failed_fonts );

		if ( count( $failed_urls ) === count( $font_data ) ) {
			return '';
		}

		return $css_content;
	}

	/**
	 * Download a single font file and return its local URL.
	 *
	 * @param string $font_name Font family name.
	 * @param string $font_url Font file URL to download.
	 *
	 * @return string|WP_Error Local file URL on success, WP_Error on failure.
	 */
	private function download_single_font_file( $font_name, $font_url ) {
		$original_font_url = trim( $font_url, '\'"' );
		$cleaned_url       = set_url_scheme( $original_font_url, 'https' );
		$cleaned_url       = strtok( $cleaned_url, '?#' );

		$font_ext = pathinfo( $cleaned_url, PATHINFO_EXTENSION );

		if ( empty( $font_ext ) ) {
			return new \WP_Error( 'invalid_font_url', "Invalid font URL: {$cleaned_url}" );
		}

		$tmp_font_file = $this->download_font_file_with_retry( $cleaned_url );

		if ( is_wp_error( $tmp_font_file ) ) {
			return $tmp_font_file;
		}

		$fonts_folder       = $this->fonts_utils::get_fonts_folder();
		$sanitize_font_name = $this->fonts_utils::sanitize_font_name( $font_name );
		$unique_font_id     = $this->fonts_utils::get_unique_font_id( $cleaned_url );

		$current_font_basename = sprintf(
			'%s-%s.%s',
			$sanitize_font_name,
			strtolower( sanitize_file_name( $unique_font_id ) ),
			$font_ext
		);

		$dest_file     = $fonts_folder['path'] . $current_font_basename;
		$dest_file_url = wp_make_link_relative( $fonts_folder['url'] . $current_font_basename );

		// Use copy and unlink because rename breaks streams.
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		if ( ! @copy( $tmp_font_file, $dest_file ) ) {
			@unlink( $tmp_font_file ); // phpcs:ignore.
			return new \WP_Error( 'copy_failed', "Failed to copy font file to: {$dest_file}" );
		}

		@unlink( $tmp_font_file ); // phpcs:ignore.

		return $dest_file_url;
	}

	/**
	 * Save all fonts to a single CSS file and update database.
	 *
	 * @param array  $fonts Array of font families and their variants.
	 * @param string $css_content Processed CSS content with local font URLs.
	 */
	private function save_all_fonts( $fonts, $css_content ) {
		$this->fonts_storage->write( $css_content, true );

		$font_url       = $this->fonts_utils::get_google_fonts_remote_url( $fonts );
		$unique_font_id = $this->fonts_utils::get_unique_font_id( $font_url );

		// Save all fonts variants to database.
		$this->fonts_data->update_font_variants( $fonts );
		$this->fonts_data->update_setting( 'last_valid_font_id', $unique_font_id );
	}

	/**
	 * Download a font file with retry logic.
	 *
	 * Attempts to download a file up to 3 times with exponential backoff.
	 *
	 * @param string $url URL to download.
	 * @param int    $attempt Current attempt number.
	 *
	 * @return string|WP_Error Local file path on success, WP_Error on failure.
	 */
	private function download_font_file_with_retry( $url, $attempt = 1 ) {
		$max_attempts = 3;

		$result = download_url( $url );

		if ( ! is_wp_error( $result ) ) {
			return $result;
		}

		if ( $attempt < $max_attempts ) {
			sleep( pow( 2, $attempt - 1 ) );
			return $this->download_font_file_with_retry( $url, $attempt + 1 );
		}

		return $result;
	}

	/**
	 * Reload local Google fonts on status page action.
	 *
	 * @return void
	 */
	public function reload_local_google_fonts_on_status_page() {
		if (
			empty( $_GET['page'] ) ||
			empty( $_GET['action'] ) ||
			empty( $_GET['_wpnonce'] ) ||
			'xts_status' !== $_GET['page'] ||
			'reload_local_google_fonts' !== $_GET['action'] ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'xts_reload_local_google_fonts_nonce' )
		) {
			return;
		}

		$this->reload_local_google_fonts();

		wp_safe_redirect( remove_query_arg( array( 'action', '_wpnonce' ) ) );
	}

	/**
	 * Reload all local Google fonts.
	 *
	 * @return void
	 */
	public function reload_local_google_fonts() {
		$local_google_fonts = $this->fonts_data->get_raw_fonts_data();

		$this->delete_all_fonts();

		$this->fetch_local_fonts( $local_google_fonts );

		$this->fonts_data->update_setting( 'time_updated', time() );
	}

	/**
	 * Delete cached CSS font data.
	 *
	 * @return void
	 */
	public function delete_css_fonts_data() {
		$this->fonts_storage->delete_css();
		$this->fonts_storage->reset_data();
		$this->fonts_storage->is_css_exists = false;
	}

	/**
	 * Delete cached assets for a specific font.
	 *
	 * Removes the font's CSS file, associated font files, and its
	 * record from the local Google fonts option.
	 *
	 * @param string $font_family Font family name.
	 *
	 * @return void
	 */
	public function delete_font( $font_family ) {
		$sanitize_font_name = $this->fonts_utils::sanitize_font_name( $font_family );
		$fonts_folder       = $this->fonts_utils::get_fonts_folder();

		$this->delete_css_fonts_data();

		if ( ! empty( $fonts_folder['path'] ) ) {
			foreach ( glob( $fonts_folder['path'] . $sanitize_font_name . '-*.*' ) as $file_path ) {
				unlink( $file_path ); // phpcs:ignore.
			}
		}

		$this->fonts_data->remove_font( $font_family );
	}

	/**
	 * Clear all locally cached Google fonts.
	 *
	 * Deletes all CSS and font files from local storage and removes the
	 * font cache data from the database.
	 *
	 * @return void
	 */
	public function delete_all_fonts() {
		$folder = $this->fonts_utils::get_fonts_folder();

		$path = $folder['path'] . '*';

		foreach ( glob( $path ) as $file_path ) {
			unlink( $file_path ); // phpcs:ignore.
		}

		$this->fonts_data->clear_fonts_data();
		$this->delete_css_fonts_data();
	}
}
