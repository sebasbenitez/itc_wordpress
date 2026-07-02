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

if ( ! function_exists( 'woodmart_shortcode_single_product_label' ) ) {
	/**
	 * Single product label shortcode.
	 *
	 * @param array $settings Shortcode attributes.
	 */
	function woodmart_shortcode_single_product_label( $settings ) {
		if ( ! woodmart_woocommerce_installed() ) {
			return '';
		}

		$default_settings = array(
			'label_id'        => '',
			'alignment'       => 'left',
			'is_wpb'          => true,
			'wrapper_classes' => '',
			'label_type'      => '',
			'css'             => '',
		);

		$settings = wp_parse_args( $settings, $default_settings );

		$wrapper_classes = $settings['wrapper_classes'];

		if ( $settings['is_wpb'] && 'wpb' === woodmart_get_current_page_builder() ) {
			$wrapper_classes .= ' wd-wpb';
			$wrapper_classes .= apply_filters( 'vc_shortcodes_css_class', '', '', $settings );

			if ( $settings['css'] ) {
				$wrapper_classes .= ' ' . vc_shortcode_custom_css_class( $settings['css'] );
			}
		}

		Main::setup_preview();

		$content       = '';
		$shape         = woodmart_get_opt( 'label_shape' );
		$label_classes = ' product-label';

		if ( 'rounded-sm' === $shape ) {
			$label_classes .= ' wd-shape-round-sm';
		} elseif ( 'rectangular' === $shape ) {
			$label_classes .= ' wd-shape-rect-sm';
		} elseif ( 'rounded' === $shape ) {
			$label_classes .= ' wd-shape-round';
		}

		$content .= Labels_Frontend::get_instance()->get_default_label_html( $settings['label_id'], $label_classes );

		if ( woodmart_get_opt( 'custom_labels' ) && ! in_array( $settings['label_id'], array( 'sale', 'out-of-stock', 'hot', 'new' ), true ) ) {
			$label_id = apply_filters( 'wpml_object_id', $settings['label_id'], 'wd_custom_label', true );
			$label    = get_post( $label_id );

			if ( ! $label || ! $label_id || 'publish' !== $label->post_status ) {
				return '';
			}

			$ids_to_show = Labels_Frontend::get_instance()->manager->get_current_custom_labels_ids();
			if ( ! in_array( (int) $label_id, $ids_to_show, true ) ) {
				return '';
			}

			$label_classes = Labels_Frontend::get_instance()->get_classes( $label_id );

			$content  = '<div class="' . esc_attr( $label_classes ) . '">';
			$content .= Labels_Frontend::get_instance()->get_content( $label_id );
			$content .= '</div>';
		}

		if ( ! $content ) {
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
		<div class="wd-single-prod-label<?php echo esc_attr( $wrapper_classes ); ?>">
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<?php

		Main::restore_preview();

		return ob_get_clean();
	}
}
