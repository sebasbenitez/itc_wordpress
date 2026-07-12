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
        'skus_individuales' => '', // códigos puntuales que se importan SIEMPRE, sin importar el rubro
        'mapeo_categorias' => '', // formato: "CategoriaGN=MiCategoriaWooCommerce", una por línea
        'categoria_generica' => 'Sin clasificar', // se usa cuando no hay mapeo para la categoría de GN
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
    $clean['skus_individuales'] = sanitize_text_field($input['skus_individuales'] ?? '');
    $clean['mapeo_categorias'] = sanitize_textarea_field($input['mapeo_categorias'] ?? '');
    $clean['categoria_generica'] = sanitize_text_field($input['categoria_generica'] ?? 'Sin clasificar');
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
                    <th scope="row">Productos individuales (por código)</th>
                    <td>
                        <input type="text" name="<?php echo GNS_OPTION_KEY; ?>[skus_individuales]" value="<?php echo esc_attr($opts['skus_individuales']); ?>" class="regular-text" placeholder="Ej: 195, 3402" />
                        <p class="description">
                            Códigos de producto de GN (<code>codigo</code>), separados por coma. Se importan
                            <strong>siempre</strong>, sin importar el filtro de rubro de arriba.
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Mapeo de categorías (GN → tu tienda)</th>
                    <td>
                        <textarea name="<?php echo GNS_OPTION_KEY; ?>[mapeo_categorias]" rows="6" class="large-text" placeholder="Cables y Adaptadores=Accesorios de Informatica&#10;Soportes=Soportes TV"><?php echo esc_textarea($opts['mapeo_categorias']); ?></textarea>
                        <p class="description">
                            Una equivalencia por línea, formato <code>CategoriaDeGN=TuCategoriaWooCommerce</code>.
                            <strong>El plugin ya NO crea categorías nuevas a partir del nombre de GN.</strong>
                            La categoría de destino tiene que existir ya en tu WooCommerce. Sin mapeo, el
                            producto va a la "categoría genérica" de abajo.
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Categoría genérica de respaldo</th>
                    <td>
                        <input type="text" name="<?php echo GNS_OPTION_KEY; ?>[categoria_generica]" value="<?php echo esc_attr($opts['categoria_generica']); ?>" class="regular-text" />
                        <p class="description">
                            Se usa cuando la categoría de GN no tiene mapeo definido. Esta SÍ se crea
                            automáticamente la primera vez si no existe.
                        </p>
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
    $categoria_fija = get_post_meta($post->ID, '_gn_categoria_fija', true);
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
        <label>
            <input type="checkbox" id="gns_categoria_fija" name="gns_categoria_fija" value="1" <?php checked($categoria_fija, '1'); ?> />
            <strong>Categoría fija (no reasignar)</strong>
        </label>
    </p>
    <p class="description">Si lo tildás, el sync nunca toca la categoría de este producto — la elegís vos con el selector normal de categorías.</p>
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
    update_post_meta($post_id, '_gn_categoria_fija', isset($_POST['gns_categoria_fija']) ? '1' : '');
    if (isset($_POST['gns_dias_entrega']) && $_POST['gns_dias_entrega'] !== '') {
        update_post_meta($post_id, '_gn_dias_entrega', max(0, intval($_POST['gns_dias_entrega'])));
    }
}

// ============================================================
// BUSCADOR DE PRODUCTOS (selección manual, uno por uno)
// ============================================================

add_action('admin_menu', 'gns_add_buscador_menu');
function gns_add_buscador_menu() {
    add_submenu_page(
        'gn-wc-sync',
        'Buscador de productos',
        'Buscador de productos',
        'manage_woocommerce',
        'gn-wc-sync-buscador',
        'gns_render_buscador_page'
    );
}

