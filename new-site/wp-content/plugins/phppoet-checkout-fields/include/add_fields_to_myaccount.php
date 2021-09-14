<?php
class pcfme_add_myaccount_fields {
	
		 private $billing_settings_key = 'pcfme_billing_settings';
	     private $shipping_settings_key = 'pcfme_shipping_settings';
	     private $additional_settings_key = 'pcfme_additional_settings';
		 
		 public function __construct() {
	 
	      
	      add_filter( 'woocommerce_after_edit_address_form_billing', array( &$this, 'pcfme_myaccount_edit_form_billing' ),10.1 );
		  add_filter( 'woocommerce_after_edit_address_form_shipping', array( &$this, 'pcfme_myaccount_edit_form_shipping' ),10.1 );
	      add_action( 'woocommerce_customer_save_address', array( &$this, 'pcfme_save_profile_fields' ),10,1 );
	      
	     }
		 
		 
		 
        public function pcfme_save_profile_fields( $user_id ) {
          
	      if ( !current_user_can( 'edit_user', $user_id ) )
		   return false;
           
		   $billing_fields                 = (array) get_option( $this->billing_settings_key );
		   $shipping_fields                = (array) get_option( $this->shipping_settings_key );
		   $additional_fields              = (array) get_option( $this->additional_settings_key );
		   
		   foreach ($billing_fields as $billingkey=>$billing_field) {
			   if (isset($billing_field['editaddress'])) {
				   
			      if (isset($_POST[$billingkey])) {
					  
					
					
					  update_user_meta( $user_id, $billingkey, $_POST[$billingkey] );
				  }
			       
			    }
		   }
		   
		   foreach ($shipping_fields as $shippingkey=>$shipping_field) {
			   
			   if (isset($shipping_field['editaddress'])) {
			      
				   if (isset($_POST[$shippingkey])) {
					  
					
					
					  update_user_meta( $user_id, $shippingkey, $_POST[$shippingkey] );
				   }
			       
			    }
		   }
		   
		    foreach ($additional_fields as $additionalkey=>$additional_field) {
			   if (isset($additional_field['editaddress'])) {
			      if (isset($_POST[$additionalkey])) {
					  
					
					
					  update_user_meta( $user_id, $additionalkey, $_POST[$additionalkey] );
				   }
			       
			    }
		   }
	      	
	      
        }



		 
		 public function pcfme_myaccount_edit_form_billing() {
			 
			 $billing_fields                = (array) get_option( $this->billing_settings_key );
		     
			 foreach ($billing_fields as $billingkey=>$billing_field) {
			      
				 if (isset($billing_field['editaddress'])) {
					 
					    $field_post_meta1 = get_user_meta( get_current_user_id() , $billingkey , true ); 
						    
						if (isset($field_post_meta1) && ($field_post_meta1 != '')) {
								$billing_field_value = $field_post_meta1;
						} elseif (isset($billing_field['value'])) {
						        $billing_field_value = $billing_field['value'];
					    } else {
						        $billing_field_value = '';
					    }
					  
					  
					  	  if (isset($billing_field['options']) && ($billing_field['options'] != '')) {
		                              $tempoptions = explode(',',$billing_field['options']);
		      
		      
		                              $billingoptions = array();
                      
                                      foreach($tempoptions as $billingval){
    
                                       $billingoptions[$billingval]  = $billingval;
      
                                      }
			 
		                    }
							
							
							if (isset($billing_field['options'])) { 
				     
					          if (isset($billing_field['options']) && ($billing_field['options'] != '')) {
				                $billing_field['options'] = $billingoptions;
					          }
					  
					        }
							   
					  
					  woocommerce_form_field( $billingkey, $billing_field, ! empty( $_POST[ $billingkey ] ) ? wc_clean( $_POST[ $billingkey ] ) : $billing_field_value ); 
				   
				   }
			 }
			 
		 }
		 
		 public function pcfme_myaccount_edit_form_shipping() {
			 $shipping_fields                = (array) get_option( $this->shipping_settings_key );
		     $additional_fields              = (array) get_option( $this->additional_settings_key );
			 
			 
			 foreach ($shipping_fields as $shippingkey=>$shipping_field) {
				 
				 if (isset($shipping_field['editaddress'])) {
				 
			              $field_post_meta2 = get_user_meta( get_current_user_id(), $shippingkey , true ); 
						  
				          if (isset($field_post_meta2) && ($field_post_meta2 != '')) {
								$shipping_field_value = $field_post_meta2;
						   } elseif (isset($shipping_field['value'])) {
						        $shipping_field_value = $shipping_field['value'];
					       } else {
						        $shipping_field_value = '';
					       }
					       
					  
					  	  if (isset($shipping_field['options']) && ($shipping_field['options'] != '')) {
		                              $tempoptions2 = explode(',',$shipping_field['options']);
		      
		      
		                              $shippingoptions = array();
                      
                                      foreach($tempoptions2 as $shippingval){
    
                                       $shippingoptions[$shippingval]  = $shippingval;
      
                                      }
			 
		                    }
							
							
							if (isset($shipping_field['options'])) { 
				     
					          if (isset($shipping_field['options']) && ($shipping_field['options'] != '')) {
				                $shipping_field['options'] = $shippingoptions;
					          }
					  
					        }
							   
							
					  woocommerce_form_field( $shippingkey, $shipping_field, ! empty( $_POST[ $shippingkey ] ) ? wc_clean( $_POST[ $shippingkey ] ) : $shipping_field_value ); 
				   
				   }
			 
			 }
			 
			 foreach ($additional_fields as $additionalkey=>$additional_field) {
			    
			         if (isset($additional_field['editaddress'])) { 
					 
					      $field_post_meta3 = get_user_meta( get_current_user_id(), $additionalkey , true ); 
						  
				          
						  
						  if (isset($field_post_meta3) && ($field_post_meta3 != '')) {
								$additional_field_value = $field_post_meta3;
						   } elseif (isset($additional_field['value'])) {
						        $additional_field_value = $additional_field['value'];
					       } else {
						        $additional_field_value = '';
					       }
					  
					       
					  	  if (isset($additional_field['options']) && ($additional_field['options'] != '')) {
		                              $tempoptions2 = explode(',',$additional_field['options']);
		      
		      
		                              $additionaloptions = array();
                      
                                      foreach($tempoptions2 as $additionalval){
    
                                       $additionaloptions[$additionalval]  = $additionalval;
      
                                      }
			 
		                    }
							
							
							if (isset($additional_field['options'])) { 
				     
					          if (isset($additional_field['options']) && ($additional_field['options'] != '')) {
				                $additional_field['options'] = $additionaloptions;
					          }
					  
					        }
							   
							
					  woocommerce_form_field( $additionalkey, $additional_field, ! empty( $_POST[ $additionalkey ] ) ? wc_clean( $_POST[ $additionalkey ] ) : $additional_field_value ); 
				   
		       }
			 }
			 
		 }
	 
	
}

new pcfme_add_myaccount_fields();
?>