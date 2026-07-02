<?php
/**
 * Enable auto updates.
 *
 * @package woodmart
 */

namespace XTS; // phpcs:ignore WordPress.NamingConventions

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

use stdClass;

/**
 * Enable auto updates
 */
class Autoupdates {
	/**
	 * API instance.
	 *
	 * @var object
	 */
	private $api;
	/**
	 * Notices instance.
	 *
	 * @var object
	 */
	private $notices;
	/**
	 * Current version.
	 *
	 * @var string
	 */
	private $current_version;
	/**
	 * Theme name.
	 *
	 * @var string
	 */
	private $theme_name;
	/**
	 * Old API URL.
	 *
	 * @var string
	 */
	private $old_api_url = 'https://xtemos.com/licenses/api/';
	/**
	 * Info object.
	 *
	 * @var object
	 */
	private $info;

	/**
	 * Available languages for auto update.
	 *
	 * @var array
	 */
	private $available_languages = array(
		'ar',
		'bg_BG',
		'cs_CZ',
		'da_DK',
		'de_DE',
		'el',
		'es_ES',
		'et',
		'fi',
		'ga',
		'fr_FR',
		'he_IL',
		'hu_HU',
		'id_ID',
		'it_IT',
		'ja',
		'ko_KR',
		'lt_LT',
		'lv',
		'nb_NO',
		'nl_NL',
		'pl_PL',
		'pt_BR',
		'pt_PT',
		'ro_RO',
		'ru_RU',
		'sk_SK',
		'sl_SI',
		'sv_SE',
		'th',
		'tl',
		'tr_TR',
		'uk',
		'vi',
		'zh_CN',
	);

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->current_version = woodmart_get_theme_info( 'Version' );
		$this->theme_name      = WOODMART_SLUG;

		$this->api     = Registry::get_instance()->api;
		$this->notices = Registry::get_instance()->notices;

		add_action( 'admin_post_xts_reset_translations', array( $this, 'reset_translations' ) );

