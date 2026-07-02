<?php
/**
 * WPML integration.
 *
 * @package woodmart
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

if ( ! function_exists( 'woodmart_wpml_output_js_data' ) ) {
	/**
	 * Output WPML languages data for JavaScript.
	 *
	 * @return void
	 */
	function woodmart_wpml_output_js_data() {
		if ( ! woodmart_get_opt( 'ajax_shop' ) || ! defined( 'WCML_VERSION' ) || ! defined( 'ICL_SITEPRESS_VERSION' ) ) {
			return;
		}

		$data = array(
			'languages' => apply_filters( 'wpml_active_languages', null ),
		);

		echo '<script>';
		echo 'var woodmart_wpml_js_data = ' . wp_json_encode( $data );
		echo '</script>';
	}

	add_action( 'woodmart_page_bottom_part', 'woodmart_wpml_output_js_data' );
}

if ( ! function_exists( 'woodmart_wpml_add_ajax_actions' ) ) {
	/**
	 * Add Woodmart AJAX actions to WCML multi-currency AJAX actions list.
	 *
	 * @param array $ajax_actions List of AJAX actions.
	 *
	 * @return array
	 */
	function woodmart_wpml_add_ajax_actions( $ajax_actions ) {
		$ajax_actions[] = 'woodmart_ajax_add_to_cart';
		$ajax_actions[] = 'woodmart_quick_view';
		$ajax_actions[] = 'woodmart_ajax_search';
		$ajax_actions[] = 'woodmart_get_products_shortcode';
		$ajax_actions[] = 'woodmart_get_products_tab_shortcode';
		$ajax_actions[] = 'woodmart_update_cart_item';
		$ajax_actions[] = 'woodmart_load_html_dropdowns';
		$ajax_actions[] = 'woodmart_quick_shop';

		return $ajax_actions;
	}

	add_filter( 'wcml_multi_currency_ajax_actions', 'woodmart_wpml_add_ajax_actions', 10, 1 );
}

if ( ! function_exists( 'woodmart_wpml_sync_variation_gallery_data' ) ) {
	/**
	 * Synchronize variation gallery data for WPML translated products.
	 *
	 * @param int    $post_id_from Source post ID.
	 * @param int    $post_id_to   Target post ID.
	 * @param string $meta_key     Meta key.
	 *
	 * @return void
	 */
	function woodmart_wpml_sync_variation_gallery_data( $post_id_from, $post_id_to, $meta_key ) {
		if ( 'woodmart_variation_gallery_data' !== $meta_key ) {
			return;
		}

		$translated_lang  = apply_filters( 'wpml_post_language_details', '', $post_id_to );
		$translated_lang  = isset( $translated_lang['language_code'] ) ? $translated_lang['language_code'] : '';
		$original_value   = get_post_meta( $post_id_from, 'woodmart_variation_gallery_data', true );
		$translated_value = $original_value;

		if ( ! empty( $original_value ) && is_array( $original_value ) ) {
			foreach ( $original_value as $key => $value ) {
				$translated_variation_id = apply_filters( 'wpml_object_id', $key, 'product_variation', false, $translated_lang );

				$translated_value[ $translated_variation_id ] = $value;
				unset( $translated_value[ $key ] );
			}

			update_post_meta( $post_id_to, 'woodmart_variation_gallery_data', $translated_value );
		}
	}

	add_action( 'wpml_after_copy_custom_field', 'woodmart_wpml_sync_variation_gallery_data', 10, 3 );
}

