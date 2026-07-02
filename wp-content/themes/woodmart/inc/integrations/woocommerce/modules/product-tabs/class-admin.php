<?php
/**
 * Custom product tabs class.
 *
 * @package woodmart
 */

namespace XTS\Modules\Custom_Product_Tabs;

use Elementor\Plugin;
use XTS\Singleton;
use XTS\Admin\Modules\Options\Metaboxes;
use XTS\Admin\Modules\Dashboard\Status_Button;
use WP_Post;

/**
 * Custom product tabs class.
 */
class Admin extends Singleton {
	/**
	 * Manager instance.
	 *
	 * @var Manager instanse.
	 */
	public $manager;

	/**
	 * Init.
	 */
	public function init() {
		if ( ! woodmart_get_opt( 'custom_product_tabs_enabled' ) || ! woodmart_woocommerce_installed() ) {
			return;
		}

		$this->manager = Manager::get_instance();

		add_action( 'new_to_publish', array( $this, 'clear_transients_on_publish' ) );
		add_action( 'save_post', array( $this, 'clear_transients' ), 10, 2 );
		add_action( 'edit_post', array( $this, 'clear_transients' ), 10, 2 );
		add_action( 'deleted_post', array( $this, 'clear_transients' ), 10, 2 );
		add_action( 'woodmart_change_post_status', array( $this, 'clear_transients_on_ajax' ) );

		add_action( 'init', array( $this, 'add_metaboxes' ) );

		new Status_Button( 'wd_product_tabs', 2 );
	}

	/**
	 * Clear transients on create post.
	 *
	 * @param WP_Post $post Post object.
	 *
	 * @return void
	 */
	public function clear_transients_on_publish( $post ) {
		$this->clear_transients( 0, $post );
	}

	/**
	 * Clear transients.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post Post object.
	 *
	 * @return void
	 */
	public function clear_transients( $post_id, $post ) {
		if ( ! $post || 'wd_product_tabs' !== $post->post_type ) {
			return;
		}

		delete_transient( $this->manager->transient_product_tabs_ids );
	}

	/**
	 * Clear transients on ajax action.
	 *
	 * @return void
	 */
	public function clear_transients_on_ajax() {
		if ( ! wp_doing_ajax() || empty( $_POST['action'] ) || empty( $_POST['id'] ) || 'wd_change_post_status' !== $_POST['action'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			return;
		}

		$post = get_post( $_POST['id'] ); // phpcs:ignore WordPress.Security

		if ( ! $post || 'wd_product_tabs' !== $post->post_type ) {
			return;
		}

		delete_transient( $this->manager->transient_product_tabs_ids );
	}

	/**
	 * Add metaboxes for custom product tabs.
	 *
	 * @return void
	 */
	public function add_metaboxes() {
		$metabox = Metaboxes::add_metabox(
			array(
				'id'         => 'wd_product_tabs_metaboxes',
				'title'      => esc_html__( 'Settings', 'woodmart' ),
				'post_types' => array( 'wd_product_tabs' ),
			)
		);

		if ( woodmart_is_elementor_installed() && is_admin() && ! empty( $_GET['post'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$doc = Plugin::$instance->documents->get( absint( $_GET['post'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			if ( $doc && $doc->is_built_with_elementor() ) {
				$metabox->add_section(
					array(
						'id'       => 'warning',
						'name'     => '',
						'priority' => 10,
					)
				);

				$metabox->add_field(
					array(
						'id'       => 'elementor_warning',
						'section'  => 'warning',
						'type'     => 'notice',
						'style'    => 'info',
						'name'     => '',
						'content'  => esc_html__( 'Custom tabs metaboxes moved to Elementor Post Settings', 'woodmart' ) . woodmart_get_admin_tooltip( 'elementor-custom-tabs-settings.jpg' ),
						'priority' => 10,
					)
				);
				return;
			}
		}

		$metabox->add_section(
			array(
				'id'       => 'general',
				'name'     => esc_html__( 'General', 'woodmart' ),
				'icon'     => 'xts-i-footer',
				'priority' => 10,
			)
		);

		$metabox->add_field(
			array(
				'id'          => 'product_tab_title',
				'name'        => esc_html__( 'Tab title', 'woodmart' ),
				'description' => esc_html__( 'Leave empty to display default post title.', 'woodmart' ),
				'type'        => 'text_input',
				'default'     => '',
				'section'     => 'general',
				'priority'    => 10,
				'class'       => 'xts-col-6',
			)
		);

		$metabox->add_field(
			array(
				'id'          => 'product_tab_priority',
				'name'        => esc_html__( 'Priority', 'woodmart' ),
				'description' => esc_html__( 'Sets this tab\'s position among product tabs. Lower values mean higher priority. By default, 130 places it after standard WooCommerce tabs; values below 10 show it before them.', 'woodmart' ),
				'type'        => 'text_input',
				'attributes'  => array(
					'type'        => 'number',
					'min'         => '1',
					'placeholder' => '130',
				),
				'section'     => 'general',
				'priority'    => 20,
				'class'       => 'xts-col-6',
			)
		);

		$metabox->add_field(
			array(
				'id'           => 'product_tab_condition',
				'group'        => esc_html__( 'Conditions', 'woodmart' ),
				'type'         => 'conditions',
				'section'      => 'general',
				'inner_fields' => array(
					'product-type-query' => array(
						'name'     => esc_html__( 'Condition query', 'woodmart' ),
						'options'  => array(
							'simple'   => esc_html__( 'Simple product', 'woodmart' ),
							'grouped'  => esc_html__( 'Grouped product', 'woodmart' ),
							'external' => esc_html__( 'External/Affiliate product', 'woodmart' ),
							'variable' => esc_html__( 'Variable product', 'woodmart' ),
						),
						'requires' => array(
							array(
								'key'     => 'type',
								'compare' => 'equals',
								'value'   => 'product_type',
							),
						),
					),
				),
				'priority'     => 30,
			)
		);
	}
}

Admin::get_instance();
