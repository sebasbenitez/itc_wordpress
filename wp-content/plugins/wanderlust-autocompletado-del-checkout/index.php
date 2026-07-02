<?php
/*
	Plugin Name: Wanderlust - Autocompletar dirección de Facturación 
	Plugin URI: https://shop.wanderlust-webdesign.com/
	Description: Este plugin te permite autocompletar los datos del checkout.
	Version: 0.8
	Author: Wanderlust Web Design
	Author URI: https://wanderlust-webdesign.com
	WC tested up to: 9.6.0
	Copyright: 2007-2025 wanderlust-webdesign.com.
	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 
 add_action( 'before_woocommerce_init', function() {
          if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
                  \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
          }
  } );

 
 add_action('wp_ajax_wanderlust_get_customer_data', 'wanderlust_get_customer_data', 1);
 add_action('wp_ajax_nopriv_wanderlust_get_customer_data', 'wanderlust_get_customer_data', 1);  

 function wanderlust_get_customer_data(){
    global $woocommerce;
    if (isset($_POST['cuit'])) {
			$params = array(
						"method" => array(
								 "consultar" => array(
												'CUIT' => $_POST['cuit'],
								 )
						)
			);			
 
      $ch = curl_init();
      curl_setopt_array($ch,	
        array(	
          CURLOPT_TIMEOUT	=> 30,
          CURLOPT_POST => TRUE,
          CURLOPT_POSTFIELDS => http_build_query($params),
          CURLOPT_URL => 'https://afip.dev/get_cuit.php',
          CURLOPT_RETURNTRANSFER => TRUE,
          CURLOPT_FOLLOWLOCATION	=> TRUE
        )
      );

      $afip_response = curl_exec ($ch);		
      $afip_response = json_decode($afip_response);
   
      if(!empty($afip_response->result) && $afip_response->result != '0'  ){
           
        $resultado = array(
          'nombre' => $afip_response->result->nombre,
          'apellido' => $afip_response->result->apellido,
          'empresa' => $afip_response->result->empresa,
          'billing_address_1' => $afip_response->result->billing_address_1,
          'billing_city' => $afip_response->result->billing_city,
          'billing_state' => $afip_response->result->billing_state,
          'billing_state_text' => $afip_response->result->billing_state_text,
          'billing_postcode' => $afip_response->result->billing_postcode,
          'alicuota' => $afip_response->result->alicuota,
        );
        $resultado = json_encode($resultado);
        echo $resultado; 
 
      } else {
  
        echo 0;
          
      }
    }
    die();    

 }

class WC_PaymentGateway_validar_checkout_std_Charges{
  public function __construct(){
    $this->load_dni();
  
    add_action( 'wp_enqueue_scripts',array($this,'load_my_script'));
   }

 

  function load_my_script(){
    wp_enqueue_script( 'wc-add-extra-charges', $this->plugin_url() . '/fee.js', array('wc-checkout'), false, true );
  }

  function load_dni() {
    require_once( 'class-dni.php' );
  }    
  
  public function plugin_url() {
    return $this->plugin_url = untrailingslashit( plugins_url( '/', __FILE__ ) );
   }

 
  public function plugin_path() {
    if ( $this->plugin_path ) return $this->plugin_path;

    return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
  }

}
new WC_PaymentGateway_validar_checkout_std_Charges();