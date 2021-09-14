<?php       
global $woocommerce;

$this->countries     = new WC_Countries();

$fields              = $this->countries->get_address_fields( $this->countries->get_base_country(),'billing_');
$billing_settings    = $this->billing_settings;
$billing_settings    = array_filter($billing_settings);
$required_slugs      = '';
$core_fields         = 'billing_country,billing_first_name,billing_last_name,billing_company,billing_address_1,billing_address_2,billing_city,billing_state,billing_postcode,billing_email,billing_phone';
$country_fields      = 'billing_country,billing_state';
$address2_field      = 'billing_address_2';

$noticerowno = 1;

if (isset($billing_settings) && (sizeof($billing_settings) >= 1)) { 
	$conditional_fields_dropdown = $billing_settings;
} else {
	$conditional_fields_dropdown = $fields;
}
?>

<center>	   
	<div class="panel-group pcfme-sortable-list" id="accordion" >
		<?php 

		if (isset($billing_settings) && (sizeof($billing_settings) >= 1)) { 

			foreach ($billing_settings as $key =>$field) { 
				$this->show_fields_form($conditional_fields_dropdown,$key,$field,$noticerowno,$this->billing_settings_key,$required_slugs,$core_fields,$country_fields,$address2_field);
				$noticerowno++;
			} 
			
		} else {

			foreach ($fields as $key =>$field) { 
				$this->show_fields_form($conditional_fields_dropdown,$key,$field,$noticerowno,$this->billing_settings_key,$required_slugs,$core_fields,$country_fields,$address2_field);
				$noticerowno++;
			}
		}
		
		?>
		<script>
			var hash= <?php echo $noticerowno; ?>;
			jQuery(document).ready(function($) {
				$(".checkout_field_width").select2({width: "250px" ,minimumResultsForSearch: -1}); 
				$(".checkout_field_visibility").select2({width: "450px" ,minimumResultsForSearch: -1});		  
				$(".row-validate-multiselect").select2({width: "250px" });  
				$(".checkout_field_conditional_showhide").select2({width: "100px",minimumResultsForSearch: -1 });  
				$(".checkout_field_conditional_parentfield").select2({width: "250px" });
				$(".checkout_field_type").select2({width: "250px",minimumResultsForSearch: -1 }); 
				$(".checkout_field_validate").select2({width: "250px" }); 

				$(".checkout_field_category").select2({width: "400px" });
				$(".checkout_field_role").select2({width: "400px" });

				$(".checkout_field_shipping").select2({
					width: "400px",
					minimumResultsForSearch: -1
				});


				$(".checkout_field_payment").select2({
					width: "400px",
					minimumResultsForSearch: -1
				});

				$('.checkout_field_products,.checkout_field_quantity_specific_product').select2({

					ajax: {
    			url: ajaxurl, // AJAX URL is predefined in WordPress admin
    			dataType: 'json',
    			delay: 250, // delay in ms while typing when to perform a AJAX search
    			data: function (params) {
    				return {
        				q: params.term, // search query
        				action: 'pdfmegetajaxproductslist' // AJAX action for admin-ajax.php
        			};
        		},
        		processResults: function( data ) {
        			var options = [];
        			if ( data ) {

					// data is the array of arrays, and each of them contains ID and the Label of the option
					$.each( data, function( index, text ) { // do not forget that "index" is just auto incremented value
						options.push( { id: text[0], text: text[1]  } );
					});

				}
				return {
					results: options
				};
			},
			cache: true
		},
		minimumInputLength: 3 ,
			 width: "400px"// the minimum of symbols to input before perform a search
			});
			});
		</script>

	</div>

	<div class="buttondiv">
	        <?php 
	             global $woocommerce;
	             $checkout_url = '#';
                     $checkout_url = wc_get_checkout_url();
                ?>	  
		<button type="button" id="add-billing-field" class="btn btn-primary" >
			<span class="dashicons dashicons-insert"></span>
			<?php echo esc_html__('Add Billing Field','pcfme'); ?>
		</button>

		<a type="button" target="_blank" href="<?php echo $checkout_url; ?>" id="pcfme_frontend_link" class="btn btn-primary pcfme_frontend_link">
			<span class="dashicons dashicons-welcome-view-site"></span>
		        <?php echo esc_html__('Frontend','pcfme'); ?>
		</a>

		<button type="button" id="restore-billing-fields" class="btn btn-danger">
			<?php echo esc_html__('Restore Billing Fields','pcfme'); ?>
		</button>
			
	</div>

	</center> <?php
	
	$this->show_new_form($conditional_fields_dropdown,$this->billing_settings_key,$country_fields);
