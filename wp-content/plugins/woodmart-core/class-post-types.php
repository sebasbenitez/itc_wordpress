<?php
/**
 * Post types file.
 *
 * @package woodmart
 */

namespace WOODMART_CORE;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Post types class.
 */
class Post_Types {
	/**
	 * Instance.
	 *
	 * @var null
	 */
	public static $instance = null;

	/**
	 * Instance.
	 *
	 * @return Post_Types|null
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_layout' ), 1 );
		add_action( 'init', array( $this, 'register_blocks' ), 1 );
		add_action( 'init', array( $this, 'slider' ), 1 );
		add_action( 'init', array( $this, 'register_sidebars' ), 1 );
		add_action( 'init', array( $this, 'register_popup_builder' ), 1 );
		add_action( 'init', array( $this, 'register_floating_blocks' ), 1 );
		add_action( 'init', array( $this, 'register_portfolio' ), 1 );
		add_action( 'init', array( $this, 'linked_variations' ), 1 );
		add_action( 'init', array( $this, 'bought_together' ), 1 );
		add_action( 'init', array( $this, 'register_discounts' ), 1 );
		add_action( 'init', array( $this, 'register_free_gifts' ), 1 );
		add_action( 'init', array( $this, 'register_estimate_delivery' ), 1 );
		add_action( 'init', array( $this, 'register_custom_product_tabs' ), 1 );
		add_action( 'init', array( $this, 'register_custom_labels' ), 1 );
		add_action( 'init', array( $this, 'size_guide' ), 1 );
		add_action( 'init', array( $this, 'register_abandoned_cart' ), 1 );
	}

	/**
	 * Register layout post type.
	 */
	public function register_layout() {
		register_post_type(
			'woodmart_layout',
			array(
				'label'               => esc_html__( 'Layouts', 'woodmart' ),
				'labels'              => array(
					'name'          => esc_html__( 'Layouts', 'woodmart' ),
					'singular_name' => esc_html__( 'Layout', 'woodmart' ),
					'menu_name'     => esc_html__( 'Layouts', 'woodmart' ),
					'all_items'     => esc_html__( 'All Layouts', 'woodmart' ),
					'add_new'       => esc_html__( 'Add Layout', 'woodmart' ),
					'add_new_item'  => esc_html__( 'Add Layout', 'woodmart' ),
				),
				'supports'            => array( 'title', 'editor', 'custom-fields' ),
				'show_in_nav_menus'   => false,
				'hierarchical'        => false,
				'public'              => true,
				'menu_position'       => 32,
				'menu_icon'           => 'dashicons-format-gallery',
				'publicly_queryable'  => is_user_logged_in(),
				'show_in_rest'        => true,
				'show_in_admin_bar'   => false,
				'capability_type'     => 'page',
				'exclude_from_search' => true,
			)
		);
	}

