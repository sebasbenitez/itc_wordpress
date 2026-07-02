<?php
/**
 * Manage google fonts.
 *
 * @package woodmart
 */

namespace XTS\Admin\Modules\Options\Google_Fonts;

use XTS\Singleton;

/**
 * Class to manage google fonts.
 *
 * @since 1.0.0
 */
class Google_Fonts extends Singleton {
	/**
	 * All Google fonts array.
	 *
	 * @var array
	 */
	public static $all_google_fonts = array();

	/**
	 * Google fonts array that will be displayed on frontend.
	 *
	 * @var array
	 */
	private static $google_fonts = array();

	/**
	 * Local Google Fonts handler instance.
	 *
	 * @var Local|null
	 */
	private static $local_fonts_handler = null;

	/**
	 * Google Fonts Utils instance.
	 *
	 * @var Utils|null
	 */
	private static $fonts_utils = null;

	/**
	 * Local Google Fonts Data manager instance.
	 *
	 * @var Local_Data|null
	 */
	private static $local_fonts_data = null;

	/**
	 * Register hooks and load base data.
	 *
	 * Initializes Google fonts array, registers action hooks for downloading fonts
	 * after settings save, and enqueuing fonts on frontend and admin pages.
	 *
	 * @return void
	 */
	public function init() {
		self::$all_google_fonts    = require __DIR__ . '/config/google-fonts.php';
		self::$local_fonts_handler = Local::get_instance();
		self::$fonts_utils         = Utils::get_instance();
		self::$local_fonts_data    = Local_Data::get_instance();

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ), 30000 );
		add_action( 'admin_print_styles-post.php', array( $this, 'enqueue' ), 30000 );
		add_action( 'admin_print_styles-post-new.php', array( $this, 'enqueue' ), 30000 );
		add_action( 'admin_print_styles-widgets.php', array( $this, 'enqueue' ), 30000 );
	}

	/**
	 * Enqueue Google fonts on frontend.
	 *
	 * Enqueues Google fonts either from local storage or CDN depending on
	 * the 'local_google_fonts' theme option. Falls back to CDN if no local
	 * fonts are available.
	 *
	 * @return void
	 */
	public function enqueue() {
		if ( woodmart_get_opt( 'local_google_fonts' ) ) {
			self::$local_fonts_handler->enqueue_styles();
		}

		$this->enqueue_from_cdn();
	}

	/**
	 * Get fonts from Google based on all fonts selected in the settings (API v2).
	 *
	 * Builds a Google Fonts API v2 URL with all required font families and variants,
	 * then enqueues the stylesheet from Google's CDN.
	 *
	 * @return void
	 */
	public function enqueue_from_cdn() {
		if ( ! self::$google_fonts ) {
			return;
		}

		$fonts = self::prepare_fonts_variants( self::$google_fonts );
		$url   = self::$fonts_utils::get_google_fonts_remote_url( $fonts );

		wp_enqueue_style( 'xts-google-fonts', $url, array(), null ); // phpcs:ignore.
	}

	/**
	 * Enqueue inline google fonts.
	 *
	 * @param array $fonts Fonts array.
	 * @param int   $post_id Post ID.
	 * @return void
	 */
	public function enqueue_inline_fonts( $fonts, $post_id ) {
		$google_fonts = array();

		foreach ( $fonts as $font ) {
			$defaults = array(
				'font-family' => '',
				'font-weight' => '',
				'font-style'  => '',
			);

			$font = wp_parse_args( $font, $defaults );

			// Skip if font-family is not set or invalid.
			if ( ! self::is_valid_font_family( $font['font-family'] ) ) {
				continue;
			}

			$sanitized_name = sanitize_key( $font['font-family'] );

			if ( ! isset( self::$all_google_fonts[ $font['font-family'] ] ) ) {
				continue;
			}

			$font_data = self::get_font( $font );

			if ( empty( $font_data['font-weight'] ) && empty( $font_data['variants'] ) ) {
				continue;
			}

			// Exclude local variants if this is a local font.
			$font_data = self::maybe_exclude_local_variants( $font_data );

			// Exclude CDN variants if this font is already enqueued from CDN.
			$font_data = self::maybe_exclude_cdn_variants( $font_data );

			if ( empty( $font_data ) ) {
				continue;
			}

			$google_fonts[]                        = $font_data;
			self::$google_fonts[ $sanitized_name ] = $font_data;
		}

		if ( empty( $google_fonts ) ) {
			return;
		}

		$fonts = self::prepare_fonts_variants( $google_fonts );
		$url   = self::$fonts_utils::get_google_fonts_remote_url( $fonts );

		if ( $url ) {
			echo '<link rel="stylesheet" id="xts-google-fonts-inline-' . $post_id . '-css" href="' . $url . '" type="text/css" media="all" />'; // phpcs:ignore
		}
	}

	/**
	 * Add google font.
	 *
	 * Adds a Google font to the queue for enqueuing. Automatically includes default
	 * font weights (300, 400, 600, 700) if available for the font family.
	 *
	 * @param array $font {
	 *     Font configuration array.
	 *
	 *     @type string $font-family Font family name.
	 *     @type string $font-weight Font weight (optional).
	 *     @type string $font-style  Font style (optional).
	 * }
	 *
	 * @return void
	 */
	public static function add_google_font( $font ) {
		$defaults = array(
			'font-family' => '',
			'font-weight' => '',
			'font-style'  => '',
		);

		$font        = wp_parse_args( $font, $defaults );
		$font_family = $font['font-family'];
		$is_var      = 0 === strpos( trim( $font_family ), 'var(' );

		if ( $is_var || empty( $font_family ) ) {
			return;
		}

		$font_to_add = self::get_font( $font );

		if ( empty( $font_to_add['font-weight'] ) && empty( $font_to_add['variants'] ) ) {
			return;
		}

		// Exclude local variants if this is a local font.
		$font_to_add = self::maybe_exclude_local_variants( $font_to_add );

		if ( empty( $font_to_add ) ) {
			return;
		}

		self::$google_fonts[ sanitize_key( $font_family ) ] = $font_to_add;
	}

	/**
	 * Remove google font.
	 *
	 * Removes a Google font from the queue for enqueuing.
	 *
	 * @param string $font_family Font family name to remove.
	 *
	 * @return void
	 */
	public static function remove_google_font( $font_family ) {
		$sanitize_font_name = sanitize_key( $font_family );

		if ( isset( self::$google_fonts[ $sanitize_font_name ] ) ) {
			unset( self::$google_fonts[ $sanitize_font_name ] );
		}
	}

	/**
	 * Get font data.
	 *
	 * @param array $font Font data.
	 *
	 * @return array
	 */
	public static function get_font( $font ) {
		$font_family        = $font['font-family'];
		$sanitize_font_name = sanitize_key( $font_family );

		// Validate font family.
		if ( ! self::is_valid_font_family( $font_family ) ) {
			return array();
		}

		// Check if font exists in available fonts.
		if ( ! isset( self::$all_google_fonts[ $font_family ] ) ) {
			return array();
		}

		// Initialize font data.
		$font_to_add = array(
			'font-family' => $font_family,
			'font-weight' => $font['font-weight'],
			'font-style'  => $font['font-style'],
			'variants'    => self::merge_existing_variants( $sanitize_font_name ),
		);

		if ( ! empty( $font['variants'] ) ) {
			$font_to_add['variants'] = array_merge( $font_to_add['variants'], $font['variants'] );
		}

		// Add variants if available for this font.
		if ( ! isset( self::$all_google_fonts[ $font_family ]['variants'] ) ) {
			return $font_to_add;
		}

		$all_variants = self::$all_google_fonts[ $font_family ]['variants'];

		// Add custom variant based on font weight and style.
		$font_to_add = self::add_custom_variant(
			$font_to_add,
			$font['font-weight'],
			$font['font-style'],
			$all_variants
		);

		// Add custom advanced variants.
		if ( ! empty( $font['advanced-variants'] ) && is_array( $font['advanced-variants'] ) ) {
			foreach ( $font['advanced-variants'] as $variant_id ) {
				$font_to_add = self::add_custom_variant(
					$font_to_add,
					$variant_id,
					'',
					$all_variants
				);
			}
		}

		$font_to_add = self::maybe_add_default_font_data( $font_to_add );

		return $font_to_add;
	}

	/**
	 * Maybe add default font data.
	 *
	 * @param array $font_data Font data to check and potentially modify.
	 *
	 * @return array Updated font data with default weights added if applicable.
	 */
	public static function maybe_add_default_font_data( $font_data ) {
		$font_family  = $font_data['font-family'];
		$variants_ids = array_column( self::$all_google_fonts[ $font_family ]['variants'], 'id' );

		if (
			(
				empty( $font_data['font-weight'] ) &&
				empty( $font_data['variants'] )
			) ||
			(
				! empty( $font_data['font-weight'] ) &&
				! in_array( $font_data['font-weight'], $variants_ids, true )
			)
		) {
			$default_font_weights = self::get_default_font_weights( $font_family );
			$variants             = array();

			if ( ! empty( $default_font_weights ) ) {
				$default_weight = reset( $default_font_weights );

				$font_data['font-weight'] = $default_weight;
				$variants                 = array(
					array(
						'id'   => $default_weight,
						'name' => 'Normal ' . $default_weight,
					),
				);
			}

			$font_data['variants'] = $variants;
		}

		return $font_data;
	}

	/**
	 * Prepare map of font families to variants.
	 *
	 * @param array $fonts Array of fonts data.
	 *
	 * @return array Associative array [ font-family => variants ].
	 */
	public static function prepare_fonts_variants( $fonts ) {
		$prepared_fonts = array();

		foreach ( (array) $fonts as $font ) {
			if ( ! is_array( $font ) || empty( $font['font-family'] ) ) {
				continue;
			}

			$font_family = $font['font-family'];
			$variants    = array();

			if ( array_key_exists( 'variants', $font ) ) {
				$variants = array_unique( array_column( $font['variants'], 'id' ) );
			}

			if ( isset( $prepared_fonts[ $font_family ] ) || empty( $variants ) ) {
				continue;
			}

			$prepared_fonts[ $font_family ] = $variants;
		}

		return $prepared_fonts;
	}

	/**
	 * Validate font family name.
	 *
	 * Checks if font family is not empty and not a CSS variable.
	 *
	 * @param string $font_family Font family name.
	 *
	 * @return bool True if valid, false otherwise.
	 */
	private static function is_valid_font_family( $font_family ) {
		$is_var = 0 === strpos( trim( $font_family ), 'var(' );

		return ! empty( $font_family ) && ! $is_var;
	}

	/**
	 * Find variant index in variants array.
	 *
	 * @param string $variant_id Variant ID to find.
	 * @param array  $variants Array of variants.
	 *
	 * @return int|false Variant index or false if not found.
	 */
	private static function find_variant_index( $variant_id, $variants ) {
		return array_search( $variant_id, array_column( $variants, 'id' ) ); // phpcs:ignore
	}

	/**
	 * Check if variant exists in array.
	 *
	 * @param array $variant Variant to check.
	 * @param array $variants Array of existing variants.
	 *
	 * @return bool True if variant exists, false otherwise.
	 */
	private static function variant_exists( $variant, $variants ) {
		foreach ( $variants as $existing_variant ) {
			if ( $existing_variant['id'] === $variant['id'] ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Merge existing variants from already loaded fonts.
	 *
	 * @param string $sanitize_font_name Sanitized font name.
	 *
	 * @return array Existing variants or empty array.
	 */
	private static function merge_existing_variants( $sanitize_font_name ) {
		if ( array_key_exists( $sanitize_font_name, self::$google_fonts ) && array_key_exists( 'variants', self::$google_fonts[ $sanitize_font_name ] ) ) {
			return self::$google_fonts[ $sanitize_font_name ]['variants'];
		}

		return array();
	}

	/**
	 * Get default font weights for a font family.
	 *
	 * Adds standard weights (300, 400, 600, 700) if they exist for the font.
	 * Skips 300 if 400 exists, and skips 700 if 600 exists.
	 *
	 * @param string $font_family Font family name to check for default weights.
	 *
	 * @return array Default font weights for the font family.
	 */
	public static function get_default_font_weights( $font_family ) {
		if ( ! array_key_exists( $font_family, self::$all_google_fonts ) || ! isset( self::$all_google_fonts[ $font_family ]['variants'] ) ) {
			return array();
		}

		$all_variants        = self::$all_google_fonts[ $font_family ]['variants'];
		$weights             = array();
		$default_font_weight = array(
			'400' => '400',
			'600' => '600',
			'300' => '300',
			'700' => '700',
		);

		// First pass: determine which defaults to skip.
		foreach ( $default_font_weight as $font_weight ) {
			$search = (string) self::find_variant_index( $font_weight, $all_variants );

			if ( $search || '0' === $search ) {
				if ( '400' === $all_variants[ $search ]['id'] ) {
					unset( $default_font_weight['300'] );
				}

				if ( '600' === $all_variants[ $search ]['id'] ) {
					unset( $default_font_weight['700'] );
				}
			} else {
				unset( $default_font_weight[ $font_weight ] );
			}
		}

		// Second pass: add remaining defaults if they don't exist.
		foreach ( $default_font_weight as $font_weight ) {
			$search = (string) self::find_variant_index( $font_weight, $all_variants );

			if ( $search || '0' === $search ) {
				$variant = $all_variants[ $search ];

				if ( ! in_array( $variant['id'], $weights, true ) ) {
					$weights[] = $variant['id'];
				}
			}
		}

		return $weights;
	}

	/**
	 * Add custom variant based on font weight and style.
	 *
	 * @param array  $font_to_add Font data being constructed.
	 * @param string $font_weight Font weight.
	 * @param string $font_style Font style.
	 * @param array  $all_variants All available variants for this font.
	 *
	 * @return array Updated font data with custom variant.
	 */
	private static function add_custom_variant( $font_to_add, $font_weight, $font_style, $all_variants ) {
		if ( empty( $font_weight ) ) {
			return $font_to_add;
		}

		$search = self::find_variant_index( $font_weight . $font_style, $all_variants );

		if ( false !== $search ) {
			$variant = $all_variants[ $search ];

			if ( ! self::variant_exists( $variant, $font_to_add['variants'] ) ) {
				$font_to_add['variants'][] = $variant;
			}
		}

		return $font_to_add;
	}

	/**
	 * Exclude local font variants from Google Fonts if the same font family is available locally.
	 *
	 * @param array $font_to_add Font data being constructed for Google Fonts.
	 *
	 * @return array Updated font data with local variants excluded if applicable.
	 */
	private static function maybe_exclude_local_variants( $font_to_add ) {
		if ( ! woodmart_get_opt( 'local_google_fonts' ) || empty( $font_to_add ) || ! isset( $font_to_add['font-family'] ) ) {
			return $font_to_add;
		}

		$font_family   = $font_to_add['font-family'];
		$is_local_font = self::$local_fonts_data->check_is_local_font( $font_family );

		if ( $is_local_font ) {
			$missed_variants = self::find_missed_local_variants( $font_to_add );

			if ( ! empty( $missed_variants ) ) {
				$font_to_add['variants'] = array_values(
					array_filter(
						$font_to_add['variants'],
						function ( $variant ) use ( $missed_variants ) {
							return in_array( $variant['id'], $missed_variants, true );
						}
					)
				);
			} else {
				return array();
			}
		}

		return $font_to_add;
	}

	/**
	 * Find variants that are missing from local storage for a given font.
	 *
	 * Compares the variants of the font being added with the variants available in local storage.
	 * Returns an array of variant IDs that are required by the current font but not present locally.
	 *
	 * @param array $font_to_add Font data being constructed for Google Fonts.
	 *
	 * @return array List of missing variant IDs that should be included from Google Fonts.
	 */
	private static function find_missed_local_variants( $font_to_add ) {
		$font_family = $font_to_add['font-family'];

		$current_font_variants = self::prepare_fonts_variants( array( $font_to_add ) );
		$current_font_variants = reset( $current_font_variants );

		if ( ! is_array( $current_font_variants ) ) {
			return array();
		}

		$local_fonts            = self::$local_fonts_data->get_raw_fonts_data();
		$storaged_font_variants = $local_fonts[ $font_family ];

		return array_unique( array_values( array_diff( $current_font_variants, $storaged_font_variants ) ) );
	}

	/**
	 * Exclude CDN font variants from Google Fonts if the same font family is already enqueued from CDN.
	 *
	 * @param array $font_to_add Font data being constructed for Google Fonts.
	 *
	 * @return array Updated font data with CDN variants excluded if applicable.
	 */
	private static function maybe_exclude_cdn_variants( $font_to_add ) {
		if ( empty( $font_to_add ) || ! isset( $font_to_add['font-family'] ) ) {
			return $font_to_add;
		}

		$sanitized_name = sanitize_key( $font_to_add['font-family'] );

		if ( ! isset( self::$google_fonts[ $sanitized_name ] ) ) {
			return $font_to_add;
		}

		$missed_variants = self::find_missed_cdn_variants( $font_to_add );

		if ( ! empty( $missed_variants ) ) {
			$font_to_add['variants'] = array_values(
				array_filter(
					$font_to_add['variants'],
					function ( $variant ) use ( $missed_variants ) {
						return in_array( $variant['id'], $missed_variants, true );
					}
				)
			);

			return $font_to_add;
		}

		return array();
	}

	/**
	 * Find variants that are missing from CDN for a given font.
	 *
	 * Compares the variants of the font being added with the variants currently enqueued from CDN.
	 * Returns an array of variant IDs that are required by the current font but not present in
	 * the CDN queue.
	 *
	 * @param array $font_to_add Font data being constructed for Google Fonts.
	 *
	 * @return array List of missing variant IDs that should be included from Google Fonts.
	 */
	private static function find_missed_cdn_variants( $font_to_add ) {
		$sanitized_name = sanitize_key( $font_to_add['font-family'] );

		$current_font_variants = self::prepare_fonts_variants( array( $font_to_add ) );
		$current_font_variants = reset( $current_font_variants );

		if ( ! is_array( $current_font_variants ) ) {
			return array();
		}

		$cdn_font_variants = array_column( self::$google_fonts[ $sanitized_name ]['variants'], 'id' );

		return array_unique( array_values( array_diff( $current_font_variants, $cdn_font_variants ) ) );
	}
}

Google_Fonts::get_instance();
