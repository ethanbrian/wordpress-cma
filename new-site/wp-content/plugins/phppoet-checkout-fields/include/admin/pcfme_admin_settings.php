<?php

	
class pcfme_add_settings_page_class {
	
	
	
	private $billing_settings_key       = 'pcfme_billing_settings';
	private $shipping_settings_key      = 'pcfme_shipping_settings';
	private $additional_settings_key    = 'pcfme_additional_settings';
	private $extra_settings_key         = 'pcfme_extra_settings';
	private $pcfme_plugin_options       = 'pcfme_plugin_options';
    private $pcfme_pcfme_plugin_settings_tabs = array();	
	
	
	public function __construct() {
	    
		
		
	    add_action( 'init', array( $this, 'load_settings' ) );
		add_action( 'admin_init', array( $this, 'register_billing_settings' ) );
		add_action( 'admin_init', array( $this, 'register_shipping_settings' ) );
		add_action( 'admin_init', array( $this, 'register_additional_settings' ) );
		add_action( 'admin_init', array( $this, 'register_extra_settings' ) );
		add_action( 'admin_menu', array( $this, 'add_admin_menus' ) ,100);
		add_action( 'admin_enqueue_scripts', array($this, 'pcfme_register_admin_scripts'));
		add_action( 'admin_enqueue_scripts', array($this, 'pcfme_load_admin_default_css'));
        add_action( 'wp_ajax_restore_billing_fields', array( $this, 'restore_billing_fields' ) );
		add_action( 'wp_ajax_restore_shipping_fields', array( $this, 'restore_shipping_fields' ) );
		add_action( 'wp_ajax_pdfmegetajaxproductslist', array( $this, 'pcfme_get_posts_ajax_callback' ) );
		
	}
	
	public function pcfme_get_posts_ajax_callback(){
 
	
	  $return = array();
      $post_type_array = array('product', 'product_variation');
	  // you can use WP_Query, query_posts() or get_posts() here - it doesn't matter
	  $search_results = new WP_Query( array( 
		's'=> $_GET['q'], // the search query
		'post_status' => 'publish', // if you don't want drafts to be returned
		'ignore_sticky_posts' => 1,
		'post_type'           => $post_type_array,
		'posts_per_page' => 50 // how much to show at once
	  ) );
	  
	
	  if( $search_results->have_posts() ) :
		while( $search_results->have_posts() ) : $search_results->the_post();	
			// shorten the title a little
			$title = ( mb_strlen( $search_results->post->post_title ) > 50 ) ? mb_substr( $search_results->post->post_title, 0, 49 ) . '...' : $search_results->post->post_title;
			$finaltitle='#'. $search_results->post->ID.'- '.$title.'';
			$return[] = array( $search_results->post->ID, $finaltitle ); // array( Post ID, Post Title )
		endwhile;
	  endif;
	   echo json_encode( $return );
	  die;
    }
	
	
	public function restore_billing_fields() {
	   delete_option( $this->billing_settings_key );
	   die();
	}
	
	public function restore_shipping_fields() {
	   delete_option( $this->shipping_settings_key );
	   die();
	}

	
	
	
	
	
	public function load_settings() {
		$this->billing_settings = (array) get_option( $this->billing_settings_key );
		
		$this->billing_settings = array_merge( array(
		), $this->billing_settings );
		
		$this->shipping_settings = (array) get_option( $this->shipping_settings_key );
		
		$this->shipping_settings = array_merge( array(
		), $this->shipping_settings );

		$this->additional_settings = (array) get_option( $this->additional_settings_key );
		
		$this->additional_settings = array_merge( array(
		), $this->additional_settings );

		$this->extra_settings = (array) get_option( $this->extra_settings_key );
		
		$this->extra_settings = array_merge( array(
		), $this->extra_settings );
		
		
		
		
	}



