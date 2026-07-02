<?php
defined('ABSPATH') || exit;

class WC_Cuotas_Admin {

    public function __construct() {
        add_action('admin_menu',            [$this, 'add_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public function enqueue_assets(string $hook): void {
        if ($hook !== 'woocommerce_page_wc-cuotas-promociones') return;
        wp_enqueue_style(
            'wc-cuotas-admin',
            WC_CUOTAS_URL . 'assets/css/admin.css',
            [],
            '1.0.0'
        );
        wp_enqueue_script(
            'wc-cuotas-admin',
            WC_CUOTAS_URL . 'assets/js/admin.js',
            [],
            '1.0.0',
            true
        );
        wp_localize_script('wc-cuotas-admin', 'wcCuotasData', [
            'tarjetas' => WC_Cuotas::TARJETAS,
            'count'    => count(WC_Cuotas::get_planes()),
        ]);
    }

    public function add_menu(): void {
        add_submenu_page(
            'woocommerce',
            'Cuotas y Promociones',
            'Cuotas y Promociones',
            'manage_options',
            'wc-cuotas-promociones',
            [$this, 'render_page']
        );
    }

    public function render_page(): void {
        if (isset($_POST['wc_cuotas_save'])) {
            check_admin_referer('wc_cuotas_nonce');
            $this->save_planes();
            echo '<div class="notice notice-success is-dismissible"><p>✅ Configuración guardada.</p></div>';
        }

        $planes = WC_Cuotas::get_planes();
        ?>
        <div class="wrap wc-cuotas-admin-wrap">
            <h1>🏦 Cuotas y Promociones</h1>
            <p class="description">
                Configurá los planes que se mostrarán en la página de cada producto.
                El precio por cuota se calcula automáticamente.
            </p>
            <form method="post" id="wc-cuotas-form">
                <?php wp_nonce_field('wc_cuotas_nonce'); ?>
                <div id="wc-planes-container">
                    <?php foreach ($planes as $i => $plan): ?>
                        <?php $this->render_plan_row($i, $plan); ?>
                    <?php endforeach; ?>
                </div>
                <p>
                    <button type="button" id="wc-add-plan" class="button">
                        ＋ Agregar plan
                    </button>
                </p>
                <hr>
                <input type="submit" name="wc_cuotas_save"
                    class="button button-primary button-large"
                    value="💾 Guardar cambios">
            </form>
        </div>
        <?php
    }

    public function render_plan_row(int $i, array $plan): void {
        $tarjetas_disponibles = WC_Cuotas::TARJETAS;
        ?>
        <div class="wc-plan-box" data-index="<?php echo $i; ?>">
            <div class="wc-plan-header">
                <span class="wc-plan-title">Plan #<span class="wc-plan-num"><?php echo $i + 1; ?></span></span>
                <button type="button" class="button button-small wc-remove-plan">✕ Eliminar</button>
            </div>
            <div class="wc-plan-grid">
                <div class="wc-field">
                    <label>Etiqueta</label>
                    <input type="text"
                        name="planes[<?php echo $i; ?>][label]"
                        value="<?php echo esc_attr($plan['label'] ?? ''); ?>"
                        class="regular-text"
                        placeholder="Ej: 30% Off en 1 Pago">
                </div>
                <div class="wc-field">
                    <label>Descuento %</label>
                    <input type="number"
                        name="planes[<?php echo $i; ?>][descuento]"
                        value="<?php echo esc_attr($plan['descuento'] ?? 0); ?>"
                        min="0" max="100" step="0.01"
                        class="small-text">
                </div>
                <div class="wc-field">
                    <label>Cuotas</label>
                    <input type="number"
                        name="planes[<?php echo $i; ?>][cuotas]"
                        value="<?php echo esc_attr($plan['cuotas'] ?? 1); ?>"
                        min="1" max="60"
                        class="small-text">
                </div>
                <div class="wc-field wc-field-check">
                    <label>
                        <input type="checkbox"
                            name="planes[<?php echo $i; ?>][sin_interes]"
                            value="1"
                            <?php checked(!empty($plan['sin_interes'])); ?>>
                        Sin interés
                    </label>
                </div>
            </div>
            <div class="wc-tarjetas-row">
                <span class="wc-tarjetas-label">Tarjetas:</span>
                <?php foreach ($tarjetas_disponibles as $key => $t): ?>
                <label class="wc-card-check">
                    <input type="checkbox"
                        name="planes[<?php echo $i; ?>][tarjetas][]"
                        value="<?php echo esc_attr($key); ?>"
                        <?php checked(in_array($key, (array) ($plan['tarjetas'] ?? []), true)); ?>>
                    <span class="wc-card-preview"
                        style="background:<?php echo esc_attr($t['color']); ?>;color:<?php echo esc_attr($t['text']); ?>">
                        <?php echo esc_html($t['label']); ?>
                    </span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    private function save_planes(): void {
        $planes = [];
        foreach ((array) ($_POST['planes'] ?? []) as $p) {
            $tarjetas = array_map('sanitize_key', (array) ($p['tarjetas'] ?? []));
            $tarjetas = array_filter($tarjetas, fn($k) => isset(WC_Cuotas::TARJETAS[$k]));
            $planes[] = [
                'label'       => sanitize_text_field($p['label'] ?? ''),
                'descuento'   => min(100, max(0, (float) ($p['descuento'] ?? 0))),
                'cuotas'      => max(1, (int) ($p['cuotas'] ?? 1)),
                'sin_interes' => !empty($p['sin_interes']),
                'tarjetas'    => array_values($tarjetas),
            ];
        }
        update_option('wc_cuotas_planes', $planes);
    }
}
