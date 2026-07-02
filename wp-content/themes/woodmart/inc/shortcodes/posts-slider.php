<?php
/**
 * Shortcode for Posts Slider element.
 *
 * @package woodmart
 */

use XTS\Gutenberg\Blocks_Assets;
use XTS\Gutenberg\Post_CSS;

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

if ( ! function_exists( 'woodmart_generate_posts_slider' ) ) {
	/**
	 * Generate posts slider shortcode.
	 *
	 * @param array  $atts Attributes.
	 * @param object $query WP_Query instance.
	 * @param array  $products Array of products.
	 * @return false|string|void
	 */
	function woodmart_generate_posts_slider( $atts, $query = false, $products = false ) {
		$parsed_atts = shortcode_atts(
			array_merge(
				woodmart_get_carousel_atts(),
				array(
					'el_class'                     => '',
					'el_id'                        => '',
					'wrapper_classes'              => '',
					'posts_query'                  => '',
					'highlighted_products'         => 0,
					'product_quantity'             => 0,
					'products_divider'             => 0,
					'products_bordered_grid'       => 0,
					'products_bordered_grid_style' => 'outside',
					'products_with_background'     => 0,
					'products_shadow'              => woodmart_get_opt( 'products_shadow' ),
					'products_color_scheme'        => 'default',
					'product_hover'                => woodmart_get_opt( 'products_hover' ),
					'product_custom_hover'         => woodmart_get_opt( 'product_custom_hover' ),
					'is_predefined_hover'          => false,
					'stretch_product'              => woodmart_get_opt( 'stretch_product_desktop' ),
					'stretch_product_tablet'       => woodmart_get_opt( 'stretch_product_tablet' ),
					'stretch_product_mobile'       => woodmart_get_opt( 'stretch_product_mobile' ),
					'spacing'                      => '',
					'spacing_tablet'               => '',
					'spacing_mobile'               => '',
					'blog_design'                  => 'default',
					'blog_carousel_design'         => 'masonry',
					'img_size'                     => 'large',
					'img_size_custom'              => '',
					'title'                        => '',
					'element_title'                => '',
					'element_title_tag'            => 'h4',
					'scroll_carousel_init'         => 'no',
					'lazy_loading'                 => 'no',
					'elementor'                    => false,
					'carousel_classes'             => '',
					'ajax_recently_viewed'         => '',
					'layout'                       => '',
					'items_per_page'               => 12,
					'woodmart_css_id'              => '',
					'grid_gallery'                 => '',
					'grid_gallery_control'         => '',
					'grid_gallery_enable_arrows'   => '',
					'parts_title'                  => true,
					'parts_meta'                   => true,
					'parts_text'                   => true,
					'parts_btn'                    => true,
					'css'                          => '',
					'inner_content'                => '',
				)
			),
			$atts
		);

		extract( $parsed_atts ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
		$is_ajax = ( defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_POST['action'] ) && $_POST['action'] === 'woodmart_get_recently_viewed_products' ); // phpcs:ignore

		if ( 'carousel' === $blog_design ) {
			woodmart_set_loop_prop( 'blog_layout', 'carousel' );
			woodmart_set_loop_prop( 'blog_design', $blog_carousel_design );
		}

		if ( ! $query && ! $products && function_exists( 'vc_build_loop_query' ) ) {
			list( $args, $query ) = vc_build_loop_query( $posts_query );
		}

		if ( ! $elementor ) {
			ob_start();
		}

		if ( ! empty( $el_id ) ) {
			$carousel_id = $el_id;
		} else {
			$carousel_id = 'carousel-' . wp_rand( 100, 999 );
		}

		$carousel_classes .= ' wd-carousel';
		$carousel_classes .= ' wd-grid';

		$wrapper_classes .= ( $element_title ) ? ' with-title' : '';

		if ( 'yes' === $lazy_loading ) {
			woodmart_lazy_loading_init( true );
			woodmart_enqueue_inline_style( 'lazy-loading' );
		}

		if ( isset( $query->query['post_type'] ) ) {
			$post_type = $query->query['post_type'];
		} elseif ( $products ) {
			$post_type = 'product';
		} else {
			$post_type = 'post';
		}

		if ( is_array( $post_type ) ) {
			$post_type = $post_type[0];
		}

		$carousel_atts = '';

		if ( $woodmart_css_id ) {
			$wrapper_classes .= ' wd-rs-' . $woodmart_css_id;
		}

		if ( function_exists( 'vc_shortcode_custom_css_class' ) ) {
			$wrapper_classes .= ' ' . vc_shortcode_custom_css_class( $css );
		}

		$arrows_hover_style = woodmart_get_opt( 'carousel_arrows_hover_style', '1' );

		if ( ! empty( $carousel_arrows_position ) ) {
			$nav_classes = ' wd-pos-' . $carousel_arrows_position;
		} elseif ( $highlighted_products ) {
			if ( $element_title ) {
				$nav_classes = ' wd-pos-together';
			} else {
				$nav_classes = ' wd-pos-sep';
			}
		} else {
			$nav_classes = ' wd-pos-' . woodmart_get_opt( 'carousel_arrows_position', 'sep' );
		}

		if ( $highlighted_products ) {
			$nav_classes .= ' wd-custom-style';
		}

		if ( ! $highlighted_products && 'disable' !== $arrows_hover_style ) {
			$nav_classes .= ' wd-hover-' . $arrows_hover_style;
		}

		if ( 'post' === $post_type ) {
			$wrapper_classes .= ' wd-posts wd-blog-element';

			woodmart_set_loop_prop( 'parts_title', $parts_title );
			woodmart_set_loop_prop( 'parts_meta', $parts_meta );
			woodmart_set_loop_prop( 'parts_text', $parts_text );
			woodmart_set_loop_prop( 'parts_btn', $parts_btn );
		}

		if ( 'product' === $post_type ) {
			if ( empty( $product_hover ) || 'inherit' === $product_hover ) {
				$product_hover_type         = woodmart_get_opt( 'products_hover_type' );
				$maybe_product_custom_hover = woodmart_get_opt( 'product_custom_hover' );

				$is_predefined_hover = true;

				if ( 'custom' === $product_hover_type && $maybe_product_custom_hover && 'publish' === get_post_status( $maybe_product_custom_hover ) ) {
					$product_hover        = 'custom';
					$product_custom_hover = $maybe_product_custom_hover;
				} else {
					$product_hover = woodmart_get_opt( 'products_hover' );
				}
			}

			woodmart_set_loop_prop( 'product_hover', $product_hover );
			woodmart_set_loop_prop( 'img_size', $img_size );

			$wrapper_classes .= ' wd-products-element wd-products products';

			if ( 'yes' === $ajax_recently_viewed ) {
				$default_atts = function_exists( 'woodmart_get_elementor_products_config' ) ? woodmart_get_elementor_products_config() : woodmart_get_default_product_shortcode_atts();

				$encoded_atts = wp_json_encode(
					array_map(
						'unserialize',
						array_diff_assoc(
							array_map( 'serialize', array_intersect_key( $parsed_atts, $default_atts ) ),
							array_map( 'serialize', $default_atts )
						)
					)
				);

				$carousel_atts .= ' data-atts=\'' . esc_attr( $encoded_atts ) . '\' ';

				if ( $query && ! $query->have_posts() ) {
					$wrapper_classes .= ' wd-hide';
				}
			}

			if ( $highlighted_products ) {
				$wrapper_classes .= ' wd-highlighted-products';

				woodmart_enqueue_inline_style( 'highlighted-product' );
			}

			if ( 'custom' === $product_hover && ! empty( $product_custom_hover ) ) {
				if ( woodmart_is_gutenberg_blocks_enabled() ) {
					echo Blocks_Assets::get_instance()->get_inline_scripts( $product_custom_hover ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo Post_CSS::get_instance()->get_inline_blocks_css( $product_custom_hover ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}

				if ( ! $is_predefined_hover ) {
					woodmart_set_loop_prop( 'products_color_scheme', 'default' );
				}

				$wrapper_classes .= ' wd-loop-builder-on';
				$wrapper_classes .= ' wd-loop-item-wrap-' . $product_custom_hover;

				if ( get_post_meta( $product_custom_hover, 'wd_bordered_grid', true ) ) {
					woodmart_enqueue_inline_style( 'bordered-product' );

					$wrapper_classes .= ' products-bordered-grid';
				}

				if ( get_post_meta( $product_custom_hover, 'wd_stretch_product', true ) ) {
					woodmart_enqueue_inline_style( 'woo-opt-stretch-cont' );

					$wrapper_classes .= ' wd-stretch-cont-lg';
				}
				if ( get_post_meta( $product_custom_hover, 'wd_stretch_productTablet', true ) ) {
					woodmart_enqueue_inline_style( 'woo-opt-stretch-cont' );

					$wrapper_classes .= ' wd-stretch-cont-md';
				}
				if ( get_post_meta( $product_custom_hover, 'wd_stretch_productMobile', true ) ) {
					woodmart_enqueue_inline_style( 'woo-opt-stretch-cont' );

					$wrapper_classes .= ' wd-stretch-cont-sm';
				}
			} else {
				$wrapper_classes    .= ' wd-loop-builder-off';
				$is_predefined_hover = true;

				if ( ! empty( $grid_gallery ) ) {
					woodmart_set_loop_prop( 'grid_gallery', $grid_gallery );

					if ( ! empty( $grid_gallery_enable_arrows ) ) {
						woodmart_set_loop_prop( 'grid_gallery_enable_arrows', $grid_gallery_enable_arrows );
					}

					if ( ! empty( $grid_gallery_control ) ) {
						woodmart_set_loop_prop( 'grid_gallery_control', $grid_gallery_control );
					}
				}

				woodmart_set_loop_prop( 'products_color_scheme', $products_color_scheme );

				if ( 'default' !== $products_color_scheme && ( $products_bordered_grid || 'enable' === $products_bordered_grid ) && 'disable' !== $products_bordered_grid && 'outside' === $products_bordered_grid_style ) {
					$wrapper_classes .= ' wd-bordered-' . woodmart_loop_prop( 'products_color_scheme' );
				}

				if ( ! empty( $products_divider ) && 'small' === $product_hover ) {
					$wrapper_classes .= ' wd-with-divider';
				}

				if ( ( woodmart_loop_prop( 'stretch_product_desktop' ) || woodmart_loop_prop( 'stretch_product_tablet' ) || woodmart_loop_prop( 'stretch_product_mobile' ) ) && in_array( $product_hover, array( 'icons', 'alt', 'button', 'standard', 'tiled', 'quick', 'base', 'fw-button', 'buttons-on-hover' ), true ) ) {
					woodmart_enqueue_inline_style( 'woo-opt-stretch-cont' );
					woodmart_enqueue_inline_style( 'woo-opt-stretch-cont-predefined' );
					if ( woodmart_loop_prop( 'stretch_product_desktop' ) ) {
						$wrapper_classes .= ' wd-stretch-cont-lg';
					}
					if ( woodmart_loop_prop( 'stretch_product_tablet' ) ) {
						$wrapper_classes .= ' wd-stretch-cont-md';
					}
					if ( woodmart_loop_prop( 'stretch_product_mobile' ) ) {
						$wrapper_classes .= ' wd-stretch-cont-sm';
					}
				}

				if ( woodmart_loop_prop( 'product_quantity' ) ) {
					$wrapper_classes .= ' wd-quantity-enabled';
				}
			}

			if ( $is_predefined_hover ) {
				if ( $products_bordered_grid && ! $highlighted_products ) {
					woodmart_enqueue_inline_style( 'bordered-product' );
					woodmart_enqueue_inline_style( 'bordered-product-predefined' );

					if ( 'outside' === $products_bordered_grid_style ) {
						$wrapper_classes .= ' products-bordered-grid';
					} elseif ( 'inside' === $products_bordered_grid_style ) {
						$wrapper_classes .= ' products-bordered-grid-ins';
					}
				}

				if ( $products_with_background ) {
					woodmart_enqueue_inline_style( 'woo-opt-products-bg' );

					$wrapper_classes .= ' wd-products-with-bg';
				}

				if ( $products_shadow ) {
					woodmart_enqueue_inline_style( 'woo-opt-products-shadow' );

					$wrapper_classes .= ' wd-products-with-shadow';
				}
			}
		}

		if ( 'portfolio' === $post_type ) {
			$wrapper_classes .= ' wd-projects wd-portfolio-element';
		}

		if ( 'yes' === $scroll_carousel_init ) {
			$carousel_classes .= ' scroll-init';
		}

		if ( woodmart_get_opt( 'disable_owl_mobile_devices' ) ) {
			$wrapper_classes .= ' wd-carousel-dis-mb wd-off-md wd-off-sm';
		}

		if ( 'none' !== woodmart_get_opt( 'product_title_lines_limit' ) ) {
			woodmart_enqueue_inline_style( 'woo-opt-title-limit-predefined' );
			$wrapper_classes .= ' title-line-' . woodmart_get_opt( 'product_title_lines_limit' );
		}

		if ( $el_class ) {
			$carousel_classes .= ' ' . $el_class;
		}

		$parsed_atts['carousel_id'] = $carousel_id;
		$parsed_atts['post_type']   = $post_type;

		$carousel_atts .= woodmart_get_carousel_attributes( $parsed_atts );

		woodmart_enqueue_js_library( 'swiper' );
		woodmart_enqueue_js_script( 'swiper-carousel' );
		woodmart_enqueue_inline_style( 'swiper' );

		if ( ( $query && $query->have_posts() ) || $products || 'yes' === $ajax_recently_viewed ) {
			?>
			<?php if ( ! $is_ajax ) : ?>

			<div id="<?php echo esc_attr( $carousel_id ); ?>" class="wd-carousel-container <?php echo esc_attr( $wrapper_classes ); ?>">
				<?php if ( $inner_content ) : ?>
					<?php echo do_shortcode( $inner_content ); ?>
				<?php endif; ?>

				<?php if ( $title || $element_title ) : ?>
					<?php
					$element_title_tag = in_array( $element_title_tag, array_keys( woodmart_get_allowed_html() ), true ) ? $element_title_tag : 'h4';

					printf(
						'<%1$s class="wd-el-title title slider-title element-title"><span>%2$s</span></%1$s>',
						esc_attr( apply_filters( 'woodmart_products_title_tag', $element_title_tag ) ),
						( $title ? esc_html( $title ) : esc_html( $element_title ) )
					);
					?>
				<?php endif; ?>
			<?php endif; ?>

				<div class="wd-carousel-inner">
					<div class="<?php echo esc_attr( $carousel_classes ); ?>" <?php echo wp_kses( $carousel_atts, true ); ?>>
						<div class="wd-carousel-wrap">
							<?php
							if ( $products ) {
								foreach ( $products as $product ) {
									woodmart_carousel_query_item( false, $product );
								}
							} else {
								while ( $query->have_posts() ) {
									woodmart_carousel_query_item( $query );
								}
							}
							?>
						</div>
					</div>

					<?php woodmart_get_carousel_nav_template( $nav_classes, $parsed_atts ); ?>
				</div>

			<?php if ( ! $is_ajax ) : ?>
				<?php woodmart_get_carousel_pagination_template( $parsed_atts ); ?>
				<?php woodmart_get_carousel_scrollbar_template( $parsed_atts ); ?>
			</div>
			<?php endif; ?>
			<?php
		}
		wp_reset_postdata();

		woodmart_reset_loop();

		if ( function_exists( 'wc_reset_loop' ) ) {
			wc_reset_loop();
		}

		if ( 'yes' === $lazy_loading ) {
			woodmart_lazy_loading_deinit();
		}

		if ( ! $elementor ) {
			$output = ob_get_contents();
			ob_end_clean();

			return $output;
		}
	}
}

if ( ! function_exists( 'woodmart_carousel_query_item' ) ) {
	/**
	 * Carousel query item template.
	 *
	 * @param object $query WP_Query instance.
	 * @param object $product WC_Product instance.
	 * @return void
	 */
	function woodmart_carousel_query_item( $query = false, $product = false ) {
		global $post;
		if ( $query ) {
			$query->the_post(); // Get post from query
		} elseif ( $product ) {
			$post_object = get_post( $product->get_id() );
			$post        = $post_object; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			setup_postdata( $post );
		}

		if ( get_option( 'woocommerce_hide_out_of_stock_items' ) === 'yes' && ! $product && is_object( $post ) ) {
			$product = wc_get_product( $post->ID );

			// Duplicate condition from content-product.php to remove the SLIDE wrapper.
			if ( $product && method_exists( $product, 'is_visible' ) && ! $product->is_visible() ) {
				return;
			}
		}

		?>
		<div class="wd-carousel-item">
			<?php if ( in_array( get_post_type(), array( 'product', 'product_variation' ), true ) && woodmart_woocommerce_installed() ) : ?>
				<?php woodmart_set_loop_prop( 'is_slider', true ); ?>
				<?php wc_get_template_part( 'content-product' ); ?>
			<?php elseif ( 'portfolio' === get_post_type() ) : ?>
				<?php get_template_part( 'content', 'portfolio-slider' ); ?>
			<?php else : ?>
				<?php get_template_part( 'templates/content', woodmart_get_blog_design_name( woodmart_loop_prop( 'blog_design' ), 'slider' ) ); ?>
			<?php endif ?>
		</div>
		<?php
	}
}
