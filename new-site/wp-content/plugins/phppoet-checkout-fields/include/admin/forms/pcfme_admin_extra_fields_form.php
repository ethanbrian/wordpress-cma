<?php
    $extra_settings    = $this->extra_settings;
    $extra_settings    = array_filter($extra_settings);
    $required_slugs    = '';
    $core_fields       = 'order_comments';
    $country_fields    = 'order_comments';
    $address2_field    = '';
    

    global $woocommerce;

       
    $this->countries        = new WC_Countries();
	    
	if (!empty($extra_settings['datepicker_disable_days'])) {
		$days_to_exclude = implode(',', $extra_settings['datepicker_disable_days']); 
	} else { 
	    $days_to_exclude=''; 
	}
	

	$order_key          = 'order_comments';

	if (isset ($extra_settings[$order_key])) {
		$order_field        = $extra_settings[$order_key];
	} else {
		$order_field        = array();
	}
	
	$noticerowno        = 1;

    $this->show_fields_form($extra_settings,$order_key,$order_field,$noticerowno,$this->extra_settings_key,$required_slugs,$core_fields,$country_fields,$address2_field);
?>
<table class="table">
    <tr>

	    <td><label for="estimate_name"><?php  echo esc_html__('Fields display on Thank You Page','pfcme'); ?></label></td>

	    <td>
			<select class="thankyou_fields_location" name="pcfme_extra_settings[thankyou_fields_location]"> 
			     <option value="after" <?php if (isset($extra_settings['thankyou_fields_location']) && ($extra_settings['thankyou_fields_location'] == "after")) { echo 'selected'; } ?>><?php  echo esc_html__('After Thank you Page ( woocommerce_thankyou hook )','pcfme'); ?>
				 <option value="before" <?php if (isset($extra_settings['thankyou_fields_location']) && ($extra_settings['thankyou_fields_location'] == "before")) { echo 'selected'; } ?>><?php  echo esc_html__('Before Thank you page ( woocommerce_before_thankyou hook )','pcfme'); ?>
			</select> 
	    </td>
	</tr>
	<tr>

	    <td><label for="datepicker_format"><?php  echo esc_html__('Datepicker format','pfcme'); ?></label></td>

	    <td>
			<select class="datepicker_format" name="pcfme_extra_settings[datepicker_format]"> 
			    <option value="01" <?php if (isset($extra_settings['datepicker_format']) && ($extra_settings['datepicker_format'] == 01)) { echo 'selected'; } ?>>
			     	<?php  echo esc_html__('dd/mm/yyyy (01/01/2020)','pcfme'); ?>
			    </option>
				<option value="02" <?php if (isset($extra_settings['datepicker_format']) && ($extra_settings['datepicker_format'] == 02)) { echo 'selected'; } ?>>
					<?php  echo esc_html__('dd-mm-yyyy (01-01-2020)','pcfme'); ?>
				</option>
				<option value="03" <?php if (isset($extra_settings['datepicker_format']) && ($extra_settings['datepicker_format'] == 03)) { echo 'selected'; } ?>>
					<?php  echo esc_html__('dd MM yyyy (01 january 2020)','pcfme'); ?>
				</option>

				<option value="04" <?php if (isset($extra_settings['datepicker_format']) && ($extra_settings['datepicker_format'] == 04)) { echo 'selected'; } ?>>
			     	<?php  echo esc_html__('mm/dd/yyyy (01/01/2020)','pcfme'); ?>
			    </option>
				<option value="05" <?php if (isset($extra_settings['datepicker_format']) && ($extra_settings['datepicker_format'] == 05)) { echo 'selected'; } ?>>
					<?php  echo esc_html__('mm-dd-yyyy (01-01-2020)','pcfme'); ?>
				</option>
				<option value="06" <?php if (isset($extra_settings['datepicker_format']) && ($extra_settings['datepicker_format'] == 06)) { echo 'selected'; } ?>>
					<?php  echo esc_html__('MM dd yyyy (january 01 2020)','pcfme'); ?>
				</option>
				
			</select>
	    </td>
	</tr>


	<tr>

	    <td><label for="datepicker_disable_days"><?php  echo esc_html__('Datepicker disable days','pfcme'); ?></label></td>

	    <td>
			<select class="datepicker_disable_days" name="pcfme_extra_settings[datepicker_disable_days][]" multiple> 
			    <option value="0" <?php if (preg_match('/\b0\b/', $days_to_exclude )) { echo 'selected';}?>>
			     	<?php  echo esc_html__('Sunday','pcfme'); ?>
			    </option>
				<option value="1" <?php if (preg_match('/\b1\b/', $days_to_exclude )) { echo 'selected';}?>>
					<?php  echo esc_html__('Monday','pcfme'); ?>
				</option>
				<option value="2" <?php if (preg_match('/\b2\b/', $days_to_exclude )) { echo 'selected';}?>>
					<?php  echo esc_html__('Tuesday','pcfme'); ?>
				</option>

				<option value="3" <?php if (preg_match('/\b3\b/', $days_to_exclude )) { echo 'selected';}?>>
			     	<?php  echo esc_html__('Wednesday','pcfme'); ?>
			    </option>
				<option value="4" <?php if (preg_match('/\b4\b/', $days_to_exclude )) { echo 'selected';}?>>
					<?php  echo esc_html__('Thursday','pcfme'); ?>
				</option>
				<option value="5" <?php if (preg_match('/\b5\b/', $days_to_exclude )) { echo 'selected';}?>>
					<?php  echo esc_html__('Friday','pcfme'); ?>
				</option>
				<option value="6" <?php if (preg_match('/\b6\b/', $days_to_exclude )) { echo 'selected';}?>>
					<?php  echo esc_html__('Saturday','pcfme'); ?>
				</option>
			</select>
	    </td>
	</tr>

	

	<tr>

	    <td><label><?php  echo esc_html__('Allowed Times','pfcme'); ?></label></td>

	    <td>
			<input type="text" class="allowed_times" name="pcfme_extra_settings[allowed_times]" value="<?php if (isset($extra_settings['allowed_times'])) { echo $extra_settings['allowed_times']; } ?>" size="70"> 
			<p><?php  echo esc_html__('Enter values separated by comma(,) for example 11:00,11:30,12:00 Leave blank to show all.','pfcme'); ?></p>
			     
	    </td>
	</tr>

	<tr>

	    <td><label for="datepicker_format"><?php  echo esc_html__('Timepicker Interval','pfcme'); ?></label></td>

	    <td>
			<select class="timepicker_interval" name="pcfme_extra_settings[timepicker_interval]"> 
			    <option value="01" <?php if (isset($extra_settings['timepicker_interval']) && ($extra_settings['timepicker_interval'] == 01)) { echo 'selected'; } ?>>
			     	<?php  echo esc_html__('60 minutes','pcfme'); ?>
			     		
			    </option>
				<option value="02" <?php if (isset($extra_settings['timepicker_interval']) && ($extra_settings['timepicker_interval'] == 02)) { echo 'selected'; } ?>>
				 	<?php  echo esc_html__('30 minutes','pcfme'); ?>
				</option>
				
			</select>
	    </td>
	</tr>

	<tr>

	    <td><label><?php  echo esc_html__('Date Time Picker Language','pfcme'); ?></label></td>

	    <td>
			<select class="datetimepicker_lang_class" name="pcfme_extra_settings[datetimepicker_lang]">  
				<?php 
				    if (isset($extra_settings['datetimepicker_lang']) && ($extra_settings['datetimepicker_lang'] != "")) { 
				    	$et_language = $extra_settings['datetimepicker_lang'];
				    } else {
                        $et_language = 'en';
				    }
				?>
				<option value="en" <?php if  ($et_language == "en") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('English','pcfme'); ?>
			    </option>
			    <option value="ar" <?php if  ($et_language == "ar") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Arabic','pcfme'); ?>
			    </option>
			    <option value="az" <?php if  ($et_language == "az") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Azerbaijanian (Azeri)','pcfme'); ?>
			    </option>
			    <option value="bg" <?php if  ($et_language == "bg") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Bulgarian','pcfme'); ?>
			    </option>
			    <option value="ca" <?php if  ($et_language == "ca") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Català','pcfme'); ?>
			    </option>
			    <option value="ch" <?php if  ($et_language == "ch") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Simplified Chinese','pcfme'); ?>
			    </option>
			    <option value="cs" <?php if  ($et_language == "cs") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Čeština','pcfme'); ?>
			    </option>
			    <option value="da" <?php if  ($et_language == "da") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Dansk','pcfme'); ?>
			    </option>
			    <option value="de" <?php if  ($et_language == "de") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('German','pcfme'); ?>
			    </option>
			    <option value="el" <?php if  ($et_language == "el") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Ελληνικά','pcfme'); ?>
			    </option>
			    <option value="en-GB" <?php if  ($et_language == "en-GB") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('English (British)','pcfme'); ?>
			    </option>
			    <option value="es" <?php if  ($et_language == "es") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Spanish','pcfme'); ?>
			    </option>
			    <option value="et" <?php if  ($et_language == "et") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Eesti','pcfme'); ?>
			    </option>
			    <option value="eu" <?php if  ($et_language == "eu") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Euskara','pcfme'); ?>
			    </option>
			    <option value="fa" <?php if  ($et_language == "fa") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Persian','pcfme'); ?>
			    </option>
			    <option value="fi" <?php if  ($et_language == "fi") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Finnish (Suomi)','pcfme'); ?>
			    </option>
			    <option value="gl" <?php if  ($et_language == "gl") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Galego','pcfme'); ?>
			    </option>
			    <option value="he" <?php if  ($et_language == "he") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Hebrew (עברית)','pcfme'); ?>
			    </option>
			    <option value="hr" <?php if  ($et_language == "hr") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Hrvatski','pcfme'); ?>
			    </option>
			    <option value="hu" <?php if  ($et_language == "hu") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Hungarian','pcfme'); ?>
			    </option>
			    <option value="id" <?php if  ($et_language == "id") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Indonesian','pcfme'); ?>
			    </option>
			    <option value="it" <?php if  ($et_language == "it") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Italian','pcfme'); ?>
			    </option>
			    <option value="ja" <?php if  ($et_language == "ja") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Japanese','pcfme'); ?>
			    </option>
			    <option value="ko" <?php if  ($et_language == "ko") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Korean (한국어)','pcfme'); ?>
			    </option>
			    <option value="kr" <?php if  ($et_language == "kr") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Korean','pcfme'); ?>
			    </option>
			    <option value="lt" <?php if  ($et_language == "lt") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Lithuanian (lietuvių)','pcfme'); ?>
			    </option>
			    <option value="lv" <?php if  ($et_language == "lv") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Latvian (Latviešu)','pcfme'); ?>
			    </option>
			    <option value="mk" <?php if  ($et_language == "mk") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Macedonian (Македонски)','pcfme'); ?>
			    </option>
			    <option value="mn" <?php if  ($et_language == "mn") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Mongolian (Монгол)','pcfme'); ?>
			    </option>
			    <option value="nl" <?php if  ($et_language == "nl") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Dutch','pcfme'); ?>
			    </option>
			    <option value="no" <?php if  ($et_language == "no") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Norwegian','pcfme'); ?>
			    </option>
			    <option value="pl" <?php if  ($et_language == "pl") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Polish','pcfme'); ?>
			    </option>
			    <option value="pt" <?php if  ($et_language == "pt") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Portuguese','pcfme'); ?>
			    </option>
			    <option value="pt-BR" <?php if  ($et_language == "pt-BR") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Português(Brasil)','pcfme'); ?>
			    </option>
			    <option value="ro" <?php if  ($et_language == "ro") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Romanian','pcfme'); ?>
			    </option>
			    <option value="ru" <?php if  ($et_language == "ru") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Russian','pcfme'); ?>
			    </option>
			    <option value="se" <?php if  ($et_language == "se") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Swedish','pcfme'); ?>
			    </option>
			    <option value="sk" <?php if  ($et_language == "sk") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Slovenčina','pcfme'); ?>
			    </option>
			    <option value="sl" <?php if  ($et_language == "sl") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Slovenščina','pcfme'); ?>
			    </option>
			    <option value="sq" <?php if  ($et_language == "sq") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Albanian (Shqip)','pcfme'); ?>
			    </option>
			    <option value="sr" <?php if  ($et_language == "sr") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Serbian Cyrillic (Српски)','pcfme'); ?>
			    </option>
			    <option value="sr-YU" <?php if  ($et_language == "sr-YU") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Serbian (Srpski)','pcfme'); ?>
			    </option>
			    <option value="sv" <?php if  ($et_language == "sv") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Svenska','pcfme'); ?>
			    </option>
			    <option value="th" <?php if  ($et_language == "th") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Thai','pcfme'); ?>
			    </option>
			    <option value="tr" <?php if  ($et_language == "tr") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Turkish','pcfme'); ?>
			    </option>
			    <option value="uk" <?php if  ($et_language == "uk") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Ukrainian','pcfme'); ?>
			    </option>
			    <option value="vi" <?php if  ($et_language == "vi") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Vietnamese','pcfme'); ?>
			    </option>
			    <option value="zh" <?php if  ($et_language == "zh") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Simplified Chinese (简体中文)','pcfme'); ?>
			    </option>
			    <option value="zh-TW" <?php if  ($et_language == "uk") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Traditional Chinese (繁體中文)','pcfme'); ?>
			    </option>
			    
	
			</select>
			     
	    </td>
	</tr>
	<tr>

	    <td><label><?php  echo esc_html__('Date Range Picker Week Starts On','pfcme'); ?></label></td>

	    <td>
	    	<select class="week_starts_on_class" name="pcfme_extra_settings[week_starts_on]">  
				<?php 
				    if (isset($extra_settings['week_starts_on']) && ($extra_settings['week_starts_on'] != "")) { 
				    	$week_starts_on = $extra_settings['week_starts_on'];
				    } else {
                        $week_starts_on = 'sunday';
				    }
				?>
				<option value="sunday" <?php if  ($week_starts_on == "sunday") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Sunday','pcfme'); ?>
			    </option>
			    <option value="monday" <?php if  ($week_starts_on == "monday") { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Monday','pcfme'); ?>
			    </option>
			</select>
	    </td>
	</tr>

	<tr>

	    <td><label><?php  echo esc_html__('Date and Time picker Week Starts On','pfcme'); ?></label></td>

	    <td>
	    	<select class="dt_week_starts_on_class" name="pcfme_extra_settings[dt_week_starts_on]">  
				<?php 
				    if (isset($extra_settings['dt_week_starts_on']) && ($extra_settings['dt_week_starts_on'] != "")) { 
				    	$dt_week_starts_on = $extra_settings['dt_week_starts_on'];
				    } else {
                        $dt_week_starts_on = 0;
				    }
				?>
				<option value="0" <?php if  ($dt_week_starts_on == 0) { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Sunday','pcfme'); ?>
			    </option>
			    <option value="1" <?php if  ($dt_week_starts_on == 1) { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Monday','pcfme'); ?>
			    </option>
			    <option value="2" <?php if  ($dt_week_starts_on == 2) { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Tuesday','pcfme'); ?>
			    </option>
			    <option value="3" <?php if  ($dt_week_starts_on == 3) { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Wednesday','pcfme'); ?>
			    </option>
			    <option value="4" <?php if  ($dt_week_starts_on == 4) { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Thursday','pcfme'); ?>
			    </option>
			    <option value="5" <?php if  ($dt_week_starts_on == 5) { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Friday','pcfme'); ?>
			    </option>
			    <option value="6" <?php if  ($dt_week_starts_on == 6) { echo 'selected'; }  ?>>
			     	<?php  echo esc_html__('Saturday','pcfme'); ?>
			    </option>
			</select>
	    </td>
	</tr>


</table>
<script>
		
		jQuery(document).ready(function($) {
		    $(".checkout_field_width").select2({width: "250px" ,minimumResultsForSearch: -1}); 
            $(".checkout_field_visibility").select2({width: "250px" ,minimumResultsForSearch: -1});			  
            
            $(".checkout_field_conditional_showhide").select2({width: "100px",minimumResultsForSearch: -1 });  
            $(".checkout_field_conditional_parentfield").select2({width: "250px" });
            $(".checkout_field_type").select2({width: "250px" ,minimumResultsForSearch: -1});  
		    $(".checkout_field_category").select2({width: "400px" });
		    $(".checkout_field_role").select2({width: "400px" });
		  	$('.checkout_field_products').select2({
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