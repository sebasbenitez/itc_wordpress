<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * AJAX search widget
 *
 * @package woodmart§
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * Register AJAX search widget.
 */
class WOODMART_Widget_Search extends WPH_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( ! woodmart_woocommerce_installed() ) {
			return;
		}

		$args = array(
			'label'       => esc_html__( 'WOODMART AJAX Search', 'woodmart' ),
			'description' => esc_html__( 'Search form by products with AJAX', 'woodmart' ),
			'slug'        => 'woodmart-ajax-search',
		);

		$args['fields'] = array(
			array(
				'id'   => 'title',
				'type' => 'text',
				'std'  => esc_html__( 'Search products', 'woodmart' ),
				'name' => esc_html__( 'Title', 'woodmart' ),
			),
			array(
				'id'     => 'post_type',
				'type'   => 'dropdown',
				'std'    => 'product',
				'name'   => esc_html__( 'Search post type', 'woodmart' ),
				'fields' => array(
					esc_html__( 'Product', 'woodmart' )   => 'product',
					esc_html__( 'Post', 'woodmart' )      => 'post',
					esc_html__( 'Portfolio', 'woodmart' ) => 'portfolio',
				),
			),
			array(
				'id'   => 'number',
				'type' => 'number',
				'std'  => 4,
				'name' => esc_html__( 'Number of products to show', 'woodmart' ),
			),
			array(
				'id'   => 'price',
				'type' => 'checkbox',
				'std'  => 1,
				'name' => esc_html__( 'Show price', 'woodmart' ),
			),
			array(
				'id'   => 'thumbnail',
				'type' => 'checkbox',
				'std'  => 1,
				'name' => esc_html__( 'Show thumbnail', 'woodmart' ),
			),
			array(
				'id'   => 'categories',
				'type' => 'checkbox',
				'std'  => 1,
				'name' => esc_html__( 'Show categories', 'woodmart' ),
			),
		);

		$this->create_widget( $args );
	}

	/**
	 * Render widget.
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Widget instance.
	 */
	public function widget( $args, $instance ) {
		extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract

		echo wp_kses_post( $before_widget );

		$number     = empty( $instance['number'] ) ? 3 : absint( $instance['number'] );
		$thumbnail  = empty( $instance['thumbnail'] ) ? 0 : absint( $instance['thumbnail'] );
		$price      = empty( $instance['price'] ) ? 0 : absint( $instance['price'] );
		$post_type  = empty( $instance['post_type'] ) ? 'product' : $instance['post_type'];
		$categories = true;

		if ( isset( $instance['categories'] ) ) {
			$categories = empty( $instance['categories'] ) ? 0 : absint( $instance['price'] );
		}

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance );

		if ( $title ) {
			echo wp_kses_post( $before_title ) . $title . wp_kses_post( $after_title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		woodmart_search_form(
			array(
				'ajax'            => true,
				'count'           => $number,
				'thumbnail'       => $thumbnail,
				'show_categories' => $categories,
				'post_type'       => $post_type,
				'price'           => $price,
			)
		);

		echo wp_kses_post( $after_widget );
	}

	/**
	 * Form.
	 *
	 * @param array $instance Instance.
	 */
	public function form( $instance ) { // phpcs:ignore Generic.CodeAnalysis.UselessOverridingMethod
		parent::form( $instance );
	}
}
