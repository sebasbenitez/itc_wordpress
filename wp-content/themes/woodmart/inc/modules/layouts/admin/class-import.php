<?php
/**
 * Import class file.
 *
 * @package woodmart
 */

namespace XTS\Modules\Layouts;

use Elementor\Plugin;
use XTS\Admin\Modules\Import\Helpers;
use XTS\Admin\Modules\Import\XML;

/**
 * Import class.
 */
class Import {
	/**
	 * Construct.
	 */
	public function __construct() {}

	/**
	 * Imports an XML file for a predefined content and processes the imported data.
	 *
	 * @param string $layout_type The type of the predefined content to import.
	 * @param string $predefined_name The name of the predefined content to import.
	 *
	 * @return int|false The ID of the newly created post on success, or false on failure.
	 */
	public static function import_xml( $layout_type, $predefined_name ) {
		if ( ! self::validate_layout_type( $layout_type ) ) {
			$error = new \WP_Error( 'invalid_layout_type', 'Invalid layout type provided.' );
			wp_die( esc_html( $error->get_error_message() ) );
		}

		$predefined_name = sanitize_file_name( (string) $predefined_name );

		if ( '' === $predefined_name ) {
			wp_die( esc_html( __( 'Invalid predefined layout name.', 'woodmart' ) ) );
		}

		$builder   = 'external' === woodmart_get_opt( 'current_builder', 'external' ) && ! str_contains( $layout_type, 'loop_item' ) ? woodmart_get_current_page_builder() : 'gutenberg';
		$version   = 'layout-' . $layout_type . '-' . $predefined_name;
		$file_path = WOODMART_THEMEROOT . '/inc/modules/layouts/admin/predefined/' . $layout_type . '/' . $predefined_name . '/';

		if ( 'wpb' === $builder ) {
			$file_path .= 'content.xml';
		} elseif ( 'elementor' === $builder ) {
			$file_path .= 'content-elementor.xml';
		} else {
			$file_path .= 'content-gutenberg.xml';
		}

		add_filter( 'wp_import_post_data_processed', array( __CLASS__, 'remove_import_id' ), 10 );
		add_filter( 'image_sideload_extensions', array( __CLASS__, 'allowed_image_sideload_extensions' ) );

		Helpers::get_instance()->set_page_builder( $builder );

		new XML( $version, 'woodmart_layout', $file_path );

		remove_filter( 'wp_import_post_data_processed', array( __CLASS__, 'remove_import_id' ) );
		remove_filter( 'image_sideload_extensions', array( __CLASS__, 'allowed_image_sideload_extensions' ) );

		$import_data = Helpers::get_instance()->get_imported_data( $version );

		if ( ! empty( $import_data['woodmart_layout'] ) ) {
			delete_option( 'wd_imported_data_' . $version );

			if ( 'elementor' === $builder && woodmart_is_elementor_installed() ) {
				Plugin::$instance->files_manager->clear_cache();
			}

			return current( $import_data['woodmart_layout'] )['new'];
		}

		return false;
	}

	/**
	 * Validate layout type.
	 *
	 * @param string $type Layout type.
	 *
	 * @return bool
	 */
	private static function validate_layout_type( $type ) {
		$valid_types = array(
			'blog_archive',
			'cart',
			'checkout_content',
			'checkout_form',
			'my_account_auth',
			'my_account_lost_password',
			'my_account_page',
			'portfolio_archive',
			'shop_archive',
			'single_portfolio',
			'single_post',
			'single_product',
			'thank_you_page',
			'product_loop_item',
		);

		return in_array( $type, $valid_types, true );
	}

	/**
	 * Allow image sideload extensions.
	 *
	 * @param array $allowed_extensions Allowed extensions.
	 * @return array
	 */
	public static function allowed_image_sideload_extensions( $allowed_extensions ) {
		$allowed_extensions[] = 'svg';

		return $allowed_extensions;
	}

	/**
	 * Force auto-increment IDs for floating blocks and attachments.
	 *
	 * @param array $postdata Post data.
	 *
	 * @return array
	 */
	public static function remove_import_id( $postdata ) {
		unset( $postdata['import_id'] );

		return $postdata;
	}
}
