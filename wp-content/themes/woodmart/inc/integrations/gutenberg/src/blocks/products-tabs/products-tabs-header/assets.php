<?php
/**
 * Product tabs header assets.
 *
 * @package woodmart
 */

$assets = array(
	'styles'    => array(),
	'scripts'   => array(),
	'libraries' => array(),
);

if ( ! empty( $this->attrs['titleStyle'] ) && 'underline' === $this->attrs['titleStyle'] ) {
	$assets['styles'][] = 'mod-nav-style-underline';
}

if ( ! empty( $this->attrs['design'] ) ) {
	if ( 'simple' === $this->attrs['design'] ) {
		$assets['styles'][] = 'tabs-style-bordered';
	} elseif ( 'alt' === $this->attrs['design'] ) {
		$assets['styles'][] = 'tabs-style-space-between';
	} elseif ( 'aside' === $this->attrs['design'] ) {
		$assets['styles'][] = 'tabs-style-aside';
	}
}

return $assets;
