<?php
/**
 * Loop Item layout type.
 *
 * @package woodmart
 */

namespace XTS\Modules\Layouts;

use WP_Query;
use XTS\Gutenberg\Block_Attributes;
use XTS\Gutenberg\Block_CSS;

/**
 * Loop Item layout type class.
 */
class Loop_Item extends Layout_Type {

	/**
	 * Meta from REST API.
	 *
	 * @var array
	 */
	private $rest_meta_data = array();

	/**
	 * Is setup preview.
	 *
	 * @var bool
	 */
	private static $is_setup_preview = false;

	/**
	 * Is setup preview.
	 *
	 * @var int
	 */
	private static $preview_product_id;

	/**
	 * Constructor.
	 */
	public function init() {
		parent::init();

		add_action( 'init', array( $this, 'register_meta_fields' ), 100 );
		add_action( 'rest_pre_insert_woodmart_layout', array( $this, 'handle_rest_post_save' ), 10, 3 );
		add_filter( 'woodmart_post_blocks_css', array( $this, 'render_css' ), 10, 3 );

		add_filter( 'admin_body_class', array( $this, 'admin_body_classes' ) );

		add_action( 'woodmart_loop_item_content', array( $this, 'output_display_template' ) );
	}

	/**
	 * Display custom template on the single page.
	 *
	 * @param int $post_id Post ID.
	 */
	public function output_display_template( $post_id = null ) {
		if ( ! $post_id ) {
			return;
		}

		$content = get_the_content( null, false, $post_id );

		echo wp_filter_content_tags( do_shortcode( shortcode_unautop( do_blocks( $content ) ) ) ); // phpcs:ignore
	}

	/**
	 * Get preview post id.
	 *
	 * @return int
	 */
	public static function get_preview_post_id() {
		if ( self::$preview_product_id ) {
			$post_id = self::$preview_product_id;
		} else {
			$post_id = get_post_meta( get_the_ID(), 'wd_preview_post', true );
		}

		if ( defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_REQUEST['wd_preview_post'] ) ) { // phpcs:ignore
			$post_id = intval( $_REQUEST['wd_preview_post'] ); // phpcs:ignore
		}

		if ( ! $post_id ) {
			$random_post = new WP_Query(
				array(
					'posts_per_page' => '1',
					'post_type'      => 'product',
				)
			);

			while ( $random_post->have_posts() ) {
				$random_post->the_post();
				$post_id = get_the_ID();
			}

			wp_reset_postdata();
		}

