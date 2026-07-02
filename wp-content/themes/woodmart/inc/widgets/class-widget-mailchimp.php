<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * This file has a class that creates a mailchimp widget.
 *
 * @package woodmart
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * Register mailchimp widget.
 */
class WOODMART_Widget_Mailchimp extends WPH_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->create_widget(
			array(
				'label'       => esc_html__( 'WOODMART Mailchimp ', 'woodmart' ),
				'description' => esc_html__( 'Newsletter subscription form', 'woodmart' ),
				'slug'        => 'wd-mailchimp-widget',
				'fields'      => array(
					array(
						'id'              => 'form_id',
						'type'            => 'dropdown',
						'callback_global' => 'woodmart_get_mailchimp_forms',
						'name'            => esc_html__( 'Select form', 'woodmart' ),
					),
				),
			)
		);
	}

	/**
	 * This is method rendering widget.
	 *
	 * @param array $args arguments for create widget.
	 * @param array $instance data for create widget preview.
	 */
	public function widget( $args, $instance ) {
		if ( ! $instance['form_id'] || ! defined( 'MC4WP_VERSION' ) || $this->is_widget_preview() ) {
			return;
		}

		echo wp_kses_post( $args['before_widget'] );

		woodmart_enqueue_inline_style( 'mc4wp' );
		?>
		<div class="wd-mc4wp-wrapper">
			<?php echo do_shortcode( '[mc4wp_form id="' . esc_attr( $instance['form_id'] ) . '"]' ); ?>
		</div>
		<?php
		echo wp_kses_post( $args['after_widget'] );
	}
}
