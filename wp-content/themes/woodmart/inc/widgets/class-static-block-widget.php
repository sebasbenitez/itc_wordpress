<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Static block widget.
 *
 * @package woodmart
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * Register widget that displays selected HTML block.
 */
class WOODMART_Static_Block_Widget extends WPH_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$args = array(
			'label'       => esc_html__( 'WOODMART HTML Block', 'woodmart' ),
			'description' => esc_html__( 'Display HTML block', 'woodmart' ),
			'slug'        => 'woodmart-html-block',
		);

		$args['fields'] = array(
			array(
				'id'              => 'id',
				'type'            => 'dropdown',
				'heading'         => esc_html__( 'Select block', 'woodmart' ),
				'callback_global' => 'woodmart_get_static_blocks_array',
				'description'     => function_exists( 'woodmart_get_html_block_links' ) ? woodmart_get_html_block_links() : '',
			),
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
		if ( $this->is_widget_preview() ) {
			return;
		}

		echo wp_kses_post( $args['before_widget'] );

		echo woodmart_get_html_block( $instance['id'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		echo wp_kses_post( $args['after_widget'] );
	}
}
