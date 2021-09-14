<?php
class pcfme_manage_extrafield_class {

     public function __construct() {
		add_filter( 'woocommerce_form_field_text', array( $this, 'pcfmetext_form_field' ), 10, 4 );
		add_filter( 'woocommerce_form_field_heading', array( $this, 'pcfmeheading_form_field' ), 10, 4 );
		add_filter( 'woocommerce_form_field_password', array( $this, 'pcfmetext_form_field' ), 10, 4 );
		add_filter( 'woocommerce_form_field_email', array( $this, 'pcfmetext_form_field' ), 10, 4 );
		add_filter( 'woocommerce_form_field_tel', array( $this, 'pcfmetext_form_field' ), 10, 4 );
		add_filter( 'woocommerce_form_field_number', array( $this, 'pcfmetext_form_field' ), 10, 4 );
		add_filter( 'woocommerce_form_field_textarea', array( $this, 'pcfmetextarea_form_field' ), 10, 4 );
		add_filter( 'woocommerce_form_field_checkbox', array( $this, 'pcfmecheckbox_form_field' ), 10, 4 );
		add_filter( 'woocommerce_form_field_radio', array( $this, 'radio_form_field' ), 10, 4 );
     	add_filter( 'woocommerce_form_field_pcfmeselect', array( $this, 'pcfmeselect_form_field' ), 10, 4 );
	    add_filter( 'woocommerce_form_field_datepicker', array( $this, 'datepicker_form_field' ), 10, 4 );
	    add_filter( 'woocommerce_form_field_datetimepicker', array( $this, 'datetimepicker_form_field' ), 10, 4 );
	    add_filter( 'woocommerce_form_field_timepicker', array( $this, 'timepicker_form_field' ), 10, 4 );
	    add_filter( 'woocommerce_form_field_daterangepicker', array( $this, 'daterangepicker_form_field' ), 10, 4 );
	    add_filter( 'woocommerce_form_field_datetimerangepicker', array( $this, 'datetimerangepicker_form_field' ), 10, 4 );
		add_filter( 'woocommerce_form_field_multiselect', array( $this, 'multiselect_form_field' ), 10, 4 );
		add_filter( 'woocommerce_form_field_paragraph', array( $this, 'paragraph_form_field' ), 10, 4 );
		
		add_filter( 'wp_enqueue_scripts', array( &$this, 'add_checkout_frountend_scripts' ));
	 }



	 
	 public function add_checkout_frountend_scripts() {
	   global $post;

	    $pcfme_woo_version    = pcfme_get_woo_version_number();
	    $pcfme_extra_settings = get_option('pcfme_extra_settings');

	    if (isset($pcfme_extra_settings['datepicker_format'])) {
	    	$datepicker_format = $pcfme_extra_settings['datepicker_format'];
	    } else {
	    	$datepicker_format = 01;
	    }


	    if (isset($pcfme_extra_settings['timepicker_interval']) && ($pcfme_extra_settings['timepicker_interval'] == 02)) {
	    	$timepicker_interval = 30;
	    } else {
	    	$timepicker_interval = 60;
	    }

	    if (isset($pcfme_extra_settings['timepicker_format'])) {
	    	$timepicker_format = $pcfme_extra_settings['timepicker_format'];
	    }

	    if (isset($pcfme_extra_settings['allowed_times']) && ($pcfme_extra_settings['allowed_times'] != '')) {
	    	$allowed_times = $pcfme_extra_settings['allowed_times'];
	 
	    } else {

	        $allowed_times = '';
	    }


	    if (!empty($pcfme_extra_settings['datepicker_disable_days'])) {
		    $days_to_exclude = implode(',', $pcfme_extra_settings['datepicker_disable_days']); 
	    } else { 
	        $days_to_exclude=''; 
	    }


	    $datetimepicker_lang = isset($pcfme_extra_settings['datetimepicker_lang']) ? $pcfme_extra_settings['datetimepicker_lang'] : "en";

	    $week_starts_on = isset($pcfme_extra_settings['week_starts_on']) ? $pcfme_extra_settings['week_starts_on'] : "sunday";

	    $dt_week_starts_on = isset($pcfme_extra_settings['dt_week_starts_on']) ? $pcfme_extra_settings['dt_week_starts_on'] : 0;
	    

	    if ( is_checkout() || is_account_page() ) {

	     
		 
		 wp_enqueue_style( 'jquery-ui', ''.pcfme_PLUGIN_URL.'assets/css/jquery-ui.css' );

		 wp_enqueue_script( 'jquery.datetimepicker', ''.pcfme_PLUGIN_URL.'assets/js/jquery.datetimepicker.js',array('jquery') );
         
         wp_enqueue_script( 'moment', ''.pcfme_PLUGIN_URL.'assets/js/moment.js');
		 wp_enqueue_script( 'daterangepicker', ''.pcfme_PLUGIN_URL.'assets/js/daterangepicker.js',array('moment'));
		 
		 if ($pcfme_woo_version < 2.3) {
		 	wp_enqueue_script( 'pcfme-frontend1', ''.pcfme_PLUGIN_URL.'assets/js/frontend1.js' );
		 } else {
            wp_enqueue_script( 'pcfme-frontend2', ''.pcfme_PLUGIN_URL.'assets/js/frontend2.js' );
		 }
         
        $pcfmefrontend_array = array( 
		    'datepicker_format'               => $datepicker_format,
		    'timepicker_interval'             => $timepicker_interval,
		    'allowed_times'                   => $allowed_times,
		    'days_to_exclude'                 => $days_to_exclude,
		    'datetimepicker_lang'             => $datetimepicker_lang,
		    'week_starts_on'                  => $week_starts_on,
		    'dt_week_starts_on'               => $dt_week_starts_on
		);
         
         wp_localize_script( 'pcfme-frontend2', 'pcfmefrontend', $pcfmefrontend_array );




		 wp_enqueue_style( 'pcfme-frontend', ''.pcfme_PLUGIN_URL.'assets/css/frontend.css' );

		 wp_enqueue_style( 'jquery.datetimepicker', ''.pcfme_PLUGIN_URL.'assets/css/jquery.datetimepicker.css' );

		 wp_enqueue_style( 'daterangepicker', ''.pcfme_PLUGIN_URL.'assets/css/daterangepicker.css' );
		}
	 }
	 
	 

      
	  public function pcfmetext_form_field( $field, $key, $args, $value ) {

	  	 $key = isset($args['field_key']) ? $args['field_key'] : $key;

         if ( ( ! empty( $args['clear'] ) ) ) $after = '<div class="clear"></div>'; else $after = '';
	  
	     if ( $args['required'] ) {
			  $args['class'][] = 'validate-required';
			  $required = ' <abbr class="required" title="' . esc_attr__( 'required', 'pcfme'  ) . '">*</abbr>';
		  } else {
			$required = '';
		  }
		 
		 
		 
		 
	    if (isset($args['visibility']) && ($args['visibility'] == 'field-specific')) {

			 $pcfme_conditional_class = pcfme_get_conditional_class($args['conditional']);
			 
		} else {

			 $pcfme_conditional_class  = '';
		}


		if (isset($args['visibility']) && ($args['visibility'] == 'shipping-specific')) {

			 $pcfme_conditional_shipping_class = pcfme_get_conditional_shipping_class($args['shipping']);
			 
		} else {

			 $pcfme_conditional_shipping_class  = '';
		}

        

		if (isset($args['visibility']) && ($args['visibility'] == 'payment-specific')) {

			 $pcfme_conditional_payment_class = pcfme_get_conditional_payment_class($args['payment']);
			 
		} else {

			 $pcfme_conditional_payment_class  = '';
		}

        $field = '<p class="form-row ' . implode( ' ', $args['class'] ) .' ' . $pcfme_conditional_class .' ' . $pcfme_conditional_shipping_class . '' . $pcfme_conditional_payment_class . '" id="' . $key . '_field">
            <label for="' . $key . '" class="' . implode( ' ', $args['label_class'] ) .'">' . $args['label']. $required . '</label>
            <input type="' . esc_attr( $args['type'] ) . '" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) .'  '. pcfmeinput_conditional_class($key) .'" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '" ' . $args['maxlength'] . ' ' . $args['autocomplete'] . ' value="' . esc_attr( $value ) . '" />
        </p>' . $after;
         

