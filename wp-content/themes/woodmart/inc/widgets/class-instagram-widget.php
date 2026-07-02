<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Instagram widget.
 *
 * @package woodmart
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * Register widget based on VC_MAP parameters that display isntagram widget.
 */
class WOODMART_Instagram_Widget extends WPH_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( ! function_exists( 'woodmart_get_instagram_params' ) ) {
			return;
		}

		$args = array(
			'label'       => esc_html__( 'WOODMART Instagram', 'woodmart' ),
			'description' => esc_html__( 'Instagram photos', 'woodmart' ),
			'slug'        => 'woodmart-instagram',
		);

		$args['fields'] = woodmart_get_instagram_params();

		$this->create_widget( $args );
	}

	/**
	 * Render widget.
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Widget instance.
	 */
	public function widget( $args, $instance ) {
		extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract

		echo wp_kses_post( $before_widget );

		if ( ! empty( $instance['title'] ) ) {
			echo wp_kses_post( $before_title ) . $instance['title'] . wp_kses_post( $after_title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		do_action( 'wpiw_before_widget', $instance );

		$instance['title']          = '';
		$instance['spacing']        = 1;
		$instance['spacing_custom'] = 6;
		$instance['per_row']        = 3;
		$instance['per_row_tablet'] = 3;
		$instance['per_row_mobile'] = 3;
		$instance['username']       = $instance['username'] ? $instance['username'] : 'flickr';

		echo woodmart_shortcode_instagram( $instance ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		do_action( 'wpiw_after_widget', $instance );

		echo wp_kses_post( $after_widget );
	}
}
