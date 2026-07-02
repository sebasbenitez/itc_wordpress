<?php
/**
 * Admin custom labels class file.
 *
 * @package woodmart
 */

namespace XTS\Modules\Custom_Labels;

use XTS\Modules\Custom_Labels\Import;
use XTS\Modules\Frequently_Bought_Together\Frontend as FBT_Frontend;
use XTS\Singleton;

/**
 * Manager class.
 */
class Manager extends Singleton {

	/**
	 * Init.
	 */
	public function init() {
		if ( woodmart_is_elementor_installed() ) {
			add_filter( 'woodmart_get_posts_by_query_results', array( $this, 'add_labels_to_autocomplete_query' ), 10, 4 );
			add_filter( 'woodmart_get_posts_title_by_id_results', array( $this, 'add_labels_to_autocomplete_title' ), 10, 4 );
		}

		if ( ! woodmart_get_opt( 'custom_labels' ) ) {
			return;
		}

		add_action( 'wp_ajax_wd_custom_label_create', array( $this, 'create_custom_label' ) );
		add_action( 'woocommerce_after_product_object_save', array( $this, 'clear_min_stock_quantity_transient' ), 10 );
	}

	/**
	 * Clear transients.
	 *
	 * @param WC_Product $product Product object.
	 *
	 * @return void
	 */
	public function clear_min_stock_quantity_transient( $product ) {
		$product_id = $product ? $product->get_id() : '';

		if ( ! $product_id ) {
			return;
		}

		delete_transient( 'woodmart_min_stock_quantity_' . $product_id );
	}

