document.addEventListener('wpcf7mailsent', function (event) {

    if (event.detail.status == 'mail_sent' && uacf7_pro_object.product_dropdown == 'checked' && uacf7_pro_object.auto_cart == 'checked' ) {

        var $product = [];

        jQuery('.wpcf7-form.sent .uacf7_auto_cart_'+event.detail.contactFormId+' .wpcf7-uacf7_product_dropdown').find('option:selected').each(function () {
            $product.push(jQuery(this).attr('product-id'));

        });

        if ( typeof $product !== 'undefined' && $product.length != 0 ) {

            jQuery.ajax({
                url: uacf7_pro_object.ajaxurl,
                type: 'post',
                data: {
                    action: 'uacf7_ajax_add_to_cart_product',
                    product_ids: $product,
                },
                success: function (data) {
                    location.href = uacf7_pro_object.cart_page;
                },
                error: function (jqXHR, exception) {
                    var error_msg = '';
                    if (jqXHR.status === 0) {
                        var error_msg = 'Not connect.\n Verify Network.';
                    } else if (jqXHR.status == 404) {
                        var error_msg = 'Requested page not found. [404]';
                    } else if (jqXHR.status == 500) {
                        var error_msg = 'Internal Server Error [500].';
                    } else if (exception === 'parsererror') {
                        var error_msg = 'Requested JSON parse failed.';
                    } else if (exception === 'timeout') {
                        var error_msg = 'Time out error.';
                    } else if (exception === 'abort') {
                        var error_msg = 'Ajax request aborted.';
                    } else {
                        var error_msg = 'Uncaught Error.\n' + jqXHR.responseText;
                    }
                    alert(error_msg);
                }
            });
        }
    }

}, false);


/*
* Multistep script
* Button text
*/
jQuery( document ).ready(function(){
    
    jQuery('.uacf7-step').each(function(){
        $next_btn = jQuery(this).attr('next-btn-text');
        $prev_btn = jQuery(this).attr('prev-btn-text');
        
        if( $next_btn != '' ){
            jQuery('.uacf7-next',this).text($next_btn);
        }
        if( $prev_btn != '' ){
            jQuery('.uacf7-prev',this).text($prev_btn);
        }
        
    });
    
});