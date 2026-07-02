<?php
/**
 * Loop Product Countdown block attributes.
 *
 * @package woodmart
 */

use XTS\Gutenberg\Block_Attributes;

if ( ! function_exists( 'wd_get_loop_builder_product_countdown_attrs' ) ) {
	/**
	 * Get Loop Product Countdown block attributes.
	 *
	 * @return array[]
	 */
	function wd_get_loop_builder_product_countdown_attrs() {
		$attr = new Block_Attributes();

		$attr->add_attr(
			array(
				'showTitle'             => array(
					'type' => 'boolean',
				),
				'textAlign'             => array(
					'type'       => 'string',
					'responsive' => true,
				),
				'woodmart_color_scheme' => array(
					'type' => 'string',
				),
				'size'                  => array(
					'type'    => 'string',
					'default' => 'small',
				),
				'layout'                => array(
					'type'    => 'string',
					'default' => 'block',
				),
				'labels'                => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'separator'             => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'separator_text'        => array(
					'type'    => 'string',
					'default' => ':',
				),
				'countdownGap'          => array(
					'type'       => 'number',
					'responsive' => true,
				),
				'separatorFontSize'     => array(
					'type'       => 'number',
					'responsive' => true,
				),
				'countdownMinHeight'    => array(
					'type'       => 'number',
					'responsive' => true,
				),
				'countdownMinWidth'     => array(
					'type'       => 'number',
					'responsive' => true,
				),
			)
		);

		$attr->add_attr( wd_get_typography_control_attrs(), 'numberTp' );
		$attr->add_attr( wd_get_typography_control_attrs(), 'labelsTp' );

		$attr->add_attr( wd_get_color_control_attrs( 'numberColor' ) );
		$attr->add_attr( wd_get_color_control_attrs( 'labelsColor' ) );
		$attr->add_attr( wd_get_color_control_attrs( 'separatorColor' ) );
		$attr->add_attr( wd_get_color_control_attrs( 'bgTimerColor' ) );

		wd_get_box_shadow_control_attrs( $attr, 'timerBoxShadow' );

		wd_get_border_control_attrs( $attr, 'itemsBorder' );

		wd_get_advanced_tab_attrs( $attr );

		return $attr->get_attr();
	}
}
