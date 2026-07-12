<?php
/**
 * Plugin Name: Cuotas Payway para WooCommerce
 * Description: Muestra precio de contado/transferencia y planes de cuotas (Payway) en la página de producto.
 * Version: 1.0.0
 * Author: ITCentro
 * Text Domain: itc-cuotas-payway
 *
 * [Confianza media] Este plugin usa el filtro estándar de WooCommerce
 * 'woocommerce_get_price_html' para agregar el bloque de cuotas justo
 * después del precio. Este filtro es prácticamente universal en temas
 * WooCommerce (incluso los que usan builders como Elementor suelen llamar
 * a $product->get_price_html(), que dispara este filtro) — pero como ya
 * vimos con el aviso de "días de entrega" que tu tema (Woodmart) a veces
 * no respeta los hooks estándar, TENÉS QUE PROBARLO Y CONFIRMARME si se
 * ve. Si no aparece, hay que buscar otro punto de inyección específico
 * para Woodmart.
 */

if (!defined('ABSPATH')) {
    exit;
}

define('ICP_OPTION_KEY', 'itc_cuotas_payway_settings');

// ============================================================
// ACTIVACIÓN
// ============================================================

register_activation_hook(__FILE__, 'icp_on_activate');
function icp_on_activate() {
    $defaults = array(
        'habilitado'            => '1',
        'descuento_contado_pct' => '15',
        // Formato: "cantidad_cuotas:CFT_porcentaje", separados por coma.
        // Ej: "1:0,3:8,6:16,9:25,12:35" — un plan de cuotas por entrada.
        'planes_payway'         => '1:0,3:8,6:16,9:25,12:35',
        // Si está activo, las cuotas con CFT 0% se muestran en un cartel
        // destacado con degradado de color, separado de la lista normal.
        'destacar_sin_interes'  => '1',
    );
    if (!get_option(ICP_OPTION_KEY)) {
        add_option(ICP_OPTION_KEY, $defaults);
    }
}

// ============================================================
// PANTALLA DE ADMINISTRACIÓN
// ============================================================

add_action('admin_menu', 'icp_add_admin_menu');
function icp_add_admin_menu() {
    add_menu_page(
        'Cuotas Payway',
        'Cuotas Payway',
        'manage_woocommerce',
        'itc-cuotas-payway',
        'icp_render_admin_page',
        'dashicons-money-alt',
        58
    );
}

add_action('admin_init', 'icp_register_settings');
function icp_register_settings() {
    register_setting('icp_settings_group', ICP_OPTION_KEY, 'icp_sanitize_settings');
}

function icp_sanitize_settings($input) {
    $clean = array();
    $clean['habilitado'] = isset($input['habilitado']) ? '1' : '0';
    $clean['descuento_contado_pct'] = max(0, floatval($input['descuento_contado_pct'] ?? 0));
    $clean['planes_payway'] = sanitize_text_field($input['planes_payway'] ?? '');
    $clean['destacar_sin_interes'] = isset($input['destacar_sin_interes']) ? '1' : '0';
    return $clean;
}