		return $post_id;
	}

	/**
	 * Setup post data.
	 */
	public static function setup_postdata() {
		global $post, $product;

		if (
			(
				! $product
				&& ! woodmart_loop_prop( 'woocommerce_loop' )
				&& (
					( $post && 'woodmart_layout' === $post->post_type )
					|| is_singular( 'woodmart_layout' )
					|| wp_doing_ajax()
					|| ( isset( $_POST['action'] ) && 'editpost' === $_POST['action'] ) // phpcs:ignore
					|| ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
				)
			)
			|| wp_is_post_revision( $post )
		) {
			$post_id = self::get_preview_post_id();
			$post = get_post( $post_id ); // phpcs:ignore

			self::$is_setup_preview = true;
			setup_postdata( $post );
		}
	}

	/**
	 * Reset post data.
	 */
	public static function reset_postdata() {
		if ( self::$is_setup_preview ) {
			wp_reset_postdata();
		}
	}

	/**
	 * Set preview product ID.
	 *
	 * @param int $product_id Product ID.
	 * @return void
	 */
	public static function set_preview_product( $product_id ) {
		self::$preview_product_id = $product_id;
	}

	/**
	 * Add admin body classes.
	 *
	 * @param string $classes Existing classes.
	 * @return string
	 */
	public function admin_body_classes( $classes ) {
		if ( get_post_type() === 'woodmart_layout' && 'product_loop_item' === get_post_meta( get_the_ID(), 'wd_layout_type', true ) ) {
			$classes .= ' xts-product-loop-item';
		}

		return $classes;
	}

	/**
	 * Register post meta fields for Gutenberg editor.
	 *
	 * @return void
	 */
	public function register_meta_fields() {
		$attributes = $this->get_attributes();

		if ( ! $attributes ) {
			return;
		}

		foreach ( $attributes as $key => $value ) {
			$settings = array(
				'single'        => true,
				'type'          => $value['type'],
				'auth_callback' => function ( $allowed, $meta_key, $post_id ) {
					return 'product_loop_item' === get_post_meta( $post_id, 'wd_layout_type', true );
				},
				'show_in_rest'  => in_array( $value['type'], array( 'array', 'object' ), true ) ? array(
					'schema' => array(
						'type'                 => $value['type'],
						'additionalProperties' => true,
					),
				) : true,
			);

			if ( isset( $value['default'] ) ) {
				$settings['default'] = $value['default'];
			}

			register_post_meta(
				'woodmart_layout',
				$key,
				$settings
			);
		}
	}

	/**
	 * Define loop items attributes configuration.
	 *
	 * @return array
	 */
	protected function get_attributes() {
		if ( ! class_exists( 'XTS\Gutenberg\Block_Attributes' ) ) {
			return array();
		}

		$attr = new Block_Attributes();

		$attr->add_attr(
			array(
				'wd_preview_post'         => array(
					'type'    => 'string',
					'default' => '',
				),
				'wd_force_block_id'       => array(
					'type' => 'boolean',
				),
				'wd_preview_width'        => array(
					'type'       => 'string',
					'default'    => '320',
					'responsive' => true,
				),
				'wd_stretch_product'      => array(
					'type'       => 'boolean',
					'responsive' => true,
				),
				'wd_bordered_grid'        => array(
					'type' => 'boolean',
				),
				'wd_blocks_gap'           => array(
					'type'       => 'string',
					'responsive' => true,
				),
				'wd_transitionTransform'  => array(
					'type' => 'string',
				),
				'wd_transitionBorder'     => array(
					'type' => 'string',
				),
				'wd_transitionBackground' => array(
					'type' => 'string',
				),
				'wd_transform'            => array(
					'type' => 'boolean',
				),
				'wd_overflowX'            => array(
					'type' => 'string',
				),
				'wd_overflowY'            => array(
					'type' => 'string',
				),
			)
		);

		$attr->add_attr( wd_get_color_control_attrs( 'wd_bordered_grid_color' ) );

		wd_get_padding_control_attrs( $attr, 'wd_padding' );
		wd_get_border_control_attrs( $attr, 'wd_border' );
		wd_get_border_control_attrs( $attr, 'wd_borderHover' );
		wd_get_background_control_attrs( $attr, 'wd_background' );
		wd_get_background_control_attrs( $attr, 'wd_backgroundHover' );

		$attr->add_attr(
			array(
				'position'   => array(
					'type'    => 'string',
					'default' => 'outline',
				),
				'horizontal' => array(
					'type'       => 'string',
					'default'    => '0',
					'responsive' => true,
				),
				'vertical'   => array(
					'type'       => 'string',
					'default'    => '0',
					'responsive' => true,
				),
				'blur'       => array(
					'type'       => 'string',
					'default'    => '10',
					'responsive' => true,
				),
				'spread'     => array(
					'type'       => 'string',
					'default'    => '0',
					'responsive' => true,
				),
			),
			'wd_box_shadow'
		);

		$attr->add_attr( wd_get_color_control_attrs( 'color' ), 'wd_box_shadow' );

		$attr->add_attr(
			array(
				'position'   => array(
					'type'    => 'string',
					'default' => 'outline',
				),
				'horizontal' => array(
					'type'       => 'string',
					'default'    => '0',
					'responsive' => true,
				),
				'vertical'   => array(
					'type'       => 'string',
					'default'    => '0',
					'responsive' => true,
				),
				'blur'       => array(
					'type'       => 'string',
					'default'    => '10',
					'responsive' => true,
				),
				'spread'     => array(
					'type'       => 'string',
					'default'    => '0',
					'responsive' => true,
				),
			),
			'wd_box_shadowHover'
		);

		$attr->add_attr( wd_get_color_control_attrs( 'color' ), 'wd_box_shadowHover' );

		$attr->add_attr(
			array(
				'rotate3d'          => array(
					'type' => 'boolean',
				),
				'perspective'       => array(
					'type'       => 'string',
					'responsive' => true,
				),
				'rotateX'           => array(
					'type'       => 'string',
					'responsive' => true,
				),
				'rotateY'           => array(
					'type'       => 'string',
					'responsive' => true,
				),
				'rotateZ'           => array(
					'type'       => 'string',
					'responsive' => true,
				),
				'translateX'        => array(
					'type'       => 'string',
					'responsive' => true,
					'units'      => 'px',
				),
				'translateY'        => array(
					'type'       => 'string',
					'responsive' => true,
					'units'      => 'px',
				),
				'proportionalScale' => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'scaleX'            => array(
					'type'       => 'string',
					'responsive' => true,
				),
				'scaleY'            => array(
					'type'       => 'string',
					'responsive' => true,
				),
				'skewX'             => array(
					'type'       => 'string',
					'responsive' => true,
				),
				'skewY'             => array(
					'type'       => 'string',
					'responsive' => true,
				),
				'originY'           => array(
					'type' => 'string',
				),
				'originX'           => array(
					'type' => 'string',
				),
			),
			'wd_transform'
		);

		$attr->add_attr(
			array(
				'rotate3d'          => array(
					'type' => 'boolean',
				),
				'perspective'       => array(
					'type'       => 'string',
					'responsive' => true,
				),
				'rotateX'           => array(
					'type'       => 'string',
					'responsive' => true,
				),
				'rotateY'           => array(
					'type'       => 'string',
					'responsive' => true,
				),
				'rotateZ'           => array(
					'type'       => 'string',
					'responsive' => true,
				),
				'translateX'        => array(
					'type'       => 'string',
					'responsive' => true,
					'units'      => 'px',
				),
				'translateY'        => array(
					'type'       => 'string',
					'responsive' => true,
					'units'      => 'px',
				),
				'proportionalScale' => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'scaleX'            => array(
					'type'       => 'string',
					'responsive' => true,
				),
				'scaleY'            => array(
					'type'       => 'string',
					'responsive' => true,
				),
				'skewX'             => array(
					'type'       => 'string',
					'responsive' => true,
				),
				'skewY'             => array(
					'type'       => 'string',
					'responsive' => true,
				),
				'originY'           => array(
					'type' => 'string',
				),
				'originX'           => array(
					'type' => 'string',
				),
			),
			'wd_transformHover'
		);

		return $attr->get_attr();
	}

	/**
	 * Handle post save from REST API and store meta data.
	 *
	 * @param \stdClass        $post Post object prepared for database.
	 * @param \WP_REST_Request $request REST request object.
	 */
	public function handle_rest_post_save( $post, $request ) {
		$this->rest_meta_data = $request->get_param( 'meta' );

		return $post;
	}

	/**
	 * Render CSS.
	 *
	 * @param array  $css Existing CSS.
	 * @param int    $post_id Post ID.
	 * @param object $post Post object.
	 * @return array
	 */
	public function render_css( $css, $post_id, $post ) {
		if ( ! $post_id || ! $post || 'woodmart_layout' !== $post->post_type ) {
			return $css;
		}

		$attrs = array();

		if ( ! empty( $this->rest_meta_data ) ) {
			$attrs = $this->rest_meta_data;
		} else {
			$raw_attrs = $this->get_attributes();

			foreach ( $raw_attrs as $attr => $value ) {
				$attrs[ $attr ] = get_post_meta( $post_id, $attr, true );
			}
		}

		if ( empty( $attrs ) || ! is_array( $attrs ) ) {
			return $css;
		}

		$block_css            = new Block_CSS( $attrs );
		$block_selector       = '.wd.wd .wd-loop-item-' . $post_id . ' .wd-product-wrapper';
		$block_hover_selector = '.wd.wd .wd-loop-item-' . $post_id . ':hover .wd-product-wrapper';

		$block_css->merge_with( wd_get_block_bg_css( $block_selector, $attrs, 'wd_background' ) );
		$block_css->merge_with( wd_get_block_bg_css( $block_hover_selector, $attrs, 'wd_backgroundHover' ) );
		$block_css->merge_with( wd_get_block_border_css( $block_selector, $attrs, 'wd_border' ) );
		$block_css->merge_with( wd_get_block_border_css( $block_hover_selector, $attrs, 'wd_borderHover' ) );
		$block_css->merge_with( wd_get_block_box_shadow_css( $block_selector, $attrs, 'wd_box_shadow' ) );
		$block_css->merge_with( wd_get_block_box_shadow_css( $block_hover_selector, $attrs, 'wd_box_shadowHover' ) );

		$block_css->merge_with( wd_get_block_padding_css( $block_selector, $attrs, 'wd_padding' ) );

		$block_css->merge_with( wd_get_block_transform_css( $block_selector, $attrs, 'wd_transform' ) );
		$block_css->merge_with( wd_get_block_transform_css( $block_hover_selector, $attrs, 'wd_transformHover' ) );

		$block_css->add_css_rules(
			$block_selector,
			array(
				array(
					'attr_name' => 'wd_overflowX',
					'template'  => 'overflow-x: {{value}};',
				),
				array(
					'attr_name' => 'wd_overflowY',
					'template'  => 'overflow-y: {{value}};',
				),
			)
		);

		if ( ! empty( $attrs['wd_bordered_grid'] ) ) {
			$block_css->add_css_rules(
				'.wd.wd .wd-loop-item-wrap-' . $post_id,
				array(
					array(
						'attr_name' => 'wd_bordered_grid_colorCode',
						'template'  => '--wd-bordered-brd: {{value}};',
					),
					array(
						'attr_name' => 'wd_bordered_grid_colorVariable',
						'template'  => '--wd-bordered-brd: var({{value}});',
					),
				)
			);
		}

		$block_css->add_css_rules(
			'.wd.wd .wd-loop-item-wrap-' . $post_id . ' .wd-product',
			array(
				array(
					'attr_name' => 'wd_blocks_gap',
					'template'  => '--wd-block-spacing: {{value}}px;',
				),
			)
		);

		$block_css->add_css_rules(
			'.wd.wd .wd-loop-item-wrap-' . $post_id . ' .wd-product',
			array(
				array(
					'attr_name' => 'wd_blocks_gapTablet',
					'template'  => '--wd-block-spacing: {{value}}px;',
				),
			),
			'tablet'
		);

		$block_css->add_css_rules(
			'.wd.wd .wd-loop-item-wrap-' . $post_id . ' .wd-product',
			array(
				array(
					'attr_name' => 'wd_blocks_gapMobile',
					'template'  => '--wd-block-spacing: {{value}}px;',
				),
			),
			'mobile'
		);

		$transition = '';

		if ( ! empty( $attrs['wd_transitionBorder'] ) ) {
			$transition .= 'border ' . $attrs['wd_transitionBorder'] . 's, box-shadow ' . $attrs['wd_transitionBorder'] . 's, border-radius ' . $attrs['wd_transitionBorder'] . 's, ';
		}

		if ( ! empty( $attrs['wd_transitionBackground'] ) ) {
			$transition .= 'background ' . $attrs['transitionBackground'] . 's, ';
		}

		if ( ! empty( $attrs['wd_transitionTransform'] ) ) {
			$transition .= 'transform ' . $attrs['wd_transitionTransform'] . 's, ';
		}

		if ( $transition ) {
			$block_css->add_to_selector(
				$block_selector,
				'transition: var(--wd-trans-main, all .25s ease), ' . substr( $transition, 0, -2 ) . ', var(--wd-trans-last, last .25s ease);'
			);
		}

		$css_for_devices = $block_css->get_css_for_devices();

		foreach ( $css_for_devices as $device => $css_device ) {
			if ( $css_device ) {
				$css[ $device ] .= ' ' . $css_device;
			}
		}

		return $css;
	}
}

Loop_Item::get_instance();
