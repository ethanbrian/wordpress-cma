<?php

class SUMOMemberships_Account_Page {

    public static $membership_endpoint = 'sumomembership' ;

    public function __construct() {
        if( get_option( 'sumomemberships_enable_customization_in_myaccount' ) != 'no' ) {
            add_action( 'init' , array( __CLASS__ , 'custom_rewrite_endpoint' ) ) ;
            add_filter( 'query_vars' , array( __CLASS__ , 'custom_query_vars' ) , 0 ) ;
            add_filter( 'woocommerce_account_menu_items' , array( __CLASS__ , 'custom_myaccount_menu' ) ) ;
            add_filter( 'the_title' , array( __CLASS__ , 'customize_menu_title' ) ) ;
        }
        add_action( 'wp' , array( $this , 'do_something_when_actions_made' ) ) ;
        add_action( 'wp_ajax_sumo_transfer_membership_plan_request' , array( $this , 'AJAX_transfer_membership_plan' ) ) ;
        add_action( 'wp_ajax_sumo_search_wordpress_users' , array( $this , 'sumo_search_wp_users' ) ) ;

        add_filter( 'query_vars' , array( $this , 'add_custom_query_var' ) ) ;
        add_action( 'init' , array( $this , 'add_custom_end_point' ) , 10 ) ;
        //Compatible with Woocommerce v2.6.x and above
        add_action( 'woocommerce_account_link-users_endpoint' , array( $this , 'link_users_info1' ) ) ;
        //Compatible up to Woocommerce v2.5.x
        add_filter( 'wc_get_template' , array( $this , 'link_users_info2' ) , 10 , 5 ) ;
        add_action( 'wp_ajax_sumo_link_membership_plan_from_myaccount_page' , array( $this , 'AJAX_link_membership_plan' ) ) ;

        add_action( 'woocommerce_account_' . self::$membership_endpoint . '_endpoint' , array( $this , 'display_membership_information' ) , 11 ) ;
    }

    /**
     * Add custom query var.
     * @param array $vars
     * @return array
     */
    public static function add_custom_query_var( $vars ) {
        $vars[] = "link-users" ;
        return $vars ;
    }

    public static function custom_rewrite_endpoint() {
        add_rewrite_endpoint( self::$membership_endpoint , EP_ROOT | EP_PAGES ) ;
    }

    public static function custom_query_vars( $vars ) {
        $vars[] = self::$membership_endpoint ;

        return $vars ;
    }

    public static function custom_myaccount_menu( $menus ) {
        if( ! is_user_logged_in() )
            return $menus ;

        $membership_menu = array( self::$membership_endpoint => get_option( 'sumo_localization_msg_for_menu' , 'My Membership' ) ) ;
        $menus           = sumomemberships_customize_array_position( $menus , 'dashboard' , $membership_menu ) ;
        return $menus ;
    }

    public static function customize_menu_title( $title ) {
        global $wp_query ;

        if( is_object( $wp_query ) && is_main_query() && in_the_loop() && is_account_page() ) {
            if( isset( $wp_query->query_vars[ self::$membership_endpoint ] ) )
                $title = get_option( 'sumo_localization_msg_for_menu' , 'My Membership' ) ;

            remove_filter( 'the_title' , array( __CLASS__ , 'customize_menu_title' ) ) ;
        }

        return $title ;
    }

    /**
     * Add custom end point.
     * @global object $wp_rewrite
     */
    public static function add_custom_end_point() {

        add_rewrite_endpoint( "link-users" , EP_ROOT | EP_PAGES ) ;
        
        $do_flush = get_option( 'sumo_flush_rewrite_rules' , 1 ) ;
        
        if( $do_flush ) {
            update_option( 'sumo_flush_rewrite_rules' , 0 ) ;
            flush_rewrite_rules() ;
        }
    }

    public static function link_users_info1() {
        global $wp ;

        if( WC()->version >= 2.6 ) {

            $plan_id = $wp->query_vars[ 'link-users' ] ;



            wc_get_template( 'link_users_info.php' , array(
                'plan_id' => $plan_id ,
            ) ) ;
        }
    }

    public static function link_users_info2( $located , $template_name , $args , $template_path , $default_path ) {
        global $wp ;

        if( WC()->version < 2.6 && isset( $_GET[ 'plan-id' ] ) ) {

            $plan_id = $_GET[ 'plan-id' ] > 0 ? $_GET[ 'plan-id' ] : 0 ;

            if( $plan_id > 0 ) {

                $wp->query_vars[ 'link-users' ] = $plan_id ;

                return plugin_dir_path( __FILE__ ) . '/templates/link_users_info.php' ;
            }
        }
        return $located ;
    }

