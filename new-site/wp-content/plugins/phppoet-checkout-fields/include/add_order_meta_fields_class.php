<?php
class pcfme_add_order_meta_class {
     
	 
	 private $billing_settings_key = 'pcfme_billing_settings';
	 private $shipping_settings_key = 'pcfme_shipping_settings';
	 private $additional_settings_key = 'pcfme_additional_settings';
     
	 public function __construct() {
	      
	      
	      add_filter('woocommerce_checkout_update_order_meta', array( &$this, 'update_order_meta' ) );
	      add_filter('woocommerce_admin_order_data_after_billing_address', array( &$this, 'data_after_billing_address' ) );
	      add_filter('woocommerce_email_order_meta', array( &$this, 'woocommerce_custom_new_order_templace' )  );
	      add_filter('wpo_wcpdf_after_order_data', array( &$this, 'woocommerce_custom_new_pdfinvoice_template' )  ,10,2);
		  
          add_filter('woocommerce_view_order', array($this, 'data_after_order_details_page'), 195);


        $extra_settings            = get_option('pcfme_extra_settings');

        $thankyou_fields_location  = isset($extra_settings['thankyou_fields_location']) ? $extra_settings['thankyou_fields_location'] : "after"; 

		
		switch($thankyou_fields_location) {
			
			case "after":
			  add_filter('woocommerce_thankyou', array($this, 'data_after_order_details_page'), 75);
			break;
			  
			case "before":
			  add_filter('woocommerce_before_thankyou', array($this, 'data_after_order_details_page'), 75);
			break;
			
			
			 
			default:
			  add_filter('woocommerce_thankyou', array($this, 'data_after_order_details_page'), 75);
			
		}
	      
	}
	
	
	public function get_core_address_labels($field,$key) {
		
		if (isset($field['label']) && ($field['label'] != '')) { 
			$label = $field['label']; 
	    }  else {
			switch ($key) {
                case "billing_address_1":
				case "shipping_address_1":
                    $label = esc_html__('Address','pcfme');
                break;
                
				case "billing_address_2":
				case "shipping_address_2":
                    $label = "";
                break;
                        
				case "billing_city":
				case "shipping_city":
                    $label = esc_html__('Town / City','pcfme');
                break;
						
				case "billing_state":
			    case "shipping_state":
                    $label = esc_html__('State / County','pcfme');
                break;
						
				case "billing_postcode":
				case "shipping_postcode":
                    $label = esc_html__('Postcode / Zip','pcfme');
                break;
						
						
						
                default:
                    $label = $key;
            }
	    }
		
		return $label;
	
	}
	 
	 public function woocommerce_custom_new_pdfinvoice_template ($template,$order) {
           
		   
		    $billing_fields                = (array) get_option( $this->billing_settings_key );
		    $shipping_fields               = (array) get_option( $this->shipping_settings_key );
		    $additional_fields             = (array) get_option( $this->additional_settings_key );
		   
	
		   
		    foreach ($billing_fields as $billingkey=>$billing_field) {

		    	$billingkey = isset($billing_field['field_key']) ? $billing_field['field_key'] : $billingkey;
			    
				if (isset($billing_field['pdfinvoice'])) {
					  
				    $order_id = $order->get_id();
				    $billingkeyvalue = get_post_meta( $order_id, $billingkey, true );
					  
				    if ( ! empty( $billingkeyvalue ) ) { ?>
				          
						<tr class="billing-nif">
                            <th><?php echo $this->get_core_address_labels($billing_field,$billingkey); ?></th>
                            <td><?php echo $billingkeyvalue; ?></td>
                        </tr>
					<?php	}	
			
			    }
			}
		   
		   
		   
		   
		     foreach ($shipping_fields as $shippingkey=>$shipping_field) {

		     	$shippingkey = isset($shipping_field['field_key']) ? $shipping_field['field_key'] : $shippingkey;
			    
				   if (isset($shipping_field['pdfinvoice'])) {
					  
					$order_id = $order->get_id();   
				    $shippingkeyvalue = get_post_meta( $order_id, $shippingkey, true );
					  
				         if ( ! empty( $shippingkeyvalue ) ) { ?>
				          
						   <tr class="billing-nif">
                             <th><?php echo $this->get_core_address_labels($shipping_field,$shippingkey); ?></th>
                             <td><?php echo $shippingkeyvalue; ?></td>
                           </tr>
					   <?php	}	
                                   }  					
				      
                    
				
			 }
		   

		   foreach ($additional_fields as $additionalkey=>$additional_field) {

		   	  $additionalkey = isset($additional_field['field_key']) ? $additional_field['field_key'] : $additionalkey;

              if (isset($additional_field['pdfinvoice'])) {
					  
					  $order_id = $order->get_id();
			          $additionalkeyvalue = get_post_meta( $order_id, $additionalkey, true );
					
				         if ( ! empty( $additionalkeyvalue ) ) { ?>
				          
						   <tr class="billing-nif">
                             <th><?php echo $additional_field['label']; ?></th>
                             <td><?php echo $additionalkeyvalue; ?></td>
                           </tr>
					   <?php	}	
				      
                     }

		   }
	    }
		
