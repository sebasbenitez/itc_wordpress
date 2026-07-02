<?php
/**
 * Main layouts class.
 *
 * @package woodmart
 */

namespace XTS\Modules\Layouts;

use XTS\Modules\Layouts\Global_Data as Builder_Data;
use XTS\Singleton;

/**
 * Main class.
 */
class Main extends Singleton {
	/**
	 * Layout.
	 *
	 * @var array
	 */
	private $layouts_cache = array();

	/**
	 * Layout.
	 *
	 * @var bool
	 */
	private $is_custom_layout = false;

	/**
	 * Layout directory.
	 *
	 * @var string
	 */
	private $layout_directory = WOODMART_THEMEROOT . '/inc/modules/layouts/';

	/**
	 * Constructor.
	 */
	public function init() {
		$this->include_classes_files();

		add_action( 'init', array( $this, 'include_files' ), 10 );
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_elementor_categories' ), 20 );
		add_filter( 'woodmart_main_content_classes', array( $this, 'add_layout_wrapper_class' ) );
		add_filter( 'woodmart_disable_sidebar', array( $this, 'disable_sidebar' ) );
	}

	/**
	 * Set is custom layout.
	 *
	 * @param bool $is_active Is active.
	 * @return void
	 */
	public function set_is_custom_layout( $is_active ) {
		$this->is_custom_layout = $is_active;
	}

	/**
	 * Is custom layout.
	 *
	 * @return bool
	 */
	public function is_custom_layout() {
		return $this->is_custom_layout;
	}

	/**
	 * Include classes files.
	 */
	public function include_classes_files() {
		require_once $this->layout_directory . 'admin/class-admin.php';
		require_once $this->layout_directory . 'admin/class-conditions-cache.php';
		require_once $this->layout_directory . 'admin/class-manager.php';
		require_once $this->layout_directory . 'admin/class-import.php';
		require_once $this->layout_directory . 'class-layout-type.php';

		if ( woodmart_woocommerce_installed() ) {
			require_once $this->layout_directory . 'class-checkout.php';
			require_once $this->layout_directory . 'class-thank-you-page.php';
			require_once $this->layout_directory . 'class-my-account.php';
			require_once $this->layout_directory . 'class-cart.php';
			require_once $this->layout_directory . 'class-shop-archive.php';
			require_once $this->layout_directory . 'class-single-product.php';
			require_once $this->layout_directory . 'class-loop-item.php';
		}

		require_once $this->layout_directory . 'class-single-post.php';
		require_once $this->layout_directory . 'class-posts-archive.php';
	}

	/**
	 * Include files.
	 */
	public function include_files() {
		$current_builder = woodmart_get_current_page_builder();

		if ( 'wpb' === $current_builder ) {
			foreach ( glob( $this->layout_directory . 'wpb/**/**/*.php', GLOB_NOSORT ) as $file ) {
				require_once $file;
			}

			require_once $this->layout_directory . 'wpb/maps/register-maps.php';
		} elseif ( 'elementor' === $current_builder ) {
			add_action( 'elementor/widgets/register', array( $this, 'register_layout_widgets' ) );
		}
	}

	/**
	 * Register widgets elements for Elementor.
	 *
	 * @return void
	 */
	public function register_layout_widgets() {
		$allowed_dirs = array(
			'post-archive',
			'single-post',
		);

		if ( woodmart_woocommerce_installed() ) {
			$allowed_dirs = array_merge(
				$allowed_dirs,
				array(
					'cart',
					'checkout',
					'thank-you-page',
					'shop-archive',
					'single-product',
					'my-account',
					'woocommerce',
				)
			);
		}

		foreach ( $allowed_dirs as $dir ) {
			$this->register_widgets_by_pattern( 'elementor/' . $dir . '/*.php' );
		}
	}

	/**
	 * Register widgets by pattern
	 *
	 * @param string $pattern Files to include.
	 * @return void
	 */
	public function register_widgets_by_pattern( $pattern ) {
		$files = glob( $this->layout_directory . $pattern );
		natsort( $files );
		foreach ( $files as $file ) {
			require_once $file;
		}
	}

