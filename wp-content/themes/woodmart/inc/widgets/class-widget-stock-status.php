<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Stock status widget.
 *
 * @package woodmart
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * Register stock status widget.
 */
class WOODMART_Stock_Status extends WPH_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( ! woodmart_woocommerce_installed() ) {
			return;
		}

		$args = array(
			'label'       => esc_html__( 'WOODMART Stock status', 'woodmart' ),
			'description' => esc_html__( 'Filter stock and on-sale products', 'woodmart' ),
			'slug'        => 'wd-widget-stock-status',
		);

		$args['fields'] = array(
			array(
				'id'      => 'title',
				'type'    => 'text',
				'default' => esc_html__( 'Stock status', 'woodmart' ),
				'name'    => esc_html__( 'Title', 'woodmart' ),
			),

			array(
				'id'      => 'instock',
				'type'    => 'checkbox',
				'default' => 1,
				'name'    => esc_html__( 'In Stock filter', 'woodmart' ),
			),

			array(
				'id'      => 'onsale',
				'type'    => 'checkbox',
				'default' => 1,
				'name'    => esc_html__( 'On Sale filter', 'woodmart' ),
			),

			array(
				'id'      => 'onbackorder',
				'type'    => 'checkbox',
				'default' => 0,
				'name'    => esc_html__( 'On backorder filter', 'woodmart' ),
			),
		);

		$this->create_widget( $args );
		$this->hooks();
	}

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_action( 'woocommerce_product_query', array( $this, 'show_in_stock_products' ) );
		add_filter( 'loop_shop_post_in', array( $this, 'show_on_sale_products' ) );
	}

	/**
	 * Show in stock products.
	 *
	 * @param object $query Query.
	 */
	public function show_in_stock_products( $query ) {
		$current_stock_status = isset( $_GET['stock_status'] ) ? explode( ',', $_GET['stock_status'] ) : array(); //phpcs:ignore

		if ( in_array( 'instock', $current_stock_status, true ) || in_array( 'onbackorder', $current_stock_status, true ) ) {
			$meta_query = array(
				'relation' => 'AND',
			);

			if ( in_array( 'instock', $current_stock_status, true ) ) {
				$meta_query[] = array(
					'key'     => '_stock_status',
					'value'   => 'instock',
					'compare' => '=',
				);
			}

			if ( in_array( 'onbackorder', $current_stock_status, true ) ) {
				$meta_query[] = array(
					'key'     => '_stock_status',
					'value'   => 'onbackorder',
					'compare' => '=',
				);
			}

			$query->set( 'meta_query', array_merge( WC()->query->get_meta_query(), $meta_query ) );
		}

		if ( in_array( 'onsale', $current_stock_status, true ) ) {
			$product_ids_on_sale = wc_get_product_ids_on_sale();

			if ( empty( $product_ids_on_sale ) ) {
				$query->set( 'post__in', array( 0 ) );
			}
		}
	}

	/**
	 * Show on sale products.
	 *
	 * @param array $ids IDs.
	 * @return array
	 */
	public function show_on_sale_products( $ids ) {
		$current_stock_status = isset( $_GET['stock_status'] ) ? explode( ',', $_GET['stock_status'] ) : array(); // phpcs:ignore WordPress.Security

		if ( in_array( 'onsale', $current_stock_status, true ) ) {
			$ids = array_merge( $ids, wc_get_product_ids_on_sale() );
		}

		return $ids;
	}

	/**
	 * Get link.
	 *
	 * @param string $status Status.
	 * @return string
	 */
	public function get_link( $status ) {
		$base_link            = woodmart_shop_page_link( true );
		$link                 = remove_query_arg( 'stock_status', $base_link );
		$current_stock_status = isset( $_GET['stock_status'] ) ? explode( ',', $_GET['stock_status'] ) : array(); // phpcs:ignore WordPress.Security
		$option_is_set        = in_array( $status, $current_stock_status, true );

		if ( ! in_array( $status, $current_stock_status, true ) ) {
			$current_stock_status[] = $status;
		}

		foreach ( $current_stock_status as $key => $value ) {
			if ( $option_is_set && $value === $status ) {
				unset( $current_stock_status[ $key ] );
			}
		}

		if ( $current_stock_status ) {
			asort( $current_stock_status );
			$link = add_query_arg( 'stock_status', implode( ',', $current_stock_status ), $link );
			$link = str_replace( '%2C', ',', $link );
		}

		return $link;
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

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance );

		if ( $title ) {
			echo wp_kses_post( $before_title ) . $title . wp_kses_post( $after_title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		$current_stock_status = isset( $_GET['stock_status'] ) ? explode( ',', $_GET['stock_status'] ) : array(); // phpcs:ignore WordPress.Security

		woodmart_enqueue_inline_style( 'woo-mod-widget-checkboxes' );
		?>
		<ul class="wd-checkboxes-on">
			<?php if ( $instance['onsale'] ) : ?>
				<li class="<?php echo in_array( 'onsale', $current_stock_status, true ) ? 'wd-active' : ''; ?>">
					<a href="<?php echo esc_url( $this->get_link( 'onsale' ) ); ?>" rel="nofollow noopener">
						<?php esc_html_e( 'On sale', 'woodmart' ); ?>
					</a>
				</li>
			<?php endif; ?>

			<?php if ( $instance['instock'] ) : ?>
				<li class="<?php echo in_array( 'instock', $current_stock_status, true ) ? 'wd-active' : ''; ?>">
					<a href="<?php echo esc_url( $this->get_link( 'instock' ) ); ?>" rel="nofollow noopener">
						<?php esc_html_e( 'In stock', 'woodmart' ); ?>
					</a>
				</li>
			<?php endif; ?>

			<?php if ( isset( $instance['onbackorder'] ) && $instance['onbackorder'] ) : ?>
				<li class="<?php echo in_array( 'onbackorder', $current_stock_status, true ) ? 'wd-active' : ''; ?>">
					<a href="<?php echo esc_url( $this->get_link( 'onbackorder' ) ); ?>" rel="nofollow noopener">
						<?php esc_html_e( 'On backorder', 'woodmart' ); ?>
					</a>
				</li>
			<?php endif; ?>
		</ul>
		<?php

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