	/**
	 * Register blocks post type and taxonomy.
	 */
	public function register_blocks() {
		$labels = array(
			'name'          => esc_html__( 'HTML Blocks', 'woodmart' ),
			'singular_name' => esc_html__( 'HTML Block', 'woodmart' ),
			'menu_name'     => esc_html__( 'HTML Blocks', 'woodmart' ),
			'all_items'     => esc_html__( 'All Blocks', 'woodmart' ),
			'add_new'       => esc_html__( 'Add Block', 'woodmart' ),
			'add_new_item'  => esc_html__( 'Add Block', 'woodmart' ),
		);

		$args = array(
			'label'               => esc_html__( 'cms_block', 'woodmart' ),
			'description'         => esc_html__( 'CMS Blocks for custom HTML to place in your pages', 'woodmart' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor' ),
			'hierarchical'        => false,
			'public'              => true,
			'publicly_queryable'  => is_user_logged_in(),
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => true,
			'menu_position'       => 32,
			'menu_icon'           => 'dashicons-schedule',
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'rewrite'             => false,
			'capability_type'     => 'page',
			'show_in_rest'        => true,
		);

		register_post_type( 'cms_block', $args );

		$labels = array(
			'name'                  => esc_html__( 'HTML Block Categories', 'woodmart' ),
			'singular_name'         => esc_html__( 'HTML Block Category', 'woodmart' ),
			'popular_items'         => esc_html__( 'Popular Categories', 'woodmart' ),
			'add_new_item'          => esc_html__( 'Add New Category', 'woodmart' ),
			'new_item_name'         => esc_html__( 'New Category', 'woodmart' ),
			'add_or_remove_items'   => esc_html__( 'Add or remove Categories', 'woodmart' ),
			'choose_from_most_used' => esc_html__( 'Choose from most used', 'woodmart' ),
			'menu_name'             => esc_html__( 'Categories', 'woodmart' ),
		);

		$args = array(
			'labels'            => $labels,
			'public'            => true,
			'show_in_nav_menus' => false,
			'show_admin_column' => false,
			'hierarchical'      => true,
			'show_tagcloud'     => true,
			'show_ui'           => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'cms_block_cat' ),
			'capabilities'      => array(),
			'default_term'      => array(
				'name' => esc_html__( 'Uncategorized', 'woodmart' ),
				'slug' => 'uncategorized',
			),
			'show_in_rest'      => true,
		);

		register_taxonomy( 'cms_block_cat', array( 'cms_block' ), $args );
	}

	/**
	 * Register slider post type and taxonomy.
	 */
	public function slider() {
		if (
			! function_exists( 'woodmart_get_opt' ) ||
			(
				! woodmart_get_opt( 'woodmart_slider', '1' ) &&
				(
					! function_exists( 'woodmart_is_import_demo_content' ) ||
					! woodmart_is_import_demo_content()
				)
			)
		) {
			return;
		}

		$labels = array(
			'name'          => esc_html__( 'Slides', 'woodmart' ),
			'singular_name' => esc_html__( 'Slide', 'woodmart' ),
			'menu_name'     => esc_html__( 'Slides', 'woodmart' ),
			'all_items'     => esc_html__( 'All Slides', 'woodmart' ),
			'add_new'       => esc_html__( 'Add Slide', 'woodmart' ),
			'add_new_item'  => esc_html__( 'Add Slide', 'woodmart' ),
		);

		$args = array(
			'label'               => 'woodmart_slide',
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'page-attributes', 'custom-fields' ),
			'hierarchical'        => false,
			'public'              => true,
			'publicly_queryable'  => is_user_logged_in(),
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => true,
			'menu_position'       => 32,
			'menu_icon'           => 'dashicons-images-alt2',
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'capability_type'     => 'page',
		);

		register_post_type( 'woodmart_slide', $args );

		$labels = array(
			'name'                  => esc_html__( 'Sliders', 'woodmart' ),
			'singular_name'         => esc_html__( 'Slider', 'woodmart' ),
			'search_items'          => esc_html__( 'Search Sliders', 'woodmart' ),
			'popular_items'         => esc_html__( 'Popular Sliders', 'woodmart' ),
			'all_items'             => esc_html__( 'All Sliders', 'woodmart' ),
			'parent_item'           => esc_html__( 'Parent Slider', 'woodmart' ),
			'parent_item_colon'     => esc_html__( 'Parent Slider', 'woodmart' ),
			'edit_item'             => esc_html__( 'Edit Slider', 'woodmart' ),
			'update_item'           => esc_html__( 'Update Slider', 'woodmart' ),
			'add_new_item'          => esc_html__( 'Add New Slider', 'woodmart' ),
			'new_item_name'         => esc_html__( 'New Slide', 'woodmart' ),
			'add_or_remove_items'   => esc_html__( 'Add or remove Sliders', 'woodmart' ),
			'choose_from_most_used' => esc_html__( 'Choose from most used sliders', 'woodmart' ),
			'menu_name'             => esc_html__( 'Sliders', 'woodmart' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'show_in_nav_menus'  => false,
			'show_admin_column'  => false,
			'hierarchical'       => true,
			'show_tagcloud'      => false,
			'show_ui'            => true,
			'query_var'          => false,
			'publicly_queryable' => false,
			'rewrite'            => array( 'slug' => 'woodmart_slider' ),
			'capabilities'       => array(),
		);

		register_taxonomy( 'woodmart_slider', array( 'woodmart_slide' ), $args );
	}

