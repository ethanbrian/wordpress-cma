<?php

//Get Admin Published Plans.

if ( ! function_exists( 'sumo_membership_check_is_array' ) ) {

    function sumo_membership_check_is_array( $data ) {
        return ( is_array( $data ) && ! empty( $data ) ) ;
    }

}

function sumo_get_membership_levels() {

    $membership_levels = array() ;

    $args = array(
        'post_type'      => 'sumomembershipplans' ,
        'post_status'    => 'publish' ,
        'posts_per_page' => -1
            ) ;

    $membership_level_posts = get_posts( $args ) ;

    foreach( $membership_level_posts as $each_post ) {

        $postid                       = $each_post->ID ;
        $membership_levels[ $postid ] = get_post_meta( $postid , 'sumomemberships_plan_name' , true ) ;
    }

    return $membership_levels ;
}

if( ! function_exists( 'sumomemberships_customize_array_position' ) ) {

    function sumomemberships_customize_array_position( $array , $key , $new_value ) {
        $keys  = array_keys( $array ) ;
        $index = array_search( $key , $keys ) ;
        $pos   = false === $index ? count( $array ) : $index + 1 ;

        $new_value = is_array( $new_value ) ? $new_value : array( $new_value ) ;

        return array_merge( array_slice( $array , 0 , $pos ) , $new_value , array_slice( $array , $pos ) ) ;
    }

}

function sumo_is_subcription_enabled( $product_id , $order_id = 0 ) {

    $order                 = new WC_Order( $order_id ) ;
    $user_id               = sumo_get_customer_id_from_order( $order ) ;
    $is_order_subscription = false ;

    //For Subscription Compatibility
    $parent = sumo_get_parent_order_id( $order ) ;
    if( $parent == 0 ) {
        $is_order_subscription = get_user_meta( $user_id , 'sumo_is_order_based_subscriptions' , true ) == 'yes' ;
    } else {
        $order_id              = $parent > 0 ? $parent : $order_id ;
        $is_order_subscription = get_post_meta( $order_id , 'sumo_is_order_based_subscriptions' , true ) == 'yes' ;
    }

    $is_product_susbcription = get_post_meta( $product_id , 'sumo_susbcription_status' , true ) == "1" ;

    if( class_exists( 'SUMOSubscriptions' ) ) {
        if( $is_order_subscription || $is_product_susbcription ) {
            return true ;
        }
    }

    return false ;
}

function sumo_get_available_cpt_to_restrict() {

    $post_types    = get_post_types() ;
    $available_cpt = array() ;

    foreach( $post_types as $type ) {
        if( get_option( "sumomemberships_$type" ) == "yes" ) {
            $available_cpt[] = $type ;
        }
    }
    return $available_cpt ;
}

function sumo_get_third_parties_cpt_exists() {
    $post_types    = get_post_types() ;
    $available_cpt = array() ;

    foreach( $post_types as $type ) {
        if(
                $type != 'shop_order' &&
                $type != 'shop_coupon' &&
                $type != 'shop_order_refund' &&
                $type != 'shop_webhook' &&
                $type != 'post' &&
                $type != 'page' &&
                $type != 'attachment' &&
                $type != 'revision' &&
                $type != 'nav_menu_item' &&
                $type != 'product' &&
                $type != 'product_variation' &&
                $type != 'sumomembershipplans' &&
                $type != 'sumomembers' &&
                $type != 'sumomem_masterlog' &&
                $type != 'sumosubscriptions' &&
                $type != 'sumomasterlog'
        ) {
            $available_cpt[] = $type ;
        }
    }

    return $available_cpt ;
}

function sumo_is_third_parties_cpt_exists() {

    $available_cpt = sumo_get_third_parties_cpt_exists() ;

    $is_exists = ! empty( $available_cpt ) ? true : false ;

    return $is_exists ;
}

function sumo_get_member_post_id( $member_id ) {
    
    if(!empty(SUMOMemberships::$member_post_id[$member_id])){
        return SUMOMemberships::$member_post_id[$member_id];
    }

    $args = array(
        'post_type'  => 'sumomembers' ,
        'meta_query' => array(
            array(
                'key'     => 'sumomemberships_userid' , 'value'   => array( $member_id ) ,
                'compare' => 'IN' )
        ) ) ;

    $get_posts = get_posts( $args ) ;

    $id = isset( $get_posts[ 0 ]->ID ) ? $get_posts[ 0 ]->ID : 0 ;
    
    SUMOMemberships::$member_post_id[$member_id] = $id;

    return $id ;
}

function sumo_get_member_plan_purchased_date( $member_post_id , $plan_id , $get_timestamp = false ) {

    $saved_plans = ( array ) get_post_meta( $member_post_id , 'sumomemberships_saved_plans' , true ) ;

    $unique_key = sumo_get_plan_key( $member_post_id , $plan_id ) ;

    $plan_since_date = isset( $saved_plans[ $unique_key ][ 'from_date' ] ) ? $saved_plans[ $unique_key ][ 'from_date' ] : '' ;

    if( $plan_since_date != '' && $get_timestamp ) {

        return strtotime( $plan_since_date ) ;
    }
    return $plan_since_date ;
}

