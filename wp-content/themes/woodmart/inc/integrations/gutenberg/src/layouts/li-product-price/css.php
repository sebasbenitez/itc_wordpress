<?php
/**
 * Loop Product price block CSS.
 *
 * @package woodmart
 */

use XTS\Gutenberg\Block_CSS;

$block_css = new Block_CSS( $attrs );

$block_css->add_css_rules(
	$block_selector,
	array(
		array(
			'attr_name' => 'align',
			'template'  => '--wd-align: var(--wd-{{value}});',
		),
	)
);

$block_css->add_css_rules(
	$block_selector,
	array(
		array(
			'attr_name' => 'alignTablet',
			'template'  => '--wd-align: var(--wd-{{value}});',
		),
	),
	'tablet'
);

$block_css->add_css_rules(
	$block_selector,
	array(
		array(
			'attr_name' => 'alignMobile',
			'template'  => '--wd-align: var(--wd-{{value}});',
		),
	),
	'mobile'
);

$block_css->add_css_rules(
	$block_selector . ' :is(.price, del)',
	array(
		array(
			'attr_name' => 'mainPriceTextColorCode',
			'template'  => 'color: {{value}};',
		),
		array(
			'attr_name' => 'mainPriceTextColorVariable',
			'template'  => 'color: var({{value}});',
		),
	)
);

$block_css->add_css_rules(
	$block_selector . ' .price del',
	array(
		array(
			'attr_name' => 'oldPriceTextColorCode',
			'template'  => 'color: {{value}};',
		),
		array(
			'attr_name' => 'oldPriceTextColorVariable',
			'template'  => 'color: var({{value}});',
		),
	)
);

$block_css->add_css_rules(
	$block_selector . ' .woocommerce-price-suffix',
	array(
		array(
			'attr_name' => 'suffixTextColorCode',
			'template'  => 'color: {{value}};',
		),
		array(
			'attr_name' => 'suffixTextColorVariable',
			'template'  => 'color: var({{value}});',
		),
	)
);

$block_css->merge_with( wd_get_block_typography_css( $block_selector . ' .price', $attrs, 'mainPriceTp' ) );
$block_css->merge_with( wd_get_block_typography_css( $block_selector . ' .price del', $attrs, 'oldPriceTp' ) );
$block_css->merge_with( wd_get_block_typography_css( $block_selector . ' .woocommerce-price-suffix', $attrs, 'suffixTp' ) );

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
