<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Author area widget.
 *
 * @package woodmart
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * Register widget based on VC_MAP parameters that display author area shortcode.
 */
class WOODMART_Author_Area_Widget extends WPH_Widget {
	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( ! function_exists( 'woodmart_get_author_area_params' ) ) {
			return;
		}

		$args = array(
			'label'       => esc_html__( 'WOODMART Author Information', 'woodmart' ),
			'description' => esc_html__( 'Small information block about blog author', 'woodmart' ),
			'slug'        => 'woodmart-author-information',
		);

		$args['fields'] = woodmart_get_author_area_params();
		$this->create_widget( $args );
	}

	/**
	 * Widget output.
	 *
	 * @param array $args The widget arguments.
	 * @param array $instance The widget instance.
	 * @return void
	 */
	public function widget( $args, $instance ) {
		extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract

		woodmart_enqueue_inline_style( 'el-author-area' );

		echo wp_kses_post( $before_widget );

		if ( ! empty( $instance['title'] ) ) {
			echo wp_kses_post( $before_title ) . $instance['title'] . wp_kses_post( $after_title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		do_action( 'wpiw_before_widget', $instance );

		$instance['title'] = '';

		echo woodmart_shortcode_author_area( $instance, $instance['content'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		do_action( 'wpiw_after_widget', $instance );

		echo wp_kses_post( $after_widget );
	}
}
