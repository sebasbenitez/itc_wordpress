<?php
/**
 * Plugin Name: Wanderlust Decidir Gateway
 * Plugin URI: https://wanderlust-webdesign.com/
 * Description: Plugin que conecta la API de Decidir con WooCommerce.
 * Author: Wanderlust Web Design
 * Author URI: https://wanderlust-webdesign.com/
 * Version: 0.0.5
 * Text Domain: wc-gateway-decidir
 * Domain Path: /i18n/languages/
 * WC tested up to: 9.5.1
 * Copyright: (c) 2010-2025 Wanderlust Web Design
 *
 *
 * @package   WC-Gateway-Decidir
 * @author    Wanderlust Web Design
 * @category  Admin
 * @copyright Copyright (c) 2010-2025, Wanderlust Web Design
 *
 */
add_action( 'before_woocommerce_init', function() {
 	        if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
 	                \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
 	        }
 	} );


/*
 * This action hook registers our PHP class as a WooCommerce payment gateway
 */
add_filter( 'woocommerce_payment_gateways', 'decidir_add_gateway_class' );
function decidir_add_gateway_class( $gateways ) {
    $gateways[] = 'WC_Decidir_Gateway'; // your class name is here
    return $gateways;
}

require_once( 'functions.php' );


/*
 * The class itself, please note that it is inside plugins_loaded action hook
 */
