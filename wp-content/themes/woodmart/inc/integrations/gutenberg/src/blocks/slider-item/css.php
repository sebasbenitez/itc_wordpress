<?php
/**
 * Slider item block CSS generation.
 *
 * @package woodmart
 */

use XTS\Gutenberg\Block_CSS;

$block_css = new Block_CSS( $attrs );

$block_css->add_css_rules(
	$block_selector . ' .wd-slide-container',
	array(
		array(
			'attr_name' => 'blockGap',
			'template'  => '--wd-row-gap: {{value}}px;',
		),
		array(
			'attr_name' => 'justify',
			'template'  => '--wd-justify-content: {{value}};',
		),
		array(
			'attr_name' => 'align',
			'template'  => '--wd-align-items: {{value}};',
		),
	)
);

$block_css->add_css_rules(
	$block_selector . ' .wd-slide-bg img',
	array(
		array(
			'attr_name' => 'imageObjectFit',
			'template'  => 'object-fit: {{value}};',
		),
	)
);

if ( ! empty( $attrs['imagePosition'] ) || ! empty( $attrs['imageCustomPositionX'] ) || ! empty( $attrs['imageCustomPositionY'] ) ) {
	$block_css->add_to_selector(
		$block_selector . ' .wd-slide-bg img',
		'object-position:' . wd_get_gutenberg_image_position( 'desktop', $attrs, $block_css ) . ';',
	);
}

if ( ! empty( $attrs['aspectRatio'] ) && 'asImage' === $attrs['aspectRatio'] ) {
	$block_css->add_css_rules(
		$block_selector . '.wd-slide',
		array(
			array(
				'attr_name' => 'imageAspectRatio',
				'template'  => '--wd-aspect-ratio: {{value}};',
			),
		)
	);

	$block_css->add_css_rules(
		$block_selector . '.wd-slide',
		array(
			array(
				'attr_name' => 'imageAspectRatioTablet',
				'template'  => '--wd-aspect-ratio: {{value}};',
			),
		),
		'tablet'
	);

	$block_css->add_css_rules(
		$block_selector . '.wd-slide',
		array(
			array(
				'attr_name' => 'imageAspectRatioMobile',
				'template'  => '--wd-aspect-ratio: {{value}};',
			),
		),
		'mobile'
	);
}

$block_css->add_css_rules(
	$block_selector . ' .wd-slide-container',
	array(
		array(
			'attr_name' => 'blockGapTablet',
			'template'  => '--wd-row-gap: {{value}}px;',
		),
		array(
			'attr_name' => 'justifyTablet',
			'template'  => '--wd-justify-content: {{value}};',
		),
		array(
			'attr_name' => 'alignTablet',
			'template'  => '--wd-align-items: {{value}};',
		),
	),
	'tablet'
);

$block_css->add_css_rules(
	$block_selector . ' .wd-slide-bg img',
	array(
		array(
			'attr_name' => 'imageObjectFitTablet',
			'template'  => 'object-fit: {{value}};',
		),
	),
	'tablet'
);

if ( ! empty( $attrs['imagePositionTablet'] ) || ! empty( $attrs['imageCustomPositionXTablet'] ) || ! empty( $attrs['imageCustomPositionYTablet'] ) ) {
	$block_css->add_to_selector(
		$block_selector . ' .wd-slide-bg img',
		'object-position:' . wd_get_gutenberg_image_position( 'tablet', $attrs, $block_css ) . ';',
		'tablet'
	);
}

$block_css->add_css_rules(
	$block_selector . ' .wd-slide-container',
	array(
		array(
			'attr_name' => 'blockGapMobile',
			'template'  => '--wd-row-gap: {{value}}px;',
		),
		array(
			'attr_name' => 'justifyMobile',
			'template'  => '--wd-justify-content: {{value}};',
		),
		array(
			'attr_name' => 'alignMobile',
			'template'  => '--wd-align-items: {{value}};',
		),
	),
	'mobile'
);

$block_css->add_css_rules(
	$block_selector . ' .wd-slide-bg img',
	array(
		array(
			'attr_name' => 'imageObjectFitMobile',
			'template'  => 'object-fit: {{value}};',
		),
	),
	'mobile'
);

if ( ! empty( $attrs['imagePositionMobile'] ) || ! empty( $attrs['imageCustomPositionXMobile'] ) || ! empty( $attrs['imageCustomPositionYMobile'] ) ) {
	$block_css->add_to_selector(
		$block_selector . ' .wd-slide-bg img',
		'object-position:' . wd_get_gutenberg_image_position( 'mobile', $attrs, $block_css ) . ';',
		'mobile'
	);
}

$block_css->merge_with(
	wd_get_block_advanced_css(
		array(
			'selector'                 => $block_selector,
			'selector_hover'           => $block_selector_hover,
			'selector_parent_hover'    => $block_selector_parent_hover,
			'selector_bg'              => $block_selector . ' .wd-slide-bg',
			'selector_bg_hover'        => $block_selector_hover . ' .wd-slide-bg',
			'selector_bg_parent_hover' => $block_selector_parent_hover . ' .wd-slide-bg',
		),
		$attrs
	)
);

return $block_css->get_css_for_devices();
