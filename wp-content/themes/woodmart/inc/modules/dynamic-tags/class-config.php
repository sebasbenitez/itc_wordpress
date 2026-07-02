<?php
/**
 * Dynamic tags config class.
 *
 * @package woodmart
 */

namespace XTS\Modules\Dynamic_Tags;

/**
 * Config class.
 */
class Config {
	/**
	 * Get post text config.
	 *
	 * @param int $id Post ID.
	 * @return array
	 */
	public function get_post_text_options( $id = 0 ) {
		$options        = $this->get_text_options();
		$options['acf'] = $this->add_acf_options( 'post', $id, 'text' );

		return $this->filter_post_options( $options, $id );
	}

	/**
	 * Get post media config.
	 *
	 * @param int $id Post ID.
	 * @return array
	 */
	public function get_post_media_options( $id = 0 ) {
		$options        = $this->get_media_options();
		$options['acf'] = $this->add_acf_options( 'post', $id, 'media' );

		return $this->filter_post_options( $options, $id );
	}

	/**
	 * Get post link config.
	 *
	 * @param int $id Post ID.
	 * @return array
	 */
	public function get_post_link_options( $id = 0 ) {
		$options        = $this->get_link_options();
		$options['acf'] = $this->add_acf_options( 'post', $id, 'link' );

		return $this->filter_post_options( $options, $id );
	}

	/**
	 * Get taxonomy text config.
	 *
	 * @param mixed $id Term ID or Taxonomy name.
	 * @return array
	 */
	public function get_taxonomy_text_options( $id = '' ) {
		$options        = $this->get_text_options();
		$options['acf'] = $this->add_acf_options( 'taxonomy', $id, 'text' );

		return $this->filter_taxonomy_options( $options, $id );
	}

	/**
	 * Get taxonomy media config.
	 *
	 * @param mixed $id Term ID or Taxonomy name.
	 * @return array
	 */
	public function get_taxonomy_media_options( $id = '' ) {
		$options        = $this->get_media_options();
		$options['acf'] = $this->add_acf_options( 'taxonomy', $id, 'media' );

		return $this->filter_taxonomy_options( $options, $id );
	}

	/**
	 * Get taxonomy link config.
	 *
	 * @param mixed $id Term ID or Taxonomy name.
	 * @return array
	 */
	public function get_taxonomy_link_options( $id = '' ) {
		$options        = $this->get_link_options();
		$options['acf'] = $this->add_acf_options( 'taxonomy', $id, 'link' );

		return $this->filter_taxonomy_options( $options, $id );
	}

	/**
	 * Get options text config.
	 *
	 * @return array
	 */
	public function get_site_text_options() {
		$options        = $this->get_text_options();
		$options['acf'] = $this->add_acf_options( 'site', 0, 'text' );

		return $this->filter_site_options( $options );
	}

	/**
	 * Get options media config.
	 *
	 * @return array
	 */
	public function get_site_media_options() {
		$options        = $this->get_media_options();
		$options['acf'] = $this->add_acf_options( 'site', 0, 'media' );

		return $this->filter_site_options( $options );
	}

	/**
	 * Get options link config.
	 *
	 * @return array
	 */
	public function get_site_link_options() {
		$options        = $this->get_link_options();
		$options['acf'] = $this->add_acf_options( 'site', 0, 'link' );

		return $this->filter_site_options( $options );
	}

	/**
	 * Filter post options.
	 *
	 * @param array $raw_options Raw options.
	 * @param int   $id          Post ID.
	 * @return array
	 */
	private function filter_post_options( $raw_options, $id ) {
		$options        = array();
		$allowed_groups = array( 'post', 'author', 'comment', 'woocommerce', 'acf' );

		foreach ( $allowed_groups as $group ) {
			if ( ! isset( $raw_options[ $group ] ) || empty( $raw_options[ $group ]['options'] ) ) {
				continue;
			}

			if ( $id ) {
				if ( 'woocommerce' === $group && 'product' !== get_post_type( $id ) ) {
					continue;
				}

				if ( 'comment' === $group && ! comments_open( get_post( $id ) ) ) {
					continue;
				}
			}

			$options[ $group ] = $raw_options[ $group ];
		}

		$options['custom'] = array(
			'label'   => esc_html__( 'Post custom fields', 'woodmart' ),
			'options' => array(
				array(
					'label' => esc_html__( 'Custom meta', 'woodmart' ),
					'value' => 'custom-post-meta',
				),
			),
		);

		return $options;
	}

