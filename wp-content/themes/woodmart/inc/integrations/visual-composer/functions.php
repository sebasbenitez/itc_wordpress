<?php
/**
 * This file adds some custom properties to the WPB editor.
 *
 * @package woodmart.
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

// **********************************************************************//
// Features that add GLOBAL maps to WPB Composer
// **********************************************************************//
if ( ! function_exists( 'woodmart_vc_map' ) ) {
	/**
	 * Register element map.
	 *
	 * @param string          $tag Element tag.
	 * @param callable-string $callback Map callback.
	 *
	 * @return void
	 */
	function woodmart_vc_map( $tag, $callback ) {
		vc_lean_map( $tag, $callback );
	}
}

if ( ! function_exists( 'woodmart_vc_column_text_custom_options' ) ) {
	/**
	 * Update custom params map to Default Column Text element in WPBakery.
	 *
	 * @throws Exception .
	 */
	function woodmart_vc_column_text_custom_options() {
		$design_options = array(
			woodmart_get_vc_display_inline_map(),

			array(
				'type'             => 'woodmart_switch',
				'heading'          => esc_html__( 'Text larger', 'woodmart' ),
				'param_name'       => 'text_larger',
				'true_state'       => 'yes',
				'false_state'      => 'no',
				'default'          => 'no',
				'edit_field_class' => 'vc_col-sm-12 vc_column',
			),

			woodmart_get_vc_animation_map( 'wd_animation' ),
			woodmart_get_vc_animation_map( 'wd_animation_delay' ),
			woodmart_get_vc_animation_map( 'wd_animation_duration' ),

			array(
				'type'       => 'woodmart_button_set',
				'heading'    => esc_html__( 'Color Scheme', 'woodmart' ),
				'param_name' => 'woodmart_color_scheme',
				'value'      => array(
					esc_html__( 'Inherit', 'woodmart' ) => '',
					esc_html__( 'Light', 'woodmart' )   => 'light',
					esc_html__( 'Dark', 'woodmart' )    => 'dark',
				),
			),
		);

		vc_add_params( 'vc_column_text', $design_options );
	}

	add_action( 'vc_before_init', 'woodmart_vc_column_text_custom_options' );
}

if ( ! function_exists( 'woodmart_vc_single_image_custom_options' ) ) {
	/**
	 * Update custom params map to Default Single Image element in WPBakery.
	 *
	 * @throws Exception .
	 */
	function woodmart_vc_single_image_custom_options() {
		$general_options = array(
			/**
			 * Woodmart Animation Option.
			 */
			woodmart_get_vc_animation_map( 'wd_animation' ),
			woodmart_get_vc_animation_map( 'wd_animation_delay' ),
			woodmart_get_vc_animation_map( 'wd_animation_duration' ),
			/**
			 * Parallax On Scroll Option.
			 */
			woodmart_parallax_scroll_map( 'parallax_scroll' ),
			woodmart_parallax_scroll_map( 'scroll_x' ),
			woodmart_parallax_scroll_map( 'scroll_y' ),
			woodmart_parallax_scroll_map( 'scroll_z' ),
			woodmart_parallax_scroll_map( 'scroll_smooth' ),
		);

		vc_add_params( 'vc_single_image', $general_options );
	}

	add_action( 'vc_before_init', 'woodmart_vc_single_image_custom_options' );
}

if ( ! function_exists( 'woodmart_vc_separator_custom_options' ) ) {
	/**
	 * Update custom params map to Default Separator element in WPBakery.
	 *
	 * @throws Exception .
	 */
	function woodmart_vc_separator_custom_options() {
		$advanced_options = array(
			woodmart_get_vc_responsive_visible_map( 'responsive_tabs_hide' ),
			woodmart_get_vc_responsive_visible_map( 'wd_hide_on_desktop' ),
			woodmart_get_vc_responsive_visible_map( 'wd_hide_on_tablet' ),
			woodmart_get_vc_responsive_visible_map( 'wd_hide_on_mobile' ),
		);

		vc_add_params( 'vc_separator', $advanced_options );
	}

	add_action( 'vc_before_init', 'woodmart_vc_separator_custom_options' );
}