	/**
	 * Register sidebars post type.
	 */
	public function register_sidebars() {

		$labels = array(
			'name'          => esc_html__( 'Sidebars', 'woodmart' ),
			'singular_name' => esc_html__( 'Sidebar', 'woodmart' ),
			'menu_name'     => esc_html__( 'Sidebars', 'woodmart' ),
			'all_items'     => esc_html__( 'All Sidebars', 'woodmart' ),
			'add_new'       => esc_html__( 'Add Sidebar', 'woodmart' ),
			'add_new_item'  => esc_html__( 'Add Sidebar', 'woodmart' ),
		);

		$args = array(
			'label'               => esc_html__( 'woodmart_sidebar', 'woodmart' ),
			'description'         => esc_html__( 'You can create additional custom sidebar and use them in Visual Composer', 'woodmart' ),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => false,
			'menu_position'       => 32,
			'menu_icon'           => 'dashicons-welcome-widgets-menus',
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'rewrite'             => false,
			'capability_type'     => 'page',
		);

		register_post_type( 'woodmart_sidebar', $args );
	}

	/**
	 * Register popup builder post type.
	 */
	public function register_popup_builder() {
		$labels = array(
			'name'          => esc_html__( 'Popups', 'woodmart' ),
			'singular_name' => esc_html__( 'Popup', 'woodmart' ),
			'menu_name'     => esc_html__( 'Popups', 'woodmart' ),
			'search_items'  => esc_html__( 'Search Popup', 'woodmart' ),
			'all_items'     => esc_html__( 'All Popups', 'woodmart' ),
			'add_new'       => esc_html__( 'Add Popup', 'woodmart' ),
			'add_new_item'  => esc_html__( 'Add Popup', 'woodmart' ),
		);

		$args = array(
			'label'               => esc_html__( 'Popups', 'woodmart' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'custom-fields' ),
			'hierarchical'        => false,
			'public'              => false,
			'publicly_queryable'  => is_user_logged_in(),
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => false,
			'menu_position'       => 32,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'rewrite'             => false,
			'capability_type'     => 'page',
			'show_in_rest'        => true,
		);

		register_post_type( 'wd_popup', $args );

		$labels = array(
			'name'                  => esc_html__( 'Popup Categories', 'woodmart' ),
			'singular_name'         => esc_html__( 'Popup Category', 'woodmart' ),
			'popular_items'         => esc_html__( 'Popular Popup Categories', 'woodmart' ),
			'all_items'             => esc_html__( 'All Popup Categories', 'woodmart' ),
			'add_new_item'          => esc_html__( 'Add New Category', 'woodmart' ),
			'new_item_name'         => esc_html__( 'New Category', 'woodmart' ),
			'add_or_remove_items'   => esc_html__( 'Add or remove Categories', 'woodmart' ),
			'choose_from_most_used' => esc_html__( 'Choose from most used popups', 'woodmart' ),
			'menu_name'             => esc_html__( 'Categories', 'woodmart' ),
		);

		$args = array(
			'labels'            => $labels,
			'public'            => true,
			'show_in_nav_menus' => false,
			'show_admin_column' => false,
			'hierarchical'      => true,
			'show_tagcloud'     => true,
			'show_ui'           => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'wd_popup_cat' ),
			'capabilities'      => array(),
			'show_in_rest'      => true,
			'default_term'      => array(
				'name' => esc_html__( 'Uncategorized', 'woodmart' ),
				'slug' => 'uncategorized',
			),
		);

