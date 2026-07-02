<?php
/**
 * Dynamic tags content class.
 *
 * @package woodmart
 */

namespace XTS\Modules\Dynamic_Tags;

use XTS\Singleton;

/**
 * Content class.
 */
class Content extends Singleton {
	/**
	 * Queried object for archive pages, used for dynamic term content.
	 *
	 * @var object
	 */
	public $queried_object = null;

	/**
	 * Initialize the class and set up hooks.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'template_redirect', array( $this, 'cache_archive_queried_object' ) );
	}

	/**
	 * Set the queried object for archive pages to enable dynamic term content retrieval.
	 *
	 * @return void
	 */
	public function cache_archive_queried_object() {
		$this->queried_object = get_queried_object();
	}

	/**
	 * Get content based on settings.
	 *
	 * @param array $settings Settings array containing 'source' and other parameters.
	 * @return string
	 */
	public function get( $settings ) {
		$settings = wp_parse_args(
			$settings,
			array(
				'type'            => 'text',
				'source'          => 'current_post',
				'post_id'         => get_the_ID(),
				'term_id'         => '',
				'option'          => '',
				'taxonomy'        => '',
				'custom_meta_key' => '',
				'show_as_link'    => false,
			)
		);

		if ( in_array( $settings['source'], array( 'current_post', 'other_post' ), true ) ) {
			if ( ! $settings['post_id'] || ! $settings['option'] ) {
				return '';
			}

			if ( str_starts_with( $settings['option'], 'acf_post_' ) ) {
				return $this->get_acf_content( $settings, 'post' );
			}

			if ( str_starts_with( $settings['option'], 'product_' ) ) {
				return $this->get_product_content( $settings );
			}

			return $this->get_post_content( $settings );
		} elseif ( in_array( $settings['source'], array( 'current_taxonomy', 'other_taxonomy' ), true ) ) {
			if ( str_starts_with( $settings['option'], 'acf_taxonomy_' ) ) {
				return $this->get_acf_content( $settings, 'taxonomy' );
			}

			return $this->get_term_content( $settings );
		} elseif ( 'site' === $settings['source'] ) {
			if ( str_starts_with( $settings['option'], 'acf_site_' ) ) {
				return $this->get_acf_content( $settings, 'site' );
			}

			return $this->get_site_content( $settings );
		}

		return '';
	}

	/**
	 * Get ACF field content based on settings.
	 *
	 * @param array  $settings Settings array.
	 * @param string $context  Context checking parameters.
	 * @return string
	 */
	private function get_acf_content( $settings, $context ) {
		if ( ! function_exists( 'get_field' ) ) {
			return '';
		}

		$field_name = str_replace( 'acf_' . $context . '_', '', $settings['option'] );
		$target_id  = '';

		if ( 'post' === $context ) {
			$target_id = $settings['post_id'];
		} elseif ( 'taxonomy' === $context ) {
			$queried_term = $this->get_queried_term( $settings );

			if ( $queried_term && property_exists( $queried_term, 'term_id' ) ) {
				$target_id = $queried_term->taxonomy . '_' . $queried_term->term_id;
			}
		} elseif ( 'site' === $context ) {
			$target_id = 'option';
		}

		if ( ! $target_id || ! $field_name ) {
			return '';
		}

		$content = get_field( $field_name, $target_id );

		if ( is_array( $content ) ) {
			if ( isset( $content['ID'] ) ) {
				$content = $content['ID'];
			} elseif ( isset( $content['id'] ) ) {
				$content = $content['id'];
			} elseif ( isset( $content['url'] ) ) {
				$content = $content['url'];
			} else {
				$content = '';
			}
		}

		if ( is_object( $content ) ) {
			return '';
		}

		return (string) $content;
	}

