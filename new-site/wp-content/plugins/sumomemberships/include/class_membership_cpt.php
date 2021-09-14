<?php

class SUMOMemberships_CPT {

    public function __construct() {

        add_action( 'init' , array( $this , 'register_memberships_cpt' ) ) ;

        add_action( 'manage_sumomembershipplans_posts_custom_column' , array( $this , 'display_membership_plans' ) , 10 , 2 ) ;
        add_filter( 'manage_sumomembershipplans_posts_columns' , array( $this , 'membershipplans_columns' ) ) ;

        add_action( 'manage_sumomembers_posts_custom_column' , array( $this , 'display_members' ) , 10 , 2 ) ;
        add_filter( 'manage_sumomembers_posts_columns' , array( $this , 'members_columns' ) ) ;

        add_action( 'manage_sumomem_masterlog_posts_custom_column' , array( $this , 'display_master_logs' ) , 10 , 2 ) ;
        add_filter( 'manage_sumomem_masterlog_posts_columns' , array( $this , 'master_logs_columns' ) ) ;

        add_filter( 'bulk_actions-edit-sumomembershipplans' , array( $this , 'remove_bulk_actions' ) ) ;
        add_filter( 'bulk_actions-edit-sumomembers' , array( $this , 'remove_bulk_actions' ) ) ;
        add_filter( 'bulk_actions-edit-sumomem_masterlog' , array( $this , 'remove_bulk_actions' ) ) ;

        add_filter( 'post_row_actions' , array( $this , 'remove_row_actions' ) , 10 , 2 ) ;
        add_filter( 'enter_title_here' , array( $this , 'enter_title_here' ) , 1 , 2 ) ;

        add_action( 'deleted_user' , array( $this , 'delete_user_details' ) ) ;
        
        // Search filters functionality.
        add_filter( 'posts_search' , array( $this , 'search_members' ) ) ;
        add_filter( 'posts_search' , array( $this , 'search_master_logs' ) ) ;
    }

    /*
     * Search members.
     * 
     * @return string
     */

    public static function search_members( $where ) {

        if( ! isset( $_GET[ "post_type" ] ) || 'sumomembers' != wc_clean( wp_unslash( $_GET[ "post_type" ] ) ) ) {
            return $where ;
        }

        if( empty( $_REQUEST[ 's' ] ) ) {
            return $where ;
        }

        global $wpdb ;
        $searched_value = isset( $_REQUEST[ 's' ] ) ? wc_clean( wp_unslash( $_REQUEST[ 's' ] ) ) : '' ;
        $user_ids       = get_users( array( 'fields' => 'ids' , 'search' => '*' . $searched_value . '*' ) ) ;
        if( ! sumo_membership_check_is_array( $user_ids ) ) {
            return $where ;
        }
        
        $user_ids       = implode( ',' , $user_ids );
        //Query for members.
        $member_post_id = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT pm.post_id FROM {$wpdb->postmeta} as pm WHERE pm.meta_key = 'sumomemberships_userid' AND pm.meta_value IN ($user_ids)" , ARRAY_A) ) ;
        $member_post_id = ! empty( $member_post_id ) ? implode( ',' , $member_post_id ) : '' ;
        if( $member_post_id ) {
            $where = " AND {$wpdb->posts}.ID IN($member_post_id)" ;
        }

        return $where ;
    }

    /*
     * Search master logs.
     */

