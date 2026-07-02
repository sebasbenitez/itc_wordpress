<?php
/**
 * Backdrop filter attributes.
 *
 * @package woodmart
 */

/**
 * Gutenberg backdrop filter CSS.
 *
 * @package woodmart
 */

use XTS\Gutenberg\Block_CSS;

if ( ! function_exists( 'wd_get_block_backdrop_filter_css' ) ) {
	/**
	 * Get backdrop filter CSS.
	 *
	 * @param string $selector CSS selector.
	 * @param array  $attributes CSS attributes.
	 * @param string $attr_prefix Attribute prefix.
	 * @param string $rule CSS rule.
	 * @return array
	 */
	function wd_get_block_backdrop_filter_css( $selector, $attributes, $attr_prefix, $rule = 'backdrop-filter' ) {
		$block_css = new Block_CSS( $attributes );
		$filters   = array();

		if ( ! empty( $attributes[ $attr_prefix . 'Blur' ] ) ) {
			$filters[] = 'blur(' . $attributes[ $attr_prefix . 'Blur' ] . 'px)';
		}

		if (
			isset( $attributes[ $attr_prefix . 'Brightness' ] ) &&
			(
				'0' === $attributes[ $attr_prefix . 'Brightness' ] ||
				(
					! empty( $attributes[ $attr_prefix . 'Brightness' ] ) &&
					'1' !== $attributes[ $attr_prefix . 'Brightness' ]
				)
			)
		) {
			$filters[] = 'brightness(' . $attributes[ $attr_prefix . 'Brightness' ] . ')';
		}

		if (
			isset( $attributes[ $attr_prefix . 'Contrast' ] ) &&
			(
				'0' === $attributes[ $attr_prefix . 'Contrast' ] ||
				(
					! empty( $attributes[ $attr_prefix . 'Contrast' ] ) &&
					'100' !== $attributes[ $attr_prefix . 'Contrast' ]
				)
			)
		) {
			$filters[] = 'contrast(' . $attributes[ $attr_prefix . 'Contrast' ] . '%)';
		}

		if ( ! empty( $attributes[ $attr_prefix . 'Grayscale' ] ) ) {
			$filters[] = 'grayscale(' . $attributes[ $attr_prefix . 'Grayscale' ] . '%)';
		}

		if ( ! empty( $attributes[ $attr_prefix . 'HueRotate' ] ) ) {
			$filters[] = 'hue-rotate(' . $attributes[ $attr_prefix . 'HueRotate' ] . 'deg)';
		}

		if ( ! empty( $attributes[ $attr_prefix . 'Invert' ] ) ) {
			$filters[] = 'invert(' . $attributes[ $attr_prefix . 'Invert' ] . '%)';
		}

		if (
			isset( $attributes[ $attr_prefix . 'Opacity' ] ) &&
			(
				'0' === $attributes[ $attr_prefix . 'Opacity' ] ||
				(
					! empty( $attributes[ $attr_prefix . 'Opacity' ] ) &&
					'100' !== $attributes[ $attr_prefix . 'Opacity' ]
				)
			)
		) {
			$filters[] = 'opacity(' . $attributes[ $attr_prefix . 'Opacity' ] . '%)';
		}

		if (
			isset( $attributes[ $attr_prefix . 'Saturate' ] ) &&
			(
				'0' === $attributes[ $attr_prefix . 'Saturate' ] ||
				(
					! empty( $attributes[ $attr_prefix . 'Saturate' ] ) &&
					'100' !== $attributes[ $attr_prefix . 'Saturate' ]
				)
			)
		) {
			$filters[] = 'saturate(' . $attributes[ $attr_prefix . 'Saturate' ] . '%)';
		}

		if ( ! empty( $attributes[ $attr_prefix . 'Sepia' ] ) ) {
			$filters[] = 'sepia(' . $attributes[ $attr_prefix . 'Sepia' ] . '%)';
		}

		if ( $filters ) {
			$backdrop_filter = implode( ' ', $filters );

			$block_css->add_to_selector(
				$selector,
				$rule . ': ' . $backdrop_filter . ';',
			);
			$block_css->add_to_selector(
				$selector,
				'-webkit-' . $rule . ': ' . $backdrop_filter . ';',
			);
		}

		return $block_css->get_css();
	}
}
