<?php

add_action( 'admin_menu', 'mtfcf7_add_admin_menu' );
add_action( 'admin_init', 'mtfcf7_settings_init' );


function mtfcf7_add_admin_menu(  ) { 

	add_menu_page( 'Magic Tooltips For Contact Form 7', 'Magic Tooltips For Contact Form 7', 'manage_options', 'magic_tooltips_for_contact_form_7', 'mtfcf7_options_page');
	add_submenu_page( 
          'magic_tooltips_for_contact_form_7' 
        , 'Magic Tooltips For Contact Form 7' 
        , 'Settings'
        , 'manage_options'
        , 'magic_tooltips_for_contact_form_7'
        , 'mtfcf7_options_page'
    );
}

function mtfcf7_resolve_root_domain($url, $max=6)
{
	$domain = parse_url($url, PHP_URL_HOST);
	if (!strstr(substr($domain, 0, $max), '.')) {
		return ($domain);
	}
	else {
		return (preg_replace("/^(.*?)\.(.*)$/", "$2", $domain));
	}
}

function mtfcf7_settings_before_save( $options, $old_value ) {
	return $options;
}


function mtfcf7_settings_init(  ) { 
	register_setting( 'mtfcf7_settings', 'mtfcf7_settings' );
	add_filter( 'pre_update_option_mtfcf7_settings', 'mtfcf7_settings_before_save', 10, 2 );

	add_settings_section(
		'mtfcf7_pluginPage_section', 
		__( 'Your section description', 'magic_tooltips_for_contact_form_7' ), 
		'mtfcf7_settings_section_callback', 
		'mtfcf7_settings'
	);

	add_settings_field( 
		'mtfcf7_select_actve_form', 
		__( 'Active Form', 'magic_tooltips_for_contact_form_7' ), 
		'mtfcf7_select_actve_form_render', 
		'mtfcf7_settings', 
		'mtfcf7_pluginPage_section' 
	);

	add_settings_field( 
		'mtfcf7_checkbox_mouse_over', 
		__( 'When to Show Tooltip', 'magic_tooltips_for_contact_form_7' ), 
		'mtfcf7_checkbox_mouse_over_render', 
		'mtfcf7_settings', 
		'mtfcf7_pluginPage_section' 
	);

	add_settings_field( 
		'mtfcf7_checkbox_when_focus_input', 
		__( '', 'magic_tooltips_for_contact_form_7' ), 
		'mtfcf7_checkbox_when_focus_input_render', 
		'mtfcf7_settings', 
		'mtfcf7_pluginPage_section' 
	);

	add_settings_field( 
		'mtfcf7_checkbox_add_icon', 
		__( 'Help Icon for Title', 'magic_tooltips_for_contact_form_7' ), 
		'mtfcf7_checkbox_add_icon_render', 
		'mtfcf7_settings', 
		'mtfcf7_pluginPage_section' 
	);

	add_settings_field( 
		'mtfcf7_checkbox_add_icon_fontawsome', 
		__( '', 'magic_tooltips_for_contact_form_7' ), 
		'mtfcf7_checkbox_add_icon_fontawsome_render', 
		'mtfcf7_settings', 
		'mtfcf7_pluginPage_section' 
	);

	add_settings_field( 
		'mtfcf7_checkbox_add_underline', 
		__( 'Underline for Title', 'magic_tooltips_for_contact_form_7' ), 
		'mtfcf7_checkbox_add_underline_render', 
		'mtfcf7_settings', 
		'mtfcf7_pluginPage_section' 
	);

	add_settings_field( 
		'mtfcf7_checkbox_custom_css', 
		__( 'Custom Css', 'magic_tooltips_for_contact_form_7' ), 
		'mtfcf7_textarea_custom_css_render', 
		'mtfcf7_settings', 
		'mtfcf7_pluginPage_section' 
	);
}


function mtfcf7_select_actve_form_render() {
	$options = get_option( 'mtfcf7_settings' );
	global $post;
	query_posts( 
		array( 
			// 'portfolio_categories' => $term -> slug, 
			'post_type' => 'wpcf7_contact_form', 
			'showposts' => -1, 
			// 'caller_get_posts' => 1, 
			// 'post__not_in' => $do_not_duplicate 
		) 
	);
	
	?>
	<select name='mtfcf7_settings[active_form]'>
		<option value="" <?php selected( $options['active_form'], '' ); ?>>Please select a form</option>
		<?php if ( have_posts()) : ?>

			<?php while ( have_posts() ) : the_post(); ?>
				<option value="<?php the_ID(); ?>" <?php selected( $options['active_form'], get_the_ID() ); ?>><?php the_title() ?></option>
			<?php endwhile; ?>
		<?php endif; ?>
	</select>
	<p>This version is limited to one form on one domain. If you want to use Magic Tooltips for unlimited forms on one domain, or unlimited forms on unlimited domains, purchase our <a href="https://contactform7.magictooltips.com/pricing/" target="_blank">Pro or Developer version</a>.</p>
	<?php
}