		add_filter( 'pre_set_site_transient_update_themes', array( $this, 'update_plugins_version' ), 30 );
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'update_plugins_version' ), 30 );
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'register_core_plugin_update' ), 30 );
		add_filter( 'woodmart_setup_wizard', array( $this, 'update_plugins_version' ), 30 );

		if ( ! woodmart_is_license_activated() ) {
			return;
		}

		add_filter( 'site_transient_update_themes', array( $this, 'update_transient' ), 20, 2 );

		add_filter( 'pre_set_site_transient_update_themes', array( $this, 'set_update_transient' ) );
		add_filter( 'themes_api', array( &$this, 'api_results' ), 10, 3 );

		add_action( 'upgrader_process_complete', array( $this, 'store_installed_languages_information' ), 10, 2 );
	}

	/**
	 * Reset translations.
	 */
	public function reset_translations() {
		if (
			! current_user_can( 'manage_options' ) ||
			empty( $_GET['action'] ) ||
			empty( $_GET['_wpnonce'] ) ||
			'xts_reset_translations' !== $_GET['action'] ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'xts_reset_translations_nonce' )
		) {
			return;
		}

		$languages_dir = WP_LANG_DIR . '/themes/';
		$extensions    = array( 'mo', 'po', 'l10n.php' );

		foreach ( $this->available_languages as $language ) {
			foreach ( $extensions as $ext ) {
				$file = $languages_dir . 'woodmart-' . $language . '.' . $ext;

				if ( file_exists( $file ) ) {
					wp_delete_file( $file );
				}
			}
		}

		delete_option( 'woodmart_installed_languages' );
		delete_option( 'woodmart_translations_version' );

		wp_safe_redirect( admin_url( 'admin.php?page=xts_status' ) );
		exit;
	}

	/**
	 * Update transient.
	 *
	 * @param string $value Data.
	 *
	 * @return false|mixed
	 */
	public function update_transient( $value ) {
		if ( isset( $_GET['force-check'] ) && '1' === (string) $_GET['force-check'] ) { // phpcs:ignore WordPress.Security
			return false;
		}

		return $value;
	}

	/**
	 * Update Transient.
	 *
	 * @param string $transient Transient.
	 *
	 * @return mixed
	 */
	public function set_update_transient( $transient ) {
		$this->check_for_update();

		if ( ! is_object( $transient ) ) {
			$transient = new stdClass();
		}

		if ( ! isset( $transient->response ) || ! is_array( $transient->response ) ) {
			$transient->response = array();
		}

		if ( ! empty( $this->info ) && is_object( $this->info ) ) {
			if ( $this->is_update_available() ) {
				$transient->response[ $this->theme_name ] = json_decode( wp_json_encode( $this->info ), true );
			}
		}

		// Add translation updates if option is enabled.
		if ( woodmart_get_opt( 'auto_update_translations', '0' ) ) {
			$locales              = array_values( get_available_languages() );
			$locales              = array_unique( $locales );
			$installed_languages  = get_option( 'woodmart_installed_languages', array() );
			$translations_version = get_option( 'woodmart_translations_version', '' );

			$compare_version = version_compare( $translations_version, $this->current_version );

			foreach ( $this->available_languages as $language ) {
				// Skip if language is not active on the site.
				if ( ! in_array( $language, $locales, true ) ) {
					continue;
				}

				$is_installed = in_array( $language, $installed_languages, true );
				$needs_update = version_compare( $translations_version, $this->current_version ) < 0;

				// Add translation if it's not installed OR if it needs an update.
				if ( ! $is_installed || $needs_update ) {
					$transient->translations[] = array(
						'type'       => 'theme',
						'slug'       => $this->theme_name,
						'version'    => $this->info->new_version,
						'updated'    => time(),
						'package'    => 'https://woodmart.xtemos.com/translations/' . $this->current_version . '/woodmart-' . $language . '.zip',
						'autoupdate' => true,
						'language'   => $language,
					);
				}
			}
		}

		remove_action( 'site_transient_update_themes', array( $this, 'update_transient' ), 20, 2 );

		return $transient;
	}


	/**
	 * Store installed languages information.
	 *
	 * @param object $upgrader Upgrader object.
	 * @param array  $data Data.
	 * @return void
	 */
	public function store_installed_languages_information( $upgrader, $data ) {
		if ( ! isset( $data['translations'] ) || ! is_array( $data['translations'] ) || ! woodmart_get_opt( 'auto_update_translations', '0' ) ) {
			return;
		}

		$installed_languages = get_option( 'woodmart_installed_languages', array() );
		foreach ( $data['translations'] as $translation ) {
			if ( $translation['slug'] !== $this->theme_name || ! isset( $translation['language'] ) || ! in_array( $translation['language'], $this->available_languages, true ) ) {
				continue;
			}

			$installed_languages[] = $translation['language'];
		}
		if ( ! empty( $installed_languages ) ) {
			update_option( 'woodmart_installed_languages', $installed_languages, false );
			update_option( 'woodmart_translations_version', $this->current_version, false );
		}
	}


	/**
	 * Get API result.
	 *
	 * @param string $result API result.
	 * @param string $action Action.
	 * @param object $args Args.
	 *
	 * @return mixed
	 */
	public function api_results( $result, $action, $args ) {
		$this->check_for_update();

		if ( isset( $args->slug ) && $args->slug === $this->theme_name && 'theme_information' === $action ) {
			if ( is_object( $this->info ) && ! empty( $this->info ) ) {
				$result = $this->info;
			}
		}

		return $result;
	}

	/**
	 * Check for theme update.
	 *
	 * @return void
	 */
	protected function check_for_update() {
		$force = false;

		if ( isset( $_GET['force-check'] ) && '1' === (string) $_GET['force-check'] ) { // phpcs:ignore WordPress.Security
			$force = true;
		}

		// Get data.
		if ( empty( $this->info ) ) {
			$version_information = get_option( 'woodmart-update-info', false );
			$version_information = $version_information ? $version_information : new stdClass();

			$this->info = is_object( $version_information ) ? $version_information : maybe_unserialize( $version_information );
		}

		$last_check = get_option( 'woodmart-update-time' );

		if ( ! $last_check ) {
			update_option( 'woodmart-update-time', time() );
		}

		if ( time() - $last_check > 172800 || $force || ! $last_check ) {
			$response = $this->api_info();

			update_option( 'woodmart-update-time', time() );

			$this->info              = new stdClass();
			$this->info->new_version = $response->version;
			$this->info->version     = $response->version;
			$this->info->theme       = $response->theme;
			$this->info->checked     = time();
			$this->info->url         = 'https://xtemos.com/woodmart-changelog.php';
			$this->info->package     = $this->download_url();
		}

		// Save results.
		update_option( 'woodmart-update-info', $this->info );
	}

	/**
	 * Get API info.
	 *
	 * @return array|mixed|stdClass|null
	 */
	public function api_info() {
		$version_information = new stdClass();

		$response = $this->api->call(
			'info/' . $this->theme_name,
			array(),
			'get',
			$this->old_api_url
		);

		$response_code = wp_remote_retrieve_response_code( $response );

		if ( '200' !== (string) $response_code ) {
			return array();
		}

		$response = json_decode( wp_remote_retrieve_body( $response ) );
		if ( ! $response->version ) {
			return $version_information;
		}

		return $response;
	}

	/**
	 * Update plugin version.
	 *
	 * @param mixed $transient Transient.
	 *
	 * @return mixed
	 */
	public function update_plugins_version( $transient ) {
		$api        = Registry::get_instance()->api;
		$plugins    = array( 'js_composer', 'woodmart-images-optimizer' );
		$force      = false;
		$last_check = get_option( 'woodmart-plugins-update-time' );

		if ( ( isset( $_GET['force-check'] ) && '1' === (string) $_GET['force-check'] ) || ( isset( $_GET['tab'] ) && 'wizard' === $_GET['tab'] ) ) { // phpcs:ignore WordPress.Security
			$force = true;
		}

		if ( ! $last_check ) {
			update_option( 'woodmart-plugins-update-time', time() );
		}

		if ( time() - $last_check > 172800 || $force || ! $last_check ) {
			update_option( 'woodmart-plugins-update-time', time() );

			foreach ( $plugins as $plugin ) {
				$query         = $this->api->call(
					'info/' . $plugin,
					array(),
					'get',
					$this->old_api_url
				);
				$response_code = wp_remote_retrieve_response_code( $query );

				if ( '200' !== (string) $response_code ) {
					continue;
				}

				$response = json_decode( wp_remote_retrieve_body( $query ) );

				if ( empty( $response ) || ! property_exists( $response, 'version' ) ) {
					continue;
				}

				update_option( 'woodmart_' . $plugin . '_version', $response->version );
			}
		}

		return $transient;
	}

	/**
	 * Check is update available.
	 *
	 * @return bool|int
	 */
	public function is_update_available() {
		return version_compare( $this->current_version, $this->release_version(), '<' );
	}

	/**
	 * Get download url files.
	 */
	public function download_url() {
		return $this->api->get_url(
			'download',
			array(
				'token' => get_option( 'woodmart_token' ),
				'theme' => $this->theme_name,
			)
		);
	}

	/**
	 * Get maybe next version.
	 *
	 * @return mixed
	 */
	public function release_version() {
		$this->check_for_update();
		return $this->info->new_version;
	}

	/**
	 * Register Woodmart core plugin update.
	 *
	 * @param object $transient Transient.
	 * @return object
	 */
	public function register_core_plugin_update( $transient ) {
		$plugin_slug = 'woodmart-core/woodmart-core.php';

		if ( empty( $transient->checked ) || empty( $transient->checked[ $plugin_slug ] ) ) {
			return $transient;
		}

		$current_version = $transient->checked[ $plugin_slug ];
		$new_version     = WOODMART_CORE_VERSION;

		$item = (object) array(
			'slug'        => 'woodmart-core',
			'plugin'      => $plugin_slug,
			'new_version' => $new_version,
			'package'     => get_parent_theme_file_path( WOODMART_FRAMEWORK . '/plugins/woodmart-core.zip' ),
		);

		if ( version_compare( $current_version, $new_version, '<' ) ) {
			$transient->response[ $plugin_slug ] = $item;
		} else {
			$transient->no_update[ $plugin_slug ] = $item;
		}

		return $transient;
	}
}
