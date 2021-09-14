<?php

function sumo_email_for_transfer_plans( $sumo_email_for , $to , $plan_receiver , $planname ) {
    if ( $sumo_email_for == 'sumo_ps_submitted' ) {
        if ( get_option( 'sumo_admin_member_transfer_request_submitted_email' ) == 'yes' ) {
            $subject = get_option( 'sumo_admin_member_transfer_request_submitted_email_subject' ) ;
            $subject = str_replace( '[site_title]' , get_bloginfo( 'name' ) , $subject ) ;
            $message = get_option( 'sumo_admin_member_transfer_request_submitted_email_message' ) ;
            $message = str_replace( '[plan_name]' , $planname , $message ) ;
            $message = str_replace( '[sumo_plan_sender]' , $to , $message ) ;
            $message = str_replace( '[sumo_plan_receiver]' , $plan_receiver , $message ) ;
            SUMOEmail_Settings_Tab::sumomemberships_send_mail( get_option( 'admin_email' ) , $subject , $message ) ;
        }
        if ( get_option( 'sumo_plan_sender_member_transfer_request_submitted_email' ) == 'yes' ) {
            $subject = get_option( 'sumo_plan_sender_member_transfer_request_submitted_email_subject' ) ;
            $subject = str_replace( '[site_title]' , get_bloginfo( 'name' ) , $subject ) ;
            $message = get_option( 'sumo_plan_sender_member_transfer_request_submitted_email_message' ) ;
            $message = str_replace( '[plan_name]' , $planname , $message ) ;
            $message = str_replace( '[sumo_plan_receiver]' , $plan_receiver , $message ) ;
            SUMOEmail_Settings_Tab::sumomemberships_send_mail( $to , $subject , $message ) ;
        }
    } elseif ( $sumo_email_for == 'sumo_ps_approved' ) {
        if ( get_option( 'sumo_admin_member_transfer_request_approved_email' ) == 'yes' ) {
            $subject = get_option( 'sumo_admin_member_transfer_request_approved_email_subject' ) ;
            $subject = str_replace( '[site_title]' , get_bloginfo( 'name' ) , $subject ) ;
            $message = get_option( 'sumo_admin_member_transfer_request_approved_email_message' ) ;
            $message = str_replace( '[plan_name]' , $planname , $message ) ;
            $message = str_replace( '[sumo_plan_sender]' , $to , $message ) ;
            $message = str_replace( '[sumo_plan_receiver]' , $plan_receiver , $message ) ;
            SUMOEmail_Settings_Tab::sumomemberships_send_mail( get_option( 'admin_email' ) , $subject , $message ) ;
        }
        if ( get_option( 'sumo_plan_sender_member_transfer_request_approved_email' ) == 'yes' ) {
            $subject = get_option( 'sumo_plan_sender_member_transfer_request_approved_email_subject' ) ;
            $subject = str_replace( '[site_title]' , get_bloginfo( 'name' ) , $subject ) ;
            $message = get_option( 'sumo_plan_sender_member_transfer_request_approved_email_message' ) ;
            $message = str_replace( '[plan_name]' , $planname , $message ) ;
            $message = str_replace( '[sumo_plan_receiver]' , $plan_receiver , $message ) ;
            SUMOEmail_Settings_Tab::sumomemberships_send_mail( $to , $subject , $message ) ;
        }
        if ( get_option( 'sumo_plan_receiver_member_transfer_request_approved_email' ) == 'yes' ) {
            $subject = get_option( 'sumo_plan_receiver_member_transfer_request_approved_email_subject' ) ;
            $subject = str_replace( '[site_title]' , get_bloginfo( 'name' ) , $subject ) ;
            $message = get_option( 'sumo_plan_receiver_member_transfer_request_approved_email_message' ) ;
            $message = str_replace( '[plan_name]' , $planname , $message ) ;
            $message = str_replace( '[sumo_plan_sender]' , $to , $message ) ;
            SUMOEmail_Settings_Tab::sumomemberships_send_mail( $plan_receiver , $subject , $message ) ;
        }
    } elseif ( $sumo_email_for == 'sumo_ps_rejected' ) {
        if ( get_option( 'sumo_admin_member_transfer_request_rejected_email' ) == 'yes' ) {
            $subject = get_option( 'sumo_admin_member_transfer_request_rejected_email_subject' ) ;
            $subject = str_replace( '[site_title]' , get_bloginfo( 'name' ) , $subject ) ;
            $message = get_option( 'sumo_admin_member_transfer_request_rejected_email_message' ) ;
            $message = str_replace( '[plan_name]' , $planname , $message ) ;
            $message = str_replace( '[sumo_plan_sender]' , $to , $message ) ;
            $message = str_replace( '[sumo_plan_receiver]' , $plan_receiver , $message ) ;
            SUMOEmail_Settings_Tab::sumomemberships_send_mail( get_option( 'admin_email' ) , $subject , $message ) ;
        }
        if ( get_option( 'sumo_plan_receiver_member_transfer_request_rejected_email' ) == 'yes' ) {
            $subject = get_option( 'sumo_plan_receiver_member_transfer_request_rejected_email_subject' ) ;
            $subject = str_replace( '[site_title]' , get_bloginfo( 'name' ) , $subject ) ;
            $message = get_option( 'sumo_plan_receiver_member_transfer_request_rejected_email_message' ) ;
            $message = str_replace( '[plan_name]' , $planname , $message ) ;
            $message = str_replace( '[sumo_plan_receiver]' , $plan_receiver , $message ) ;
            SUMOEmail_Settings_Tab::sumomemberships_send_mail( $to , $subject , $message ) ;
        }
    } elseif ( $sumo_email_for == 'sumo_plan_manual_transfer' ) {
        if ( get_option( 'sumo_admin_member_plan_transfered_manually_email' ) == 'yes' ) {
            $subject = get_option( 'sumo_admin_member_plan_transfered_manually_email_subject' ) ;
            $subject = str_replace( '[site_title]' , get_bloginfo( 'name' ) , $subject ) ;
            $message = get_option( 'sumo_admin_member_plan_transfered_manually_email_message' ) ;
            $message = str_replace( '[plan_name]' , $planname , $message ) ;
            $message = str_replace( '[sumo_plan_sender]' , $to , $message ) ;
            $message = str_replace( '[sumo_plan_receiver]' , $plan_receiver , $message ) ;
            SUMOEmail_Settings_Tab::sumomemberships_send_mail( get_option( 'admin_email' ) , $subject , $message ) ;
        }
        if ( get_option( 'sumo_plan_sender_member_plan_transfered_manually_email' ) == 'yes' ) {
            $subject = get_option( 'sumo_plan_sender_member_plan_transfered_manually_email_subject' ) ;
            $subject = str_replace( '[site_title]' , get_bloginfo( 'name' ) , $subject ) ;
            $message = get_option( 'sumo_plan_sender_member_plan_transfered_manually_email_message' ) ;
            $message = str_replace( '[plan_name]' , $planname , $message ) ;
            $message = str_replace( '[sumo_plan_receiver]' , $plan_receiver , $message ) ;
            SUMOEmail_Settings_Tab::sumomemberships_send_mail( $to , $subject , $message ) ;
        }
        if ( get_option( 'sumo_plan_receiver_member_plan_transfered_manually_email' ) == 'yes' ) {
            $subject = get_option( 'sumo_plan_receiver_member_plan_transfered_manually_email_subject' ) ;
            $subject = str_replace( '[site_title]' , get_bloginfo( 'name' ) , $subject ) ;
            $message = get_option( 'sumo_plan_receiver_member_plan_transfered_manually_email_message' ) ;
            $message = str_replace( '[plan_name]' , $planname , $message ) ;
            $message = str_replace( '[sumo_plan_sender]' , $to , $message ) ;
            SUMOEmail_Settings_Tab::sumomemberships_send_mail( $plan_receiver , $subject , $message ) ;
        }
    }
}