// **********************************************************************//
// Filters for WPB Composer
// **********************************************************************//

if ( ! function_exists( 'woodmart_vc_extra_classes' ) ) {
	if ( defined( 'VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG' ) ) {
		add_filter( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'woodmart_vc_extra_classes', 30, 3 );
	}

	/**
	 * Adds classes depending on the passed settings.
	 *
	 * @param string $classes list of classes.
	 * @param mixed  $base .
	 * @param array  $atts list of settings.
	 * @return string new classes.
	 */
	function woodmart_vc_extra_classes( $classes, $base, $atts ) {
		if ( isset( $atts['wd_z_index'] ) && 'yes' === $atts['wd_z_index'] ) {
			$classes .= ' wd-z-index';
		}

		if ( 'vc_column' === $base || 'vc_column_inner' === $base ) {
			if ( ! empty( $atts['vertical_alignment'] ) || ! empty( $atts['horizontal_alignment'] ) ) {
				$classes .= ' wd-enabled-flex';
			}
		}

		if ( 'vc_column' === $base && ! empty( $atts['wd_column_role'] ) ) {
			if ( isset( $atts['wd_column_role_offcanvas_desktop'] ) && 'yes' === $atts['wd_column_role_offcanvas_desktop'] ) {
				$classes .= ' wd-col-offcanvas-lg';
			}

			if ( isset( $atts['wd_column_role_offcanvas_tablet'] ) && 'yes' === $atts['wd_column_role_offcanvas_tablet'] ) {
				$classes .= ' wd-col-offcanvas-md-sm';
			}

			if ( isset( $atts['wd_column_role_offcanvas_mobile'] ) && 'yes' === $atts['wd_column_role_offcanvas_mobile'] ) {
				$classes .= ' wd-col-offcanvas-sm';
			}

			if ( isset( $atts['wd_column_role_content_desktop'] ) && 'yes' === $atts['wd_column_role_content_desktop'] ) {
				$classes .= ' wd-col-content-lg';
			}

			if ( isset( $atts['wd_column_role_content_tablet'] ) && 'yes' === $atts['wd_column_role_content_tablet'] ) {
				$classes .= ' wd-col-content-md-sm';
			}

			if ( isset( $atts['wd_column_role_content_mobile'] ) && 'yes' === $atts['wd_column_role_content_mobile'] ) {
				$classes .= ' wd-col-content-sm';
			}

			if ( isset( $atts['wd_off_canvas_alignment'] ) && ! empty( $atts['wd_off_canvas_alignment'] ) ) {
				$classes .= ' wd-alignment-' . $atts['wd_off_canvas_alignment'];
			}
		}

		if ( ! empty( $atts['woodmart_inline'] ) && 'yes' === $atts['woodmart_inline'] ) {
			$classes .= ' inline-element';
		}
		if ( ! empty( $atts['woodmart_color_scheme'] ) && ( 'vc_column' === $base ||
		'vc_column_inner' === $base || 'vc_empty_space' === $base || 'vc_column_text' === $base ) ) {
			$classes .= ' color-scheme-' . $atts['woodmart_color_scheme'];
		}
		if ( isset( $atts['text_larger'] ) && 'yes' === $atts['text_larger'] ) {
			$classes .= ' text-larger';
		}
		if ( isset( $atts['woodmart_sticky_column'] ) && 'true' === $atts['woodmart_sticky_column'] ) {
			$classes .= ' woodmart-sticky-column';

			if ( isset( $atts['woodmart_sticky_column_offset'] ) && $atts['woodmart_sticky_column_offset'] ) {
				$classes .= ' wd_sticky_offset_' . $atts['woodmart_sticky_column_offset'];
			}
		}
		if ( isset( $atts['woodmart_parallax'] ) && $atts['woodmart_parallax'] ) {
			$classes .= ' wd-parallax';
		}
		if ( isset( $atts['woodmart_disable_overflow'] ) && $atts['woodmart_disable_overflow'] ) {
			$classes .= ' wd-disable-overflow';
		}
		if ( isset( $atts['woodmart_gradient_switch'] ) && 'yes' === $atts['woodmart_gradient_switch'] && apply_filters( 'woodmart_gradients_enabled', true ) ) {
			$classes .= ' wd-row-gradient-enable';
		}
		// Bg option.
		if ( ! empty( $atts['woodmart_bg_position'] ) ) {
			$classes .= ' wd-bg-' . $atts['woodmart_bg_position'];
		}
		// Text align option.
		if ( ! empty( $atts['woodmart_text_align'] ) ) {
			$classes .= ' text-' . $atts['woodmart_text_align'];
		}
		// Responsive opt.
		if ( isset( $atts['woodmart_hide_large'] ) && $atts['woodmart_hide_large'] ) {
			$classes .= ' hidden-lg';
		}
		if ( isset( $atts['woodmart_hide_medium'] ) && $atts['woodmart_hide_medium'] ) {
			$classes .= ' hidden-md hidden-sm';
		}
		if ( isset( $atts['woodmart_hide_small'] ) && $atts['woodmart_hide_small'] ) {
			$classes .= ' hidden-xs';
		}
		// Row reverse opt.
		if ( isset( $atts['row_reverse_mobile'] ) && $atts['row_reverse_mobile'] ) {
			$classes .= ' row-reverse-mobile';
		}
		if ( isset( $atts['row_reverse_tablet'] ) && $atts['row_reverse_tablet'] ) {
			$classes .= ' row-reverse-tablet';
		}

		// Hide bg img on mobile.
		if ( isset( $atts['mobile_bg_img_hidden'] ) && 'yes' === $atts['mobile_bg_img_hidden'] ) {
			$classes .= ' mobile-bg-img-hidden';
		}

		// Hide bg img on tablet.
		if ( isset( $atts['tablet_bg_img_hidden'] ) && 'yes' === $atts['tablet_bg_img_hidden'] ) {
			$classes .= ' tablet-bg-img-hidden';
		}

		// Reset margin (deprecated).
		if ( isset( $atts['mobile_reset_margin'] ) && 'yes' === $atts['mobile_reset_margin'] ) {
			$classes .= ' reset-margin-mobile';
		}

		if ( isset( $atts['tablet_reset_margin'] ) && 'yes' === $atts['tablet_reset_margin'] ) {
			$classes .= ' reset-margin-tablet';
		}

		if ( ! empty( $atts['wd_animation'] ) && 'none' !== $atts['wd_animation'] ) {
			$classes .= ' wd-animation';
			$classes .= ' wd-transform';
			$classes .= ' wd-animation-' . $atts['wd_animation'];

			$duration = ! empty( $atts['wd_animation_duration'] ) ? $atts['wd_animation_duration'] : 'normal';
			$classes .= ' wd-animation-' . $duration;
			if ( ! empty( $atts['wd_animation_delay'] ) ) {
				$classes .= ' wd_delay_' . $atts['wd_animation_delay'];
			}
		}

		if ( ! empty( $atts['woodmart_css_id'] ) ) {
			$classes .= ' wd-rs-' . $atts['woodmart_css_id'];
		}

		if ( ! empty( $atts['woodmart_stretch_content'] ) ) {
			$classes .= ' wd-' . $atts['woodmart_stretch_content'];
		}

		if ( isset( $atts['wd_hide_on_desktop'] ) && 'yes' === $atts['wd_hide_on_desktop'] ) {
			$classes .= ' hidden-lg';
		}

		if ( isset( $atts['wd_hide_on_tablet'] ) && 'yes' === $atts['wd_hide_on_tablet'] ) {
			$classes .= ' hidden-md hidden-sm';
		}

		if ( isset( $atts['wd_hide_on_mobile'] ) && 'yes' === $atts['wd_hide_on_mobile'] ) {
			$classes .= ' hidden-xs';
		}

		if ( isset( $atts['wd_collapsible_content_switcher'] ) && 'yes' === $atts['wd_collapsible_content_switcher'] ) {
			$classes .= ' wd-collapsible-content';
		}

		/**
		 * Single Product Layout.
		 */
		if (
			(
				! empty( $atts['width_desktop'] )
			) ||
			(
				(
					isset( $atts['width_tablet'] ) &&
					! empty( $atts['width_tablet'] )
				) ||
				(
					isset( $atts['width_mobile'] ) &&
					! empty( $atts['width_mobile'] )
				)
			)
		) {
			$classes .= ' wd-enabled-width';
		}

		return $classes;
	}
}

