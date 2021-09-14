<?php
/*
	Magic Tooltips For Contact Form 7
*/
if ( ! defined( 'ABSPATH' ) ) exit;

$mcfgfBaseDir = dirname(__FILE__);
require_once ($mcfgfBaseDir.'/lib/wp-hooks.php');

class MagicTooltipsForContactForm7 extends WP_Hooks {

	function wpcf7_form_elements($html) {
		// var_dump($html);
		// die();
		return str_replace("<p></tip>", "</tip><p>", $html);
	}
	
	function esc_html($safe_text, $text) {
		// echo $text;
		if(strpos($text, '&lt;tip&gt;') !== false) {
			return htmlspecialchars_decode($text);
		}
		else if(strpos($text, '<tip>') !== false) {
			return $text;
		}
		return $safe_text;
	}
	
	function admin_enqueue_scripts() {
		// Register and Enqueue a Stylesheet
		// if(is_admin()) {
		    wp_register_style( 'mtfcf7_admin', plugins_url( 'assets/css/admin.css', __FILE__ ));
		    wp_enqueue_style( 'mtfcf7_admin' );

		    wp_register_script( 'mtfcf7_admin', plugins_url( "assets/js/admin.js", __FILE__ ), array('jquery'), '1.61');
	    	wp_enqueue_script( 'mtfcf7_admin' );
		// }
	}
	
	function wpcf7_enqueue_scripts() {

	    wp_register_style( 'qtip2', plugins_url( 'assets/css/jquery.qtip.min.css', __FILE__ ));
	    wp_enqueue_style( 'qtip2' );
		
	    wp_register_style( 'mtfcf7', plugins_url( 'assets/css/custom.css', __FILE__ ));
	    wp_enqueue_style( 'mtfcf7' );

	    wp_register_script( 'qtip2', plugins_url( "assets/js/jquery.qtip.js", __FILE__ ), array('jquery'));
	    wp_enqueue_script( 'qtip2' );
		
		wp_register_script( 'jquery.imagesloaded', plugins_url( "assets/js/imagesloaded.pkgd.min.js", __FILE__ ), array('jquery'));
		wp_enqueue_script( 'jquery.imagesloaded' );
		
		wp_register_script( 'mtfcf7', plugins_url( "assets/js/custom.js", __FILE__ ), array('jquery'), '1.61');
	    wp_enqueue_script( 'mtfcf7' );

	    $mtfcf7_options = get_option('mtfcf7_tooltip_generator', false);

	    wp_localize_script( 'mtfcf7', 'mtfcf7', $mtfcf7_options);

	    $mtfcf7_settings = get_option('mtfcf7_settings', false);

	    if(isset($mtfcf7_settings['license_key'])) {
			unset($mtfcf7_settings['license_key']);
		}

	    //add fontawsome support
	    if($mtfcf7_settings && $mtfcf7_settings['add_icon_fontawsome']) {
	    	wp_register_style( 'fontawesome', plugins_url( 'assets/css/font-awesome.min.css', __FILE__ ), array(), '4.7');
	    	wp_enqueue_style( 'fontawesome' );
	    }

	    wp_localize_script( 'mtfcf7', 'mtfcf7_settings', $mtfcf7_settings);
	}
}

global $magic_tooltips_for_contact_form_7;
$magic_tooltips_for_contact_form_7 = new MagicTooltipsForContactForm7();