<?php
/**
 * Gutenberg integration for dynamic tags.
 *
 * @package woodmart
 */

namespace XTS\Modules\Dynamic_Tags\Integrations;

use XTS\Modules\Dynamic_Tags\Config;
use XTS\Modules\Dynamic_Tags\Content;
use XTS\Modules\Layouts\Single_Post;
use XTS\Modules\Layouts\Single_Product;

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Class Gutenberg
 */
class Gutenberg {
	/**
	 * Initialize the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_rest_fields' ) );

		add_filter( 'render_block', array( $this, 'render_image' ), 10, 2 );
		add_filter( 'render_block', array( $this, 'render_link' ), 10, 2 );
		add_filter( 'render_block', array( $this, 'render_text_content' ) );
	}

	/**
	 * Register REST fields.
	 *
	 * @return void
	 */
	public function register_rest_fields() {
		register_rest_route(
			'wd/v1',
			'/dynamic-options-link',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_options_link' ),
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
				'args'                => array(
					'source' => array(
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
			)
		);

		register_rest_route(
			'wd/v1',
			'/dynamic-options-media',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_options_media' ),
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
				'args'                => array(
					'source' => array(
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
			)
		);

		register_rest_route(
			'wd/v1',
			'/dynamic-options-text',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_options_text' ),
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
				'args'                => array(
					'source' => array(
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
			)
		);
	}

	/**
	 * Handle the REST API request to get dynamic fields.
	 *
	 * @param object $request Request object.
	 * @return \WP_Error|\WP_HTTP_Response|\WP_REST_Response
	 */
	public function get_options_link( $request ) {
		$source   = $request->get_param( 'source' );
		$post_id  = $request->get_param( 'post_id' );
		$term_id  = $request->get_param( 'term_id' );
		$taxonomy = $request->get_param( 'taxonomy' );

		$results = array();

		if ( 'current_post' === $source ) {
			$post_id = $this->resolve_preview_post_id( $post_id );
		}

		$config = new Config();

		if ( in_array( $source, array( 'current_post', 'other_post' ), true ) ) {
			$results = $config->get_post_link_options( $post_id );
		} elseif ( 'site' === $source ) {
			$results = $config->get_site_link_options();
		} elseif ( in_array( $source, array( 'other_taxonomy', 'current_taxonomy' ), true ) ) {
			$results = $config->get_taxonomy_link_options( 'current_taxonomy' === $source ? $taxonomy : $term_id );
		}

		return rest_ensure_response( array_values( apply_filters( 'woodmart_get_dynamic_fields', $results, $post_id, $source ) ) );
	}

	/**
	 * Handle the REST API request to get dynamic fields.
	 *
	 * @param object $request Request object.
	 * @return \WP_Error|\WP_HTTP_Response|\WP_REST_Response
	 */
	public function get_options_media( $request ) {
		$source   = $request->get_param( 'source' );
		$post_id  = $request->get_param( 'post_id' );
		$term_id  = $request->get_param( 'term_id' );
		$taxonomy = $request->get_param( 'taxonomy' );

		$results = array();

		if ( 'current_post' === $source ) {
			$post_id = $this->resolve_preview_post_id( $post_id );
		}

		$config = new Config();

		if ( in_array( $source, array( 'current_post', 'other_post' ), true ) ) {
			$results = $config->get_post_media_options( $post_id );
		} elseif ( 'site' === $source ) {
			$results = $config->get_site_media_options();
		} elseif ( in_array( $source, array( 'other_taxonomy', 'current_taxonomy' ), true ) ) {
			$results = $config->get_taxonomy_media_options( 'current_taxonomy' === $source ? $taxonomy : $term_id );
		}

		return rest_ensure_response( array_values( apply_filters( 'woodmart_get_dynamic_fields', $results, $post_id, $source ) ) );
	}

	/**
	 * Handle the REST API request to get dynamic fields.
	 *
	 * @param object $request Request object.
	 * @return \WP_Error|\WP_HTTP_Response|\WP_REST_Response
	 */
	public function get_options_text( $request ) {
		$source   = $request->get_param( 'source' );
		$post_id  = $request->get_param( 'post_id' );
		$term_id  = $request->get_param( 'term_id' );
		$taxonomy = $request->get_param( 'taxonomy' );

		$results = array();

		if ( 'current_post' === $source ) {
			$post_id = $this->resolve_preview_post_id( $post_id );
		}

		$config = new Config();

		if ( in_array( $source, array( 'current_post', 'other_post' ), true ) ) {
			$results = $config->get_post_text_options( $post_id );
		} elseif ( 'site' === $source ) {
			$results = $config->get_site_text_options();
		} elseif ( in_array( $source, array( 'other_taxonomy', 'current_taxonomy' ), true ) ) {
			$results = $config->get_taxonomy_text_options( 'current_taxonomy' === $source ? $taxonomy : $term_id );
		}

		return rest_ensure_response( array_values( apply_filters( 'woodmart_get_dynamic_fields', $results, $post_id, $source ) ) );
	}

	/**
	 * Render image blocks with dynamic content.
	 *
	 * @param string $block_content Content.
	 * @param array  $block Block update.
	 * @return string
	 */
	public function render_image( $block_content, $block ) {
		if (
			empty( $block['blockName'] ) ||
			! in_array( $block['blockName'], array( 'wd/image', 'wd/icon', 'wd/cover', 'wd/slider-item' ), true ) ||
			! isset( $block['attrs']['image']['wd-dynamic-tags'] ) ||
			! is_array( $block['attrs']['image']['wd-dynamic-tags'] ) ||
			empty( $block['attrs']['image']['wd-dynamic-tags']['source'] )
		) {
			return $block_content;
		}

		$data         = $block['attrs']['image']['wd-dynamic-tags'];
		$data['type'] = 'media';
		$image_size   = ! empty( $block['attrs']['imageSize'] ) ? $block['attrs']['imageSize'] : 'full';
		$dynamic_data = Content::get_instance()->get( $data );
		$image_url    = '';

		if ( str_contains( $dynamic_data, 'http' ) ) {
			$image_id = attachment_url_to_postid( $dynamic_data );
		} else {
			$image_id = $dynamic_data;
		}

		if ( $image_id ) {
			$image_url = woodmart_otf_get_image_url( $image_id, $image_size );
		}

		if ( ! $image_url ) {
			if ( in_array( $block['blockName'], array( 'wd/cover', 'wd/slider-item' ), true ) ) {
				return preg_replace( '/<img[^>]+>/i', '', $block_content );
			} else {
				return '';
			}
		}

		$block_content = preg_replace(
			'/(<img[^>]+src=["\'])([^"\']*)(["\'])/i',
			'$1' . esc_url( $image_url ) . '$3',
			$block_content
		);

		$block_content = str_replace(
			'wp-image-wd-dynamic-tags',
			'wp-image-' . $image_id,
			$block_content
		);

		$alt = trim( wp_strip_all_tags( get_post_meta( $image_id, '_wp_attachment_image_alt', true ) ) );

		if ( $alt ) {
			if ( str_contains( $block_content, ' alt="' ) ) {
				$block_content = preg_replace( '/\salt="[^"]*"/i', ' alt="' . $alt . '"', $block_content );
			} else {
				$block_content = str_replace( '<img ', '<img alt="' . esc_attr( $alt ) . '" ', $block_content );
			}
		}

		$title = get_the_title( $image_id );

		if ( $title ) {
			if ( str_contains( $block_content, ' title="' ) ) {
				$block_content = preg_replace( '/\stitle="[^"]*"/i', ' title="' . esc_attr( $title ) . '"', $block_content );
			} else {
				$block_content = str_replace( '<img ', '<img title="' . esc_attr( $title ) . '" ', $block_content );
			}
		}

		return $block_content;
	}

	/**
	 * Render link blocks with dynamic content.
	 *
	 * @param string $block_content Content.
	 * @param array  $block Block data.
	 * @return string
	 */
	public function render_link( $block_content, $block ) {
		if (
			empty( $block['blockName'] ) ||
			! str_contains( $block['blockName'], 'wd/' ) ||
			! str_contains( $block_content, '{{wd-dynamic/' )
		) {
			return $block_content;
		}

		return preg_replace_callback(
			'/\{\{wd-dynamic\/\{(.*?)\}\}\}/si',
			function ( $matches ) {
				$raw_data = $matches[1];

				$decoded = html_entity_decode( $raw_data, ENT_QUOTES );

				if ( ! strpos( $decoded, '{' ) ) {
					$decoded = '{' . $decoded . '}';
				}

				$data = json_decode( $decoded, true );

				if ( ! is_array( $data ) ) {
					return '';
				}

				$value = Content::get_instance()->get( $data );

				if ( $value ) {
					return $value;
				}

				return '';
			},
			$block_content
		);
	}

	/**
	 * Render text content blocks with dynamic content.
	 *
	 * @param string $block_content Content.
	 * @return string
	 */
	public function render_text_content( $block_content ) {
		if ( ! strpos( $block_content, 'data-wd-dynamic' ) ) {
			return $block_content;
		}

		$is_hide_block = false;

		$block_content = preg_replace_callback(
			'/<span[^>]*data-wd-dynamic="([^"]+)"[^>]*>(.*?)<\/span>/is',
			function ( $matches ) use ( &$is_hide_block ) {
				$settings = json_decode( html_entity_decode( $matches[1] ), true );

				if ( ! is_array( $settings ) ) {
					return '';
				}

				$dynamic_content = Content::get_instance()->get( $settings );

				if ( ! $dynamic_content ) {
					if ( ! empty( $settings['hide_empty'] ) ) {
						$is_hide_block = true;
					}

					return '';
				}

				return $dynamic_content;
			},
			$block_content
		);

		if ( $is_hide_block ) {
			return '';
		}

		return $block_content;
	}

	/**
	 * Update the post ID for dynamic tags based on the context of the layout or label being edited.
	 *
	 * @param int $post_id Post ID.
	 * @return int
	 */
	private function resolve_preview_post_id( $post_id ) {
		$post_type = get_post_type( $post_id );

		if ( 'woodmart_layout' === $post_type ) {
			$layout_type = get_post_meta( $post_id, 'wd_layout_type', true );

			if ( in_array( $layout_type, array( 'single_product', 'product_loop_item' ), true ) ) {
				$post_id = Single_Product::get_instance()::get_preview_product_id();
			} elseif ( in_array( $layout_type, array( 'single_portfolio', 'single_post' ), true ) ) {
				$post_id = Single_Post::get_instance()::get_preview_post_id( 'single_portfolio' === $layout_type ? 'portfolio' : 'post' );
			}
		} elseif ( 'wd_custom_label' === $post_type ) {
			$post_id = Single_Product::get_instance()::get_preview_product_id();
		}

		return $post_id;
	}
}

new Gutenberg();
