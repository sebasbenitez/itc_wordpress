<?php
/**
 * Plugin Name: Invid WooCommerce Sync
 * Description: Sincroniza el catálogo de Invid Computers con WooCommerce. Activable/desactivable y configurable desde el admin.
 * Version: 1.0.0
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
        'modo_cotizacion'   => 'automatico', // 'automatico' (DolarAPI oficial) o 'manual' (usd_to_ars fijo)
        'markup'            => '1.35',
        'sync_enabled'      => '0', // arranca apagado hasta que el usuario lo prenda
        'sync_interval'     => 'twicedaily',
        'rubros_permitidos' => '', // vacío = importa todo (no recomendado)
        'skus_individuales' => '', // códigos puntuales que se importan SIEMPRE, sin importar el rubro
        'mapeo_categorias' => '', // formato: "CategoriaInvid=MiCategoriaWooCommerce", una por línea
        'categoria_generica' => 'Sin clasificar', // se usa cuando no hay mapeo para la categoría de Invid
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
    $clean['modo_cotizacion'] = in_array($input['modo_cotizacion'] ?? 'automatico', array('automatico', 'manual'), true)
        ? $input['modo_cotizacion']
        : 'automatico';
    $clean['markup']         = floatval($input['markup'] ?? 1.35);
    $clean['sync_enabled']   = isset($input['sync_enabled']) ? '1' : '0';
    $clean['sync_interval']  = sanitize_text_field($input['sync_interval'] ?? 'twicedaily');
    $clean['rubros_permitidos'] = sanitize_text_field($input['rubros_permitidos'] ?? '');
    $clean['skus_individuales'] = sanitize_text_field($input['skus_individuales'] ?? '');
    $clean['mapeo_categorias'] = sanitize_textarea_field($input['mapeo_categorias'] ?? '');
    $clean['categoria_generica'] = sanitize_text_field($input['categoria_generica'] ?? 'Sin clasificar');
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
                        <p class="description">Valor fijo. Solo se usa si el modo de cotización de abajo está en "Manual", o como respaldo si falla la consulta automática.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Modo de cotización</th>
                    <td>
                        <select name="<?php echo IVS_OPTION_KEY; ?>[modo_cotizacion]">
                            <option value="automatico" <?php selected($opts['modo_cotizacion'], 'automatico'); ?>>Automático (DolarAPI, dólar oficial)</option>
                            <option value="manual" <?php selected($opts['modo_cotizacion'], 'manual'); ?>>Manual (uso el valor fijo de arriba)</option>
                        </select>
                        <p class="description">
                            [Confianza media] En modo automático, cada sincronización consulta
                            <code>https://dolarapi.com/v1/dolares/oficial</code> (dólar oficial, campo <code>venta</code>)
                            antes de calcular precios. Si esa consulta falla por cualquier motivo
                            (el servicio no responde, etc.), el plugin usa automáticamente el valor
                            fijo de arriba como respaldo, y lo deja anotado en el log.
                        </p>
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
                    <th scope="row">Productos individuales (por código)</th>
                    <td>
                        <input type="text" name="<?php echo IVS_OPTION_KEY; ?>[skus_individuales]" value="<?php echo esc_attr($opts['skus_individuales']); ?>" class="regular-text" placeholder="Ej: V11HB72221, 0418324" />
                        <p class="description">
                            Códigos de producto de Invid (<code>PART_NUMBER</code>), separados por coma.
                            Estos productos se importan <strong>siempre</strong>, sin importar el filtro de
                            "Rubros a importar" de arriba — útil para traer un modelo puntual (ej. una
                            impresora láser) sin tener que habilitar todo el rubro de impresoras.
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Mapeo de categorías (Invid → tu tienda)</th>
                    <td>
                        <textarea name="<?php echo IVS_OPTION_KEY; ?>[mapeo_categorias]" rows="6" class="large-text" placeholder="Proyectores=Proyectores y Pantallas&#10;Mouse=Perifericos"><?php echo esc_textarea($opts['mapeo_categorias']); ?></textarea>
                        <p class="description">
                            Una equivalencia por línea, formato <code>CategoriaDeInvid=TuCategoriaWooCommerce</code>.
                            <strong>Importante: el plugin ya NO crea categorías nuevas a partir del nombre de Invid.</strong>
                            La categoría de destino (después del <code>=</code>) tiene que ser una que ya
                            exista en tu WooCommerce. Si Invid trae una categoría que no mapeaste acá,
                            el producto va a la "categoría genérica" de abajo.
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Categoría genérica de respaldo</th>
                    <td>
                        <input type="text" name="<?php echo IVS_OPTION_KEY; ?>[categoria_generica]" value="<?php echo esc_attr($opts['categoria_generica']); ?>" class="regular-text" />
                        <p class="description">
                            Se usa cuando la categoría de Invid no tiene mapeo definido. Esta categoría
                            SÍ se crea automáticamente la primera vez si no existe (a diferencia de las
                            categorías de Invid, que nunca se crean solas).
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
    $categoria_fija = get_post_meta($post->ID, '_invid_categoria_fija', true);
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
        <label>
            <input type="checkbox" id="ivs_categoria_fija" name="ivs_categoria_fija" value="1" <?php checked($categoria_fija, '1'); ?> />
            <strong>Categoría fija (no reasignar)</strong>
        </label>
    </p>
    <p class="description">
        Si lo tildás, el sync <strong>nunca va a tocar la categoría</strong> de este
        producto. Usá el selector de "Categorías del producto" de más abajo
        para elegir vos mismo dónde va, y quedará así para siempre (salvo
        que destildes esta casilla).
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
    update_post_meta($post_id, '_invid_categoria_fija', isset($_POST['ivs_categoria_fija']) ? '1' : '');
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
// BUSCADOR DE PRODUCTOS (selección manual, uno por uno)
// ============================================================

add_action('admin_menu', 'ivs_add_buscador_menu');
function ivs_add_buscador_menu() {
    add_submenu_page(
        'invid-wc-sync',
        'Buscador de productos',
        'Buscador de productos',
        'manage_woocommerce',
        'invid-wc-sync-buscador',
        'ivs_render_buscador_page'
    );
}

function ivs_render_buscador_page() {
    $opts = get_option(IVS_OPTION_KEY);
    $clave_transient = 'ivs_buscador_resultados_' . get_current_user_id();
    $resultados = get_transient($clave_transient);
    $mensaje = '';

    // --- Procesar búsqueda ---
    if (isset($_POST['ivs_buscador_action']) && $_POST['ivs_buscador_action'] === 'buscar'
        && check_admin_referer('ivs_buscador_nonce_action', 'ivs_buscador_nonce')) {

        $rubro_busqueda = sanitize_text_field($_POST['rubro_busqueda'] ?? '');

        if ($rubro_busqueda === '') {
            $mensaje = 'Escribí un rubro para buscar.';
        } else {
            try {
                // Misma lógica de cotización que en el sync automático:
                // intenta DolarAPI, si falla usa el valor fijo de respaldo.
                $cotizacion_para_preview = floatval($opts['usd_to_ars']);
                if (($opts['modo_cotizacion'] ?? 'automatico') === 'automatico') {
                    try {
                        $cotizacion_para_preview = ivs_obtener_cotizacion_dolar_oficial();
                    } catch (Exception $e) {
                        // Silenciosamente usamos el valor fijo de respaldo acá;
                        // no es necesario loguear en el buscador manual.
                    }
                }
                set_transient('ivs_buscador_cotizacion_' . get_current_user_id(), $cotizacion_para_preview, HOUR_IN_SECONDS);

                $token = ivs_get_invid_token();
                $catalogo = ivs_get_invid_catalog($token);
                $resultados = array();

                foreach ($catalogo as $item) {
                    $estado_stock = $item['STOCK_STATUS'] ?? '';
                    if (!in_array($estado_stock, array('STOCK OK', 'BAJO STOCK'), true)) {
                        continue; // seguimos respetando la regla de stock
                    }

                    $candidatos = array();
                    if (!empty($item['CATEGORY'])) $candidatos[] = $item['CATEGORY'];
                    foreach (($item['CATEGORIES'] ?? array()) as $cat) {
                        if (!empty($cat['NAME'])) $candidatos[] = $cat['NAME'];
                        if (!empty($cat['PARENT']['NAME'])) $candidatos[] = $cat['PARENT']['NAME'];
                    }

                    foreach ($candidatos as $c) {
                        if (mb_stripos($c, $rubro_busqueda) !== false) {
                            $resultados[] = $item;
                            break;
                        }
                    }
                }

                set_transient($clave_transient, $resultados, HOUR_IN_SECONDS);
                $mensaje = 'Se encontraron ' . count($resultados) . ' productos para "' . esc_html($rubro_busqueda) . '" (con stock disponible).';
            } catch (Exception $e) {
                $mensaje = 'Error al buscar en Invid: ' . $e->getMessage();
            }
        }
    }

    // --- Procesar importación de los seleccionados ---
    if (isset($_POST['ivs_buscador_action']) && $_POST['ivs_buscador_action'] === 'importar'
        && check_admin_referer('ivs_buscador_nonce_action', 'ivs_buscador_nonce')) {

        $seleccionados = isset($_POST['seleccionados']) ? array_map('sanitize_text_field', (array) $_POST['seleccionados']) : array();
        $categoria_destino_id = intval($_POST['categoria_destino'] ?? 0);

        if (empty($seleccionados)) {
            $mensaje = 'No tildaste ningún producto.';
        } elseif ($categoria_destino_id <= 0) {
            $mensaje = 'Tenés que elegir una categoría de destino antes de importar.';
        } elseif (!is_array($resultados)) {
            $mensaje = 'La búsqueda expiró (dura 1 hora). Volvé a buscar el rubro.';
        } else {
            $creados = 0; $actualizados = 0; $errores = 0;
            foreach ($resultados as $item) {
                $clave_item = (string) ($item['PART_NUMBER'] ?: $item['ID']);
                if (in_array($clave_item, $seleccionados, true)) {
                    try {
                        $accion = ivs_upsert_producto($item, $opts, $categoria_destino_id);
                        if ($accion === 'creado') $creados++;
                        elseif ($accion === 'actualizado') $actualizados++;
                    } catch (Exception $e) {
                        $errores++;
                    }
                }
            }
            $mensaje = "Importación manual completa — Creados: $creados, Actualizados: $actualizados, Errores: $errores";
            delete_transient($clave_transient);
            $resultados = null;
        }
    }

    ?>
    <div class="wrap">
        <h1>Buscador de productos Invid</h1>
        <p class="description">
            Buscá un rubro, mirá los productos reales (con imagen y precio) y elegí a mano
            cuáles traer a tu tienda — a diferencia del sync automático, acá vos decidís
            producto por producto, y elegís vos mismo a qué categoría de tu tienda va todo
            el lote que selecciones.
        </p>

        <?php if ($mensaje): ?>
            <div class="notice notice-info"><p><?php echo esc_html($mensaje); ?></p></div>
        <?php endif; ?>

        <form method="post">
            <?php wp_nonce_field('ivs_buscador_nonce_action', 'ivs_buscador_nonce'); ?>
            <input type="hidden" name="ivs_buscador_action" value="buscar" />
            <p>
                <input type="text" name="rubro_busqueda" placeholder="Ej: Impresoras" class="regular-text" />
                <?php submit_button('Buscar en Invid', 'primary', 'submit', false); ?>
            </p>
        </form>

        <?php if (is_array($resultados) && !empty($resultados)): ?>
            <hr>
            <form method="post">
                <?php wp_nonce_field('ivs_buscador_nonce_action', 'ivs_buscador_nonce'); ?>
                <input type="hidden" name="ivs_buscador_action" value="importar" />

                <p>
                    <label for="categoria_destino"><strong>Categoría de destino para TODOS los que tildes abajo:</strong></label><br>
                    <?php
                    wp_dropdown_categories(array(
                        'taxonomy'         => 'product_cat',
                        'name'             => 'categoria_destino',
                        'show_option_none' => '-- Elegir categoría --',
                        'hide_empty'       => false,
                        'id'               => 'categoria_destino',
                    ));
                    ?>
                </p>

                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th style="width:40px;"><input type="checkbox" onclick="document.querySelectorAll('.ivs-check-item').forEach(c => c.checked = this.checked)" /></th>
                            <th style="width:70px;">Imagen</th>
                            <th>Nombre</th>
                            <th>Código</th>
                            <th>Precio estimado (ARS)</th>
                            <th>Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $cotizacion_preview = get_transient('ivs_buscador_cotizacion_' . get_current_user_id());
                        if (!$cotizacion_preview) {
                            $cotizacion_preview = floatval($opts['usd_to_ars']); // respaldo si el transient venció
                        }
                        foreach ($resultados as $item):
                            $clave_item = (string) ($item['PART_NUMBER'] ?: $item['ID']);
                            $precio_ars_prev = floatval($item['FINAL_PRICE'] ?? 0) * $cotizacion_preview * floatval($opts['markup']);
                        ?>
                            <tr>
                                <td><input type="checkbox" class="ivs-check-item" name="seleccionados[]" value="<?php echo esc_attr($clave_item); ?>" /></td>
                                <td>
                                    <?php if (!empty($item['IMAGE_URL'])): ?>
                                        <img src="<?php echo esc_url($item['IMAGE_URL']); ?>" style="max-width:50px;max-height:50px;" />
                                    <?php endif; ?>
                                </td>
                                <td><?php echo esc_html($item['TITLE'] ?? ''); ?></td>
                                <td><?php echo esc_html($clave_item); ?></td>
                                <td>$<?php echo esc_html(number_format($precio_ars_prev, 2, ',', '.')); ?></td>
                                <td><?php echo esc_html($item['STOCK_STATUS'] ?? ''); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <p><?php submit_button('Importar seleccionados', 'primary', 'submit', false); ?></p>
            </form>
        <?php endif; ?>
    </div>
    <?php
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

function ivs_sku_en_lista_individual($item, $opts) {
    $lista_raw = trim($opts['skus_individuales'] ?? '');
    if ($lista_raw === '') {
        return false;
    }

    $codigos_permitidos = array_map(function ($c) {
        return mb_strtolower(trim($c));
    }, explode(',', $lista_raw));

    $sku_item = mb_strtolower(trim($item['PART_NUMBER'] ?? ''));
    $id_item  = mb_strtolower(trim((string) ($item['ID'] ?? '')));

    return in_array($sku_item, $codigos_permitidos, true) || in_array($id_item, $codigos_permitidos, true);
}

/**
 * Resuelve a qué categoría de WooCommerce (ya existente) debe ir un
 * producto, según el nombre de categoría que trae Invid.
 *
 * - Si hay un mapeo configurado para ese nombre, busca esa categoría
 *   EXISTENTE en WooCommerce (no la crea si no existe — para evitar
 *   errores de tipeo silenciosos, cae a la genérica y lo deja en el log).
 * - Si no hay mapeo, usa la categoría genérica de respaldo (esta sí se
 *   crea automáticamente la primera vez, porque es una decisión tuya
 *   explícita, no un nombre arbitrario de Invid).
 */
