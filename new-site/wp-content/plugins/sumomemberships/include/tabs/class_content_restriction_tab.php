<?php

class SUMOMemberships_Content_Restriction_Tab {

    public function __construct() {

        add_filter( 'woocommerce_sumomemberships_settings_tabs_array' , array ( $this , 'general_tab_setting' ) ) ; // Register a New Tab in a WooCommerce

        add_action( 'woocommerce_sumomemberships_settings_tabs_sumomembership_content_restriction' , array ( $this , 'register_admin_settings' ) ) ; // Call to register the admin settings in the Plugin Submenu with general settings tab

        add_action( 'woocommerce_admin_field_sumomemberships_content_restriction' , array ( $this , 'content_restriction' ) ) ;
    }

    /*
     * Function to Define Name of the Tab
     */

    public static function general_tab_setting( $setting_tabs ) {
        if ( ! is_array( $setting_tabs ) )
            $setting_tabs                                         = ( array ) $setting_tabs ;
        $setting_tabs[ 'sumomembership_content_restriction' ] = __( 'Content Restriction by Shortcodes' , 'sumomemberships' ) ;
        return array_filter( $setting_tabs ) ;
    }

    /*
     * Function label settings to Member Level Tab
     */

    public static function default_settings() {
        global $woocommerce ;

        return apply_filters( 'woocommerce_sumomemberships_content_restriction' , array (
            array (
                'type' => 'sumomemberships_content_restriction' ,
            ) ,
                ) ) ;
    }

    public static function content_restriction() {
        ?>
        <style type="text/css">
            p.submit{
                display: none;
            }
            #mainforms{
                display: none;
            }
        </style>
        <p>Content Sections of Pages and Posts can be restricted Using Shortcode Blocks.<br>

        <h2>Standard Format of a Shortcode Block</h2><br>

        <b>[membership plan ="plan_slug"]</b>  Restricted Content Goes Here  <b>[/membership]</b><br><br>

        Use <b>"allmembers"</b> & <b>"nonmembers" </b>as plan_slug for All Members & Non Members.<br><br>

        Contents within the Shortcode block will be Accessible only those Members who have the Specified Plan.<br><br>

        The "plan_slug" can be found in the <a href=edit.php?post_type=sumomembershipplans> Membership Plan Slug </a> Section
        <?php

    }

    /**
     * Registering Custom Field Admin Settings
     */
    public static function register_admin_settings() {

        woocommerce_admin_fields( SUMOMemberships_Content_Restriction_Tab::default_settings() ) ;
    }

}

new SUMOMemberships_Content_Restriction_Tab() ;

function sumomembership_content_restriction() {
    foreach ( SUMOMemberships_Content_Restriction_Tab::default_settings() as $setting ) {
        if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
            delete_option( $setting[ 'newids' ] ) ;
            add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
        }
    }
}
