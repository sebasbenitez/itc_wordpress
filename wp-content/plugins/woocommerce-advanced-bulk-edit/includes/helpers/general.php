<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (!function_exists('wcabe_starts_with')) {
	/**
	 * Function to check if string starting
	 * with given substring
	 *
	 * @param $string string The string to search within
	 * @param $startString string The string to search for
	 *
	 * @return bool
	 */
	function wcabe_starts_with ($string, $startString)
	{
		$len = strlen($startString);
		return (substr($string, 0, $len) === $startString);
	}
}

if (!function_exists('wcabe_ends_with')) {
	/**
	 * Function to check the string if it ends
	 * with given substring or not
	 *
	 * @param $string string The string to search within
	 * @param $endString string The string to search for
	 *
	 * @return bool
	 */
	function wcabe_ends_with($string, $endString)
	{
		$len = strlen($endString);
		if ($len == 0) {
			return true;
		}
		return (substr($string, -$len) === $endString);
	}
}

if (!function_exists('wcabe_verify_ajax_nonce')) {
	/**
	 * Checks the $_POST request for existing valid nonce
	 *
	 * @return bool
	 */
	function wcabe_verify_ajax_nonce()
	{
		return wp_verify_nonce( $_POST['nonce'], 'w3ex-advbedit-nonce' );
	}
}

if (!function_exists('wcabe_verify_ajax_nonce_or_die')) {
	/**
	 * Checks the $_POST request for existing valid nonce
	 *
	 * @param string $die_message Message that will be send back to the ajax request
	 *
	 * @return void
	 */
	function wcabe_verify_ajax_nonce_or_die($die_message='no-nonce')
	{
		if (!wcabe_verify_ajax_nonce()) {
			echo json_encode( [
				'error'  => $die_message,
				'products' => []
			] );
			error_log('dying');
			die();
		}
	}
}

if (!function_exists( 'wcabe_get_current_user_roles' )) {
	/**
	 * Get an array of the current user assigned roles
	 *
	 * @return array
	 */
	function wcabe_get_current_user_roles() {
		
		if( is_user_logged_in() ) { // check if there is a logged in user
			
			$user = wp_get_current_user(); // getting & setting the current user
			$roles = ( array ) $user->roles; // obtaining the role
			
			return $roles; // return the role for the current user
			
		} else {
			
			return array(); // if there is no logged in user return empty array
			
		}
	}
}