function gns_render_buscador_page() {
    $opts = get_option(GNS_OPTION_KEY);
    $clave_transient = 'gns_buscador_resultados_' . get_current_user_id();
    $resultados = get_transient($clave_transient);
    $mensaje = '';
    $cotizacion_usd = get_transient('gns_buscador_cotizacion_' . get_current_user_id());

    // --- Procesar búsqueda ---
    if (isset($_POST['gns_buscador_action']) && $_POST['gns_buscador_action'] === 'buscar'
        && check_admin_referer('gns_buscador_nonce_action', 'gns_buscador_nonce')) {

        $rubro_busqueda = sanitize_text_field($_POST['rubro_busqueda'] ?? '');

        if ($rubro_busqueda === '') {
            $mensaje = 'Escribí un rubro para buscar.';
        } else {
            try {
                $token = gns_get_token();
                $cotizacion_usd = gns_get_exchange_rate($token);
                $catalogo = gns_get_catalog($token);
                $resultados = array();

                foreach ($catalogo as $item) {
                    $candidatos = array();
                    if (!empty($item['categoria'])) $candidatos[] = $item['categoria'];
                    if (!empty($item['subcategoria'])) $candidatos[] = $item['subcategoria'];

                    foreach ($candidatos as $c) {
                        if (mb_stripos($c, $rubro_busqueda) !== false) {
                            $resultados[] = $item;
                            break;
                        }
                    }
                }

                set_transient($clave_transient, $resultados, HOUR_IN_SECONDS);
                set_transient('gns_buscador_cotizacion_' . get_current_user_id(), $cotizacion_usd, HOUR_IN_SECONDS);
                $mensaje = 'Se encontraron ' . count($resultados) . ' productos para "' . esc_html($rubro_busqueda) . '" (GN solo devuelve productos con stock positivo).';
            } catch (Exception $e) {
                $mensaje = 'Error al buscar en GN: ' . $e->getMessage();
            }
        }
    }

    // --- Procesar importación de los seleccionados ---
    if (isset($_POST['gns_buscador_action']) && $_POST['gns_buscador_action'] === 'importar'
        && check_admin_referer('gns_buscador_nonce_action', 'gns_buscador_nonce')) {

        $seleccionados = isset($_POST['seleccionados']) ? array_map('sanitize_text_field', (array) $_POST['seleccionados']) : array();
        $categoria_destino_id = intval($_POST['categoria_destino'] ?? 0);

        if (empty($seleccionados)) {
            $mensaje = 'No tildaste ningún producto.';
        } elseif ($categoria_destino_id <= 0) {
            $mensaje = 'Tenés que elegir una categoría de destino antes de importar.';
        } elseif (!is_array($resultados) || !$cotizacion_usd) {
            $mensaje = 'La búsqueda expiró (dura 1 hora). Volvé a buscar el rubro.';
        } else {
            $creados = 0; $actualizados = 0; $errores = 0;
            foreach ($resultados as $item) {
                $clave_item = (string) ($item['codigo'] ?: $item['item_id']);
                if (in_array($clave_item, $seleccionados, true)) {
                    try {
                        $accion = gns_upsert_producto($item, $opts, $cotizacion_usd, $categoria_destino_id);
                        if ($accion === 'creado') $creados++;
                        elseif ($accion === 'actualizado') $actualizados++;
                    } catch (Exception $e) {
                        $errores++;
                    }
                }
            }
            $mensaje = "Importación manual completa — Creados: $creados, Actualizados: $actualizados, Errores: $errores";
            delete_transient($clave_transient);
            delete_transient('gns_buscador_cotizacion_' . get_current_user_id());
            $resultados = null;
        }
    }

    ?>
    <div class="wrap">
        <h1>Buscador de productos Grupo Núcleo</h1>
        <p class="description">
            Buscá un rubro, mirá los productos reales (con imagen y precio) y elegí a mano
            cuáles traer a tu tienda, con la categoría de destino que vos definas.
        </p>

        <?php if ($mensaje): ?>
            <div class="notice notice-info"><p><?php echo esc_html($mensaje); ?></p></div>
        <?php endif; ?>

        <form method="post">
            <?php wp_nonce_field('gns_buscador_nonce_action', 'gns_buscador_nonce'); ?>
            <input type="hidden" name="gns_buscador_action" value="buscar" />
            <p>
                <input type="text" name="rubro_busqueda" placeholder="Ej: Soportes" class="regular-text" />
                <?php submit_button('Buscar en GN', 'primary', 'submit', false); ?>
            </p>
        </form>

        <?php if (is_array($resultados) && !empty($resultados)): ?>
            <hr>
            <form method="post">
                <?php wp_nonce_field('gns_buscador_nonce_action', 'gns_buscador_nonce'); ?>
                <input type="hidden" name="gns_buscador_action" value="importar" />

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
                            <th style="width:40px;"><input type="checkbox" onclick="document.querySelectorAll('.gns-check-item').forEach(c => c.checked = this.checked)" /></th>
                            <th style="width:70px;">Imagen</th>
                            <th>Nombre</th>
                            <th>Código</th>
                            <th>Precio estimado (ARS)</th>
                            <th>Stock (MDP+CABA)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resultados as $item):
                            $clave_item = (string) ($item['codigo'] ?: $item['item_id']);
                            $stock_total = intval($item['stock_mdp'] ?? 0) + intval($item['stock_caba'] ?? 0);

                            // Precio ESTIMADO para la vista previa: neto + impuestos (IVA)
                            // convertido a ARS con la cotización del momento, aplicando el
                            // markup por defecto configurado. [Importante] Es una estimación:
                            // si el producto ya existe con un markup propio distinto, o si lo
                            // marcaste como "Precio fijo", el precio final real podría diferir
                            // de este número — esto es solo para orientarte antes de importar.
                            $precio_neto_usd_prev = floatval($item['precioNeto_USD'] ?? 0);
                            $total_impuestos_prev = 0;
                            foreach (($item['impuestos'] ?? array()) as $imp) {
                                $total_impuestos_prev += floatval($imp['imp_porcentaje'] ?? 0);
                            }
                            $precio_final_usd_prev = $precio_neto_usd_prev * (1 + ($total_impuestos_prev / 100));
                            $precio_ars_prev = $precio_final_usd_prev * $cotizacion_usd * floatval($opts['markup']);
                        ?>
                            <tr>
                                <td><input type="checkbox" class="gns-check-item" name="seleccionados[]" value="<?php echo esc_attr($clave_item); ?>" /></td>
                                <td>
                                    <?php if (!empty($item['url_imagenes'][0]['url'])): ?>
                                        <img src="<?php echo esc_url($item['url_imagenes'][0]['url']); ?>" style="max-width:50px;max-height:50px;" />
                                    <?php endif; ?>
                                </td>
                                <td><?php echo esc_html($item['item_desc_1'] ?? ($item['item_desc_0'] ?? '')); ?></td>
                                <td><?php echo esc_html($clave_item); ?></td>
                                <td>$<?php echo esc_html(number_format($precio_ars_prev, 2, ',', '.')); ?></td>
                                <td><?php echo esc_html($stock_total); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <p class="description">
                    Precio estimado con la cotización del momento (<?php echo esc_html($cotizacion_usd); ?>)
                    y tu markup por defecto (<?php echo esc_html((floatval($opts['markup']) - 1) * 100); ?>%).
                    Si el producto es nuevo, este va a ser el precio real al importarlo. Si ya existe con
                    un markup propio distinto, el precio final real puede variar de esta estimación.
                </p>

                <p><?php submit_button('Importar seleccionados', 'primary', 'submit', false); ?></p>
            </form>
        <?php endif; ?>
    </div>
    <?php
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