	/**
	 * Get post content based on settings.
	 *
	 * @param array $settings Settings array.
	 * @return string
	 */
	private function get_post_content( $settings ) {
		$post_id = $settings['post_id'];
		$content = '';

		switch ( $settings['option'] ) {
			case 'post_id':
				$content = $post_id;
				break;

			case 'post_title':
				$content = get_the_title( $post_id );

				if ( ! empty( $settings['show_as_link'] ) ) {
					$content = '<a href="' . esc_url( get_permalink( $post_id ) ) . '">' . $content . '</a>';
				}
				break;

			case 'post_permalink':
				$content = get_permalink( $post_id );
				break;

			case 'post_slug':
				$content = get_post_field( 'post_name', $post_id );
				break;

			case 'post_excerpt':
				$content = wp_kses_post( get_the_excerpt( $post_id ) );
				break;

			case 'post_date':
				$content = get_the_date( '', $post_id );
				break;

			case 'post_type':
				$content = get_post_type( $post_id );
				break;

			case 'post_status':
				$content = get_post_status( $post_id );
				break;

			case 'post_reading_time':
				$post_content = get_post_field( 'post_content', $post_id );
				$word_count   = str_word_count( wp_strip_all_tags( $post_content ) );
				$content      = ceil( $word_count / 200 );
				break;

			case 'post_author_name':
				$author_id = get_post_field( 'post_author', $post_id );
				$content   = get_the_author_meta( 'display_name', $author_id );
				break;

			case 'author_posts_url':
				$author_id = get_post_field( 'post_author', $post_id );
				$content   = get_author_posts_url( $author_id );
				break;

			case 'author_avatar':
				$author_id = get_post_field( 'post_author', $post_id );
				$args      = get_avatar_data( $author_id );
				$content   = $args['id'] ?? '';
				break;

			case 'author_description':
				$author_id = get_post_field( 'post_author', $post_id );
				$content   = get_the_author_meta( 'description', $author_id );
				break;

			case 'author_posts':
				$author_id = get_post_field( 'post_author', $post_id );
				$content   = count_user_posts( $author_id );
				break;

			case 'author_first_name':
				$author_id = get_post_field( 'post_author', $post_id );
				$content   = get_the_author_meta( 'first_name', $author_id );
				break;

			case 'author_last_name':
				$author_id = get_post_field( 'post_author', $post_id );
				$content   = get_the_author_meta( 'last_name', $author_id );
				break;

			case 'comment_number':
				$content = get_comments_number( $post_id );
				break;

			case 'comment_status':
				$content = get_post_field( 'comment_status', $post_id );
				break;

			case 'featured_image':
				$content = get_post_thumbnail_id( $post_id );
				break;

			case 'custom-post-meta':
				$content = get_post_meta( $post_id, $settings['custom_meta_key'], true );

				if ( is_array( $content ) ) {
					return '';
				}
				break;
		}

		return $content;
	}

	/**
	 * Get product-specific field content based on settings.
	 *
	 * @param array $settings Settings array.
	 * @return string
	 */
	private function get_product_content( $settings ) {
		$product = wc_get_product( $settings['post_id'] );
		$content = '';

		if ( ! $product ) {
			return '';
		}

		switch ( $settings['option'] ) {
			case 'product_price':
				$content = wp_kses( wc_price( $product->get_price() ), array() );
				break;
			case 'product_price_regular':
				$content = $product->get_regular_price() ? wp_kses( wc_price( $product->get_regular_price() ), array() ) : '';
				break;
			case 'product_price_sale':
				$content = $product->get_sale_price() ? wp_kses( wc_price( $product->get_sale_price() ), array() ) : '';
				break;
			case 'product_sale_percentage':
				if ( $product->is_on_sale() ) {
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

						$content = '-' . $max_percentage . '%';
					} elseif ( ( 'simple' === $product->get_type() || 'external' === $product->get_type() || 'variation' === $product->get_type() ) ) {
						$content = '-' . round( ( ( (float) $product->get_regular_price() - (float) $product->get_sale_price() ) / (float) $product->get_regular_price() ) * 100 ) . '%';
					}
				}
				break;
			case 'product_save_amount':
				if ( $product->is_on_sale() ) {
					$content = wp_kses( wc_price( (float) $product->get_regular_price() - (float) $product->get_sale_price() ), array() );
				}
				break;

			case 'product_short_description':
				$content = apply_filters( 'woocommerce_short_description', $product->get_short_description() );
				break;
			case 'product_sku':
				$content = $product->get_sku();
				break;

			case 'product_stock_status':
				$status  = $product->get_stock_status();
				$options = wc_get_product_stock_status_options();
				$content = $options[ $status ] ?? $status;
				break;
			case 'product_stock_quantity':
				$content = $product->get_stock_quantity() ?? 0;
				break;
			case 'product_weight':
				$content = $product->has_weight() ? $product->get_weight() . ' ' . get_option( 'woocommerce_weight_unit' ) : '';
				break;
			case 'product_length':
				$content = $product->get_length();
				$content = $content ? $content . ' ' . get_option( 'woocommerce_dimension_unit' ) : '';
				break;
			case 'product_width':
				$content = $product->get_width();
				$content = $content ? $content . ' ' . get_option( 'woocommerce_dimension_unit' ) : '';
				break;
			case 'product_height':
				$content = $product->get_height();
				$content = $content ? $content . ' ' . get_option( 'woocommerce_dimension_unit' ) : '';
				break;
			case 'product_total_sales':
				$content = $product->get_total_sales();
				break;
			case 'product_purchase_note':
				$content = $product->get_purchase_note();
				break;

			case 'product_add_to_cart_url':
				$content = esc_url( $product->add_to_cart_url() );
				break;

			case 'product_second_image':
				$attachment_ids = $product->get_gallery_image_ids();
				if ( ! empty( $attachment_ids ) ) {
					$content = current( $attachment_ids );
				}
				break;
		}

