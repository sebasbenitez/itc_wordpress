<?php
/**
 * Dynamic tags main class.
 *
 * @package woodmart
 */

namespace XTS\Modules\Dynamic_Tags;

/**
 * Main class.
 */
class Main {
	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( ! woodmart_is_gutenberg_blocks_enabled() ) {
			return;
		}

		$this->include_files();
	}

	/**
	 * Include classes files.
	 */
	public function include_files() {
		$dir = WOODMART_THEMEROOT . '/inc/modules/dynamic-tags/';

		require_once $dir . 'class-config.php';
		require_once $dir . 'class-content.php';
		require_once $dir . 'class-gutenberg.php';
	}
}

new Main();