/**
 * Resuelve a qué categoría de WooCommerce (ya existente) debe ir un
 * producto de GN, según el mapeo configurado. Igual mecanismo que Invid:
 * nunca crea categorías nuevas a partir del nombre crudo de GN, salvo
 * la genérica de respaldo (una sola, decisión explícita del usuario).
 */
function gns_resolver_categoria_mapeada($nombre_categoria_gn, $opts) {
    $mapeo_raw = trim($opts['mapeo_categorias'] ?? '');
    $mapeo = array();
    if ($mapeo_raw !== '') {
        foreach (preg_split('/\r\n|\r|\n/', $mapeo_raw) as $linea) {
            $linea = trim($linea);
            if ($linea === '' || strpos($linea, '=') === false) {
                continue;
            }
            list($gn_nombre, $woo_nombre) = array_map('trim', explode('=', $linea, 2));
            $mapeo[mb_strtolower($gn_nombre)] = $woo_nombre;
        }
    }

    $categoria_generica_nombre = trim($opts['categoria_generica'] ?? 'Sin clasificar');

    if ($nombre_categoria_gn && isset($mapeo[mb_strtolower(trim($nombre_categoria_gn))])) {
        $nombre_woo_destino = $mapeo[mb_strtolower(trim($nombre_categoria_gn))];
        $term = get_term_by('name', $nombre_woo_destino, 'product_cat');
        if ($term) {
            return $term->term_id;
        }
        gns_log("AVISO: el mapeo dice \"$nombre_categoria_gn\" -> \"$nombre_woo_destino\", pero esa categoría no existe en WooCommerce. Usando la genérica.");
    }

    $term = get_term_by('name', $categoria_generica_nombre, 'product_cat');
    if (!$term) {
        $result = wp_insert_term($categoria_generica_nombre, 'product_cat');
        return is_wp_error($result) ? null : $result['term_id'];
    }
    return $term->term_id;
}

function gns_sku_en_lista_individual($item, $opts) {
    $lista_raw = trim($opts['skus_individuales'] ?? '');
    if ($lista_raw === '') {
        return false;
    }
    $codigos_permitidos = array_map(function ($c) {
        return mb_strtolower(trim($c));
    }, explode(',', $lista_raw));

    $codigo_item = mb_strtolower(trim($item['codigo'] ?? ''));
    $id_item = mb_strtolower(trim((string) ($item['item_id'] ?? '')));

    return in_array($codigo_item, $codigos_permitidos, true) || in_array($id_item, $codigos_permitidos, true);
}

function gns_upsert_producto($item, $opts, $cotizacion_usd, $forzar_categoria_id = null) {
    if ($forzar_categoria_id === null) {
        if (!gns_categoria_permitida($item, $opts) && !gns_sku_en_lista_individual($item, $opts)) {
            return 'omitido_rubro';
        }
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

    // Categoría: se resuelve por mapeo controlado (no se crean categorías
    // nuevas a partir del nombre crudo de GN), salvo que el producto tenga
    // "Categoría fija" tildada, o que venga forzada desde el Buscador manual.
    $categoria_fija = $product_id_existente
        ? (get_post_meta($product_id_existente, '_gn_categoria_fija', true) === '1')
        : false;

    if ($forzar_categoria_id !== null) {
        $product->set_category_ids(array(intval($forzar_categoria_id)));
        $categoria_fija = true; // evita que el bloque de abajo la pise
    } elseif (!$categoria_fija) {
        $nombre_categoria_gn = $item['subcategoria'] ?? ($item['categoria'] ?? null);
        $term_id = gns_resolver_categoria_mapeada($nombre_categoria_gn, $opts);
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

    if ($forzar_categoria_id !== null) {
        update_post_meta($product->get_id(), '_gn_categoria_fija', '1');
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