function sumo_get_member_purchased_plans_list( $member_post_id , $plan_status = 'active' ) {

    $arr                  = array() ;
    $get_associated_plans = ( array ) get_post_meta( $member_post_id , 'sumomemberships_saved_plans' , true ) ;

    foreach( $get_associated_plans as $each_plan_key => $each_plan ) {
        if( isset( $each_plan[ 'choose_status' ] ) && $each_plan[ 'choose_status' ] == $plan_status && $each_plan[ 'choose_plan' ] > 0 ) {
            $arr[ $each_plan_key ] = $each_plan[ 'choose_plan' ] ;
        }
    }
    return $arr ;
}

function sumo_get_member_purchased_all_plans_list( $member_post_id ) {

    $arr                  = array() ;
    $get_associated_plans = ( array ) get_post_meta( $member_post_id , 'sumomemberships_saved_plans' , true ) ;

    foreach( $get_associated_plans as $each_plan_key => $each_plan ) {
        if( $each_plan[ 'choose_plan' ] > 0 ) {
            $arr[ $each_plan_key ] = $each_plan[ 'choose_plan' ] ;
        }
    }
    return $arr ;
}

function sumo_get_plan_key( $member_post_id , $plan_id , $get_active_plan_key = false ) {

    $saved_plans = ( array ) get_post_meta( $member_post_id , 'sumomemberships_saved_plans' , true ) ;

    foreach( $saved_plans as $each_plan_key => $each_plan ) {
        if( $get_active_plan_key && isset( $each_plan[ 'choose_plan' ] ) && $each_plan[ 'choose_plan' ] == $plan_id && ($each_plan[ 'choose_status' ] == 'active' || $each_plan[ 'choose_status' ] == 'paused') ) {
            return $each_plan_key ;
        } else if( isset( $each_plan[ 'choose_plan' ] ) && $each_plan[ 'choose_plan' ] == $plan_id ) {
            return $each_plan_key ;
        }
    }
    return false ;
}

function sumo_get_plan_id_by_plan_name( $plan_name ) {
    $args             = array(
        'post_type'      => 'sumomembershipplans' ,
        'post_status'    => 'publish' ,
        'posts_per_page' => -1 ,
        'fields'         => 'ids' ,
        'meta_key'       => 'sumomemberships_plan_name' ,
        'meta_value'     => $plan_name
            ) ;
    $membership_plans = get_posts( $args ) ;
    $plan_id          = isset( $membership_plans[ 0 ] ) ? $membership_plans[ 0 ] : "" ;
    return $plan_id ;
}

function sumo_is_member_has_active_r_paused_plan( $member_post_id ) {

    $saved_plans = ( array ) get_post_meta( $member_post_id , 'sumomemberships_saved_plans' , true ) ;

    foreach( $saved_plans as $each_plan_key => $each_plan ) {
        if( isset( $each_plan[ 'choose_status' ] ) && $each_plan[ 'choose_plan' ] > 0 && ($each_plan[ 'choose_status' ] == 'active' || $each_plan[ 'choose_status' ] == 'paused') ) {
            return true ;
        }
    }
    return false ;
}

function sumo_is_plan_active( $plan_id , $member_post_id ) {

    $saved_plans = ( array ) get_post_meta( $member_post_id , 'sumomemberships_saved_plans' , true ) ;

    foreach( $saved_plans as $each_plan_key => $each_plan ) {
        if( isset( $each_plan[ 'choose_status' ] ) && $each_plan[ 'choose_plan' ] == $plan_id && $each_plan[ 'choose_status' ] == 'active' ) {
            return true ;
        }
    }
    return false ;
}

function sumo_plan_is_already_had( $plan_id , $member_post_id ) {
    $saved_plans = ( array ) get_post_meta( $member_post_id , 'sumomemberships_saved_plans' , true ) ;
    if( ! empty( $saved_plans ) ) {
        foreach( $saved_plans as $each_plan_key => $each_plan ) {
            if( $each_plan[ 'choose_plan' ] == $plan_id && $each_plan[ 'choose_status' ] != 'expired' ) {
                return false ;
            }
        }
    }
    return true ;
}

function sumo_clear_linked_plans_privilege_cron( $postid , $unique_key , $member_id , $plan_id ) {

    $saved_plans = get_post_meta( $postid , 'sumomemberships_saved_plans' , true ) ;

    $scheduled_link_plans = isset( $saved_plans[ $unique_key ][ 'scheduled_link_plans' ] ) ? $saved_plans[ $unique_key ][ 'scheduled_link_plans' ] : array() ;

    if( is_array( $scheduled_link_plans ) && ! empty( $scheduled_link_plans ) ) {
        foreach( $scheduled_link_plans as $each_link_plan_id ) {

            $is_linked_plan_privilege_set = wp_next_scheduled( 'sumo_memberships_process_linked_plan_privilege' , array( ( int ) $member_id , ( int ) $each_link_plan_id , ( int ) $plan_id ) ) ;

            if( $is_linked_plan_privilege_set > 0 ) {
                wp_clear_scheduled_hook( 'sumo_memberships_process_linked_plan_privilege' , array( ( int ) $member_id , ( int ) $each_link_plan_id , ( int ) $plan_id ) ) ;
            }
        }
    }
}

function sumo_is_member_purchased_any_plan( $member_post_id ) {

    $boolean = SUMOMemberships_Account_Page::check_is_data_available( $member_post_id ) ;

    return $boolean ;
}