if ( ! function_exists( 'woodmart_vc_enqueue_scripts' ) ) {
	/**
	 * Enqueue scripts and styles for WPB elements depending on the passed settings.
	 *
	 * @param string $content Element content.
	 * @param string $shortcode Shortcode name.
	 * @param array  $atts Shortcode attributes.
	 *
	 * @return string
	 */
	function woodmart_vc_enqueue_scripts( $content, $shortcode, $atts ) {
		if ( 'vc_column' === $shortcode ) {
			if ( ! empty( $atts['wd_column_role'] ) ) {
				woodmart_enqueue_inline_style( 'int-wpb-opt-off-canvas-column' );
			}

			if ( isset( $atts['woodmart_sticky_column'] ) && 'true' === $atts['woodmart_sticky_column'] ) {
				woodmart_enqueue_js_library( 'sticky-kit' );
				woodmart_enqueue_js_script( 'sticky-column' );
			}
		}

		if ( isset( $atts['wd_collapsible_content_switcher'] ) && 'yes' === $atts['wd_collapsible_content_switcher'] ) {
			woodmart_enqueue_inline_style( 'collapsible-content' );
		}

		if ( ! empty( $atts['wd_animation'] ) && 'none' !== $atts['wd_animation'] ) {
			woodmart_enqueue_js_script( 'css-animations' );
			woodmart_enqueue_inline_style( 'mod-animations-transform-base' );
			woodmart_enqueue_inline_style( 'mod-animations-transform' );
			woodmart_enqueue_inline_style( 'mod-transform' );
		}

		if ( ! empty( $atts['css_animation'] ) && 'none' !== $atts['css_animation'] ) {
			woodmart_enqueue_inline_style( 'mod-animations-keyframes' );
		}

		if ( isset( $atts['woodmart_parallax'] ) && $atts['woodmart_parallax'] ) {
			woodmart_enqueue_js_library( 'parallax' );
			woodmart_enqueue_js_script( 'parallax' );
		}

		return $content;
	}

	add_filter( 'do_shortcode_tag', 'woodmart_vc_enqueue_scripts', 10, 3 );
}

