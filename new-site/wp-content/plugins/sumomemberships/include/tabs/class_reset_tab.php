<?php

class SUMOMemberships_Reset_Tab {

    public function __construct() {

        add_action( 'init' , array ( $this , 'load_default_settings' ) , 103 ) ; // update the default settings on page load

        add_filter( 'woocommerce_sumomemberships_settings_tabs_array' , array ( $this , 'general_tab_setting' ) ) ; // Register a New Tab in a WooCommerce

        add_action( 'woocommerce_sumomemberships_settings_tabs_reset' , array ( $this , 'register_admin_settings' ) ) ; // Call to register the admin settings in the Plugin Submenu with general settings tab

        add_action( 'woocommerce_update_options_reset' , array ( $this , 'coupon_system_update_settings' ) ) ; // call the woocommerce_update_options_{slugname} to update the coupon

        add_action( 'woocommerce_admin_field_sumomembership_reset_button' , array ( $this , 'sumomembership_reset_button' ) ) ;

        add_action( 'woocommerce_admin_field_sumomembership_reset_tabs' , array ( $this , 'sumomembership_reset_tabs' ) ) ;

        add_action( 'admin_head' , array ( $this , 'sumomembership_reset_data_for_user_through_ajax' ) ) ;

        add_action( 'wp_ajax_sumomembershipresetuserdata' , array ( $this , 'sumomembership_process_reset_user_data' ) ) ;
    }

    /*
     * Function to Define Name of the Tab
     */

    public static function general_tab_setting( $setting_tabs ) {
        if ( ! is_array( $setting_tabs ) )
            $setting_tabs          = ( array ) $setting_tabs ;
        $setting_tabs[ 'reset' ] = __( 'Reset' , 'sumomemberships' ) ;
        return array_filter( $setting_tabs ) ;
    }

    /*
     * Function label settings to Member Level Tab
     */

    public static function default_settings() {
        global $woocommerce ;

        return apply_filters( 'woocommerce_sumomemberships_reset' , array (
            array (
                'name' => __( 'Reset' , 'sumorewardcoupons' ) ,
                'type' => 'title' ,
                'id'   => 'sumomembership_reset_setting' ,
            ) ,
            array (
                'type' => 'sumomembership_reset_tabs' ,
            ) ,
            array (
                'name'   => __( 'Reset Master Log' , 'sumorewardcoupons' ) ,
                'type'   => 'checkbox' ,
                'id'     => 'sumomembership_reset_master_log' ,
                'newids' => 'sumomembership_reset_master_log' ,
                'class'  => 'sumomembership_reset_master_log' ,
                'std'    => 'no' ,
            ) ,
            array (
                'type' => 'sumomembership_reset_button' ,
            ) ,
            array ( 'type' => 'sectionend' , 'id' => 'sumomembership_reset_setting' ) ,
        ) ) ;
    }

    /**
     * Registering Custom Field Admin Settings
     */
    public static function register_admin_settings() {

        woocommerce_admin_fields( SUMOMemberships_Reset_Tab::default_settings() ) ;
    }

    /**
     * Update the Settings on Save Changes
     */
    public static function advance_update_settings() {

        woocommerce_update_options( SUMOMemberships_Reset_Tab::default_settings() ) ;
    }