	/**
	 * Add theme widget categories.
	 *
	 * @param Elements_Manager $elements_manager Elements manager instance.
	 */
	public function register_elementor_categories( $elements_manager ) {
		$elements_manager->add_category(
			'wd-posts-elements',
			array(
				'title' => esc_html__( '[XTemos] Posts elements', 'woodmart' ),
				'icon'  => 'fab fa-plug',
			)
		);

		$elements_manager->add_category(
			'wd-site-elements',
			array(
				'title' => esc_html__( '[XTemos] Site', 'woodmart' ),
				'icon'  => 'fab fa-plug',
			)
		);

		$elements_manager->add_category(
			'wd-single-product-elements',
			array(
				'title' => esc_html__( '[XTemos] Single product', 'woodmart' ),
				'icon'  => 'fab fa-plug',
			)
		);

		$elements_manager->add_category(
			'wd-shop-archive-elements',
			array(
				'title' => esc_html__( '[XTemos] Products archive', 'woodmart' ),
				'icon'  => 'fab fa-plug',
			)
		);

		$elements_manager->add_category(
			'wd-cart-elements',
			array(
				'title' => esc_html__( '[XTemos] Cart', 'woodmart' ),
				'icon'  => 'fab fa-plug',
			)
		);

		$elements_manager->add_category(
			'wd-checkout-elements',
			array(
				'title' => esc_html__( '[XTemos] Checkout', 'woodmart' ),
				'icon'  => 'fab fa-plug',
			)
		);

		$elements_manager->add_category(
			'wd-thank-you-page-elements',
			array(
				'title' => esc_html__( '[XTemos] Thank you page', 'woodmart' ),
				'icon'  => 'fab fa-plug',
			)
		);

		$elements_manager->add_category(
			'wd-my-account-elements',
			array(
				'title' => esc_html__( '[XTemos] My account', 'woodmart' ),
				'icon'  => 'fab fa-plug',
			)
		);
	}

	/**
	 * Disable sidebar from layouts.
	 *
	 * @param bool $condition Where sidebar needs to remove.
	 *
	 * @return bool
	 */
	public function disable_sidebar( $condition ) {
		return $condition || $this->is_custom_layout();
	}

	/**
	 * Check if the custom template is set.
	 *
	 * @param  string $type  Template type.
	 *
	 * @return bool
	 */
	public function has_custom_layout( $type ) {
		do_action( 'woodmart_get_layout_id', $this );

		$id   = $this->get_layout_id( $type );
		$post = get_post( $id );

		return ( ! empty( $id ) && $post && 'publish' === $post->post_status ) || self::is_layout_type( $type );
	}

	/**
	 * Set builder class wd-builder-(on/off) for layout wrapper.
	 *
	 * @param string $classes Classes.
	 *
	 * @return bool
	 */
	public function add_layout_wrapper_class( $classes ) {
		$wishlist_page    = function_exists( 'wpml_object_id_filter' ) ? wpml_object_id_filter( woodmart_get_opt( 'wishlist_page' ), 'page', true ) : woodmart_get_opt( 'wishlist_page' );
		$is_wishlist_page = $wishlist_page && (int) woodmart_get_the_ID() === (int) $wishlist_page;

		if ( 'woodmart_layout' === get_post_type() ) {
			$classes .= ' wd-builder-on';
		} elseif (
			woodmart_is_blog_archive() || woodmart_is_portfolio_archive() || is_singular( array( 'post', 'portfolio' ) ) ||
			( woodmart_woocommerce_installed() && ( woodmart_is_shop_archive() || is_singular( 'product' ) || is_cart() || is_checkout() || woodmart_is_thank_you_page() || is_account_page() || $is_wishlist_page ) )
		) {
			$classes .= $this->is_custom_layout() ? ' wd-builder-on' : ' wd-builder-off';
		}

		return $classes;
	}

