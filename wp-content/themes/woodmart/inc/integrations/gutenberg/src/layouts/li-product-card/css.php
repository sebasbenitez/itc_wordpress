<?php
/**
 * Loop Product Card CSS.
 *
 * @package woodmart
 */

use XTS\Gutenberg\Block_CSS;

$block_css = new Block_CSS( $attrs );

$block_css->merge_with( wd_get_block_bg_css( $block_selector . ' .wd-product-card-bg', $attrs, 'bg' ) );
$block_css->merge_with( wd_get_block_backdrop_filter_css( $block_selector . ' .wd-product-card-bg', $attrs, 'backdropFilter' ) );
$block_css->merge_with( wd_get_block_border_css( $block_selector . ' .wd-product-card-bg', $attrs, 'border' ) );
$block_css->merge_with( wd_get_block_box_shadow_css( $block_selector . ' .wd-product-card-bg', $attrs, 'boxShadow' ) );

$block_css->merge_with(
	wd_get_block_padding_css(
		$block_selector,
		$attrs,
		'padding',
		array(
			'top'    => '--wd-prod-card-pt',
			'right'  => '--wd-prod-card-pr',
			'bottom' => '--wd-prod-card-pb',
			'left'   => '--wd-prod-card-pl',
		)
	)
);

return $block_css->get_css_for_devices();
