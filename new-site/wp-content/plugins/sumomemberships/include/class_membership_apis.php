<?php

class SUMOMemberships_APIs {

    public function __construct() {

        add_action( 'sumomemberships_manual_plan_updation' , array( $this , 'record_log_on_plan_change' ) , 10 , 4 ) ;
        add_action( 'sumomemberships_manual_plan_status_updation' , array( $this , 'record_log_on_status_change' ) , 10 , 4 ) ;
        add_action( 'sumomemberships_manual_plan_expiry_date_updation' , array( $this , 'record_log_on_date_change' ) , 10 , 4 ) ;
        add_action( 'sumomemberships_manual_first_new_plan_addition' , array( $this , 'record_log_new_plan_addition' ) , 10 , 3 ) ;
        add_action( 'sumomemberships_manual_new_plan_addition' , array( $this , 'record_log_new_plan_addition' ) , 10 , 3 ) ;
        add_action( 'sumomemberships_delete_plan' , array( $this , 'record_log_plan_deletion' ) , 10 , 3 ) ;
        add_action( 'sumomemberships_active_subscription' , array( $this , 'record_log_for_subscription_status' ) , 10 , 4 ) ;
        add_action( 'sumomemberships_status_subscription' , array( $this , 'record_log_for_subscription_status' ) , 10 , 4 ) ;
        add_action( 'sumomemberships_cron_expiry_plan_status_updation' , array( $this , 'record_log_for_auto_expiry_membership_status' ) , 10 , 4 ) ;
        add_action( 'sumomemberships_manual_member_plan_status_updation' , array( $this , 'record_log_for_manual_user_membership_status' ) , 10 , 4 ) ;
        add_action( 'sumomemberships_add_new_plan_upon_order_status' , array( $this , 'record_log_on_add_new_plan_order_status' ) , 10 , 4 ) ;
        add_action( 'sumomemberships_update_existing_plan_upon_order_status' , array( $this , 'record_log_on_update_plan_order_status' ) , 10 , 4 ) ;
    }

    public function record_log_on_plan_change( $previous_plan_id , $new_plan_id , $postid , $each_unique_key ) {
        $saved_plans       = get_post_meta( $postid , 'sumomemberships_saved_plans' , true ) ;
        $userid            = get_post_meta( $postid , 'sumomemberships_userid' , true ) ;
        $previous_planname = get_post_meta( $previous_plan_id , 'sumomemberships_plan_name' , true ) ;
        $new_planname      = get_post_meta( $new_plan_id , 'sumomemberships_plan_name' , true ) ;
        $string            = __( "Site Admin has Updated the Plan from [sumo_previous_plan_name] to [sumo_new_plan_name]." , 'sumomemberships' ) ;
        $event             = str_replace( '[sumo_previous_plan_name]' , $previous_planname , $string ) ;
        $event             = str_replace( '[sumo_new_plan_name]' , $new_planname , $event ) ;
        $to_date           = $saved_plans[ $each_unique_key ][ 'to_date' ] ;

        $this_status = $_POST[ 'sumomember_plan_meta' ][ $each_unique_key ][ 'choose_status' ] ;

        if( $this_status == 'active' ) {
            SUMOEmail_Settings_Tab::send_mail_when_access_granted( $userid , $new_planname ) ;
            SUMOEmail_Settings_Tab::setup_schedule_before_expiry( $new_plan_id , $each_unique_key , $postid , $to_date ) ;
        } else {
            $this->clear_expire_cron_and_linked_privilege_cron( $postid , $each_unique_key , $userid , $previous_plan_id ) ;
            SUMOEmail_Settings_Tab::clear_schedule_upon_status( $previous_plan_id , $each_unique_key , $postid ) ;
        }
        $username = get_post_meta( $postid , 'sumomemberships_userid' , true ) ;
        self::sumo_function_to_set_data_for_masterlog( $event , $username , $new_planname ) ;

        SUMOMemberships_Admin_Meta_Boxes::add_membership_note( $event , $postid , $username , $this_status , $new_plan_id ) ;
    }

