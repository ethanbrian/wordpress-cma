<?php

/*
 * Plugin Name: SUMO Memberships
 * Plugin URI:
 * Description: SUMO Memberships is a WooCommerce Membership System
 * Version: 6.0
 * Author: Fantastic Plugins
 * Author URI:http://fantasticplugins.com/
 */

if( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly
}

include_once (ABSPATH . 'wp-admin/includes/plugin.php') ;

/*
 * Normal Structure to Check WooCommerce is Active or Not
 *
 */

class SUMOMemberships {

    public $version = '6.0' ;
    
     /*
      * Member Post ID.
      */
    public static $member_post_id;

    public function __construct() {

        add_action( 'init' , array( $this , 'prevent_header_already_sent_problem' ) , 1 ) ;

        add_action( 'init' , array( $this , 'sumomemberships_woocommerce_dependency_warning' ) ) ;

        $this->define_constants() ;
        include_once('include/sumo-memberships-privacy.php') ;
        if( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
            $this->include_files() ;

            add_action( 'plugins_loaded' , array( $this , 'set_language_to_translate' ) ) ;
            add_filter( 'woocommerce_screen_ids' , array( $this , 'display_enhanced_multiselect' ) ) ;
            add_action( 'admin_enqueue_scripts' , array( $this , 'enqueue_script' ) ) ;
            add_action( 'admin_head' , array( $this , 'display_datepicker' ) ) ;
            add_action( 'wp_enqueue_scripts' , array( $this , 'load_scripts' ) ) ;
            add_filter( 'woocommerce_locate_template' , array( $this , 'alter_template_path' ) , 10 , 3 ) ;
            
            $this->init_hooks() ;
        }
    }

    public function define_constants() {

        define( 'SUMO_MEMBERSHIPS_TEMPLATE_PATH' , plugin_dir_path( __FILE__ ) . 'templates/' ) ;
        define( 'SUMO_MEMBERSHIPS_PLUGIN_URL' , untrailingslashit( plugins_url( '/' , __FILE__ ) ) ) ;
        define( 'SUMO_MEMBERSHIPS_VERSION' , $this->version ) ;
    }

    public static function prevent_header_already_sent_problem() {
        ob_start() ;
    }

    public function include_files() {

        include 'include/class_membership_cpt.php' ;
        include 'include/class_admin_metaboxes.php' ;
        include 'include/membership_common_functions.php' ;
        include 'include/class_admin_menu_settings.php' ;
        include 'include/sumo-memberships-personal-data-handler.php' ;
        include 'include/class_membership_shortcodes.php' ;
        include 'include/class_membership_restrictions.php' ;
        include 'include/class_my_account_page.php' ;
        include 'include/class_subscription_compatibility.php' ;
        include 'include/class_send_email_when_transfer_mplan.php' ;
        include 'include/class_membership_apis.php' ;
    }

