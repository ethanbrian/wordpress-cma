<?php



if ( ! function_exists( 'pcfme_get_woo_version_number' ) ) {

    /**
	 * Outputs a installed woocommerce version
	 *
	 * @access public
	 * @subpackage	Forms
	 */



    function pcfme_get_woo_version_number() {
        // If get_plugins() isn't available, require it
	   
	   if ( ! function_exists( 'get_plugins' ) )
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	
        // Create the plugins folder and file variables
	   $plugin_folder = get_plugins( '/' . 'woocommerce' );
	   $plugin_file = 'woocommerce.php';
	
	   // If the plugin version number is set, return it 
	   if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
		 return $plugin_folder[$plugin_file]['Version'];

	   } else {
	// Otherwise return null
		return NULL;
	  }
   }
   
}


if ( ! function_exists( 'pfcme_parent_visibility_check' ) ) {

    /**
	 * returns conditional classes
	 *
	 * @access public
	 * @subpackage	Forms
	 */



    function pfcme_parent_visibility_check($parentfield) {

        $default = 'visible';

        if (strpos($parentfield, 'billing') !== false) {
            
            $field_type = 'billing';

        } elseif (strpos($parentfield, 'shipping') !== false) {
        	
        	$field_type = 'shipping';
        
        } elseif (strpos($parentfield, 'shipping') !== false) {
            
            $field_type = 'additional';

        }


        if (isset($field_type)) {

        	switch($field_type) {
        		case "billing":
        		    $fields_data = get_option('pcfme_billing_settings');
        		break;

        		case "shipping":
        		    $fields_data = get_option('pcfme_shipping_settings');
        		break;

        		case "additional":
        		    $fields_data = get_option('pcfme_additional_settings');
        		break;

        	}
        }

        if (isset($fields_data) && isset($fields_data[$parentfield])) {
        	
        	$value = $fields_data[$parentfield];

        	if (isset($value['visibility'])) {
				
				$visibilityarray = $value['visibility'];
				 
				if (isset($value['products'])) { 
				    $allowedproducts = $value['products'];
				} else {
					$allowedproducts = array(); 
				}
				 
				if (isset($value['category'])) {
					$allowedcats = $value['category'];
				} else {
					$allowedcats = array();
				}

				if (isset($value['role'])) {
					$allowedroles = $value['role'];
				} else {
					$allowedroles = array();
				}

				if (isset($value['total-quantity'])) {
					$total_quantity = $value['total-quantity'];
				} else {
					$total_quantity = 0;
				}

				if (isset($value['specific-product'])) {
					$prd = $value['specific-product'];
				} else {
					$prd = 0;
				}

				if (isset($value['specific-quantity'])) {
					$prd_qnty = $value['specific-quantity'];
				} else {
					$prd_qnty = 0;
				}

                $default_class = new pcfme_update_checkout_fields();
				 
				$is_field_hidden=$default_class->pcfme_check_if_field_is_hidden($visibilityarray,$allowedproducts,$allowedcats,$allowedroles,$total_quantity,$prd,$prd_qnty);

				if ((isset($is_field_hidden)) && ($is_field_hidden == 0)) {

					return 'hidden';

				}
            }

        }

        return $default;

    }

}



if ( ! function_exists( 'pcfme_get_conditional_class' ) ) {

    /**
	 * returns conditional classes
	 *
	 * @access public
	 * @subpackage	Forms
	 */



    function pcfme_get_conditional_class($conditional) {

    	$class = '';

    	$parent_visibility_class = '';

    	foreach ($conditional as $key=>$value) {

            if (isset($value['showhide'])) {
            	$showhide                 = $value['showhide'];
            }

            if (isset($value['parentfield'])) {
            	$parentfield               = $value['parentfield'];
            }

            if (isset($showhide) && ($showhide == "open") && isset($parentfield)) {

            	$parent_visibility   = pfcme_parent_visibility_check($parentfield);

            	if (isset($parent_visibility) && ($parent_visibility == 'hidden')) {
            	    $parent_visibility_class = 'parent_hidden';
                } 
            }





            if (isset($value['equalto'])) {
            	$equalto               = $value['equalto'];
            	$equalto = str_replace(' ', '_', $equalto);
            }
    		
	        
	        if ((isset($showhide)) && (isset($parentfield))) {

	        	if (isset($equalto) && ($equalto != '')) {
			        $class  .= '' . $showhide . '_by_' . $parentfield . '_' . $equalto .' '.$parent_visibility_class.''; 
	            } else {
			        $class  .= '' . $showhide . '_by_' . $parentfield . ' '.$parent_visibility_class.''; 
		        }
	        }

	        

    	}
    

		return $class;
    }
   
}



if ( ! function_exists( 'pcfme_get_conditional_shipping_class' ) ) {

    /**
	 * returns conditional classes
	 *
	 * @access public
	 * @subpackage	Forms
	 */



    function pcfme_get_conditional_shipping_class($shipping) {

    	$shipping_method = $shipping['method'];
    	$showhide        = $shipping['showhide'];


    	$class = 'conditional_shipping '.$showhide.'_by_'.$shipping_method.'';
    

		return $class;
    }
   
}