	public function pcfme_load_admin_default_css() {

	    wp_enqueue_style( 'woomatrix_admin_menu_css', ''.pcfme_PLUGIN_URL.'assets/css/admin_menu.css' );
	    wp_enqueue_script( 'woomatrix_admin_menu_js', ''.pcfme_PLUGIN_URL.'assets/js/admin_menu.js' );

	}
	
	

	
	/*
	 * registers admin scripts via admin enqueue scripts
	 */
	public function pcfme_register_admin_scripts($hook) {
	    global $billing_pcfmesettings_page;
			
		if ( $hook == $billing_pcfmesettings_page ) {
		     
 
		 
		 
		 
		    wp_enqueue_style( 'select2', ''.pcfme_PLUGIN_URL.'assets/css/select2.css' );
		    wp_enqueue_script( 'select2', ''.pcfme_PLUGIN_URL.'assets/js/select2.js' ,array('jquery') );
		 
		 
		    wp_enqueue_script( 'bootstrap-min', ''.pcfme_PLUGIN_URL.'assets/js/bootstrap-min.js' );
		    wp_enqueue_script( 'jquery-ui-sortable');
		 
		 
		    wp_enqueue_script( 'jquery.tag-editor', ''.pcfme_PLUGIN_URL.'assets/js/jquery.tag-editor.js' );
		    wp_enqueue_style( 'jquery.tag-editor', ''.pcfme_PLUGIN_URL.'assets/css/jquery.tag-editor.css' );
		    wp_enqueue_script( 'pcfmeadmin', ''.pcfme_PLUGIN_URL.'assets/js/pcfmeadmin.js' );
		 
         
		    wp_enqueue_style( 'pcfmeadmin', ''.pcfme_PLUGIN_URL.'assets/css/pcfmeadmin.css' );
		    wp_enqueue_style ( 'bootstrap',''.pcfme_PLUGIN_URL.'assets/css/bootstrap.css');
		 

		 
		    wp_enqueue_script( 'pcfme-frontend1', ''.pcfme_PLUGIN_URL.'assets/js/frontend1.js' );
		    wp_enqueue_style( 'jquery-ui', ''.pcfme_PLUGIN_URL.'assets/css/jquery-ui.css' );
		    wp_enqueue_style( 'pcfme-frontend', ''.pcfme_PLUGIN_URL.'assets/css/frontend.css' );
		 


        

            $billing_select = '';

            $country_fields = 'billing_country,billing_state';

		    $billing_select .= '<select class="checkout_field_conditional_parentfield" name="">';
				     
	        foreach ($this->billing_settings as $optionkey=>$optionvalue) { 
				   
		        if ( (isset ($optionvalue['type']) && ($optionvalue['type'] == 'email')) || (preg_match('/\b'.$optionkey.'\b/', $country_fields ))) { 
					 
			    } else { 

				    if (isset($optionvalue['label']))  { 

					    $optionlabel = $optionvalue['label']; 

				    } else { 

					    $optionlabel = $optionkey; 
				    }  
					    	
				    $billing_select .='<option value="'.$optionkey.'">'.$optionlabel.'</option>';
			    } 
		    } 
				 
		    $billing_select .= '</select>';



		    $shipping_select = '';

            $country_fields = 'shipping_country,shipping_state';

		    $shipping_select .= '<select class="checkout_field_conditional_parentfield" name="">';

				     
	        foreach ($this->shipping_settings as $optionkey=>$optionvalue) { 
				   
		        if ( (isset ($optionvalue['type']) && ($optionvalue['type'] == 'email')) || (preg_match('/\b'.$optionkey.'\b/', $country_fields ))) { 
					 
			    } else { 

				    if (isset($optionvalue['label']))  { 

					    $optionlabel = $optionvalue['label']; 

				    } else { 

					    $optionlabel = $optionkey; 
				    }  
					    	
				    $shipping_select .='<option value="'.$optionkey.'">'.$optionlabel.'</option>';
			    } 
		    } 
				 
		    $shipping_select .= '</select>';



		    $additional_select  = '';

            $country_fields   = '';

		    $additional_select .= '<select class="checkout_field_conditional_parentfield" name="">';
				     
	        foreach ($this->additional_settings as $optionkey=>$optionvalue) { 
				   
		        if ( (isset ($optionvalue['type']) && ($optionvalue['type'] == 'email')) || (preg_match('/\b'.$optionkey.'\b/', $country_fields ))) { 
					 
			    } else { 

				    if (isset($optionvalue['label']))  { 

					    $optionlabel = $optionvalue['label']; 

				    } else { 

					    $optionlabel = $optionkey; 
				    }  
					    	
				    $additional_select .='<option value="'.$optionkey.'">'.$optionlabel.'</option>';
			    } 
		    } 
				 
		    $additional_select .= '</select>';


        
		
		 
		    $translation_array = array( 
		        'removealert'               => esc_html__( 'Are you sure you want to delete?' ,'pcfme'),
		        'checkoutfieldtext'         => esc_html__( 'billing_field_' ,'pcfme'),
		        'checkoutfieldtext2'        => esc_html__( 'shipping_field_' ,'pcfme'),
		        'checkoutfieldtext3'        => esc_html__( 'additional_field_' ,'pcfme'),
		        'checkoutfieldtext4'        => esc_html__( 'billing field ' ,'pcfme'),
		        'checkoutfieldtext5'        => esc_html__( 'shipping field ' ,'pcfme'),
		        'checkoutfieldtext6'        => esc_html__( 'additional field ' ,'pcfme'),
		        'placeholder'               => esc_html__( 'Search and Select ' ,'pcfme'),
		        'restorealert'              => esc_html__( 'Restoring Default fields will undo all your Changes. Are you sure you want to do this ?' ,'pcfme'),
			    'optionplaceholder'         => esc_html__( 'Enter Option' ,'pcfme'),
			    'classplaceholder'          => esc_html__( 'Enter Class' ,'pcfme'),
			    'billing_select'            => $billing_select,
			    'shipping_select'           => $shipping_select,
			    'additional_select'         => $additional_select,
			    'showtext'                  => esc_html__( 'Show' ,'pcfme'),
			    'hidetext'                  => esc_html__( 'Hide' ,'pcfme'),
			    'valuetext'                 => esc_html__( 'If value of' ,'pcfme'),
			    'equaltext'                 => esc_html__( 'is equal to' ,'pcfme'),
			    'copiedalert'               => esc_html__( 'Field key successfully copied to clipboard.' ,'pcfme')
		    );
         
            wp_localize_script( 'pcfmeadmin', 'pcfmeadmin', $translation_array );
        }
	

	}


	
	
	public function register_billing_settings() {
		$this->pcfme_plugin_settings_tabs[$this->billing_settings_key] = esc_html__( 'Billing Fields' ,'pcfme');
		
		register_setting( $this->billing_settings_key, $this->billing_settings_key );
		add_settings_section( 'pcfme_section_billing', '', '', $this->billing_settings_key );
		add_settings_field( 'pcfme_billing_option', '', array( &$this, 'pcfme_field_billing_option' ), $this->billing_settings_key, 'pcfme_section_billing' );
	}
	
	public function register_shipping_settings() {
		$this->pcfme_plugin_settings_tabs[$this->shipping_settings_key] = esc_html__( 'Shipping Fields' ,'pcfme');
		
		register_setting( $this->shipping_settings_key, $this->shipping_settings_key );
		add_settings_section( 'pcfme_section_shipping', '', '', $this->shipping_settings_key );
		add_settings_field( 'pcfme_shipping_option', '', array( &$this, 'pcfme_field_shipping_option' ), $this->shipping_settings_key, 'pcfme_section_shipping' );
	}


	public function register_additional_settings() {
		$this->pcfme_plugin_settings_tabs[$this->additional_settings_key] = esc_html__( 'Additional Fields' ,'pcfme');
		
		register_setting( $this->additional_settings_key, $this->additional_settings_key );
		add_settings_section( 'pcfme_section_additional', '', '', $this->additional_settings_key );
		add_settings_field( 'pcfme_additional_option', '', array( &$this, 'pcfme_field_additional_option' ), $this->additional_settings_key, 'pcfme_section_additional' );
	}



	public function register_extra_settings() {
		$this->pcfme_plugin_settings_tabs[$this->extra_settings_key] = esc_html__( 'Settings' ,'pcfme');;
		
		register_setting( $this->extra_settings_key, $this->extra_settings_key );
		add_settings_section( 'pcfme_section_extra', '', '', $this->extra_settings_key );
		add_settings_field( 'pcfme_extra_option', '', array( &$this, 'pcfme_field_extra_option' ), $this->extra_settings_key, 'pcfme_section_extra' );
	}
	

	


	

	
	
