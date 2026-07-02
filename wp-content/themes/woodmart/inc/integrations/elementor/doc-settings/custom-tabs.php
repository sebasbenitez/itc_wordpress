<?php
/**
 * Custom tabs settings class.
 *
 * @package woodmart
 */

use Elementor\Controls_Manager;
use Elementor\Core\Base\Document;

if ( ! function_exists( 'woodmart_register_custom_tabs_settings_controls' ) ) {
	/**
	 * Register custom tabs settings controls.
	 *
	 * @param Document $document The document instance.
	 */
	function woodmart_register_custom_tabs_settings_controls( $document ) {
		if ( ! method_exists( $document, 'get_main_id' ) ) {
			return;
		}

		$post_id = $document->get_main_id();

		if ( ! $post_id ) {
			return;
		}

		$post_type = get_post_type( $post_id );

		if ( 'wd_product_tabs' !== $post_type ) {
			return;
		}

		$document->remove_control( 'template' );
		$document->remove_control( 'template_default_description' );
		$document->remove_control( 'template_theme_description' );
		$document->remove_control( 'template_canvas_description' );
		$document->remove_control( 'template_header_footer_description' );
		$document->remove_control( 'reload_preview_description' );

		// Title field (Tab title).
		$document->start_controls_section(
			'product_tab_settings_section',
			array(
				'label' => esc_html__( 'Tab Settings', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_SETTINGS,
			)
		);

		$tab_title = get_post_meta( $post_id, 'product_tab_title', true );
		$document->add_control(
			'wd_product_tab_title',
			array(
				'label'       => esc_html__( 'Tab title', 'woodmart' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'Leave empty to display default post title.', 'woodmart' ),
				'default'     => $tab_title ? $tab_title : '',
			)
		);

		// Priority field.
		$tab_priority = get_post_meta( $post_id, 'product_tab_priority', true );
		$document->add_control(
			'wd_product_tab_priority',
			array(
				'label'       => esc_html__( 'Priority', 'woodmart' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => esc_html__( 'Sets this tab\'s position among product tabs. Lower values mean higher priority. By default, 130 places it after standard WooCommerce tabs; values below 10 show it before them.', 'woodmart' ),
				'default'     => $tab_priority ? intval( $tab_priority ) : 130,
				'min'         => 1,
			)
		);

		$document->add_control(
			'wd_hidden_repeater',
			array(
				'type'    => Controls_Manager::REPEATER,
				'default' => array(),
				'fields'  => array(),
				'classes' => 'elementor-hidden',
			)
		);

		$conditions         = get_post_meta( $post_id, 'product_tab_condition', true );
		$default_conditions = array();

		if ( ! empty( $conditions ) && is_array( $conditions ) ) {
			foreach ( $conditions as $condition ) {
				if ( ! is_array( $condition ) ) {
					continue;
				}

				$repeater_item = array(
					'_id'        => uniqid(),
					'comparison' => isset( $condition['comparison'] ) ? $condition['comparison'] : 'include',
					'type'       => isset( $condition['type'] ) ? $condition['type'] : 'all',
				);

				$type = $repeater_item['type'];

				if ( 'product_type' === $type && isset( $condition['product-type-query'] ) ) {
					$repeater_item['query_product_type'] = $condition['product-type-query'];
				} elseif ( 'all' !== $type && isset( $condition['query'] ) ) {
					$repeater_item[ 'query_' . $type ] = $condition['query'];
				}

				$default_conditions[] = $repeater_item;
			}
		}

		if ( empty( $default_conditions ) ) {
			$default_conditions = array(
				array(
					'comparison' => 'include',
					'type'       => 'all',
				),
			);
		}

		$document->add_control(
			'wd_product_tab_condition',
			array(
				'label'   => esc_html__( 'Display conditions', 'woodmart' ),
				'type'    => Controls_Manager::REPEATER,
				'default' => $default_conditions,
				'fields'  => array(
					array(
						'name'    => 'comparison',
						'label'   => esc_html__( 'Comparison condition', 'woodmart' ),
						'type'    => Controls_Manager::SELECT,
						'default' => 'include',
						'options' => array(
							'include' => esc_html__( 'Include', 'woodmart' ),
							'exclude' => esc_html__( 'Exclude', 'woodmart' ),
						),
					),
					array(
						'name'    => 'type',
						'label'   => esc_html__( 'Condition type', 'woodmart' ),
						'type'    => Controls_Manager::SELECT,
						'default' => 'all',
						'options' => array(
							'all'                    => esc_html__( 'All products', 'woodmart' ),
							'product'                => esc_html__( 'Single product id', 'woodmart' ),
							'product_cat'            => esc_html__( 'Product category', 'woodmart' ),
							'product_cat_children'   => esc_html__( 'Child product categories', 'woodmart' ),
							'product_tag'            => esc_html__( 'Product tag', 'woodmart' ),
							'product_attr_term'      => esc_html__( 'Product attribute', 'woodmart' ),
							'product_type'           => esc_html__( 'Product type', 'woodmart' ),
							'product_shipping_class' => esc_html__( 'Product shipping class', 'woodmart' ),
							'product_brand'          => esc_html__( 'Product brand', 'woodmart' ),
						),
					),
					array(
						'name'      => 'query_product',
						'label'     => esc_html__( 'Condition query', 'woodmart' ),
						'type'      => 'wd_autocomplete',
						'search'    => 'woodmart_get_posts_by_query',
						'render'    => 'woodmart_get_posts_title_by_id',
						'post_type' => 'product',
						'default'   => '',
						'condition' => array(
							'type' => 'product',
						),
					),
					array(
						'name'       => 'query_product_cat',
						'label'      => esc_html__( 'Condition query', 'woodmart' ),
						'type'       => 'wd_autocomplete',
						'search'     => 'woodmart_get_posts_by_query',
						'render'     => 'woodmart_get_posts_title_by_id',
						'query_type' => 'product_cat',
						'default'    => '',
						'condition'  => array(
							'type' => 'product_cat',
						),
					),
					array(
						'name'       => 'query_product_cat_children',
						'label'      => esc_html__( 'Condition query', 'woodmart' ),
						'type'       => 'wd_autocomplete',
						'search'     => 'woodmart_get_posts_by_query',
						'render'     => 'woodmart_get_posts_title_by_id',
						'query_type' => 'product_cat_children',
						'default'    => '',
						'condition'  => array(
							'type' => 'product_cat_children',
						),
					),
					array(
						'name'       => 'query_product_tag',
						'label'      => esc_html__( 'Condition query', 'woodmart' ),
						'type'       => 'wd_autocomplete',
						'search'     => 'woodmart_get_posts_by_query',
						'render'     => 'woodmart_get_posts_title_by_id',
						'query_type' => 'product_tag',
						'default'    => '',
						'condition'  => array(
							'type' => 'product_tag',
						),
					),
					array(
						'name'       => 'query_product_attr_term',
						'label'      => esc_html__( 'Condition query', 'woodmart' ),
						'type'       => 'wd_autocomplete',
						'search'     => 'woodmart_get_posts_by_query',
						'render'     => 'woodmart_get_posts_title_by_id',
						'query_type' => 'product_attr_term',
						'default'    => '',
						'condition'  => array(
							'type' => 'product_attr_term',
						),
					),
					array(
						'name'      => 'query_product_type',
						'label'     => esc_html__( 'Condition query', 'woodmart' ),
						'type'      => Controls_Manager::SELECT,
						'default'   => 'simple',
						'options'   => array(
							'simple'   => esc_html__( 'Simple product', 'woodmart' ),
							'grouped'  => esc_html__( 'Grouped product', 'woodmart' ),
							'external' => esc_html__( 'External/Affiliate product', 'woodmart' ),
							'variable' => esc_html__( 'Variable product', 'woodmart' ),
						),
						'condition' => array(
							'type' => 'product_type',
						),
					),
					array(
						'name'       => 'query_product_shipping_class',
						'label'      => esc_html__( 'Condition query', 'woodmart' ),
						'type'       => 'wd_autocomplete',
						'search'     => 'woodmart_get_posts_by_query',
						'render'     => 'woodmart_get_posts_title_by_id',
						'query_type' => 'product_shipping_class',
						'default'    => '',
						'condition'  => array(
							'type' => 'product_shipping_class',
						),
					),
					array(
						'name'       => 'query_product_brand',
						'label'      => esc_html__( 'Condition query', 'woodmart' ),
						'type'       => 'wd_autocomplete',
						'search'     => 'woodmart_get_posts_by_query',
						'render'     => 'woodmart_get_posts_title_by_id',
						'query_type' => 'product_brand',
						'default'    => '',
						'condition'  => array(
							'type' => 'product_brand',
						),
					),
				),
			)
		);

		$document->end_controls_section();
	}

	add_action( 'elementor/documents/register_controls', 'woodmart_register_custom_tabs_settings_controls', 10, 1 );
}
