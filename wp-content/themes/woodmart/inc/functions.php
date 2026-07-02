<?php
/**
 * Functions file.
 *
 * @package woodmart
 */

use Elementor\Plugin;
use XTS\Gutenberg\Blocks_Assets;
use XTS\Gutenberg\Post_CSS;
use XTS\Registry;
use XTS\Admin\Modules\Options\Google_Fonts\Google_Fonts;

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

if ( ! function_exists( 'woodmart_get_opt' ) ) {
	/**
	 * Get theme option.
	 *
	 * @param string $slug Option slug.
	 * @param string $default_val Default value.
	 *
	 * @return mixed|null
	 */
	function woodmart_get_opt( $slug = '', $default_val = false ) {
		global $woodmart_options, $xts_woodmart_options;

		$opt = $default_val;

		if ( isset( $xts_woodmart_options[ $slug ] ) ) {
			$opt = $xts_woodmart_options[ $slug ];

			return apply_filters( 'woodmart_option', $opt, $slug );
		}

		if ( isset( $woodmart_options[ $slug ] ) ) {
			$opt = $woodmart_options[ $slug ];

			return apply_filters( 'woodmart_option', $opt, $slug );
		}

		return apply_filters( 'woodmart_option', $opt, $slug );
	}
}

if ( ! function_exists( 'woodmart_page_ID' ) ) {
	/**
	 * Get current page ID.
	 *
	 * @return mixed
	 */
	function woodmart_page_ID() { // phpcs:ignore
		return Registry::get_instance()->layout->get_page_id();
	}
}

if ( ! function_exists( 'woodmart_get_content_inline_style' ) ) {
	/**
	 * Get content inline style.
	 *
	 * @return mixed
	 */
	function woodmart_get_content_inline_style() {
		return Registry::get_instance()->layout->get_content_inline_style();
	}
}

if ( ! function_exists( 'woodmart_get_page_layout' ) ) {
	/**
	 * Get page layout.
	 *
	 * @return mixed
	 */
	function woodmart_get_page_layout() {
		return Registry::get_instance()->layout->get_page_layout();
	}
}

if ( ! function_exists( 'woodmart_get_carousel_breakpoints' ) ) {
	/**
	 * Added global breakpoints for carousel config.
	 *
	 * @return array
	 */
	function woodmart_get_carousel_breakpoints() {
		return apply_filters(
			'woodmart_get_carousel_breakpoints',
			array(
				'1025'   => 'lg',
				'768.98' => 'md',
				'0'      => 'sm',
			)
		);
	}
}

if ( ! function_exists( 'woodmart_get_color_value' ) ) {
	/**
	 * Get color value.
	 *
	 * @param string $key Option key.
	 * @param string $default_val Default value.
	 * @return mixed
	 */
	function woodmart_get_color_value( $key, $default_val ) {
		$color = woodmart_get_opt( $key );

		if ( isset( $color['idle'] ) && $color['idle'] ) {
			return $color['idle'];
		} else {
			return $default_val;
		}
	}
}

if ( ! function_exists( 'woodmart_get_css_animation' ) ) {
	/**
	 * Get CSS animation.
	 *
	 * @param string $css_animation CSS animation.
	 * @return string
	 */
	function woodmart_get_css_animation( $css_animation ) {
		$output = '';

		if ( $css_animation && 'none' !== $css_animation ) {
			wp_enqueue_style( 'animate-css' );
			wp_enqueue_style( 'vc_animate-css' );

			woodmart_enqueue_js_library( 'waypoints' );
			woodmart_enqueue_js_script( 'animations-offset' );
			$output = ' wd-off-anim wpb_animate_when_almost_visible wpb_' . $css_animation . ' ' . $css_animation;

			$output .= ' wd-anim-name_' . $css_animation;
		}
		return $output;
	}
}

if ( ! function_exists( 'woodmart_get_link_attributes' ) ) {
	/**
	 * Get link attributes.
	 *
	 * @param string  $link Link.
	 * @param boolean $popup Is popup.
	 * @param string  $custom_attributes Custom attributes.
	 *
	 * @return string
	 */
	function woodmart_get_link_attributes( $link, $popup = false, $custom_attributes = '' ) {
		$link = ( '||' === $link ) ? '' : $link;

		if ( $link && ! is_array( $link ) && strpos( $link, 'url:' ) === false && strpos( $link, 'title:' ) === false && strpos( $link, 'target:' ) === false && strpos( $link, 'rel:' ) === false ) { // Fix for widget Banner.
			$link = array( 'url' => esc_url( $link ) );
		} elseif ( function_exists( 'woodmart_vc_parse_multi_attribute' ) && ! is_array( $link ) ) {
			$link = woodmart_vc_parse_multi_attribute( $link );
		}

		$use_link = false;

		if ( isset( $link['url'] ) && strlen( $link['url'] ) > 0 ) {
			$use_link = true;
			$a_href   = apply_filters( 'woodmart_extra_menu_url', $link['url'] );
			// Ensure URL is properly escaped.
			$a_href = esc_url( $a_href );
			if ( $popup ) {
				$a_href = esc_url( $link['url'] );
			}

			$a_title  = isset( $link['title'] ) ? sanitize_text_field( $link['title'] ) : '';
			$a_target = isset( $link['target'] ) ? sanitize_text_field( $link['target'] ) : '';
			$a_rel    = isset( $link['rel'] ) ? sanitize_text_field( $link['rel'] ) : '';
		}

		$attributes = array();

		if ( $use_link ) {
			$attributes[] = 'href="' . trim( $a_href ) . '"';
			$attributes[] = 'title="' . esc_attr( trim( $a_title ) ) . '"';
			if ( ! empty( $a_target ) ) {
				$attributes[] = 'target="' . esc_attr( trim( $a_target ) ) . '"';
			}
			if ( ! empty( $a_rel ) ) {
				$attributes[] = 'rel="' . esc_attr( trim( $a_rel ) ) . '"';
			}
		}

		if ( $custom_attributes ) {
			$raw_attributes = explode( ',', $custom_attributes );

			foreach ( $raw_attributes as $attribute ) {
				$attr_key_value = explode( '|', $attribute );

				$attr_key = mb_strtolower( $attr_key_value[0] );

				// Remove any not allowed characters.
				preg_match( '/[-_a-z0-9]+/', $attr_key, $attr_key_matches );

				if ( empty( $attr_key_matches[0] ) ) {
					continue;
				}

				$attr_key = $attr_key_matches[0];

				// Avoid Javascript events and unescaped href.
				if ( 'href' === $attr_key || 'on' === substr( $attr_key, 0, 2 ) ) {
					continue;
				}

				if ( isset( $attr_key_value[1] ) ) {
					$attr_value = trim( $attr_key_value[1] );
				} else {
					$attr_value = '';
				}

				$attributes[] = $attr_key . '="' . esc_attr( $attr_value ) . '"';
			}
		}

		return implode( ' ', $attributes );
	}
}

