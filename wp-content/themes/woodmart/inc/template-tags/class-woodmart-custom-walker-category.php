<?php
/**
 * Custom Walker Category class.
 *
 * @package woodmart
 */

/**
 * Custom Walker Category class.
 */
class WOODMART_Custom_Walker_Category extends Walker_Category {
	/**
	 * Starts the element output.
	 *
	 * @see Walker::start_el()
	 *
	 * @param string  $output   Used to append additional content (passed by reference).
	 * @param WP_Term $category Category data object.
	 * @param int     $depth    Depth of category in reference to parents. Default 0.
	 * @param array   $args     An array of arguments. @see wp_list_categories().
	 * @param int     $id       ID of the current category.
	 */
	public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
		/** This filter is documented in wp-includes/category-template.php */
		$cat_name = apply_filters(
			'list_cats',
			esc_attr( $category->name ),
			$category
		);

		// Don't generate an element if the category name is empty.
		if ( ! $cat_name ) {
			return;
		}

		$link = get_term_link( $category );

		if ( ! empty( $args['active_filter_url'] ) ) {
			if ( ! empty( $args['current_category'] ) && $category->term_id === $args['current_category'] ) {
				$link = get_permalink( wc_get_page_id( 'shop' ) );
			}

			$parsed_url = wp_parse_url( $args['active_filter_url'] );

			if ( ! empty( $parsed_url['query'] ) ) {
				wp_parse_str( $parsed_url['query'], $query_args );

				$link = add_query_arg( $query_args, $link );
			}
		}

		$link = '<a class="pf-value" href="' . esc_url( $link ) . '" data-val="' . esc_attr( $category->slug ) . '" data-title="' . esc_attr( $category->name ) . '" ';
		if ( $args['use_desc_for_title'] && ! empty( $category->description ) ) {
			/**
			 * Filters the category description for display.
			 *
			 * @since 1.2.0
			 *
			 * @param string $description Category description.
			 * @param object $category    Category object.
			 */
			$link .= 'title="' . esc_attr( wp_strip_all_tags( apply_filters( 'category_description', $category->description, $category ) ) ) . '"';
		}

		$link .= '>';
		$link .= $cat_name . '</a>';

		if ( ! empty( $args['feed_image'] ) || ! empty( $args['feed'] ) ) {
			$link .= ' ';

			if ( empty( $args['feed_image'] ) ) {
				$link .= '(';
			}

			$link .= '<a href="' . esc_url( get_term_feed_link( $category->term_id, $category->taxonomy, $args['feed_type'] ) ) . '"';

			if ( empty( $args['feed'] ) ) {
				// translators: %s: Category name.
				$alt = ' alt="' . sprintf( esc_html__( 'Feed for all posts filed under %s', 'woodmart' ), $cat_name ) . '"';
			} else {
				$alt   = ' alt="' . $args['feed'] . '"';
				$name  = $args['feed'];
				$link .= empty( $args['title'] ) ? '' : $args['title'];
			}

			$link .= '>';

			if ( empty( $args['feed_image'] ) ) {
				$link .= $name;
			} else {
				$link .= "<img src='" . $args['feed_image'] . "'$alt" . ' />';
			}
			$link .= '</a>';

			if ( empty( $args['feed_image'] ) ) {
				$link .= ')';
			}
		}

		if ( ! empty( $args['show_count'] ) ) {
			$link .= ' (' . number_format_i18n( $category->count ) . ')';
		}
		if ( 'list' === $args['style'] ) {
			$output     .= "\t<li";
			$css_classes = array(
				'cat-item',
				'cat-item-' . $category->term_id,
			);

			if ( ! empty( $args['current_category'] ) ) {
				// 'current_category' can be an array, so we use `get_terms()`.
				$_current_terms = get_terms(
					array(
						'taxonomy'   => $category->taxonomy,
						'include'    => $args['current_category'],
						'hide_empty' => false,
					)
				);

				foreach ( $_current_terms as $_current_term ) {
					if ( $category->term_id === $_current_term->term_id ) {
						$css_classes[] = 'current-cat wd-active';
					} elseif ( $category->term_id === $_current_term->parent ) {
						$css_classes[] = 'current-cat-parent wd-current-active-parent';
					}
					while ( $_current_term->parent ) {
						if ( $category->term_id === $_current_term->parent ) {
							$css_classes[] = 'current-cat-ancestor wd-current-active-ancestor';
							break;
						}
						$_current_term = get_term( $_current_term->parent, $category->taxonomy );
					}
				}
			}

			/**
			 * Filters the list of CSS classes to include with each category in the list.
			 *
			 * @since 4.2.0
			 *
			 * @see wp_list_categories()
			 *
			 * @param array  $css_classes An array of CSS classes to be applied to each list item.
			 * @param object $category    Category data object.
			 * @param int    $depth       Depth of page, used for padding.
			 * @param array  $args        An array of wp_list_categories() arguments.
			 */
			$css_classes = implode( ' ', apply_filters( 'category_css_class', $css_classes, $category, $depth, $args ) );

			$output .= ' class="' . $css_classes . '"';
			$output .= ">$link\n";
		} elseif ( isset( $args['separator'] ) ) {
			$output .= "\t$link" . $args['separator'] . "\n";
		} else {
			$output .= "\t$link<br />\n";
		}
	}

	/**
	 * Displays element.
	 *
	 * @see Walker::display_element()
	 *
	 * @param object $element          Data object.
	 * @param array  $children_elements List of elements to continue traversing.
	 * @param int    $max_depth        Max depth to traverse.
	 * @param int    $depth            Depth of current element.
	 * @param array  $args             An array of arguments.
	 * @param string $output           Used to append additional content (passed by reference).
	 */
	public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
		if ( ! $element || ( 0 === $element->count && ! empty( $args[0]['hide_empty'] ) ) ) {
			return;
		}
		parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}
}
