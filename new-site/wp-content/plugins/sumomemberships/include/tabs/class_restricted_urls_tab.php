<?php

class SUMOMemberships_Restricted_Urls_Tab {

    public function __construct() {

        add_action( 'init' , array( $this , 'load_default_settings' ) , 103 ) ; // update the default settings on page load

        add_filter( 'woocommerce_sumomemberships_settings_tabs_array' , array( $this , 'general_tab_setting' ) ) ; // Register a New Tab in a WooCommerce

        add_action( 'woocommerce_sumomemberships_settings_tabs_sumomembership_restricted_urls' , array( $this , 'register_admin_settings' ) ) ; // Call to register the admin settings in the Plugin Submenu with general settings tab

        add_action( 'woocommerce_update_options_sumomembership_restricted_urls' , array( $this , 'advance_update_settings' ) ) ; // call the woocommerce_update_options_{slugname} to update the values

        add_action( 'woocommerce_admin_field_sumo_restricted_url_table' , array( $this , 'restricted_url_table' ) ) ;

        add_action( 'wp_ajax_sumo_delete_current_restricted_url_table_row' , array( $this , 'delete_current_table_row' ) ) ;

        add_action( 'wp_ajax_sumo_update_restricted_url_table_row_count_on_click' , array( $this , 'update_table_row_count_on_click' ) ) ;
    }

    /*
     * Function to Define Name of the Tab
     */

    public static function general_tab_setting( $setting_tabs ) {
        if( ! is_array( $setting_tabs ) )
            $setting_tabs                                     = ( array ) $setting_tabs ;
        $setting_tabs[ 'sumomembership_restricted_urls' ] = __( "URL Restriction by Redirection" , 'sumomemberships' ) ;
        return array_filter( $setting_tabs ) ;
    }

    /*
     * Function label settings to Member Level Tab
     */

    public static function default_settings() {
        global $woocommerce ;

        return apply_filters( 'woocommerce_sumomemberships_restricted_urls' , array(
            array(
                'name' => __( "Url Restriction Settings" , 'sumomemberships' ) ,
                'type' => 'title' ,
                'id'   => 'restricted_urls_setting'
            ) ,
            array(
                'type' => 'sumo_restricted_url_table'
            ) ,
            array( 'type' => 'sectionend' , 'id' => 'restricted_urls_setting' ) ,
                ) ) ;
    }