function sumo_get_available_privileged_linked_plans( $member_post_id , $plan_status = 'active' ) {

    $saved_plans          = ( array ) get_post_meta( $member_post_id , 'sumomemberships_saved_plans' , true ) ;
    $member_id            = get_post_meta( $member_post_id , 'sumomemberships_userid' , true ) ;
    $current_plans_linked = array() ;
    // currently linked plans
    if( get_option( 'sumo_allow_clp_access_to_oldmembers' ) == 'yes' ) {
        foreach( $saved_plans as $each_plan ) {

            if( isset( $each_plan[ 'choose_plan' ] ) && isset( $each_plan[ 'choose_status' ] ) && $each_plan[ 'choose_status' ] == $plan_status ) {

                $plan_id = $each_plan[ 'choose_plan' ] ;


                $check_user_to_access_linked_plans = sm_check_user_purchase_history( $plan_id , $member_id ) ;

                if( sumo_is_global_plan_status_active( $plan_id ) && $check_user_to_access_linked_plans ) {
                    $row_counts = get_option( 'sumomemberships_no_of_links_added' . $plan_id ) ;
                    if( $row_counts > 0 && sumo_is_membership_post( $plan_id ) ) {
                        for( $i = 1 ; $i <= $row_counts ; $i ++ ) {
                            $linked_plan_id = get_post_meta( $plan_id , 'sumomemberships_plan_to_link_with' . $i , true ) ;
                            if( sumo_is_global_plan_status_active( $linked_plan_id ) ) {
                                $current_plans_linked[] = $linked_plan_id ;
                            }
                        }
                    }
                }
            }
        }
    }
    // plans linked in member post id
    $plans_linked_in_member = sumo_get_available_linked_plans( $member_post_id ) ;
    $privileged_plans       = array_merge( $current_plans_linked , $plans_linked_in_member ) ;
    return $privileged_plans ;
}

function sumo_get_available_linked_plans( $member_post_id , $plan_status = 'active' ) {

    $saved_plans      = ( array ) get_post_meta( $member_post_id , 'sumomemberships_saved_plans' , true ) ;
    $privileged_plans = array() ;
    $member_id        = get_post_meta( $member_post_id , 'sumomemberships_userid' , true ) ;

    foreach( $saved_plans as $each_plan ) {

        if( isset( $each_plan[ 'link_plans' ] ) && is_array( $each_plan[ 'link_plans' ] ) && isset( $each_plan[ 'choose_status' ] ) && $each_plan[ 'choose_status' ] == $plan_status ) {

            $link_plans = $each_plan[ 'link_plans' ] ;

            foreach( $link_plans as $each_link_plan_id ) {

                if( sumo_is_global_plan_status_active( $each_link_plan_id ) && sm_check_user_purchase_history( $each_plan[ 'choose_plan' ] , $member_id ) ) {
                    $privileged_plans[] = $each_link_plan_id ;
                }
            }
        }
    }
    return $privileged_plans ;
}

function sumo_get_privileged_link_plans( $plan_id , $member_id ) {

    $link_plans = sumo_get_linking_additional_plans( $plan_id , $member_id ) ;

    $get_privileged_link_plans = array() ;

    if( ! empty( $link_plans ) && is_array( $link_plans ) ) {
        foreach( $link_plans as $each_link_plan ) {

            if( isset( $each_link_plan[ 'schedule_type' ] ) && $each_link_plan[ 'schedule_type' ] != "scheduled" ) {
                //Immediately set privilege to linked plan. Since its duration set as Infinite
                $get_privileged_link_plans[] = ( int ) $each_link_plan[ 'linking_plan' ] ;
            }
        }
    }

    return $get_privileged_link_plans ;
}

function sumo_get_schedule_link_plans( $plan_id , $member_id ) {

    $link_plans = sumo_get_linking_additional_plans( $plan_id , $member_id ) ;

    $get_schedule_link_plans = array() ;

    if( ! empty( $link_plans ) && is_array( $link_plans ) ) {
        foreach( $link_plans as $each_link_plan ) {

            if( isset( $each_link_plan[ 'schedule_type' ] ) ) {

                if( $each_link_plan[ 'schedule_type' ] == "scheduled" ) {

                    $timestamp = sumo_get_timestamp_to_schedule_cron( $each_link_plan[ 'duration_value' ] , $each_link_plan[ 'duration_period' ] ) ;
                    //Provide privilege to linked plan after the certain period of time
                    if( $timestamp > 0 ) {
                        if( ! wp_next_scheduled( 'sumo_memberships_process_linked_plan_privilege' , array( ( int ) $member_id , ( int ) $each_link_plan[ 'linking_plan' ] , ( int ) $plan_id ) ) ) {
                            wp_schedule_single_event( $timestamp , 'sumo_memberships_process_linked_plan_privilege' , array( ( int ) $member_id , ( int ) $each_link_plan[ 'linking_plan' ] , ( int ) $plan_id ) ) ;

                            $get_schedule_link_plans[] = ( int ) $each_link_plan[ 'linking_plan' ] ;
                        }
                    }
                }
            }
        }
    }
    return $get_schedule_link_plans ;
}

function sumo_multidimensional_array_difference( $array1 , $array2 ) {
    $newdata = array() ;
    if( is_array( $array1 ) && is_array( $array2 ) ) {
        foreach( $array1 as $key => $value ) {
            if( isset( $array1[ $key ] ) && isset( $array2[ $key ] ) ) {
                $newdata[ $key ] = @array_diff_assoc( $array1[ $key ] , $array2[ $key ] ) ;
            }
        }
    }
    return $newdata ;
}

