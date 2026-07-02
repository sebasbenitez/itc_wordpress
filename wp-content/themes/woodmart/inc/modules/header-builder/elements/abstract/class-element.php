<?php
/**
 * Header builder element abstract class file.
 *
 * @package woodmart
 */

namespace XTS\Modules\Header_Builder;

use XTS\Modules\Header_Builder;

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );}

/**
 * Abstract class for all elements used in the builder. This class is used both on backend and
 * on the frontend.
 */
abstract class Element {

	/**
	 * Element arguments.
	 *
	 * @var array
	 */
	public $args = array();

	/**
	 * Template name.
	 *
	 * @var string
	 */
	public $template_name;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->map();
	}

	/**
	 * Map element.
	 *
	 * @return void
	 */
	public function map() {}

	/**
	 * Get header options.
	 *
	 * @return array
	 */
	public function get_header_options() {
		return Header_Builder::get_instance()->structure->get_header_options();
	}

	/**
	 * Get element arguments.
	 *
	 * @return array
	 */
	public function get_args() {
		if ( isset( $this->args['params'] ) ) {
			foreach ( $this->args['params'] as $field_id => $field ) {
				if ( isset( $field['type'] ) && 'select' === $field['type'] && isset( $field['callback'] ) && method_exists( $this, $field['callback'] ) ) {
					$this->args['params'][ $field_id ]['options'] = $this->{$field['callback']}();
				}
			}
		}

		return $this->args;
	}

	/**
	 * Render element.
	 *
	 * @param array  $el Element arguments.
	 * @param string $children Children HTML (Used in templates files).
	 *
	 * @return void
	 */
	public function render( $el, $children = '' ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		$args = $this->parse_args( $el );

		extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract

		$path = '/header-elements/' . $this->template_name . '.php';

		$located = '';

		if ( file_exists( get_stylesheet_directory() . $path ) ) {
			$located = get_stylesheet_directory() . $path;
		} elseif ( file_exists( get_template_directory() . $path ) ) {
			$located = get_template_directory() . $path;
		}

		if ( file_exists( $located ) ) {
			require $located;
		}
	}

	/**
	 * Parse element arguments.
	 *
	 * @param array $el Element arguments.
	 *
	 * @return array
	 */
	protected function parse_args( $el ) {
		$args = $this->parse_default_args( $el );

		return $args;
	}

	/**
	 * Parse element arguments.
	 *
	 * @param array $el Element arguments.
	 *
	 * @return array
	 */
	private function parse_default_args( $el ) {
		$a = array();

		foreach ( $el['params'] as $arg ) {
			$a[ $arg['id'] ] = $arg['value'];
		}

		unset( $el['content'] );

		$el['params'] = $a;

		return $el;
	}


	/**
	 * Check if element has background.
	 *
	 * @param array $params Element arguments.
	 *
	 * @return bool
	 */
	public function has_background( $params ) {
		return( isset( $params['background'] ) && ( isset( $params['background']['background-color'] ) || isset( $params['background']['background-image'] ) ) );
	}

	/**
	 * Check if element has backdrop filter.
	 *
	 * @param array $params Element arguments.
	 *
	 * @return bool
	 */
	public function has_backdrop_filter( $params ) {
		return isset( $params['background'] ) && (
			! empty( $params['background']['background-blur'] )
			|| ( isset( $params['background']['background-brightness'] ) && 1 !== (float) $params['background']['background-brightness'] && ( $params['background']['background-brightness'] || 0 === (float) $params['background']['background-brightness'] ) )
			|| ( ! empty( $params['background']['background-contrast'] ) && 100 !== (int) $params['background']['background-contrast'] )
			|| ! empty( $params['background']['background-grayscale'] )
			|| ! empty( $params['background']['background-hue-rotate'] )
			|| ! empty( $params['background']['background-invert'] )
			|| ( ! empty( $params['background']['background-opacity'] ) && 100 !== (int) $params['background']['background-opacity'] )
			|| ( ! empty( $params['background']['background-saturate'] ) && 100 !== (int) $params['background']['background-saturate'] )
			|| ! empty( $params['background']['background-sepia'] )
		);
	}

	/**
	 * Check if element has border.
	 *
	 * @param array $params Element arguments.
	 *
	 * @return bool
	 */
	public function has_border( $params ) {
		return( isset( $params['border'] ) && isset( $params['border']['width'] ) && (int) $params['border']['width'] > 0 );
	}

	/**
	 * Get menu options with empty.
	 *
	 * @return array
	 */
	public function get_menu_options_with_empty() {
		return $this->get_menu_options( true );
	}

	/**
	 * Get menu options.
	 *
	 * @param bool $is_empty Empty.
	 *
	 * @return array
	 */
	public function get_menu_options( $is_empty = false ) {
		$array = array();

		if ( $is_empty ) {
			$array[''] = array(
				'label' => esc_html__( 'Select', 'woodmart' ),
				'value' => '',
			);
		}

		$menus = get_terms(
			array(
				'taxonomy'   => 'nav_menu',
				'hide_empty' => false,
				'orderby'    => 'name',
			)
		);

		foreach ( $menus as $menu ) {
			$array[ $menu->slug ] = array(
				'label' => $menu->name,
				'value' => $menu->slug,
			);
		}

		return $array;
	}

	/**
	 * Get HTML block options.
	 *
	 * @return array
	 */
	public function get_html_block_options() {
		$array        = array();
		$args         = array(
			'posts_per_page'   => 500, // phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page
			'post_type'        => 'cms_block',
			'suppress_filters' => false,
		);
		$blocks_posts = get_posts( $args );

		foreach ( $blocks_posts as $post ) {
			$array[ $post->ID ] = array(
				'label' => $post->post_title ? $post->post_title : esc_html__( '(no title)', 'woodmart' ),
				'value' => $post->ID,
			);
		}

		return $array;
	}
}
