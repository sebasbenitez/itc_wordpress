<?php
/**
 * Plugin Name: Gestor de Categorías ITCentro
 * Description: Control completo de visibilidad de categorías WooCommerce (ITCentro Edition PRO).
 * Version: 4.5.0
 * Author: ITCentro & T3 Chat
 * License: GPL2+
 */

if (!defined('ABSPATH')) exit;

// ========================================================================
// 1. CONFIGURACIÓN BÁSICA (MENÚS Y SCRIPTS)
// ========================================================================

add_action('init', function () {
    register_term_meta('product_cat', '_category_active', ['type' => 'string', 'single' => true, 'default' => '1']);
});

add_action('admin_menu', function () {
    add_submenu_page('woocommerce', 'Gestor de Categorías ITCentro', 'Gestor de Categorías', 'manage_woocommerce', 'gestor-categorias-itcentro', 'itc_render_admin_page');
});

add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook === 'woocommerce_page_gestor-categorias-itcentro') {
        wp_enqueue_style('itc-admin-style', plugin_dir_url(__FILE__) . 'admin-style.css', [], '4.5.0');
        wp_enqueue_script('itc-admin-script', plugin_dir_url(__FILE__) . 'admin-script.js', ['jquery'], '4.5.0', true);
        wp_localize_script('itc-admin-script', 'itc_ajax', ['ajax_url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('itc_ajax_nonce')]);
    }
});

// ========================================================================
// 2. RENDERIZADO DEL PANEL DE ADMINISTRACIÓN
// ========================================================================

function itc_render_admin_page() {
    echo '<div class="wrap"><h1>Gestor de Categorías ITCentro</h1>';
    if (isset($_GET['cat_id']) && !empty($_GET['cat_id'])) {
        itc_render_subcategory_view(intval($_GET['cat_id']));
    } else {
        itc_render_grid_view();
    }
    echo '</div>';
}

function itc_render_grid_view() {
    $parent_cats = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false, 'parent' => 0]);
    echo '<p>Gestiona la visibilidad de tus categorías principales. Haz clic en "Ver Subcategorías" para diagnosticar su contenido.</p>';
    echo '<div class="itc-actions-bar"><input type="text" id="itc-search" class="itc-search-field" placeholder="🔍 Buscar categoría..."><button id="itc-sync-states" class="button button-primary">Sincronizar Estados</button></div>';
    echo '<div id="itc-sync-message" class="notice notice-success is-dismissible" style="display:none; margin-top: 10px;"></div>';
    if (empty($parent_cats) || is_wp_error($parent_cats)) { echo '<p>No se encontraron categorías principales.</p>'; return; }
    echo '<div class="itc-grid">';
    foreach ($parent_cats as $cat) {
        $active = get_term_meta($cat->term_id, '_category_active', true) !== '0';
        $cls = $active ? '' : 'inactive';
        $subcat_count = count(get_term_children($cat->term_id, 'product_cat'));
        $subcat_link = admin_url('admin.php?page=gestor-categorias-itcentro&cat_id=' . $cat->term_id);
        $all_child_ids = get_term_children($cat->term_id, 'product_cat');
        $all_category_ids = array_merge([$cat->term_id], $all_child_ids);
        $product_query = new WP_Query(['post_type' => 'product', 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids', 'tax_query' => [['taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => $all_category_ids]]]);
        $total_product_count = $product_query->found_posts;
        echo '<div class="itc-card cat-item ' . esc_attr($cls) . '" data-id="' . esc_attr($cat->term_id) . '"><div class="card-header"><h2 class="cat-name">' . esc_html($cat->name) . '</h2><label class="switch"><input type="checkbox" class="toggle" ' . checked($active, true, false) . '><span class="slider"></span></label></div><div class="card-body"><p>Subcategorías: ' . esc_html($subcat_count) . '</p><p>Total Productos (todos): ' . esc_html($total_product_count) . '</p></div><div class="card-footer"><a href="' . esc_url($subcat_link) . '" class="button button-secondary">Ver Subcategorías</a></div></div>';
    }
    echo '</div>';
}