function sumo_get_plan_schedules_from_post_page_product( $each_rule ) {

    $timestamp = '' ;

    if( isset( $each_rule[ 'schedule_type' ] ) && $each_rule[ 'schedule_type' ] == 'scheduled' ) {

        $duration_value  = $each_rule[ 'duration_value' ] ;
        $duration_period = $each_rule[ 'duration_period' ] ;

        $duration_length = sumo_conversion_for_plan_duration( $duration_value , $duration_period ) ;

        if( $duration_length > 0 ) {
            $timestamp = ( int ) $duration_length ;
        }
    }
    return $timestamp ;
}

function sumo_get_plan_key_from_member_order( $member_post_id , $order_id ) {

    $saved_plans = ( array ) get_post_meta( $member_post_id , 'sumomemberships_saved_plans' , true ) ;

    foreach( $saved_plans as $each_plan_key => $each_plan ) {

        if( isset( $each_plan[ 'order_id' ] ) && is_array( $each_plan[ 'order_id' ] ) && in_array( $order_id , $each_plan[ 'order_id' ] ) ) {
            return $each_plan_key ;
        }
    }
    return false ;
}

//Get Specific Membership Plan Duration.

function sumo_get_membership_plan_duration( $plan_id ) {

    if( sumo_is_membership_post( $plan_id ) ) {

        $duration_type   = get_post_meta( $plan_id , 'sumomemberships_duration_type' , true ) ;
        $duration_value  = get_post_meta( $plan_id , 'sumomemberships_duration_value' , true ) ;
        $duration_period = get_post_meta( $plan_id , 'sumomemberships_duration_period' , true ) ;

        if( $duration_type == 'limited_duration' ) {
            return $duration_value . ' ' . $duration_period ;
        }
        return 'Indefinite' ;
    }
    return '' ;
}

//Plan Duration Conversion in Seconds.

function sumo_conversion_for_plan_duration( $duration_value , $duration_period ) {

    $value  = ( int ) $duration_value ;
    $period = $duration_period ;

    if( $period == 'days' || $period == 'day' ) {
        return 86400 * $value ;
    } elseif( $period == 'weeks' || $period == 'week' ) {
        return 604800 * $value ;
    } elseif( $period == 'months' || $period == 'month' ) {
        return 2629743 * $value ;
    } else {
        return 31556926 * $value ;
    }
}

//Get Timestamp to schedule Cron Event.

function sumo_get_timestamp_to_schedule_cron( $duration_value , $duration_period , $plan_id = 0 , $member_id = 0 , $from_time = '' ) {
    if( $from_time ) {
        $this_time = strtotime( $from_time ) ;
    } else {
        $this_time = time() ;
    }

    $existing_timestamp = wp_next_scheduled( 'sumo_memberships_process_plan_duration_validity' , array( ( int ) $member_id , ( int ) $plan_id ) ) ;

    $duration_length = sumo_conversion_for_plan_duration( $duration_value , $duration_period ) ;
    //New Timestamp for the member respect to the Plan
    $timestamp       = $duration_length > 0 ? ( int ) ($this_time + $duration_length) : $this_time ;
    //Cron already set for this plan, so the Existing Timestamp extends with the New Timestamp
    if( $existing_timestamp > 0 ) {

        $timestamp = $duration_length > 0 ? ( int ) ($existing_timestamp + $duration_length) : $existing_timestamp ;
    }
    //Timestamp to schedule Next Cron Job
    return ( int ) $timestamp ;
}

//Get current Plan Duration for the Member

function sumo_get_plan_duration( $member_id , $plan_id ) {

    $timestamp = wp_next_scheduled( 'sumo_memberships_process_plan_duration_validity' , array( ( int ) $member_id , ( int ) $plan_id ) ) ;

    if( $timestamp > 0 ) {

        return $timestamp ;
    }
    return '' ;
}

function sumo_get_plan_id_from_slug( $this_slug ) {

    $args = array(
        'post_type'      => 'sumomembershipplans' ,
        'post_status'    => 'publish' ,
        'posts_per_page' => -1
            ) ;

    $membership_level_plans = get_posts( $args ) ;

    foreach( $membership_level_plans as $each_plan ) {

        $plan_id              = $each_plan->ID ;
        $membership_plan_slug = get_post_meta( $plan_id , 'sumomemberships_plan_slug' , true ) ;

        if( $membership_plan_slug == $this_slug ) {
            return $plan_id ;
        }
    }
    return "" ;
}

//Get Members Who are Purchased the Plan.

function sumo_get_plan_purchased_members( $plan_id , $count = false ) {

    $plan_keys = array() ;

    $args = array(
        'post_type'      => "sumomembers" ,
        'post_status'    => 'publish' ,
        'posts_per_page' => -1
            ) ;

    $posts = get_posts( $args ) ;

    if( is_array( $posts ) && ! empty( $posts ) ) {

        foreach( $posts as $each_post ) {

            $post_id = $each_post->ID ;

            $plan_key = sumo_get_plan_key( $post_id , $plan_id , true ) ;

            if( $plan_key != '' ) {
                $plan_keys[ $post_id ] = $plan_key ;
            }
        }
    }

    $values = $count ? count( $plan_keys ) : $plan_keys ;

    return $values ;
}

