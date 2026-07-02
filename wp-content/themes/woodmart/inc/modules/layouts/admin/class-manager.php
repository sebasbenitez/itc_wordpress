<?php
/**
 * Manager class file.
 *
 * @package woodmart
 */

namespace XTS\Modules\Layouts;

use WP_Post;
use XTS\Singleton;

/**
 * Manager class.
 */
class Manager extends Singleton {
	/**
	 * Conditions cache.
	 *
	 * @var Conditions_Cache
	 */
	private $cache;
	/**
	 * Post type.
	 *
	 * @var string
	 */
	private $post_type = 'woodmart_layout';
	/**
	 * Type meta key.
	 *
	 * @var string
	 */
	private $type_meta_key = 'wd_layout_type';
	/**
	 * Conditions meta key.
	 *
	 * @var string
	 */
	private $conditions_meta_key = 'wd_layout_conditions';

	/**
	 * Constructor.
	 */
	public function init() {
		$this->cache = new Conditions_Cache();

		$this->add_actions();
	}

	/**
	 * Add actions.
	 */
	public function add_actions() {
		add_action( 'wp_ajax_wd_layout_create', array( $this, 'create_layout' ) );
		add_action( 'wp_ajax_wd_layout_edit', array( $this, 'edit_layout' ) );
		add_action( 'wp_ajax_wd_layout_conditions_query', array( $this, 'conditions_query' ) );
		add_action( 'wp_ajax_wd_layout_change_status', array( $this, 'change_status_action' ) );
		add_action( 'wp_trash_post', array( $this, 'remove_post_from_cache' ) );
		add_action( 'untrashed_post', array( $this, 'on_untrash_post' ) );
		add_action( 'save_post_' . $this->post_type, array( $this, 'save_post' ) );
		add_action( 'woodmart_after_import', array( $this, 'save_post' ) );
	}

	/**
	 * Save template post.
	 *
	 * @param int $post_id Post id.
	 */
	public function save_post( $post_id ) {
		$this->cache->regenerate();

		if ( 'shop_archive' === get_post_meta( $post_id, $this->type_meta_key, true ) ) {
			delete_transient( 'woodmart_products_per_page_variations' );
		}
	}

	/**
	 * On post untrashed.
	 *
	 * @param int $post_id Post id.
	 */
	public function on_untrash_post( $post_id ) {
		$conditions = get_post_meta( $post_id, $this->conditions_meta_key, true );
		$type       = get_post_meta( $post_id, $this->type_meta_key, true );

		$this->cache->add( $type, $conditions, $post_id )->save();
	}

	/**
	 * Remove post from cache.
	 *
	 * @param int $post_id Post id.
	 */
	public function remove_post_from_cache( $post_id ) {
		$this->cache->remove( $post_id )->save();
	}

