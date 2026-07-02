<?php
/**
 * Loop Product Rating block assets.
 *
 * @package woodmart
 */

use XTS\Gutenberg\Block_CSS;

$block_css = new Block_CSS( $attrs );

$block_css->add_css_rules(
	$block_selector . ' .star-rating',
	array(
		array(
			'attr_name' => 'size',
			'template'  => 'font-size: {{value}}px;',
		),
	)
);

$block_css->add_css_rules(
	$block_selector,
	array(
		array(
			'attr_name' => 'textAlign',
			'template'  => '--wd-align: var(--wd-{{value}});',
		),
		array(
			'attr_name' => 'filledColorCode',
			'template'  => '--wd-star-color: {{value}};',
		),
		array(
			'attr_name' => 'filledColorVariable',
			'template'  => '--wd-star-color: var({{value}});',
		),
		array(
			'attr_name' => 'emptyColorCode',
			'template'  => '--wd-empty-star-color: {{value}};',
		),
		array(
			'attr_name' => 'emptyColorVariable',
			'template'  => '--wd-empty-star-color: var({{value}});',
		),
	)
);

$block_css->add_css_rules(
	$block_selector . ' .star-rating',
	array(
		array(
			'attr_name' => 'sizeTablet',
			'template'  => 'font-size: {{value}}px;',
		),
	),
	'tablet'
);

$block_css->add_css_rules(
	$block_selector,
	array(
		array(
			'attr_name' => 'textAlignTablet',
			'template'  => '--wd-align: var(--wd-{{value}});',
		),
	),
	'tablet'
);

$block_css->add_css_rules(
	$block_selector . ' .star-rating',
	array(
		array(
			'attr_name' => 'sizeMobile',
			'template'  => 'font-size: {{value}}px;',
		),
	),
	'mobile'
);

$block_css->add_css_rules(
	$block_selector,
	array(
		array(
			'attr_name' => 'textAlignMobile',
			'template'  => '--wd-align: var(--wd-{{value}});',
		),
	),
	'mobile'
);

if ( ! empty( $attrs['design'] ) && 'compact' === $attrs['design'] ) {
	$block_css->add_css_rules(
		$block_selector . ' .star-rating > div',
		array(
			array(
				'attr_name' => 'textColorCode',
				'template'  => 'color: {{value}};',
			),
			array(
				'attr_name' => 'textColorVariable',
				'template'  => 'color: var({{value}});',
			),
		)
	);

	$block_css->merge_with( wd_get_block_typography_css( $block_selector . ' .star-rating > div', $attrs, 'textTp' ) );
}

if ( ! empty( $attrs['show_count'] ) ) {
	$block_css->add_css_rules(
		$block_selector . ' .woocommerce-review-link',
		array(
			array(
				'attr_name' => 'countColorCode',
				'template'  => 'color: {{value}};',
			),
			array(
				'attr_name' => 'countColorVariable',
				'template'  => 'color: var({{value}});',
			),
		)
	);

	$block_css->add_css_rules(
		$block_selector . ' .woocommerce-review-link:hover',
		array(
			array(
				'attr_name' => 'countColorHoverCode',
				'template'  => 'color: {{value}};',
			),
			array(
				'attr_name' => 'countColorHoverVariable',
				'template'  => 'color: var({{value}});',
			),
		)
	);

	$block_css->merge_with( wd_get_block_typography_css( $block_selector . ' .woocommerce-review-link', $attrs, 'countTp' ) );
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
