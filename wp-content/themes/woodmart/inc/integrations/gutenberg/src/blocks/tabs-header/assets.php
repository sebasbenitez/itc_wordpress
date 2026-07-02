<?php
/**
 * Assets for tabs header block.
 *
 * @package woodmart
 */

$assets = array(
	'styles'    => array( 'block-title' ),
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

if ( isset( $this->attrs['parallaxScroll'] ) ) {
	$assets['libraries'][] = 'parallax-scroll-bundle';
}

return $assets;
