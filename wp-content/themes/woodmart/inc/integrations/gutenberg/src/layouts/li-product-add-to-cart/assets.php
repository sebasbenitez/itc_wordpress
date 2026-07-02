<?php
/**
 * Loop Product Add to Cart Block assets.
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

if ( ! isset( $this->attrs['style'] ) || 'button' === $this->attrs['style'] ) {
	$assets['styles'][] = 'woo-mod-loop-prod-add-btn-replace';

	if ( ! empty( $this->attrs['show_quantity'] ) && ! empty( $this->attrs['quantity_overlap'] ) ) {
		$assets['styles'][] = 'woo-mod-quantity-overlap';
	}
}

if ( isset( $this->attrs['style'] ) && 'icon' === $this->attrs['style'] ) {
	$assets['scripts'][]   = 'btns-tooltips';
	$assets['libraries'][] = 'tooltips';
}

return $assets;