    public function record_log_on_status_change( $previous_plan_status , $new_plan_status , $postid , $each_unique_key ) {
        $retrieve_saved_plans = get_post_meta( $postid , 'sumomemberships_saved_plans' , true ) ;
        $plan_id              = $retrieve_saved_plans[ $each_unique_key ][ 'choose_plan' ] ;
        $to_date              = $retrieve_saved_plans[ $each_unique_key ][ 'to_date' ] ;
        $planname             = get_post_meta( $plan_id , 'sumomemberships_plan_name' , true ) ;
        $previous_plan_status = ucfirst( $previous_plan_status ) ;
        $new_plan_status      = ucfirst( $new_plan_status ) ;
        $string               = __( "Site Admin has Updated the Plan Status from [sumo_previous_plan_status] to [sumo_new_plan_status]." , 'sumomemberships' ) ;
        $event                = str_replace( '[sumo_previous_plan_status]' , $previous_plan_status , $string ) ;
        $event                = str_replace( '[sumo_new_plan_status]' , $new_plan_status , $event ) ;
        $username             = get_post_meta( $postid , 'sumomemberships_userid' , true ) ;

        self::sumo_function_to_set_data_for_masterlog( $event , $username , $planname ) ;

        if( strtolower( $new_plan_status ) == 'active' ) {
            SUMOEmail_Settings_Tab::send_mail_when_access_granted( $username , $planname ) ;
            SUMOEmail_Settings_Tab::setup_schedule_before_expiry( $plan_id , $each_unique_key , $postid , $to_date ) ;
        } else {
            SUMOEmail_Settings_Tab::clear_schedule_upon_status( $plan_id , $each_unique_key , $postid ) ;
        }
        SUMOMemberships_Admin_Meta_Boxes::add_membership_note( $event , $postid , $username , strtolower( $new_plan_status ) , $plan_id ) ;
    }

    public function record_log_on_date_change( $previous_plan_expiry_date , $new_plan_expiry_date , $postid , $each_unique_key ) {
        $retrieve_saved_plans = get_post_meta( $postid , 'sumomemberships_saved_plans' , true ) ;
        $plan_id              = $retrieve_saved_plans[ $each_unique_key ][ 'choose_plan' ] ;
        $planname             = get_post_meta( $plan_id , 'sumomemberships_plan_name' , true ) ;
        $username             = get_post_meta( $postid , 'sumomemberships_userid' , true ) ;

        $this_plan_status = $_POST[ 'sumomember_plan_meta' ][ $each_unique_key ][ 'choose_status' ] ;

        if( $this_plan_status == 'active' ) {
//Cron setup
            if( $previous_plan_expiry_date != '' && $new_plan_expiry_date != '' ) {
                SUMOEmail_Settings_Tab::setup_schedule_before_expiry( $plan_id , $each_unique_key , $postid , $new_plan_expiry_date ) ;
            } elseif( $previous_plan_expiry_date != '' && $new_plan_expiry_date == '' ) {
                $this->clear_expire_cron_and_linked_privilege_cron( $postid , $each_unique_key , $username , $plan_id ) ;
                SUMOEmail_Settings_Tab::clear_schedule_upon_status( $plan_id , $each_unique_key , $postid ) ;
            } else {
                $this->set_expire_cron_and_linked_privilege_cron( $postid , $each_unique_key , $username , $plan_id ) ;
                SUMOEmail_Settings_Tab::setup_schedule_before_expiry( $plan_id , $each_unique_key , $postid , $new_plan_expiry_date ) ;
            }

            $previous_plan_expiry_date = $previous_plan_expiry_date == '' ? "Unlimited" : $previous_plan_expiry_date ;
            $new_plan_expiry_date      = $new_plan_expiry_date == '' ? "Unlimited" : $new_plan_expiry_date ;
            $available_duration        = ( int ) $retrieve_saved_plans[ $each_unique_key ][ 'available_duration' ] ;

            if( $available_duration > 0 && $previous_plan_expiry_date == '--' ) {
                $previous_plan_expiry_date = "Paused" ;
                $new_plan_expiry_date      = date( 'Y-m-d h:i:s' , ( int ) ($available_duration + time()) ) ;
            } elseif( $new_plan_expiry_date == '--' ) {
                $new_plan_expiry_date = 'Paused' ;
            }
            $event = __( "Site Admin has Updated the Expiry Date from $previous_plan_expiry_date to $new_plan_expiry_date." , 'sumomemberships' ) ;

            self::sumo_function_to_set_data_for_masterlog( $event , $username , $planname ) ;
            $status = $retrieve_saved_plans[ $each_unique_key ][ 'choose_status' ] ;
            SUMOMemberships_Admin_Meta_Boxes::add_membership_note( $event , $postid , $username , $status , $plan_id ) ;
        }
    }

