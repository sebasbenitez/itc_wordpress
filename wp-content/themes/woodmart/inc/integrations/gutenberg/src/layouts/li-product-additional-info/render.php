<?php
/**
 * Loop Product Additional Info block render.
 *
 * @package woodmart
 */

use XTS\Modules\Layouts\Loop_Item;
use XTS\Modules\Layouts\Main;
use XTS\Modules\Layouts\Global_Data as Builder;

if ( ! function_exists( 'wd_gutenberg_loop_builder_product_additional_info' ) ) {
	/**
	 * Render Loop Product Additional Info block.
	 *
	 * @param array  $block_attributes Block attributes.
	 * @param string $inner_content Inner content.
	 * @return false|string
	 */
	function wd_gutenberg_loop_builder_product_additional_info( $block_attributes, $inner_content ) {
		if ( ! woodmart_woocommerce_installed() ) {
			return '';
		}

		$classes  = wd_get_gutenberg_element_classes( $block_attributes );
		$classes .= ' wd-layout-' . $block_attributes['layout'];
		$classes .= ' wd-style-' . $block_attributes['style'];

		Loop_Item::setup_postdata();

		global $product;

		if ( ! $product ) {
			Loop_Item::reset_postdata();
			return '';
		}

		$display_dimensions = apply_filters( 'wc_product_enable_dimensions_display', $product->has_weight() || $product->has_dimensions() );
		$attributes         = array_keys( array_filter( $product->get_attributes(), 'wc_attributes_array_filter_visible' ) );

		if ( $display_dimensions && $product->has_weight() ) {
			$attributes[] = 'weight';
		}

		if ( $display_dimensions && $product->has_dimensions() ) {
			$attributes[] = 'dimensions';
		}

		$include = array();
		$exclude = array();

		if ( 'include' === $block_attributes['source'] && ! empty( $block_attributes['include'] ) ) {
			$raw_include = explode( ',', $block_attributes['include'] );
			$include     = array_map( 'wc_attribute_taxonomy_name_by_id', wp_parse_id_list( $raw_include ) );

			if ( in_array( 'weight', $raw_include, true ) ) {
				$include[] = 'weight';
			}
			if ( in_array( 'dimensions', $raw_include, true ) ) {
				$include[] = 'dimensions';
			}
		}

		if ( 'include' !== $block_attributes['source'] && ! empty( $block_attributes['exclude'] ) ) {
			$raw_exclude = explode( ',', $block_attributes['exclude'] );
			$exclude     = array_map( 'wc_attribute_taxonomy_name_by_id', wp_parse_id_list( $raw_exclude ) );

			if ( in_array( 'weight', $raw_exclude, true ) ) {
				$exclude[] = 'weight';
			}
			if ( in_array( 'dimensions', $raw_exclude, true ) ) {
				$exclude[] = 'dimensions';
			}
		}

		if ( $include ) {
			if ( $include === $exclude || ! array_intersect( $attributes, $include ) ) {
				Main::restore_preview();
				return '';
			}

			Builder::get_instance()->set_data( 'wd_product_attributes_include', $include );
		}

		if ( $exclude ) {
			if ( ! array_diff( $attributes, $exclude ) ) {
				Builder::get_instance()->set_data( 'wd_product_attributes_include', array() );
				Main::restore_preview();
				return '';
			}

			Builder::get_instance()->set_data( 'wd_product_attributes_exclude', $exclude );
		}

		Builder::get_instance()->set_data(
			'wd_additional_info_table_args',
			array(
				// Attributes.
				'attr_image' => ! empty( $block_attributes['attrImage'] ),
				'attr_name'  => ! empty( $block_attributes['attrName'] ),
				// Terms.
				'term_label' => ! empty( $block_attributes['termLabel'] ),
				'term_image' => ! empty( $block_attributes['termImage'] ),
			)
		);

		ob_start();

		woodmart_enqueue_inline_style( 'woo-mod-shop-attributes-builder' );

		wc_display_product_attributes( $product );

		$content = ob_get_clean();

		Builder::get_instance()->set_data( 'wd_additional_info_table_args', array() );

		Builder::get_instance()->set_data( 'wd_product_attributes_include', array() );
		Builder::get_instance()->set_data( 'wd_product_attributes_exclude', array() );

		if ( ! trim( $content ) ) {
			Loop_Item::reset_postdata();
			return '';
		}

		ob_start();

		?>
		<div class="wd-loop-prod-attrs<?php echo esc_attr( $classes ); ?>">
			<?php echo do_shortcode( $inner_content ); ?>

			<?php echo $content; //phpcs:ignore ?>
		</div>
		<?php
		Loop_Item::reset_postdata();

		return ob_get_clean();
	}
}
