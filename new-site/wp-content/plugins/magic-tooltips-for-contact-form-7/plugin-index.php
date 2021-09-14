<?php
/*
 * Plugin Name: Magic Tooltips For Contact Form 7
 * Version: 1.0.24
 * Plugin URI: https://contactform7.magictooltips.com
 * Description: Easily add tooltips to your Contact Form 7 fields
 * Author: Magic Tooltips
 * Author URI: https://contactform7.magictooltips.com
 * Requires at least: 3.9
 * Tested up to: 5.5
 *
 * @package WordPress
 * @author Flannian
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$mcfgfBaseDir = dirname(__FILE__);

function mtfcf7_check_pro_activated() {
	$apl=get_option('active_plugins');
	$plugins=get_plugins();
	$activated_plugins=array();
	foreach ($apl as $p){           
	    if(isset($plugins[$p]) && $plugins[$p]['Name'] == 'Magic Tooltips For Contact Form 7 Pro'){
	    	return $p;
	    }           
	}
	return false;
}

function magic_tooltips_for_contact_form_7_install(){
	if(mtfcf7_check_pro_activated()) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		wp_die( 'You have installed Magic Tooltips For Contact Form 7 Pro version. please disable Pro version first or use Pro version instead.' );
	}
	
	$options = get_option('mtfcf7_tooltip_generator',false);
	if(!$options) {
		$defaultOptions = '{"css_code":".mm-tooltip-cf7-container { color: #FFFFFF; border-radius: 5px; font-size: 14px; background-color: #333333; -webkit-border-radius: 5px; -moz-border-radius: 5px; margin-left: 0px; margin-top: 0px; border-color: #333333; border-width: 1; line-height: 150%;}.mm-tooltip-cf7-content { line-height: 150%; padding: 2.4000000000000004px 6px 2.4000000000000004px 6px;}","css_options":"{\"fontColor\":\"#FFFFFF\",\"fontSize\":\"14\",\"backgroundColor\":\"#333333\",\"borderRadius\":5,\"offsetLeft\":\"0\",\"padding\":0.2,\"offsetTop\":\"0\",\"borderColor\":\"#333333\",\"borderWidth\":1,\"lineHeight\":\"150%\"}","js_code":"{\"position\":{\"my\":\"left center\",\"at\":\"right center\",\"adjust\":{\"method\":\"none\"}},\"style\":{\"classes\":\"mm-tooltip-cf7-container\"},\"content\":{\"text\":{\"0\":{},\"length\":1,\"prevObject\":{\"0\":{\"jQuery172021905201394110918\":4},\"context\":{\"jQuery172021905201394110918\":4},\"length\":1},\"context\":{\"jQuery172021905201394110918\":4},\"selector\":\".next(div)\"}},\"show\":true}"}';

		$s = json_decode($defaultOptions,true);
		update_option('mtfcf7_tooltip_generator',$s);
	}

	$settings = get_option('mtfcf7_settings',false);

	if(!$settings) {
		$defaultSettings = '{"dummy":"1","mouse_over":"1","add_icon":"1","add_icon_fontawsome":"1","add_underline":"1"}';
		$ss = json_decode($defaultSettings,true);
		
		update_option('mtfcf7_settings',$ss);
	}
}

require_once ($mcfgfBaseDir.'/settings.php');
require_once ($mcfgfBaseDir.'/css-generator.php');
require_once ($mcfgfBaseDir.'/help.php');
require_once ($mcfgfBaseDir.'/magic-tooltips-for-contact-form-7.php');
register_activation_hook( __FILE__, 'magic_tooltips_for_contact_form_7_install' );

add_action( 'admin_notices', 'mtfcf7_settings_notices' );

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'mtfcf7_upgrade_premium_action_link');

function mtfcf7_upgrade_premium_action_link($links) {
	return array_merge(array('mtfcf7PluginCallout' => '<a href="https://contactform7.magictooltips.com/pricing/" target="_blank"><strong style="color: #6A9A22; display: inline;">Upgrade To Premium</strong></a>'), $links);
}

function mtfcf7_settings_notices() {
	global $pagenow;
	global $current_user;
	$userid = $current_user->ID;
	$settings = get_option('mtfcf7_settings',false);

	if ( $settings && $settings['active_form'] ) { // already done.
		// Check if plugin install date is set in database
		$opt_install = get_option('mtfcf7_install');
		
		if($opt_install === false) {
			// Set install date to today
			update_option('mtfcf7_install', date('Y-m-d'));
		}


		// Compare install date with today
		$date_install = isset($opt_install) ? $opt_install : date('Y-m-d');
		
		// If install date is more than 10 days old...
		if(strtotime($date_install) < strtotime('-10 days')){
			// If the user clicked to dismiss notice...
			if ( isset( $_GET['dismiss_mtfcf7_ug_notice'] ) && 'yes' == $_GET['dismiss_mtfcf7_ug_notice'] ) {
				
				// Update user meta
				add_user_meta( $userid, 'ignore_mtfcf7_ag_notice', 'yes', true );
			}
			if ( !get_user_meta( $userid, 'ignore_mtfcf7_ag_notice' ) ) {
				mtfcf7_wordpress_version_notice();
			}
		}
		return;
	}

	$link = add_query_arg(
		array( ),
		menu_page_url( 'magic_tooltips_for_contact_form_7', false ) );

	$link = sprintf( '<a href="%s">%s</a>', $link, esc_html( __( 'Set up Magic Tooltips For Contact Form 7 now.', 'mtfcf7' ) ) );

	$message = __( "To display Magic Tooltips, choose a Contact Form 7 form as your active form", 'mtfcf7' );

	echo sprintf( '<div class="notice notice-warning is-dismissible"><p>%s &raquo; %s</p></div>', esc_html( $message ), $link );
}

// Alert plugin update message
function mtfcf7_wordpress_version_notice() {
	
	global $pagenow;
	
	echo '<style type="text/css">';
	echo '.mtfcf7_plugins_page_banner {
		border: 1px solid #d4d4d4;
		margin: 12px 0;
		background: #FFF;
		position: relative;
		overflow: hidden;
	}
	.mtfcf7_plugins_page_banner .text {
		color: #000;
		font-size: 15px;
		line-height: 26px;
		margin: 18px 18px 14px;
		float: left;
		width: auto;
		max-width: 80%;
	}
	.mtfcf7_plugins_page_banner .text span {
		font-size: 12px;
		opacity: 0.7;
	}
	.mtfcf7_plugins_page_banner .button {
		float: left;
		border: none; 
		font-size: 14px;
		margin: 18px 0 18px 16px;
		padding: 12px 0;
		color: #FFF;
		text-shadow: none;
		font-weight: bold;
		background: #0074A2;
		-moz-border-radius: 3px;
		border-radius: 3px;
		-webkit-border-radius: 3px;
		text-decoration: none;
		height: 50px;
		text-align: center;
		text-transform: uppercase;
		width: 147px;
		box-shadow: none;
		line-height: 26px;
	}
	.mtfcf7_plugins_page_banner .button:hover,
	.mtfcf7_plugins_page_banner .button:focus {    
		background: #222;
		color: #FFF;
	}
	.mtfcf7_plugins_page_banner .icon {
		float: right;
		margin: 12px 8px 8px 0;
	}
	.mtfcf7_plugins_page_banner .close_icon {
		float: right;
		margin: 8px;
		cursor: pointer;
	}
	.mtfcf7_plugins_page_banner .close_icon:before {
		background: 0 0;
		color: #b4b9be;
		content: "\f153";
		display: block;
		font: 400 16px/20px dashicons;
		speak: none;
		height: 20px;
		text-align: center;
		width: 20px;
		-webkit-font-smoothing: antialiased;
	}';
	echo '</style>';
	
	echo '<div class="updated" style="padding: 0; margin: 0; border: none; background: none;">
			<div class="mtfcf7_plugins_page_banner">
				<a href="'.$pagenow.'?dismiss_mtfcf7_ug_notice=yes" class="close_icon" data-repository="wpml" style="text-decoration:none"><span class="screen-reader-text">Dismiss</span></a>
				<div class="button_div">
					<a class="button" target="_blank" href="https://contactform7.magictooltips.com/pricing/">Learn More</a>				
				</div>
				<div class="text">
					It\'s time to consider upgrading <strong>Magic Tooltips For Contact Form 7</strong> to the <strong>PRO</strong> version.<br />
					<span>Extend standard plugin functionality with new, enhanced options.</span>
				</div>
			</div>  
		</div>';
}