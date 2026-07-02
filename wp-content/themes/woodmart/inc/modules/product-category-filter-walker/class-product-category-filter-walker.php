<?php

namespace XTS\Modules;

use WC_Query;
use WP_Meta_Query;
use WP_Tax_Query;
use WC_Product_Cat_List_Walker;
use Automattic\WooCommerce\Internal\ProductAttributesLookup\Filterer;

if ( ! class_exists( 'WC_Product_Cat_List_Walker' ) ) {
	require_once WC()->plugin_path() . '/includes/walkers/class-wc-product-cat-list-walker.php';
}

class Product_Category_Filter_Walker extends WC_Product_Cat_List_Walker {
	/**
	 * List of current categories ids.
	 *
	 * @var array List of current categories ids.
	 */
	public $current_categories = array();

	/**
	 * List of current category ancestors ids.
	 *
	 * @var array List of current category ancestors ids.
	 */
	public $current_category_ancestors = array();

	/**
	 * List of filtered product counts.
	 *
	 * @var array List of filtered product counts.
	 */
	public $filtered_counts = array();

	/**
	 * Constructor.
	 *
	 * @param array $current_categories List of current categories ids.
	 */
	public function __construct( $current_categories = array() ) {
		$this->current_categories = $current_categories;

		foreach ( $this->current_categories as $current_category ) {
			$this->current_category_ancestors = array_merge(
				$this->current_category_ancestors,
				get_ancestors( $current_category, 'product_cat' )
			);
		}

		$this->current_category_ancestors = array_unique( $this->current_category_ancestors );
	}

	/**
	 * Display array of elements hierarchically.
	 *
	 * @param array $elements  An array of elements.
	 * @param int   $max_depth The maximum hierarchical depth.
	 * @param mixed ...$args Additional arguments.
	 *
	 * @return string The hierarchical item output.
	 */
	public function walk( $elements, $max_depth, ...$args ) {
		$term_ids = wp_list_pluck( $elements, 'term_id' );

		if ( ! empty( $term_ids ) ) {
			$all_counts            = $this->get_filtered_product_cat_counts( $term_ids, $args[0]['query_type'] );
			$this->filtered_counts = $all_counts;
		}

		return parent::walk( $elements, $max_depth, ...$args );
	}

	/**
	 * Start the element output.
	 *
	 * @param string  $output            Passed by reference. Used to append additional content.
	 * @param object  $category          Category.
	 * @param int     $depth             Depth of category in reference to parents.
	 * @param array   $args              Arguments.
	 * @param integer $current_object_id Current object ID.
	 */
	public function start_el( &$output, $category, $depth = 0, $args = array(), $current_object_id = 0 ) {
		switch ( $args['view_type'] ) {
			case 'list':
				$this->start_el_list( $output, $category, $depth, $args, $current_object_id );
				break;
			case 'dropdown':
				$this->start_el_dropdown( $output, $category, $depth, $args, $current_object_id );
				break;
		}
	}

	/**
	 * Start the element output.
	 *
	 * @param string  $output            Passed by reference. Used to append additional content.
	 * @param object  $category          Category.
	 * @param int     $depth             Depth of category in reference to parents.
	 * @param array   $args              Arguments.
	 * @param integer $current_object_id Current object ID.
	 */
	public function start_el_list( &$output, $category, $depth = 0, $args = array(), $current_object_id = 0 ) {
		$class_names = array();

		$cat_id = intval( $category->term_id );

		$class_names[] = 'cat-item cat-item-' . $cat_id;

		if ( in_array( $cat_id, $this->current_categories, true ) ) {
			$class_names[] = 'wd-active';
		}

		if ( $args['has_children'] && $args['hierarchical'] && ( empty( $args['max_depth'] ) || $args['max_depth'] > $depth + 1 ) ) {
			$class_names[] = ' wd-active-parent';
		}

		if ( $this->current_category_ancestors && $this->current_categories && in_array( $cat_id, $this->current_category_ancestors, true ) ) {
			$class_names[] = 'wd-current-active-parent';
		}

		$output .= '<li class="' . implode( ' ', $class_names ) . '">';
		$output .= '<a href="' . $this->get_filter_url( $category, $args['query_type'] ) . '" class="wd-filter-lable">' . apply_filters( 'list_product_cats', $category->name, $category ) . '</a>';

		if ( $args['show_count'] ) {
			$cat_count = array_key_exists( $cat_id, $this->filtered_counts ) ? $this->filtered_counts[ $cat_id ] : 0;
			$output   .= ' <span class="count">' . $cat_count . '</span>';
		}
	}