if ( ! function_exists( 'woodmart_get_taxonomies_by_ids_autocomplete' ) ) {
	/**
	 * Autocomplete by taxonomies ids.
	 *
	 * @since 1.0.0
	 *
	 * @param array $ids Posts ids.
	 *
	 * @return array
	 */
	function woodmart_get_taxonomies_by_ids_autocomplete( $ids ) {
		$output = array();

		if ( ! $ids ) {
			return $output;
		}

		if ( ! is_array( $ids ) ) {
			$ids = array( $ids );
		}

		foreach ( $ids as $id ) {
			$term = get_term( $id );

			if ( $term && ! is_wp_error( $term ) ) {
				$output[ $term->term_id ] = array(
					'name'  => $term->name,
					'value' => $term->term_id,
				);
			}
		}

		return $output;
	}
}

if ( ! function_exists( 'woodmart_get_post_by_ids_autocomplete' ) ) {
	/**
	 * Autocomplete by post ids.
	 *
	 * @since 1.0.0
	 *
	 * @param array $ids Posts ids.
	 *
	 * @return array
	 */
	function woodmart_get_post_by_ids_autocomplete( $ids ) {
		$output = array();

		if ( ! $ids ) {
			return $output;
		}

		if ( ! is_array( $ids ) ) {
			$ids = array( $ids );
		}

		foreach ( $ids as $id ) {
			$post = get_post( $id );

			if ( $post ) {
				$output[ $post->ID ] = array(
					'name'  => $post->post_title . ' ID:(' . $post->ID . ')',
					'value' => $post->ID,
				);
			}
		}

		return $output;
	}
}

if ( ! function_exists( 'woodmart_product_attributes_array' ) ) {
	/**
	 * Get product attributes array.
	 *
	 * @return array
	 */
	function woodmart_product_attributes_array() {
		if ( ! function_exists( 'wc_get_attribute_taxonomies' ) ) {
			return array();
		}
		$attributes = array();

		foreach ( wc_get_attribute_taxonomies() as $attribute ) {
			$attributes[ 'pa_' . $attribute->attribute_name ] = array(
				'name'  => $attribute->attribute_label,
				'value' => 'pa_' . $attribute->attribute_name,
			);
		}

		return $attributes;
	}
}

