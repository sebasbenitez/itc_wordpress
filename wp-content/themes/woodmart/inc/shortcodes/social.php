<?php
/**
 * Shortcode for Social element.
 *
 * @package woodmart
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

if ( ! function_exists( 'woodmart_shortcode_social' ) ) {
	/**
	 * Social shortcode
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Shortcode content.
	 *
	 * @return string
	 */
	function woodmart_shortcode_social( $atts, $content = '' ) {
		$classes = apply_filters( 'vc_shortcodes_css_class', '', '', $atts );

		$links_atts = array(
			'fb_link'         => '',
			'twitter_link'    => '',
			'bluesky_link'    => '',
			'isntagram_link'  => '',
			'threads_link'    => '',
			'pinterest_link'  => '',
			'youtube_link'    => '',
			'tumblr_link'     => '',
			'linkedin_link'   => '',
			'vimeo_link'      => '',
			'flickr_link'     => '',
			'github_link'     => '',
			'dribbble_link'   => '',
			'behance_link'    => '',
			'soundcloud_link' => '',
			'spotify_link'    => '',
			'ok_link'         => '',
			'vk_link'         => '',
			'whatsapp_link'   => '',
			'snapchat_link'   => '',
			'tg_link'         => '',
			'viber_link'      => '',
			'tiktok_link'     => '',
			'discord_link'    => '',
			'yelp_link'       => '',
		);

		$default_atts = array(
			'show_label'          => 'no',
			'label_text'          => esc_html__( 'Share: ', 'woodmart' ),
			'is_element'          => false,
			'layout'              => '',
			'type'                => 'share',
			'social_links_source' => 'theme_settings',
			'align'               => 'center',
			'tooltip'             => 'no',
			'style'               => 'default',
			'size'                => 'default',
			'form'                => 'circle',
			'color'               => '',
			'css_animation'       => 'none',
			'el_class'            => '',
			'el_id'               => '',
			'title_classes'       => '',
			'page_link'           => false,
			'elementor'           => false,
			'sticky'              => false,
			'css'                 => '',
		);

		$atts = shortcode_atts( array_merge( $default_atts, $links_atts ), $atts );

		if ( 'follow' === $atts['type'] && 'theme_settings' === $atts['social_links_source'] ) {
			foreach ( array_keys( $links_atts ) as $link_option_name ) {
				$atts[ $link_option_name ] = woodmart_get_opt( $link_option_name, '' );
			}
		}

		extract( $atts ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract

		$target        = '_blank';
		$title_classes = $title_classes ? ' ' . $title_classes : '';
		$classes      .= ' wd-social-icons';

		if ( function_exists( 'vc_shortcode_custom_css_class' ) ) {
			$classes .= ' ' . vc_shortcode_custom_css_class( $css );
		}

		$classes .= ! empty( $layout ) ? ' wd-layout-' . $layout : '';
		$classes .= $style ? ' wd-style-' . $style : '';
		$classes .= $size ? ' wd-size-' . $size : '';
		$classes .= ' social-' . $type;
		$classes .= $form ? ' wd-shape-' . $form : '';
		$classes .= ( $el_class ) ? ' ' . $el_class : '';

		if ( $color ) {
			$classes .= ' color-scheme-' . $color;
		}

		$classes .= woodmart_get_css_animation( $css_animation );

		if ( $align ) {
			$classes .= ' text-' . $align;
		}

		$thumb_id   = get_post_thumbnail_id();
		$thumb_url  = wp_get_attachment_image_src( $thumb_id, 'thumbnail-size', true );
		$page_title = get_the_title();

		if ( ! $page_link ) {
			$page_link = get_the_permalink();
		}

		if ( woodmart_woocommerce_installed() ) {
			if ( is_shop() ) {
				$page_link = get_permalink( get_option( 'woocommerce_shop_page_id' ) );
			} elseif ( is_product_category() || is_category() ) {
				$page_link = get_category_link( get_queried_object()->term_id );
			} elseif ( is_tax() ) {
				$page_link = get_term_link( get_queried_object()->term_id );
			}
		}

		if ( is_home() && ! is_front_page() ) {
			$page_link = get_permalink( get_option( 'page_for_posts' ) );
		}

		if ( ! $elementor ) {
			ob_start();
		}

		$tooltip_class = 'yes' === $tooltip ? 'wd-tooltip' : '';
		?>
			<div
			<?php if ( $el_id ) : ?>
			id="<?php echo esc_attr( $el_id ); ?>"
			<?php endif ?>
			class="<?php echo esc_attr( $classes ); ?>">
				<?php
				woodmart_enqueue_inline_style( 'social-icons' );

				if ( 'default' !== $style ) {
					woodmart_enqueue_inline_style( 'social-icons-styles' );
				}
				?>
				<?php echo do_shortcode( $content ); ?>

				<?php if ( 'yes' === $show_label && $label_text ) : ?>
					<span class="wd-label<?php echo esc_attr( $title_classes ); ?>"><?php echo esc_html( $label_text ); ?></span>
				<?php endif; ?>

				<?php if ( ( 'share' === $type && woodmart_get_opt( 'share_fb' ) ) || ( 'follow' === $type && '' !== $fb_link ) ) : ?>
					<a rel="noopener noreferrer nofollow" href="<?php echo 'follow' === $type ? esc_attr( $fb_link ) : esc_url( 'https://www.facebook.com/sharer/sharer.php?u=' . $page_link ); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php echo esc_attr( $tooltip_class ); ?> wd-social-icon social-facebook" aria-label="<?php esc_attr_e( 'Facebook social link', 'woodmart' ); ?>">
						<span class="wd-icon"></span>
						<?php if ( $sticky ) : ?>
							<span class="wd-icon-name"><?php esc_html_e( 'Facebook', 'woodmart' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( ( 'share' === $type && woodmart_get_opt( 'share_twitter' ) ) || ( 'follow' === $type && '' !== $twitter_link ) ) : ?>
					<a rel="noopener noreferrer nofollow" href="<?php echo 'follow' === $type ? esc_attr( $twitter_link ) : esc_url( 'https://x.com/share?url=' . $page_link ); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php echo esc_attr( $tooltip_class ); ?> wd-social-icon social-twitter" aria-label="<?php esc_attr_e( 'X social link', 'woodmart' ); ?>">
						<span class="wd-icon"></span>
						<?php if ( $sticky ) : ?>
							<span class="wd-icon-name"><?php esc_html_e( 'X', 'woodmart' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( 'follow' === $type && '' !== $bluesky_link ) : ?>
					<a rel="noopener noreferrer nofollow" href="<?php echo esc_attr( $bluesky_link ); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php echo esc_attr( $tooltip_class ); ?> wd-social-icon social-bluesky" aria-label="<?php esc_attr_e( 'Bluesky social link', 'woodmart' ); ?>">
						<span class="wd-icon"></span>
						<?php if ( $sticky ) : ?>
							<span class="wd-icon-name"><?php esc_html_e( 'Bluesky', 'woodmart' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( ( 'share' === $type && woodmart_get_opt( 'share_email' ) ) || ( 'follow' === $type && woodmart_get_opt( 'social_email_links' ) ) ) : ?>
					<a rel="noopener noreferrer nofollow" href="mailto:<?php echo '?subject=' . esc_html__( 'Check%20this%20', 'woodmart' ) . esc_url( $page_link ); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php echo esc_attr( $tooltip_class ); ?> wd-social-icon social-email" aria-label="<?php esc_attr_e( 'Email social link', 'woodmart' ); ?>">
						<span class="wd-icon"></span>
						<?php if ( $sticky ) : ?>
							<span class="wd-icon-name"><?php esc_html_e( 'Email', 'woodmart' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( 'follow' === $type && '' !== $isntagram_link ) : ?>
					<a rel="noopener noreferrer nofollow" href="<?php echo 'follow' === $type ? esc_attr( $isntagram_link ) : '' . esc_url( $page_link ); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php echo esc_attr( $tooltip_class ); ?> wd-social-icon social-instagram" aria-label="<?php esc_attr_e( 'Instagram social link', 'woodmart' ); ?>">
						<span class="wd-icon"></span>
						<?php if ( $sticky ) : ?>
							<span class="wd-icon-name"><?php esc_html_e( 'Instagram', 'woodmart' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( 'follow' === $type && '' !== $threads_link ) : ?>
					<a rel="noopener noreferrer nofollow" href="<?php echo esc_attr( $threads_link ); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php echo esc_attr( $tooltip_class ); ?> wd-social-icon social-threads" aria-label="<?php esc_attr_e( 'Threads social link', 'woodmart' ); ?>">
						<span class="wd-icon"></span>
						<?php if ( $sticky ) : ?>
							<span class="wd-icon-name"><?php esc_html_e( 'Threads', 'woodmart' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( 'follow' === $type && '' !== $youtube_link ) : ?>
					<a rel="noopener noreferrer nofollow" href="<?php echo 'follow' === $type ? esc_attr( $youtube_link ) : '' . esc_url( $page_link ); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php echo esc_attr( $tooltip_class ); ?> wd-social-icon social-youtube" aria-label="<?php esc_attr_e( 'YouTube social link', 'woodmart' ); ?>">
						<span class="wd-icon"></span>
						<?php if ( $sticky ) : ?>
							<span class="wd-icon-name"><?php esc_html_e( 'YouTube', 'woodmart' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( ( 'share' === $type && woodmart_get_opt( 'share_pinterest' ) ) || ( 'follow' === $type && '' !== $pinterest_link ) ) : ?>
					<a rel="noopener noreferrer nofollow" href="<?php echo 'follow' === $type ? esc_attr( $pinterest_link ) : esc_url( 'https://pinterest.com/pin/create/button/?url=' . $page_link . '&media=' . $thumb_url[0] . '&description=' . rawurlencode( $page_title ) ); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php echo esc_attr( $tooltip_class ); ?> wd-social-icon social-pinterest" aria-label="<?php esc_attr_e( 'Pinterest social link', 'woodmart' ); ?>">
						<span class="wd-icon"></span>
						<?php if ( $sticky ) : ?>
							<span class="wd-icon-name"><?php esc_html_e( 'Pinterest', 'woodmart' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( 'follow' === $type && '' !== $tumblr_link ) : ?>
					<a rel="noopener noreferrer nofollow" href="<?php echo 'follow' === $type ? esc_attr( $tumblr_link ) : '' . esc_url( $page_link ); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php echo esc_attr( $tooltip_class ); ?> wd-social-icon social-tumblr" aria-label="<?php esc_attr_e( 'Tumblr social link', 'woodmart' ); ?>">
						<span class="wd-icon"></span>
						<?php if ( $sticky ) : ?>
							<span class="wd-icon-name"><?php esc_html_e( 'Tumblr', 'woodmart' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( ( 'share' === $type && woodmart_get_opt( 'share_linkedin' ) ) || ( 'follow' === $type && '' !== $linkedin_link ) ) : ?>
					<a rel="noopener noreferrer nofollow" href="<?php echo 'follow' === $type ? esc_attr( $linkedin_link ) : esc_url( 'https://www.linkedin.com/shareArticle?mini=true&url=' . $page_link ); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php echo esc_attr( $tooltip_class ); ?> wd-social-icon social-linkedin" aria-label="<?php esc_attr_e( 'Linkedin social link', 'woodmart' ); ?>">
						<span class="wd-icon"></span>
						<?php if ( $sticky ) : ?>
							<span class="wd-icon-name"><?php esc_html_e( 'linkedin', 'woodmart' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( 'follow' === $type && '' !== $vimeo_link ) : ?>
					<a rel="noopener noreferrer nofollow" href="<?php echo 'follow' === $type ? esc_attr( $vimeo_link ) : '' . esc_url( $page_link ); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php echo esc_attr( $tooltip_class ); ?> wd-social-icon social-vimeo" aria-label="<?php esc_attr_e( 'Vimeo social link', 'woodmart' ); ?>">
						<span class="wd-icon"></span>
						<?php if ( $sticky ) : ?>
							<span class="wd-icon-name"><?php esc_html_e( 'Vimeo', 'woodmart' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( 'follow' === $type && '' !== $flickr_link ) : ?>
					<a rel="noopener noreferrer nofollow" href="<?php echo 'follow' === $type ? esc_attr( $flickr_link ) : '' . esc_url( $page_link ); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php echo esc_attr( $tooltip_class ); ?> wd-social-icon social-flickr" aria-label="<?php esc_attr_e( 'Flickr social link', 'woodmart' ); ?>">
						<span class="wd-icon"></span>
						<?php if ( $sticky ) : ?>
							<span class="wd-icon-name"><?php esc_html_e( 'Flickr', 'woodmart' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( 'follow' === $type && '' !== $github_link ) : ?>
					<a rel="noopener noreferrer nofollow" href="<?php echo 'follow' === $type ? esc_attr( $github_link ) : '' . esc_url( $page_link ); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php echo esc_attr( $tooltip_class ); ?> wd-social-icon social-github" aria-label="<?php esc_attr_e( 'GitHub social link', 'woodmart' ); ?>">
						<span class="wd-icon"></span>
						<?php if ( $sticky ) : ?>
							<span class="wd-icon-name"><?php esc_html_e( 'GitHub', 'woodmart' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( 'follow' === $type && '' !== $dribbble_link ) : ?>
					<a rel="noopener noreferrer nofollow" href="<?php echo 'follow' === $type ? esc_attr( $dribbble_link ) : '' . esc_url( $page_link ); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php echo esc_attr( $tooltip_class ); ?> wd-social-icon social-dribbble" aria-label="<?php esc_attr_e( 'Dribbble social link', 'woodmart' ); ?>">
						<span class="wd-icon"></span>
						<?php if ( $sticky ) : ?>
							<span class="wd-icon-name"><?php esc_html_e( 'Dribbble', 'woodmart' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( 'follow' === $type && '' !== $behance_link ) : ?>
					<a rel="noopener noreferrer nofollow" href="<?php echo 'follow' === $type ? esc_attr( $behance_link ) : '' . esc_url( $page_link ); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php echo esc_attr( $tooltip_class ); ?> wd-social-icon social-behance" aria-label="<?php esc_attr_e( 'Behance social link', 'woodmart' ); ?>">
						<span class="wd-icon"></span>
						<?php if ( $sticky ) : ?>
							<span class="wd-icon-name"><?php esc_html_e( 'Behance', 'woodmart' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( 'follow' === $type && '' !== $soundcloud_link ) : ?>
						<a rel="noopener noreferrer nofollow" href="<?php echo 'follow' === $type ? esc_attr( $soundcloud_link ) : '' . esc_url( $page_link ); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php echo esc_attr( $tooltip_class ); ?> wd-social-icon social-soundcloud" aria-label="<?php esc_attr_e( 'Soundcloud social link', 'woodmart' ); ?>">
							<span class="wd-icon"></span>
							<?php if ( $sticky ) : ?>
								<span class="wd-icon-name"><?php esc_html_e( 'Soundcloud', 'woodmart' ); ?></span>
							<?php endif; ?>
						</a>
				<?php endif ?>

				<?php if ( 'follow' === $type && '' !== $spotify_link ) : ?>
					<a rel="noopener noreferrer nofollow" href="<?php echo 'follow' === $type ? esc_attr( $spotify_link ) : '' . esc_url( $page_link ); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php echo esc_attr( $tooltip_class ); ?> wd-social-icon social-spotify" aria-label="<?php esc_attr_e( 'Spotify social link', 'woodmart' ); ?>">
						<span class="wd-icon"></span>
						<?php if ( $sticky ) : ?>
							<span class="wd-icon-name"><?php esc_html_e( 'Spotify', 'woodmart' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( ( 'share' === $type && woodmart_get_opt( 'share_ok' ) ) || ( 'follow' === $type && '' !== $ok_link ) ) : ?>
					<a rel="noopener noreferrer nofollow" href="<?php echo 'follow' === $type ? esc_attr( $ok_link ) : esc_url( 'https://connect.ok.ru/offer?url=' . $page_link ); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php echo esc_attr( $tooltip_class ); ?> wd-social-icon social-ok" aria-label="<?php esc_attr_e( 'Odnoklassniki social link', 'woodmart' ); ?>">
						<span class="wd-icon"></span>
						<?php if ( $sticky ) : ?>
							<span class="wd-icon-name"><?php esc_html_e( 'Odnoklassniki', 'woodmart' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( ( 'share' === $type && woodmart_get_opt( 'share_whatsapp' ) ) || ( 'follow' === $type && '' !== $whatsapp_link ) ) : ?>
					<a rel="noopener noreferrer nofollow" href="<?php echo 'follow' === $type ? esc_attr( $whatsapp_link ) : esc_url( 'https://api.whatsapp.com/send?text=' . rawurlencode( $page_link ) ); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php echo esc_attr( $tooltip_class ); ?> wd-hide-md wd-social-icon social-whatsapp" aria-label="<?php esc_attr_e( 'WhatsApp social link', 'woodmart' ); ?>">
						<span class="wd-icon"></span>
						<?php if ( $sticky ) : ?>
							<span class="wd-icon-name"><?php esc_html_e( 'WhatsApp', 'woodmart' ); ?></span>
						<?php endif; ?>
					</a>

					<a rel="noopener noreferrer nofollow" href="<?php echo 'follow' === $type ? esc_attr( $whatsapp_link ) : esc_attr( 'whatsapp://send?text=' . rawurlencode( $page_link ) ); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php echo esc_attr( $tooltip_class ); ?> wd-hide-lg wd-social-icon social-whatsapp" aria-label="<?php esc_attr_e( 'WhatsApp social link', 'woodmart' ); ?>">
						<span class="wd-icon"></span>
						<?php if ( $sticky ) : ?>
							<span class="wd-icon-name"><?php esc_html_e( 'WhatsApp', 'woodmart' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( ( 'share' === $type && woodmart_get_opt( 'share_vk' ) ) || ( 'follow' === $type && '' !== $vk_link ) ) : ?>
					<a rel="noopener noreferrer nofollow" href="<?php echo 'follow' === $type ? esc_attr( $vk_link ) : esc_url( 'https://vk.com/share.php?url=' . $page_link . '&image=' . $thumb_url[0] . '&title=' . $page_title ); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php echo esc_attr( $tooltip_class ); ?> wd-social-icon social-vk" aria-label="<?php esc_attr_e( 'VK social link', 'woodmart' ); ?>">
						<span class="wd-icon"></span>
						<?php if ( $sticky ) : ?>
							<span class="wd-icon-name"><?php esc_html_e( 'VK', 'woodmart' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( 'follow' === $type && '' !== $snapchat_link ) : ?>
					<a rel="noopener noreferrer nofollow" href="<?php echo esc_attr( $snapchat_link ); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php echo esc_attr( $tooltip_class ); ?> wd-social-icon social-snapchat" aria-label="<?php esc_attr_e( 'Snapchat social link', 'woodmart' ); ?>">
						<span class="wd-icon"></span>
						<?php if ( $sticky ) : ?>
							<span class="wd-icon-name"><?php esc_html_e( 'Snapchat', 'woodmart' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( 'follow' === $type && '' !== $tiktok_link ) : ?>
					<a rel="noopener noreferrer nofollow" href="<?php echo esc_attr( $tiktok_link ); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php echo esc_attr( $tooltip_class ); ?> wd-social-icon social-tiktok" aria-label="<?php esc_attr_e( 'TikTok social link', 'woodmart' ); ?>">
						<span class="wd-icon"></span>
						<?php if ( $sticky ) : ?>
							<span class="wd-icon-name"><?php esc_html_e( 'TikTok', 'woodmart' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( 'follow' === $type && '' !== $discord_link ) : ?>
					<a rel="noopener noreferrer nofollow" href="<?php echo esc_attr( $discord_link ); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php echo esc_attr( $tooltip_class ); ?> wd-social-icon social-discord" aria-label="<?php esc_attr_e( 'Discord social link', 'woodmart' ); ?>">
						<span class="wd-icon"></span>
						<?php if ( $sticky ) : ?>
							<span class="wd-icon-name"><?php esc_html_e( 'Discord', 'woodmart' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( 'follow' === $type && '' !== $yelp_link ) : ?>
					<a rel="noopener noreferrer nofollow" href="<?php echo esc_attr( $yelp_link ); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php echo esc_attr( $tooltip_class ); ?> wd-social-icon social-yelp" aria-label="<?php esc_attr_e( 'Yelp social link', 'woodmart' ); ?>">
						<span class="wd-icon"></span>
						<?php if ( $sticky ) : ?>
							<span class="wd-icon-name"><?php esc_html_e( 'Yelp', 'woodmart' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( ( 'share' === $type && woodmart_get_opt( 'share_tg' ) ) || ( 'follow' === $type && '' !== $tg_link ) ) : ?>
					<a rel="noopener noreferrer nofollow" href="<?php echo 'follow' === $type ? esc_attr( $tg_link ) : esc_url( 'https://telegram.me/share/url?url=' . $page_link ); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php echo esc_attr( $tooltip_class ); ?> wd-social-icon social-tg" aria-label="<?php esc_attr_e( 'Telegram social link', 'woodmart' ); ?>">
						<span class="wd-icon"></span>
						<?php if ( $sticky ) : ?>
							<span class="wd-icon-name"><?php esc_html_e( 'Telegram', 'woodmart' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

				<?php if ( ( 'share' === $type && woodmart_get_opt( 'share_viber' ) ) || ( 'follow' === $type && '' !== $viber_link ) ) : ?>
					<a rel="noopener noreferrer nofollow" href="<?php echo 'follow' === $type ? esc_attr( $viber_link ) : esc_attr( 'viber://forward?text=' . rawurlencode( $page_link ) ); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php echo esc_attr( $tooltip_class ); ?> wd-social-icon social-viber" aria-label="<?php esc_attr_e( 'Viber social link', 'woodmart' ); ?>">
						<span class="wd-icon"></span>
						<?php if ( $sticky ) : ?>
							<span class="wd-icon-name"><?php esc_html_e( 'Viber', 'woodmart' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif ?>

			</div>

		<?php
		if ( ! $elementor ) {
			$output = ob_get_contents();
			ob_end_clean();

			return $output;
		}
	}
}