	public function pcfme_field_billing_option() {
	    
		include ('forms/pcfme_admin_billing_fields_form.php');
  
		
	}
	
	public function pcfme_field_shipping_option() { 
	     
       include ('forms/pcfme_admin_shipping_fields_form.php');
		 
		 
	 }

	 public function pcfme_field_additional_option() { 
	     
       include ('forms/pcfme_admin_additional_fields_form.php');
		 
		 
	 }


	public function pcfme_field_extra_option() { 
	     
       include ('forms/pcfme_admin_extra_fields_form.php');
		 
		 
	}
	 

	
	
	

	public function add_admin_menus() {
	   global $billing_pcfmesettings_page;

	    add_menu_page(
          esc_html__( 'woomatrix', 'plps' ),
         'WooMatrix',
         'manage_woocommerce',
         'woomatrix',
         array($this,'plugin_options_page'),
         ''.pcfme_PLUGIN_URL.'assets/images/icon.png',
         70
        );
	    

        
	   
	    $billing_pcfmesettings_page = add_submenu_page( 'woomatrix', esc_html__('Checkout Fields Editor'), esc_html__('Checkout fields'), 'manage_woocommerce', $this->pcfme_plugin_options, array($this, 'plugin_options_page'));
	}
	
	
	public function plugin_options_page() {
	    global $woocommerce;
		$tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->billing_settings_key;
		global $billing_fields;
		$billing_fields = '';
		?>
		<div class="wrap">
		    <?php $this->plugin_options_tabs(); ?>
		
			<form method="post" class="<?php echo $tab; ?>" action="options.php">
				<?php wp_nonce_field( 'update-options' ); ?>
				<?php settings_fields( $tab ); ?>
				<?php do_settings_sections( $tab ); ?>
				
				
				<br />
				<center><input type="submit" name="submit" id="submit" class="btn btn-success" value="<?php echo esc_html__('Save Changes','pcfme'); ?>"></center>
				
				<?php 

				

				if (isset($tab) && ($tab == $this->extra_settings_key)) {
					global $woocommerce;
					$checkout_url = '#';
					$checkout_url = wc_get_checkout_url();
					?>
					<a type="button" target="_blank" href="<?php echo $checkout_url; ?>" id="pcfme_frontend_link" class="btn btn-primary pcfme_frontend_link">
						<span class="dashicons dashicons-welcome-view-site"></span>
						<?php echo esc_html__('Frontend','pcfme'); ?>
					</a>
					<?php
				}

				?> 
			</form>
			<br />
			
		</div>
		<div id="responsediv">
		</div>
		<?php
	}

