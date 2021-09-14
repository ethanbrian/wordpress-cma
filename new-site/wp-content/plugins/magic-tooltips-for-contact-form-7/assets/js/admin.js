(function($) {
	
	$(document).ready(function(){
		$('#mtfcf7-tooltip-generator-form').submit(function(event){
			var tooltip = window.Tooltip;
			$('#mtfcf7-tooltip-generator-css-code').val(tooltip.css());
			$('#mtfcf7-tooltip-generator-css-options').val(JSON.stringify(tooltip.cssOptions()));
			//console.log(document.getElementById('mtfcf7-tooltip-generator-iframe').contentWindow.Tooltip.options());
			$('#mtfcf7-tooltip-generator-js-code').val(JSON.stringify(tooltip.options()));		
			//event.preventDefault();
		});

		$('#reset-css-cf7').click(function(){
			var tooltip = window.Tooltip;
			tooltip.reset();
		});

		// $('#toplevel_page_magic_tooltips_for_contact_form_7 .wp-submenu li:last-child a').attr('target', '_blank');
	});

})(jQuery);