	 	public function update_order_meta($order_id) {
		   
		   $billing_fields      = (array) get_option( $this->billing_settings_key );
		   $shipping_fields     = (array) get_option( $this->shipping_settings_key );
		   $additional_fields   = (array) get_option( $this->additional_settings_key );
	       
		   
		   
		     foreach ($billing_fields as $billingkey=>$billing_field) {

		     	$billingkey = isset($billing_field['field_key']) ? $billing_field['field_key'] : $billingkey;
			   
				   if ((isset($billing_field['orderedition'])) || (isset($billing_field['emailfields'])) || (isset($billing_field['pdfinvoice']))) {
				     if ( ! empty( $_POST[$billingkey] ) ) {
						 
						if (is_array($_POST[$billingkey]))  {
							$billingkeyvalue = implode(',', $_POST[$billingkey]);
						} else {
							$billingkeyvalue = $_POST[$billingkey];
						}
						 
                        update_post_meta( $order_id, $billingkey, sanitize_text_field( $billingkeyvalue ) );
                       } 
				   }
				
			 }
		   
		   
		   
		   
		     foreach ($shipping_fields as $shippingkey=>$shipping_field) {

		     	$shippingkey = isset($shipping_field['field_key']) ? $shipping_field['field_key'] : $shippingkey;
			    
				   if ((isset($shipping_field['orderedition'])) || (isset($shipping_field['emailfields'])) || (isset($shipping_field['pdfinvoice']))) {
				     if ( ! empty( $_POST[$shippingkey] ) ) {
						 
						if (is_array($_POST[$shippingkey]))  {
							$shippingkeyvalue = implode(',', $_POST[$shippingkey]);
						} else {
							$shippingkeyvalue = $_POST[$shippingkey];
						}
						
                        update_post_meta( $order_id, $shippingkey, sanitize_text_field( $shippingkeyvalue ) );
                       } 
				   }
				
			 }
		   

		   foreach ($additional_fields as $additionalkey=>$additional_field) {

		   	    $additionalkey = isset($additional_field['field_key']) ? $additional_field['field_key'] : $additionalkey;

		   	    if ((isset($additional_field['orderedition'])) || (isset($additional_field['emailfields'])) || (isset($additional_field['pdfinvoice']))) {
				     if ( ! empty( $_POST[$additionalkey] ) ) {
						 
						if (is_array($_POST[$additionalkey]))  {
							$additionalkeyvalue = implode(',', $_POST[$additionalkey]);
						} else {
							$additionalkeyvalue = $_POST[$additionalkey];
						}
						
                        update_post_meta( $order_id, $additionalkey, sanitize_text_field( $additionalkeyvalue ) );
                       } 
				   }
		   }
		   
		   
	       
	 }   
	 
	    public function data_after_order_details_page($orderid)  {
	       
	      
		   
		   
		   $billing_fields      = (array) get_option( $this->billing_settings_key );
		   $shipping_fields     = (array) get_option( $this->shipping_settings_key );
           $additional_fields   = (array) get_option( $this->additional_settings_key );
		   
		     ?>
		   <table class="shop_table additional_details">
		    <tbody>
		    <?php
		     foreach ($billing_fields as $billingkey=>$billing_field) {

		     	    $billingkey = isset($billing_field['field_key']) ? $billing_field['field_key'] : $billingkey;
			    
				   if (isset($billing_field['orderedition'])) {
					  
				  
				     $billingkeyvalue = get_post_meta( $orderid, $billingkey, true );
					  
				        if ( ! empty( $billingkeyvalue ) ) { ?>
				          
						   <tr>
                             <th><?php echo $this->get_core_address_labels($billing_field,$billingkey); ?>:</th>
                             <td><?php echo $billingkeyvalue; ?></td>
                           </tr>
					   <?php	}	
			
			       }
			 }
		   
		   
		   
		   
		     foreach ($shipping_fields as $shippingkey=>$shipping_field) {

		     	   $shippingkey = isset($shipping_field['field_key']) ? $shipping_field['field_key'] : $shippingkey;
			    
				   if (isset($shipping_field['orderedition'])) {
					  
					   
				    $shippingkeyvalue = get_post_meta( $orderid, $shippingkey, true );
					  
				         if ( ! empty( $shippingkeyvalue ) ) { ?>
				          
						   <tr>
                             <th><?php echo $this->get_core_address_labels($shipping_field,$shippingkey); ?>:</th>
                             <td><?php echo $shippingkeyvalue; ?></td>
                           </tr>
					   <?php	}	
                                   }  					
				      
                    
				
			 }
		   

		   foreach ($additional_fields as $additionalkey=>$additional_field) {

		   	    $additionalkey = isset($additional_field['field_key']) ? $additional_field['field_key'] : $additionalkey;

              if (isset($additional_field['orderedition'])) {
					  
			          $additionalkeyvalue = get_post_meta( $orderid, $additionalkey, true );
					
				         if ( ! empty( $additionalkeyvalue ) ) { ?>
				          
						   <tr>
                             <th><?php echo $additional_field['label']; ?>:</th>
                             <td><?php echo $additionalkeyvalue; ?></td>
                           </tr>
					   <?php	}	
				      
                     }

		   }
		   ?>
		   </tbody>
		   </table>
	       <?php  
	    }
	 
