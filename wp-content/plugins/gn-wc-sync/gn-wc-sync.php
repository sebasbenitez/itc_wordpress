<?php
/**
 * Plugin Name: Grupo Núcleo WooCommerce Sync
 * Description: Sincroniza el catálogo de Grupo Núcleo S.A. con WooCommerce. Plugin independiente del de Invid — activable/desactivable por separado.
 * Version: 1.0.0
 * Author: ITCentro
 * Text Domain: gn-wc-sync
 *
 * NOTA DE PRECISIÓN [Alta confianza]: los nombres de campos de la API de GN
 * (item_id, codigo, item_desc_1, precioNeto_USD, stock_mdp, stock_caba,
 * impuestos, url_imagenes, etc.) fueron tomados directamente de la
 * documentación oficial de Grupo Núcleo (apimanual.gruponucleo.com.ar),
 * página "Catálogo con precio y stock". No son una suposición.
 *
 * [Confianza media] No hay evidencia en la documentación de que GetCatalog
 * pagine sus resultados (a diferencia de Invid, que usa next_page_url).
 * El código asume que devuelve todo el catálogo en una sola llamada. Si en
 * la práctica el catálogo es muy grande y GN sí pagina de alguna forma no
 * documentada, este código no lo va a detectar — revisar el log después
 * de la primera corrida para confirmar que la cantidad de productos
 * recibidos tiene sentido.
 */

if (!defined('ABSPATH')) {
    exit;
}

define('GNS_OPTION_KEY', 'gn_wc_sync_settings');
define('GNS_CRON_HOOK', 'gn_wc_sync_cron_event');
define('GNS_BASE_URL', 'https://api.gruponucleosa.com');

// ============================================================
// ACTIVACIÓN / DESACTIVACIÓN
// ============================================================

register_activation_hook(__FILE__, 'gns_on_activate');
function gns_on_activate() {
    $defaults = array(
        'gn_id'             => '',
        'gn_username'       => '',
        'gn_password'       => '',
        'markup'            => '1.35',
        'sync_enabled'      => '0',
        'sync_interval'     => 'twicedaily',
        'rubros_permitidos' => '',
        'estado_producto'   => 'draft',
        'dias_entrega_default' => '4',
        'umbral_stock_bajo' => '5', // [Asunción mía, ajustable] unidades o menos = "stock limitado"
    );
    if (!get_option(GNS_OPTION_KEY)) {
        add_option(GNS_OPTION_KEY, $defaults);
    }
}

register_deactivation_hook(__FILE__, 'gns_on_deactivate');
function gns_on_deactivate() {
    $timestamp = wp_next_scheduled(GNS_CRON_HOOK);
    if ($timestamp) {
        wp_unschedule_event($timestamp, GNS_CRON_HOOK);
    }
}

// ============================================================
// PANTALLA DE ADMINISTRACIÓN
// ============================================================

add_action('admin_menu', 'gns_add_admin_menu');
function gns_add_admin_menu() {
    add_menu_page(
        'GN Sync',
        'GN Sync',
        'manage_woocommerce',
        'gn-wc-sync',
        'gns_render_admin_page',
        'dashicons-update',
        57
    );
}

add_action('admin_init', 'gns_register_settings');
function gns_register_settings() {
    register_setting('gns_settings_group', GNS_OPTION_KEY, 'gns_sanitize_settings');
}