    public function sumomemberships_woocommerce_dependency_warning() {

        if( is_multisite() ) {
            // This Condition is for Multi Site WooCommerce Installation
            if( ! is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) && ( ! is_plugin_active( 'woocommerce/woocommerce.php' )) ) {
                if( is_admin() ) {
                    $variable = "<div class='error'><p> SUMO Memberships will not work until WooCommerce Plugin is Activated. Please Activate the WooCommerce Plugin. </p></div>" ;
                    echo $variable ;
                }
                return ;
            }
        } else {
            // This Condition is for Single Site WooCommerce Installation
            if( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
                if( is_admin() ) {
                    $variable = "<div class='error'><p> SUMO Memberships will not work until WooCommerce Plugin is Activated. Please Activate the WooCommerce Plugin. </p></div>" ;
                    echo $variable ;
                }
                return ;
            }
        }
    }

    public function set_language_to_translate() {
        load_plugin_textdomain( 'sumomemberships' , false , dirname( plugin_basename( __FILE__ ) ) . '/languages' ) ;
    }

    public function enqueue_script() {

        wp_enqueue_script( 'jquery' ) ;
        wp_enqueue_script( 'jquery-ui-datepicker' ) ;
        if( isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] == 'previous_order' ) {
            //register script
            wp_register_script( 'sumo_membership_previous_order_tab' , SUMO_MEMBERSHIPS_PLUGIN_URL . '/assets/js/tabs/sm-previous-orders-tab.js' , array( 'jquery' ) , SUMO_MEMBERSHIPS_VERSION ) ;
            //localize script
            wp_localize_script( 'sumo_membership_previous_order_tab' , 'sumo_membership_previous_order_tab_obj' , array(
                'sm_updated_count'       => __( "Orders found and provided Membership Access" , "sumomemberships" ) ,
                'sm_empty_order_message' => __( "No Orders found" , "sumomemberships" ) ,
                'sm_chunk_count'         => 5 ,
            ) ) ;
            //enqueue script
            wp_enqueue_script( 'sumo_membership_previous_order_tab' ) ;
        }
    }

    public function load_scripts() {
        $suffix      = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' ;
        wp_register_script( 'wc-enhanced-select' , WC()->plugin_url() . '/assets/js/admin/wc-enhanced-select' . $suffix . '.js' , array( 'jquery' , 'select2' ) , WC_VERSION ) ;
        wp_localize_script( 'wc-enhanced-select' , 'wc_enhanced_select_params' , array(
            'i18n_matches_1'            => _x( 'One result is available, press enter to select it.' , 'enhanced select' , 'woocommerce' ) ,
            'i18n_matches_n'            => _x( '%qty% results are available, use up and down arrow keys to navigate.' , 'enhanced select' , 'woocommerce' ) ,
            'i18n_no_matches'           => _x( 'No matches found' , 'enhanced select' , 'woocommerce' ) ,
            'i18n_ajax_error'           => _x( 'Loading failed' , 'enhanced select' , 'woocommerce' ) ,
            'i18n_input_too_short_1'    => _x( 'Please enter 1 or more characters' , 'enhanced select' , 'woocommerce' ) ,
            'i18n_input_too_short_n'    => _x( 'Please enter %qty% or more characters' , 'enhanced select' , 'woocommerce' ) ,
            'i18n_input_too_long_1'     => _x( 'Please delete 1 character' , 'enhanced select' , 'woocommerce' ) ,
            'i18n_input_too_long_n'     => _x( 'Please delete %qty% characters' , 'enhanced select' , 'woocommerce' ) ,
            'i18n_selection_too_long_1' => _x( 'You can only select 1 item' , 'enhanced select' , 'woocommerce' ) ,
            'i18n_selection_too_long_n' => _x( 'You can only select %qty% items' , 'enhanced select' , 'woocommerce' ) ,
            'i18n_load_more'            => _x( 'Loading more results&hellip;' , 'enhanced select' , 'woocommerce' ) ,
            'i18n_searching'            => _x( 'Searching&hellip;' , 'enhanced select' , 'woocommerce' ) ,
            'ajax_url'                  => admin_url( 'admin-ajax.php' ) ,
            'search_products_nonce'     => wp_create_nonce( 'search-products' ) ,
            'search_customers_nonce'    => wp_create_nonce( 'search-customers' )
        ) ) ;
        wp_enqueue_script( 'wc-enhanced-select' ) ;
        wp_enqueue_script( 'select2' ) ;
        $assets_path = str_replace( array( 'http:' , 'https:' ) , '' , WC()->plugin_url() ) . '/assets/' ;
        wp_enqueue_style( 'select2' , $assets_path . 'css/select2.css' ) ;
        
        wp_register_style( 'sumo-inline-style' , false , array() , 'sumomemberships' ) ; // phpcs:ignore
	wp_enqueue_style( 'sumo-inline-style' ) ;

	//add inline style
	$this->add_inline_style() ;
    }
    
    public function add_inline_style(){
        
        $contents = get_option('sumo_custom_css');
        if ( ! $contents ) {
	       return ;
	}
        //Add custom css as inline style.
	wp_add_inline_style( 'sumo-inline-style' , $contents ) ;
    }

    public function display_enhanced_multiselect( $screen_ids ) {

        global $my_admin_page ;

        $newscreenids = get_current_screen() ;
        $screen_ids[] = $newscreenids->id ;

        return $screen_ids ;
    }

    public function display_datepicker() {
        global $post ;

        if( isset( $post->ID ) ) {

            $post_id = $post->ID ;
            $obj     = get_post( $post_id ) ;

            if( isset( $obj->post_type ) && $obj->post_type == 'sumomembers' ) {
                ?>
                <script type="text/javascript">
                    jQuery( document ).ready( function() {
                        jQuery( ".sumomemberships_select_to_date" ).datepicker( {
                            minDate : 0 ,
                            dateFormat : "yy-mm-dd"
                        } ) ;
                    } ) ;
                </script>
                <?php

            }
        }
    }

    public function alter_template_path( $template , $template_name , $template_path ) {
        $plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/' ;

        if( file_exists( $plugin_path . $template_name ) ) {
            $template = $plugin_path . $template_name ;
            return $template ;
        }
        return $template ;
    }
    
    public function init_hooks(){
        register_deactivation_hook( __FILE__ , array( $this , 'flush_rules' ) ) ;
    }
    
    public function flush_rules(){
          // Update flush option for my memberships menu.  
          update_option( 'sumo_flush_rewrite_rules' , 1 ) ;
    }

}

new SUMOMemberships() ;
