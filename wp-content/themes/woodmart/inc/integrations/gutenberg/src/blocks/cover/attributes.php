<?php

use XTS\Gutenberg\Block_Attributes;

if ( ! function_exists( 'wd_get_block_cover_attrs' ) ) {
	function wd_get_block_cover_attrs() {
		$attr = new Block_Attributes();

		$attr->add_attr(
			array(
				'size'        => array(
					'type'    => 'string',
					'default' => 'custom',
				),
				'aspectRatio' => array(
					'type'       => 'string',
					'default'    => '4/3',
					'responsive' => true,
				),
			)
		);

		return $attr->get_attr();
	}
}
