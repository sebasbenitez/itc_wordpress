<?php
/**
 * Category walker class.
 *
 * @package woodmart
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WOODMART_Walker_Category' ) ) {
	/**
	 * WOODMART_Walker_Category class.
	 */
	class WOODMART_Walker_Category extends Walker_Category {
		/**
		 * Get drilldown back button HTML.
		 *
		 * @param array  $args Arguments.
		 * @param string $tag HTML tag.
		 *
		 * @return string
		 */
		public function get_drilldown_back_button( $args, $tag = 'div' ) {
			if ( 'side-hidden' !== $args['mobile_categories_layout'] || 'drilldown' !== $args['mobile_categories_menu_layout'] ) {
				return '';
			}

			ob_start();

			woodmart_enqueue_inline_style( 'nav-opener' );

			?>
			<<?php echo $tag; // phpcs:ignore. ?> class="wd-drilldown-back">
				<span class="wd-nav-opener"></span>
				<a href="#">
					<?php esc_html_e( 'Back', 'woodmart' ); ?>
				</a>
			</<?php echo $tag;  // phpcs:ignore. ?>>
			<?php
			return ob_get_clean();
		}

		/**
		 * Starts the list before the elements are added.
		 *
		 * @param string $output Used to append additional content (passed by reference).
		 * @param int    $depth  Depth of category. Used for padding.
		 * @param array  $args   An array of arguments.
		 */
		public function start_lvl( &$output, $depth = 0, $args = array() ) {
			if ( 'list' !== $args['style'] ) {
				return;
			}

			$sub_menu_class  = 'wd-sub-menu';
			$sub_menu_class .= ' wd-dropdown wd-dropdown-menu';

			$indent  = str_repeat( "\t", $depth );
			$output .= $indent . '<ul class="children wd-design-default ' . esc_attr( $sub_menu_class ) . '">';
			$output .= $this->get_drilldown_back_button( $args, 'li' );
		}

		/**
		 * Ends the list of after the elements are added.
		 *
		 * @param string $output Used to append additional content (passed by reference).
		 * @param int    $depth  Depth of category. Used for padding.
		 * @param array  $args   An array of arguments.
		 */
		public function end_lvl( &$output, $depth = 0, $args = array() ) {
			if ( 'list' !== $args['style'] ) {
				return;
			}

			$indent  = str_repeat( "\t", $depth );
			$output .= $indent . '</ul>';
		}

		/**
		 * Starts the element output.
		 *
		 * @param string $output   Used to append additional content (passed by reference).
		 * @param object $category Category data object.
		 * @param int    $depth    Depth of category in reference to parents.
		 * @param array  $args     An array of arguments.
		 * @param int    $id       ID of the current category.
		 */
		public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
			/** This filter is documented in wp-includes/category-template.php */
			$cat_name = apply_filters(
				'list_cats', // phpcs:ignore.
				esc_attr( $category->name ),
				$category
			);

			// Don't generate an element if the category name is empty.
			if ( ! $cat_name ) {
				return;
			}

			$mobile_categories_link_classes = 'side-hidden' === $args['mobile_categories_layout'] ? ' woodmart-nav-link' : '';

			$link = '<a class="category-nav-link' . esc_attr( $mobile_categories_link_classes ) . '" href="' . esc_url( get_term_link( $category ) ) . '" ';

			$link .= '>';

			$image_output = '';

			$icon_data = get_term_meta( $category->term_id, 'category_icon', true );

			if ( $icon_data && isset( $args['show_images'] ) && $args['show_images'] ) {
				if ( is_array( $icon_data ) && $icon_data['id'] ) {
					if ( woodmart_is_svg( $icon_data['url'] ) ) {
						$image_output .= woodmart_get_svg_html( $icon_data['id'], apply_filters( 'woodmart_cat_menu_icon_size_svg', '40x40' ), array( 'class' => 'wd-nav-img' ) );
					} else {
						$image_output .= wp_get_attachment_image( $icon_data['id'], apply_filters( 'woodmart_cat_menu_icon_size', 'thumbnail' ), false, array( 'class' => 'wd-nav-img' ) );
					}
				} else {
					if ( isset( $icon_data['url'] ) ) {
						$icon_data = $icon_data['url'];
					}

					if ( $icon_data ) {
						$image_output .= '<img src="' . esc_url( $icon_data ) . '" alt="' . esc_attr( $category->cat_name ) . '" class="wd-nav-img" />';
					}
				}
			}

			$link .= $image_output;

			$link .= '<span class="nav-link-summary">';
			$link .= '<span class="nav-link-text">' . $cat_name . '</span>';

			if ( ! empty( $args['show_count'] ) ) {
				$link .= '<span class="nav-link-count">' . number_format_i18n( $category->count ) . ' ' . _n( 'product', 'products', $category->count, 'woodmart' ) . '</span>';
			}

			$link .= '</span>';
			$link .= '</a>';

			if ( 'list' === $args['style'] ) {
				$default_cat = get_option( 'default_product_cat' );
				$output     .= "\t<li";
				$css_classes = array(
					'cat-item',
					'cat-item-' . $category->term_id,
					( strval( $category->term_id ) === strval( $default_cat ) && apply_filters( 'woodmart_wc_default_product_cat', false ) ? 'wc-default-cat wd-hide' : '' ),
				);

				if ( 'side-hidden' === $args['mobile_categories_layout'] ) {
					$css_classes[] = 'menu-item';
					$css_classes[] = 'item-level-' . $depth;
				}

				if ( $args['walker']->has_children ) {
					woodmart_enqueue_inline_style( 'nav-opener' );

					$css_classes[] = 'wd-event-hover menu-item-has-children';
				}

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
						if ( (int) $category->term_id === (int) $_current_term->term_id ) {
							$css_classes[] = 'wd-active';
						} elseif ( (int) $category->term_id === (int) $_current_term->parent ) {
							$css_classes[] = 'current-cat-parent wd-current-active-parent';
						}
						while ( $_current_term->parent ) {
							if ( (int) $category->term_id === (int) $_current_term->parent ) {
								$css_classes[] = 'current-cat-ancestor wd-current-active-ancestor';
								break;
							}
							$_current_term = get_term( $_current_term->parent, $category->taxonomy );
						}
					}
				}

				/**
				 * Filter the list of CSS classes to include with each category in the list.
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
				$css_classes = implode( ' ', apply_filters( 'category_css_class', $css_classes, $category, $depth, $args ) ); // phpcs:ignore.

				$output .= ' class="' . $css_classes . '"';
				$output .= ">$link\n";
			} elseif ( isset( $args['separator'] ) ) {
				$output .= "\t$link" . $args['separator'] . "\n";
			} else {
				$output .= "\t$link<br />\n";
			}
		}

		/**
		 * Display element.
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
}
