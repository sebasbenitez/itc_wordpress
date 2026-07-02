<?php
/**
 * Custom labels class.
 *
 * @package woodmart
 */

namespace XTS\Modules\Custom_Labels;

use XTS\Admin\Modules\Dashboard\Status_Button;
use XTS\Admin\Modules\Options;
use XTS\Singleton;

/**
 * Custom labels class.
 */
class Main extends Singleton {
	/**
	 * Init.
	 */
	public function init() {
		if ( ! woodmart_woocommerce_installed() ) {
			return;
		}

		define( 'WOODMART_CUSTOM_LABELS_DIR', WOODMART_THEMEROOT . '/inc/integrations/woocommerce/modules/custom-labels/' );

		add_action( 'init', array( $this, 'include_files' ), 8 );
		new Status_Button( 'wd_custom_label', 2 );
	}

	/**
	 * Include files.
	 *
	 * @return void
	 */
	public function include_files() {
		require_once WOODMART_CUSTOM_LABELS_DIR . '/class-admin.php';
		require_once WOODMART_CUSTOM_LABELS_DIR . '/class-import.php';
		require_once WOODMART_CUSTOM_LABELS_DIR . '/class-manager.php';
		require_once WOODMART_CUSTOM_LABELS_DIR . '/class-frontend.php';
	}
}

Main::get_instance();
