<?php
/**
 * Header text element.
 *
 * @package woodmart
 */

$classes  = ' whb-' . $id;
$classes .= $params['inline'] ? ' wd-inline' : '';
$classes .= $params['css_class'] ? ' ' . $params['css_class'] : '';

?>

<div class="wd-header-text reset-last-child<?php echo esc_html( $classes ); ?>"><?php echo do_shortcode( $params['content'] ); ?></div>