    public static function search_master_logs( $where ) {

        if( ! isset( $_GET[ "post_type" ] ) || 'sumomem_masterlog' != wc_clean( wp_unslash( $_GET[ "post_type" ] ) ) ) {
            return $where ;
        }

        if( empty( $_REQUEST[ 's' ] ) ) {
            return $where ;
        }

        global $wpdb ;
        $searched_value = isset( $_REQUEST[ 's' ] ) ? wc_clean( wp_unslash( $_REQUEST[ 's' ] ) ) : '' ;
        $user_ids       = get_users( array( 'fields' => 'ids' , 'search' => '*' . $searched_value . '*' ) ) ;
        
        $member_post_id = '' ;
        if( sumo_membership_check_is_array( $user_ids ) ) {
            $user_ids       = implode( ',' , $user_ids );
            // Query for members.
            $member_post_id = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT pm.post_id FROM {$wpdb->postmeta} as pm WHERE pm.meta_key = 'sumo_username_for_masterlog' AND pm.meta_value IN ($user_ids)" , ARRAY_A) ) ;
            $member_post_id = ! empty( $member_post_id ) ? implode( ',' , $member_post_id ) : '' ;
        } else {
            // Query for plans.
            $member_post_id = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT pm.post_id FROM {$wpdb->postmeta} as pm WHERE pm.meta_key = 'sumo_planname_for_masterlog' AND pm.meta_value = %s" , $searched_value ) ) ;
            $member_post_id = ! empty( $member_post_id ) ? implode( ',' , $member_post_id ) : '' ;
        }

        if( $member_post_id ) {
            $where = " AND {$wpdb->posts}.ID IN($member_post_id)" ;
        }

        return $where ;
    }