if ( ! function_exists( 'pcfme_get_conditional_payment_class' ) ) {

    /**
	 * returns conditional classes
	 *
	 * @access public
	 * @subpackage	Forms
	 */



    function pcfme_get_conditional_payment_class($payment) {

    	$payment_geteway = $payment['gateway'];
    	$showhide        = $payment['showhide'];

    	$class = 'conditional_payment '.$showhide.'_by_'.$payment_geteway.'';
    

		return $class;
    }
   
}







if ( ! function_exists( 'pcfmeinput_conditional_class' ) ) {
	
	function pcfmeinput_conditional_class($fieldkey) {

		$billing_settings_key      = 'pcfme_billing_settings';
	    $shipping_settings_key     = 'pcfme_shipping_settings';
	    $pcfme_additional_settings = 'pcfme_additional_settings';
		$pcfme_class_text          = '';
		 
		 
		$billing_fields                = (array) get_option( $billing_settings_key );
		$shipping_fields               = (array) get_option( $shipping_settings_key );
		$additional_fields             = (array) get_option( $pcfme_additional_settings );
		 
		$hiderlist  = array();
		$openerlist = array();
		 
		foreach ($billing_fields as $billingkey=>$billingvalue) {

			if (isset($billingvalue['visibility']) && ($billingvalue['visibility'] == 'field-specific')) {
			 
			    $conditional                = $billingvalue['conditional'];

			    foreach ($conditional as $key1=>$value1) {

                    if (isset($value1['parentfield'])) {
                    	$parentfield1               = $value1['parentfield'];
                    }

                    if (isset($value1['showhide'])) {
                    	$cxshowhide1               = $value1['showhide'];
                    }
			 	    
			        
			        

			        if (isset($parentfield1) && ($parentfield1 != '')) {
				
				        if (isset($cxshowhide1) && ($cxshowhide1 != '')) {
					        switch ($cxshowhide1) {
						        case "open":
						            if (!in_array($parentfield1, $openerlist)) array_push($openerlist, $parentfield1);
						        break;
						
						        case "hide":
						            if (!in_array($parentfield1, $hiderlist)) array_push($hiderlist, $parentfield1);
						        break;
						    }
				        }
			        }
			    }
            }   
		}
		 
		foreach ($shipping_fields as $shippingkey=>$shippingvalue) {

			if (isset($shippingvalue['visibility']) && ($shippingvalue['visibility'] == 'field-specific')) {
			 
			    $conditional2                = $shippingvalue['conditional'];

			    foreach ($conditional2 as $key2=>$value2) {

			 	    if (isset($value2['parentfield'])) {
                    	$parentfield2               = $value2['parentfield'];
                    }

                    if (isset($value2['showhide'])) {
                    	$cxshowhide2                = $value2['showhide'];
                    }

			        if (isset($parentfield2) && ($parentfield2 != '')) {
				
				        if (isset($cxshowhide2) && ($cxshowhide2 != '')) {
					        switch ($cxshowhide2) {
						        case "open":
						            if (!in_array($parentfield2, $openerlist)) array_push($openerlist, $parentfield2);
						        break;
						
						        case "hide":
						            if (!in_array($parentfield2, $hiderlist)) array_push($hiderlist, $parentfield2);
						        break;
						    }
				        }
			        }
			    }
			}   
		}
		 
		 
        
        foreach ($additional_fields as $additionalkey=>$additionalvalue) {

			if (isset($additionalvalue['visibility']) && ($additionalvalue['visibility'] == 'field-specific')) {
			 
			    if (isset($additionalvalue['conditional'])) {
			    	$conditional3                = $additionalvalue['conditional'];
			    }
			    
                if (isset($conditional3)) {

			        foreach ($conditional3 as $key3=>$value3) {

			 	        if (isset($value3['parentfield'])) {
                    	    $parentfield3               = $value3['parentfield'];
                        }

                        if (isset($value3['showhide'])) {
                    	    $cxshowhide3                = $value3['showhide'];
                        }

			            if (isset($parentfield3) && ($parentfield3 != '')) {
				
				            if (isset($cxshowhide3) && ($cxshowhide3 != '')) {
					            switch ($cxshowhide3) {
						            case "open":
						                if (!in_array($parentfield3, $openerlist)) array_push($openerlist, $parentfield3);
						            break;
						
						            case "hide":
						                if (!in_array($parentfield3, $hiderlist)) array_push($hiderlist, $parentfield3);
						            break;
						        }
				            }
			            }
			        }
			    }
			}   
		}
		 
		   
		if (in_array($fieldkey, $openerlist)) {

			$pcfmeopernertext                = 'pcfme-opener';

		} else {

			$pcfmeopernertext                = '';
		}
		   
		if (in_array($fieldkey, $hiderlist)) {

			$pcfmehidertext                 = 'pcfme-hider';

		} else {

			$pcfmehidertext                 = '';
		}
			
			
		$pcfme_class_text  = ''.$pcfmeopernertext.' '.$pcfmehidertext.'';
			
		    
			
	    return $pcfme_class_text;
	}
	        
}
?>