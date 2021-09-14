<?php
/*
	Plugin Name: WooCommerce Easy Checkout Fields Editor
	Plugin URI: https://woomatrix.com
	Description: lets you Add/edit/delete checkout fields for woocoomerce. 
    Version: 2.4.1
	Author: WooMatrix
	Author URI: https://woomatrix.com
	Text Domain: pcfme
	Domain Path: /languages
	Requires at least: 3.3
    Tested up to: 5.7.2
    WC requires at least: 3.0.0
    WC tested up to: 5.4.1
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


 if( !defined( 'pcfme_PLUGIN_URL' ) )
define( 'pcfme_PLUGIN_URL', plugin_dir_url( __FILE__ ) );



load_plugin_textdomain( 'pcfme', false, basename( dirname(__FILE__) ).'/languages' );

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if (is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	
  include dirname( __FILE__ ) . '/include/pcmfe_core_functions.php';
  include dirname( __FILE__ ) . '/include/update_checkout_fields_class.php';
  include dirname( __FILE__ ) . '/include/add_order_meta_fields_class.php';
  include dirname( __FILE__ ) . '/include/manage_extrafield_class.php';
  include dirname( __FILE__ ) . '/include/add_fields_to_myaccount.php';
  include dirname( __FILE__ ) . '/include/admin/pcfme_admin_settings.php';

}


function pcfme_plugin_add_settings_link( $links ) {
    $settings_link1 = '<a href="https://woomatrix.com/knowledge-base/get-updates-for-woocommerce-easy-checkout-field-editor/">' . esc_html__( 'Enable dashboad updates','pcfme' ) . '</a>';
    
    array_push( $links, $settings_link1 );
    
  	return $links;
}
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'pcfme_plugin_add_settings_link' );



 
function pcfme_plugin_row_meta( $links, $file ) {    
    if ( plugin_basename( __FILE__ ) == $file ) {
        $row_meta = array(
          'docs'    => '<a href="' . esc_url( 'https://woomatrix.com/knowledge-base/category/woocommerce-easy-checkout-field-editor/' ) . '" target="_blank" aria-label="' . esc_html__( 'Docs', 'pcfme' ) . '" style="color:green;">' . esc_html__( 'Docs', 'pcfme' ) . '</a>',
          'support'    => '<a href="' . esc_url( 'https://woomatrix.com/support/' ) . '" target="_blank" aria-label="' . esc_html__( 'Support', 'pcfme' ) . '" style="color:green;">' . esc_html__( 'Support', 'pcfme' ) . '</a>'
        );

 
        return array_merge( $links, $row_meta );
    }
    return (array) $links;
}

add_filter( 'plugin_row_meta', 'pcfme_plugin_row_meta', 10, 2 );

register_activation_hook( __FILE__, 'pcfme_subscriber_check_activation_hook' );
function pcfme_subscriber_check_activation_hook() {
    set_transient( 'pcfme-admin-notice-activation', true, 5 );
}

add_action( 'admin_notices', 'pcfme_subscriber_check_activation_notice' );
function pcfme_subscriber_check_activation_notice(){
     if( get_transient( 'pcfme-admin-notice-activation' ) ){
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php echo esc_html__( 'Thanks for purchasing WooCommerce Easy Checkout Fields Editor.To enable dashboard updates ', 'pcfme' ); ?> <a href="https://woomatrix.com/knowledge-base/get-updates-for-woocommerce-easy-checkout-field-editor/"><?php echo esc_html__( 'Follow this', 'pcfme' ); ?></a>.</p>
        </div>
        <?php
        delete_transient( 'pcfme-admin-notice-activation' );
    }
}


?>