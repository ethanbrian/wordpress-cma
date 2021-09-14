(function($){
	$(document).ready(function() {
	  Tweaker.init();

	  if (navigator.platform && navigator.platform.match(/win/i)) {
	    $(".stdl").show();
	  }

	  $('.mm-tooltip-cf7').each(function() { // Notice the .each() loop, discussed below
	      console.log('Tooltip.options()', Tooltip.options());
	      $(this).qtip($.extend(Tooltip.options(), {
	          content: {
	              text: $(this).next('div') // Use the "div" element next to this for the content
	          },
	          show: true
	      }));
	  });
	});
})(jQuery);