<?php
/**
 * Register all of the default WordPress widgets on startup.
 *
 * Calls 'woodmart_widgets_init' action after all of the WordPress widgets have been
 * registered.
 *
 * @package woodmart
 * @since 2.2.0
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

require_once get_parent_theme_file_path( WOODMART_FRAMEWORK . '/widgets/wph-widget-class.php' );

require_once get_parent_theme_file_path( WOODMART_FRAMEWORK . '/widgets/class-widget-price-filter.php' );
require_once get_parent_theme_file_path( WOODMART_FRAMEWORK . '/widgets/class-widget-layered-nav.php' );

require_once get_parent_theme_file_path( WOODMART_FRAMEWORK . '/widgets/class-wp-nav-menu-widget.php' );
require_once get_parent_theme_file_path( WOODMART_FRAMEWORK . '/widgets/class-widget-search.php' );
require_once get_parent_theme_file_path( WOODMART_FRAMEWORK . '/widgets/class-widget-sorting.php' );
require_once get_parent_theme_file_path( WOODMART_FRAMEWORK . '/widgets/class-user-panel-widget.php' );
require_once get_parent_theme_file_path( WOODMART_FRAMEWORK . '/widgets/class-author-area-widget.php' );
require_once get_parent_theme_file_path( WOODMART_FRAMEWORK . '/widgets/class-banner-widget.php' );
require_once get_parent_theme_file_path( WOODMART_FRAMEWORK . '/widgets/class-instagram-widget.php' );
require_once get_parent_theme_file_path( WOODMART_FRAMEWORK . '/widgets/class-static-block-widget.php' );
require_once get_parent_theme_file_path( WOODMART_FRAMEWORK . '/widgets/class-widget-recent-posts.php' );
require_once get_parent_theme_file_path( WOODMART_FRAMEWORK . '/widgets/class-widget-twitter.php' );
require_once get_parent_theme_file_path( WOODMART_FRAMEWORK . '/widgets/class-widget-stock-status.php' );

require_once get_parent_theme_file_path( WOODMART_FRAMEWORK . '/widgets/class-widget-mailchimp.php' );

if ( class_exists( 'WooCommerce' ) ) {
	require_once get_parent_theme_file_path( WOODMART_FRAMEWORK . '/widgets/class-product-category-filter.php' );
}
