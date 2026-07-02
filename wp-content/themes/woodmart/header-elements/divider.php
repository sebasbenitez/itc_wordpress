<?php
/**
 * Header divider element.
 *
 * @package woodmart
 */

$class  = ( $params['full_height'] ) ? 'wd-full-height' : 'whb-divider-default';
$class .= $params['css_class'] ? ' ' . $params['css_class'] : '';
$class .= ' whb-' . $id;

?>
<div class="wd-header-divider <?php echo esc_attr( $class ); ?>">
	<?php woodmart_enqueue_inline_style( 'header-divider' ); ?>
</div>
