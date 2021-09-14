<?php

class SUMOEmail_Settings_Tab {

    public function __construct() {

        add_action( 'init' , array ( $this , 'load_default_settings' ) ) ;

        add_filter( 'woocommerce_sumomemberships_settings_tabs_array' , array ( $this , 'general_tab_setting' ) ) ; // Register a New Tab in a WooCommerce

        add_action( 'woocommerce_sumomemberships_settings_tabs_sumomembership_email_settings' , array ( $this , 'register_admin_settings' ) ) ; // Call to register the admin settings in the Plugin Submenu with general settings tab

        add_action( 'woocommerce_update_options_sumomembership_email_settings' , array ( $this , 'advance_update_settings' ) ) ; // call the woocommerce_update_options_{slugname} to update the values

        add_action( 'sumomemberships_schedule_before_expiry' , array ( $this , 'expiry_reminder_email' ) , 10 , 3 ) ;

        add_action( 'sumo_memberships_process_plan_duration_validity' , array ( $this , 'send_mail_when_expired' ) , 10 , 2 ) ;

        add_action( 'admin_head' , array ( $this , 'sumo_jQuery_function' ) ) ;
    }

    public static function sumo_jQuery_function() {
        if ( isset( $_GET[ 'tab' ] ) ) {
            if ( $_GET[ 'tab' ] == 'sumomembership_email_settings' ) {
                ?>
                <script type="text/javascript">
                    jQuery( document ).ready( function () {
                        if ( jQuery( '#sumo_admin_member_transfer_request_submitted_email' ).prop( 'checked' ) ) {
                            jQuery( '#sumo_admin_member_transfer_request_submitted_email_subject' ).closest( 'tr' ).show() ;
                            jQuery( '#sumo_admin_member_transfer_request_submitted_email_message' ).closest( 'tr' ).show() ;
                        } else {
                            jQuery( '#sumo_admin_member_transfer_request_submitted_email_subject' ).closest( 'tr' ).hide() ;
                            jQuery( '#sumo_admin_member_transfer_request_submitted_email_message' ).closest( 'tr' ).hide() ;
                        }
                        jQuery( '#sumo_admin_member_transfer_request_submitted_email' ).change( function () {
                            if ( jQuery( this ).is( ':checked' ) ) {
                                jQuery( '#sumo_admin_member_transfer_request_submitted_email_subject' ).closest( 'tr' ).show() ;
                                jQuery( '#sumo_admin_member_transfer_request_submitted_email_message' ).closest( 'tr' ).show() ;
                            } else {

                                jQuery( '#sumo_admin_member_transfer_request_submitted_email_subject' ).closest( 'tr' ).hide() ;
                                jQuery( '#sumo_admin_member_transfer_request_submitted_email_message' ).closest( 'tr' ).hide() ;
                            }
                        } ) ;
                        if ( jQuery( '#sumo_plan_sender_member_transfer_request_submitted_email' ).prop( 'checked' ) ) {
                            jQuery( '#sumo_plan_sender_member_transfer_request_submitted_email_subject' ).closest( 'tr' ).show() ;
                            jQuery( '#sumo_plan_sender_member_transfer_request_submitted_email_message' ).closest( 'tr' ).show() ;
                        } else {
                            jQuery( '#sumo_plan_sender_member_transfer_request_submitted_email_subject' ).closest( 'tr' ).hide() ;
                            jQuery( '#sumo_plan_sender_member_transfer_request_submitted_email_message' ).closest( 'tr' ).hide() ;
                        }
                        jQuery( '#sumo_plan_sender_member_transfer_request_submitted_email' ).change( function () {
                            if ( jQuery( this ).is( ':checked' ) ) {
                                jQuery( '#sumo_plan_sender_member_transfer_request_submitted_email_subject' ).closest( 'tr' ).show() ;
                                jQuery( '#sumo_plan_sender_member_transfer_request_submitted_email_message' ).closest( 'tr' ).show() ;
                            } else {
                                jQuery( '#sumo_plan_sender_member_transfer_request_submitted_email_subject' ).closest( 'tr' ).hide() ;
                                jQuery( '#sumo_plan_sender_member_transfer_request_submitted_email_message' ).closest( 'tr' ).hide() ;
                            }
                        } ) ;
                        if ( jQuery( '#sumo_admin_member_transfer_request_approved_email' ).prop( 'checked' ) ) {
                            jQuery( '#sumo_admin_member_transfer_request_approved_email_subject' ).closest( 'tr' ).show() ;
                            jQuery( '#sumo_admin_member_transfer_request_approved_email_message' ).closest( 'tr' ).show() ;
                        } else {
                            jQuery( '#sumo_admin_member_transfer_request_approved_email_subject' ).closest( 'tr' ).hide() ;
                            jQuery( '#sumo_admin_member_transfer_request_approved_email_message' ).closest( 'tr' ).hide() ;
                        }
                        jQuery( '#sumo_admin_member_transfer_request_approved_email' ).change( function () {
                            if ( jQuery( this ).is( ':checked' ) ) {
                                jQuery( '#sumo_admin_member_transfer_request_approved_email_subject' ).closest( 'tr' ).show() ;
                                jQuery( '#sumo_admin_member_transfer_request_approved_email_message' ).closest( 'tr' ).show() ;
                            } else {
                                jQuery( '#sumo_admin_member_transfer_request_approved_email_subject' ).closest( 'tr' ).hide() ;
                                jQuery( '#sumo_admin_member_transfer_request_approved_email_message' ).closest( 'tr' ).hide() ;
                            }
                        } ) ;
                        if ( jQuery( '#sumo_plan_sender_member_transfer_request_approved_email' ).prop( 'checked' ) ) {
                            jQuery( '#sumo_plan_sender_member_transfer_request_approved_email_subject' ).closest( 'tr' ).show() ;
                            jQuery( '#sumo_plan_sender_member_transfer_request_approved_email_message' ).closest( 'tr' ).show() ;
                        } else {
                            jQuery( '#sumo_plan_sender_member_transfer_request_approved_email_subject' ).closest( 'tr' ).hide() ;
                            jQuery( '#sumo_plan_sender_member_transfer_request_approved_email_message' ).closest( 'tr' ).hide() ;
                        }
                        jQuery( '#sumo_plan_sender_member_transfer_request_approved_email' ).change( function () {
                            if ( jQuery( this ).is( ':checked' ) ) {
                                jQuery( '#sumo_plan_sender_member_transfer_request_approved_email_subject' ).closest( 'tr' ).show() ;
                                jQuery( '#sumo_plan_sender_member_transfer_request_approved_email_message' ).closest( 'tr' ).show() ;
                            } else {
                                jQuery( '#sumo_plan_sender_member_transfer_request_approved_email_subject' ).closest( 'tr' ).hide() ;
                                jQuery( '#sumo_plan_sender_member_transfer_request_approved_email_message' ).closest( 'tr' ).hide() ;
                            }
                        } ) ;

                        if ( jQuery( '#sumo_plan_receiver_member_transfer_request_approved_email' ).prop( 'checked' ) ) {
                            jQuery( '#sumo_plan_receiver_member_transfer_request_approved_email_subject' ).closest( 'tr' ).show() ;
                            jQuery( '#sumo_plan_receiver_member_transfer_request_approved_email_message' ).closest( 'tr' ).show() ;
                        } else {
                            jQuery( '#sumo_plan_receiver_member_transfer_request_approved_email_subject' ).closest( 'tr' ).hide() ;
                            jQuery( '#sumo_plan_receiver_member_transfer_request_approved_email_message' ).closest( 'tr' ).hide() ;
                        }
                        jQuery( '#sumo_plan_receiver_member_transfer_request_approved_email' ).change( function () {
                            if ( jQuery( this ).is( ':checked' ) ) {
                                jQuery( '#sumo_plan_receiver_member_transfer_request_approved_email_subject' ).closest( 'tr' ).show() ;
                                jQuery( '#sumo_plan_receiver_member_transfer_request_approved_email_message' ).closest( 'tr' ).show() ;
                            } else {
                                jQuery( '#sumo_plan_receiver_member_transfer_request_approved_email_subject' ).closest( 'tr' ).hide() ;
                                jQuery( '#sumo_plan_receiver_member_transfer_request_approved_email_message' ).closest( 'tr' ).hide() ;
                            }
                        } ) ;

                        if ( jQuery( '#sumo_admin_member_transfer_request_rejected_email' ).prop( 'checked' ) ) {
                            jQuery( '#sumo_admin_member_transfer_request_rejected_email_subject' ).closest( 'tr' ).show() ;
                            jQuery( '#sumo_admin_member_transfer_request_rejected_email_message' ).closest( 'tr' ).show() ;
                        } else {
                            jQuery( '#sumo_admin_member_transfer_request_rejected_email_subject' ).closest( 'tr' ).hide() ;
                            jQuery( '#sumo_admin_member_transfer_request_rejected_email_message' ).closest( 'tr' ).hide() ;
                        }
                        jQuery( '#sumo_admin_member_transfer_request_rejected_email' ).change( function () {
                            if ( jQuery( this ).is( ':checked' ) ) {
                                jQuery( '#sumo_admin_member_transfer_request_rejected_email_subject' ).closest( 'tr' ).show() ;
                                jQuery( '#sumo_admin_member_transfer_request_rejected_email_message' ).closest( 'tr' ).show() ;
                            } else {
                                jQuery( '#sumo_admin_member_transfer_request_rejected_email_subject' ).closest( 'tr' ).hide() ;
                                jQuery( '#sumo_admin_member_transfer_request_rejected_email_message' ).closest( 'tr' ).hide() ;
                            }
                        } ) ;

                        if ( jQuery( '#sumo_plan_receiver_member_transfer_request_rejected_email' ).prop( 'checked' ) ) {
                            jQuery( '#sumo_plan_receiver_member_transfer_request_rejected_email_subject' ).closest( 'tr' ).show() ;
                            jQuery( '#sumo_plan_receiver_member_transfer_request_rejected_email_message' ).closest( 'tr' ).show() ;
                        } else {
                            jQuery( '#sumo_plan_receiver_member_transfer_request_rejected_email_subject' ).closest( 'tr' ).hide() ;
                            jQuery( '#sumo_plan_receiver_member_transfer_request_rejected_email_message' ).closest( 'tr' ).hide() ;
                        }
                        jQuery( '#sumo_plan_receiver_member_transfer_request_rejected_email' ).change( function () {
                            if ( jQuery( this ).is( ':checked' ) ) {
                                jQuery( '#sumo_plan_receiver_member_transfer_request_rejected_email_subject' ).closest( 'tr' ).show() ;
                                jQuery( '#sumo_plan_receiver_member_transfer_request_rejected_email_message' ).closest( 'tr' ).show() ;
                            } else {
                                jQuery( '#sumo_plan_receiver_member_transfer_request_rejected_email_subject' ).closest( 'tr' ).hide() ;
                                jQuery( '#sumo_plan_receiver_member_transfer_request_rejected_email_message' ).closest( 'tr' ).hide() ;
                            }
                        } ) ;

                        if ( jQuery( '#sumo_admin_member_plan_transfered_manually_email' ).prop( 'checked' ) ) {
                            jQuery( '#sumo_admin_member_plan_transfered_manually_email_subject' ).closest( 'tr' ).show() ;
                            jQuery( '#sumo_admin_member_plan_transfered_manually_email_message' ).closest( 'tr' ).show() ;
                        } else {
                            jQuery( '#sumo_admin_member_plan_transfered_manually_email_subject' ).closest( 'tr' ).hide() ;
                            jQuery( '#sumo_admin_member_plan_transfered_manually_email_message' ).closest( 'tr' ).hide() ;
                        }
                        jQuery( '#sumo_admin_member_plan_transfered_manually_email' ).change( function () {
                            if ( jQuery( this ).is( ':checked' ) ) {
                                jQuery( '#sumo_admin_member_plan_transfered_manually_email_subject' ).closest( 'tr' ).show() ;
                                jQuery( '#sumo_admin_member_plan_transfered_manually_email_message' ).closest( 'tr' ).show() ;
                            } else {
                                jQuery( '#sumo_admin_member_plan_transfered_manually_email_subject' ).closest( 'tr' ).hide() ;
                                jQuery( '#sumo_admin_member_plan_transfered_manually_email_message' ).closest( 'tr' ).hide() ;
                            }
                        } ) ;
                        if ( jQuery( '#sumo_plan_sender_member_plan_transfered_manually_email' ).prop( 'checked' ) ) {
                            jQuery( '#sumo_plan_sender_member_plan_transfered_manually_email_subject' ).closest( 'tr' ).show() ;
                            jQuery( '#sumo_plan_sender_member_plan_transfered_manually_email_message' ).closest( 'tr' ).show() ;
                        } else {
                            jQuery( '#sumo_plan_sender_member_plan_transfered_manually_email_subject' ).closest( 'tr' ).hide() ;
                            jQuery( '#sumo_plan_sender_member_plan_transfered_manually_email_message' ).closest( 'tr' ).hide() ;
                        }
                        jQuery( '#sumo_plan_sender_member_plan_transfered_manually_email' ).change( function () {
                            if ( jQuery( this ).is( ':checked' ) ) {
                                jQuery( '#sumo_plan_sender_member_plan_transfered_manually_email_subject' ).closest( 'tr' ).show() ;
                                jQuery( '#sumo_plan_sender_member_plan_transfered_manually_email_message' ).closest( 'tr' ).show() ;
                            } else {
                                jQuery( '#sumo_plan_sender_member_plan_transfered_manually_email_subject' ).closest( 'tr' ).hide() ;
                                jQuery( '#sumo_plan_sender_member_plan_transfered_manually_email_message' ).closest( 'tr' ).hide() ;
                            }
                        } ) ;

                        if ( jQuery( '#sumo_plan_receiver_member_plan_transfered_manually_email' ).prop( 'checked' ) ) {
                            jQuery( '#sumo_plan_receiver_member_plan_transfered_manually_email_subject' ).closest( 'tr' ).show() ;
                            jQuery( '#sumo_plan_receiver_member_plan_transfered_manually_email_message' ).closest( 'tr' ).show() ;
                        } else {
                            jQuery( '#sumo_plan_receiver_member_plan_transfered_manually_email_subject' ).closest( 'tr' ).hide() ;
                            jQuery( '#sumo_plan_receiver_member_plan_transfered_manually_email_message' ).closest( 'tr' ).hide() ;
                        }
                        jQuery( '#sumo_plan_receiver_member_plan_transfered_manually_email' ).change( function () {
                            if ( jQuery( this ).is( ':checked' ) ) {
                                jQuery( '#sumo_plan_receiver_member_plan_transfered_manually_email_subject' ).closest( 'tr' ).show() ;
                                jQuery( '#sumo_plan_receiver_member_plan_transfered_manually_email_message' ).closest( 'tr' ).show() ;
                            } else {
                                jQuery( '#sumo_plan_receiver_member_plan_transfered_manually_email_subject' ).closest( 'tr' ).hide() ;
                                jQuery( '#sumo_plan_receiver_member_plan_transfered_manually_email_message' ).closest( 'tr' ).hide() ;
                            }
                        } ) ;
                    } ) ;
                </script>
                <?php

            }
        }
    }

