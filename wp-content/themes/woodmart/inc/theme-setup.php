<?php
/**
 * Woodmart Theme setup file
 *
 * @package woodmart
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

add_option( 'woodmart_is_activated' );
add_option( 'xts-theme_settings_default-file-data' );
add_option( 'xts-options-presets' );
add_option( 'xts-woodmart-options' );
add_option( 'woodmart_setup_status' );
add_option( 'wd_import_theme_version' );

if ( ! function_exists( 'woodmart_gallery_shortcode_add_scripts_styles' ) ) {
	/**
	 * Add scripts and styles to default gallery shortcode.
	 *
	 * @param string $output Gallery styles.
	 *
	 * @return string
	 */
	function woodmart_gallery_shortcode_add_scripts_styles( $output ) {
		woodmart_enqueue_js_library( 'magnific' );
		woodmart_enqueue_js_script( 'mfp-popup' );

		ob_start();
		woodmart_enqueue_inline_style( 'mfp-popup' );
		woodmart_enqueue_inline_style( 'mod-animations-transform' );
		woodmart_enqueue_inline_style( 'mod-transform' );
		$style = ob_get_clean();

		return $style . $output;
	}

	add_filter( 'gallery_style', 'woodmart_gallery_shortcode_add_scripts_styles' );
}
/**
 * ------------------------------------------------------------------------------------------------
 * Set up theme default and register various supported features
 * ------------------------------------------------------------------------------------------------
 */
if ( ! function_exists( 'woodmart_theme_setup' ) ) {
	/**
	 * Set up theme defaults and register various supported features.
	 */
	function woodmart_theme_setup() {
		/**
		 * Add support for post formats
		 */
		add_theme_support(
			'post-formats',
			array(
				'video',
				'audio',
				'quote',
				'image',
				'gallery',
				'link',
			)
		);

		/**
		 * Add support for automatic feed links
		 */
		add_theme_support( 'automatic-feed-links' );

		/**
		 * Add support for post thumbnails
		 */
		add_theme_support( 'post-thumbnails' );

		/**
		 * Add support for post title tag
		 */
		add_theme_support( 'title-tag' );

		add_theme_support(
			'html5',
			array(
				'comment-form',
			)
		);

		/**
		 * Register nav menus
		 */
		register_nav_menus(
			array(
				'main-menu'   => esc_html__( 'Main Menu', 'woodmart' ),
				'mobile-menu' => esc_html__( 'Mobile Side Menu', 'woodmart' ),
			)
		);

		add_theme_support( 'editor-styles' );
		add_editor_style( '/css/editor-style.css' );
		add_theme_support( 'align-wide' );

		if ( woodmart_get_opt( 'load_text_domain' ) ) {
			/**
			 * Make the theme available for translations.
			 */
			$lang_dir = WOODMART_THEMEROOT . '/languages';
			load_theme_textdomain( 'woodmart', $lang_dir );
		}
	}

	add_action( 'after_setup_theme', 'woodmart_theme_setup' );
}


/**
 * ------------------------------------------------------------------------------------------------
 * Disable emoji styles
 * ------------------------------------------------------------------------------------------------
 */

remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
/**
 * ------------------------------------------------------------------------------------------------
 * Allow SVG logo
 * ------------------------------------------------------------------------------------------------
 */

if ( ! function_exists( 'woodmart_upload_mimes' ) ) {
	add_filter( 'upload_mimes', 'woodmart_upload_mimes', 100, 1 );
	/**
	 * Allow svg upload
	 *
	 * @param array $mimes Current mime types.
	 *
	 * @return array
	 */
	function woodmart_upload_mimes( $mimes ) {
		if ( woodmart_get_opt( 'allow_upload_svg' ) ) {
			$mimes['svg']  = 'image/svg+xml';
			$mimes['svgz'] = 'image/svg+xml';
		}
		$mimes['woff']  = 'font/woff';
		$mimes['woff2'] = 'font/woff2';
		$mimes['ttf']   = 'font/ttf';
		$mimes['eot']   = 'font/eot';
		return $mimes;
	}
}


/**
 * ------------------------------------------------------------------------------------------------
 * Register the widget areas
 * ------------------------------------------------------------------------------------------------
 */
