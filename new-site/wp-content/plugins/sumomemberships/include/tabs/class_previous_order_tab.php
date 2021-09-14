<?php

class SUMOPreviousOrder_Settings_Tab {

    public function __construct() {

        add_filter( 'woocommerce_sumomemberships_settings_tabs_array' , array( $this , 'general_tab_setting' ) ) ; // Register a New Tab in a WooCommerce
        add_action( 'woocommerce_sumomemberships_settings_tabs_previous_order' , array( $this , 'register_admin_settings' ) ) ; // Call to register the admin settings in the Plugin Submenu with general settings tab
        add_action( 'woocommerce_admin_field_sumomemberships_previous_order_content' , array( __CLASS__ , 'sumomemberships_display_check_previous_orders' ) ) ;
        add_action( 'wp_ajax_sm_add_old_order' , array( __CLASS__ , 'sm_add_old_order_callback' ) ) ;
        add_action( 'wp_ajax_sm_chunk_previous_order_list' , array( __CLASS__ , 'sm_chunk_previous_order_list_callback' ) ) ;
    }

    /*
     * Function to Define Name of the Tab
     */

    public static function general_tab_setting( $setting_tabs ) {
        if( ! is_array( $setting_tabs ) )
            $setting_tabs                     = ( array ) $setting_tabs ;
        $setting_tabs[ 'previous_order' ] = __( 'Check Previous Orders' , 'sumomemberships' ) ;
        return array_filter( $setting_tabs ) ;
    }

    /*
     * Function label settings to Member Level Tab
     */

    public static function default_settings() {
        global $woocommerce ;

        return apply_filters( 'woocommerce_sumomemberships_previous_order' , array(
            array(
                'name' => __( 'Check Old WooCommerce Orders to provide membership acces for old customers' , 'sumomemberships' ) ,
                'type' => 'title' ,
                'id'   => 'previous_order_tab_setting' ,
            ) ,
            array(
                'type' => 'sumomemberships_previous_order_content' ,
            ) ,
            array( 'type' => 'sectionend' , 'id' => 'previous_order_tab_setting' ) ,
                ) ) ;
    }

