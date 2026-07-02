<?php
/**
 * CSS for Product Categories block.
 *
 * @package woodmart
 */

use XTS\Gutenberg\Block_CSS;

$block_css = new Block_CSS( $attrs );

$block_css->add_css_rules(
	$block_selector,
	array(
		array(
			'attr_name' => 'custom_rounding_size',
			'template'  => '--wd-cat-brd-radius: {{value}}' . $block_css->get_units_for_attribute( 'custom_rounding_size' ) . ';',
		),
	)
);

$block_css->add_css_rules(
	$block_selector,
	array(
		array(
			'attr_name' => 'image_container_width',
			'template'  => '--wd-cat-img-width: {{value}}' . $block_css->get_units_for_attribute( 'image_container_width' ) . ';',
		),
	)
);

$block_css->add_css_rules(
	$block_selector,
	array(
		array(
			'attr_name' => 'image_container_widthTablet',
			'template'  => '--wd-cat-img-width: {{value}}' . $block_css->get_units_for_attribute( 'image_container_width', 'tablet' ) . ';',
		),
	),
	'tablet'
);

$block_css->add_css_rules(
	$block_selector,
	array(
		array(
			'attr_name' => 'image_container_widthMobile',
			'template'  => '--wd-cat-img-width: {{value}}' . $block_css->get_units_for_attribute( 'image_container_width', 'mobile' ) . ';',
		),
	),
	'mobile'
);

$block_css->add_css_rules(
	$block_selector . ' .wd-products-with-bg, ' . $block_selector . ' .wd-products-with-bg .wd-cat, ' . $block_selector . '.wd-products-with-bg, ' . $block_selector . '.wd-products-with-bg .wd-cat',
	array(
		array(
			'attr_name' => 'categoriesBackgroundCode',
			'template'  => '--wd-prod-bg: {{value}};--wd-bordered-bg: {{value}};',
		),
		array(
			'attr_name' => 'categoriesBackgroundVariable',
			'template'  => '--wd-prod-bg: var({{value}});--wd-bordered-bg: var({{value}});',
		),
	)
);

$block_css->add_css_rules(
	$block_selector . ' [class*="products-bordered-grid"], ' . $block_selector . ' [class*="products-bordered-grid"] .wd-cat,' . $block_selector . '[class*="products-bordered-grid"], ' . $block_selector . '[class*="products-bordered-grid"] .wd-cat',
	array(
		array(
			'attr_name' => 'categoriesBorderColorCode',
			'template'  => '--wd-bordered-brd: {{value}};',
		),
		array(
			'attr_name' => 'categoriesBorderColorVariable',
			'template'  => '--wd-prod-bg: var({{value}});',
		),
	)
);

