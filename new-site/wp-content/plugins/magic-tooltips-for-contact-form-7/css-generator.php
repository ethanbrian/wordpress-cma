<?php

add_action( 'admin_menu', 'mtfcf7_tooltip_generator_add_admin_menu' );
add_action( 'admin_init', 'mtfcf7_tooltip_generator_init' );


function mtfcf7_tooltip_generator_add_admin_menu(  ) { 

	add_submenu_page( 
          'magic_tooltips_for_contact_form_7' 
        , 'Tooltip Style Generator' 
        , 'Tooltip Style Generator'
        , 'manage_options'
        , 'mtfcf7_tooltip_generator'
        , 'mtfcf7_tooltip_generator_page'
    );
}

function mtfcf7_tooltip_generator_before_save($options_tooltip_generator, $old_value) {
	return $options_tooltip_generator;
}


function mtfcf7_tooltip_generator_init(  ) { 

	register_setting( 'mtfcf7_tooltip_generator', 'mtfcf7_tooltip_generator' );
	add_filter( 'pre_update_option_mtfcf7_tooltip_generator', 'mtfcf7_tooltip_generator_before_save', 10, 2 );

	add_settings_section(
		'mtfcf7_tooltip_generator_section', 
		'',
		//__( 'Your section description', 'magic_tooltips_for_contact_form_7' ), 
		'mtfcf7_tooltip_generator_section_callback', 
		'mtfcf7_tooltip_generator'
	);

	add_settings_field( 
		'css_code', 
		__( 'Css Code', 'magic_tooltips_for_contact_form_7' ), 
		'mtfcf7_tooltip_generator_css_code_render', 
		'mtfcf7_tooltip_generator', 
		'mtfcf7_tooltip_generator_section' 
	);

	add_settings_field( 
		'css_options', 
		__( 'Css Code', 'magic_tooltips_for_contact_form_7' ), 
		'mtfcf7_tooltip_generator_css_options_render', 
		'mtfcf7_tooltip_generator', 
		'mtfcf7_tooltip_generator_section' 
	);

	add_settings_field( 
		'js_code', 
		__( 'Js Code', 'magic_tooltips_for_contact_form_7' ), 
		'mtfcf7_tooltip_generator_js_code_render', 
		'mtfcf7_tooltip_generator', 
		'mtfcf7_tooltip_generator_section' 
	);

	if (isset($_GET['page']))
		if(in_array( $_GET['page'], array( 'mtfcf7_tooltip_generator' ) ) !== false ) { # load js for options page
		mtfcf7_load_css_generator_css_and_js();
	}
}

function mtfcf7_load_css_generator_css_and_js() {
    $styles = array(
        array('handle' => 'mtfcf7-jquery-ui-slider-custom', 'src' => 'jquery-ui.custom.css', 'deps' => false, 'media'=>"all"),
        array('handle' => 'jquery.miniColors', 'src' => 'jquery.miniColors.css', 'deps' => false, 'media'=>"all"),
        array('handle' => 'mtfcf7_css_generator_style', 'src' => 'style.css', 'dep'=> array('jquery-ui-slider' ), 'media'=>"all"),
        array('handle' => 'jquery.qtip', 'src' => '../../css/jquery.qtip.min.css', 'deps' => false, 'media'=>"all"),
        array('handle' => 'mtfcf7_css_generator_init', 'src' => 'init.css', 'deps' => false, 'media'=>"all")
    );
    for ($i = 0; $i < sizeof($styles); $i++) {
        wp_enqueue_style($styles[$i]['handle'], plugins_url( "assets/tooltip-style-generator/css/". $styles[$i]['src'], __FILE__ ) , $styles[$i]['deps'], $styles[$i]['media'] );
    }

    $scripts = array(
        array('handle' => 'jquery.qtip', 'src'=>'../../js/jquery.qtip.min.js','dep'=> array( 'jquery', 'jquery-ui-slider' ),'var'=> false,'in_foot'=> true),
        array('handle' => 'jquery.mousewheel', 'src'=>'libs/jquery.mousewheel.min.js','dep'=> array( 'jquery' ),'var'=> false,'in_foot'=> true),
        array('handle' => 'jquery.miniColors', 'src'=>'libs/jquery.miniColors.min.js','dep'=> array( 'jquery' ),'var'=> false,'in_foot'=> true),
        array('handle' => 'jquery.cookie', 'src'=>'libs/jquery.cookie.js','dep'=> array( 'jquery' ),'var'=> false,'in_foot'=> true),
        array('handle' => 'mtfcf7_tinycolor', 'src'=>'tinycolor.js','dep'=> array( 'jquery' ),'var'=> false,'in_foot'=> true),
        array('handle' => 'mtfcf7_tooltip', 'src'=>'tooltip.js','dep'=> array( 'jquery' ),'var'=> false,'in_foot'=> true),
        array('handle' => 'mtfcf7_tweaker', 'src'=>'tweaker.js','dep'=> array( 'jquery' ),'var'=> false,'in_foot'=> true),
        array('handle' => 'mtfcf7_script', 'src'=>'script.js','dep'=> array( 'jquery' ),'var'=> false,'in_foot'=> true),
    );

    for ($i=0; $i < sizeof($scripts); $i++) {
        wp_enqueue_script( $scripts[$i]['handle'], plugins_url( "assets/tooltip-style-generator/js/". $scripts[$i]['src'], __FILE__ ), $scripts[$i]['dep'], $scripts[$i]['ver'], $scripts[$i]['in_foot'] );    
    }
}