    public function register_memberships_cpt() {

        $labels = array(
            'name'               => _x( 'Membership Plans' , 'general name' , 'sumomemberships' ) ,
            'singular_name'      => _x( 'SUMO Memberships' , 'singular name' , 'sumomemberships' ) ,
            'menu_name'          => _x( 'SUMO Memberships' , 'admin menu' , 'sumomemberships' ) ,
            'name_admin_bar'     => _x( 'SUMO Memberships' , 'add new on admin bar' , 'sumomemberships' ) ,
            'add_new'            => _x( 'Add New Membership Plan' , 'membership' , 'sumomemberships' ) ,
            'add_new_item'       => __( 'Add New Membership Plan' , 'sumomemberships' ) ,
            'new_item'           => __( 'New Membership' , 'sumomemberships' ) ,
            'edit_item'          => __( 'Edit Membership' , 'sumomemberships' ) ,
            'view_item'          => __( 'View Membership' , 'sumomemberships' ) ,
            'all_items'          => __( 'Membership Plans' , 'sumomemberships' ) ,
            'search_items'       => __( 'Search Membership' , 'sumomemberships' ) ,
            'parent_item_colon'  => __( 'Parent Membership:' , 'sumomemberships' ) ,
            'not_found'          => __( 'No Membership Levels Found.' , 'sumomemberships' ) ,
            'not_found_in_trash' => __( 'No Membership Levels found in Trash.' , 'sumomemberships' )
                ) ;

        $args = array(
            'labels'             => $labels ,
            'description'        => __( 'Description.' , 'sumomemberships' ) ,
            'public'             => false ,
            'publicly_queryable' => false ,
            'show_ui'            => true ,
            'show_in_menu'       => true ,
            'show_in_admin_bar'  => false ,
            'query_var'          => true ,
            'rewrite'            => array( 'slug' => 'sumomemberships' ) ,
            'capability_type'    => 'post' ,
            'has_archive'        => true ,
            'menu_icon'          => 'dashicons-groups' ,
            'hierarchical'       => false ,
            'menu_position'      => 56 ,
            'supports'           => array( 'title' ) ,
            'capabilities'       => array(
                'edit_post'          => 'manage_options' ,
                'edit_posts'         => 'manage_options' ,
                'edit_others_posts'  => 'manage_options' ,
                'publish_posts'      => 'manage_options' ,
                'read_post'          => 'manage_options' ,
                'read_private_posts' => 'manage_options' ,
                'delete_post'        => 'manage_options' ,
                'delete_posts'       => true ,
                'create_posts'       => true ,
            ) ,
            'map_meta_cap'       => null ,
                ) ;

        register_post_type( 'sumomembershipplans' , $args ) ;

        $labels = array(
            'name'               => _x( 'Members' , 'general name' , 'sumomemberships' ) ,
            'singular_name'      => _x( 'Members' , 'singular name' , 'sumomemberships' ) ,
            'menu_name'          => _x( 'Members' , 'admin menu' , 'sumomemberships' ) ,
            'name_admin_bar'     => _x( 'Members' , 'add new on admin bar' , 'sumomemberships' ) ,
            'add_new'            => _x( 'Add Member' , 'sumomemberships' , 'sumomemberships' ) ,
            'add_new_item'       => __( 'Add Member' , 'sumomemberships' ) ,
            'new_item'           => __( 'New Member' , 'sumomemberships' ) ,
            'edit_item'          => __( 'Edit Member' , 'sumomemberships' ) ,
            'view_item'          => __( 'View Member' , 'sumomemberships' ) ,
            'all_items'          => __( 'Members' , 'sumomemberships' ) ,
            'search_items'       => __( 'Search Members' , 'sumomemberships' ) ,
            'parent_item_colon'  => __( 'Parent Member:' , 'sumomemberships' ) ,
            'not_found'          => __( 'No Members Found.' , 'sumomemberships' ) ,
            'not_found_in_trash' => __( 'No Members found in Trash.' , 'sumomemberships' )
                ) ;

        $args = array(
            'labels'             => $labels ,
            'description'        => __( 'Description.' , 'sumomemberships' ) ,
            'public'             => false ,
            'publicly_queryable' => false ,
            'show_ui'            => true ,
            'show_in_menu'       => 'edit.php?post_type=sumomembershipplans' ,
            'query_var'          => true ,
            'rewrite'            => array( 'slug' => 'sumomembers' ) ,
            'capability_type'    => 'post' ,
            'has_archive'        => true ,
            'menu_icon'          => 'dashicons-backup' ,
            'hierarchical'       => false ,
            'menu_position'      => null ,
            'supports'           => array( NULL ) ,
            'capabilities'       => array(
                'delete_post'  => 'manage_options' ,
                'delete_posts' => true ,
                'edit_post'    => 'manage_options' ,
            ) ,
                ) ;

        register_post_type( 'sumomembers' , $args ) ;

        $labels = array(
            'name'               => _x( 'Master Log' , 'general name' , 'sumomemberships' ) ,
            'singular_name'      => _x( 'Master Log' , 'singular name' , 'sumomemberships' ) ,
            'menu_name'          => _x( 'Master Log' , 'admin menu' , 'sumomemberships' ) ,
            'name_admin_bar'     => _x( 'Master Log' , 'add new on admin bar' , 'sumomemberships' ) ,
            'add_new'            => _x( 'Add Member' , 'sumomemberships' , 'sumomemberships' ) ,
            'add_new_item'       => __( 'Add Member' , 'sumomemberships' ) ,
            'new_item'           => __( 'New Log' , 'sumomemberships' ) ,
            'edit_item'          => __( 'Edit Log' , 'sumomemberships' ) ,
            'view_item'          => __( 'View Log' , 'sumomemberships' ) ,
            'all_items'          => __( 'All Log' , 'sumomemberships' ) ,
            'search_items'       => __( 'Search Log' , 'sumomemberships' ) ,
            'parent_item_colon'  => __( 'Parent Log:' , 'sumomemberships' ) ,
            'not_found'          => __( 'No Logs Found.' , 'sumomemberships' ) ,
            'not_found_in_trash' => __( 'No Logs found in Trash.' , 'sumomemberships' )
                ) ;

        $args = array(
            'labels'             => $labels ,
            'description'        => __( 'Description.' , 'sumomemberships' ) ,
            'public'             => false ,
            'publicly_queryable' => false ,
            'show_ui'            => true ,
            'show_in_menu'       => false ,
            'query_var'          => true ,
            'rewrite'            => array( 'slug' => 'sumomembers' ) ,
            'capability_type'    => 'post' ,
            'has_archive'        => true ,
            'menu_icon'          => 'dashicons-backup' ,
            'hierarchical'       => false ,
            'menu_position'      => null ,
            'supports'           => array( NULL ) ,
            'capabilities'       => array(
                'create_posts' => 'do_not_allow' ,
                'delete_post'  => 'manage_options' ,
                'delete_posts' => true ,
                'edit_post'    => 'manage_options' ,
            ) ,
                ) ;

        register_post_type( 'sumomem_masterlog' , $args ) ;
    }