    public static function sumo_search_wp_users() {
        $json_ids  = array() ;
        $args      = array(
            'offset'                 => 0 ,
            's'                      => $_REQUEST[ 'term' ] ,
            'blog_id'                => $GLOBALS[ 'blog_id' ] ,
            'role'                   => '' ,
            'role__in'               => array() ,
            'role__not_in'           => array() ,
            'meta_key'               => '' ,
            'meta_value'             => '' ,
            'meta_compare'           => '' ,
            'meta_query'             => array() ,
            'date_query'             => array() ,
            'include'                => array() ,
            'exclude'                => array() ,
            'orderby'                => 'display_name' ,
            'order'                  => 'ASC' ,
            'search'                 => '*' . $_REQUEST[ 'term' ] . '*' ,
            'search_columns'         => array( 'user_login' , 'user_email' , 'user_nicename' , 'display_name' ) ,
            'number'                 => '' ,
            'count_total'            => false ,
            'fields'                 => 'all' ,
            'who'                    => '' ,
            'update_post_term_cache' => false ,
            'update_post_post_cache' => false ,
            'cache_results'          => false ,
                ) ;
        $get_users = get_users( $args ) ;
        foreach( $get_users as $user ) {
            $user_string           = ( string ) $user->display_name . ' #' . $user->user_email ;
            $json_ids[ $user->ID ] = $user_string ;
        }
        wp_send_json( $json_ids ) ;
        exit() ;
    }

    public static function check_is_data_available( $member_id ) {

        $saved_plans = get_post_meta( $member_id , 'sumomemberships_saved_plans' , true ) ;
        $returnvalue = false ;
        if( is_array( $saved_plans ) && ! empty( $saved_plans ) ) {
            foreach( $saved_plans as $key => $value ) {
                if( $value[ 'choose_plan' ] != '' ) {
                    return true ;
                }
            }
        }
        return $returnvalue ;
    }

