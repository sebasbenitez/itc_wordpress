<?php
/**
 * Manage local google fonts data.
 *
 * @package woodmart
 */

namespace XTS\Admin\Modules\Options\Google_Fonts;

use XTS\Singleton;

/**
 * Class to manage local google fonts data.
 */
class Local_Data extends Singleton {
	/**
	 * Fonts option name.
	 *
	 * @var string
	 */
	private $fonts_option_name = 'woodmart_local_google_fonts';

	/**
	 * Fonts settings option name.
	 *
	 * @var string
	 */
	private $fonts_settings_option_name = 'woodmart_local_google_fonts_settings';

	/**
	 * Failed fonts option name.
	 *
	 * @var string
	 */
	private $failed_fonts_option_name = 'woodmart_failed_local_google_fonts';

	/**
	 * Initialize module.
	 *
	 * @return void
	 */
	public function init() {}

	/**
	 * Get raw fonts data from storage.
	 *
	 * @return array
	 */
	public function get_raw_fonts_data() {
		return (array) get_option( $this->fonts_option_name, array() );
	}

	/**
	 * Get fonts settings from storage.
	 *
	 * @param string|null $key Optional specific setting key to retrieve. If null, returns all settings.
	 *
	 * @return mixed|null
	 */
	public function get_settings( $key = null ) {
		$settings = (array) get_option( $this->fonts_settings_option_name, array() );

		return $key ? ( $settings[ $key ] ?? null ) : $settings;
	}

	/**
	 * Get failed fonts data from storage.
	 *
	 * @return array
	 */
	public function get_failed_fonts() {
		return (array) get_option( $this->failed_fonts_option_name, array() );
	}

	/**
	 * Update fonts settings in storage.
	 *
	 * @param array $settings Settings array.
	 *
	 * @return void
	 */
	public function save_settings( $settings ) {
		$settings = (array) $settings;

		update_option(
			$this->fonts_settings_option_name,
			$settings,
			false
		);
	}

	/**
	 * Update a specific setting key in storage.
	 *
	 * @param string $key Setting key.
	 * @param mixed  $value Setting value.
	 *
	 * @return void
	 */
	public function update_setting( $key, $value ) {
		$settings         = $this->get_settings();
		$settings[ $key ] = $value;

		$this->save_settings( $settings );
	}

	/**
	 * Update fonts data in storage.
	 *
	 * @param array $fonts Fonts data array.
	 *
	 * @return void
	 */
	public function update_font_variants( $fonts ) {
		update_option( $this->fonts_option_name, $fonts, false );
	}

	/**
	 * Update failed fonts data in storage.
	 *
	 * @param array $fonts Failed fonts data array.
	 *
	 * @return void
	 */
	public function update_failed_fonts( $fonts ) {
		update_option( $this->failed_fonts_option_name, $fonts, false );
	}

	/**
	 * Check if current display setting matches stored setting.
	 *
	 * This is used to determine if the local fonts CSS file is still valid for the current display setting.
	 *
	 * @return bool True if current display setting matches stored setting, false otherwise.
	 */
	public function check_is_current_display() {
		$stored_display  = $this->get_settings( 'display' );
		$current_display = woodmart_get_opt( 'google_font_display' );

		return $stored_display === $current_display;
	}

	/**
	 * Check if a specific font family is stored as a local font and current display setting is valid.
	 *
	 * @param string $font_family Font family name to check.
	 *
	 * @return bool True if font family is stored as a local font and current display setting is valid, false otherwise.
	 */
	public function check_is_local_font( $font_family ) {
		$fonts_data = $this->get_raw_fonts_data();

		return ! empty( $fonts_data[ $font_family ] ) && $this->check_is_current_display();
	}

	/**
	 * Remove font from storage.
	 *
	 * @param string $font_family Font family name.
	 *
	 * @return void
	 */
	public function remove_font( $font_family ) {
		$fonts = $this->get_raw_fonts_data();

		if ( isset( $fonts[ $font_family ] ) ) {
			unset( $fonts[ $font_family ] );
			update_option( $this->fonts_option_name, $fonts, false );
		}
	}

	/**
	 * Clear fonts data.
	 *
	 * @return void
	 */
	public function clear_fonts_data() {
		delete_option( $this->fonts_option_name );
	}
}
