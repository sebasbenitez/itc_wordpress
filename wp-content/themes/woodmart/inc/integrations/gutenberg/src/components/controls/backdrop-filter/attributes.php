<?php
/**
 * Backdrop filter attributes.
 *
 * @package woodmart
 */

if ( ! function_exists( 'wd_get_backdrop_filter_control_attrs' ) ) {
	/**
	 * Get backdrop filter control attributes.
	 *
	 * @param mixed  $attr Block attributes.
	 * @param string $attrs_prefix Attributes prefix.
	 *
	 * @return void
	 */
	function wd_get_backdrop_filter_control_attrs( $attr, $attrs_prefix = '' ) {
		$attr->add_attr(
			array(
				'blur'       => array(
					'type' => 'string',
				),
				'brightness' => array(
					'type'    => 'string',
					'default' => '1',
				),
				'contrast'   => array(
					'type'    => 'string',
					'default' => '100',
				),
				'grayscale'  => array(
					'type' => 'string',
				),
				'hueRotate'  => array(
					'type' => 'string',
				),
				'invert'     => array(
					'type' => 'string',
				),
				'opacity'    => array(
					'type'    => 'string',
					'default' => '100',
				),
				'saturate'   => array(
					'type'    => 'string',
					'default' => '100',
				),
				'sepia'      => array(
					'type' => 'string',
				),
			),
			$attrs_prefix
		);
	}
}
