<?php

class SUMOMemberships_Members_Tab {

    public function __construct() {

        add_filter( 'woocommerce_sumomemberships_settings_tabs_array' , array ( $this , 'general_tab_setting' ) ) ; // Register a New Tab in a WooCommerce
        add_action( 'woocommerce_sumomemberships_settings_tabs_members_tab' , array ( $this , 'register_admin_settings' ) ) ; // Call to register the admin settings in the Plugin Submenu with general settings tab
    }

    /*
     * Function to Define Name of the Tab
     */

    public static function general_tab_setting( $setting_tabs ) {
        if ( ! is_array( $setting_tabs ) )
            $setting_tabs                = ( array ) $setting_tabs ;
        $setting_tabs[ 'members_tab' ] = __( 'Members' , 'sumomemberships' ) ;
        return array_filter( $setting_tabs ) ;
    }

    /*
     * Function label settings to Member Level Tab
     */

    public static function default_settings() {
        global $woocommerce ;

        return apply_filters( 'woocommerce_sumomemberships_members_tab' , array (
            array (
                'type' => 'title' ,
                'id'   => 'members_tab_setting'
            ) ,
            array ( 'type' => 'sectionend' , 'id' => 'members_tab_setting' ) ,
        ) ) ;
    }

    /**
     * Registering Custom Field Admin Settings
     */
    public static function register_admin_settings() {


        woocommerce_admin_fields( SUMOMemberships_Members_Tab::default_settings() ) ;
    }

    /**
     * Update the Settings on Save Changes
     */
    public static function advance_update_settings() {

        woocommerce_update_options( SUMOMemberships_Members_Tab::default_settings() ) ;
    }

}

new SUMOMemberships_Members_Tab() ;
