<?php
/**
 * Estimate delivery class.
 *
 * @package woodmart
 */

namespace XTS\Modules\Estimate_Delivery;

use DateTime;

/**
 * Estimate delivery class.
 */
class Overall_Delivery_Date {
	/**
	 * List of WC_Product class instances.
	 *
	 * @var WC_Product
	 */
	public $products;

	/**
	 * Shipping method id for calculate date on admin panel.
	 *
	 * @var int|false
	 */
	public $shipping_method_id;

	/**
	 * If set, the delivery date will be calculated from this day.
	 *
	 * @var string|false
	 */
	public $start_date;

	/**
	 * Manager instance.
	 *
	 * @var Manager instance.
	 */
	public $manager;

	/**
	 * Constructor.
	 *
	 * @param WC_Product[] $products List of WC_Product class instances.
	 * @param int|false    $shipping_method_id Shipping method id for calculate date on admin panel.
	 * @param int|false    $start_date Date of order.
	 *
	 * @return void
	 */
	public function __construct( $products, $shipping_method_id = false, $start_date = false ) {
		if ( ! woodmart_get_opt( 'estimate_delivery_enabled' ) || ! woodmart_woocommerce_installed() ) {
			return;
		}

		$this->products           = $products;
		$this->shipping_method_id = $shipping_method_id;
		$this->start_date         = $start_date;
		$this->manager            = Manager::get_instance();
	}

	/**
	 * Get a raw overall delivery date array. Example: array( 'min' => 'Oct 2, 2024', 'max' => 'Oct 4, 2024' ).
	 *
	 * @return array
	 */
	public function get_overall() {
		$date_format = woodmart_get_opt( 'estimate_delivery_date_format', 'M j, Y' );
		$date_format = 'default' === $date_format ? get_option( 'date_format' ) : $date_format;
		$date_format = apply_filters( 'woodmart_est_del_date_format', $date_format );
		$overall     = array();

		$min_delivery_days = 0;
		$max_delivery_days = 0;

		foreach ( $this->products as $key => $product ) {
			$delivery_date = new Delivery_Date( $product, $this->shipping_method_id, $this->start_date );
			$min           = $delivery_date->get_rule_meta_box( 'est_del_day_min' );
			$max           = $delivery_date->get_rule_meta_box( 'est_del_day_max' );
			$skipped_dates = $delivery_date->get_all_skipped_dates();

			if ( false === $min || false === $max || false === $skipped_dates ) {
				continue;
			}

			$current_min = $delivery_date->get_date_after( $min, $skipped_dates );
			$current_max = $delivery_date->get_date_after( $max, $skipped_dates );

			if ( $current_min > $min_delivery_days ) {
				$min_delivery_days = $current_min;
			}

			if ( $current_max > $max_delivery_days ) {
				$max_delivery_days = $current_max;
			}

			$overall = array(
				'min' => $min_delivery_days,
				'max' => $max_delivery_days,
			);
		}

		if ( empty( $overall ) ) {
			return array();
		}

		$min                      = 0 !== $overall['min'] ? $overall['min'] : time();
		$overall['min_timestamp'] = $min;
		$overall['min']           = wp_date( $date_format, $min );

		if ( 0 !== $overall['max'] ) {
			$overall['max_timestamp'] = $overall['max'];
			$overall['max']           = wp_date( $date_format, $overall['max'] );
		}

		return $overall;
	}

	/**
	 * Get delivery text string.
	 * Example: 'Overall estimated delivery dates'.
	 *
	 * @return string
	 */
	public function get_label() {
		$overall = $this->get_overall();

		if ( empty( $overall ) ) {
			return '';
		}

		return esc_html__( 'Overall estimated delivery', 'woodmart' );
	}

	/**
	 * Get overall product date string.
	 *
	 * @return string
	 */
	public function get_date() {
		$overall = $this->get_overall();

		if ( empty( $overall ) ) {
			return '';
		}

		if ( 'days' === woodmart_get_opt( 'estimate_delivery_display_format' ) ) {
			return $this->get_days_count( $overall );
		} else {
			return $this->get_specific_dates( $overall );
		}
	}

	/**
	 * Get overall delivery time as specific dates. Example: 'Oct 2, 2024 - Oct 4, 2024'.
	 *
	 * @param array $overall Raw overall delivery date array.
	 * @return string
	 */
	public function get_specific_dates( $overall ) {
		$single_date = self::is_single_date( $overall );
		$format      = '%s';

		if ( ! $single_date ) {
			$format .= apply_filters( 'woodmart_dates_separator', ' – ' );
			$format .= '%s';
		}

		return sprintf(
			$format,
			$overall['min'],
			! $single_date ? $overall['max'] : null
		);
	}

	/**
	 * Get overall delivery time as days count. Example: '3-8 days'.
	 *
	 * @param array $overall Raw overall delivery date array.
	 * @return string
	 */
	public function get_days_count( $overall ) {
		if ( ! empty( $this->start_date ) ) {
			$start_date_time_obj = new DateTime( $this->start_date );
			$current_time        = $start_date_time_obj->getTimestamp();
		} else {
			$current_time = wp_date( 'U' );
		}

		$single_date = self::is_single_date( $overall );
		$min_days    = max( 1, ceil( ( $overall['min_timestamp'] - $current_time ) / DAY_IN_SECONDS ) );

		if ( ! $single_date ) {
			$format   = '%s';
			$format  .= apply_filters( 'woodmart_dates_separator', ' – ' );
			$format  .= '%s %s';
			$max_days = max( 1, ceil( ( $overall['max_timestamp'] - $current_time ) / DAY_IN_SECONDS ) );

			return sprintf(
				$format,
				$min_days,
				$max_days,
				esc_html__( 'days', 'woodmart' )
			);
		} else {
			return $min_days . ' ' . _n( 'day', 'days', $min_days, 'woodmart' );
		}
	}

	/**
	 * Get a ready overall delivery date string. Example: 'Overall estimated delivery dates: Oct 2, 2024 - Oct 4, 2024'.
	 *
	 * @return string
	 */
	public function get_date_string() {
		$text = $this->get_label();
		$date = $this->get_date();

		$date_string = '';

		if ( ! empty( $text ) ) {
			$date_string = '<strong>' . $text . ':</strong> ';
		}

		if ( ! empty( $date ) ) {
			$date_string .= $date;
		}

		return $date_string;
	}

	/**
	 * Get a ready overall delivery date array.
	 *
	 * @return array
	 */
	public function get_date_array() {
		$text = $this->get_label();
		$date = $this->get_date();

		if ( empty( $date ) ) {
			return array();
		}

		return array(
			'label' => $text ? $text . ': ' : '',
			'value' => $date,
		);
	}

	/**
	 * Check or show one delivery date.
	 *
	 * @param array $overall Raw overall delivery date array.
	 *
	 * @return bool
	 */
	public static function is_single_date( $overall ) {
		return empty( $overall['max_timestamp'] ) || $overall['min_timestamp'] > $overall['max_timestamp'] || $overall['min_timestamp'] === $overall['max_timestamp'];
	}
}