// **********************************************************************//
// Add custom animations to WPB Composer
// **********************************************************************//

if ( ! function_exists( 'woodmart_add_css_animation' ) ) {
	/**
	 * Add animation map settings for VC.
	 *
	 * @param array $animations list of animations.
	 * @return array
	 */
	function woodmart_add_css_animation( $animations ) {
		$animations[] = array(
			'label'  => esc_html__( 'Theme Animations', 'woodmart' ),
			'values' => array(
				esc_html__( 'Slide from top', 'woodmart' ) => array(
					'value' => 'wd-slide-from-top',
					'type'  => 'in',
				),
				esc_html__( 'Slide from bottom', 'woodmart' ) => array(
					'value' => 'wd-slide-from-bottom',
					'type'  => 'in',
				),
				esc_html__( 'Slide from left', 'woodmart' ) => array(
					'value' => 'wd-slide-from-left',
					'type'  => 'in',
				),
				esc_html__( 'Slide from right', 'woodmart' ) => array(
					'value' => 'wd-slide-from-right',
					'type'  => 'in',
				),
				esc_html__( 'Right flip Y', 'woodmart' )   => array(
					'value' => 'wd-right-flip-y',
					'type'  => 'in',
				),
				esc_html__( 'Left flip Y', 'woodmart' )    => array(
					'value' => 'wd-left-flip-y',
					'type'  => 'in',
				),
				esc_html__( 'Top flip X', 'woodmart' )     => array(
					'value' => 'wd-top-flip-x',
					'type'  => 'in',
				),
				esc_html__( 'Bottom flip X', 'woodmart' )  => array(
					'value' => 'wd-bottom-flip-x',
					'type'  => 'in',
				),
				esc_html__( 'Zoom in', 'woodmart' )        => array(
					'value' => 'wd-zoom-in',
					'type'  => 'in',
				),
				esc_html__( 'Rotate Z', 'woodmart' )       => array(
					'value' => 'wd-rotate-z',
					'type'  => 'in',
				),
			),
		);

		return $animations;
	}

	add_action( 'vc_param_animation_style_list', 'woodmart_add_css_animation', 1000 );
}

