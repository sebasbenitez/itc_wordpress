<?php
/**
 * The social template.
 *
 * @package woodmart
 */

if ( isset( $id ) ) {
	$params['el_class'] = ' whb-' . $id;
}

$params['style'] = ( ! $params['style'] ) ? 'default' : $params['style'];

echo woodmart_shortcode_social( $params ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