if ( ! function_exists( 'woodmart_wpml_register_header_builder_strings' ) ) {
	/**
	 * Register Header Builder strings for WPML translation.
	 *
	 * @param string $file Configuration file path.
	 *
	 * @return void
	 */
	function woodmart_wpml_register_header_builder_strings( $file ) {
		global $wpdb;

		if ( ! is_string( $file ) || 'woodmart' !== basename( dirname( $file ) ) || ! class_exists( 'WPML_Admin_Text_Configuration' ) ) {
			return;
		}

		$file       .= ':whb';
		$admin_texts = array();
		$headers     = get_option( 'whb_saved_headers', array() );

		foreach ( $headers as $key => $header ) {
			$admin_texts[] = array(
				'value' => '',
				'attr'  => array( 'name' => 'whb_' . $key ),
				'key'   => array(
					array(
						'value' => '',
						'attr'  => array( 'name' => 'structure' ),
						'key'   => array(
							array(
								'value' => '',
								'attr'  => array( 'name' => 'content' ),
								'key'   => array(
									array(
										'value' => '',
										'attr'  => array( 'name' => '*' ),
										'key'   => array(
											array(
												'value' => '',
												'attr'  => array( 'name' => 'content' ),
												'key'   => array(
													array(
														'value' => '',
														'attr' => array( 'name' => '*' ),
														'key' => array(
															array(
																'value' => '',
																'attr' => array( 'name' => 'content' ),
																'key' => array(
																	array(
																		'value' => '',
																		'attr' => array( 'name' => '*' ),
																		'key' => array(
																			array(
																				'value' => '',
																				'attr' => array( 'name' => 'params' ),
																				'key' => array(
																					array(
																						'value' => '',
																						'attr' => array( 'name' => 'content' ),
																						'key' => array(
																							array(
																								'value' => '',
																								'attr' => array( 'name' => 'value' ),
																								'key' => array(),
																							),
																						),
																					),
																					array(
																						'value' => '',
																						'attr' => array( 'name' => 'title' ),
																						'key' => array(
																							array(
																								'value' => '',
																								'attr' => array( 'name' => 'value' ),
																								'key' => array(),
																							),
																						),
																					),
																					array(
																						'value' => '',
																						'attr' => array( 'name' => 'subtitle' ),
																						'key' => array(
																							array(
																								'value' => '',
																								'attr' => array( 'name' => 'value' ),
																								'key' => array(),
																							),
																						),
																					),
																					array(
																						'value' => '',
																						'attr' => array( 'name' => 'btn_text' ),
																						'key' => array(
																							array(
																								'value' => '',
																								'attr' => array( 'name' => 'value' ),
																								'key' => array(),
																							),
																						),
																					),
																					array(
																						'value' => '',
																						'attr' => array( 'name' => 'image' ),
																						'key' => array(
																							array(
																								'value' => '',
																								'attr' => array( 'name' => 'value' ),
																								'key' => array(),
																							),
																						),
																					),
																					array(
																						'value' => '',
																						'attr' => array( 'name' => 'link' ),
																						'key' => array(
																							array(
																								'value' => '',
																								'attr' => array( 'name' => 'value' ),
																								'key' => array(),
																							),
																						),
																					),
																					array(
																						'value' => '',
																						'attr' => array( 'name' => 'categories_title' ),
																						'key' => array(
																							array(
																								'value' => '',
																								'attr' => array( 'name' => 'value' ),
																								'key' => array(),
																							),
																						),
																					),
																					array(
																						'value' => '',
																						'attr' => array( 'name' => 'primary_menu_title' ),
																						'key' => array(
																							array(
																								'value' => '',
																								'attr' => array( 'name' => 'value' ),
																								'key' => array(),
																							),
																						),
																					),
																					array(
																						'value' => '',
																						'attr' => array( 'name' => 'secondary_menu_title' ),
																						'key' => array(
																							array(
																								'value' => '',
																								'attr' => array( 'name' => 'value' ),
																								'key' => array(),
																							),
																						),
																					),
																				),
																			),
																		),
																	),
																),
															),
														),
													),
												),
											),
										),
									),
								),
							),
						),
					),
				),
			);
		}

		$object = (object) array(
			'config'             => array(
				'wpml-config' => array(
					'admin-texts' => array(
						'value' => '',
						'key'   => $admin_texts,
					),
				),
			),
			'type'               => 'theme',
			'admin_text_context' => 'woodmart-header-builder',
		);

		$config       = new WPML_Admin_Text_Configuration( $object );
		$config_array = $config->get_config_array();

		if ( ! empty( $config_array ) ) {
			$st_records          = new WPML_ST_Records( $wpdb );
			$import              = new WPML_Admin_Text_Import( $st_records, new WPML_WP_API() );
			$config_handler_hash = md5( serialize( 'whb' ) ); // phpcs:ignore.
			$import->parse_config( $config_array, $config_handler_hash );
		}
	}

	add_filter( 'wpml_parse_config_file', 'woodmart_wpml_register_header_builder_strings' );
}

