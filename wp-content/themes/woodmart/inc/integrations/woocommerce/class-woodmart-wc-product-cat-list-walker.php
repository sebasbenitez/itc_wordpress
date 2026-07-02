<?php
/**
 * WooCommerce Product Category List Walker Class.
 *
 * @package woodmart
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_Product_Cat_List_Walker' ) && function_exists( 'WC' ) ) {
	include_once WC()->plugin_path() . '/includes/walkers/class-product-cat-list-walker.php';
}

if ( ! class_exists( 'WOODMART_WC_Product_Cat_List_Walker' ) && function_exists( 'WC' ) ) {
	/**
	 * WOODMART_WC_Product_Cat_List_Walker class.
	 */
	class WOODMART_WC_Product_Cat_List_Walker extends WC_Product_Cat_List_Walker {
		/**
		 * Start the element output.
		 *
		 * @see Walker::start_el()
		 * @since 2.1.0
		 *
		 * @param string  $output Passed by reference. Used to append additional content.
		 * @param object  $cat    Category data object.
		 * @param int     $depth Depth of category in reference to parents.
		 * @param array   $args    An array of arguments.
		 * @param integer $current_object_id Current object ID.
		 */
		public function start_el( &$output, $cat, $depth = 0, $args = array(), $current_object_id = 0 ) {
			$output .= '<li class="cat-item cat-item-' . $cat->term_id;

			if ( (int) $args['current_category'] === (int) $cat->term_id ) {
				$output .= ' current-cat wd-active';
			}

			if ( $args['has_children'] && $args['hierarchical'] ) {
				$output .= ' cat-parent wd-active-parent';
			}

			if ( $args['current_category_ancestors'] && $args['current_category'] && in_array( (int) $cat->term_id, array_map( 'intval', $args['current_category_ancestors'] ), true ) ) {
				$output .= ' current-cat-parent wd-current-active-parent';
			}

			$output .= '"><a href="' . esc_url( get_term_link( (int) $cat->term_id, $this->tree_type ) ) . '">' . $cat->name . '</a>';

			if ( $args['show_count'] ) {
				$output .= ' <span class="count">' . $cat->count . '</span>';
			}
		}
	}
}
