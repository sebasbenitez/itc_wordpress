<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Twitter widget.
 *
 * @package woodmart
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * Register twitter widget
 */
class WOODMART_Twitter extends WPH_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$args = array(
			'label'       => esc_html__( 'WOODMART X (Twitter)', 'woodmart' ),
			'description' => esc_html__( 'Displays the most recent posts from your X (Twitter) Stream.', 'woodmart' ),
			'slug'        => 'woodmart-twitter',
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

		extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract

		echo wp_kses_post( $before_widget );

		if ( ! empty( $instance['title'] ) ) {
			echo wp_kses_post( $before_title ) . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . wp_kses_post( $after_title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		$widget_args = array(
			'name'               => $instance['name'],
			'num_tweets'         => $instance['numTweets'],
			'consumer_key'       => trim( $instance['consumerKey'] ),
			'consumer_secret'    => trim( $instance['consumerSecret'] ),
			'access_token'       => trim( $instance['accessToken'] ),
			'accesstoken_secret' => trim( $instance['accessTokenSecret'] ),
			'show_avatar'        => $instance['showAvatar'],
			'avatar_size'        => wp_strip_all_tags( $instance['avatarSize'] ),
			'exclude_replies'    => $instance['exclude_replies'],
		);
		woodmart_enqueue_inline_style( 'twitter' );
		?>
		<div class="wd-twitter-element wd-twitter-widget">
			<?php woodmart_get_twitts( $widget_args ); ?>
		</div>
		<?php

		echo wp_kses_post( $after_widget );
	}

	/**
	 * Update widget.
	 *
	 * @param array $new_instance New instance.
	 * @param array $old_instance Old instance.
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                      = $old_instance;
		$instance['title']             = wp_strip_all_tags( $new_instance['title'] );
		$instance['name']              = wp_strip_all_tags( $new_instance['name'] );
		$instance['numTweets']         = $new_instance['numTweets'];
		$instance['consumerKey']       = trim( $new_instance['consumerKey'] );
		$instance['consumerSecret']    = trim( $new_instance['consumerSecret'] );
		$instance['accessToken']       = trim( $new_instance['accessToken'] );
		$instance['accessTokenSecret'] = trim( $new_instance['accessTokenSecret'] );
		$instance['showAvatar']        = $new_instance['showAvatar'];
		$instance['avatarSize']        = wp_strip_all_tags( $new_instance['avatarSize'] );
		$instance['exclude_replies']   = $new_instance['exclude_replies'];

		return $instance;
	}

	/**
	 * Form.
	 *
	 * @param array $instance Instance.
	 */
	public function form( $instance ) {
		$defaults = array(
			'title'             => esc_html__( 'Recent posts', 'woodmart' ),
			'name'              => 'x',
			'numTweets'         => 4,
			'consumerKey'       => esc_html__( 'xxxxxxxxxxxx', 'woodmart' ),
			'consumerSecret'    => esc_html__( 'xxxxxxxxxxxx', 'woodmart' ),
			'accessToken'       => esc_html__( 'xxxxxxxxxxxx', 'woodmart' ),
			'accessTokenSecret' => esc_html__( 'xxxxxxxxxxxx', 'woodmart' ),
			'showAvatar'        => false,
			'roundCorners'      => false,
			'avatarSize'        => '', // what size should it be - defaults to 48px.
			'exclude_replies'   => false,
		);
		$instance = wp_parse_args( (array) $instance, $defaults );

		?>
		<p class="wd-widget-field">
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'woodmart' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p class="wd-widget-field">
			<label for="<?php echo esc_attr( $this->get_field_id( 'name' ) ); ?>"><?php esc_html_e( 'X Name (without @ symbol):', 'woodmart' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'name' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'name' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['name'] ); ?>" />
		</p>
		<p class="wd-widget-field">
			<label for="<?php echo esc_attr( $this->get_field_id( 'numTweets' ) ); ?>"><?php esc_html_e( 'Number of posts:', 'woodmart' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'numTweets' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'numTweets' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['numTweets'] ); ?>" />
		</p>
		<p class="wd-widget-field">
			<label for="<?php echo esc_attr( $this->get_field_id( 'consumerKey' ) ); ?>"><?php esc_html_e( 'Consumer Key:', 'woodmart' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'consumerKey' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'consumerKey' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['consumerKey'] ); ?>" />
		</p>
		<p class="wd-widget-field">
			<label for="<?php echo esc_attr( $this->get_field_id( 'consumerSecret' ) ); ?>"><?php esc_html_e( 'Consumer Secret:', 'woodmart' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'consumerSecret' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'consumerSecret' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['consumerSecret'] ); ?>" />
		</p>
		<p class="wd-widget-field">
			<label for="<?php echo esc_attr( $this->get_field_id( 'accessToken' ) ); ?>"><?php esc_html_e( 'Access Token:', 'woodmart' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'accessToken' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'accessToken' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['accessToken'] ); ?>" />
		</p>
		<p class="wd-widget-field">
			<label for="<?php echo esc_attr( $this->get_field_id( 'accessTokenSecret' ) ); ?>"><?php esc_html_e( 'Access Token Secret:', 'woodmart' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'accessTokenSecret' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'accessTokenSecret' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['accessTokenSecret'] ); ?>" />
		</p>
		<p class="wd-widget-field wd-type-checkbox">
			<label for="<?php echo esc_attr( $this->get_field_id( 'showAvatar' ) ); ?>"><?php esc_html_e( 'Show your avatar image', 'woodmart' ); ?></label>
			<input class="checkbox" type="checkbox" value="true" <?php checked( ( isset( $instance['showAvatar'] ) && ( 'true' == $instance['showAvatar'] ) ), true ); ?> id="<?php echo esc_attr( $this->get_field_id( 'showAvatar' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'showAvatar' ) ); // phpcs:ignore. ?>" />
		</p>
		<p class="wd-widget-field wd-type-checkbox">
			<label for="<?php echo esc_attr( $this->get_field_id( 'exclude_replies' ) ); ?>"><?php esc_html_e( 'Exclude Replies', 'woodmart' ); ?></label>
			<input class="checkbox" type="checkbox" value="true" <?php checked( ( isset( $instance['exclude_replies'] ) && ( 'true' == $instance['exclude_replies'] ) ), true ); ?> id="<?php echo esc_attr( $this->get_field_id( 'exclude_replies' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'exclude_replies' ) ); // phpcs:ignore. ?>" />
		</p>
		<p class="wd-widget-field">
			<label for="<?php echo esc_attr( $this->get_field_id( 'avatarSize' ) ); ?>"><?php esc_html_e( 'Size of Avatar (default: 48):', 'woodmart' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'avatarSize' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'avatarSize' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['avatarSize'] ); ?>" />
			<small class="description"><?php esc_html_e( 'Input number only', 'woodmart' ); ?></small>
		</p>
		<?php
	}
}
