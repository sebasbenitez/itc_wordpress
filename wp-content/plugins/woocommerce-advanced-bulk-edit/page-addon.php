<?php
/**
 * Template for displaying add-on content
 * 
 * This file serves as a template for all WCABE add-ons.
 * It provides a consistent layout and styling while dynamically
 * loading content from registered add-ons.
 */

// Security check
if (!defined('ABSPATH')) {
    exit;
}

// Get addon ID from URL
$addon_id = sanitize_text_field($_GET['addon_id'] ?? '');

// Get registered add-ons
global $wcabe_addons;

// Default values
$addon_title = esc_html__('Add-on', 'woocommerce-advbulkedit');
$addon_description = '';
$addon_content = '';

// Check if the add-on exists and load its content
if (!empty($addon_id) && isset($wcabe_addons[$addon_id])) {
    $addon = $wcabe_addons[$addon_id];
    $addon_title = $addon['name'] ?? $addon_title;
    $addon_description = $addon['description'] ?? '';
    
    // Call the content callback
    if (isset($addon['content_callback']) && is_callable($addon['content_callback'])) {
        $addon_content = call_user_func($addon['content_callback']);
    }
} else {
    // Invalid or missing add-on ID
    $addon_content = '<div class="wcabe-notice wcabe-notice-error">' . 
                     esc_html__('The requested add-on could not be found.', 'woocommerce-advbulkedit') . 
                     '</div>';
}
?>

<div class="wcabe-top-bar-container">
    <div class="wcabe-title"><?php esc_html_e("WooCommerce Advanced Bulk Edit", "woocommerce-advbulkedit"); ?></div>
</div>

<div class="wrap boxed-layout-wcabe">
    <p>&nbsp;</p>

<!--    <h3>--><?php //echo esc_html($addon_title); ?><!--</h3>-->

    <a href="<?php echo admin_url('edit.php?post_type=product&page=advanced_bulk_edit'); ?>"><?php esc_html_e("< back", "woocommerce-advbulkedit"); ?></a>

    <?php
    // Check if user has admin access
    if (!wcabe_is_current_user_admin()) {
    ?>
        <p><?php esc_html_e("Only admins can access this page.", "woocommerce-advbulkedit"); ?></p>
    <?php
        return;
    }
    ?>

    <?php if (!empty($addon_description)): ?>
    <div class="wcabe-addon-description">
        <p><?php echo esc_html($addon_description); ?></p>
    </div>
    <?php endif; ?>

    <div class="wcabe-addon-content">
        <?php 
        // Output the add-on content
        echo $addon_content; 
        ?>
    </div>
</div>

<?php
// Allow add-ons to enqueue scripts and styles
do_action('wcabe_addon_enqueue_scripts', $addon_id);
?>

<style>
    .wcabe-addon-content {
        margin-top: 20px;
    }
    
    .wcabe-notice {
        padding: 12px;
        margin: 15px 0;
        border-left: 4px solid #ddd;
        background: #fff;
        box-shadow: 0 1px 1px rgba(0,0,0,.04);
    }
    
    .wcabe-notice-error {
        border-left-color: #dc3232;
    }
</style>