if ( ! function_exists( 'woodmart_widget_init' ) ) {
	/**
	 * Register widget areas
	 */
	function woodmart_widget_init() {
		if ( function_exists( 'register_sidebar' ) ) {
			$widget_class = ( woodmart_get_opt( 'widget_toggle' ) ) ? ' widget-hidable' : '';

			$before_title = '<' . woodmart_get_widget_title_tag() . ' class="widget-title">';
			$after_title  = '</' . woodmart_get_widget_title_tag() . '>';

			register_sidebar(
				array(
					'name'          => esc_html__( 'Main Widget Area', 'woodmart' ),
					'id'            => 'sidebar-1',
					'description'   => esc_html__( 'Default Widget Area for posts and pages', 'woodmart' ),
					'class'         => '',
					'before_widget' => '<div id="%1$s" class="wd-widget widget sidebar-widget' . $widget_class . ' %2$s">',
					'after_widget'  => '</div>',
					'before_title'  => $before_title,
					'after_title'   => $after_title,
				)
			);

			if ( woodmart_get_opt( 'portfolio', '1' ) ) {
				register_sidebar(
					array(
						'name'          => esc_html__( 'Portfolio Widget Area', 'woodmart' ),
						'id'            => 'portfolio-widgets-area',
						'description'   => esc_html__( 'Default Widget Area for projects', 'woodmart' ),
						'class'         => '',
						'before_widget' => '<div id="%1$s" class="wd-widget widget sidebar-widget' . $widget_class . ' %2$s">',
						'after_widget'  => '</div>',
						'before_title'  => $before_title,
						'after_title'   => $after_title,
					)
				);
			}

			if ( woodmart_woocommerce_installed() ) {
				$sidebar_shop_classes = '';

				if ( 'all' === woodmart_get_opt( 'shop_widgets_collapse' ) ) {
					$sidebar_shop_classes .= ' wd-widget-collapse';
				}

				register_sidebar(
					array(
						'name'          => esc_html__( 'Shop page Widget Area', 'woodmart' ),
						'id'            => 'sidebar-shop',
						'description'   => esc_html__( 'Widget Area for shop pages', 'woodmart' ),
						'class'         => '',
						'before_widget' => '<div id="%1$s" class="wd-widget widget sidebar-widget' . $widget_class . $sidebar_shop_classes . ' %2$s">',
						'after_widget'  => '</div>',
						'before_title'  => $before_title,
						'after_title'   => $after_title,
					)
				);
				register_sidebar(
					array(
						'name'          => esc_html__( 'Shop filters', 'woodmart' ),
						'id'            => 'filters-area',
						'description'   => esc_html__( 'Widget Area for shop filters above the products', 'woodmart' ),
						'class'         => '',
						'before_widget' => '<div id="%1$s" class="wd-widget widget filter-widget wd-col' . $widget_class . ' %2$s">',
						'after_widget'  => '</div>',
						'before_title'  => $before_title,
						'after_title'   => $after_title,
					)
				);
				register_sidebar(
					array(
						'name'          => esc_html__( 'Single product page Widget Area', 'woodmart' ),
						'id'            => 'sidebar-product-single',
						'description'   => esc_html__( 'Widget Area for single product page', 'woodmart' ),
						'class'         => '',
						'before_widget' => '<div id="%1$s" class="wd-widget widget sidebar-widget' . $widget_class . ' %2$s">',
						'after_widget'  => '</div>',
						'before_title'  => $before_title,
						'after_title'   => $after_title,
					)
				);

				register_sidebar(
					array(
						'name'          => esc_html__( 'My Account pages sidebar (Deprecated)', 'woodmart' ),
						'id'            => 'sidebar-my-account-pages',
						'description'   => esc_html__( 'Widget Area for My Account, orders and other user pages.', 'woodmart' ),
						'class'         => '',
						'before_widget' => '<div id="%1$s" class="wd-widget widget sidebar-widget' . $widget_class . ' widget-my-account %2$s">',
						'after_widget'  => '</div>',
						'before_title'  => $before_title,
						'after_title'   => $after_title,
					)
				);
			}

			register_sidebar(
				array(
					'name'          => esc_html__( 'Full Screen Menu Area', 'woodmart' ),
					'id'            => 'sidebar-full-screen-menu',
					'description'   => esc_html__( 'Widget Area for full screen menu', 'woodmart' ),
					'class'         => '',
					'before_widget' => '<div id="%1$s" class="wd-widget widget full-screen-menu-widget' . $widget_class . ' %2$s">',
					'after_widget'  => '</div>',
					'before_title'  => $before_title,
					'after_title'   => $after_title,
				)
			);

			register_sidebar(
				array(
					'name'          => esc_html__( 'Area after the mobile menu', 'woodmart' ),
					'id'            => 'mobile-menu-widgets',
					'description'   => esc_html__( 'Place your widgets that will be displayed after the mobile menu links', 'woodmart' ),
					'class'         => '',
					'before_widget' => '<div id="%1$s" class="wd-widget widget mobile-menu-widget %2$s">',
					'after_widget'  => '</div>',
					'before_title'  => $before_title,
					'after_title'   => $after_title,
				)
			);

			$footer_layout = woodmart_get_opt( 'footer-layout' );
			$footer_config = woodmart_get_footer_config( $footer_layout );

			if ( count( $footer_config['cols'] ) > 0 ) {
				foreach ( $footer_config['cols'] as $key => $columns ) {
					$index = $key + 1;
					register_sidebar(
						array(
							'name'          => 'Footer Column ' . $index,
							'id'            => 'footer-' . $index,
							'description'   => 'Footer column',
							'class'         => '',
							'before_widget' => '<div id="%1$s" class="wd-widget widget footer-widget %2$s">',
							'after_widget'  => '</div>',
							'before_title'  => $before_title,
							'after_title'   => $after_title,
						)
					);
				}
			}

			$custom_sidebars = get_posts(
				array(
					'post_type'      => 'woodmart_sidebar',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
				)
			);

			foreach ( $custom_sidebars as $sidebar ) {
				register_sidebar(
					array(
						'name'          => $sidebar->post_title,
						'id'            => 'sidebar-' . $sidebar->ID,
						'description'   => '',
						'class'         => '',
						'before_widget' => '<div id="%1$s" class="wd-widget widget sidebar-widget %2$s">',
						'after_widget'  => '</div>',
						'before_title'  => $before_title,
						'after_title'   => $after_title,
					)
				);
			}
		}
	}

	add_action( 'widgets_init', 'woodmart_widget_init' );
}


