<?php
/**
 * Stock status block CSS.
 *
 * @package woodmart
 */

use XTS\Gutenberg\Block_CSS;

$block_css = new Block_CSS( $attrs );

$block_css->merge_with( wd_get_block_typography_css( '.wd' . $block_selector . ' p', $attrs, 'tp' ) );

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
