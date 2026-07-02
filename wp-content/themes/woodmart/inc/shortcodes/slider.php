<?php
/**
 * Shortcode for Slider element.
 *
 * @package woodmart
 */

use Elementor\Plugin;
use XTS\Admin\Modules\Options\Metaboxes;
use XTS\Modules\Styles_Storage;

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

if ( ! function_exists( 'woodmart_get_slide_data' ) ) {
	/**
	 * Get slide data.
	 *
	 * @param int    $id        Post ID.
	 * @param string $title     Post title.
	 * @param string $animation Animation type.
	 *
	 * @return string
	 */
	function woodmart_get_slide_data( $id, $title, $animation = '' ) {
		$attributes = '';
		$url        = get_edit_post_link( $id );

		if ( is_user_logged_in() && ! empty( $url ) ) {
			if ( 'elementor' === woodmart_get_current_page_builder() ) {
				$url = str_replace( 'action=edit', 'action=elementor', $url );
			}

			$data = array(
				'title' => $title,
				'url'   => $url,
			);

			$attributes .= ' data-slide=\'' . wp_json_encode( $data ) . '\'';
		}

		if ( 'distortion' === $animation ) {
			$bg_image_desktop = has_post_thumbnail( $id ) ? wp_get_attachment_url( get_post_thumbnail_id( $id ) ) : '';

			$meta_bg_image_desktop = get_post_meta( $id, 'bg_image_desktop', true );
			$image_desktop         = woodmart_get_post_meta_value( $id, 'image' );

			$bg_image_el = woodmart_get_post_meta_value( $id, 'bg_image' );
			if ( $bg_image_el ) {
				$meta_bg_image_desktop = $bg_image_el;
			}

			$bg_image_tablet = woodmart_get_post_meta_value( $id, 'bg_image_tablet' );
			$bg_image_mobile = woodmart_get_post_meta_value( $id, 'bg_image_mobile' );

			if ( is_array( $image_desktop ) && ! empty( $image_desktop['id'] ) ) {
				$image_size = woodmart_get_post_meta_value( $id, 'image_size' );

				if ( 'custom' === $image_size ) {
					$image_custom_dimensions = woodmart_get_post_meta_value( $id, 'image_custom_dimension' );

					if ( $image_custom_dimensions ) {
						$image_width  = isset( $image_custom_dimensions['width'] ) ? $image_custom_dimensions['width'] : '';
						$image_height = isset( $image_custom_dimensions['height'] ) ? $image_custom_dimensions['height'] : '';
					} else {
						$image_width  = get_post_meta( $id, 'image_size_custom_width', true );
						$image_height = get_post_meta( $id, 'image_size_custom_height', true );
					}

					if ( $image_width || $image_height ) {
						$image_size = array( (int) $image_width, (int) $image_height );
					} else {
						$image_size = 'full';
					}
				}

				$image_size = $image_size ? $image_size : 'full';

				$bg_image_desktop = woodmart_otf_get_image_url( $image_desktop['id'], $image_size );
			} elseif ( is_array( $meta_bg_image_desktop ) && ! empty( $meta_bg_image_desktop['id'] ) ) {
				if ( isset( $meta_bg_image_desktop['size'] ) ) {
					$image_size = $meta_bg_image_desktop['size'];
				} else {
					$image_size = get_post_meta( $id, 'bg_image_desktop_size', true );
				}

				if ( 'custom' === $image_size ) {
					$image_width  = get_post_meta( $id, 'bg_image_desktop_size_custom_width', true );
					$image_height = get_post_meta( $id, 'bg_image_desktop_size_custom_height', true );

					if ( $image_width || $image_height ) {
						$image_size = array( (int) $image_width, (int) $image_height );
					} else {
						$image_size = 'full';
					}
				}

				$image_size = $image_size ? $image_size : 'full';

				$meta_bg_image_desktop = woodmart_otf_get_image_url( $meta_bg_image_desktop['id'], $image_size );
			}

			if ( ! is_array( $meta_bg_image_desktop ) && $meta_bg_image_desktop ) {
				$bg_image_desktop = $meta_bg_image_desktop;
			}
			if ( is_array( $bg_image_tablet ) && ! empty( $bg_image_tablet['id'] ) ) {
				if ( isset( $bg_image_tablet['size'] ) ) {
					$bg_image_size_tablet = $bg_image_tablet['size'];
				} else {
					$bg_image_size_tablet = get_post_meta( $id, 'bg_image_tablet_size', true );
				}

				if ( 'custom' === $bg_image_size_tablet ) {
					$image_width  = get_post_meta( $id, 'bg_image_tablet_size_custom_width', true );
					$image_height = get_post_meta( $id, 'bg_image_tablet_size_custom_height', true );

					if ( $image_width || $image_height ) {
						$bg_image_size_tablet = array( (int) $image_width, (int) $image_height );
					} else {
						$bg_image_size_tablet = 'full';
					}
				}

				$bg_image_size_tablet = $bg_image_size_tablet ? $bg_image_size_tablet : 'full';

				$bg_image_tablet = woodmart_otf_get_image_url( $bg_image_tablet['id'], $bg_image_size_tablet );
			}
			if ( is_array( $bg_image_mobile ) && ! empty( $bg_image_mobile['url'] ) ) {
				if ( isset( $bg_image_mobile['size'] ) ) {
					$bg_image_size_mobile = $bg_image_mobile['size'];
				} else {
					$bg_image_size_mobile = get_post_meta( $id, 'bg_image_mobile_size', true );
				}

				if ( 'custom' === $bg_image_size_mobile ) {
					$image_width  = get_post_meta( $id, 'bg_image_mobile_size_custom_width', true );
					$image_height = get_post_meta( $id, 'bg_image_mobile_size_custom_height', true );

					if ( $image_width || $image_height ) {
						$bg_image_size_mobile = array( (int) $image_width, (int) $image_height );
					} else {
						$bg_image_size_mobile = 'full';
					}
				}

				$bg_image_size_mobile = $bg_image_size_mobile ? $bg_image_size_mobile : 'full';

				$bg_image_mobile = woodmart_otf_get_image_url( $bg_image_mobile['id'], $bg_image_size_mobile );
			}

			if ( $bg_image_desktop && ! is_array( $bg_image_desktop ) ) {
				$attributes .= ' data-image-url="' . $bg_image_desktop . '"';
			}

			if ( $bg_image_tablet && ! is_array( $bg_image_tablet ) ) {
				$attributes .= ' data-image-url-md="' . $bg_image_tablet . '"';
			}

			if ( $bg_image_mobile && ! is_array( $bg_image_mobile ) ) {
				$attributes .= ' data-image-url-sm="' . $bg_image_mobile . '"';
			}
		}

		return $attributes;
	}
}