function icp_render_admin_page() {
    $opts = get_option(ICP_OPTION_KEY);
    ?>
    <div class="wrap">
        <h1>Cuotas Payway para WooCommerce</h1>

        <div style="background:#fff;border:1px solid #ccd0d4;border-left:4px solid #2271b1;padding:16px 20px;margin:16px 0;max-width:900px;">
            <h2 style="margin-top:0;">¿Qué hace este plugin?</h2>
            <p>
                Agrega, debajo del precio de cada producto en tu tienda, un bloque con:
                un <strong>precio de contado/transferencia</strong> (con descuento), el
                <strong>precio de lista</strong>, y el <strong>detalle de cuotas</strong> que
                pagaría un cliente con tarjeta a través de Payway.
            </p>

            <h3>1. Precio de contado/transferencia</h3>
            <p>
                Se calcula así: <code>precio_lista × (1 − descuento_contado / 100)</code>.<br>
                Ejemplo: si el precio de lista es <strong>$100.000</strong> y el descuento
                configurado es <strong>15%</strong>, el precio de contado queda en
                <strong>$85.000</strong>.
            </p>

            <h3>2. Cómo se calcula cada cuota</h3>
            <p>
                Cada plan de cuotas tiene una <strong>cantidad de cuotas</strong> y un
                <strong>CFT</strong> (Costo Financiero Total, el % que cobra Payway por
                financiar en esa cantidad de cuotas). La fórmula es:
            </p>
            <p style="background:#f6f7f7;padding:10px;border-radius:4px;font-family:monospace;">
                monto_total_financiado = precio_lista × (1 + CFT / 100)<br>
                monto_de_cada_cuota = monto_total_financiado / cantidad_de_cuotas
            </p>
            <p>
                Ejemplo con precio de lista $100.000 y un plan de <strong>3 cuotas con 8% de CFT</strong>:<br>
                Total financiado = $100.000 × 1,08 = $108.000 → cada cuota = $108.000 / 3 = <strong>$36.000</strong>.
            </p>

            <h3>3. Dónde se configuran los planes de cuotas</h3>
            <p>
                En el campo <strong>"Planes de cuotas Payway"</strong> se escribe una lista con el
                formato <code>cantidad:CFT</code>, separada por comas. Por ejemplo:
            </p>
            <p style="background:#f6f7f7;padding:10px;border-radius:4px;font-family:monospace;">
                1:0,3:8,6:16,9:25,12:35
            </p>
            <p>Esto se traduce como:</p>
            <ul style="list-style:disc;padding-left:20px;">
                <li><code>1:0</code> → pago único, 0% de interés (CFT 0)</li>
                <li><code>3:8</code> → 3 cuotas, con 8% de interés total</li>
                <li><code>6:16</code> → 6 cuotas, con 16% de interés total</li>
                <li><code>9:25</code> → 9 cuotas, con 25% de interés total</li>
                <li><code>12:35</code> → 12 cuotas, con 35% de interés total</li>
            </ul>
            <p>
                <strong>Importante:</strong> estos porcentajes los tenés que consultar con Payway
                (tu ejecutivo de cuenta o el panel de comercio) — el plugin no los sabe solo,
                vos los cargás a mano acá.
            </p>

            <h3>4. Cuotas "sin interés" con cartel destacado</h3>
            <p>
                Cualquier plan cargado con <code>CFT</code> en <strong>0</strong> (por ejemplo
                <code>1:0</code> o <code>3:0</code>) se considera automáticamente "sin interés" y,
                si el checkbox <strong>"Destacar cuotas sin interés"</strong> está tildado, se
                muestra en un cartel verde separado del resto, para que salte a la vista del cliente.
                Si el checkbox está destildado, se muestra igual pero en la lista normal, sin colores.
            </p>

            <h3>5. Dónde aparece esto en la tienda</h3>
            <p>
                Solo en la <strong>página individual de cada producto</strong> (no en los listados
                de categorías/grillas, para no saturar visualmente). Aparece justo debajo del precio.
            </p>

            <h3>6. Cosas a tener en cuenta</h3>
            <ul style="list-style:disc;padding-left:20px;">
                <li>El checkbox <strong>"Mostrar en la página de producto"</strong> es el interruptor
                    general: si está destildado, no se muestra nada, en ningún producto.</li>
                <li>Los cálculos se hacen siempre sobre el <strong>precio de lista actual</strong> del
                    producto en WooCommerce — si cambiás el precio del producto, las cuotas se
                    recalculan solas la próxima vez que alguien visite la página (no hace falta
                    "resincronizar" nada, es en tiempo real).</li>
                <li>Por ahora este plugin solo contempla <strong>Payway</strong>. Mercado Pago se
                    puede sumar más adelante como un bloque aparte, si se decide hacerlo.</li>
            </ul>
        </div>

        <form method="post" action="options.php">
            <?php settings_fields('icp_settings_group'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">Mostrar en la página de producto</th>
                    <td>
                        <label>
                            <input type="checkbox" name="<?php echo ICP_OPTION_KEY; ?>[habilitado]" value="1" <?php checked($opts['habilitado'], '1'); ?> />
                            Activar el bloque de precio contado + cuotas
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Descuento contado/transferencia (%)</th>
                    <td>
                        <input type="number" step="0.1" name="<?php echo ICP_OPTION_KEY; ?>[descuento_contado_pct]" value="<?php echo esc_attr($opts['descuento_contado_pct']); ?>" class="regular-text" />
                        <p class="description">
                            Se resta sobre el precio actual del producto (<code>regular_price</code>)
                            para calcular el "Precio efectivo o transferencia".
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Planes de cuotas Payway</th>
                    <td>
                        <input type="text" name="<?php echo ICP_OPTION_KEY; ?>[planes_payway]" value="<?php echo esc_attr($opts['planes_payway']); ?>" class="large-text" />
                        <p class="description">
                            Formato: <code>cantidad_cuotas:CFT_porcentaje</code>, separados por coma.<br>
                            Ejemplo: <code>1:0,3:8,6:16,9:25,12:35</code> significa: 1 pago sin interés,
                            3 cuotas con 8% de CFT, 6 cuotas con 16%, etc.<br>
                            El precio de cada cuota se calcula como:
                            <code>(precio_lista × (1 + CFT/100)) / cantidad_cuotas</code>.<br>
                            <strong>Truco:</strong> cualquier plan con CFT en <code>0</code> se considera
                            "sin interés" automáticamente — no hace falta un campo aparte para eso.
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Destacar cuotas sin interés</th>
                    <td>
                        <label>
                            <input type="checkbox" name="<?php echo ICP_OPTION_KEY; ?>[destacar_sin_interes]" value="1" <?php checked($opts['destacar_sin_interes'], '1'); ?> />
                            Mostrar un cartel con color aparte para los planes con CFT 0%
                        </label>
                        <p class="description">
                            Si lo activás, los planes sin interés (CFT 0) se separan de la lista normal
                            y se muestran en un cartel destacado con degradado de color, para que salten
                            a la vista. Si lo desactivás, todo se muestra en una sola lista simple.
                        </p>
                    </td>
                </tr>
            </table>
            <?php submit_button('Guardar configuración'); ?>
        </form>

        <hr>
        <h2>Vista previa</h2>
        <p class="description">Con un precio de lista de ejemplo ($100.000), así se vería el bloque:</p>
        <div style="background:#fff;border:1px solid #ccc;padding:15px;max-width:500px;">
            <?php echo icp_generar_html_cuotas(100000); ?>
        </div>
    </div>
    <?php
}

// ============================================================
// CÁLCULO Y RENDERIZADO DEL BLOQUE DE CUOTAS
// ============================================================

function icp_formatear_ars($numero) {
    return '$' . number_format($numero, 2, ',', '.');
}

function icp_generar_html_cuotas($precio_lista) {
    $opts = get_option(ICP_OPTION_KEY);
    $precio_lista = floatval($precio_lista);

    if ($precio_lista <= 0) {
        return '';
    }

    $descuento_pct = floatval($opts['descuento_contado_pct']);
    $precio_contado = $precio_lista * (1 - ($descuento_pct / 100));

    $planes_raw = trim($opts['planes_payway'] ?? '');
    $planes = array();
    if ($planes_raw !== '') {
        foreach (explode(',', $planes_raw) as $par) {
            $partes = explode(':', trim($par));
            if (count($partes) === 2) {
                $n_cuotas = intval($partes[0]);
                $cft_pct  = floatval($partes[1]);
                if ($n_cuotas > 0) {
                    $planes[] = array('n' => $n_cuotas, 'cft' => $cft_pct);
                }
            }
        }
    }

    ob_start();
    ?>
    <div class="itc-cuotas-payway" style="margin:10px 0;padding:12px 0;border-top:1px solid #e0e0e0;">
        <?php if ($descuento_pct > 0): ?>
            <p style="margin:0 0 4px;font-size:0.9em;color:#555;">Precio efectivo o transferencia</p>
            <p style="margin:0 0 10px;font-size:1.4em;font-weight:bold;color:#d6006e;">
                <?php echo icp_formatear_ars($precio_contado); ?>
            </p>
        <?php endif; ?>

        <p style="margin:0 0 10px;font-size:0.9em;color:#777;">
            Precio de lista: <?php echo icp_formatear_ars($precio_lista); ?>
        </p>

        <?php
        // Separamos los planes en "sin interés" (CFT 0) y "con interés",
        // para poder destacar los primeros con un cartel de color aparte.
        $planes_sin_interes = array_filter($planes, function ($p) { return floatval($p['cft']) == 0; });
        $planes_con_interes = array_filter($planes, function ($p) { return floatval($p['cft']) > 0; });
        $destacar = (!empty($opts['destacar_sin_interes']) && $opts['destacar_sin_interes'] === '1');
        ?>

        <?php if (!empty($planes_sin_interes) && $destacar): ?>
            <div style="
                background: linear-gradient(135deg, #00c853 0%, #00e676 100%);
                border-radius: 10px;
                padding: 14px 16px;
                margin: 0 0 12px;
                box-shadow: 0 2px 8px rgba(0,200,83,0.25);
            ">
                <p style="margin:0 0 8px;font-weight:bold;font-size:1em;color:#fff;letter-spacing:0.3px;">
                    ✨ CUOTAS SIN INTERÉS
                </p>
                <ul style="margin:0;padding-left:18px;list-style:none;">
                    <?php foreach ($planes_sin_interes as $plan):
                        $monto_cuota = $precio_lista / $plan['n'];
                    ?>
                        <li style="color:#fff;font-size:1em;padding:2px 0;">
                            ✓ <strong><?php echo intval($plan['n']); ?> x <?php echo icp_formatear_ars($monto_cuota); ?></strong>
                            <span style="background:#fff;color:#00a544;font-size:0.75em;font-weight:bold;padding:1px 6px;border-radius:10px;margin-left:6px;">CFT 0%</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php elseif (!empty($planes_sin_interes) && !$destacar): ?>
            <ul style="margin:0 0 10px;padding-left:18px;font-size:0.9em;">
                <?php foreach ($planes_sin_interes as $plan):
                    $monto_cuota = $precio_lista / $plan['n'];
                ?>
                    <li><?php echo intval($plan['n']); ?> x <?php echo icp_formatear_ars($monto_cuota); ?> — CFT 0%</li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if (!empty($planes_con_interes)): ?>
            <p style="margin:0 0 6px;font-weight:bold;font-size:0.9em;">Cuotas con tarjeta (Payway):</p>
            <ul style="margin:0;padding-left:18px;font-size:0.9em;">
                <?php foreach ($planes_con_interes as $plan):
                    $total_financiado = $precio_lista * (1 + ($plan['cft'] / 100));
                    $monto_cuota = $total_financiado / $plan['n'];
                ?>
                    <li>
                        <?php echo intval($plan['n']); ?> x <?php echo icp_formatear_ars($monto_cuota); ?>
                        — CFT <?php echo icp_formatear_numero_simple($plan['cft']); ?>%
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}

function icp_formatear_numero_simple($n) {
    // Si es un entero (ej. 8.0), lo mostramos sin decimales (ej. "8"),
    // si tiene decimales reales los mostramos (ej. "8,5").
    return (floor($n) == $n) ? number_format($n, 0) : number_format($n, 1, ',', '.');
}

// ============================================================
// HOOK: agregar el bloque después del precio en la página de producto
// ============================================================

add_filter('woocommerce_get_price_html', 'icp_agregar_cuotas_al_precio', 20, 2);
function icp_agregar_cuotas_al_precio($price_html, $product) {
    $opts = get_option(ICP_OPTION_KEY);

    if (empty($opts['habilitado']) || $opts['habilitado'] !== '1') {
        return $price_html;
    }

    // Solo en la página individual de producto, no en listados/grillas,
    // para no saturar visualmente las categorías.
    if (!is_product()) {
        return $price_html;
    }

    $precio_lista = floatval($product->get_regular_price());
    if ($precio_lista <= 0) {
        return $price_html;
    }

    return $price_html . icp_generar_html_cuotas($precio_lista);
}