        return $field;
      }
	  
	  
	  public function pcfmeheading_form_field($field, $key, $args, $value) {

         if ( ( ! empty( $args['clear'] ) ) ) $after = '<div class="clear"></div>'; else $after = '';
	  
	     if ( $args['required'] ) {
			  $args['class'][] = 'validate-required';
			  $required = ' <abbr class="required" title="' . esc_attr__( 'required', 'pcfme'  ) . '">*</abbr>';
		  } else {
			$required = '';
		  }
		 
		 
		 
		 
	     if (isset($args['visibility']) && ($args['visibility'] == 'field-specific')) {

			$pcfme_conditional_class = pcfme_get_conditional_class($args['conditional']);
			 
		 } else {

			$pcfme_conditional_class  = '';
		 }

        $field = '<h3 class="form-row ' . implode( ' ', $args['class'] ) .' ' . $pcfme_conditional_class .'" id="' . $key . '_field">
		
            <span for="' . $key . '" class="pcfme_heading ' . implode( ' ', $args['label_class'] ) .'">' . $args['label']. $required . '</span>
			
        </h3>' . $after;
         

        return $field;
      }


    /**
     * Paragraph Field
     * params $field - 
     * params $key- unique key
     * $args- required,placeholder,label etc
     * $value- default value
     */


    public function paragraph_form_field( $field, $key, $args, $value) {

         if ( ( ! empty( $args['clear'] ) ) ) $after = '<div class="clear"></div>'; else $after = '';
	  
	     if ( $args['required'] ) {
			  $args['class'][] = 'validate-required';
			  $required = ' <abbr class="required" title="' . esc_attr__( 'required', 'pcfme'  ) . '">*</abbr>';
		  } else {
			$required = '';
		  }
		 
		 
		 
		 
	     if (isset($args['visibility']) && ($args['visibility'] == 'field-specific')) {

			$pcfme_conditional_class = pcfme_get_conditional_class($args['conditional']);
			 
		 } else {

			$pcfme_conditional_class  = '';
		 }

        $field = '<p class="form-row ' . implode( ' ', $args['class'] ) .' ' . $pcfme_conditional_class .'" id="' . $key . '_field">
		
            <span for="' . $key . '" class="pcfme_heading ' . implode( ' ', $args['label_class'] ) .'">' . $args['label']. $required . '</span>
			
        </p>' . $after;
         

        return $field;
    }
	  

	  
	public function pcfmetextarea_form_field($field,$key, $args, $value ) {

		 $key = isset($args['field_key']) ? $args['field_key'] : $key;

         if ( ( ! empty( $args['clear'] ) ) ) $after = '<div class="clear"></div>'; else $after = '';
	  
	     if ( $args['required'] ) {
			  $args['class'][] = 'validate-required';
			  $required = ' <abbr class="required" title="' . esc_attr__( 'required', 'pcfme'  ) . '">*</abbr>';
		  } else {
			$required = '';
		  }
		  
		 if (isset($args['visibility']) && ($args['visibility'] == 'field-specific')) {
			 
			 $pcfme_conditional_class = pcfme_get_conditional_class($args['conditional']);
			 
		 } else {
			 $pcfme_conditional_class  = '';
		 }
		
	    

        $field = '<p class="form-row ' . implode( ' ', $args['class'] ) .' ' . $pcfme_conditional_class .'" id="' . $key . '_field">
            <label for="' . $key . '" class="' . implode( ' ', $args['label_class'] ) .'">' . $args['label']. $required . '</label>
            <textarea name="' . esc_attr( $key ) . '" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) .'  '. pcfmeinput_conditional_class($key) .'" id="' . $key . '" placeholder="' . esc_attr( $args['placeholder'] ) . '" ' . $args['maxlength'] . ' ' . $args['autocomplete'] . ' ' . ( empty( $args['custom_attributes']['rows'] ) ? ' rows="2"' : '' ) . ( empty( $args['custom_attributes']['cols'] ) ? ' cols="5"' : '' ) . '>'. esc_textarea( $value  ) .'</textarea>
        </p>' . $after;
         

        return $field;
      }
	  
	 public function pcfmecheckbox_form_field($field,$key, $args, $value) {

	 	 $key = isset($args['field_key']) ? $args['field_key'] : $key;

         if ( ( ! empty( $args['clear'] ) ) ) $after = '<div class="clear"></div>'; else $after = '';
	  
	     if ( $args['required'] ) {
			  $args['class'][] = 'validate-required';
			  $required = ' <abbr class="required" title="' . esc_attr__( 'required', 'pcfme'  ) . '">*</abbr>';
		  } else {
			$required = '';
		  }
		
		 if (isset($args['visibility']) && ($args['visibility'] == 'field-specific')) {

			 $pcfme_conditional_class = pcfme_get_conditional_class($args['conditional']);
			 
		 } else {
			 $pcfme_conditional_class  = '';
		 }
	   

         $field = '<p class="form-row ' . implode( ' ', $args['class'] ) .' ' . $pcfme_conditional_class .'" id="' . $key . '_field"><label class="checkbox ' . implode( ' ', $args['label_class'] ) .' ' . implode( ' ', $args['class'] ) .' ' . $pcfme_conditional_class .'" ><input type="' . esc_attr( $args['type'] ) . '" class="input-checkbox ' . esc_attr( implode( ' ', $args['input_class'] ) ) .' ' . $pcfme_conditional_class .' '. pcfmeinput_conditional_class($key) .'" name="' . esc_attr( $key ) . '" id="' . $key . '" value="yes" '.checked( $value, 'yes' , false ) .' /> '
						 . $args['label'] . $required . '</label></p>' . $after;
         

        return $field;
      }
     
      public function radio_form_field($field, $key, $args, $value ) {
      
	    $key = isset($args['field_key']) ? $args['field_key'] : $key;
	  
        if ( ( ! empty( $args['clear'] ) ) ) $after = '<div class="clear"></div>'; else $after = '';
        
		if ( $args['required'] ) {
			$args['class'][] = 'validate-required';
			$required = ' <abbr class="required" title="' . esc_attr__( 'required', 'pcfme'  ) . '">*</abbr>';
		} else {
			$required = '';
		}
		
        
		
		if (isset($args['visibility']) && ($args['visibility'] == 'field-specific')) {
			 
			$pcfme_conditional_class = pcfme_get_conditional_class($args['conditional']);
			 
		 } else {
			
			$pcfme_conditional_class  = '';
		 }

		 $options = '';

		if (! empty ($args['placeholder'])) {
		
		    $value    = $args['placeholder'];
	    }

        if ( !empty( $args[ 'options' ] ) ) {
		  
	       foreach ( $args[ 'options' ] as $option_key => $option_text ) {
	       	
	       	  $option_key = preg_replace('/\s+/', '_', $option_key);

			  $options .= '&nbsp;&nbsp;<input type="radio" name="' . $key . '" id="' . $key . '" value="' . $option_key . '" ' . checked( $value, $option_key, false ) . 'class="select  '. pcfmeinput_conditional_class($key) .'">  ' . $option_text . '';
		   }
            
            
			$field = '<p class="form-row ' . implode( ' ', $args[ 'class' ] ) . ' ' . $pcfme_conditional_class .'" id="' . $key . '_field">
			          <label for="' . $key . '" class="' . implode( ' ', $args[ 'label_class' ] ) . '">' . $args[ 'label' ] . $required . '</label>' . $options . '</p>' . $after;
        }



        return $field;
       
     }
      

     public function pcfmeselect_form_field( $field, $key, $args, $value) {

     $key = isset($args['field_key']) ? $args['field_key'] : $key;

     if ( ( ! empty( $args['clear'] ) ) ) $after = '<div class="clear"></div>'; else $after = '';
	  
	 if ( $args['required'] ) {
			$args['class'][] = 'validate-required';
			$required = ' <abbr class="required" title="' . esc_attr__( 'required', 'pcfme'  ) . '">*</abbr>';
		} else {
			$required = '';
		}
	 
	 if (isset($args['visibility']) && ($args['visibility'] == 'field-specific')) {
			
			$pcfme_conditional_class = pcfme_get_conditional_class($args['conditional']);
			 
		 } else {
			 $pcfme_conditional_class  = '';
    }	  
	    $options = '';
	
	if (! empty ($args['placeholder'])) {
		$options .= '<option></option>';
		$value    = $args['placeholder'];
	}
    

    if ( ! empty( $args['options'] ) ) {
        foreach ( $args['options'] as $option_key => $option_text ) {

        	$option_key = preg_replace('/\s+/', '_', $option_key);

            $options .= '<option value="' . $option_key . '" '. selected( $value, $option_key, false ) . '>' . $option_text .'</option>';
        }

        $field = '<p class="form-row ' . implode( ' ', $args['class'] ) .' ' . $pcfme_conditional_class .'" id="' . $key . '_field">
            <label for="' . $key . '" class="' . implode( ' ', $args['label_class'] ) .'">' . $args['label']. $required . '</label>
            <select placeholder="'.$args['placeholder'].'" name="' . $key . '" id="' . $key . '" class="select pcfme-singleselect  '. pcfmeinput_conditional_class($key) .'" >
				' . $options . '
            </select>
        </p>' . $after;
      }

       return $field;
     }


	 
	 public function multiselect_form_field( $field, $key, $args, $value) {
	 	$key = isset($args['field_key']) ? $args['field_key'] : $key;

	  if ( ( ! empty( $args['clear'] ) ) ) $after = '<div class="clear"></div>'; else $after = '';
	  
	    if ( $args['required'] ) {
			$args['class'][] = 'validate-required';
			$required = ' <abbr class="required" title="' . esc_attr__( 'required', 'pcfme'  ) . '">*</abbr>';
		} else {
			$required = '';
		}



	
     
       
	    $optionsarray='';
	    
		if (isset($value)) {
			   
			 foreach ($value as $optionvalue) {
			       $optionsarray.=''.$optionvalue.',';
			    } 
			  
			$optionsarray=substr_replace($optionsarray, "", -1);
			
	    }
		
		
	    if (isset($args['visibility']) && ($args['visibility'] == 'field-specific')) {

			$pcfme_conditional_class = pcfme_get_conditional_class($args['conditional']);
			 
		 } else {
			 $pcfme_conditional_class  = '';
		 }
		
	    $options = '';

    if ( ! empty( $args['options'] ) ) {
        foreach ( $args['options'] as $option_key => $option_text ) {

        	$option_key = preg_replace('/\s+/', '_', $option_key);

			if (preg_match('/\b'.$option_key.'\b/', $optionsarray )) {
				$selectstatus = 'selected';
			} else {
				$selectstatus = '';
			}

            $options .= '<option value="' . $option_key . '" '. $selectstatus . '>' . $option_text .'</option>';
        }

        $field = '<p class="form-row ' . implode( ' ', $args['class'] ) .' ' . $pcfme_conditional_class .'" id="' . $key . '_field">
            <label for="' . $key . '" class="' . implode( ' ', $args['label_class'] ) .'">' . $args['label']. $required . '</label>
            <select name="' . $key . '[]" id="' . $key . '" class="select pcfme-multiselect  '. pcfmeinput_conditional_class($key) .'" multiple="multiple">
                ' . $options . '
            </select>
        </p>' . $after;
      }

       return $field;
	 }
	 
	 
	public function datepicker_form_field(  $field, $key, $args, $value) {
		$key = isset($args['field_key']) ? $args['field_key'] : $key;

	    if ( ( ! empty( $args['clear'] ) ) ) $after = '<div class="clear"></div>'; else $after = '';

		if ( $args['required'] ) {
			$args['class'][] = 'validate-required';
			$required = ' <abbr class="required" title="' . esc_attr__( 'required', 'pcfme'  ) . '">*</abbr>';
		} else {
			$required = '';
		}
		
		 if (isset($args['visibility']) && ($args['visibility'] == 'field-specific')) {

			$pcfme_conditional_class = pcfme_get_conditional_class($args['conditional']);
			 
		 } else {
			 $pcfme_conditional_class  = '';
		 }
		
		if (isset($args['disable_past'])) {
			$datepicker_class='pcfme-datepicker-disable-past';
		} else {
			$datepicker_class='pcfme-datepicker';
		}

		$args['maxlength'] = ( $args['maxlength'] ) ? 'maxlength="' . absint( $args['maxlength'] ) . '"' : '';

		if ( ! empty( $args['validate'] ) )
			foreach( $args['validate'] as $validate )
				$args['class'][] = 'validate-' . $validate;

		$field = '<p class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .' ' . $pcfme_conditional_class .'" id="' . esc_attr( $key ) . '_field">';

		if ( $args['label'] )
			$field .= '<label for="' . esc_attr( $key ) . '" class="' . implode( ' ', $args['label_class'] ) .'">' . $args['label'] . $required . '</label>';

		$field .= '<input type="text" class="'. $datepicker_class .' input-text  '. pcfmeinput_conditional_class($key) .'" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" placeholder="' . $args['placeholder'] . '" '.$args['maxlength'].' value="' . esc_attr( $value ) . '" />
			</p>' . $after;

		return $field;
	 }



	public function datetimepicker_form_field( $field, $key, $args, $value) {
		$key = isset($args['field_key']) ? $args['field_key'] : $key;

	    if ( ( ! empty( $args['clear'] ) ) ) $after = '<div class="clear"></div>'; else $after = '';

		if ( $args['required'] ) {
			$args['class'][] = 'validate-required';
			$required = ' <abbr class="required" title="' . esc_attr__( 'required', 'pcfme'  ) . '">*</abbr>';
		} else {
			$required = '';
		}
		
		 if (isset($args['visibility']) && ($args['visibility'] == 'field-specific')) {

			 $pcfme_conditional_class = pcfme_get_conditional_class($args['conditional']);
			 
		 } else {
			 $pcfme_conditional_class  = '';
		 }
		
		if (isset($args['disable_past'])) {
			$datepicker_class='pcfme-datetimepicker-disable-past';
		} else {
			$datepicker_class='pcfme-datetimepicker';
		}

		$args['maxlength'] = ( $args['maxlength'] ) ? 'maxlength="' . absint( $args['maxlength'] ) . '"' : '';

		if ( ! empty( $args['validate'] ) )
			foreach( $args['validate'] as $validate )
				$args['class'][] = 'validate-' . $validate;

		$field = '<p class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .' ' . $pcfme_conditional_class .'" id="' . esc_attr( $key ) . '_field">';

		if ( $args['label'] )
			$field .= '<label for="' . esc_attr( $key ) . '" class="' . implode( ' ', $args['label_class'] ) .'">' . $args['label'] . $required . '</label>';

		$field .= '<input type="text" class="'. $datepicker_class .' input-text  '. pcfmeinput_conditional_class($key) .'" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" placeholder="' . $args['placeholder'] . '" '.$args['maxlength'].' value="' . esc_attr( $value ) . '" />
			</p>' . $after;

		return $field;
	 }


	public function daterangepicker_form_field(  $field, $key, $args, $value ) {

		$key = isset($args['field_key']) ? $args['field_key'] : $key;

	    if ( ( ! empty( $args['clear'] ) ) ) $after = '<div class="clear"></div>'; else $after = '';

		if ( $args['required'] ) {
			$args['class'][] = 'validate-required';
			$required = ' <abbr class="required" title="' . esc_attr__( 'required', 'pcfme'  ) . '">*</abbr>';
		} else {
			$required = '';
		}
		
		 if (isset($args['visibility']) && ($args['visibility'] == 'field-specific')) {

			 $pcfme_conditional_class = pcfme_get_conditional_class($args['conditional']);
			 
		 } else {
			 $pcfme_conditional_class  = '';
		 }
		
		if (isset($args['disable_past'])) {
			$datepicker_class='pcfme-daterangepicker-disable-past';
		} else {
			$datepicker_class='pcfme-daterangepicker';
		}

		$args['maxlength'] = ( $args['maxlength'] ) ? 'maxlength="' . absint( $args['maxlength'] ) . '"' : '';

		if ( ! empty( $args['validate'] ) )
			foreach( $args['validate'] as $validate )
				$args['class'][] = 'validate-' . $validate;

		$field = '<p class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .' ' . $pcfme_conditional_class .'" id="' . esc_attr( $key ) . '_field">';

		if ( $args['label'] )
			$field .= '<label for="' . esc_attr( $key ) . '" class="' . implode( ' ', $args['label_class'] ) .'">' . $args['label'] . $required . '</label>';

		$field .= '<input type="text" class="'. $datepicker_class .' input-text  '. pcfmeinput_conditional_class($key) .'" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" placeholder="' . $args['placeholder'] . '" '.$args['maxlength'].' value="' . esc_attr( $value ) . '" />
			</p>' . $after;

		return $field;
	}



	public function datetimerangepicker_form_field(  $field, $key, $args, $value ) {

		$key = isset($args['field_key']) ? $args['field_key'] : $key;

	    if ( ( ! empty( $args['clear'] ) ) ) $after = '<div class="clear"></div>'; else $after = '';

		if ( $args['required'] ) {
			$args['class'][] = 'validate-required';
			$required = ' <abbr class="required" title="' . esc_attr__( 'required', 'pcfme'  ) . '">*</abbr>';
		} else {
			$required = '';
		}
		
		 if (isset($args['visibility']) && ($args['visibility'] == 'field-specific')) {
			
			$pcfme_conditional_class = pcfme_get_conditional_class($args['conditional']);
			 
		 } else {
			
			$pcfme_conditional_class  = '';
		 }
		
		if (isset($args['disable_past'])) {
			$datepicker_class='pcfme-datetimerangepicker-disable-past';
		} else {
			$datepicker_class='pcfme-datetimerangepicker';
		}

		$args['maxlength'] = ( $args['maxlength'] ) ? 'maxlength="' . absint( $args['maxlength'] ) . '"' : '';

		if ( ! empty( $args['validate'] ) )
			foreach( $args['validate'] as $validate )
				$args['class'][] = 'validate-' . $validate;

		$field = '<p class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .' ' . $pcfme_conditional_class .'" id="' . esc_attr( $key ) . '_field">';

		if ( $args['label'] )
			$field .= '<label for="' . esc_attr( $key ) . '" class="' . implode( ' ', $args['label_class'] ) .'">' . $args['label'] . $required . '</label>';

		$field .= '<input type="text" class="'. $datepicker_class .' input-text  '. pcfmeinput_conditional_class($key) .'" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" placeholder="' . $args['placeholder'] . '" '.$args['maxlength'].' value="' . esc_attr( $value ) . '" />
			</p>' . $after;

		return $field;
	}


	public function timepicker_form_field(  $field,$key, $args, $value) {
		$key = isset($args['field_key']) ? $args['field_key'] : $key;
		
	    if ( ( ! empty( $args['clear'] ) ) ) $after = '<div class="clear"></div>'; else $after = '';

		if ( $args['required'] ) {
			$args['class'][] = 'validate-required';
			$required = ' <abbr class="required" title="' . esc_attr__( 'required', 'pcfme'  ) . '">*</abbr>';
		} else {
			$required = '';
		}
		
		 if (isset($args['visibility']) && ($args['visibility'] == 'field-specific')) {

			$pcfme_conditional_class = pcfme_get_conditional_class($args['conditional']);
			 
		 } else {
			 $pcfme_conditional_class  = '';
		 }
		
		
			$datepicker_class='pcfme-timepicker';
		

		$args['maxlength'] = ( $args['maxlength'] ) ? 'maxlength="' . absint( $args['maxlength'] ) . '"' : '';

		if ( ! empty( $args['validate'] ) )
			foreach( $args['validate'] as $validate )
				$args['class'][] = 'validate-' . $validate;

		$field = '<p class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .' ' . $pcfme_conditional_class .'" id="' . esc_attr( $key ) . '_field">';

		if ( $args['label'] )
			$field .= '<label for="' . esc_attr( $key ) . '" class="' . implode( ' ', $args['label_class'] ) .'">' . $args['label'] . $required . '</label>';

		$field .= '<input type="text" class="'. $datepicker_class .' input-text  '. pcfmeinput_conditional_class($key) .'" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" placeholder="' . $args['placeholder'] . '" '.$args['maxlength'].' value="' . esc_attr( $value ) . '" />
			</p>' . $after;

		return $field;
	}
}

new pcfme_manage_extrafield_class();
?>