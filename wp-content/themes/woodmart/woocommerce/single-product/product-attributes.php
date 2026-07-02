<?php
/**
 * Product attributes
 *
 * Used by list_attributes() in the products class.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-attributes.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @var array $product_attributes List of product attributes.
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.3.0
 */

use XTS\Modules\Layouts\Global_Data as Builder_Data;

defined( 'ABSPATH' ) || exit;

if ( ! $product_attributes ) {
	return;
}

$args = Builder_Data::get_instance()->get_data( 'wd_additional_info_table_args' );

$show_name  = true;
$show_image = true;

if ( $args ) {
	$show_name  = ! empty( $args['attr_name'] );
	$show_image = ! empty( $args['attr_image'] );
}

$output = '';

foreach ( $product_attributes as $product_attribute_key => $product_attribute ) {
	$attribute_name = str_replace( 'attribute_pa_', '', $product_attribute_key );
	$thumb_id       = get_option( 'woodmart_pa_' . $attribute_name . '_thumbnail' );
	$image_size     = apply_filters( 'woodmart_product_attributes_table_image_size', 'thumbnail' );
	$attribute_hint = get_option( 'woodmart_pa_' . $attribute_name . '_hint' );
	$has_name       = $show_image || $show_name;

	if ( ! $has_name && ! $product_attribute['value'] ) {
		continue;
	}

	if ( ! empty( Builder_Data::get_instance()->get_data( 'wd_product_attributes_include' ) ) || ! empty( Builder_Data::get_instance()->get_data( 'wd_product_attributes_exclude' ) ) ) {
		$attributes_include     = Builder_Data::get_instance()->get_data( 'wd_product_attributes_include' );
		$attributes_exclude     = Builder_Data::get_instance()->get_data( 'wd_product_attributes_exclude' );
		$current_attribute_name = str_replace( 'attribute_pa_', 'pa_', $product_attribute_key );

		if ( $attributes_include && ! in_array( $current_attribute_name, $attributes_include, true ) ) {
			continue;
		}
		if ( $attributes_exclude && in_array( $current_attribute_name, $attributes_exclude, true ) ) {
			continue;
		}
	}

	$output .= '<tr class="woocommerce-product-attributes-item woocommerce-product-attributes-item--' . esc_attr( $product_attribute_key ) . '">';

	if ( $has_name ) {
		$output .= '<th class="woocommerce-product-attributes-item__label" scope="row">';
		$output .= '<span class="wd-attr-label">';

		if ( $show_image && ! empty( $thumb_id ) ) {
			if ( woodmart_is_svg( wp_get_attachment_image_url( $thumb_id ) ) ) {
				$output .= woodmart_get_svg_html( $thumb_id, $image_size, array( 'class' => 'wd-attr-img' ) );
			} else {
				$output .= wp_get_attachment_image( $thumb_id, $image_size, false, array( 'class' => 'wd-attr-img' ) );
			}
		}

		if ( $show_name ) {
			$output .= '<span class="wd-attr-name">';
			$output .= $product_attribute['label'];
			$output .= '</span>';
		}

		if ( $attribute_hint ) {
			woodmart_enqueue_js_library( 'tooltips' );
			woodmart_enqueue_js_script( 'btns-tooltips' );

			$output .= '<span class="wd-hint wd-tooltip">';
			$output .= '<span class="wd-tooltip-content">';
			$output .= $attribute_hint;
			$output .= '</span>';
			$output .= '</span>';
		}
		$output .= '</span>';
		$output .= '</th>';
	}

	if ( $product_attribute['value'] ) {
		$output .= '<td class="woocommerce-product-attributes-item__value">';
		$output .= $product_attribute['value'];
		$output .= '</td>';
	}

	$output .= '</tr>';
}

if ( $output ) {
	echo '<table class="woocommerce-product-attributes shop_attributes" aria-label="' . esc_attr__( 'Product Details', 'woocommerce' ) . '">';
	echo wp_kses_post( $output );
	echo '</table>';
}