function gns_sanitize_settings($input) {
    $clean = array();
    $clean['gn_id']       = sanitize_text_field($input['gn_id'] ?? '');
    $clean['gn_username'] = sanitize_text_field($input['gn_username'] ?? '');
    $clean['gn_password'] = sanitize_text_field($input['gn_password'] ?? '');
    $clean['markup']      = floatval($input['markup'] ?? 1.35);
    $clean['sync_enabled']  = isset($input['sync_enabled']) ? '1' : '0';
    $clean['sync_interval'] = sanitize_text_field($input['sync_interval'] ?? 'twicedaily');
    $clean['rubros_permitidos'] = sanitize_text_field($input['rubros_permitidos'] ?? '');
    $clean['estado_producto']   = in_array($input['estado_producto'] ?? 'draft', array('draft', 'publish'))
        ? $input['estado_producto']
        : 'draft';
    $clean['dias_entrega_default'] = max(0, intval($input['dias_entrega_default'] ?? 4));
    $clean['umbral_stock_bajo']    = max(0, intval($input['umbral_stock_bajo'] ?? 5));

    $timestamp = wp_next_scheduled(GNS_CRON_HOOK);
    if ($clean['sync_enabled'] === '1') {
        if (!$timestamp) {
            wp_schedule_event(time(), $clean['sync_interval'], GNS_CRON_HOOK);
        }
    } else {
        if ($timestamp) {
            wp_unschedule_event($timestamp, GNS_CRON_HOOK);
        }
    }

    return $clean;
}