    public function record_log_new_plan_addition( $get_plan_keys , $new_data , $postid ) {
        if( is_array( $get_plan_keys ) && ! empty( $get_plan_keys ) ) {
            foreach( $get_plan_keys as $eachkey ) {
                $plan_id = $new_data[ $eachkey ][ 'choose_plan' ] ;
                if( $plan_id != '' ) {
                    $plan_status = $new_data[ $eachkey ][ 'choose_status' ] ;
                    $planname    = get_post_meta( $plan_id , 'sumomemberships_plan_name' , true ) ;
                    $username    = get_post_meta( $postid , 'sumomemberships_userid' , true ) ;

                    if( $new_data[ $eachkey ][ 'to_date' ] ) {
                        $to_date = $new_data[ $eachkey ][ 'to_date' ] . ' ' . date( "h:i:s" ) ;
                    } else {
                        $to_date = "" ;
                    }

                    if( $plan_status == 'active' ) {
                        $string = __( "Site Admin has granted access to [sumo_plan_name]." , 'sumomemberships' ) ;
                        $event  = str_replace( '[sumo_plan_name]' , $planname , $string ) ;
                        if( $to_date != "" ) {
                            $this->set_expire_cron_and_linked_privilege_cron( $postid , $eachkey , $username , $plan_id , $to_date ) ;
                            SUMOEmail_Settings_Tab::send_mail_when_access_granted( $username , $planname ) ;
                            SUMOEmail_Settings_Tab::setup_schedule_before_expiry( $plan_id , $eachkey , $postid , $to_date ) ;
                        } else {
                            if( isset( $_POST[ 'sumomember_plan_metas' ][ $eachkey ][ 'to_date' ] ) ) {
                                $new_scheduled_link_plans                                               = sumo_get_schedule_link_plans( $plan_id , $username ) ;
                                $new_privileged_link_plans                                              = sumo_get_privileged_link_plans( $plan_id , $username ) ;
                                $_POST[ 'sumomember_plan_metas' ][ $eachkey ][ 'to_date' ]              = "" ;
                                $_POST[ 'sumomember_plan_metas' ][ $eachkey ][ 'link_plans' ]           = $new_privileged_link_plans ;
                                $_POST[ 'sumomember_plan_metas' ][ $eachkey ][ 'scheduled_link_plans' ] = $new_scheduled_link_plans ;
                                
                                SUMOEmail_Settings_Tab::send_mail_when_access_granted( $username , $planname ) ;
                            }
                        }
                    } elseif( $plan_status == 'paused' ) {
                        $string = __( "Site Admin has paused the [sumo_plan_name]" , 'sumomemberships' ) ;
                        $event  = str_replace( '[sumo_plan_name]' , $planname , $string ) ;
                        SUMOEmail_Settings_Tab::clear_schedule_upon_status( $plan_id , $eachkey , $postid ) ;
                        $this->clear_expire_cron_and_linked_privilege_cron( $postid , $eachkey , $username , $plan_id ) ;
                    } elseif( $plan_status == 'cancelled' ) {
                        $string = __( "Site Admin has cancelled the [sumo_plan_name]" , 'sumomemberships' ) ;
                        $event  = str_replace( '[sumo_plan_name]' , $planname , $string ) ;
                        SUMOEmail_Settings_Tab::clear_schedule_upon_status( $plan_id , $eachkey , $postid ) ;
                        $this->clear_expire_cron_and_linked_privilege_cron( $postid , $eachkey , $username , $plan_id ) ;
                    } else {
                        $string = __( "Site Admin has [sumo_plan_status] the [sumo_plan_name]" , 'sumomemberships' ) ;
                        $event  = str_replace( '[sumo_plan_status]' , $plan_status , $string ) ;
                        $event  = str_replace( '[sumo_plan_name]' , $planname , $event ) ;
                        SUMOEmail_Settings_Tab::clear_schedule_upon_status( $plan_id , $eachkey , $postid ) ;
                        $this->clear_expire_cron_and_linked_privilege_cron( $postid , $eachkey , $username , $plan_id ) ;
                    }
                    self::sumo_function_to_set_data_for_masterlog( $event , $username , $planname ) ;

                    SUMOMemberships_Admin_Meta_Boxes::add_membership_note( $event , $postid , $username , $plan_status , $plan_id ) ;
                }
            }
        }
    }