	/**
	 * Start the element output.
	 *
	 * @param string  $output            Passed by reference. Used to append additional content.
	 * @param object  $category          Category.
	 * @param int     $depth             Depth of category in reference to parents.
	 * @param array   $args              Arguments.
	 * @param integer $current_object_id Current object ID.
	 */
	public function start_el_dropdown( &$output, $category, $depth = 0, $args = array(), $current_object_id = 0 ) {
		if ( ! empty( $args['hierarchical'] ) ) {
			$pad = str_repeat( '&nbsp;', $depth * 3 );
		} else {
			$pad = '';
		}

		$cat_id   = intval( $category->term_id );
		$cat_name = apply_filters( 'list_product_cats', $category->name, $category );
		$output  .= "\t<option class=\"level-$depth\" value=\"" . esc_attr( $category->slug ) . '"';

		if ( in_array( $cat_id, $this->current_categories, true ) ) {
			$output .= ' selected="selected"';
		}

		$output .= '>';
		$output .= esc_html( $pad . $cat_name );

		if ( ! empty( $args['show_count'] ) ) {
			$cat_count = array_key_exists( $cat_id, $this->filtered_counts ) ? $this->filtered_counts[ $cat_id ] : 0;
			$output   .= '&nbsp;(' . absint( $cat_count ) . ')';
		}

		$output .= "</option>\n";
	}

	/**
	 * Get filter URL for a category.
	 *
	 * @param object $category Category.
	 * @param string $query_type Query Type.
	 * @return string
	 */
	public function get_filter_url( $category, $query_type = 'or' ) {
		$base_link         = woodmart_filters_get_page_base_url();
		$category_slug     = $category->slug;
		$chosen_categories = array_map(
			function ( $chosen_category_id ) {
				$chosen_category = get_term_by( 'term_id', $chosen_category_id, 'product_cat' );

				if ( $chosen_category ) {
					return $chosen_category->slug;
				}
			},
			$this->current_categories
		);

		if ( is_product_category() && ! isset( $_GET['filter_category'] ) ) { // phpcs:ignore.
			global $wp_query;

			if ( isset( $wp_query->queried_object_id ) ) {
				$current_cat_obj = $wp_query->queried_object;
				$key             = array_search( $current_cat_obj->slug, $chosen_categories, true );

				if ( false !== $key ) {
					unset( $chosen_categories[ $key ] );
				}
			}
		} elseif ( ! empty( $category->parent ) && in_array( $category->parent, $this->current_categories, true ) ) {
			$parent_category = get_term_by( 'term_id', $category->parent, 'product_cat' );

			if ( $parent_category ) {
				$key = array_search( $parent_category->slug, $chosen_categories, true );

				if ( false !== $key ) {
					unset( $chosen_categories[ $key ] );
				}
			}
		} elseif ( empty( $category->parent ) ) {
			$child_categories = get_terms(
				array(
					'taxonomy' => 'product_cat',
					'parent'   => $category->term_id,
					'fields'   => 'ids',
				)
			);

			if ( ! is_wp_error( $child_categories ) && ! empty( $child_categories ) ) {
				foreach ( $child_categories as $child_category_id ) {
					$child_category = get_term_by( 'term_id', $child_category_id, 'product_cat' );

					if ( $child_category ) {
						$key = array_search( $child_category->slug, $chosen_categories, true );

						if ( false !== $key ) {
							unset( $chosen_categories[ $key ] );
						}
					}
				}
			}
		}

		// Remove all child categories when clicking on parent category.
		$all_child_categories = get_terms(
			array(
				'taxonomy' => 'product_cat',
				'child_of' => $category->term_id,
				'fields'   => 'ids',
			)
		);

		if ( ! is_wp_error( $all_child_categories ) && ! empty( $all_child_categories ) ) {
			foreach ( $all_child_categories as $child_category_id ) {
				$child_category = get_term_by( 'term_id', $child_category_id, 'product_cat' );

				if ( $child_category ) {
					$key = array_search( $child_category->slug, $chosen_categories, true );

					if ( false !== $key ) {
						unset( $chosen_categories[ $key ] );
					}
				}
			}
		}

		if ( in_array( $category_slug, $chosen_categories, true ) ) {
			$key = array_search( $category_slug, $chosen_categories, true );

			if ( false !== $key ) {
				unset( $chosen_categories[ $key ] );
			}
		} else {
			$chosen_categories[] = $category->slug;
		}

		if ( ! empty( $chosen_categories ) ) {
			$link = add_query_arg( 'filter_category', implode( ',', $chosen_categories ), $base_link );

			if ( ! empty( $query_type ) && 'and' === $query_type ) { // phpcs:ignore.
				$link = add_query_arg( 'query_type_category', sanitize_text_field( wp_unslash( $query_type ) ), $link ); // phpcs:ignore.
			}
		} else {
			$link = remove_query_arg( array( 'filter_category', 'query_type_category' ), $base_link );
		}

		$link = str_replace( '%2C', ',', $link );

		return $link;
	}