function sumo_pause_r_disable_plan( $plan_id , $member_post_id , $member_id ) {

    $unique_id = sumo_get_plan_key( $member_post_id , $plan_id ) ;

    $saved_plans = get_post_meta( $member_post_id , 'sumomemberships_saved_plans' , true ) ;

    if( isset( $saved_plans[ $unique_id ][ 'choose_status' ] ) && $saved_plans[ $unique_id ][ 'associated_subsc_id' ] == '' ) {

        $saved_plans[ $unique_id ][ 'choose_status' ] = 'paused' ;

        $expire_duration = $saved_plans[ $unique_id ][ 'to_date' ] ;

        if( $expire_duration != '' ) {

            $timestamp = strtotime( $expire_duration ) > 0 ? strtotime( $expire_duration ) - time() : '' ;

            $saved_plans[ $unique_id ][ 'available_duration' ] = $timestamp ;
            $saved_plans[ $unique_id ][ 'to_date' ]            = '--' ;
        }
        wp_clear_scheduled_hook( 'sumo_memberships_process_plan_duration_validity' , array( ( int ) $member_id , ( int ) $plan_id ) ) ;

        sumo_clear_linked_plans_privilege_cron( $member_post_id , $unique_id , $member_id , $plan_id ) ;

        $saved_plans[ $unique_id ][ 'link_plans' ]           = array() ;
        $saved_plans[ $unique_id ][ 'scheduled_link_plans' ] = array() ;
        do_action( 'sumomemberships_plan_status_changed' , $member_post_id , $plan_id , $saved_plans[ $unique_id ][ 'choose_status' ] ) ;
        update_post_meta( $member_post_id , 'sumomemberships_saved_plans' , $saved_plans ) ;
    }
}

function sumo_resume_plan_after_plan_paused_r_disabled( $plan_id , $member_post_id , $member_id ) {

    $unique_id = sumo_get_plan_key( $member_post_id , $plan_id ) ;

    $saved_plans = get_post_meta( $member_post_id , 'sumomemberships_saved_plans' , true ) ;

    if( isset( $saved_plans[ $unique_id ][ 'choose_status' ] ) && $saved_plans[ $unique_id ][ 'associated_subsc_id' ] == '' ) {

        $saved_plans[ $unique_id ][ 'choose_status' ] = 'active' ;

        $available_duration = $saved_plans[ $unique_id ][ 'available_duration' ] ;

        if( $available_duration > 0 ) {
            $timestamp = ( int ) $available_duration + time() ;

            if( $timestamp > 0 ) {
                if( ! wp_next_scheduled( 'sumo_memberships_process_plan_duration_validity' , array( ( int ) $member_id , ( int ) $plan_id ) ) ) {
                    wp_schedule_single_event( $timestamp , 'sumo_memberships_process_plan_duration_validity' , array( ( int ) $member_id , ( int ) $plan_id ) ) ;

                    $saved_plans[ $unique_id ][ 'to_date' ]            = date( 'Y-m-d h:i:s' , $timestamp ) ;
                    $saved_plans[ $unique_id ][ 'available_duration' ] = "" ;
                }
            }
        } else {
            $saved_plans[ $unique_id ][ 'to_date' ] = '' ;
        }

        $new_privileged_link_plans = sumo_get_privileged_link_plans( $plan_id , $member_id ) ;
        $new_scheduled_link_plans  = sumo_get_schedule_link_plans( $plan_id , $member_id ) ;

        $saved_plans[ $unique_id ][ 'link_plans' ]           = $new_privileged_link_plans ;
        $saved_plans[ $unique_id ][ 'scheduled_link_plans' ] = $new_scheduled_link_plans ;

        $plan_slug = get_post_meta( $plan_id , 'sumomemberships_plan_slug' , true ) ;

        $saved_plans[ $unique_id ][ 'plan_slug' ] = $plan_slug ;
        do_action( 'sumomemberships_plan_status_changed' , $member_post_id , $plan_id , $saved_plans[ $unique_id ][ 'choose_status' ] ) ;
        update_post_meta( $member_post_id , 'sumomemberships_saved_plans' , $saved_plans ) ;
    }
}

//Use On Cart/Checkout Process. Since it would dynamically vary.

function sumo_is_membership_product( $this_product_id ) {

    foreach( sumo_get_membership_levels() as $plan_id => $plan_name ) {
        $membership_product = get_post_meta( $plan_id , 'sumomemberships_plan_associated_product' , true ) ;

        if( $this_product_id == $membership_product ) {
            return true ;
        }
    }
    return false ;
}

//Get the Membership Linked/Associated Product Plan IDs.

function sumo_get_product_associated_plan_ids( $this_product_id ) {

    $plan_ids = array() ;

    $membership_levels = sumo_get_membership_levels() ;

    foreach( $membership_levels as $plan_id => $plan_name ) {
        $membership_product = get_post_meta( $plan_id , 'sumomemberships_plan_associated_product' , true ) ;

        if( $this_product_id == $membership_product ) {
            $plan_ids[] = $plan_id ;
        }
    }
    return $plan_ids ;
}

//Get Linked Additional Plans Added by the Admin for Every Membership Posts

