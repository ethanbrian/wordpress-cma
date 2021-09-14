<?php

add_action( 'admin_menu', 'mtfcf7_tooltip_help_add_admin_menu' );


function mtfcf7_tooltip_help_add_admin_menu(  ) { 

	add_submenu_page( 
          'magic_tooltips_for_contact_form_7' 
        , 'Help' 
        , 'Help'
        , 'manage_options'
        , 'mtfcf7_tooltip_help'
        , 'mtfcf7_tooltip_help_page'
    );
}

function mtfcf7_tooltip_help_page(  ) { 
	?>
		<div class="wrap"><h1>Magic Tooltips For Contact Form 7 Help</h1></div>
		<p>Please review the plugin documentation and frequently asked questions (FAQ) first. If you still can't find the answer <a target="_blank" href="https://contactform7.magictooltips.com/contact-2/" target="_blank">open a support ticket</a> and we will be happy to answer your questions and assist you with any problems. Please note: If you have not purchased a license from us, you will not have access to these help resources.</p>
		<h3>Documentation</h3>
		<ul>
			<li><a href="https://contactform7.magictooltips.com/documentation/" target="_blank">Online Documentation</a></li>
			<li><a target="_blank" href="https://contactform7.magictooltips.com/faq/">FAQ</a></li>
		</ul>
		<h3>More</h3>
		<ul>
			<li><a href="https://contactform7.magictooltips.com/contact-2/" target="_blank">Open a support ticket</a></li>
			<li><a  target="_blank" href="https://contactform7.magictooltips.com/pricing/">Purchase Pro or Developer version</a></li>
		</ul>
	<?php
}

?>