/**
 * ------------------------------------------------------------------------------------------------
 * Register plugins necessary for theme
 * ------------------------------------------------------------------------------------------------
 */

if ( ! function_exists( 'woodmart_register_required_plugins' ) ) {
	/**
	 * Register the required plugins for this theme.
	 */
	function woodmart_register_required_plugins() {

		$plugins = array(

			// This is an example of how to include a plugin pre-packaged with a theme.

			array(
				'name'     => 'Elementor', // The plugin name.
				'slug'     => 'elementor', // The plugin slug (typically the folder name).
				'required' => true, // If false, the plugin is only 'recommended' instead of required.
			),
			array(
				'name'               => 'WPBakery Page Builder', // The plugin name.
				'slug'               => 'js_composer', // The plugin slug (typically the folder name).
				'source'             => WOODMART_PLUGINS_URL . 'js_composer.zip', // The plugin source.
				'required'           => true, // If false, the plugin is only 'recommended' instead of required.
				'version'            => get_option( 'woodmart_js_composer_version', '6.4.1' ), // E.g. 1.0.0. If set, the active plugin must be this version or higher.
				'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
				'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
				'external_url'       => '', // If set, overrides default API URL and points to an external URL.
			),

			array(
				'name'               => 'Woodmart Core', // The plugin name.
				'slug'               => 'woodmart-core', // The plugin slug (typically the folder name).
				'source'             => get_parent_theme_file_path( WOODMART_FRAMEWORK . '/plugins/woodmart-core.zip' ), // The plugin source.
				'required'           => true, // If false, the plugin is only 'recommended' instead of required.
				'version'            => WOODMART_CORE_VERSION, // E.g. 1.0.0. If set, the active plugin must be this version or higher.
				'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
				'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
				'external_url'       => '', // If set, overrides default API URL and points to an external URL.
			),

			array(
				'name'    => 'Image Optimizer', // The plugin name.
				'slug'    => 'woodmart-images-optimizer', // The plugin slug (typically the folder name).
				'source'  => WOODMART_PLUGINS_URL . 'woodmart-images-optimizer.zip',
				'version' => get_option( 'woodmart_woodmart-images-optimizer_version', '1.2.0' ),
			),

			array(
				'name'     => 'WooCommerce',
				'slug'     => 'woocommerce',
				'required' => false,
			),

			array(
				'name'     => 'Contact Form 7',
				'slug'     => 'contact-form-7',
				'required' => false,
			),

			array(
				'name'     => 'Safe SVG',
				'slug'     => 'safe-svg',
				'required' => false,
			),

			array(
				'name'     => 'MailChimp for WordPress',
				'slug'     => 'mailchimp-for-wp',
				'required' => false,
			),
		);

		$external_builder = 'wpb' === woodmart_get_current_page_builder() ? 'wpb' : 'elementor';
		$builder          = 'native' === woodmart_get_opt( 'current_builder' ) ? 'gutenberg' : $external_builder;

		if ( ! empty( $_GET['wd_builder'] ) ) { // phpcs:ignore
			$builder = wp_unslash( $_GET['wd_builder'] ); // phpcs:ignore
		}

		if ( ! empty( $_POST['xts_builder'] ) ) { // phpcs:ignore
			$builder = wp_unslash( $_POST['xts_builder'] ); // phpcs:ignore
		}

		if ( isset( $_REQUEST['page'], $_REQUEST['plugin'] ) && 'tgmpa-install-plugins' === $_REQUEST['page'] && in_array( $_REQUEST['plugin'], array( 'js_composer', 'elementor' ) ) ) { // phpcs:ignore
			$builder = 'js_composer' === $_REQUEST['plugin'] ? 'wpb' : 'elementor'; // phpcs:ignore
		}

		$plugins = array_filter(
			$plugins,
			function ( $plugin ) use ( $builder ) {
				if ( 'gutenberg' === $builder ) {
					return in_array( $plugin['slug'], array( 'elementor', 'js_composer' ), true ) ? '' : $plugin;
				} elseif ( 'wpb' === $builder ) {
					return 'elementor' === $plugin['slug'] ? '' : $plugin;
				} else {
					return 'js_composer' === $plugin['slug'] ? '' : $plugin;
				}
			}
		);

		if ( ( ( ! isset( $_GET['page'] ) || ! in_array( $_GET['page'], array( 'tgmpa-install-plugins', 'xts_plugins' ), true ) ) && ( ! isset( $_POST['action'] ) || ! in_array( $_POST['action'], array( 'woodmart_deactivate_plugin', 'woodmart_check_plugins' ), true ) ) ) && ! defined( 'WOODMART_IMAGES_OPTIMIZER_VERSION' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
			$plugins = array_filter(
				$plugins,
				function ( $plugin ) {
					return 'woodmart-images-optimizer' !== $plugin['slug'] ? $plugin : '';
				}
			);
		}

		$plugins = apply_filters( 'woodmart_tgmpa_plugins', $plugins );
		$config  = apply_filters(
			'woodmart_tgmpa_configs_plugins',
			array(
				'default_path' => '',                      // Default absolute path to pre-packaged plugins.
				'menu'         => 'tgmpa-install-plugins', // Menu slug.
				'has_notices'  => true,                    // Show admin notices or not.
				'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
				'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
				'is_automatic' => false,                   // Automatically activate plugins after installation or not.
				'message'      => '',                      // Message to output right before the plugins table.
				'strings'      => array(
					'page_title'                      => esc_html__( 'Install Required Plugins', 'woodmart' ),
					'menu_title'                      => esc_html__( 'Install Plugins', 'woodmart' ),
					// translators: 1: plugin name.
					'installing'                      => 'Installing Plugin: %s',
					'oops'                            => esc_html__( 'Something went wrong with the plugin API.', 'woodmart' ),
					// translators: 1: plugin name.
					'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'woodmart' ),
					// translators: 1: plugin name.
					'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'woodmart' ),
					// translators: 1: plugin name.
					'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'woodmart' ),
					// translators: 1: plugin name.
					'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'woodmart' ),
					// translators: 1: plugin name.
					'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'woodmart' ),
					// translators: 1: plugin name.
					'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'woodmart' ),
					// translators: 1: plugin name.
					'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'woodmart' ),
					// translators: 1: plugin name.
					'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'woodmart' ),
					'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins', 'woodmart' ),
					'activate_link'                   => _n_noop( 'Begin activating plugin', 'Begin activating plugins', 'woodmart' ),
					'return'                          => esc_html__( 'Return to Required Plugins Installer', 'woodmart' ),
					'plugin_activated'                => esc_html__( 'Plugin activated successfully.', 'woodmart' ),
					// translators: %s: dashboard link.
					'complete'                        => 'All plugins installed and activated successfully. %s',
					'nag_type'                        => 'updated', // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
				),
			)
		);

		tgmpa( $plugins, $config );
	}

	add_action( 'tgmpa_register', 'woodmart_register_required_plugins' );
}

