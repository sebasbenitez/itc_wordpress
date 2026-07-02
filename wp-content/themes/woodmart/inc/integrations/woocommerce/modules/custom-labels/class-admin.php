<?php
/**
 * Custom labels class.
 *
 * @package woodmart
 */

namespace XTS\Modules\Custom_Labels;

use XTS\Gutenberg\Block_Attributes;
use XTS\Gutenberg\Block_CSS;
use XTS\Modules\Styles_Storage;
use XTS\Singleton;

/**
 * Admin class.
 */
class Admin extends Singleton {
	/**
	 * Meta from REST API.
	 *
	 * @var array
	 */
	private $rest_meta_data = array();

	/**
	 * Init.
	 */
	public function init() {
		add_action( 'rest_api_init', array( $this, 'register_labels_rest_routes' ) );

		if ( ! woodmart_get_opt( 'custom_labels' ) ) {
			return;
		}

		add_filter( 'woodmart_admin_localized_string_array', array( $this, 'add_localized_settings' ) );
		add_filter( 'views_edit-wd_custom_label', array( $this, 'print_interface' ) );
		add_action( 'delete_post', array( $this, 'delete_label' ) );
		add_action( 'save_post', array( $this, 'clear_css' ), 10, 2 );

		add_action( 'init', array( $this, 'register_meta_fields' ), 100 );
		add_action( 'rest_pre_insert_wd_custom_label', array( $this, 'handle_rest_post_save' ), 10, 3 );
		add_filter( 'woodmart_post_blocks_css', array( $this, 'render_css' ), 10, 3 );

		add_action( 'pre_get_posts', array( $this, 'sort_by_menu_order' ) );
		add_filter( 'manage_wd_custom_label_posts_columns', array( $this, 'add_order_column' ) );
		add_action( 'manage_wd_custom_label_posts_custom_column', array( $this, 'render_order_column' ), 10, 2 );
		add_filter( 'manage_edit-wd_custom_label_sortable_columns', array( $this, 'sortable_order_column' ) );
	}

	/**
	 * Register REST routes.
	 *
	 * @return void
	 */
	public function register_labels_rest_routes() {
		register_rest_route(
			'wd/v1',
			'/product-label',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_product_label_callback' ),
				'permission_callback' => function () {
					return is_user_logged_in();
				},
			)
		);

