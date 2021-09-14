jQuery(document).ready(function () {

    var uacf7_step = '.uacf7-step';
    var uacf7_next = jQuery('.uacf7-next');
    var uacf7_prev = jQuery('.uacf7-prev');
    var total_steps = jQuery(uacf7_step).length;
    
    $uacf7_sid = 1;
    jQuery(uacf7_step).each(function () {
        
        jQuery(this).attr('id', 'step-' + $uacf7_sid);
        
        if( $uacf7_sid == 1 ) {
            jQuery(this).addClass('step-start');
        }
        
        if( total_steps == $uacf7_sid ) {
            jQuery(this).addClass('step-end');
        }
        
        $uacf7_sid++;
        
    });

    uacf7_prev.on('click', function (e) {
        e.preventDefault();
    });

    uacf7_next.on('click', function (e) {
        e.preventDefault();

        var $this = jQuery(this);

        uacf7_step_validation($this);
    });

    function uacf7_step_validation($this) {

        var uacf7_current_step = jQuery($this).closest(uacf7_step);

        var uacf7_current_step_fields = uacf7_current_step.find('.wpcf7-form-control').map(function () {
            return this.name;
        }).get();

        var uacf7_form_ids = '';

        var fields_to_check_serialized = jQuery(uacf7_current_step).find(".wpcf7-form-control").serialize();

        if (jQuery(uacf7_current_step).find(".wpcf7-form-control[type='file']").length > 0) {
            jQuery(uacf7_current_step).find(".wpcf7-form-control[type='file']").each(function (i, n) {
                fields_to_check_serialized += "&" + jQuery(this).attr('name') + "=" + jQuery(this).val();
            });
        }

        var data = fields_to_check_serialized +
            '&' + 'action=' + 'check_fields_validation' +
            //'&' + 'form_id=' + wpcf7.getId(jQuery('form')) +
            '&' + 'form_id=' + jQuery('input[name="_wpcf7"]').val() +
            '&' + 'current_fields_to_check=' + uacf7_current_step_fields +
            '&' + 'ajax_nonce=' + uacf7_multistep_obj.nonce;

        jQuery.ajax({
            url: uacf7_multistep_obj.ajax_url,
            type: 'post',
            dataType: "json",
            data: data,
            beforeSend: function(){
                
                jQuery($this).closest(".uacf7-step").find('.uacf7-ajax-loader').addClass('is-active');
                
            },
            success: function (response) {
                
                jQuery($this).closest(".uacf7-step").find('.uacf7-ajax-loader').removeClass('is-active');
                
                var json_result = (typeof response === 'object') ? response : JSON.parse(response);

                var $form = jQuery('form');
                clear_error_messages($form, uacf7_current_step);

                try {

                    if (json_result.is_valid) {

                        var curStep = jQuery($this).closest(".uacf7-step"),
                            curStepBtn = curStep.attr("id"),
                            nextStepWizard = jQuery('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a");

                        nextStepWizard.removeAttr('disabled').trigger('click');
     
                    } else {
						
                        // show errors
						jQuery.each(json_result.invalid_fields, function (i, n) {
							
                            jQuery(n.into, 'form').each(function () {
																
                                jQuery('.wpcf7-form-control', this).addClass('wpcf7-not-valid');
                                jQuery('[aria-invalid]', this).attr('aria-invalid', 'true');
								
								jQuery( this ).append( '<span class="wpcf7-not-valid-tip" aria-hidden="true">'+n.message+'</span>' );
								
                            });
                        });
						
                    }
                } catch (e) {
                    console.log("error: " + e);
                }
            },
            error: function () {
                alert('Error');
            }
        });
    }

    function clear_error_messages($form, uacf7_current_step) {
        $form.removeClass('invalid');
        jQuery('.wpcf7-response-output', $form).removeClass('wpcf7-validation-errors');
        jQuery('.wpcf7-form-control', uacf7_current_step).removeClass('wpcf7-not-valid');
        jQuery('[aria-invalid]', uacf7_current_step).attr('aria-invalid', 'false');
        jQuery('.wpcf7-not-valid-tip', uacf7_current_step).remove();
    };

});