if (!function_exists( 'wcabe_get_users_who_can_create_products' )) {
	/**
	 * Get an array of all the users that have created products before and merge them with the roles that can create
     * products. This way we have all the product creators list.
	 *
	 * @return array
	 */
	function wcabe_get_users_who_can_create_products() {
		global $wpdb;

		$product_authors = $wpdb->get_results("
			SELECT DISTINCT u.ID, u.display_name
			FROM $wpdb->posts p
			INNER JOIN $wpdb->users u ON p.post_author = u.ID
			WHERE p.post_type = 'product'
		");// 
		$admin_users = get_users(["role__in" => ["administrator", "shop_manager", 'seller', 'vendor']]);

		$combined_users = [];

		foreach ($product_authors as $author) {
			$combined_users[$author->ID] = $author;
		}

		foreach ($admin_users as $admin) {
			if (!isset($combined_users[$admin->ID])) {
				$combined_users[$admin->ID] = (object) [
					"ID" => $admin->ID,
					"display_name" => $admin->display_name
				];
			}
		}

		return array_values($combined_users);

	}
}

if (!function_exists('wcabe_is_current_user_admin')) {
	/**
	 * Check the current user if he has administrator role.
	 *
	 * @return bool
	 */
	function wcabe_is_current_user_admin(): bool {
		$current_user = wp_get_current_user();
		return in_array( 'administrator', (array) $current_user->roles );
	}
}

if (!function_exists('wcabe_log')) {
	/**
	 * Logs a message in the  plugin's log, located in plugin's folder.
	 *
	 * @param $msg
	 *
	 * @return void
	 */
	function wcabe_log($msg): void {
		//$condPluginPageLoaded = isset($_GET['page']) && $_GET['page'] == 'advanced_bulk_edit';
		//$condPluginAjaxRequest = isset($_POST['action']) && $_POST['action'] == 'wpmelon_adv_bulk_edit';
		$log_path = WCABE_PLUGIN_PATH . 'wcabe.log';
		$log = date("Y-m-d H:i:s") . " > " . $msg . PHP_EOL;
		file_put_contents($log_path, $log, FILE_APPEND);
	}
}

if (!function_exists('wcabe_log_read')) {
	function wcabe_log_read() {
		$log_path = WCABE_PLUGIN_PATH . 'wcabe.log';
		return file_get_contents($log_path);
	}
}

if (!function_exists('wcabe_connection_log_read')) {
	function wcabe_connection_log_read(): string {
		$log_path = WCABE_PLUGIN_PATH . 'wcabe.log';
		$filteredLog = '';
		$filterStr = '[conn-log]';
		$logFileExists = file_exists($log_path);

		if ($logFileExists && $handle = fopen($log_path, "r")) {
			while (($line = fgets($handle)) !== false) {
				if (strpos($line, $filterStr) !== false) {
					// Append the line to the result string if it contains the substring
					$filteredLog .= $line;
				}
			}
			fclose($handle);
		} else {
			return "No log available.";
		}

		return $filteredLog;
	}
}

if (!function_exists('wcabe_connection_log_clear')) {
	function wcabe_connection_log_clear() {
		$log_file  = WCABE_PLUGIN_PATH . 'wcabe.log';
		$temp_file = WCABE_PLUGIN_PATH . 'tempwcabe.log';
		
		if ( $handle = fopen( $log_file, "r" ) ) {
			if ( $tempHandle = fopen( $temp_file, "w" ) ) {
				while ( ( $line = fgets( $handle ) ) !== false ) {
					if ( strpos( $line, '[conn-log]' ) === false ) {
						fwrite( $tempHandle, $line );
					}
				}
				fclose( $tempHandle );
			} else {
				wcabe_log( "Unable to open the temporary file for writing." );
			}
			fclose( $handle );
			
			// Replace the original log file with the filtered content from the temp file
			rename( $temp_file, $log_file );
		} else {
		}
	}
}

if (!function_exists('version_checks_for_plugin_updates')) {
	function version_checks_for_plugin_updates($remote): array {
		$errors = [];
		if (!isset($remote->version)) {
			$errors[] = "Warning: Update info version is not set!";
		}
		if (version_compare( WCABE_VERSION, $remote->version, '>=' )) {
			$errors[] = "Plugin version: ".WCABE_VERSION.", update version: ".$remote->version." (no need to update)!";
		}
		if (version_compare( $remote->requires, get_bloginfo( 'version' ), '>' )) {
			$errors[] = "Warning: Update requires newer WP version! Current version: ".get_bloginfo( 'version' ).", update version: ".$remote->requires." (need to update WP)!";
		}
		if (version_compare( $remote->requires_php, PHP_VERSION, '>' )) {
			$errors[] = "Warning: Update requires newer PHP version! Current version: ".PHP_VERSION.", update version: ".$remote->requires_php." (need to update PHP)!";
		}
		
		return $errors;
	}
}



if (!function_exists('wcabe_check_plugin_update_connection')) {
	function wcabe_check_plugin_update_connection(): void {
		$response = W3ExAdvancedBulkEditMain::request(true);
		if (is_string($response)) {
			wcabe_log("[conn-log] ".$response);
		} else if ($response instanceof StdClass) {
			wcabe_log("[conn-log] *** Check START *** ");
			wcabe_log("[conn-log] Connection successful!");
			wcabe_log("[conn-log] Update version: ".$response->version);
			wcabe_log("[conn-log] Download url: ". (!empty($response->download_url) ? 'Valid' : 'Invalid'));
			wcabe_log("[conn-log] Download status: ". (empty($response->download_msg) ? 'OK' : $response->download_msg)); ;
			$version_checks = version_checks_for_plugin_updates($response, true);
			if (!empty($version_checks) && is_array($version_checks)) {
				foreach ($version_checks as $version_check_error) {
					wcabe_log("[conn-log] ".$version_check_error);
				}
			}
			//wcabe_log("[conn-log] ".json_encode($response));
			wcabe_log("[conn-log] *** Check END *** ");

		}
	}
}

if (!function_exists('wcabe_fix_conflicting_plugins')) {
	/**
	 * De-registers the JS scripts which are loading improperly on WCABE admin page and are conflicting,
	 * preventing WCABE scripts from loading.
	 *
	 * @return void
	 */
	function wcabe_fix_conflicting_plugins(): void {
		$conflicting_scripts_list = [
			// Fixes for conflicting plugins
			'jquery-magnific-popup',
			'porto-builder-admin',
			'tidio-chat-admin',
			'bm-admin',
			'wbm-admin',
			'lpc_admin_notices',
			'paypal-for-woocommerce-multi-account-management',
			'soisy-pagamento-rateale',
			'my-great-script',
			'cfpa-admin.js',
			'con-apply-setting.js',
			'fma_order_on_whatsapp_admin',
			'fma_order_on_whatsapp_my_script_for_whatsapp_contact',
			'itsec-core-admin-notices',
			'validate-config-form',
			'service-selector',
			'slicewp-script',
			'cardcom-admin-script',
			'flizpay',
			'alpus-plugin-framework-admin',
			'awca_js',
			'yoast-seo-premium-metabox',
		];
		
		if (defined('YITH_YWPI_ASSETS_URL')) {
			$conflicting_scripts_list[] = 'ywpi_' . YITH_YWPI_ASSETS_URL . '-js';
		}
		
		foreach ($conflicting_scripts_list as $conflicting_script) {
			if (wp_script_is($conflicting_script, 'enqueued'))
			{
				wp_deregister_script( $conflicting_script );
			}
		}
		
	}
}
