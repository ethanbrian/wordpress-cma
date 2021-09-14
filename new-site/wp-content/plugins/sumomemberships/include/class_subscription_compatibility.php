<?php

class SUMOMemberships_Subscription_Compatibility {

    public function __construct() {
        add_action( 'sumosubscriptions_active_subscription' , array( $this , 'process_active_plans' ) , 10 , 2 ) ;
        add_action( 'sumosubscriptions_pause_subscription' , array( $this , 'process_pause_plans' ) , 10 , 2 ) ;
        add_action( 'sumosubscriptions_cancel_subscription' , array( $this , 'process_cancel_plans' ) , 10 , 2 ) ;
        add_action( 'sumosubscriptions_subscription_is_switched' , array( $this , 'upgrade_or_downgrade_plans' ) , 10 , 3 ) ;
    }

    public function process_active_plans( $subscription_id , $order_id ) {

        $this->update_plan_status_based_on_subscription( $subscription_id , 'active' ) ;
    }

    public function process_pause_plans( $subscription_id , $order_id ) {

        $this->update_plan_status_based_on_subscription( $subscription_id , 'paused' ) ;
    }

    public function process_cancel_plans( $subscription_id , $order_id ) {

        $subscription_status = get_post_meta( $subscription_id , 'sumo_get_status' , true ) ;

        if ( $subscription_status == 'Expired' ) {
            $this->update_plan_status_based_on_subscription( $subscription_id , 'expired' ) ;
        } else {
            $this->update_plan_status_based_on_subscription( $subscription_id , 'cancelled' ) ;
        }
    }

    public function update_plan_status_based_on_subscription( $subscription_id , $status ) {

        $user_id = get_post_meta( $subscription_id , 'sumo_get_user_id' , true ) ;

        $my_plan_id_array = get_plan_id_by_subscription_id( $subscription_id ) ;

        $my_plan_id_array = array_filter( $my_plan_id_array ) ;

        if ( ! empty( $my_plan_id_array ) ) {

            foreach ( $my_plan_id_array as $my_plan_id ) {
                $transfered_user_id = get_post_meta( $user_id , $my_plan_id . 'plan_switched_to' , true ) ;
                if ( get_userdata( $transfered_user_id ) ) {
                    self::interior_update_plan_status_based_on_subscription( $subscription_id , $status , $transfered_user_id ) ;
                } else {
                    self::interior_update_plan_status_based_on_subscription( $subscription_id , $status , $user_id ) ;
                }
            }
        } else {
            self::interior_update_plan_status_based_on_subscription( $subscription_id , $status , $user_id ) ;
        }
    }

    public function interior_update_plan_status_based_on_subscription( $subscription_id , $status , $user_id ) {

        if ( $user_id > 0 ) {

            $member_post_id = sumo_get_member_post_id( $user_id ) ;

            $saved_plans = ( array ) get_post_meta( $member_post_id , 'sumomemberships_saved_plans' , true ) ;

            $unique_ids = $this->get_plan_key_from_associated_subsc_product( $member_post_id , $subscription_id ) ;

            if ( ! empty( $unique_ids ) ) {
                foreach ( $unique_ids as $unique_id ) {
                    if ( $unique_id != '' && isset( $saved_plans[ $unique_id ][ 'choose_status' ] ) ) {

                        $saved_plans[ $unique_id ][ 'choose_status' ]       = "$status" ;
                        $saved_plans[ $unique_id ][ 'associated_subsc_id' ] = $subscription_id ;
                        do_action( 'sumomemberships_plan_status_changed' , $member_post_id , $saved_plans[ $unique_id ][ 'choose_plan' ] , $saved_plans[ $unique_id ][ 'choose_status' ] ) ;
                        switch ( $status ) {
                            case 'active':
                                $plan_id = $saved_plans[ $unique_id ][ 'choose_plan' ] ;

                                $new_privileged_link_plans = sumo_get_privileged_link_plans( $plan_id , $user_id ) ;
                                $new_scheduled_link_plans  = sumo_get_schedule_link_plans( $plan_id , $user_id ) ;

                                $saved_plans[ $unique_id ][ 'link_plans' ]           = $new_privileged_link_plans ;
                                $saved_plans[ $unique_id ][ 'scheduled_link_plans' ] = $new_scheduled_link_plans ;

                                $plan_slug = get_post_meta( $plan_id , 'sumomemberships_plan_slug' , true ) ;

                                $saved_plans[ $unique_id ][ 'plan_slug' ] = $plan_slug ;

                                do_action( 'sumomemberships_active_subscription' , $unique_id , $saved_plans , $member_post_id , $status ) ;
                                break ;
                            case 'paused':
                            case 'cancelled':
                            case 'expired':
                                $plan_id = $saved_plans[ $unique_id ][ 'choose_plan' ] ;

                                sumo_clear_linked_plans_privilege_cron( $member_post_id , $unique_id , $user_id , $plan_id ) ;

                                if ( $status == 'expired' || $status == 'cancelled' ) {

                                    $saved_plans[ $unique_id ][ 'associated_subsc_id' ] = '' ;
                                }
                                $saved_plans[ $unique_id ][ 'link_plans' ]           = array() ;
                                $saved_plans[ $unique_id ][ 'scheduled_link_plans' ] = array() ;
                                do_action( 'sumomemberships_status_subscription' , $unique_id , $saved_plans , $member_post_id , $status ) ;
                                break ;
                        }
                    }
                }
                update_post_meta( $member_post_id , 'sumomemberships_saved_plans' , $saved_plans ) ;
            }
        }
    }

