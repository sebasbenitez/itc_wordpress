<?php
/**
 * Shortcode for Twitter element.
 *
 * @package woodmart
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

if ( ! function_exists( 'woodmart_twitter' ) ) {
	/**
	 * Twitter shortcode
	 *
	 * @param array $atts Shortcode attributes.
	 *
	 * @return false|string
	 */
	function woodmart_twitter( $atts ) {
		extract( // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
			shortcode_atts(
				array(
					'name'               => 'Twitter',
					'num_tweets'         => 5,
					'cache_time'         => 5,
					'consumer_key'       => '',
					'consumer_secret'    => '',
					'access_token'       => '',
					'accesstoken_secret' => '',
					'show_avatar'        => 0,
					'avatar_size'        => '',
					'exclude_replies'    => false,
					'el_class'           => '',
					'woodmart_css_id'    => '',
					'css'                => '',
				),
				$atts
			)
		);

		$class = '';

		if ( ! empty( $woodmart_css_id ) ) {
			$class = ' wd-rs-' . $woodmart_css_id;
		}

		if ( function_exists( 'vc_shortcode_custom_css_class' ) ) {
			$class .= ' ' . vc_shortcode_custom_css_class( $css );
		}

		if ( $el_class ) {
			$class .= ' ' . $el_class;
		}

		ob_start();

		if ( empty( $name ) || empty( $consumer_key ) || empty( $consumer_secret ) || empty( $access_token ) || empty( $accesstoken_secret ) ) {
			echo '<div class="wd-notice wd-info">' . esc_html__( 'You need to enter your Consumer key and secret to display your recent X (Twitter) feed.', 'woodmart' ) . '</div>';
			return ob_get_clean();
		}

		woodmart_enqueue_inline_style( 'widget-general' );
		woodmart_enqueue_inline_style( 'twitter' );
		?>
		<div class="wd-twitter-element wd-twitter-vc-element<?php echo esc_attr( $class ); ?>">
			<?php woodmart_get_twitts( $atts ); ?>
		</div>
		<?php

		return ob_get_clean();
	}
}