    public function membershipplans_sortable_columns( $columns ) {

        $custom = array(
            'plan_name'     => 'title' ,
            'plan_duration' => 'date' ,
                ) ;
        return wp_parse_args( $custom , $columns ) ;
    }

    public function membershipplans_columns( $existing_columns ) {

        $columns = array(
            'cb'            => $existing_columns[ 'cb' ] ,
            'plan_ids'      => __( 'Membership Plan ID' , 'sumomemberships' ) ,
            'plan_name'     => __( 'Membership Plan Name' , 'sumomemberships' ) ,
            'plan_slug'     => __( 'Membership Plan Slug' , 'sumomemberships' ) ,
            'plan_duration' => __( 'Membership Duration' , 'sumomemberships' ) ,
            'plan_members'  => __( 'Members' , 'sumomemberships' ) ,
                ) ;
        return $columns ;
    }

    public function members_columns( $existing_columns ) {

        $columns = array(
            'cb'           => $existing_columns[ 'cb' ] ,
            'user_name'    => __( 'Member Name' , 'sumomemberships' ) ,
            'user_email'   => __( 'Email' , 'sumomemberships' ) ,
            'plan_name'    => __( 'Membership Plan / Status' , 'sumomemberships' ) ,
            'member_since' => __( 'Membership Plan / Member Since' , 'sumomemberships' ) ,
            'expires_on'   => __( 'Membership Plan / Expires on' , 'sumomemberships' ) ,
                ) ;
        return $columns ;
    }

    public function master_logs_columns( $existing_columns ) {

        $columns = array(
            'cb'        => $existing_columns[ 'cb' ] ,
            'user_name' => __( 'User Name' , 'sumomemberships' ) ,
            'plan_name' => __( 'Plan Name' , 'sumomemberships' ) ,
            'event'     => __( 'Event' , 'sumomemberships' ) ,
            'date'      => __( 'Date' , 'sumomemberships' ) ,
                ) ;
        return $columns ;
    }

    public function display_membership_plans( $column , $postid ) {

        switch( $column ) {

            case 'plan_ids':
                echo $postid ;
                break ;
            case 'plan_name':
                echo get_post_meta( $postid , 'sumomemberships_plan_name' , true ) == "" ? 'This Plan is currently in Draft Mode.' : get_post_meta( $postid , 'sumomemberships_plan_name' , true ) ;
                break ;
            case 'plan_slug':
                echo get_post_meta( $postid , 'sumomemberships_plan_slug' , true ) == "" ? '--' : get_post_meta( $postid , 'sumomemberships_plan_slug' , true ) ;
                break ;
            case 'plan_duration':
                $product_id = get_post_meta( $postid , 'sumomemberships_plan_associated_product' , true ) ;

                if( $product_id > 0 && sumo_is_subcription_enabled( $product_id ) ) {
                    echo sumo_display_susbcription_plan_message( $product_id ) ;
                } else {
                    echo sumo_get_membership_plan_duration( $postid ) == "" ? '--' : sumo_get_membership_plan_duration( $postid ) ;
                }
                break ;
            case 'plan_members':
                echo sumo_get_plan_purchased_members( $postid , true ) ;
                break ;
        }
    }

