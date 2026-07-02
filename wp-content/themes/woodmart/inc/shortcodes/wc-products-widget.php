<?php
/**
 * Shortcode for Products Widget element.
 *
 * @package woodmart
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

if ( ! function_exists( 'woodmart_shortcode_products_widget' ) ) {
	/**
	 * Render products widget shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 *
	 * @return string Shortcode output.
	 */
	function woodmart_shortcode_products_widget( $atts ) {
		global $woodmart_widget_product_img_size;

		$atts = shortcode_atts(
			array(
				'title'            => '',
				'show'             => '',
				'number'           => 3,
				'include_products' => '',
				'orderby'          => 'date',
				'order'            => 'asc',
				'ids'              => '',
				'hide_free'        => 0,
				'show_hidden'      => 0,
				'images_size'      => 'woocommerce_thumbnail',
				'el_class'         => '',
				'woodmart_css_id'  => '',
				'css'              => '',
			),
			$atts
		);

		$woodmart_widget_product_img_size = $atts['images_size'];

		$class = '';

		if ( ! empty( $atts['el_class'] ) ) {
			$class .= $atts['el_class'];
		}

		if ( ! empty( $atts['woodmart_css_id'] ) ) {
			$class .= ' wd-rs-' . $atts['woodmart_css_id'];
		}

		if ( function_exists( 'vc_shortcode_custom_css_class' ) ) {
			$class .= ' ' . vc_shortcode_custom_css_class( $atts['css'] );
		}

		$output = '<div class="widget_products' . esc_attr( $class ) . '">';
		$type   = 'WC_Widget_Products';

		$args = array( 'widget_id' => uniqid() );

		ob_start();

		woodmart_enqueue_inline_style( 'widget-general' );
		woodmart_enqueue_inline_style( 'widget-product-list' );

		$add_category_order = function ( $query_args ) use ( $atts ) {
			$ids = explode( ',', $atts['ids'] );

			if ( ! empty( $ids[0] ) ) {
				$query_args['tax_query'][] = array(
					'taxonomy' => 'product_cat',
					'field'    => 'id',
					'terms'    => $ids,
				);
			}

			return $query_args;
		};

		$add_product_order = function ( $query_args ) use ( $atts ) {
			$ids = explode( ',', $atts['include_products'] );

			if ( ! empty( $ids[0] ) ) {
				$query_args['post__in']       = $ids;
				$query_args['orderby']        = 'post__in';
				$query_args['posts_per_page'] = -1;
			}

			return $query_args;
		};

		add_filter( 'woocommerce_products_widget_query_args', $add_category_order, 10 );
		add_filter( 'woocommerce_products_widget_query_args', $add_product_order, 20 );

		if ( function_exists( 'woodmart_woocommerce_installed' ) && woodmart_woocommerce_installed() ) {
			the_widget( $type, $atts, $args );
		}

		remove_filter( 'woocommerce_products_widget_query_args', $add_category_order, 10 );
		remove_filter( 'woocommerce_products_widget_query_args', $add_product_order, 20 );

		$output .= ob_get_clean();

		$output .= '</div>';

		unset( $woodmart_widget_product_img_size );

		return $output;
	}
}
