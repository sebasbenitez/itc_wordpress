<?php
/**
 * Slider admin module.
 *
 * @package woodmart
 */

namespace XTS\Admin\Modules\Dashboard;

use XTS\Singleton;

/**
 * Slider class.
 */
class Slider extends Singleton {
	/**
	 * Constructor.
	 */
	public function init() {
		add_action( 'woodmart_slider_term_edit_form_top', array( $this, 'add_slides_to_slider_page' ), 9 );
		add_action( 'wp_ajax_woodmart_get_slides_data', array( $this, 'get_slides_data' ) );
		add_action( 'post_edit_form_tag', array( $this, 'enqueue_script' ) );

		add_filter( 'manage_edit-woodmart_slide_columns', array( $this, 'edit_woodmart_slide_columns' ) );
		add_action( 'manage_woodmart_slide_posts_custom_column', array( $this, 'manage_woodmart_slide_columns' ), 11, 2 );

		add_filter( 'hidden_meta_boxes', array( $this, 'hide_custom_fields' ), 10, 3 );
	}

	/**
	 * Hide Custom Fields meta box by default for this post type.
	 *
	 * @param array     $hidden Hidden meta boxes.
	 * @param WP_Screen $screen Current screen.
	 * @param bool      $use_defaults Whether to use default meta boxes.
	 *
	 * @return array
	 */
	public function hide_custom_fields( $hidden, $screen, $use_defaults ) {
		if ( isset( $screen->id ) && 'woodmart_slide' === $screen->id ) {
			if ( ! is_array( $hidden ) ) {
				$hidden = array();
			}
			if ( ! in_array( 'postcustom', $hidden, true ) ) {
				$hidden[] = 'postcustom';
			}
		}

		return $hidden;
	}

	/**
	 * Enqueue script.
	 *
	 * @param object $post Post.
	 */
	public function enqueue_script( $post ) {
		if ( ! $post || 'woodmart_slide' !== $post->post_type ) {
			return;
		}

		wp_enqueue_script( 'wd-sliders-ui', WOODMART_ASSETS . '/js/sliders-ui.js', array(), WOODMART_VERSION, true );
	}

	/**
	 * Add slides to slider list.
	 */
	public function get_slides_data() {
		check_ajax_referer( 'woodmart-get-slides-nonce', 'security' );

		$output     = array();
		$taxonomies = get_terms(
			array(
				'taxonomy'   => 'woodmart_slider',
				'hide_empty' => false,
			)
		);

		if ( ! $taxonomies ) {
			wp_send_json_error();
		}

		foreach ( $taxonomies as $taxonomy ) {
			$slider_id = $taxonomy->term_id;

			$output[ $slider_id ]['slider_edit_link'] = get_edit_term_link( $slider_id, 'woodmart_slider' );
			$output[ $slider_id ]['slider_edit_text'] = esc_html__( 'Slider settings', 'woodmart' );
		}

		if ( empty( $_GET['slider_id'] ) ) {
			wp_send_json_success( $output );
		}

		$args = array(
			'posts_per_page' => -1,
			'post_type'      => 'woodmart_slide',
			'tax_query'      => array( // phpcs:ignore  WordPress.DB.SlowDBQuery
				'relation' => 'OR',
			),
		);

		$slider_ids = $_GET['slider_id']; //phpcs:ignore

		foreach ( $slider_ids as $id ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'woodmart_slider',
				'field'    => 'term_id',
				'terms'    => (int) $id,
			);
		}

		$slides = new \WP_Query( $args );

		if ( $slides->posts ) {
			foreach ( $slides->posts as $slide ) {
				$slide_image           = woodmart_get_post_meta_value( $slide->ID, 'image' );
				$bg_image_desktop      = has_post_thumbnail( $slide->ID ) ? wp_get_attachment_url( get_post_thumbnail_id( $slide->ID ) ) : '';
				$meta_bg_image_desktop = woodmart_get_post_meta_value( $slide->ID, 'bg_image_desktop' );

				if ( is_array( $meta_bg_image_desktop ) ) {
					$meta_bg_image_desktop = $meta_bg_image_desktop['url'];
				}

				if ( $meta_bg_image_desktop ) {
					$bg_image_desktop = $meta_bg_image_desktop;
				}

				if ( ! empty( $slide_image['url'] ) ) {
					$bg_image_desktop = $slide_image['url'];
				}

				$slider_term = wp_get_post_terms( $slide->ID, 'woodmart_slider' );

				if ( ! $slider_term ) {
					continue;
				}

				foreach ( $slider_term as $term ) {
					$slider_id = $term->term_id;

					$output[ $slider_id ]['slides'][ $slide->ID ] = array(
						'id'       => $slide->ID,
						'title'    => $slide->post_title,
						'link'     => get_edit_post_link( $slide->ID, 'url' ),
						'img_url'  => $bg_image_desktop,
						'bg_color' => woodmart_get_post_meta_value( $slide->ID, 'bg_color' ),
					);
				}
			}
		}

