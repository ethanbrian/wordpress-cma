(function( $ ) {
    $(function() {
         
        // Add Color Picker to all inputs that have 'color-field' class
        $( '.uacf7-color-picker' ).wpColorPicker();
         
    });
})( jQuery );

jQuery(document).ready(function($) {
  wp.codeEditor.initialize($('#uacf7-customcss'), cm_settings);
})