<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
add_filter( 'wp_privacy_personal_data_exporters' , 'show_membership_plans_action' ) ;
add_filter( 'wp_privacy_personal_data_erasers' , 'remove_membership_plans_action' ) ;

function show_membership_plans_action( $data ) {
    $data[ 'sumo-memberships-user-data' ] = array ( 'exporter_friendly_name' => 'SUMO Membership Customer Data' , 'callback' => 'sumo_membership_customer_personal_data_exporter' ) ;
    $data[ 'sumo-memberships' ]           = array ( 'exporter_friendly_name' => 'SUMO Membership Plans' , 'callback' => 'sumo_membership_plans_personal_data_exporter' ) ;
    return $data ;
}

function remove_membership_plans_action( $data ) {
    $data[ 'sumo-memberships' ] = array ( 'eraser_friendly_name' => 'SUMO Membership Plans' , 'callback' => 'sumo_membership_plans_personal_data_eraser' ) ;
    return $data ;
}

function sumo_membership_customer_personal_data_exporter( $email_address ) {
    $email_address_trimmed = trim( $email_address ) ;

    $data_to_export = array () ;

    $user = get_user_by( 'email' , $email_address_trimmed ) ;
    if ( ! $user ) {
        return array (
            'data' => array () ,
            'done' => true ,
                ) ;
    }

    $post_data_to_export = array (
        array ( 'name' => __( 'User Id' , 'sumomemberships' ) , 'value' => $user->ID ) ,
            ) ;

    $data_to_export[] = array (
        'group_id'    => 'sumo-memberships-user-data' ,
        'group_label' => __( 'Membership User Data' , 'sumomemberships' ) ,
        'item_id'     => "post-{$user->ID}" ,
        'data'        => $post_data_to_export ,
            ) ;

    return array (
        'data' => $data_to_export ,
        'done' => true ,
            ) ;
}

function sumo_membership_plans_personal_data_exporter( $email_address ) {
    $email_address_trimmed = trim( $email_address ) ;

    $data_to_export = array () ;

    $user = get_user_by( 'email' , $email_address_trimmed ) ;
    if ( ! $user ) {
        return array (
            'data' => array () ,
            'done' => true ,
                ) ;
    }
    $member_id   = sumo_get_member_post_id( $user->ID ) ;
    $new_getdata = get_post_meta( $member_id , 'sumomemberships_saved_plans' , true ) ;
    if ( is_array( $new_getdata ) && ! empty( $new_getdata ) ) {
        foreach ( $new_getdata as $key => $new_eachdata ) {
            $plan_id           = $new_eachdata[ 'choose_plan' ] ? $new_eachdata[ 'choose_plan' ] : '' ;
            $status            = $new_eachdata[ 'choose_status' ] ? $new_eachdata[ 'choose_status' ] : '' ;
            $plan_created_date = $new_eachdata[ 'from_date' ] ? $new_eachdata[ 'from_date' ] : '' ;
            $plan_expiry_date  = $new_eachdata[ 'to_date' ] ? $new_eachdata[ 'to_date' ] : '--' ;
            if ( $status == 'active' ) {
                $plan_status = __( 'Active' , 'sumomemberships' ) ;
            } elseif ( $status == 'paused' ) {
                $plan_status = __( 'Paused' , 'sumomemberships' ) ;
            } elseif ( $status == 'expired' ) {
                $plan_status = __( 'Expired' , 'sumomemberships' ) ;
            } else {
                $plan_status = __( 'Cancelled' , 'sumomemberships' ) ;
            }
            $post_data_to_export = array (
                array ( 'name' => __( 'Plan name' , 'sumomemberships' ) , 'value' => get_the_title( $plan_id ) ) ,
                array ( 'name' => __( 'Created on' , 'sumomemberships' ) , 'value' => $plan_created_date ) ,
                array ( 'name' => __( 'Expiry date' , 'sumomemberships' ) , 'value' => $plan_expiry_date ) ,
                array ( 'name' => __( 'Staus' , 'sumomemberships' ) , 'value' => $plan_status ) ,
                    ) ;

            $data_to_export[] = array (
                'group_id'    => 'sumo-memberships' ,
                'group_label' => __( 'Membership Plans' , 'sumomemberships' ) ,
                'item_id'     => "post-{$plan_id}" ,
                'data'        => $post_data_to_export ,
                    ) ;
        }
    }

    return array (
        'data' => $data_to_export ,
        'done' => true ,
            ) ;
}

function sumo_membership_plans_personal_data_eraser( $email_address ) {
    $user     = get_user_by( 'email' , $email_address ) ; // Check if user has an ID in the DB to load stored personal data.
    $response = array (
        'items_removed'  => false ,
        'items_retained' => false ,
        'messages'       => array () ,
        'done'           => true ,
            ) ;
    if ( $user ) {
        $member_id = sumo_get_member_post_id( $user->ID ) ;
        if ( $member_id ) {
            $new_getdata = get_post_meta( $member_id , 'sumomemberships_saved_plans' , true ) ;
            if ( is_array( $new_getdata ) && ! empty( $new_getdata ) ) {
                foreach ( $new_getdata as $key => $new_eachdata ) {
                    $plan_id                     = $new_eachdata[ 'choose_plan' ] ? $new_eachdata[ 'choose_plan' ] : '' ;
                    $response[ 'messages' ][]    = sprintf( __( 'Membership plan %s removed.' , 'sumomemberships' ) , get_the_title( $plan_id ) ) ;
                    $response[ 'items_removed' ] = true ;
                }
            } else {
                $response[ 'done' ] = true ;
            }
            wp_delete_post( $member_id ) ;
        } else {
            $response[ 'done' ] = true ;
        }
    }

    return $response ;
}
