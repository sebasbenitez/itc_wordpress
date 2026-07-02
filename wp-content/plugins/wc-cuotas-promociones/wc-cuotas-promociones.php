<?php
/**
 * Plugin Name:  WC Cuotas y Promociones
 * Description:  Muestra planes de cuotas con descuentos en la página de producto.
 * Version:      1.0.0
 * Author:       T3 Chat
 * Text Domain:  wc-cuotas
 * Requires Plugins: woocommerce
 */

defined('ABSPATH') || exit;

define('WC_CUOTAS_PATH', plugin_dir_path(__FILE__));
define('WC_CUOTAS_URL',  plugin_dir_url(__FILE__));

require_once WC_CUOTAS_PATH . 'includes/class-wc-cuotas.php';
require_once WC_CUOTAS_PATH . 'admin/class-wc-cuotas-admin.php';

add_action('plugins_loaded', function () {
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', function () {
            echo '<div class="notice notice-error"><p>'
                . '<strong>WC Cuotas y Promociones</strong> requiere WooCommerce activo.'
                . '</p></div>';
        });
        return;
    }
    new WC_Cuotas();
    new WC_Cuotas_Admin();
});