function sumo_get_linking_additional_plans( $plan_id , $member_id ) {

    $linked_plans_arg = array() ;

    $row_counts = get_option( 'sumomemberships_no_of_links_added' . $plan_id ) ;

    if( $row_counts > 0 && sumo_is_membership_post( $plan_id ) ) {

        for( $i = 1 ; $i <= $row_counts ; $i ++ ) {

            $plan_linking    = get_post_meta( $plan_id , 'sumomemberships_plan_to_link_with' . $i , true ) ;
            $schedule_type   = get_post_meta( $plan_id , 'sumomemberships_linking_plan_schedule_type' . $i , true ) ;
            $duration_value  = get_post_meta( $plan_id , 'sumomemberships_linking_plan_duration_value' . $i , true ) ;
            $duration_period = get_post_meta( $plan_id , 'sumomemberships_linking_plan_duration_period' . $i , true ) ;

            if( $plan_linking && $schedule_type ) {

                $linked_plans_arg[] = array(
                    'linking_plan'    => $plan_linking ,
                    'schedule_type'   => $schedule_type ,
                    'duration_value'  => $duration_value ,
                    'duration_period' => $duration_period
                        ) ;
            }
        }
    }
    return $linked_plans_arg ;
}

function sm_check_user_purchase_history( $plan_id , $user_id ) {
    $user_purchase_history_for_linking_plans = get_post_meta( $plan_id , 'user_purchase_history_for_linking_plans' , true ) ;
    $purchase_period                         = get_post_meta( $plan_id , 'sm_user_purchase_history_period' , true ) ;
    $from_date                               = get_post_meta( $plan_id , 'sm_uph_from_period' , true ) ;
    $to_date                                 = get_post_meta( $plan_id , 'sm_uph_to_period' , true ) ;
    $successfull_orders                      = get_post_meta( $plan_id , 'sm_no_of_orders_placed' , true ) ;
    $minamtspent                             = get_post_meta( $plan_id , 'sm_total_amount_spent_in_site' , true ) ;

    if( $user_purchase_history_for_linking_plans == '' ) {
        $return = true ;
    } elseif( $user_purchase_history_for_linking_plans == '1' ) {
        $no_of_orders_placed = sm_get_no_of_orders_placed( $user_id , $purchase_period , $from_date , $to_date ) ;
        if( $no_of_orders_placed >= ( int ) $successfull_orders ) {
            $return = true ;
        } else {
            $return = false ;
        }
    } elseif( $user_purchase_history_for_linking_plans == '2' ) {
        $amount_spented = ( float ) sm_get_customer_total_spent( $user_id , $purchase_period , $from_date , $to_date ) ;
        if( $amount_spented >= ( float ) $minamtspent ) {
            $return = true ;
        } else {
            $return = false ;
        }
    }
    return $return ;
}