    /*
     * Function to Define Name of the Tab
     */

    public static function general_tab_setting( $setting_tabs ) {
        if ( ! is_array( $setting_tabs ) )
            $setting_tabs                                  = ( array ) $setting_tabs ;
        $setting_tabs[ 'sumomembership_email_settings' ] = __( 'Email' , 'sumomemberships' ) ;
        return array_filter( $setting_tabs ) ;
    }

    /*
     * Function label settings to Member Level Tab
     */

    public static function default_settings() {
        global $woocommerce ;

        return apply_filters( 'woocommerce_sumomemberships_email_settings' , array (
            array (
                'name' => __( 'Membership Email Settings' , 'sumomemberships' ) ,
                'type' => 'title' ,
                'id'   => 'email_setting_for_renewal'
            ) ,
            array (
                'name' => __( 'Membership Access Provided Email Settings' , 'sumomemberships' ) ,
                'type' => 'title' ,
                'id'   => 'email_setting_for_access'
            ) ,
            array (
                'name'    => __( 'Send Membership Access Provided Reminder Emails' , 'sumomemberships' ) ,
                'type'    => 'checkbox' ,
                'id'      => 'sumo_member_access_provided_email_check' ,
                'newids'  => 'sumo_member_access_provided_email_check' ,
                'class'   => 'sumo_member_access_provided_email_check' ,
                'std'     => 'yes' ,
                'default' => 'yes' ,
            ) ,
            array (
                'name'    => __( 'Email Subject' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:350px' ,
                'id'      => 'sumo_member_access_provider_email_subject' ,
                'newids'  => 'sumo_member_access_provider_email_subject' ,
                'class'   => 'sumo_member_access_provider_email_subject' ,
                'std'     => '[plan_name] has been provided for You on [site_title]' ,
                'default' => '[plan_name] has been provided for You on [site_title]' ,
            ) ,
            array (
                'name'    => __( 'Email Message' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:350px' ,
                'id'      => 'sumo_member_access_provider_email_message' ,
                'newids'  => 'sumo_member_access_provider_email_message' ,
                'class'   => 'sumo_member_access_provider_email_message' ,
                'std'     => 'You have been provided access to [plan_name] on [site_title]' ,
                'default' => 'You have been provided access to [plan_name] on [site_title]' ,
            ) ,
            array ( 'type' => 'sectionend' , 'id' => 'email_setting_for_access' ) ,
            array (
                'name' => __( 'Membership Expired Email Settings' , 'sumomemberships' ) ,
                'type' => 'title' ,
                'id'   => 'email_setting_for_expired'
            ) ,
            array (
                'name'    => __( 'Send Membership Expired Reminder Emails' , 'sumomemberships' ) ,
                'type'    => 'checkbox' ,
                'id'      => 'sumo_member_expired_email_check' ,
                'newids'  => 'sumo_member_expired_email_check' ,
                'class'   => 'sumo_member_expired_email_check' ,
                'std'     => 'yes' ,
                'default' => 'yes' ,
            ) ,
            array (
                'name'    => __( 'Email Subject' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:350px' ,
                'id'      => 'sumo_member_expired_email_subject' ,
                'newids'  => 'sumo_member_expired_email_subject' ,
                'class'   => 'sumo_member_expired_email_subject' ,
                'std'     => '[plan_name] has been Expired on [site_title]' ,
                'default' => '[plan_name] has been Expired on [site_title]' ,
            ) ,
            array (
                'name'    => __( 'Email Message' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:350px' ,
                'id'      => 'sumo_member_expired_email_message' ,
                'newids'  => 'sumo_member_expired_email_message' ,
                'class'   => 'sumo_member_expired_email_message' ,
                'std'     => 'Your [plan_name] from [site_title] was Expired on [expiry_date]' ,
                'default' => 'Your [plan_name] from [site_title] was Expired on [expiry_date]' ,
            ) ,
            array ( 'type' => 'sectionend' , 'id' => 'email_setting_for_expired' ) ,
            array (
                'name' => __( 'Membership Expiration Reminder Email Settings' , 'sumomemberships' ) ,
                'type' => 'title' ,
                'id'   => 'email_setting_going_to_expire'
            ) ,
            array (
                'name'    => __( 'Send Membership Expiration Reminder Emails' , 'sumomemberships' ) ,
                'type'    => 'checkbox' ,
                'id'      => 'sumo_member_expiration_email_check' ,
                'newids'  => 'sumo_member_expiration_email_check' ,
                'class'   => 'sumo_member_expiration_email_check' ,
                'std'     => 'yes' ,
                'default' => 'yes' ,
            ) ,
            array (
                'name'              => __( 'Send Membership Expiration Emails' , 'sumomemberships' ) ,
                'type'              => 'number' ,
                'desc'              => __( 'day(s) before due date' , 'sumomemberships' ) ,
                'id'                => 'sumo_member_expiration_email_in_days' ,
                'newids'            => 'sumo_member_expiration_email_in_days' ,
                'class'             => 'sumo_member_expiration_email_in_days' ,
                'std'               => '1' ,
                'default'           => '1' ,
                'custom_attributes' => array (
                    'min' => 0 ,
                )
            ) ,
            array (
                'name'    => __( 'Email Subject' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:350px' ,
                'id'      => 'sumo_member_going_to_expire_email_subject' ,
                'newids'  => 'sumo_member_going_to_expire_email_subject' ,
                'class'   => 'sumo_member_going_to_expire_email_subject' ,
                'std'     => '[plan_name] is going to Expire on [site_title]' ,
                'default' => '[plan_name] is going to Expire on [site_title]' ,
            ) ,
            array (
                'name'    => __( 'Email Message' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:350px' ,
                'id'      => 'sumo_member_going_to_expire_email_message' ,
                'newids'  => 'sumo_member_going_to_expire_email_message' ,
                'class'   => 'sumo_member_going_to_expire_email_message' ,
                'std'     => 'Your [plan_name] from [site_title] is going to Expire on [expiry_date]' ,
                'default' => 'Your [plan_name] from [site_title] is going to Expire on [expiry_date]' ,
            ) ,
            array ( 'type' => 'sectionend' , 'id' => 'email_setting_going_to_expire' ) ,
            array (
                'name' => __( 'Transfer Membership Email Settings' , 'sumomemberships' ) ,
                'type' => 'title' ,
                'id'   => 'email_setting_going_to_transfer'
            ) ,
            array (
                'name'    => __( 'Enable Email for Admin when Transfer Request submitted' , 'sumomemberships' ) ,
                'type'    => 'checkbox' ,
                'id'      => 'sumo_admin_member_transfer_request_submitted_email' ,
                'newids'  => 'sumo_admin_member_transfer_request_submitted_email' ,
                'class'   => 'sumo_admin_member_transfer_request_submitted_email' ,
                'std'     => 'no' ,
                'default' => 'no' ,
            ) ,
            array (
                'name'    => __( 'Email Subject' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:350px' ,
                'id'      => 'sumo_admin_member_transfer_request_submitted_email_subject' ,
                'newids'  => 'sumo_admin_member_transfer_request_submitted_email_subject' ,
                'class'   => 'sumo_admin_member_transfer_request_submitted_email_subject' ,
                'std'     => 'Plan Tranfer Request on [site_title]' ,
                'default' => 'Plan Tranfer Request on [site_title]' ,
            ) ,
            array (
                'name'    => __( 'Email Message' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:350px' ,
                'id'      => 'sumo_admin_member_transfer_request_submitted_email_message' ,
                'newids'  => 'sumo_admin_member_transfer_request_submitted_email_message' ,
                'class'   => 'sumo_admin_member_transfer_request_submitted_email_message' ,
                'std'     => '[sumo_plan_sender] is requesting to transfer their plan [plan_name] to [sumo_plan_receiver]' ,
                'default' => '[sumo_plan_sender] is requesting to transfer their plan [plan_name] to [sumo_plan_receiver]' ,
            ) ,
            array (
                'name'    => __( 'Enable Email for Plan Sender when Transfer Request submitted' , 'sumomemberships' ) ,
                'type'    => 'checkbox' ,
                'id'      => 'sumo_plan_sender_member_transfer_request_submitted_email' ,
                'newids'  => 'sumo_plan_sender_member_transfer_request_submitted_email' ,
                'class'   => 'sumo_plan_sender_member_transfer_request_submitted_email' ,
                'std'     => 'no' ,
                'default' => 'no' ,
            ) ,
            array (
                'name'    => __( 'Email Subject' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:350px' ,
                'id'      => 'sumo_plan_sender_member_transfer_request_submitted_email_subject' ,
                'newids'  => 'sumo_plan_sender_member_transfer_request_submitted_email_subject' ,
                'class'   => 'sumo_plan_sender_member_transfer_request_submitted_email_subject' ,
                'std'     => 'Plan Tranfer Request Submitted on [site_title]' ,
                'default' => 'Plan Tranfer Request Submitted on [site_title]' ,
            ) ,
            array (
                'name'    => __( 'Email Message' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:350px' ,
                'id'      => 'sumo_plan_sender_member_transfer_request_submitted_email_message' ,
                'newids'  => 'sumo_plan_sender_member_transfer_request_submitted_email_message' ,
                'class'   => 'sumo_plan_sender_member_transfer_request_submitted_email_message' ,
                'std'     => 'Your Request for Transfer plan [plan_name] to [sumo_plan_receiver] is Submitted.' ,
                'default' => 'Your Request for Transfer plan [plan_name] to [sumo_plan_receiver] is Submitted.' ,
            ) ,
            array (
                'name'    => __( 'Enable Email for Admin when Transfer Request Approved' , 'sumomemberships' ) ,
                'type'    => 'checkbox' ,
                'id'      => 'sumo_admin_member_transfer_request_approved_email' ,
                'newids'  => 'sumo_admin_member_transfer_request_approved_email' ,
                'class'   => 'sumo_admin_member_transfer_request_approved_email' ,
                'std'     => 'no' ,
                'default' => 'no' ,
            ) ,
            array (
                'name'    => __( 'Email Subject' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:350px' ,
                'id'      => 'sumo_admin_member_transfer_request_approved_email_subject' ,
                'newids'  => 'sumo_admin_member_transfer_request_approved_email_subject' ,
                'class'   => 'sumo_admin_member_transfer_request_approved_email_subject' ,
                'std'     => 'Plan Tranfered on [site_title]' ,
                'default' => 'Plan Tranfered on [site_title]' ,
            ) ,
            array (
                'name'    => __( 'Email Message' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:350px' ,
                'id'      => 'sumo_admin_member_transfer_request_approved_email_message' ,
                'newids'  => 'sumo_admin_member_transfer_request_approved_email_message' ,
                'class'   => 'sumo_admin_member_transfer_request_approved_email_message' ,
                'std'     => 'Plan [plan_name] transfered to [sumo_plan_receiver] from [sumo_plan_sender]' ,
                'default' => 'Plan [plan_name] transfered to [sumo_plan_receiver] from [sumo_plan_sender]' ,
            ) ,
            array (
                'name'    => __( 'Enable Email for Plan Sender when Transfer Request Approved' , 'sumomemberships' ) ,
                'type'    => 'checkbox' ,
                'id'      => 'sumo_plan_sender_member_transfer_request_approved_email' ,
                'newids'  => 'sumo_plan_sender_member_transfer_request_approved_email' ,
                'class'   => 'sumo_plan_sender_member_transfer_request_approved_email' ,
                'std'     => 'no' ,
                'default' => 'no' ,
            ) ,
            array (
                'name'    => __( 'Email Subject' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:350px' ,
                'id'      => 'sumo_plan_sender_member_transfer_request_approved_email_subject' ,
                'newids'  => 'sumo_plan_sender_member_transfer_request_approved_email_subject' ,
                'class'   => 'sumo_plan_sender_member_transfer_request_approved_email_subject' ,
                'std'     => 'Plan Tranfer Request Approved on [site_title]' ,
                'default' => 'Plan Tranfer Request Approved on [site_title]' ,
            ) ,
            array (
                'name'    => __( 'Email Message' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:350px' ,
                'id'      => 'sumo_plan_sender_member_transfer_request_approved_email_message' ,
                'newids'  => 'sumo_plan_sender_member_transfer_request_approved_email_message' ,
                'class'   => 'sumo_plan_sender_member_transfer_request_approved_email_message' ,
                'std'     => 'Your Request for Transfer plan [plan_name] to [sumo_plan_receiver] is Approved.' ,
                'default' => 'Your Request for Transfer plan [plan_name] to [sumo_plan_receiver] is Approved.' ,
            ) ,
            array (
                'name'    => __( 'Enable Email for Plan Receiver when Transfer Request Approved' , 'sumomemberships' ) ,
                'type'    => 'checkbox' ,
                'id'      => 'sumo_plan_receiver_member_transfer_request_approved_email' ,
                'newids'  => 'sumo_plan_receiver_member_transfer_request_approved_email' ,
                'class'   => 'sumo_plan_receiver_member_transfer_request_approved_email' ,
                'std'     => 'no' ,
                'default' => 'no' ,
            ) ,
            array (
                'name'    => __( 'Email Subject' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:350px' ,
                'id'      => 'sumo_plan_receiver_member_transfer_request_approved_email_subject' ,
                'newids'  => 'sumo_plan_receiver_member_transfer_request_approved_email_subject' ,
                'class'   => 'sumo_plan_receiver_member_transfer_request_approved_email_subject' ,
                'std'     => 'Plan Received on [site_title]' ,
                'default' => 'Plan Received on [site_title]' ,
            ) ,
            array (
                'name'    => __( 'Email Message' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:350px' ,
                'id'      => 'sumo_plan_receiver_member_transfer_request_approved_email_message' ,
                'newids'  => 'sumo_plan_receiver_member_transfer_request_approved_email_message' ,
                'class'   => 'sumo_plan_receiver_member_transfer_request_approved_email_message' ,
                'std'     => 'You have been Received a plan [plan_name] from [sumo_plan_sender].' ,
                'default' => 'You have been Received a plan [plan_name] from [sumo_plan_sender].' ,
            ) ,
            array (
                'name'    => __( 'Enable Email for Admin when Transfer Request Rejected' , 'sumomemberships' ) ,
                'type'    => 'checkbox' ,
                'id'      => 'sumo_admin_member_transfer_request_rejected_email' ,
                'newids'  => 'sumo_admin_member_transfer_request_rejected_email' ,
                'class'   => 'sumo_admin_member_transfer_request_rejected_email' ,
                'std'     => 'no' ,
                'default' => 'no' ,
            ) ,
            array (
                'name'    => __( 'Email Subject' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:350px' ,
                'id'      => 'sumo_admin_member_transfer_request_rejected_email_subject' ,
                'newids'  => 'sumo_admin_member_transfer_request_rejected_email_subject' ,
                'class'   => 'sumo_admin_member_transfer_request_rejected_email_subject' ,
                'std'     => 'Plan Transfer Rejected on [site_title]' ,
                'default' => 'Plan Transfer Rejected on [site_title]' ,
            ) ,
            array (
                'name'    => __( 'Email Message' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:350px' ,
                'id'      => 'sumo_admin_member_transfer_request_rejected_email_message' ,
                'newids'  => 'sumo_admin_member_transfer_request_rejected_email_message' ,
                'class'   => 'sumo_admin_member_transfer_request_rejected_email_message' ,
                'std'     => '[sumo_plan_sender] Request for Transfer plan [plan_name] to [sumo_plan_receiver] is Rejected.' ,
                'default' => '[sumo_plan_sender] Request for Transfer plan [plan_name] to [sumo_plan_receiver] is Rejected.' ,
            ) ,
            array (
                'name'    => __( 'Enable Email for Plan Receiver when Transfer Request Rejected' , 'sumomemberships' ) ,
                'type'    => 'checkbox' ,
                'id'      => 'sumo_plan_receiver_member_transfer_request_rejected_email' ,
                'newids'  => 'sumo_plan_receiver_member_transfer_request_rejected_email' ,
                'class'   => 'sumo_plan_receiver_member_transfer_request_rejected_email' ,
                'std'     => 'no' ,
                'default' => 'no' ,
            ) ,
            array (
                'name'    => __( 'Email Subject' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:350px' ,
                'id'      => 'sumo_plan_receiver_member_transfer_request_rejected_email_subject' ,
                'newids'  => 'sumo_plan_receiver_member_transfer_request_rejected_email_subject' ,
                'class'   => 'sumo_plan_receiver_member_transfer_request_rejected_email_subject' ,
                'std'     => 'Plan Transfer Rejected on [site_title]' ,
                'default' => 'Plan Transfer Rejected on [site_title]' ,
            ) ,
            array (
                'name'    => __( 'Email Message' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:350px' ,
                'id'      => 'sumo_plan_receiver_member_transfer_request_rejected_email_message' ,
                'newids'  => 'sumo_plan_receiver_member_transfer_request_rejected_email_message' ,
                'class'   => 'sumo_plan_receiver_member_transfer_request_rejected_email_message' ,
                'std'     => 'Your Request for Transfer plan [plan_name] to [sumo_plan_receiver] is Rejected.' ,
                'default' => 'Your Request for Transfer plan [plan_name] to [sumo_plan_receiver] is Rejected.' ,
            ) ,
            array (
                'name'    => __( 'Enable Email for Admin when Transfer plan made by Admin Manually' , 'sumomemberships' ) ,
                'type'    => 'checkbox' ,
                'id'      => 'sumo_admin_member_plan_transfered_manually_email' ,
                'newids'  => 'sumo_admin_member_plan_transfered_manually_email' ,
                'class'   => 'sumo_admin_member_plan_transfered_manually_email' ,
                'std'     => 'no' ,
                'default' => 'no' ,
            ) ,
            array (
                'name'    => __( 'Email Subject' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:350px' ,
                'id'      => 'sumo_admin_member_plan_transfered_manually_email_subject' ,
                'newids'  => 'sumo_admin_member_plan_transfered_manually_email_subject' ,
                'class'   => 'sumo_admin_member_plan_transfered_manually_email_subject' ,
                'std'     => 'Plan Transfered on [site_title]' ,
                'default' => 'Plan Transfered on [site_title]' ,
            ) ,
            array (
                'name'    => __( 'Email Message' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:350px' ,
                'id'      => 'sumo_admin_member_plan_transfered_manually_email_message' ,
                'newids'  => 'sumo_admin_member_plan_transfered_manually_email_message' ,
                'class'   => 'sumo_admin_member_plan_transfered_manually_email_message' ,
                'std'     => 'You have been Transfered plan [plan_name] to [sumo_plan_receiver] from [sumo_plan_sender].' ,
                'default' => 'You have been Transfered plan [plan_name] to [sumo_plan_receiver] from [sumo_plan_sender].' ,
            ) ,
            array (
                'name'    => __( 'Enable Email for Plan Sender when Transfer plan made by Admin Manually' , 'sumomemberships' ) ,
                'type'    => 'checkbox' ,
                'id'      => 'sumo_plan_sender_member_plan_transfered_manually_email' ,
                'newids'  => 'sumo_plan_sender_member_plan_transfered_manually_email' ,
                'class'   => 'sumo_plan_sender_member_plan_transfered_manually_email' ,
                'std'     => 'no' ,
                'default' => 'no' ,
            ) ,
            array (
                'name'    => __( 'Email Subject' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:350px' ,
                'id'      => 'sumo_plan_sender_member_plan_transfered_manually_email_subject' ,
                'newids'  => 'sumo_plan_sender_member_plan_transfered_manually_email_subject' ,
                'class'   => 'sumo_plan_sender_member_plan_transfered_manually_email_subject' ,
                'std'     => 'Plan Transfered on [site_title]' ,
                'default' => 'Plan Transfered on [site_title]' ,
            ) ,
            array (
                'name'    => __( 'Email Message' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:350px' ,
                'id'      => 'sumo_plan_sender_member_plan_transfered_manually_email_message' ,
                'newids'  => 'sumo_plan_sender_member_plan_transfered_manually_email_message' ,
                'class'   => 'sumo_plan_sender_member_plan_transfered_manually_email_message' ,
                'std'     => 'Admin have been Transfered plan [plan_name] to [sumo_plan_receiver] from You.' ,
                'default' => 'Admin have been Transfered plan [plan_name] to [sumo_plan_receiver] from You.' ,
            ) ,
            array (
                'name'    => __( 'Enable Email for Plan Receiver when Transfer plan made by Admin Manually' , 'sumomemberships' ) ,
                'type'    => 'checkbox' ,
                'id'      => 'sumo_plan_receiver_member_plan_transfered_manually_email' ,
                'newids'  => 'sumo_plan_receiver_member_plan_transfered_manually_email' ,
                'class'   => 'sumo_plan_receiver_member_plan_transfered_manually_email' ,
                'std'     => 'no' ,
                'default' => 'no' ,
            ) ,
            array (
                'name'    => __( 'Email Subject' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:350px' ,
                'id'      => 'sumo_plan_receiver_member_plan_transfered_manually_email_subject' ,
                'newids'  => 'sumo_plan_receiver_member_plan_transfered_manually_email_subject' ,
                'class'   => 'sumo_plan_receiver_member_plan_transfered_manually_email_subject' ,
                'std'     => 'Plan Transfered on [site_title]' ,
                'default' => 'Plan Transfered on [site_title]' ,
            ) ,
            array (
                'name'    => __( 'Email Message' , 'sumomemberships' ) ,
                'type'    => 'textarea' ,
                'css'     => 'min-width:350px' ,
                'id'      => 'sumo_plan_receiver_member_plan_transfered_manually_email_message' ,
                'newids'  => 'sumo_plan_receiver_member_plan_transfered_manually_email_message' ,
                'class'   => 'sumo_plan_receiver_member_plan_transfered_manually_email_message' ,
                'std'     => 'Admin have been Transfered plan [plan_name] to You from [sumo_plan_sender].' ,
                'default' => 'Admin have been Transfered plan [plan_name] to You from [sumo_plan_sender].' ,
            ) ,
            array ( 'type' => 'sectionend' , 'id' => 'email_setting_going_to_expire' ) ,
        ) ) ;
    }

    /**
     * Registering Custom Field Admin Settings
     */
    public static function register_admin_settings() {


        woocommerce_admin_fields( SUMOEmail_Settings_Tab::default_settings() ) ;
    }

    /**
     * Update the Settings on Save Changes
     */
    public static function advance_update_settings() {

        woocommerce_update_options( SUMOEmail_Settings_Tab::default_settings() ) ;
    }

    public static function sumomemberships_send_mail( $to , $subject , $message ) {
        global $woocommerce ;
        ob_start() ;
        wc_get_template( 'emails/email-header.php' , array ( 'email_heading' => $subject ) ) ;
        echo $message ;
        wc_get_template( 'emails/email-footer.php' ) ;
        $woo_temp_msg = ob_get_clean() ;
        $headers      = "MIME-Version: 1.0\r\n" ;
        $headers      .= "Content-Type: text/html; charset=UTF-8\r\n" ;
        $headers      .= "From: " . get_option( 'woocommerce_email_from_name' ) . " <" . get_option( 'woocommerce_email_from_address' ) . ">\r\n" ;
        if ( ( float ) $woocommerce->version <= ( float ) ('2.2.0') ) {
            if ( wp_mail( $to , $subject , $woo_temp_msg , $headers ) ) {
                
            }
        } else {
            $mailer = WC()->mailer() ;
            $mailer->send( $to , $subject , $woo_temp_msg , '' , '' ) ;
        }
    }

    public static function send_mail_when_expired( $username , $planname ) {
        if ( get_option( 'sumo_member_expired_email_check' ) == 'yes' ) {
            if ( $username != '' ) {
                $userdetail         = get_userdata( $username ) ;
                $to                 = $userdetail->user_email ;
                $subject_to_display = get_option( 'sumo_member_expired_email_subject' ) ;
                $message_to_display = get_option( 'sumo_member_expired_email_message' ) ;
                $plan_name          = get_post_meta( $planname , 'sumomemberships_plan_name' , true ) ;

                $rpl_planname_for_sub = str_replace( '[plan_name]' , $planname , $subject_to_display ) ;
                $subject              = str_replace( '[site_title]' , get_bloginfo( 'name' ) , $rpl_planname_for_sub ) ;

                $rpl_planname_for_msg = str_replace( '[plan_name]' , $planname , $message_to_display ) ;
                $message              = str_replace( '[site_title]' , get_bloginfo( 'name' ) , $rpl_planname_for_msg ) ;
                SUMOEmail_Settings_Tab::sumomemberships_send_mail( $to , $subject , $message ) ;
            }
        }
    }

    public static function setup_schedule_before_expiry( $planid , $uniqid , $postid , $todate ) {

        $expiration_before = get_option( "sumo_member_expiration_email_check" ) ;
        $expiration_days   = get_option( 'sumo_member_expiration_email_in_days' ) ;
        if ( $expiration_before == 'yes' && $todate != '' ) {
            $todate               = date( 'Y-m-d' , strtotime( $todate ) ) . " " . date( 'h:i:s' ) ;
            $daytimestamp         = 86400 * ( int ) $expiration_days ;
            $expirydate_timestamp = ( int ) (strtotime( $todate ) - $daytimestamp) ;
            if ( $expirydate_timestamp >= time() ) {
                self::clear_schedule_upon_status( $planid , $uniqid , $postid ) ;
                wp_schedule_single_event( $expirydate_timestamp , 'sumomemberships_schedule_before_expiry' , array ( ( int ) $planid , ( string ) $uniqid , ( int ) $postid ) ) ;
            } else {
                self::clear_schedule_upon_status( $planid , $uniqid , $postid ) ;
                wp_schedule_single_event( time() , 'sumomemberships_schedule_before_expiry' , array ( ( int ) $planid , ( string ) $uniqid , ( int ) $postid ) ) ;
            }
        }
    }

    public static function clear_schedule_upon_status( $previous_plan_id , $uniqid , $postid ) {
        wp_clear_scheduled_hook( 'sumomemberships_schedule_before_expiry' , array ( ( int ) $previous_plan_id , ( string ) $uniqid , ( int ) $postid ) ) ;
    }

    public static function expiry_reminder_email( $planid , $uniqid , $postid ) {
        if ( get_option( 'sumo_member_expiration_email_check' ) == 'yes' ) {
            $username = get_post_meta( $postid , 'sumomemberships_userid' , true ) ;
            if ( $username != '' ) {
                $userdetail         = get_userdata( $username ) ;
                $to                 = $userdetail->user_email ;
                $subject_to_display = get_option( 'sumo_member_going_to_expire_email_subject' ) ;
                $message_to_display = get_option( 'sumo_member_going_to_expire_email_message' ) ;

                $planname      = get_post_meta( $planid , 'sumomemberships_plan_name' , true ) ;
                $find_array    = array ( '[plan_name]' , '[site_title]' , '[expiry_date]','[subscription_end_date]','[subscription_renewal_date]' ) ;
                $get_plan_data = get_post_meta( $postid , 'sumomemberships_saved_plans' , true ) ;
                
                $plan_duration = isset($get_plan_data[ $uniqid ][ 'to_date' ] ) ? $get_plan_data[ $uniqid ][ 'to_date' ] :'';
                $plan_status   = isset($get_plan_data[ $uniqid ][ 'choose_status' ]) ? $get_plan_data[ $uniqid ][ 'choose_status' ]:'';

                $subscription_end_date     = '--';
                $subscription_renewal_date = '--';
		$expiry_date               = '--';
                if('active' == $plan_status){
		    $expiry_date                   = $plan_duration == "" ? esc_html__( "Never Expires" , 'sumomemberships' ) : $plan_duration ;
                    $subscription_id               = isset($get_plan_data[ $uniqid ][ 'associated_subsc_id' ]) ? $get_plan_data[ $uniqid ][ 'associated_subsc_id' ]:'';
                    if( $subscription_id ) {
                       $subscription_end_date      = function_exists('sumo_display_end_date') ? sumo_display_end_date($subscription_id) :'';
                       $subscription_renewal_date  = function_exists('sumo_display_next_due_date') ? sumo_display_next_due_date($subscription_id):'';
                    } 
                } 
                                
                $replace_array = array ( $planname , get_bloginfo( 'name' ) , $expiry_date, $subscription_end_date, $subscription_renewal_date ) ;
                $subject       = str_replace( $find_array , $replace_array , $subject_to_display ) ;

                $message = str_replace( $find_array , $replace_array , $message_to_display ) ;

                SUMOEmail_Settings_Tab::sumomemberships_send_mail( $to , $subject , $message ) ;
            }
        }
    }

    public static function send_mail_when_access_granted( $username , $planname ) {
        if ( get_option( 'sumo_member_access_provided_email_check' ) != 'no' ) {
            if ( $username != '' ) {
                $userdetail         = get_userdata( $username ) ;
                $to                 = $userdetail->user_email ;
                $subject_to_display = get_option( 'sumo_member_access_provider_email_subject' ) ;
                $message_to_display = get_option( 'sumo_member_access_provider_email_message' ) ;

                $rpl_planname_for_sub = str_replace( '[plan_name]' , $planname , $subject_to_display ) ;
                $subject              = str_replace( '[site_title]' , get_bloginfo( 'name' ) , $rpl_planname_for_sub ) ;

                $rpl_planname_for_msg = str_replace( '[plan_name]' , $planname , $message_to_display ) ;
                $message              = str_replace( '[site_title]' , get_bloginfo( 'name' ) , $rpl_planname_for_msg ) ;
                SUMOEmail_Settings_Tab::sumomemberships_send_mail( $to , $subject , $message ) ;
            }
        }
    }

    public static function load_default_settings() {
        foreach ( SUMOEmail_Settings_Tab::default_settings() as $setting ) {
            if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
            }
        }
    }

}

new SUMOEmail_Settings_Tab() ;

function sumomembership_email_settings() {
    foreach ( SUMOEmail_Settings_Tab::default_settings() as $setting ) {
        if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
            delete_option( $setting[ 'newids' ] ) ;
            add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
        }
    }
}
