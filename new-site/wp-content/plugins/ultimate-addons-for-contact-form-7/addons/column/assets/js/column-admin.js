;(function ($) {
    'use strict';

    if (_wpcf7 == null) {
        var _wpcf7 = wpcf7;
    };

    var uacf7_column_compose = _wpcf7.taggen.compose;

    _wpcf7.taggen.compose = function (tagType, $form) {

        var uacf7_column_tag_close = uacf7_column_compose.apply(this, arguments);
		var uacf7_row = "[uacf7-row]";
        if (tagType == 'uacf7-col'){
			return "[uacf7-row][uacf7-col col:12] <your code> [/uacf7-col][/uacf7-row]";
		} 

        return uacf7_column_tag_close;
    };
	
	jQuery(document).on( 'click', '.uacf7-column-select', function(){
		jQuery(this).siblings().removeClass('example-active');
		jQuery(this).addClass('example-active');
		
		var uacf7ColumnTag = jQuery(this).attr('data-column-codes');
		jQuery('.uacf7-column-tag-insert').val(uacf7ColumnTag);
		
		jQuery('.insert-tag.uacf7-column-insert-tag').trigger('click');
	});

})(jQuery);
