<?php
/**
 * Floating Blocks Import class for post types with metaboxes.
 *
 * @package woodmart
 */

namespace XTS\Modules\Floating_Blocks;

use XTS\Admin\Modules\Import\Helpers;
use XTS\Admin\Modules\Import\XML;

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * Floating Blocks Import class for CPT with metaboxes.
 */
class Import {
	/**
	 * Helpers.
	 *
	 * @var Helpers
	 */
	private $helpers;

	/**
	 * Block types.
	 *
	 * @var array
	 */
	private $block_types;

	/**
	 * Module path for XML files.
	 *
	 * @var string
	 */
	private $module_path = '/inc/modules/floating-blocks/admin/predefined/';

	/**
	 * Constructor method.
	 */
	public function __construct() {
		$this->helpers     = Helpers::get_instance();
		$this->block_types = woodmart_get_config( 'fb-types' );
	}

	/**
	 * Force auto-increment IDs for floating blocks and attachments.
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
	 * Imports an XML file for a predefined content and processes the imported data.
	 *
	 * @param string $predefined_name The name of the predefined content to import.
	 * @param string $predefined_type The type of the predefined content to import.
	 * @param string $block_type      The block type key (e.g., 'floating-block', 'popup').
	 *
	 * @return int|false The ID of the newly created post on success, or false on failure.
	 */
	public function import_xml( $predefined_name, $predefined_type, $block_type = 'floating-block' ) {
		$external_builder = 'wpb' === woodmart_get_current_page_builder() ? 'wpb' : 'elementor';
		$builder          = 'native' === woodmart_get_opt( 'current_builder' ) ? 'gutenberg' : $external_builder;
		$version          = $block_type . '-' . $predefined_type . '-' . $predefined_name;

		$this->helpers->set_page_builder( $builder );

		$file_path = WOODMART_THEMEROOT . $this->module_path . $block_type . '/' . $predefined_type . '/' . $predefined_name . '/';

		if ( 'wpb' === $builder ) {
			$file_path .= 'content.xml';
		} elseif ( 'elementor' === $builder ) {
			$file_path .= 'content-elementor.xml';
		} else {
			$file_path .= 'content-gutenberg.xml';
		}

		$post_type = '';

		if ( isset( $this->block_types[ $block_type ]['post_type'] ) ) {
			$post_type = $this->block_types[ $block_type ]['post_type'];
		}

		if ( ! $post_type ) {
			return false;
		}

		add_filter( 'wp_import_post_data_processed', array( $this, 'remove_import_id' ), 10, 2 );

		new XML( $version, $block_type, $file_path );

		remove_filter( 'wp_import_post_data_processed', array( $this, 'remove_import_id' ), 10, 2 );

		$import_data = $this->helpers->get_imported_data( $version );

		if ( ! empty( $import_data[ $post_type ] ) ) {
			delete_option( 'wd_imported_data_' . $version );

			if ( woodmart_is_elementor_installed() ) {
				\Elementor\Plugin::$instance->files_manager->clear_cache();
			}

			return current( $import_data[ $post_type ] )['new'];
		}

		return false;
	}
}