    public function display_field_label($key,$field) {
		    if (isset($field['label'])) { 
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
			if (isset($field['type']) && ($field['type'] == "heading")) {
				$label ="";
			}
			return $label;
	}

	public function display_visual_preview($key,$field) { 
     global $woocommerce;
     
		?>
	 
	  <td width="30%">
	    <label class="">
		  <?php 
		  echo $this->display_field_label($key,$field);
		  
		  ?>
	    </label>
        
      </td>
	  <td width="30%">
	  	
	  </td>
	 
	<?php }
	
	
	public function show_fields_form($fields,$key,$field,$noticerowno,$slug,$required_slugs,$core_fields,$country_fields,$address2_field) { ?>
	      <?php
		    
            if (isset($field['width'])) {
                 
                $fieldwidth= $field['width'];
               	 
            } elseif (isset($field['class'])) {
                  
                foreach($field['class'] as $class) {
               	  	if (isset($class)) {
                        switch($class) {
                    	    case "form-row-first":
                                $fieldwidth='form-row-first';
						    break;

                    	    case "form-row-last":
                                $fieldwidth='form-row-last';
						    break;

                    	    default:
                    	        $fieldwidth='form-row-wide';
                    	}
                    }
               	} 
            }
	    
	    global $wp_roles;

        if ( ! isset( $wp_roles ) ) { 
    	    $wp_roles = new WP_Roles();  
        }
	
	    $roles = $wp_roles->roles;
        $shipping_methods = WC()->shipping->get_shipping_methods();

	    $payment_gateways = WC()->payment_gateways->get_available_payment_gateways();

	
		$catargs = array(
	      'orderby'                  => 'name',
	      'taxonomy'                 => 'product_cat',
	      'hide_empty'               => 0
	    );
		 
	  
		$categories           = get_categories( $catargs );  

      
		if (!empty($field['category'])) {
		       $chosencategories = implode(',', $field['category']); 
		} else { 
			   $chosencategories=''; 
		}

		if (!empty($field['role'])) {
		       $chosenroles = implode(',', $field['role']); 
		} else { 
			   $chosenroles=''; 
		}
			 
        switch($slug) {
		
		  case "pcfme_billing_settings":
		    $headlingtext  =''.esc_html__('billing_field_','pcfme').''.$noticerowno.'';
		    $mntext        ='billing';
		   break;
	
          case "pcfme_shipping_settings":
		    $headlingtext =''.esc_html__('shipping_field_','pcfme').''.$noticerowno.'';
		    $mntext       ='shipping';
		   break;

		   case "pcfme_additional_settings":
		     $headlingtext =''.esc_html__('additional_field_','pcfme').''.$noticerowno.'';
		     $mntext       ='additional';
		   break;
		
		
	       } ?>   

       
	   <div class="panel-group panel panel-default pcfme_list_item" id="pcfme_list_items_<?php echo $noticerowno; ?>" style="display:block;">
           <div class="panel-heading"> 
		
	     <table class="heading-table <?php echo $key; ?>_panel <?php if (isset($field['hide']) && ($field['hide'] == 1)) { echo "pcfme_disabled";} ?>">
	     	<tr>
	     		<td>
	     			<?php if (preg_match('/\b'.$key.'\b/', $core_fields )) { ?>
	     				<input type="checkbox" class="pcfme_accordion_onoff" parentkey="<?php echo $key; ?>" <?php if (!isset($field['hide']) || ($field['hide'] == 0)) { echo "checked";} ?>>
	     				<input type="hidden" class="<?php echo $key; ?>_hidden_checkbox" name="<?php echo $slug; ?>[<?php echo $key; ?>][hide]" value="<?php if (isset($field['hide'])) { echo ($field['hide']); } else { echo 0; } ?>" checked>
	     			<?php } else { ?>
                        <span class="glyphicon glyphicon-trash pcfme_trash_icon"></span>
	     			<?php } ?>


	     			<a class="accordion-toggle pcfme_edit_icon_a" data-toggle="collapse" data-parent="#accordion" href="#pcfme<?php echo $noticerowno; ?>">
	     				<span class="glyphicon glyphicon-edit pcfme_edit_icon"></span>
	     			</a>
	     		</td>

	     		<?php $this->display_visual_preview($key,$field); ?>

	     		
	     	</tr>
		  </table>
           </div>
           <div id="pcfme<?php echo $noticerowno; ?>" class="panel-collapse collapse">

		     <table class="table"> 
			 
			 

		     <tr class="pcfme_field_key_tr">
			    <td width="25%"><label for="<?php echo $key; ?>_type"><?php echo esc_html__('Field Key','pcfme'); ?></label></td>
			    <td width="75%" class="pcfme_field_key_tr">
			    	<?php 
                        if (isset($field['field_key']) && ($field['field_key'] != "")) { 
                         	$field_key = $field['field_key'];
                        } else { 
                        	$field_key = $key;
                        }
			    	?>
			    	<?php if (!preg_match('/\b'.$key.'\b/', $core_fields )) { ?>   
			    	    <input type="text" class="pcfme_change_key_input" clkey="<?php echo $key; ?>" name="<?php echo $slug; ?>[<?php echo $key; ?>][field_key]" value="<?php echo $field_key ?>">
			    	<?php } ?>

			   	    <span class="pcfme_field_key pcfme_field_key_<?php echo $key; ?>"><?php echo $field_key ?></span>
			   	    <span onclick="pcfme_copyToClipboard('.pcfme_copy_key_icon_<?php echo $key; ?>')" cpkey="<?php echo $field_key; ?>" title="<?php echo esc_html__('Copy to clipboard','pcfme'); ?>" class="glyphicon glyphicon-book pcfme_copy_key_icon pcfme_copy_key_icon_<?php echo $key; ?> "></span>

			   	</td>
		     </tr> 

			 <?php if (!preg_match('/\b'.$key.'\b/', $country_fields )) { ?>   
		       <tr>
	           <td width="25%"><label for="<?php echo $key; ?>_type"><?php echo esc_html__('Field Type','pcfme'); ?></label></td>
		       <td width="75%">
		          <select class="checkout_field_type" name="<?php echo $slug; ?>[<?php echo $key; ?>][type]" >
			        <option value="text" <?php if (isset($field['type']) && ($field['type'] == "text")) { echo "selected";} ?> ><?php echo esc_html__('Text','pcfme'); ?></option>
					<option value="heading" <?php if (isset($field['type']) && ($field['type'] == "heading")) { echo "selected";} ?> ><?php echo esc_html__('Heading','pcfme'); ?></option>
					<option value="paragraph" <?php if (isset($field['type']) && ($field['type'] == "paragraph")) { echo "selected";} ?> ><?php echo esc_html__('Paragraph','pcfme'); ?></option>
					<option value="email" <?php if (isset($field['type']) && ($field['type'] == "email")) { echo "selected";} ?> ><?php echo esc_html__('Email','pcfme'); ?></option>
					<option value="tel" <?php if (isset($field['type']) && ($field['type'] == "tel")) { echo "selected";} ?> ><?php echo esc_html__('Telephone Number','pcfme'); ?></option>
					<option value="number" <?php if (isset($field['type']) && ($field['type'] == "number")) { echo "selected";} ?> ><?php echo esc_html__('Number','pcfme'); ?></option>
			        <option value="password" <?php if (isset($field['type']) && ($field['type'] == "password")) { echo "selected";} ?>><?php echo esc_html__('Password','pcfme'); ?></option>
			        <option value="textarea" <?php if (isset($field['type']) && ($field['type'] == "textarea")) { echo "selected";} ?>><?php echo esc_html__('Textarea','pcfme'); ?></option>
					<option value="checkbox" <?php if (isset($field['type']) && ($field['type'] == "checkbox")) { echo "selected";} ?>><?php echo esc_html__('Checkbox','pcfme'); ?></option>
			        <option value="pcfmeselect" <?php if (isset($field['type']) && ($field['type'] == "pcfmeselect")) { echo "selected";} ?>><?php echo esc_html__('Select','pcfme'); ?></option>
					<option value="multiselect" <?php if (isset($field['type']) && ($field['type'] == "multiselect")) { echo "selected";} ?>><?php echo esc_html__('multiselect','pcfme'); ?></option>
			        <option value="radio" <?php if (isset($field['type']) && ($field['type'] == "radio")) { echo "selected";} ?>><?php echo esc_html__('Radio Select','pcfme'); ?></option>
			        <option value="datepicker" <?php if (isset($field['type']) && ($field['type'] == "datepicker")) { echo "selected";} ?>><?php echo esc_html__('Date Picker','pcfme'); ?></option>
			        <option value="datetimepicker" <?php if (isset($field['type']) && ($field['type'] == "datetimepicker")) { echo "selected";} ?>><?php echo esc_html__('Date Time Picker','pcfme'); ?></option>
			        <option value="timepicker" <?php if (isset($field['type']) && ($field['type'] == "timepicker")) { echo "selected";} ?>><?php echo esc_html__('Time Picker','pcfme'); ?></option>
			        <option value="daterangepicker" <?php if (isset($field['type']) && ($field['type'] == "daterangepicker")) { echo "selected";} ?>><?php echo esc_html__('Date Range Picker','pcfme'); ?></option>
			        <option value="datetimerangepicker" <?php if (isset($field['type']) && ($field['type'] == "datetimerangepicker")) { echo "selected";} ?>><?php echo esc_html__('Date Time Range Picker','pcfme'); ?></option>
			       </select>
		       </td>
	           </tr>
               <?php }  ?>
               
               <?php if (!preg_match('/\b'.$key.'\b/', $address2_field )) { ?>
			   <tr>
                <td width="25%"><label for="<?php echo $key; ?>_label"><?php  echo esc_html__('Label','pcfme'); ?></label></td>
	            <td width="75%"><input type="text" name="<?php echo $slug; ?>[<?php echo $key; ?>][label]" value="<?php 
	                if (isset($field['label']) && ($field['label'] != '')) { 
	            	    echo $field['label']; 
	            	} elseif ($key == "order_comments") {
                        echo esc_html__('Order notes','pcfme');
	            	} else { 
	            		echo $headlingtext; 

	            	} ?>" size="100"></td>
               </tr>
			   <?php }  ?>
			
			   
			   
			   <tr>
	           <td width="25%"><label for="<?php echo $key; ?>_width"><?php echo esc_html__('Width','pcfme'); ?></label></td>
		       <td width="75%">
		       <select class="checkout_field_width" name="<?php echo $slug; ?>[<?php echo $key; ?>][width]" >
			    
				<option value="form-row-wide" <?php if (isset($fieldwidth) && ($fieldwidth == "form-row-wide" )) { echo 'selected'; } ?>><?php echo esc_html__('Full Width','pcfme'); ?></option>
			    <option value="form-row-first" <?php if (isset($fieldwidth) && ($fieldwidth == "form-row-first" )) { echo 'selected'; } ?>><?php echo esc_html__('First Half','pcfme'); ?></option>
			    <option value="form-row-last" <?php if (isset($fieldwidth) && ($fieldwidth == "form-row-last" )) { echo 'selected'; } ?>><?php echo esc_html__('Second Half','pcfme'); ?></option>
				
				
			   </select>
		       </td>
	           </tr>
			   
			   <?php if (!preg_match('/\b'.$key.'\b/', $required_slugs )) { ?>
		       <tr>
                <td width="25%"><label for="<?php echo $key; ?>_required"><?php  echo esc_html__('Required','pcfme'); ?></label></td>
                <td width="75%"><input type="checkbox" name="<?php echo $slug; ?>[<?php echo $key; ?>][required]" <?php if (isset($field['required']) && ($field['required'] == 1)) { echo "checked";} ?> value="1"></td>
			   </tr>
			   <?php } ?>
			   
			   <tr>
                <td width="25%"><label for="<?php echo $key; ?>_clear"><?php  echo esc_html__('Clearfix','pcfme'); ?></label></td>
                <td width="75%"><input type="checkbox" name="<?php echo $slug; ?>[<?php echo $key; ?>][clear]" <?php if (isset($field['clear']) && ($field['clear'] == 1)) { echo "checked";} ?> value="1"></td>
			   </tr>
			   
			   
			   <tr>
                <td width="25%"><label for="<?php echo $key; ?>_label"><?php  echo esc_html__('Placeholder ','pcfme'); ?></label></td>
	            <td width="75%"><input type="text" name="<?php echo $slug; ?>[<?php echo $key; ?>][placeholder]" value="<?php if (isset($field['placeholder'])) { echo $field['placeholder']; } ?>" size="35"></td>
               </tr>
			   
			   <tr class="add-field-extraclass" style="">
	            <td width="25%">
		         <label for="<?php echo $key; ?>_extraclass"><?php echo esc_html__('Extra Class','pcfme'); ?></label>
		        </td>
		        <td width="75%">
		         <input type="text" class="pcfme_checkout_field_extraclass" name="<?php echo $slug; ?>[<?php echo $key; ?>][extraclass]" value="<?php if (isset($field['extraclass'])) { echo $field['extraclass']; } ?>" size="35">
		         <?php echo esc_html__('Use space key or comma to separate class','pcfme'); ?>
				</td>
	           </tr>
			   
            <?php if ($key != 'order_comments') { ?>
			   
			   <tr class="add-field-options" style="">
	           <td width="25%">
		         <label for="<?php echo $key; ?>_options"><?php echo esc_html__('Options','pcfme'); ?></label>
		       </td>
		       <td width="75%">
		        <input type="text" class="pcfme_checkout_field_option_values" name="<?php echo $slug; ?>[<?php echo $key; ?>][options]" value="<?php if (isset($field['options'])) { echo $field['options']; } ?>" size="35">
		        <ul>
		        	<li>
		                <?php echo esc_html__('Use pipe key or comma to separate option.If you are using it for field specific conditional visibility replace space with underscore ( _ ) . For Example value for "Option 2" will be Option_2','pcfme','pcfme'); ?>
		            </li>
		        </ul>

			   </td>
	           </tr>
	        <?php } ?>
			   
		
			   
			   
			   <?php 
			   $validatearray='';
			   
			    if (isset($field['validate'])) {
			        foreach ($field['validate'] as $z=>$value) {
			          $validatearray.=''.$value.',';
			        } 
			       
				   $validatearray=substr_replace($validatearray, "", -1);
			    }
			  
			   
			   ?>
			   <tr>
                <td width="25%"><label><?php  echo esc_html__('Visibility','pcfme'); ?></label></td>
	            <td width="75%">
		            <select class="checkout_field_visibility" name="<?php echo $slug; ?>[<?php echo $key; ?>][visibility]" >
		                <option value="always-visible" <?php if (isset($field['visibility']) && ($field['visibility'] == "always-visible" )) { echo 'selected'; } ?>><?php echo esc_html__('Always Visibile','pcfme'); ?></option>
					    <option value="product-specific" <?php if (isset($field['visibility']) && ($field['visibility'] == "product-specific" )) { echo 'selected'; } ?>><?php echo esc_html__('Conditional - Product Specific','pcfme'); ?></option>
					    <option value="category-specific" <?php if (isset($field['visibility']) && ($field['visibility'] == "category-specific" )) { echo 'selected'; } ?>><?php echo esc_html__('Conditional - Category Specific','pcfme'); ?></option>
					    <option value="field-specific" <?php if (isset($field['visibility']) && ($field['visibility'] == "field-specific" )) { echo 'selected'; } ?>><?php echo esc_html__('Conditional - Field Specific','pcfme'); ?></option>
					    <option value="role-specific" <?php if (isset($field['visibility']) && ($field['visibility'] == "role-specific" )) { echo 'selected'; } ?>><?php echo esc_html__('Conditional - Role Specific','pcfme'); ?></option>
					    <option value="total-quantity" <?php if (isset($field['visibility']) && ($field['visibility'] == "total-quantity" )) { echo 'selected'; } ?>><?php echo esc_html__('Conditional - Based on Total Cart Quantity','pcfme'); ?></option>
					    <option value="cart-quantity-specific" <?php if (isset($field['visibility']) && ($field['visibility'] == "cart-quantity-specific" )) { echo 'selected'; } ?>><?php echo esc_html__('Conditional - Based on Cart Quantity of Specific Product','pcfme'); ?></option>
					    <!--
					    <option value="shipping-specific" <?php if (isset($field['visibility']) && ($field['visibility'] == "shipping-specific" )) { echo 'selected'; } ?>><?php echo esc_html__('Conditional - Specific Shipping Method','pcfme'); ?></option>
					    <option value="payment-specific" <?php if (isset($field['visibility']) && ($field['visibility'] == "payment-specific" )) { echo 'selected'; } ?>><?php echo esc_html__('Conditional - Specific Payment Gateway','pcfme'); ?></option>
                        -->

			       </select>
		        </td>
	           </tr>
			   
			  <tr class="checkout_field_products_tr" style="<?php if (isset($field['visibility']) && ($field['visibility'] == "product-specific" )) { echo "display:;"; } else { echo 'display:none;'; } ?>">
			   <td width="25%">
                 <label><?php echo esc_html__('Select Products','pcfme'); ?></label>
	           </td>
			   <td width="75%">
			   	<select class="checkout_field_products" data-placeholder="<?php echo esc_html__('Choose Products','pcfme'); ?>" name="<?php echo $slug; ?>[<?php echo $key; ?>][products][]" multiple  style="width:600px">
			   		<?php if (isset($field['products']) && (!empty($field['products']))) { ?>
			   			<?php foreach ($field['products'] as $uniquekey => $unique_id) { ?>
			   				<option value="<?php echo $unique_id; ?>" selected>#<?php echo $unique_id; ?>- <?php echo get_the_title($unique_id); ?></option>
			   			<?php } ?>
			   		<?php  } ?>
			   	</select>
               </td>
			   </tr>
			    <tr class="checkout_field_category_tr" style="<?php if (isset($field['visibility']) && ($field['visibility'] == "category-specific" )) { echo "display:;"; } else { echo 'display:none;'; } ?>" >
			        <td width="25%">
                        <label for="notice_category"><span class="pcfmeformfield"><?php echo esc_html__('Select Categories','pcfme'); ?></span></label>
	                </td>
			        <td width="75%">
			            <select class="checkout_field_category" data-placeholder="<?php echo esc_html__('Choose Categories','pcfme'); ?>" name="<?php echo $slug; ?>[<?php echo $key; ?>][category][]"  multiple style="width:600px">
                            <?php foreach ($categories as $category) { ?>
				                <option value="<?php echo $category->term_id; ?>" <?php if (preg_match('/\b'.$category->term_id.'\b/', $chosencategories )) { echo 'selected';}?>>#<?php echo $category->term_id; ?>- <?php echo $category->name; ?></option>
				            <?php } ?>
                        </select>
                    </td>
			    </tr>

			    <tr class="checkout_field_role_tr" style="<?php if (isset($field['visibility']) && ($field['visibility'] == "role-specific" )) { echo "display:;"; } else { echo 'display:none;'; } ?>" >
			        <td width="25%">
                        <label><span class="pcfmeformfield"><?php echo esc_html__('Select Roles','pcfme'); ?></span></label>
	                </td>
			        <td width="75%">
			            <select class="checkout_field_role" data-placeholder="<?php echo esc_html__('Choose Roles','pcfme'); ?>" name="<?php echo $slug; ?>[<?php echo $key; ?>][role][]"  multiple style="width:600px">
                            <?php foreach ($roles as $rkey=>$rvalue) { ?>
				                <option value="<?php echo $rkey; ?>" <?php if (preg_match('/\b'.$rkey.'\b/', $chosenroles )) { echo 'selected';}?>><?php echo $rvalue['name']; ?></option>
				            <?php } ?>
                        </select>
                    </td>
			    </tr>


			    <tr class="checkout_field_shipping_tr" style="<?php if (isset($field['visibility']) && ($field['visibility'] == "shipping-specific" )) { echo "display:;"; } else { echo 'display:none;'; } ?>" >
			        <td width="25%">
                        <label><span class="pcfmeformfield"><?php echo esc_html__('Choose Shipping Method','pcfme'); ?></span></label>
	                </td>
			        <td width="75%">
			        	<select class="checkout_field_shipping_showhide" name="<?php echo $slug; ?>[<?php echo $key; ?>][shipping][showhide]" style="width:100px">
                            <option value="show" <?php if (isset($field['shipping']['showhide']) && ($field['shipping']['showhide'] != "hide")) { echo 'selected';}?>><?php echo esc_html__('show','pcfme'); ?></option>
				            <option value="hide" <?php if (isset($field['shipping']['showhide']) && ($field['shipping']['showhide'] == "hide")) { echo 'selected';}?>><?php echo esc_html__('hide','pcfme'); ?></option>
                        </select>&emsp;
                        <span><?php echo esc_html__('by','pcfme'); ?></span>&emsp;
			            <select class="checkout_field_shipping" data-placeholder="<?php echo esc_html__('Choose Shipping Method','pcfme'); ?>" name="<?php echo $slug; ?>[<?php echo $key; ?>][shipping][method]" style="width:600px">
                            <?php foreach ($shipping_methods as $rkey=>$rvalue) { ?>
				                <option value="<?php echo $rkey; ?>" <?php if (isset($field['shipping']['method']) && ($field['shipping']['method'] == $rkey)) { echo 'selected';}?>><?php echo $rkey; ?></option>
				            <?php } ?>
                        </select>
                    </td>
			    </tr>


			    <tr class="checkout_field_payment_tr" style="<?php if (isset($field['visibility']) && ($field['visibility'] == "payment-specific" )) { echo "display:;"; } else { echo 'display:none;'; } ?>" >
			        <td width="25%">
                        <label><span class="pcfmeformfield"><?php echo esc_html__('Choose Payment Gateway','pcfme'); ?></span></label>
	                </td>
			        <td width="75%">
			        	<select class="checkout_field_payment_showhide" name="<?php echo $slug; ?>[<?php echo $key; ?>][payment][showhide]" style="width:100px">
                            <option value="show" <?php if (isset($field['payment']['showhide']) && ($field['payment']['showhide'] != "hide")) { echo 'selected';}?>><?php echo esc_html__('show','pcfme'); ?></option>
				            <option value="hide" <?php if (isset($field['payment']['showhide']) && ($field['payment']['showhide'] == "hide")) { echo 'selected';}?>><?php echo esc_html__('hide','pcfme'); ?></option>
                        </select>&emsp;
                        <span><?php echo esc_html__('by','pcfme'); ?></span>&emsp;
			            <select class="checkout_field_payment" data-placeholder="<?php echo esc_html__('Choose Payment Gateway','pcfme'); ?>" name="<?php echo $slug; ?>[<?php echo $key; ?>][payment][gateway]" style="width:600px">
                            <?php foreach ($payment_gateways as $rkey=>$rvalue) { ?>
				                <option value="<?php echo $rkey; ?>" <?php if (isset($field['payment']['gateway']) && ($field['payment']['gateway'] == $rkey)) { echo 'selected';}?>><?php echo $rkey; ?></option>
				            <?php } ?>
                        </select>
                    </td>
			    </tr>

			    <tr class="checkout_field_total_quantity_tr" style="<?php if (isset($field['visibility']) && ($field['visibility'] == "total-quantity" )) { echo "display:;"; } else { echo 'display:none;'; } ?>" >
			        <td width="25%">
                        <label><span class="pcfmeformfield"><?php echo esc_html__('Total Cart Quantity','pcfme'); ?></span></label>
	                </td>
			        <td width="75%">
			            <input type="number" placeholder="<?php echo esc_html__('Choose Quantity','pcfme'); ?>" class="checkout_field_total_quantity" name="<?php echo $slug; ?>[<?php echo $key; ?>][total-quantity]" value="<?php if (isset($field['total-quantity'])) { echo $field['total-quantity']; } ?>"/>
                    </td>
			    </tr>


			    <tr class="checkout_field_cart_quantity_specific_tr" style="<?php if (isset($field['visibility']) && ($field['visibility'] == "cart-quantity-specific" )) { echo "display:;"; } else { echo 'display:none;'; } ?>" >
			        <td width="25%">
                        <label><span class="pcfmeformfield"><?php echo esc_html__('Product and Quantity','pcfme'); ?></span></label>
	                </td>
			        <td width="75%">
			        	<select class="checkout_field_quantity_specific_product" data-placeholder="<?php echo esc_html__('Choose Product','pcfme'); ?>" name="<?php echo $slug; ?>[<?php echo $key; ?>][specific-product]" style="width:600px">
                            <?php if (isset($field['specific-product'])) { ?>
				                <option value="<?php echo $field['specific-product']; ?>" selected>#<?php echo $field['specific-product'] ?>-<?php echo get_the_title($field['specific-product']); ?></option>
				            <?php } ?>
                        </select>
			            <input type="number" class="checkout_field_cart_quantity_specific" name="<?php echo $slug; ?>[<?php echo $key; ?>][specific-quantity]" placeholder="<?php echo esc_html__('Choose Quantity','pcfme'); ?>" value="<?php if (isset($field['specific-quantity'])) { echo $field['specific-quantity']; } ?>"/>
                    </td>
			    </tr>

				<?php if (isset($field['conditional'])) $conditional_field = $field['conditional']; ?>

				<tr class="checkout_field_conditional_tr" style="<?php if (isset($field['visibility']) && ($field['visibility'] == "field-specific" )) { echo "display:;"; } else { echo 'display:none;'; } ?>" >
			        <td width="25%">
                        <label for="notice_category"><span class="pcfmeformfield">
                        	<?php echo esc_html__('Set Rule','pcfme'); ?></span>
                        </label>
	                </td>
			        <td width="75%">
			            <div class="conditional_fields_div_wrapper conditional_fields_div_wrapper_<?php echo $key; ?>">
                            
                            <?php $mnindex = 1; ?>

                            <?php if (isset($field['conditional'])) { ?>
                            
                            <?php $mnindex = max(array_keys($field['conditional']))+1; ?>

                            

			            	    <?php foreach ($field['conditional'] as $conditionalkey=>$conditionalvalue) { ?>

                                    

			            	        <div class="conditional-row">
			            		        <select class="checkout_field_conditional_showhide" name="pcfme_<?php echo $mntext; ?>_settings[<?php echo $key; ?>][conditional][<?php echo $conditionalkey; ?>][showhide]" >
			            			        <option value="open" <?php if (isset($conditionalvalue['showhide']) && ($conditionalvalue['showhide']) == "open") { echo 'selected';} ?> ><?php echo esc_html__('Show','pcfme'); ?></option>
			            			        <option value="hide" <?php if (isset($conditionalvalue['showhide']) && ($conditionalvalue['showhide']) == "hide") { echo 'selected';} ?>><?php echo esc_html__('Hide','pcfme'); ?></option>
			            		        </select>
			            		        <span class="pcfmeformfield">
			            		        	<strong>&emsp;<?php echo esc_html__('If value of','pcfme'); ?>&emsp;</strong>
			            		        </span>

			            		        <select class="checkout_field_conditional_parentfield" name="pcfme_<?php echo $mntext; ?>_settings[<?php echo $key; ?>][conditional][<?php echo $conditionalkey; ?>][parentfield]" >
                                            <?php foreach ($fields as $fkey=>$ffield) { ?>
                                            	<?php 
                                                    $nkey = isset($ffield['field_key']) ? $ffield['field_key'] : $fkey;
                                            	?>
                                            	<?php if ($key != $fkey) { ?>
                                                <option value="<?php echo $nkey; ?>" <?php if (isset($conditionalvalue['parentfield']) && ($conditionalvalue['parentfield']) == $nkey) { echo 'selected';} ?>>
                                        	        <?php if (isset($ffield['label'])) {
                                                        echo $ffield['label']; 
                                        	        } else { 
                                                        echo $nkey;
                                        	        }
                                        	    ?>

                                                </option>
                                            <?php } ?>
                                            <?php } ?>
			            			
			            		        </select>

			            		        <span class="pcfmeformfield">
			            			        <strong>&emsp;<?php echo esc_html__('is equal to','pcfme'); ?> </strong>
			            		        </span>

			            		        <input type="text" class="checkout_field_conditional_equalto" name="pcfme_<?php echo $mntext; ?>_settings[<?php echo $key; ?>][conditional][<?php echo $conditionalkey; ?>][equalto]" value="<?php if (isset($conditionalvalue['equalto'])) { echo $conditionalvalue['equalto']; } ?>"> 
			            		        <span class="glyphicon glyphicon-trash pcfme-remove-condition"></span>

			            	        </div>

			                    <?php  } ?>
			                
			                <?php } ?>
			            
			                </div>

			                <ul>
			                    <li>
			             	        <?php echo esc_html__('If parent field is checkbox leave blank for equal to field.','pcfme'); ?>
			             		</li>
			                    <li>
			        	            <?php echo esc_html__('If parent field is radio/select and its value has space, replace space with underscore ( _ ) for example equal to field for "Option 3" will be Option_3.','pcfme'); ?>
			        		    </li>
			        		    <li>
			             	        <?php echo esc_html__('You can also use text/textarea field as parent field.','pcfme'); ?>
			             		</li>
			                </ul>

			            <input type="button" mnindex="<?php echo $mnindex; ?>" mntype="<?php if (isset($mntext)) { echo $mntext; } ?>" keyno="<?php echo $key; ?>" class="btn button-primary add-condition-button" value="<?php echo esc_html__('Add Condition','pcfme'); ?>">
                    </td>
			    </tr>

			 <?php if (($slug != 'pcfme_additional_settings') && ($key != 'order_comments')) { ?>
			   <tr>
                <td width="25%"><label for="<?php echo $key; ?>_label"><?php  echo esc_html__('Validate','pcfme'); ?></label></td>
	            <td width="75%">
		           <select name="<?php echo $slug; ?>[<?php echo $key; ?>][validate][]" class="row-validate-multiselect" multiple>
			         <option value="state" <?php if (preg_match('/\bstate\b/', $validatearray )) { echo 'selected'; } ?>><?php echo esc_html__('state','pcfme'); ?></option>
			         <option value="postcode" <?php if (preg_match('/\bpostcode\b/', $validatearray )) { echo 'selected'; } ?>><?php echo esc_html__('postcode','pcfme'); ?></option>
			         <option value="email" <?php if (preg_match('/\bemail\b/', $validatearray )) { echo 'selected'; } ?>><?php echo esc_html__('email','pcfme'); ?></option>
			         <option value="phone" <?php if (preg_match('/\bphone\b/', $validatearray )) { echo 'selected'; } ?>><?php echo esc_html__('phone','pcfme'); ?></option>
			       </select>
		        </td>
	           </tr>
			 <?php } ?>
			   
			   <tr>
			     <td width="25%"><label for="<?php echo $key; ?>_clear"><?php  echo esc_html__('Chose Options','pcfme'); ?></label></td>
			     <td  width="75%">
			      <table>
			       
			   
			        <tr class="disable_datepicker_tr" style="<?php if (isset($field['type']) && (($field['type'] == "datepicker") || ($field['type'] == "datetimepicker") || ($field['type'] == "daterangepicker")|| ($field['type'] == "datetimerangepicker"))) { echo "display:;";} else { echo "display:none;"; } ?>">
                     <td><input class="checkout_field_disable_past_dates" type="checkbox" name="<?php echo $slug; ?>[<?php echo $key; ?>][disable_past]" <?php if (isset($field['disable_past']) && ($field['disable_past'] == 1)) { echo "checked";} ?> value="1"></td>
			         <td><label ><?php  echo esc_html__('Disable Past Date Selection In Datepicker','pcfme'); ?></label></td>
					</tr>
					
					<tr>
			        <td><input type="checkbox" name="<?php echo $slug; ?>[<?php echo $key; ?>][orderedition]" <?php if (isset($field['orderedition']) && ($field['orderedition'] == 1)) { echo "checked";} ?> value="1"></td>
                    <td><label for="<?php echo $key; ?>_clear"><?php  echo esc_html__('Show field detail along with orders','pcfme'); ?></label></td>
                    </tr>
					
					<tr>
			        <td><input type="checkbox" name="<?php echo $slug; ?>[<?php echo $key; ?>][emailfields]" <?php if (isset($field['emailfields']) && ($field['emailfields'] == 1)) { echo "checked";} ?> value="1"></td>
                    <td><label><?php  echo esc_html__('Show field detail on woocommerce order email','pcfme'); ?></label></td>
                    </tr>
					
					<tr>
			        <td><input type="checkbox" name="<?php echo $slug; ?>[<?php echo $key; ?>][pdfinvoice]" <?php if (isset($field['pdfinvoice']) && ($field['pdfinvoice'] == 1)) { echo "checked";} ?> value="1"></td>
                    <td><label><?php  echo esc_html__('Show field detail on WooCommerce PDF Invoices & Packing Slips Invoice','pcfme'); ?></label></td>
                    </tr>
					<?php if (!preg_match('/\b'.$key.'\b/', $core_fields )) { ?>
					<tr>
			        <td><input type="checkbox" name="<?php echo $slug; ?>[<?php echo $key; ?>][editaddress]" <?php if (isset($field['editaddress']) && ($field['editaddress'] == 1)) { echo "checked";} ?> value="1"></td>
                    <td><label><?php  echo esc_html__('Add this field to myaccount/edit address page','pcfme'); ?></label></td>
                    </tr>
			        <?php } ?>
			        </table>
				   </td>
				 </tr>
			   </table>

             </div>
			
          </div>
	<?php }
	
	
	

	

	public function plugin_options_tabs() {
		$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->billing_settings_key;
        echo '<h2 class="nav-tab-wrapper">';
		foreach ( $this->pcfme_plugin_settings_tabs as $tab_key => $tab_caption ) {
			$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
			echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->pcfme_plugin_options . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';	
		}
		echo '</h2>';
	}
	
	public function show_new_form($fields,$slug,$country_fields) {
		
       
		

     	 
        $catargs = array(
	      'orderby'                  => 'name',
	      'taxonomy'                 => 'product_cat',
	      'hide_empty'               => 0
	     );
		 
	  
		$categories           = get_categories( $catargs ); 
		
	    include ('forms/pcfme_show_new_form.php');
    }
	

}




new pcfme_add_settings_page_class();

?>