    public function record_log_plan_deletion( $get_plan_keys , $new_data , $postid ) {

        if( isset( $new_data[ $get_plan_keys ][ 'choose_plan' ] ) ) {
            $plan_id = $new_data[ $get_plan_keys ][ 'choose_plan' ] ;
            if( $plan_id != '' ) {
                $plan_status = $new_data[ $get_plan_keys ][ 'choose_status' ] ;
                $planname    = get_post_meta( $plan_id , 'sumomemberships_plan_name' , true ) ;
                $username    = get_post_meta( $postid , 'sumomemberships_userid' , true ) ;
                $string      = __( "Site Admin has Removed the [sumo_plan_name]" , 'sumomemberships' ) ;
                $event       = str_replace( '[sumo_plan_name]' , $planname , $string ) ;
                $this->clear_expire_cron_and_linked_privilege_cron( $postid , $get_plan_keys , $username , $plan_id ) ;
                SUMOEmail_Settings_Tab::clear_schedule_upon_status( $plan_id , $get_plan_keys , $postid ) ;
                self::sumo_function_to_set_data_for_masterlog( $event , $username , $planname ) ;
                SUMOMemberships_Admin_Meta_Boxes::add_membership_note( $event , $postid , $username , $plan_status , $plan_id ) ;
            }
        }
    }

    public function record_log_for_subscription_status( $unique_id , $new_data , $member_post_id , $status ) {

        if( isset( $new_data[ $unique_id ][ 'choose_plan' ] ) ) {
            $plan_id              = $new_data[ $unique_id ][ 'choose_plan' ] ;
            $subscription_post_id = $new_data[ $unique_id ][ 'associated_subsc_id' ] ;
            $subscription_no      = get_post_meta( $subscription_post_id , 'sumo_get_subscription_number' , true ) ;
            if( $plan_id != '' ) {
                $plan_status = $new_data[ $unique_id ][ 'choose_status' ] ;
                $planname    = get_post_meta( $plan_id , 'sumomemberships_plan_name' , true ) ;
                $username    = get_post_meta( $member_post_id , 'sumomemberships_userid' , true ) ;
                $string      = __( "[sumo_plan_name] is currently [sumo_plan_status] (based on Subscription #[sumo_subscription_no] Status)." , 'sumomemberships' ) ;
                $event       = str_replace( '[sumo_plan_name]' , $planname , $string ) ;
                $event       = str_replace( '[sumo_plan_status]' , ucfirst( $status ) , $event ) ;
                $event       = str_replace( '[sumo_subscription_no]' , $subscription_no , $event ) ;

                if( $status == 'active' ) {
                    SUMOEmail_Settings_Tab::send_mail_when_access_granted( $username , $planname ) ;
                }
                self::sumo_function_to_set_data_for_masterlog( $event , $username , $planname ) ;
                SUMOMemberships_Admin_Meta_Boxes::add_membership_note( $event , $member_post_id , $username , $plan_status , $plan_id ) ;
            }
        }
    }

    public function record_log_for_manual_user_membership_status( $plan_id , $action , $post_id , $unique_id ) {
        if( $plan_id != '' ) {
            $plan_action = ucfirst( $action . "d" ) ;
            $planname    = get_post_meta( $plan_id , 'sumomemberships_plan_name' , true ) ;
            $username    = get_post_meta( $post_id , 'sumomemberships_userid' , true ) ;
            $saved_plans = get_post_meta( $post_id , 'sumomemberships_saved_plans' , true ) ;
            $to_date     = $saved_plans[ $unique_id ][ 'to_date' ] ;
            $string      = __( "User has [sumo_plan_action] the [sumo_plan_name]" , 'sumomemberships' ) ;
            $event       = str_replace( '[sumo_plan_action]' , $plan_action , $string ) ;
            $event       = str_replace( '[sumo_plan_name]' , $planname , $event ) ;
            if( $plan_action == 'Resumed' ) {
                $plan_status = "active" ;
                SUMOEmail_Settings_Tab::send_mail_when_access_granted( $username , $planname ) ;
                SUMOEmail_Settings_Tab::setup_schedule_before_expiry( $plan_id , $unique_id , $post_id , $to_date ) ;
            } elseif( $plan_action == 'Paused' ) {
                $plan_status = "paused" ;
                SUMOEmail_Settings_Tab::clear_schedule_upon_status( $plan_id , $unique_id , $post_id ) ;
            }
            self::sumo_function_to_set_data_for_masterlog( $event , $username , $planname ) ;
            SUMOMemberships_Admin_Meta_Boxes::add_membership_note( $event , $post_id , $username , $plan_status , $plan_id ) ;
        }
    }

