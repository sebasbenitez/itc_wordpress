<?php
/**
 * Loop Product Title block assets.
 *
 * @package woodmart
 */

$assets = array(
	'styles'    => array(),
	'scripts'   => array(),
	'libraries' => array(),
);

if ( ! empty( $this->attrs['linesLimit'] ) ) {
	$assets['styles'][] = 'woo-opt-title-limit-builder';
}

return $assets;