if ( ! function_exists( 'woodmart_shortcode_slider' ) ) {
	/**
	 * Slider shortcode.
	 *
	 * @param array $atts Element settings.
	 *
	 * @return false|string|void
	 */
	function woodmart_shortcode_slider( $atts ) {
		$class            = '';
		$nav_classes      = '';
		$pagin_classes    = '';
		$wrapper_classes  = '';
		$carousel_classes = '';

		$parsed_atts = shortcode_atts(
			array(
				'slider'         => '',
				'el_class'       => '',
				'elementor'      => false,
				'carousel_sync'  => '',
				'sync_parent_id' => '',
				'sync_child_id'  => '',
				'sync_group_id'  => '',
			),
			$atts
		);

		$class .= ' ' . $parsed_atts['el_class'];

		$slider_term = get_term_by( 'slug', $parsed_atts['slider'], 'woodmart_slider' );

		if ( is_wp_error( $slider_term ) || empty( $slider_term ) ) {
			return;
		}

		$args = array(
			'posts_per_page'   => -1,
			'post_type'        => 'woodmart_slide',
			'orderby'          => 'menu_order',
			'order'            => 'ASC',
			'suppress_filters' => false,
			'tax_query'        => array( // phpcs:ignore
				array(
					'taxonomy' => 'woodmart_slider',
					'field'    => 'id',
					'terms'    => $slider_term->term_id,
				),
			),
		);

		$slides = get_posts( $args );

		if ( is_wp_error( $slides ) || empty( $slides ) ) {
			return;
		}

		$stretch_slider  = get_term_meta( $slider_term->term_id, 'stretch_slider', true );
		$stretch_content = get_term_meta( $slider_term->term_id, 'stretch_content', true );

		$carousel_id = 'slider-' . $slider_term->term_id;

		$animation            = get_term_meta( $slider_term->term_id, 'animation', true );
		$arrows_style         = get_term_meta( $slider_term->term_id, 'arrows_style', true );
		$pagination_style     = get_term_meta( $slider_term->term_id, 'pagination_style', true );
		$pagination_display   = get_term_meta( $slider_term->term_id, 'pagination_display', true );
		$scroll_carousel_init = get_term_meta( $slider_term->term_id, 'scroll_carousel_init', true );
		$pagination_color     = get_term_meta( $slider_term->term_id, 'pagination_color', true );

		if ( '' === $pagination_style ) {
			$pagination_style = '1';
		}

		if ( '' === $arrows_style ) {
			$arrows_style = 1;
		}

		$slide_speed = apply_filters( 'woodmart_slider_sliding_speed', 700 );

		$slider_atts = array(
			'carousel_id'             => $carousel_id,
			'slides_per_view'         => '1',
			'hide_pagination_control' => get_term_meta( $slider_term->term_id, 'pagination_style', true ) === '0' ? 'yes' : 'no',
			'hide_prev_next_buttons'  => get_term_meta( $slider_term->term_id, 'arrows_style', true ) === '0' ? 'yes' : 'no',
			'autoplay'                => ( get_term_meta( $slider_term->term_id, 'autoplay', true ) === 'on' ) ? 'yes' : 'no',
			'speed'                   => get_term_meta( $slider_term->term_id, 'autoplay_speed', true ) ? get_term_meta( $slider_term->term_id, 'autoplay_speed', true ) : 9000,
			'sliding_speed'           => $slide_speed,
			'content_animation'       => true,
			'autoheight'              => 'yes',
			'wrap'                    => 'yes',
			'effect'                  => $animation,
			'carousel_sync'           => $parsed_atts['carousel_sync'],
			'sync_parent_id'          => $parsed_atts['sync_parent_id'],
			'sync_child_id'           => $parsed_atts['sync_child_id'],
			'sync_group_id'           => $parsed_atts['sync_group_id'],
		);

		if ( is_user_logged_in() ) {
			$slider_atts['slider'] = array(
				'title' => $slider_term->name,
				'url'   => get_edit_term_link( $slider_term->term_id, 'woodmart_slider' ),
			);
		}

		if ( ! $parsed_atts['elementor'] ) {
			ob_start();
		}

		woodmart_enqueue_js_library( 'swiper' );
		woodmart_enqueue_js_script( 'swiper-carousel' );
		woodmart_enqueue_js_script( 'slider-element' );
		woodmart_enqueue_inline_style( 'swiper' );
		woodmart_enqueue_inline_style( 'slider' );

		if ( 'distortion' === $animation ) {
			woodmart_enqueue_inline_style( 'slider-anim-distortion' );
			woodmart_enqueue_js_script( 'slider-distortion' );
		}

		$wrapper_classes .= ' wd-anim-' . $animation;

		if ( 'on' === $stretch_slider ) {
			if ( 'wpb' === woodmart_get_current_page_builder() ) {
				$wrapper_classes .= ' wd-section-stretch-content';
			} else {
				$wrapper_classes .= ' wd-section-stretch';
			}

			if ( 'on' === $stretch_content ) {
				$wrapper_classes .= ' wd-container-full-width';
			}
		} else {
			$wrapper_classes .= ' wd-section-container';
		}

		if ( 'yes' === $slider_atts['autoplay'] ) {
			$wrapper_classes .= ' wd-autoplay-on';
		}

		if ( 'on' === $scroll_carousel_init ) {
			$carousel_classes .= ' scroll-init';
		}

		if ( $arrows_style ) {
			$nav_color = get_term_meta( $slider_term->term_id, 'navigation_color_scheme', true );

			if ( ! $nav_color ) {
				$nav_color = $pagination_color;
			}

			if ( in_array( $arrows_style, array( '2', '3' ), true ) ) {
				woodmart_enqueue_inline_style( 'slider-arrows' );
			}

			$nav_classes .= ' wd-style-' . $arrows_style;
			$nav_classes .= ' wd-pos-sep';

			if ( 'on' === get_term_meta( $slider_term->term_id, 'arrows_custom_settings', true ) ) {
				$arrows_hover_style = get_term_meta( $slider_term->term_id, 'arrows_hover_style', true );

				if ( $arrows_hover_style && 'disable' !== $arrows_hover_style ) {
					$nav_classes .= ' wd-hover-' . $arrows_hover_style;
				}
			}

			if ( $nav_color ) {
				$nav_classes .= ' color-scheme-' . $nav_color;
			}
		}

		if ( $pagination_style ) {
			$pagination_hr_align = get_term_meta( $slider_term->term_id, 'pagination_horizon_align', true );

			if ( ! $pagination_hr_align ) {
				$pagination_hr_align = 'center';
			}

			if ( in_array( $pagination_style, array( '1', '3' ), true ) ) {
				$pagin_classes .= ' wd-style-shape-' . $pagination_style;
			} elseif ( in_array( $pagination_style, array( '2' ), true ) ) {
				woodmart_enqueue_inline_style( 'slider-dots-style-2' );

				$pagin_classes .= ' wd-style-number-' . $pagination_style;
			}

			if ( '3' === $pagination_style ) {
				woodmart_enqueue_inline_style( 'slider-dots-style-3' );
			}

			if ( '4' === $pagination_style ) {
				woodmart_enqueue_inline_style( 'slider-pagin-style-4' );
			}

			$pagin_classes .= ' text-' . $pagination_hr_align;

			if ( $pagination_color ) {
				$pagin_classes .= ' color-scheme-' . $pagination_color;
			}

			if ( '4' === $pagination_style ) {
				$pagin_classes .= ' wd-style-text-1';
			}
		}

		$first_slide_key = array_key_first( $slides );

		woodmart_get_slider_css( $slider_term->term_id, $carousel_id, $slides );
		?>

		<div id="<?php echo esc_attr( $carousel_id ); ?>" data-id="<?php echo esc_html( $slider_term->term_id ); ?>" class="wd-slider wd-carousel-container<?php echo esc_attr( $wrapper_classes ); ?>">
			<div class="wd-carousel-inner">
				<div class="wd-carousel wd-grid<?php echo esc_attr( $carousel_classes ); ?>" <?php echo woodmart_get_carousel_attributes( $slider_atts ); // phpcs:ignore ?>>
					<div class="wd-carousel-wrap<?php echo esc_attr( $class ); ?>">
						<?php foreach ( $slides as $key => $slide ) : ?>
							<?php
							$slide_id        = 'slide-' . $slide->ID;
							$slide_animation = woodmart_get_post_meta_value( $slide->ID, 'slide_animation' );
							$slide_classes   = '';
							$slide_image     = woodmart_get_post_meta_value( $slide->ID, 'image' );

							if ( $key === $first_slide_key ) {
								$slide_classes .= ' woodmart-loaded';
								woodmart_lazy_loading_deinit( true );
							}

							// Link.
							$link              = get_post_meta( $slide->ID, 'link', true );
							$link_target_blank = get_post_meta( $slide->ID, 'link_target_blank', true );

							$slide_attrs = woodmart_get_slide_data( $slide->ID, $slide->post_title, $animation );

							if ( ( '2' === $pagination_style && 'text' === $pagination_display ) || '4' === $pagination_style ) {
								$slide_attrs .= ' data-pagination-text="' . esc_attr( $slide->post_title ) . '"';
							}
							?>
							<div id="<?php echo esc_attr( $slide_id ); ?>" class="wd-slide wd-carousel-item<?php echo esc_attr( $slide_classes ); ?>" <?php echo $slide_attrs; // phpcs:ignore ?>>
								<?php
								if ( ! empty( $slide_animation ) && 'none' !== $slide_animation ) {
									woodmart_enqueue_inline_style( 'mod-animations-transform-base' );
									woodmart_enqueue_inline_style( 'mod-animations-transform' );
									woodmart_enqueue_inline_style( 'mod-transform' );
									woodmart_enqueue_js_script( 'css-animations' );
								}
								?>
								<div class="container wd-slide-container<?php echo woodmart_get_slide_class( $slide->ID ); // phpcs:ignore ?>">
									<div class="wd-slide-inner<?php echo ( ! empty( $slide_animation ) && 'none' !== $slide_animation ) ? ' wd-animation wd-transform wd-animation-normal wd-animation-' . esc_attr( $slide_animation ) : ''; // phpcs:ignore ?>">
										<?php if ( woodmart_is_elementor_installed() && Elementor\Plugin::$instance->documents->get( $slide->ID )->is_built_with_elementor() ) : ?>
											<?php echo woodmart_elementor_get_content( $slide->ID, apply_filters('woodamrt_enqueue_inline_slide_style', true ) ); // phpcs:ignore ?>
										<?php else : ?>
											<?php echo apply_filters( 'the_content', $slide->post_content ); // phpcs:ignore ?>
										<?php endif; ?>
									</div>
								</div>

								<div class="wd-slide-bg wd-fill">
									<?php if ( ! empty( $slide_image['id'] ) ) : ?>
										<?php
										$image_size = woodmart_get_post_meta_value( $slide->ID, 'image_size' );

										if ( 'custom' === $image_size ) {
											$image_custom_dimensions = woodmart_get_post_meta_value( $slide->ID, 'image_custom_dimension' );

											if ( $image_custom_dimensions ) {
												$image_width  = isset( $image_custom_dimensions['width'] ) ? $image_custom_dimensions['width'] : '';
												$image_height = isset( $image_custom_dimensions['height'] ) ? $image_custom_dimensions['height'] : '';
											} else {
												$image_width  = get_post_meta( $slide->ID, 'image_size_custom_width', true );
												$image_height = get_post_meta( $slide->ID, 'image_size_custom_height', true );
											}

											if ( $image_width || $image_height ) {
												$image_size = array( (int) $image_width, (int) $image_height );
											} else {
												$image_size = 'full';
											}
										}

										$image_size = $image_size ? $image_size : 'full';

										echo woodmart_otf_get_image_html( $slide_image['id'], $image_size ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										?>
									<?php endif; ?>
								</div>

								<?php if ( $link ) : ?>
									<a href="<?php echo esc_url( $link ); ?>"<?php echo $link_target_blank ? ' target="_blank"' : ''; ?> class="wd-slide-link wd-fill" aria-label="<?php esc_attr_e( 'Slide link', 'woodmart' ); ?>"></a>
								<?php endif; ?>
							</div>

							<?php if ( $key === $first_slide_key ) : ?>
								<?php woodmart_lazy_loading_init(); ?>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				</div>

				<?php if ( $arrows_style ) : ?>
					<?php woodmart_enqueue_inline_style( 'swiper-arrows' ); ?>
					<div class="wd-nav-arrows wd-slider-arrows wd-custom-style<?php echo esc_attr( $nav_classes ); ?>">
						<div class="wd-btn-arrow wd-prev">
							<div class="wd-arrow-inner"></div>
						</div>
						<div class="wd-btn-arrow wd-next">
							<div class="wd-arrow-inner"></div>
						</div>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( $pagination_style ) : ?>
				<?php woodmart_enqueue_inline_style( 'swiper-pagin' ); ?>
				<div class="wd-nav-pagin-wrap wd-slider-pagin wd-custom-style<?php echo esc_attr( $pagin_classes ); ?>">
					<ul class="wd-nav-pagin"></ul>
				</div>
			<?php endif; ?>
		</div>
		<?php

		if ( ! $parsed_atts['elementor'] ) {
			$output = ob_get_contents();
			ob_end_clean();

			return $output;
		}
	}
}

if ( ! function_exists( 'woodmart_get_slider_css' ) ) {
	/**
	 * Get slider CSS.
	 *
	 * @param int   $id     Post ID.
	 * @param int   $el_id  Element ID.
	 * @param array $slides Slides.
	 */
	function woodmart_get_slider_css( $id, $el_id, $slides ) {
		$v_align_value = array(
			'top'    => 'flex-start',
			'middle' => 'center',
			'bottom' => 'flex-end',
		);

		$height_type  = get_term_meta( $id, 'height_type', true );
		$aspect_ratio = get_term_meta( $id, 'aspect_ratio', true );

		$storage       = new Styles_Storage( 'term-' . $id, 'term', $id );
		$is_output_css = true;

		if ( ! get_metadata( 'term', $id, 'xts-term-' . $id . '-status', true ) && ! $storage->is_css_exists() ) {
			$css = Metaboxes::get_instance()->get_metabox_css( $id, 'xts_slider_metaboxes' );

			if ( ! function_exists( 'WP_Filesystem' ) ) {
				require_once ABSPATH . '/wp-admin/includes/file.php';
			}

			if ( $css ) {
				$storage->reset_data();
				$storage->write( $css );
			} else {
				$storage->write( '' );
			}
		}

		$storage->print_styles_inline();

		if ( 'elementor' === woodmart_get_current_page_builder() && woodmart_is_elementor_installed() ) {
			$breakpoints = array(
				'mobile' => array(
					'value' => 767.98,
				),
			);

			if ( Plugin::$instance->experiments->is_feature_active( 'additional_custom_breakpoints' ) && Plugin::$instance->breakpoints->has_custom_breakpoints() ) {
				$breakpoints = wp_parse_args( Plugin::$instance->breakpoints->get_breakpoints_config(), $breakpoints );
			}

			$doc = Plugin::$instance->documents->get( $id );

			if ( $doc && $doc->is_built_with_elementor() ) {
				$elementor_options = $doc->get_settings_for_display();

				if ( isset( $elementor_options['wd_vertical_align'] ) || isset( $elementor_options['wd_horizontal_align'] ) || isset( $elementor_options['wd_content_without_padding'] ) || isset( $elementor_options['wd_content_full_width'] ) ) {
					$is_output_css = false;
				}
			} elseif ( woodmart_elementor_is_edit_mode() || woodmart_elementor_is_preview_mode() ) {
				$is_output_css = false;
			}

			$mobile_breakpoint = $breakpoints['mobile']['value'];
		} elseif ( 'wpb' === woodmart_get_current_page_builder() ) {
			$mobile_breakpoint = 767.98;
		} else {
			$mobile_breakpoint = 768.98;
		}

		$devices = array(
			'desktop' => array( 'max' => null ),
			'tablet'  => array( 'max' => 1024 ),
			'mobile'  => array( 'max' => $mobile_breakpoint ),
		);

		echo '<style>';

		foreach ( $slides as $slide ) {
			$bg_color           = get_post_meta( $slide->ID, 'bg_color', true );
			$content_full_width = get_post_meta( $slide->ID, 'content_full_width', true );
			$slide_image        = get_post_meta( $slide->ID, 'image', true );

			$post_thumb_id  = get_post_thumbnail_id( $slide->ID );
			$post_thumb_url = $post_thumb_id ? wp_get_attachment_url( $post_thumb_id ) : '';

			foreach ( $devices as $device => $settings ) {
				$width   = get_post_meta( $slide->ID, ( 'desktop' === $device ? 'content_width' : "content_width_{$device}" ), true );
				$v_align = get_post_meta( $slide->ID, ( 'desktop' === $device ? 'vertical_align' : "vertical_align_{$device}" ), true );
				$h_align = get_post_meta( $slide->ID, ( 'desktop' === $device ? 'horizontal_align' : "horizontal_align_{$device}" ), true );

				$bg_image            = get_post_meta( $slide->ID, "bg_image_{$device}", true );
				$bg_image_size       = get_post_meta( $slide->ID, "bg_image_size_{$device}", true );
				$bg_image_position   = get_post_meta( $slide->ID, "bg_image_position_{$device}", true );
				$bg_image_position_x = get_post_meta( $slide->ID, "bg_image_position_x_{$device}", true );
				$bg_image_position_y = get_post_meta( $slide->ID, "bg_image_position_y_{$device}", true );

				$image_object_fit        = get_post_meta( $slide->ID, ( 'desktop' === $device ? 'image_object_fit' : "image_object_fit_{$device}" ), true );
				$image_object_position   = get_post_meta( $slide->ID, ( 'desktop' === $device ? 'image_object_position' : "image_object_position_{$device}" ), true );
				$image_object_position_x = get_post_meta( $slide->ID, ( 'desktop' === $device ? 'image_object_position_x' : "image_object_position_x_{$device}" ), true );
				$image_object_position_y = get_post_meta( $slide->ID, ( 'desktop' === $device ? 'image_object_position_y' : "image_object_position_y_{$device}" ), true );

				$image_dimensions = '';

				if ( 'as_image' === $height_type && ! empty( $slide_image['id'] ) ) {
					$image_data = wp_get_attachment_metadata( $slide_image['id'] );
					$image_size = get_post_meta( $slide->ID, 'image_size', true );

					if ( 'custom' === $image_size ) {
						$image_width  = get_post_meta( $slide->ID, 'image_size_custom_width', true );
						$image_height = get_post_meta( $slide->ID, 'image_size_custom_height', true );
						$image_size   = ( $image_width || $image_height ) ? $image_width . 'x' . $image_height : 'full';
					}

					$image_size = $image_size ? $image_size : 'full';

					if ( 'full' !== $image_size && ! empty( $image_data['sizes'][ $image_size ] ) ) {
						$image_dimensions = $image_data['sizes'][ $image_size ]['width'] . '/' . $image_data['sizes'][ $image_size ]['height'];
					} elseif ( ! empty( $image_data['width'] ) && ! empty( $image_data['height'] ) ) {
						$image_dimensions = $image_data['width'] . '/' . $image_data['height'];
					}
				} elseif ( is_array( $bg_image ) && ! empty( $bg_image['id'] ) ) {
					$image_size = $bg_image_size;

					if ( 'custom' === $image_size ) {
						$image_width  = get_post_meta( $slide->ID, "bg_image_{$device}_size_custom_width", true );
						$image_height = get_post_meta( $slide->ID, "bg_image_{$device}_size_custom_height", true );
						$image_size   = ( $image_width || $image_height ) ? array( (int) $image_width, (int) $image_height ) : 'full';
					}

					$image_size = $image_size ? $image_size : 'full';
					$image_data = wp_get_attachment_metadata( $bg_image['id'] );

					if ( 'as_image' === $height_type && ! empty( $image_data['width'] ) && ! empty( $image_data['height'] ) ) {
						if ( is_string( $image_size ) && 'full' !== $image_size && ! empty( $image_data['sizes'][ $image_size ] ) ) {
							$image_dimensions = $image_data['sizes'][ $image_size ]['width'] . '/' . $image_data['sizes'][ $image_size ]['height'];
						} else {
							$image_dimensions = $image_data['width'] . '/' . $image_data['height'];
						}
					}

					$bg_image = woodmart_otf_get_image_url( $bg_image['id'], $image_size );
				} elseif ( 'desktop' === $device && $post_thumb_id ) {
					$bg_image = $post_thumb_url;

					if ( 'as_image' === $height_type ) {
						$image_data = wp_get_attachment_metadata( $post_thumb_id );
						if ( ! empty( $image_data['width'] ) && ! empty( $image_data['height'] ) ) {
							$image_dimensions = $image_data['width'] . '/' . $image_data['height'];
						}
					}
				} else {
					$bg_image = '';
				}
				?>

				<?php if ( $settings['max'] ) : ?>
					@media (max-width: <?php echo esc_attr( $settings['max'] ); ?>px) {
				<?php endif; ?>

				<?php if ( 'as_image' === $height_type && $image_dimensions ) : ?>
					#slide-<?php echo esc_attr( $slide->ID ); ?> {
						<?php woodmart_maybe_set_css_rule( '--wd-aspect-ratio', $image_dimensions ); ?>
					}
				<?php endif; ?>

				<?php if ( ! $is_output_css ) : ?>
					<?php if ( $settings['max'] ) : ?>
						}
					<?php endif; ?>

					<?php continue; ?>
				<?php endif; ?>

				<?php if ( $v_align || $h_align ) : ?>
					#slide-<?php echo esc_attr( $slide->ID ); ?> .wd-slide-container {
					<?php if ( $v_align ) : ?>
						--wd-align-items: <?php echo esc_attr( $v_align_value[ $v_align ] ); ?>;
					<?php endif; ?>
					<?php if ( $h_align ) : ?>
						--wd-justify-content: <?php echo esc_attr( $h_align ); ?>;
					<?php endif; ?>
					}
				<?php endif; ?>

				<?php if ( ! $content_full_width && $width ) : ?>
					#slide-<?php echo esc_attr( $slide->ID ); ?> .wd-slide-inner {
						<?php woodmart_maybe_set_css_rule( 'max-width', $width ); ?>
					}
				<?php endif; ?>

				<?php if ( $bg_image ) : ?>
					#slide-<?php echo esc_attr( $slide->ID ); ?>.woodmart-loaded .wd-slide-bg {
						<?php woodmart_maybe_set_css_rule( 'background-image', $bg_image ); ?>
					}
				<?php endif; ?>

				<?php if ( ( 'desktop' === $device && $bg_color ) || $bg_image_size || $bg_image_position ) : ?>
					#slide-<?php echo esc_attr( $slide->ID ); ?> .wd-slide-bg {
						<?php
						if ( 'desktop' === $device ) {
							woodmart_maybe_set_css_rule( 'background-color', $bg_color );
						}
						if ( 'inherit' !== $bg_image_size ) {
							woodmart_maybe_set_css_rule( 'background-size', $bg_image_size );
						}
						if ( 'custom' !== $bg_image_position ) {
							woodmart_maybe_set_css_rule( 'background-position', $bg_image_position );
						} else {
							woodmart_maybe_set_css_rule( 'background-position', "{$bg_image_position_x} {$bg_image_position_y}" );
						}
						?>
					}
				<?php endif; ?>

				<?php if ( $image_object_fit || $image_object_position ) : ?>
					#slide-<?php echo esc_attr( $slide->ID ); ?> .wd-slide-bg img {
						<?php woodmart_maybe_set_css_rule( 'object-fit', $image_object_fit ); ?>
						<?php
						if ( 'custom' !== $image_object_position ) {
							woodmart_maybe_set_css_rule( 'object-position', $image_object_position );
						} elseif ( $image_object_position_x || $image_object_position_y ) {
							woodmart_maybe_set_css_rule( 'object-position', "{$image_object_position_x} {$image_object_position_y}" );
						}
						?>
					}
				<?php endif; ?>

				<?php if ( $settings['max'] ) : ?>
					}
				<?php endif; ?>
				<?php
			}

			if ( get_post_meta( $slide->ID, '_wpb_shortcodes_custom_css', true ) ) {
				echo get_post_meta( $slide->ID, '_wpb_shortcodes_custom_css', true ); // phpcs:ignore
			}
			if ( get_post_meta( $slide->ID, 'woodmart_shortcodes_custom_css', true ) ) {
				echo get_post_meta( $slide->ID, 'woodmart_shortcodes_custom_css', true ); // phpcs:ignore
			}
		}

		echo '</style>';
	}
}

