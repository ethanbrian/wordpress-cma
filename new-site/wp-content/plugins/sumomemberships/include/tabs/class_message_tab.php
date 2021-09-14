<?php

class SUMOMemberships_Message_Tab {

    public function __construct() {

        add_action( 'init' , array( $this , 'load_default_settings' ) , 103 ) ; // update the default settings on page load

        add_filter( 'woocommerce_sumomemberships_settings_tabs_array' , array( $this , 'general_tab_setting' ) ) ; // Register a New Tab in a WooCommerce

        add_action( 'woocommerce_sumomemberships_settings_tabs_sumomembership_message' , array( $this , 'register_admin_settings' ) ) ; // Call to register the admin settings in the Plugin Submenu with general settings tab

        add_action( 'woocommerce_update_options_sumomembership_message' , array( $this , 'advance_update_settings' ) ) ; // call the woocommerce_update_options_{slugname} to update the values
    }

    /*
     * Function to Define Name of the Tab
     */

    public static function general_tab_setting( $setting_tabs ) {
        if( ! is_array( $setting_tabs ) )
            $setting_tabs                             = ( array ) $setting_tabs ;
        $setting_tabs[ 'sumomembership_message' ] = __( 'Messages' , 'sumomemberships' ) ;
        return array_filter( $setting_tabs ) ;
    }

    /*
     * Function label settings to Member Level Tab
     */

