<?php
/**
 * Custom labels Import class for post types with metaboxes.
 *
 * @package woodmart
 */

namespace XTS\Modules\Custom_Labels;

use XTS\Admin\Modules\Import\Helpers;
use XTS\Admin\Modules\Import\XML;

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * Custom labels Import class for CPT with metaboxes.
 */
class Import {
	/**
	 * Helpers.
	 *
	 * @var Helpers
	 */
	private $helpers;

	/**
	 * Module path for XML files.
	 *
	 * @var string
	 */
	private $module_path = WOODMART_CUSTOM_LABELS_DIR . '/admin/predefined/';

	/**
	 * Constructor method.
	 */
	public function __construct() {
		if ( ! woodmart_get_opt( 'custom_labels' ) ) {
			return;
		}

		$this->helpers = Helpers::get_instance();
	}

	/**
	 * Force auto-increment IDs for custom labels.
	 *
	 * @param array $postdata Post data.
	 *
	 * @return array
	 */
	public function remove_import_id( $postdata ) {
		unset( $postdata['import_id'] );

		return $postdata;
	}

	/**
	 * Bypass post exists check to force import of custom labels.
	 *
	 * @param int   $post_exists Post ID, or 0 if post did not exist.
	 * @param array $post        The post array to be inserted.
	 *
	 * @return int
	 */
	public function bypass_post_exists( $post_exists, $post ) {
		if ( 'wd_custom_label' === $post['post_type'] ) {
			return 0;
		}

		return $post_exists;
	}

	/**
	 * Imports an XML file for a predefined content and processes the imported data.
	 *
	 * @param string $predefined_name The name of the predefined content to import.
	 *
	 * @return int|false The ID of the newly created post on success, or false on failure.
	 */
	public function import_xml( $predefined_name ) {
		$version = 'custom_label-' . $predefined_name;

		$file_path = $this->module_path . $predefined_name . '/';

		$file_path .= 'content.xml';

		$this->helpers->set_page_builder( 'gutenberg' );

		add_filter( 'wp_import_existing_post', array( $this, 'bypass_post_exists' ), 10, 2 );
		add_filter( 'wp_import_post_data_processed', array( $this, 'remove_import_id' ), 10, 2 );

		new XML( $version, 'wd_custom_label', $file_path );

		remove_filter( 'wp_import_post_data_processed', array( $this, 'remove_import_id' ), 10, 2 );
		remove_filter( 'wp_import_existing_post', array( $this, 'bypass_post_exists' ), 10, 2 );

		$import_data = $this->helpers->get_imported_data( $version );

		if ( ! empty( $import_data['wd_custom_label'] ) ) {
			delete_option( 'wd_imported_data_' . $version );

			return current( $import_data['wd_custom_label'] )['new'];
		}

		return false;
	}
}