if ( ! function_exists( 'woodmart_get_pages_array' ) ) {
	/**
	 * Get all pages array
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	function woodmart_get_pages_array() {
		$pages = array();

		foreach ( get_pages() as $page ) {
			$pages[ $page->ID ] = array(
				'name'  => $page->post_title,
				'value' => $page->ID,
			);
		}

		return $pages;
	}
}

if ( ! function_exists( 'woodmart_get_footer_config' ) ) {
	/**
	 * Get predefined footer configuration by index.
	 *
	 * @param int $index Footer index.
	 * @return array|mixed
	 */
	function woodmart_get_footer_config( $index ) {
		if ( $index > 20 || $index < 1 ) {
			$index = 1;
		}

		$configs = apply_filters(
			'woodmart_footer_configs_array',
			array(
				1  => array(
					'cols' => array(
						'--wd-col-lg:12;',
					),
				),
				2  => array(
					'cols' => array(
						'--wd-col-xs:12;--wd-col-lg:6;',
						'--wd-col-xs:12;--wd-col-lg:6;',
					),
				),
				3  => array(
					'cols' => array(
						'--wd-col-xs:12;--wd-col-lg:4;',
						'--wd-col-xs:12;--wd-col-lg:4;',
						'--wd-col-xs:12;--wd-col-lg:4;',
					),
				),
				4  => array(
					'cols' => array(
						'--wd-col-xs:12;--wd-col-md:6;--wd-col-lg:3',
						'--wd-col-xs:12;--wd-col-md:6;--wd-col-lg:3',
						'--wd-col-xs:12;--wd-col-md:6;--wd-col-lg:3',
						'--wd-col-xs:12;--wd-col-md:6;--wd-col-lg:3',
					),
				),
				5  => array(
					'cols' => array(
						'--wd-col-xs:12;--wd-col-sm:6;--wd-col-md:4;--wd-col-lg:2;',
						'--wd-col-xs:12;--wd-col-sm:6;--wd-col-md:4;--wd-col-lg:2;',
						'--wd-col-xs:12;--wd-col-sm:6;--wd-col-md:4;--wd-col-lg:2;',
						'--wd-col-xs:12;--wd-col-sm:6;--wd-col-md:4;--wd-col-lg:2;',
						'--wd-col-xs:12;--wd-col-sm:6;--wd-col-md:4;--wd-col-lg:2;',
						'--wd-col-xs:12;--wd-col-sm:6;--wd-col-md:4;--wd-col-lg:2;',
					),
				),
				6  => array(
					'cols' => array(
						'--wd-col-xs:12;--wd-col-md:4;--wd-col-lg:3;',
						'--wd-col-xs:12;--wd-col-md:4;--wd-col-lg:6;',
						'--wd-col-xs:12;--wd-col-md:4;--wd-col-lg:3;',
					),
				),
				7  => array(
					'cols' => array(
						'--wd-col-xs:12;--wd-col-md:4;--wd-col-lg:6;',
						'--wd-col-xs:12;--wd-col-md:4;--wd-col-lg:3;',
						'--wd-col-xs:12;--wd-col-md:4;--wd-col-lg:3;',
					),
				),
				8  => array(
					'cols' => array(
						'--wd-col-xs:12;--wd-col-md:4;--wd-col-lg:3;',
						'--wd-col-xs:12;--wd-col-md:4;--wd-col-lg:3;',
						'--wd-col-xs:12;--wd-col-md:4;--wd-col-lg:6;',
					),
				),
				9  => array(
					'cols' => array(
						'--wd-col-lg:12;',
						'--wd-col-xs:12;--wd-col-md:6;--wd-col-lg:3;',
						'--wd-col-xs:12;--wd-col-md:6;--wd-col-lg:3;',
						'--wd-col-xs:12;--wd-col-md:6;--wd-col-lg:3;',
						'--wd-col-xs:12;--wd-col-md:6;--wd-col-lg:3;',
					),
				),
				10 => array(
					'cols' => array(
						'--wd-col-sm:12;--wd-col-lg:6;',
						'--wd-col-sm:12;--wd-col-lg:6;',
						'--wd-col-xs:12;--wd-col-md:6;--wd-col-lg:3;',
						'--wd-col-xs:12;--wd-col-md:6;--wd-col-lg:3;',
						'--wd-col-xs:12;--wd-col-md:6;--wd-col-lg:3;',
						'--wd-col-xs:12;--wd-col-md:6;--wd-col-lg:3;',
					),
				),
				11 => array(
					'cols' => array(
						'--wd-col-sm:12;--wd-col-lg:6;',
						'--wd-col-sm:12;--wd-col-lg:6;',
						'--wd-col-xs:12;--wd-col-sm:6;--wd-col-md:3;--wd-col-lg:2;',
						'--wd-col-xs:12;--wd-col-sm:6;--wd-col-md:3;--wd-col-lg:2;',
						'--wd-col-xs:12;--wd-col-sm:6;--wd-col-md:3;--wd-col-lg:2;',
						'--wd-col-xs:12;--wd-col-sm:6;--wd-col-md:3;--wd-col-lg:2;',
						'--wd-col-md:12;--wd-col-lg:4;',
					),
				),
				12 => array(
					'cols' => array(
						'--wd-col-lg:12;',
						'--wd-col-xs:12;--wd-col-sm:6;--wd-col-md:3;--wd-col-lg:2;',
						'--wd-col-xs:12;--wd-col-sm:6;--wd-col-md:3;--wd-col-lg:2;',
						'--wd-col-xs:12;--wd-col-sm:6;--wd-col-md:3;--wd-col-lg:2;',
						'--wd-col-xs:12;--wd-col-sm:6;--wd-col-md:3;--wd-col-lg:2;',
						'--wd-col-md:12;--wd-col-lg:4;',
					),
				),
				13 => array(
					'cols' => array(
						'--wd-col-xs:12;--wd-col-md:6;--wd-col-lg:3;',
						'--wd-col-xs:12;--wd-col-md:6;--wd-col-lg:3;',
						'--wd-col-xs:12;--wd-col-md:4;--wd-col-lg:2;',
						'--wd-col-xs:12;--wd-col-md:4;--wd-col-lg:2;',
						'--wd-col-xs:12;--wd-col-md:4;--wd-col-lg:2;',
					),
				),
			)
		);

		return ( isset( $configs[ $index ] ) ) ? $configs[ $index ] : array();
	}
}

if ( ! function_exists( 'woodmart_get_the_ID' ) ) {
	/**
	 * This function is called once when initializing WOODMART_Layout object
	 * then you can use function woodmart_page_ID to get current page id
	 *
	 * @return false|int|mixed|null
	 */
	function woodmart_get_the_ID() { // phpcs:ignore
		global $post;

		$page_id = 0;

		$custom_404_id = woodmart_get_opt( 'custom_404_page' );

		if ( isset( $post->ID ) ) {
			$page_id = $post->ID;
		}

		if ( isset( $post->ID ) && ( is_singular( 'page' ) || is_singular( 'post' ) ) ) {
			$page_id = $post->ID;
		} elseif ( is_home() || is_singular( 'post' ) || is_search() || is_tag() || is_category() || is_date() || is_author() ) {
			$page_id = get_option( 'page_for_posts' );
		} elseif ( is_archive() && get_post_type() === 'portfolio' ) {
			$page_id = woodmart_get_portfolio_page_id();
		}

		if ( woodmart_is_shop_archive() ) {
			$page_id = get_option( 'woocommerce_shop_page_id' );
		}

		if ( is_404() && ( 'default' !== $custom_404_id || ! empty( $custom_404_id ) ) ) {
			$page_id = $custom_404_id;
		}

		return $page_id;
	}
}

