<?php

class SUMOAdvanced_Settings_Tab {

    public function __construct() {

        add_action( 'init' , array( $this , 'load_default_settings' ) , 103 ) ; // update the default settings on page load

        add_filter( 'woocommerce_sumomemberships_settings_tabs_array' , array( $this , 'general_tab_setting' ) ) ; // Register a New Tab in a WooCommerce

        add_action( 'woocommerce_sumomemberships_settings_tabs_sumomembership_advanced_settings' , array( $this , 'register_admin_settings' ) ) ; // Call to register the admin settings in the Plugin Submenu with advanced settings tab

        add_action( 'woocommerce_update_options_sumomembership_advanced_settings' , array( $this , 'advance_update_settings' ) ) ; // call the woocommerce_update_options_{slugname} to update the values

        add_action( 'woocommerce_admin_field_sumo_display_active_third_parties_cpt' , array( $this , 'display_active_third_parties_cpt' ) ) ;

        add_action( 'woocommerce_admin_field_sumo_display_experimental_settings' , array( $this , 'sumo_display_experimental_settings' ) ) ;
    }

    /*
     * Function to Define Name of the Tab
     */

    public static function general_tab_setting( $setting_tabs ) {
        if( ! is_array( $setting_tabs ) )
            $setting_tabs                                       = ( array ) $setting_tabs ;
        $setting_tabs[ 'sumomembership_advanced_settings' ] = __( 'Advanced' , 'sumomemberships' ) ;
        return array_filter( $setting_tabs ) ;
    }

    /*
     * Function label settings to Member Level Tab
     */

    public static function default_settings() {
        global $woocommerce ;

        return apply_filters( 'woocommerce_sumomemberships_advanced_settings' , array(
            array(
                'name' => __( 'Advanced Settings' , 'sumomemberships' ) ,
                'type' => 'title' ,
                'id'   => 'advanced_settings'
            ) ,
            array(
                'name' => __( 'Memberships Restrictions for Custom Post Type' , 'sumomemberships' ) ,
                'type' => 'title' ,
                'id'   => 'cpt_restriction_settings'
            ) ,
            array( 'type' => 'sumo_display_active_third_parties_cpt' ) ,
            array( 'type' => 'sectionend' , 'id' => 'cpt_restriction_settings' ) ,
            array(
                'name' => __( 'Experimental Settings' , 'sumomemberships' ) ,
                'type' => 'title' ,
                'id'   => 'cpt_experimental_settings'
            ) ,
            array( 'type' => 'sumo_display_experimental_settings' ) ,
            array( 'type' => 'sectionend' , 'id' => 'cpt_experimental_settings' ) ,
            array(
                'name' => __( 'Custom CSS Settings' , 'sumomemberships' ) ,
                'type' => 'title' ,
                'id'   => 'sumo_custom_css_setting'
            ) ,
            array(
                'name'    => __( 'Custom CSS' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'id'      => 'sumo_custom_css' ,
                'newids'  => 'sumo_custom_css' ,
                'class'   => 'sumo_custom_css' ,
            ) ,
            array( 'type' => 'sectionend' , 'id' => 'sumo_custom_css_setting' ) ,
            array( 'type' => 'sectionend' , 'id' => 'advanced_settings' ) ,
                ) ) ;
    }

    /**
     * Registering Custom Field Admin Settings
     */
    public static function register_admin_settings() {

        woocommerce_admin_fields( SUMOAdvanced_Settings_Tab::default_settings() ) ;
    }

    /**
     * Update the Settings on Save Changes
     */
    public static function advance_update_settings() {

        woocommerce_update_options( SUMOAdvanced_Settings_Tab::default_settings() ) ;

        if( isset( $_POST[ 'sumomemberships_enable_redirection_for_home_page' ] ) ) {
            update_option( "sumomemberships_enable_redirection_for_home_page" , "yes" ) ;
        } else {
            update_option( "sumomemberships_enable_redirection_for_home_page" , "no" ) ;
        }
                
        if( isset( $_POST[ 'sumomemberships_member_since_display_type_in_post_table' ] ) ) {
            update_option( "sumomemberships_member_since_display_type_in_post_table" , wc_clean(wp_unslash($_POST[ 'sumomemberships_member_since_display_type_in_post_table' ])) ) ;
        } else {
            update_option( "sumomemberships_member_since_display_type_in_post_table" , "" ) ;
        }
       
        $post_types = get_post_types() ;

        foreach( $post_types as $type ) {
            if( isset( $_POST[ "sumomemberships_$type" ] ) ) {
                update_option( "sumomemberships_$type" , "yes" ) ;
            } else {
                update_option( "sumomemberships_$type" , "" ) ;
            }
        }
    }

    /**
     * Initialize the Default Settings by looping this function
     */
    public static function load_default_settings() {
        global $woocommerce ;
        foreach( SUMOAdvanced_Settings_Tab::default_settings() as $setting )
            if( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
            }

        $post_types = sumo_get_third_parties_cpt_exists() ;

        foreach( $post_types as $type ) {
            add_option( "sumomemberships_$type" , "" ) ;
        }
    }

    public static function display_active_third_parties_cpt() {
        $post_types = sumo_get_third_parties_cpt_exists() ;
        ?>
        <table class="form-table sumo_display_active_third_parties_cpt">
            <?php
            foreach( $post_types as $type ) {
                ?>
                <tr>
                    <td>
                        <?php echo $type ; ?>
                    </td>
                    <td>
                        <input type="checkbox" id="sumomemberships_<?php echo $type ; ?>" name="sumomemberships_<?php echo $type ; ?>" 
                               value="1" <?php if( get_option( "sumomemberships_$type" ) == "yes" ) { ?> checked="checked" <?php } ?>>
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>
        <?php
    }

    public static function sumo_display_experimental_settings() {
        ?>
        <table class = "form-table sumo_display_experimental_settings">
            <tr>
                <td>
                    <?php esc_html_e( 'Enable Redirection for Home Page' , 'sumomemberships' ) ; ?>
                </td>
                <td>
                    <input type="checkbox" id="sumomemberships_enable_redirection_for_home_page" name="sumomemberships_enable_redirection_for_home_page" 
                           value="1" <?php if( get_option( "sumomemberships_enable_redirection_for_home_page" , 'off' ) == 'yes' ) { ?> checked="checked" <?php } ?>>
                </td>
            </tr>
            
            <tr>
                <td>
                    <?php esc_html_e( 'Time Display for Member Since column in Member post table' , 'sumomemberships' ) ; ?>
                </td>
                <td>
                    <?php $display_type = get_option('sumomemberships_member_since_display_type_in_post_table',1); ?>
                    <select id="sumomemberships_member_since_display_type_in_post_table"
                            name="sumomemberships_member_since_display_type_in_post_table">
                        <option value ="1" <?php  if("1" == $display_type) { ?> selected="selected" <?php } ?>><?php esc_html_e('UTC Time','sumomemberships'); ?></option>
                        <option value ="2" <?php if("2" == $display_type) { ?> selected="selected" <?php } ?>><?php esc_html_e('Local Time','sumomemberships'); ?></option>
                    </select>
                </td>
            </tr>
            <?php
            ?>
        </table>

        <?php
    }

}

new SUMOAdvanced_Settings_Tab() ;