function ivs_resolver_categoria_mapeada($nombre_categoria_invid, $opts) {
    $mapeo_raw = trim($opts['mapeo_categorias'] ?? '');
    $mapeo = array();
    if ($mapeo_raw !== '') {
        foreach (preg_split('/\r\n|\r|\n/', $mapeo_raw) as $linea) {
            $linea = trim($linea);
            if ($linea === '' || strpos($linea, '=') === false) {
                continue;
            }
            list($invid_nombre, $woo_nombre) = array_map('trim', explode('=', $linea, 2));
            $mapeo[mb_strtolower($invid_nombre)] = $woo_nombre;
        }
    }

    $categoria_generica_nombre = trim($opts['categoria_generica'] ?? 'Sin clasificar');

    if ($nombre_categoria_invid && isset($mapeo[mb_strtolower(trim($nombre_categoria_invid))])) {
        $nombre_woo_destino = $mapeo[mb_strtolower(trim($nombre_categoria_invid))];
        $term = get_term_by('name', $nombre_woo_destino, 'product_cat');
        if ($term) {
            return $term->term_id;
        }
        // El mapeo apunta a una categoría que no existe (typo, o la
        // borraste) — no la creamos silenciosamente, avisamos en el log
        // y usamos la genérica como respaldo seguro.
        ivs_log("AVISO: el mapeo dice \"$nombre_categoria_invid\" -> \"$nombre_woo_destino\", pero esa categoría no existe en WooCommerce. Usando la genérica.");
    }

    // Sin mapeo (o mapeo roto): va a la categoría genérica de respaldo.
    // Esta SÍ se crea automáticamente la primera vez, porque es una
    // categoría que vos elegiste a propósito, no un nombre de Invid.
    $term = get_term_by('name', $categoria_generica_nombre, 'product_cat');
    if (!$term) {
        $result = wp_insert_term($categoria_generica_nombre, 'product_cat');
        return is_wp_error($result) ? null : $result['term_id'];
    }
    return $term->term_id;
}