if ( ! function_exists( 'woodmart_get_html_block' ) ) {
	/**
	 * Get HTML block content.
	 *
	 * @param int     $id Block ID.
	 * @param boolean $inline_css Inline CSS.
	 * @return string
	 */
	function woodmart_get_html_block( $id, $inline_css = false ) {
		$id   = apply_filters( 'wpml_object_id', $id, 'cms_block', true );
		$post = get_post( $id );

		if ( ! $post || 'cms_block' !== $post->post_type || ! $id ) {
			return '';
		}

		return woodmart_get_post_content( $id, $inline_css );
	}
}

if ( ! function_exists( 'woodmart_get_post_content' ) ) {
	/**
	 * Get post content for an active page builder.
	 *
	 * @param int     $id Block ID.
	 * @param boolean $inline_css Inline CSS.
	 *
	 * @return string
	 */
	function woodmart_get_post_content( $id, $inline_css = false ) {
		$post_content = get_the_content( null, false, $id );
		$content      = '';

		if ( woodmart_is_elementor_installed() && Plugin::$instance->documents->get( $id )->is_built_with_elementor() ) {
			$content .= woodmart_elementor_get_content( $id );
		} elseif ( has_blocks( $post_content ) ) {
			if ( woodmart_is_gutenberg_blocks_enabled() ) {
				$content .= Blocks_Assets::get_instance()->get_inline_scripts( $id );
				$content .= Post_CSS::get_instance()->get_inline_blocks_css( $id, $inline_css );
			}

			$content .= wp_filter_content_tags( do_shortcode( shortcode_unautop( do_blocks( $post_content ) ) ) );
		} else {
			$shortcodes_custom_css          = get_post_meta( $id, '_wpb_shortcodes_custom_css', true );
			$woodmart_shortcodes_custom_css = get_post_meta( $id, 'woodmart_shortcodes_custom_css', true );
			$fonts                          = get_post_meta( $id, 'woodmart_shortcodes_fonts', true );

			if ( ! empty( $fonts ) && is_array( $fonts ) ) {
				$fonts = array_map(
					function ( $font ) {
						if ( empty( $font ) || ! array_key_exists( 'font-family', $font ) || empty( $font['font-family'] ) ) {
							return;
						}

						$font_data = wp_parse_args(
							$font,
							array(
								'font-family' => '',
								'font-weight' => '',
								'font-style'  => '',
								'font-subset' => '',
							)
						);

						return $font_data;
					},
					array_values( $fonts )
				);

				ob_start();

				Google_Fonts::get_instance()->enqueue_inline_fonts( $fonts, $id );

				$content .= ob_get_clean();
			}

			if ( ! empty( $shortcodes_custom_css ) || ! empty( $woodmart_shortcodes_custom_css ) ) {
				$content .= '<style data-type="vc_shortcodes-custom-css">';

				if ( ! empty( $shortcodes_custom_css ) ) {
					$content .= $shortcodes_custom_css;
				}

				if ( ! empty( $woodmart_shortcodes_custom_css ) ) {
					$content .= $woodmart_shortcodes_custom_css;
				}

				$content .= '</style>';
			}

			if ( ! str_contains( $post_content, '[vc_row' ) && ! has_blocks( $post_content ) ) {
				$post_content = wpautop( $post_content );
			} elseif ( class_exists( 'Vc_Base' ) && method_exists( 'Vc_Base', 'fixPContent' ) ) {
				$vc           = new Vc_Base();
				$post_content = $vc->fixPContent( $post_content );
			}

			if ( function_exists( 'vc_modules_manager' ) && vc_modules_manager()->is_module_on( 'vc-custom-css' ) ) {
				ob_start();

				vc_modules_manager()->get_module( 'vc-custom-css' )->output_custom_css_to_page( $id );

				$content .= ob_get_clean();
			}

			$content .= do_shortcode( $post_content );
		}

		woodmart_add_editable_post_to_admin_bar( $id );

		return $content;
	}
}

if ( ! function_exists( 'woodmart_get_static_blocks_array' ) ) {
	/**
	 * Get static blocks array.
	 *
	 * @param boolean $is_new New.
	 * @param boolean $is_empty Empty.
	 * @return array
	 */
	function woodmart_get_static_blocks_array( $is_new = false, $is_empty = false ) {
		$args         = array(
			'posts_per_page' => 500, // phpcs:ignore
			'post_type'      => 'cms_block',
		);
		$blocks_posts = get_posts( $args );
		$array        = array();
		foreach ( $blocks_posts as $post ) :
			if ( $is_new ) {
				if ( $is_empty ) {
					$array[''] = array(
						'name'  => esc_html__( 'Select', 'woodmart' ),
						'value' => '',
					);
				}
				$array[ $post->ID ] = array(
					'name'  => $post->post_title . ' (ID:' . $post->ID . ')',
					'value' => $post->ID,
				);
			} else {
				if ( $is_empty ) {
					$array[ esc_html__( 'Select', 'woodmart' ) ] = '';
				}
				$array[ $post->post_title . ' (ID:' . $post->ID . ')' ] = $post->ID;
			}
		endforeach;
		return $array;
	}
}

if ( ! function_exists( 'woodmart_get_theme_settings_html_blocks_array' ) ) {
	/**
	 * Function to get array of HTML Blocks in theme settings array style.
	 *
	 * @return array
	 */
	function woodmart_get_theme_settings_html_blocks_array() {
		return woodmart_get_static_blocks_array( true );
	}
}

if ( ! function_exists( 'woodmart_get_html_blocks_array_with_empty' ) ) {
	/**
	 * Function to get array of HTML Blocks in WPB element array style.
	 *
	 * @return array
	 */
	function woodmart_get_html_blocks_array_with_empty() {
		return woodmart_get_static_blocks_array( false, true );
	}
}