	/**
	 * Filter taxonomy options.
	 *
	 * @param array $raw_options Raw options.
	 * @param mixed $id          Term ID or Taxonomy name.
	 * @return array
	 */
	private function filter_taxonomy_options( $raw_options, $id ) {
		$options        = array();
		$allowed_groups = array( 'term', 'queried_term', 'category', 'product_cat', 'tag', 'product_tag', 'attribute', 'acf' );
		$taxonomy       = '';

		if ( is_numeric( $id ) && $id ) {
			$term = get_term( $id );

			if ( ! is_wp_error( $term ) ) {
				$taxonomy = $term->taxonomy;
			}
		} else {
			$taxonomy = $id;
		}

		foreach ( $allowed_groups as $group ) {
			if ( ! isset( $raw_options[ $group ] ) || empty( $raw_options[ $group ]['options'] ) ) {
				continue;
			}

			if ( 'queried_term' !== $taxonomy ) {
				if ( 'attribute' === $group ) {
					if ( $taxonomy && ! str_starts_with( $taxonomy, 'pa_' ) ) {
						continue;
					}
				} elseif ( 'term' !== $group && $taxonomy !== $group && 'acf' !== $group ) {
					continue;
				} elseif ( 'term' === $group && ( str_starts_with( $taxonomy, 'pa_' ) || in_array( $taxonomy, array( 'post_tag', 'product_tag', 'category' ), true ) ) && 'term_thumbnail' === $raw_options[ $group ]['options'][0]['value'] ) {
					continue;
				}
			}

			$options[ $group ] = $raw_options[ $group ];
		}

		$options['custom'] = array(
			'label'   => esc_html__( 'Term custom fields', 'woodmart' ),
			'options' => array(
				array(
					'label' => esc_html__( 'Custom meta', 'woodmart' ),
					'value' => 'custom-term-meta',
				),
			),
		);

		return $options;
	}

	/**
	 * Filter site options.
	 *
	 * @param array $raw_options Raw options.
	 * @return array
	 */
	private function filter_site_options( $raw_options ) {
		$options = array();

		if ( isset( $raw_options['site'] ) && ! empty( $raw_options['site']['options'] ) ) {
			$options['site'] = $raw_options['site'];
		}
		if ( isset( $raw_options['acf'] ) && ! empty( $raw_options['acf']['options'] ) ) {
			$options['acf'] = $raw_options['acf'];
		}

		$options['custom'] = array(
			'label'   => esc_html__( 'Custom option', 'woodmart' ),
			'options' => array(
				array(
					'label' => esc_html__( 'Custom option', 'woodmart' ),
					'value' => 'custom-option',
				),
			),
		);

		return $options;
	}