// **********************************************************************//
// ! Theme 3d plugins
// **********************************************************************//

if ( ! function_exists( 'woodmart_3d_plugins' ) ) {
	/**
	 * Set revslider as theme plugin
	 */
	function woodmart_3d_plugins() {
		if ( function_exists( 'set_revslider_as_theme' ) ) {
			set_revslider_as_theme();
		}
	}

	add_action( 'init', 'woodmart_3d_plugins' );
}

if ( ! function_exists( 'woodmart_vcSetAsTheme' ) ) {
	/**
	 * Set vc as theme plugin.
	 */
	function woodmart_vcSetAsTheme() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
		if ( function_exists( 'vc_set_as_theme' ) ) {
			vc_set_as_theme();
		}
	}

	add_action( 'vc_before_init', 'woodmart_vcSetAsTheme' );
}

if ( ! function_exists( 'woodmart_register_core_plugin_details' ) ) {
	/**
	 * Register view details popup for Woodmart core.
	 *
	 * @param object $result The result object or array.
	 * @param string $action The type of information being requested from the Plugin Installation API.
	 * @param object $args Plugin API arguments.
	 * @return object
	 */
	function woodmart_register_core_plugin_details( $result, $action, $args ) {
		if ( 'plugin_information' !== $action || empty( $args->slug ) || 'woodmart-core' !== $args->slug ) {
			return $result;
		}

		$description = '<p><strong>WoodMart Core</strong> is the required companion plugin for the WoodMart WordPress theme. It contains the core building blocks that the theme relies on to render templates correctly and to provide advanced functionality across the site.</p>
		<h3>What this plugin provides:</h3>
		<ul>
			<li>Theme components and integration modules used by Woodmart templates</li>
			<li>Custom post types and related features used by the theme</li>
			<li>Theme shortcodes and UI elements required by page content</li>
			<li>Authentication / API helpers used by specific integrations</li>
			<li>Shared libraries and internal utilities used by the theme</li>
		</ul>
		<h3>Why it is required:</h3>
		<p>The WoodMart theme is designed to work together with this plugin. Many theme features are loaded from WoodMart Core to keep the theme lightweight and to separate presentation from functionality. If the plugin is deactivated, parts of the site may stop working or display incorrectly.</p>
		<h3>If you deactivate this plugin:</h3>
		<ul>
			<li>Theme elements, widgets, or shortcodes may no longer render</li>
			<li>Some content blocks may show as missing or unregistered</li>
			<li>Certain integrations may be disabled</li>
		</ul>
		<p>For stable operation, keep WoodMart Core active whenever the WoodMart theme is active.</p>

		<h3>Updates and compatibility:</h3>
		<p>WoodMart Core should be updated together with the WoodMart theme to maintain compatibility. Mismatched versions can cause warnings, missing features, or visual issues. After updating, it may be necessary to clear caches to ensure changes apply correctly.</p>

		<p>This plugin is not intended to be used as a standalone solution. It is specifically built to support the WoodMart theme and its feature set.</p>';

		return (object) array(
			'name'         => 'WoodMart Core',
			'slug'         => $args->slug,
			'version'      => WOODMART_CORE_VERSION,
			'author'       => '<a href="' . esc_url( woodmart_get_theme_info( 'AuthorURI' ) ) . '">' . esc_html( woodmart_get_theme_info( 'Author' ) ) . '</a>',
			'homepage'     => woodmart_get_theme_info( 'ThemeURI' ),
			'requires_php' => woodmart_get_theme_info( 'RequiresPHP' ) ?? '7.4',
			'tested'       => get_bloginfo( 'version' ),
			'premium'      => true,
			'banners'      => array(
				'high' => WOODMART_ASSETS_IMAGES . '/core-plugin-bg.jpg',
			),
			'sections'     => array(
				'description' => $description,
			),
		);
	}

	add_filter( 'plugins_api', 'woodmart_register_core_plugin_details', 10, 3 );
}
