<?php
/**
 * Gallery shortcode.
 *
 * @package woodmart
 */

use XTS\Modules\Layouts\Main;
use XTS\Modules\Custom_Labels\Frontend as Labels_Frontend;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_shortcode_single_product_labels' ) ) {
	/**
	 * Gallery shortcode.
	 *
	 * @param array $settings Shortcode attributes.
	 */
	function woodmart_shortcode_single_product_labels( $settings ) {
		if ( ! woodmart_woocommerce_installed() ) {
			return '';
		}

		$default_settings = array(
			'horizontal_alignment' => 'left',
			'is_wpb'               => true,
			'wrapper_classes'      => '',
			'css'                  => '',
		);

		$settings        = wp_parse_args( $settings, $default_settings );
		$wrapper_classes = $settings['wrapper_classes'];

		if ( $settings['is_wpb'] && 'wpb' === woodmart_get_current_page_builder() ) {
			$wrapper_classes .= ' wd-wpb';
			$wrapper_classes .= apply_filters( 'vc_shortcodes_css_class', '', '', $settings );

			if ( $settings['css'] ) {
				$wrapper_classes .= ' ' . vc_shortcode_custom_css_class( $settings['css'] );
			}
		}

		Main::setup_preview();

		$output = array();

		$shape         = woodmart_get_opt( 'label_shape', 'rounded' );
		$label_classes = ' product-label';

		if ( 'rounded-sm' === $shape ) {
			$label_classes .= ' wd-shape-round-sm';
		} elseif ( 'rectangular' === $shape ) {
			$label_classes .= ' wd-shape-rect-sm';
		} elseif ( 'rounded' === $shape ) {
			$label_classes .= ' wd-shape-round';
		}

		$default_labels = Labels_Frontend::get_instance()->get_default_labels_output( $label_classes );
		$output         = array_merge( $output, $default_labels );

		$source  = isset( $settings['source'] ) ? $settings['source'] : 'all';
		$include = ! empty( $settings['include'] ) ? $settings['include'] : array();
		$exclude = ! empty( $settings['exclude'] ) ? $settings['exclude'] : array();

		if ( ! is_array( $include ) ) {
			$include = ! empty( $settings['include'] ) ? array_map( 'trim', explode( ',', $settings['include'] ) ) : array();
		}

		if ( ! is_array( $exclude ) ) {
			$exclude = ! empty( $settings['exclude'] ) ? array_map( 'trim', explode( ',', $settings['exclude'] ) ) : array();
		}

		$output = apply_filters(
			'woodmart_product_label_output',
			$output,
			array(
				'source'  => $source,
				'include' => $include,
				'exclude' => $exclude,
			)
		);

		if ( ! $output ) {
			Main::restore_preview();
			return '';
		}

		ob_start();

		woodmart_enqueue_inline_style( 'woo-mod-product-labels' );
		woodmart_enqueue_inline_style( 'woo-mod-product-labels-builder' );

		if ( $settings['is_wpb'] ) {
			woodmart_enqueue_inline_style( 'woo-single-prod-el-labels' );
		}

		if ( 'rounded' === $shape ) {
			woodmart_enqueue_inline_style( 'woo-mod-product-labels-round' );
		}

		?>
		<div class="wd-single-prod-labels product-labels<?php echo esc_attr( $wrapper_classes ); ?>">
			<?php echo implode( ' ', $output ); // phpcs:ignore ?>
		</div>
		<?php

		Main::restore_preview();

		return ob_get_clean();
	}
}