if ( ! function_exists( 'woodmart_get_loop_items_array_with_empty' ) ) {
	/**
	 * Function to get array of Loop items in WPB element array style.
	 *
	 * @return array
	 */
	function woodmart_get_loop_items_array_with_empty() {
		$posts        = array();
		$blocks_posts = get_posts(
			array(
				'posts_per_page' => 100, // phpcs:ignore
				'post_type'      => 'woodmart_layout',
				'meta_key'       => 'wd_layout_type', // phpcs:ignore
				'meta_value'     => 'product_loop_item', // phpcs:ignore
			)
		);

		$posts[ esc_html__( 'Select', 'woodmart' ) ] = '';

		foreach ( $blocks_posts as $post ) {
			$posts[ $post->post_title . ' (ID:' . $post->ID . ')' ] = $post->ID;
		}

		return $posts;
	}
}

if ( ! function_exists( 'woodmart_get_elementor_blocks_array' ) ) {
	/**
	 * Function to get array of HTML Blocks.
	 *
	 * @return array
	 */
	function woodmart_get_elementor_blocks_array() {
		$output = array();

		$posts = get_posts(
			array(
				'posts_per_page' => 500, // phpcs:ignore
				'post_type'      => 'cms_block',
			)
		);

		$output['0'] = esc_html__( 'Select', 'woodmart' );

		foreach ( $posts as $post ) {
			$output[ $post->ID ] = $post->post_title;
		}

		return $output;
	}
}

if ( ! function_exists( 'woodmart_get_elementor_loop_items_array' ) ) {
	/**
	 * Function to get array of HTML Blocks.
	 *
	 * @return array
	 */
	function woodmart_get_elementor_loop_items_array() {
		$output = array();

		$posts = get_posts(
			array(
				'posts_per_page' => 500, // phpcs:ignore
				'post_type'      => 'woodmart_layout',
				'meta_key'       => 'wd_layout_type', // phpcs:ignore
				'meta_value'     => 'product_loop_item', // phpcs:ignore
			)
		);

		$output['0'] = esc_html__( 'Select', 'woodmart' );

		foreach ( $posts as $post ) {
			$output[ $post->ID ] = $post->post_title;
		}

		return $output;
	}
}


if ( ! function_exists( 'woodmart_get_related_posts_args' ) ) {
	/**
	 * Get related posts args.
	 *
	 * @param integer $post_id Post ID.
	 * @return array|array[]
	 */
	function woodmart_get_related_posts_args( $post_id ) {
		$taxs = wp_get_post_tags( $post_id );
		$args = array();

		if ( $taxs ) {
			$tax_ids = array();

			foreach ( $taxs as $individual_tax ) {
				$tax_ids[] = $individual_tax->term_id;
			}

			$args = array(
				'tag__in'             => $tax_ids,
				'post__not_in'        => array( $post_id ),
				'showposts'           => 12,
				'ignore_sticky_posts' => 1,
			);
		}

		return $args;
	}
}

if ( ! function_exists( 'woodmart_get_related_projects_args' ) ) {
	/**
	 * Get related projects args.
	 *
	 * @param integer $post_id Post ID.
	 * @return array
	 */
	function woodmart_get_related_projects_args( $post_id ) {
		$taxs = wp_get_post_terms( $post_id, 'project-cat' );
		$args = array();
		if ( $taxs ) {
			$tax_ids = array();
			foreach ( $taxs as $individual_tax ) {
				$tax_ids[] = $individual_tax->term_id;
			}

			$args = array(
				'post_type'    => 'portfolio',
				'post__not_in' => array( $post_id ),
				'tax_query'    => array( // phpcs:ignore
					array(
						'taxonomy'         => 'project-cat',
						'terms'            => $tax_ids,
						'include_children' => false,
					),
				),
			);
		}

		return $args;
	}
}

if ( ! function_exists( 'woodmart_strip_shortcode_gallery' ) ) {
	/**
	 * Deletes first gallery shortcode and returns content.
	 *
	 * @param string $content Content.
	 * @return array|mixed|string|string[]
	 */
	function woodmart_strip_shortcode_gallery( $content ) {
		preg_match_all( '/' . get_shortcode_regex() . '/s', $content, $matches, PREG_SET_ORDER );
		if ( ! empty( $matches ) ) {
			foreach ( $matches as $shortcode ) {
				if ( 'gallery' === $shortcode[2] ) {
					$pos = strpos( $content, $shortcode[0] );
					if ( false !== $pos ) {
						return substr_replace( $content, '', $pos, strlen( $shortcode[0] ) );
					}
				}
			}
		}
		return $content;
	}
}

if ( ! function_exists( 'woodmart_excerpt_from_content' ) ) {
	/**
	 * Get excerpt from post content.
	 *
	 * @param string  $post_content Post content.
	 * @param integer $limit Limit.
	 * @param string  $shortcodes Shortcodes.
	 * @return string
	 */
	function woodmart_excerpt_from_content( $post_content, $limit, $shortcodes = '' ) {
		// Strip shortcodes and HTML tags.
		if ( empty( $shortcodes ) ) {
			$post_content = preg_replace( '/\[caption(.*)\[\/caption\]/i', '', $post_content );
			$post_content = preg_replace( '`\[[^\]]*\]`', '', $post_content );
			$post_content = preg_replace( '/<!--(.*?)?-->/', '', $post_content );
		}

		$post_content = trim( stripslashes( wp_filter_nohtml_kses( $post_content ) ) );

		if ( 'letter' === woodmart_get_opt( 'blog_words_or_letters' ) ) {
			$excerpt = mb_substr( $post_content, 0, $limit );
			if ( mb_strlen( $excerpt ) >= $limit ) {
				$excerpt .= '...';
			}
		} else {
			++$limit;
			$excerpt = explode( ' ', $post_content, $limit );
			if ( count( $excerpt ) >= $limit ) {
				array_pop( $excerpt );
				$excerpt = implode( ' ', $excerpt ) . '...';
			} else {
				$excerpt = implode( ' ', $excerpt );
			}
		}

		$excerpt = wp_strip_all_tags( $excerpt );

		if ( '...' === trim( $excerpt ) ) {
			return '';
		}

		return $excerpt;
	}
}