	/**
	 * Add ACF options.
	 *
	 * @param string $context Context: 'post', 'taxonomy', 'site'.
	 * @param mixed  $id      Object ID or taxonomy name.
	 * @param string $type    Field type (text, media, link).
	 * @return array
	 */
	private function add_acf_options( $context, $id = 0, $type = 'text' ) {
		if ( ! function_exists( 'acf_get_field_groups' ) || ! function_exists( 'acf_get_fields' ) ) {
			return array();
		}

		$args = array();

		if ( 'post' === $context && $id ) {
			$args['post_id'] = $id;
		} elseif ( 'taxonomy' === $context && $id ) {
			if ( is_numeric( $id ) ) {
				$term = get_term( $id );
				if ( ! is_wp_error( $term ) && $term ) {
					$args['taxonomy'] = $term->taxonomy;
				} else {
					$args['taxonomy'] = $id;
				}
			} else {
				$args['taxonomy'] = $id;
			}
		}

		$groups      = acf_get_field_groups( $args );
		$acf_options = array();

		foreach ( $groups as $group ) {
			$is_match = false;

			if ( empty( $group['location'] ) ) {
				continue;
			}

			if ( ! $id || 'site' === $context ) {
				foreach ( $group['location'] as $rule_group ) {
					foreach ( $rule_group as $rule ) {
						if ( 'post' === $context && in_array( $rule['param'], array( 'post_type', 'post', 'page', 'page_template', 'post_category', 'post_format', 'post_status' ), true ) ) {
							$is_match = true;
							break;
						}

						if ( 'taxonomy' === $context && in_array( $rule['param'], array( 'taxonomy', 'user_form', 'user_role' ), true ) ) {
							$is_match = true;
							break;
						}

						if ( 'site' === $context && in_array( $rule['param'], array( 'options_page' ), true ) ) {
							$is_match = true;
							break;
						}
					}

					if ( $is_match ) {
						break;
					}
				}
			} else {
				$is_match = true;
			}

			if ( ! $is_match ) {
				continue;
			}

			$fields = acf_get_fields( $group['key'] );

			if ( ! $fields ) {
				continue;
			}

			foreach ( $fields as $field ) {
				if ( 'text' === $type && in_array( $field['type'], array( 'image', 'file', 'gallery', 'group', 'repeater', 'flexible_content', 'clone' ), true ) ) {
					continue;
				} elseif ( 'media' === $type && ! in_array( $field['type'], array( 'image' ), true ) ) {
					continue;
				} elseif ( 'link' === $type && ! in_array( $field['type'], array( 'url', 'link', 'page_link', 'file' ), true ) ) {
					continue;
				}

				$acf_options[] = array(
					'label' => $field['label'],
					'value' => 'acf_' . $context . '_' . $field['name'],
				);
			}
		}

		if ( $acf_options ) {
			return array(
				'label'   => esc_html__( 'ACF', 'woodmart' ),
				'options' => $acf_options,
			);
		}

		return array();
	}

