<?php
/**
 * Shortcode for Row Divider element.
 *
 * @package woodmart
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

if ( ! function_exists( 'woodmart_row_divider' ) ) {
	/**
	 * Shortcode to display row divider.
	 *
	 * @param array $atts Shortcode attributes.
	 *
	 * @return string
	 */
	function woodmart_row_divider( $atts ) {
		extract( // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
			shortcode_atts(
				array(
					'position'        => 'top',
					'color'           => '#e1e1e1',
					'style'           => 'waves-small',
					'content_overlap' => '',
					'custom_height'   => '',
					'el_class'        => '',
					'woodmart_css_id' => '',
				),
				$atts
			)
		);

		if ( ! $woodmart_css_id ) {
			$woodmart_css_id = uniqid();
		}
		$divider_id = 'wd-' . $woodmart_css_id;

		$classes  = $divider_id;
		$classes .= ' dvr-position-' . $position;
		$classes .= ' dvr-style-' . $style;

		( 'enable' === $content_overlap ) ? $classes .= ' dvr-overlap-enable' : false;
		( '' !== $el_class ) ? $classes              .= ' ' . $el_class : false;

		ob_start();

		woodmart_enqueue_inline_style( 'dividers' );
		?>
			<div id="<?php echo esc_attr( $divider_id ); ?>" class="wd-row-divider <?php echo esc_attr( $classes ); ?>">
				<?php echo woodmart_get_svg_content( $style . '-' . $position ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php
				if ( ( $color && ! woodmart_is_css_encode( $color ) ) || $custom_height ) {
					$css = '.' . esc_attr( $divider_id ) . ' svg {';
					if ( $color && ! woodmart_is_css_encode( $color ) ) {
						$css .= 'fill: ' . esc_attr( $color ) . ';';
					}

					if ( $custom_height ) {
						$css .= 'height: ' . esc_attr( $custom_height ) . ';';
					}
					$css .= '}';
					wp_add_inline_style( 'woodmart-inline-css', $css );
				}
				?>
			</div>
		<?php

		return ob_get_clean();
	}
}
