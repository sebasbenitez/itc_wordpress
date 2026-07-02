<?php
/**
 * Single Product Labels block CSS.
 *
 * @package woodmart
 */

use XTS\Gutenberg\Block_CSS;

$block_css = new Block_CSS( $attrs );

$orientation     = isset( $attrs['orientation'] ) ? $attrs['orientation'] : 'vertical';
$horizontal_prop = 'horizontal' === $orientation ? 'justify-content' : 'align-items';

$block_css->add_css_rules(
	$block_selector,
	array(
		array(
			'attr_name' => 'horizontalAlign',
			'template'  => $horizontal_prop . ': {{value}};',
		),
		array(
			'attr_name' => 'labelsGap',
			'template'  => 'gap: {{value}}px;',
		),
	)
);

$block_css->add_css_rules(
	$block_selector,
	array(
		array(
			'attr_name' => 'horizontalAlignTablet',
			'template'  => $horizontal_prop . ': {{value}};',
		),
		array(
			'attr_name' => 'labelsGapTablet',
			'template'  => 'gap: {{value}}px;',
		),
	),
	'tablet'
);

$block_css->add_css_rules(
	$block_selector,
	array(
		array(
			'attr_name' => 'horizontalAlignMobile',
			'template'  => $horizontal_prop . ': {{value}};',
		),
		array(
			'attr_name' => 'labelsGapMobile',
			'template'  => 'gap: {{value}}px;',
		),
	),
	'mobile'
);

if ( 'horizontal' === $orientation ) {
	$block_css->add_css_rules(
		$block_selector,
		array(
			array(
				'attr_name' => 'verticalAlign',
				'template'  => 'align-items: {{value}};',
			),
			array(
				'attr_name' => 'orientation',
				'template'  => 'flex-direction: row;',
			),
		)
	);

	$block_css->add_css_rules(
		$block_selector,
		array(
			array(
				'attr_name' => 'verticalAlignTablet',
				'template'  => 'align-items: {{value}};',
			),
		),
		'tablet'
	);

	$block_css->add_css_rules(
		$block_selector,
		array(
			array(
				'attr_name' => 'verticalAlignMobile',
				'template'  => 'align-items: {{value}};',
			),
		),
		'mobile'
	);
}

$block_css->merge_with(
	wd_get_block_advanced_css(
		array(
			'selector'              => $block_selector,
			'selector_hover'        => $block_selector_hover,
			'selector_parent_hover' => $block_selector_parent_hover,
		),
		$attrs
	)
);

return $block_css->get_css_for_devices();