    public function record_log_for_auto_expiry_membership_status( $plan_id , $status , $post_id , $unique_id ) {
        if( $plan_id != '' ) {
            $plan_status = ucfirst( $status ) ;
            $planname    = get_post_meta( $plan_id , 'sumomemberships_plan_name' , true ) ;
            $username    = get_post_meta( $post_id , 'sumomemberships_userid' , true ) ;
            $string      = __( "[sumo_plan_name] has been [sumo_plan_status]" , 'sumomemberships' ) ;
            $event       = str_replace( '[sumo_plan_name]' , $planname , $string ) ;
            $event       = str_replace( '[sumo_plan_status]' , $plan_status , $event ) ;

            self::sumo_function_to_set_data_for_masterlog( $event , $username , $planname ) ;

            if( $plan_status == 'expired' ) {
                $this->clear_expire_cron_and_linked_privilege_cron( $post_id , $unique_id , $username , $plan_id ) ;
                SUMOEmail_Settings_Tab::clear_schedule_upon_status( $plan_id , $unique_id , $post_id ) ;
                SUMOEmail_Settings_Tab::send_mail_when_expired( $username , $planname ) ;
            }
            SUMOMemberships_Admin_Meta_Boxes::add_membership_note( $event , $post_id , $username , $status , $plan_id ) ;
        }
    }

    public function record_log_on_add_new_plan_order_status( $saved_plans , $plan_id , $firstuniqid , $post_id ) {
        if( $plan_id != '' ) {
            $plan_status = ucfirst( $saved_plans[ $firstuniqid ][ 'choose_status' ] ) ;
            $to_date     = $saved_plans[ $firstuniqid ][ 'to_date' ] ;
            $planname    = get_post_meta( $plan_id , 'sumomemberships_plan_name' , true ) ;
            $username    = get_post_meta( $post_id , 'sumomemberships_userid' , true ) ;
            $string      = __( "[sumo_plan_name] has been [sumo_plan_status] based on the Order Status" , 'sumomemberships' ) ;
            $event       = str_replace( '[sumo_plan_name]' , $planname , $string ) ;
            $event       = str_replace( '[sumo_plan_status]' , $plan_status , $event ) ;

            if( strtolower( $plan_status ) == 'active' ) {
                SUMOEmail_Settings_Tab::setup_schedule_before_expiry( $plan_id , $firstuniqid , $post_id , $to_date ) ;
                SUMOEmail_Settings_Tab::send_mail_when_access_granted( $username , $planname ) ;
            } else {
                $this->clear_expire_cron_and_linked_privilege_cron( $post_id , $firstuniqid , $username , $plan_id ) ;
                SUMOEmail_Settings_Tab::clear_schedule_upon_status( $plan_id , $firstuniqid , $post_id ) ;
            }

            self::sumo_function_to_set_data_for_masterlog( $event , $username , $planname ) ;
            SUMOMemberships_Admin_Meta_Boxes::add_membership_note( $event , $post_id , $username , strtolower( $plan_status ) , $plan_id ) ;
        }

        if( ! empty( $saved_plans[ $firstuniqid ][ "link_plans" ] ) && array_filter( $saved_plans[ $firstuniqid ][ "link_plans" ] ) ) {

            foreach( array_filter( $saved_plans[ $firstuniqid ][ "link_plans" ] ) as $linked_plan_id ) {

                $plan_status      = sumo_is_global_plan_status_active( $linked_plan_id ) == true ? 'active' : '' ;
                $linked_plan_name = get_post_meta( $linked_plan_id , 'sumomemberships_plan_name' , true ) ;
                $parent_name      = get_post_meta( $plan_id , 'sumomemberships_plan_name' , true ) ;
                $username         = get_post_meta( $post_id , 'sumomemberships_userid' , true ) ;
                $string           = __( "Linked plan [sumo_linked_plan_name] has been [plan_status] upto parent plan duration" , 'sumomemberships' ) ;
                $event            = str_replace( array( '[sumo_linked_plan_name]' , '[plan_status]' ) , array( $linked_plan_name , $plan_status ) , $string ) ;
//                self::sumo_function_to_set_data_for_masterlog( $event , $username , $linked_plan_name ) ;
                SUMOMemberships_Admin_Meta_Boxes::add_membership_note( $event , $post_id , $username , strtolower( $plan_status ) , $plan_id ) ;
            }
        }
    }

