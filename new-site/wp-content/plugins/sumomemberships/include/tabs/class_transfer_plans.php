<?php

class SUMOTransferMemberPlans_Tab {

    public function __construct() {

        add_filter( 'woocommerce_sumomemberships_settings_tabs_array' , array ( $this , 'export_import_tab_setting' ) ) ; // Register a New Tab in a WooCommerce

        add_action( 'woocommerce_sumomemberships_settings_tabs_sumomembership_transfer_plans' , array ( $this , 'register_admin_settings' ) ) ; // Call to register the admin settings in the Plugin Submenu with general settings tab

        add_action( 'woocommerce_update_options_sumomembership_transfer_plans' , array ( $this , 'advance_update_settings' ) ) ; // call the woocommerce_update_options_{slugname} to update the values

        add_action( 'woocommerce_admin_field_sumo_plan_transfer_request' , array ( $this , 'sumo_plan_transfer_request_table' ) ) ;

        add_action( 'admin_head' , array ( $this , 'jQuery_function' ) ) ;

        add_action( 'wp_ajax_sumo_transfer_membership_plan_approve' , array ( $this , 'sumo_transfer_membership_plan_approve_action' ) ) ;

        add_action( 'wp_ajax_sumo_transfer_membership_plan_discard' , array ( $this , 'sumo_transfer_membership_plan_discard_action' ) ) ;
    }

    /*
     * Function to Define Name of the Tab
     */

    public static function jQuery_function() {
        if ( isset( $_GET[ 'tab' ] ) ) {
            if ( $_GET[ 'tab' ] == 'sumomembership_transfer_plans' ) {
                ?>
                <script type="text/javascript">
                    jQuery( document ).ready( function () {
                        jQuery( 'p.submit' ).hide() ;
                    } ) ;
                </script>
                <?php
            }
        }
    }

    public static function export_import_tab_setting( $setting_tabs ) {
        if ( ! is_array( $setting_tabs ) )
            $setting_tabs                                  = ( array ) $setting_tabs ;
        $setting_tabs[ 'sumomembership_transfer_plans' ] = __( 'Transfer Requests' , 'sumomemberships' ) ;
        return array_filter( $setting_tabs ) ;
    }

    /*
     * Function label settings to Member Level Tab
     */

    public static function default_settings() {
        return apply_filters( 'woocommerce_sumomemberships_transfer_plans' , array (
            array (
                'name' => __( 'Transfer Requests' , 'sumomemberships' ) ,
                'type' => 'title' ,
                'id'   => 'sumo_member_plans_transfer'
            ) ,
            array (
                'type' => 'sumo_plan_transfer_request'
            ) ,
            array (
                'type' => 'sectionend' ,
                'id'   => 'sumo_member_plans_transfer'
            )
        ) ) ;
    }

    /**
     * Registering Custom Field Admin Settings
     */
    public static function register_admin_settings() {


        woocommerce_admin_fields( SUMOTransferMemberPlans_Tab::default_settings() ) ;
    }

    /**
     * Update the Settings on Save Changes
     */
    public static function advance_update_settings() {

        woocommerce_update_options( SUMOTransferMemberPlans_Tab::default_settings() ) ;
    }