if ( ! function_exists( 'woodmart_wpml_add_language_switcher_to_mobile_menu' ) ) {
	/**
	 * Add WPML language switcher to mobile menu.
	 *
	 * @param string  $items     Menu items HTML.
	 * @param array   $args      Menu arguments.
	 * @param boolean $is_return Whether to return or echo.
	 *
	 * @return string
	 */
	function woodmart_wpml_add_language_switcher_to_mobile_menu( $items = '', $args = array(), $is_return = false ) {
		$is_mobile_menu = ! empty( $args ) && 'mobile-menu' === $args->theme_location;

		if ( ! $is_mobile_menu && ! $is_return ) {
			return $items;
		}

		$settings = whb_get_settings();

		if ( empty( $settings['burger']['languages'] ) ) {
			return $items;
		}

		$languages    = apply_filters( 'wpml_active_languages', array() );
		$current_lang = esc_html__( 'Languages', 'woodmart' );
		$flag_url     = '';
		$current_url  = '';
		$current_code = '';

		if ( $languages ) {
			foreach ( $languages as $key => $language ) {
				if ( $language['active'] ) {
					$flag_url     = $language['country_flag_url'];
					$current_lang = $language['native_name'];
					$current_url  = $language['url'];
					$current_code = $language['language_code'];

					unset( $languages[ $key ] );
				}
			}
		}

		ob_start();
		?>
		<li class="menu-item menu-item-languages <?php echo esc_attr( $languages || ! $flag_url ? ' menu-item-has-children' : '' ); ?> item-level-0 wpml-ls-item-<?php echo esc_attr( $current_code ); ?>">
			<a href="<?php echo esc_url( $current_url ); ?>" class="woodmart-nav-link wpml-ls-link">
				<?php if ( $flag_url && $settings['burger']['show_language_flag'] ) : ?>
					<?php // translators: Current language. ?>
					<img src="<?php echo esc_url( $flag_url ); ?>" alt="<?php echo esc_attr( sprintf( __( 'Flag for %s', 'woodmart' ), $current_lang ) ); ?>" class="wd-nav-img">
				<?php endif; ?>
				<span class="nav-link-text">
				<?php echo esc_html( $current_lang ); ?>
			</span>
			</a>
			<ul class="wd-sub-menu">
				<?php echo $args->walker->get_drilldown_back_button( 'li' ); // phpcs:ignore. ?>

				<?php if ( $languages ) : ?>
					<?php foreach ( $languages as $language ) : ?>
						<li class="menu-item wpml-ls-item-<?php echo esc_attr( $language['language_code'] ); ?>">
							<a href="<?php echo esc_url( $language['url'] ); ?>" hreflang="<?php echo esc_attr( $language['language_code'] ); ?>" class="woodmart-nav-link wpml-ls-link">
								<?php if ( $language['country_flag_url'] && $settings['burger']['show_language_flag'] ) : ?>
									<?php // translators: Current language. ?>
									<img src="<?php echo esc_url( $language['country_flag_url'] ); ?>" alt="<?php echo esc_attr( sprintf( __( 'Flag for %s', 'woodmart' ), $current_lang ) ); ?>" class="wd-nav-img">
								<?php endif; ?>
								<span class="nav-link-text">
								<?php echo esc_html( $language['native_name'] ); ?>
							</span>
							</a>
						</li>
					<?php endforeach; ?>
				<?php elseif ( ! $flag_url ) : ?>
					<li>
						<a href="#">
							<?php esc_html_e( 'You need WPML plugin for this to work. You can remove it from Header builder.', 'woodmart' ); ?>
						</a>
					</li>
				<?php endif; ?>
			</ul>
		</li>
		<?php

		$items .= ob_get_clean();

		return $items;
	}

	add_filter( 'wp_nav_menu_items', 'woodmart_wpml_add_language_switcher_to_mobile_menu', 40, 2 );
}

if ( ! function_exists( 'woodmart_wpml_translate_product_video_attachment' ) ) {
	/**
	 * Translate product video attachment ID to default language.
	 *
	 * @param int $attachment_id Attachment ID.
	 *
	 * @return int
	 */
	function woodmart_wpml_translate_product_video_attachment( $attachment_id ) {
		return apply_filters( 'wpml_object_id', $attachment_id, 'attachment', true, apply_filters( 'wpml_default_language', null ) );
	}

	add_filter( 'woodmart_single_product_image_thumbnail_id', 'woodmart_wpml_translate_product_video_attachment', 10 );
}