		wp_send_json_success( $output );
	}

	/**
	 * Add slides list to slider.
	 *
	 * @param object $tag Term object.
	 */
	public function add_slides_to_slider_page( $tag ) {
		$args = array(
			'posts_per_page' => -1,
			'post_type'      => 'woodmart_slide',
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'tax_query'      => array( // phpcs:ignore
				array(
					'taxonomy' => 'woodmart_slider',
					'field'    => 'id',
					'terms'    => $tag->term_id,
				),
			),
		);

		$slides = new \WP_Query( $args );

		?>
		<div class="xts-edit-slider-slides-wrap">
			<div class="xts-edit-slider-slides">
				<div class="xts-wp-add-heading">
					<h1 class="wp-heading-inline">
						<?php esc_html_e( 'Slides', 'woodmart' ); ?>
					</h1>

					<a class="page-title-action" href="<?php echo esc_url( admin_url( 'post-new.php?post_type=woodmart_slide&slider_id=' . $tag->term_id ) ); ?>">
						<?php esc_html_e( 'Add new', 'woodmart' ); ?>
					</a>
				</div>

				<?php if ( $slides->posts ) : ?>
					<div class="xts-wp-table">
						<div class="xts-wp-row xts-wp-row-heading">
							<div class="xts-wp-table-img"></div>
							<div class="xts-wp-table-title"><?php esc_html_e( 'Title', 'woodmart' ); ?></div>
							<div class="xts-wp-table-date"><?php esc_html_e( 'Date', 'woodmart' ); ?></div>
						</div>
						<?php foreach ( $slides->posts as $slide ) : ?>
							<?php
							$slide_image           = woodmart_get_post_meta_value( $slide->ID, 'image' );
							$bg_image_desktop      = has_post_thumbnail( $slide->ID ) ? wp_get_attachment_url( get_post_thumbnail_id( $slide->ID ) ) : '';
							$meta_bg_image_desktop = woodmart_get_post_meta_value( $slide->ID, 'bg_image_desktop' );
							$bg_slide_color        = woodmart_get_post_meta_value( $slide->ID, 'bg_color' );

							if ( is_array( $meta_bg_image_desktop ) ) {
								$meta_bg_image_desktop = $meta_bg_image_desktop['url'];
							}

							if ( $meta_bg_image_desktop ) {
								$bg_image_desktop = $meta_bg_image_desktop;
							}

							$duplicate_url = wp_nonce_url(
								add_query_arg(
									array(
										'action' => 'woodmart_duplicate_post_as_draft',
										'post'   => $slide->ID,
									),
									'admin.php'
								),
								'woodmart_duplicate_post_as_draft',
								'duplicate_nonce'
							);

							?>
							<div class="xts-wp-row">
								<div class="xts-wp-table-img">
									<?php if ( ! empty( $slide_image['url'] ) ) : ?>
										<img src="<?php echo esc_url( $slide_image['url'] ); ?>" alt="slide image">
									<?php elseif ( $bg_image_desktop ) : ?>
										<img src="<?php echo esc_url( $bg_image_desktop ); ?>" alt="slide image">
									<?php elseif ( $bg_slide_color ) : ?>
										<div class="xts-slider-bg-color" style="background-color: <?php echo esc_attr( $bg_slide_color ); ?>"></div>
									<?php endif; ?>
								</div>

								<div class="xts-wp-table-title">
									<a href="<?php echo esc_url( get_edit_post_link( $slide->ID, 'url' ) ); ?>">
										<?php echo esc_html( $slide->post_title ); ?>
									</a>
									<div class="xts-actions">
										<a href="<?php echo esc_url( get_edit_post_link( $slide->ID, 'url' ) ); ?>">
											<?php esc_html_e( 'Edit', 'woodmart' ); ?>
										</a>

										<a class="xts-bin" href="<?php echo esc_url( get_delete_post_link( $slide->ID ) ); ?>">
											<?php esc_html_e( 'Trash', 'woodmart' ); ?>
										</a>

										<a href="<?php echo esc_url( get_preview_post_link( $slide->ID ) ); ?>">
											<?php esc_html_e( 'View', 'woodmart' ); ?>
										</a>

										<a href="<?php echo esc_url( $duplicate_url ); ?>">
											<?php esc_html_e( 'Duplicate', 'woodmart' ); ?>
										</a>
									</div>
								</div>

								<div class="xts-wp-table-date">
									<span><?php esc_html_e( 'Published', 'woodmart' ); ?></span>
									<br>
									<span>
										<?php echo esc_html( $slide->post_modified ); ?>
									</span>
								</div>
							</div>
						<?php endforeach; ?>
						<div class="xts-wp-row xts-wp-row-heading">
							<div class="xts-wp-table-img"></div>
							<div class="xts-wp-table-title"><?php esc_html_e( 'Title', 'woodmart' ); ?></div>
							<div class="xts-wp-table-date"><?php esc_html_e( 'Date', 'woodmart' ); ?></div>
						</div>
					</div>
				<?php else : ?>
					<div class="xts-notice xts-info">
						<?php esc_html_e( 'There are no slides yet.', 'woodmart' ); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Edit the columns for the woodmart_slide post type.
	 *
	 * @param array $columns The existing columns.
	 *
	 * @return array The modified columns.
	 */
	public function edit_woodmart_slide_columns( $columns ) {
		$columns = array(
			'cb'           => '<input type="checkbox" />',
			'thumb'        => '',
			'title'        => esc_html__( 'Title', 'woodmart' ),
			'slide-slider' => esc_html__( 'Slider', 'woodmart' ),
			'date'         => esc_html__( 'Date', 'woodmart' ),
		);

		return $columns;
	}

	/**
	 * Edit slide columns.
	 *
	 * @param string $column Column.
	 * @param int    $post_id Post ID.
	 * @return void
	 */
	public function manage_woodmart_slide_columns( $column, $post_id ) {
		switch ( $column ) {
			case 'thumb':
				$slide_image           = woodmart_get_post_meta_value( $post_id, 'image' );
				$meta_bg_image_desktop = woodmart_get_post_meta_value( $post_id, 'bg_image_desktop' );
				$meta_bg_slide_color   = woodmart_get_post_meta_value( $post_id, 'bg_color' );

				if ( ! empty( $slide_image['url'] ) ) {
					?>
					<img src="<?php echo esc_url( $slide_image['url'] ); ?>" alt="<?php echo esc_attr__( 'Slide thumbnail', 'woodmart' ); ?>">
					<?php
				} elseif ( ( $meta_bg_image_desktop && ! is_array( $meta_bg_image_desktop ) ) || ! empty( $meta_bg_image_desktop['url'] ) ) {
					if ( is_array( $meta_bg_image_desktop ) && isset( $meta_bg_image_desktop['url'] ) ) {
						$meta_bg_image_desktop = $meta_bg_image_desktop['url'];
					}
					?>
					<img src="<?php echo esc_url( $meta_bg_image_desktop ); ?>" alt="<?php echo esc_attr__( 'Slide thumbnail', 'woodmart' ); ?>">
					<?php
				} elseif ( has_post_thumbnail( $post_id ) ) {
					the_post_thumbnail( array( 60, 60 ) );
				} elseif ( $meta_bg_slide_color ) {
					?>
					<div class="xts-slider-bg-color" style="background-color: <?php echo esc_attr( $meta_bg_slide_color ); ?>"></div>
					<?php
				}

				break;
			case 'slide-slider':
				$terms    = wp_get_post_terms( $post_id, 'woodmart_slider' );
				$keys     = array_keys( $terms );
				$last_key = end( $keys );

				if ( ! $terms ) {
					echo '—';

					return;
				}

				foreach ( $terms as $key => $term ) {
					$name = $term->name;

					if ( $key !== $last_key ) {
						$name .= ',';
					}
					?>
					<a href="<?php echo esc_url( get_edit_term_link( $term->term_id, 'woodmart_slider' ) ); ?>">
						<?php echo esc_html( $name ); ?>
					</a>
					<?php
				}

				break;
		}
	}
}

Slider::get_instance();
