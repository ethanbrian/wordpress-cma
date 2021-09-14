<?php
/**
 * Plugin Name: UACF7 Addon - Post Submission
 * Plugin URI: https://live.themefic.com/ultimate-cf7/pro
 * Description: Build a customizable form to submit post from the frontend. 
 * Version: 1.0.0
 * Author: Themefic
 * Author URI: https://themefic.com/
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: ultimate-post-submission
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
* Init post submission
*/
add_action( 'plugins_loaded', 'uacf7_post_submission_init' );
function uacf7_post_submission_init(){
    
    //Register text domain
    load_plugin_textdomain( 'ultimate-post-submission', false, basename( dirname( __FILE__ ) ) . '/languages' ); 

    if(class_exists('Ultimate_Addons_CF7_PRO')){
        
        //Require functions file
        require_once( 'inc/functions.php' );
        
        //Admin scripts
        add_action( 'admin_enqueue_scripts', 'admin_enqueue_scripts' );
        
        //Load post submission
        uacf7_post_submission_load();

    }else{
        //Admin notice
        add_action( 'admin_notices',  'uacf7_pro_post_submission_admin_notice_error' );
    }
    
}

/*
* Admin enqueue scripts
*/
function admin_enqueue_scripts() {

    wp_enqueue_style( 'uacf7-post-submission-admin', plugin_dir_url( __FILE__ ) . 'assets/admin-style.css' );
}

/*
* Loaded post submission
*/
function uacf7_post_submission_load() {
    
    if( get_option('uacf7_pro_activated') == 'yes' ) {
        
        $uacf7_options = get_option( 'uacf7_option_name' );
        
        if( isset($uacf7_options['uacf7_enable_post_submission']) && $uacf7_options['uacf7_enable_post_submission'] === 'on' ) {
            
            require_once( 'inc/post-submission.php' );
        }
        
    }else {
        
        //Admin notice
        add_action( 'admin_notices',  'uacf7_pro_post_submission_admin_notice_error' );
    }
}

/*
* Admin notice: Require pro version
*/
function uacf7_pro_post_submission_admin_notice_error() {
    ?>
    <div class="notice notice-error is-dismissible">
        <p><?php echo esc_html__( 'Ultimate Post Submission requires Ultimate Addons For Contact Form 7 Pro to be installed and active with license key.', 'ultimate-post-submission' ); ?>
        </p>
    </div>
    <?php
}