	/**
	 * Create custom label.
	 */
	public function create_custom_label() {
		check_ajax_referer( 'wd-new-template-nonce', 'security' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'You do not have permission to perform this action.', 'woodmart' ),
				),
				403
			);
		}

		$name            = woodmart_clean( isset( $_POST['name'] ) ? $_POST['name'] : 'New label' ); // phpcs:ignore
		$predefined_name = isset( $_POST['predefined_name'] ) ? woodmart_clean( $_POST['predefined_name'] ) : ''; // phpcs:ignore
		$importer        = new Import();

		if ( ! $predefined_name ) {
			$args = array(
				'post_title' => $name,
				'post_type'  => 'wd_custom_label',
			);

			$id = wp_insert_post( $args );

			wp_send_json(
				array(
					'redirect_url' => html_entity_decode( get_edit_post_link( $id ) ),
				)
			);
		}

		ob_start();
		$id = $importer->import_xml( $predefined_name );
		ob_end_clean();

		wp_update_post(
			array(
				'ID'          => $id,
				'post_title'  => $name,
				'post_name'   => sanitize_title( $name ),
				'post_status' => 'draft',
				'post_date'   => current_time( 'Y-m-d H:i:s' ),
			)
		);

		$url = html_entity_decode( get_edit_post_link( $id ) );

		if ( ! $url ) {
			wp_send_json_error( esc_html__( 'Error creating custom label', 'woodmart' ) );
		}

		wp_send_json(
			array(
				'redirect_url' => $url,
			)
		);
	}

	/**
	 * Get condition priority.
	 *
	 * @param string $type Condition type.
	 *
	 * @return int
	 */
	public function get_condition_priority( $type ) {
		$priority = 50;

		switch ( $type ) {
			case 'all':
				$priority = 10;
				break;
			case 'product_cats':
			case 'product_cat_children':
			case 'product_tag':
				$priority = 20;
				break;
			case 'product_attr':
			case 'product_type':
			case 'product_stock_status':
			case 'product_shipping_class':
			case 'product_with_reviews':
			case 'sale_products':
			case 'featured_products':
			case 'new_products':
				$priority = 30;
				break;
			case 'products':
				$priority = 40;
				break;
			case 'user_role':
				$priority = 50;
				break;
		}

		return $priority;
	}

	/**
	 * Sort conditions by priority.
	 *
	 * @param array $a First condition.
	 * @param array $b Second condition.
	 *
	 * @return int
	 */
	public function sort_by_priority( $a, $b ) {
		return $b['condition_priority'] <=> $a['condition_priority'];
	}

	/**
	 * Check custom labels conditions and determine if a label should be active.
	 *
	 * @param array $conditions Custom label condition name.
	 * @param int   $label_id Custom label ID.
	 *
	 * @return bool
	 */
	public function check_conditions( $conditions, $label_id ) {
		if ( empty( $conditions ) || ! is_array( $conditions ) ) {
			return false;
		}

		global $product;

		if ( ! $product && function_exists( 'wc_get_product' ) ) {
			$product = wc_get_product( get_the_ID() );
		}

		$product_id = $product ? $product->get_id() : 0;

		foreach ( $conditions as $id => $condition ) {
			$conditions[ $id ]['condition_priority'] = $this->get_condition_priority( $condition['type'] );
		}

		uasort( $conditions, array( $this, 'sort_by_priority' ) );

		$is_active  = false;
		$is_exclude = false;

		foreach ( $conditions as $condition ) {
			$type       = isset( $condition['type'] ) ? $condition['type'] : '';
			$query      = isset( $condition['query'] ) ? $condition['query'] : '';
			$comparison = isset( $condition['comparison'] ) ? $condition['comparison'] : 'include';

			if ( $query ) {
				$query = apply_filters( 'wpml_object_id', $query, $type, true, apply_filters( 'wpml_current_language', null ) ); // phpcs:ignore.
			}

			switch ( $type ) {
				case 'all':
					$is_active = 'include' === $comparison;

					if ( 'exclude' === $comparison ) {
						$is_exclude = true;
					}
					break;

				case 'products':
					$query              = array_map( 'intval', explode( ',', (string) $query ) );
					$is_needed_products = $product_id && in_array( (int) $product_id, $query, true );

					if ( $is_needed_products ) {
						if ( 'exclude' === $comparison ) {
							$is_active  = false;
							$is_exclude = true;
						} else {
							$is_active = true;
						}
					}
					break;

				case 'product_type':
					$is_needed_type = $product && $product->get_type() === $query;

					if ( $is_needed_type ) {
						if ( 'exclude' === $comparison ) {
							$is_active  = false;
							$is_exclude = true;
						} else {
							$is_active = true;
						}
					}
					break;

				case 'product_attr':
					$query = explode( ',', (string) $query );

					foreach ( $query as $attribute_id ) {
						$taxonomy = 'product_brand' === $attribute_id ? $attribute_id : wc_attribute_taxonomy_name_by_id( (int) $attribute_id );

						if ( $product_id && $taxonomy && taxonomy_exists( $taxonomy ) ) {
							$terms = wp_get_post_terms( $product_id, $taxonomy, array( 'fields' => 'ids' ) );

							if ( $terms && ! is_wp_error( $terms ) && ! empty( $terms ) ) {
								if ( 'exclude' === $comparison ) {
									$is_active  = false;
									$is_exclude = true;
								} else {
									$is_active = true;
								}
							}
						}
					}

					break;

				case 'product_cats':
				case 'product_tag':
				case 'product_brand':
				case 'product_attr_term':
				case 'product_shipping_class':
					if ( $product_id ) {
						$terms = wp_get_post_terms( $product_id, get_taxonomies(), array( 'fields' => 'ids' ) );

						if ( $terms && ! is_wp_error( $terms ) ) {
							$query           = array_map( 'intval', explode( ',', (string) $query ) );
							$is_needed_terms = array_intersect( $query, $terms );

							if ( $is_needed_terms ) {
								if ( 'exclude' === $comparison ) {
									$is_active  = false;
									$is_exclude = true;
								} else {
									$is_active = true;
								}
							}
						}
					}
					break;

				case 'product_cat_children':
					if ( $product_id ) {
						$terms                  = wp_get_post_terms( $product_id, get_taxonomies(), array( 'fields' => 'ids' ) );
						$query                  = array_map( 'intval', explode( ',', (string) $query ) );
						$is_needed_cat_children = false;

						foreach ( $query as $term_id ) {
							$term_children = get_term_children( $term_id, 'product_cat' );

							if ( $terms && ! is_wp_error( $terms ) && ! is_wp_error( $term_children ) ) {
								$is_needed_cat_children = ! empty( array_intersect( $terms, $term_children ) );

								if ( $is_needed_cat_children ) {
									break;
								}
							}
						}

						if ( $is_needed_cat_children ) {
							if ( 'exclude' === $comparison ) {
								$is_active  = false;
								$is_exclude = true;
							} else {
								$is_active = true;
							}
						}
					}
					break;

				case 'product_stock_status':
					if ( $product ) {
						$is_needed_stock_status = $product->get_stock_status() === $query;
						if ( $is_needed_stock_status ) {
							if ( 'exclude' === $comparison ) {
								$is_active  = false;
								$is_exclude = true;
							} else {
								$is_active = true;
							}
						}
					}

					break;

				case 'product_with_reviews':
					$is_needed_reviews = $product && $product->get_rating_count() > 0;

					if ( $is_needed_reviews ) {
						if ( 'exclude' === $comparison ) {
							$is_active  = false;
							$is_exclude = true;
						} else {
							$is_active = true;
						}
					}
					break;

				case 'user_role':
					$user_roles     = is_user_logged_in() ? (array) wp_get_current_user()->roles : array();
					$is_needed_role = in_array( $query, $user_roles, true );

					if ( $is_needed_role ) {
						if ( 'exclude' === $comparison ) {
							$is_active  = false;
							$is_exclude = true;
						} else {
							$is_active = true;
						}
					}
					break;

				case 'sale_products':
					$is_on_sale = $product && ( $product->is_on_sale() || FBT_Frontend::get_instance()->get_discount_product_bundle( $product_id ) );

					if ( $is_on_sale ) {
						if ( 'exclude' === $comparison ) {
							$is_active  = false;
							$is_exclude = true;
						} else {
							$is_active = true;
						}
					}
					break;

				case 'featured_products':
					$is_featured = $product && $product->is_featured();

					if ( $is_featured ) {
						if ( 'exclude' === $comparison ) {
							$is_active  = false;
							$is_exclude = true;
						} else {
							$is_active = true;
						}
					}
					break;

				case 'new_products':
					$is_new = woodmart_is_new_label_needed( get_the_ID() );

					if ( $is_new ) {
						if ( 'exclude' === $comparison ) {
							$is_active  = false;
							$is_exclude = true;
						} else {
							$is_active = true;
						}
					}
					break;

				case 'bestselling':
					$limit       = (int) get_post_meta( $label_id, 'bestselling_limit', true );
					$bestsellers = $this->get_bestsellers( $limit );

					if ( in_array( $product_id, $bestsellers, true ) ) {
						if ( 'exclude' === $comparison ) {
							$is_active  = false;
							$is_exclude = true;
						} else {
							$is_active = true;
						}
					}
					break;

				case 'low_stock':
					$stock_limit = (int) get_post_meta( $label_id, 'low_stock_limit', true );

					if ( $stock_limit > 0 ) {
						$min_stock = get_transient( 'woodmart_min_stock_quantity_' . $product_id );

						if ( ! $min_stock ) {
							$min_stock = $this->get_min_stock_quantity( $product );
							set_transient( 'woodmart_min_stock_quantity_' . $product_id, $min_stock, WEEK_IN_SECONDS );
						}

						if ( $min_stock && $min_stock > 0 && $min_stock <= $stock_limit ) {
							if ( 'exclude' === $comparison ) {
								$is_active  = false;
								$is_exclude = true;
							} else {
								$is_active = true;
							}
						}
					}

					break;
			}

			if ( $is_exclude || $is_active ) {
				break;
			}
		}

		return $is_active;
	}

	/**
	 * Get minimum stock quantity for products.
	 *
	 * @param WC_Product $product Product object.
	 *
	 * @return int|false Stock quantity or false if stock management is disabled.
	 */
	private function get_min_stock_quantity( $product ) {
		if ( $product->is_type( 'variable' ) ) {
			$min_stock  = false;
			$variations = $product->get_available_variations();

			foreach ( $variations as $variation_data ) {
				$variation = wc_get_product( $variation_data['variation_id'] );

				if ( $variation && $variation->managing_stock() ) {
					$stock     = $variation->get_stock_quantity();
					$min_stock = false === $min_stock ? $stock : min( $min_stock, $stock );
				}
			}

			return $min_stock;
		}

		if ( $product->managing_stock() ) {
			return $product->get_stock_quantity();
		}

		return false;
	}

	/**
	 * Get top bestselling products.
	 *
	 * @param int $count Number of products to retrieve.
	 *
	 * @return array Array of product IDs.
	 */
	private function get_bestsellers( $count = 10 ) {
		$transient_key = 'woodmart_top_bestsellers_' . $count;
		$bestsellers   = get_transient( $transient_key );

		if ( false === $bestsellers ) {
			$args = array(
				'post_type'      => 'product',
				'posts_per_page' => $count,
				'meta_key'       => 'total_sales', // phpcs:ignore.
				'orderby'        => 'meta_value_num',
				'order'          => 'DESC',
				'fields'         => 'ids',
				'post_status'    => 'publish',
			);

			$bestsellers = get_posts( $args );
			set_transient( $transient_key, $bestsellers, DAY_IN_SECONDS );
		}

		return $bestsellers;
	}

	/**
	 * Retrieves the IDs of custom labels that exist on current page.
	 *
	 * @return array The IDs of rendered custom labels.
	 */
	public function get_current_custom_labels_ids() {
		$rendered_labels_ids = array();
		$all_conditions      = $this->get_all_conditions();

		if ( ! woodmart_get_opt( 'custom_labels' ) ) {
			return array();
		}

		foreach ( $all_conditions as $label_id => $conditions ) {
			if ( in_array( $label_id, $rendered_labels_ids, true ) ) {
				continue;
			}

			if ( ! $this->check_conditions( $conditions, $label_id ) || 'publish' !== get_post_status( $label_id ) ) {
				continue;
			}

			$rendered_labels_ids[] = (int) $label_id;
		}
		return $rendered_labels_ids;
	}


	/**
	 * Get all custom label IDs.
	 *
	 * Retrieves all IDs of posts with the post type 'wd_custom_label'.
	 *
	 * @return array List of custom label IDs.
	 */
	public function get_all_custom_labels_ids() {
		$all_ids = get_posts(
			array(
				'fields'         => 'ids',
				'posts_per_page' => apply_filters( 'woodmart_custom_labels_posts_per_page', 100 ),
				'post_type'      => 'wd_custom_label',
				'orderby'        => 'menu_order',
				'order'          => 'ASC',
				'post_status'    => 'publish',
			)
		);

		return $all_ids;
	}

	/**
	 * Get all custom label conditions.
	 *
	 * Retrieves conditions for all custom labels by their IDs.
	 *
	 * @return array List of conditions grouped by custom label IDs.
	 */
	public function get_all_conditions() {
		$all_ids        = $this->get_all_custom_labels_ids();
		$all_conditions = array();

		foreach ( $all_ids as $id ) {
			$conditions = $this->get_custom_label_conditions( $id );
			if ( ! empty( $conditions ) ) {
				$all_conditions[ $id ] = $conditions;
			}
		}

		return $all_conditions;
	}

	/**
	 * Get conditions for a single custom label
	 *
	 * @param string $id Custom label ID.
	 *
	 * @return array List of conditions for the custom label.
	 */
	public function get_custom_label_conditions( $id = '' ) {
		$conditions = get_post_meta( $id, 'wd_label_conditions', true );

		if ( empty( $conditions ) || ! is_array( $conditions ) ) {
			return array();
		}

		return $conditions;
	}

	/**
	 * Add default labels to Elementor autocomplete.
	 *
	 * @param array  $results Results.
	 * @param string $search_string Search string.
	 * @param string $post_type Post type.
	 * @param string $query_type Query type.
	 * @return array
	 */
	public function add_labels_to_autocomplete_query( $results, $search_string, $post_type, $query_type ) {
		if ( 'wd_custom_label' !== $post_type ) {
			return $results;
		}

		$extra_labels = array(
			array(
				'id'   => 'sale',
				'text' => esc_html__( 'Sale', 'woodmart' ),
			),
			array(
				'id'   => 'out-of-stock',
				'text' => esc_html__( 'Out of stock', 'woodmart' ),
			),
			array(
				'id'   => 'hot',
				'text' => esc_html__( 'Hot', 'woodmart' ),
			),
			array(
				'id'   => 'new',
				'text' => esc_html__( 'New', 'woodmart' ),
			),
		);

		if ( 'product_labels' === $query_type ) {
			$extra_labels[] = array(
				'id'   => 'attrs',
				'text' => esc_html__( 'Product attributes', 'woodmart' ),
			);
		}

		if ( $search_string ) {
			$extra_labels = array_filter(
				$extra_labels,
				function ( $item ) use ( $search_string ) {
					return false !== stripos( $item['text'], $search_string );
				}
			);
		}

		$results = array_merge( $results, $extra_labels );

		foreach ( $results as $key => $label ) {
			if ( isset( $label['id'], $label['text'] ) ) {
				$text = ' (' . esc_html__( 'Custom', 'woodmart' ) . ')';

				if ( in_array( $label['id'], array( 'sale', 'out-of-stock', 'hot', 'new', 'attrs' ), true ) ) {
					$text = ' (' . esc_html__( 'Default', 'woodmart' ) . ')';
				}

				$results[ $key ]['text'] = $label['text'] . $text;
			}
		}

		return $results;
	}

	/**
	 * Add default labels to Elementor autocomplete.
	 *
	 * @param array  $results Results.
	 * @param string $ids IDs.
	 * @param string $post_type Post type.
	 * @param string $query_type Query type.
	 * @return array
	 */
	public function add_labels_to_autocomplete_title( $results, $ids, $post_type, $query_type ) {
		if ( 'wd_custom_label' !== $post_type ) {
			return $results;
		}

		$extra_labels = array(
			'sale'         => esc_html__( 'Sale', 'woodmart' ),
			'out-of-stock' => esc_html__( 'Out of stock', 'woodmart' ),
			'hot'          => esc_html__( 'Hot', 'woodmart' ),
			'new'          => esc_html__( 'New', 'woodmart' ),
		);

		if ( 'product_labels' === $query_type ) {
			$extra_labels['attrs'] = esc_html__( 'Product attributes', 'woodmart' );
		}

		foreach ( (array) $ids as $id ) {
			if ( isset( $extra_labels[ $id ] ) ) {
				$results[ $id ] = $extra_labels[ $id ];
			}
		}

		return $results;
	}
}

Manager::get_instance();