if ( ! function_exists( 'woodmart_get_projects_cats_array' ) ) {
	/**
	 * Get projects categories array.
	 *
	 * @return array|string[]
	 */
	function woodmart_get_projects_cats_array() {
		$return = array( 'All' => '' );

		if ( ! post_type_exists( 'portfolio' ) ) {
			return array();
		}

		$cats = get_terms( 'project-cat' );

		foreach ( $cats as $cat ) {
			$return[ $cat->name ] = $cat->term_id;
		}

		return $return;
	}
}

if ( ! function_exists( 'woodmart_get_menus_array' ) ) {
	/**
	 * Get menus dropdown.
	 *
	 * @param string $style Style.
	 * @return array
	 */
	function woodmart_get_menus_array( $style = 'default' ) {
		$output = array();

		$menus = wp_get_nav_menus();

		if ( 'elementor' === $style ) {
			$output[''] = esc_html__( 'Select', 'woodmart' );
		}

		foreach ( $menus as $menu ) {
			if ( 'elementor' === $style ) {
				$output[ $menu->term_id ] = $menu->name;
			} else {
				$output[ $menu->name ] = $menu->name;
			}
		}

		return $output;
	}
}

if ( ! function_exists( 'woodmart_get_sidebars_array' ) ) {
	/**
	 * Get registered sidebars dropdown.
	 *
	 * @param boolean $is_new New.
	 * @return array
	 */
	function woodmart_get_sidebars_array( $is_new = false ) {
		global $wp_registered_sidebars;
		$sidebars = array();
		if ( $is_new ) {
			$sidebars['none'] = array(
				'name'  => esc_html__( 'None', 'woodmart' ),
				'value' => 'none',
			);
		} else {
			$sidebars['none'] = 'none';
		}
		foreach ( $wp_registered_sidebars as $id => $sidebar ) {
			if ( $is_new ) {
				$sidebars[ $id ] = array(
					'name'  => $sidebar['name'],
					'value' => $id,
				);
			} else {
				$sidebars[ $id ] = $sidebar['name'];
			}
		}
		return $sidebars;
	}
}

if ( ! function_exists( 'woodmart_get_theme_settings_sidebars_array' ) ) {
	/**
	 * Get sidebars array in theme settings array style.
	 *
	 * @return array
	 */
	function woodmart_get_theme_settings_sidebars_array() {
		return woodmart_get_sidebars_array( true );
	}
}

if ( ! function_exists( 'woodmart_pages_ids_from_template' ) ) {
	/**
	 * Get page id by template name.
	 *
	 * @param string $name Template name.
	 * @return array
	 */
	function woodmart_pages_ids_from_template( $name ) {
		$pages = get_pages(
			array(
				'meta_key'   => '_wp_page_template', // phpcs:ignore
				'meta_value' => $name . '.php', // phpcs:ignore
			)
		);

		$return = array();

		foreach ( $pages as $page ) {
			$return[] = $page->ID;
		}

		return $return;
	}
}


if ( ! function_exists( 'woodmart_get_col_sizes' ) ) {
	/**
	 * Get auto column sizes.
	 *
	 * @param integer $desktop_columns Desktop columns.
	 * @param string  $post_type Post type.
	 *
	 * @return array
	 */
	function woodmart_get_col_sizes( $desktop_columns, $post_type = '' ) {
		$desktop_columns = (int) $desktop_columns;

		$sizes = array(
			'1'  => array(
				'desktop' => '1',
				'tablet'  => '1',
				'mobile'  => '1',
			),
			'2'  => array(
				'desktop' => '2',
				'tablet'  => '2',
				'mobile'  => '1',
			),
			'3'  => array(
				'desktop' => '3',
				'tablet'  => '3',
				'mobile'  => '1',
			),
			'4'  => array(
				'desktop' => '4',
				'tablet'  => '4',
				'mobile'  => '1',
			),
			'5'  => array(
				'desktop' => '5',
				'tablet'  => '4',
				'mobile'  => '2',
			),
			'6'  => array(
				'desktop' => '6',
				'tablet'  => '4',
				'mobile'  => '2',
			),
			'7'  => array(
				'desktop' => '7',
				'tablet'  => '4',
				'mobile'  => '2',
			),
			'8'  => array(
				'desktop' => '8',
				'tablet'  => '4',
				'mobile'  => '2',
			),
			'9'  => array(
				'desktop' => '9',
				'tablet'  => '4',
				'mobile'  => '2',
			),
			'10' => array(
				'desktop' => '10',
				'tablet'  => '4',
				'mobile'  => '2',
			),
			'11' => array(
				'desktop' => '11',
				'tablet'  => '4',
				'mobile'  => '2',
			),
			'12' => array(
				'desktop' => '12',
				'tablet'  => '4',
				'mobile'  => '2',
			),
		);

		if ( 'product' === $post_type ) {
			$sizes['2']['mobile'] = '2';
			$sizes['3']['mobile'] = '2';
			$sizes['4']['mobile'] = '2';
		}

		return isset( $sizes[ $desktop_columns ] ) ? $sizes[ $desktop_columns ] : $sizes['3'];
	}
}

