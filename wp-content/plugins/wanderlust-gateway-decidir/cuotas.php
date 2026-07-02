<tr valign="top" id="packing_options">
	<th scope="row" class="titledesc"><?php _e( 'Tarjetas y Cantidad de Cuotas', 'wc_wanderlust' ); ?></th>
	<td class="forminp">
		<style type="text/css">
			.wc-modal-shipping-method-settings form .form-table tr td input[type=checkbox] {
						min-width: 15px !important;
				}
			.wanderlust_boxes .small {
				width: 25px !important;
    		min-width: 25px !important;
			}
			.wanderlust_boxes td, .wanderlust_services td {
				vertical-align: middle;
				padding: 4px 7px;
			}
			.wanderlust_services th, .wanderlust_boxes th {
				padding: 9px 7px;
			}
			.wanderlust_boxes td input {
				margin-right: 4px;
			}
			.wanderlust_boxes .check-column {
				vertical-align: middle;
				text-align: left;
				padding: 0 7px;
			}
			.wanderlust_services th.sort {
				width: 16px;
				padding: 0 16px;
			}
			.wanderlust_services td.sort {
				cursor: move;
				width: 16px;
				padding: 0 16px;
				cursor: move;
				background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAYAAADED76LAAAAHUlEQVQYV2O8f//+fwY8gJGgAny6QXKETRgEVgAAXxAVsa5Xr3QAAAAASUVORK5CYII=) no-repeat center;
			}
		</style>
		<table class="wanderlust_boxes widefat">
			<thead>
				<tr>
					<th class="check-column"><input type="checkbox" /></th>
					<th><?php _e( 'Banco', 'wc_wanderlust' ); ?></th>
					<th><?php _e( 'Tarjeta', 'wc_wanderlust' ); ?></th>
					<th><?php _e( 'Recargo %', 'wc_wanderlust' ); ?></th>
 					<th><?php _e( 'Cuotas', 'wc_wanderlust' ); ?></th>
					<th><?php _e( 'Activo', 'wc_wanderlust' ); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th colspan="3">
						<a href="#" class="button plus insert"><?php _e( 'Agregar', 'wc_wanderlust' ); ?></a>
						<a href="#" class="button minus remove"><?php _e( 'Remover', 'wc_wanderlust' ); ?></a>
					</th>
					<th colspan="6">
  				</th>
				</tr>
			</tfoot>
			<tbody id="rates">
				<?php //global $woocommerce;		
					if ( $this->settings['cuotas'] ) {
						foreach ( $this->settings['cuotas'] as $key => $box ) {
 							if ( ! is_numeric( $key ) )
								continue;
							?>
							<tr>
								<td class="check-column"><input type="checkbox" /> </td>
								<td><input type="text" size="35" name="banco_name[<?php echo $key; ?>]" value="<?php echo esc_attr( $box['banco_name'] ); ?>" /></td>
								<td>
                      <select class="select tarjetas" name="tarjetas[<?php echo $key; ?>]" id="tarjetas" style="">
													<option value="0" <?php if($box['tarjetas'] == '0') { ?> selected <?php } ?> >Seleccionar</option>
													<option value="103" <?php if($box['tarjetas'] == '103') { ?> selected <?php } ?> >FavaCard</option>
													<option value="1" <?php if($box['tarjetas'] == '1') { ?> selected <?php } ?> >Visa</option>
													<option value="31" <?php if($box['tarjetas'] == '31') { ?> selected <?php } ?> >Visa Débito</option>
													<option value="8" <?php if($box['tarjetas'] == '8') { ?> selected <?php } ?> >Diners Club	</option>								
													<option value="23" <?php if($box['tarjetas'] == '23') { ?> selected <?php } ?> >Tarjeta Shopping</option>			
													<option value="24" <?php if($box['tarjetas'] == '24') { ?> selected <?php } ?> >Tarjeta Naranja</option>	
													<option value="39" <?php if($box['tarjetas'] == '39') { ?> selected <?php } ?> >Tarjeta Nevada</option>
													<option value="42" <?php if($box['tarjetas'] == '42') { ?> selected <?php } ?> >Nativa</option>								
													<option value="55" <?php if($box['tarjetas'] == '55') { ?> selected <?php } ?> >Patagonia 365	</option>			
													<option value="63" <?php if($box['tarjetas'] == '63') { ?> selected <?php } ?> >Cabal Prisma	</option>												
													<option value="108" <?php if($box['tarjetas'] == '108') { ?> selected <?php } ?> >Cabal Débito Prisma	</option>												
													<option value="65" <?php if($box['tarjetas'] == '65') { ?> selected <?php } ?> >American Express	</option>									
													<option value="104" <?php if($box['tarjetas'] == '104') { ?> selected <?php } ?> >MasterCard Prisma</option>									
													<option value="105" <?php if($box['tarjetas'] == '105') { ?> selected <?php } ?> >MasterCard Debit Prisma</option>									
													<option value="106" <?php if($box['tarjetas'] == '106') { ?> selected <?php } ?> >Maestro Prisma</option>									
                        
	         	               	
											</select>
                </td>
								<td><input class="recargo" type="text" size="15" maxlength="6" name="recargo[<?php echo $key; ?>]" value="<?php echo esc_attr( $box['recargo'] ); ?>" /> </td>
								<td>
											<select class="select cuotas" name="cuotas[<?php echo $key; ?>]" id="cuotas" style="">
													<option value="0" <?php if($box['cuotas'] == '0') { ?> selected <?php } ?> >Seleccionar</option>
													<option value="1" <?php if($box['cuotas'] == '1') { ?> selected <?php } ?> >1</option>
													<option value="2" <?php if($box['cuotas'] == '2') { ?> selected <?php } ?> >2</option>								
													<option value="3" <?php if($box['cuotas'] == '3') { ?> selected <?php } ?> >3</option>			
													<option value="4" <?php if($box['cuotas'] == '4') { ?> selected <?php } ?> >4</option>	
													<option value="5" <?php if($box['cuotas'] == '5') { ?> selected <?php } ?> >5</option>
													<option value="6" <?php if($box['cuotas'] == '6') { ?> selected <?php } ?> >6</option>								
													<option value="7" <?php if($box['cuotas'] == '7') { ?> selected <?php } ?> >7</option>			
													<option value="8" <?php if($box['cuotas'] == '8') { ?> selected <?php } ?> >8</option>												
													<option value="9" <?php if($box['cuotas'] == '9') { ?> selected <?php } ?> >9</option>												
													<option value="10" <?php if($box['cuotas'] == '10') { ?> selected <?php } ?> >10</option>												
													<option value="11" <?php if($box['cuotas'] == '11') { ?> selected <?php } ?> >11</option>												
													<option value="12" <?php if($box['cuotas'] == '12') { ?> selected <?php } ?> >12</option>												
													<option value="13" <?php if($box['cuotas'] == '13') { ?> selected <?php } ?> >Cuota Simple 3</option>												
													<option value="16" <?php if($box['cuotas'] == '16') { ?> selected <?php } ?> >Cuota Simple 6</option>												
													<option value="17" <?php if($box['cuotas'] == '17') { ?> selected <?php } ?> >Cuota Simple 12</option>												
											</select>
								</td>			
 								<td><input type="checkbox" name="service_enabled[<?php echo $key; ?>]" <?php checked( ! isset( $box['enabled'] ) || $box['enabled'] == 1, true ); ?> /></td>
							</tr>
							<?php
						}
					}
				?>
			</tbody>
		</table>
		<script type="text/javascript">
 		 

 			jQuery(document).ready(function () { 
				
				jQuery('#woocommerce_wanderlust_packing_method').change(function(){
					if ( jQuery(this).val() == 'box_packing' )
						jQuery('#packing_options').show();
					else
						jQuery('#packing_options').hide();
				}).change();

				jQuery('.wanderlust_boxes .insert').click( function() {
					var $tbody = jQuery('.wanderlust_boxes').find('tbody');
					var size = $tbody.find('tr').size();
					var code = '<tr class="new">\
							<td class="check-column"><input type="checkbox" /></td>\
							<td><input type="text" size="35" name="banco_name[' + size + ']" /></td>\
							<td><select class="select tarjetas" name="tarjetas[' + size + ']" id="tarjetas" style="">   <option value="0">Seleccionar</option> <option value="103" >FavaCard</option> <option value="1" >Visa</option>	<option value="31" >Visa Débito</option><option value="8"  >Diners Club	</option>		<option value="23" >Tarjeta Shopping</option>		<option value="24" >Tarjeta Naranja</option>	<option value="39" >Tarjeta Nevada</option><option value="42" >Nativa</option>							<option value="55" >Patagonia 365	</option>				<option value="63" >Cabal Prisma	</option>			<option value="108" >Cabal Débito Prisma	</option>		<option value="65" >American Express	</option>		<option value="104" >MasterCard Prisma</option>	<option value="105" >MasterCard Debit Prisma</option><option value="106" >Maestro Prisma</option></select></td>\
							<td><input type="text" size="15" name="recargo[' + size + ']" /></td>\
							<td><select class="select cuotas" name="cuotas[' + size + ']" id="cuotas" style="">    <option value="0">Seleccionar</option><option value="1"  >1</option><option value="2" >2</option> <option value="3"  >3</option> <option value="4"  >4</option>				<option value="5"  >5</option>			<option value="6"  >6</option> <option value="7" >7</option> <option value="8"  >8</option>	<option value="9"  >9</option><option value="10" >10</option><option value="11" >11</option><option value="12" >12</option><option value="13" >Cuota Simple 3</option>	<option value="16" >Cuota Simple 6</option>	<option value="17" >Cuota Simple 12</option> 	</select></td>\
							<td><input type="checkbox" name="service_enabled[' + size + ']" /></td>\
						</tr>';
					$tbody.append( code );
					return false;
				});

				jQuery('.wanderlust_boxes .remove').click(function() {
					var $tbody = jQuery('.wanderlust_boxes').find('tbody');
					$tbody.find('.check-column input:checked').each(function() {
						jQuery(this).closest('tr').hide().find('input').val('');
					});
					return false;
				});

				// Ordering
				jQuery('.wanderlust_services tbody').sortable({
					items:'tr',
					cursor:'move',
					axis:'y',
					handle: '.sort',
					scrollSensitivity:40,
					forcePlaceholderSize: true,
					helper: 'clone',
					opacity: 0.65,
					placeholder: 'wc-metabox-sortable-placeholder',
					start:function(event,ui){
						ui.item.css('baclbsround-color','#f6f6f6');
					},
					stop:function(event,ui){
						ui.item.removeAttr('style');
						wanderlust_services_row_indexes();
					}
				});

				function wanderlust_services_row_indexes() {
					jQuery('.wanderlust_services tbody tr').each(function(index, el){
						jQuery('input.order', el).val( parseInt( jQuery(el).index('.wanderlust_services tr') ) );
					});
				};

			});

		</script>
	</td>
</tr>