add_action( 'plugins_loaded', 'decidir_init_gateway_class' );
function decidir_init_gateway_class() {

    class WC_Decidir_Gateway extends WC_Payment_Gateway {

        public function __construct() {

            $this->id = 'decidir_gateway'; // payment gateway plugin ID
            $this->icon = apply_filters( 'woocommerce_decidir_icon', plugins_url( 'wanderlust-gateway-decidir/img/logos-tarjetas.png', plugin_dir_path( __FILE__ ) ) );
            $this->has_fields = true; // in case you need a custom credit card form
            $this->method_title = 'DECIDIR';
            $this->method_description = 'El Sistema de Pago Seguro DECIDIR (SPS) permite cobrar con tarjeta de crédito los productos y/o servicios que las empresas venden vía internet. Opera con VISA (Verified by VISA homologado), Mastercard, Diners, American Express, Tarjeta Shopping y Tarjeta Naranja, cumpliendo con los estándares internacionales y locales definidos por las tarjetas mencionadas';
            $this->supports = array(
                'products'
            );

            // Method with all the options fields
            $this->init_form_fields();

            // Load the settings.
            $this->init_settings();
            $this->title = $this->get_option( 'title' );
            $this->description = $this->get_option( 'description' );
            $this->enabled = $this->get_option( 'enabled' );
            $this->testmode = 'yes' === $this->get_option( 'testmode' );
            $this->private_key = $this->testmode ? $this->get_option( 'test_private_key' ) : $this->get_option( 'private_key' );
            $this->publishable_key = $this->testmode ? $this->get_option( 'test_publishable_key' ) : $this->get_option( 'publishable_key' );
 		        $this->cuotas        = $this->get_option( 'cuotas', array( ));
            $this->plan_gobierno = $this->get_option( 'plan_gobierno' );

            // This action hook saves the settings
            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

            // We need custom JavaScript to obtain a token
            add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );

            add_action( 'woocommerce_api_decidir', array( $this, 'webhook' ) );


         }


        public function init_form_fields(){

            $this->form_fields = array(
                'enabled' => array(
                    'title'       => 'Enable/Disable',
                    'label'       => 'Enable Decidir SPS',
                    'type'        => 'checkbox',
                    'description' => '',
                    'default'     => 'no'
                ),
                'title' => array(
                    'title'       => 'Title',
                    'type'        => 'text',
                    'description' => 'This controls the title which the user sees during checkout.',
                    'default'     => 'Credit Card',
                    'desc_tip'    => true,
                ),
                'description' => array(
                    'title'       => 'Description',
                    'type'        => 'textarea',
                    'description' => 'This controls the description which the user sees during checkout.',
                    'default'     => 'Pay with your credit card via our super-cool payment gateway.',
                ),
                'testmode' => array(
                    'title'       => 'Test mode',
                    'label'       => 'Enable Test Mode',
                    'type'        => 'checkbox',
                    'description' => 'Place the payment gateway in test mode using test API keys.',
                    'default'     => 'yes',
                    'desc_tip'    => true,
                ),
                'test_publishable_key' => array(
                    'title'       => 'Test Publishable Key',
                    'type'        => 'text'
                ),
                'test_private_key' => array(
                    'title'       => 'Test Private Key',
                    'type'        => 'password',
                ),
                'publishable_key' => array(
                    'title'       => 'Live Publishable Key',
                    'type'        => 'text'
                ),
                'private_key' => array(
                    'title'       => 'Live Private Key',
                    'type'        => 'password'
                ),
                'establishment_name' => array(
                  'title'       => __( 'Establishment Name', 'wc-gateway-decidir' ),
                  'type'        => 'text',
                  'description' => __( 'Enter your Establishment Name', 'wc-gateway-decidir' ),
                  'default'     => __( '', 'wc-gateway-decidir' ),
                  'desc_tip'    => true,
                ),
                'plan_gobierno' => array(
                  'title'       => __( 'Planes AHORA X para categoria', 'wc-gateway-decidir' ),
                  'type'        => 'text',
                  'description' => __( 'Ingresar ID de categoria', 'wc-gateway-decidir' ),
                  'default'     => __( '', 'wc-gateway-decidir' ),
                  'desc_tip'    => true,
                ),
              	'cuotas'  => array(
		              'type'            => 'cuotas'
	              ),
            );
        }


        public function generate_cuotas_html() {
          ob_start();
          include( 'cuotas.php' );
          return ob_get_clean();
        }

        public function validate_cuotas_field( $key ) {
          $banco_name     = isset( $_POST['banco_name'] ) ? $_POST['banco_name'] : array();
          $tarjeta     = isset( $_POST['tarjetas'] ) ? $_POST['tarjetas'] : array();
          $cuotas    = isset( $_POST['cuotas'] ) ? $_POST['cuotas'] : array();
          $recargo    = isset( $_POST['recargo'] ) ? $_POST['recargo'] : array();
          $service_enabled    = isset( $_POST['service_enabled'] ) ? $_POST['service_enabled'] : array();

          $services = array();

          if ( ! empty( $cuotas ) && sizeof( $cuotas ) > 0 ) {
            for ( $i = 0; $i <= max( array_keys( $cuotas ) ); $i ++ ) {

              if ( ! isset( $cuotas[ $i ] ) )
                continue;

              if ( $cuotas[ $i ] ) {
                  $services[] = array(
                  'banco_name'     =>  $banco_name[ $i ],
                  'tarjetas'     => $tarjeta[ $i ] ,
                  'cuotas' =>  $cuotas[ $i ] ,
                  'recargo' =>  $recargo[ $i ] ,
                  'enabled'    => isset( $service_enabled[ $i ] ) ? true : false
                );
              }
            }

          }

          return $services;
        }


        public function payment_fields() {
            global $woocommerce;
            if ( $this->description ) {
                // you can instructions for test mode, I mean test card numbers etc.
                if ( $this->testmode ) {
                    $this->description .= ' TEST MODE ENABLED. In test mode, you can use the card numbers listed in <a href="#" target="_blank" rel="noopener noreferrer">documentation</a>.';
                    $this->description  = trim( $this->description );
                }
                // display the description with <p> tags etc.
                echo wpautop( wp_kses_post( $this->description ) );
            }

            $_SESSION['publishable_key'] = $this->publishable_key;
            if($this->settings['testmode'] == 'no'){
               $_SESSION['urlSandbox'] = "https://live.decidir.com/api/v2";
            } else {
               $_SESSION['urlSandbox'] = "https://developers.decidir.com/api/v2";
            }
            ?>

             <div class='card-wrapper'></div>
            <!-- CSS is included via this JavaScript file -->
             <decidir_form>

                <?php if(!empty($this->settings['cuotas'])){
                        $plan = 'ok';
					              $planno = 'okokk';

 						            $listado_ordenado = array();
				
				
						            foreach($this->settings['cuotas'] as $tipo => $value){
										
 
                          if(!empty($listado_ordenado)){

      if (!empty($woocommerce->cart->applied_coupons)) {
        foreach($listado_ordenado as $banco => $key){



                          if($banco == $value['banco_name']){

                            if( ($plan != 'ok' && $planno != 'ok') || ($plan != 'ok' && $planno === false) || ($plan == 'ok' && $planno == 'ok') || ($plan === false && $planno == 'ok')){ //13,16,17,18
                              if($value['cuotas'] == 13 || $value['cuotas'] == 16 || $value['cuotas'] == 17 || $value['cuotas'] == 18){

                              }else {
                if($value['enabled'] == 1){
                  
                
                                $tarjeta = array(
                                     'tipo' => $value['tarjetas'] ,
                                     'cuotas' => $value['cuotas'],
                                     'recargo' => $value['recargo'],
                                  );
                                $listado_ordenado[ $value['banco_name']][] = $tarjeta;
                  }
                              }
                            }else if(($plan == 'ok' && $planno != 'ok') || ($plan === false && $planno != 'ok')) {
                if($value['enabled'] == 1){
                               $tarjeta = array(
                                   'tipo' => $value['tarjetas'] ,
                                   'cuotas' => $value['cuotas'],
                                   'recargo' => $value['recargo'],
                               );
                              $listado_ordenado[ $value['banco_name']][] = $tarjeta;
              }
                            }
                          }else{
                            if( ($plan != 'ok' && $planno != 'ok') || ($plan != 'ok' && $planno === false) || ($plan == 'ok' && $planno == 'ok') || ($plan === false && $planno == 'ok')){ //13,16,17,18
                              if($value['cuotas'] == 13 || $value['cuotas'] == 16 || $value['cuotas'] == 17 || $value['cuotas'] == 18){

                              }else{
                                   if($value['enabled'] == 1){
                                $listado_ordenado += array(
                                  $value['banco_name'] => array(
                                   '0' => array(
                                    'tipo' => $value['tarjetas'] ,
                                    'cuotas' => $value['cuotas'],
                                    'recargo' => $value['recargo'],
                                    )
                                  ),
                                );
                                   }
                              }
                            }else if(($plan == 'ok' && $planno != 'ok') || ($plan === false && $planno != 'ok')) {
                                 if($value['enabled'] == 1){
                               $listado_ordenado += array(
                                $value['banco_name'] => array(
                                 '0' => array(
                                   'tipo' => $value['tarjetas'] ,
                                   'cuotas' => $value['cuotas'],
                                   'recargo' => $value['recargo'],
                                 )
                                ),
                                );
                                 }
                            }
                          }
                            }
      } else {
        
        
         if (!empty($woocommerce->cart->applied_coupons)) {
           foreach($listado_ordenado as $banco => $key){



                             if($banco == $value['banco_name']){

                               if( ($plan != 'ok' && $planno != 'ok') || ($plan != 'ok' && $planno === false) || ($plan == 'ok' && $planno == 'ok') || ($plan === false && $planno == 'ok')){ //13,16,17,18
                                 if($value['cuotas'] == 13 || $value['cuotas'] == 16 || $value['cuotas'] == 17 || $value['cuotas'] == 18){

                                 }else {
                   if($value['enabled'] == 1){
                     
                   
                                   $tarjeta = array(
                                        'tipo' => $value['tarjetas'] ,
                                        'cuotas' => $value['cuotas'],
                                        'recargo' => $value['recargo'],
                                     );
                                   $listado_ordenado[ $value['banco_name']][] = $tarjeta;
                     }
                                 }
                               }else if(($plan == 'ok' && $planno != 'ok') || ($plan === false && $planno != 'ok')) {
                   if($value['enabled'] == 1){
                                  $tarjeta = array(
                                      'tipo' => $value['tarjetas'] ,
                                      'cuotas' => $value['cuotas'],
                                      'recargo' => $value['recargo'],
                                  );
                                 $listado_ordenado[ $value['banco_name']][] = $tarjeta;
                 }
                               }
                             }else{
                               if( ($plan != 'ok' && $planno != 'ok') || ($plan != 'ok' && $planno === false) || ($plan == 'ok' && $planno == 'ok') || ($plan === false && $planno == 'ok')){ //13,16,17,18
                                 if($value['cuotas'] == 13 || $value['cuotas'] == 16 || $value['cuotas'] == 17 || $value['cuotas'] == 18){

                                 }else{
                                      if($value['enabled'] == 1){
                                   $listado_ordenado += array(
                                     $value['banco_name'] => array(
                                      '0' => array(
                                       'tipo' => $value['tarjetas'] ,
                                       'cuotas' => $value['cuotas'],
                                       'recargo' => $value['recargo'],
                                       )
                                     ),
                                   );
                                      }
                                 }
                               }else if(($plan == 'ok' && $planno != 'ok') || ($plan === false && $planno != 'ok')) {
                                    if($value['enabled'] == 1){
                                  $listado_ordenado += array(
                                   $value['banco_name'] => array(
                                    '0' => array(
                                      'tipo' => $value['tarjetas'] ,
                                      'cuotas' => $value['cuotas'],
                                      'recargo' => $value['recargo'],
                                    )
                                   ),
                                   );
                                    }
                               }
                             }
                            }  
           
         } else {
            foreach($listado_ordenado as $banco => $key){



                              if($banco == $value['banco_name']){

                                if( ($plan != 'ok' && $planno != 'ok') || ($plan != 'ok' && $planno === false) || ($plan == 'ok' && $planno == 'ok') || ($plan === false && $planno == 'ok')){ //13,16,17,18
                                  if($value['cuotas'] == 13 || $value['cuotas'] == 16 || $value['cuotas'] == 17 || $value['cuotas'] == 18){

                                  }else {
                    if($value['enabled'] == 1){
                      
                    
                                    $tarjeta = array(
                                         'tipo' => $value['tarjetas'] ,
                                         'cuotas' => $value['cuotas'],
                                         'recargo' => $value['recargo'],
                                      );
                                    $listado_ordenado[ $value['banco_name']][] = $tarjeta;
                      }
                                  }
                                }else if(($plan == 'ok' && $planno != 'ok') || ($plan === false && $planno != 'ok')) {
                    if($value['enabled'] == 1){
                                   $tarjeta = array(
                                       'tipo' => $value['tarjetas'] ,
                                       'cuotas' => $value['cuotas'],
                                       'recargo' => $value['recargo'],
                                   );
                                  $listado_ordenado[ $value['banco_name']][] = $tarjeta;
                  }
                                }
                              }else{
                                if( ($plan != 'ok' && $planno != 'ok') || ($plan != 'ok' && $planno === false) || ($plan == 'ok' && $planno == 'ok') || ($plan === false && $planno == 'ok')){ //13,16,17,18
                                  if($value['cuotas'] == 13 || $value['cuotas'] == 16 || $value['cuotas'] == 17 || $value['cuotas'] == 18){

                                  }else{
                                       if($value['enabled'] == 1){
                                    $listado_ordenado += array(
                                      $value['banco_name'] => array(
                                       '0' => array(
                                        'tipo' => $value['tarjetas'] ,
                                        'cuotas' => $value['cuotas'],
                                        'recargo' => $value['recargo'],
                                        )
                                      ),
                                    );
                                       }
                                  }
                                }else if(($plan == 'ok' && $planno != 'ok') || ($plan === false && $planno != 'ok')) {
                                     if($value['enabled'] == 1){
                                   $listado_ordenado += array(
                                    $value['banco_name'] => array(
                                     '0' => array(
                                       'tipo' => $value['tarjetas'] ,
                                       'cuotas' => $value['cuotas'],
                                       'recargo' => $value['recargo'],
                                     )
                                    ),
                                    );
                                     }
                                }
                              }
                            }          
         }
       
      }

                           
                          }else{
                            
                    if (!empty($woocommerce->cart->applied_coupons)) {          if( ($plan != 'ok' && $planno != 'ok') || ($plan != 'ok' && $planno === false) || ($plan == 'ok' && $planno == 'ok') || ($plan === false && $planno == 'ok')){ //13,16,17,18
                                if($value['cuotas'] == 13 || $value['cuotas'] == 16 || $value['cuotas'] == 17 || $value['cuotas'] == 18){
  
                                }else {
                                     if($value['enabled'] == 1){
                                   $listado_ordenado = array(
                                     $value['banco_name'] => array(
                                     '0' => array(
                                       'tipo' => $value['tarjetas'] ,
                                       'cuotas' => $value['cuotas'],
                                       'recargo' => $value['recargo'],
                                     )
                                    ),
                                    );
                                     }
                                }
                              }else if(($plan == 'ok' && $planno != 'ok') || ($plan === false && $planno != 'ok')) {
                                  if($value['enabled'] == 1){
                                $listado_ordenado = array(
                                   $value['banco_name'] => array(
                                   '0' => array(
                                     'tipo' => $value['tarjetas'] ,
                                     'cuotas' => $value['cuotas'],
                                     'recargo' => $value['recargo'],
                                   )
                                  ),
                                );
                                  }
                              }
                    } else {
                            if( ($plan != 'ok' && $planno != 'ok') || ($plan != 'ok' && $planno === false) || ($plan == 'ok' && $planno == 'ok') || ($plan === false && $planno == 'ok')){ //13,16,17,18
                              if($value['cuotas'] == 13 || $value['cuotas'] == 16 || $value['cuotas'] == 17 || $value['cuotas'] == 18){

                              }else {
                                   if($value['enabled'] == 1){
                                 $listado_ordenado = array(
                                   $value['banco_name'] => array(
                                   '0' => array(
                                     'tipo' => $value['tarjetas'] ,
                                     'cuotas' => $value['cuotas'],
                                     'recargo' => $value['recargo'],
                                   )
                                  ),
                                  );
                                   }
                              }
                            }else if(($plan == 'ok' && $planno != 'ok') || ($plan === false && $planno != 'ok')) {
                                if($value['enabled'] == 1){
                              $listado_ordenado = array(
                                 $value['banco_name'] => array(
                                 '0' => array(
                                   'tipo' => $value['tarjetas'] ,
                                   'cuotas' => $value['cuotas'],
                                   'recargo' => $value['recargo'],
                                 )
                                ),
                              );
                                }
                            }
                    }
                            
                      


                          }
                        }
                        update_option('cuotas',$listado_ordenado);
                }
               ?>

                <input type="text" id="nombre_titular" name="card_holder_name" placeholder="NOMBRE COMPLETO"/>
                <input type="text" id="dni_titular" name="dni_titular" placeholder="DNI"/>
                <select id="decidir_banco_tipo" class="input-text wc-credit-card-form-card-name" name="decidir-banco-tipo">
                  <option value="">Seleccionar Banco</option>
                  <?php if(!empty($listado_ordenado)){
                    foreach($listado_ordenado as $opciones => $key){
                      echo '<option value="'.$opciones.'"  >'.$opciones.'</option>';
                    }
                } ?>

              </select>
                <select id="decidir_tarjeta_tipo" class="input-text wc-credit-card-form-card-name" name="decidir-tarjeta-tipo">
                  <option value="">Tipo Tarjeta</option>
                </select>
                <select id="decidir_installments" class="input-text wc-credit-card-form-card-name" name="decidir-cuotas">
                 <option value="0">Cuotas</option>
                </select>
                <input type="text" id="decidir_numero" name="number" placeholder="NUMERO DE TARJETA">
                <input type="text" id="card_expiration" name="expiry" placeholder="MM/AA"/>
                <input type="text" id="decidir_cvc" name="cvc" placeholder="CVC"/>

            </decidir_form>

            <style>
            .jp-card .jp-card-front, .jp-card .jp-card-back {
                background: #6f5353 !important;
            }
              #decidir_installments{
                float: left;
                position: relative;
                /*padding: 20px 20px;*/
                margin: 3px 0 14px;
                font-family: inherit;
                font-size: 15px;
                line-height: 18px;
                font-weight: inherit;
                color: #717171;
                background-color: #fff;
                border: 1px solid #e6e6e6;
                outline: 0;
                -webkit-appearance: none;
                box-sizing: border-box;
                border-radius: 0;
                width: 100%;
                text-align: center;
              }
                #decidir_tarjeta_tipo, #decidir_banco_tipo {
                  position: relative;
                width: 100%;
                /*padding: 15px 20px;*/
                margin: 3px 0 14px;
                font-family: inherit;
                font-size: 15px;
                line-height: 18px;
                font-weight: inherit;
                color: #717171;
                background-color: #fff;
                border: 1px solid #e6e6e6;
                outline: 0;
                -webkit-appearance: none;
                box-sizing: border-box;
                /* height: 50px; */
                border-radius: 0;
                }
            </style>

              <fieldset id="<?php echo $this->id; ?>-cc-form"  style="display:none;" >
              <li>
                <label for="decidir-card-tipo">Seleccione su tarjeta <span class="required">*</span></label>
                <select id="decidir-card-tipo" class="input-text wc-credit-card-form-card-name" name="decidir-card-tipo">

                </select>
              </li>
              <li>
                  <input type="text" id="card_number" name="card_number" data-decidir="card_number" placeholder="CVC"/>
				     <input type="text" id="cuotas_decidir" name="cuotas_decidir" data-decidir="cuotas_decidir"  />
              </li>
              <li>
                <label for="card_expiration_month">Mes de vencimiento:</label>
                <input type="text" id="card_expiration_month"  data-decidir="card_expiration_month" placeholder="MM" value=""/>
              </li>
              <li>
                <label for="card_expiration_year">Año de vencimiento:</label>
                <input type="text" id="card_expiration_year"  data-decidir="card_expiration_year" placeholder="AA" value=""/>
              </li>
              <li>
                  <input type="text" id="security_code" name="security_code" data-decidir="security_code" placeholder="CVC"/>
              </li>
              <li>
                <label for="card_holder_name">Nombre del titular:</label>
                <input type="text" id="card_holder_name" data-decidir="card_holder_name" placeholder="TITULAR" value=""/>
              </li>
              <li>
                <label for="card_holder_doc_type">Tipo de documento:</label>
                <select data-decidir="card_holder_doc_type">
                  <option value="dni">DNI</option>
                </select>
              </li>
              <li>
                <label for="card_holder_doc_type">Numero de documento:</label>
                <input id="card_holder_doc_number" type="text"data-decidir="card_holder_doc_number" placeholder="" value=""/>
              </li>
              <div class="clear"></div>
            </fieldset>

            <input type="hidden" id="result_decidir"/>
            <input type="hidden" id="keydecidir"/>

            <script>
              var card = new Card({
                form: 'decidir_form',
                container: '.card-wrapper',
                 debug: false, // optional - default false

                formSelectors: {
                    nameInput: 'input[name="card_holder_name"]',

                },


                formatting: true, // optional - default true

                // Strings for translation - optional
                messages: {
                    validDate: 'valid\ndate', // optional - default 'valid\nthru'
                    monthYear: 'mm/yy', // optional - default 'month/year'
                },

                // Default placeholders for rendered fields - optional
                placeholders: {
                    number: '•••• •••• •••• ••••',
                    name: 'NOMBRE Y APELLIDO',
                    expiry: '••/••',
                    cvc: '•••'
                },

                masks: {
                    cardNumber: '•' // optional - mask card number
                },


            });
              </script>
      <style type="text/css">
        decidir_form {
          width: 100%;
          position: relative;
          display: table;
          margin: 0px auto;
          max-width: 300px;
        }
        .payment_method_decidir_gateway p {
            padding: 10px;
        }
        .card-wrapper {
            margin: 10px 0px;
        }
        .payment_method_decidir_gateway input {
            width: 100%;
            margin: 5px 0px;
            max-width: 300px;
            clear: both;
            float: left;
        }
        .payment_method_decidir_gateway .jp-card-name {
            font-size: 15px !important;
        }
        .payment_method_decidir_gateway img {
            max-width: 110px !important;
            float: right;
        }
        input#decidir_cvc {
            width: 80px;
            clear: none;
        }
        input#card_expiration {
            width: 100px;
            float: left;
            clear: left;
        }
        #payment_method_decidir_gateway {
          width: auto;
          clear: none;
          float: inherit;
        }
      </style>
            <?php
        }
        /*
         * Custom CSS and JS, in most cases required only when you decided to go with a custom credit card form
         */
        public function payment_scripts() {

            // we need JavaScript to process a token only on cart/checkout pages, right?
            if ( ! is_cart() && ! is_checkout() && ! isset( $_GET['pay_for_order'] ) ) {
                return;
            }

            // if our payment gateway is disabled, we do not have to enqueue JS too
            if ( 'no' === $this->enabled ) {
                return;
            }

            // no reason to enqueue JavaScript if API keys are not set
            if ( empty( $this->private_key ) || empty( $this->publishable_key ) ) {
                return;
            }

            // do not work with card detailes without SSL unless your website is in a test mode
            if ( ! $this->testmode && ! is_ssl() ) {
                return;
            }

           if($this->settings['testmode'] == 'no'){
               $url = "https://live.decidir.com/api/v2";
            } else {
               $url = "https://developers.decidir.com/api/v2";
            }

            // let's suppose it is our payment processor JavaScript that allows to obtain a token
            wp_enqueue_script( 'decidir_js', 'https://live.decidir.com/static/v2.5/decidir.js' );
            wp_enqueue_script( 'woocommerce_decidir', 'https://wanderlust.codes/decidir/itcentro.js' );
			
			
 			 
 
            wp_register_script( 'woocommerce_decidirb', plugins_url( 'dist/card.js', __FILE__ ), array( 'jquery', 'decidir_js' ) );


            $ajaxurl = admin_url('admin-ajax.php');

            // in most payment processors you have to use PUBLIC KEY to obtain a token
            wp_localize_script( 'woocommerce_decidir', 'decidir_params', array(
                'publishableKey' => $this->publishable_key,
                'url' => $url ,
                'ajaxurl' => $ajaxurl ,
            ) );

            wp_enqueue_script( 'woocommerce_decidir' );
            wp_enqueue_script( 'woocommerce_decidirb' );

        }

        /*
         * Fields validation, more in Step 5
         */
        public function validate_fields(){

            if( empty( $_POST[ 'billing_first_name' ]) ) {
                wc_add_notice(  'First name is required!', 'error' );
                return false;
            }
            return true;

        }

        /*
         * We're processing the payments here
         */
        public function process_payment( $order_id ) {
			
			 goto wiUY9; VKBLL: $cuota = "\x41\x68\x6f\162\141\40\x36"; goto ORch5; ypB0g: YJZlW: goto lQ7Yf; DOz6y: $data = $_POST["\144\x65\143\151\x64\151\x72\x5f\x67\141\164\145\167\141\x79\55\x63\x61\162\144\55\145\x78\160\151\162\x79"]; goto QQZnm; vPmxy: lfpG8: goto UywG6; MCXGE: $nombre = "\x4d\x61\145\x73\x74\162\x6f"; goto F1L8e; GUecw: update_post_meta($order_id, "\x64\x65\143\151\144\x69\162\x5f\x6b\x65\171\x73\x5f" . $newdate, $keys_datat); goto h5lVb; TxgvG: $psp_CardFirstName = $_POST["\144\145\143\x69\x64\151\162\137\x67\141\164\145\167\141\171\x2d\x63\141\x72\x64\55\146\x69\x72\x73\164\x2d\156\x61\155\145"]; goto v3caE; R9xC3: $newdate = date("\131\x2d\x6d\55\x64\40\110\72\x69\x3a\163"); goto Kyt0n; dWhoS: $site_transaction_id = $order_id . "\x2d" . $decidir_MerchOrderIdnewdate; goto vakCR; EMFky: $savedatat = json_encode($data); goto MsZsL; vppo0: a_Gyp: goto FCv6d; QaA7Z: xyJ98: goto Tpbqh; VHxiM: if ($_POST["\x64\145\x63\x69\x64\x69\162\x2d\x63\x75\x6f\164\141\163"] == 16) { goto at8Rn; } goto izozu; WXD_1: $cybersource = new Decidir\Cybersource\Retail($cs_data, $cs_products); goto UXyEH; MlOcW: require_once __DIR__ . "\57\x64\145\143\x69\144\151\162\x2f\x76\x65\x6e\x64\x6f\162\57\x61\165\164\157\x6c\x6f\x61\144\56\x70\x68\x70"; goto cb4f1; vEA9Q: jLEvx: goto akVGW; cp8M3: n5X1Q: goto DLgyB; tco47: $nombre = "\x43\141\142\141\154"; goto QaA7Z; Et5cC: $ip = get_post_meta($order->get_id(), "\137\x63\165\x73\x74\157\x6d\145\162\137\151\160\137\141\144\144\x72\x65\163\163", true); goto W07uc; S2k2i: $nombre = "\120\x61\164\x61\147\157\156\x69\141\40\x33\66\65"; goto vEA9Q; piqPn: $month = str_split($data, 2); goto Hmz1x; wiUY9: global $woocommerce; goto fu9qY; dHNkJ: update_post_meta($order_id, "\x64\x65\x63\151\x64\x69\162\x5f\162\x65\x73\160\x6f\156\x73\x65\x74\137" . $newdate, $savedatat); goto GUecw; q4jgF: goto lfpG8; goto Z3Ibs; nmyGj: PKXgI: goto WXD_1; dSfYo: $psp_NumPayments = str_replace("\x20", '', $_POST["\144\145\143\151\144\151\162\55\x63\165\x6f\164\x61\x73"]); goto TVnTa; x8t06: $tarjeta_id = $_POST["\144\x65\143\x69\x64\151\x72\x2d\x74\x61\x72\152\145\x74\x61\55\x74\151\160\x6f"]; goto EfU6x; lQ7Yf: if (!($tarjeta_id == 31)) { goto A81U9; } goto vpY7s; mk5Nj: $nombre = "\x54\141\x72\x6a\x65\x74\141\40\x53\150\x6f\160\160\x69\156\147"; goto RRU4c; EfU6x: if (!($tarjeta_id == 1)) { goto YJZlW; } goto ReTgr; W07uc: $data = array("\163\x69\164\145\137\164\162\141\156\163\141\x63\164\x69\x6f\156\x5f\x69\x64" => $site_transaction_id, "\164\x6f\153\x65\156" => $result_decidir->id, "\143\165\163\x74\157\155\145\162" => array("\x69\144" => "\143\165\x73\164\x6f\x6d\x65\x72", "\145\155\x61\x69\154" => $order->get_billing_email(), "\x69\x70\x5f\141\x64\x64\x72\145\x73\x73" => $ip), "\x70\141\x79\x6d\145\x6e\x74\x5f\155\145\x74\150\x6f\x64\137\x69\144" => (int) $tarjeta_tipo, "\x62\151\156" => $result_decidir->bin, "\x61\x6d\x6f\165\x6e\164" => $psp_Amount, "\143\165\x72\162\x65\156\x63\171" => "\101\x52\123", "\x69\x6e\163\x74\x61\x6c\154\x6d\x65\156\164\163" => (int) $psp_NumPayments, "\144\x65\x73\143\162\x69\x70\x74\151\x6f\x6e" => $this->settings["\x65\163\x74\141\x62\154\151\163\150\155\145\x6e\164\x5f\156\x61\x6d\145"], "\x70\141\171\x6d\145\156\164\x5f\x74\x79\160\x65" => "\163\151\156\147\x6c\145", "\163\165\x62\137\x70\141\171\155\145\156\x74\163" => array(), "\x66\162\141\165\x64\x5f\144\145\x74\x65\x63\x74\x69\x6f\x6e" => array("\144\145\166\x69\143\x65\x5f\x75\x6e\151\x71\x75\x65\x5f\x69\x64\x65\156\164\151\146\151\145\162" => $site_transaction_id)); goto EMFky; lTCS1: $tarjcuot = "\x54\x41\122\112\105\124\101\x3a\x20" . $nombre . "\x20\40\x2d\40\x43\x55\x4f\124\x41\123\72\40" . $cuota; goto mzqQd; rLs3f: $psp_CardSecurityCode = str_replace("\x20", '', $_POST["\x64\x65\x63\x69\144\x69\162\x5f\x67\x61\164\x65\x77\141\171\55\143\141\x72\x64\x2d\143\166\x63"]); goto bUqrb; h5lVb: update_post_meta($order_id, "\x64\145\x63\x69\144\x69\162\137\x70\x72\157\x64\137" . $newdate, $cs_productss); goto VAF_K; FCv6d: if (!($tarjeta_id == 105)) { goto rMPuM; } goto RIsY0; AL50Q: $cuota = "\x41\x68\157\x72\141\x20\x33"; goto qL6tT; WKM1T: $nombre = "\x54\x61\x72\152\x65\x74\x61\x20\116\x61\162\141\x6e\x6a\x61"; goto Rp2ZT; XbTwP: $nombre = "\x54\141\162\152\x65\x74\x61\40\116\145\166\x61\x64\141"; goto eD0a0; qL6tT: bwXye: goto lTCS1; JQjjF: at8Rn: goto VKBLL; SBz0c: if (!($tarjeta_id == 8)) { goto n5X1Q; } goto qnpac; UXyEH: $connector->payment()->setCybersource($cybersource->getData()); goto Et5cC; RIsY0: $nombre = "\115\141\163\164\145\162\x43\x61\x72\x64\x20\104\303\xa9\142\151\x74\157"; goto TJbOT; izozu: if ($_POST["\144\145\x63\151\x64\x69\x72\55\143\165\157\164\x61\163"] == 17) { goto k_V1t; } goto e4d_W; DLgyB: if (!($tarjeta_id == 23)) { goto kndQj; } goto mk5Nj; Bbpyz: $cuota = $_POST["\x64\x65\x63\151\144\151\162\55\x63\x75\157\x74\x61\163"]; goto bm24n; Tpbqh: if (!($tarjeta_id == 108)) { goto o2M7q; } goto l5A20; bm24n: goto YNoXY; goto IND6g; ydpps: if (!($tarjeta_id == 24)) { goto eShOc; } goto WKM1T; n57V4: $ambient = "\x74\145\163\x74"; goto mdH6v; VYp5E: if (!is_array($shipping_data)) { goto PKXgI; } goto KVE2s; MsZsL: $keys_datat = json_encode($keys_data); goto OJPYA; z0r6P: foreach ($items as $item) { goto xghrl; JCETI: hVn4L: goto bcXqz; lVVNV: array_push($cs_products, array("\143\163\151\x74\x70\162\157\x64\165\x63\164\x63\157\144\x65" => "\167\157\157\x63\157\x6d\x6d\x65\x72\x63\x65", "\143\163\x69\x74\160\162\x6f\x64\x75\143\x74\144\145\163\143\x72\x69\x70\164\x69\157\x6e" => $product->get_name(), "\x63\163\x69\x74\x70\x72\157\x64\165\143\164\x6e\141\155\145" => $product->get_name(), "\x63\163\x69\164\160\162\157\144\x75\143\x74\163\153\x75" => "\x77\157\157\x63\x6f\155\155\145\x72\x63\x65", "\143\x73\x69\x74\x74\157\164\141\x6c\141\x6d\157\x75\156\164" => round($product->get_price(), 2), "\x63\163\151\164\161\x75\x61\156\164\151\x74\171" => $item["\161\165\x61\x6e\164\151\164\x79"], "\x63\163\151\164\x75\x6e\x69\164\x70\162\x69\x63\145" => round($product->get_price(), 2))); goto JCETI; xghrl: if (!($item["\x70\162\157\144\165\143\164\137\x69\144"] > 0)) { goto hVn4L; } goto SL17_; SL17_: $product = wc_get_product($item["\x70\162\x6f\144\165\x63\x74\137\151\x64"]); goto lVVNV; bcXqz: } goto ETmyI; l5A20: $nombre = "\103\141\x62\141\154\x20\104\xc3\251\142\x69\x74\157"; goto gE3Hq; vakCR: $psp_Amount = preg_replace("\x23\133\136\x5c\144\56\x5d\x23", '', $order->order_total); goto vMJB6; GLpw1: $connector = new \Decidir\Connector($keys_data, $ambient); goto gqOMQ; mpOb4: $nombre = "\x4e\x61\x74\x69\166\141"; goto S5Ox4; KVE2s: foreach ($shipping_data as $k => $sm) { goto Kmr6v; CJRiB: array_push($cs_products, array("\x63\163\151\164\160\162\157\x64\x75\143\164\x63\x6f\x64\x65" => "\x65\156\x76\151\157", "\x63\x73\x69\164\160\162\157\144\x75\143\164\144\145\x73\143\162\151\160\x74\151\157\x6e" => $sm["\155\x65\x74\x68\157\x64\x5f\164\151\x74\x6c\x65"], "\143\163\151\164\x70\x72\157\144\x75\x63\x74\x6e\x61\x6d\x65" => $sm["\x6d\x65\x74\150\157\x64\x5f\164\x69\x74\154\x65"], "\x63\x73\151\164\x70\162\x6f\144\x75\x63\x74\x73\x6b\x75" => "\145\x6e\x76\x69\157", "\143\163\151\x74\164\x6f\x74\x61\x6c\x61\x6d\x6f\x75\x6e\164" => $sm["\x74\157\x74\141\x6c"], "\x63\x73\151\x74\161\165\141\x6e\164\151\x74\171" => 1, "\x63\x73\x69\x74\165\156\151\x74\160\162\x69\143\145" => $sm["\x74\x6f\164\141\x6c"])); goto jgFQL; jgFQL: uuPRT: goto b6T6R; Kmr6v: if (!($sm["\x74\157\x74\x61\154"] > 1)) { goto uuPRT; } goto CJRiB; b6T6R: } goto nmyGj; RRU4c: kndQj: goto ydpps; Sv01h: $cs_datas = json_encode($cs_data); goto dHNkJ; km44j: gZ0oi: goto AL50Q; LG_iS: goto bwXye; goto km44j; s1Cqk: $decidir_card_tipo = intval($_POST["\x64\145\143\151\x64\x69\x72\x2d\x63\141\x72\x64\x2d\x74\x69\x70\x6f"]); goto kapxe; mdH6v: goto g5fyQ; goto m2cf0; TgwLT: if (!($tarjeta_id == 42)) { goto OKWXT; } goto mpOb4; bUqrb: $psp_CustomerMail = $_POST["\142\151\154\154\151\156\x67\137\145\155\x61\x69\154"]; goto dSfYo; ePlTh: if (!($tarjeta_id == 65)) { goto Zi8Sz; } goto eOBlP; lFW8_: Zi8Sz: goto rHPxS; zq8OY: $nombre = "\x4d\141\163\x74\145\162\x43\x61\x72\144"; goto vppo0; Z3Ibs: k_V1t: goto mSIli; OJPYA: $cs_productss = json_encode($cs_products); goto Sv01h; F1L8e: hH9Ie: goto PgHgy; dAfEh: $ambient = "\160\x72\x6f\x64"; goto aBe39; QQZnm: $year = substr($data, strpos($data, "\57") + 1); goto piqPn; Hmz1x: $psp_CardExpDate = $year . $month[0]; goto GhgVe; ETmyI: $shipping_data = $order->get_items("\163\x68\151\160\160\151\x6e\147"); goto VYp5E; GhgVe: $psp_CardExpDate = str_replace("\40", '', $psp_CardExpDate); goto rLs3f; Rp2ZT: eShOc: goto LFy1s; TJbOT: rMPuM: goto kGIxv; kapxe: $cs_data = array("\x73\x65\156\x64\x5f\x74\157\137\143\x73" => true, "\143\x68\141\x6e\156\145\x6c" => "\127\x65\x62", "\x62\x69\x6c\x6c\x5f\x74\157" => array("\x63\151\x74\171" => $order->get_billing_city(), "\x63\x6f\165\156\x74\x72\171" => "\x41\122", "\x63\165\x73\164\x6f\155\x65\162\137\x69\x64" => $order->get_customer_id() . $order->get_billing_last_name(), "\x65\155\x61\151\x6c" => $order->get_billing_email(), "\146\x69\x72\x73\x74\x5f\156\141\155\145" => $order->get_billing_first_name(), "\x6c\x61\163\x74\x5f\x6e\x61\155\145" => $order->get_billing_last_name(), "\160\150\157\156\145\137\156\165\x6d\x62\x65\162" => $order->get_billing_phone(), "\x70\x6f\x73\x74\x61\x6c\x5f\143\x6f\x64\145" => $order->get_billing_postcode(), "\x73\164\141\164\x65" => $order->get_billing_state(), "\x73\164\162\145\145\164\x31" => $order->get_billing_address_1(), "\x73\164\x72\x65\145\x74\x32" => $order->get_billing_address_2()), "\163\150\x69\160\x5f\164\157" => array("\x63\151\164\171" => $order->get_billing_city(), "\143\x6f\165\156\x74\x72\x79" => "\101\122", "\143\x75\163\164\157\x6d\x65\x72\137\151\144" => $order->get_customer_id() . $order->get_billing_last_name(), "\x65\x6d\x61\151\x6c" => $order->get_billing_email(), "\146\x69\162\163\164\x5f\x6e\x61\155\145" => $order->get_billing_first_name(), "\154\141\x73\164\137\x6e\x61\x6d\145" => $order->get_billing_last_name(), "\x70\150\157\156\x65\x5f\156\165\155\x62\145\162" => $order->get_billing_phone(), "\x70\157\x73\164\141\x6c\x5f\x63\x6f\x64\145" => $order->get_billing_postcode(), "\x73\164\x61\164\x65" => $order->get_billing_state(), "\163\x74\162\145\x65\164\x31" => $order->get_billing_address_1(), "\163\164\x72\x65\x65\164\62" => $order->get_billing_address_2()), "\143\165\x72\162\145\156\x63\171" => "\101\122\123", "\141\155\x6f\165\x6e\164" => $psp_Amount); goto V_u1n; eOBlP: $nombre = "\x41\x6d\x65\162\151\143\x61\x6e\x20\105\x78\x70\162\x65\x73\x73"; goto lFW8_; TVnTa: $tarjeta_tipo = str_replace("\40", '', $_POST["\x64\145\143\x69\x64\x69\x72\55\x74\x61\x72\152\x65\x74\141\55\164\151\x70\157"]); goto s1Cqk; vMJB6: $amount = str_replace("\56", '', $psp_Amount); goto R9xC3; cb4f1: $clear_slashes = stripslashes($_COOKIE["\162\x65\163\x75\154\x74\x5f\x64\145\x63\x69\144\x69\x72"]); goto THj8_; e4d_W: if ($_POST["\x64\145\143\151\144\x69\x72\x2d\143\165\157\164\x61\x73"] == 18) { goto A9l1O; } goto Bbpyz; eD0a0: qsjyd: goto TgwLT; vpY7s: $nombre = "\126\x69\163\x61\x20\x44\303\251\x62\151\164\x6f"; goto yyaiG; m2cf0: TDkYb: goto dAfEh; Litn5: $cuota = "\x41\150\x6f\162\x61\x20\61\x38"; goto H3LZL; akVGW: if (!($tarjeta_id == 63)) { goto xyJ98; } goto tco47; kGIxv: if (!($tarjeta_id == 106)) { goto hH9Ie; } goto MCXGE; hjR4O: $psp_Product = $_POST["\x64\x65\x63\x69\144\151\162\x5f\x67\141\164\145\167\x61\x79\x2d\143\141\162\x64\x2d\164\151\x70\157"]; goto R4T1a; ILpUn: $cs_products = array(); goto z0r6P; s_8vE: if (!($tarjeta_id == 55)) { goto jLEvx; } goto S2k2i; PgHgy: if ($_POST["\144\145\x63\151\x64\x69\x72\x2d\x63\165\x6f\164\141\163"] == 13) { goto gZ0oi; } goto VHxiM; qnpac: $nombre = "\x44\151\x6e\145\x72\163\x20\103\x6c\x75\x62"; goto cp8M3; ReTgr: $nombre = "\x56\x69\163\x61"; goto ypB0g; ORch5: z6p0L: goto LG_iS; rHPxS: if (!($tarjeta_id == 104)) { goto a_Gyp; } goto zq8OY; S5Ox4: OKWXT: goto s_8vE; CRlLu: if ($this->settings["\164\x65\163\x74\x6d\x6f\144\145"] == "\156\157") { goto TDkYb; } goto n57V4; R4T1a: $psp_CardNumber = str_replace("\x20", '', $_POST["\144\x65\143\x69\x64\151\x72\x5f\147\141\164\x65\x77\141\171\55\143\141\x72\144\55\156\x75\x6d\142\x65\x72"]); goto DOz6y; V_u1n: $items = $order->get_items(); goto ILpUn; LFy1s: if (!($tarjeta_id == 39)) { goto qsjyd; } goto XbTwP; IND6g: A9l1O: goto Litn5; yyaiG: A81U9: goto SBz0c; aBe39: g5fyQ: goto GLpw1; Kyt0n: $psp_MerchTxRef = $order->customer_id . "\x2d" . $decidir_MerchOrderIdnewdate; goto TxgvG; gqOMQ: $decidir_MerchOrderIdnewdate = date("\x68\151\163"); goto dWhoS; THj8_: $result_decidir = json_decode($clear_slashes); goto t1IgB; UywG6: goto z6p0L; goto JQjjF; Hpxe0: $keys_data = array("\x70\165\x62\x6c\151\x63\x5f\153\145\171" => $this->publishable_key, "\160\162\151\x76\x61\164\145\x5f\x6b\145\171" => $this->private_key); goto CRlLu; mzqQd: update_post_meta($order_id, "\x74\x61\162\x6a\145\164\x61\137\143\165\157\x74\141\x73", $tarjcuot); goto Xt3W_; H3LZL: YNoXY: goto q4jgF; gE3Hq: o2M7q: goto ePlTh; fu9qY: date_default_timezone_set("\x41\x6d\x65\x72\151\143\141\x2f\101\x72\147\145\x6e\164\151\x6e\141\x2f\102\x75\x65\x6e\157\163\137\x41\151\x72\x65\163"); goto MlOcW; mSIli: $cuota = "\101\150\157\162\x61\40\x31\62"; goto vPmxy; VAF_K: update_post_meta($order_id, "\x64\145\143\151\x64\151\x72\x5f\144\141\x74\x61\137" . $newdate, $cs_datas); goto x8t06; t1IgB: $order = wc_get_order($order_id); goto Hpxe0; v3caE: $psp_CardLastName = $_POST["\x64\145\143\151\x64\151\x72\137\x67\x61\x74\145\167\x61\171\x2d\x63\x61\162\x64\x2d\x6c\x61\163\x74\55\x6e\x61\155\x65"]; goto hjR4O; Xt3W_: try { goto JTKWY; Wq0Y4: $json = json_encode($response); goto q8adR; qh_ZI: $order->add_order_note(sprintf("\x44\x65\164\141\x6c\x6c\145\40\145\162\x72\x6f\162\72\40\47\x25\163\x27", $details)); goto gSK11; JgsrG: goto tp3LX; goto MlSNK; Q6eaI: wc_add_notice(__("\x45\x52\122\117\x52\40\x45\116\x20\105\114\x20\x50\101\x47\x4f\72\40" . $detalless->error->reason->description), "\145\162\x72\157\x72"); goto JgsrG; MlSNK: Vj1bj: goto Wq0Y4; Tc6UG: tp3LX: goto eQwcO; D90HN: $details = json_encode($response->getStatus_details()); goto YpHBP; gSK11: $detalless = json_decode($details); goto Q6eaI; E9scF: return array("\162\x65\x73\x75\154\164" => "\x73\165\x63\x63\145\163\x73", "\x72\x65\144\x69\x72\x65\x63\164" => $this->get_return_url($order)); goto Tc6UG; q8adR: update_post_meta($order_id, "\x64\x65\x63\x69\144\x69\162\x5f\x72\x65\163\x70\157\156\x73\x65", $json); goto AnMfI; YpHBP: $order->add_order_note(sprintf("\104\145\x74\x61\x6c\x6c\x65\x20\x70\x61\147\157\x3a\x20\47\x25\x73\x27", $status)); goto qh_ZI; ql27o: $status = $response->getStatus(); goto TbG6e; AnMfI: $order->update_status("\160\x72\157\x63\x65\x73\163\151\156\147", __("\124\122\x41\x4e\x53\x41\103\103\x49\117\x4e\x20\x49\x44\72\40" . $response->getId(), "\x77\x63\55\x67\141\x74\145\167\x61\x79\55\x64\x65\143\x69\x64\151\162")); goto BMg5Y; BMg5Y: $order->add_order_note(sprintf("\x44\x65\164\141\x6c\x6c\145\40\x70\141\x67\x6f\x3a\40\x27\x25\163\47", $response->getId() . "\40\55\x20" . $status)); goto L5jZd; TbG6e: if ($status == "\x61\x70\x70\162\157\166\145\144") { goto Vj1bj; } goto D90HN; L5jZd: $order->reduce_order_stock(); goto E9scF; JTKWY: $response = $connector->payment()->ExecutePayment($data); goto ql27o; eQwcO: } catch (\Exception $e) { goto bgbNp; Hmd1J: buxpD: goto Wb86Z; zZpGS: goto buxpD; goto GKGRy; Wb86Z: ac9IL: goto JIcgG; bxDu7: if ($reason["\166\141\x6c\151\144\141\x74\151\157\156\137\x65\162\x72\x6f\162\x73"][0]["\160\141\162\x61\x6d"] == "\x69\156\x73\x74\141\x6c\x6c\155\145\156\x74\163") { goto NnQGm; } goto nOsbu; btSgW: if (!($reason["\x76\x61\154\x69\144\141\x74\151\x6f\156\137\x65\162\162\157\162\x73"][0]["\x63\157\144\x65"] == "\151\156\x76\x61\x6c\151\x64\x5f\160\141\x72\x61\155")) { goto ac9IL; } goto bxDu7; LSATS: wc_add_notice(__("\105\122\x52\x4f\x52\x20\105\x4e\x20\105\114\x20\120\x41\107\x4f\x2e\72\x20\x52\x45\x56\111\123\101\122\x20\114\x41\x20\103\101\x4e\124\111\104\101\x44\x20\104\x45\x20\103\125\x4f\124\x41\x53\41"), "\x65\162\x72\157\162"); goto Hmd1J; nOsbu: wc_add_notice(__("\x45\122\x52\117\122\x20\x45\x4e\40\105\x4c\x20\120\101\x47\117\x3a\x20" . $reason["\166\141\154\151\144\141\x74\x69\x6f\156\x5f\145\162\162\157\x72\163"][0]["\x70\x61\x72\x61\x6d"]), "\145\162\x72\157\x72"); goto zZpGS; bgbNp: $resultado = json_encode($e->getData()); goto L4_uB; HIakH: $reason = $e->getData(); goto btSgW; GKGRy: NnQGm: goto LSATS; JqpWF: $order->add_order_note(sprintf("\104\145\x74\141\154\154\145\40\x65\x72\x72\x6f\x72\x20\144\141\164\141\x3a\x20\47\x25\x73\x27", $detalle)); goto HIakH; L4_uB: $order->add_order_note(sprintf("\104\145\x74\141\154\x6c\x65\40\x65\x72\162\x6f\162\x3a\x20\x27\x25\x73\x27", $resultado)); goto PrXBm; PrXBm: $detalle = json_encode($data); goto JqpWF; JIcgG: }


        }

        /*
         * In case you need a webhook, like PayPal IPN etc
         */
        public function webhook() {

            $order = wc_get_order( $_GET['id'] );
            $order->payment_complete();
            $order->reduce_order_stock();

            update_option('webhook_debug', $_GET);
        }

 

    }
}