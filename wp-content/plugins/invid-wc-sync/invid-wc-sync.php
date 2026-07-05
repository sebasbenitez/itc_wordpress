<?php
/**
 * Plugin Name: Invid WooCommerce Sync
 * Description: Sincroniza el catálogo de Invid Computers con WooCommerce. Activable/desactivable y configurable desde el admin.
 * Version: 2.0.0
 * Author: ITCentro
 * Text Domain: invid-wc-sync
 *
 * NOTA DE PRECISIÓN [Alta confianza]: los nombres de funciones de WooCommerce
 * usados acá (wc_get_product_id_by_sku, WC_Product_Simple, etc.) corresponden
 * a la API estable de WooCommerce documentada públicamente. Si tu versión de
 * WooCommerce es muy antigua o muy nueva, convendría confirmar que estos
 * métodos siguen vigentes antes de correrlo en producción.
 */

if (!defined('ABSPATH')) {
    exit; // Que no se acceda directo al archivo
}

define('IVS_OPTION_KEY', 'invid_wc_sync_settings');
define('IVS_CRON_HOOK', 'invid_wc_sync_cron_event');
define('IVS_INVID_BASE_URL', 'https://www.invidcomputers.com/api/v1');

// ============================================================
// ACTIVACIÓN / DESACTIVACIÓN DEL PLUGIN
// ============================================================

register_activation_hook(__FILE__, 'ivs_on_activate');
function ivs_on_activate() {
    $defaults = array(
        'invid_username'    => '',
        'invid_password'    => '',
        'usd_to_ars'        => '1450',
        'markup'            => '1.35',
        'sync_enabled'      => '0', // arranca apagado hasta que el usuario lo prenda
        'sync_interval'     => 'twicedaily',
        'rubros_permitidos' => '', // vacío = importa todo (no recomendado)
        'estado_producto'   => 'draft', // draft = borrador, publish = publicado directo
        'dias_entrega_default' => '4',
    );
    if (!get_option(IVS_OPTION_KEY)) {
        add_option(IVS_OPTION_KEY, $defaults);
    }
}

register_deactivation_hook(__FILE__, 'ivs_on_deactivate');
function ivs_on_deactivate() {
    $timestamp = wp_next_scheduled(IVS_CRON_HOOK);
    if ($timestamp) {
        wp_unschedule_event($timestamp, IVS_CRON_HOOK);
    }
}

// ============================================================
// PANTALLA DE ADMINISTRACIÓN
// ============================================================

add_action('admin_menu', 'ivs_add_admin_menu');
function ivs_add_admin_menu() {
    add_menu_page(
        'Invid Sync',
        'Invid Sync',
        'manage_woocommerce',
        'invid-wc-sync',
        'ivs_render_admin_page',
        'dashicons-update',
        56
    );
}

add_action('admin_init', 'ivs_register_settings');
function ivs_register_settings() {
    register_setting('ivs_settings_group', IVS_OPTION_KEY, 'ivs_sanitize_settings');
}

function ivs_sanitize_settings($input) {
    $clean = array();
    $clean['invid_username'] = sanitize_text_field($input['invid_username'] ?? '');
    $clean['invid_password'] = sanitize_text_field($input['invid_password'] ?? '');
    $clean['usd_to_ars']     = floatval($input['usd_to_ars'] ?? 1450);
    $clean['markup']         = floatval($input['markup'] ?? 1.35);
    $clean['sync_enabled']   = isset($input['sync_enabled']) ? '1' : '0';
    $clean['sync_interval']  = sanitize_text_field($input['sync_interval'] ?? 'twicedaily');
    $clean['rubros_permitidos'] = sanitize_text_field($input['rubros_permitidos'] ?? '');
    $clean['estado_producto']   = in_array($input['estado_producto'] ?? 'draft', array('draft', 'publish'))
        ? $input['estado_producto']
        : 'draft';
    $clean['dias_entrega_default'] = max(0, intval($input['dias_entrega_default'] ?? 4));

    // Reprograma el cron según el estado del toggle
    $timestamp = wp_next_scheduled(IVS_CRON_HOOK);
    if ($clean['sync_enabled'] === '1') {
        if (!$timestamp) {
            wp_schedule_event(time(), $clean['sync_interval'], IVS_CRON_HOOK);
        }
    } else {
        if ($timestamp) {
            wp_unschedule_event($timestamp, IVS_CRON_HOOK);
        }
    }

    return $clean;
}

