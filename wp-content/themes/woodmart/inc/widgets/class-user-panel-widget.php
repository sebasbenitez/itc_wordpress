<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * User panel widget.
 *
 * @package woodmart
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * Register widget based on VC_MAP parameters that displays user panel
 */
class WOODMART_User_Panel_Widget extends WPH_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$args = array(
			'label'       => esc_html__( 'WOODMART User Panel', 'woodmart' ),
			'description' => esc_html__( 'User panel to use in My Account area', 'woodmart' ),
			'slug'        => 'woodmart-user-panel',
		);

		$args['fields'] = apply_filters(
			'woodmart_get_user_panel_params',
			array(
				array(
					'type'       => 'textfield',
					'heading'    => esc_html__( 'Title', 'woodmart' ),
					'param_name' => 'title',
				),
			)
		);

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

		$instance['title'] = '';

		echo woodmart_shortcode_user_panel( $instance ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		do_action( 'wpiw_after_widget', $instance );

		echo wp_kses_post( $after_widget );
	}

	/**
	 * Render form.
	 *
	 * @param array $instance Widget instance.
	 */
	public function form( $instance ) { // phpcs:ignore Generic.CodeAnalysis.UselessOverridingMethod
		parent::form( $instance );
	}
}