		return $content;
	}

	/**
	 * Get site content based on settings.
	 *
	 * @param array $settings Settings array.
	 * @return string
	 */
	private function get_site_content( $settings ) {
		switch ( $settings['option'] ) {
			case 'site_title':
				return get_bloginfo( 'name' );

			case 'site_desc':
				return get_bloginfo( 'description' );

			case 'site_url':
				return get_bloginfo( 'url' );

			case 'site_admin_email':
				return get_bloginfo( 'admin_email' );

			case 'current_year':
				return gmdate( 'Y' );

			case 'custom-option':
				$option_name = isset( $settings['custom_option_key'] ) ? $settings['custom_option_key'] : '';

				$content = get_option( $option_name, '' );

				if ( is_array( $content ) ) {
					return '';
				}

				return $content;
		}

		return '';
	}

	/**
	 * Get dynamic term content based on settings.
	 *
	 * @param array $settings Settings for dynamic content.
	 * @return string
	 */
	private function get_term_content( $settings ) {
		$content      = '';
		$queried_term = $this->get_queried_term( $settings );

		switch ( $settings['option'] ) {
			case 'archive_title':
				add_filter( 'get_the_archive_title_prefix', '__return_empty_string', 1 );
				$content = get_the_archive_title();
				remove_filter( 'get_the_archive_title_prefix', '__return_empty_string', 1 );
				break;

			case 'archive_description':
				$content = get_the_archive_description();
				break;

			case 'archive_result_count':
				global $wp_query;
				$count = $wp_query->found_posts;
				// Translators: %s is replaced with the number of found posts.
				$content = sprintf( esc_html__( '%s results found', 'woodmart' ), $count );
				break;
		}

		if ( $queried_term && property_exists( $queried_term, 'term_id' ) ) {
			switch ( $settings['option'] ) {
				case 'term_name':
					$content = $queried_term->name;

					if ( ! empty( $settings['show_as_link'] ) ) {
						$content = '<a href="' . esc_url( get_term_link( $queried_term ) ) . '">' . $content . '</a>';
					}
					break;
				case 'term_id':
					$content = $queried_term->term_id;
					break;
				case 'term_slug':
					$content = $queried_term->slug;
					break;
				case 'term_description':
					$content = $queried_term->description;
					break;
				case 'term_count_posts':
					$content = $queried_term->count;
					break;
				case 'term_permalink':
					$content = get_term_link( $queried_term );
					break;
				case 'term_thumbnail':
					$content = get_term_meta( $queried_term->term_id, 'thumbnail_id', true );
					break;
				case 'custom-term-meta':
					$tern_key = isset( $settings['custom_term_key'] ) ? $settings['custom_term_key'] : '';

					$content = get_term_meta( $queried_term->term_id, $tern_key, true );
					break;
				case 'product_cat_icon_alt':
					$content = $this->get_attachment_id( get_term_meta( $queried_term->term_id, 'category_icon_alt', true ) );
					break;
				case 'product_cat_icon':
					$content = $this->get_attachment_id( get_term_meta( $queried_term->term_id, 'category_icon', true ) );
					break;
				case 'product_cat_title_image':
					$content = $this->get_attachment_id( get_term_meta( $queried_term->term_id, 'title_image', true ) );
					break;
				case 'product_cat_extra_desc':
					$content_type = get_term_meta( $queried_term->term_id, 'category_extra_description_type', true );

					if ( 'text' === $content_type ) {
						$content = do_shortcode( wpautop( get_term_meta( $queried_term->term_id, 'category_extra_description_text', true ) ) );
					} elseif ( 'html_block' === $content_type ) {
						$content = woodmart_get_html_block( get_term_meta( $queried_term->term_id, 'category_extra_description_html_block', true ) );
					}
					break;
				case 'pa_swatch_image':
					$content = $this->get_attachment_id( get_term_meta( $queried_term->term_id, 'image', true ) );
					break;
				case 'pa_term_image':
					$content = $this->get_attachment_id( get_term_meta( $queried_term->term_id, 'pa_term_image', true ) );
					break;
				case 'pa_term_hint':
					$content = get_term_meta( $queried_term->term_id, 'pa_term_hint', true );
					break;
			}
		}

		if ( is_array( $content ) ) {
			return '';
		}

		return $content;
	}

	/**
	 * Extract attachment ID from a meta array value.
	 *
	 * @param array|string $value Meta value.
	 * @return string
	 */
	private function get_attachment_id( $value ) {
		if ( is_array( $value ) ) {
			if ( ! empty( $value['id'] ) ) {
				return $value['id'];
			} else {
				return reset( $value );
			}
		}

		return $value;
	}

	/**
	 * Get the queried term based on settings, handling different sources and contexts.
	 *
	 * @param array $settings Settings.
	 * @return WP_Term[]|null
	 */
	private function get_queried_term( $settings ) {
		$queried_term = null;

		if ( 'other_taxonomy' === $settings['source'] ) {
			$term = get_term( $settings['term_id'] );

			if ( $term instanceof \WP_Term ) {
				$queried_term = $term;
			}
		} elseif ( is_archive() && ( ! empty( $settings['taxonomy'] ) && 'queried_term' === $settings['taxonomy'] ) ) {
			$queried_term = $this->queried_object;
		} elseif ( ! empty( $settings['taxonomy'] ) ) {
			$terms = get_the_terms( get_the_ID(), $settings['taxonomy'] );

			if ( ! is_wp_error( $terms ) && $terms && is_array( $terms ) ) {
				$term = reset( $terms );

				if ( $term instanceof \WP_Term ) {
					$queried_term = $term;
				}
			}
		}

		return $queried_term;
	}
}

Content::get_instance();
