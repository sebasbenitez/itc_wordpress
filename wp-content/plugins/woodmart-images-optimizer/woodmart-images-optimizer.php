<?php
/**
 * Plugin Name: WoodMart Images Optimizer
 * Plugin URI: https://woodmart.xtemos.com
 * Description: Image optimization plugin exclusively for WoodMart theme. Requires WoodMart theme to be active.
 * Version: 1.4.0
 * Author: XTemos
 * Author URI: https://xtemos.com
 * Text Domain: woodmart-images-optimizer
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * Network: false
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

namespace WoodMart\ImagesOptimizer;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// Define plugin constants.
define( 'WOODMART_IMAGES_OPTIMIZER_VERSION', '1.4.0' );
define( 'WOODMART_IMAGES_OPTIMIZER_PLUGIN_FILE', __FILE__ );
define( 'WOODMART_IMAGES_OPTIMIZER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WOODMART_IMAGES_OPTIMIZER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WOODMART_IMAGES_OPTIMIZER_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Main Plugin Bootstrap Class
 */
class Plugin {

	/**
	 * Plugin instance.
	 *
	 * @var Plugin
	 */
	private static $instance = null;

	/**
	 * Get plugin instance.
	 *
	 * @return Plugin
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		\add_action( 'plugins_loaded', array( $this, 'init' ) );
		\register_activation_hook( __FILE__, array( $this, 'activate' ) );
		\register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
	}

	/**
	 * Initialize the plugin.
	 */
	public function init() {
		// Check if WoodMart theme is active.
		if ( ! $this->is_woodmart_theme_active() ) {
			\add_action( 'admin_notices', array( $this, 'show_theme_dependency_notice' ) );
			return;
		}

		// Load text domain for translations.
		\load_plugin_textdomain( 'woodmart-images-optimizer', false, dirname( \plugin_basename( __FILE__ ) ) . '/languages' );

		// Include required files.
		$this->include_files();

		// Initialize the main functionality.
		if ( \class_exists( 'WoodMart\ImagesOptimizer\Main' ) ) {
			new Main();
		}

		// Add theme settings.
		\add_action( 'init', array( $this, 'add_theme_settings' ), 20 );
	}

	/**
	 * Include required files.
	 */
	private function include_files() {
		require_once WOODMART_IMAGES_OPTIMIZER_PLUGIN_DIR . 'classes/class-helpers.php';
		require_once WOODMART_IMAGES_OPTIMIZER_PLUGIN_DIR . 'classes/class-api-client.php';
		require_once WOODMART_IMAGES_OPTIMIZER_PLUGIN_DIR . 'classes/class-optimizer.php';
		require_once WOODMART_IMAGES_OPTIMIZER_PLUGIN_DIR . 'classes/class-picture-display.php';
		require_once WOODMART_IMAGES_OPTIMIZER_PLUGIN_DIR . 'classes/class-webp-replace.php';
		require_once WOODMART_IMAGES_OPTIMIZER_PLUGIN_DIR . 'classes/class-ui-components.php';
		require_once WOODMART_IMAGES_OPTIMIZER_PLUGIN_DIR . 'classes/class-main.php';
	}

	/**
	 * Check if WoodMart theme is active.
	 *
	 * @return bool
	 */
	private function is_woodmart_theme_active() {
		$theme = wp_get_theme();
		$template = strtolower( $theme->get_template() );
		$stylesheet = strtolower( $theme->get_stylesheet() );
		
		return false !== strpos( $template, 'woodmart' ) || false !== strpos( $stylesheet, 'woodmart' );
	}

	/**
	 * Show admin notice when WoodMart theme is not active.
	 */
	public function show_theme_dependency_notice() {
		$class = 'notice notice-error';
		$message = __( 'WoodMart Images Optimizer requires WoodMart theme to be active. Please activate WoodMart theme or deactivate this plugin.', 'woodmart-images-optimizer' );

		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
	}

	/**
	 * Add settings to WoodMart theme options.
	 */
	public function add_theme_settings() {
		if ( ! class_exists( 'XTS\Admin\Modules\Options' ) ) {
			return;
		}

		// Add the settings to WoodMart theme performance section.
		$this->add_woodmart_theme_settings();
	}

