jQuery(document).ready(function () {
	//Conditional check
	function uacf7_redirect_conditional_field() {
		var uacf7_redirect_to_type = jQuery('input.uacf7_redirect_to_type:checked').val();

		if (uacf7_redirect_to_type == 'to_page') {
			jQuery('.uacf7_redirect_to_page').show();
			jQuery('.uacf7_redirect_to_url').hide();
		} else {
			jQuery('.uacf7_redirect_to_url').show();
			jQuery('.uacf7_redirect_to_page').hide();
		}
	}
	uacf7_redirect_conditional_field();
	
	jQuery('input.uacf7_redirect_to_type').on('change', function(){
		uacf7_redirect_conditional_field();
	});
});