	/**
	 * Get text options.
	 *
	 * @return array[]
	 */
	private function get_text_options() {
		return array(
			'post'         => array(
				'label'   => esc_html__( 'Post', 'woodmart' ),
				'options' => array(
					array(
						'label' => esc_html__( 'Post Title', 'woodmart' ),
						'value' => 'post_title',
					),
					array(
						'label' => esc_html__( 'Post ID', 'woodmart' ),
						'value' => 'post_id',
					),
					array(
						'label' => esc_html__( 'Post Slug', 'woodmart' ),
						'value' => 'post_slug',
					),
					array(
						'label' => esc_html__( 'Post Excerpt', 'woodmart' ),
						'value' => 'post_excerpt',
					),
					array(
						'label' => esc_html__( 'Post Date', 'woodmart' ),
						'value' => 'post_date',
					),
					array(
						'label' => esc_html__( 'Post Author Name', 'woodmart' ),
						'value' => 'post_author_name',
					),
					array(
						'label' => esc_html__( 'Reading Time', 'woodmart' ),
						'value' => 'post_reading_time',
					),
				),
			),
			'author'       => array(
				'label'   => esc_html__( 'Author', 'woodmart' ),
				'options' => array(
					array(
						'label' => esc_html__( 'First Name', 'woodmart' ),
						'value' => 'author_first_name',
					),
					array(
						'label' => esc_html__( 'Last Name', 'woodmart' ),
						'value' => 'author_last_name',
					),
					array(
						'label' => esc_html__( 'Author Description', 'woodmart' ),
						'value' => 'author_description',
					),
					array(
						'label' => esc_html__( 'Author Posts Count', 'woodmart' ),
						'value' => 'author_posts',
					),
				),
			),
			'comment'      => array(
				'label'   => esc_html__( 'Comment', 'woodmart' ),
				'options' => array(
					array(
						'label' => esc_html__( 'Comment Number', 'woodmart' ),
						'value' => 'comment_number',
					),
					array(
						'label' => esc_html__( 'Comment Status', 'woodmart' ),
						'value' => 'comment_status',
					),
				),
			),
			'woocommerce'  => array(
				'label'   => esc_html__( 'Products', 'woodmart' ),
				'options' => array(
					array(
						'label' => esc_html__( 'Product Price', 'woodmart' ),
						'value' => 'product_price',
					),
					array(
						'label' => esc_html__( 'Product Regular Price', 'woodmart' ),
						'value' => 'product_price_regular',
					),
					array(
						'label' => esc_html__( 'Product Sale Price', 'woodmart' ),
						'value' => 'product_price_sale',
					),
					array(
						'label' => esc_html__( 'Product Sale Percentage', 'woodmart' ),
						'value' => 'product_sale_percentage',
					),
					array(
						'label' => esc_html__( 'Product Save Amount', 'woodmart' ),
						'value' => 'product_save_amount',
					),
					array(
						'label' => esc_html__( 'Product Short Description', 'woodmart' ),
						'value' => 'product_short_description',
					),
					array(
						'label' => esc_html__( 'Product SKU', 'woodmart' ),
						'value' => 'product_sku',
					),
					array(
						'label' => esc_html__( 'Product Stock Status', 'woodmart' ),
						'value' => 'product_stock_status',
					),
					array(
						'label' => esc_html__( 'Product Stock Quantity', 'woodmart' ),
						'value' => 'product_stock_quantity',
					),
					array(
						'label' => esc_html__( 'Product Weight', 'woodmart' ),
						'value' => 'product_weight',
					),
					array(
						'label' => esc_html__( 'Product Length', 'woodmart' ),
						'value' => 'product_length',
					),
					array(
						'label' => esc_html__( 'Product Width', 'woodmart' ),
						'value' => 'product_width',
					),
					array(
						'label' => esc_html__( 'Product Height', 'woodmart' ),
						'value' => 'product_height',
					),
					array(
						'label' => esc_html__( 'Product Purchase Note', 'woodmart' ),
						'value' => 'product_purchase_note',
					),
					array(
						'label' => esc_html__( 'Total Sales Count', 'woodmart' ),
						'value' => 'product_total_sales',
					),
				),
			),
			'term'         => array(
				'label'   => esc_html__( 'Term', 'woodmart' ),
				'options' => array(
					array(
						'label' => esc_html__( 'Term ID', 'woodmart' ),
						'value' => 'term_id',
					),
					array(
						'label' => esc_html__( 'Term Name', 'woodmart' ),
						'value' => 'term_name',
					),
					array(
						'label' => esc_html__( 'Term Description', 'woodmart' ),
						'value' => 'term_description',
					),
					array(
						'label' => esc_html__( 'Term Slug', 'woodmart' ),
						'value' => 'term_slug',
					),
					array(
						'label' => esc_html__( 'Term Permalink', 'woodmart' ),
						'value' => 'term_permalink',
						'type'  => 'link',
					),
					array(
						'label' => esc_html__( 'Term Count Posts', 'woodmart' ),
						'value' => 'term_count_posts',
					),
				),
			),
			'queried_term' => array(
				'label'   => esc_html__( 'Archive', 'woodmart' ),
				'options' => array(
					array(
						'label' => esc_html__( 'Archive Title', 'woodmart' ),
						'value' => 'archive_title',
					),
					array(
						'label' => esc_html__( 'Archive Description', 'woodmart' ),
						'value' => 'archive_description',
					),
					array(
						'label' => esc_html__( 'Results Count Text', 'woodmart' ),
						'value' => 'archive_result_count',
					),
				),
			),
			'product_cat'  => array(
				'label'   => esc_html__( 'Product Category', 'woodmart' ),
				'options' => array(
					array(
						'label' => esc_html__( 'Extra description', 'woodmart' ),
						'value' => 'product_cat_extra_desc',
					),
				),
			),
			'attribute'    => array(
				'label'   => esc_html__( 'Attribute', 'woodmart' ),
				'options' => array(
					array(
						'label' => esc_html__( 'Term hint content', 'woodmart' ),
						'value' => 'pa_term_hint',
					),
				),
			),
			'site'         => array(
				'label'   => esc_html__( 'Site', 'woodmart' ),
				'options' => array(
					array(
						'label' => esc_html__( 'Site Title', 'woodmart' ),
						'value' => 'site_title',
					),
					array(
						'label' => esc_html__( 'Site Description', 'woodmart' ),
						'value' => 'site_desc',
					),
					array(
						'label' => esc_html__( 'Admin Email', 'woodmart' ),
						'value' => 'site_admin_email',
					),
					array(
						'label' => esc_html__( 'Current Year', 'woodmart' ),
						'value' => 'current_year',
					),
				),
			),
		);
	}

