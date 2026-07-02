<?php
/**
 * Google Fonts Utils class.
 *
 * @package woodmart
 */

namespace XTS\Admin\Modules\Options\Google_Fonts;

use XTS\Singleton;

/**
 * Class Utils
 */
class Utils extends Singleton {
	/**
	 * Folders paths and URLs.
	 *
	 * @var array
	 */
	private static $folders = array();

	const FOLDER_BASE       = 'woodmart/google-fonts';
	const FOLDER_FONTS      = 'fonts';
	const AVAILABLE_FOLDERS = array(
		self::FOLDER_FONTS,
	);

	/**
	 * Constructor.
	 */
	public function init() {}

	/**
	 * Generate Google Fonts remote URL based on font data and settings.
	 *
	 * @param array $fonts Associative array of font data indexed by font family names.
	 *
	 * @return string Generated Google Fonts remote URL.
	 */
	public static function get_google_fonts_remote_url( $fonts ) {
		$families = array();

		foreach ( $fonts as $font_name => $variants ) {
			$variants   = self::get_variants_string( $variants );
			$families[] = str_replace( ' ', '+', $font_name ) . $variants;
		}

		$url     = 'https://fonts.googleapis.com/css2?family=' . implode( '&family=', $families );
		$display = woodmart_get_opt( 'google_font_display' );

		if ( $display && 'disable' !== $display ) {
			$url .= '&display=' . $display;
		}

		return $url;
	}

	/**
	 * Get unique font identifier.
	 *
	 * @param string $font_url Font URL.
	 *
	 * @return string
	 */
	public static function get_unique_font_id( $font_url ) {
		return substr( sha1( $font_url ), 0, 8 );
	}

	/**
	 * Get variants string for Google Fonts API based on variants array and settings.
	 *
	 * @param array $variants Array of font variants.
	 *
	 * @return string Variants string formatted for Google Fonts API.
	 */
	public static function get_variants_string( $variants ) {
		if ( empty( $variants ) ) {
			return ':ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900';
		}

		// Detect if any variant is in the old format (e.g., 400italic, 700italic, etc.)
		$has_italic = false;
		$wght       = array();
		$ital       = array();

		foreach ( $variants as $variant ) {
			if ( preg_match( '/^([0-9]+)italic$/', $variant, $m ) ) {
				$ital[]     = $m[1];
				$has_italic = true;
			} elseif ( preg_match( '/^[0-9]+$/', $variant ) ) {
				$wght[] = $variant;
			}
		}

		// Sort for css2 rules.
		sort( $wght );
		sort( $ital );

		if ( $has_italic ) {
			// Compose ":ital,wght@0,400;1,400;0,700;1,700 etc".
			$all = array();

			foreach ( $wght as $weight ) {
				$all[] = '0,' . $weight;
			}

			foreach ( $ital as $italic_variant ) {
				$all[] = '1,' . $italic_variant;
			}

			$variants = ':ital,wght@' . implode( ';', $all );
		} else {
			$variants = ':wght@' . implode( ';', $wght );
		}

		return $variants;
	}

	/**
	 * Sanitize font name for use in file names and keys.
	 *
	 * @param string $font_name Font family name.
	 *
	 * @return string Sanitized font name.
	 */
	public static function sanitize_font_name( $font_name ) {
		return sanitize_key( $font_name );
	}

	/**
	 * Get human-readable last updated time.
	 *
	 * @return string
	 */
	public static function get_human_last_updated() {
		$time_updated = Local_Data::get_instance()->get_settings( 'time_updated' );

		if ( ! $time_updated ) {
			return esc_html__( 'Never', 'woodmart' );
		}

		$time_diff = time() - $time_updated;

		if ( $time_diff < MINUTE_IN_SECONDS ) {
			$time_string = esc_html__( 'Just now', 'woodmart' );
		} elseif ( $time_diff < DAY_IN_SECONDS ) {
			// translators: 1. Date diff since wishlist creation (EG: 1 hour, 2 seconds, etc...).
			$time_string = sprintf( esc_html__( '%s ago', 'woodmart' ), human_time_diff( $time_updated ) );
		} else {
			$time_string = date_i18n( wc_date_format(), $time_updated );
		}

		return $time_string;
	}

	/**
	 * Check if two variant arrays differ.
	 *
	 * @param array $cached_variants Cached font variants.
	 * @param array $required_variants Required font variants.
	 *
	 * @return bool True if variants differ, false otherwise.
	 */
	public static function variants_differ( $cached_variants, $required_variants ) {
		return (bool) ( array_diff( $cached_variants, $required_variants ) || array_diff( $required_variants, $cached_variants ) );
	}

	/**
	 * Get fonts folder information.
	 *
	 * @return array Fonts folder path and URL.
	 */
	public static function get_fonts_folder() {
		return self::get_folder( self::FOLDER_FONTS );
	}

	/**
	 * Get folder information by folder name.
	 *
	 * @param string $folder Folder name (e.g., 'css' or 'fonts').
	 *
	 * @return array Folder path and URL.
	 */
	public static function get_folder( $folder ) {
		self::init_folders();

		return self::$folders[ $folder ] ?? array();
	}

	/**
	 * Initialize folders for CSS and fonts if not already initialized.
	 *
	 * @return void
	 */
	private static function init_folders() {
		if ( ! empty( self::$folders ) ) {
			return;
		}

		$upload_dir = wp_upload_dir();

		foreach ( self::AVAILABLE_FOLDERS as $folder ) {
			$folder_path = $upload_dir['basedir'] . '/' . self::FOLDER_BASE . '/' . $folder;
			$folder_url  = $upload_dir['baseurl'] . '/' . self::FOLDER_BASE . '/' . $folder;

			if ( ! file_exists( $folder_path ) ) {
				wp_mkdir_p( $folder_path );
			}

			self::$folders[ $folder ] = array(
				'path' => trailingslashit( $folder_path ),
				'url'  => trailingslashit( $folder_url ),
			);
		}
	}
}
