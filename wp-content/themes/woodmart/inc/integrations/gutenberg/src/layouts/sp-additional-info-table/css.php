<?php
/**
 * Single product block additional info table CSS.
 *
 * @package woodmart
 */

use XTS\Gutenberg\Block_CSS;

$block_css = new Block_CSS( $attrs );

$block_css->add_css_rules(
	$block_selector . ' .shop_attributes',
	array(
		array(
			'attr_name' => 'columns',
			'template'  => '--wd-attr-col: {{value}};',
		),
		array(
			'attr_name' => 'columnGap',
			'template'  => '--wd-attr-h-gap: {{value}}px;',
		),
		array(
			'attr_name' => 'rowGap',
			'template'  => '--wd-attr-v-gap: {{value}}px;',
		),
		array(
			'attr_name' => 'imageWidth',
			'template'  => '--wd-attr-img-width: {{value}}px;',
		),
		array(
			'attr_name' => 'termImageWidth',
			'template'  => '--wd-term-img-width: {{value}}px;',
		),
	)
);

$block_css->add_css_rules(
	$block_selector . ' .shop_attributes',
	array(
		array(
			'attr_name' => 'columnsTablet',
			'template'  => '--wd-attr-col: {{value}};',
		),
		array(
			'attr_name' => 'columnGapTablet',
			'template'  => '--wd-attr-h-gap: {{value}}px;',
		),
		array(
			'attr_name' => 'rowGapTablet',
			'template'  => '--wd-attr-v-gap: {{value}}px;',
		),
		array(
			'attr_name' => 'imageWidthTablet',
			'template'  => '--wd-attr-img-width: {{value}}px;',
		),
		array(
			'attr_name' => 'termImageWidthTablet',
			'template'  => '--wd-term-img-width: {{value}}px;',
		),
	),
	'tablet'
);

$block_css->add_css_rules(
	$block_selector . ' .shop_attributes',
	array(
		array(
			'attr_name' => 'columnsMobile',
			'template'  => '--wd-attr-col: {{value}};',
		),
		array(
			'attr_name' => 'columnGapMobile',
			'template'  => '--wd-attr-h-gap: {{value}}px;',
		),
		array(
			'attr_name' => 'rowGapMobile',
			'template'  => '--wd-attr-v-gap: {{value}}px;',
		),
		array(
			'attr_name' => 'imageWidthMobile',
			'template'  => '--wd-attr-img-width: {{value}}px;',
		),
		array(
			'attr_name' => 'termImageWidthMobile',
			'template'  => '--wd-term-img-width: {{value}}px;',
		),
	),
	'mobile'
);

$block_css->add_css_rules(
	$block_selector . ' .shop_attributes th',
	array(
		array(
			'attr_name' => 'attrNameColorCode',
			'template'  => 'color: {{value}};',
		),
		array(
			'attr_name' => 'attrNameColorVariable',
			'template'  => 'color: var({{value}});',
		),
	)
);

$block_css->merge_with( wd_get_block_typography_css( $block_selector . ' .shop_attributes th', $attrs, 'attrNameTp' ) );

$block_css->add_css_rules(
	$block_selector . ' .shop_attributes td',
	array(
		array(
			'attr_name' => 'attrTermColorCode',
			'template'  => 'color: {{value}};',
		),
		array(
			'attr_name' => 'attrTermColorVariable',
			'template'  => 'color: var({{value}});',
		),
		array(
			'attr_name' => 'termLinkColorCode',
			'template'  => '--wd-link-color: {{value}};',
		),
		array(
			'attr_name' => 'termLinkColorVariable',
			'template'  => '--wd-link-color: var({{value}});',
		),
		array(
			'attr_name' => 'termLinkColorHoverCode',
			'template'  => '--wd-link-color-hover: {{value}};',
		),
		array(
			'attr_name' => 'termLinkColorHoverVariable',
			'template'  => '--wd-link-color-hover: var({{value}});',
		),
	)
);

if ( isset( $attrs['layout'] ) && 'inline' === $attrs['layout'] ) {
	$block_css->add_css_rules(
		$block_selector . ' .shop_attributes th',
		array(
			array(
				'attr_name' => 'attrNameColumnWidth',
				'template'  => 'width: {{value}}' . $block_css->get_units_for_attribute( 'attrNameColumnWidth' ) . ';',
			),
		)
	);

	$block_css->add_css_rules(
		$block_selector . ' .shop_attributes th',
		array(
			array(
				'attr_name' => 'attrNameColumnWidthTablet',
				'template'  => 'width: {{value}}' . $block_css->get_units_for_attribute( 'attrNameColumnWidth', 'tablet' ) . ';',
			),
		),
		'tablet'
	);

	$block_css->add_css_rules(
		$block_selector . ' .shop_attributes th',
		array(
			array(
				'attr_name' => 'attrNameColumnWidthMobile',
				'template'  => 'width: {{value}}' . $block_css->get_units_for_attribute( 'attrNameColumnWidth', 'mobile' ) . ';',
			),
		),
		'mobile'
	);
}

if ( ! isset( $attrs['style'] ) || 'bordered' === $attrs['style'] ) {
	$block_css->merge_with( wd_get_block_border_css( $block_selector . ' .shop_attributes', $attrs, 'itemsBorder', '--wd-attr-brd', '', false ) );
}

$block_css->merge_with( wd_get_block_typography_css( $block_selector . ' .shop_attributes td', $attrs, 'attrTermTp' ) );

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