	/**
	 * Add settings to WoodMart theme performance section.
	 */
	private function add_woodmart_theme_settings() {
		if ( ! \class_exists( 'XTS\Admin\Modules\Options' ) ) {
			return;
		}

		$options_class = 'XTS\Admin\Modules\Options';

		// Add images optimizer section to performance.
		$options_class::add_section(
			array(
				'id'       => 'performance_images_optimizer',
				'name'     => \esc_html__( 'Images optimizer', 'woodmart-images-optimizer' ),
				'parent'   => 'general_performance',
				'priority' => 45,
				'icon'     => 'xts-i-performance',
			)
		);

		// Add quota information notice
		$this->add_quota_notice_field( $options_class );

		// Add optimization quality setting.
		$options_class::add_field(
			array(
				'id'          => 'woodmart_optimizer_quality',
				'name'        => \esc_html__( 'Optimization quality', 'woodmart-images-optimizer' ),
				'description' => \esc_html__( 'Set the quality level for image optimization. Higher values preserve more quality but result in larger file sizes.', 'woodmart-images-optimizer' ),
				'type'        => 'range',
				'section'     => 'performance_images_optimizer',
				'default'     => 80,
				'min'         => 10,
				'max'         => 100,
				'step'        => 1,
				'priority'    => 10,
			)
		);

		// Add WebP generation setting.
		$options_class::add_field(
			array(
				'id'          => 'woodmart_optimizer_generate_webp',
				'name'        => \esc_html__( 'Generate WebP images', 'woodmart-images-optimizer' ),
				'description' => \esc_html__( 'Generate WebP versions of optimized images alongside the original optimized images. WebP format provides better compression for modern browsers. When enabled, all images on your website will automatically be replaced with their WebP versions.', 'woodmart-images-optimizer' ),
				'type'        => 'switcher',
				'section'     => 'performance_images_optimizer',
				'default'     => false,
				'priority'    => 15,
			)
		);

		// Add auto-optimization setting.
		$options_class::add_field(
			array(
				'id'          => 'woodmart_optimizer_auto_optimize',
				'name'        => \esc_html__( 'Auto-optimize on upload', 'woodmart-images-optimizer' ),
				'description' => \esc_html__( 'Automatically optimize images when they are uploaded to the media library. Images will be optimized in the background shortly after upload.', 'woodmart-images-optimizer' ),
				'type'        => 'switcher',
				'section'     => 'performance_images_optimizer',
				'default'     => false,
				'priority'    => 20,
			)
		);

	}

	/**
	 * Add dynamic quota notice field
	 *
	 * @param string $options_class The options class name.
	 */
	private function add_quota_notice_field( $options_class ) {
		// Get quota information
		$api_client = new Api_Client();
		$quota_info = $api_client->get_quota_info();
		
		if ( ! $quota_info || ! isset( $quota_info['remaining_mb'], $quota_info['limit_mb'] ) ) {
			// If no quota info available, don't add the field
			return;
		}

		// Calculate values
		$remaining = round( $quota_info['remaining_mb'], 1 );
		$limit = round( $quota_info['limit_mb'], 1 );
		$used_percentage = round( ( ( $limit - $remaining ) / $limit ) * 100, 1 );

		// Build quota message
		$quota_message = sprintf(
			/* translators: %1$s: remaining MB, %2$s: total limit MB, %3$s: used percentage */
			\esc_html__( 'Quota: %1$s MB remaining of %2$s MB (%3$s%% used)', 'woodmart-images-optimizer' ),
			$remaining,
			$limit,
			$used_percentage
		);

		// Check if we should show upgrade button (total quota < 2GB = 2000MB)
		if ( $limit < 2000 ) {
			// Add upgrade button to the message
			$upgrade_url = 'https://xtemos.com/checkout/?add-to-cart=677003';
			$quota_message .= '<br><a href="' . \esc_url( $upgrade_url ) . '" target="_blank" class="xts-btn xts-color-primary" style="margin-top: 10px;">' . 
				\esc_html__( 'Upgrade to 5GB Plan', 'woodmart-images-optimizer' ) . 
				'</a>';
		}

		// Add the quota notice at the top
		$options_class::add_field(
			array(
				'id'       => 'images_optimizer_quota_notice',
				'type'     => 'notice',
				'style'    => 'info',
				'name'     => '',
				'content'  => \wp_kses(
					'<strong>' . \esc_html__( 'Optimization Quota:', 'woodmart-images-optimizer' ) . '</strong> ' . $quota_message,
					array(
						'strong' => array(),
						'br'     => array(),
						'a'      => array(
							'href'   => true,
							'target' => true,
							'class'  => true,
							'style'  => true,
						),
					)
				),
				'section'  => 'performance_images_optimizer',
				'priority' => 5,
			)
		);
	}

	/**
	 * Plugin activation.
	 */
	public function activate() {
		// Check if WoodMart theme is active during activation.
		if ( ! $this->is_woodmart_theme_active() ) {
			\wp_die(
				\esc_html__( 'WoodMart Images Optimizer requires WoodMart theme to be active. Please activate WoodMart theme first.', 'woodmart-images-optimizer' ),
				\esc_html__( 'Plugin Activation Error', 'woodmart-images-optimizer' ),
				array( 'back_link' => true )
			);
		}

		// Clear any existing optimization schedules.
		\wp_clear_scheduled_hook( 'xts_auto_optimize_image' );
	}

	/**
	 * Plugin deactivation.
	 */
	public function deactivate() {
		// Clear all scheduled optimization events.
		\wp_clear_scheduled_hook( 'xts_auto_optimize_image' );
	}
}

// Initialize the plugin.
Plugin::get_instance();