    public static function sumo_plan_transfer_request_table() {
        ?>
        <table class="widefat fixed donationrule_rewards" cellspacing="0">
            <thead>
                <tr>
                    <th class="manage-column column-columnname" scope="col"><?php _e( 'From User' , 'sumodiscounts' ) ; ?></th>
                    <th class="manage-column column-columnname" scope="col"><?php _e( 'Plan' , 'sumodiscounts' ) ; ?></th>
                    <th class="manage-column column-columnname" scope="col"><?php _e( 'To User' , 'sumodiscounts' ) ; ?></th>
                    <th class="manage-column column-columnname" scope="col"><?php _e( 'Approve' , 'sumodiscounts' ) ; ?></th>
                    <th class="manage-column column-columnname" scope="col"><?php _e( 'Discard' , 'sumodiscounts' ) ; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $array          = ( array ) get_option( 'sumo_plan_transfer_requests' ) ;
                $filtered_array = array_filter( $array ) ;
                if ( ! empty( $filtered_array ) ) {
                    foreach ( $filtered_array as $delete_id => $each_array ) {
                        $post_id         = $each_array[ 'post_id' ] ;
                        $access_lu       = $each_array[ 'access_lu' ] ;
                        $getdata         = get_post_meta( $post_id , 'sumomemberships_saved_plans' , true ) ;
                        $current_user_id = get_post_meta( $post_id , 'sumomemberships_userid' , true ) ;
                        $user_id         = $each_array[ 'user_id' ] ;
                        $user            = get_userdata( $user_id ) ;
                        if ( get_userdata( $current_user_id ) ) {
                            $current_user = get_userdata( $current_user_id ) ;
                            ?>
                            <tr>
                                <td class="manage-column column-columnname" scope="col">
                                    <?php _e( $current_user->user_login . '<br>' . $current_user->user_email ) ?>
                                </td>
                                <td class="manage-column column-columnname" scope="col">
                                    <?php
                                    $plan_id      = ($getdata[ $each_array[ 'unique_id' ] ][ 'choose_plan' ]) ;
                                    _e( get_the_title( $plan_id ) ) ;
                                    ?>
                                </td>
                                <td class="manage-column column-columnname" scope="col">
                                    <?php _e( $user->user_login . '<br>' . $user->user_email ) ?>
                                </td>
                                <td class="manage-column column-columnname" scope="col">
                                    <input type="button" value="Approve" data-access_lu="<?php echo $access_lu ?>" data-delete_id="<?php echo $delete_id ?>" id="sumomemberships_transfer_confirm" data-user_id="<?php echo $user_id ?>" data-post_id="<?php echo $post_id ?>" data-uniqid='<?php echo $each_array[ 'unique_id' ] ; ?>' class="sumomemberships_transfer_confirm button-primary"/>
                                </td>
                                <td class="manage-column column-columnname" scope="col">
                                    <input type="button" value="Discard" data-delete_id="<?php echo $delete_id ?>" id="sumomemberships_transfer_discard" data-user_id="<?php echo $user_id ?>" data-post_id="<?php echo $post_id ?>" data-uniqid='<?php echo $each_array[ 'unique_id' ] ; ?>' class="sumomemberships_transfer_discard button-primary"/>
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
                    <th class="manage-column column-columnname" scope="col"><?php _e( 'From User' , 'sumodiscounts' ) ; ?></th>
                    <th class="manage-column column-columnname" scope="col"><?php _e( 'Plan' , 'sumodiscounts' ) ; ?></th>
                    <th class="manage-column column-columnname" scope="col"><?php _e( 'To User' , 'sumodiscounts' ) ; ?></th>
                    <th class="manage-column column-columnname" scope="col"><?php _e( 'Approve' , 'sumodiscounts' ) ; ?></th>
                    <th class="manage-column column-columnname" scope="col"><?php _e( 'Discard' , 'sumodiscounts' ) ; ?></th>
                </tr>
            </tfoot>
        </table>
        <script type="text/javascript">
            jQuery( function () {
                jQuery( '.sumomemberships_transfer_confirm' ).click( function () {
                    var uniqid = jQuery( this ).attr( 'data-uniqid' ) ;
                    var user_id = jQuery( this ).attr( 'data-user_id' ) ;
                    var post_id = jQuery( this ).attr( 'data-post_id' ) ;
                    var delete_id = jQuery( this ).attr( 'data-delete_id' ) ;
                    var access_lu = jQuery( this ).attr( 'data-access_lu' ) ;
                    var dataparam = ( {
                        action : 'sumo_transfer_membership_plan_approve' ,
                        uniqid : uniqid ,
                        userid : user_id ,
                        postid : post_id ,
                        access_lu : access_lu ,
                        deleteid : delete_id
                    } ) ;
                    jQuery.post( "<?php echo admin_url( 'admin-ajax.php' ) ; ?>" , dataparam , function ( response ) {
                        location.reload( true ) ;
                    } ) ;
                } ) ;
                jQuery( '.sumomemberships_transfer_discard' ).click( function () {
                    jQuery( this ).closest( 'tr' ).hide() ;
                    var delete_id = jQuery( this ).attr( 'data-delete_id' ) ;
                    var dataparam = ( {
                        action : 'sumo_transfer_membership_plan_discard' ,
                        deleteid : delete_id
                    } ) ;
                    jQuery.post( "<?php echo admin_url( 'admin-ajax.php' ) ; ?>" , dataparam , function ( response ) {
                        //                        location.reload(true);
                    } ) ;
                } ) ;
            } ) ;
        </script>
        <?php
    }

    public static function sumo_transfer_membership_plan_approve_action() {
        if ( isset( $_POST[ 'uniqid' ] ) && isset( $_POST[ 'postid' ] ) && isset( $_POST[ 'userid' ] ) && isset( $_POST[ 'deleteid' ] ) ) {
            $postid          = $_POST[ 'postid' ] ;
            $uniqid          = $_POST[ 'uniqid' ] ;
            $userid          = $_POST[ 'userid' ] ;
            $delete_id       = $_POST[ 'deleteid' ] ;
            $access_lu       = $_POST[ 'access_lu' ] ;
            $user_name       = get_userdata( $userid )->user_login ;
            $getdata         = get_post_meta( $postid , 'sumomemberships_saved_plans' , true ) ;
            $current_user_id = get_post_meta( $postid , 'sumomemberships_userid' , true ) ;
            $new_post_id     = sumo_get_member_post_id( $userid ) ;
            $plan_id         = $getdata[ $uniqid ][ 'choose_plan' ] ;
            $linked_users    = get_post_meta( $postid , 'sumo_linked_users_of_' . $plan_id , true ) ;
            if ( $new_post_id > 0 ) {
                if ( sumo_plan_is_already_had( $plan_id , $new_post_id ) ) {
                    $new_getdata = get_post_meta( $new_post_id , 'sumomemberships_saved_plans' , true ) ;
                    if ( is_array( $new_getdata ) && ! empty( $new_getdata ) ) {
                        foreach ( $new_getdata as $key => $new_eachdata ) {
                            $replace_array[ $key ] = $new_eachdata[ 'choose_plan' ] ;
                        }
                    }
                    if ( ! empty( $replace_array ) ) {
                        if ( in_array( $plan_id , $replace_array ) ) {
                            $search = array_search( $plan_id , $replace_array ) ;
                            unset( $new_getdata[ $search ] ) ;
                        }
                    }
                    $new_getdata[ $uniqid ] = $getdata[ $uniqid ] ;
                    do_action( 'sumomemberships_plan_status_changed' , $postid , $plan_id , $getdata[ $uniqid ][ 'choose_status' ] ) ;
                    update_post_meta( $new_post_id , 'sumomemberships_saved_plans' , $new_getdata ) ;
                    if ( $access_lu == 'yes' ) {
                        update_post_meta( $new_post_id , 'sumo_linked_users_of_' . $plan_id , $linked_users ) ;
                    }
                    update_post_meta( $postid , 'sumo_linked_users_of_' . $plan_id , array () ) ;
                }
            } else {
                $args        = array (
                    'post_title'     => $user_name ,
                    'post_type'      => "sumomembers" ,
                    'post_status'    => 'publish' ,
                    'posts_per_page' => -1
                ) ;
                $new_post_id = wp_insert_post( $args ) ;
                $firstuniqid = uniqid() ;
                $saved_plans = array (
                    $firstuniqid => $getdata[ $uniqid ]
                ) ;
                update_post_meta( $new_post_id , 'sumomemberships_userid' , $userid ) ;
                do_action( 'sumomemberships_plan_status_changed' , $new_post_id , $plan_id , $getdata[ $uniqid ][ 'choose_status' ] ) ;
                update_post_meta( $new_post_id , 'sumomemberships_saved_plans' , $saved_plans ) ;
                if ( $access_lu == 'yes' ) {
                    update_post_meta( $new_post_id , 'sumo_linked_users_of_' . $plan_id , $linked_users ) ;
                }
                update_post_meta( $postid , 'sumo_linked_users_of_' . $plan_id , array () ) ;
                add_post_meta( $new_post_id , 'sumomemberships_member_since_date' , time() ) ;
                do_action( 'sumomemberships_add_new_plan_upon_order_status' , $saved_plans , $plan_id , $firstuniqid , $new_post_id ) ;
            }
            $sender_user_id = get_post_meta( $postid , 'sumomemberships_userid' , true ) ;
            $sumo_email_for = 'sumo_ps_approved' ;
            $planname       = get_the_title( $plan_id ) ;
            $plan_receiver  = get_userdata( $userid )->user_email ;
            $to             = get_userdata( $sender_user_id )->user_email ;
            sumo_email_for_transfer_plans( $sumo_email_for , $to , $plan_receiver , $planname ) ;

            update_post_meta( $sender_user_id , $plan_id . 'plan_switched_to' , $userid ) ;
            if ( isset( $getdata[ $uniqid ] ) ) {
                do_action( 'sumomemberships_delete_plan' , $uniqid , $getdata , $postid ) ;
                do_action( 'sumomemberships_plan_status_changed' , $postid , $getdata[ $uniqid ][ 'choose_plan' ] , 'delete' ) ;
                unset( $getdata[ $uniqid ] ) ;
            }
            update_post_meta( $postid , 'sumomemberships_saved_plans' , $getdata ) ;
            $array = ( array ) get_option( 'sumo_plan_transfer_requests' ) ;
            unset( $array[ $delete_id ] ) ;
            update_option( 'sumo_plan_transfer_requests' , $array ) ;
            update_post_meta( $current_user_id , 'pending_status_' . $uniqid , 'no' ) ;
        }
        exit() ;
    }

    public static function sumo_transfer_membership_plan_discard_action() {
        if ( isset( $_POST[ 'deleteid' ] ) ) {
            $delete_id       = $_POST[ 'deleteid' ] ;
            $array           = ( array ) get_option( 'sumo_plan_transfer_requests' ) ;
            $post_id         = $array[ $delete_id ][ 'post_id' ] ;
            $current_user_id = get_post_meta( $post_id , 'sumomemberships_userid' , true ) ;
            $unique_id       = $array[ $delete_id ][ 'unique_id' ] ;
            $sumo_email_for  = 'sumo_ps_rejected' ;
            $getdata         = get_post_meta( $post_id , 'sumomemberships_saved_plans' , true ) ;
            $uniqid          = $array[ $delete_id ][ 'unique_id' ] ;
            $plan_id         = $getdata[ $uniqid ][ 'choose_plan' ] ;
            $to              = get_userdata( $current_user_id )->user_email ;
            $planname        = get_the_title( $plan_id ) ;
            $plan_receiver   = get_userdata( $array[ $delete_id ][ 'user_id' ] )->user_email ;
            sumo_email_for_transfer_plans( $sumo_email_for , $to , $plan_receiver , $planname ) ;
            update_post_meta( $current_user_id , 'pending_status_' . $unique_id , 'no' ) ;
            unset( $array[ $delete_id ] ) ;
            update_option( 'sumo_plan_transfer_requests' , $array ) ;
        }
    }

}

new SUMOTransferMemberPlans_Tab() ;
