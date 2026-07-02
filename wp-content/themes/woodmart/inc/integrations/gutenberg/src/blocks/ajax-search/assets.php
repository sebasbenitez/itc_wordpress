<?php
/**
 * Assets for ajax search block.
 *
 * @package woodmart
 */

$assets = array(
	'styles'    => array(),
	'scripts'   => array(),
	'libraries' => array(),
);

if ( ! empty( $this->attrs['woodmart_color_scheme'] ) ) {
	$assets['styles'][] = 'search-opt-color-scheme';
}

return $assets;
