<?php
/**
 * WPBakery custom templates library
 *
 * @package woodmart
 */

namespace XTS; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedNamespaceFound

use WPBMap;
use WOODMART_CORE\Importer\Import;

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * WPBakery custom templates library.
 */
class Vctemplates {
	/**
	 * Folder with templates
	 *
	 * @var string $folder Folder path for templates.
	 */
	public $folder = '';

	/**
	 * Importer instance
	 *
	 * @var Import|false $importer Importer instance.
	 */
	private $importer = null;

	/**
	 * Template data
	 *
	 * @var array $template_data Template data.
	 */
	private $template_data = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->config();
		$this->hooks();
	}

	/**
	 * Add custom templates category to WPBakery templates library.
	 *
	 * @param array $data Existing templates categories.
	 *
	 * @return array Modified templates categories.
	 */
	public function library( $data ) {
		if ( woodmart_get_opt( 'white_label' ) ) {
			$title = esc_html__( 'Templates library', 'woodmart' );
		} else {
			$title = esc_html__( 'WoodMart templates library', 'woodmart' );
		}

		$data[] = array(
			'category'             => 'woodmart_templates',
			'category_name'        => $title,
			'category_weight'      => 5,
			'category_description' => esc_html__( 'WPBakery predefined template parts and layouts from XTemos Studio. Designed for WoodMart WordPress template.', 'woodmart' ),
			'templates'            => array(),
		);

		return $data;
	}

	/**
	 * Render template content for WPBakery backend editor.
	 *
	 * @param string $template_id Template ID.
	 * @param string $template_type Template type.
	 *
	 * @return string Rendered template content or original template ID if type is not 'woodmart_templates'.
	 */
	public function render_template_code( $template_id, $template_type ) {
		if ( 'woodmart_templates' === $template_type ) {
			$this->load_template( $template_id );
			echo $this->get_template_content(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $template_id;
		}
	}

	/**
	 * Render template content for WPBakery frontend editor.
	 *
	 * @param string $template_id Template ID.
	 *
	 * @return string Rendered template content or original template ID if type is not 'woodmart_templates'.
	 */
	public function render_template_html_code( $template_id ) {
		$this->load_template( $template_id );
		$content = $this->get_template_content();

		if ( ! $content ) {
			return;
		}

		WPBMap::addAllMappedShortcodes();

		vc_frontend_editor()->setTemplateContent( $content );
		vc_frontend_editor()->enqueueRequired();
		vc_include_template(
			'editors/frontend_template.tpl.php',
			array(
				'editor' => vc_frontend_editor(),
			)
		);

		die(); // no needs to do anything more. optimization.
	}

	/**
	 * Get template content with replaced variables.
	 *
	 * @param string $shortcodes Template shortcodes content.
	 * @param array  $config Template configuration array.
	 *
	 * @return string Template content with replaced variables.
	 */
	public function get_content( $shortcodes, $config ) {
		$replace_vars = array();

		if ( is_array( $config ) ) {
			if ( isset( $config['assets'] ) && $config['assets'] ) {
				foreach ( $config['assets'] as $asset ) {
					$id = $this->add_media( $asset['src'] );

					switch ( $asset['type'] ) {
						case 'external-image':
							if ( $id ) {
								$replace_vars[ '{{' . $asset['id'] . '}}' ] = $id;
							}

							break;
						case 'external-image-url':
							if ( $id ) {
								$image = wp_get_attachment_image_src( $id, 'full' );
								if ( isset( $image[0] ) ) {
									$replace_vars[ '{{' . $asset['id'] . '}}' ] = $image[0];
								}
							}

							break;
					}
				}
			}
		}

		if ( ! empty( $replace_vars ) ) {
			$shortcodes = $this->replace_vars( $shortcodes, $replace_vars );
		}

		return $shortcodes;
	}

	/**
	 * Get template content from the loaded template data and replace variables with actual values from the configuration.
	 *
	 * @return string Template content with replaced variables or null if shortcodes are not found.
	 */
	public function get_template_content() {
		$shortcodes  = $this->get_shortcodes();
		$config_json = json_decode( $this->get_config(), true );

		if ( ! $shortcodes ) {
			return;
		}

		return $this->get_content( $shortcodes, $config_json );
	}

	/**
	 * Render template category in WPBakery templates library.
	 *
	 * @param array $category Template category data.
	 *
	 * @return array Modified template category data with rendered output.
	 */
	public function render_template( $category ) {
		$category['output'] = '';

		$category['output'] .= '<div class="xts-box xts-wpb-templates xts-theme-style">';
		$category['output'] .= '<div class="xts-box-header xts-wpb-templates-heading">';
		$category['output'] .= '<div class="xts-row">';
		$category['output'] .= '<div class="xts-col"><div>';
		if ( isset( $category['category_name'] ) ) {
			$category['output'] .= '<h3>' . esc_html( $category['category_name'] ) . '</h3>';
		}
		if ( isset( $category['category_description'] ) ) {
			$category['output'] .= '<p class="vc_description">' . esc_html( $category['category_description'] ) . '</p>';
		}
		$category['output'] .= '</div></div>';

		$category['output'] .= '<div class="xts-col-auto"><div class="xts-import-search xts-search xts-i-search"><input type="text" class="woodmart-templates-search" placeholder="Start typing to search..." /></div></div>';

		$category['output'] .= '</div>';
		$category['output'] .= '</div>';
		$category['output'] .= '
			<div class="xts-box-content xts-wpb-templates-content woodmart-templates-list xts-loading" data-vc-action="collapseAll"></div>';
		$category['output'] .= '</div>';
		$category['output'] .= '';

		return $category;
	}

	/**
	 * Initialize class properties and load necessary importers for media handling.
	 */
	private function config() {
		$this->folder = WOODMART_CONFIGS . '/templates-library/';
	}

	/**
	 * Register hooks for WPBakery templates library integration.
	 */
	private function hooks() {
		add_filter( 'vc_get_all_templates', array( $this, 'library' ), 1, 1 );
		add_filter( 'vc_templates_render_backend_template', array( $this, 'render_template_code' ), 10, 2 );
		add_filter( 'vc_templates_render_frontend_template', array( $this, 'render_template_html_code' ) );
		add_filter( 'vc_templates_render_category', array( $this, 'render_template' ), 1, 1 );
	}

	/**
	 * Get template shortcodes content from the loaded template data.
	 *
	 * @return string Template shortcodes content or null if not found.
	 */
	private function get_shortcodes() {
		return $this->template_data['element']['content'];
	}

	/**
	 * Load template data from remote server for a given template ID and store it in the class property.
	 *
	 * @param string $id Template ID.
	 */
	private function load_template( $id ) {
		$response = wp_remote_get( WOODMART_DEMO_URL . '?woodmart_action=woodmart_get_template&id=' . $id );
		$body     = '';

		if ( is_array( $response ) ) {
			if ( isset( $response['body'] ) ) {
				$body = $response['body'];

				$this->template_data = json_decode( $body, true );
			}

			if ( isset( $response['errors'] ) || ! isset( $response['body'] ) ) {
				die( wp_json_encode( $response ) );
			}
		}

		return $body;
	}

	/**
	 * Get template configuration from the loaded template data.
	 *
	 * @return string Template configuration in JSON format or null if not found.
	 */
	private function get_config() {
		return $this->template_data['element']['config'];
	}

	/**
	 * Replace variables in the template shortcodes content with actual values from the configuration.
	 *
	 * @param string $code Template shortcodes content.
	 * @param array  $vars Array of variables to replace in the template content, where keys are placeholders and values are actual values.
	 *
	 * @return string Template content with replaced variables.
	 */
	private function replace_vars( $code, $vars ) {
		$code = str_replace( array_keys( $vars ), $vars, $code );

		return $code;
	}

	/**
	 * Add media to the WordPress media library and return its ID. If the media already exists, return the existing media ID.
	 *
	 * @param string $src Media source URL.
	 *
	 * @return int|false Media ID on success, false on failure.
	 */
	private function add_media( $src ) {
		$this->load_importers();

		$postdata = array();
		$id       = $this->media_exists( $src );
		$media_id = false;

		if ( $id ) {
			$media_id = $id;
		} elseif ( is_object( $this->importer ) ) {
			$media_id = $this->importer->process_attachment( $postdata, $src );
			$this->save_media_id( $src, $media_id );
		}

		if ( is_wp_error( $media_id ) || ! $media_id ) {
			return false;
		}

		return $media_id;
	}

	/**
	 * Check if media with the given source URL already exists in the WordPress media library and return its ID if it does.
	 *
	 * @param string $src Media source URL.
	 *
	 * @return int|false Media ID if media exists, false if it does not exist.
	 */
	private function media_exists( $src ) {
		$media = get_option( 'woodmart-vc-imported-media' );

		if ( ! $media || ! is_array( $media ) ) {
			return false;
		}

		$id = array_search( $src, $media, true );

		if ( $id ) {
			$image = wp_get_attachment_image_src( $id, 'full' );
			if ( isset( $image[0] ) ) {
				return $id;
			}
		}

		return false;
	}

	/**
	 * Save media ID and source URL in the WordPress options table for future reference to avoid duplicate media imports.
	 *
	 * @param string $src Media source URL.
	 * @param int    $id  Media ID.
	 *
	 * @return bool True if the media ID was saved successfully, false on failure.
	 */
	private function save_media_id( $src, $id ) {
		if ( is_wp_error( $id ) || ! $id ) {
			return false;
		}

		$media = get_option( 'woodmart-vc-imported-media' );

		if ( ! $media || ! is_array( $media ) ) {
			$media = array();
		}

		$exists_id = array_search( $src, $media, true );

		if ( $exists_id ) {
			unset( $media[ $exists_id ] );
		}

		$media[ $id ] = $src;

		return update_option( 'woodmart-vc-imported-media', $media, false );
	}

	/**
	 * Load necessary importer classes for media handling. This method checks if the required importer classes are already loaded, and if not, it includes them from the specified paths. It also initializes the importer instance for later use in media processing.
	 *
	 * @return bool True if importers were loaded successfully, false on failure.
	 */
	private function load_importers() {
		// Load Importer API
		if ( ! function_exists( 'woodmart_get_importer' ) ) {
			return false;
		}

		$this->importer                    = woodmart_get_importer();
		$this->importer->fetch_attachments = true;
	}
}

Registry::get_instance()->vctemplates;