if ( ! function_exists( 'woodmart_get_grid_attrs' ) ) {
	/**
	 * Get grid attributes.
	 *
	 * @param array $settings Settings.
	 * @return string
	 */
	function woodmart_get_grid_attrs( $settings ) {
		$desktop_columns = isset( $settings['columns'] ) ? $settings['columns'] : '3';
		$tablet_columns  = isset( $settings['columns_tablet'] ) ? $settings['columns_tablet'] : 'auto';
		$mobile_columns  = isset( $settings['columns_mobile'] ) ? $settings['columns_mobile'] : 'auto';
		$post_type       = isset( $settings['post_type'] ) ? $settings['post_type'] : '';

		if ( isset( $tablet_columns['size'] ) ) {
			$tablet_columns = $tablet_columns['size'];
		}
		if ( isset( $mobile_columns['size'] ) ) {
			$mobile_columns = $mobile_columns['size'];
		}

		$auto_columns = woodmart_get_col_sizes( $desktop_columns, $post_type );
		$style_attrs  = '';

		if ( ! $tablet_columns || 'auto' === $tablet_columns ) {
			$tablet_columns = $auto_columns['tablet'];
		}

		if ( ! $mobile_columns || 'auto' === $mobile_columns ) {
			$mobile_columns = $auto_columns['mobile'];
		}

		$style_attrs .= '--wd-col-lg:' . $desktop_columns . ';';
		$style_attrs .= '--wd-col-md:' . $tablet_columns . ';';
		$style_attrs .= '--wd-col-sm:' . $mobile_columns . ';';

		if ( isset( $settings['spacing'] ) && '' !== (string) $settings['spacing'] ) {
			$style_attrs .= '--wd-gap-lg:' . $settings['spacing'] . 'px;';
		}

		if ( isset( $settings['spacing_tablet'] ) && 'false' === $settings['spacing_tablet'] ) {
			$settings['spacing_tablet'] = '';
		}
		if ( isset( $settings['spacing_mobile'] ) && 'false' === $settings['spacing_mobile'] ) {
			$settings['spacing_mobile'] = '';
		}

		if ( isset( $settings['spacing_tablet'] ) && '' !== (string) $settings['spacing_tablet'] && ( empty( $settings['spacing'] ) || $settings['spacing'] !== $settings['spacing_tablet'] ) ) {
			$style_attrs .= '--wd-gap-md:' . $settings['spacing_tablet'] . 'px;';
		}

		if ( isset( $settings['spacing'], $settings['spacing_mobile'] ) && '' === (string) $settings['spacing_mobile'] && in_array( (int) $settings['spacing'], array( 20, 30 ), true ) ) {
			$settings['spacing_mobile'] = 10;
		}

		if ( isset( $settings['spacing_mobile'] ) && '' !== (string) $settings['spacing_mobile'] && ( empty( $settings['spacing_tablet'] ) || $settings['spacing_tablet'] !== $settings['spacing_mobile'] ) ) {
			$style_attrs .= '--wd-gap-sm:' . $settings['spacing_mobile'] . 'px;';
		}

		return $style_attrs;
	}
}

if ( ! function_exists( 'woodmart_get_wide_items_array' ) ) {
	/**
	 * Get wide items array.
	 *
	 * @param array $different_sizes Different sizes.
	 * @return mixed|null
	 */
	function woodmart_get_wide_items_array( $different_sizes = false ) {
		$items_wide = apply_filters( 'woodmart_wide_items', array( 5, 6, 7, 8, 13, 14 ) );

		if ( is_array( $different_sizes ) ) {
			$items_wide = apply_filters( 'woodmart_wide_items', $different_sizes );
		}

		return $items_wide;
	}
}

if ( ! function_exists( 'woodmart_get_custom_conditions_list' ) ) {
	/**
	 * Get custom conditions list
	 *
	 * @return array
	 */
	function woodmart_get_custom_conditions_list() {
		return array(
			'search'         => esc_html__( 'Search results', 'woodmart' ),
			'blog'           => esc_html__( 'Default "Your Latest Posts" screen', 'woodmart' ),
			'front'          => esc_html__( 'Front page', 'woodmart' ),
			'archives'       => esc_html__( 'All archives', 'woodmart' ),
			'author'         => esc_html__( 'Author archives', 'woodmart' ),
			'error404'       => esc_html__( '404 error screens', 'woodmart' ),
			'shop'           => esc_html__( 'Shop page', 'woodmart' ),
			'single_product' => esc_html__( 'Single product', 'woodmart' ),
			'cart'           => esc_html__( 'Cart page', 'woodmart' ),
			'checkout'       => esc_html__( 'Checkout page', 'woodmart' ),
			'account'        => esc_html__( 'Account pages', 'woodmart' ),
			'logged_in'      => esc_html__( 'Is user logged in', 'woodmart' ),
			'is_mobile'      => esc_html__( 'Is mobile device', 'woodmart' ),
			'is_rtl'         => esc_html__( 'Is RTL', 'woodmart' ),
		);
	}
}