	/**
	 * Get media options.
	 *
	 * @return array[]
	 */
	private function get_media_options() {
		return array(
			'post'        => array(
				'label'   => esc_html__( 'Post', 'woodmart' ),
				'options' => array(
					array(
						'label' => esc_html__( 'Featured Image', 'woodmart' ),
						'value' => 'featured_image',
					),
				),
			),
			'author'      => array(
				'label'   => esc_html__( 'Author', 'woodmart' ),
				'options' => array(
					array(
						'label' => esc_html__( 'Author Avatar', 'woodmart' ),
						'value' => 'author_avatar',
					),
				),
			),
			'woocommerce' => array(
				'label'   => esc_html__( 'Products', 'woodmart' ),
				'options' => array(
					array(
						'label' => esc_html__( 'Product Second Image', 'woodmart' ),
						'value' => 'product_second_image',
					),
				),
			),
			'term'        => array(
				'label'   => esc_html__( 'Term', 'woodmart' ),
				'options' => array(
					array(
						'label' => esc_html__( 'Term Thumbnail', 'woodmart' ),
						'value' => 'term_thumbnail',
					),
				),
			),
			'product_cat' => array(
				'label'   => esc_html__( 'Product Category', 'woodmart' ),
				'options' => array(
					array(
						'label' => esc_html__( 'Category icon', 'woodmart' ),
						'value' => 'product_cat_icon_alt',
					),
					array(
						'label' => esc_html__( 'Large category icon', 'woodmart' ),
						'value' => 'product_cat_icon',
					),
					array(
						'label' => esc_html__( 'Category page title background', 'woodmart' ),
						'value' => 'product_cat_title_image',
					),
				),
			),
			'attribute'   => array(
				'label'   => esc_html__( 'Attribute', 'woodmart' ),
				'options' => array(
					array(
						'label' => esc_html__( 'Image swatch', 'woodmart' ),
						'value' => 'pa_swatch_image',
					),
					array(
						'label' => esc_html__( 'Term image', 'woodmart' ),
						'value' => 'pa_term_image',
					),
				),
			),
		);
	}

	/**
	 * Get link options.
	 *
	 * @return array[]
	 */
	private function get_link_options() {
		return array(
			'post'        => array(
				'label'   => esc_html__( 'Post', 'woodmart' ),
				'options' => array(
					array(
						'label' => esc_html__( 'Post Permalink', 'woodmart' ),
						'value' => 'post_permalink',
					),
				),
			),
			'author'      => array(
				'label'   => esc_html__( 'Author', 'woodmart' ),
				'options' => array(
					array(
						'label' => esc_html__( 'Author Posts URL', 'woodmart' ),
						'value' => 'author_posts_url',
					),
				),
			),
			'woocommerce' => array(
				'label'   => esc_html__( 'Products', 'woodmart' ),
				'options' => array(
					array(
						'label' => esc_html__( 'Product Add To Cart URL', 'woodmart' ),
						'value' => 'product_add_to_cart_url',
					),
				),
			),
			'term'        => array(
				'label'   => esc_html__( 'Term', 'woodmart' ),
				'options' => array(
					array(
						'label' => esc_html__( 'Term Permalink', 'woodmart' ),
						'value' => 'term_permalink',
					),
				),
			),
			'site'        => array(
				'label'   => esc_html__( 'Site', 'woodmart' ),
				'options' => array(
					array(
						'label' => esc_html__( 'Site URL', 'woodmart' ),
						'value' => 'site_url',
					),
				),
			),
		);
	}
}