	/**
	 * Count products within certain terms, taking the main WP query into consideration.
	 *
	 * This query allows counts to be generated based on the viewed products, not all products.
	 *
	 * @param  array  $term_ids Term IDs.
	 * @param  string $query_type Query Type.
	 * @return array
	 */
	public function get_filtered_product_cat_counts( $term_ids, $query_type ) {
		global $wpdb;

		$taxonomy = 'product_cat';

		$tax_query  = WC_Query::get_main_tax_query();
		$meta_query = WC_Query::get_main_meta_query();

		if ( 'or' === $query_type && ! is_product_category() ) {
			foreach ( $tax_query as $key => $query ) {
				if ( is_array( $query ) && $taxonomy === $query['taxonomy'] ) {
					unset( $tax_query[ $key ] );
				}
			}
		}

		$tax_query  = new WP_Tax_Query( $tax_query );
		$meta_query = new WP_Meta_Query( $meta_query );

		// Generate query.
		$query = $this->get_product_counts_query( $tax_query, $meta_query, $term_ids );
		$query = apply_filters( 'woocommerce_get_filtered_term_product_counts_query', $query );
		$query = implode( ' ', $query );

		// We have a query - let's see if cached results of this query already exist.
		$query_hash = md5( $query );

		// Maybe store a transient of the count values.
		$cache = apply_filters( 'woocommerce_layered_nav_count_maybe_cache', true );

		if ( true === $cache ) {
			$cached_counts = (array) get_transient( 'wc_layered_nav_counts_' . sanitize_title( $taxonomy ) );
		} else {
			$cached_counts = array();
		}

		if ( ! isset( $cached_counts[ $query_hash ] ) ) {
			$results                      = $wpdb->get_results( $query, ARRAY_A ); // @codingStandardsIgnoreLine
			$counts                       = array_map( 'absint', wp_list_pluck( $results, 'term_count', 'term_count_id' ) );
			$cached_counts[ $query_hash ] = $counts;
			if ( true === $cache ) {
				set_transient( 'wc_layered_nav_counts_' . sanitize_title( $taxonomy ), $cached_counts, DAY_IN_SECONDS );
			}
		}

		return array_map( 'absint', (array) $cached_counts[ $query_hash ] );
	}

	/**
	 * Get the query for counting products by terms NOT using the product attributes lookup table.
	 *
	 * @param \WP_Tax_Query  $tax_query The current main tax query.
	 * @param \WP_Meta_Query $meta_query The current main meta query.
	 * @param string         $term_ids The term ids to include in the search.
	 * @return array An array of SQL query parts.
	 */
	private function get_product_counts_query( $tax_query, $meta_query, $term_ids ) {
		global $wpdb;

		$meta_query_sql = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
		$tax_query_sql  = $tax_query->get_sql( $wpdb->posts, 'ID' );

		// Generate query.
		$query           = array();
		$query['select'] = "SELECT COUNT( DISTINCT {$wpdb->posts}.ID ) AS term_count, terms.term_id AS term_count_id";
		$query['from']   = "FROM {$wpdb->posts}";
		$query['join']   = "
			INNER JOIN {$wpdb->term_relationships} AS term_relationships ON {$wpdb->posts}.ID = term_relationships.object_id
			INNER JOIN {$wpdb->term_taxonomy} AS term_taxonomy USING( term_taxonomy_id )
			INNER JOIN {$wpdb->terms} AS terms USING( term_id )
			" . $tax_query_sql['join'] . $meta_query_sql['join'];

		$term_ids_sql   = $this->get_term_ids_sql( $term_ids );
		$query['where'] = "
			WHERE {$wpdb->posts}.post_type IN ( 'product' )
			AND {$wpdb->posts}.post_status = 'publish'
			{$tax_query_sql['where']} {$meta_query_sql['where']}
			AND terms.term_id IN $term_ids_sql";

		$search_query_sql = \WC_Query::get_main_search_query_sql();
		if ( $search_query_sql ) {
			$query['where'] .= ' AND ' . $search_query_sql;
		}

		$query['group_by'] = 'GROUP BY terms.term_id';

		return $query;
	}

	/**
	 * Formats a list of term ids as "(id,id,id)".
	 *
	 * @param array $term_ids The list of terms to format.
	 * @return string The formatted list.
	 */
	private function get_term_ids_sql( $term_ids ) {
		return '(' . implode( ',', array_map( 'absint', $term_ids ) ) . ')';
	}
}
