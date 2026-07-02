<?php
defined('ABSPATH') || exit;

class WC_Cuotas {

    const TARJETAS = [
        'visa'       => ['label' => 'VISA',    'color' => '#1a1f71', 'text' => '#fff'],
        'mastercard' => ['label' => 'MC',      'color' => '#eb001b', 'text' => '#fff'],
        'amex'       => ['label' => 'AMEX',    'color' => '#2e77bc', 'text' => '#fff'],
        'naranja'    => ['label' => 'NARANJA', 'color' => '#ff6600', 'text' => '#fff'],
        'cabal'      => ['label' => 'CABAL',   'color' => '#005ea6', 'text' => '#fff'],
        'debito'     => ['label' => 'DÉBITO',  'color' => '#4caf50', 'text' => '#fff'],
    ];

    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_styles']);
        add_action('woocommerce_single_product_summary', [$this, 'render'], 25);
    }

    public function enqueue_styles(): void {
        if (!is_product()) return;
        wp_enqueue_style(
            'wc-cuotas-frontend',
            WC_CUOTAS_URL . 'assets/css/frontend.css',
            [],
            '1.0.0'
        );
    }

    public static function get_planes(): array {
        return get_option('wc_cuotas_planes', self::default_planes());
    }

    public static function default_planes(): array {
        return [
            [
                'label'       => '30% Off en 1 Pago',
                'descuento'   => 30,
                'cuotas'      => 1,
                'sin_interes' => false,
                'tarjetas'    => ['visa', 'mastercard'],
            ],
            [
                'label'       => '20% Off + 3 Cuotas sin interés',
                'descuento'   => 20,
                'cuotas'      => 3,
                'sin_interes' => true,
                'tarjetas'    => ['visa', 'mastercard'],
            ],
            [
                'label'       => '15% Off + 6 Cuotas sin interés',
                'descuento'   => 15,
                'cuotas'      => 6,
                'sin_interes' => true,
                'tarjetas'    => ['visa', 'mastercard'],
            ],
            [
                'label'       => '12 Cuotas sin interés',
                'descuento'   => 0,
                'cuotas'      => 12,
                'sin_interes' => true,
                'tarjetas'    => ['visa', 'mastercard'],
            ],
        ];
    }

    public function render(): void {
        global $product;
        if (!$product || !$product->get_price()) return;

        $price  = (float) $product->get_price();
        $planes = self::get_planes();
        if (empty($planes)) return;

        echo '<div class="wc-cuotas-wrapper">';
        foreach ($planes as $plan) {
            $descuento    = (float) ($plan['descuento'] ?? 0);
            $cuotas       = max(1, (int) ($plan['cuotas'] ?? 1));
            $precio_final = $price * (1 - $descuento / 100);
            $por_cuota    = $precio_final / $cuotas;

            echo '<div class="wc-cuota-row">';
            echo '<span class="wc-cuota-label">' . esc_html($plan['label']) . ':</span>';
            echo '<span class="wc-cuota-precio">' . wc_price($por_cuota) . '</span>';
            echo '<span class="wc-cuota-tarjetas">';
            foreach ((array) ($plan['tarjetas'] ?? []) as $key) {
                $t = self::TARJETAS[$key] ?? null;
                if (!$t) continue;
                printf(
                    '<span class="wc-card" style="background:%s;color:%s">%s</span>',
                    esc_attr($t['color']),
                    esc_attr($t['text']),
                    esc_html($t['label'])
                );
            }
            echo '</span>';
            echo '</div>';
        }
        echo '</div>';
    }
}
