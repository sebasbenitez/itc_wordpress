<?php
/**
 * WoodMart Images Optimizer Uninstall
 *
 * Fired when the plugin is uninstalled.
 *
 * @package WoodMart_Images_Optimizer
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) || ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Clean up plugin data when uninstalling.
 */
function woodmart_images_optimizer_uninstall() {
	// Clear all scheduled optimization events.
	wp_clear_scheduled_hook( 'xts_auto_optimize_image' );
	
	// Remove optimization notices transient.
	delete_transient( 'xts_optimizer_notices' );
	
	
	// Note: We intentionally do NOT remove backup files or restore images
	// as users might want to keep their optimized images even after uninstalling.
	// Manual cleanup would be required if users want to restore all images.
	// The optimization quality setting remains in WoodMart theme options.
}

// Run the uninstall function.
woodmart_images_optimizer_uninstall(); 