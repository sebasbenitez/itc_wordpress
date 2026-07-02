<?php
/**
 * Single Product Size Guide Button block CSS.
 *
 * @package woodmart
 */

use XTS\Gutenberg\Block_CSS;

$block_css = new Block_CSS( $attrs );

$block_css->add_css_rules(
	$block_selector . ' .wd-action-btn',
	array(
		array(
			'attr_name' => 'iconColorCode',
			'template'  => '--wd-action-icon-color: {{value}};',
		),
		array(
			'attr_name' => 'iconColorVariable',
			'template'  => '--wd-action-icon-color: var({{value}});',
		),
		array(
			'attr_name' => 'iconColorHoverCode',
			'template'  => '--wd-action-icon-color-hover: {{value}};',
		),
		array(
			'attr_name' => 'iconColorHoverVariable',
			'template'  => '--wd-action-icon-color-hover: var({{value}});',
		),
	)
);

if ( ! isset( $attrs['style'] ) || 'text' === $attrs['style'] ) {
	$block_css->add_css_rules(
		$block_selector . ' .wd-action-btn',
		array(
			array(
				'attr_name' => 'textColorCode',
				'template'  => '--wd-action-text-color: {{value}};',
			),
			array(
				'attr_name' => 'textColorVariable',
				'template'  => '--wd-action-text-color: var({{value}});',
			),
			array(
				'attr_name' => 'textColorHoverCode',
				'template'  => '--wd-action-text-color-hover: {{value}};',
			),
			array(
				'attr_name' => 'textColorHoverVariable',
				'template'  => '--wd-action-text-color-hover: var({{value}});',
			),
		)
	);

	$block_css->merge_with( wd_get_block_typography_css( $block_selector . '  .wd-action-text', $attrs, 'textTp' ) );
}

$block_css->add_css_rules(
	$block_selector . ' .wd-action-btn',
	array(
		array(
			'attr_name' => 'iconSize',
			'template'  => '--wd-action-icon-size: {{value}}px;',
		),
	)
);

$block_css->add_css_rules(
	$block_selector . ' .wd-action-btn',
	array(
		array(
			'attr_name' => 'iconSizeTablet',
			'template'  => '--wd-action-icon-size: {{value}}px;',
		),
	),
	'tablet'
);

$block_css->add_css_rules(
	$block_selector . ' .wd-action-btn',
	array(
		array(
			'attr_name' => 'iconSizeMobile',
			'template'  => '--wd-action-icon-size: {{value}}px;',
		),
	),
	'mobile'
);

$block_css->merge_with( wd_get_block_padding_css( $block_selector . ' .wd-action-btn > a', $attrs, 'linkPadding' ) );

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