function gns_render_admin_page() {
    $opts = get_option(GNS_OPTION_KEY);
    $next_run = wp_next_scheduled(GNS_CRON_HOOK);
    ?>
    <div class="wrap">
        <h1>Grupo Núcleo WooCommerce Sync</h1>

        <form method="post" action="options.php">
            <?php settings_fields('gns_settings_group'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">Sincronización automática</th>
                    <td>
                        <label>
                            <input type="checkbox" name="<?php echo GNS_OPTION_KEY; ?>[sync_enabled]" value="1" <?php checked($opts['sync_enabled'], '1'); ?> />
                            Activar sincronización automática (cron)
                        </label>
                        <?php if ($next_run): ?>
                            <p class="description">Próxima ejecución: <?php echo esc_html(date_i18n('d/m/Y H:i', $next_run)); ?></p>
                        <?php else: ?>
                            <p class="description">Sin programar (desactivado).</p>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Frecuencia</th>
                    <td>
                        <select name="<?php echo GNS_OPTION_KEY; ?>[sync_interval]">
                            <option value="hourly" <?php selected($opts['sync_interval'], 'hourly'); ?>>Cada hora</option>
                            <option value="twicedaily" <?php selected($opts['sync_interval'], 'twicedaily'); ?>>Dos veces al día</option>
                            <option value="daily" <?php selected($opts['sync_interval'], 'daily'); ?>>Una vez al día</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">ID de cliente GN</th>
                    <td><input type="text" name="<?php echo GNS_OPTION_KEY; ?>[gn_id]" value="<?php echo esc_attr($opts['gn_id']); ?>" class="regular-text" />
                    <p class="description">El campo "id" numérico que pide el login de GN.</p></td>
                </tr>
                <tr>
                    <th scope="row">Usuario GN</th>
                    <td><input type="text" name="<?php echo GNS_OPTION_KEY; ?>[gn_username]" value="<?php echo esc_attr($opts['gn_username']); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">Contraseña GN</th>
                    <td><input type="password" name="<?php echo GNS_OPTION_KEY; ?>[gn_password]" value="<?php echo esc_attr($opts['gn_password']); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">Markup por defecto (multiplicador)</th>
                    <td>
                        <input type="number" step="0.01" name="<?php echo GNS_OPTION_KEY; ?>[markup]" value="<?php echo esc_attr($opts['markup']); ?>" class="regular-text" />
                        <p class="description">Ej: 1.35 = 35% de margen. Se aplica a productos NUEVOS; cada producto existente guarda su propio % editable (igual que con Invid).</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Rubros a importar</th>
                    <td>
                        <input type="text" name="<?php echo GNS_OPTION_KEY; ?>[rubros_permitidos]" value="<?php echo esc_attr($opts['rubros_permitidos']); ?>" class="regular-text" placeholder="Ej: Mouse, Auriculares" />
                        <p class="description">Nombres de <code>categoria</code> o <code>subcategoria</code> de GN, separados por coma. Vacío = importa todo el catálogo (no recomendado).</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Estado del producto al crearlo</th>
                    <td>
                        <select name="<?php echo GNS_OPTION_KEY; ?>[estado_producto]">
                            <option value="draft" <?php selected($opts['estado_producto'], 'draft'); ?>>Borrador</option>
                            <option value="publish" <?php selected($opts['estado_producto'], 'publish'); ?>>Publicado directamente</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Días de entrega por defecto</th>
                    <td><input type="number" min="0" name="<?php echo GNS_OPTION_KEY; ?>[dias_entrega_default]" value="<?php echo esc_attr($opts['dias_entrega_default']); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">Umbral de "stock limitado"</th>
                    <td>
                        <input type="number" min="0" name="<?php echo GNS_OPTION_KEY; ?>[umbral_stock_bajo]" value="<?php echo esc_attr($opts['umbral_stock_bajo']); ?>" class="regular-text" />
                        <p class="description">
                            [Esto es una decisión mía, no algo que GN defina] GN no marca explícitamente
                            "stock bajo" como hacía Invid — solo da números de stock por depósito.
                            Este umbral define cuándo mostrar el aviso de "Stock limitado": si el stock
                            total (suma de ambos depósitos) es menor o igual a este número. Ajustalo a tu criterio.
                        </p>
                    </td>
                </tr>
            </table>
            <?php submit_button('Guardar configuración'); ?>
        </form>

        <hr>
        <h2>Sincronización manual</h2>
        <form method="post">
            <?php wp_nonce_field('gns_manual_sync', 'gns_nonce'); ?>
            <input type="hidden" name="gns_action" value="manual_sync" />
            <?php submit_button('Sincronizar ahora', 'secondary'); ?>
        </form>

        <?php
        if (isset($_POST['gns_action']) && $_POST['gns_action'] === 'manual_sync'
            && check_admin_referer('gns_manual_sync', 'gns_nonce')) {
            echo '<div class="notice notice-info"><p>Ejecutando sincronización...</p></div>';
            $resultado = gns_run_sync();
            echo '<div class="notice notice-success"><p>' . esc_html($resultado) . '</p></div>';
        }
        ?>

        <hr>
        <h2>Últimas líneas del log</h2>
        <pre style="background:#fff;padding:10px;max-height:300px;overflow:auto;border:1px solid #ccc;">
<?php
$log_lines = get_option('gns_last_log', array());
echo esc_html(implode("\n", $log_lines));
?>
        </pre>
    </div>
    <?php
}

function gns_log($mensaje) {
    $log = get_option('gns_last_log', array());
    $log[] = '[' . date('Y-m-d H:i:s') . '] ' . $mensaje;
    $log = array_slice($log, -50);
    update_option('gns_last_log', $log);
}

// ============================================================
// META BOX POR PRODUCTO (markup, precio fijo, días de entrega)
// ============================================================

add_action('add_meta_boxes', 'gns_add_meta_box');
function gns_add_meta_box() {
    add_meta_box('gns_markup_box', 'Markup Grupo Núcleo', 'gns_render_meta_box', 'product', 'side', 'default');
}

function gns_render_meta_box($post) {
    $markup_pct   = get_post_meta($post->ID, '_gn_markup_pct', true);
    $precio_fijo  = get_post_meta($post->ID, '_gn_precio_fijo', true);
    $dias_entrega = get_post_meta($post->ID, '_gn_dias_entrega', true);
    $opts = get_option(GNS_OPTION_KEY);

    if ($markup_pct === '') {
        $markup_pct = (floatval($opts['markup']) - 1) * 100;
    }
    if ($dias_entrega === '') {
        $dias_entrega = $opts['dias_entrega_default'];
    }
    wp_nonce_field('gns_save_markup', 'gns_markup_nonce');
    ?>
    <p>
        <label for="gns_markup_pct"><strong>Markup para este producto (%)</strong></label><br>
        <input type="number" step="0.1" id="gns_markup_pct" name="gns_markup_pct"
               value="<?php echo esc_attr($markup_pct); ?>" style="width:100%;" <?php echo $precio_fijo ? 'disabled' : ''; ?> />
    </p>
    <hr style="margin:10px 0;">
    <p>
        <label>
            <input type="checkbox" id="gns_precio_fijo" name="gns_precio_fijo" value="1" <?php checked($precio_fijo, '1'); ?> />
            <strong>Precio fijo (no recalcular)</strong>
        </label>
    </p>
    <hr style="margin:10px 0;">
    <p>
        <label for="gns_dias_entrega"><strong>Días de entrega</strong></label><br>
        <input type="number" min="0" id="gns_dias_entrega" name="gns_dias_entrega"
               value="<?php echo esc_attr($dias_entrega); ?>" style="width:100%;" />
    </p>
    <script>
    (function(){
        var chk = document.getElementById('gns_precio_fijo');
        var inp = document.getElementById('gns_markup_pct');
        if (chk && inp) { chk.addEventListener('change', function(){ inp.disabled = chk.checked; }); }
    })();
    </script>
    <?php
}

add_action('save_post_product', 'gns_save_meta_box');
function gns_save_meta_box($post_id) {
    if (!isset($_POST['gns_markup_nonce']) || !wp_verify_nonce($_POST['gns_markup_nonce'], 'gns_save_markup')) {
        return;
    }
    if (isset($_POST['gns_markup_pct']) && $_POST['gns_markup_pct'] !== '') {
        update_post_meta($post_id, '_gn_markup_pct', floatval($_POST['gns_markup_pct']));
    }
    update_post_meta($post_id, '_gn_precio_fijo', isset($_POST['gns_precio_fijo']) ? '1' : '');
    if (isset($_POST['gns_dias_entrega']) && $_POST['gns_dias_entrega'] !== '') {
        update_post_meta($post_id, '_gn_dias_entrega', max(0, intval($_POST['gns_dias_entrega'])));
    }
}

// ============================================================
// CRON
// ============================================================

add_action(GNS_CRON_HOOK, 'gns_run_sync');

// ============================================================
// LÓGICA DE SINCRONIZACIÓN
// ============================================================

/**
 * Login contra GN. [Alta confianza, confirmado en la doc oficial]:
 * la respuesta es el TOKEN CRUDO como texto plano, no un JSON con
 * un campo "access_token" (a diferencia de Invid).
 */
function gns_get_token() {
    $opts = get_option(GNS_OPTION_KEY);

    $response = wp_remote_post(GNS_BASE_URL . '/Authentication/Login', array(
        'headers' => array('Content-Type' => 'application/json'),
        'body'    => wp_json_encode(array(
            'id'       => intval($opts['gn_id']),
            'username' => $opts['gn_username'],
            'password' => $opts['gn_password'],
        )),
        'timeout' => 30,
    ));

    if (is_wp_error($response)) {
        throw new Exception('Error de red al conectar con GN: ' . $response->get_error_message());
    }

    $code = wp_remote_retrieve_response_code($response);
    $body = trim(wp_remote_retrieve_body($response));

    if ($code !== 200 || empty($body)) {
        throw new Exception("Login a GN falló (HTTP $code): " . $body);
    }

    // El token puede venir con comillas si el servidor lo devuelve como
    // un string JSON válido (ej. "eyJhbGc..."). Le sacamos las comillas
    // si están, por las dudas.
    $body = trim($body, '"');

    return $body;
}

function gns_get_exchange_rate($token) {
    $response = wp_remote_get(GNS_BASE_URL . '/API_V1/GetUSDExchange', array(
        'headers' => array('Authorization' => 'Bearer ' . $token),
        'timeout' => 30,
    ));

    if (is_wp_error($response)) {
        throw new Exception('Error obteniendo cotización de GN: ' . $response->get_error_message());
    }

    $data = json_decode(wp_remote_retrieve_body($response), true);
    if (!isset($data['cotizacionUSD'])) {
        throw new Exception('Respuesta inesperada de GetUSDExchange: ' . wp_remote_retrieve_body($response));
    }

    return floatval($data['cotizacionUSD']);
}

function gns_get_catalog($token) {
    $response = wp_remote_get(GNS_BASE_URL . '/API_V1/GetCatalog', array(
        'headers' => array('Authorization' => 'Bearer ' . $token),
        'timeout' => 60,
    ));

    if (is_wp_error($response)) {
        throw new Exception('Error obteniendo catálogo de GN: ' . $response->get_error_message());
    }

    $code = wp_remote_retrieve_response_code($response);
    if ($code !== 200) {
        throw new Exception("GetCatalog devolvió HTTP $code: " . wp_remote_retrieve_body($response));
    }

    $items = json_decode(wp_remote_retrieve_body($response), true);
    if (!is_array($items)) {
        throw new Exception('GetCatalog no devolvió un array válido.');
    }

    return $items;
}

function gns_categoria_permitida($item, $opts) {
    $rubros_raw = trim($opts['rubros_permitidos'] ?? '');
    if ($rubros_raw === '') {
        return true;
    }

    $rubros_permitidos = array_map(function ($r) {
        return mb_strtolower(trim($r));
    }, explode(',', $rubros_raw));

    $candidatos = array();
    if (!empty($item['categoria'])) $candidatos[] = mb_strtolower(trim($item['categoria']));
    if (!empty($item['subcategoria'])) $candidatos[] = mb_strtolower(trim($item['subcategoria']));

    // Coincidencia PARCIAL (contains), no exacta — así "Soportes" matchea
    // con "Soportes para TV", "Soportes TV/Audio", etc. Antes comparaba
    // con in_array() estricto, lo cual fallaba si el nombre real de GN
    // tenía palabras adicionales.
    foreach ($candidatos as $candidato) {
        foreach ($rubros_permitidos as $rubro) {
            if ($rubro !== '' && mb_strpos($candidato, $rubro) !== false) {
                return true;
            }
        }
    }
    return false;
}

function gns_upsert_producto($item, $opts, $cotizacion_usd) {
    if (!gns_categoria_permitida($item, $opts)) {
        return 'omitido_rubro';
    }

    $stock_mdp  = intval($item['stock_mdp'] ?? 0);
    $stock_caba = intval($item['stock_caba'] ?? 0);
    $stock_total = $stock_mdp + $stock_caba; // según lo que definiste: sumar ambos depósitos

    if ($stock_total <= 0) {
        return 'omitido_stock'; // por las dudas, aunque GN dice que no debería mandar estos
    }

    $es_stock_bajo = $stock_total <= intval($opts['umbral_stock_bajo']);

    // Precio neto USD + impuestos (ej. IVA 21%) declarados en el propio item
    $precio_neto_usd = floatval($item['precioNeto_USD'] ?? 0);
    $total_impuestos_pct = 0;
    foreach (($item['impuestos'] ?? array()) as $imp) {
        $total_impuestos_pct += floatval($imp['imp_porcentaje'] ?? 0);
    }
    $precio_final_usd = $precio_neto_usd * (1 + ($total_impuestos_pct / 100));

    $sku = $item['codigo'] ?? ($item['partNumber'] ?? $item['item_id']);
    $product_id_existente = wc_get_product_id_by_sku($sku);
    $precio_fijo = false;

    if ($product_id_existente) {
        $precio_fijo = (get_post_meta($product_id_existente, '_gn_precio_fijo', true) === '1');
    }

    if (!$precio_fijo) {
        if ($product_id_existente) {
            $markup_pct = get_post_meta($product_id_existente, '_gn_markup_pct', true);
            if ($markup_pct === '') {
                $markup_pct = (floatval($opts['markup']) - 1) * 100;
            }
        } else {
            $markup_pct = (floatval($opts['markup']) - 1) * 100;
        }
        $multiplicador = 1 + (floatval($markup_pct) / 100);
        $precio_ars = $precio_final_usd * $cotizacion_usd * $multiplicador;
    }

    if ($product_id_existente) {
        $product = wc_get_product($product_id_existente);
        $accion = 'actualizado';
    } else {
        $product = new WC_Product_Simple();
        $product->set_sku($sku);
        $product->set_status($opts['estado_producto']);
        $accion = 'creado';
    }

    // Nombre: usamos item_desc_1 como nombre "completo". [Confianza media]
    // no tengo 100% certeza de que item_desc_1 sea siempre el más
    // descriptivo de los 3 (item_desc_0/1/2) — revisar con datos reales
    // si el nombre queda raro en algún producto.
    $nombre_base = $item['item_desc_1'] ?? ($item['item_desc_0'] ?? 'Producto GN ' . $sku);
    $product->set_name($es_stock_bajo ? $nombre_base . ' (Stock limitado)' : $nombre_base);

    if (!$precio_fijo) {
        $product->set_regular_price(number_format($precio_ars, 2, '.', ''));
    }

    $product->set_description($item['item_desc_2'] ?? '');

    // Descripción corta: aviso de entrega + aviso de stock limitado
    $dias_entrega_actual = $product_id_existente
        ? get_post_meta($product_id_existente, '_gn_dias_entrega', true)
        : '';
    if ($dias_entrega_actual === '') {
        $dias_entrega_actual = $opts['dias_entrega_default'];
    }
    $dias_entrega_actual = intval($dias_entrega_actual);

    $short_desc = $item['item_desc_0'] ?? '';
    if ($es_stock_bajo) {
        $short_desc = "⚠ Stock limitado — pocas unidades disponibles.\n\n" . $short_desc;
    }
    if ($dias_entrega_actual > 0) {
        $texto_entrega = ($dias_entrega_actual === 1)
            ? '📦 Producto disponible en 1 día.'
            : sprintf('📦 Producto disponible en %d días.', $dias_entrega_actual);
        $short_desc = $texto_entrega . "\n\n" . $short_desc;
    }
    $product->set_short_description($short_desc);

    $product->set_manage_stock(false);
    $product->set_stock_status('instock');

    // Peso: GN lo da en GRAMOS (peso_gr), tu WooCommerce está en Kg
    // (confirmado por vos), así que convertimos dividiendo por 1000.
    if (isset($item['peso_gr']) && $item['peso_gr'] !== '') {
        $product->set_weight(floatval($item['peso_gr']) / 1000);
    }
    // Dimensiones: GN las da en cm, igual que tu configuración de WooCommerce.
    if (isset($item['largo_cm'])) $product->set_length($item['largo_cm']);
    if (isset($item['ancho_cm'])) $product->set_width($item['ancho_cm']);
    if (isset($item['alto_cm']))  $product->set_height($item['alto_cm']);

    // Categoría: usamos "categoria" (o "subcategoria" si existe, como
    // categoría más específica). Se crea en WooCommerce si no existe.
    $nombre_categoria = $item['subcategoria'] ?? ($item['categoria'] ?? null);
    if ($nombre_categoria) {
        $term = get_term_by('name', $nombre_categoria, 'product_cat');
        if (!$term) {
            $result = wp_insert_term($nombre_categoria, 'product_cat');
            $term_id = is_wp_error($result) ? null : $result['term_id'];
        } else {
            $term_id = $term->term_id;
        }
        if ($term_id) {
            $product->set_category_ids(array($term_id));
        }
    }

    // Imagen: solo en la creación, para no re-descargar en cada sync.
    // [Confianza media] asumo que url_imagenes es un array de objetos
    // con una clave "url" cada uno — confirmar con un ejemplo real si
    // la imagen no aparece bien.
    if ($accion === 'creado' && !empty($item['url_imagenes'][0]['url'])) {
        $image_id = gns_sideload_image($item['url_imagenes'][0]['url']);
        if ($image_id) {
            $product->set_image_id($image_id);
        }
    }

    $product->save();

    if (!$precio_fijo) {
        update_post_meta($product->get_id(), '_gn_markup_pct', $markup_pct);
    }
    if (get_post_meta($product->get_id(), '_gn_dropship', true) === '') {
        update_post_meta($product->get_id(), '_gn_dropship', '1');
    }
    if (get_post_meta($product->get_id(), '_gn_dias_entrega', true) === '') {
        update_post_meta($product->get_id(), '_gn_dias_entrega', intval($opts['dias_entrega_default']));
    }

    return $accion;
}

function gns_sideload_image($url) {
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $tmp = download_url($url, 30);
    if (is_wp_error($tmp)) {
        return null;
    }
    $file_array = array(
        'name'     => basename(parse_url($url, PHP_URL_PATH)),
        'tmp_name' => $tmp,
    );
    $attachment_id = media_handle_sideload($file_array, 0);
    if (is_wp_error($attachment_id)) {
        @unlink($tmp);
        return null;
    }
    return $attachment_id;
}

function gns_run_sync() {
    $opts = get_option(GNS_OPTION_KEY);
    gns_log('=== Iniciando sincronización GN ===');

    try {
        $token = gns_get_token();
    } catch (Exception $e) {
        gns_log('ERROR login: ' . $e->getMessage());
        return 'Error de login: ' . $e->getMessage();
    }

    try {
        $cotizacion = gns_get_exchange_rate($token);
        gns_log("Cotización USD obtenida: $cotizacion");
    } catch (Exception $e) {
        gns_log('ERROR cotización: ' . $e->getMessage());
        return 'Error obteniendo cotización: ' . $e->getMessage();
    }

    try {
        $productos = gns_get_catalog($token);
    } catch (Exception $e) {
        gns_log('ERROR catálogo: ' . $e->getMessage());
        return 'Error obteniendo catálogo: ' . $e->getMessage();
    }

    gns_log('GN devolvió ' . count($productos) . ' artículos');

    $creados = 0;
    $actualizados = 0;
    $omitidos_stock = 0;
    $omitidos_rubro = 0;
    $errores = 0;
    $categorias_vistas = array();

    foreach ($productos as $item) {
        if (!empty($item['categoria'])) {
            $categorias_vistas[$item['categoria'] . ' > ' . ($item['subcategoria'] ?? '')] = true;
        }
        try {
            $resultado = gns_upsert_producto($item, $opts, $cotizacion);
            if ($resultado === 'creado') $creados++;
            elseif ($resultado === 'actualizado') $actualizados++;
            elseif ($resultado === 'omitido_stock') $omitidos_stock++;
            elseif ($resultado === 'omitido_rubro') $omitidos_rubro++;
        } catch (Exception $e) {
            $errores++;
            gns_log('Error en item ' . ($item['codigo'] ?? $item['item_id'] ?? '?') . ': ' . $e->getMessage());
        }
    }

    // Diagnóstico: si NINGÚN producto matcheó el filtro de rubro, buscamos
    // específicamente si existe algo parecido al rubro buscado en TODO el
    // catálogo (no una muestra al azar), para saber si el problema es de
    // nombre mal escrito o si esos productos simplemente no están en el
    // catálogo ahora mismo (recordá: GN solo devuelve productos con stock).
    if ($creados === 0 && $actualizados === 0 && $omitidos_rubro > 0 && trim($opts['rubros_permitidos']) !== '') {
        $rubros_buscados = array_map('trim', explode(',', $opts['rubros_permitidos']));
        $todas_las_categorias = array_keys($categorias_vistas);

        foreach ($rubros_buscados as $rubro) {
            $coincidencias_parciales = array_filter($todas_las_categorias, function ($cat) use ($rubro) {
                return mb_stripos($cat, $rubro) !== false;
            });

            if (empty($coincidencias_parciales)) {
                gns_log("DIAGNÓSTICO: no se encontró NADA parecido a \"$rubro\" en ninguna categoría/subcategoría de los "
                    . count($productos) . " productos devueltos por GN. Esto sugiere que esos productos "
                    . "no tienen stock positivo ahora mismo (GN no los devuelve si no hay stock), o que el "
                    . "nombre real es distinto al que escribiste.");
            } else {
                gns_log("DIAGNÓSTICO: SÍ existen categorías parecidas a \"$rubro\": "
                    . implode(' | ', array_slice($coincidencias_parciales, 0, 10))
                    . " — si el filtro sigue sin matchear, puede haber un problema en la lógica de comparación, no en el nombre.");
            }
        }

        $muestra = array_slice($todas_las_categorias, 0, 25);
        gns_log('Muestra general de categorías en el catálogo: ' . implode(' | ', $muestra));
    }

    $resumen = "Creados: $creados, Actualizados: $actualizados, "
        . "Omitidos (sin stock): $omitidos_stock, Omitidos (rubro): $omitidos_rubro, Errores: $errores";
    gns_log('=== Finalizada — ' . $resumen . ' ===');

    return $resumen;
}
