<?php
/**
 * Shortcode for Text Block element.
 *
 * @package woodmart
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

if ( ! function_exists( 'woodmart_shortcode_text_block' ) ) {
	/**
	 * Text block shortcode
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Shortcode content.
	 *
	 * @return false|string
	 */
	function woodmart_shortcode_text_block( $atts, $content ) {
		$wrapper_classes = apply_filters( 'vc_shortcodes_css_class', '', '', $atts );

		$atts = shortcode_atts(
			array(
				'text_font_family'                   => 'default',
				'text_font_size'                     => 'default',
				'text_font_weight'                   => 'default',
				'text_color'                         => 'default',
				'text_color_scheme'                  => 'inherit',
				'text_align'                         => 'left',
				'content_width'                      => '100',
				'woodmart_css_id'                    => '',
				'extra_classes'                      => '',
				'css'                                => '',
				// Global.
				'parallax_scroll'                    => 'no',
				'scroll_x'                           => 0,
				'scroll_y'                           => -80,
				'scroll_z'                           => 0,
				'scroll_smooth'                      => '',
				'woodmart_inline'                    => 'no',
				// Extra.
				'title_decoration_bg_image'          => '',
				'title_decoration_bg_image_size'     => 'full',
				'title_decoration_bg_image_sizes'    => 'cover',
				'title_decoration_bg_image_position' => 'center center',
				'title_decoration_bg_image_repeat'   => 'no-repeat',
			),
			$atts
		);

		if ( empty( $atts['woodmart_css_id'] ) ) {
			$atts['woodmart_css_id'] = uniqid();
		}

		$id               = 'wd-' . $atts['woodmart_css_id'];
		$wrapper_classes .= ' text-' . $atts['text_align'];
		$style_attr       = '';
		if ( $atts['content_width'] && 'custom' !== $atts['content_width'] && '100' !== $atts['content_width'] ) {
			$style_attr      .= ' style="--wd-max-width: ' . $atts['content_width'] . '%;"';
			$wrapper_classes .= ' wd-width-enabled';
		} elseif ( 'custom' === $atts['content_width'] ) {
			$wrapper_classes .= ' wd-width-custom';
		}
		if ( 'inherit' !== $atts['text_color_scheme'] ) {
			$wrapper_classes .= ' color-scheme-' . $atts['text_color_scheme'];
		}
		if ( 'default' !== $atts['text_font_weight'] ) {
			$wrapper_classes .= ' wd-font-weight-' . $atts['text_font_weight'];
		}
		if ( 'default' !== $atts['text_color'] && 'custom' !== $atts['text_color'] ) {
			$wrapper_classes .= ' color-' . $atts['text_color'];
		}
		if ( 'default' !== $atts['text_font_size'] ) {
			$wrapper_classes .= ' wd-fontsize-' . $atts['text_font_size'];
		}
		if ( 'default' !== $atts['text_font_family'] ) {
			$wrapper_classes .= ' font-' . $atts['text_font_family'];
		}
		if ( function_exists( 'vc_shortcode_custom_css_class' ) ) {
			$wrapper_classes .= ' ' . vc_shortcode_custom_css_class( $atts['css'] );
		}
		if ( ! empty( $atts['extra_classes'] ) ) {
			$wrapper_classes .= ' ' . $atts['extra_classes'];
		}

		ob_start();

		woodmart_enqueue_inline_style( 'text-block' );

		$css = '';

		$highlight_image = woodmart_otf_get_image_url( $atts['title_decoration_bg_image'], $atts['title_decoration_bg_image_size'] );

		if ( $highlight_image ) {
			$css .= '#' . $id . ' u {';
			$css .= 'background-image: url(' . $highlight_image . ');';
			$css .= 'background-size: ' . $atts['title_decoration_bg_image_sizes'] . ';';
			$css .= 'background-position: ' . $atts['title_decoration_bg_image_position'] . ';';
			$css .= 'background-repeat: ' . $atts['title_decoration_bg_image_repeat'] . ';';
			$css .= '}';
		}

		if ( $css ) {
			wp_add_inline_style( 'woodmart-inline-css', $css );
		}

		?>
		<div id="<?php echo esc_attr( $id ); ?>" class="wd-text-block wd-wpb reset-last-child<?php echo esc_attr( $wrapper_classes ); ?>"<?php echo wp_kses( $style_attr, true ); ?>>
			<?php echo wpb_js_remove_wpautop( $content, true ); // phpcs:ignore ?>
		</div>
		<?php

		return apply_filters( 'vc_shortcode_output', ob_get_clean(), new WD_WPBakeryShortCodeFix(), $atts, 'woodmart_text_block' );
	}
}
