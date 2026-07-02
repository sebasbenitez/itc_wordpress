<?php
/**
 * Single Product Add to Cart block CSS.
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
	$block_selector . ' .variations_form .woocommerce-variation-price :is(.price, del)',
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
	$block_selector . ' .variations_form .woocommerce-variation-price .price del',
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

/**
 * Add to cart styles.
 */
$add_to_cart_selector  = $block_selector . ' .single_add_to_cart_button';
$add_to_cart_css_rules = array(
	// Text color.
	array(
		'attr_name' => 'addToCartColorCode',
		'template'  => '--btn-accented-color: {{value}};',
	),
	array(
		'attr_name' => 'addToCartColorVariable',
		'template'  => '--btn-accented-color: var({{value}});',
	),
	// Text hover color.
	array(
		'attr_name' => 'addToCartColorHoverCode',
		'template'  => '--btn-accented-color-hover: {{value}};',
	),
	array(
		'attr_name' => 'addToCartColorHoverVariable',
		'template'  => '--btn-accented-color-hover: var({{value}});',
	),
	// Background color.
	array(
		'attr_name' => 'addToCartBgColorCode',
		'template'  => '--btn-accented-bgcolor: {{value}};',
	),
	array(
		'attr_name' => 'addToCartBgColorVariable',
		'template'  => '--btn-accented-bgcolor: var({{value}});',
	),
	// Background hover color.
	array(
		'attr_name' => 'addToCartBgColorHoverCode',
		'template'  => '--btn-accented-bgcolor-hover: {{value}};',
	),
	array(
		'attr_name' => 'addToCartBgColorHoverVariable',
		'template'  => '--btn-accented-bgcolor-hover: var({{value}});',
	),
);

$block_css->add_css_rules( $add_to_cart_selector, $add_to_cart_css_rules );
$block_css->merge_with( wd_get_block_typography_css( $add_to_cart_selector, $attrs, 'addToCartTp' ) );

/**
 * Buy now styles.
 */
$buy_now_selector  = $block_selector . ' .wd-buy-now-btn';
$buy_now_css_rules = array(
	// Text color.
	array(
		'attr_name' => 'buyNowColorCode',
		'template'  => '--btn-accented-color: {{value}};',
	),
	array(
		'attr_name' => 'buyNowColorVariable',
		'template'  => '--btn-accented-color: var({{value}});',
	),
	// Text hover color.
	array(
		'attr_name' => 'buyNowColorHoverCode',
		'template'  => '--btn-accented-color-hover: {{value}};',
	),
	array(
		'attr_name' => 'buyNowColorHoverVariable',
		'template'  => '--btn-accented-color-hover: var({{value}});',
	),
	// Background color.
	array(
		'attr_name' => 'buyNowBgColorCode',
		'template'  => '--btn-accented-bgcolor: {{value}};',
	),
	array(
		'attr_name' => 'buyNowBgColorVariable',
		'template'  => '--btn-accented-bgcolor: var({{value}});',
	),
	// Background hover color.
	array(
		'attr_name' => 'buyNowBgColorHoverCode',
		'template'  => '--btn-accented-bgcolor-hover: {{value}};',
	),
	array(
		'attr_name' => 'buyNowBgColorHoverVariable',
		'template'  => '--btn-accented-bgcolor-hover: var({{value}});',
	),
);

$block_css->add_css_rules( $buy_now_selector, $buy_now_css_rules );
$block_css->merge_with( wd_get_block_typography_css( $buy_now_selector, $attrs, 'buyNowTp' ) );

$block_css->merge_with( wd_get_block_typography_css( $block_selector . ' .variations_form .woocommerce-variation-price .price', $attrs, 'mainPriceTp' ) );
$block_css->merge_with( wd_get_block_typography_css( $block_selector . ' .variations_form .woocommerce-variation-price .price del', $attrs, 'oldPriceTp' ) );
$block_css->merge_with( wd_get_block_typography_css( $block_selector . ' .woocommerce-price-suffix', $attrs, 'suffixTp' ) );

$block_css->merge_with( wd_get_block_border_css( $block_selector . ' .single_add_to_cart_button', $attrs, 'addToCartBorder' ) );
$block_css->merge_with( wd_get_block_border_css( $block_selector . ' .single_add_to_cart_button:hover', $attrs, 'addToCartBorderHover' ) );
$block_css->merge_with( wd_get_block_border_css( $block_selector . ' .wd-buy-now-btn', $attrs, 'buyNowBorder' ) );
$block_css->merge_with( wd_get_block_border_css( $block_selector . ' .wd-buy-now-btn:hover', $attrs, 'buyNowBorderHover' ) );

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