	/**
	 * Get custom template ID.
	 *
	 * @param  string $type  Template type.
	 *
	 * @return string
	 */
	public function get_layout_id( $type ) {
		if ( isset( $this->layouts_cache[ $type ] ) ) {
			return apply_filters( 'woodmart_layout_id', $this->layouts_cache[ $type ] );
		}

		$conditions_data          = get_option( 'wd_layouts_conditions' );
		$current_conditions_data  = isset( $conditions_data[ $type ] ) ? $conditions_data[ $type ] : array();
		$sorted_data              = array();
		$conditions_priority      = array();
		$is_active_prev_condition = false;
		$has_filtered_condition   = false;
		$is_active                = false;

		foreach ( $current_conditions_data as $post_id => $conditions ) {
			$post_id = apply_filters( 'wpml_object_id', $post_id, 'woodmart_layout' );

			if ( ! $post_id ) {
				continue;
			}

			$post = get_post( $post_id );

			foreach ( $conditions as $condition ) {
				if ( woodmart_woocommerce_installed() ) {
					if ( 'single_product' === $type ) {
						$is_active = Single_Product::get_instance()->check( $condition );
					}

					if ( 'shop_archive' === $type ) {
						$is_active = Shop_Archive::get_instance()->check( $condition );

						if ( apply_filters( 'woodmart_allow_conditions_with_filter', true ) ) {
							if ( $has_filtered_condition && ! $is_active_prev_condition && $is_active ) {
								unset( $sorted_data[ $post_id ] );
								break;
							}

							if ( str_contains( $condition['condition_type'], 'filtered' ) && 'include' === $condition['condition_comparison'] ) {
								if ( ! $is_active ) {
									unset( $sorted_data[ $post_id ] );
									break;
								}

								$has_filtered_condition = true;
							}
						}
					}

					if ( 'checkout_form' === $type || 'checkout_content' === $type ) {
						$is_active = Checkout::get_instance()->check( $condition, $type );
					}

					if ( 'thank_you_page' === $type ) {
						$is_active = Thank_You_Page::get_instance()->check( $condition );
					}

					if ( 'my_account_page' === $type || 'my_account_auth' === $type || 'my_account_lost_password' === $type ) {
						$is_active = My_Account::get_instance()->check( $condition, $type );
					}

					if ( 'cart' === $type || 'empty_cart' === $type ) {
						$is_active = Cart::get_instance()->check( $condition, $type );
					}
				}

				if ( 'single_post' === $type || 'single_portfolio' === $type ) {
					$is_active = Single_Post::get_instance()->check( $condition, $type );
				}

				if ( 'blog_archive' === $type || 'portfolio_archive' === $type ) {
					$is_active = Posts_Archive::get_instance()->check( $condition, $type );
				}

				if ( $is_active && $post && 'publish' === $post->post_status ) {
					$sorted_data[ $post_id ][ $condition['condition_comparison'] ][] = array(
						'is_active' => $is_active,
						'priority'  => $this->get_condition_priority( $condition['condition_type'] ),
					);

					$is_active_prev_condition = true;
				}
			}
		}

		foreach ( $sorted_data as $post_id => $conditions ) {
			if ( isset( $conditions['include'] ) ) {
				foreach ( $conditions['include'] as $condition ) {
					if ( $condition['is_active'] ) {
						$conditions_priority[ $post_id ] = $condition['priority'];
					}
				}
			}

			if ( isset( $conditions['exclude'] ) ) {
				foreach ( $conditions['exclude'] as $condition ) {
					if ( $condition['is_active'] ) {
						unset( $conditions_priority[ $post_id ] );
					}
				}
			}
		}

		asort( $conditions_priority );

		$conditions_priority = array_flip( $conditions_priority );

		$this->layouts_cache[ $type ] = end( $conditions_priority );

		return apply_filters( 'woodmart_layout_id', $this->layouts_cache[ $type ] );
	}

	/**
	 * Get condition priority;
	 *
	 * @param string $type Condition type.
	 *
	 * @return int
	 */
	private function get_condition_priority( $type ) {
		$priority = 100;

		switch ( $type ) {
			case 'all':
				$priority = 10;
				break;
			case 'shop_page':
				$priority = 20;
				break;
			case 'product_cats':
			case 'product_tags':
			case 'product_attr':
			case 'order_total':
			case 'order_subtotal':
			case 'order_subtotal_after_discount':
				$priority = 30;
				break;
			case 'product_cat_children':
				$priority = 40;
				break;
			case 'product_search':
				$priority = 50;
				break;
			case 'product_cat':
			case 'product_tag':
			case 'product_brand':
			case 'product_attr_term':
			case 'product_term':
			case 'product_shipping_class':
				$priority = 70;
				break;
			case 'filtered_product_term':
			case 'filtered_product_by_term':
			case 'filtered_product_term_any':
			case 'filtered_product_stock_status':
				$priority = 80;
				break;
			case 'product':
			case 'products':
				$priority = 90;
				break;
		}

		return apply_filters( 'woodmart_layouts_condition_priority', $priority, $type );
	}

