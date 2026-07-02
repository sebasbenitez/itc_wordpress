<?php
/**
 * Custom labels class.
 *
 * @package woodmart
 */

namespace XTS\Modules\Custom_Labels;

use XTS\Singleton;
use XTS\Gutenberg\Blocks_Assets;
use XTS\Gutenberg\Post_CSS;

/**
 * Frontend class.
 *
 * @codeCoverageIgnore
 */
class Frontend extends Singleton {
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
		$this->manager = Manager::get_instance();

		add_filter( 'woodmart_product_label_output', array( $this, 'render_labels' ), 10, 2 );
	}

	/**
	 * Array of label IDs for which styles have already been output.
	 *
	 * @var array
	 */
	private static $rendered_styles = array();

	/**
	 * Render all custom labels.
	 *
	 * @param array $output The output array.
	 * @param array $selection The selection array.
	 */
	public function render_labels( $output, $selection = array() ) {
		$label_ids = $this->manager->get_current_custom_labels_ids();

		ob_start();
		$this->print_custom_label_preview( ' wd-loop-prod-labels' );
		$preview_content = ob_get_clean();

		if ( $preview_content ) {
			return array( 'preview' => $preview_content );
		}

		$source  = isset( $selection['source'] ) ? $selection['source'] : 'all';
		$include = ! empty( $selection['include'] ) ? $selection['include'] : array();
		$exclude = ! empty( $selection['exclude'] ) ? $selection['exclude'] : array();

		foreach ( $label_ids as $label_id ) {
			$post = get_post( $label_id );

			if ( ! $post || ! $label_id ) {
				continue;
			}

			$label_id = apply_filters( 'wpml_object_id', $label_id, $post->post_type, true );

			$classes = $this->get_classes( $label_id );
			$content = $this->get_content( $label_id );

			$output[$label_id] = '<div class="' . esc_attr( $classes ) . '">' . $content . '</div>'; // phpcs:ignore.
		}

		if ( 'include' === $source && empty( $include ) ) {
			return array();
		}

		foreach ( $output as $label_id => $content ) {
			if ( 'all' === $source && ! empty( $exclude ) && in_array( (string) $label_id, $exclude, true ) ) {
				unset( $output[ $label_id ] );
			}

			if ( 'include' === $source && ! empty( $include ) && ! in_array( (string) $label_id, $include, true ) ) {
				unset( $output[ $label_id ] );
			}
		}

		return $output;
	}

	/**
	 * Get sale label percentage.
	 *
	 * @param string $classes Classes.
	 * @return string
	 */
	private function get_sale_label_percentage( $classes = '' ) {
		global $product;

		if ( ! $product ) {
			return '';
		}

		$percentage = '';

		if ( 'variable' === $product->get_type() ) {
			$available_variations = $product->get_variation_prices();
			$max_percentage       = 0;

			foreach ( $available_variations['regular_price'] as $key => $regular_price ) {
				$sale_price = $available_variations['sale_price'][ $key ];

				if ( $sale_price < $regular_price ) {
					$percentage = round( ( ( (float) $regular_price - (float) $sale_price ) / (float) $regular_price ) * 100 );

					if ( $percentage > $max_percentage ) {
						$max_percentage = $percentage;
					}
				}
			}

			$percentage = $max_percentage;
		} elseif ( 'simple' === $product->get_type() || 'external' === $product->get_type() || 'variation' === $product->get_type() ) {
			$percentage = round( ( ( (float) $product->get_regular_price() - (float) $product->get_sale_price() ) / (float) $product->get_regular_price() ) * 100 );
		}

		/* translators: %d: sale percentage value */
		return '<span class="onsale' . $classes . '">' . sprintf( _x( '-%d%%', 'sale percentage', 'woodmart' ), $percentage ) . '</span>';
	}

	/**
	 * Get default labels output.
	 *
	 * @param string $classes Classes.
	 *
	 * @return array
	 */
	public function get_default_labels_output( $classes = '' ) {
		global $product;

		if ( ! $product ) {
			return array();
		}

		$output = array();

		if ( $product->is_on_sale() && woodmart_get_opt( 'sale_label', 1 ) ) {
			$percentage_label = woodmart_get_opt( 'percentage_label' );

			if ( $percentage_label && in_array( $product->get_type(), array( 'variable', 'simple', 'external', 'variation' ), true ) ) {
				$output['sale'] = $this->get_sale_label_percentage( $classes );
			} else {
				$output['sale'] = '<span class="onsale' . $classes . '">' . esc_html__( 'Sale', 'woodmart' ) . '</span>';
			}
		}

		if ( ! $product->is_in_stock() && woodmart_get_opt( 'stock_label', 1 ) && 'thumbnail' === woodmart_get_opt( 'stock_status_position', 'thumbnail' ) ) {
			$output['out-of-stock'] = '<span class="out-of-stock' . $classes . '">' . esc_html__( 'Sold out', 'woodmart' ) . '</span>';
		}

		if ( $product->is_featured() && woodmart_get_opt( 'hot_label', 1 ) ) {
			$output['hot'] = '<span class="featured' . $classes . '">' . esc_html__( 'Hot', 'woodmart' ) . '</span>';
		}

		if ( woodmart_is_new_label_needed( get_the_ID() ) && woodmart_get_opt( 'new_label', 1 ) ) {
			$output['new'] = '<span class="new' . $classes . '">' . esc_html__( 'New', 'woodmart' ) . '</span>';
		}

		if ( woodmart_get_opt( 'attribute_label', 1 ) ) {
			$product_attributes = woodmart_get_product_attributes_label( $classes );

			if ( $product_attributes ) {
				$output['attrs'] = $product_attributes;
			}
		}

		if ( $output ) {
			woodmart_enqueue_inline_style( 'woo-mod-product-labels-default' );
		}

		return $output;
	}

	/**
	 * Get default label HTML.
	 *
	 * @param bool   $label_id Label ID.
	 * @param string $label_classes Label classes.
	 * @return string
	 */
	public function get_default_label_html( $label_id = false, $label_classes = '' ) {
		global $product;

		if ( ! $label_id || ! $product ) {
			return '';
		}

		$is_preview = woodmart_is_builder_editor_preview();

		$content = '';

		if ( 'sale' === $label_id && ( $is_preview || $product->is_on_sale() ) ) {
			$percentage_label = woodmart_get_opt( 'percentage_label' );

			if ( $percentage_label && in_array( $product->get_type(), array( 'variable', 'simple', 'external', 'variation' ), true ) ) {
				$content = $is_preview
				/* translators: %d is the percentage value. */
				? '<span class="onsale' . $label_classes . '">' . sprintf( _x( '-%d%%', 'sale percentage', 'woodmart' ), 10 ) . '</span>'
				: $this->get_sale_label_percentage( $label_classes );
			} else {
				$content = '<span class="onsale' . $label_classes . '">' . esc_html__( 'Sale', 'woodmart' ) . '</span>';
			}
		}

		if ( 'out-of-stock' === $label_id && ( $is_preview || ! $product->is_in_stock() ) ) {
			$content = '<span class="out-of-stock' . $label_classes . '">' . esc_html__( 'Sold out', 'woodmart' ) . '</span>';
		}

		if ( 'hot' === $label_id && ( $is_preview || $product->is_featured() ) ) {
			$content = '<span class="featured' . $label_classes . '">' . esc_html__( 'Hot', 'woodmart' ) . '</span>';
		}

		if ( 'new' === $label_id && ( $is_preview || woodmart_is_new_label_needed( get_the_ID() ) ) ) {
			$content = '<span class="new' . $label_classes . '">' . esc_html__( 'New', 'woodmart' ) . '</span>';
		}

		if ( $content ) {
			woodmart_enqueue_inline_style( 'woo-mod-product-labels-default' );
		}

		return $content;
	}

	/**
	 * Show custom label preview.
	 *
	 * @param string $classes Classes.
	 * @return void
	 */
	public function print_custom_label_preview( $classes = '' ) {
		$label_id = get_queried_object_id();

		if ( ! $label_id || 'wd_custom_label' !== get_post_type( $label_id ) ) {
			return;
		}

		?>
		<div class="product-labels<?php echo esc_attr( $classes ); ?>">
			<div class="wd-custom-label wd-label-<?php echo esc_attr( $label_id ); ?>">
				<?php echo $this->get_content( $label_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Get the classes for a given label.
	 *
	 * @param int $label_id The ID of the label post to retrieve classes for.
	 * @return string The classes.
	 */
	public function get_classes( $label_id ) {
		$classes  = 'wd-custom-label';
		$classes .= ' wd-label-' . esc_attr( $label_id );

		$hide_on_desktop = ! empty( get_post_meta( $label_id, 'wd_hide_custom_labels', true ) );
		$hide_on_tablet  = ! empty( get_post_meta( $label_id, 'wd_hide_custom_labelsTablet', true ) );
		$hide_on_mobile  = ! empty( get_post_meta( $label_id, 'wd_hide_custom_labelsMobile', true ) );

		if ( $hide_on_desktop ) {
			$classes .= ' wd-hide-lg';
		}

		if ( $hide_on_tablet ) {
			$classes .= ' wd-hide-md-sm';
		}

		if ( $hide_on_mobile ) {
			$classes .= ' wd-hide-sm';
		}

		return $classes;
	}

	/**
	 * Get the content for a given label.
	 *
	 * @param int $label_id The ID of the label post to retrieve content for.
	 * @return string The content.
	 */
	public function get_content( $label_id ) {
		$label_id     = apply_filters( 'wpml_object_id', $label_id, 'wd_custom_label', true );
		$post         = get_post( $label_id );
		$post_content = get_the_content( null, false, $label_id );

		$content = '';

		if ( ! $post || ! $label_id ) {
			return '';
		}

		$bg_image = get_post_meta( $label_id, 'wd_backgroundImage', true );

		if ( ! in_array( $label_id, self::$rendered_styles, true ) ) {
			woodmart_enqueue_inline_style( 'opt-custom-labels' );

			$content  = Blocks_Assets::get_instance()->get_inline_scripts( $label_id );
			$content .= Post_CSS::get_instance()->get_inline_blocks_css( $label_id, false );

			self::$rendered_styles[] = $label_id;
		}

		if ( has_blocks( $post_content ) ) {
			if ( ! empty( $bg_image['id'] ) ) {
				$content .= '<div class="wd-bg-img">';

				$bg_image_size = get_post_meta( $label_id, 'wd_backgroundImageSize', true );
				$image_size    = $bg_image_size ? $bg_image_size : 'full';

				$content .= woodmart_otf_get_image_html( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					$bg_image['id'],
					$image_size,
					false
				);

				$content .= '</div>';
			}

			$content .= wp_filter_content_tags( do_shortcode( shortcode_unautop( do_blocks( $post_content ) ) ) );
		}

		return $content;
	}
}

Frontend::get_instance();
