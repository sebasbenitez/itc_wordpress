<?php
/**
 * Import XML.
 *
 * @package woodmart
 */

namespace XTS\Admin\Modules\Import;

use Elementor\Plugin;
use Exception;
use WOODMART_CORE\Importer\Import;

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * Import XML.
 */
class XML {
	/**
	 * Version name.
	 *
	 * @var string
	 */
	private $version;
	/**
	 * File type.
	 *
	 * @var string
	 */
	private $type;
	/**
	 * Imported data.
	 *
	 * @var array
	 */
	private $imported_data;
	/**
	 * Helpers.
	 *
	 * @var Helpers
	 */
	private $helpers;

	/**
	 * File name.
	 *
	 * @var string
	 */
	private $file;

	/**
	 * Constructor.
	 *
	 * @param string $version Version name.
	 * @param string $type    File type.
	 * @param string $file_path File path.
	 */
	public function __construct( $version = '', $type = '', $file_path = '' ) {
		if ( ! $version || ! $type ) {
			return;
		}

		$this->helpers = Helpers::get_instance();
		$this->version = $version;
		$this->type    = $type;
		$this->file    = $file_path ? $file_path : $this->helpers->get_file_path( $this->get_file_name(), $this->version );

		define( 'WP_IMPORTING', true ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound

		$this->import_xml();

		$this->imported_data = $this->helpers->get_imported_data( $version );

		$this->term_replace();
		$this->post_meta_replace();

		if ( 'elementor' === $this->helpers->get_page_builder() ) {
			$this->elementor_post_content_replace();
			$this->elementor_page_settings_replace();
		} elseif ( 'gutenberg' === $this->helpers->get_page_builder() ) {
			$this->gutenberg_post_content_replace();
		} else {
			$this->wpb_post_content_replace();
		}
	}

	/**
	 * Get file name.
	 */
	private function get_file_name() {
		$file_name = 'content';

		if ( 'elementor' === $this->helpers->get_page_builder() ) {
			$file_name .= '-elementor';
		} elseif ( 'gutenberg' === $this->helpers->get_page_builder() ) {
			$file_name .= '-gutenberg';
		}

		if ( 'posts' !== $this->type ) {
			$file_name .= '-' . $this->type;
		}

		$file_name .= '.xml';

		return $file_name;
	}

	/**
	 * Import XML.
	 */
	private function import_xml() {
		ob_start(); // Don`t remove this line, it`s needed for correct import process.

		if ( ! function_exists( 'woodmart_get_importer' ) ) {
			echo esc_html( '[ERROR] Importer not exists<br>' );

			return;
		}

		$importer = woodmart_get_importer();

		if ( ! $this->file ) {
			return;
		}

		try {
			add_filter( 'wp_revisions_to_keep', '__return_zero' );
			add_filter(
				'intermediate_image_sizes',
				function () {
					return array();
				}
			);

			$importer->fetch_attachments = true;

			$importer->import( $this->file, $this->version );
		} catch ( Exception $e ) {
			echo esc_html( '[ERROR] XML import<br>' );
		}

		ob_get_clean(); // Don`t remove this line, it`s needed for correct import process.
	}

	/**
	 * Term replace.
	 */
	private function term_replace() {
		$term_meta_key_list = array(
			'thumbnail_id',
			'category_icon_alt',
			'category_icon',
			'title_image',
			'image',
		);

		if ( ! empty( $this->imported_data['term'] ) ) {
			foreach ( $this->imported_data['term'] as $terms ) {
				foreach ( $terms as $term ) {
					if ( $this->is_replaced( $term['new'], 'term_meta' ) ) {
						continue;
					}

					foreach ( $term_meta_key_list as $meta_key ) {
						$flag      = false;
						$term_meta = get_term_meta( $term['new'], $meta_key, true );

						if ( ! $term_meta ) {
							continue;
						}

						if ( is_numeric( $term_meta ) ) {
							if ( ! isset( $this->imported_data['attachment'][ $term_meta ] ) ) {
								continue;
							}

							$flag      = true;
							$term_meta = $this->imported_data['attachment'][ $term_meta ]['new'];
						} elseif ( is_array( $term_meta ) && ! empty( $term_meta['id'] ) ) {
							if ( ! isset( $this->imported_data['attachment'][ $term_meta['id'] ] ) ) {
								continue;
							}

							$flag      = true;
							$term_meta = array(
								'url' => wp_get_attachment_image_url( $this->imported_data['attachment'][ $term_meta['id'] ]['new'], 'full' ),
								'id'  => $this->imported_data['attachment'][ $term_meta['id'] ]['new'],
							);
						}

						if ( $flag ) {
							$this->add_to_replaced( $term['new'], 'term_meta' );
							update_term_meta( $term['new'], $meta_key, $term_meta );
						}
					}
				}
			}
		}
	}

	/**
	 * Post meta replace.
	 */
	private function post_meta_replace() {
		$post_meta_key_list = array(
			'_woodmart_title_image',
			'_thumbnail_id',
			'bg_image_desktop',
			'bg_image_tablet',
			'bg_image_mobile',
			'_menu_item_block',
			'woodmart_sguide_select',
			'wd_layout_conditions',
			'wd_backgroundImage',
			'background',
			'background_tablet',
			'background_mobile',
			'background_image',
			'image',
			'woodmart_wc_video_gallery',
		);
		if ( ! empty( $this->imported_data['all_posts'] ) ) {
			foreach ( $this->imported_data['all_posts'] as $value ) {
				$data = (array) $this->imported_data['all_posts'];

				if ( isset( $this->imported_data['term'] ) ) {
					$data = (array) $this->imported_data['all_posts'] + (array) $this->imported_data['term']['product_cat'];
				}

				if ( $this->is_replaced( $value['new'], 'post_meta' ) ) {
					continue;
				}

				foreach ( $post_meta_key_list as $meta_key ) {
					$post_meta = get_post_meta( $value['new'], $meta_key, true );

					if ( ! $post_meta ) {
						continue;
					}

					$flag = false;

					if ( is_string( $post_meta ) && strpos( $post_meta, ',' ) ) {
						$output = array();
						$ids    = explode( ',', $post_meta );

						foreach ( $ids as $id ) {
							if ( ! isset( $data[ $id ] ) ) {
								continue;
							}

							$output[] = $data[ $id ]['new'];
						}

						$flag      = true;
						$post_meta = implode( ',', $output );
					} elseif ( is_numeric( $post_meta ) ) {
						if ( ! isset( $data[ $post_meta ] ) ) {
							continue;
						}

						$flag      = true;
						$post_meta = $data[ $post_meta ]['new'];
					} elseif ( is_array( $post_meta ) && ! empty( $post_meta['id'] ) ) {
						if ( ! isset( $this->imported_data['attachment'][ $post_meta['id'] ] ) ) {
							continue;
						}

						$flag             = true;
						$post_meta['id']  = $this->imported_data['attachment'][ $post_meta['id'] ]['new'];
						$post_meta['url'] = wp_get_attachment_image_url( $post_meta['id'], 'full' );
					} elseif ( 'wd_layout_conditions' === $meta_key && is_array( $post_meta ) ) {
						foreach ( $post_meta as $key => $condition ) {
							if ( ! isset( $this->imported_data['term']['product_cat'][ $condition['condition_query'] ] ) ) {
								continue;
							}

							$flag = true;

							$post_meta[ $key ]['condition_query'] = $this->imported_data['term']['product_cat'][ $condition['condition_query'] ]['new'];
						}
					} elseif ( 'woodmart_wc_video_gallery' === $meta_key && is_array( $post_meta ) ) {
						foreach ( $post_meta as $img_id => $img_data ) {
							if ( isset( $this->imported_data['attachment'][ $img_id ] ) && $this->imported_data['attachment'][ $img_id ]['new'] !== $this->imported_data['attachment'][ $img_id ]['old'] ) {
								$post_meta[ $this->imported_data['attachment'][ $img_id ]['new'] ] = $img_data;
								unset( $post_meta[ $img_id ] );
								$img_id = $this->imported_data['attachment'][ $img_id ]['new'];
							}

							if ( ! isset( $img_data['upload_video_id'] ) || ! isset( $this->imported_data['attachment'][ $img_data['upload_video_id'] ] ) ) {
								continue;
							}

							$flag = true;

							$post_meta[ $img_id ]['upload_video_id']  = $this->imported_data['attachment'][ $img_data['upload_video_id'] ]['new'];
							$post_meta[ $img_id ]['upload_video_url'] = wp_get_attachment_url( $img_data['upload_video_id'] );
						}
					}

					if ( $flag ) {
						$this->add_to_replaced( $value['new'], 'post_meta' );
						update_post_meta( $value['new'], $meta_key, $post_meta );
					}
				}

				if ( woodmart_is_import_demo_content() && 'product_loop_item' === get_post_meta( $value['new'], 'wd_layout_type', true ) ) {
					wp_update_post(
						array(
							'ID'            => $value['new'],
							'post_modified' => current_time( 'mysql' ),
						)
					);
				}
			}
		}
	}

	/**
	 * Update Elementor page settings URLs to local base.
	 *
	 * @since 1.0.0
	 */
	private function elementor_page_settings_replace() {
		if ( empty( $this->imported_data['all_posts'] ) ) {
			return;
		}

		$image_keys = array(
			'wd_fb_background_image',
			'wd_bg_image',
			'wd_image',
			'background_image',
			'wd__woodmart_title_image',
		);

		foreach ( $this->imported_data['all_posts'] as $imported_post ) {
			$post_id  = $imported_post['new'];
			$settings = get_post_meta( $post_id, '_elementor_page_settings', true );

			if ( ! $settings || ! is_array( $settings ) ) {
				continue;
			}

			if ( ! str_contains( maybe_serialize( $settings ), 'dummy.xtemos.com' ) ) {
				continue;
			}

			$flag = false;

			foreach ( $image_keys as $key ) {
				if ( ! empty( $settings[ $key ]['id'] ) && ! empty( $this->imported_data['attachment'][ $settings[ $key ]['id'] ] ) ) {
					$new_id = $this->imported_data['attachment'][ $settings[ $key ]['id'] ]['new'];

					$settings[ $key ]['id']  = $new_id;
					$settings[ $key ]['url'] = wp_get_attachment_image_url( $new_id, 'full' );
					$flag                    = true;
				}
			}

			if ( $flag ) {
				update_post_meta( $post_id, '_elementor_page_settings', $settings );
			}
		}
	}

	/**
	 * Post content replace.
	 */
	private function gutenberg_post_content_replace() {
		if ( ! empty( $this->imported_data['all_posts'] ) ) {
			foreach ( $this->imported_data['all_posts'] as $value ) {
				if ( isset( $value['type'] ) || $this->is_replaced( $value['new'], 'post_content' ) ) {
					continue;
				}

				$wd_post = get_post( $value['new'] );

				if ( ! $wd_post || ( is_object( $wd_post ) && ! property_exists( $wd_post, 'post_content' ) ) ) {
					continue;
				}

				$wd_post_content = $wd_post->post_content;

				// Terms replace.
				$terms_data = array();

				if ( isset( $this->imported_data['term'] ) ) {
					foreach ( $this->imported_data['term'] as $terms ) {
						foreach ( $terms as $key => $term ) {
							$terms_data[ $key ] = $term;
						}
					}
				}

				$wd_post_content = $this->gutenberg_post_content_replace_process(
					$terms_data,
					$wd_post_content,
					$value['new'],
					array(
						'/"ids":"([^"]*)"/i',
						'/"tagsIds":"([^"]*)"/i',
						'/"categoriesIds":"([^"]*)"/i',
						'/"nav_menu":"([^"]*)"/i',
					)
				);

				// Post and attachment replace.
				$wd_post_content = $this->gutenberg_post_content_replace_process(
					$this->imported_data['all_posts'],
					$wd_post_content,
					$value['new'],
					array(
						'/"id":([^"]*),/i',
						'/"form_id":"([^"]*)"/i',
						'/"sidebar_id":"([^"]*)"/i',
						'/wp-image-([^"]*)/i',
					)
				);

				$wd_post_content = str_replace( '{/{', '', $wd_post_content );
				$wd_post_content = str_replace( '}/}', '', $wd_post_content );

				$wd_post_content = preg_replace_callback(
					'/<!-- wp:wd\/countdown-timer\s+({.*?"date":"[^"]+.*?})\s+-->(.*?)<!-- \/wp:wd\/countdown-timer -->/s',
					function ( $matches ) {
						$block_data = json_decode( $matches[1], true );
						if ( isset( $block_data['date'] ) ) {
							$block_data['date'] = ( gmdate( 'Y' ) + 1 ) . '/01/01';
						}
						$updated_json = wp_json_encode( $block_data, JSON_UNESCAPED_SLASHES );

						$html_content         = $matches[2];
						$updated_html_content = preg_replace(
							'/data-end-date="[^"]+"/',
							'data-end-date="' . ( gmdate( 'Y' ) + 1 ) . '/01/01"',
							$html_content
						);

						return '<!-- wp:wd/countdown-timer ' . $updated_json . ' -->' . $updated_html_content . '<!-- /wp:wd/countdown-timer -->';
					},
					$wd_post_content
				);

				$wd_post_content = $this->replace_url_in_content( $wd_post_content );

				wp_update_post(
					array(
						'ID'           => $value['new'],
						'post_content' => wp_slash( $wd_post_content ),
					)
				);
			}
		}
	}

	/**
	 * Post content replace.
	 *
	 * @param array   $post_data       Data.
	 * @param string  $wd_post_content Content.
	 * @param integer $post_id         Post id.
	 * @param array   $attrs           Attributes to replace.
	 *
	 * @return array|mixed|string|string[]
	 */
	private function gutenberg_post_content_replace_process( $post_data, $wd_post_content, $post_id, $attrs ) {
		foreach ( $post_data as $data ) {
			foreach ( $attrs as $attr ) {
				$array = array();
				preg_match_all( $attr, $wd_post_content, $array );

				if ( empty( $array[1] ) ) {
					continue;
				}

				$found_values = $array[1];

				foreach ( $found_values as $found_value_key => $found_value ) {
					$flag           = false;
					$replaced_value = $found_value;
					$diff           = array();

					if ( strpos( $found_value, 'sidebar' ) ) {
						if ( 'sidebar-' . $data['old'] === $found_value ) {
							$flag           = true;
							$replaced_value = '{/{sidebar-' . $data['new'] . '}/}';

							// Diff.
							$diff[ $data['old'] ] = $data['new'];
						}
					} elseif ( strpos( $found_value, ',' ) ) {
						$ids = explode( ',', $found_value );

						foreach ( $ids as $key => $id ) {
							if ( (int) $data['old'] === (int) $id ) {
								$ids[ $key ] = '{/{' . $data['new'] . '}/}';
								$flag        = true;

								// Diff.
								$diff[ $data['old'] ] = $data['new'];
							}
						}

						$replaced_value = implode( ',', $ids );
					} elseif ( (int) $data['old'] === (int) $found_value ) {
						$flag           = true;
						$replaced_value = '{/{' . $data['new'] . '}/}';

						// Diff.
						$diff[ $data['old'] ] = $data['new'];
					}

					if ( $flag ) {
						$this->add_to_replaced( $post_id, 'post_content', $diff );
						// Fix unnecessary replace.
						$old_value       = $array[0][ $found_value_key ];
						$new_value       = str_replace( $found_value, $replaced_value, $old_value );
						$wd_post_content = str_replace( $old_value, $new_value, $wd_post_content );
					}
				}
			}
		}

		return $wd_post_content;
	}

	/**
	 * Post content replace.
	 */
	private function wpb_post_content_replace() {
		if ( ! empty( $this->imported_data['all_posts'] ) ) {
			foreach ( $this->imported_data['all_posts'] as $value ) {
				if ( isset( $value['type'] ) ) {
					continue;
				}

				$wd_post = get_post( $value['new'] );

				if ( ! $wd_post || ( is_object( $wd_post ) && ! property_exists( $wd_post, 'post_content' ) ) ) {
					continue;
				}

				$wd_post_content = $wd_post->post_content;

				// Terms replace.
				$terms_data = array();

				if ( isset( $this->imported_data['term'] ) ) {
					foreach ( $this->imported_data['term'] as $terms ) {
						foreach ( $terms as $key => $term ) {
							$terms_data[ $key ] = $term;
						}
					}
				}

				$wd_post_content = $this->wpb_post_content_replace_process(
					$terms_data,
					$wd_post_content,
					$value['new'],
					array(
						'/ids="([^"]*)"/i',
						'/taxonomies="([^"]*)"/i',
						'/categories="([^"]*)"/i',
					)
				);

				// Post and attachment replace.
				$wd_post_content = $this->wpb_post_content_replace_process(
					$this->imported_data['all_posts'],
					$wd_post_content,
					$value['new'],
					array(
						'/list="([^"]*)"/i',
						'/images="([^"]*)"/i',
						'/image="([^"]*)"/i',
						'/img_id="([^"]*)"/i',
						'/img="([^"]*)"/i',
						'/form_id="([^"]*)"/i',
						'/contact-form-7 id="([^"]*)"/i',
						'/html_block id="([^"]*)"/i',
						'/product_id="([^"]*)"/i',
						'/poster_image="([^"]*)"/i',
						'/include="([^"]*)"/i',
						'/sidebar_id="([^"]*)"/i',
						'/html_block_id="([^"]*)"/i',
						'/wp-image-([^"]*)/i',
						'/marquee_contents="([^"]*)"/i',
					)
				);

				$wd_post_content = str_replace( '{/{', '', $wd_post_content );
				$wd_post_content = str_replace( '}/}', '', $wd_post_content );

				$wd_post_content = preg_replace_callback(
					'/\[(woodmart_countdown_timer|promo_banner)([^\]]*?)date="([^"]+)"([^\]]*?)\]/',
					function ( $matches ) {
						return '[' . $matches[1] . $matches[2] . 'date="' . ( gmdate( 'Y' ) + 1 ) . '/01/01"' . $matches[4] . ']';
					},
					$wd_post_content
				);

				$wd_post_content = $this->replace_url_in_content( $wd_post_content );

				wp_update_post(
					array(
						'ID'           => $value['new'],
						'post_content' => $wd_post_content,
					)
				);
			}
		}
	}

	/**
	 * Post content replace.
	 *
	 * @param array   $post_data       Data.
	 * @param string  $wd_post_content Content.
	 * @param integer $post_id         Post id.
	 * @param array   $attrs           Attributes to replace.
	 *
	 * @return array|mixed|string|string[]
	 */
	private function wpb_post_content_replace_process( $post_data, $wd_post_content, $post_id, $attrs ) {
		foreach ( $post_data as $data ) {
			foreach ( $attrs as $attr ) {
				$array = array();
				preg_match_all( $attr, $wd_post_content, $array );

				if ( ! isset( $array[1] ) ) {
					continue;
				}

				$found_values = $array[1];

				foreach ( $found_values as $found_value_key => $found_value ) {
					$flag           = false;
					$replaced_value = $found_value;
					$diff           = array();

					if ( strpos( $found_value, 'list-content' ) || strpos( $attr, 'marquee_contents' ) ) {
						$data_decoded = json_decode( urldecode( $found_value ), true );
						foreach ( $data_decoded as $key => $list_data ) {
							if ( ! isset( $list_data['image_id'] ) ) {
								continue;
							}

							if ( (int) $data['old'] === (int) $list_data['image_id'] ) {
								$data_decoded[ $key ]['image_id'] = $data['new'];
								$flag                             = true;

								// Diff.
								$diff[ $data['old'] ] = $data['new'];
							}
						}

						$replaced_value = rawurlencode( wp_json_encode( $data_decoded ) );
					} elseif ( strpos( $found_value, 'sidebar' ) ) {
						if ( 'sidebar-' . $data['old'] === $found_value ) {
							$flag           = true;
							$replaced_value = '{/{sidebar-' . $data['new'] . '}/}';

							// Diff.
							$diff[ $data['old'] ] = $data['new'];
						}
					} elseif ( strpos( $found_value, ',' ) ) {
						$ids = explode( ',', $found_value );

						foreach ( $ids as $key => $id ) {
							if ( (int) $data['old'] === (int) $id ) {
								$ids[ $key ] = '{/{' . $data['new'] . '}/}';
								$flag        = true;

								// Diff.
								$diff[ $data['old'] ] = $data['new'];
							}
						}

						$replaced_value = implode( ',', $ids );
					} elseif ( (int) $data['old'] === (int) $found_value ) {
						$flag           = true;
						$replaced_value = '{/{' . $data['new'] . '}/}';

						// Diff.
						$diff[ $data['old'] ] = $data['new'];
					}

					if ( $flag ) {
						$this->add_to_replaced( $post_id, 'post_content', $diff );
						// Fix unnecessary replace.
						$old_value       = $array[0][ $found_value_key ];
						$new_value       = str_replace( $found_value, $replaced_value, $old_value );
						$wd_post_content = str_replace( $old_value, $new_value, $wd_post_content );
					}
				}
			}
		}

		return $wd_post_content;
	}

	/**
	 * Elementor content replace.
	 */
	private function elementor_post_content_replace() {
		if ( ! empty( $this->imported_data['all_posts'] ) ) {
			foreach ( $this->imported_data['all_posts'] as $value ) {
				if ( ! isset( $value['type'] ) || ( isset( $value['type'] ) && 'elementor' !== $value['type'] ) ) {
					continue;
				}

				$raw_post_meta = get_post_meta( $value['new'], '_elementor_data', true );

				if ( ! $raw_post_meta ) {
					continue;
				}

				if ( is_array( $raw_post_meta ) ) {
					$raw_post_meta = current( $raw_post_meta );
				} elseif ( strpos( $raw_post_meta, '{\"' ) ) {
					$raw_post_meta = wp_unslash( $raw_post_meta );
				}

				$post_meta = json_decode( $raw_post_meta, true );

				if ( ! $post_meta ) {
					continue;
				}

				$imported_data = $this->imported_data;

				$post_meta = Plugin::$instance->db->iterate_data(
					$post_meta,
					function ( $element_data ) use ( $imported_data ) {
						$element = Plugin::$instance->elements_manager->create_element_instance( $element_data );

						if ( is_null( $element ) ) {
							return $element_data;
						}

						$terms_data = array();

						if ( isset( $imported_data['term'] ) ) {
							foreach ( $imported_data['term'] as $terms ) {
								foreach ( $terms as $key => $term ) {
									$terms_data[ $key ] = $term;
								}
							}
						}

						$posts_data = $imported_data['all_posts'];

						foreach ( $posts_data as $data ) {
							foreach ( $element->get_controls() as $control ) {
								if ( ! isset( $element_data['settings'][ $control['name'] ] ) ) {
									continue;
								}

								$settings = $element_data['settings'][ $control['name'] ];

								$repeater_id_fields = array(
									'content_html_block',
									'html_block_id',
									'include',
									'exclude',
									'product_id',
									'include_products',
									'marquee_contents',
								);

								if ( 'repeater' === $control['type'] ) {
									foreach ( $repeater_id_fields as $field ) {
										foreach ( $settings as $key => $value ) {
											if ( empty( $value ) ) {
												continue;
											}

											if ( isset( $value[ $field ] ) && (int) $value[ $field ] === (int) $data['old'] ) {
												$settings[ $key ][ $field ] = strval( '{/{' . $data['new'] . '}/}' );
											} elseif ( ! empty( $value['image']['id'] ) && (int) $value['image']['id'] === (int) $data['old'] ) {
												$settings[ $key ]['image']['id']  = '{/{' . $data['new'] . '}/}';
												$settings[ $key ]['image']['url'] = wp_get_attachment_image_url( $data['new'], 'full' );
											}
										}
									}
								}

								$select_id_fields = array(
									'content',
									'form_id',
								);
								if ( ( 'select' === $control['type'] || 'select2' === $control['type'] ) && in_array( $control['name'], $select_id_fields, true ) ) {
									if ( empty( $settings ) ) {
										continue;
									}

									if ( (int) $settings === (int) $data['old'] ) {
										$settings = strval( '{/{' . $data['new'] . '}/}' );
									}
								}

								$autocomplete_id_fields = array(
									'include',
									'exclude',
									'product_id',
									'include_products',
								);
								if ( 'wd_autocomplete' === $control['type'] && in_array( $control['name'], $autocomplete_id_fields, true ) ) {
									foreach ( $settings as $key => $value ) {
										if ( empty( $value ) ) {
											continue;
										}

										if ( (int) $value === (int) $data['old'] ) {
											$settings[ $key ] = strval( '{/{' . $data['new'] . '}/}' );
										}
									}
								}

								if ( 'media' === $control['type'] ) {
									if ( empty( $settings['url'] ) ) {
										continue;
									}

									if ( (int) $settings['id'] === (int) $data['old'] ) {
										$settings['url'] = wp_get_attachment_url( $data['new'] );
										$settings['id']  = '{/{' . $data['new'] . '}/}';
									}
								}

								if ( 'gallery' === $control['type'] ) {
									foreach ( $settings as $key => $value ) {
										if ( empty( $value['url'] ) ) {
											continue;
										}

										if ( (int) $value['id'] === (int) $data['old'] ) {
											$settings[ $key ]['url'] = wp_get_attachment_image_url( $data['new'], 'full' );
											$settings[ $key ]['id']  = '{/{' . $data['new'] . '}/}';
										}
									}
								}

								if ( 'date_time' === $control['type'] && 'date' === $control['name'] && $settings ) {
									$settings = ( gmdate( 'Y' ) + 1 ) . '/01/01';
								}

								$element_data['settings'][ $control['name'] ] = $settings;
							}
						}

						foreach ( $terms_data as $data ) {
							foreach ( $element->get_controls() as $control ) {
								if ( ! isset( $element_data['settings'][ $control['name'] ] ) ) {
									continue;
								}

								$settings = $element_data['settings'][ $control['name'] ];

								$repeater_id_fields = array(
									'taxonomies',
									'ids',
									'categories',
								);
								if ( 'repeater' === $control['type'] ) {
									foreach ( $repeater_id_fields as $field ) {
										foreach ( $settings as $key => $value ) {
											if ( empty( $value ) || ! isset( $value[ $field ] ) ) {
												continue;
											}

											if ( (int) $value[ $field ] === (int) $data['old'] ) {
												$settings[ $key ][ $field ] = strval( '{/{' . $data['new'] . '}/}' );
											}
										}
									}
								}

								$select_id_fields = array(
									'nav_menu',
								);
								if ( ( 'select' === $control['type'] || 'select2' === $control['type'] ) && in_array( $control['name'], $select_id_fields, true ) ) {
									if ( empty( $settings ) ) {
										continue;
									}

									if ( (int) $settings === (int) $data['old'] ) {
										$settings = strval( '{/{' . $data['new'] . '}/}' );
									}
								}

								$autocomplete_id_fields = array(
									'taxonomies',
									'ids',
									'categories',
								);
								if ( 'wd_autocomplete' === $control['type'] && in_array( $control['name'], $autocomplete_id_fields, true ) ) {
									foreach ( $settings as $key => $value ) {
										if ( empty( $value ) ) {
											continue;
										}

										if ( (int) $value === (int) $data['old'] ) {
											$settings[ $key ] = strval( '{/{' . $data['new'] . '}/}' );
										}
									}
								}

								$element_data['settings'][ $control['name'] ] = $settings;
							}
						}

						return $element_data;
					}
				);

				$post_meta = wp_json_encode( $post_meta );

				$post_meta = str_replace( '{\/{', '', $post_meta );
				$post_meta = str_replace( '}\/}', '', $post_meta );

				$this->add_to_replaced( $value['new'], 'post_content' );

				update_post_meta( $value['new'], '_elementor_data', wp_slash( $post_meta ) );
			}
		}
	}

	/**
	 * Replace URL in content.
	 *
	 * @param string $content Content to replace URLs in.
	 * @return string
	 */
	private function replace_url_in_content( $content ) {
		if ( str_contains( $content, 'dummy.xtemos.com' ) ) {
			$content = $this->helpers->links_replace( $content, '/', $this->version );
		}

		return $content;
	}

	/**
	 * Add to replace.
	 *
	 * @param int        $id   Post id.
	 * @param string     $type Data type.
	 * @param bool|array $diff Diff data.
	 */
	private function add_to_replaced( $id, $type, $diff = false ) {
		$data = get_option( 'wd_import_replaced_items', array() );

		$data[ $id ][ $type ] = $type;

		if ( $diff ) {
			$data[ $id ]['diff'][] = $diff;
		}

		update_option( 'wd_import_replaced_items', $data, false );
	}

	/**
	 * Is replaced.
	 *
	 * @param int    $id   Post id.
	 * @param string $type Data type.
	 *
	 * @return bool
	 */
	private function is_replaced( $id, $type ) {
		$data = get_option( 'wd_import_replaced_items', array() );

		return isset( $data[ $id ] ) && in_array( $type, $data[ $id ], true );
	}
}