    public function restricted_url_table() {
        $membership_levels = sumo_get_membership_levels() ;
        ?>
        <table class="widefat wc_input_table sumo_restricted_url_table" style="border-spacing: 5px;" id="sumo_restricted_url_table">
            <thead>
                <tr>
                    <th class="sort">&nbsp;</th>
                    <th><?php _e( 'URL' , 'sumomemberships' ) ; ?></th>
                    <th><?php _e( 'Redirect For' , 'sumomemberships' ) ; ?></th>
                    <th><?php _e( 'Redirect To' , 'sumomemberships' ) ; ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if( ! $this->is_table_row_saved() ) {
                    delete_option( 'sumomemberships_restricted_urls_row_count' ) ;
                }

                $row_count = get_option( 'sumomemberships_restricted_urls_row_count' ) > 0 ? get_option( 'sumomemberships_restricted_urls_row_count' ) : 0 ;

                if( $row_count > 0 ) {

                    for( $i = 1 ; $i <= $row_count ; $i ++ ) {

                        if( $this->is_table_row_saved( $i ) ) {
                            ?>
                            <tr>
                                <td></td>
                                <td style="width:50%">
                                    <input type="text" placeholder="Type an URL" value="<?php echo get_option( 'sumomemberships_url_to_restrict' . $i ) ; ?>" style="width:100%" name="sumomemberships_url_to_restrict<?php echo $i ; ?>" id="sumomemberships_url_to_restrict<?php echo $i ; ?>">
                                </td>
                                <td>
                                    <select name="sumomemberships_restrictions_type<?php echo $i ; ?>" id="sumomemberships_restrictions_type<?php echo $i ; ?>">
                                        <option value="all_users"
                                                <?php if( get_option( 'sumomemberships_restrictions_type' . $i ) == 'all_users' ) { ?> selected="selected" <?php } ?>>
                                                    <?php _e( 'All Users' , 'sumomemberships' ) ; ?>
                                        </option>
                                        <option value="with_particular_plans"
                                                <?php if( get_option( 'sumomemberships_restrictions_type' . $i ) == 'with_particular_plans' ) { ?> selected="selected" <?php } ?>>
                                                    <?php _e( 'Members with Particular Plans' , 'sumomemberships' ) ; ?>
                                        </option>
                                        <option value="without_particular_plans"
                                                <?php if( get_option( 'sumomemberships_restrictions_type' . $i ) == 'without_particular_plans' ) { ?> selected="selected" <?php } ?>>
                                                    <?php _e( 'Users without Particular Plans' , 'sumomemberships' ) ; ?>
                                        </option>
                                        <option value="all_members"
                                                <?php if( get_option( 'sumomemberships_restrictions_type' . $i ) == 'all_members' ) { ?> selected="selected" <?php } ?>>
                                                    <?php _e( 'All Members' , 'sumomemberships' ) ; ?>
                                        </option>
                                        <option value="all_non_members"
                                                <?php if( get_option( 'sumomemberships_restrictions_type' . $i ) == 'all_non_members' ) { ?> selected="selected" <?php } ?>>
                                                    <?php _e( 'All Non Members' , 'sumomemberships' ) ; ?>
                                        </option>
                                    </select>
                                    <br>
                                    <select name="sumomemberships_with_r_without_access_plan_level<?php echo $i ; ?>" id="sumomemberships_with_r_without_access_plan_level<?php echo $i ; ?>">
                                        <?php foreach( $membership_levels as $each_level_key => $each_level_value ) { ?>
                                            <option value="<?php echo $each_level_key ; ?>" <?php if( get_option( 'sumomemberships_with_r_without_access_plan_level' . $i ) == $each_level_key ) { ?> selected="selected" <?php } ?>>
                                                <?php echo $each_level_value ; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </td>
                                <td style="width:48%">
                                    <input type="text" placeholder="Type an URL" value="<?php echo get_option( 'sumomemberships_redirect_to' . $i ) ; ?>" style="width:100%" name="sumomemberships_redirect_to<?php echo $i ; ?>" id="sumomemberships_redirect_to<?php echo $i ; ?>">
                                </td>
                                <td style="width:3%">
                                    <span class="sumo_delete_row" data-rowid="<?php echo $i ; ?>">&#10006;</span>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5"><input type="button" class="button-primary" id="add_restriction_button" name="add_restriction_button" value="Add Restriction"></th>
                </tr>
            </tfoot>
        </table>

        <style>
            .sumo_delete_row{
                background: red;
                background-repeat: no-repeat;
                border-radius: 100%;
                cursor:pointer;
                margin-top: 5px;
                margin-left: 3px;
                padding-left: 5px;
                padding-right: 5px;
                position:absolute;
            }
        </style>

        <script type="text/javascript">

            jQuery( document ).ready( function() {

                function show_r_hide_plan_levels( i ) {

                    if( jQuery( '#sumomemberships_restrictions_type' + i ).val() == "with_particular_plans" || jQuery( '#sumomemberships_restrictions_type' + i ).val() == "without_particular_plans" ) {
                        jQuery( '#sumomemberships_with_r_without_access_plan_level' + i ).show() ;
                    } else {
                        jQuery( '#sumomemberships_with_r_without_access_plan_level' + i ).hide() ;
                    }

                    jQuery( '#sumomemberships_restrictions_type' + i ).change( function() {
                        if( this.value == "with_particular_plans" || this.value == "without_particular_plans" ) {
                            jQuery( '#sumomemberships_with_r_without_access_plan_level' + i ).show() ;
                        } else {
                            jQuery( '#sumomemberships_with_r_without_access_plan_level' + i ).hide() ;
                        }
                    } ) ;
                }

                if( !jQuery( ".sumo_delete_row" ).is( ":visible" ) ) {

                    var addrow = jQuery(
                            '<tr>\n\
                               <td></td>\n\
                               <td class="no_restricted_urls" style="width:20%;">No URL\'s Resctricted.</td>\n\
                            </tr>' ) ;
                    jQuery( 'table.sumo_restricted_url_table' ).append( addrow ) ;

                }

                var row_count = "<?php echo get_option( 'sumomemberships_restricted_urls_row_count' ) > 0 ? get_option( 'sumomemberships_restricted_urls_row_count' ) : 0 ; ?>" ;

                for( var k = 1 ; k <= row_count ; k++ ) {

                    show_r_hide_plan_levels( k ) ;
                }

                jQuery( '#add_restriction_button' ).click( function( event ) {

                    event.preventDefault() ;

                    jQuery( ".no_restricted_urls" ).hide() ;

                    row_count++ ;

                    var data = {
                        action : 'sumo_update_restricted_url_table_row_count_on_click' ,
                        row_count : row_count
                    } ;

                    jQuery.post( "<?php echo admin_url( 'admin-ajax.php' ) ; ?>" , data ,
                            function( response ) {

                                console.log( response ) ;

                                if( response.trim() == 'success' ) {

                                    var newRow = jQuery( "<tr>\n\
                                            <td></td>\n\
                                            <td style='width:50%'>\n\
                                                <input type='text' placeholder='Type an URL' value='' name='sumomemberships_url_to_restrict" + row_count + "' style='width:100%' id='sumomemberships_url_to_restrict" + row_count + "'/>\n\
                                            </td>\n\
                                            <td>\n\
                                                <select name='sumomemberships_restrictions_type" + row_count + "' id='sumomemberships_restrictions_type" + row_count + "'>\n\
                                                    <option value='all_users'>All Users</option>\n\
                                                    <option value='with_particular_plans'>Members with Particular Plans</option>\n\
                                                    <option value='without_particular_plans'>Users without Particular Plans</option>\n\
                                                    <option value='all_members'>All Members</option>\n\
                                                    <option value='all_non_members'>All Non Members</option>\n\
                                                </select>\n\
                                                <br>\n\
                                                <select name='sumomemberships_with_r_without_access_plan_level" + row_count + "' id='sumomemberships_with_r_without_access_plan_level" + row_count + "'>\n\
        <?php foreach( $membership_levels as $each_level_key => $each_level_value ) { ?>\n\
                                                            <option value='<?php echo $each_level_key ; ?>'><?php echo $each_level_value ; ?></option>\n\
        <?php } ?>\n\
                                                </select>\n\
                                            </td>\n\
                                            <td style='width:48%'>\n\
                                                <input type='text' placeholder='Type an URL' value='' name='sumomemberships_redirect_to" + row_count + "' style='width:100%' id='sumomemberships_redirect_to" + row_count + "'/>\n\
                                            </td>\n\
                                            <td style='width:3%'>\n\
                                                <span class='sumo_delete_row' data-rowid='" + row_count + "'>&#10006;</span>\n\
                                            </td>\n\
                                        </tr>" ) ;
                                    jQuery( 'table.sumo_restricted_url_table' ).append( newRow ) ;

                                    show_r_hide_plan_levels( row_count ) ;

                                    jQuery( ".wc_input_table .sumo_delete_row" ).click( function() {

                                        jQuery( this ).parents( 'tr:first' ).css( 'display' , 'none' ) ;

                                        if( !jQuery( ".sumo_delete_row" ).is( ":visible" ) ) {

                                            location.reload() ;
                                        }
                                    } ) ;
                                }
                            } ) ;
                } ) ;

                jQuery( ".wc_input_table .sumo_delete_row" ).click( function() {

                    var rowid = jQuery( this ).data( 'rowid' ) ;

                    jQuery( this ).parents( 'tr:first' ).css( 'display' , 'none' ) ;

                    var data = {
                        action : 'sumo_delete_current_restricted_url_table_row' ,
                        rowid : rowid
                    } ;

                    jQuery.post( "<?php echo admin_url( 'admin-ajax.php' ) ; ?>" , data ,
                            function( response , status ) {

                                console.log( status ) ;

                                if( status == 'success' ) {

                                    location.reload() ;

                                }
                            }
                    ) ;
                } ) ;

                jQuery( "#mainform" ).submit( function() {

                    //                    if (jQuery(".no_restricted_urls").is(":visible")) {
                    //                        alert("Please Add Restriction and Try Again.");
                    //                        return false;
                    //                    }

                    for( var i = 1 ; i <= row_count ; i++ ) {

                        var is_field_visible = jQuery( "#sumomemberships_url_to_restrict" + i ).is( ":visible" ) ;
                        var url_to_restrict = jQuery( "#sumomemberships_url_to_restrict" + i ).val() ;
                        var redirect_url = jQuery( "#sumomemberships_redirect_to" + i ).val() ;

                        if( is_field_visible && ( url_to_restrict == "" || redirect_url == "" ) ) {
                            alert( 'Please enter the URL. And Try Again.' ) ;
                            return false ;
                        }

                        if( is_field_visible && ( !isUrlValid( url_to_restrict ) || !isUrlValid( redirect_url ) ) ) {
                            alert( 'Please enter the valid URL. And Try Again.' ) ;
                            return false ;
                        }
                    }
                } ) ;

                function isUrlValid( url ) {

                    if( /^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,10}(:[0-9]{1,5})?(\/.*)?$/i.test( url ) ) {
                        return true ;
                    } else {
                        return false ;
                    }
                }
            } ) ;
        </script>
        <?php
    }

    public function is_table_row_saved( $this_row = '' ) {

        if( $this_row != '' ) {

            if( get_option( 'sumomemberships_url_to_restrict' . $this_row ) != '' ) {
                return true ;
            }
            return false ;
        }

        $row_count = get_option( 'sumomemberships_restricted_urls_row_count' ) ;

        for( $i = 1 ; $i <= $row_count ; $i ++ ) {

            if( get_option( 'sumomemberships_url_to_restrict' . $i ) != '' ) {
                return true ;
            }
        }
        return false ;
    }

    public static function update_table_row_count_on_click() {

        if( isset( $_POST[ 'row_count' ] ) ) {

            update_option( 'sumomemberships_restricted_urls_row_count' , ( int ) $_POST[ 'row_count' ] ) ;

            echo 'success' ;
        }
        exit ;
    }

    public static function delete_current_table_row() {

        if( isset( $_POST[ 'rowid' ] ) ) {

            $i = $_POST[ 'rowid' ] ;

            delete_option( 'sumomemberships_url_to_restrict' . $i ) ;
            delete_option( 'sumomemberships_restrictions_type' . $i ) ;
            delete_option( 'sumomemberships_with_r_without_access_plan_level' . $i ) ;
            delete_option( 'sumomemberships_redirect_to' . $i ) ;
        }
        exit ;
    }

    /**
     * Registering Custom Field Admin Settings
     */
    public static function register_admin_settings() {

        woocommerce_admin_fields( SUMOMemberships_Restricted_Urls_Tab::default_settings() ) ;
    }

    /**
     * Update the Settings on Save Changes
     */
    public static function advance_update_settings() {

        woocommerce_update_options( SUMOMemberships_Restricted_Urls_Tab::default_settings() ) ;

        $row_count = get_option( 'sumomemberships_restricted_urls_row_count' ) ;

        for( $i = 1 ; $i <= $row_count ; $i ++ ) {

            if( isset( $_POST[ 'sumomemberships_url_to_restrict' . $i ] ) ) {

                if( $_POST[ 'sumomemberships_url_to_restrict' . $i ] != '' ) {

                    update_option( 'sumomemberships_url_to_restrict' . $i , $_POST[ 'sumomemberships_url_to_restrict' . $i ] ) ;
                    update_option( 'sumomemberships_restrictions_type' . $i , $_POST[ 'sumomemberships_restrictions_type' . $i ] ) ;

                    if( isset( $_POST[ 'sumomemberships_with_r_without_access_plan_level' . $i ] ) ) {
                        update_option( 'sumomemberships_with_r_without_access_plan_level' . $i , $_POST[ 'sumomemberships_with_r_without_access_plan_level' . $i ] ) ;
                    }
                    update_option( 'sumomemberships_redirect_to' . $i , $_POST[ 'sumomemberships_redirect_to' . $i ] ) ;
                }
            }
        }
    }

    /**
     * Initialize the Default Settings by looping this function
     */
    public static function load_default_settings() {
        global $woocommerce ;
        foreach( SUMOMemberships_Restricted_Urls_Tab::default_settings() as $setting )
            if( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
            }
    }

}

new SUMOMemberships_Restricted_Urls_Tab() ;

function sumomembership_restricted_urls() {
    foreach( SUMOMemberships_Restricted_Urls_Tab::default_settings() as $setting ) {
        if( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
            delete_option( $setting[ 'newids' ] ) ;
            add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
        }
    }
}
