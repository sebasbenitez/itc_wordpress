<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Widget sort by.
 *
 * @package woodmart
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * Register sort by widget.
 */
class WOODMART_Widget_Sorting extends WPH_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( ! woodmart_woocommerce_installed() || ! function_exists( 'wc_get_attribute_taxonomies' ) ) {
			return;
		}

		$args = array(
			'label'       => esc_html__( 'WOODMART WooCommerce Sort by', 'woodmart' ),
			'description' => esc_html__( 'Sort products by name, price, popularity etc.', 'woodmart' ),
			'slug'        => 'woodmart-woocommerce-sort-by',
		);

		$args['fields'] = array(
			array(
				'id'   => 'title',
				'type' => 'text',
				'std'  => esc_html__( 'Sort by', 'woodmart' ),
				'name' => esc_html__( 'Title', 'woodmart' ),
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
		if ( ! $this->is_widget_preview() && ! woocommerce_products_will_display() ) {
			return;
		}

		$orderby                 = isset( $_GET['orderby'] ) ? wc_clean( $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) ); // phpcs:ignore WordPress.Security
		$show_default_orderby    = 'menu_order' === apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
		$catalog_orderby_options = apply_filters(
			'woocommerce_catalog_orderby',
			array(
				'menu_order' => esc_html__( 'Default', 'woodmart' ),
				'popularity' => esc_html__( 'Popularity', 'woodmart' ),
				'rating'     => esc_html__( 'Average rating', 'woodmart' ),
				'date'       => esc_html__( 'Newness', 'woodmart' ),
				'price'      => esc_html__( 'Price: low to high', 'woodmart' ),
				'price-desc' => esc_html__( 'Price: high to low', 'woodmart' ),
			)
		);

		if ( ! $show_default_orderby ) {
			unset( $catalog_orderby_options['menu_order'] );
		}

		if ( get_option( 'woocommerce_enable_review_rating' ) === 'no' ) {
			unset( $catalog_orderby_options['rating'] );
		}

		echo wp_kses_post( $args['before_widget'] );

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance );

		if ( $title ) {
			echo wp_kses_post( $args['before_title'] ) . $title . wp_kses_post( $args['after_title'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		wc_get_template(
			'loop/orderby.php',
			array(
				'catalog_orderby_options' => $catalog_orderby_options,
				'orderby'                 => $orderby,
				'show_default_orderby'    => $show_default_orderby,
				'list'                    => true,
			)
		);

		echo wp_kses_post( $args['after_widget'] );
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
