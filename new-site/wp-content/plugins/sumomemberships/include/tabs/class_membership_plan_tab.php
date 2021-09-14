<?php

class SUMOMemberships_Membership_Plans_Tab {

    public function __construct() {

        add_filter( 'woocommerce_sumomemberships_settings_tabs_array' , array ( $this , 'general_tab_setting' ) ) ; // Register a New Tab in a WooCommerce
        add_action( 'woocommerce_sumomemberships_settings_tabs_sumomembership_membership_plan' , array ( $this , 'register_admin_settings' ) ) ; // Call to register the admin settings in the Plugin Submenu with general settings tab
    }

    /*
     * Function to Define Name of the Tab
     */

    public static function general_tab_setting( $setting_tabs ) {
        if ( ! is_array( $setting_tabs ) )
            $setting_tabs                                   = ( array ) $setting_tabs ;
        $setting_tabs[ 'sumomembership_membership_plan' ] = __( 'Membership Plans' , 'sumomemberships' ) ;
        return array_filter( $setting_tabs ) ;
    }

    /*
     * Function label settings to Member Level Tab
     */

    public static function default_settings() {
        global $woocommerce ;

        return apply_filters( 'woocommerce_sumomemberships_plan' , array (
            array (
                'type' => 'title' ,
                'id'   => 'membership_plan_setting'
            ) ,
            array ( 'type' => 'sectionend' , 'id' => 'membership_plan_setting' ) ,
        ) ) ;
    }

    /**
     * Registering Custom Field Admin Settings
     */
    public static function register_admin_settings() {


        woocommerce_admin_fields( SUMOMemberships_Membership_Plans_Tab::default_settings() ) ;
    }

    /**
     * Update the Settings on Save Changes
     */
    public static function advance_update_settings() {

        woocommerce_update_options( SUMOMemberships_Membership_Plans_Tab::default_settings() ) ;
    }

}

new SUMOMemberships_Membership_Plans_Tab() ;

function sumomembership_membership_plan() {
    foreach ( SUMOMemberships_Membership_Plans_Tab::default_settings() as $setting ) {
        if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
            delete_option( $setting[ 'newids' ] ) ;
            add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
        }
    }
}
