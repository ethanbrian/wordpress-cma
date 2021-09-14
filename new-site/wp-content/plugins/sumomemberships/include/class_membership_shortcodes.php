<?php
/**
 * Shortcodes
 */
if( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if( ! class_exists( 'SUMOMembership_Shortcodes' ) ) {

    /**
     * Class.
     */
    class SUMOMembership_Shortcodes {

        /**
         * Plan add to cart URL.
         */
        public static $plan_add_to_cart_url ;

        /**
         * Class Initialization.
         */
        public static function init() {

            $shortcodes = array(
                /* My Account Shortcodes */
                'sumo_member_details' ,
                'sumo_members_details' ,
                'sumo_members' ,
                'sumo_members_based_on_plans' ,
                /* Membership Restriction Shortcodes */
                'membership_products' ,
                'default_membership_plans' ,
                'login_link' ,
                'membership'
                    ) ;
            foreach( $shortcodes as $shortcode_name ) {

                add_shortcode( $shortcode_name , array( __CLASS__ , 'process_shortcode' ) ) ;
            }
        }

        /**
         * Process Shortcode
         */
        public static function process_shortcode( $atts , $content , $tag ) {

            $shortcodename = str_replace( array( '(' , ')' ) , array( '' , '' ) , $tag ) ;
            $function      = 'shortcode_' . $shortcodename ;

            switch( $shortcodename ) {

                case 'sumo_member_details':
                case 'sumo_members':
                case 'sumo_members_details':
                case 'sumo_members_based_on_plans':
                case 'membership_products':
                case 'default_membership_plans':
                case 'login_link':

                    ob_start() ;
                    self::$function( $atts , $content ) ; // output for shortcode
                    $content = ob_get_contents() ;
                    ob_end_clean() ;

                    break ;

                case 'membership' :
                    $content = self::$function( $atts , $content ) ; // output for shortcode
                    break ;
            }
            return $content ;
        }

        /*
         * Display Membership Information
         */

        public static function shortcode_sumo_member_details( $atts , $content ) {

            SUMOMemberships_Account_Page::display_membership_information() ;
        }

        /*
         * Display All Members Plan.
         */

        public static function shortcode_sumo_members_details( $atts , $content ) {

            $posts = self::get_membership_users() ;
            ?>
            <form method="POST" action="">
                <p class ="sumo-member-search-box">
                    <label class ="screen-reader-text"><?php esc_html_e( 'Search Member' , 'sumomemberships' ) ?></label>
                    <input type="search" name="sumo_member_shortcode_search" id="sumo_member_shortcode_search" value="" placeholder=<?php esc_html_e( 'search..' , 'sumomemberships' ) ?> />
                    <input id="search-submit" class ="button" type="submit" value=<?php esc_html_e( 'Search Member' , 'sumomemberships' ) ?> />
                </p>
            </form>
            <table class="sumo-member-shortcode-table">
                <tbody>
                    <tr>
                        <th><?php _e( 'S.no' , 'sumomemberships' ) ?></th>
                        <th><?php _e( 'User First Name' , 'sumomemberships' ) ?></th>
                        <th><?php _e( 'User Last Name' , 'sumomemberships' ) ?></th>
                        <th><?php _e( 'User Email' , 'sumomemberships' ) ?></th>
                        <th><?php _e( 'Billing City' , 'sumomemberships' ) ?></th>
                        <th><?php _e( 'Billing State' , 'sumomemberships' ) ?></th>
                        <th><?php _e( 'Billing Country' , 'sumomemberships' ) ?></th>
                        <th><?php _e( 'Plan / Status' , 'sumomemberships' ) ?></th>
                    </tr>

                    <?php
                    $sno   = 1 ;
                    if( ! empty( $posts ) ) {

                        foreach( $posts as $member_id ) {

                            $user_id = get_post_meta( $member_id , 'sumomemberships_userid' , true ) ;
                            if( $user_id != '' ) {

                                $getdatatodisplay = get_post_meta( $member_id , 'sumomemberships_saved_plans' , true ) ;
                                if( is_array( $getdatatodisplay ) && ! empty( $getdatatodisplay ) ) {

                                    $saved_plans = "" ;
                                    foreach( $getdatatodisplay as $key => $value ) {
                                        if( isset( $value[ 'choose_plan' ] ) && $value[ 'choose_plan' ] != '' ) {
                                            $saved_plans .= get_the_title( $value[ 'choose_plan' ] ) ;
                                            $status      = $value[ 'choose_status' ] ? $value[ 'choose_status' ] : '' ;
                                            if( $status == 'active' ) {
                                                $plan_status = __( 'Active' , 'sumomemberships' ) ;
                                            } elseif( $status == 'paused' ) {
                                                $plan_status = __( 'Paused' , 'sumomemberships' ) ;
                                            } elseif( $status == 'expired' ) {
                                                $plan_status = __( 'Expired' , 'sumomemberships' ) ;
                                            } else {
                                                $plan_status = __( 'Cancelled' , 'sumomemberships' ) ;
                                            }

                                            $saved_plans .= '/' . '<b>' . $plan_status . '</b>, ' ;
                                        }
                                    }

                                    $saved_plans = rtrim( $saved_plans , ', ' ) ;

                                    if( ! empty( $saved_plans ) ) {
                                        ?><tr><td><?php echo $sno ; ?></td><?php
                                            $user_info = get_userdata( $user_id ) ;
                                            ?><td><?php echo $user_info->first_name ; ?></td><?php
                                            ?><td><?php echo $user_info->last_name ; ?></td><?php
                                            ?><td><?php echo $user_info->user_email ; ?></td><?php
                                            ?><td><?php echo $user_info->billing_city ; ?></td><?php
                                            ?><td><?php echo $user_info->billing_state ; ?></td><?php
                                            ?><td><?php echo $user_info->billing_country ; ?></td><?php
                                            ?><td><?php echo $saved_plans ; ?></td><?php
                                            $sno ++ ;
                                        }
                                    }
                                }
                            }
                        }
                        ?>
                </tbody>
            </table>

            <?php
            if( empty( $posts ) ) {
                echo 'No Result Found.' ;
            }
        }

        /*
         * Display Membership Data
         */

        public static function get_membership_users() {
            $args = array( 'post_type' => 'sumomembers' , 'numberposts' => -1 , 'fields' => 'ids' , 'post_status' => 'published' ) ;

            //for search
            if( isset( $_POST[ 'sumo_member_shortcode_search' ] ) ) {
                $search_key = $_POST[ 'sumo_member_shortcode_search' ] ;

                global $wpdb ;

                $user_email_ids = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT user.ID
                                 FROM $wpdb->postmeta as pm
                                 INNER JOIN $wpdb->users as user ON pm.meta_value=user.ID
                                 WHERE pm.meta_key=%s AND user.user_email LIKE %s" , 'sumomemberships_userid' , '%' . $search_key . '%' ) ) ;

                $user_ids = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT user_meta.user_id
                                 FROM $wpdb->postmeta as pm
                                 INNER JOIN $wpdb->usermeta as user_meta ON pm.meta_value=user_meta.user_id 
                                 WHERE pm.meta_key=%s AND user_meta.meta_key IN(%s,%s,%s,%s,%s) 
                                 AND user_meta.meta_value LIKE %s" , 'sumomemberships_userid' , 'first_name' , 'last_name' , 'billing_state' , 'billing_city' , 'billing_country' , '%' . $search_key . '%' ) ) ;

                $user_ids = array_merge( $user_email_ids , $user_ids ) ;

                $args[ 'meta_key' ]     = 'sumomemberships_userid' ;
                $args[ 'meta_value' ]   = array_filter( array_unique( $user_ids ) ) ;
                $args[ 'meta_compare' ] = 'IN' ;

                if( empty( $user_ids ) )
                    return array() ;
            }
            //Get Membership Data
            $posts = get_posts( $args ) ;

            return $posts ;
        }

        /*
         * Display All Members.
         */

        public static function shortcode_sumo_members( $atts , $content ) {

            $args  = array( 'post_type' => 'sumomembers' , 'numberposts' => -1 , 'fields' => 'ids' , 'post_status' => 'published' ) ;
            $posts = get_posts( $args ) ;
            ?>
            <table>
                <tbody>
                    <tr>

                        <th><?php _e( 'S.no' , 'sumomemberships' ) ?></th>
                        <th><?php _e( 'Name' , 'sumomemberships' ) ?></th>
                        <th><?php _e( 'Avatar' , 'sumomemberships' ) ?></th>
                        <th><?php _e( 'Plans' , 'sumomemberships' ) ?></th>
                    </tr>

                    <?php
                    $sno   = 1 ;
                    if( ! empty( $posts ) ) {

                        foreach( $posts as $member_id ) {

                            $user_id = get_post_meta( $member_id , 'sumomemberships_userid' , true ) ;
                            if( $user_id != '' ) {

                                $getdatatodisplay = get_post_meta( $member_id , 'sumomemberships_saved_plans' , true ) ;
                                if( is_array( $getdatatodisplay ) && ! empty( $getdatatodisplay ) ) {

                                    $saved_plans = array() ;
                                    foreach( $getdatatodisplay as $key => $value ) {

                                        if( isset( $value[ 'choose_plan' ] ) && $value[ 'choose_plan' ] != '' && $value[ 'choose_status' ] == 'active' ) {

                                            $saved_plans[] = get_the_title( $value[ 'choose_plan' ] ) ;
                                        }
                                    }
                                    if( ! empty( $saved_plans ) ) {
                                        ?><tr><td><?php echo $sno ; ?></td><?php
                                            $user_info = get_userdata( $user_id ) ;
                                            $username  = $user_info->user_login ;
                                            $useremail = $user_info->user_email ;
                                            ?><td><?php echo $username ; ?></td><?php
                                            ?><td><?php echo get_avatar( $user_id , 50 , '' , $username , '' ) ; ?></td><?php
                                            ?><td><?php echo implode( ', ' , $saved_plans ) ; ?></td></tr><?php
                                        $sno ++ ;
                                    }
                                }
                            }
                        }
                    }
                    ?>
                </tbody>
            </table>

            <?php
        }

        /*
         * Display Members Based On Plans.
         */

        public static function shortcode_sumo_members_based_on_plans( $atts , $content ) {

            extract( shortcode_atts( array(
                'plan_ids' => '' ,
                            ) , $atts ) ) ;
            $display_plans = explode( ',' , $plan_ids ) ;
            $args          = array( 'post_type' => 'sumomembers' , 'numberposts' => -1 , 'fields' => 'ids' , 'post_status' => 'published' ) ;
            $posts         = get_posts( $args ) ;
            ?>
            <table>
                <tbody>
                    <tr>

                        <th><?php _e( 'S.no' , 'sumomemberships' ) ?></th>
                        <th><?php _e( 'Name' , 'sumomemberships' ) ?></th>
                        <th><?php _e( 'Avatar' , 'sumomemberships' ) ?></th>
                        <th><?php _e( 'Plans' , 'sumomemberships' ) ?></th>
                    </tr>

                    <?php
                    $sno           = 1 ;
                    if( ! empty( $posts ) ) {

                        foreach( $posts as $member_id ) {

                            $user_id = get_post_meta( $member_id , 'sumomemberships_userid' , true ) ;
                            if( $user_id != '' ) {

                                $getdatatodisplay = get_post_meta( $member_id , 'sumomemberships_saved_plans' , true ) ;
                                if( is_array( $getdatatodisplay ) && ! empty( $getdatatodisplay ) ) {

                                    $saved_plans = array() ;
                                    foreach( $getdatatodisplay as $key => $value ) {

                                        if( isset( $value[ 'choose_plan' ] ) && $value[ 'choose_plan' ] != '' && $value[ 'choose_status' ] == 'active' && in_array( $value[ 'choose_plan' ] , $display_plans ) ) {

                                            $saved_plans[] = get_the_title( $value[ 'choose_plan' ] ) ;
                                        }
                                    }

                                    if( ! empty( $saved_plans ) ) {
                                        ?><tr><td><?php echo $sno ; ?></td><?php
                                            $user_info = get_userdata( $user_id ) ;
                                            $username  = $user_info->user_login ;
                                            $useremail = $user_info->user_email ;
                                            ?><td><?php echo $username ; ?></td><?php
                                            ?><td><?php echo get_avatar( $user_id , 50 , '' , $username , '' ) ; ?></td><?php
                                            ?><td><?php echo implode( ', ' , $saved_plans ) ; ?></td></tr><?php
                                        $sno ++ ;
                                    }
                                }
                            }
                        }
                    }
                    ?>
                </tbody>
            </table>
            <?php
        }

        /*
         * Add to Cart Membership Product - Displaying for Restriction Messages.
         */

        public static function shortcode_membership_products( $atts , $content ) {

            global $post ;

	    $this_id = isset( $post->ID ) ? $post->ID : 0 ;

            $plan_add_to_cart_url = self::get_plan_add_to_cart_url( $this_id ) ;

            echo is_array( $plan_add_to_cart_url ) ? implode( " / " , array_unique( $plan_add_to_cart_url ) ) : "" ;
        }

        /*
         * Get Membership plan add to cart URL.
         */

        public static function get_plan_add_to_cart_url( $this_id ) {

            if( isset( SUMOMembership_Shortcodes::$plan_add_to_cart_url ) ) {
                return SUMOMembership_Shortcodes::$plan_add_to_cart_url ;
            }

            $membership_products  = self::get_membership_products_to_disable_restriction( $this_id ) ;
            $plan_add_to_cart_url = array() ;
            foreach( $membership_products as $membership_product_id ) {

                $product = sumo_get_product( $membership_product_id ) ;

                if( is_object( $product ) ) {

                    $plan_add_to_cart_url[] = '<a class="membership_add_to_cart" style="color:blue;" href="' . $product->add_to_cart_url() . '">' . $product->get_name() . '</a>' ;
                }
            }

            SUMOMembership_Shortcodes::$plan_add_to_cart_url = $plan_add_to_cart_url ;

            return $plan_add_to_cart_url ;
        }

        /*
         * Get Membership Products to Disable Restriction.
         */

        public static function get_membership_products_to_disable_restriction( $this_id ) {

            $this_type           = get_post_type( $this_id ) ;
            $this_member_id      = get_current_user_id() ;
            $this_member_post_id = sumo_get_member_post_id( $this_member_id ) ;

            $membership_products = array() ;
            $content_access_type = get_post_meta( $this_id , 'sumomemberships_products_posts_pages_settings' , true ) ;

            $plan_ids = array() ;

            if( $content_access_type == 'all_users' ) {

                $plan_ids = sumo_get_membership_plans() ;
            } else if( $content_access_type == 'all_members' ) {

                $check_plan_exists = check_plan_exists( $this_member_id ) ;

                if( $check_plan_exists ) {
                    $plan_ids = sumo_get_membership_plans() ;
                }
            } else if( $content_access_type == 'all_non_members' ) {

                $check_plan_exists = check_plan_exists( $this_member_id ) ;

                if( ! $check_plan_exists ) {
                    $plan_ids = sumo_get_membership_plans() ;
                }
            } else if( $content_access_type == 'with_particular_plans' ) {

                $check_member_with_particular_plan = self::check_member_with_particular_plan( $this_id , $this_member_post_id ) ;

                if( $check_member_with_particular_plan ) {
                    $plan_ids = sumo_get_membership_plans() ;
                }
            } else if( $content_access_type == 'without_particular_plans' ) {

                $check_member_without_particular_plan = self::check_member_without_particular_plan( $this_id , $this_member_post_id ) ;

                if( $check_member_without_particular_plan ) {
                    $plan_ids = sumo_get_membership_plans() ;
                }
            }

            if( empty( $plan_ids ) )
                return $plan_ids ;

            foreach( $plan_ids as $plan_id ) {

                if( get_post_meta( $plan_id , 'sumomemberships_plan_associated_product' , true ) ) {

                    $membership_products[] = get_post_meta( $plan_id , 'sumomemberships_plan_associated_product' , true ) ;
                }
            }

            return $membership_products ;
        }

        /*
         * Get Plan Ids.
         */

        public static function extract_plan_ids_from_plan_rules( $plan_rules ) {

            $plan_ids = array() ;

            if( is_array( $plan_rules ) ) {
                foreach( $plan_rules as $each_rules ) {

                    $plan_ids[] = $each_rules[ 'plan_id' ] ;
                }
            }

            return $plan_ids ;
        }

        /*
         * Check Member With Particular Plan.
         */

        public static function check_member_with_particular_plan( $this_id , $this_member_post_id ) {

            $plan_rules = sumo_get_plan_rules_added( $this_id , 'members_with_plan' ) ;

            $plan_ids = self::extract_plan_ids_from_plan_rules( $plan_rules ) ;

            $member_plan_list = sumo_get_member_purchased_plans_list( $this_member_post_id ) ;

            if( empty( $member_plan_list ) )
                return false ;

            foreach( $plan_rules as $each_values ) {

                if( $each_values[ 'schedule_type' ] == 'immediately' ) {
                    if( ! in_array( $each_values[ 'plan_id' ] , $member_plan_list ) )
                        return false ;
                } else {
                    $plan_schedule_duration_timestamp = sumo_get_plan_schedules_from_post_page_product( $each_values ) ;

                    $plan_since_time = sumo_check_linked_users_of_plan_activate_since( get_current_user_id() ) ;

                    $delay_timestamp      = time() + ( int ) ($plan_schedule_duration_timestamp + $plan_since_time) ;
                    $check_scheduled_date = date( 'd' ) == date( 'd' , $delay_timestamp ) ? true : false ;

                    if( $check_scheduled_date && $delay_timestamp > 0 ) {
                        if( in_array( $each_values[ 'plan_id' ] , $member_plan_list ) )
                            return false ;
                    } else if( $delay_timestamp == "" ) {
                        if( in_array( $each_values[ 'plan_id' ] , $member_plan_list ) )
                            return false ;
                    }
                }
            }

            return true ;
        }

        /*
         * Check Member Without Particular Plan.
         */

        public static function check_member_without_particular_plan( $this_id , $this_member_post_id ) {

            $plan_rules = sumo_get_plan_rules_added( $this_id , 'users_without_plan' ) ;

            $member_plan_list = sumo_get_member_purchased_plans_list( $this_member_post_id ) ;

            if( empty( $member_plan_list ) )
                return false ;

            foreach( $plan_rules as $each_values ) {

                if( in_array( $each_values[ 'plan_id' ] , $member_plan_list ) ) {
                    return false ;
                }
            }

            return true ;
        }

        /*
         * Display Default Plans.
         */

        public static function shortcode_default_membership_plans( $atts , $content ) {

            $plan_name           = array() ;
            $default_valid_plans = get_option( 'sumomemberships_valid_default_plans' ) ;

            if( is_array( $default_valid_plans ) && ! empty( $default_valid_plans ) ) {

                foreach( $default_valid_plans as $plan_id ) {

                    $membership_product_id = get_post_meta( $plan_id , 'sumomemberships_plan_associated_product' , true ) ;

                    $product = sumo_get_product( $membership_product_id ) ;

                    if( is_object( $product ) ) {

                        $plan_name[] = "<span class=sumo_default_plans style=color:green;>" . get_post_meta( $plan_id , 'sumomemberships_plan_name' , true ) . "</span>" ;
                    }
                }
            }

            echo implode( ", " , $plan_name ) ;
        }

        /*
         * Display Site Members Login Link For Guests.
         */

        public static function shortcode_login_link( $atts , $content ) {

            if( is_user_logged_in() )
                return ;

            $login_link = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ;

            echo '<a href="' . $login_link . '" style = "color: blue; font-size: 18px;">' . $login_link . '</a>' ;
        }

        /*
         * Shortcode to Restrict the Content block on Posts or Pages based upon the Plan Slug.
         */

        public static function shortcode_membership( $atts , $content ) {

            $this_member_id = get_current_user_id() ;

            $member_post_id         = sumo_get_member_post_id( $this_member_id ) ;
            $linked_plans           = sumo_get_available_linked_plans( $member_post_id , 'active' ) ;
            $check_for_linked_users = sumo_check_linked_users_had_privilege( $this_member_id ) ;
            $bool                   = false ;
            $plan                   = "" ;
            $admin_access           = self::get_user_role_for_admin_access() ;

            /* Extract Plan Slug from the array index "plan" */
            extract( shortcode_atts( array( 'plan' => '' ) , $atts ) ) ;

            $plan_slugs = explode( "," , $plan ) ;
            foreach( $plan_slugs as $each_slug ) {

                $plan_id = sumo_get_plan_id_from_slug( $each_slug ) ;

                /* Check if the Member not purchased this Plan Slug associated Plan then Restrict the corresponding Content. */
                if( (sumo_is_member_purchased_any_plan( $member_post_id ) && sumo_is_plan_active( $plan_id , $member_post_id ) ) || in_array( $plan_id , $linked_plans ) || in_array( $plan_id , $check_for_linked_users ) ) {
                    $bool = true ;
                }
            }
            if( in_array( 'allmembers' , $plan_slugs ) ) {

                if( $member_post_id ) {

                    $member_purchased_active_plans = sumo_get_member_purchased_plans_list( $member_post_id ) ;
                    if( ! empty( $member_purchased_active_plans ) || ! empty( $linked_plans ) || ! empty( $check_for_linked_users ) ) {
                        $bool = true ;
                    }
                } else {

                    if( ! empty( $check_for_linked_users ) ) {
                        $bool = true ;
                    }
                }
            }
            if( in_array( 'nonmembers' , $plan_slugs ) ) {

                if( $member_post_id ) {

                    $member_purchased_active_plans = sumo_get_member_purchased_plans_list( $member_post_id ) ;
                    if( empty( $member_purchased_active_plans ) && empty( $linked_plans ) && empty( $check_for_linked_users ) ) {

                        $bool = true ;
                    }
                } else {

                    if( empty( $check_for_linked_users ) ) {

                        $bool = true ;
                    }
                }
            }

            if( $admin_access ) {

                $bool = true ;
            }

            return $bool ? do_shortcode( $content ) : "" ;
        }

        /*
         * Display Content Restriction to Administrator With admin_access function.
         */

        public static function get_user_role_for_admin_access() {

            $this_member_id = get_current_user_id() ;
            $user_meta      = get_userdata( $this_member_id ) ;
            $user_roles     = is_object( $user_meta ) ? $user_meta->roles : '' ;
            $bool           = false ;
            if( is_array( $user_roles ) && ! empty( $user_roles ) ) {

                foreach( $user_roles as $role ) {

                    /*  $array refers to key and $role refers to value. eg: 0 => administrator  */
                    if( $role === "administrator" ) {

                        $bool = true ;
                    }

                    return $bool ;
                }
            }
        }

    }

    SUMOMembership_Shortcodes::init() ;
}