function ivs_render_admin_page() {
    $opts = get_option(IVS_OPTION_KEY);
    $next_run = wp_next_scheduled(IVS_CRON_HOOK);
    ?>
    <div class="wrap">
        <h1>Invid WooCommerce Sync</h1>

        <form method="post" action="options.php">
            <?php settings_fields('ivs_settings_group'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">Sincronización automática</th>
                    <td>
                        <label>
                            <input type="checkbox" name="<?php echo IVS_OPTION_KEY; ?>[sync_enabled]" value="1" <?php checked($opts['sync_enabled'], '1'); ?> />
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
                        <select name="<?php echo IVS_OPTION_KEY; ?>[sync_interval]">
                            <option value="hourly" <?php selected($opts['sync_interval'], 'hourly'); ?>>Cada hora</option>
                            <option value="twicedaily" <?php selected($opts['sync_interval'], 'twicedaily'); ?>>Dos veces al día</option>
                            <option value="daily" <?php selected($opts['sync_interval'], 'daily'); ?>>Una vez al día</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Usuario Invid</th>
                    <td><input type="text" name="<?php echo IVS_OPTION_KEY; ?>[invid_username]" value="<?php echo esc_attr($opts['invid_username']); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">Contraseña Invid</th>
                    <td><input type="password" name="<?php echo IVS_OPTION_KEY; ?>[invid_password]" value="<?php echo esc_attr($opts['invid_password']); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">Tipo de cambio USD → ARS</th>
                    <td>
                        <input type="number" step="0.01" name="<?php echo IVS_OPTION_KEY; ?>[usd_to_ars]" value="<?php echo esc_attr($opts['usd_to_ars']); ?>" class="regular-text" />
                        <p class="description">Valor fijo. Actualizalo manualmente según el dólar del día. [Recordá: esto se desactualiza rápido]</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Markup (multiplicador de margen)</th>
                    <td>
                        <input type="number" step="0.01" name="<?php echo IVS_OPTION_KEY; ?>[markup]" value="<?php echo esc_attr($opts['markup']); ?>" class="regular-text" />
                        <p class="description">Ej: 1.35 = 35% de margen sobre el costo (FINAL_PRICE de Invid) ya convertido a ARS.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Rubros a importar</th>
                    <td>
                        <input type="text" name="<?php echo IVS_OPTION_KEY; ?>[rubros_permitidos]" value="<?php echo esc_attr($opts['rubros_permitidos']); ?>" class="regular-text" placeholder="Ej: Mouse, Auriculares, Teclados" />
                        <p class="description">
                            Nombres de categoría de Invid (<code>CATEGORY</code>), separados por coma, tal como aparecen en su catálogo.
                            <strong>Dejalo vacío e importa TODO el catálogo de Invid</strong> — no recomendado salvo que sea intencional.
                            La comparación no distingue mayúsculas/minúsculas ni espacios extra.
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Estado del producto al crearlo</th>
                    <td>
                        <select name="<?php echo IVS_OPTION_KEY; ?>[estado_producto]">
                            <option value="draft" <?php selected($opts['estado_producto'], 'draft'); ?>>Borrador (revisás antes de publicar)</option>
                            <option value="publish" <?php selected($opts['estado_producto'], 'publish'); ?>>Publicado directamente</option>
                        </select>
                        <p class="description">
                            Solo aplica a productos NUEVOS. Si un producto ya existe y lo estás actualizando,
                            no le toca el estado que ya tenga puesto (para no des-publicar algo que vos ya revisaste y activaste).
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Días de entrega por defecto</th>
                    <td>
                        <input type="number" min="0" name="<?php echo IVS_OPTION_KEY; ?>[dias_entrega_default]" value="<?php echo esc_attr($opts['dias_entrega_default']); ?>" class="regular-text" />
                        <p class="description">
                            Se muestra en la página de cada producto de Invid como "Producto disponible en X días".
                            Aplica a productos NUEVOS; en cada producto existente lo podés ajustar individualmente
                            desde su propia pantalla de edición (caja "Markup Invid").
                        </p>
                    </td>
                </tr>
            </table>
            <?php submit_button('Guardar configuración'); ?>
        </form>

        <hr>

        <h2>Sincronización manual</h2>
        <form method="post">
            <?php wp_nonce_field('ivs_manual_sync', 'ivs_nonce'); ?>
            <input type="hidden" name="ivs_action" value="manual_sync" />
            <?php submit_button('Sincronizar ahora', 'secondary'); ?>
        </form>

        <?php
        if (isset($_POST['ivs_action']) && $_POST['ivs_action'] === 'manual_sync'
            && check_admin_referer('ivs_manual_sync', 'ivs_nonce')) {
            echo '<div class="notice notice-info"><p>Ejecutando sincronización...</p></div>';
            $resultado = ivs_run_sync();
            echo '<div class="notice notice-success"><p>' . esc_html($resultado) . '</p></div>';
        }
        ?>

        <hr>
        <h2>Últimas líneas del log</h2>
        <pre style="background:#fff;padding:10px;max-height:300px;overflow:auto;border:1px solid #ccc;">
<?php
$log_lines = get_option('ivs_last_log', array());
echo esc_html(implode("\n", $log_lines));
?>
        </pre>
    </div>
    <?php
}

// ============================================================
// LOGGING SIMPLE (guardado en options, últimas ~50 líneas)
// ============================================================

function ivs_log($mensaje) {
    $log = get_option('ivs_last_log', array());
    $log[] = '[' . date('Y-m-d H:i:s') . '] ' . $mensaje;
    $log = array_slice($log, -50); // conserva solo las últimas 50 líneas
    update_option('ivs_last_log', $log);
}

// ============================================================
// CAMPO DE MARKUP POR PRODUCTO (editable a mano en cada producto)
// ============================================================

add_action('add_meta_boxes', 'ivs_add_markup_meta_box');
function ivs_add_markup_meta_box() {
    add_meta_box(
        'ivs_markup_box',
        'Markup Invid',
        'ivs_render_markup_meta_box',
        'product',
        'side',
        'default'
    );
}

function ivs_render_markup_meta_box($post) {
    $markup_pct = get_post_meta($post->ID, '_invid_markup_pct', true);
    $precio_fijo = get_post_meta($post->ID, '_invid_precio_fijo', true);
    $dias_entrega = get_post_meta($post->ID, '_invid_dias_entrega', true);
    $opts = get_option(IVS_OPTION_KEY);
    if ($markup_pct === '') {
        // Si el producto no tiene markup propio todavía, mostramos el
        // default global como referencia (pero no lo guardamos hasta
        // que el usuario guarde el producto al menos una vez)
        $markup_pct = (floatval($opts['markup']) - 1) * 100;
    }
    if ($dias_entrega === '') {
        $dias_entrega = $opts['dias_entrega_default'];
    }
    wp_nonce_field('ivs_save_markup', 'ivs_markup_nonce');
    ?>
    <p>
        <label for="ivs_markup_pct"><strong>Markup para este producto (%)</strong></label><br>
        <input type="number" step="0.1" id="ivs_markup_pct" name="ivs_markup_pct"
               value="<?php echo esc_attr($markup_pct); ?>" style="width:100%;" <?php echo $precio_fijo ? 'disabled' : ''; ?> />
    </p>
    <p class="description">
        Ej: 30 = 30% de margen sobre el costo de Invid. Cada vez que el sync
        actualice este producto, va a recalcular el precio con ESTE
        porcentaje sobre el costo más reciente — no va a pisarlo con el
        default global salvo que lo cambies acá.
    </p>
    <hr style="margin:10px 0;">
    <p>
        <label>
            <input type="checkbox" id="ivs_precio_fijo" name="ivs_precio_fijo" value="1" <?php checked($precio_fijo, '1'); ?> />
            <strong>Precio fijo (no recalcular)</strong>
        </label>
    </p>
    <p class="description">
        Si lo tildás, el sync va a seguir actualizando stock/nombre/descripción
        de este producto, pero <strong>nunca va a tocar el precio</strong> —
        ni con el markup de acá arriba, ni con el costo nuevo de Invid.
        Útil si para este producto puntual definiste un precio final propio
        que no depende del costo del proveedor.
    </p>
    <hr style="margin:10px 0;">
    <p>
        <label for="ivs_dias_entrega"><strong>Días de entrega (dropshipping)</strong></label><br>
        <input type="number" min="0" id="ivs_dias_entrega" name="ivs_dias_entrega"
               value="<?php echo esc_attr($dias_entrega); ?>" style="width:100%;" />
    </p>
    <p class="description">
        Se muestra en la página del producto como "Producto disponible en X días".
        Ajustalo acá si conseguís acelerar la entrega de este producto en particular.
    </p>
    <script>
    (function(){
        var chk = document.getElementById('ivs_precio_fijo');
        var inp = document.getElementById('ivs_markup_pct');
        if (chk && inp) {
            chk.addEventListener('change', function(){ inp.disabled = chk.checked; });
        }
    })();
    </script>
    <?php
}

add_action('save_post_product', 'ivs_save_markup_meta_box');
function ivs_save_markup_meta_box($post_id) {
    if (!isset($_POST['ivs_markup_nonce']) || !wp_verify_nonce($_POST['ivs_markup_nonce'], 'ivs_save_markup')) {
        return;
    }
    if (isset($_POST['ivs_markup_pct']) && $_POST['ivs_markup_pct'] !== '') {
        update_post_meta($post_id, '_invid_markup_pct', floatval($_POST['ivs_markup_pct']));
    }
    update_post_meta($post_id, '_invid_precio_fijo', isset($_POST['ivs_precio_fijo']) ? '1' : '');
    if (isset($_POST['ivs_dias_entrega']) && $_POST['ivs_dias_entrega'] !== '') {
        update_post_meta($post_id, '_invid_dias_entrega', max(0, intval($_POST['ivs_dias_entrega'])));
    }
}

// ============================================================
// AVISO DE TIEMPO DE ENTREGA (solo productos de Invid / dropshipping)
// ============================================================

add_action('woocommerce_single_product_summary', 'ivs_mostrar_aviso_entrega', 11);
function ivs_mostrar_aviso_entrega() {
    global $product;
    if (!$product) {
        return;
    }

    $es_dropship = get_post_meta($product->get_id(), '_invid_dropship', true);
    if ($es_dropship !== '1') {
        return; // producto propio, no de Invid: no mostramos nada
    }

    $dias = get_post_meta($product->get_id(), '_invid_dias_entrega', true);
    if ($dias === '') {
        $opts = get_option(IVS_OPTION_KEY);
        $dias = $opts['dias_entrega_default'];
    }
    $dias = intval($dias);

    if ($dias <= 0) {
        return; // 0 días = no mostrar aviso (por si algún producto ya lo tenés en stock propio)
    }

    $texto = ($dias === 1)
        ? 'Producto disponible en 1 día'
        : sprintf('Producto disponible en %d días', $dias);

    echo '<p class="ivs-aviso-entrega" style="margin:8px 0;font-size:0.95em;color:#555;">'
        . '<span class="dashicons dashicons-clock" style="font-size:1em;vertical-align:middle;"></span> '
        . esc_html($texto) . '</p>';
}

// ============================================================
// CRON HOOK
// ============================================================

add_action(IVS_CRON_HOOK, 'ivs_run_sync');

// ============================================================
// LÓGICA DE SINCRONIZACIÓN
// ============================================================

function ivs_get_invid_token() {
    $opts = get_option(IVS_OPTION_KEY);

    $response = wp_remote_post(IVS_INVID_BASE_URL . '/auth', array(
        'headers' => array('Content-Type' => 'application/json'),
        'body'    => wp_json_encode(array(
            'username' => $opts['invid_username'],
            'password' => $opts['invid_password'],
        )),
        'timeout' => 30,
    ));

    if (is_wp_error($response)) {
        throw new Exception('Error de red al conectar con Invid: ' . $response->get_error_message());
    }

    $data = json_decode(wp_remote_retrieve_body($response), true);
    if (empty($data['status']) || $data['status'] != 1 || empty($data['access_token'])) {
        throw new Exception('Login a Invid falló: ' . wp_remote_retrieve_body($response));
    }

    return $data['access_token'];
}

function ivs_get_invid_catalog($token) {
    $productos = array();
    $url = IVS_INVID_BASE_URL . '/articulo.php?exclude_zero_price=1&exclude_zero_stock=1&published_only=1';

    while ($url) {
        $response = wp_remote_get($url, array(
            'headers' => array('Authorization' => 'Bearer ' . $token),
            'timeout' => 30,
        ));

        if (is_wp_error($response)) {
            throw new Exception('Error obteniendo catálogo: ' . $response->get_error_message());
        }

        $code = wp_remote_retrieve_response_code($response);
        if ($code === 429) {
            ivs_log('Rate limit de Invid alcanzado, esperando 60s...');
            sleep(60);
            continue;
        }

        $data = json_decode(wp_remote_retrieve_body($response), true);
        $items = $data['data'] ?? array();
        if (isset($items['ID'])) {
            $items = array($items); // un solo objeto en vez de array
        }
        $productos = array_merge($productos, $items);

        $url = $data['next_page_url'] ?? null;
        // Pausa breve entre páginas para no acercarse al límite de 200 req/hora
        usleep(1500000); // 1.5s
    }

    return $productos;
}

/**
 * Chequea si la categoría del producto está en la lista de rubros
 * permitidos configurada por el usuario. Si la lista está vacía,
 * permite todo (comportamiento por defecto, no recomendado).
 */
function ivs_categoria_permitida($item, $opts) {
    $rubros_raw = trim($opts['rubros_permitidos'] ?? '');
    if ($rubros_raw === '') {
        return true; // sin filtro configurado = pasa todo
    }

    $rubros_permitidos = array_map(function ($r) {
        return mb_strtolower(trim($r));
    }, explode(',', $rubros_raw));

    // Chequea contra CATEGORY (categoría principal simple), contra cada
    // entrada de CATEGORIES.NAME, y también contra CATEGORIES.PARENT.NAME
    // — esto último es clave: en Invid, categorías como "Proyectores"
    // pueden ser una categoría PADRE que agrupa subcategorías (ej.
    // "Proyectores Full HD"), y sin este chequeo esos productos nunca
    // matchearían el filtro.
    $categorias_del_item = array();
    if (!empty($item['CATEGORY'])) {
        $categorias_del_item[] = $item['CATEGORY'];
    }
    foreach (($item['CATEGORIES'] ?? array()) as $cat) {
        if (!empty($cat['NAME'])) {
            $categorias_del_item[] = $cat['NAME'];
        }
        if (!empty($cat['PARENT']['NAME'])) {
            $categorias_del_item[] = $cat['PARENT']['NAME'];
        }
    }

    foreach ($categorias_del_item as $nombre) {
        if (in_array(mb_strtolower(trim($nombre)), $rubros_permitidos, true)) {
            return true;
        }
    }

    return false;
}

function ivs_upsert_producto($item, $opts) {
    $estado_stock = $item['STOCK_STATUS'] ?? '';
    if (!in_array($estado_stock, array('STOCK OK', 'BAJO STOCK'), true)) {
        return 'omitido_stock'; // otros estados (ej. sin stock) sí se excluyen
    }

    if (!ivs_categoria_permitida($item, $opts)) {
        return 'omitido_rubro';
    }

    $sku = $item['PART_NUMBER'] ?: $item['ID'];
    $precio_usd = floatval($item['FINAL_PRICE']);
    $product_id_existente = wc_get_product_id_by_sku($sku);
    $precio_fijo = false;

    if ($product_id_existente) {
        $precio_fijo = (get_post_meta($product_id_existente, '_invid_precio_fijo', true) === '1');
    }

    if (!$precio_fijo) {
        if ($product_id_existente) {
            // Producto ya existe: usamos el markup guardado en su meta,
            // que pudiste haber ajustado a mano. Si por algún motivo no
            // tiene meta guardada todavía (ej: se creó antes de este
            // cambio), caemos al default global como respaldo.
            $markup_pct = get_post_meta($product_id_existente, '_invid_markup_pct', true);
            if ($markup_pct === '') {
                $markup_pct = (floatval($opts['markup']) - 1) * 100;
            }
        } else {
            // Producto nuevo: arranca con el default global (ej: 30%)
            $markup_pct = (floatval($opts['markup']) - 1) * 100;
        }

        $multiplicador = 1 + (floatval($markup_pct) / 100);
        $precio_ars = $precio_usd * floatval($opts['usd_to_ars']) * $multiplicador;
    }

    if ($product_id_existente) {
        $product = wc_get_product($product_id_existente);
        $accion = 'actualizado';
        // No tocamos el status del producto existente: si vos ya lo
        // revisaste y publicaste, una actualización de precio/stock
        // no debería des-publicarlo ni republicarlo por sorpresa.
    } else {
        $product = new WC_Product_Simple();
        $product->set_sku($sku);
        $product->set_status($opts['estado_producto']); // 'draft' o 'publish', según configuración
        $accion = 'creado';
    }

    $product->set_name(
        $estado_stock === 'BAJO STOCK'
            ? $item['TITLE'] . ' (Stock limitado)'
            : $item['TITLE']
    );
    if (!$precio_fijo) {
        $product->set_regular_price(number_format($precio_ars, 2, '.', ''));
    }
    $product->set_description($item['LONG_DESCRIPTION'] ?? '');

    $short_desc = $item['DESCRIPTION'] ?? '';
    if ($estado_stock === 'BAJO STOCK') {
        $short_desc = "⚠ Stock limitado — pocas unidades disponibles.\n\n" . $short_desc;
    }

    // Aviso de días de entrega: lo metemos directamente en la descripción
    // corta (en vez de depender del hook woocommerce_single_product_summary)
    // porque tu tema (Woodmart + Elementor) arma la página de producto con
    // widgets propios que no disparan ese hook — pero SÍ leen este campo,
    // como confirmamos con el aviso de "Stock limitado".
    $dias_entrega_actual = '';
    if ($product_id_existente) {
        $dias_entrega_actual = get_post_meta($product_id_existente, '_invid_dias_entrega', true);
    }
    if ($dias_entrega_actual === '') {
        $dias_entrega_actual = $opts['dias_entrega_default'];
    }
    $dias_entrega_actual = intval($dias_entrega_actual);
    if ($dias_entrega_actual > 0) {
        $texto_entrega = ($dias_entrega_actual === 1)
            ? '📦 Producto disponible en 1 día.'
            : sprintf('📦 Producto disponible en %d días.', $dias_entrega_actual);
        $short_desc = $texto_entrega . "\n\n" . $short_desc;
    }

    $product->set_short_description($short_desc);
    $product->set_manage_stock(false);
    $product->set_stock_status('instock');

    // Peso y dimensiones: Invid los manda en Kg y Cm. WooCommerce usa las
    // unidades configuradas globalmente en Ajustes > Productos > Medidas.
    // [Importante] Si tu WooCommerce está configurado en unidades distintas
    // (ej. gramos o pulgadas), estos valores van a guardarse como número
    // crudo sin convertir — confirmá que tu configuración de WooCommerce
    // use Kg y Cm, o vamos a tener que agregar una conversión acá.
    if (isset($item['WEIGHT']) && $item['WEIGHT'] !== '') {
        $product->set_weight($item['WEIGHT']);
    }
    if (isset($item['LENGTH']) && $item['LENGTH'] !== '') {
        $product->set_length($item['LENGTH']);
    }
    if (isset($item['WIDTH']) && $item['WIDTH'] !== '') {
        $product->set_width($item['WIDTH']);
    }
    if (isset($item['HEIGHT']) && $item['HEIGHT'] !== '') {
        $product->set_height($item['HEIGHT']);
    }

    // Categoría: usa la primaria si existe, si no la primera de la lista.
    // NOTA: esto crea la categoría en WooCommerce si no existe todavía,
    // usando el nombre tal cual viene de Invid. Si preferís mapear a tus
    // propias categorías, esta parte hay que reemplazarla por un array
    // de equivalencias Invid -> WooCommerce.
    $categorias = $item['CATEGORIES'] ?? array();
    $nombre_categoria = null;
    foreach ($categorias as $cat) {
        if (!empty($cat['IS_PRIMARY'])) {
            $nombre_categoria = $cat['NAME'];
            break;
        }
    }
    if (!$nombre_categoria && !empty($categorias)) {
        $nombre_categoria = $categorias[0]['NAME'];
    }
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

    // Imagen: solo la asignamos si el producto es nuevo (o no tiene imagen),
    // para no forzar una re-descarga innecesaria en cada sync.
    if ($accion === 'creado' && !empty($item['IMAGE_URL'])) {
        $image_id = ivs_sideload_image($item['IMAGE_URL']);
        if ($image_id) {
            $product->set_image_id($image_id);
        }
    }

    $product->save();

    // Guardamos el markup efectivamente usado como meta del producto.
    // Para productos nuevos, esto deja el 30% (o el default que tengas
    // configurado) visible y editable en el meta box "Markup Invid".
    // Para productos existentes, si ya tenían un meta guardado, esto
    // simplemente lo reescribe con el mismo valor (no lo cambia).
    // Si el producto tiene precio fijo, no tocamos este meta tampoco.
    if (!$precio_fijo) {
        update_post_meta($product->get_id(), '_invid_markup_pct', $markup_pct);
    }

    // Marca el producto como "de Invid" (dropshipping), para que el
    // frontend sepa que debe mostrarle el aviso de tiempo de entrega.
    // Solo la seteamos si no existe todavía, para no pisarla si alguna
    // vez decidís convertir manualmente un producto en "propio".
    if (get_post_meta($product->get_id(), '_invid_dropship', true) === '') {
        update_post_meta($product->get_id(), '_invid_dropship', '1');
    }

    // Días de entrega: si el producto no tiene uno propio guardado
    // todavía (por ejemplo, es la primera vez que se crea), le
    // asignamos el default global configurado.
    if (get_post_meta($product->get_id(), '_invid_dias_entrega', true) === '') {
        update_post_meta($product->get_id(), '_invid_dias_entrega', intval($opts['dias_entrega_default']));
    }

    return $accion;
}

/**
 * Descarga una imagen externa y la sube a la Media Library de WordPress.
 * [Confianza media]: usa las funciones estándar de WP para sideload de
 * medios (media_sideload_image / media_handle_sideload). Puede fallar
 * silenciosamente si la URL de Invid no responde o cambia de formato.
 */
function ivs_sideload_image($url) {
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

function ivs_run_sync() {
    $opts = get_option(IVS_OPTION_KEY);
    ivs_log('=== Iniciando sincronización ===');

    try {
        $token = ivs_get_invid_token();
    } catch (Exception $e) {
        ivs_log('ERROR login: ' . $e->getMessage());
        return 'Error de login: ' . $e->getMessage();
    }

    try {
        $productos = ivs_get_invid_catalog($token);
    } catch (Exception $e) {
        ivs_log('ERROR catálogo: ' . $e->getMessage());
        return 'Error obteniendo catálogo: ' . $e->getMessage();
    }

    ivs_log('Invid devolvió ' . count($productos) . ' artículos (pre-filtro)');

    $creados = 0;
    $actualizados = 0;
    $omitidos_stock = 0;
    $omitidos_rubro = 0;
    $errores = 0;

    foreach ($productos as $item) {
        try {
            $resultado = ivs_upsert_producto($item, $opts);
            if ($resultado === 'creado') $creados++;
            elseif ($resultado === 'actualizado') $actualizados++;
            elseif ($resultado === 'omitido_stock') $omitidos_stock++;
            elseif ($resultado === 'omitido_rubro') $omitidos_rubro++;
        } catch (Exception $e) {
            $errores++;
            ivs_log('Error en SKU ' . ($item['PART_NUMBER'] ?? $item['ID'] ?? '?') . ': ' . $e->getMessage());
        }
    }

    $resumen = "Creados: $creados, Actualizados: $actualizados, "
        . "Omitidos (sin stock): $omitidos_stock, Omitidos (rubro no coincide): $omitidos_rubro, "
        . "Errores: $errores";
    ivs_log('=== Sincronización finalizada — ' . $resumen . ' ===');

    return $resumen;
}
