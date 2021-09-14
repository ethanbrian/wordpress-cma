(function ($) {
    
    jQuery('.uacf7_multistep_image_upload').each(function(){
        
        var uacf7_multistep_image_file_frame;
        jQuery(this).on('click', function (e) {
            e.preventDefault();

            var uacf7_multistep_progressbar_image = jQuery(this).parent('.uacf7_multistep_progressbar_image_row').find('input.uacf7_multistep_progressbar_image');

            // If the media frame already exists, reopen it.
            if (uacf7_multistep_image_file_frame) {
                uacf7_multistep_image_file_frame.open();
                return;
            }

            // Create the media frame.
            uacf7_multistep_image_file_frame = wp.media.frames.uacf7_multistep_image_file_frame = wp.media({
                title: jQuery(this).data('uploader_title'),
                button: {
                    text: jQuery(this).data('uploader_button_text'),
                },
                multiple: false // Set to true to allow multiple files to be selected
            });

            // When a file is selected, run a callback.
            uacf7_multistep_image_file_frame.on('select', function () {
                // We set multiple to false so only get one image from the uploader
                attachment = uacf7_multistep_image_file_frame.state().get('selection').first().toJSON();

                var url = attachment.url;

                //var field = document.getElementById('bafg_before_after_image');
                //var thumbnail = document.getElementById('bafg_before_after_image_thumbnail');

                //field.value = url;
                uacf7_multistep_progressbar_image.val(url);
                //thumbnail.setAttribute('src', url);
            });

            // Finally, open the modal
            uacf7_multistep_image_file_frame.open();
        });
        
    });
    	
})(jQuery);