if ( isset( $attrs['type'] ) && 'navigation' === $attrs['type'] ) {
	$block_css->add_css_rules(
		$block_selector,
		array(
			array(
				'attr_name' => 'navAlignment',
				'template'  => '--wd-align: var(--wd-{{value}});',
			),
		)
	);

	$block_css->add_css_rules(
		$block_selector . ' .wd-nav-product-cat',
		array(
			array(
				'attr_name' => 'titleIdleColorCode',
				'template'  => '--nav-color: {{value}};',
			),
			array(
				'attr_name' => 'titleIdleColorVariable',
				'template'  => '--nav-color: var({{value}});',
			),
		)
	);

	$block_css->add_css_rules(
		$block_selector . ' .wd-nav-product-cat',
		array(
			array(
				'attr_name' => 'titleHoverColorCode',
				'template'  => '--nav-color-hover: {{value}};',
			),
			array(
				'attr_name' => 'titleHoverColorVariable',
				'template'  => '--nav-color-hover: var({{value}});',
			),
		)
	);

	$block_css->add_css_rules(
		$block_selector . ' .wd-nav-product-cat',
		array(
			array(
				'attr_name' => 'navItemsBgColorCode',
				'template'  => '--nav-bg: {{value}};',
			),
			array(
				'attr_name' => 'navItemsBgColorVariable',
				'template'  => '--nav-bg: var({{value}});',
			),
		)
	);

	if ( isset( $attrs['product_count'] ) && $attrs['product_count'] ) {
		$block_css->add_css_rules(
			$block_selector . ' .wd-nav-product-cat > li > a .nav-link-count',
			array(
				array(
					'attr_name' => 'countIdleColorCode',
					'template'  => 'color: {{value}};',
				),
				array(
					'attr_name' => 'countIdleColorVariable',
					'template'  => 'color: var({{value}});',
				),
			)
		);

		$block_css->add_css_rules(
			$block_selector . ' .wd-nav-product-cat > li:hover > a .nav-link-count',
			array(
				array(
					'attr_name' => 'countHoverColorCode',
					'template'  => 'color: {{value}};',
				),
				array(
					'attr_name' => 'countHoverColorVariable',
					'template'  => 'color: var({{value}});',
				),
			)
		);

		if ( ! isset( $attrs['navDisableActiveStyle'] ) || ! $attrs['navDisableActiveStyle'] ) {
			$block_css->add_css_rules(
				$block_selector . ' .wd-nav-product-cat > li.wd-active > a .nav-link-count',
				array(
					array(
						'attr_name' => 'countActiveColorCode',
						'template'  => 'color: {{value}};',
					),
					array(
						'attr_name' => 'countActiveColorVariable',
						'template'  => 'color: var({{value}});',
					),
				)
			);
		}
	}

	$block_css->add_css_rules(
		$block_selector . ' .wd-nav-product-cat',
		array(
			array(
				'attr_name' => 'navItemsBgHoverColorCode',
				'template'  => '--nav-bg-hover: {{value}};',
			),
			array(
				'attr_name' => 'navItemsBgHoverColorVariable',
				'template'  => '--nav-bg-hover: var({{value}});',
			),
		)
	);

	if ( ! isset( $attrs['navDisableActiveStyle'] ) || ! $attrs['navDisableActiveStyle'] ) {
		$block_css->add_css_rules(
			$block_selector . ' .wd-nav-product-cat',
			array(
				array(
					'attr_name' => 'titleActiveColorCode',
					'template'  => '--nav-color-active: {{value}};',
				),
				array(
					'attr_name' => 'titleActiveColorVariable',
					'template'  => '--nav-color-active: var({{value}});',
				),
				array(
					'attr_name' => 'navItemsBgActiveColorCode',
					'template'  => '--nav-bg-active: {{value}};',
				),
				array(
					'attr_name' => 'navItemsBgActiveColorVariable',
					'template'  => '--nav-bg-active: var({{value}});',
				),
			)
		);
	}

	$block_css->add_css_rules(
		$block_selector . ' .wd-nav-product-cat > li > a .wd-nav-img',
		array(
			array(
				'attr_name' => 'iconWidth',
				'template'  => '--nav-img-width: {{value}}px;',
			),
			array(
				'attr_name' => 'iconHeight',
				'template'  => '--nav-img-height: {{value}}px;',
			),
		)
	);

	$block_css->add_css_rules(
		$block_selector,
		array(
			array(
				'attr_name' => 'navAlignmentTablet',
				'template'  => '--wd-align: var(--wd-{{value}});',
			),
		),
		'tablet'
	);

	$block_css->add_css_rules(
		$block_selector . ' .wd-nav-product-cat > li > a .wd-nav-img',
		array(
			array(
				'attr_name' => 'iconWidthTablet',
				'template'  => '--nav-img-width: {{value}}px;',
			),
			array(
				'attr_name' => 'iconHeightTablet',
				'template'  => '--nav-img-height: {{value}}px;',
			),
		),
		'tablet'
	);

	$block_css->add_css_rules(
		$block_selector,
		array(
			array(
				'attr_name' => 'navAlignmentMobile',
				'template'  => '--wd-align: var(--wd-{{value}});',
			),
		),
		'mobile'
	);

	$block_css->add_css_rules(
		$block_selector . ' .wd-nav-product-cat > li > a .wd-nav-img',
		array(
			array(
				'attr_name' => 'iconWidthMobile',
				'template'  => '--nav-img-width: {{value}}px;',
			),
			array(
				'attr_name' => 'iconHeightMobile',
				'template'  => '--nav-img-height: {{value}}px;',
			),
		),
		'mobile'
	);

	if ( 'custom' === $attrs['navItemsGap'] ) {
		$block_css->add_css_rules(
			$block_selector . ' .wd-nav-product-cat',
			array(
				// Gap.
				array(
					'attr_name' => 'navCustomItemsGap',
					'template'  => '--nav-gap: {{value}}px;',
				),
			)
		);

		$block_css->add_css_rules(
			$block_selector . ' .wd-nav-product-cat',
			array(
				// Gap.
				array(
					'attr_name' => 'navCustomItemsGapTablet',
					'template'  => '--nav-gap: {{value}}px;',
				),
			),
			'tablet'
		);

		$block_css->add_css_rules(
			$block_selector . ' .wd-nav-product-cat',
			array(
				// Gap.
				array(
					'attr_name' => 'navCustomItemsGapMobile',
					'template'  => '--nav-gap: {{value}}px;',
				),
			),
			'mobile'
		);
	}

	$block_css->merge_with(
		wd_get_block_typography_css(
			$block_selector . ' .wd-nav-product-cat > li > a',
			$attrs,
			'navItemTp'
		)
	);
}

$block_css->merge_with(
	wd_get_block_carousel_css(
		$block_selector,
		$attrs
	)
);

if ( isset( $attrs['type'] ) && 'grid' === $attrs['type'] ) {
	$block_css->merge_with( wd_get_block_typography_css( $block_selector . ' div.product-category .wd-entities-title', $attrs, 'title' ) );
}

$block_css->merge_with(
	wd_get_block_box_shadow_css(
		$block_selector . ' .wd-nav-product-cat',
		$attrs,
		'navItemsBoxShadow',
		'--nav-shadow'
	)
);

$block_css->merge_with(
	wd_get_block_box_shadow_css(
		$block_selector . ' .wd-nav-product-cat',
		$attrs,
		'navItemsBoxShadowHover',
		'--nav-shadow-hover'
	)
);

$block_css->merge_with(
	wd_get_block_border_css(
		$block_selector . ' .wd-nav-product-cat',
		$attrs,
		'navItemsBorder',
		'--nav-border',
		'--nav-radius'
	)
);

$block_css->merge_with(
	wd_get_block_border_css(
		$block_selector . ' .wd-nav-product-cat',
		$attrs,
		'navItemsBorderHover',
		'--nav-border-hover',
		'--nav-radius-hover'
	)
);

$block_css->merge_with(
	wd_get_block_padding_css(
		$block_selector . ' .wd-nav-product-cat',
		$attrs,
		'navItemsPadding',
		'--nav-pd',
		true
	)
);

if ( ! isset( $attrs['navDisableActiveStyle'] ) || ! $attrs['navDisableActiveStyle'] ) {
	$block_css->merge_with(
		wd_get_block_box_shadow_css(
			$block_selector . ' .wd-nav-product-cat',
			$attrs,
			'navItemsBoxShadowActive',
			'--nav-shadow-active'
		)
	);

	$block_css->merge_with(
		wd_get_block_border_css(
			$block_selector . ' .wd-nav-product-cat',
			$attrs,
			'navItemsBorderActive',
			'--nav-border-active',
			'--nav-radius-active'
		)
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
