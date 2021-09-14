<?php

class SUMOHelp_Settings_Tab {

    public function __construct() {

        add_filter( 'woocommerce_sumomemberships_settings_tabs_array' , array ( $this , 'general_tab_setting' ) ) ; // Register a New Tab in a WooCommerce
        add_action( 'woocommerce_sumomemberships_settings_tabs_help' , array ( $this , 'register_admin_settings' ) ) ; // Call to register the admin settings in the Plugin Submenu with general settings tab
        add_action( 'woocommerce_admin_field_sumomemberships_documentation_content' , array ( $this , 'documentation_content' ) ) ;
    }

    /*
     * Function to Define Name of the Tab
     */

    public static function general_tab_setting( $setting_tabs ) {
        if ( ! is_array( $setting_tabs ) )
            $setting_tabs         = ( array ) $setting_tabs ;
        $setting_tabs[ 'help' ] = __( 'Help' , 'sumomemberships' ) ;
        return array_filter( $setting_tabs ) ;
    }

    public static function documentation_content() {
        ?>
        <style type="text/css">
            p.submit{
                display: none;
            }
            #mainforms{
                display: none;
            }
        </style>
        <?php

    }

    /*
     * Function label settings to Member Level Tab
     */

    public static function default_settings() {
        global $woocommerce ;

        return apply_filters( 'woocommerce_sumomemberships_help' , array (
            array (
                'name' => __( 'Documentation' , 'sumomemberships' ) ,
                'type' => 'title' ,
                'id'   => 'help_tab_setting' ,
                'desc' => __( 'The documentation file can be found inside the documentation folder  which you will find when you unzip the downloaded zip file.' , 'sumomemberships' ) ,
            ) ,
            array (
                'name' => __( 'Help' , 'sumosubscriptions' ) ,
                'type' => 'title' ,
                'id'   => '_sumo_memberships_help_setting' ,
                'desc' => __( 'If you need Help, please <a href="http://support.fantasticplugins.com" target="_blank" > register and open a support ticket</a>' , 'sumomemberships' ) ,
            ) ,
            array (
                'type' => 'sumomemberships_documentation_content' ,
            ) ,
            array ( 'type' => 'sectionend' , 'id' => 'help_tab_setting' ) ,
        ) ) ;
    }

    /**
     * Registering Custom Field Admin Settings
     */
    public static function register_admin_settings() {


        woocommerce_admin_fields( SUMOHelp_Settings_Tab::default_settings() ) ;
    }

    /**
     * Update the Settings on Save Changes
     */
    public static function advance_update_settings() {

        woocommerce_update_options( SUMOHelp_Settings_Tab::default_settings() ) ;
    }

}

new SUMOHelp_Settings_Tab() ;