		register_rest_route(
			'wd/v1',
			'/product-labels',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_product_labels_callback' ),
				'permission_callback' => function () {
					return is_user_logged_in();
				},
			)
		);
	}

	/**
	 * Get product label.
	 *
	 * @param object $request Request object.
	 * @return array
	 */
	public function get_product_label_callback( $request ) {
		$response = array();
		$search   = $request->get_param( 'search' );

		$default_labels = $this->get_rest_default_labels_options( $search );
		$custom_labels  = $this->get_rest_custom_labels_options( $search );

		if ( $default_labels ) {
			$response[] = $default_labels;
		}

		if ( woodmart_get_opt( 'custom_labels' ) && $custom_labels ) {
			$response[] = $custom_labels;
		}

		return rest_ensure_response( $response );
	}

	/**
	 * Get product labels.
	 *
	 * @param object $request Request object.
	 * @return array
	 */
	public function get_product_labels_callback( $request ) {
		$response     = array();
		$search       = $request->get_param( 'search' );
		$extra_fields = array(
			array(
				'value' => 'attrs',
				'label' => esc_html__( 'Product attributes', 'woodmart' ),
			),
		);

		$default_labels = $this->get_rest_default_labels_options( $search, $extra_fields );
		$custom_labels  = $this->get_rest_custom_labels_options( $search );

		if ( $default_labels ) {
			$response[] = $default_labels;
		}

		if ( woodmart_get_opt( 'custom_labels' ) && $custom_labels ) {
			$response[] = $custom_labels;
		}

		return rest_ensure_response( $response );
	}

	/**
	 * Get custom labels group for REST API.
	 *
	 * @param string $search Search string.
	 * @return array
	 */
	public function get_rest_custom_labels_options( $search ) {
		$args = array(
			'post_type'   => 'wd_custom_label',
			'post_status' => array( 'publish', 'private', 'draft' ),
			'numberposts' => 50,
		);

		if ( $search ) {
			$args['s'] = $search;
		}

		$custom_labels = get_posts( $args );

		if ( ! $custom_labels ) {
			return array();
		}

		$options = array();

		foreach ( $custom_labels as $custom_label ) {
			$options[] = array(
				'value' => (string) $custom_label->ID,
				'label' => $custom_label->post_title,
			);
		}

		return array(
			'label'   => esc_html__( 'Custom labels', 'woodmart' ),
			'options' => $options,
		);
	}

	/**
	 * Get product labels.
	 *
	 * @param string $search Search string.
	 * @param array  $extra_fields Extra fields.
	 * @return array
	 */
	public function get_rest_default_labels_options( $search, $extra_fields = array() ) {
		$options = array(
			array(
				'value' => 'sale',
				'label' => esc_html__( 'Sale', 'woodmart' ),
			),
			array(
				'value' => 'out-of-stock',
				'label' => esc_html__( 'Out of stock', 'woodmart' ),
			),
			array(
				'value' => 'hot',
				'label' => esc_html__( 'Hot', 'woodmart' ),
			),
			array(
				'value' => 'new',
				'label' => esc_html__( 'New', 'woodmart' ),
			),
		);

		if ( $extra_fields ) {
			$options = array_merge( $options, $extra_fields );
		}

		if ( $search ) {
			$options = array_filter(
				$options,
				function ( $option ) use ( $search ) {
					return $search && false !== stripos( $option['label'], $search );
				}
			);

			if ( ! $options ) {
				return array();
			}
		}

		return array(
			'label'   => esc_html__( 'Default labels', 'woodmart' ),
			'options' => array_values( $options ),
		);
	}

	/**
	 * Add order column to custom labels list table.
	 *
	 * @param array $columns Columns.
	 * @return array Columns.
	 */
	public function add_order_column( $columns ) {
		$columns['menu_order'] = esc_html__( 'Order', 'woodmart' );
		return $columns;
	}

	/**
	 * Render order column in custom labels list table.
	 *
	 * @param string $column Column.
	 * @param int    $post_id Post ID.
	 * @return void
	 */
	public function render_order_column( $column, $post_id ) {
		if ( 'menu_order' === $column ) {
			echo (int) get_post_field( 'menu_order', $post_id );
		}
	}

	/**
	 * Make order column sortable.
	 *
	 * @param array $columns Columns.
	 * @return array Columns.
	 */
	public function sortable_order_column( $columns ) {
			$columns['menu_order'] = 'menu_order';
			return $columns;
	}

	/**
	 * Sort custom labels by menu order.
	 *
	 * @param WP_Query $query Query object.
	 * @return void
	 */
	public function sort_by_menu_order( $query ) {
		if ( ! is_admin() || ! $query->is_main_query() ) {
			return;
		}

		if ( 'wd_custom_label' !== $query->get( 'post_type' ) ) {
			return;
		}

		if ( ! $query->get( 'orderby' ) ) {
			$query->set( 'orderby', 'menu_order' );
			$query->set( 'order', 'ASC' );
		}
	}

	/**
	 * Register post meta fields for Gutenberg editor.
	 *
	 * @return void
	 */
	public function register_meta_fields() {
		$attributes = $this->get_block_attributes();

		if ( ! $attributes ) {
			return;
		}

		foreach ( $attributes as $key => $value ) {
			$settings = array(
				'single'        => true,
				'type'          => $value['type'],
				'auth_callback' => '__return_true',
				'show_in_rest'  => in_array( $value['type'], array( 'array', 'object' ), true ) ? $this->prepare_rest_schema_for_attribute( $value['type'] ) : true,
			);

			if ( isset( $value['default'] ) ) {
				$settings['default'] = $value['default'];
			}

			register_post_meta(
				'wd_custom_label',
				$key,
				$settings
			);
		}
	}

	/**
	 * Prepare REST schema configuration for an attribute type.
	 *
	 * @param string $type Attribute type.
	 * @return array
	 */
	public function prepare_rest_schema_for_attribute( $type ) {
		return array(
			'schema' => array(
				'type'                 => $type,
				'additionalProperties' => true,
			),
		);
	}

	/**
	 * Define custom labels attributes configuration.
	 *
	 * @return array
	 */
	protected function get_block_attributes() {
		if ( ! class_exists( 'XTS\Gutenberg\Block_Attributes' ) ) {
			return array();
		}

		$attr = new Block_Attributes();

		wd_get_padding_control_attrs( $attr, 'wd_padding' );
		wd_get_box_shadow_control_attrs( $attr, 'wd_box_shadow' );
		wd_get_border_control_attrs( $attr, 'wd_border' );
		wd_get_background_control_attrs( $attr, 'wd_background' );
		wd_get_backdrop_filter_control_attrs( $attr, 'wd_backdrop_filter' );

		$attr->add_attr(
			array(
				'wd_horizontal_align'   => array(
					'type'    => 'string',
					'default' => '',
				),
				'wd_vertical_align'     => array(
					'type'    => 'string',
					'default' => '',
				),
				'wd_width'              => array(
					'type'       => 'string',
					'responsive' => true,
					'units'      => 'px',
				),
				'wd_height'             => array(
					'type'       => 'string',
					'responsive' => true,
					'units'      => 'px',
				),
				'wd_orientation'        => array(
					'type'    => 'string',
					'default' => 'row',
				),
				'wd_block_gap'          => array(
					'type'       => 'string',
					'responsive' => true,
				),
				'wd_z_index'            => array(
					'type' => 'string',
				),
				'wd_hide_custom_labels' => array(
					'type'       => 'boolean',
					'responsive' => true,
				),
				'wd_label_conditions'   => array(
					'type'    => 'object',
					'default' => array(
						array(
							'comparison' => 'include',
							'type'       => 'all',
						),
					),
				),
				'bestselling'           => array(
					'type' => 'boolean',
				),
				'bestselling_limit'     => array(
					'type'    => 'string',
					'default' => '5',
				),
				'low_stock'             => array(
					'type' => 'boolean',
				),
				'low_stock_limit'       => array(
					'type'    => 'string',
					'default' => '5',
				),
			),
		);

		return $attr->get_attr();
	}

	/**
	 * Handle post save from REST API and store meta data.
	 *
	 * @param \stdClass        $post Post object prepared for database.
	 * @param \WP_REST_Request $request REST request object.
	 */
	public function handle_rest_post_save( $post, $request ) {
		$this->rest_meta_data = $request->get_param( 'meta' );

		return $post;
	}

	/**
	 * Render CSS.
	 *
	 * @param array  $css Existing CSS.
	 * @param int    $post_id Post ID.
	 * @param object $post Post object.
	 * @return array
	 */
	public function render_css( $css, $post_id, $post ) {
		if ( ! $post_id || ! $post || 'wd_custom_label' !== $post->post_type ) {
			return $css;
		}

		$attrs = array();

		if ( ! empty( $this->rest_meta_data ) ) {
			$attrs = $this->rest_meta_data;
		} else {
			$raw_attrs = $this->get_block_attributes();

			foreach ( $raw_attrs as $attr => $value ) {
				$attrs[ $attr ] = get_post_meta( $post_id, $attr, true );
			}
		}

		if ( empty( $attrs ) || ! is_array( $attrs ) ) {
			return $css;
		}

		$orientation     = isset( $attrs['wd_orientation'] ) ? $attrs['wd_orientation'] : 'column';
		$horizontal_prop = ( 'column' === $orientation ) ? 'align-items' : 'justify-content';
		$vertical_prop   = ( 'column' === $orientation ) ? 'justify-content' : 'align-items';

		$block_css      = new Block_CSS( $attrs );
		$block_selector = '.wd.wd .wd-label-' . $post_id;

		$block_css->add_css_rules(
			$block_selector,
			array(
				array(
					'attr_name' => 'wd_width',
					'template'  => '--wd-label-w: {{value}}' . $block_css->get_units_for_attribute( 'wd_width' ) . ';',
				),
				array(
					'attr_name' => 'wd_height',
					'template'  => '--wd-label-h: {{value}}' . $block_css->get_units_for_attribute( 'wd_height' ) . ';',
				),
				array(
					'attr_name' => 'wd_block_gap',
					'template'  => '--wd-label-gap: {{value}}px;',
				),
				array(
					'attr_name' => 'wd_horizontal_align',
					'template'  => $horizontal_prop . ': {{value}};',
				),
				array(
					'attr_name' => 'wd_vertical_align',
					'template'  => $vertical_prop . ': {{value}};',
				),
			)
		);

		if ( 'row' !== $orientation ) {
			$block_css->add_css_rules(
				$block_selector,
				array(
					array(
						'attr_name' => 'wd_orientation',
						'template'  => 'flex-direction: {{value}};',
					),
				)
			);
		}

		$block_css->add_css_rules(
			$block_selector,
			array(
				array(
					'attr_name' => 'wd_widthTablet',
					'template'  => '--wd-label-w: {{value}}' . $block_css->get_units_for_attribute( 'wd_width', 'tablet' ) . ';',
				),
				array(
					'attr_name' => 'wd_heightTablet',
					'template'  => '--wd-label-h: {{value}}' . $block_css->get_units_for_attribute( 'wd_height', 'tablet' ) . ';',
				),
				array(
					'attr_name' => 'wd_block_gapTablet',
					'template'  => '--wd-label-gap: {{value}}px;',
				),

			),
			'tablet'
		);

		$block_css->add_css_rules(
			$block_selector,
			array(
				array(
					'attr_name' => 'wd_widthMobile',
					'template'  => '--wd-label-w: {{value}}' . $block_css->get_units_for_attribute( 'wd_width', 'mobile' ) . ';',
				),
				array(
					'attr_name' => 'wd_heightMobile',
					'template'  => '--wd-label-h: {{value}}' . $block_css->get_units_for_attribute( 'wd_height', 'mobile' ) . ';',
				),
				array(
					'attr_name' => 'wd_block_gapMobile',
					'template'  => '--wd-label-gap: {{value}}px;',
				),
			),
			'mobile'
		);

		$block_css->merge_with( wd_get_block_bg_css( $block_selector, $attrs, 'wd_background', 'image' ) );
		$block_css->merge_with( wd_get_block_backdrop_filter_css( $block_selector, $attrs, 'wd_backdrop_filter' ) );
		$block_css->merge_with( wd_get_block_border_css( $block_selector, $attrs, 'wd_border' ) );
		$block_css->merge_with( wd_get_block_box_shadow_css( $block_selector, $attrs, 'wd_box_shadow' ) );
		$block_css->merge_with( wd_get_block_padding_css( $block_selector, $attrs, 'wd_padding' ) );

		$css_for_devices = $block_css->get_css_for_devices();

		foreach ( $css_for_devices as $device => $css_device ) {
			if ( $css_device ) {
				$css[ $device ] .= ' ' . $css_device;
			}
		}

		return $css;
	}

	/**
	 * Add localized settings.
	 *
	 * @param array $localize_data List of localized dates.
	 *
	 * @return array
	 */
	public function add_localized_settings( $localize_data ) {
		$localize_data['label_creation_error'] = esc_html__( 'Something went wrong with the creation of the label!', 'woodmart' );

		return $localize_data;
	}

	/**
	 * Clear CSS data.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post Post object.
	 *
	 * @return void
	 */
	public function clear_css( $post_id, $post ) {
		if ( 'wd_custom_label' !== $post->post_type ) {
			return;
		}

		$storage = new Styles_Storage( 'label-' . $post_id, 'post_meta', $post_id );
		$storage->delete_css();
		$storage->reset_data();
	}

	/**
	 * Delete post.
	 *
	 * @param int $post_id Post ID.
	 */
	public function delete_label( $post_id ) {
		$storage = new Styles_Storage( 'label-' . $post_id, 'post_meta', $post_id );
		$storage->delete_css();
		$storage->reset_data();
	}

	/**
	 * Print interface.
	 *
	 * @param array $views Views.
	 *
	 * @return array Views.
	 */
	public function print_interface( $views ) {
		wp_enqueue_script( 'wd-custom-labels', WOODMART_THEME_DIR . '/inc/integrations/woocommerce/modules/custom-labels/admin/assets/createForm.js', array( 'jquery' ), WOODMART_VERSION, true );
		$this->get_template( 'interface', array( 'admin' => $this ) );
		return $views;
	}

	/**
	 * Print create form.
	 */
	public function get_form() {
		ob_start();

		$this->get_template(
			'create-form',
			array(
				'admin' => $this,
			)
		);

		return ob_get_clean();
	}

	/**
	 * Get template.
	 *
	 * @param string $template_name Template name.
	 * @param array  $args          Arguments.
	 */
	public function get_template( $template_name, $args = array() ) {
		if ( ! empty( $args ) && is_array( $args ) ) {
			extract( $args ); // phpcs:ignore
		}
		include WOODMART_THEMEROOT . '/inc/integrations/woocommerce/modules/custom-labels/admin/templates/' . $template_name . '.php';
	}
}

Admin::get_instance();