function ivs_upsert_producto($item, $opts, $forzar_categoria_id = null) {
    $estado_stock = $item['STOCK_STATUS'] ?? '';
    if (!in_array($estado_stock, array('STOCK OK', 'BAJO STOCK'), true)) {
        return 'omitido_stock'; // otros estados (ej. sin stock) sí se excluyen
    }

    // Si viene de una selección manual del "Buscador de productos"
    // ($forzar_categoria_id no es null), el usuario ya decidió a mano
    // que quiere este producto — nos saltamos el filtro de rubro por
    // completo. Si viene del sync automático, aplica el filtro normal.
    if ($forzar_categoria_id === null) {
        if (!ivs_categoria_permitida($item, $opts) && !ivs_sku_en_lista_individual($item, $opts)) {
            return 'omitido_rubro';
        }
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

    // Categoría: SOLO se toca si el producto no tiene "Categoría fija"
    // marcada (mismo patrón que "Precio fijo"). Ya NO se crean categorías
    // nuevas a partir del nombre crudo de Invid — se usa la tabla de
    // mapeo que vos definís, y si no hay mapeo, cae en la categoría
    // genérica de respaldo. Esto evita que se te llene la tienda de
    // categorías que no pediste.
    $categoria_fija = $product_id_existente
        ? (get_post_meta($product_id_existente, '_invid_categoria_fija', true) === '1')
        : false;

    if ($forzar_categoria_id !== null) {
        // Viene del "Buscador de productos": el usuario eligió la
        // categoría a mano, así que la aplicamos directo y la marcamos
        // como fija para que el sync automático nunca se la pise después.
        $product->set_category_ids(array(intval($forzar_categoria_id)));
        $categoria_fija = true; // para que más abajo no la sobreescriba
    } elseif (!$categoria_fija) {
        $categorias = $item['CATEGORIES'] ?? array();
        $nombre_categoria_invid = null;
        foreach ($categorias as $cat) {
            if (!empty($cat['IS_PRIMARY'])) {
                $nombre_categoria_invid = $cat['NAME'];
                break;
            }
        }
        if (!$nombre_categoria_invid && !empty($categorias)) {
            $nombre_categoria_invid = $categorias[0]['NAME'];
        }
        if (!$nombre_categoria_invid && !empty($item['CATEGORY'])) {
            $nombre_categoria_invid = $item['CATEGORY'];
        }

        $term_id = ivs_resolver_categoria_mapeada($nombre_categoria_invid, $opts);
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

    if ($forzar_categoria_id !== null) {
        update_post_meta($product->get_id(), '_invid_categoria_fija', '1');
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

/**
 * Consulta la cotización del dólar oficial en DolarAPI.
 * [Confianza alta en el endpoint y el campo "venta" — verificado contra
 * la documentación pública de DolarAPI antes de escribir esto]
 */
function ivs_obtener_cotizacion_dolar_oficial() {
    $response = wp_remote_get('https://dolarapi.com/v1/dolares/oficial', array('timeout' => 15));

    if (is_wp_error($response)) {
        throw new Exception('Error de red consultando DolarAPI: ' . $response->get_error_message());
    }

    $code = wp_remote_retrieve_response_code($response);
    if ($code !== 200) {
        throw new Exception("DolarAPI devolvió HTTP $code");
    }

    $data = json_decode(wp_remote_retrieve_body($response), true);
    if (!isset($data['venta'])) {
        throw new Exception('Respuesta inesperada de DolarAPI: ' . wp_remote_retrieve_body($response));
    }

    return floatval($data['venta']);
}

function ivs_run_sync() {
    $opts = get_option(IVS_OPTION_KEY);
    ivs_log('=== Iniciando sincronización ===');

    // Resolvemos la cotización a usar ANTES de procesar productos.
    // En modo automático, si DolarAPI falla, caemos al valor fijo
    // configurado como respaldo (nunca se corta el sync por esto).
    if (($opts['modo_cotizacion'] ?? 'automatico') === 'automatico') {
        try {
            $cotizacion_obtenida = ivs_obtener_cotizacion_dolar_oficial();
            $opts['usd_to_ars'] = $cotizacion_obtenida;
            ivs_log("Cotización automática (DolarAPI, oficial): $cotizacion_obtenida");
        } catch (Exception $e) {
            ivs_log('AVISO: falló la cotización automática (' . $e->getMessage() . '). Usando valor fijo de respaldo: ' . $opts['usd_to_ars']);
        }
    } else {
        ivs_log('Modo manual — usando tipo de cambio fijo: ' . $opts['usd_to_ars']);
    }

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