    public static function sumomemberships_display_check_previous_orders() {
        ?> 
        <table class="form-table">
            <tr>
                <th><?php _e( 'Time Duration' , 'sumomemberships' ) ; ?></th>
                <td>
                    <select id = "sm_order_time">
                        <option value = "all"><?php _e( "All time" , "sumomemberships" ) ; ?></option>
                        <option value = "specific"><?php _e( "Specific" , "sumomemberships" ) ; ?></option>
                    </select>
                </td>
            </tr>
            <tr style = "display: none" id = "sm_specific_row">
                <th><?php _e( "Specific Time" , "sumomemberships" ) ; ?></th>
                <td>
                    <label><?php _e( "From" , "sumomemberships" ) ; ?></label> 
                    <input type = "text" name = "from_date" id = "from_time" class = "sm_date"> 
                    <label><?php _e( "To" , "sumomemberships" ) ; ?></label> 
                    <input type = "text" id = "to_time" name = "to_date" class = "sm_date">
                </td>
            </tr>
            <tr>
                <td>
                    <input type = "button" class = "button button-primary" name = "update_order" id = "sm_update_order" value = "<?php _e( "Check for Previous Orders" , "sumomemberships" ) ; ?>">
                </td>
                <td>
                    <img style = "width: 30px;height: 30px;display: none;" class = "perloader_image" src = "<?php echo SUMO_MEMBERSHIPS_PLUGIN_URL ?>/assets/images/loader.gif"/>
                    <p id = "update_response"></p>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Registering Custom Field Admin Settings
     */
    public static function register_admin_settings() {


        woocommerce_admin_fields( SUMOPreviousOrder_Settings_Tab::default_settings() ) ;
    }

    /**
     * Update the Settings on Save Changes
     */
    public static function advance_update_settings() {

        woocommerce_update_options( SUMOPreviousOrder_Settings_Tab::default_settings() ) ;
    }

    public static function sm_add_old_order_callback() {
        if( isset( $_POST[ 'sm_order_time' ] ) ) {
            $order_statuses1 = get_option( 'sumo_membership_plan_access_order_status' ) ;
            $post_status1    = array() ;
            if( is_array( $order_statuses1 ) && ! empty( $order_statuses1 ) ) {
                foreach( $order_statuses1 as $each_status ) {
                    $post_status1[] = 'wc-' . $each_status ;
                }
            }
            $args = array(
                'post_type'      => 'shop_order' ,
                'posts_per_page' => '-1' ,
                "post_status"    => array( 'wc-completed' ) ,
                'fields'         => 'ids' ,
                'cache_results'  => false ,
//                'meta_query'     => array (
//                    'relation' => 'AND' ,
//                    array (
//                        'key'     => 'sumomemberships_check_for_order_placed' ,
//                        'compare' => 'NOT EXISTS'
//                    )
//                )
                    ) ;
            if( "all" != $_POST[ 'sm_order_time' ] ) {
                if( ! empty( $_POST[ 'sm_from_time' ] ) || ! empty( $_POST[ 'sm_to_time' ] ) ) {
                    $convert_from_date = ( $_POST[ 'sm_from_time' ] ) ;
                    $convert_to_date   = ( $_POST[ 'sm_to_time' ] ) ;
                    //convert std format
                    $from_time_array   = explode( "-" , $convert_from_date ) ;
                    $to_time_array     = explode( "-" , $convert_to_date ) ;
                    if( ! empty( $_POST[ 'sm_to_time' ] ) ) {
                        $date_query = array(
                            'before'    => array(
                                'year'  => $to_time_array[ 0 ] ,
                                'month' => $to_time_array[ 1 ] ,
                                'day'   => $to_time_array[ 2 ] ,
                            ) ,
                            'inclusive' => true ,
                                ) ;
                    } elseif( ! empty( $_POST[ 'sm_from_time' ] ) ) {
                        $date_query = array(
                            'after'     => array(
                                'year'  => $from_time_array[ 0 ] ,
                                'month' => $from_time_array[ 1 ] ,
                                'day'   => $from_time_array[ 2 ] ,
                            ) ,
                            'inclusive' => true ,
                                ) ;
                    } else {
                        $date_query = array(
                            'after'     => array(
                                'year'  => $from_time_array[ 0 ] ,
                                'month' => $from_time_array[ 1 ] ,
                                'day'   => $from_time_array[ 2 ] ,
                            ) ,
                            'before'    => array(
                                'year'  => $to_time_array[ 0 ] ,
                                'month' => $to_time_array[ 1 ] ,
                                'day'   => $to_time_array[ 2 ] ,
                            ) ,
                            'inclusive' => true ,
                                ) ;
                    }
                    $args = array_merge( $args , array( 'date_query' => $date_query ) ) ;
                }
            }
            $orders = get_posts( $args ) ;
            delete_option( 'sm_previous_count' ) ;
            echo json_encode( $orders ) ;
        }
        exit() ;
    }

    public static function sm_chunk_previous_order_list_callback() {

        if( ! empty( $_POST[ 'ids' ] ) ) {
            $updated_count       = 0 ;
            $order_ids           = $_POST[ 'ids' ] ;
            $check_previous_data = get_option( 'sm_previous_count' ) ;
            $obj                 = new SUMOMemberships_Restrictions() ;
            if( is_array( $order_ids ) && ! empty( $order_ids ) ) {
                foreach( $order_ids as $order_id ) {
                    $order              = new WC_Order( $order_id ) ;
                    $subscription_check = self::sm_check_is_subscription( $order_id ) ;

//                    if ( get_post_meta( $order_id , 'sumomemberships_check_for_order_placed' , true ) != $order_id ) {

                    $this_member_id            = sumo_get_customer_id_from_order( $order ) ;
                    $order_contains_membership = '' ;
                    foreach( $order->get_items() as $item ) {
                        if( isset( $item[ 'variation_id' ] ) ) {

                            if( ($item[ 'variation_id' ] != '0') && ($item[ 'variation_id' ] != '') ) {
                                $productid                    = $item[ 'variation_id' ] ;
                                $productlevelid_for_variation = ( int ) $item[ 'product_id' ] ;
                            } else {
                                $productid                    = $item[ 'product_id' ] ;
                                $productlevelid_for_variation = 0 ;
                            }
                        } else {
                            $productid                    = $item[ 'product_id' ] ;
                            $productlevelid_for_variation = 0 ;
                        }
                        $product_id = ( int ) $productid ;
                        if( sumo_is_membership_product( $product_id ) && $this_member_id > 0 ) {

                            $plan_ids = sumo_get_product_associated_plan_ids( $product_id ) ;
                            foreach( $plan_ids as $plan_id ) {
                                $obj->sumo_common_function_for_save_plan_id_for_users( $product_id , $this_member_id , $order_id , $plan_id ) ;
                                $order_contains_membership = 'yes' ;
                            }
                        }
                    }
                    if( $order_contains_membership == 'yes' ) {
                        $updated_count ++ ;
                    }

                    if( $subscription_check ) {
                        /* Subscription Compatibility Code Improvement Made in V4.7.2 */
                        $parent = wp_get_post_parent_id( $order_id ) ;

                        $subscription_obj = new SUMOMemberships_Subscription_Compatibility() ;

                        if( $parent ) {

                            $subscription_id = sumosubs_get_subscription_id_from_renewal_order( $order_id ) ;
                            $status          = strtolower( get_post_meta( $subscription_id , 'sumo_get_status' , true ) ) ;
                            $subscription_obj->interior_update_plan_status_based_on_subscription( $subscription_id , $status , $this_member_id ) ;
                        } else {

                            $subscriptions_in_order = get_post_meta( $order_id , 'sumo_subsc_get_available_postids_from_parent_order' , true ) ;

                            /* Selected Subscription Status */
                            if( is_array( $subscriptions_in_order ) && isset( $subscriptions_in_order[ $product_id ] ) ) {
                                $subscription_id = $subscriptions_in_order[ $product_id ] ;
                                $status          = strtolower( get_post_meta( $subscription_id , 'sumo_get_status' , true ) ) ;
                                $subscription_obj->interior_update_plan_status_based_on_subscription( $subscription_id , $status , $this_member_id ) ;
                            }
                        }
                    }
//                    update_post_meta( $order_id , 'sumomemberships_check_for_order_placed' , $order_id ) ;
//                }
                }
            }
            update_option( 'sm_previous_count' , $check_previous_data + $updated_count ) ;
        } else {
            echo json_encode( array( 'count' => get_option( 'sm_previous_count' ) ) ) ;
        }
        exit() ;
    }

    public static function sm_check_is_subscription( $order_id ) {
        $bool = false ;
        if( function_exists( 'sumo_is_order_contains_subscriptions' ) ) {
            $bool = sumo_is_order_contains_subscriptions( $order_id ) ;
        }
        return $bool ;
    }

}

new SUMOPreviousOrder_Settings_Tab() ;