function itc_render_subcategory_view($parent_id) {
    $parent_cat = get_term($parent_id, 'product_cat');
    if (!$parent_cat || is_wp_error($parent_cat)) { echo '<p>Categoría no válida.</p>'; return; }
    $back_link = admin_url('admin.php?page=gestor-categorias-itcentro');
    echo '<a href="' . esc_url($back_link) . '" class="button button-secondary" style="margin-bottom: 20px;">&larr; Volver a la vista principal</a>';
    echo '<h2>Gestionando Subcategorías de: ' . esc_html($parent_cat->name) . '</h2>';
    echo '<input type="text" id="itc-search" class="itc-search-field" placeholder="🔍 Buscar subcategoría...">';
    $sub_cats = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false, 'parent' => $parent_id]);
    if (empty($sub_cats) || is_wp_error($sub_cats)) { echo '<p>Esta categoría no tiene subcategorías directas.</p>'; return; }
    echo '<div class="itc-grid">';
    foreach ($sub_cats as $cat) {
        $active = get_term_meta($cat->term_id, '_category_active', true) !== '0';
        $cls = $active ? '' : 'inactive';
        $status_counts = itc_get_detailed_product_counts($cat->term_id); // NUEVA FUNCIÓN
        $product_list_url = admin_url('edit.php?post_type=product&product_cat=' . $cat->slug); // NUEVO LINK

        echo '<div class="itc-card cat-item ' . esc_attr($cls) . '" data-id="' . esc_attr($cat->term_id) . '">';
        echo '<div class="card-header"><h2 class="cat-name">' . esc_html($cat->name) . '</h2><label class="switch"><input type="checkbox" class="toggle" ' . checked($active, true, false) . '><span class="slider"></span></label></div>';
        echo '<div class="card-body product-status-list">';
        if (empty($status_counts)) {
            echo '<p>Sin productos.</p>';
        } else {
            foreach ($status_counts as $status => $count) {
                if ($count > 0) { // Solo mostrar si hay productos en ese estado
                    echo '<p>' . esc_html($status) . ': ' . esc_html($count) . '</p>';
                }
            }
        }
        echo '</div>';
        echo '<div class="card-footer"><a href="' . esc_url($product_list_url) . '" class="button button-secondary" target="_blank">Ver Productos</a></div>'; // NUEVO BOTÓN
        echo '</div>';
    }
    echo '</div>';
}

/** NUEVA FUNCIÓN HELPER MEJORADA **/
function itc_get_detailed_product_counts($term_id) {
    $counts = [
        'Publicados' => 0,
        'Sin Stock' => 0,
        'Borrador' => 0,
        'Pendiente' => 0,
    ];

    // Contar por estado de publicación
    $post_statuses = ['publish', 'draft', 'pending'];
    foreach ($post_statuses as $status) {
        $query = new WP_Query(['post_type' => 'product', 'post_status' => $status, 'posts_per_page' => -1, 'tax_query' => [['taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => $term_id, 'include_children' => false]], 'fields' => 'ids']);
        $label = ucfirst($status === 'publish' ? 'Publicados' : ($status === 'draft' ? 'Borrador' : 'Pendiente'));
        $counts[$label] = $query->found_posts;
    }

    // Contar "Sin Stock" (solo de los productos publicados)
    $out_of_stock_query = new WP_Query([
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'tax_query' => [['taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => $term_id, 'include_children' => false]],
        'meta_query' => [['key' => '_stock_status', 'value' => 'outofstock']],
        'fields' => 'ids'
    ]);
    $counts['Sin Stock'] = $out_of_stock_query->found_posts;

    return $counts;
}

// ========================================================================
// 3. LÓGICA DE FILTRADO EN FRONTEND
// ========================================================================
function itc_filter_frontend_categories($terms, $taxonomies) {
    if (is_admin() || !is_array($terms)) { return $terms; }
    if (!in_array('product_cat', (array)$taxonomies)) { return $terms; }
    $filtered_terms = [];
    foreach ($terms as $term) {
        if (is_object($term) && isset($term->term_id)) {
            $is_active = get_term_meta($term->term_id, '_category_active', true);
            if ($is_active !== '0') { $filtered_terms[] = $term; }
        }
    }
    return $filtered_terms;
}
add_filter('get_terms', 'itc_filter_frontend_categories', 20, 2);

// ========================================================================
// 4. FUNCIONES AJAX
// ========================================================================
add_action('wp_ajax_itc_toggle_category', function () {
    check_ajax_referer('itc_ajax_nonce', 'nonce');
    $id = intval($_POST['id']);
    $active = ($_POST['active'] === 'true');
    update_term_meta($id, '_category_active', $active ? '1' : '0');
    $prods_query = new WP_Query(['post_type' => 'product', 'posts_per_page' => -1, 'tax_query' => [['taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => [$id]]], 'fields' => 'ids']);
    foreach ($prods_query->posts as $p) {
        wp_update_post(['ID' => $p, 'post_status' => $active ? 'publish' : 'draft']);
    }
    wp_send_json_success(['ok' => true]);
});
add_action('wp_ajax_itc_sync_states', function () {
    check_ajax_referer('itc_ajax_nonce', 'nonce');
    $inactive_cats = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false, 'meta_key' => '_category_active', 'meta_value' => '0', 'fields' => 'ids']);
    if (empty($inactive_cats)) { wp_send_json_success(['message' => 'No hay categorías inactivas para sincronizar.']); return; }
    $products_to_update_query = new WP_Query(['post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => -1, 'fields' => 'ids', 'tax_query' => [['taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => $inactive_cats]]]);
    $products_to_update = $products_to_update_query->posts;
    foreach ($products_to_update as $product_id) {
        wp_update_post(['ID' => $product_id, 'post_status' => 'draft']);
    }
    $count = count($products_to_update);
    $message = $count > 0 ? "Sincronización completa. Se han actualizado {$count} producto(s) a 'Borrador'." : "Sincronización completa. No se encontraron productos desactualizados.";
    wp_send_json_success(['message' => $message]);
});