    public function record_log_on_update_plan_order_status( $previous_plan_information , $existing_saved_plans , $unique_id , $post_id ) {
        $previous_value    = sumo_multidimensional_array_difference( $previous_plan_information , $existing_saved_plans ) ;
        $post_updated_plan = sumo_multidimensional_array_difference( $existing_saved_plans , $previous_plan_information ) ;

        if( ! empty( $previous_value ) && is_array( $previous_value ) ) {

            foreach( $previous_value as $each_unique_key => $each_val ) {

                if( is_array( $each_val ) && ! empty( $each_val ) ) {

                    if( isset( $each_val[ 'to_date' ] ) ) {

                        $previous_plan_expiry_date = $each_val[ 'to_date' ] ;

                        $new_plan_expiry_date = $post_updated_plan[ $each_unique_key ][ 'to_date' ] ;

                        $plan_id = $existing_saved_plans[ $each_unique_key ][ 'choose_plan' ] ;

                        $plan_status = $existing_saved_plans[ $each_unique_key ][ 'choose_status' ] ;

                        if( $plan_id != '' ) {

                            $planname = get_post_meta( $plan_id , 'sumomemberships_plan_name' , true ) ;
                            $username = get_post_meta( $post_id , 'sumomemberships_userid' , true ) ;

                            if( $previous_plan_expiry_date != '' && $new_plan_expiry_date != '' ) {
                                SUMOEmail_Settings_Tab::setup_schedule_before_expiry( $plan_id , $each_unique_key , $post_id , $new_plan_expiry_date ) ;
                            } elseif( $previous_plan_expiry_date != '' && $new_plan_expiry_date == '' ) {
                                $this->clear_expire_cron_and_linked_privilege_cron( $post_id , $each_unique_key , $username , $plan_id ) ;
                                SUMOEmail_Settings_Tab::clear_schedule_upon_status( $plan_id , $each_unique_key , $post_id ) ;
                            } else {
                                $this->set_expire_cron_and_linked_privilege_cron( $post_id , $each_unique_key , $username , $plan_id ) ;
                                SUMOEmail_Settings_Tab::setup_schedule_before_expiry( $plan_id , $each_unique_key , $post_id , $new_plan_expiry_date ) ;
                            }
                            $string = __( "Expiry has been updated for [sumo_plan_name] from [sumo_previous_plan_expiry_date] to [sumo_new_plan_expiry_date] based on the Order Status" , 'sumomemberships' ) ;
                            $event  = str_replace( '[sumo_plan_name]' , $planname , $string ) ;
                            $event  = str_replace( '[sumo_previous_plan_expiry_date]' , $previous_plan_expiry_date , $event ) ;
                            $event  = str_replace( '[sumo_new_plan_expiry_date]' , $new_plan_expiry_date , $event ) ;
                            self::sumo_function_to_set_data_for_masterlog( $event , $username , $planname ) ;
                            SUMOMemberships_Admin_Meta_Boxes::add_membership_note( $event , $post_id , $username , $plan_status , $plan_id ) ;
                        }
                    }
                }
            }
        }
    }

    public function clear_expire_cron_and_linked_privilege_cron( $postid , $unique_key , $member_id , $plan_id ) {

        sumo_clear_linked_plans_privilege_cron( $postid , $unique_key , $member_id , $plan_id ) ;

        wp_clear_scheduled_hook( 'sumo_memberships_process_plan_duration_validity' , array( ( int ) $member_id , ( int ) $plan_id ) ) ;
    }