if ( ! function_exists( 'woodmart_get_tab_title_category_for_wpb' ) ) {
	/**
	 * Get tab title category for WPB builder.
	 *
	 * @param string $title Title category.
	 * @param string $layout Layout type.
	 * @return string
	 */
	function woodmart_get_tab_title_category_for_wpb( $title, $layout = '' ) {
		if ( $layout ) {
			$layout = ' xts-layout-' . $layout;
		}

		return '<span class="xts-wpb-tab-title' . $layout . '">' . $title . '</span>';
	}
}

if ( ! function_exists( 'woodmart_get_gradient_attr' ) ) {
	/**
	 * Get gradient attribute.
	 *
	 * @param string            $output Output html.
	 * @param WPBakeryShortCode $obj WPBakeryShortCode object.
	 * @param array             $attr Attributes.
	 *
	 * @return array|mixed|string|string[]|null
	 */
	function woodmart_get_gradient_attr( $output, $obj, $attr ) {
		if ( isset( $attr['woodmart_gradient_switch'] ) && 'yes' === $attr['woodmart_gradient_switch'] ) {
			$gradient_css = woodmart_get_gradient_css( $attr['woodmart_color_gradient'] );
			$output       = preg_replace_callback(
				'/wd-row-gradient-enable.*?>/',
				function ( $matches ) use ( $gradient_css ) {
					return strtolower( $matches[0] . '<div class="woodmart-row-gradient wd-fill" style="' . $gradient_css . '"></div>' );
				},
				$output
			);
		}
		return $output;
	}

	add_filter( 'vc_shortcode_output', 'woodmart_get_gradient_attr', 10, 3 );
}

