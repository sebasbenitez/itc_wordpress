<?php
/**
 * Shortcode for Breadcrumbs element.
 *
 * @package woodmart
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

use XTS\Modules\Layouts\Main;

if ( ! function_exists( 'woodmart_shortcode_el_breadcrumbs' ) ) {
	/**
	 * Breadcrumbs shortcode.
	 *
	 * @param array $settings Shortcode attributes.
	 *
	 * @return string
	 */
	function woodmart_shortcode_el_breadcrumbs( $settings ) {
		$default_settings = array(
			'alignment'       => 'left',
			'nowrap_md'       => 'no',
			'el_id'           => '',
			'wrapper_classes' => '',
			'css'             => '',
			'is_wpb'          => true,
		);

		$settings        = wp_parse_args( $settings, $default_settings );
		$wrapper_classes = $settings['wrapper_classes'];

		if ( $settings['is_wpb'] && 'wpb' === woodmart_get_current_page_builder() ) {
			$wrapper_classes .= ' wd-wpb';
			$wrapper_classes .= apply_filters( 'vc_shortcodes_css_class', '', '', $settings );
			$wrapper_classes .= ' text-' . woodmart_vc_get_control_data( $settings['alignment'], 'desktop' );

			if ( $settings['css'] ) {
				$wrapper_classes .= ' ' . vc_shortcode_custom_css_class( $settings['css'] );
			}
		}

		if ( 'yes' === $settings['nowrap_md'] ) {
			$wrapper_classes .= ' wd-nowrap-md';
			woodmart_enqueue_inline_style( 'mod-breadcrumbs-no-wrap' );
		}

		ob_start();
		?>
			<div <?php echo $settings['el_id'] ? 'id="' . esc_attr( $settings['el_id'] ) . '" ' : ''; ?>class="wd-el-breadcrumbs<?php echo esc_attr( $wrapper_classes ); ?>">
				<?php
				Main::setup_preview();
				woodmart_current_breadcrumbs( 'pages' );
				Main::restore_preview();
				?>
			</div>   
		<?php

		return ob_get_clean();
	}
}