function mtfcf7_checkbox_description_as_tooltip_render(  ) { 

	$options = get_option( 'mtfcf7_settings' );
	?>
	<input type='checkbox' name='mtfcf7_settings[description_as_tooltip]' <?php checked( $options['description_as_tooltip'], 1 ); ?> value='1'> Hide description and show field description as tooltip.
	<?php

}

function mtfcf7_checkbox_mouse_over_render(  ) { 

	$options = get_option( 'mtfcf7_settings' );
	?>
	<input type='checkbox' name='mtfcf7_settings[mouse_over]' <?php checked( $options['mouse_over'], 1 ); ?> value='1'> When mouse hovers over the title of form field.
	<?php

}

function mtfcf7_checkbox_when_focus_input_render(  ) { 

	$options = get_option( 'mtfcf7_settings' );
	?>
	<input type='checkbox' name='mtfcf7_settings[focus_input]' <?php checked( $options['focus_input'], 1 ); ?> value='1'> When a form field is currently targeted by the keyboard, or activated by the mouse.
	<?php
}

function mtfcf7_checkbox_when_hover_input_render(  ) { 

	$options = get_option( 'mtfcf7_settings' );
	?>
	<input type='checkbox' name='mtfcf7_settings[hover_input]' <?php checked( $options['hover_input'], 1 ); ?> value='1'> When mouse hover a form field.
	<?php
}

function mtfcf7_checkbox_add_icon_render(  ) { 

	$options = get_option( 'mtfcf7_settings' );
	?>
	<input type='checkbox' name='mtfcf7_settings[add_icon]' <?php checked( $options['add_icon'], 1 ); ?> value='1'> Add help icon after the title of form field.
	<?php

}

function mtfcf7_checkbox_add_icon_fontawsome_render(  ) { 

	$options = get_option( 'mtfcf7_settings' );
	?>
	<input type='checkbox' name='mtfcf7_settings[add_icon_fontawsome]' <?php checked( $options['add_icon_fontawsome'], 1 ); ?> value='1'> Check this option if your website does not include <a target="_blank" href="https://fortawesome.github.io">Font Awesome</a> yet. 
	<?php

}

function mtfcf7_checkbox_add_underline_render(  ) { 

	$options = get_option( 'mtfcf7_settings' );
	?>
	<input type='checkbox' name='mtfcf7_settings[add_underline]' <?php checked( $options['add_underline'], 1 ); ?> value='1'> Add underline to the title of form field.
	<?php

}

function mtfcf7_textarea_custom_css_render(  ) { 

	$options = get_option( 'mtfcf7_settings' );
	?>
	<textarea cols='80' rows='5' name='mtfcf7_settings[custom_css]'><?php echo $options['custom_css']; ?></textarea>
	<?php

}


function mtfcf7_select_field_4_render(  ) { 

	$options = get_option( 'mtfcf7_settings' );
	?>
	<select name='mtfcf7_settings[mtfcf7_select_field_4]'>
		<option value='1' <?php selected( $options['mtfcf7_select_field_4'], 1 ); ?>>Option 1</option>
		<option value='2' <?php selected( $options['mtfcf7_select_field_4'], 2 ); ?>>Option 2</option>
	</select>

<?php

}


function mtfcf7_radio_field_5_render(  ) { 

	$options = get_option( 'mtfcf7_settings' );
	?>
	<input type='radio' name='mtfcf7_settings[mtfcf7_radio_field_5]' <?php checked( $options['mtfcf7_radio_field_5'], 1 ); ?> value='1'>
	<?php

}


function mtfcf7_settings_section_callback(  ) { 

	echo __( 'Choose your tooltip options', 'magic_tooltips_for_contact_form_7' );

}


function mtfcf7_options_page(  ) { 



	?>
	<form action='options.php' method='post' id="mtfcf7-options-form">
		<input type='hidden' name='mtfcf7_settings[dummy]' value="1">
		<div class="wrap"><h1>Magic Tooltips For Contact Form 7 Settings</h1></div>

		
		<?php
		// mtfcf7_show_premium();
		settings_fields( 'mtfcf7_settings' );
		do_settings_sections( 'mtfcf7_settings' );
		submit_button();
		?>
		
	</form>
	<?php

}

?>