    /**
     * Initialize the Default Settings by looping this function
     */
    public static function load_default_settings() {
        global $woocommerce ;
        foreach ( SUMOMemberships_Reset_Tab::default_settings() as $setting )
            if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
            }
    }

    public static function sumomembership_reset_tabs() {
        $tabs = '' ;
        $tabs = apply_filters( 'woocommerce_sumomemberships_settings_tabs_array' , $tabs ) ;
        foreach ( $tabs as $name => $label ) {
            if ( $label != 'Reset' && $label != 'Help' && $label != 'Advanced' ) {
                ?>
                <style type="text/css">
                    p.submit{
                        display: none;
                    }
                    #mainforms{
                        display: none;
                    }
                </style>
                <tr>
                    <th class="titledesc" scope="row">
                        <label for="sumomembership_reset_date_for_selected_user"> <?php _e( $label . ' Settings Tab' , 'sumorewardcoupons' ) ; ?></label>
                    </th>
                    <td>
                        <input type="checkbox" value="<?php echo $name ?>" id="sumomembership_reset_<?php echo $name ?>" class="sumomembership_reset_tabs" data-tab="<?php echo $name ?>"/>
                    </td>
                </tr>
                <?php
            }
        }
    }

    public static function sumomembership_reset_button() {
        ?>
        <tr valign="top">
            <td>
            </td>
            <td>
                <input type="submit" class="button-primary" name="sumomembership_reset_data_submit" id="sumomembership_reset_data_submit" value="Reset Data" />
                <img class="gif_sumomembership_button_for_reset" src="<?php echo WP_PLUGIN_URL ; ?>/sumomemberships/images/assets/loader.gif" style="width:32px;height:32px;position:absolute"/><br>
                <div class="sumomembership_reset_success_data">
                </div>
            </td>
        </tr>
        <?php
    }

    public static function sumomembership_reset_data_for_user_through_ajax() {
        ?>
        <script type="text/javascript">
            jQuery( function () {

                jQuery( '.gif_sumomembership_button_for_reset' ).css( 'display' , 'none' ) ;
                jQuery( '#sumomembership_reset_data_submit' ).click( function () {
                    if ( confirm( "Are You Sure ? Do You Want to Reset Your Data?" ) == true ) {
                        jQuery( '.gif_sumomembership_button_for_reset' ).css( 'display' , 'inline-block' ) ;
                        var resetmasterlogs = jQuery( '#sumomembership_reset_master_log' ).filter( ":checked" ).val() ;
                        var newarray = new Array() ;
                        jQuery( '.sumomembership_reset_tabs' ).each( function () {
                            var checkbox_value = jQuery( this ).is( ':checked' ) ;
                            if ( checkbox_value ) {
                                newarray.push( jQuery( this ).attr( 'data-tab' ) ) ;
                            }

                        } ) ;
                        var dataparam = ( {
                            action : 'sumomembershipresetuserdata' ,
                            sumomembershipresetmasterlogs : resetmasterlogs ,
                            sumomembershipresettab : newarray
                        } ) ;

                        jQuery.post( "<?php echo admin_url( 'admin-ajax.php' ) ; ?>" , dataparam ,
                                function ( response ) {
                                    console.log( response )
                                    if ( response != 'success' ) {
                                        jQuery( '.gif_sumomembership_button_for_reset' ).css( 'display' , 'none' ) ;
                                        jQuery( '.sumomembership_reset_success_data' ).fadeIn() ;
                                        jQuery( '.sumomembership_reset_success_data' ).html( "Data's Resetted Successfully" ) ;
                                        jQuery( '.sumomembership_reset_success_data' ).fadeOut( 5000 ) ;
                                        location.reload( true ) ;

                                    }
                                } , 'json' ) ;
                        return false ;
                    } else {
                        return false ;
                    }

                } ) ;
            } ) ;
        </script>
        <?php
    }

    public static function sumomembership_process_reset_user_data() {

        if ( isset( $_POST ) ) {
            $resetmasterlog = isset( $_POST[ 'sumomembershipresetmasterlogs' ] ) ? $_POST[ 'sumomembershipresetmasterlogs' ] : '' ;
            $resettabs      = isset( $_POST[ 'sumomembershipresettab' ] ) ? $_POST[ 'sumomembershipresettab' ] : array () ;
            if ( ! empty( $resettabs ) ) {
                foreach ( $resettabs as $tabs ) {
                    $tabs() ;
                }
            }
            if ( $resetmasterlog == ('1' || 'yes') ) {
                $my_post = array (
                    'post_status'   => 'publish' ,
                    'post_type'     => 'sumomem_masterlog' ,
                    'numberposts'   => '-1' ,
                    'fields'        => 'ids' ,
                    'cache_results' => false
                ) ;
                $getpost = get_posts( $my_post ) ;
                foreach ( $getpost as $id ) {
                    wp_delete_post( $id ) ;
                }
            }
        }
        exit() ;
    }

}

new SUMOMemberships_Reset_Tab() ;