if ( ! function_exists( 'woodmart_add_editable_post_to_admin_bar' ) ) {
	/**
	 * Set template for admin bar.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	function woodmart_add_editable_post_to_admin_bar( $post_id ) {
		if ( ! woodmart_is_pjax() && is_user_logged_in() && is_admin_bar_showing() && in_array( get_post_type( $post_id ), array( 'cms_block', 'wd_popup', 'wd_floating_block', 'woodmart_layout' ), true ) ) {
			global $woodmart_editable_posts_bar_data;

			$edit_url = get_edit_post_link( $post_id );

			if ( woodmart_is_elementor_installed() && Plugin::$instance->documents->get( $post_id )->is_built_with_elementor() ) {
				$edit_url = Plugin::$instance->documents->get( $post_id )->get_edit_url();
			}

			$woodmart_editable_posts_bar_data[] = array(
				'id'       => $post_id,
				'title'    => get_the_title( $post_id ),
				'type'     => get_post_type( $post_id ),
				'edit_url' => $edit_url,
			);
		}
	}
}

if ( ! function_exists( 'woodmart_get_post_meta_value' ) ) {
	/**
	 * Get post meta value considering Elementor settings.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $meta_key Meta key.
	 * @return mixed
	 */
	function woodmart_get_post_meta_value( $post_id, $meta_key ) {
		if ( woodmart_is_elementor_installed() ) {
			$doc = Plugin::$instance->documents->get( $post_id );

			if ( $doc && $doc->is_built_with_elementor() ) {
				$elementor_options = $doc->get_settings();
				if ( isset( $elementor_options[ 'wd_' . $meta_key ] ) ) {
					return $elementor_options[ 'wd_' . $meta_key ];
				}
			}
		}

		return get_post_meta( $post_id, $meta_key, true );
	}
}

if ( ! function_exists( 'woodmart_get_theme_settings_product_loop_items_array' ) ) {
	/**
	 * Function to get array of Product Loop Items in theme settings array style.
	 *
	 * @return array
	 */
	function woodmart_get_theme_settings_product_loop_items_array() {
		$args         = array(
			'posts_per_page' => 100,
			'post_type'      => 'woodmart_layout',
			'meta_key'       => 'wd_layout_type', // phpcs:ignore
			'meta_value'     => 'product_loop_item', // phpcs:ignore
		);
		$blocks_posts = get_posts( $args );
		$array        = array();
		foreach ( $blocks_posts as $post ) {
			$array[ $post->ID ] = array(
				'name'  => $post->post_title . ' (ID:' . $post->ID . ')',
				'value' => $post->ID,
			);
		}

		return $array;
	}
}

if ( ! function_exists( 'woodmart_is_builder_editor_preview' ) ) {
	/**
	 * Check if we are in Elementor or Gutenberg editor preview.
	 *
	 * @return boolean
	 */
	function woodmart_is_builder_editor_preview() {
		if (
			(
				function_exists( 'wd_gutenberg_is_rest_api' ) &&
				wd_gutenberg_is_rest_api()
			) ||
			(
				function_exists( 'woodmart_elementor_is_edit_mode' ) &&
				woodmart_elementor_is_edit_mode()
			)
		) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'woodmart_is_gutenberg_blocks_enabled' ) ) {
	/**
	 * Enable Gutenberg blocks.
	 *
	 * @return array[]
	 */
	function woodmart_is_gutenberg_blocks_enabled() {
		return apply_filters( 'woodmart_is_gutenberg_blocks_enabled', true );
	}
}

if ( ! function_exists( 'woodmart_get_document_title' ) ) {
	/**
	 * Returns document title for the current page.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	function woodmart_get_document_title() {
		$title = wp_get_document_title();

		$post_meta = get_post_meta( woodmart_get_the_ID(), '_yoast_wpseo_title', true );
		if ( is_object( get_queried_object() ) && property_exists( get_queried_object(), 'term_id' ) && function_exists( 'YoastSEO' ) ) {
			$taxonomy_helper = YoastSEO()->helpers->taxonomy;
			$meta            = $taxonomy_helper->get_term_meta( get_queried_object() );

			if ( isset( $meta['wpseo_title'] ) && $meta['wpseo_title'] ) {
				$title = wpseo_replace_vars( $meta['wpseo_title'], get_queried_object() );
			}
		} elseif ( $post_meta && function_exists( 'wpseo_replace_vars' ) ) {
			$title = wpseo_replace_vars( $post_meta, get_post( woodmart_get_the_ID() ) );
		}

		return $title;
	}
}

if ( ! function_exists( 'woodmart_get_backdrop_filter_css_parts' ) ) {
	/**
	 * Get backdrop filter CSS parts based on control values.
	 *
	 * @param array $control_value Associative array of filter values (e.g., 'blur' => 5).
	 *
	 * @return array List of CSS filter function strings (e.g., ['blur(5px)', 'brightness(1.2)']).
	 */
	function woodmart_get_backdrop_filter_css_parts( $control_value ) {
		$parts          = array();
		$default_values = array(
			'blur'       => 0,
			'brightness' => 1,
			'contrast'   => 100,
			'grayscale'  => 0,
			'hue-rotate' => 0,
			'invert'     => 0,
			'opacity'    => 100,
			'saturate'   => 100,
			'sepia'      => 0,
		);
		$control_value  = shortcode_atts( $default_values, $control_value );

		foreach ( $control_value as $filter => $value ) {
			$default_value = isset( $default_values[ $filter ] ) ? $default_values[ $filter ] : '';

			if ( '' === $value || ! isset( $default_values[ $filter ] ) || $value === $default_value ) {
				continue;
			}

			switch ( $filter ) {
				case 'blur':
					$unit = 'px';
					break;
				case 'brightness':
					$unit = '';
					break;
				case 'hue-rotate':
					$unit = 'deg';
					break;
				default:
					$unit = '%';
					break;
			}

			$parts[] = $filter . '(' . $value . $unit . ')';
		}

		return $parts;
	}
}

if ( ! function_exists( 'woodmart_set_inline_style_deferred_context' ) ) {
	/**
	 * Increment or decrement deferred-inline counter.
	 *
	 * @param bool $enable True to increment (enable deferred), false to decrement (disable one level).
	 */
	function woodmart_set_inline_style_deferred_context( $enable = false ) {
		if ( $enable ) {
			$GLOBALS['wd_page_css_deferred'] = true;
		} else {
			$GLOBALS['wd_page_css_deferred'] = false;
		}
	}
}