if ( ! function_exists( 'woodmart_maybe_set_css_rule' ) ) {
	/**
	 * Get CSS rule.
	 *
	 * @param string $rule CSS rule.
	 * @param string $value Value.
	 * @param string $before Before value.
	 * @param string $after After value.
	 */
	function woodmart_maybe_set_css_rule( $rule, $value = '', $before = '', $after = '' ) {
		if ( in_array( $rule, array( 'width', 'height', 'max-width', 'max-height' ), true ) && empty( $after ) ) {
			$after = 'px';
		}

		if ( in_array( $rule, array( 'background-image' ), true ) && ( empty( $before ) || empty( $after ) ) ) {
			$before = 'url(';
			$after  = ')';
		}

		echo ! empty( $value ) ? $rule . ':' . $before . $value . $after . ';' : ''; // phpcs:ignore
	}
}

if ( ! function_exists( 'woodmart_get_slider_class' ) ) {
	/**
	 * Get slider classes.
	 *
	 * @param int $id Slider ID.
	 *
	 * @return string
	 */
	function woodmart_get_slider_class( $id ) {
		$class = '';

		$arrows_style         = get_term_meta( $id, 'arrows_style', true );
		$pagination_style     = get_term_meta( $id, 'pagination_style', true );
		$pagination_hr_align  = get_term_meta( $id, 'pagination_horizon_align', true );
		$pagination_color     = get_term_meta( $id, 'pagination_color', true );
		$stretch_slider       = get_term_meta( $id, 'stretch_slider', true );
		$stretch_content      = get_term_meta( $id, 'stretch_content', true );
		$scroll_carousel_init = get_term_meta( $id, 'scroll_carousel_init', true );
		$animation            = get_term_meta( $id, 'animation', true );

		if ( ! $pagination_hr_align ) {
			$pagination_hr_align = 'center';
		}

		if ( '' === $pagination_style ) {
			$pagination_style = 1;
		}

		if ( '' === $arrows_style ) {
			$arrows_style = 1;
		}

		$class .= ' arrows-style-' . $arrows_style;
		$class .= ' pagin-style-' . $pagination_style;
		$class .= ' pagin-scheme-' . $pagination_color;
		$class .= ' anim-' . $animation;
		$class .= ' text-' . $pagination_hr_align;

		if ( 'on' === $scroll_carousel_init ) {
			$class .= ' scroll-init';
		}

		if ( 'on' === $stretch_slider ) {
			if ( 'wpb' === woodmart_get_current_page_builder() ) {
				$class .= ' wd-section-stretch-content';
			} else {
				$class .= ' wd-section-stretch';
			}

			if ( 'on' === $stretch_content ) {
				$class .= ' wd-container-full-width';
			}
		} else {
			$class .= ' wd-section-container';
		}

		return $class;
	}
}

if ( ! function_exists( 'woodmart_get_slide_class' ) ) {
	/**
	 * Get slide classes.
	 *
	 * @param int $id Post ID.
	 *
	 * @return string
	 */
	function woodmart_get_slide_class( $id ) {
		$class  = ' content-' . ( woodmart_get_post_meta_value( $id, 'content_full_width' ) ? 'full-width' : 'fixed' );
		$class .= woodmart_get_post_meta_value( $id, 'content_without_padding' ) ? ' wd-padding-off' : '';

		return apply_filters( 'woodmart_slide_classes', $class );
	}
}
