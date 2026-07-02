<?php
/**
 * Menu block assets.
 *
 * @package woodmart
 */

$assets = array(
	'styles'    => array( 'el-menu' ),
	'scripts'   => array(),
	'libraries' => array(),
);

if ( ( empty( $this->attrs['design'] ) || 'horizontal' === $this->attrs['design'] ) && ! empty( $this->attrs['style'] ) ) {
	if ( 'underline' === $this->attrs['style'] ) {
		$assets['styles'][] = 'mod-nav-style-underline';
	} elseif ( in_array( $this->attrs['style'], array( 'bordered', 'separated' ), true ) ) {
		$assets['styles'][] = 'mod-nav-style-bordered-separated';
	} elseif ( 'bg' === $this->attrs['style'] ) {
		$assets['styles'][] = 'mod-nav-style-bg';
	}
}

return $assets;