		register_taxonomy( 'wd_popup_cat', array( 'wd_popup' ), $args );
	}

	/**
	 * Register floating blocks post type.
	 */
	public function register_floating_blocks() {
		$labels = array(
			'name'          => esc_html__( 'Floating Blocks', 'woodmart' ),
			'singular_name' => esc_html__( 'Floating Block', 'woodmart' ),
			'menu_name'     => esc_html__( 'Floating Blocks', 'woodmart' ),
			'search_items'  => esc_html__( 'Search Blocks', 'woodmart' ),
			'all_items'     => esc_html__( 'All Blocks', 'woodmart' ),
			'add_new'       => esc_html__( 'Add Block', 'woodmart' ),
			'add_new_item'  => esc_html__( 'Add Block', 'woodmart' ),
		);

		$args = array(
			'label'               => esc_html__( 'Floating Blocks', 'woodmart' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'custom-fields' ),
			'hierarchical'        => false,
			'public'              => false,
			'publicly_queryable'  => is_user_logged_in(),
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => false,
			'menu_position'       => 32,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'rewrite'             => false,
			'capability_type'     => 'page',
			'show_in_rest'        => true,
		);

		register_post_type( 'wd_floating_block', $args );

		$labels = array(
			'name'                  => esc_html__( 'Floating Block Categories', 'woodmart' ),
			'singular_name'         => esc_html__( 'Floating Block Category', 'woodmart' ),
			'popular_items'         => esc_html__( 'Popular Floating Block Categories', 'woodmart' ),
			'all_items'             => esc_html__( 'All Floating Block Categories', 'woodmart' ),
			'add_new_item'          => esc_html__( 'Add New Category', 'woodmart' ),
			'new_item_name'         => esc_html__( 'New Category', 'woodmart' ),
			'add_or_remove_items'   => esc_html__( 'Add or remove Categories', 'woodmart' ),
			'choose_from_most_used' => esc_html__( 'Choose from most used floating blocks', 'woodmart' ),
			'menu_name'             => esc_html__( 'Categories', 'woodmart' ),
		);

		$args = array(
			'labels'            => $labels,
			'public'            => true,
			'show_in_nav_menus' => false,
			'show_admin_column' => false,
			'hierarchical'      => true,
			'show_tagcloud'     => true,
			'show_ui'           => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'wd_floating_block_cat' ),
			'capabilities'      => array(),
			'show_in_rest'      => true,
			'default_term'      => array(
				'name' => esc_html__( 'Uncategorized', 'woodmart' ),
				'slug' => 'uncategorized',
			),
		);

		register_taxonomy( 'wd_floating_block_cat', array( 'wd_floating_block' ), $args );
	}

	/**
	 * Register portfolio post type and taxonomy.
	 */
	public function register_portfolio() {
		if (
			! function_exists( 'woodmart_get_opt' ) ||
			(
				! woodmart_get_opt( 'portfolio', '1' ) &&
				(
					! function_exists( 'woodmart_is_import_demo_content' ) ||
					! woodmart_is_import_demo_content()
				)
			)
		) {
			return;
		}

		$portfolio_slug     = function_exists( 'woodmart_get_opt' ) ? woodmart_get_opt( 'portfolio_item_slug', 'portfolio' ) : 'portfolio';
		$portfolio_cat_slug = function_exists( 'woodmart_get_opt' ) ? woodmart_get_opt( 'portfolio_cat_slug', 'project-cat' ) : 'project-cat';
		$portfolio_page_id  = function_exists( 'woodmart_get_portfolio_page_id' ) ? woodmart_get_portfolio_page_id() : '';
		$has_archive        = $portfolio_page_id && get_post( $portfolio_page_id ) ? urldecode( get_page_uri( $portfolio_page_id ) ) : true;

		$labels = array(
			'name'          => esc_html__( 'Portfolio', 'woodmart' ),
			'singular_name' => esc_html__( 'Project', 'woodmart' ),
			'menu_name'     => esc_html__( 'Projects', 'woodmart' ),
			'all_items'     => esc_html__( 'All Projects', 'woodmart' ),
			'add_new'       => esc_html__( 'Add Project', 'woodmart' ),
			'add_new_item'  => esc_html__( 'Add Project', 'woodmart' ),
		);

		$args = array(
			'label'               => esc_html__( 'portfolio', 'woodmart' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments', 'page-attributes' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 32,
			'menu_icon'           => 'dashicons-format-gallery',
			'can_export'          => true,
			'has_archive'         => $has_archive,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'rewrite'             => array(
				'slug'       => $portfolio_slug,
				'with_front' => false,
				'feeds'      => true,
			),
			'capability_type'     => 'page',
			'show_in_rest'        => true,
		);

		register_post_type( 'portfolio', $args );

		/**
		 * Create a taxonomy category for portfolio
		 *
		 * @uses  Inserts new taxonomy object into the list
		 * @uses  Adds query vars
		 *
		 * @param string  Name of taxonomy object
		 * @param array|string  Name of the object type for the taxonomy object.
		 * @param array|string  Taxonomy arguments
		 * @return null|WP_Error WP_Error if errors, otherwise null.
		 */

		$labels = array(
			'name'                  => esc_html__( 'Project Categories', 'woodmart' ),
			'singular_name'         => esc_html__( 'Project Category', 'woodmart' ),
			'popular_items'         => esc_html__( 'Popular Project Categories', 'woodmart' ),
			'all_items'             => esc_html__( 'All Project Categories', 'woodmart' ),
			'add_new_item'          => esc_html__( 'Add New Category', 'woodmart' ),
			'new_item_name'         => esc_html__( 'New Category', 'woodmart' ),
			'add_or_remove_items'   => esc_html__( 'Add or remove Categories', 'woodmart' ),
			'choose_from_most_used' => esc_html__( 'Choose from most used text-domain', 'woodmart' ),
			'menu_name'             => esc_html__( 'Categories', 'woodmart' ),
		);

		$args = array(
			'labels'            => $labels,
			'public'            => true,
			'show_in_nav_menus' => true,
			'show_admin_column' => false,
			'hierarchical'      => true,
			'show_tagcloud'     => true,
			'show_ui'           => true,
			'query_var'         => true,
			'rewrite'           => array(
				'slug'         => $portfolio_cat_slug,
				'with_front'   => false,
				'hierarchical' => true,
			),
			'capabilities'      => array(),
			'show_in_rest'      => true,
		);

		register_taxonomy( 'project-cat', array( 'portfolio' ), $args );
	}

	/**
	 * Register layout post type.
	 */
	public function linked_variations() {
		if (
			! function_exists( 'woodmart_get_opt' ) ||
			(
				! woodmart_get_opt( 'linked_variations' ) &&
				(
					! function_exists( 'woodmart_is_import_demo_content' ) ||
					! woodmart_is_import_demo_content()
				)
			)
		) {
			return;
		}

		register_post_type(
			'woodmart_woo_lv',
			array(
				'label'               => esc_html__( 'Linked Variations', 'woodmart' ),
				'labels'              => array(
					'name'          => esc_html__( 'Linked Variations', 'woodmart' ),
					'singular_name' => esc_html__( 'Linked Variations', 'woodmart' ),
					'menu_name'     => esc_html__( 'Linked Variations', 'woodmart' ),
					'add_new'       => esc_html__( 'Add New', 'woodmart' ),
					'add_new_item'  => esc_html__( 'Add New', 'woodmart' ),
				),
				'supports'            => array( 'title' ),
				'show_in_nav_menus'   => false,
				'hierarchical'        => false,
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => 'edit.php?post_type=product',
				'publicly_queryable'  => false,
				'show_in_rest'        => true,
				'show_in_admin_bar'   => false,
				'capability_type'     => 'product',
				'exclude_from_search' => true,
			)
		);
	}

	/**
	 * Register frequently bought together post type.
	 */
	public function bought_together() {
		if (
			! function_exists( 'woodmart_get_opt' ) ||
			(
				! woodmart_get_opt( 'bought_together_enabled' ) &&
				(
					! function_exists( 'woodmart_is_import_demo_content' ) ||
					! woodmart_is_import_demo_content()
				)
			)
		) {
			return;
		}

		register_post_type(
			'woodmart_woo_fbt',
			array(
				'label'               => esc_html__( 'Frequently Bought Together', 'woodmart' ),
				'labels'              => array(
					'name'          => esc_html__( 'Frequently Bought Together', 'woodmart' ),
					'singular_name' => esc_html__( 'Frequently Bought Together', 'woodmart' ),
					'menu_name'     => esc_html__( 'Frequently Bought Together', 'woodmart' ),
					'add_new'       => esc_html__( 'Add New', 'woodmart' ),
					'add_new_item'  => esc_html__( 'Add New', 'woodmart' ),
				),
				'supports'            => array( 'title' ),
				'show_in_nav_menus'   => false,
				'hierarchical'        => false,
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => 'edit.php?post_type=product',
				'publicly_queryable'  => false,
				'show_in_rest'        => true,
				'show_in_admin_bar'   => false,
				'capability_type'     => 'product',
				'exclude_from_search' => true,
			)
		);
	}

	/**
	 * Register Dynamic Pricing & Discounts post type.
	 */
	public function register_discounts() {
		if (
			! function_exists( 'woodmart_get_opt' ) ||
			(
				! woodmart_get_opt( 'discounts_enabled' ) &&
				(
					! function_exists( 'woodmart_is_import_demo_content' ) ||
					! woodmart_is_import_demo_content()
				)
			)
		) {
			return;
		}

		register_post_type(
			'wd_woo_discounts',
			array(
				'label'               => esc_html__( 'Dynamic Discounts', 'woodmart' ),
				'labels'              => array(
					'name'          => esc_html__( 'Dynamic Discounts', 'woodmart' ),
					'singular_name' => esc_html__( 'Dynamic Discount', 'woodmart' ),
					'menu_name'     => esc_html__( 'Dynamic Discounts', 'woodmart' ),
					'add_new'       => esc_html__( 'Add New', 'woodmart' ),
					'add_new_item'  => esc_html__( 'Add New Rule', 'woodmart' ),
					'edit_item'     => esc_html__( 'Edit Discount Rule', 'woodmart' ),
				),
				'supports'            => array( 'title' ),
				'show_in_nav_menus'   => false,
				'hierarchical'        => false,
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => 'edit.php?post_type=product',
				'publicly_queryable'  => false,
				'show_in_rest'        => true,
				'capability_type'     => 'product',
				'exclude_from_search' => true,
			)
		);
	}

	/**
	 * Register Free gifts post type.
	 */
	public function register_free_gifts() {
		if (
			! function_exists( 'woodmart_get_opt' ) ||
			(
				! woodmart_get_opt( 'free_gifts_enabled' ) &&
				(
					! function_exists( 'woodmart_is_import_demo_content' ) ||
					! woodmart_is_import_demo_content()
				)
			)
		) {
			return;
		}

		register_post_type(
			'wd_woo_free_gifts',
			array(
				'label'               => esc_html__( 'Free Gifts', 'woodmart' ),
				'labels'              => array(
					'name'          => esc_html__( 'Free Gifts', 'woodmart' ),
					'singular_name' => esc_html__( 'Free Gift', 'woodmart' ),
					'menu_name'     => esc_html__( 'Free Gifts', 'woodmart' ),
					'add_new'       => esc_html__( 'Add New', 'woodmart' ),
					'add_new_item'  => esc_html__( 'Add New Gift', 'woodmart' ),
					'edit_item'     => esc_html__( 'Edit Gift Rule', 'woodmart' ),
				),
				'supports'            => array( 'title' ),
				'show_in_nav_menus'   => false,
				'hierarchical'        => false,
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => 'edit.php?post_type=product',
				'publicly_queryable'  => false,
				'show_in_rest'        => true,
				'capability_type'     => 'product',
				'exclude_from_search' => true,
			)
		);
	}

	/**
	 * Register Estimate Delivery post type.
	 */
	public function register_estimate_delivery() {
		if (
			! function_exists( 'woodmart_get_opt' ) ||
			(
				! woodmart_get_opt( 'estimate_delivery_enabled' ) &&
				(
					! function_exists( 'woodmart_is_import_demo_content' ) ||
					! woodmart_is_import_demo_content()
				)
			)
		) {
			return;
		}

		register_post_type(
			'wd_woo_est_del',
			array(
				'label'               => esc_html__( 'Estimate Delivery', 'woodmart' ),
				'labels'              => array(
					'name'          => esc_html__( 'Estimate Delivery', 'woodmart' ),
					'singular_name' => esc_html__( 'Estimate Delivery', 'woodmart' ),
					'menu_name'     => esc_html__( 'Estimate Delivery', 'woodmart' ),
					'add_new'       => esc_html__( 'Add New', 'woodmart' ),
					'add_new_item'  => esc_html__( 'Add New Rule', 'woodmart' ),
					'edit_item'     => esc_html__( 'Edit Estimate Delivery Rule', 'woodmart' ),
				),
				'supports'            => array( 'title' ),
				'show_in_nav_menus'   => false,
				'hierarchical'        => false,
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => 'edit.php?post_type=product',
				'publicly_queryable'  => false,
				'show_in_rest'        => true,
				'capability_type'     => 'product',
				'exclude_from_search' => true,
			)
		);
	}

	/**
	 * Register custom product tabs.
	 */
	public function register_custom_product_tabs() {
		if (
			! function_exists( 'woodmart_get_opt' ) ||
			(
				! woodmart_get_opt( 'custom_product_tabs_enabled' ) &&
				(
					! function_exists( 'woodmart_is_import_demo_content' ) ||
					! woodmart_is_import_demo_content()
				)
			)
		) {
			return;
		}

		register_post_type(
			'wd_product_tabs',
			array(
				'label'               => esc_html__( 'Custom Tabs', 'woodmart' ),
				'labels'              => array(
					'name'          => esc_html__( 'Custom Tabs', 'woodmart' ),
					'singular_name' => esc_html__( 'Custom Tabs', 'woodmart' ),
					'menu_name'     => esc_html__( 'Custom Tabs', 'woodmart' ),
					'add_new'       => esc_html__( 'Add New Tab', 'woodmart' ),
					'add_new_item'  => esc_html__( 'Add New Tab', 'woodmart' ),
					'edit_item'     => esc_html__( 'Edit Tabs', 'woodmart' ),
				),
				'supports'            => array( 'title', 'editor' ),
				'show_in_nav_menus'   => false,
				'hierarchical'        => false,
				'public'              => false,
				'show_in_admin_bar'   => false,
				'show_in_menu'        => 'edit.php?post_type=product',
				'publicly_queryable'  => is_user_logged_in(),
				'show_in_rest'        => true,
				'capability_type'     => 'product',
				'exclude_from_search' => true,
				'show_ui'             => true,
				'rewrite'             => false,
			)
		);
	}

	/**
	 * Register custom labels post type.
	 */
	public function register_custom_labels() {
		if (
			! function_exists( 'woodmart_get_opt' ) ||
			(
				! woodmart_get_opt( 'custom_labels' ) &&
				(
					! function_exists( 'woodmart_is_import_demo_content' ) ||
					! woodmart_is_import_demo_content()
				)
			)
		) {
			return;
		}

		$labels = array(
			'name'          => esc_html__( 'Custom Labels', 'woodmart' ),
			'singular_name' => esc_html__( 'Custom Labels', 'woodmart' ),
			'menu_name'     => esc_html__( 'Custom Labels', 'woodmart' ),
			'add_new'       => esc_html__( 'Add New Label', 'woodmart' ),
			'add_new_item'  => esc_html__( 'Add New Label', 'woodmart' ),
			'edit_item'     => esc_html__( 'Edit Labels', 'woodmart' ),
		);

		$args = array(
			'label'               => esc_html__( 'Custom Labels', 'woodmart' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'custom-fields', 'page-attributes' ),
			'hierarchical'        => false,
			'public'              => false,
			'publicly_queryable'  => is_user_logged_in(),
			'show_ui'             => true,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => false,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'rewrite'             => false,
			'capability_type'     => 'product',
			'show_in_menu'        => 'edit.php?post_type=product',
			'show_in_rest'        => true,
		);

		register_post_type( 'wd_custom_label', $args );
	}

	/**
	 * Register size guide post type.
	 */
	public function size_guide() {
		if (
			! function_exists( 'woodmart_get_opt' ) ||
			(
				! woodmart_get_opt( 'size_guides', '1' ) &&
				(
					! function_exists( 'woodmart_is_import_demo_content' ) ||
					! woodmart_is_import_demo_content()
				)
			)
		) {
			return;
		}

		$labels = array(
			'name'          => esc_html__( 'Size Guides', 'woodmart' ),
			'singular_name' => esc_html__( 'Size Guide', 'woodmart' ),
			'menu_name'     => esc_html__( 'Size Guides', 'woodmart' ),
		);

		$args = array(
			'label'               => esc_html__( 'woodmart_size_guide', 'woodmart' ),
			'description'         => esc_html__( 'Size guide to place in your products', 'woodmart' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor' ),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => 'edit.php?post_type=product',
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => true,
			'menu_icon'           => 'dashicons-editor-kitchensink',
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'rewrite'             => false,
			'show_in_rest'        => true,
			'capability_type'     => 'product',
		);

		register_post_type( 'woodmart_size_guide', $args );
	}

	/**
	 * Register Abandoned Cart post type.
	 */
	public function register_abandoned_cart() {
		if (
			! function_exists( 'woodmart_get_opt' ) ||
			(
				! woodmart_get_opt( 'cart_recovery_enabled' ) &&
				(
					! function_exists( 'woodmart_is_import_demo_content' ) ||
					! woodmart_is_import_demo_content()
				)
			)
		) {
			return;
		}

		register_post_type(
			'wd_abandoned_cart',
			array(
				'label'               => esc_html__( 'Carts', 'woodmart' ),
				'labels'              => array(
					'name'          => esc_html__( 'Abandoned Cart', 'woodmart' ),
					'singular_name' => esc_html__( 'Abandoned Cart', 'woodmart' ),
					'menu_name'     => esc_html__( 'Abandoned Cart', 'woodmart' ),
					'add_new'       => esc_html__( 'Add New Abandoned Cart', 'woodmart' ),
					'add_new_item'  => esc_html__( 'Add New Abandoned Cart', 'woodmart' ),
					'edit_item'     => esc_html__( 'Abandoned cart information', 'woodmart' ),
				),
				'supports'            => array( '' ),
				'show_in_nav_menus'   => false,
				'hierarchical'        => false,
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => false,
				'exclude_from_search' => true,
				'capability_type'     => 'post',
				'capabilities'        => array( 'create_posts' => false ),
				'map_meta_cap'        => true,
			)
		);
	}
}

Post_Types::get_instance();
