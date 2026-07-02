<?php
/**
 * Shortcode for Info box element in header builder.
 *
 * @package woodmart
 */

$params['source'] = 'header';

if ( isset( $id ) ) {
	$params['wrapper_classes'] = ' whb-' . $id;
}

foreach ( array( 'image', 'bg_image_box', 'bg_hover_image' ) as $key ) {
	if ( isset( $params[ $key ]['id'] ) ) {
		$params[ $key ] = $params[ $key ]['id'];
	}
}

if ( ! empty( $params['link']['blank'] ) ) {
	$params['link']['target'] = '_blank';
}

// Remove value for control with css generators.
$params['bg_hover_color']           = '';
$params['icon_text_color']          = '';
$params['icon_bg_color']            = '';
$params['icon_bg_hover_color']      = '';
$params['icon_border_color']        = '';
$params['icon_border_hover_color']  = '';
$params['icon_text_color']          = '';
$params['subtitle_font_weight']     = '';
$params['subtitle_custom_bg_color'] = '';
$params['subtitle_custom_color']    = '';
$params['title_color']              = '';
$params['title_font_weight']        = '';
$params['custom_text_color']        = '';

echo woodmart_shortcode_info_box( $params, $params['content'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
