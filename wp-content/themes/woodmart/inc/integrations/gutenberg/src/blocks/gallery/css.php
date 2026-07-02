<?php
/**
 * Gallery block CSS generation.
 *
 * @package woodmart
 */

use XTS\Gutenberg\Block_CSS;

$block_css = new Block_CSS( $attrs );

$block_css->add_css_rules(
	$block_selector,
	array(
		array(
			'attr_name' => 'rounding',
			'template'  => '--wd-brd-radius: {{value}}' . $block_css->get_units_for_attribute( 'rounding' ) . ';',
		),
		array(
			'attr_name' => 'contentAlignHorizontal',
			'template'  => '--wd-justify-content: {{value}};',
		),
		array(
			'attr_name' => 'contentAlignVertical',
			'template'  => '--wd-align-items: {{value}};',
		),
	)
);

if ( ! empty( $attrs['size'] ) ) {
	if ( 'aspectRatio' === $attrs['size'] ) {
		if ( isset( $attrs['aspectRatio'] ) && 'custom' === $attrs['aspectRatio'] ) {
			$block_css->add_css_rules(
				$block_selector . ' .wd-block-image img',
				array(
					array(
						'attr_name' => 'customAspectRatio',
						'template'  => '--wd-aspect-ratio: {{value}};',
					),
				)
			);
		} else {
			$block_css->add_css_rules(
				$block_selector . ' .wd-block-image img',
				array(
					array(
						'attr_name' => 'aspectRatio',
						'template'  => '--wd-aspect-ratio: {{value}};',
					),
				)
			);
		}

		if ( isset( $attrs['aspectRatioTablet'] ) && 'custom' === $attrs['aspectRatioTablet'] ) {
			$block_css->add_css_rules(
				$block_selector . ' .wd-block-image img',
				array(
					array(
						'attr_name' => 'customAspectRatioTablet',
						'template'  => '--wd-aspect-ratio: {{value}};',
					),
				),
				'tablet'
			);
		} else {
			$block_css->add_css_rules(
				$block_selector . ' .wd-block-image img',
				array(
					array(
						'attr_name' => 'aspectRatioTablet',
						'template'  => '--wd-aspect-ratio: {{value}};',
					),
				),
				'tablet'
			);
		}

		if ( isset( $attrs['aspectRatioMobile'] ) && 'custom' === $attrs['aspectRatioMobile'] ) {
			$block_css->add_css_rules(
				$block_selector . ' .wd-block-image img',
				array(
					array(
						'attr_name' => 'customAspectRatioMobile',
						'template'  => '--wd-aspect-ratio: {{value}};',
					),
				),
				'mobile'
			);
		} else {
			$block_css->add_css_rules(
				$block_selector . ' .wd-block-image img',
				array(
					array(
						'attr_name' => 'aspectRatioMobile',
						'template'  => '--wd-aspect-ratio: {{value}};',
					),
				),
				'mobile'
			);
		}
	} elseif ( 'custom' === $attrs['size'] ) {
		$block_css->add_css_rules(
			$block_selector . ' .wd-block-image img',
			array(
				array(
					'attr_name' => 'height',
					'template'  => 'height: {{value}}' . $block_css->get_units_for_attribute( 'height' ) . ';',
				),
			)
		);

		$block_css->add_css_rules(
			$block_selector . ' .wd-block-image img',
			array(
				array(
					'attr_name' => 'heightTablet',
					'template'  => 'height: {{value}}' . $block_css->get_units_for_attribute( 'height', 'tablet' ) . ';',
				),
			),
			'tablet'
		);

		$block_css->add_css_rules(
			$block_selector . ' .wd-block-image img',
			array(
				array(
					'attr_name' => 'heightMobile',
					'template'  => 'height: {{value}}' . $block_css->get_units_for_attribute( 'height', 'mobile' ) . ';',
				),
			),
			'mobile'
		);
	}

	$block_css->add_css_rules(
		$block_selector . ' .wd-block-image img',
		array(
			array(
				'attr_name' => 'imageObjectFit',
				'template'  => 'object-fit: {{value}};',
			),
		),
	);

	if ( ! empty( $attrs['imagePosition'] ) || ! empty( $attrs['imageCustomPositionX'] ) || ! empty( $attrs['imageCustomPositionY'] ) ) {
		$block_css->add_to_selector(
			$block_selector . ' .wd-block-image img',
			'object-position:' . wd_get_gutenberg_image_position( 'desktop', $attrs, $block_css ) . ';',
		);
	}

	$block_css->add_css_rules(
		$block_selector . ' .wd-block-image img',
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
			$block_selector . ' .wd-block-image img',
			'object-position:' . wd_get_gutenberg_image_position( 'tablet', $attrs, $block_css ) . ';',
			'tablet'
		);
	}

	$block_css->add_css_rules(
		$block_selector . ' .wd-block-image img',
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
			$block_selector . ' .wd-block-image img',
			'object-position:' . wd_get_gutenberg_image_position( 'mobile', $attrs, $block_css ) . ';',
			'mobile'
		);
	}
}

$block_css->merge_with(
	wd_get_block_carousel_css(
		$block_selector,
		$attrs
	)
);

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