if ( class_exists( 'woocommerce_wpml' ) && ! function_exists( 'woodmart_wpml_convert_shipping_progress_bar_amount' ) ) {
	/**
	 * Convert shipping progress bar amount for WPML multi-currency.
	 *
	 * @param float $limit Shipping limit amount.
	 *
	 * @return float
	 */
	function woodmart_wpml_convert_shipping_progress_bar_amount( $limit ) {
		global $woocommerce_wpml;

		if ( 'wc' === woodmart_get_opt( 'shipping_progress_bar_calculation', 'custom' ) || ! $woocommerce_wpml || ! method_exists( $woocommerce_wpml, 'get_multi_currency' ) ) {
			return $limit;
		}

		$multi_currency = $woocommerce_wpml->get_multi_currency();

		if ( ! empty( $multi_currency->prices ) && method_exists( $multi_currency->prices, 'convert_price_amount' ) ) {
			$limit = $multi_currency->prices->convert_price_amount( $limit );
		}

		return $limit;
	}

	add_filter( 'woodmart_shipping_progress_bar_amount', 'woodmart_wpml_convert_shipping_progress_bar_amount' );
	add_filter( 'woodmart_pricing_amount_discounts_value', 'woodmart_wpml_convert_shipping_progress_bar_amount' );
	add_filter( 'woodmart_product_pricing_amount_discounts_value', 'woodmart_wpml_convert_shipping_progress_bar_amount' );
}

if ( ! function_exists( 'woodmart_wpml_register_email_options_for_translation' ) ) {
	/**
	 * Register Woodmart email options for WooCommerce Multilingual translation.
	 *
	 * @param array $email_options List of email option names.
	 *
	 * @return array
	 */
	function woodmart_wpml_register_email_options_for_translation( $email_options ) {
		// Wishlist.
		$email_options[] = 'woocommerce_woodmart_wishlist_back_in_stock_settings';
		$email_options[] = 'woocommerce_woodmart_wishlist_on_sale_products_settings';
		$email_options[] = 'woocommerce_woodmart_promotional_email_settings';

		// Waitlist.
		$email_options[] = 'woocommerce_woodmart_waitlist_confirm_subscription_email_settings';
		$email_options[] = 'woocommerce_woodmart_waitlist_in_stock_settings';
		$email_options[] = 'woocommerce_woodmart_waitlist_subscribe_email_settings';

		// Abandoned cart.
		$email_options[] = 'woocommerce_woodmart_abandoned_cart_email_settings';

		return $email_options;
	}

	add_filter( 'wcml_emails_options_to_translate', 'woodmart_wpml_register_email_options_for_translation' );
}

if ( ! function_exists( 'woodmart_wpml_remove_email_section_prefix' ) ) {
	/**
	 * Remove WCML email section name prefix for Woodmart custom emails.
	 *
	 * @param string $prefix       Email section name prefix.
	 * @param string $email_option Email option name.
	 *
	 * @return string
	 */
	function woodmart_wpml_remove_email_section_prefix( $prefix, $email_option ) {
		$email_options = array(
			// Wishlist.
			'woocommerce_woodmart_wishlist_back_in_stock_settings',
			'woocommerce_woodmart_wishlist_on_sale_products_settings',
			'woocommerce_woodmart_promotional_email_settings',
			// Waitlist.
			'woocommerce_woodmart_waitlist_confirm_subscription_email_settings',
			'woocommerce_woodmart_waitlist_in_stock_settings',
			'woocommerce_woodmart_waitlist_subscribe_email_settings',
			// Abandoned cart.
			'woocommerce_woodmart_abandoned_cart_email_settings',
		);

		if ( in_array( $email_option, $email_options, true ) ) {
			return '';
		}

		return $prefix;
	}

	add_filter( 'wcml_emails_section_name_prefix', 'woodmart_wpml_remove_email_section_prefix', 10, 2 );
}

if ( ! function_exists( 'woodmart_wpml_map_email_section_names' ) ) {
	/**
	 * Map WCML email section names for Woodmart custom emails.
	 *
	 * @param string $section_name Email section name.
	 *
	 * @return string
	 */
	function woodmart_wpml_map_email_section_names( $section_name ) {
		$email_section_map = array(
			// Wishlist.
			'woodmart_wishlist_back_in_stock'              => 'xts_email_wishlist_back_in_stock',
			'woodmart_wishlist_on_sale_products'           => 'xts_email_wishlist_on_sale_products',
			'woodmart_promotional_email'                   => 'xts_email_wishlist_promotional',
			// Waitlist.
			'woodmart_waitlist_confirm_subscription_email' => 'xts_email_waitlist_confirm_subscription',
			'woodmart_waitlist_subscribe_email'            => 'xts_email_waitlist_subscribe',
			'woodmart_waitlist_in_stock'                   => 'xts_email_waitlist_back_in_stock',
			// Abandoned cart.
			'woodmart_abandoned_cart_email'                => 'xts_email_abandoned_cart',
		);

		if ( isset( $email_section_map[ $section_name ] ) ) {
			return $email_section_map[ $section_name ];
		}

		return $section_name;
	}

	add_filter( 'wcml_emails_section_name_to_translate', 'woodmart_wpml_map_email_section_names' );
}