	 	 public function data_after_billing_address($order)  {
	       
	      
		   
		   $order_id            = $order->get_id();
		   $billing_fields      = (array) get_option( $this->billing_settings_key );
		   $shipping_fields     = (array) get_option( $this->shipping_settings_key );
           $additional_fields   = (array) get_option( $this->additional_settings_key );
		   
		   
		  
		     foreach ($billing_fields as $billingkey=>$billing_field) {

		     	    $billingkey = isset($billing_field['field_key']) ? $billing_field['field_key'] : $billingkey;
			    
				  if (isset($billing_field['orderedition'])) {
					 
					 $billingkeyvalue = get_post_meta( $order_id, $billingkey, true );
				     if ( ! empty( $billingkeyvalue ) ) {
						 echo '<p><strong>'.__(''.$this->get_core_address_labels($billing_field,$billingkey).'').':</strong> ' . $billingkeyvalue . '</p>';
                     }						 
					 
				   }
				
			 }
		   
		   
		   
		    foreach ($shipping_fields as $shippingkey=>$shipping_field) {
				
				$shippingkey = isset($shipping_field['field_key']) ? $shipping_field['field_key'] : $shippingkey;
			    
				   if (isset($shipping_field['orderedition'])) {
					  
					 $shippingkeyvalue = get_post_meta( $order_id, $shippingkey, true );
					 
					  if ( ! empty( $shippingkeyvalue ) ) {
						  echo '<p><strong>'.__(''.$this->get_core_address_labels($shipping_field,$shippingkey).'').':</strong> ' . $shippingkeyvalue . '</p>';
					  }
				     
				   }
				
			}
		   
            
		    foreach ($additional_fields as $additionalkey=>$additional_field) {

		    	$additionalkey = isset($additional_field['field_key']) ? $additional_field['field_key'] : $additionalkey;

		   	    if (isset($additional_field['orderedition'])) {
					
					$additionalkeyvalue = get_post_meta( $order_id, $additionalkey, true );
				    
					if ( ! empty( $additionalkeyvalue ) ) {
					   echo '<p><strong>'.__(''.$additional_field['label'].'').':</strong> ' . $additionalkeyvalue . '</p>';
					}
					
					
                 }
		   }
	       
	 }
	 
	 public function woocommerce_custom_new_order_templace ($order) {
          
		   $order_id            = $order->get_id();
		   $billing_fields      = (array) get_option( $this->billing_settings_key );
		   $shipping_fields     = (array) get_option( $this->shipping_settings_key );
           $additional_fields   = (array) get_option( $this->additional_settings_key );
		   
		     ?>
		   <br />
		   <br />
		   <table width="100%">
		    <tbody>
		    <?php
		     foreach ($billing_fields as $billingkey=>$billing_field) {

		     	    $billingkey = isset($billing_field['field_key']) ? $billing_field['field_key'] : $billingkey;
			    
				   if (isset($billing_field['emailfields'])) {
					  
				  
				     $billingkeyvalue = get_post_meta( $order_id, $billingkey, true );
					  
				        if ( ! empty( $billingkeyvalue ) ) { ?>
				          
						   <tr>
                             <th width="50%" ><?php echo $this->get_core_address_labels($billing_field,$billingkey); ?>:</th>
                             <td width="50%" ><?php echo $billingkeyvalue; ?></td>
                           </tr>
					   <?php	}	
			
			       }
			 }
		   
		   
		   
		   
		    foreach ($shipping_fields as $shippingkey=>$shipping_field) {
				
				$shippingkey = isset($shipping_field['field_key']) ? $shipping_field['field_key'] : $shippingkey;
			    
				if (isset($shipping_field['emailfields'])) {
					  
					   
				    $shippingkeyvalue = get_post_meta( $order_id, $shippingkey, true );
					  
				    if ( ! empty( $shippingkeyvalue ) ) { ?>
				          
						<tr>
                            <th width="50%" ><?php echo $this->get_core_address_labels($shipping_field,$shippingkey); ?>:</th>
                            <td width="50%" ><?php echo $shippingkeyvalue; ?></td>
                        </tr>
					<?php	}	
                }  					
				      
                    
				
			}
		   

		   foreach ($additional_fields as $additionalkey=>$additional_field) {
		   	    $additionalkey = isset($additional_field['field_key']) ? $additional_field['field_key'] : $additionalkey;

              if (isset($additional_field['emailfields'])) {
					  
			          $additionalkeyvalue = get_post_meta( $order_id, $additionalkey, true );
					
				         if ( ! empty( $additionalkeyvalue ) ) { ?>
				          
						   <tr>
                             <th width="50%" ><?php echo $additional_field['label']; ?>:</th>
                             <td width="50%" ><?php echo $additionalkeyvalue; ?></td>
                           </tr>
					   <?php	}	
				      
                     }

		   }
		   ?>
		   </tbody>
		   </table>
	       <?php  
	 }
	 
}

new pcfme_add_order_meta_class();
?>