    public static function display_membership_information() {

        $this_member_id = get_current_user_id() ;

        $post_id = sumo_get_member_post_id( $this_member_id ) ;

        $saved_plans = get_post_meta( $post_id , 'sumomemberships_saved_plans' , true ) ;

        if( is_user_logged_in() ) {
            ?>
            <h2><?php esc_html_e( get_option( 'sumo_localization_msg_for_table_title' , 'My Memberships' ) ) ; ?></h2>
            <?php if( is_array( $saved_plans ) && ! empty( $saved_plans ) && SUMOMemberships_Account_Page::check_is_data_available( $post_id ) ) { ?>

                <table class="shop_table shop_table_responsive my_account_orders">

                    <thead>
                        <tr>
                            <?php if( get_option( 'sumo_show_r_hide_plan_name' , '1' ) == '1' ) { ?>
                                <th class="sumomemberships-plan"><span class="nobr" style="text-align:left; white-space:nowrap;"><?php esc_html_e( get_option( 'sumo_localization_msg_for_planname' , 'Plan Name' ) ) ; ?></span></th>
                                <?php
                            }

                            if( get_option( 'sumo_allow_member_show_linked_plans' ) == "yes" ) {
                                ?>
                                <th class="sumomemberships-linked-plans"><span class="nobr" style="text-align:left; white-space:nowrap;"><?php esc_html_e( 'Linked Plans' , 'sumomemberships' ) ; ?></span></th>
                                <?php
                            }

                            if( get_option( 'sumo_show_r_hide_plan_duration' , '1' ) == '1' ) {
                                ?>
                                <th class="sumomemberships-plan_duration"><span class="nobr" style="text-align:left; white-space:nowrap;"><?php esc_html_e( get_option( 'sumo_localization_msg_for_planduration' , 'Expires On' ) ) ; ?></span></th>
                                <?php
                            }

                            if( get_option( 'sumo_show_r_hide_plan_status' , '1' ) == '1' ) {
                                ?>
                                <th class="sumomemberships-status"><span class="nobr" style="text-align:left; white-space:nowrap;"><?php esc_html_e( get_option( 'sumo_localization_msg_for_status' , 'Status' ) ) ; ?></span></th>
                <?php }
                ?>
                            <th class="sumomemberships-actions">&nbsp;</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php
                        foreach( $saved_plans as $unique_key => $each_plan ) {

                            $check = get_post_meta( get_current_user_id() , 'pending_status_' . $unique_key , true ) ;

                            if( isset( $each_plan[ 'choose_plan' ] ) && isset( $each_plan[ 'choose_status' ] ) && $each_plan[ 'choose_plan' ] > 0 ) {

                                $plan_id                 = $each_plan[ 'choose_plan' ] ;
                                $plan_status             = $each_plan[ 'choose_status' ] ;
                                $dateformat              = get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ;
                                $plan_duration           = '' != $each_plan[ 'to_date' ] ? date( $dateformat , strtotime( $each_plan[ 'to_date' ] ) ) : '' ;
                                $is_subscription_enabled = isset( $each_plan[ 'associated_subsc_id' ] ) && $each_plan[ 'associated_subsc_id' ] > 0 ? true : false ;
                                ?>
                                <tr class="membr">

                        <?php if( get_option( 'sumo_show_r_hide_plan_name' , '1' ) == '1' ) { ?>
                                        <td class="sumomemberships-plan" data-title="<?php _e( 'Plan Name' , 'sumomemberships' ) ; ?>">
                                        <?php echo get_post_meta( $plan_id , 'sumomemberships_plan_name' , true ) ; ?>
                                        </td>

                                        <?php
                                    }

                                    if( get_option( 'sumo_allow_member_show_linked_plans' ) == "yes" ) {

                                        $available_linked_plans = sumo_get_available_linked_plans( $post_id ) ;

                                        $plan_name = array() ;

                                        if( is_array( $each_plan [ "link_plans" ] ) && array_filter( $each_plan [ "link_plans" ] ) ) {

                                            foreach( $each_plan [ "link_plans" ] as $plan_id ) {

                                                if( in_array( $plan_id , $available_linked_plans ) ) {

                                                    $plan_name[] = get_post_meta( $plan_id , 'sumomemberships_plan_name' , true ) ;
                                                }
                                            }
                                            ?>

                                            <td class="sumomemberships-linked-plans"> 

                                            <?php echo esc_html( implode( ' , ' , $plan_name ) ) ;
                                            ?>
                                            </td>

                                <?php
                            } else {
                                ?>

                                            <td class="sumomemberships-linked-plans"> 

                                            <?php echo ' - ' ;
                                            ?>
                                            </td>

                                            <?php
                                        }
                                    }
                                    if( get_option( 'sumo_show_r_hide_plan_duration' , '1' ) == '1' ) {
                                        ?>

                                        <td class="sumomemberships-plan_duration" data-title="<?php _e( 'Expires On' , 'sumomemberships' ) ; ?>">
                                            <?php
                                            if( $plan_status == 'active' ) {
                                                if( $is_subscription_enabled ) {
                                                    $subscription_number = get_post_meta( $each_plan[ 'associated_subsc_id' ] , 'sumo_get_subscription_number' , true ) ;
                                                    echo _e( "This Plan is linked with Subscription #$subscription_number" , 'sumomemberships' ) ;
                                                } else {
                                                    echo $plan_duration == "" ? _e( "Never Expires" , 'sumomemberships' ) : $plan_duration ;
                                                }
                                            } else {
                                                echo '--' ;
                                            }
                                            ?>
                                        </td>

                                        <?php
                                    }

                                    if( get_option( 'sumo_show_r_hide_plan_status' , '1' ) == '1' ) {
                                        ?>

                                        <td class="sumomemberships-status" data-title="<?php _e( 'Status' , 'sumomemberships' ) ; ?>" style="text-align:left; white-space:nowrap;">

                                            <?php
                                            if( $plan_status == 'paused' && ! sumo_is_global_plan_status_active( $plan_id ) ) {
                                                echo _e( 'Disabled by Admin' , 'sumomemberships' ) ;
                                            } else {
                                                echo __( ucfirst( $plan_status ) , 'sumomemberships' ) ;
                                            }
                                            ?>

                                        </td>

                                        <?php } ?>

                                    <td class="sumomemberships-actions">
                                        <?php
                                        $actions = array() ;

                                        if( ! $is_subscription_enabled && get_option( 'sumomemberships_pause_resume_option' ) == "yes" ) {

                                            if( $plan_status == 'paused' && ! sumo_is_global_plan_status_active( $plan_id ) ) {

                                                $actions = array() ;
                                            } else if( $plan_status == 'active' ) {

                                                $actions[ 'pause' ] = array(
                                                    'url'  => add_query_arg( array( 'plan_id' => $plan_id , 'action' => 'pause' , '_mynonce' => wp_create_nonce( "$plan_id" ) ) ) ,
                                                    'name' => __( 'Pause' , 'sumomemberships' )
                                                        ) ;
                                            } else if( $plan_status == 'paused' ) {

                                                $actions[ 'resume' ] = array(
                                                    'url'  => add_query_arg( array( 'plan_id' => $plan_id , 'action' => 'resume' , '_mynonce' => wp_create_nonce( "$plan_id" ) ) ) ,
                                                    'name' => __( 'Resume' , 'sumomemberships' )
                                                        ) ;
                                            }
                                        }

                                        if( $actions ) {

                                            foreach( $actions as $key => $action ) {

                                                if( $check != 'yes' ) {
                                                    echo '<a href="' . esc_url( $action[ 'url' ] ) . '" class="button ' . sanitize_html_class( $key ) . '">' . esc_html( $action[ 'name' ] ) . '</a>' ;
                                                }
                                            }
                                        }
                                        ?>
                                    </td>

                                    <?php
                                    $restrict_transfer = '' ;
                                    $restrict_linking  = '' ;

                                    if( get_option( 'sumomemberships_transfer_option' ) == 'yes' ) {

                                        $linked_users   = get_post_meta( $post_id , 'sumo_linked_users_of_' . $plan_id , true ) ;
                                        $allow_transfer = get_option( 'sumo_allow_transfer_when_users_linked_with_plan' ) ;

                                        $default_plans = ! is_array( get_option( 'sumomemberships_default_plans' ) ) ? explode( ',' , get_option( 'sumomemberships_default_plans' ) ) : get_option( 'sumomemberships_default_plans' ) ;
                                        if( ! empty( $default_plans ) ) {

                                            if( in_array( $plan_id , $default_plans ) ) {

                                                if( get_option( 'sumo_restrict_trans_plan_for_dmp' ) == 'yes' ) {

                                                    $restrict_transfer = 'yes' ;
                                                }
                                                if( get_option( 'sumo_restrict_link_users_for_dmp' ) == 'yes' ) {

                                                    $restrict_linking = 'yes' ;
                                                }
                                            }
                                        }

                                        if( $allow_transfer == '2' ) {

                                            $check_for_linked_users = (is_array( $linked_users ) && ! empty( $linked_users )) ? false : true ;
                                            if( $check_for_linked_users ) {
                                                ?>
                                                <td>
                                                    <?php
                                                    if( $plan_status == 'active' ) {

                                                        if( $check == 'yes' ) {

                                                            echo 'Awaiting for transfer' ;
                                                        } else {

                                                            if( $restrict_transfer != 'yes' ) {
                                                                ?>
                                                                <input type="button" value="Transfer Membership" data-multiple="false" id="sumomemberships_transfer_plan_button<?php echo $unique_key ; ?>" data-uniqid='<?php echo $unique_key ; ?>' data-plan_id="<?php echo $plan_id ?>" class="sumomemberships_transfer_plan_button button view"/>
                                                                <?php
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </td>
                                                <?php
                                            }
                                        } else {
                                            ?>
                                            <td>
                                                <?php
                                                if( $plan_status == 'active' ) {

                                                    if( $check == 'yes' ) {

                                                        echo 'Awaiting for transfer' ;
                                                    } else {

                                                        $allow_access_for_linked_users = (is_array( $linked_users ) && ! empty( $linked_users )) ? 'yes' : 'no' ;
                                                        if( $restrict_transfer != 'yes' ) {
                                                            ?>
                                                            <input type="button" value="Transfer Membership" data-multiple="false" id="sumomemberships_transfer_plan_button<?php echo $unique_key ; ?>" data-uniqid='<?php echo $unique_key ; ?>' data-sumo_aatls="<?php echo $allow_access_for_linked_users ?>" class="sumomemberships_transfer_plan_button button view"/>
                                                            <?php
                                                        }
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <?php
                                        }
                                    }
                                    if( get_option( 'sumo_allow_member_link_users_with_plan' ) == '1' ) {
                                        ?>
                                        <td>
                                            <?php
                                            if( WC()->version < 2.6 ) {

                                                $url = add_query_arg( array( 'q' => 'link-users' , 'plan-id' => $plan_id ) ) ;
                                            } else {

                                                $url = wc_get_endpoint_url( 'link-users' , $plan_id , wc_get_page_permalink( 'myaccount' ) ) ;
                                            }
                                            if( $check !== 'yes' ) {

                                                if( $restrict_linking != 'yes' ) {

                                                    echo "<a href='$url' class='button'>" . __( 'Link Users' , 'sumomemberships' ) . "</a>" ;
                                                }
                                            }
                                            ?>
                                        </td>
                                <?php } ?>
                                </tr>
                                <?php
                            }
                        }
                        if( ( float ) WC()->version >= ( float ) '3.0.0' ) {
                            ?>
                        <style type="text/css">
                            .select2-container--open .select2-dropdown--below{
                                z-index: 9999
                            }
                        </style>

                    <?php
                }
                ?>
                    <script type="text/javascript">

                        jQuery( function() {

                            jQuery( '.sumomemberships_transfer_plan_button' ).click( function() {
                                var unique_key = jQuery( this ).attr( 'data-uniqid' ) ;
                                var value = jQuery( this ).attr( 'data-sumo_aatls' ) ;
                                var html = '' ;
                                var selecttwo = '' ;
                <?php if( WC()->version < 3.0 ) {
                    ?>
                                    selecttwo = '<input type="text" name="sumo_select_user' + unique_key + '" id="sumo_select_user' + unique_key + '" value="">' ;
                    <?php
                } else {
                    ?>
                                    selecttwo = '<select name="sumo_select_user' + unique_key + '" style="width:300px; z-index:9999" id="sumo_select_user' + unique_key + '"></select>' ;
                <?php }
                ?>
                                if( value === 'yes' ) {

                                    html = '<input type = checkbox name = "allow_access_to_linked_users" id = "allow_access_to_linked_users" value = "yes"><?php echo __( 'Allow Access to Linked Users' , 'sumomemberships' ) ; ?>' ;
                                }
                                swal( {
                                    title : '<i><?php _e( 'Select User' , '' ) ?> </i>' ,
                                    html : selecttwo + html ,
                                    showCloseButton : true ,
                                    showCancelButton : true ,
                                    confirmButtonText : '<i class="fa fa-thumbs-up"></i>Confim Transfer' ,
                                    cancelButtonText : '<i class="fa fa-thumbs-down"></i>Cancel'
                                } ).then( function( isConfirm ) {
                                    if( isConfirm ) {
                                        var user_id = jQuery( '#sumo_select_user' + unique_key ).val() ;
                                        var current_user_id = "<?php echo $this_member_id ; ?>" ;
                                        if( user_id !== "" ) {
                                            var sumo_aatlu = jQuery( '#allow_access_to_linked_users' ).prop( 'checked' ) ;
                                            var access_lu = '' ;
                                            if( sumo_aatlu ) {
                                                access_lu = 'yes' ;
                                            }
                                            if( user_id != current_user_id ) {
                                                swal( {
                                                    title : 'Are you sure?' ,
                                                    text : "You won't be able to revert this!" ,
                                                    type : 'warning' ,
                                                    showCancelButton : true ,
                                                    confirmButtonColor : '#3085d6' ,
                                                    cancelButtonColor : '#d33' ,
                                                    confirmButtonText : 'Yes, Transfer it!'
                                                } ).then( function( isConfirm ) {
                                                    if( isConfirm ) {
                                                        var dataparam = ( {
                                                            action : 'sumo_transfer_membership_plan_request' ,
                                                            uniqid : unique_key ,
                                                            userid : user_id ,
                                                            access_lu : access_lu ,
                                                            postid : "<?php echo sumo_get_member_post_id( $this_member_id ) ; ?>"
                                                        } ) ;
                                                        jQuery.post( "<?php echo admin_url( 'admin-ajax.php' ) ; ?>" , dataparam ,
                                                                function( response ) {

                                                                } ).then( function( response ) {
                                                            if( response == 555 ) {
                                                                swal( {
                                                                    title : "This User had this Plan Already!" ,
                                                                    type : "error"
                                                                } ) ;
                                                            } else {
                                                                swal( {
                                                                    title : "Request Submitted!" ,
                                                                    text : "Membership Plan Transfered after Admin approves." ,
                                                                    type : "success" ,
                                                                    showConfirmButton : false
                                                                } ) ;
                                                                window.location.reload( true ) ;
                                                            }
                                                        } ) ;

                                                    } else {
                                                        swal( {
                                                            title : "Cancelled!" ,
                                                            type : "error"
                                                        } ) ;
                                                    }
                                                } ) ;
                                            } else {
                                                swal( {
                                                    title : "Please Select the different User!" ,
                                                    type : "error"
                                                } ) ;
                                            }
                                        } else {
                                            swal( {
                                                title : "Please Select the User!" ,
                                                type : "error"
                                            } ) ;
                                        }
                                    }
                                } ) ;

                <?php if( WC()->version < 3.0 ) { ?>

                                    jQuery( "#sumo_select_user" + unique_key ).select2( {
                                        placeholder : "Enter atleast 3 characters" ,
                                        allowClear : true ,
                                        enable : false ,
                                        maximumSelectionSize : 1 ,
                                        readonly : false ,
                                        multiple : false ,
                                        initSelection : function( data , callback ) {
                                            var data_show = {
                                                id : data.val() ,
                                                text : data.attr( 'data-selected' )
                                            } ;
                                            if( data.val() > 0 ) {
                                                return callback( data_show ) ;
                                            }
                                        } ,
                                        minimumInputLength : 3 ,
                                        tags : [ ] ,
                                        ajax : {
                                            url : '<?php echo admin_url( 'admin-ajax.php' ) ; ?>' ,
                                            dataType : 'json' ,
                                            type : "GET" ,
                                            quietMillis : 250 ,
                                            data : function( term ) {
                                                return {
                                                    term : term ,
                                                    action : "sumo_search_wordpress_users"
                                                } ;
                                            } ,
                                            results : function( data ) {
                                                var terms = [ ] ;
                                                if( data ) {
                                                    jQuery.each( data , function( id , text ) {
                                                        terms.push( {
                                                            id : id ,
                                                            text : text
                                                        } ) ;
                                                    } ) ;
                                                }
                                                return { results : terms } ;
                                            }
                                        }
                                    } ) ;
                <?php } else {
                    ?>
                                    jQuery( "#sumo_select_user" + unique_key ).select2( {
                                        placeholder : "Enter atleast 3 characters" ,
                                        allowClear : true ,
                                        minimumInputLength : 3 ,
                                        escapeMarkup : function( m ) {
                                            return m ;
                                        } ,
                                        ajax : {
                                            url : '<?php echo admin_url( 'admin-ajax.php' ) ; ?>' ,
                                            dataType : 'json' ,
                                            quietMillis : 250 ,
                                            data : function( params ) {
                                                return {
                                                    term : params.term ,
                                                    action : 'sumo_search_wordpress_users'
                                                } ;
                                            } ,
                                            processResults : function( data ) {
                                                var terms = [ ] ;
                                                if( data ) {
                                                    jQuery.each( data , function( id , text ) {
                                                        terms.push( {
                                                            id : id ,
                                                            text : text
                                                        } ) ;
                                                    } ) ;
                                                }
                                                return {
                                                    results : terms
                                                } ;
                                            } ,
                                            cache : true
                                        }
                                    } ) ;
                <?php }
                ?>
                            } ) ;
                        } ) ;

                    </script>

                </tbody>

                </table>

                <?php
            } else {
                ?>
                <div class="sumomemberships_not_found">

                    <p>
                <?php _e( "You Don't have any Memberships" , 'sumomemberships' ) ; ?>
                    </p>
                </div>
                <?php
            }
        } else {

            echo get_option( 'sumo_msg_for_guest_access' ) ;
        }
    }

    public static function AJAX_transfer_membership_plan() {
        if( isset( $_POST[ 'uniqid' ] ) && isset( $_POST[ 'postid' ] ) && isset( $_POST[ 'userid' ] ) ) {
            $postid          = $_POST[ 'postid' ] ;
            $access_lu       = $_POST[ 'access_lu' ] ;
            $current_user_id = get_post_meta( $postid , 'sumomemberships_userid' , true ) ;
            $uniqid          = $_POST[ 'uniqid' ] ;
            $getdata         = get_post_meta( $postid , 'sumomemberships_saved_plans' , true ) ;
            $plan_id         = $getdata[ $uniqid ][ 'choose_plan' ] ;
            $userid          = $_POST[ 'userid' ] ;
            $new_post_id     = sumo_get_member_post_id( $userid ) ;
            if( $new_post_id > 0 ) {
                if( ! sumo_plan_is_already_had( $plan_id , $new_post_id ) ) {
                    echo 555 ;
                    exit() ;
                }
            }
            update_post_meta( $current_user_id , 'pending_status_' . $uniqid , 'yes' ) ;
            $array              = ( array ) get_option( 'sumo_plan_transfer_requests' ) ;
            $new_index          = array( 'post_id' => $postid , 'unique_id' => $uniqid , 'user_id' => $userid , 'access_lu' => $access_lu ) ;
            $array1             = array_filter( $array ) ;
            $array1[ uniqid() ] = $new_index ;
            update_option( 'sumo_plan_transfer_requests' , $array1 ) ;
            $sumo_email_for     = 'sumo_ps_submitted' ;
            $to                 = get_userdata( $current_user_id )->user_email ;
            $planname           = get_the_title( $plan_id ) ;
            $plan_receiver      = get_userdata( $userid )->user_email ;
            sumo_email_for_transfer_plans( $sumo_email_for , $to , $plan_receiver , $planname ) ;
        }
        exit() ;
    }

    public static function AJAX_link_membership_plan() {
        if( isset( $_POST[ 'postid' ] ) ) {
            $post_id       = $_POST[ 'postid' ] ;
            $users         = is_array( $_POST[ 'users' ] ) ? $_POST[ 'users' ] : explode( ',' , $_POST[ 'users' ] ) ;
            $plan_id       = $_POST[ 'plan_id' ] ;
            $linked_users1 = get_post_meta( $post_id , 'sumo_linked_users_of_' . $plan_id , true ) ;
            $linked_users  = is_array( $linked_users1 ) ? $linked_users1 : array() ;
            $post_author   = get_post_meta( $post_id , 'sumomemberships_userid' , true ) ;
            foreach( $users as $user_id ) {
                $check = SUMOMemberships_Admin_Meta_Boxes::sumo_check_user_has_already_this_plan( $user_id , $plan_id ) ;
                if( $check ) {
                    if( $post_author != $user_id ) {
                        if( ! empty( $linked_users ) ) {
                            if( ! in_array( $user_id , $linked_users ) ) {
                                array_push( $linked_users , $user_id ) ;
                            }
                        } else {
                            array_push( $linked_users , $user_id ) ;
                        }
                    }
                }
            }
            update_post_meta( $post_id , 'sumo_linked_users_of_' . $plan_id , $linked_users ) ;
        }
    }

    public function do_something_when_actions_made() {

        $this_member_id = get_current_user_id() ;

        if( isset( $_GET[ '_mynonce' ] ) && isset( $_GET[ 'plan_id' ] ) && isset( $_GET[ 'action' ] ) ) {
            if( ! wp_verify_nonce( $_GET[ '_mynonce' ] , $_GET[ 'plan_id' ] ) ) {
                // This nonce is not valid.
                wp_safe_redirect( remove_query_arg( array( 'plan_id' , 'action' , '_mynonce' ) ) ) ;
            } else {

                $plan_id = ( int ) $_GET[ 'plan_id' ] ;
                $action  = $_GET[ 'action' ] ;

                $post_id = sumo_get_member_post_id( $this_member_id ) ;

                $unique_id = sumo_get_plan_key( $post_id , $plan_id ) ;

                $saved_plans = get_post_meta( $post_id , 'sumomemberships_saved_plans' , true ) ;

                if( is_array( $saved_plans ) && ! empty( $saved_plans ) ) {

                    if( $action == "pause" ) {

                        sumo_remove_capability_from_member( $this_member_id , $plan_id ) ;

                        sumo_pause_r_disable_plan( $plan_id , $post_id , $this_member_id ) ;
                    } else if( $action == "resume" ) {
                        sumo_resume_plan_after_plan_paused_r_disabled( $plan_id , $post_id , $this_member_id ) ;

                        sumo_add_capability_to_member( $this_member_id , $plan_id ) ;
                    }
                    do_action( 'sumomemberships_manual_member_plan_status_updation' , $plan_id , $action , $post_id , $unique_id ) ;
                }
                wp_safe_redirect( remove_query_arg( array( 'add-to-cart' , 'variation_id' , 'plan_id' , 'action' , '_mynonce' ) ) ) ;
            }
        }
    }

}

new SUMOMemberships_Account_Page() ;