    public function get_plan_key_from_associated_subsc_product( $member_post_id , $subscription_id ) {
        $plan_key = array() ;

        $saved_plans = ( array ) get_post_meta( $member_post_id , 'sumomemberships_saved_plans' , true ) ;

        $is_order_subscription = get_post_meta( $subscription_id , 'sumo_is_order_based_subscriptions' , true ) == 'yes' ;

        if ( $is_order_subscription ) {
            $subscription_product_info = get_post_meta( $subscription_id , 'sumo_subscriptions_order_details' , true ) ;
        } else {
            $subscription_product_info = get_post_meta( $subscription_id , 'sumo_subscription_product_details' , true ) ;
        }

        if ( isset( $subscription_product_info[ 'productid' ] ) ) {

            $subscription_product_id = $subscription_product_info[ 'variation_product_level_id' ] > 0 ? $subscription_product_info[ 'productid' ] : $subscription_product_info[ 'productid' ] ;

            foreach ( $saved_plans as $each_plan_key => $each_plan ) {
                if ( isset( $each_plan[ 'associated_subsc_product' ] ) ) {

                    if ( is_array( $subscription_product_id ) && in_array( $each_plan[ 'associated_subsc_product' ] , $subscription_product_id ) ) {
                        $plan_key[] = $each_plan_key ;
                    } else if ( ! is_array( $subscription_product_id ) && $each_plan[ 'associated_subsc_product' ] == $subscription_product_id ) {
                        $plan_key[] = $each_plan_key ;
                    }
                }
            }
        }
        return $plan_key ;
    }

    public function upgrade_or_downgrade_plans( $order_id , $old_subscription , $switched_subscription ) {
        $OrderObj     = new WC_Order( $order_id ) ;
        $UserId       = $OrderObj->get_user_id() ;
        $MemberPostId = sumo_get_member_post_id( $UserId ) ;
        $saved_plans  = ( array ) get_post_meta( $MemberPostId , 'sumomemberships_saved_plans' , true ) ;
        $old_plan_ids = sumo_get_product_associated_plan_ids( $old_subscription->get_subscribed_product() ) ;
        foreach ( $saved_plans as $unique_id => $plan ) {
            foreach ( $old_plan_ids as $old_plan_id ) {
                if ( $plan[ 'choose_plan' ] == $old_plan_id )
                    unset( $saved_plans[ $unique_id ] ) ;
            }
        }
        update_post_meta( $MemberPostId , 'sumomemberships_saved_plans' , $saved_plans ) ;
        $product_id    = $switched_subscription->get_id() ;
        $plan_ids      = sumo_get_product_associated_plan_ids( $product_id ) ;
        $MembershipObj = new SUMOMemberships_Restrictions() ;
        foreach ( $plan_ids as $plan_id ) {
            $MembershipObj->sumo_common_function_for_save_plan_id_for_users( $product_id , $MemberPostId , $order_id , $plan_id ) ;
        }
    }

}

new SUMOMemberships_Subscription_Compatibility() ;
