<?php
/**
 * Social network authentication
 *
 * @package woodmart
 */

namespace WOODMART_CORE\Inc;

use Opauth;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

define( 'WOODMART_PT_3D', plugin_dir_path( __DIR__ ) );

/**
 * Social network authentication class.
 */
class Auth {
	/**
	 * Current URL.
	 *
	 * @var string
	 */
	private $current_url;

	/**
	 * Available networks.
	 *
	 * @var array
	 */
	private $available_networks = array( 'facebook', 'google' );

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( isset( $_SERVER['HTTP_HOST'] ) && isset( $_SERVER['REQUEST_URI'] ) ) {
			$scheme = ( ! empty( $_SERVER['HTTPS'] ) && 'off' !== $_SERVER['HTTPS'] )
				? 'https'
				: 'http';

			$this->current_url = $scheme . '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}"; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidatedNotSanitized
		}

		add_action( 'init', array( $this, 'auth' ), 20 );
		add_action( 'init', array( $this, 'process_auth_callback' ), 30 );
		add_action( 'init', array( $this, 'remove_captcha' ), -10 );

		add_action( 'woocommerce_save_account_details', array( $this, 'set_user_full_name_changed' ), 10, 1 );
	}

	/**
	 * Remove captcha.
	 */
	public function remove_captcha() {
		add_filter(
			'anr_get_option',
			function ( $option_values, $option ) {
				if ( is_array( $option_values ) && 'enabled_forms' === $option ) {
					foreach ( $option_values as $key => $value ) {
						if ( ( 'registration' === $value || 'login' === $value ) && isset( $_GET['opauth'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
							unset( $option_values[ $key ] );
						}
					}
				}
				return $option_values;
			},
			10000000,
			2
		);
	}

	/**
	 * Auth.
	 */
	public function auth() {
		if ( empty( $_GET['social_auth'] ) && empty( $_GET['code'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		$network = ( empty( $_GET['social_auth'] ) ) ? $this->get_current_callback_network() : sanitize_key( $_GET['social_auth'] ); // phpcs:ignore WordPress.Security

		if ( ! in_array( $network, $this->available_networks, true ) ) {
			return;
		}

		$config = $this->get_config( $network );

		if ( empty( $config ) || empty( $config['Strategy'] ) ) {
			return;
		}

		new Opauth( $config );
	}

	/**
	 * Process auth callback.
	 */
	public function process_auth_callback() {
		if ( isset( $_GET['error_reason'] ) && 'user_denied' === $_GET['error_reason'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			wp_safe_redirect( $this->get_account_url() );
			exit;
		}

		if ( empty( $_GET['opauth'] ) || is_user_logged_in() ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		$response = json_decode( base64_decode( $_GET['opauth'] ), true ); // phpcs:ignore WordPress.Security, WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode

		if (
			empty( $response['auth'] ) ||
			empty( $response['auth']['uid'] ) ||
			empty( $response['timestamp'] ) ||
			empty( $response['signature'] ) ||
			empty( $response['auth']['provider'] )
		) {
			wp_safe_redirect( $this->get_account_url() );
			exit;
		}

		$config = $this->get_config( strtolower( $response['auth']['provider'] ) );

		if ( empty( $config ) || empty( $config['Strategy'] ) ) {
			return;
		}

		$opauth = new Opauth( $config, false );

		$reason = '';

		if ( ! $opauth->validate( sha1( print_r( $response['auth'], true ) ), $response['timestamp'], $response['signature'], $reason ) ) { // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			wp_safe_redirect( $this->get_account_url() );
			exit;
		}

		switch ( $response['auth']['provider'] ) {
			case 'Facebook':
				if ( empty( $response['auth']['info'] ) ) {
					wc_add_notice( __( 'Can\'t login with Facebook. Please, try again later.', 'woodmart' ), 'error' );
					return;
				}

				$email = isset( $response['auth']['info']['email'] ) ? $response['auth']['info']['email'] : '';

				if ( ! $email ) {
					wc_add_notice( __( 'Facebook doesn\'t provide your email. Try to register manually.', 'woodmart' ), 'error' );
					return;
				}

				$this->register_or_login( $response );
				break;
			case 'Google':
				if ( empty( $response['auth']['info'] ) ) {
					wc_add_notice( __( 'Can\'t login with Google. Please, try again later.', 'woodmart' ), 'error' );
					return;
				}

				$email = isset( $response['auth']['info']['email'] ) ? $response['auth']['info']['email'] : '';

				if ( ! $email ) {
					wc_add_notice( __( 'Google doesn\'t provide your email. Try to register manually.', 'woodmart' ), 'error' );
					return;
				}

				$this->register_or_login( $response );
				break;
			default:
				break;
		}
	}

	/**
	 * Set user full name changed.
	 *
	 * @param int $user_id User ID.
	 */
	public function set_user_full_name_changed( $user_id ) {
		$user = get_userdata( $user_id );

		if ( $user ) {
			$first_name = isset( $_POST['account_first_name'] ) ? sanitize_text_field( $_POST['account_first_name'] ) : ''; // phpcs:ignore WordPress.Security
			$last_name  = isset( $_POST['account_last_name'] ) ? sanitize_text_field( $_POST['account_last_name'] ) : ''; // phpcs:ignore WordPress.Security

			if ( ! empty( $first_name ) || ! empty( $last_name ) ) {
				update_user_meta( $user_id, 'wd_user_full_name_changed', true );
			}
		}
	}

	/**
	 * Register or login user.
	 *
	 * @param array $response Response.
	 */
	public function register_or_login( $response ) {
		$email = isset( $response['auth']['info']['email'] ) ? $response['auth']['info']['email'] : '';
		$args  = array(
			'first_name' => isset( $response['auth']['info']['first_name'] ) ? $response['auth']['info']['first_name'] : '',
			'last_name'  => isset( $response['auth']['info']['last_name'] ) ? $response['auth']['info']['last_name'] : '',
		);

		add_filter( 'pre_option_woocommerce_registration_generate_username', array( $this, 'return_yes' ), 10 );
		add_filter( 'dokan_register_nonce_check', '__return_false' );

		if ( function_exists( 'dokan_get_container' ) ) {
			$registration_instance = dokan_get_container()->get( 'registration' );

			remove_filter( 'woocommerce_process_registration_errors', array( $registration_instance, 'validate_registration' ) );
			remove_filter( 'woocommerce_registration_errors', array( $registration_instance, 'validate_registration' ) );
		}

		$password = wp_generate_password();
		$customer = wc_create_new_customer( $email, '', $password, $args );
		$user     = get_user_by( 'email', $email );

		if ( is_wp_error( $customer ) ) {
			if ( isset( $customer->errors['registration-error-email-exists'] ) && $user && ! is_wp_error( $user ) ) {
				$this->update_user_from_social( $user->ID, $response );
				wc_set_customer_auth_cookie( $user->ID );
			}
		} else {
			$user = get_user_by( 'id', $customer );
			wc_set_customer_auth_cookie( $customer );
		}

		// translators: %s is the user display name.
		wc_add_notice( sprintf( __( 'You are now logged in as <strong>%s</strong>', 'woodmart' ), $user->display_name ) );

		remove_filter( 'pre_option_woocommerce_registration_generate_username', array( $this, 'return_yes' ), 10 );
	}

	/**
	 * Update user data from social network.
	 *
	 * @param int   $user_id User ID.
	 * @param array $response OAuth response.
	 */
	private function update_user_from_social( $user_id, $response ) {
		$name_changed_manually = get_user_meta( $user_id, 'wd_user_full_name_changed', true );

		if ( ! $name_changed_manually ) {
			$first_name = isset( $response['auth']['info']['first_name'] ) ? $response['auth']['info']['first_name'] : '';
			$last_name  = isset( $response['auth']['info']['last_name'] ) ? $response['auth']['info']['last_name'] : '';

			if ( ! empty( $first_name ) ) {
				update_user_meta( $user_id, 'first_name', $first_name );
			}

			if ( ! empty( $last_name ) ) {
				update_user_meta( $user_id, 'last_name', $last_name );
			}
		}
	}

	/**
	 * Get current callback network.
	 *
	 * @return string|false
	 */
	public function get_current_callback_network() {
		$account_url = $this->get_account_url();

		foreach ( $this->available_networks as $network ) {
			if ( strstr( $this->current_url, trailingslashit( $account_url ) . $network ) ) {
				return $network;
			}
		}

		return false;
	}

	/**
	 * Get account URL.
	 *
	 * @return string
	 */
	public function get_account_url() {
		if ( function_exists( 'wc_get_page_permalink' ) ) {
			return untrailingslashit( wc_get_page_permalink( 'myaccount' ) );
		}

		return '';
	}

	/**
	 * Return yes.
	 *
	 * @return string
	 */
	public function return_yes() {
		return 'yes';
	}

	/**
	 * Get config.
	 *
	 * @param string $network Network.
	 * @return array
	 */
	private function get_config( $network ) {
		$callback_param = 'int_callback';
		$security_salt  = apply_filters( 'woodmart_opauth_salt', '2NlBUibcszrVtNmDnxqDbwCOpLWq91eatIz6O1O' );

		if ( defined( 'SECURE_AUTH_SALT' ) ) {
			$security_salt = SECURE_AUTH_SALT;
		}

		switch ( $network ) {
			case 'google':
				$app_id     = woodmart_get_opt( 'goo_app_id' );
				$app_secret = woodmart_get_opt( 'goo_app_secret' );

				if ( empty( $app_secret ) || empty( $app_id ) ) {
					return array();
				}

				$strategy = array(
					'Google' => array(
						'client_id'     => $app_id,
						'client_secret' => $app_secret,
					),
				);

				$callback_param = 'oauth2callback';

				break;
			default:
				$app_id     = woodmart_get_opt( 'fb_app_id' );
				$app_secret = woodmart_get_opt( 'fb_app_secret' );

				if ( empty( $app_secret ) || empty( $app_id ) ) {
					return array();
				}

				$strategy = array(
					'Facebook' => array(
						'app_id'     => $app_id,
						'app_secret' => $app_secret,
						'scope'      => 'email',
						'fields'     => 'email,name,first_name,last_name',
					),
				);
				break;
		}

		$callback_url = $this->get_account_url();

		if ( function_exists( 'woodmart_set_cookie' ) && function_exists( 'wc_get_page_permalink' ) ) {
			if ( ! empty( $_COOKIE['wd_social_auth'] ) ) {
				$callback_url = $_COOKIE['wd_social_auth']; // phpcs:ignore WordPress.Security
				woodmart_set_cookie( 'wd_social_auth', null );
			} elseif ( ! empty( $_GET['is_checkout'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$callback_url = untrailingslashit( wc_get_page_permalink( 'checkout' ) );
				woodmart_set_cookie( 'wd_social_auth', $callback_url );
			}
		}

		$account_url = $this->get_account_url();
		$config      = array(
			'security_salt'      => $security_salt,
			'host'               => $account_url,
			'path'               => '/',
			'callback_url'       => $callback_url,
			'callback_transport' => 'get',
			'strategy_dir'       => WOODMART_PT_3D . '/vendor/opauth/',
			'Strategy'           => $strategy,
		);

		if ( empty( $_GET['code'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$config['request_uri'] = '/' . $network;
		} else {
			$config['request_uri'] = '/' . $network . '/' . $callback_param . '?code=' . $_GET['code']; // phpcs:ignore WordPress.Security
		}

		return $config;
	}
}

new Auth();