    public static function default_settings() {
        global $woocommerce ;

        return apply_filters( 'woocommerce_sumomemberships_message' , array(
            array(
                'name' => __( 'Product Restriction Messages' , 'sumomemberships' ) ,
                'type' => 'title' ,
                'id'   => 'message_tab_setting_for_product'
            ) ,
            array(
                'name'    => __( 'Product Viewing Restriction - Requires Membership Purchase (for Logged In Users)' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:550px' ,
                'id'      => 'sumo_msg_for_site_users_product_view_restriction_for_membership_purchase' ,
                'newids'  => 'sumo_msg_for_site_users_product_view_restriction_for_membership_purchase' ,
                'class'   => 'sumo_msg_for_site_users_product_view_restriction_for_membership_purchase' ,
                'std'     => 'This Product has been limited only to Members. To View this Product, you will have to purchase [membership_product(s)].' ,
                'default' => 'This Product has been limited only to Members. To View this Product, you will have to purchase [membership_product(s)].'
            ) ,
            array(
                'name'    => __( 'Product Viewing Restriction - Requires Membership Purchase (for Guests)' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:550px' ,
                'id'      => 'sumo_msg_for_guests_product_view_restriction_for_membership_purchase' ,
                'newids'  => 'sumo_msg_for_guests_product_view_restriction_for_membership_purchase' ,
                'class'   => 'sumo_msg_for_guests_product_view_restriction_for_membership_purchase' ,
                'std'     => 'This Product has been limited only to Members. To View this Product, you will have to purchase [membership_product(s)]. If you are already a Member, login using the [login_link]' ,
                'default' => 'This Product has been limited only to Members. To View this Product, you will have to purchase [membership_product(s)]. If you are already a Member, login using the [login_link]'
            ) ,
            array(
                'name'    => __( 'Product Viewing Restriction - Requires Membership (for Logged In Users)' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:550px' ,
                'id'      => 'sumo_msg_for_site_users_product_view_restriction_for_membership' ,
                'newids'  => 'sumo_msg_for_site_users_product_view_restriction_for_membership' ,
                'class'   => 'sumo_msg_for_site_users_product_view_restriction_for_membership' ,
                'std'     => 'This Product has been limited only to Members.' ,
                'default' => 'This Product has been limited only to Members.'
            ) ,
            array(
                'name'    => __( 'Product Viewing Restriction - Requires Membership (for Guests)' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:550px' ,
                'id'      => 'sumo_msg_for_guests_product_view_restriction_for_membership' ,
                'newids'  => 'sumo_msg_for_guests_product_view_restriction_for_membership' ,
                'class'   => 'sumo_msg_for_guests_product_view_restriction_for_membership' ,
                'std'     => 'This Product has been limited only to Members. If you are already a Member, login using the [login_link]' ,
                'default' => 'This Product has been limited only to Members. If you are already a Member, login using the [login_link]'
            ) ,
            array(
                'name'    => __( 'Product Purchase Restriction - Requires Membership Purchase (for Logged In Users)' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:550px' ,
                'id'      => 'sumo_msg_for_site_users_product_purchase_restriction_for_membership_purchase' ,
                'newids'  => 'sumo_msg_for_site_users_product_purchase_restriction_for_membership_purchase' ,
                'class'   => 'sumo_msg_for_site_users_product_purchase_restriction_for_membership_purchase' ,
                'std'     => 'This Product has been limited only to Members. To Purchase this Product, you will have to purchase [membership_product(s)].' ,
                'default' => 'This Product has been limited only to Members. To Purchase this Product, you will have to purchase [membership_product(s)].'
            ) ,
            array(
                'name'    => __( 'Product Purchase Restriction - Requires Membership Purchase (for Guests)' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:550px' ,
                'id'      => 'sumo_msg_for_guests_product_purchase_restriction_for_membership_purchase' ,
                'newids'  => 'sumo_msg_for_guests_product_purchase_restriction_for_membership_purchase' ,
                'class'   => 'sumo_msg_for_guests_product_purchase_restriction_for_membership_purchase' ,
                'std'     => 'This Product has been limited only to Members. To Purchase this Product, you will have to purchase [membership_product(s)]. If you are already a Member login using the [login_link]' ,
                'default' => 'This Product has been limited only to Members. To Purchase this Product, you will have to purchase [membership_product(s)]. If you are already a Member login using the [login_link]'
            ) ,
            array(
                'name'    => __( 'Product Purchase Restriction - Requires Membership (for Logged In Users)' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:550px' ,
                'id'      => 'sumo_msg_for_site_users_product_purchase_restriction_for_membership' ,
                'newids'  => 'sumo_msg_for_site_users_product_purchase_restriction_for_membership' ,
                'class'   => 'sumo_msg_for_site_users_product_purchase_restriction_for_membership' ,
                'std'     => 'This Product has been limited only to Members. To Purchase this Product, you will have to purchase [membership_product(s)].' ,
                'default' => 'This Product has been limited only to Members. To Purchase this Product, you will have to purchase [membership_product(s)].'
            ) ,
            array(
                'name'    => __( 'Product Purchase Restriction - Requires Membership (for Guests)' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:550px' ,
                'id'      => 'sumo_msg_for_guests_product_purchase_restriction_for_membership' ,
                'newids'  => 'sumo_msg_for_guests_product_purchase_restriction_for_membership' ,
                'class'   => 'sumo_msg_for_guests_product_purchase_restriction_for_membership' ,
                'std'     => 'This Product can be purchased only by Members. If you already a Member, login using the [login_link]' ,
                'default' => 'This Product can be purchased only by Members. If you already a Member, login using the [login_link]'
            ) ,
            array(
                'name'    => __( 'Product Purchase Restriction' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:550px' ,
                'id'      => 'sumo_msg_product_restriction_for_membership' ,
                'newids'  => 'sumo_msg_product_restriction_for_membership' ,
                'class'   => 'sumo_msg_product_restriction_for_membership' ,
                'std'     => 'Sorry, This Product can not be purchased' ,
                'default' => 'Sorry, This Product can not be purchased'
            ) ,
            array( 'type' => 'sectionend' , 'id' => 'message_tab_setting_for_product' ) ,
            array(
                'name' => __( 'Content Restriction Messages' , 'sumomemberships' ) ,
                'type' => 'title' ,
                'id'   => 'message_tab_setting_for_content'
            ) ,
            array(
                'name'    => __( 'Content Restriction - Requires Membership Purchase (for Logged In Users)' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:550px' ,
                'id'      => 'sumo_msg_for_site_users_content_restriction_for_membership_purchase' ,
                'newids'  => 'sumo_msg_for_site_users_content_restriction_for_membership_purchase' ,
                'class'   => 'sumo_msg_for_site_users_content_restriction_for_membership_purchase' ,
                'std'     => 'This Content has been limited only to Members. To View this Content, you will have to purchase [membership_product(s)].' ,
                'default' => 'This Content has been limited only to Members. To View this Content, you will have to purchase [membership_product(s)].'
            ) ,
            array(
                'name'    => __( 'Content Restriction - Requires Membership Purchase (for Guests)' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:550px' ,
                'id'      => 'sumo_msg_for_guests_content_restriction_for_membership_purchase' ,
                'newids'  => 'sumo_msg_for_guests_content_restriction_for_membership_purchase' ,
                'class'   => 'sumo_msg_for_guests_content_restriction_for_membership_purchase' ,
                'std'     => 'This Content has been limited only to Members. To View this Content, you will have to purchase [membership_product(s)]. If you already a Member, login using the [login_link]' ,
                'default' => 'This Content has been limited only to Members. To View this Content, you will have to purchase [membership_product(s)]. If you already a Member, login using the [login_link]'
            ) ,
            array(
                'name'    => __( 'Content Restriction - Requires Membership (for Logged In Users)' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:550px' ,
                'id'      => 'sumo_msg_for_site_users_content_restriction_for_membership' ,
                'newids'  => 'sumo_msg_for_site_users_content_restriction_for_membership' ,
                'class'   => 'sumo_msg_for_site_users_content_restriction_for_membership' ,
                'std'     => 'This Content can be accessed only by Members.' ,
                'default' => 'This Content can be accessed only by Members.'
            ) ,
            array(
                'name'    => __( 'Content Restriction - Requires Membership (for Guests)' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:550px' ,
                'id'      => 'sumo_msg_for_guests_content_restriction_for_membership' ,
                'newids'  => 'sumo_msg_for_guests_content_restriction_for_membership' ,
                'class'   => 'sumo_msg_for_guests_content_restriction_for_membership' ,
                'std'     => 'This Content can be accessed only by Members. If you already a Member, login using the [login_link]' ,
                'default' => 'This Content can be accessed only by Members. If you already a Member, login using the [login_link]'
            ) ,
            array(
                'name'    => __( 'Content Restriction' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:550px' ,
                'id'      => 'sumo_msg_content_restriction_for_membership' ,
                'newids'  => 'sumo_msg_content_restriction_for_membership' ,
                'class'   => 'sumo_msg_content_restriction_for_membership' ,
                'std'     => 'Sorry, access to this Content is currently Restricted' ,
                'default' => 'Sorry, access to this Content is currently Restricted'
            ) ,
            array( 'type' => 'sectionend' , 'id' => 'message_tab_setting_for_content' ) ,
            array(
                'name' => __( 'User Sign Up Message' , 'sumomemberships' ) ,
                'type' => 'title' ,
                'id'   => 'message_tab_setting_for_user_signup'
            ) ,
            array(
                'name'    => __( 'Default Plans Information on User Sign Up' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:550px' ,
                'id'      => 'sumo_msg_for_default_plans_msg_on_user_signup' ,
                'newids'  => 'sumo_msg_for_default_plans_msg_on_user_signup' ,
                'class'   => 'sumo_msg_for_default_plans_msg_on_user_signup' ,
                'std'     => 'By Signing Up, you will receive [default_membership_plan(s)]' ,
                'default' => 'By Signing Up, you will receive [default_membership_plan(s)]'
            ) ,
            array( 'type' => 'sectionend' , 'id' => 'message_tab_setting_for_user_signup' ) ,
            array(
                'name' => __( 'Error Message' , 'sumomemberships' ) ,
                'type' => 'title' ,
                'id'   => 'message_tab_setting_for_error'
            ) ,
            array(
                'name'    => __( 'Guest Error Message' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:550px' ,
                'id'      => 'sumo_msg_for_guest_access' ,
                'newids'  => 'sumo_msg_for_guest_access' ,
                'class'   => 'sumo_msg_for_guest_access' ,
                'std'     => 'You Must Login to your Account to see your Membership plan(s)' ,
                'default' => 'You Must Login to your Account to see your Membership plan(s)'
            ) ,
            array( 'type' => 'sectionend' , 'id' => 'message_tab_setting_for_error' ) ,
            array(
                'name' => __( 'My Account Page Membership Customization Message' , 'sumomemberships' ) ,
                'type' => 'title' ,
                'id'   => 'message_for_myaccount_customization'
            ) ,
            array(
                'name'    => __( 'My Account Menu' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:550px' ,
                'id'      => 'sumo_localization_msg_for_menu' ,
                'newids'  => 'sumo_localization_msg_for_menu' ,
                'class'   => 'sumo_localization_msg_for_menu' ,
                'std'     => 'My Memberships' ,
                'default' => 'My Memberships'
            ) ,
            array(
                'name'    => __( 'My Membership Table Title' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:550px' ,
                'id'      => 'sumo_localization_msg_for_table_title' ,
                'newids'  => 'sumo_localization_msg_for_table_title' ,
                'class'   => 'sumo_localization_msg_for_table_title' ,
                'std'     => 'Memberships' ,
                'default' => 'Memberships'
            ) ,
            array(
                'name'    => __( 'Plan Name Column' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:550px' ,
                'id'      => 'sumo_localization_msg_for_planname' ,
                'newids'  => 'sumo_localization_msg_for_planname' ,
                'class'   => 'sumo_localization_msg_for_planname' ,
                'std'     => 'Plan Name' ,
                'default' => 'Plan Name'
            ) ,
            array(
                'name'    => __( 'Expiry Column' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:550px' ,
                'id'      => 'sumo_localization_msg_for_planduration' ,
                'newids'  => 'sumo_localization_msg_for_planduration' ,
                'class'   => 'sumo_localization_msg_for_planduration' ,
                'std'     => 'Expires On' ,
                'default' => 'Expires On'
            ) ,
            array(
                'name'    => __( 'Status Column' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:550px' ,
                'id'      => 'sumo_localization_msg_for_status' ,
                'newids'  => 'sumo_localization_msg_for_status' ,
                'class'   => 'sumo_localization_msg_for_status' ,
                'std'     => 'Status' ,
                'default' => 'Status'
            ) ,
            array(
                'name'    => __( 'Show/Hide Plan Name Column in My Membership Table' , 'sumomemberships' ) ,
                'type'    => 'select' ,
                'css'     => 'min-width:550px' ,
                'id'      => 'sumo_show_r_hide_plan_name' ,
                'newids'  => 'sumo_show_r_hide_plan_name' ,
                'class'   => 'sumo_show_r_hide_plan_name' ,
                'std'     => '1' ,
                'default' => '1' ,
                'options' => array(
                    '1' => __( 'Show' , 'sumomemberships' ) ,
                    '2' => __( 'Hide' , 'sumomemberships' ) ,
                ) ,
            ) ,
            array(
                'name'    => __( 'Show/Hide Expires On Column in My Membership Table' , 'sumomemberships' ) ,
                'type'    => 'select' ,
                'css'     => 'min-width:550px' ,
                'id'      => 'sumo_show_r_hide_plan_duration' ,
                'newids'  => 'sumo_show_r_hide_plan_duration' ,
                'class'   => 'sumo_show_r_hide_plan_duration' ,
                'std'     => '1' ,
                'default' => '1' ,
                'options' => array(
                    '1' => __( 'Show' , 'sumomemberships' ) ,
                    '2' => __( 'Hide' , 'sumomemberships' ) ,
                ) ,
            ) ,
            array(
                'name'    => __( 'Show/Hide Status Column in My Membership Table' , 'sumomemberships' ) ,
                'type'    => 'select' ,
                'css'     => 'min-width:550px' ,
                'id'      => 'sumo_show_r_hide_plan_status' ,
                'newids'  => 'sumo_show_r_hide_plan_status' ,
                'class'   => 'sumo_show_r_hide_plan_status' ,
                'std'     => '1' ,
                'default' => '1' ,
                'options' => array(
                    '1' => __( 'Show' , 'sumomemberships' ) ,
                    '2' => __( 'Hide' , 'sumomemberships' ) ,
                ) ,
            ) ,
            array( 'type' => 'sectionend' , 'id' => 'message_for_myaccount_customization' ) ,
                ) ) ;
    }

    /**
     * Registering Custom Field Admin Settings
     */
    public static function register_admin_settings() {


        woocommerce_admin_fields( SUMOMemberships_Message_Tab::default_settings() ) ;
    }

    /**
     * Update the Settings on Save Changes
     */
    public static function advance_update_settings() {

        woocommerce_update_options( SUMOMemberships_Message_Tab::default_settings() ) ;
    }

    /**
     * Initialize the Default Settings by looping this function
     */
    public static function load_default_settings() {
        global $woocommerce ;
        foreach( SUMOMemberships_Message_Tab::default_settings() as $setting )
            if( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
            }
    }

}

new SUMOMemberships_Message_Tab() ;

function sumomembership_message() {
    foreach( SUMOMemberships_Message_Tab::default_settings() as $setting ) {
        if( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
            delete_option( $setting[ 'newids' ] ) ;
            add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
        }
    }
}
