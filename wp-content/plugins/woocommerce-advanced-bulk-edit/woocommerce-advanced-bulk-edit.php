<?php
/*
Plugin Name: WooCommerce Advanced Bulk Edit
Plugin URI: https://wpmelon.com
Description: Edit your products both individually or in bulk.
Author: WPMelon
Author URI: https://wpmelon.com
Version: 5.5.3.1
Text Domain: woocommerce-advbulkedit
Requires Plugins: woocommerce
WC requires at least: 5.0
WC tested up to: 9.6.2
*/

defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

// Global variable to store registered add-ons
global $wcabe_addons;
$wcabe_addons = [];

add_action( 'before_woocommerce_init', function() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );

define('WCABE_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('WCABE_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Helper function for external plugins to register WCABE add-ons
 *
 * @param string $id Unique identifier for the add-on
 * @param string $name Display name of the add-on
 * @param string $description Short description of the add-on
 * @param callable $content_callback Function that returns the add-on content
 * @param array $args Additional arguments (icon, version, etc.)
 * @return bool Success status
 */
function wcabe_register_addon($id, $name, $description, $content_callback, $args = []) {
	if (class_exists('W3ExAdvancedBulkEditMain')) {
		return W3ExAdvancedBulkEditMain::register_addon($id, $name, $description, $content_callback, $args);
	}
	return false;
}
define('WCABE_VERSION', '5.5.3.1');
define('WCABE_SITE_URL', 'https://wpmelon.com');
define('WCABE_SUPPORT_URL', 'https://wpmelon.com/r/support');
define('WCABE_SUPPORT_JS_NOTICE_MORE_INFO_URL', 'https://wpmelon.com/r/support-sj-notice-more-info');
define('WCABE_UPDATE_INFO_URL', 'https://wpmelon.com/r/wcabe-update-info');
define('WCABE_PURCHASE_URL', 'https://wpmelon.com/r/wcabe-purchase');
define('WCABE_UPDATE_URL', 'https://wpmelon-updates.com/be/api/v1/getupdate');
define( 'WCABE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define('WCABE_E_R', false);

require_once WCABE_PLUGIN_PATH . 'includes/notices/rate.php';
require_once WCABE_PLUGIN_PATH . 'includes/notices/getting-started.php';
require_once WCABE_PLUGIN_PATH . 'includes/helpers/general.php';
require_once WCABE_PLUGIN_PATH . 'includes/helpers/products.php';
require_once WCABE_PLUGIN_PATH . 'includes/helpers/integrations.php';
require_once WCABE_PLUGIN_PATH . 'includes/classes/product-attribute-updater.php';

class W3ExAdvancedBulkEditMain {

	private static $ins = null;
	private static $idCounter = 0;
	public static $table_name = "";
	public static $cache_key;
	public static $cache_expire;
	public static $cache_allowed;
	const PLUGIN_SLUG = 'advanced_bulk_edit';

	public static function init() {
		$settings = get_option('w3exabe_settings');
		if ((!defined('WP_ALLOW_MULTISITE') || !WP_ALLOW_MULTISITE)
			&& getenv('WCABE_UNIT_TEST') === false
		){
			require (ABSPATH . WPINC . '/pluggable.php');
			$roles = [];
			if( is_user_logged_in() ) {
				$user = wp_get_current_user();
				$roles = ( array ) $user->roles;
			}
			if(!in_array('administrator', $roles)) {
				define('WCABE_CANT_ACCESS_ADMIN_PLUGIN_SETTINGS', true);
				if (isset($settings['setting_enable_admin_only_visible']) && $settings['setting_enable_admin_only_visible'] == 1) {
					return;
				}
			}
		}



		if (!defined('CONCATENATE_SCRIPTS')) {
			define('CONCATENATE_SCRIPTS', false);
		}

		add_action('admin_menu', array(self::instance(), '_setup'));
		add_action('wp_ajax_wpmelon_adv_bulk_edit',  array(__CLASS__, 'ajax_request'));
		add_action('wp_ajax_wpmelon_wcabe',  array(__CLASS__, 'new_ajax_request'));
		add_action('plugins_loaded', array(self::instance(), '_load_translations'));
		add_action( 'admin_init', array(__CLASS__, 'wcabe_settings_form_submission') );
		//}

		self::load_extensions();

		if (file_exists( __DIR__.'/integrations/acf-custom-fields-customizations-for-viktor.php')) {
			require_once('integrations/acf-custom-fields-customizations-for-viktor.php');
			W3ExABulkEdit_Integ_ACFCustomFieldsCustomizationsForViktor::init();
		}
		
		self::$cache_key = 'wcabe_custom_update';
		self::$cache_allowed = true;
		self::$cache_expire = 3*DAY_IN_SECONDS;
		if (wp_get_environment_type() == 'local') {
			self::$cache_expire = 5;
		}
		
		$disable_update_check = isset($settings['disable_update_check']) && $settings['disable_update_check'] == 1;
		
		if (!$disable_update_check) {
			add_filter( 'plugins_api', array( __CLASS__, 'info' ), 20, 3 );
			add_filter( 'site_transient_update_plugins', array( __CLASS__, 'update' ) );
			add_action( 'upgrader_process_complete', array( __CLASS__, 'purge' ), 10, 2 );
			add_action( 'in_plugin_update_message-woocommerce-advanced-bulk-edit/woocommerce-advanced-bulk-edit.php', array(__CLASS__, 'wcabe_update_message'), 10, 2 );
		}

		add_filter( 'plugin_action_links_' . WCABE_PLUGIN_BASENAME, array(self::instance(), 'plugin_section_action_links') );
		add_filter( 'plugin_row_meta', array( self::instance(), 'plugin_section_row_meta' ), 10, 2 );
	}
	
	public function plugin_section_action_links($links) {
		$wcabe_links = array(
			'<a href="' . admin_url( 'edit.php?post_type=product&page=advanced_bulk_edit' ) . '">'.esc_html__('Bulk Edit Products', 'woocommerce-advbulkedit').'</a>'
		);

		return array_merge( $wcabe_links, $links );
	}

	public function plugin_section_row_meta($links, $file) {
		if ( WCABE_PLUGIN_BASENAME !== $file ) {
			return $links;
		}
		
		$row_meta = array(
			'docs'    => '<a href="https://wpmelon.com/r/support" aria-label="' . esc_attr__( 'Get WCABE support', 'woocommerce-advbulkedit' ) . '">' . esc_html__( 'Get Support', 'woocommerce-advbulkedit' ) . '</a>',
		);
		
		return array_merge(
			$links,
			$row_meta
		);
	}

	public static function request($is_test=false){
		if ($is_test) {
			delete_transient( self::$cache_key );
		}
		$remote = get_transient( self::$cache_key );
		
		if( false === $remote || ! self::$cache_allowed ) {
			
			$settings = get_option('w3exabe_settings');
			$wcabe_license_key = $settings['license_key'] ?? '';
			$wp_url = home_url();
			
			
			$remote = wp_remote_get(
				add_query_arg(
					array(
						'license' => base64_encode( "$wcabe_license_key|$wp_url" )
					),
					WCABE_UPDATE_URL
				),
				array(
					'timeout' => 10,
					'headers' => array(
						'Accept' => 'application/json'
					)
				)
			);
			
			if(
				is_wp_error( $remote )
				|| 200 !== wp_remote_retrieve_response_code( $remote )
				|| empty( wp_remote_retrieve_body( $remote ) )
			) {
				if ($is_test) {
					return $remote->get_error_message();
				} else {
					//wcabe_log('Error retrieving plugin update info: ' . $remote->get_error_message());
					return false;
				}
			}
			
			set_transient( self::$cache_key, $remote, self::$cache_expire );
			
		}
		
		$remote = json_decode( wp_remote_retrieve_body( $remote ) );
		
		return $remote;
		
	}
	
	public static function info( $res, $action, $args ) {
		
		if( 'plugin_information' !== $action ) {
			return $res;
		}
		
		if( self::PLUGIN_SLUG !== $args->slug ) {
			return $res;
		}
		
		$remote = self::request();
		
		if( ! $remote ) {
			return $res;
		}
		
		$res = new stdClass();
		
		$res->name = $remote->name;
		$res->slug = $remote->slug;
		$res->version = $remote->version;
		$res->tested = $remote->tested;
		$res->requires = $remote->requires;
		$res->author = $remote->author;
		$res->author_profile = $remote->author_profile;
		$res->download_link = $remote->download_url;
		$res->trunk = $remote->download_url;
		$res->requires_php = $remote->requires_php;
		$res->last_updated = $remote->last_updated;
		$res->rating = $remote->rating;
		$res->num_ratings = $remote->num_ratings;
		
		$res->sections = [];
		foreach ($remote->sections as $section_title => $section_content ) {
			$res->sections[$section_title] = $section_content;
		}
		
		if( ! empty( $remote->banners ) ) {
			$res->banners = array(
				'low' => $remote->banners->low,
				'high' => $remote->banners->high
			);
		}
		
		return $res;
		
	}
	
	public static function update( $transient ) {
		
		if ( empty($transient->checked ) ) {
			return $transient;
		}
		
		$remote = self::request();
		
		if(
			$remote
			&& isset($remote->version)
			&& version_compare( WCABE_VERSION, $remote->version, '<' )
			&& version_compare( $remote->requires, get_bloginfo( 'version' ), '<=' )
			&& version_compare( $remote->requires_php, PHP_VERSION, '<=' )
		) {
			$res = new stdClass();
			$res->slug = self::PLUGIN_SLUG;
			$res->plugin = plugin_basename( __FILE__ );
			$res->new_version = $remote->version;
			$res->tested = $remote->tested;
			$res->package = $remote->download_url;
			
			$transient->response[ $res->plugin ] = $res;
			
		}
		
		return $transient;
		
	}
	
	public static function purge( $upgrader, $options ){
		
		if (
			self::$cache_allowed
			&& 'update' === $options['action']
			&& 'plugin' === $options[ 'type' ]
		) {
			delete_transient( self::$cache_key );
		}
		
	}
	
	public static function wcabe_update_message( $plugin_info_array, $plugin_info_object ) {
		if( empty( $plugin_info_array[ 'package' ] ) ) {
			$renew_msg = ' To enable updates, please enter your purchase code on the <a href="'.
			             admin_url( 'edit.php?post_type=product&page=advanced_bulk_edit&section=wcabe_settings' ).
			             '">Settings</a> page. If you don\'t have one, please '.
			             '<a href="'. WCABE_PURCHASE_URL . '" target="_blank">purchase it here</a>.';
			echo $renew_msg;
		}
	}

	public function _load_translations()
	{
		 load_plugin_textdomain('woocommerce-advbulkedit', false,  dirname(plugin_basename(__FILE__)) .'/languages');
	}

	public static function instance()
	{
		is_null(self::$ins) && self::$ins = new self;
		return self::$ins;
	}

	public function _setup()
	{
		add_submenu_page(
			'edit.php?post_type=product',
			'WooCommerce Advanced Bulk Edit',
			'WooCommerce Advanced Bulk Edit',
			'manage_woocommerce',
			self::PLUGIN_SLUG,
			array(self::instance(), 'showpage')
		);
		add_action( 'admin_enqueue_scripts', array(self::instance(), 'admin_scripts'), 100 );


		$settings = get_option('w3exabe_settings');
		if (!isset($settings['setting_display_top_bar_link_bulkedit']) || $settings['setting_display_top_bar_link_bulkedit'] == "0") {
			add_action('admin_bar_menu', function($wp_admin_bar) {
				$args = array(
					'id' => 'btn-wcabe-admin-bar',
					'title' => esc_html__('Bulk Edit Products', 'woocommerce-advbulkedit'),
					'href' => admin_url('edit.php?post_type=product&page=advanced_bulk_edit'),
					'meta' => array(
						'class' => 'wp-admin-bar-btn-wcabe',
						'title' => 'Load WooCommerce Advanced Bulk Edit'
					)
				);
				$wp_admin_bar->add_node($args);

			}, 200);
		}

	}

	protected static function load_extensions()
	{
		$extensions_base_path = WCABE_PLUGIN_PATH.'extension/';
		$extension_paths = array_map('basename', glob($extensions_base_path.'*', GLOB_ONLYDIR));
		foreach ($extension_paths as $extension) {
			$extension_main_file = $extensions_base_path.$extension.'/'.$extension.'.php';
			if (file_exists($extension_main_file)) {
				require_once ($extension_main_file);
			}
		}
	}

	public static function ajax_request()
	{
		require_once(dirname(__FILE__).'/ajax_handler.php');
		W3ExABulkEditAjaxHandler::ajax();
		die();
	}

	public static function new_ajax_request()
	{
		require_once(dirname(__FILE__).'/new_ajax_handler.php');
		WpMelonWCABENewAjaxHandler::ajax();
		die();
	}

	function admin_scripts($hook)
	{
		$is_wcabe_admin_page = strpos($hook,'advanced_bulk_edit',0);
		if( !$is_wcabe_admin_page) {
			return;
		}
			
		$is_main_wcabe_page = ($hook === 'product_page_advanced_bulk_edit' && 
							(!isset($_GET['section']) && !isset($_GET['addon_id'])));
		$purl = WCABE_PLUGIN_URL;

		$ver = WCABE_VERSION;
		$settings = get_option('w3exabe_settings');
		
		wcabe_fix_conflicting_plugins();

		wp_deregister_script('jquery');
		wp_deregister_script('jquery-ui');

		wp_register_script('jquery', $purl.'lib/jquery-1.12.4.min.js', false, '1.12.4');
		wp_register_script('jquery-ui', $purl.'lib/jquery-ui-1.12.1.min.js', false, '1.12.1');
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui');
		wp_enqueue_script('jquery-ui-dialog');
		wp_enqueue_script('jquery-ui-tabs');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('jquery-ui-draggable');
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('jquery-ui-accordion');
		if(function_exists( 'wp_enqueue_media' )){
			wp_enqueue_media();
		}else{
			wp_enqueue_style('thickbox');
			wp_enqueue_script('media-upload');
			wp_enqueue_script('thickbox');
		}

		if ($is_main_wcabe_page) {
			wp_enqueue_style('slick.grid',$purl.'css/slick.grid.css',false, $ver, 'all' );
		}
		wp_enqueue_style('jqueryui-new',$purl.'css/jq-ui-themes/base/jquery-ui.min.css',false, $ver, 'all' );

		wp_enqueue_style('w3exabe-main',$purl.'css/main.css',false, $ver, 'all' );
		wp_enqueue_style('chosen',$purl.'chosen/chosen.min.css',false, $ver, 'all' );
		if ($is_main_wcabe_page) {
			wp_enqueue_style('slick.columnpicker',$purl.'controls/slick.columnpicker.css',false, $ver, 'all' );
		}

		wp_enqueue_style('w3exabe-main-extend',$purl.'css/main-extend.css',false, $ver, 'all' );
		$row_height = $settings['rowheight'] ?? '1';
		$row_style_file = 'css/row-style-default.css';
		if ($row_height === '2') {
			$row_style_file = 'css/row-style-medium.css';
		} elseif ($row_height === '3') {
			$row_style_file = 'css/row-style-large.css';
		}
		wp_enqueue_style('w3exabe-dyn-css',$purl.$row_style_file,false, $ver, 'all' );


		if (!isset($settings['setting_disable_hints']) || $settings['setting_disable_hints'] != 1) {
			wp_enqueue_style('tippy-light',$purl.'css/tippy/light.css',false, $ver, 'all' );
			wp_enqueue_style('tippy-light-border',$purl.'css/tippy/light-border.css',false, $ver, 'all' );
			wp_enqueue_style('tippy-google',$purl.'css/tippy/google.css',false, $ver, 'all' );
			wp_enqueue_style('tippy-translucent',$purl.'css/tippy/translucent.css',false, $ver, 'all' );
		}


		wp_enqueue_script('jquery.event.drag-2.2',$purl.'lib/jquery.event.drag-2.2.js', array(), $ver, true );

		if ($is_main_wcabe_page) {
			wp_enqueue_script('slick.core',$purl.'js/slick.core.js', array(), $ver, true );
			wp_enqueue_script('slick.checkboxselectcolumn',$purl.'plugins/slick.checkboxselectcolumn.js', array(), $ver, true );
			wp_enqueue_script('slick.autotooltips',$purl.'plugins/slick.autotooltips.js', array(), $ver, true );
			wp_enqueue_script('slick.cellrangedecorator',$purl.'plugins/slick.cellrangedecorator.js', array(), $ver, true );
			wp_enqueue_script('slick.cellrangeselector',$purl.'plugins/slick.cellrangeselector.js', array(), $ver, true );
			wp_enqueue_script('slick.cellcopymanager',$purl.'plugins/slick.cellcopymanager.js', array(), $ver, true );
			wp_enqueue_script('slick.cellselectionmodel',$purl.'plugins/slick.cellselectionmodel.js', array(), $ver, true );
			wp_enqueue_script('slick.rowselectionmodel',$purl.'plugins/slick.rowselectionmodel.js', array(), $ver, true );
			wp_enqueue_script('slick.columnpicker',$purl.'controls/slick.columnpicker.js', array(), $ver, true );
			wp_enqueue_script('slick.formatters',$purl.'js/slick.formatters.js', array(), $ver, true );
			wp_enqueue_script('slick.editors',$purl.'js/slick.editors.js', array(), $ver, true );
			wp_enqueue_script('slick.grid',$purl.'js/slick.grid.js', array(), $ver, true );
		}
		wp_enqueue_script('chosen.jquery',$purl.'chosen/chosen.jquery.min.js', array(), $ver, true );
		if ($is_main_wcabe_page) {
			wp_enqueue_script('w3exabe-adminjs',$purl.'js/admin.js', array(), $ver, true );
			wp_enqueue_script('w3exabe-adminjsext',$purl.'js/admin-ext.js', array(), $ver, true );
			wp_enqueue_script('w3exabe-adminhelpersjs',$purl.'js/admin-helpers.js', array(), $ver, true );
			wp_enqueue_script('w3exabe-adminui',$purl.'js/admin-ui.js', array(), $ver, true );
		}

		if (!isset($settings['setting_disable_hints']) || $settings['setting_disable_hints'] != 1) {
			wp_enqueue_script('popper',$purl.'js/tippy/popper.min.js', array(), $ver, true );
			wp_enqueue_script('tippy',$purl.'js/tippy/index.all.min.js', array(), $ver, true );
		}

		wp_localize_script('w3exabe-adminjs', 'W3ExABE', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'w3ex-advbedit-nonce' ),
			'is_main_wcabe_page' => $is_main_wcabe_page,
			)
		);

	}

	/**
	 * Register a WCABE add-on
	 *
	 * @param string $id Unique identifier for the add-on
	 * @param string $name Display name of the add-on
	 * @param string $description Short description of the add-on
	 * @param callable $content_callback Function that returns the add-on content
	 * @param array $args Additional arguments (icon, version, etc.)
	 * @return bool Success status
	 */
	public static function register_addon($id, $name, $description, $content_callback, $args = []) {
		global $wcabe_addons;
		
		if (empty($id) || !is_callable($content_callback)) {
			return false;
		}
		
		$wcabe_addons[$id] = [
			'name' => sanitize_text_field($name),
			'description' => sanitize_text_field($description),
			'content_callback' => $content_callback,
			'icon' => $args['icon'] ?? '',
			'version' => $args['version'] ?? '1.0.0',
			'author' => $args['author'] ?? '',
			'url' => $args['url'] ?? ''
		];
		
		return true;
	}
	
	/**
	 * Get registered add-ons
	 *
	 * @return array Array of registered add-ons
	 */
	public static function get_addons() {
		global $wcabe_addons;
		return $wcabe_addons;
	}
	
	/**
	 * Get a specific add-on by ID
	 *
	 * @param string $id Add-on ID
	 * @return array|null Add-on data or null if not found
	 */
	public static function get_addon($id) {
		global $wcabe_addons;
		return isset($wcabe_addons[$id]) ? $wcabe_addons[$id] : null;
	}
	
	/**
	 * Display the appropriate page based on the section parameter
	 */
	public function showpage()
	{
		do_action('wcabe_register_addons');
		
		if (isset($_GET['section']) && $_GET['section'] == 'site_wide_ops') {
			if (wcabe_load_integration(WCABE_179_INTEG_SITE_WIDE_OPS)) {
				W3ExABulkEdit_Integ_SiteWideOps::site_wide_ops_admin_page();
			}
		}
		else if (isset($_GET['section']) && $_GET['section'] == 'wcabe_settings') {
			require_once(dirname(__FILE__).'/page-settings.php');
		}
		else if (isset($_GET['section']) && $_GET['section'] == 'addon') {
			require_once(dirname(__FILE__).'/page-addon.php');
		}
		else {
			require_once(dirname(__FILE__).'/bulkedit.php');
		}
	}
	
	public static function wcabe_settings_form_submission()
	{
		if ( isset( $_POST['wcabe-submit-settings'] ) ) {
			$wcabe_license_key = sanitize_text_field( $_POST['wcabe_license_key'] );
			
			$settings = get_option('w3exabe_settings');
			$settings['license_key'] = $wcabe_license_key;
			update_option('w3exabe_settings',$settings);
			delete_transient( self::$cache_key );

			$redirect_url = admin_url( 'edit.php?post_type=product&page=advanced_bulk_edit&section=wcabe_settings' );
			wp_redirect( $redirect_url );
			exit;
		}
		elseif ( isset( $_POST['wcabe-submit-settings-connection-test'] ) ) {
			
			wcabe_check_plugin_update_connection();
			
			$redirect_url = admin_url( 'edit.php?post_type=product&page=advanced_bulk_edit&section=wcabe_settings' );
			wp_redirect( $redirect_url );
			exit;
		} elseif ( isset( $_POST['wcabe-submit-settings-connection-clear-log'] )) {
			wcabe_connection_log_clear();

			$redirect_url = admin_url( 'edit.php?post_type=product&page=advanced_bulk_edit&section=wcabe_settings' );
			wp_redirect( $redirect_url );
			exit;
		}

	}
}

W3ExAdvancedBulkEditMain::init();
