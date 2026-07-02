<?php
	if (isset($_COOKIE['andreani_notice'])) {
			$_SESSION['andreani_notice'] = $_COOKIE['andreani_notice'];
			add_action( 'admin_notices', 'andreani_admin_notice' );
	}

	add_action('wp_ajax_check_sucursales_andreani', 'check_sucursales_andreani', 10);
	add_action('wp_ajax_nopriv_check_sucursales_andreani', 'check_sucursales_andreani', 10);

	add_action('wp_ajax_check_admision_andreani', 'check_admision_andreani', 10);
	add_action('wp_ajax_nopriv_check_admision_andreani', 'check_admision_andreani', 10);

	function check_admision_andreani() {
		global $woocommerce, $wp_session;
		session_start();
		if (isset($_POST['post_code'])) {

			$params = array(
						"method" => array(
								 "get_centros_destino" => array(
												'api_user' => $_POST['api_user'],
												'api_password' => $_POST['api_password'],
												'api_confirmarretiro' => $_POST['prod'],
												'api_nrocuenta' => $_POST['api_nrocuenta'],
												'operativa' => $_POST['operativa'],
												'cp_destino' => $_POST['post_code'],   
								 )
						)
				);
 					
			$andreani_response = wp_remote_post( $wp_session['url_andreani'], array(  'body' => $params, ) );
			if ( !is_wp_error( $andreani_response ) ) {            
			  $andreani_response = json_decode($andreani_response['body']);	 
 				echo '<select id="pv_centro_andreani_estandar" name="pv_centro_andreani_estandar">';
				$listado_andreani = array();
				foreach($andreani_response->results as $sucursales){
					$idCentroImposicion = $sucursales->sucursales->Sucursal;
				 	$sucursales_finales = $sucursales->sucursales->Direccion;
					$listado_andreani[] = $sucursales->sucursales;
 					echo '<option value="'. $idCentroImposicion.'">'. $sucursales_finales . '</option>';
				}
				echo '</select>';
				$_SESSION['listado_andreani'] = $listado_andreani;
				$_SESSION['params_andreani'] = $params;
      }
 			
 			die();
		}
	}

	function check_sucursales_andreani() {
		global $woocommerce, $wp_session;
		session_start();
		if (isset($_POST['post_code'])) {
			$settings = 'woocommerce_andreani_wanderlust_'.$_POST['instance_id'].'_settings';
			$settings_andreani =  get_option( $settings ); 
 			
			$params = array(
						"method" => array(
								 "get_centros_destino" => array(
												'api_user' => $settings_andreani['api_user'],
												'api_password' => $settings_andreani['api_password'],
												'api_confirmarretiro' => $settings_andreani['api_confirmarretiro'],
												'api_nrocuenta' => $settings_andreani['api_nrocuenta'],
												'operativa' => $_POST['operativa'],
												'cp_destino' => $_POST['post_code'],   
								 )
						)
				);
			$andreani_response = wp_remote_post( $wp_session['url_andreani'], array(  'body' => $params, ) );
  
			if ( !is_wp_error( $andreani_response ) ) {            
			  $andreani_response = json_decode($andreani_response['body']);	 				
 				echo '<select id="pv_centro_andreani_estandar" name="pv_centro_andreani_estandar">';
				$listado_andreani = array();
				foreach($andreani_response->results as $sucursales){
					$idCentroImposicion = $sucursales->sucursales->Sucursal;
				 	$sucursales_finales = $sucursales->sucursales->Direccion;
					$listado_andreani[] = $sucursales->sucursales;
 					echo '<option value="'. $idCentroImposicion.'">'. $sucursales_finales . '</option>';
				}
			
				echo '</select>';
			
				$_SESSION['listado_andreani'] = $listado_andreani;
				$_SESSION['params_andreani'] = $params;        
			} 			
 			die();
		}
	}

  add_action( 'wp_footer', 'only_numbers_andreanis');
	function only_numbers_andreanis(){ 
		if ( is_checkout() ) { ?>
 			<script type="text/javascript">
 				jQuery(document).ready(function () {  
        jQuery('#order_sucursal_mainandreani').insertAfter( jQuery( '.woocommerce-checkout-review-order-table' ) );
				jQuery('#calc_shipping_postcode').attr({ maxLength : 4 });
				jQuery('#billing_postcode').attr({ maxLength : 4 });
				jQuery('#shipping_postcode').attr({ maxLength : 4 });

		          jQuery("#calc_shipping_postcode").keypress(function (e) {
		          if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
		          	return false;
		          }
		          });
		          jQuery("#billing_postcode").keypress(function (e) { 
		          if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) { 
		          return false;
		          }
		          });
		          jQuery("#shipping_postcode").keypress(function (e) {  
		          if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
		          return false;
		          }
		          });
					
							 		
						jQuery('#billing_postcode').focusout(function () {
				    	if (jQuery('#ship-to-different-address-checkbox').is(':checked')) {
				    		var state = jQuery('#shipping_state').val();
				    		var post_code = jQuery('#shipping_postcode').val();
				    	} else {
				    		var state = jQuery('#billing_postcode').val();
				    		var post_code = jQuery('#billing_postcode').val();
				    	}
				    	
						
 							var selectedMethod = jQuery('input:checked', '#shipping_method').attr('id');
							var selectedMethodb = jQuery( "#order_review .shipping .shipping_method option:selected" ).val();
							if (selectedMethod == null) {
									if(selectedMethodb != null){
										selectedMethod = selectedMethodb;
									} else {
										return false;
									}
							}	 					
									var order_sucursal = 'ok';
									var instance_id = selectedMethod.substr(selectedMethod.indexOf("instance_id") + 11);
									var operativa = selectedMethod.substr(selectedMethod.indexOf("operativa") + 9)
									var cuit = selectedMethod.substr(selectedMethod.indexOf("api_nrocuenta") + 4)
     							var cuit_ok = cuit.substr(0, 9);
									var operativaok = operativa.substr(0, 9);
  
									jQuery("#order_sucursal_mainandreani_result").fadeOut(100);
									jQuery("#order_sucursal_mainandreani_result_cargando").fadeIn(100);	
									jQuery.ajax({
										type: 'POST',
										cache: false,
										url: wc_checkout_params.ajax_url,
										data: {
											action: 'check_sucursales_andreani',
											post_code: post_code,
											order_sucursal: order_sucursal,
											operativa: operativaok,
											cuit: cuit_ok,
											instance_id: instance_id,
										},
										success: function(data, textStatus, XMLHttpRequest){
													jQuery("#order_sucursal_mainandreani_result").fadeIn(100);
													jQuery("#order_sucursal_mainandreani_result_cargando").fadeOut(100);	
													jQuery("#order_sucursal_mainandreani_result").html('');
													jQuery("#order_sucursal_mainandreani_result").append(data);
											
	 											var selectList = jQuery('#pv_centro_andreani_estandar option');
												var arr = selectList.map(function(_, o) { return { t: jQuery(o).text(), v: o.value }; }).get();
												arr.sort(function(o1, o2) { return o1.t > o2.t ? 1 : o1.t < o2.t ? -1 : 0; });
												selectList.each(function(i, o) {
													o.value = arr[i].v;
													jQuery(o).text(arr[i].t);
												});
												jQuery('#pv_centro_andreani_estandar').html(selectList);
												jQuery("#pv_centro_andreani_estandar").prepend("<option value='0' selected='selected'>Sucursales Disponibles</option>");										
											
												},
												error: function(MLHttpRequest, textStatus, errorThrown){alert(errorThrown);}
											});
									return false;	
					 
				    });		
					
				});

				function toggleCustomBox() {
 				        var selectedMethod = jQuery('input:checked', '#shipping_method').attr('id');
								var selectedMethodb = jQuery( "#order_review .shipping .shipping_method option:selected" ).val();
								if (selectedMethod == null) {
									if(selectedMethodb != null){
										selectedMethod = selectedMethodb;
									} else {
										return false;
									}
								}	                  
									//sas, sasp, pasp, pas
                if (selectedMethod.indexOf("-sas") >= 0 || selectedMethod.indexOf("-sasp") >= 0 || selectedMethod.indexOf("-pasp") >= 0 || selectedMethod.indexOf("-pas") >= 0) {
									
                  jQuery('#order_sucursal_mainandreani').show();
									jQuery('#order_sucursal_mainandreani').insertAfter( jQuery('.shop_table') );

									if (jQuery('#ship-to-different-address-checkbox').is(':checked')) {
										var state = jQuery('#shipping_state').val();
										var post_code = jQuery('#shipping_postcode').val();
									} else {
										var state = jQuery('#billing_postcode').val();
										var post_code = jQuery('#billing_postcode').val();
									}
 									
									var order_sucursal = 'ok';
									var instance_id = selectedMethod.substr(selectedMethod.indexOf("instance_id") + 11);
									var operativa = selectedMethod.substr(selectedMethod.indexOf("operativa") + 9)
									var cuit = selectedMethod.substr(selectedMethod.indexOf("api_nrocuenta") + 4)
     							var cuit_ok = cuit.substr(0, 9);
									var operativaok = operativa.substr(0, 9);
  
									jQuery("#order_sucursal_mainandreani_result").fadeOut(100);
									jQuery("#order_sucursal_mainandreani_result_cargando").fadeIn(100);	
									jQuery.ajax({
										type: 'POST',
										cache: false,
										url: wc_checkout_params.ajax_url,
										data: {
											action: 'check_sucursales_andreani',
											post_code: post_code,
											order_sucursal: order_sucursal,
											operativa: operativaok,
											cuit: cuit_ok,
											instance_id: instance_id,
										},
										success: function(data, textStatus, XMLHttpRequest){
													jQuery("#order_sucursal_mainandreani_result").fadeIn(100);
													jQuery("#order_sucursal_mainandreani_result_cargando").fadeOut(100);	
													jQuery("#order_sucursal_mainandreani_result").html('');
													jQuery("#order_sucursal_mainandreani_result").append(data);
											
	 											var selectList = jQuery('#pv_centro_andreani_estandar option');
												var arr = selectList.map(function(_, o) { return { t: jQuery(o).text(), v: o.value }; }).get();
												arr.sort(function(o1, o2) { return o1.t > o2.t ? 1 : o1.t < o2.t ? -1 : 0; });
												selectList.each(function(i, o) {
													o.value = arr[i].v;
													jQuery(o).text(arr[i].t);
												});
												jQuery('#pv_centro_andreani_estandar').html(selectList);
												jQuery("#pv_centro_andreani_estandar").prepend("<option value='0' selected='selected'>Sucursales Disponibles</option>");										
											
												},
												error: function(MLHttpRequest, textStatus, errorThrown){alert(errorThrown);}
											});
									return false;					

                } else {
                  jQuery('#order_sucursal_mainandreani').hide();  
                }
				}; //ends toggleCustomBox

				jQuery(document).ready(toggleCustomBox);
				jQuery(document).on('change', '#shipping_method input:radio', toggleCustomBox);
 				jQuery(document).on('change', '#order_review .shipping .shipping_method', toggleCustomBox);

 						 
			</script>

			<style type="text/css">
         #order_sucursal_mainandreani h3 {
            text-align: left;
            padding: 5px 0 5px 115px;
        }
				.andreani-logo {
					position: absolute;
    			margin: 0px;
				}
			</style>
		<?php }
	}	//ends only_numbers_andreanis

  /**
	 * Add the field to the checkout
	 */
	add_action( 'woocommerce_after_order_notes', 'order_sucursal_mainandreani' );
	function order_sucursal_mainandreani( $checkout ) {
		global $woocommerce;
		session_start();
 		$items = $woocommerce->cart->cart_contents;
    
 		foreach($items as $item){
			$user_id = $item['data']->post->post_author;
 		}
 
	  $_SESSION['user_id'] = $user_id;
 		echo '<input type="hidden" value="'. $user_id .'" id="user_id_vendor" name="user_id_vendor" />';

	  echo '<div id="order_sucursal_mainandreani" style="display:none; margin-bottom: 50px;"><img class="andreani-logo" src="'. plugins_url( 'img/suc-andreani.png', __FILE__ ) . '"><h3>' . __(' ') . '</h3>';
    	echo '<small style="    margin-top: 10px;    padding-top: 14px;    float: left;    clear: both;    width: 100%;">Si seleccionaste retirar por sucursal, elegí tu sucursal en el listado.</small>';
      echo '<div id="order_sucursal_mainandreani_result_cargando">Cargando Sucursales...';echo '</div>';
 			echo '<div id="order_sucursal_mainandreani_result" style="display:none;">Cargando Sucursales...';echo '</div>';
    echo '</div>';
 	}


	 /**
	 * Process the checkout
	 */
	add_action('woocommerce_checkout_process', 'checkout_field_andreani_process_andreani');
	function checkout_field_andreani_process_andreani() {
			global $woocommerce;
			session_start();
		
			$chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
			$chosen_shipping = $chosen_methods[0]; 
			$_SESSION['chosen_shipping'] = $chosen_shipping;
			if (strpos($chosen_shipping, '-saspapi_nrocuenta') !== false || strpos($chosen_shipping, '-paspapi_nrocuenta') !== false || strpos($chosen_shipping, '-pasapi_nrocuenta') !== false || strpos($chosen_shipping, '-sasapi_nrocuenta') !== false) {
				if (empty($_POST['pv_centro_andreani_estandar']) )
									wc_add_notice( __( 'Por favor, seleccionar una sucursal de retiro.' ), 'error' ); 
			}
	}

	 /**
	 * Update the order meta with field value
	 */
	add_action( 'woocommerce_checkout_update_order_meta', 'order_sucursal_mainandreani_update_order_meta_andreani' );
	function order_sucursal_mainandreani_update_order_meta_andreani( $order_id ) {
		session_start();
 	    if ( ! empty( $_POST['pv_centro_andreani_estandar'] ) ) {
				foreach($_SESSION['listado_andreani'] as $opciones){
					if($_POST['pv_centro_andreani_estandar'] == $opciones->Sucursal){
						$opciones = json_encode($opciones);
						update_post_meta( $order_id, '_sucursal_andreani_c', $opciones );
					}				
 				}
	    }
			$chosen_shipping = json_encode($_SESSION['chosen_shipping'] );
			$params_andreani = json_encode($_SESSION['params_andreani'] );
			update_post_meta( $order_id, '_params_andreani', $params_andreani );
 
			if (isset($_COOKIE['andreani_origen_datos'])) {
				update_post_meta( $order_id, '_origen_datos', $_COOKIE['andreani_origen_datos'] );	
			}  
			update_post_meta( $order_id, '_chosen_shipping', $chosen_shipping );
	}

	 /**
	 * Show info at order
	 */
	add_action('add_meta_boxes', 'woocommerce_andreani_box_add_box');

	function woocommerce_andreani_box_add_box() {
		add_meta_box( 'woocommerce-andreani-box', __( 'Andreani - Detalles Envio', 'woocommerce-andreani' ), 'woocommerce_andreani_box_create_box_content', 'shop_order', 'side', 'default' );
	}
	function woocommerce_andreani_box_create_box_content() {
		global $post; $site_url = get_site_url();  $order = wc_get_order( $post->ID ); $shipping = $order->get_items( 'shipping' );  $sucursal_andreani_c = get_post_meta($post->ID, '_sucursal_andreani_c', true);
			echo '<div class="andreani-single">';
			echo '<strong>Contrato</strong></br>';
			foreach($shipping as $method){
				echo $method['name'];
			}
			if(!empty($sucursal_andreani_c)){
				$andreani_response = json_decode($sucursal_andreani_c);			
 				echo '</br></br><strong>Dirección</strong></br>'; 
				echo $andreani_response->Direccion .'</br>';
				echo '<strong>Tel.</strong> ' . $andreani_response->Telefono1 . '</br>';
				echo '<strong>Sucursal.</strong> ' . $andreani_response->Sucursal;
			}
			echo '</div>';
			//ETIQUETA
			$andreani_shipping_label_tracking = get_post_meta($post->ID, '_tracking_number', true); $etiqueta = get_post_meta($post->ID, '_etiqueta_andreani', true); $andreani_estado_ordenretiro = get_post_meta($post->ID, '_andreani_estado_ordenretiro', true); $andreani_estado_numeroenvio = get_post_meta($post->ID, '_andreani_estado_numeroenvio', true);
 			if(!empty($etiqueta) and !empty($andreani_shipping_label_tracking)){
				echo  '<div style="position: relative; width: 100%; height: 60px;"><a style=" width: 225px;text-align: center;background: #D72E2B;color: white;padding: 10px;margin: 10px;float: left;text-decoration: none;" href="'. $etiqueta .'" target="_blank">IMPRIMIR ETIQUETA</a></div>';
			}
			if(!empty($andreani_shipping_label_tracking)){
				echo  '<div style="position: relative; width: 100%; height: 60px;" ><a style=" width: 225px; text-align: center;background: #D72E2B;color: white;padding: 10px;margin: 10px;float: left;text-decoration: none;" href="http://seguimiento.andreani.com/envio/'. $andreani_estado_ordenretiro .'" target="_blank">Seguir Paquete</a></div>';
 				echo  '<div style="position: relative; width: 100%; height: 60px;" >Nro. Seguimiento: '.$andreani_shipping_label_tracking.'</div>';
			}		 					 
			if (empty($andreani_shipping_label_tracking)){ ?>
        <style type="text/css"> #generar-andreani { background: #D72E2B;   color: white;   width: 100%;   text-align: center;   height: 40px;   padding: 0px;   line-height: 37px;   margin-top: 20px; } </style>
        <div id="generar-andreani" class="button" data-id="<?php echo $post->ID; ?>">Generar Etiqueta</div>
        <input type="hidden" value="<?php echo $site_url; ?>" id="site" name="site" />
        </br>
        <div class="andreani-single-label"> </div>	
        <script type="text/javascript">
        jQuery('body').on('click', '#generar-andreani',function(e){ 
          e.preventDefault();
          var site = jQuery('#site').val();
          var urls = " "+ site+"/wp-admin/admin-ajax.php";
          var dataid = jQuery(this).data("id");
          jQuery(this).hide();
          jQuery('.andreani-single-label').html('ENVIANDO DATOS A ANDREANI..');
          jQuery('.andreani-single-label').fadeIn(400);
          jQuery.ajax({
            type: 'POST',
            cache: false,
            url: urls,
            data: {action: 'purchase_order_wanderlust_andreani',dataid: dataid,},
            success: function(data, textStatus, XMLHttpRequest){ 
              jQuery("#generar-andreani").fadeOut(100);
              jQuery(".andreani-single-label").fadeIn(400);
              jQuery(".andreani-single-label").html('');
              jQuery(".andreani-single-label").append(data);
              //jQuery("#generar-andreani").fadeOut(100);

              //landreanition.reload();
            },
            error: function(MLHttpRequest, textStatus, errorThrown){ jQuery("#generar-andreani").fadeOut(100); }
          });
        });	
        </script>
			<?php } 
	}


	add_action( 'wp_ajax_nopriv_purchase_order_wanderlust_andreani', 'purchase_order_wanderlust_andreani', 10);
 	add_action( 'wp_ajax_purchase_order_wanderlust_andreani', 'purchase_order_wanderlust_andreani', 10);

	/* GENERAR ETIQUETA */
	function purchase_order_wanderlust_andreani() { 
		global $woocommerce, $post, $wp_session;
      $order_id  = $_POST['dataid'];
      $params_andreani = get_post_meta($order_id, '_params_andreani', true);
      $origen_datos = get_post_meta($order_id, '_origen_datos', true);
      $sucursal_andreani_c = get_post_meta($order_id, '_sucursal_andreani_c', true);
      $chosen_shipping = get_post_meta($order_id, '_chosen_shipping', true);
      $instance_id = substr($chosen_shipping, strpos($chosen_shipping, "instance_id") + 11, -1);
      $sucursal_origen = get_option( 'woocommerce_andreani_wanderlust_'.$instance_id.'_settings' );
    
    
      $check_dni = json_decode($origen_datos);
      $dni_destino = $check_dni[0]->dni_destino;
      $dni_destino = get_post_meta($order_id, $dni_destino, true);
    
      $order = wc_get_order( $order_id );	

      $destino_datos[] = array (
        'nroremito' => '#'.$order_id,
        'apellido' => $order->shipping_last_name,
        'nombre' => $order->shipping_first_name,
        'calle' => $order->shipping_address_1,
        'nro' => $order->shipping_address_2,
        'piso' => '',
        'depto' => '',
        'localidad' => $order->shipping_city,
        'provincia' => $order->shipping_state,
        'cp' => $order->shipping_postcode,
        'telefono' => $order->billing_phone,
        'email' => $order->billing_email,
        'celular' => $order->billing_phone,
        'sucursal_origen' => $sucursal_origen['sucursal_origin'],
        'andreani_tarifa' => $order->shipping_total,
        'dni' => $dni_destino,
        'notas' => $order->get_customer_note(),
      );
		  $destino_datos = json_encode($destino_datos);

      $params = array(
              "method" => array(
                   "get_etiquetas" => array(
                          'sucursal_andreani_c' => $sucursal_andreani_c,
                          'origen_datos'   => $origen_datos,   
                          'destino_datos'  => $destino_datos,
                          'chosen_shipping' => $chosen_shipping,
                   )
              )
      ); 		
     
			$andreani_response = wp_remote_post( $wp_session['url_andreani'], array(
          'method'      => 'POST',
          'timeout'     => 90,
          'redirection' => 5,
          'httpversion' => '1.0',
          'blocking'    => true,
          'headers'     => array(),
          'body'        => $params,
          'cookies'     => array()
          )
      );    
 			if ( !is_wp_error( $andreani_response ) ) {            
			  $andreani_response = json_decode($andreani_response['body']);	 
        if(!empty($andreani_response->error)){
          echo '<pre style="background: #333333;width: 100%;display: inline-table;color: white !important;padding: 10px;"> ERROR DE ANDREANI: ';print_r($andreani_response->error);echo' INTENTAR MAS TARDE.</pre>';
        } else {
          
          if($andreani_response->results->detalleingresos->DescripcionDeResultado){
            
            $etiqueta = $andreani_response->results->etiqueta;
            $date = strtotime( date('Y-m-d') );
            update_post_meta($order_id, '_tracking_number',  $andreani_response->results->numeroenvio);
            update_post_meta($order_id, '_custom_tracking_provider', 'Andreani'); 
            update_post_meta($order_id, '_custom_tracking_link', 'http://www.andreani.com.ar/#envio');
            update_post_meta($order_id, '_date_shipped', $date);
            update_post_meta($order_id, '_etiqueta_andreani', $etiqueta);
            update_post_meta($order_id, '_andreani_estado_numero_andreani', $andreani_response->results->numeroenvio);
            update_post_meta($order_id, '_andreani_estado_numero_permisionaria', $andreani_response->results->detalleingresos->NumeroDePermisionaria);
            update_post_meta($order_id, '_andreani_estado', $andreani_response->results->detalleingresos->DescripcionDeResultado);
            
          } else {

            $save_path = plugin_dir_path ( __DIR__ ) . 'etiquetas/';
            $save_url = plugin_dir_url(dirname(__FILE__)) . 'etiquetas/';          
            $pdfdata = base64_decode($andreani_response->results->etiqueta);
            file_put_contents($save_path . $andreani_response->results->numeroenvio . '.pdf', $pdfdata);


            $etiqueta =  $save_url . $andreani_response->results->numeroenvio .'.pdf';
            $date = strtotime( date('Y-m-d') );
            update_post_meta($order_id, '_tracking_number', $andreani_response->results->numeroenvio);
            update_post_meta($order_id, '_custom_tracking_provider', 'Andreani'); 
            update_post_meta($order_id, '_custom_tracking_link', 'https://seguimiento.andreani.com/envio/'. $andreani_response->results->numeroenvio);
            update_post_meta($order_id, '_date_shipped', $date);
            update_post_meta($order_id, '_etiqueta_andreani', $etiqueta);
            update_post_meta($order_id, '_andreani_estado_numero_andreani', $andreani_response->results->numeroenvio);
            update_post_meta($order_id, '_andreani_estado', $andreani_response->results->detalleingresos);	
            
          }
           
          echo  '<div  style="position: relative; width: 100%; height: 60px;" ><a style=" width: 225px; text-align: center;background: #643494;color: white;padding: 10px;margin: 10px;float: left;text-decoration: none;" href="'. $etiqueta .'" target="_blank">IMPRIMIR ETIQUETA</a></div>';
          echo  '<div  style="position: relative; width: 100%; height: 60px;" ><a style=" width: 225px; text-align: center;background: #643494;color: white;padding: 10px;margin: 10px;float: left;text-decoration: none;" href="#" target="_blank">'.$andreani_response->results->numeroenvio.'</a></div>';

        }       
      }	

			die();
	}


	function andreani_admin_notice() {
			?>
			<div class="notice error my-acf-notice is-dismissible" >
					<p><?php print_r($_SESSION['andreani_notice'] ); ?></p>
			</div>

			<?php
	}


?>