if ( ! function_exists( 'woodmart_parse_gradient_string' ) ) {
	/**
	 * Parse theme gradient attribute string into a valid CSS gradient() value.
	 *
	 * Expected input structure (pipes-delimited):
	 *   colors|<unused>|type|position
	 * Where colors is like: "rgba(r,g,b,a) - 0/ rgba(r,g,b,a) - 100".
	 *
	 * @param string $input Raw gradient attribute value.
	 * @return string CSS gradient value or empty string on failure.
	 */
	function woodmart_parse_gradient_string( $input ) {
		$parts = array_map( 'trim', explode( '|', (string) $input ) );

		// We need at least: colors, (unused), type, position.
		if ( count( $parts ) < 4 ) {
			return '';
		}

		$colors_str = trim( $parts[0], " /\t\n\r\0\x0B" );
		$type       = strtolower( (string) $parts[2] );
		$pos        = trim( (string) $parts[3] );

		// Extract all color stops like: rgba(...) - 0 | rgb(...) - 50.5.
		$stops = array();
		if ( preg_match_all( '/(rgba?\([^)]*\))\s*-\s*([+-]?\d+(?:\.\d+)?)/i', $colors_str, $matches, PREG_SET_ORDER ) ) {
			foreach ( $matches as $m ) {
				$stops[] = $m[1] . ' ' . $m[2] . '%';
			}
		}

		if ( empty( $stops ) ) {
			return '';
		}

		// Radial gradient: type is circle|ellipse; normalize position keywords.
		if ( 'circle' === $type || 'ellipse' === $type ) {
			$pos   = '' === $pos ? 'center' : $pos;
			$words = preg_split( '/\s+/', trim( $pos ) );

			if ( 1 === count( $words ) ) {
				$w = strtolower( $words[0] );

				if ( 'center' === $w ) {
					$pos = 'center center';
				} elseif ( in_array( $w, array( 'left', 'right' ), true ) ) {
					$pos = $w . ' center';
				} elseif ( in_array( $w, array( 'top', 'bottom' ), true ) ) {
					$pos = 'center ' . $w;
				} else {
					$pos = $w; // Custom value, pass-through.
				}
			} else {
				$pos = implode( ' ', $words );
			}

			return "radial-gradient({$type} at {$pos}, " . implode( ', ', $stops ) . ')';
		}

		// Linear gradient: accept angles (deg|rad|turn) or keyword directions.
		if ( 'linear' === $type ) {
			// Reverse direction keywords for gradient position.
			$reverse_map = array(
				'left'   => 'right',
				'right'  => 'left',
				'top'    => 'bottom',
				'bottom' => 'top',
			);

			// Only reverse if it's a single direction or a pair.
			if ( '' !== $pos ) {
				$words = preg_split( '/\s+/', strtolower( $pos ) );

				foreach ( $words as $i => $word ) {
					if ( isset( $reverse_map[ $word ] ) ) {
						$words[ $i ] = $reverse_map[ $word ];
					}
				}

				$pos = implode( ' ', $words );
			}

			$dir      = '' === $pos ? '' : $pos;
			$is_angle = (bool) preg_match( '/^\s*\d+(?:\.\d+)?(?:deg|rad|turn)\s*$/i', $dir );
			$has_to   = (bool) preg_match( '/^\s*to\s+/i', $dir );

			if ( '' !== $dir && ! $is_angle && ! $has_to ) {
				// Normalize keyword directions. If contains center, drop direction.
				if ( preg_match( '/\bcenter\b/i', $dir ) ) {
					$dir = '';
				} else {
					$dir = 'to ' . $dir;
				}
			}

			return ( '' === $dir )
				? 'linear-gradient(' . implode( ', ', $stops ) . ')'
				: "linear-gradient({$dir}, " . implode( ', ', $stops ) . ')';
		}

		// Unknown type.
		return '';
	}
}

if ( ! function_exists( 'woodmart_get_gradient_css' ) ) {
	/**
	 * Get gradient css.
	 *
	 * @param string $gradient_attr Gradient attribute string.
	 *
	 * @return string
	 */
	function woodmart_get_gradient_css( $gradient_attr ) {
		$css = woodmart_parse_gradient_string( $gradient_attr );

		return ! empty( $css ) ? 'background-image:' . $css . ';' : '';
	}
}

