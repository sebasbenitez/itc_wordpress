<?php
/**
 * Floating Blocks Main Class.
 *
 * @package woodmart
 */

namespace XTS\Modules\Floating_Blocks;

use XTS\Singleton;

/**
 * Floating Blocks Main Class.
 */
class Main extends Singleton {
	/**
	 * Constructor.
	 */
	public function init() {
		add_action( 'init', array( $this, 'include_files' ), 10 );
	}

	/**
	 * Include required admin files.
	 */
	public function include_files() {
		$dir = WOODMART_THEMEROOT . '/inc/modules/floating-blocks/';

		require_once $dir . '/class-manager.php';

		if ( woodmart_is_elementor_installed() ) {
			require_once $dir . '/integrations/elementor/class-fb-doc.php';
			require_once $dir . '/integrations/elementor/class-popup-doc.php';
		}

		// WPBakery.
		if ( defined( 'WPB_VC_VERSION' ) && ! isset( $_GET['vcv-gutenberg-editor'] ) ) { // phpcs:ignore.
			require_once $dir . '/integrations/wpb/class-fb.php';
			require_once $dir . '/integrations/wpb/class-popup.php';
		}

		if ( woodmart_is_gutenberg_blocks_enabled() ) {
			require_once $dir . '/integrations/gutenberg/class-popup.php';
			require_once $dir . '/integrations/gutenberg/class-fb.php';
		}

		require_once $dir . '/class-import.php';
		require_once $dir . '/class-admin.php';
		require_once $dir . '/class-frontend.php';
	}
}

Main::get_instance();
