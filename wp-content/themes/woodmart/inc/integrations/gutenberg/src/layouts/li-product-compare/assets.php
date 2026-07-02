<?php
/**
 * Loop Product Compare assets
 *
 * @package woodmart
 */

$assets = array(
	'styles'    => array(),
	'scripts'   => array(),
	'libraries' => array(),
);

if ( ( ! isset( $this->attrs['style'] ) || 'button' === $this->attrs['style'] ) && ! empty( $this->attrs['stretch'] ) ) {
	$assets['styles'][] = 'woo-mod-loop-prod-btn-full-width-builder';
}

if ( ! isset( $this->attrs['style'] ) || 'icon' === $this->attrs['style'] ) {
	$assets['scripts'][]   = 'btns-tooltips';
	$assets['libraries'][] = 'tooltips';
}

if ( isset( $this->attrs['style'] ) && 'button' === $this->attrs['style'] ) {
	$assets['styles'][] = 'woo-mod-loop-prod-add-btn-replace';
}

return $assets;