    public function display_members( $column , $postid ) {

        $getdatatodisplay = get_post_meta( $postid , 'sumomemberships_saved_plans' , true ) ;
        $saved_plans      = array() ;
        if( is_array( $getdatatodisplay ) && ! empty( $getdatatodisplay ) ) {
            foreach( $getdatatodisplay as $key => $value ) {
                if( isset( $value[ 'choose_plan' ] ) && $value[ 'choose_plan' ] != '' ) {
                    $saved_plans[ $value[ 'choose_plan' ] ] = array( 'planname' => $value[ 'choose_plan' ] , 'subsc_id' => isset( $value[ 'associated_subsc_id' ] ) ? $value[ 'associated_subsc_id' ] : "" , 'planstatus' => $value[ 'choose_status' ] , 'membersince' => $value[ 'from_date' ] , 'expireson' => isset( $value[ 'to_date' ] ) ? $value[ 'to_date' ] : "" ) ;
                }
            }
        }
        $membership_level = sumo_get_membership_levels() ;
        $user_id          = get_post_meta( $postid , 'sumomemberships_userid' , true ) ;
        $username         = '' ;
        $useremail        = '' ;
        if( $user_id != '' ) {
            $user_info = get_userdata( $user_id ) ;
            if( $user_info ) {
                $username  = $user_info->user_login ;
                $useremail = $user_info->user_email ;
            } else {
                $username  = '' ;
                $useremail = '' ;
            }
        }
        switch( $column ) {
            case 'user_name':
                echo $username ;
                break ;
            case 'user_email':
                echo $useremail ;
                break ;
            case 'plan_name':
                if( ! empty( $saved_plans ) && ! empty( $membership_level ) ) {
                    foreach( $membership_level as $key => $value ) {
                        if( array_key_exists( $key , $saved_plans ) ) {
                            if( $saved_plans[ $key ][ 'planstatus' ] == 'paused' ) {
                                echo $value . ' / <mark style="display: table-cell;font: 15px arial,sans-serif;height: 20px;text-align: center;width: 50px;padding: 3px; border-radius: 10px;background-color:blue;color:white;">' . __( 'Paused' , 'sumomemberships' ) . '</mark></br>' ;
                            } elseif( $saved_plans[ $key ][ 'planstatus' ] == 'active' ) {
                                echo $value . ' / <mark style="display: table-cell;font: 15px arial,sans-serif;height: 20px;text-align: center;width: 50px;padding: 3px; border-radius: 10px;background-color:green;color:white;">' . __( 'Active' , 'sumomemberships' ) . '</mark></br>' ;
                            } else {
                                echo $value . ' / <mark style="display: table-cell;font: 15px arial,sans-serif;height: 20px;text-align: center;width: 50px;padding: 3px; border-radius: 10px;background-color:red;color:white;">' . __( ucfirst( $saved_plans[ $key ][ 'planstatus' ] ) , 'sumomemberships' ) . '</mark></br>' ;
                            }
                        }
                    }
                } else {
                    echo '--' ;
                }
                break ;
            case 'member_since':
                $dateformat = get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ;
                if( ! empty( $saved_plans ) && ! empty( $membership_level ) ) {
                    foreach( $membership_level as $key => $value ) {
                        if( array_key_exists( $key , $saved_plans ) ) {
                            $gmt_offset = (2 == get_option('sumomemberships_member_since_display_type_in_post_table',1) ) ? (int) ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ):0;
                            $member_since_in_time = strtotime($saved_plans[ $key ][ 'membersince' ])+ $gmt_offset;
                            echo $value . ' / <mark style="display: table-cell;font: 15px arial,sans-serif;height: 20px;text-align: center;width: 50px;padding: 3px; border-radius: 10px;background-color:violet;color:white;">' . date($dateformat,$member_since_in_time) . '</mark></br>' ;
                        }
                    }
                } else {
                    echo '--' ;
                }
                break ;
            case 'expires_on':
                if( ! empty( $saved_plans ) && ! empty( $membership_level ) ) {
                    foreach( $membership_level as $key => $value ) {
                        if( array_key_exists( $key , $saved_plans ) ) {
                            $subscription_id     = $saved_plans[ $key ][ 'subsc_id' ] ;
                            $subscription_number = get_post_meta( $subscription_id , 'sumo_get_subscription_number' , true ) ;
                            if( $subscription_id > 0 && $subscription_number > 0 ) {
                                echo $value . ' / <mark style="display: table-cell;font: 15px arial,sans-serif;height: 20px;text-align: center;width: 50px;padding: 3px; border-radius: 10px;background-color:orange;color:white;">Linked with Subscription #' . $subscription_number . '</mark></br>' ;
                            } else if( $saved_plans[ $key ][ 'expireson' ] == "" ) {
                                if( $saved_plans[ $key ][ 'planstatus' ] == 'cancelled' || $saved_plans[ $key ][ 'planstatus' ] == 'expired' ) {
                                    echo $value . ' / <mark style="display: table-cell;font: 15px arial,sans-serif;height: 20px;text-align: center;width: 50px;padding: 3px; border-radius: 10px;background-color:orange;color:white;">--</mark></br>' ;
                                } else {
                                    echo $value . ' / <mark style="display: table-cell;font: 15px arial,sans-serif;height: 20px;text-align: center;width: 50px;padding: 3px; border-radius: 10px;background-color:orange;color:white;">Never Expires</mark></br>' ;
                                }
                            } else {
                                if( $saved_plans[ $key ][ 'planstatus' ] == "paused" ) {
                                    echo $value . ' / <mark style="display: table-cell;font: 15px arial,sans-serif;height: 20px;text-align: center;width: 50px;padding: 3px; border-radius: 10px;background-color:orange;color:white;">--</mark></br>' ;
                                } else {
                                    echo $value . ' / <mark style="display: table-cell;font: 15px arial,sans-serif;height: 20px;text-align: center;width: 50px;padding: 3px; border-radius: 10px;background-color:orange;color:white;">' . $saved_plans[ $key ][ 'expireson' ] . '</mark></br>' ;
                                }
                            }
                        }
                    }
                } else {
                    echo '--' ;
                }
                break ;
        }
    }

    public function display_master_logs( $column , $postid ) {
        switch( $column ) {
            case 'user_name':
                $get_user_name = get_post_meta( $postid , 'sumo_username_for_masterlog' , true ) ;
                if( $get_user_name != '' ) {
                    $userdetail = get_userdata( $get_user_name ) ;
                    if( $userdetail ) {
                        $username = $userdetail->user_login ;
                    } else {
                        $username = '' ;
                    }
                } else {
                    $username = '' ;
                }
                echo $username ;
                break ;

            case 'plan_name':
                echo get_post_meta( $postid , 'sumo_planname_for_masterlog' , true ) ;
                break ;

            case 'event':
                echo get_post_meta( $postid , 'sumo_event_for_masterlog' , true ) ;
                break ;
        }
    }

    public function remove_bulk_actions( $actions ) {
        global $current_screen ;

        if( isset( $current_screen->post_type ) ) {
            if( $current_screen->post_type == 'sumomem_masterlog' ) {
                unset( $actions[ 'edit' ] ) ;
            } else {
                ?>
                <style>
                    .bulkactions{
                        display: none;
                    }
                    .check-column{
                        display: none;
                    }
                </style>
                <?php
                unset( $actions[ 'edit' ] ) ;
                unset( $actions[ 'trash' ] ) ;
            }
        }
        return $actions ;
    }

    public function remove_row_actions( $actions , $post ) {
        global $current_screen ;

        $key          = '' ;
        $title        = _draft_or_post_title() ;
        $oldmetavalue = get_post_meta( $post->ID , 'sumomemberships_saved_plans' , true ) ;

        if( $oldmetavalue == '' ) {
            $edit_link = get_edit_post_link( $post->ID ) . '&tab=add_plan_tab' ;
        } else {
            if( is_array( $oldmetavalue ) && ! empty( $oldmetavalue ) ) {
                foreach( $oldmetavalue as $uniqueid => $data ) {
                    if( isset( $data[ 'choose_plan' ] ) && $data[ 'choose_plan' ] != '' ) {
                        $key = $uniqueid ;
                    }
                }
            } else {
                $key = '' ;
            }
            $edit_link = get_edit_post_link( $post->ID ) . '&tab=add_plan_tab' . $key ;
        }
        if( isset( $current_screen->post_type ) ) {
            if( $current_screen->post_type == 'sumomembershipplans' ) {
                $plan_id                   = $post->ID ;
                $is_plan_active_for_member = sumo_get_plan_purchased_members( $plan_id , true ) > 0 ? true : false ;
                //Disabled Trash when Plan is Active for Member
                if( $is_plan_active_for_member ) {
                    ?>
                    <script type="text/javascript">
                        jQuery( document ).ready( function() {
                            jQuery( '#post-<?php echo $plan_id ; ?> .submitdelete' ).wrap( "<strike>" ) ;
                            jQuery( '#post-<?php echo $plan_id ; ?> .submitdelete' ).removeAttr( "href" ) ;
                        } ) ;
                    </script>
                    <?php
                }

                unset( $actions[ 'inline hide-if-no-js' ] ) ;
                unset( $actions[ 'view' ] ) ;
            }
            if( $current_screen->post_type == 'sumomembers' ) {
                $member_post_id = $post->ID ;

                if( sumo_is_member_has_active_r_paused_plan( $member_post_id ) ) {
                    ?>
                    <script type="text/javascript">
                        jQuery( document ).ready( function() {
                            jQuery( '#post-<?php echo $member_post_id ; ?> .submitdelete' ).wrap( "<strike>" ) ;
                            jQuery( '#post-<?php echo $member_post_id ; ?> .submitdelete' ).removeAttr( "href" ) ;
                        } ) ;
                    </script>
                    <?php
                }
                $actions[ 'edit' ] = sprintf(
                        '<a href="%s" aria-label="%s">%s</a>' , $edit_link ,
                        /* translators: %s: post title */ esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ) , $title ) ) , __( 'Edit' )
                        ) ;
                unset( $actions[ 'inline hide-if-no-js' ] ) ;
                unset( $actions[ 'view' ] ) ;
            }
            if( $current_screen->post_type == 'sumomem_masterlog' ) {
                unset( $actions[ 'edit' ] ) ;
                unset( $actions[ 'inline hide-if-no-js' ] ) ;
            }
        }
        return $actions ;
    }

    public function enter_title_here( $text , $post ) {
        switch( $post->post_type ) {
            case 'sumomembershipplans' :
                $text = __( 'Plan name' , 'sumomemberships' ) ;
                break ;
        }

        return $text ;
    }

    public function delete_user_details( $userid ) {
        $args        = array( 'post_type' => 'sumomembers' , 'numberposts' => '-1' , 'meta_query' => array( array( 'key' => 'sumomemberships_userid' , 'compare' => 'EXISTS' , 'value' => $userid ) ) , 'post_status' => 'published' , 'fields' => 'ids' , 'cache_results' => false ) ;
        $all_post_id = get_posts( $args ) ;
        foreach( $all_post_id as $postid ) {
            $getdatatodisplay = get_post_meta( $postid , 'sumomemberships_saved_plans' , true ) ;
            if( is_array( $getdatatodisplay ) && ! empty( $getdatatodisplay ) ) {
                foreach( $getdatatodisplay as $key => $value ) {
                    if( isset( $value[ 'choose_plan' ] ) && $value[ 'choose_plan' ] != '' ) {
                        $plan_id = $value[ 'choose_plan' ] ;
                        wp_clear_scheduled_hook( 'sumo_memberships_process_plan_duration_validity' , array( ( int ) $userid , ( int ) $plan_id ) ) ;

                        if( isset( $value[ 'scheduled_link_plans' ] ) && is_array( $value[ 'scheduled_link_plans' ] ) ) {
                            $link_plan = $value[ 'scheduled_link_plans' ] ;
                            foreach( $link_plan as $each_link_plan ) {
                                if( $each_link_plan != '' ) {
                                    wp_clear_scheduled_hook( 'sumo_memberships_process_linked_plan_privilege' , array( ( int ) $userid , ( int ) $each_link_plan , ( int ) $plan_id ) ) ;
                                }
                            }
                        }

                        wp_clear_scheduled_hook( 'sumomemberships_schedule_before_expiry' , array( ( int ) $plan_id , ( string ) $key , ( int ) $postid ) ) ;
                    }
                }
            }
            wp_delete_post( $postid ) ;
        }

        $args        = array( 'post_type' => 'sumomem_masterlog' , 'numberposts' => '-1' , 'meta_query' => array( array( 'key' => 'sumo_username_for_masterlog' , 'compare' => 'EXISTS' , 'value' => $userid ) ) , 'post_status' => 'published' , 'fields' => 'ids' , 'cache_results' => false ) ;
        $all_post_id = get_posts( $args ) ;
        foreach( $all_post_id as $postid ) {
            wp_delete_post( $postid ) ;
        }
    }

}

new SUMOMemberships_CPT() ;