	/**
	 * Setup preview.
	 *
	 * @param array $query_args Query arguments.
	 * @param int   $force_post_id Force post ID.
	 */
	public static function setup_preview( $query_args = array(), $force_post_id = false ) {
		global $post;

		$post_id = get_the_ID();

		if ( ! empty( $post ) && 'revision' === $post->post_type ) {
			$post_id = $post->post_parent;
		} elseif ( ! empty( $post ) && 'woodmart_layout' !== $post->post_type ) {
			return;
		}

		$layout_type     = get_post_meta( $post_id, 'wd_layout_type', true );
		$setup_condition = ( wp_doing_ajax() && isset( $_POST['action'] ) && 'wd_layout_create' === $_POST['action'] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing

		$is_post_layout = isset( $_POST['type'] ) && in_array( $_POST['type'], array( 'single_post', 'single_portfolio' ), true ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( 'single_post' === $layout_type || 'single_portfolio' === $layout_type || ( $setup_condition && $is_post_layout ) ) {
			Single_Post::setup_postdata();
		}

		if ( 'blog_archive' === $layout_type || 'portfolio_archive' === $layout_type ) {
			$query_args = array(
				'post_type' => 'blog_archive' === $layout_type ? 'post' : 'portfolio',
				'paged'     => get_query_var( 'paged', 1 ),
			);

			if ( 'portfolio_archive' === $layout_type ) {
				$query_args['posts_per_page'] = (int) woodmart_get_opt( 'portoflio_per_page' );
			}

			Posts_Archive::switch_to_preview_query( $query_args );
		}

		if ( ! woodmart_woocommerce_installed() ) {
			return;
		}

		$is_product_layout = isset( $_POST['type'] ) && 'single_product' === $_POST['type']; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( 'single_product' === $layout_type || ( $setup_condition && $is_product_layout ) || $force_post_id ) {
			Single_Product::setup_postdata( $force_post_id );
		} elseif ( 'shop_archive' === $layout_type ) {
			if ( ! $query_args ) {
				$query_args = array(
					'post_type'      => 'product',
					'posts_per_page' => woodmart_get_products_per_page(),
				);
			}

			Shop_Archive::switch_to_preview_query( $query_args );
		} elseif ( 'cart' === $layout_type || 'checkout_form' === $layout_type ) {
			Cart::setup_cart();
		} elseif ( 'thank_you_page' === $layout_type ) {
			Thank_You_Page::setup_postdata();
		} elseif ( 'my_account_page' === $layout_type || 'my_account_auth' === $layout_type || 'my_account_lost_password' === $layout_type ) {
			My_Account::setup_postdata();
		}
	}

	/**
	 * Restore preview.
	 *
	 * @param bool $force_post_id Force post ID.
	 */
	public static function restore_preview( $force_post_id = false ) {
		if ( woodmart_woocommerce_installed() ) {
			Single_Product::reset_postdata( $force_post_id );
			Shop_Archive::restore_current_query();
			Cart::reset_cart();
			Thank_You_Page::reset_postdata();
			My_Account::reset_postdata();
		}

		Single_Post::reset_postdata();
		Posts_Archive::restore_current_query();
	}

	/**
	 * Return true if currently type.
	 *
	 * @param  string $type  Layout type.
	 *
	 * @return bool
	 */
	public static function is_layout_type( $type ) {
		$layout_id = '';

		if ( 'elementor' === woodmart_get_current_page_builder() ) {
			$layout_id = Builder_Data::get_instance()->get_data( 'layout_id' ) ? Builder_Data::get_instance()->get_data( 'layout_id' ) : get_the_ID();
		} elseif ( ( 'post.php' === $GLOBALS['pagenow'] || wp_doing_ajax() ) && ( isset( $_GET['post'] ) || isset( $_REQUEST['post_id'] ) || isset( $_POST['post_ID'] ) ) ) { // phpcs:ignore WordPress.Security
			// phpcs:disable WordPress.Security
			if ( isset( $_GET['post'] ) && $_GET['post'] ) {
				$layout_id = woodmart_clean( $_GET['post'] );
			} elseif ( isset( $_REQUEST['post_id'] ) && $_REQUEST['post_id'] ) {
				$layout_id = woodmart_clean( $_REQUEST['post_id'] );
			} elseif ( isset( $_POST['post_ID'] ) && $_POST['post_ID'] ) {
				$layout_id = woodmart_clean( $_POST['post_ID'] );
			}
			// phpcs:enable
		} elseif ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
			$layout_id = (int) vc_get_param( 'vc_post_id' );
		} elseif ( 'post-new.php' === $GLOBALS['pagenow'] && ( ! isset( $_REQUEST['post_type'] ) || 'woodmart_layout' !== $_REQUEST['post_type'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return false;
		} elseif ( is_admin() ) {
			return true;
		} else {
			$layout_id = get_the_ID();
		}

		if ( isset( $_POST['action'] ) && 'wd_layout_create' === $_POST['action'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			return true;
		}

		if ( wp_is_post_revision( $layout_id ) ) {
			$layout_id = wp_get_post_parent_id( $layout_id );
		}

		$layout_type = get_post_meta( $layout_id, 'wd_layout_type', true );

		return $layout_type === $type;
	}
}

Main::get_instance();