function mtfcf7_tooltip_generator_css_code_render() { 
	$options = get_option( 'mtfcf7_tooltip_generator' );
	?>
	<input id="mtfcf7-tooltip-generator-css-code" type='text' name='mtfcf7_tooltip_generator[css_code]' value='<?php echo $options['css_code']; ?>'>
	<?php

}

function mtfcf7_tooltip_generator_css_options_render() { 
	$options = get_option( 'mtfcf7_tooltip_generator' );
	?>
	<input id="mtfcf7-tooltip-generator-css-options" type='text' name='mtfcf7_tooltip_generator[css_options]' value='<?php echo $options['css_options']; ?>'>
	<?php

}

function mtfcf7_tooltip_generator_js_code_render() { 
	$options = get_option( 'mtfcf7_tooltip_generator' );
	?>
	<input id="mtfcf7-tooltip-generator-js-code" type='text' name='mtfcf7_tooltip_generator[js_code]' value='<?php echo $options['js_code']; ?>'>
	<?php
}


function mtfcf7_tooltip_generator_section_callback(  ) { 
	echo __( 'Use this tool to generate your custom tooltip style.', 'magic_tooltips_for_contact_form_7' );
}


function mtfcf7_tooltip_generator_page(  ) { 
	?>
	<form action='options.php' id="mtfcf7-tooltip-generator-form" method='post'>
		<div class="wrap"><h1>Tooltip Style Generator</h1></div>
		<style>
			#mtfcf7-tooltip-generator-form .form-table {display: none;}
		</style>
		<div class="mtfcf7-tooltip-generator-form-box">
		<?php
		settings_fields( 'mtfcf7_tooltip_generator' );
		do_settings_sections( 'mtfcf7_tooltip_generator' );
		?>
		</div>
		<div class="main" role="main">
		  <div class="configurator clearfix">
		    <div class="controls">
		      
		      <div class="clearfix">

		    <div class="text">
		      <h3>Position</h3>
		      <div class="inline-container">
		          <select id="position" class="form-control">
		            <option value="bottom center|center top">top</option>
		            <option value="top center|center bottom">bottom</option>
		            <option value="right center|left center">left</option>
		            <option value="left center|right center">right</option>
		          </select>
		      </div>
		    </div>
		    <div class="font-size">
		      <h3>Offset Left</h3>
		      <input type="text" id="offsetLeftInput" data-bind="styles.offsetLeft" class="wheel">
		    </div>

		    <div class="font-size">
		      <h3>Offset Top</h3>
		      <input type="text" id="offsetTopInput" data-bind="styles.offsetTop" class="wheel">
		    </div>

		</div>
		<div class="clearfix">
		        <div class="text">
		          <h3>Line Height</h3>
		          <input type="text" data-bind="styles.lineHeight" class="wheel">
		        </div>
		        <div class="font-size">
		          <h3>Font Size</h3>
		          <input type="text" data-bind="styles.fontSize" class="wheel">
		        </div>
		        <div class="font-color">
		          <h3>Font Color</h3>
		          <input type="text" data-bind="styles.fontColor" class="color">
		        </div>
		      </div>
		      
		      <div class="clearfix second-row-ct hidden">
		        <input type="checkbox" class="second-row" data-bind="secondRow">
		        <div class="text">
		          <input type="text" data-bind="text.secondRow">
		        </div>
		        <div class="font-size">
		          <input type="text" data-bind="styles.secondRowFontSize" class="wheel">
		        </div>
		      </div>

		      <div class=" clearfix" style="margin-top: 15px;">
		        <div class="text">
		        <h3>Background Color</h3>
		        <input type="text" data-bind="styles.backgroundColor" class="color" id="background-color">
		        </div>
		        <div class="font-color">
		          <h3>Border Color</h3>
		          <input type="text" data-bind="styles.borderColor" class="color" id="border-color">
		        </div>
		        <div class="font-color">
		          <h3>Border Width</h3>
		          <input type="text" data-bind="styles.borderWidth" class="wheel" id="border-width">
		        </div>
		      </div>

		      <div class="border clearfix">
		        <h3>Border Radius</h3>
		        <div class="slider" id="border-slider"></div>
		        <input type="text" data-bind="styles.borderRadius" class="wheel" id="border">
		      </div>

		      <div class="padding clearfix">
		        <h3>Padding Multiply</h3>
		        <div class="slider" id="padding-slider"></div>
		        <input type="text" data-bind="styles.padding" id="padding">
		      </div>

		    </div>
		    <div class="preview"></div>
		  </div>

		</div>
		<p class="submit">
		<?php submit_button(null, 'primary', 'submit', false); ?>
		<input type="button" value="Reset" class="button button-info" id="reset-css-cf7" name="reset"></p>
	</form>
	<?php
}

?>