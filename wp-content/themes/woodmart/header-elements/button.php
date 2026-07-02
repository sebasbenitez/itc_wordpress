<?php
/**
 * Shortcode for Button element in header builder.
 *
 * @package woodmart
 */

if ( ! empty( $params['link']['blank'] ) ) {
	$params['link']['target'] = '_blank';
}

if ( ! empty( $params['button_smooth_scroll'] ) ) {
	$params['button_smooth_scroll'] = 'yes';
}
if ( ! empty( $params['full_width'] ) ) {
	$params['full_width'] = 'yes';
}
if ( ! empty( $params['button_inline'] ) ) {
	$params['button_inline'] = 'yes';
}

if ( ! empty( $params['image'] ) ) {
	$params['icon_type'] = 'image';
}

if ( isset( $id ) ) {
	$params['wrapper_class'] = 'whb-' . $id;
}

$params['generate_css'] = false;

echo woodmart_shortcode_button( $params ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