function sm_get_no_of_orders_placed( $user_id , $purchase_period , $from_date , $to_date ) {
    global $wpdb ;
    $query    = "" ;
    $fromdate = $from_date != "" ? $from_date : "" ;
    $todate   = $to_date != "" ? $to_date : date_i18n( 'Y-m-d' ) ;
    if( $purchase_period != '' ) {
        $from_query = $fromdate != "" ? "AND posts.post_date >= '" . $fromdate . " 00:00:01'" : "" ;
        $query      = $from_query . "AND posts.post_date <= '" . $todate . " 23:59:59' " ;
    }
    $count = $wpdb->get_var( "SELECT COUNT(*)
                FROM $wpdb->posts as posts

                LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id

                WHERE   meta.meta_key       = '_customer_user'
                AND     posts.post_type     IN ('" . implode( "','" , wc_get_order_types( 'order-count' ) ) . "')
                AND     posts.post_status IN ( 'wc-completed', 'wc-processing' )" . $query . "
                AND     meta_value          = $user_id
            " ) ;
    return ( int ) $count ;
}

function sm_get_customer_total_spent( $user_id , $purchase_period , $from_date , $to_date ) {
    $spent      = '' ;
    global $wpdb ;
    $query      = "" ;
    $fromdate   = $from_date != "" ? $from_date : '' ;
    $todate     = $to_date != "" ? $to_date : date_i18n( 'Y-m-d' ) ;
    $from_query = $from_date != "" ? "AND posts.post_date >= '" . $fromdate . " 00:00:01' " : "" ;
    if( $purchase_period != '' ) {
        $query = $from_query . "AND posts.post_date <= '" . $todate . " 23:59:59' " ;
    }
    $spent = $wpdb->get_var( "SELECT SUM(meta2.meta_value)
			FROM $wpdb->posts as posts

			LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id
			LEFT JOIN {$wpdb->postmeta} AS meta2 ON posts.ID = meta2.post_id

			WHERE   meta.meta_key       = '_customer_user'
			AND     meta.meta_value     = $user_id
			AND     posts.post_type     IN ('" . implode( "','" , wc_get_order_types( 'reports' ) ) . "')
			AND     posts.post_status   IN ( 'wc-completed', 'wc-processing' )" . $query . "
			AND     meta2.meta_key      = '_order_total'
		" ) ;



    return $spent ;
}

//Get Multiple Plan Rules added by the Admin from Each Posts/Pages/Products.

function sumo_get_plan_rules_added( $postid , $restriction_type ) {

    $restricted_rules_arg = array() ;

    if( $restriction_type == 'members_with_plan' ) {
        $row_counts = get_option( 'sumomemberships_restrict_members_with_particular_plan_no_of_rules_added' . $postid ) ;
    } else {
        $row_counts = get_option( 'sumomemberships_restrict_users_without_particular_plan_no_of_rules_added' . $postid ) ;
    }

    if( $row_counts > 0 ) {

        for( $i = 1 ; $i <= $row_counts ; $i ++ ) {

            if( $restriction_type == 'members_with_plan' ) {
                $plan_id         = get_post_meta( $postid , 'sumomemberships_restrict_members_with_particular_plan_purchased' . $i , true ) ;
                $schedule_type   = get_post_meta( $postid , 'sumomemberships_restrict_members_with_particular_plan_schedule_type' . $i , true ) ;
                $duration_value  = get_post_meta( $postid , 'sumomemberships_restrict_members_with_particular_plan_duration_value' . $i , true ) ;
                $duration_period = get_post_meta( $postid , 'sumomemberships_restrict_members_with_particular_plan_duration_period' . $i , true ) ;
            } else {
                $plan_id         = get_post_meta( $postid , 'sumomemberships_restrict_users_without_particular_plan_purchased' . $i , true ) ;
                $schedule_type   = '' ;
                $duration_value  = '' ;
                $duration_period = '' ;
            }

            if( $plan_id > 0 ) {

                $restricted_rules_arg[] = array(
                    'plan_id'         => $plan_id ,
                    'schedule_type'   => $schedule_type ,
                    'duration_value'  => $duration_value ,
                    'duration_period' => $duration_period
                        ) ;
            }
        }
    }
    return $restricted_rules_arg ;
}

//Get global plan status which is triggered by Admin in the Plan Edit Page.

function sumo_is_global_plan_status_active( $planid ) {

    $global_plan_status = get_post_meta( $planid , 'sumomemberships_plan_status' , true ) ;

    if( $global_plan_status == 'disable' ) {
        return false ;
    }
    return true ;
}

function sumo_add_capability_to_member( $member_id , $plan_id , $plan_slug_name = false ) {

    $this_user = new WP_User( $member_id ) ;

    $member_post_id = sumo_get_member_post_id( $member_id ) ;

    $unique_id = sumo_get_plan_key( $member_post_id , $plan_id ) ;

    $saved_plans = get_post_meta( $member_post_id , 'sumomemberships_saved_plans' , true ) ;

    if( sumo_is_plan_active( $plan_id , $member_post_id ) ) {

        $plan_slug = $saved_plans[ $unique_id ][ 'plan_slug' ] ;
        //Add User Capabilities to Member. This can be helpful for Compatibility with other Plugins.
        if( isset( $plan_slug_name ) ) {
            $plan_slug = $plan_slug_name ;
        }
        $this_user->add_cap( $plan_slug ) ;
    }
}

//Remove capabilities from this member if Plan is Paused/Disabled/Cancelled/Expired/Refunded

function sumo_remove_capability_from_member( $member_id , $plan_id ) {

    $this_user = new WP_User( $member_id ) ;

    $member_post_id = sumo_get_member_post_id( $member_id ) ;

    $unique_id = sumo_get_plan_key( $member_post_id , $plan_id ) ;

    $saved_plans = get_post_meta( $member_post_id , 'sumomemberships_saved_plans' , true ) ;

    if( ! sumo_is_plan_active( $plan_id , $member_post_id ) ) {

        $plan_slug = isset( $saved_plans[ $unique_id ][ 'plan_slug' ] ) ? $saved_plans[ $unique_id ][ 'plan_slug' ] : '' ;

        //Remove User Capabilities from Member. Since the Plan might be paused/failed.
        $this_user->remove_cap( $plan_slug ) ;
    }
}

//Check Whether the Requested ID is Membership Posts.

function sumo_is_membership_post( $request_id ) {

    $args = array(
        'post_type'      => 'sumomembershipplans' ,
        'post_status'    => 'publish' ,
        'posts_per_page' => -1
            ) ;

    $membership_level_posts = get_posts( $args ) ;

    foreach( $membership_level_posts as $each_post ) {

        $postid = $each_post->ID ;

        if( $request_id == $postid ) {
            return true ;
        }
    }

    return false ;
}

function get_plan_id_by_subscription_id( $subscribtion_id ) {
    $args    = array(
        'post_type'      => 'sumomembers' ,
        'post_status'    => 'publish' ,
        'posts_per_page' => -1 ,
        'fields'         => 'ids'
            ) ;
    $plan_id = array() ;
    $posts   = get_posts( $args ) ;
    foreach( $posts as $each_post_id ) {
        $existing_saved_plans = ( array ) get_post_meta( $each_post_id , 'sumomemberships_saved_plans' , true ) ;
        foreach( $existing_saved_plans as $each_plan ) {
            if( $each_plan[ 'associated_subsc_id' ] == $subscribtion_id ) {
                $plan_id[] = $each_plan[ 'choose_plan' ] ;
            }
        }
    }
    return $plan_id ;
}

function sumo_get_product( $product_id ) {
    if( function_exists( 'wc_get_product' ) ) {
        $product_object = wc_get_product( $product_id ) ;
    } else {
        $product_object = get_product( $product_id ) ;
    }
    return $product_object ;
}

function sumo_get_parent_order_id( $order ) {
    if( ( float ) WC()->version >= '3.0.0' ) {
        $parent_order_id = $order->get_parent_id() ;
    } else {
        $parent_order_id = $order->post->post_parent ;
    }
    return $parent_order_id ;
}

function sumo_get_customer_id_from_order( $order ) {
    if( ( float ) WC()->version >= '3.0.0' ) {
        $user_id = $order->get_customer_id() ;
    } else {
        $user_id = $order->user_id ;
    }
    return $user_id ;
}

function sumo_get_product_id( $product ) {
    if( ( float ) WC()->version >= '3.0.0' ) {
        $product_id = $product->get_id() ;
    } else {
        $product_id = $product->variation_id ? $product->variation_id : $product->id ;
    }
    return $product_id ;
}

function sumo_get_product_level_id( $product ) {
    if( ( float ) WC()->version >= '3.0.0' ) {
        $product_id = $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id() ;
    } else {
        $product_id = $product->id ;
    }
    return $product_id ;
}

if( ! function_exists( 'sumo_get_membership_plans' ) ) {

    function sumo_get_membership_plans() {
        $args = array(
            'post_type'      => 'sumomembershipplans' ,
            'post_status'    => 'publish' ,
            'posts_per_page' => -1 ,
            'fields'         => 'ids'
                ) ;
        $ids  = get_posts( $args ) ;
        return $ids ;
    }

}

if( ! function_exists( 'check_plan_exists' ) ) {

    function check_plan_exists( $user_id ) {

        $member_post_id = sumo_get_member_post_id( $user_id ) ;

        $plans_list = sumo_get_member_purchased_plans_list( $member_post_id ) ;

        if( ! empty( $plans_list ) )
            return true ;

        return false ;
    }

}

if( ! function_exists( 'sumo_update_product_in_plan' ) ) {

    function sumo_update_product_in_plan( $post_id , $product_id ) {

        if( ! $post_id && ! $product_id )
            return ;

        if( get_post_type( $post_id ) != "sumomembershipplans" )
            return ;

        update_post_meta( $post_id , 'sumomemberships_plan_associated_product' , $product_id ) ;
    }

}

if( ! function_exists( 'sumo_add_particular_plan_to_user' ) ) {

    function sumo_add_particular_plan_to_user( $user_id , $plan_id , $status = "active" , $expiry_date = "" ) {

        if( ! $user_id )
            return ;

        $member_post_id = sumo_get_member_post_id( $user_id ) ;

        $plan_ids = sumo_get_member_purchased_plans_list( $member_post_id ) ;

        if( in_array( $plan_id , array_values( ( array ) $plan_ids ) ) )
            return ;

        $firstuniqid = uniqid() ;

        $args = array(
            'post_title'     => get_userdata( $user_id )->user_login ,
            'post_type'      => "sumomembers" ,
            'post_status'    => 'publish' ,
            'posts_per_page' => -1
                ) ;

        $post_id = wp_insert_post( $args ) ;

        $product_id = get_post_meta( $plan_id , 'sumomemberships_plan_associated_product' , true ) ;

        if( ! $product_id )
            return ;

        if( ! $expiry_date ) {
            $expiry_date = sumo_get_membership_plan_duration( $plan_id ) == "Indefinite" ? "" : date( 'Y-m-d h:i:s' , strtotime( sumo_get_membership_plan_duration( $plan_id ) ) ) ;
        }

        $saved_plans = array(
            $firstuniqid => array(
                'choose_plan'              => $plan_id ,
                'choose_status'            => apply_filters( 'sumomemberships_plan_status_management' , $status , $product_id , $order_id                  = "" ) ,
                'from_date'                => date( 'Y-m-d h:i:s' ) ,
                'to_date'                  => $expiry_date ,
                'associated_product'       => $product_id ,
                'associated_subsc_product' => '' ,
                'associated_subsc_id'      => '' ,
                'plan_slug'                => get_post_meta( $plan_id , 'sumomemberships_plan_slug' , true ) ,
                'scheduled_link_plans'     => array() ,
                'link_plans'               => array() ,
                'available_duration'       => '' ,
                'order_id'                 => ''
            ) ) ;

        update_post_meta( $post_id , 'sumomemberships_userid' , $user_id ) ;
        update_post_meta( $post_id , 'sumomemberships_saved_plans' , $saved_plans ) ;

        add_post_meta( $post_id , 'sumomemberships_member_since_date' , time() ) ;
    }

}

if( ! function_exists( 'sumo_replace_membership_products_shortcode' ) ) {

    function sumo_replace_membership_products_shortcode( $option_name ) {

        switch( $option_name ) {

            case 'user_product_view':
                $message = get_option( 'sumo_msg_for_site_users_product_view_restriction_for_membership_purchase' ) ;
                break ;

            case 'guest_product_view':
                $message = get_option( 'sumo_msg_for_guests_product_view_restriction_for_membership_purchase' ) ;
                break ;

            case 'user_product_purchase':
                $message = get_option( 'sumo_msg_for_site_users_product_purchase_restriction_for_membership_purchase' ) ;
                break ;

            case 'guest_product_purchase':
                $message = get_option( 'sumo_msg_for_guests_product_purchase_restriction_for_membership_purchase' ) ;
                break ;

            case 'site_users_product_purchase':
                $message = get_option( 'sumo_msg_for_site_users_product_purchase_restriction_for_membership' ) ;
                break ;

            case 'site_users_content':
                $message = get_option( 'sumo_msg_for_site_users_content_restriction_for_membership_purchase' ) ;
                break ;

            case 'site_guest_content':
                $message = get_option( 'sumo_msg_for_guests_content_restriction_for_membership_purchase' ) ;
                break ;
        }
	
        $message = str_replace( '[membership_product(s)]' , '[membership_products]' , $message ) ;

        return $message ;
    }

}

if ( ! function_exists( 'sumo_member_saved_plans' ) ) {

    function sumo_member_saved_plans( $member_post_id ) {

        if ( 'yes' == get_option( 'sumo_restrict_multiple_plans' ) ) {
            return array() ;
        }

        return ( array ) get_post_meta( $member_post_id , 'sumomemberships_saved_plans' , true ) ;
    }

}