	/**
	 * Change status action.
	 */
	public function change_status_action() {
		check_ajax_referer( 'wd-new-template-nonce', 'security' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'You do not have permission to perform this action.', 'woodmart' ),
				),
				403
			);
		}

		$post_id = woodmart_clean( $_POST['id'] ); // phpcs:ignore
		$status  = woodmart_clean( $_POST['status'] ); // phpcs:ignore

		wp_update_post(
			array(
				'ID'          => $post_id,
				'post_status' => $status,
			)
		);

		wp_send_json(
			array(
				'new_html' => Admin::get_instance()->get_status_button_template( $post_id ),
			)
		);
	}

	/**
	 * Edit layout.
	 */
	public function edit_layout() {
		check_ajax_referer( 'wd-new-template-nonce', 'security' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'You do not have permission to perform this action.', 'woodmart' ),
				),
				403
			);
		}

		$post_id = woodmart_clean( $_POST['id'] ); // phpcs:ignore
		$data    = isset( $_POST['data'] ) ? woodmart_clean( $_POST['data'] ) : array(); // phpcs:ignore

		update_post_meta( $post_id, $this->conditions_meta_key, $data );

		$this->cache->regenerate();

		wp_send_json(
			array(
				'new_html' => Admin::get_instance()->get_edit_conditions_template( $post_id ),
			)
		);
	}

	/**
	 * Create new layout.
	 */
	public function create_layout() {
		check_ajax_referer( 'wd-new-template-nonce', 'security' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'You do not have permission to perform this action.', 'woodmart' ),
				),
				403
			);
		}

		$type            = woodmart_clean( $_POST['type'] ); // phpcs:ignore
		$title           = woodmart_clean( $_POST['name'] ); // phpcs:ignore
		$data            = isset( $_POST['data'] ) ? woodmart_clean( $_POST['data'] ) : array(); // phpcs:ignore
		$predefined_name = isset( $_POST['predefined_name'] ) ? woodmart_clean( $_POST['predefined_name'] ) : ''; // phpcs:ignore

		$post_args = array(
			'post_title'  => $title,
			'post_type'   => $this->post_type,
			'post_status' => str_contains( $type, 'loop_item' ) ? 'publish' : 'draft',
			'post_date'   => current_time( 'Y-m-d H:i:s' ),
			'meta_input'  => array(
				$this->type_meta_key       => $type,
				$this->conditions_meta_key => $data,
			),
		);

		if ( in_array( $type, array( 'single_product', 'product_loop_item' ), true ) ) {
			$published_products = wc_get_products(
				array(
					'status' => 'publish',
					'limit'  => 1,
				)
			);

			if ( empty( $published_products ) ) {
				$create_product_link = add_query_arg(
					array(
						'post_type' => 'product',
					),
					admin_url( 'post-new.php' )
				);

				wp_send_json_error(
					array(
						'message' => sprintf(
							'%s <a href="' . $create_product_link . '">%s</a>',
							esc_html__( 'In order to create this layout, you must first publish at least one product!', 'woodmart' ),
							esc_html__( 'Add new product', 'woodmart' )
						),
					)
				);
			}
		}

		if ( 'single_post' === $type ) {
			$published_posts = get_posts(
				array(
					'post_type'   => 'post',
					'post_status' => 'publish',
					'numberposts' => 1,
				)
			);

			if ( empty( $published_posts ) ) {
				$create_post_link = add_query_arg(
					array(
						'post_type' => 'post',
					),
					admin_url( 'post-new.php' )
				);

				wp_send_json_error(
					array(
						'message' => sprintf(
							'%s <a href="' . $create_post_link . '">%s</a>',
							esc_html__( 'In order to create this layout, you must first publish at least one post!', 'woodmart' ),
							esc_html__( 'Add new post', 'woodmart' )
						),
					)
				);
			}
		}

		if ( 'single_portfolio' === $type ) {
			$published_projects = get_posts(
				array(
					'post_type'   => 'portfolio',
					'post_status' => 'publish',
					'numberposts' => 1,
				)
			);

			if ( empty( $published_projects ) ) {
				$create_project_link = add_query_arg(
					array(
						'post_type' => 'portfolio',
					),
					admin_url( 'post-new.php' )
				);

				wp_send_json_error(
					array(
						'message' => sprintf(
							'%s <a href="' . $create_project_link . '">%s</a>',
							esc_html__( 'In order to create a Single portfolio layout, you must first publish at least one project!', 'woodmart' ),
							esc_html__( 'Add new project', 'woodmart' )
						),
					)
				);
			}
		}

		if ( $predefined_name ) {
			$imported_post_id = Import::import_xml( $type, $predefined_name );

			if ( ! $imported_post_id ) {
				wp_send_json_error(
					array(
						'message' => esc_html__( 'Failed to import the predefined layout. Please try again.', 'woodmart' ),
					)
				);
			}

			if ( str_contains( $type, 'loop_item' ) ) {
				update_post_meta( $imported_post_id, 'wd_force_block_id', true );
			}

			$post_args['ID'] = $imported_post_id;
			$post_id         = wp_update_post( $post_args );
		} else {
			$post_id = wp_insert_post( $post_args );
		}

		$action = 'external' === woodmart_get_opt( 'current_builder', 'external' ) && 'elementor' === woodmart_get_current_page_builder() && ! str_contains( $type, 'loop_item' ) ? 'elementor' : 'edit';

		$url = add_query_arg(
			array(
				'post'           => $post_id,
				'action'         => $action,
				'classic-editor' => '',
			),
			admin_url( 'post.php' )
		);

		$this->cache->regenerate();

		wp_send_json(
			array(
				'redirect_url' => $url,
			)
		);
	}

	/**
	 * Conditions query.
	 */
	public function conditions_query() {
		check_ajax_referer( 'wd-new-template-nonce', 'security' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'You do not have permission to perform this action.', 'woodmart' ),
				),
				403
			);
		}

		$query_type = woodmart_clean( $_POST['query_type'] ); // phpcs:ignore
		$search     = isset( $_POST['search'] ) ? woodmart_clean( $_POST['search'] ) : false; // phpcs:ignore

		$items = array();

		switch ( $query_type ) {
			// Single product.
			case 'product_cat':
			case 'product_cat_children':
			case 'product_tag':
			case 'product_term':
			case 'product_attr_term':
			case 'product_brand':
			case 'filtered_product_by_term':
			case 'product_shipping_class':
				$taxonomy = array();

				if ( 'product_cat' === $query_type || 'product_cat_children' === $query_type || 'product_term' === $query_type ) {
					$taxonomy[] = 'product_cat';
				}
				if ( 'product_tag' === $query_type || 'product_term' === $query_type ) {
					$taxonomy[] = 'product_tag';
				}
				if ( ( 'product_brand' === $query_type || 'product_term' === $query_type ) && taxonomy_exists( 'product_brand' ) ) {
					$taxonomy[] = 'product_brand';
				}
				if ( 'product_attr_term' === $query_type || 'product_term' === $query_type || 'filtered_product_by_term' === $query_type ) {
					$attribute_taxonomies = wc_get_attribute_taxonomies();

					if ( ! empty( $attribute_taxonomies ) ) {
						foreach ( $attribute_taxonomies as $tax ) {
							if ( taxonomy_exists( wc_attribute_taxonomy_name( $tax->attribute_name ) ) ) {
								$taxonomy[] = wc_attribute_taxonomy_name( $tax->attribute_name );
							}
						}
					}
				}
				if ( 'product_shipping_class' === $query_type ) {
					$taxonomy[] = 'product_shipping_class';
				}

				$terms = get_terms(
					array(
						'hide_empty' => false,
						'fields'     => 'all',
						'taxonomy'   => $taxonomy,
						'search'     => $search,
					)
				);

				if ( count( $terms ) > 0 ) {
					foreach ( $terms as $term ) {
						$items[] = array(
							'id'   => $term->term_id,
							'text' => $term->name . ' (ID: ' . $term->term_id . ') (Tax: ' . $term->taxonomy . ')',
						);
					}
				}
				break;
			case 'product_attr':
			case 'filtered_product_term':
				foreach ( wc_get_attribute_taxonomies() as $attribute ) {
					$items[] = array(
						'id'   => 'pa_' . $attribute->attribute_name,
						'text' => $attribute->attribute_label . ' (Tax: pa_' . $attribute->attribute_name . ')',
					);
				}
				break;
			case 'filtered_product_stock_status':
				$items = array(
					array(
						'id'   => 'instock',
						'text' => esc_html__( 'In Stock', 'woodmart' ),
					),
					array(
						'id'   => 'onsale',
						'text' => esc_html__( 'On Sale', 'woodmart' ),
					),
					array(
						'id'   => 'onbackorder',
						'text' => esc_html__( 'On backorder', 'woodmart' ),
					),
				);
				break;
			case 'product_type':
				foreach ( wc_get_product_types() as $type => $title ) {
					$items[] = array(
						'id'   => $type,
						'text' => $title,
					);
				}
				break;
			case 'product':
			case 'products':
				$post_type = 'product';

				if ( 'products' === $query_type ) {
					$post_type = array( 'product', 'product_variation' );
				}

				$posts = get_posts(
					array(
						's'              => $search,
						'post_type'      => $post_type,
						'posts_per_page' => 100,
					)
				);

				if ( count( $posts ) > 0 ) {
					foreach ( $posts as $post ) {
						$items[] = array(
							'id'   => $post->ID,
							'text' => $post->post_title . ' (ID: ' . $post->ID . ')',
						);
					}
				}
				break;
			// Single post.
			case 'post_cat':
			case 'post_tag':
				$taxonomy = array();

				if ( 'post_cat' === $query_type ) {
					$taxonomy[] = 'category';
				}
				if ( 'post_tag' === $query_type ) {
					$taxonomy[] = 'post_tag';
				}

				$terms = get_terms(
					array(
						'hide_empty' => false,
						'fields'     => 'all',
						'taxonomy'   => $taxonomy,
						'search'     => $search,
					)
				);

				if ( count( $terms ) > 0 ) {
					foreach ( $terms as $term ) {
							$items[] = array(
								'id'   => $term->term_id,
								'text' => $term->name . ' (ID: ' . $term->term_id . ') (Tax: ' . $term->taxonomy . ')',
							);
					}
				}
				break;
			case 'post_id':
				$posts = get_posts(
					array(
						's'              => $search,
						'post_type'      => 'post',
						'posts_per_page' => 100,
					)
				);

				if ( count( $posts ) > 0 ) {
					foreach ( $posts as $post ) {
						$items[] = array(
							'id'   => $post->ID,
							'text' => $post->post_title . ' (ID: ' . $post->ID . ')',
						);
					}
				}
				break;
			case 'post_format':
				$post_formats = get_post_format_strings();

				foreach ( $post_formats as $format => $label ) {
					$items[] = array(
						'id'   => $format,
						'text' => $label,
					);
				}
				break;
			// Single portfolio.
			case 'project_cat':
				$taxonomy = array();

				if ( 'project_cat' === $query_type ) {
					$taxonomy[] = 'project-cat';
				}

				$terms = get_terms(
					array(
						'post-type'  => 'portfolio',
						'hide_empty' => false,
						'fields'     => 'all',
						'taxonomy'   => $taxonomy,
						'search'     => $search,
					)
				);

				if ( count( $terms ) > 0 ) {
					foreach ( $terms as $term ) {
							$items[] = array(
								'id'   => $term->term_id,
								'text' => $term->name . ' (ID: ' . $term->term_id . ') (Tax: ' . $term->taxonomy . ')',
							);
					}
				}
				break;
			case 'project_id':
				$posts = get_posts(
					array(
						's'              => $search,
						'post_type'      => 'portfolio',
						'posts_per_page' => 100,
					)
				);

				if ( count( $posts ) > 0 ) {
					foreach ( $posts as $post ) {
						$items[] = array(
							'id'   => $post->ID,
							'text' => $post->post_title . ' (ID: ' . $post->ID . ')',
						);
					}
				}
				break;

			// Blog.
			case 'blog_category':
			case 'blog_tag':
				$taxonomy = ( 'blog_category' === $query_type ) ? 'category' : 'post_tag';

				$terms = get_terms(
					array(
						'hide_empty' => false,
						'fields'     => 'all',
						'taxonomy'   => $taxonomy,
						'search'     => $search,
					)
				);

				if ( ! empty( $terms ) ) {
					foreach ( $terms as $term ) {
							$items[] = array(
								'id'   => $term->term_id,
								'text' => $term->name . ' (ID: ' . $term->term_id . ')',
							);
					}
				}
				break;

			// Portfolio.
			case 'portfolio_category':
				$terms = get_terms(
					array(
						'hide_empty' => false,
						'fields'     => 'all',
						'taxonomy'   => 'project-cat',
						'search'     => $search,
					)
				);

				if ( ! empty( $terms ) ) {
					foreach ( $terms as $term ) {
							$items[] = array(
								'id'   => $term->term_id,
								'text' => $term->name . ' (ID: ' . $term->term_id . ')',
							);
					}
				}
				break;
			// Thank you page.
			case 'order_payment_gateway':
				if ( woodmart_woocommerce_installed() ) {
					foreach ( WC()->payment_gateways()->payment_gateways() as $gateway ) {
						if ( 'yes' === $gateway->enabled ) {
							$name = $gateway->get_title();

							if ( $search && false === stripos( $name, $search ) ) {
								continue;
							}

							$items[] = array(
								'id'   => $gateway->id,
								'text' => $gateway->get_title(),
							);
						}
					}
				}

				break;
			case 'order_shipping_method':
				if ( woodmart_woocommerce_installed() ) {
					foreach ( WC()->shipping()->get_shipping_methods() as $method_id => $method ) {
						$name = $method->get_method_title();

						if ( $search && false === stripos( $name, $search ) ) {
							continue;
						}

						$items[] = array(
							'id'   => $method_id,
							'text' => $name,
						);
					}
				}

				break;
			case 'order_shipping_country':
			case 'order_billing_country':
				if ( woodmart_woocommerce_installed() ) {
					$countries = WC()->countries->get_allowed_countries();

					foreach ( $countries as $code => $name ) {
						if ( $search && false === stripos( $name, $search ) ) {
							continue;
						}

						$items[] = array(
							'id'   => $code,
							'text' => $name,
						);
					}
				}

				break;
		}

		wp_send_json(
			array(
				'results' => $items,
			)
		);
	}
}

Manager::get_instance();