if ( ! function_exists( 'woodmart_wpml_set_global_product_for_ajax' ) ) {
	/**
	 * Set global product for WPML AJAX requests.
	 *
	 * @return void
	 */
	function woodmart_wpml_set_global_product_for_ajax() {
		global $product;

		$current_url = $_SERVER['REQUEST_URI']; // phpcs:ignore.

		if ( ! wp_is_json_request() || $product || ! str_contains( $current_url, '/wpml/' ) ) {
			return;
		}

		$product_id     = false;
		$random_product = new WP_Query(
			array(
				'posts_per_page' => '1',
				'post_type'      => 'product',
			)
		);

		while ( $random_product->have_posts() ) {
			$random_product->the_post();
			$product_id = get_the_ID();
		}

		wp_reset_postdata();

		$product = wc_get_product( $product_id );
	}

	add_action( 'woocommerce_before_template_part', 'woodmart_wpml_set_global_product_for_ajax' );
}

if ( ! function_exists( 'woodmart_wpml_enable_ajax_language_switcher' ) ) {
	/**
	 * Enable WPML AJAX language switcher on shop/archive pages when Woodmart AJAX shop is enabled.
	 *
	 * @param bool $enabled Whether AJAX navigation is enabled.
	 *
	 * @return bool
	 */
	function woodmart_wpml_enable_ajax_language_switcher( $enabled ) {
		if ( woodmart_get_opt( 'ajax_shop' ) && woodmart_is_shop_archive() ) {
			return true;
		}

		return $enabled;
	}

	add_filter( 'wpml_ls_enable_ajax_navigation', 'woodmart_wpml_enable_ajax_language_switcher', 10, 1 );
}

if ( ! function_exists( 'woodmart_wpml_pb_shortcode_encode_urlencoded_json' ) ) {
	/**
	 * Encode urlencoded json.
	 *
	 * @param string $str String.
	 * @param string $encoding Format.
	 * @param array  $original_string Original string.
	 * @return string
	 */
	function woodmart_wpml_pb_shortcode_encode_urlencoded_json( $str, $encoding, $original_string ) {
		if ( 'urlencoded_json' === $encoding ) {
			$output = array();

			foreach ( $original_string as $combined_key => $value ) {
				$parts                = explode( '_', $combined_key );
				$i                    = array_pop( $parts );
				$key                  = implode( '_', $parts );
				$output[ $i ][ $key ] = $value;
			}

			$str = rawurlencode( wp_json_encode( $output ) );
		}
		return $str;
	}

	add_filter( 'wpml_pb_shortcode_encode', 'woodmart_wpml_pb_shortcode_encode_urlencoded_json', 10, 3 );
}

if ( ! function_exists( 'woodmart_wpml_pb_shortcode_decode_urlencoded_json' ) ) {
	/**
	 * Decode urlencoded json.
	 *
	 * @param string $str String.
	 * @param string $encoding Format.
	 * @param string $original_string Original string.
	 * @return string
	 */
	function woodmart_wpml_pb_shortcode_decode_urlencoded_json( $str, $encoding, $original_string ) {
		if ( 'urlencoded_json' === $encoding ) {
			$rows = json_decode( urldecode( $original_string ), true );

			$str = array();

			foreach ( $rows as $i => $row ) {
				foreach ( $row as $key => $value ) {
					if ( in_array( $key, array( 'list', 'list-content', 'text' ), true ) ) {
						$str[ $key . '_' . $i ] = array(
							'value'     => $value,
							'translate' => true,
						);
					} else {
						$str[ $key . '_' . $i ] = array(
							'value'     => $value,
							'translate' => false,
						);
					}
				}
			}
		}

		return $str;
	}

	add_filter( 'wpml_pb_shortcode_decode', 'woodmart_wpml_pb_shortcode_decode_urlencoded_json', 10, 3 );
}