if ( ! function_exists( 'woodmart_responsive_text_size_css' ) ) {
	/**
	 * Get responsive text size css.
	 *
	 * @param integer $id ID.
	 * @param string  $classes Classes.
	 * @param integer $data Data.
	 * @param string  $action Action.
	 * @return string|void
	 */
	function woodmart_responsive_text_size_css( $id, $classes, $data, $action = 'echo' ) {
		if ( 'return' === $action ) {
			return '#' . $id . ' .' . $classes . '{font-size:' . $data . 'px;line-height:' . intval( $data + 10 ) . 'px;}'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			echo '#' . $id . ' .' . $classes . '{font-size:' . $data . 'px;line-height:' . intval( $data + 10 ) . 'px;}'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}

if ( ! function_exists( 'woodmart_register_vc_roles' ) ) {
	/**
	 * Register VC roles.
	 */
	function woodmart_register_vc_roles() {
		if ( ! function_exists( 'vc_path_dir' ) || ! defined( 'WPB_VC_VERSION' ) ) {
			return;
		}

		$post_types_to_enable = array( 'wd_floating_block', 'wd_popup', 'wd_product_tabs', 'woodmart_size_guide' );
		$admin                = vc_role_access()->who( 'administrator' );

		foreach ( $post_types_to_enable as $post_type ) {
			$is_enabled = $admin->part( 'post_types' )->getCapRule( $post_type );

			if ( ! $is_enabled ) {
				$cap_key = $admin->part( 'post_types' )->getStateKey() . '/' . $post_type;
				get_role( 'administrator' )->add_cap( $cap_key, true );
			}
		}
	}

	add_action( 'admin_init', 'woodmart_register_vc_roles' );
}

if ( ! function_exists( 'woodmart_vc_parse_multi_attribute' ) ) {
	/**
	 * Parse multi attribute and sanitize data.
	 *
	 * @param string|array $value Value.
	 * @return array
	 */
	function woodmart_vc_parse_multi_attribute( $value ) {
		$result = array(
			'url'    => '',
			'title'  => '',
			'target' => '',
			'rel'    => '',
		);

		if ( $value && ! is_array( $value ) && strpos( $value, 'url:' ) === false && strpos( $value, 'title:' ) === false && strpos( $value, 'target:' ) === false && strpos( $value, 'rel:' ) === false ) { // Fix for widget Banner.
			$result['url'] = esc_url( $value );

			return $result;
		}

		if ( is_array( $value ) ) {
			$params_pairs = $value;
		} else {
			$params_pairs = explode( '|', $value );
		}

		if ( ! empty( $params_pairs ) ) {
			foreach ( $params_pairs as $pair ) {
				$param = preg_split( '/\:/', $pair );
				if ( ! empty( $param[0] ) && isset( $param[1] ) ) {
					$key   = sanitize_text_field( $param[0] );
					$value = rawurldecode( $param[1] );

					// Sanitize each field appropriately.
					if ( 'url' === $key ) {
						$result[ $key ] = esc_url( $value );
					} elseif ( 'title' === $key ) {
						$result[ $key ] = sanitize_text_field( $value );
					} elseif ( 'target' === $key ) {
						// Only allow _blank, _self, _parent, _top
						$allowed_targets = array( '_blank', '_self', '_parent', '_top' );
						$value           = sanitize_text_field( $value );
						$result[ $key ]  = in_array( $value, $allowed_targets, true ) ? $value : '';
					} elseif ( 'rel' === $key ) {
						// Sanitize rel values (no-opener, no-referrer, etc.)
						$result[ $key ] = sanitize_text_field( $value );
					} else {
						$result[ $key ] = sanitize_text_field( $value );
					}
				}
			}
		}

		return $result;
	}
}

if ( ! function_exists( 'woodmart_product_id_autocomplete_suggester' ) ) {
	/**
	 * Get product by title, ID or SKU for autocomplete field.
	 *
	 * @param int $query Search query.
	 * @return array
	 */
	function woodmart_product_id_autocomplete_suggester( $query ) {
		global $wpdb;
		$product_id      = (int) $query;
		$post_meta_infos = $wpdb->get_results( //phpcs:ignore
			$wpdb->prepare(
				"SELECT a.ID AS id, a.post_title AS title, b.meta_value AS sku
					FROM {$wpdb->posts} AS a
					LEFT JOIN ( SELECT meta_value, post_id  FROM {$wpdb->postmeta} WHERE `meta_key` = '_sku' ) AS b ON b.post_id = a.ID
					WHERE a.post_type = 'product' AND ( a.ID = '%d' OR b.meta_value LIKE '%%%s%%' OR a.post_title LIKE '%%%s%%' )", //phpcs:ignore
				$product_id > 0 ? $product_id : - 1,
				stripslashes( $query ),
				stripslashes( $query )
			),
			ARRAY_A
		);

		$results = array();
		if ( is_array( $post_meta_infos ) && ! empty( $post_meta_infos ) ) {
			foreach ( $post_meta_infos as $value ) {
				$data          = array();
				$data['value'] = $value['id'];
				$data['label'] = __( 'Id', 'woodmart' ) . ': ' . $value['id'] . ( ( strlen( $value['title'] ) > 0 ) ? ' - ' . __( 'Title', 'woodmart' ) . ': ' . $value['title'] : '' ) . ( ( strlen( $value['sku'] ) > 0 ) ? ' - ' . __( 'Sku', 'woodmart' ) . ': ' . $value['sku'] : '' );
				$results[]     = $data;
			}
		}

		return $results;
	}
}

if ( ! function_exists( 'woodmart_product_id_autocomplete_suggester_new' ) ) {
	/**
	 * Get product by title, ID or SKU for autocomplete field for WC 3.6.0 and higher.
	 *
	 * @param int $query Search query.
	 * @return array
	 */
	function woodmart_product_id_autocomplete_suggester_new( $query ) {
		global $wpdb;
		$product_id      = (int) $query;
		$post_meta_infos = $wpdb->get_results( //phpcs:ignore
			$wpdb->prepare(
				"SELECT a.ID AS id, a.post_title AS title
				FROM {$wpdb->posts} AS a
				LEFT JOIN ( SELECT product_id, sku FROM {$wpdb->wc_product_meta_lookup} ) AS b ON b.product_id = a.ID
				WHERE a.post_type = 'product' AND ( a.ID = '%d' OR b.sku LIKE '%%%s%%' OR a.post_title LIKE '%%%s%%' )", // phpcs:ignore
				$product_id > 0 ? $product_id : - 1,
				stripslashes( $query ),
				stripslashes( $query )
			),
			ARRAY_A
		);

		$results = array();
		if ( is_array( $post_meta_infos ) && ! empty( $post_meta_infos ) ) {
			foreach ( $post_meta_infos as $value ) {
				$data          = array();
				$data['value'] = $value['id'];
				$data['label'] = esc_html__( 'Id', 'woodmart' ) . ': ' . $value['id'] . ( ( isset( $value['title'] ) ) ? ' - ' . esc_html__( 'Title', 'woodmart' ) . ': ' . $value['title'] : '' ) . ( ( isset( $value['sku'] ) ) ? ' - ' . esc_html__( 'Sku', 'woodmart' ) . ': ' . $value['sku'] : '' );
				$results[]     = $data;
			}
		}

		return $results;
	}
}

if ( ! function_exists( 'woodmart_product_id_autocomplete_render' ) ) {
	/**
	 * Get product's title and SKU from id for autocomplete field.
	 *
	 * @param array $query Search query.
	 * @return array|false
	 */
	function woodmart_product_id_autocomplete_render( $query ) {
		$query = trim( $query['value'] ); // get value from requested
		if ( ! empty( $query ) ) {
			// get product
			$product_object = wc_get_product( (int) $query );
			if ( is_object( $product_object ) ) {
				$product_sku   = $product_object->get_sku();
				$product_title = $product_object->get_title();
				$product_id    = $product_object->get_id();

				$product_sku_display = '';
				if ( ! empty( $product_sku ) ) {
					$product_sku_display = ' - ' . esc_html__( 'Sku', 'woodmart' ) . ': ' . $product_sku;
				}

				$product_title_display = '';
				if ( ! empty( $product_title ) ) {
					$product_title_display = ' - ' . esc_html__( 'Title', 'woodmart' ) . ': ' . $product_title;
				}

				$product_id_display = esc_html__( 'Id', 'woodmart' ) . ': ' . $product_id;

				$data          = array();
				$data['value'] = $product_id;
				$data['label'] = $product_id_display . $product_title_display . $product_sku_display;

				return ! empty( $data ) ? $data : false;
			}

			return false;
		}

		return false;
	}
}