    public function set_expire_cron_and_linked_privilege_cron( $postid , $unique_key , $member_id , $plan_id , $to_date = "" ) {

        $timestamp     = '' ;
        $plan_duration = sumo_get_membership_plan_duration( $plan_id ) ;

        if( $to_date != "" ) {
            $timestamp = strtotime( $to_date ) ;
        } else if( $plan_duration != "Indefinite" && $plan_duration != "" ) {

            $duration_value  = get_post_meta( $plan_id , 'sumomemberships_duration_value' , true ) ;
            $duration_period = get_post_meta( $plan_id , 'sumomemberships_duration_period' , true ) ;

            $timestamp = sumo_get_timestamp_to_schedule_cron( $duration_value , $duration_period , $plan_id , $member_id ) ;
        }

        if( $timestamp > 0 ) {
            wp_clear_scheduled_hook( 'sumo_memberships_process_plan_duration_validity' , array( ( int ) $member_id , ( int ) $plan_id ) ) ;

            if( ! wp_next_scheduled( 'sumo_memberships_process_plan_duration_validity' , array( ( int ) $member_id , ( int ) $plan_id ) ) ) {
                wp_schedule_single_event( $timestamp , 'sumo_memberships_process_plan_duration_validity' , array( ( int ) $member_id , ( int ) $plan_id ) ) ;

                $new_scheduled_link_plans  = sumo_get_schedule_link_plans( $plan_id , $member_id ) ;
                $new_privileged_link_plans = sumo_get_privileged_link_plans( $plan_id , $member_id ) ;

                if( $to_date != "" && isset( $_POST[ 'sumomember_plan_metas' ][ $unique_key ][ 'to_date' ] ) ) {
                    $_POST[ 'sumomember_plan_metas' ][ $unique_key ][ 'to_date' ]              = date( 'Y-m-d h:i:s' , $timestamp ) ;
                    $_POST[ 'sumomember_plan_metas' ][ $unique_key ][ 'link_plans' ]           = $new_privileged_link_plans ;
                    $_POST[ 'sumomember_plan_metas' ][ $unique_key ][ 'scheduled_link_plans' ] = $new_scheduled_link_plans ;
                } elseif( isset( $_POST[ 'sumomember_plan_meta' ][ $unique_key ][ 'to_date' ] ) ) {
                    $_POST[ 'sumomember_plan_meta' ][ $unique_key ][ 'to_date' ]              = date( 'Y-m-d h:i:s' , $timestamp ) ;
                    $_POST[ 'sumomember_plan_meta' ][ $unique_key ][ 'link_plans' ]           = $new_privileged_link_plans ;
                    $_POST[ 'sumomember_plan_meta' ][ $unique_key ][ 'scheduled_link_plans' ] = $new_scheduled_link_plans ;
                }
            }
        }
    }

    public function sumo_function_to_set_data_for_masterlog( $event , $username , $planname ) {
// Create post object
        $my_post = array(
            'post_status' => 'publish' ,
            'post_type'   => 'sumomem_masterlog'
                ) ;

// Insert the post into the database
        $postid = wp_insert_post( $my_post ) ;
        update_post_meta( $postid , 'sumo_username_for_masterlog' , $username ) ;
        update_post_meta( $postid , 'sumo_event_for_masterlog' , $event ) ;
        update_post_meta( $postid , 'sumo_planname_for_masterlog' , $planname ) ;
        update_post_meta( $postid , 'sumo_date_for_masterlog' , date( 'Y/m/d h:i:s' ) ) ;
    }

    public function sumo_function_to_get_event_for_masterlog( $status , $memberid ) {
        switch( $status ) {
            case 'active':
                __( 'Access to [plan_name] has been Provided.' , 'sumomemberships' ) ;
                break ;
            case 'paused':
                __( '-' , 'sumomemberships' ) ;
                break ;
            case 'cancelled':
                __( '-' , 'sumomemberships' ) ;
                break ;
            case 'expired':
                __( '-' , 'sumomemberships' ) ;
                break ;
        }
    }

}

new SUMOMemberships_APIs() ;
