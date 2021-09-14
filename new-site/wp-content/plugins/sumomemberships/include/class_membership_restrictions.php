<?php

class SUMOMemberships_Restrictions {

    public function __construct() {
        add_action( 'wp' , array( $this , 'check_for_page_r_post_redirection' ) ) ;
        add_filter( 'the_content' , array( $this , 'hide_post_r_page_content' ) , 10 , 1 ) ;
        add_filter( 'walker_nav_menu_start_el' , array( $this , 'hide_restricted_post_r_page_menu' ) , 10 , 4 ) ;

        if( get_option( 'sumo_restriction_type' ) == '1' && get_option( 'sumo_hide_restricted_products_in_shop_page' ) == "yes" ) {
            add_action( 'pre_get_posts' , array( $this , 'hide_restricted_products' ) ) ;
        }

        add_filter( 'woocommerce_is_purchasable' , array( $this , 'restrict_product_purchase' ) , 10 , 2 ) ;
        add_filter( 'woocommerce_get_price_html' , array( $this , 'display_restriction_message_in_product_page' ) , 999 , 2 ) ;

        add_action( 'sumo_memberships_process_plan_duration_validity' , array( $this , 'process_plan_duration_validity' ) , 10 , 2 ) ;
        add_action( 'sumo_memberships_process_linked_plan_privilege' , array( $this , 'process_linked_plan_privilege' ) , 10 , 3 ) ;

        add_action( 'woocommerce_checkout_process' , array( $this , 'force_create_account_if_guest' ) ) ;
        add_action( 'woocommerce_before_checkout_form' , array( $this , 'force_enable_signup_if_guest' ) , 10 , 1 ) ;

        add_action( 'woocommerce_register_form' , array( $this , 'display_default_plans_on_user_signup_form' ) ) ;
        add_action( 'user_register' , array( $this , 'activate_default_plans_on_user_signing_up' ) ) ;
        add_action( 'fpsl_registerd_successfully' , array( $this , 'activate_default_plans_on_user_signing_up' ) , 10 , 1 ) ;
        $order_status1 = is_array( get_option( 'sumo_membership_plan_access_order_status' ) ) ? get_option( 'sumo_membership_plan_access_order_status' ) : array() ;
        $order_status  = ! empty( $order_status1 ) ? $order_status1 : array( 'completed' ) ;
        foreach( $order_status as $status ) {
            add_action( 'woocommerce_order_status_' . $status , array( $this , 'activate_membership_after_order_placed_successful' ) ) ;
        }

        add_action( 'woocommerce_order_status_changed' , array( $this , 'cancel_membership_after_order_fails' ) , 10 , 3 ) ;

        add_filter( 'woocommerce_product_add_to_cart_url' , array( $this , 'membership_add_to_cart_for_page_r_post' ) , 10 , 2 ) ;

        add_action( 'wp_head' , array( $this , 'display_notice_on_membership_products' ) ) ;
        add_action( 'woocommerce_before_single_product' , array( $this , 'display_notice_on_variation_product' ) ) ;
        add_action( 'wp_ajax_sumo_display_notice_on_variation' , array( $this , 'AJAX_notice_on_variation_product' ) ) ;
        add_action( 'wp_ajax_nopriv_sumo_display_notice_on_variation' , array( $this , 'AJAX_notice_on_variation_product' ) ) ;

        add_filter( 'posts_where_paged' , array( $this , 'hide_post_from_front_feed_category_page' ) , 10 , 2 ) ;
    }

    public function sumo_subscription_is_switching( $product ) {
        $product_id = 0 ;
        if( is_a( $product , 'WC_Product' ) ) {
            if( is_callable( array( $product , 'is_type' ) ) && ! $product->is_type( 'variable' ) ) {
                $product_id = $product->get_id() ;
            }
        } else if( is_numeric( $product ) ) {
            $product_id = $product ;
        }

        if( class_exists( 'SUMOSubscriptions' ) && function_exists( 'sumo_subscription_is_switching' ) ) {
            if( sumo_subscription_is_switching( $product_id ) ) {
                return true ;
            }
        }
        return false ;
    }

    //Checking Whether the Product Restrict to Purchase or Not

    public function restrict_product_purchase( $is_purchasable , $this_object ) {

        $product_id = sumo_get_product_id( $this_object ) ;

        if( $this->sumo_subscription_is_switching( $product_id ) ) {
            return $is_purchasable ;
        }

        if( $this_object->is_type( 'variation' ) ) {
            $is_purchasable = $this->post_r_page_r_product_accessibility( $product_id , $is_purchasable , true ) ;
        } else {
            $is_purchasable = $this->post_r_page_r_product_accessibility( $product_id , $is_purchasable ) ;
        }
        if( ! $is_purchasable ) {
            if( $this->get_user_role_for_admin_access() ) {
                return true ;
            }
            return false ;
        }
        return $is_purchasable ;
    }

    //Display Content Restriction to Administrator With admin_access function

    public function get_user_role_for_admin_access() {
        $this_member_id = get_current_user_id() ;
        $user_meta      = get_userdata( $this_member_id ) ;
        $user_roles     = is_object( $user_meta ) ? $user_meta->roles : '' ;
        $bool           = false ;
        if( is_array( $user_roles ) && ! empty( $user_roles ) ) {
            foreach( $user_roles as $role ) {
//            $array refers to key and $role refers to value. eg: 0 => administrator
                if( $role === "administrator" ) {
                    $bool = true ;
                }
                return $bool ;
            }
        }
    }

    //For Single Product Page Restriction Messages

    public function display_restriction_message_in_product_page( $price , $this_obj ) {

        if( $this->sumo_subscription_is_switching( $this_obj ) ) {
            return $price ;
        }

        $product_level_id    = sumo_get_product_level_id( $this_obj ) ;
        $is_registered_user  = get_current_user_id() > 0 ? true : false ;
        $content_access_type = get_post_meta( $product_level_id , 'sumomemberships_products_posts_pages_settings' , true ) ;

        //To display restriction message, Check the Membership product with its restriction type - with/without
        $is_requires_membership_to_access = ( ! count( $this->get_membership_products_to_disable_restriction( $product_level_id ) ) > 0 && $content_access_type == "without_particular_plans") ? true : false ;
        $is_plan_not_purchasable          = ( ! count( $this->get_membership_products_to_disable_restriction( $product_level_id ) ) > 0 && $content_access_type == "with_particular_plans") ? true : false ;
        if( $is_registered_user ) {
            if( $is_requires_membership_to_access ) {
                $purchase_restriction_msg = do_shortcode( sumo_replace_membership_products_shortcode( 'site_users_product_purchase' ) ) ;
                $view_restriction_msg     = do_shortcode( get_option( 'sumo_msg_for_site_users_product_view_restriction_for_membership' ) ) ;
            } else {
                $purchase_restriction_msg = do_shortcode( sumo_replace_membership_products_shortcode( 'user_product_purchase' ) ) ;
                $view_restriction_msg     = do_shortcode( sumo_replace_membership_products_shortcode( 'user_product_view' ) ) ;
            }
        } else {
            if( $is_requires_membership_to_access ) {
                $purchase_restriction_msg = do_shortcode( get_option( 'sumo_msg_for_guests_product_purchase_restriction_for_membership' ) ) ;
                $view_restriction_msg     = do_shortcode( get_option( 'sumo_msg_for_guests_product_view_restriction_for_membership' ) ) ;
            } else {
                $purchase_restriction_msg = do_shortcode( sumo_replace_membership_products_shortcode( 'guest_product_purchase' ) ) ;
                $view_restriction_msg     = do_shortcode( sumo_replace_membership_products_shortcode( 'guest_product_view' ) ) ;
            }
        }
        if( $content_access_type == "without_particular_plans" || $content_access_type == "all_non_members" || $is_plan_not_purchasable ) {
            $purchase_restriction_msg = do_shortcode( get_option( 'sumo_msg_product_restriction_for_membership' ) ) ;
            $view_restriction_msg     = do_shortcode( get_option( 'sumo_msg_product_restriction_for_membership' ) ) ;
        } elseif( $content_access_type == "all_members" ) {
            if( $is_registered_user ) {
                $purchase_restriction_msg = do_shortcode( sumo_replace_membership_products_shortcode( 'site_users_product_purchase' ) ) ;
                $view_restriction_msg     = do_shortcode( sumo_replace_membership_products_shortcode( 'site_users_product_purchase' ) ) ;
            } else {
                $purchase_restriction_msg = do_shortcode( get_option( 'sumo_msg_for_guests_product_purchase_restriction_for_membership' ) ) ;
                $view_restriction_msg     = do_shortcode( get_option( 'sumo_msg_for_guests_product_purchase_restriction_for_membership' ) ) ;
            }
        }
        $number_price = $this_obj->get_price() ;

        //Product Not Viewing Restriction Message
        if( is_product() && get_option( 'sumo_restriction_type' ) == '2' && get_option( 'sumo_restriction_type_for_content' ) == '1' ) {

            $product_accessability = $this->post_r_page_r_product_accessibility( $product_level_id , $number_price ) ;
            if( $product_accessability == "" && $number_price != "" ) {
                if( $this->get_user_role_for_admin_access() ) {
                    add_action( 'woocommerce_single_product_summary' , 'woocommerce_template_single_add_to_cart' , 30 ) ;
                    return $price ;
                }

                remove_action( 'woocommerce_single_product_summary' , 'woocommerce_template_single_add_to_cart' , 30 ) ;
                return $price . '<br>' . $this->display_restriction_message( $view_restriction_msg ) ;
            }

            return $price ;
        }
        //Product Not Purchasable Restriction Messages
        if( is_product() && get_option( 'sumo_restriction_type' ) == '1' ) {
            //For Simple/Variable Product
            $simple_product_accessability = $this->post_r_page_r_product_accessibility( $product_level_id , $number_price ) ;
            if( $simple_product_accessability == "" && $number_price != "" ) {
                if( $this->get_user_role_for_admin_access() ) {
                    return $price ;
                }
                remove_action( 'woocommerce_single_product_summary' , 'woocommerce_template_single_add_to_cart' , 30 ) ;
                return $price . '<br>' . $this->display_restriction_message( $purchase_restriction_msg ) ;
            }
        }
        return $price ;
    }

    public function sumo_membership_compatibility_for_sumo_discounts( $price , $this_obj ) {
        //Product Not Viewing Restriction Message
        $product_level_id    = sumo_get_product_level_id( $this_obj ) ;
        $is_registered_user  = get_current_user_id() > 0 ? true : false ;
        $content_access_type = get_post_meta( $product_level_id , 'sumomemberships_products_posts_pages_settings' , true ) ;

        //To display restriction message, Check the Membership product with its restriction type - with/without
        $is_requires_membership_to_access = ( ! count( $this->get_membership_products_to_disable_restriction( $product_level_id ) ) > 0 && $content_access_type == "without_particular_plans") ? true : false ;
        $is_plan_not_purchasable          = ( ! count( $this->get_membership_products_to_disable_restriction( $product_level_id ) ) > 0 && $content_access_type == "with_particular_plans") ? true : false ;

        if( $is_registered_user ) {
            if( $is_requires_membership_to_access ) {
                $purchase_restriction_msg = sumo_replace_membership_products_shortcode( 'site_users_product_purchase' ) ;
                $view_restriction_msg     = get_option( 'sumo_msg_for_site_users_product_view_restriction_for_membership' ) ;
            } else {
                $purchase_restriction_msg = do_shortcode( sumo_replace_membership_products_shortcode( 'user_product_purchase' ) ) ;
                $view_restriction_msg     = do_shortcode( sumo_replace_membership_products_shortcode( 'user_product_view' ) ) ;
            }
        } else {
            if( $is_requires_membership_to_access ) {
                $purchase_restriction_msg = do_shortcode( get_option( 'sumo_msg_for_guests_product_purchase_restriction_for_membership' ) ) ;
                $view_restriction_msg     = do_shortcode( get_option( 'sumo_msg_for_guests_product_view_restriction_for_membership' ) ) ;
            } else {
                $purchase_restriction_msg = do_shortcode( sumo_replace_membership_products_shortcode( 'guest_product_purchase' ) ) ;
                $view_restriction_msg     = do_shortcode( sumo_replace_membership_products_shortcode( 'guest_product_view' ) ) ;
            }
        }
        if( $content_access_type == "all_users" || $content_access_type == "all_members" || $content_access_type == "all_non_members" || $is_plan_not_purchasable ) {
            $purchase_restriction_msg = get_option( 'sumo_msg_product_restriction_for_membership' ) ;
            $view_restriction_msg     = get_option( 'sumo_msg_product_restriction_for_membership' ) ;
        }
        if( is_product() && get_option( 'sumo_restriction_type' ) == '2' && get_option( 'sumo_restriction_type_for_content' ) == '1' ) {

            $product_accessability = $this->post_r_page_r_product_accessibility( $product_level_id , $price ) ;

            if( $product_accessability == "" && $price != "" ) {
                remove_action( 'woocommerce_single_product_summary' , 'woocommerce_template_single_add_to_cart' , 30 ) ;
                return $this->display_restriction_message( $view_restriction_msg ) ;
            }
        }

        //Product Not Purchasable Restriction Messages
        if( is_product() && get_option( 'sumo_restriction_type' ) == '1' ) {

            //For Simple/Variable Product
            $simple_product_accessability = $this->post_r_page_r_product_accessibility( $product_level_id , $price ) ;

            if( $simple_product_accessability == "" && $price != "" ) {
                remove_action( 'woocommerce_single_product_summary' , 'woocommerce_template_single_add_to_cart' , 30 ) ;
                return $this->display_restriction_message( $purchase_restriction_msg ) ;
            }
        }
    }

    public function hide_restricted_post_r_page_menu( $item_output , $item , $depth , $args ) {

        $item_id = $item->object_id ;

        $menu_accessability = $this->post_r_page_r_product_accessibility( $item_id , $item_output ) ;

        $hide_restricted_products = get_option( 'sumo_hide_restricted_posts_and_pages_from_menu' ) == "yes" ? true : false ;

        if( $hide_restricted_products && $menu_accessability == "" ) {
            return '' ;
        }
        return $item_output ;
    }

    public function hide_restricted_products( $query ) {
        if( ! $query->is_main_query() )
            return ;

        $args            = array( 'post_type'              => 'product' ,
            'fields'                 => 'ids' ,
            'posts_per_page'         => '-1' ,
            'meta_query'             => array(
                array(
                    'key'     => 'sumomemberships_products_posts_pages_settings' ,
                    'value'   => '' ,
                    'compare' => 'NOT IN' ,
                ) ,
            ) ,
            'no_found_rows'          => true ,
            'update_post_term_cache' => false ,
            'update_post_post_cache' => false ,
            'cache_results'          => false ,
                ) ;
        $products        = get_posts( $args ) ;
        $ids_to_restrict = array() ;

        if( is_array( $products ) && ! empty( $products ) && ! $this->get_user_role_for_admin_access() ) {
            foreach( $products as $eachproducts ) {
                $check_return_access = $this->post_r_page_r_product_accessibility( $eachproducts , $eachproducts ) ;
                if( $check_return_access == '' ) {
                    $ids_to_restrict[] = $eachproducts ;
                }
            }
        }

        if( isset( $query->query[ 'post_type' ] ) ) {
            if( ! is_admin() && $query->query[ 'post_type' ] == "product" && is_shop() ) {
                $query->set( 'post__not_in' , $ids_to_restrict ) ;
            }
        } elseif( ! is_admin() && (is_tax( 'product_cat' ) || is_tax( 'product_tag' )) ) {
            $query->set( 'post__not_in' , $ids_to_restrict ) ;
        }
    }

    //Post or Page Content Restriction

    public function hide_post_r_page_content( $content ) {

        global $post ;
        
        if(!is_object($post)){
            return $content;
        }

        $this_member_id = get_current_user_id() ;

        $member_post_id = sumo_get_member_post_id( $this_member_id ) ;
        $admin_access   = $this->get_user_role_for_admin_access() ;

        $is_registered_user  = $this_member_id > 0 ? true : false ;
        $content_access_type = get_post_meta( $post->ID , 'sumomemberships_products_posts_pages_settings' , true ) ;

        $is_excerpt_enabled     = get_option( 'sumo_display_excerpt_content' ) == "yes" ? true : false ;
        $no_of_words_to_display = get_option( 'sumo_no_of_words_to_display' ) ;
        $content_accessability  = $this->post_r_page_r_product_accessibility( $post->ID , $content ) ;

        //To display restriction message, Check the Membership product with its restriction type - with/without
        $is_requires_membership_to_access = ( ! count( $this->get_membership_products_to_disable_restriction( $post->ID ) ) > 0 && $content_access_type == "without_particular_plans") ? true : false ;
        $is_plan_not_purchasable          = ( ! count( $this->get_membership_products_to_disable_restriction( $post->ID ) ) > 0 && $content_access_type == "with_particular_plans") ? true : false ;

        if( $is_registered_user ) {
            $view_restriction_msg = $is_requires_membership_to_access ? get_option( 'sumo_msg_for_site_users_content_restriction_for_membership' ) : do_shortcode( sumo_replace_membership_products_shortcode( 'site_users_content' ) ) ;
        } else {
            $view_restriction_msg = $is_requires_membership_to_access ? do_shortcode( get_option( 'sumo_msg_for_guests_content_restriction_for_membership' ) ) : do_shortcode( sumo_replace_membership_products_shortcode( 'site_guest_content' ) ) ;
        }
        if( $content_access_type == "without_particular_plans" && sumo_is_member_purchased_any_plan( $member_post_id ) ) {
            $view_restriction_msg = get_option( 'sumo_msg_content_restriction_for_membership' ) ;
        }
        if( $content_access_type == "all_users" || $content_access_type == "all_members" || $content_access_type == "all_non_members" || $is_plan_not_purchasable ) {
            $view_restriction_msg = get_option( 'sumo_msg_content_restriction_for_membership' ) ;
        }

        if( (get_option( 'sumo_restriction_type_for_content' ) == '1' || get_option( 'sumo_restriction_type_for_content' ) == '2') && $content_accessability == "" && get_post_type( $post->ID ) == 'page' ) {
            $check = get_post_meta( $post->ID , 'sumomemberships_display_content_when_restriction_enabled' , true ) ;
            if( $check == 'show' ) {
                $content_for_reu = get_post_meta( $post->ID , 'sumo_content_for_reu' , true ) ;
                return $content_for_reu ;
            }
        }

        if( get_option( 'sumo_restriction_type_for_content' ) == '1' && $content_accessability == "" && $content != "" && get_post_type( $post->ID ) != 'product' ) {
            if( $admin_access ) {
                return $content ;
            }
            return $is_excerpt_enabled ? wp_trim_words( $content , $no_of_words_to_display , '<a href="#" class="sumo_read_more"> [Read More]</a>' )
                    . $this->display_restriction_message( $view_restriction_msg ) . $this->toggle_read_more_message() : "" ;
        }
        return $content ;
    }

    public function toggle_read_more_message() {
        ob_start() ;
        ?>
        <script type="text/javascript">
            //            jQuery('.sumo_restriction_message').hide();
            jQuery( '.sumo_read_more' ).click( function() {
                jQuery( this ).next( 'p.sumo_restriction_message' ).slideToggle( 'slow' ) ;
                return false ;
            } ) ;
        </script>
        <?php
        return ob_get_contents() ;
    }

    public function check_for_page_r_post_redirection() {

        global $post , $wp_query ;

        if( ! isset( $post ) || $this->get_user_role_for_admin_access() )
            return ;

        if( ! is_home() ) {

            $this->page_r_post_redirection( $post , $wp_query ) ;
        } else if( is_home() && get_option( 'sumomemberships_enable_redirection_for_home_page' ) == "yes" ) {

            $this->page_r_post_redirection( $post , $wp_query ) ;
        }
    }

    public function page_r_post_redirection( $post , $wp_query ) {

        $this_id = $post->ID ;

        $content_accessibility = $this->post_r_page_r_product_accessibility( $this_id , $post ) ;

        $redirect_for = $this->get_redirect_url_from_restricted_url_tab( $this_id ) ;

        if( $redirect_for != "" ) {

            wp_redirect( $redirect_for ) ;
        }

        if( $content_accessibility == "" && get_option( 'sumo_restriction_type_for_content' ) == '3' && ! is_shop() ) {
            if( $post->post_type == 'product' && get_option( 'sumo_restriction_type' ) == '2' ) {

                wp_redirect( get_option( 'sumo_redirect_to' ) ) ;
            } else if( $post->post_type != 'product' ) {

                wp_redirect( get_option( 'sumo_redirect_to' ) ) ;
            }
        } else {
            if( get_option( 'sumo_restriction_type_for_content' ) == '2' && ( ! $this->get_user_role_for_admin_access()) ) {

                if( get_option( 'sumo_restriction_type' ) == '2' || (get_option( 'sumo_restriction_type' ) == '1' && ! in_array( 'product' , ( array ) get_post_type( $this_id ) )) ) {

                    if( $content_accessibility == '' ) {

                        $wp_query->set_404() ;

                        status_header( 404 ) ;
                    }
                }
            }
        }
    }

    public function membership_add_to_cart_for_page_r_post( $url , $obj ) {
        global $post ;

        $post_id       = $post->ID ;
        $get_post_type = get_post_type( $post_id ) ;

        if( $get_post_type == 'post' || $get_post_type == 'page' ) {

            $querystring_arr = parse_url( $url ) ;

            if( isset( $querystring_arr[ 'query' ] ) ) {
                $querystring = $querystring_arr[ 'query' ] ;

                parse_str( $querystring , $newquery ) ;
                $url = add_query_arg( $newquery , get_permalink( sumo_get_product_id( $obj ) ) ) ;

                return $url ;
            }
        }
        return $url ;
    }

    public function get_membership_products_to_disable_restriction( $this_id ) {

        $membership_products = array() ;
        $content_access_type = get_post_meta( $this_id , 'sumomemberships_products_posts_pages_settings' , true ) ;

        if( $content_access_type == 'with_particular_plans' ) {
            $plan_rules = sumo_get_plan_rules_added( $this_id , 'members_with_plan' ) ;

            $plan_ids = $this->extract_plan_ids_from_plan_rules( $plan_rules ) ;

            foreach( $plan_ids as $plan_id ) {
                if( get_post_meta( $plan_id , 'sumomemberships_plan_associated_product' , true ) != "" ) {

                    $membership_products[] = get_post_meta( $plan_id , 'sumomemberships_plan_associated_product' , true ) ;
                }
            }
        }
        return $membership_products ;
    }

    public function extract_plan_ids_from_plan_rules( $plan_rules ) {

        $plan_ids = array() ;

        if( is_array( $plan_rules ) ) {
            foreach( $plan_rules as $each_rules ) {

                $plan_ids[] = $each_rules[ 'plan_id' ] ;
            }
        }
        return $plan_ids ;
    }

    //Common Function for Post, Page and Product accessibility

    public function post_r_page_r_product_accessibility( $this_id , $return_value , $is_type_variable = false ) {
        $this_type           = get_post_type( $this_id ) ;
        $this_member_id      = get_current_user_id() ;
        $this_member_post_id = sumo_get_member_post_id( $this_member_id ) ;

        $is_this_third_party_type_enabled = get_option( "sumomemberships_$this_type" ) == "yes" ;
        //Provide Access if Third Party Cutom Post Type is disabled
        if( ! $is_this_third_party_type_enabled && $this_type != "post" && $this_type != "page" && $this_type != "product" ) {
            return $return_value ;
        }

        $content_access_type      = get_post_meta( $this_id , 'sumomemberships_products_posts_pages_settings' , true ) ;
        $members_with_plan_rules  = sumo_get_plan_rules_added( $this_id , 'members_with_plan' ) ;
        $users_without_plan_rules = sumo_get_plan_rules_added( $this_id , 'users_without_plan' ) ;

        if( $is_type_variable ) {

            $this_obj              = sumo_get_product( $this_id ) ;
            $this_product_level_id = sumo_get_product_level_id( $this_obj ) ;

            $content_access_type      = get_post_meta( $this_product_level_id , 'sumomemberships_products_posts_pages_settings' , true ) ;
            $members_with_plan_rules  = sumo_get_plan_rules_added( $this_product_level_id , 'members_with_plan' ) ;
            $users_without_plan_rules = sumo_get_plan_rules_added( $this_product_level_id , 'users_without_plan' ) ;
        }
        //Get Active Plans purchased
        $member_purchased_active_plans = sumo_get_member_purchased_plans_list( $this_member_post_id ) ;
        //Get active privileged linked plans.
        $get_active_linked_plans       = sumo_get_available_privileged_linked_plans( $this_member_post_id ) ;

        $check_for_linked_users = sumo_check_linked_users_had_privilege( $this_member_id ) ;
        if( $content_access_type == 'all_members' ) {

            if( sumo_is_member_purchased_any_plan( $this_member_post_id ) ) {

                $member_since_date = get_post_meta( $this_member_post_id , 'sumomemberships_member_since_date' , true ) ;

                $is_scheduled = get_post_meta( $this_id , 'sumomemberships_all_members_schedule_type' , true ) == 'scheduled' ? true : false ;

                if( $is_scheduled ) {
                    $duration_value  = get_post_meta( $this_id , 'sumomemberships_all_members_scheduled_duration_value' , true ) ;
                    $duration_period = get_post_meta( $this_id , 'sumomemberships_all_members_scheduled_duration_period' , true ) ;

                    $duration_length = sumo_conversion_for_plan_duration( $duration_value , $duration_period ) ;

                    $delaytimestamp = ( int ) ($member_since_date + $duration_length) ;
                } else {
                    $delaytimestamp = '' ;
                }
                //Checking Active plans
                if( is_array( $member_purchased_active_plans ) && ! empty( $member_purchased_active_plans ) ) {

                    if( time() >= $delaytimestamp && $delaytimestamp > 0 ) {
                        return $return_value ;
                    } else if( $delaytimestamp == '' ) {
                        return $return_value ;
                    }
                }
            }
            if( is_array( $check_for_linked_users ) && ! empty( $check_for_linked_users ) ) {
                $is_scheduled = get_post_meta( $this_id , 'sumomemberships_all_members_schedule_type' , true ) == 'scheduled' ? true : false ;

                if( $is_scheduled ) {
                    $duration_value  = get_post_meta( $this_id , 'sumomemberships_all_members_scheduled_duration_value' , true ) ;
                    $duration_period = get_post_meta( $this_id , 'sumomemberships_all_members_scheduled_duration_period' , true ) ;

                    $duration_length = sumo_conversion_for_plan_duration( $duration_value , $duration_period ) ;
                    $since_date      = sumo_check_linked_users_of_member_since( $this_member_id ) ;
                    $delaytimestamp1 = ( int ) ($since_date + $duration_length) ;
                } else {
                    $delaytimestamp1 = '' ;
                }
                if( time() >= $delaytimestamp1 && $delaytimestamp1 > 0 ) {
                    return $return_value ;
                } else if( $delaytimestamp1 == '' ) {
                    return $return_value ;
                }
            }
            return '' ;
        } elseif( $content_access_type == 'all_non_members' ) {
            if( ! empty( $check_for_linked_users ) ) {
                return '' ;
            }
            if( sumo_is_member_purchased_any_plan( $this_member_post_id ) ) {
                //Checking Active plans
                if( is_array( $member_purchased_active_plans ) && ! empty( $member_purchased_active_plans ) ) {
                    return '' ;
                }
            }
            return $return_value ;
        } elseif( $content_access_type == 'with_particular_plans' ) {
            if( is_array( $check_for_linked_users ) && ! empty( $check_for_linked_users ) ) {
                foreach( $members_with_plan_rules as $each_values ) {
                    if( in_array( $each_values[ 'plan_id' ] , $check_for_linked_users ) ) {
                        $plan_schedule_duration_timestamp = sumo_get_plan_schedules_from_post_page_product( $each_values ) ;

                        $plan_since_time = sumo_check_linked_users_of_plan_activate_since( $this_member_id ) ;

                        $delay_timestamp = ( int ) ($plan_schedule_duration_timestamp + $plan_since_time) ;

                        if( time() >= $delay_timestamp && $delay_timestamp > 0 ) {
                            return $return_value ;
                        } else if( $delay_timestamp == "" ) {
                            return $return_value ;
                        }
                    }
                }
            }
            if( sumo_is_member_purchased_any_plan( $this_member_post_id ) && is_array( $members_with_plan_rules ) ) {
                foreach( $members_with_plan_rules as $each_values ) {
                    $plan_id = $each_values[ 'plan_id' ] ;
                    //Provide Access if Plan ids from rules are available either in Linked Plans or Member purchased Plans.
                    if( in_array( $plan_id , $get_active_linked_plans ) || in_array( $plan_id , $member_purchased_active_plans ) ) {
                        $plan_schedule_duration_timestamp = sumo_get_plan_schedules_from_post_page_product( $each_values ) ;

                        $plan_since_time = sumo_get_member_plan_purchased_date( $this_member_post_id , $plan_id , true ) ;

                        $delay_timestamp = (( int ) $plan_schedule_duration_timestamp + ( int ) $plan_since_time) ;
                        if( $plan_schedule_duration_timestamp != '' ) {
                            if( time() >= $delay_timestamp && $delay_timestamp > 0 ) {
                                return $return_value ;
                            } else if( $delay_timestamp == "" ) {
                                return $return_value ;
                            }
                        } else {
                            return $return_value ;
                        }
                    }
                }
                //If no rules are given then it would default set to No Restriction for Members alone
                if( empty( $members_with_plan_rules ) ) {
                    return $return_value ;
                }
            }
            return '' ;
        } elseif( $content_access_type == 'without_particular_plans' ) {

            if( is_array( $check_for_linked_users ) && ! empty( $check_for_linked_users ) ) {
                foreach( $users_without_plan_rules as $each_values ) {
                    if( in_array( $each_values[ 'plan_id' ] , $check_for_linked_users ) ) {
                        return '' ;
                    }
                }
            }

            if( sumo_is_member_purchased_any_plan( $this_member_post_id ) && is_array( $users_without_plan_rules ) ) {

                foreach( $users_without_plan_rules as $each_values ) {

                    $plan_id = $each_values[ 'plan_id' ] ;
                    //Provide Restriction if Plan ids from rules are available either in Linked Plans or Member purchased Plans.
                    if( in_array( $plan_id , $get_active_linked_plans ) || in_array( $plan_id , $member_purchased_active_plans ) ) {
                        return '' ;
                    }
                }
            }
            return $return_value ;
        }
        //Access by All Users
        return $return_value ;
    }

    //Get Redirect URL from Restricted URL Tab.

    public function get_redirect_url_from_restricted_url_tab( $this_id ) {


        $this_member_id = get_current_user_id() ;

        $this_member_post_id = sumo_get_member_post_id( $this_member_id ) ;

        $this_url = array( '1' => "http://" . $_SERVER[ "HTTP_HOST" ] . $_SERVER[ "REQUEST_URI" ] , '2' => "https://" . $_SERVER[ "HTTP_HOST" ] . $_SERVER[ "REQUEST_URI" ] ) ;


        //Get Active Plans purchased
        $member_purchased_active_plans = sumo_get_member_purchased_plans_list( $this_member_post_id ) ;
        //Get active privileged linked plans.
        $get_active_linked_plans       = sumo_get_available_privileged_linked_plans( $this_member_post_id ) ;

        $linked_plans           = sumo_get_available_linked_plans( $this_member_post_id , 'active' ) ;
        $check_for_linked_users = sumo_check_linked_users_had_privilege( $this_member_id ) ;

        $row_count = get_option( 'sumomemberships_restricted_urls_row_count' ) > 0 ? get_option( 'sumomemberships_restricted_urls_row_count' ) : 0 ;

        for( $i = 1 ; $i <= $row_count ; $i ++ ) {


            $restrict_url  = get_option( 'sumomemberships_url_to_restrict' . $i ) ;
            $access_method = get_option( 'sumomemberships_restrictions_type' . $i ) ;
            $plan_level    = get_option( 'sumomemberships_with_r_without_access_plan_level' . $i ) ;
            $redirect_to   = get_option( 'sumomemberships_redirect_to' . $i ) ;
            //Check if Access type presents both in post/page/product rules and redirection url tab rules
            if( $access_method && in_array( $restrict_url , $this_url ) && $redirect_to != "" ) {

                if( $access_method == "with_particular_plans" ) {
                    if( ( ! empty( $member_purchased_active_plans ) && in_array( $plan_level , $member_purchased_active_plans )) || ( ! empty( $linked_plans ) && in_array( $plan_level , $linked_plans )) || ( ! empty( $check_for_linked_users ) && in_array( $plan_level , $check_for_linked_users )) ) {
                        return $redirect_to ;
                    }
                } elseif( $access_method == "without_particular_plans" ) {
                    if( ( ! empty( $member_purchased_active_plans ) && ! in_array( $plan_level , $member_purchased_active_plans ) ) ) {
                        if( ! empty( $linked_plans ) && ! in_array( $plan_level , $linked_plans ) || ( ! empty( $check_for_linked_users ) && ! in_array( $plan_level , $check_for_linked_users )) ) {
                            return $redirect_to ;
                        }
                        if( empty( $linked_plans ) && empty( $check_for_linked_users ) ){
                            return $redirect_to ;
                        }
                    } elseif( empty( $member_purchased_active_plans ) && empty( $linked_plans ) && empty( $check_for_linked_users ) ) {
                        return $redirect_to ;
                    }
                } elseif( $access_method == "all_members" ) {
                    if( $this_member_post_id ) {
                        if( ! empty( $member_purchased_active_plans ) || ! empty( $linked_plans ) || ! empty( $check_for_linked_users ) ) {
                            return $redirect_to ;
                        }
                    } else {
                        if( ! empty( $check_for_linked_users ) ) {
                            return $redirect_to ;
                        }
                    }
                } elseif( $access_method == "all_non_members" ) {
                    if( $this_member_post_id ) {
                        if( empty( $member_purchased_active_plans ) && empty( $linked_plans ) && empty( $check_for_linked_users ) ) {
                            return $redirect_to ;
                        }
                    } else {
                        if( empty( $check_for_linked_users ) ) {
                            return $redirect_to ;
                        }
                    }
                } else {
                    return $redirect_to ;
                }
            }
        }
        return "" ;
    }

    public function display_restriction_message( $message ) {

        return '<p class="sumo_restriction_message" style = "color: red; font-size: 18px;">' . $message . '</p>' ;
    }

    //Checking whether Membership order is placing on and Saving User Meta.

    public function activate_membership_after_order_placed_successful( $order_id ) {

        $order = new WC_Order( $order_id ) ;

        if( get_post_meta( $order_id , 'sumomemberships_check_for_order_placed' , true ) != $order_id ) {

            $this_member_id = sumo_get_customer_id_from_order( $order ) ;

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

                $this->save_membership_details( $product_id , $this_member_id , $order_id ) ;
            }
            //Controlling duplication while changing Order status
            update_post_meta( $order_id , 'sumomemberships_check_for_order_placed' , $order_id ) ;
        }
    }

    public function get_expire_duration_timestamp( $this_member_id , $plan_id , $from_time ) {

        $timestamp     = '' ;
        $plan_duration = sumo_get_membership_plan_duration( $plan_id ) ;

        if( $plan_duration != "Indefinite" && $plan_duration != "" ) {

            $duration_value  = get_post_meta( $plan_id , 'sumomemberships_duration_value' , true ) ;
            $duration_period = get_post_meta( $plan_id , 'sumomemberships_duration_period' , true ) ;

            $timestamp = sumo_get_timestamp_to_schedule_cron( $duration_value , $duration_period , $plan_id , $this_member_id , $from_time ) ;

            wp_clear_scheduled_hook( 'sumo_memberships_process_plan_duration_validity' , array( $this_member_id , $plan_id ) ) ;

            if( $timestamp > 0 ) {
                if( ! wp_next_scheduled( 'sumo_memberships_process_plan_duration_validity' , array( $this_member_id , $plan_id ) ) ) {
                    wp_schedule_single_event( $timestamp , 'sumo_memberships_process_plan_duration_validity' , array( $this_member_id , $plan_id ) ) ;
                }
            }
        }
        return $timestamp > 0 ? date( 'Y-m-d h:i:s' , $timestamp ) : '' ;
    }

    //Cron Callback. Provide privilege to linked plan for the member

    public function process_linked_plan_privilege( $member_id , $linked_plan_id , $parent_plan_id ) {

        $post_id = sumo_get_member_post_id( $member_id ) ;

        $unique_id = sumo_get_plan_key( $post_id , $parent_plan_id ) ;

        $saved_plans = get_post_meta( $post_id , 'sumomemberships_saved_plans' , true ) ;

        if( $unique_id && isset( $saved_plans[ $unique_id ][ 'link_plans' ] ) ) {

            $get_existing_link_data = $saved_plans[ $unique_id ][ 'link_plans' ] ;

            $get_scheduled_link_data = $saved_plans[ $unique_id ][ 'scheduled_link_plans' ] ;

            unset( $get_scheduled_link_data[ $linked_plan_id ] ) ;

            $new_data_to_link = ( array ) $linked_plan_id ;

            $new_data = array_merge( $new_data_to_link , $get_existing_link_data ) ;

            $saved_plans[ $unique_id ][ 'link_plans' ] = $new_data ;

            $saved_plans[ $unique_id ][ 'scheduled_link_plans' ] = $get_scheduled_link_data ;
            do_action( 'sumomemberships_plan_status_changed' , $post_id , $linked_plan_id , $saved_plans[ $unique_id ][ 'choose_status' ] ) ;
            update_post_meta( $post_id , 'sumomemberships_saved_plans' , $saved_plans ) ;
        }
    }

    //Cron Callback. After it reaches its duration time revoke this plan

    public function process_plan_duration_validity( $member_id , $plan_id ) {

        sumo_remove_capability_from_member( $member_id , $plan_id ) ;

        $this->revoke_membership_plan( $member_id , $plan_id , 'expired' ) ;
    }

    //Cancelling Memberships when the order fails to process membership plan

    public function cancel_membership_after_order_fails( $order_id , $old_status , $new_status ) {

        $membership_order_status = is_array( get_option( 'sumo_membership_plan_access_order_status' ) ) && ! empty( get_option( 'sumo_membership_plan_access_order_status' ) ) ? get_option( 'sumo_membership_plan_access_order_status' ) : array() ;

        if( in_array( $new_status , $membership_order_status ) )
            return ;

        if( ! get_post_meta( $order_id , 'sumomemberships_check_for_order_placed' , true ) )
            return ;

        $order = new WC_Order( $order_id ) ;

        $member_id = ( int ) sumo_get_customer_id_from_order( $order ) ;

        $post_id = sumo_get_member_post_id( $member_id ) ;

        $unique_id = sumo_get_plan_key_from_member_order( $post_id , $order_id ) ;

        $saved_plans = get_post_meta( $post_id , 'sumomemberships_saved_plans' , true ) ;

        if( $unique_id && isset( $saved_plans[ $unique_id ][ 'choose_plan' ] ) ) {

            $plan_id = $saved_plans[ $unique_id ][ 'choose_plan' ] ;

            sumo_remove_capability_from_member( $member_id , $plan_id ) ;

            $this->revoke_membership_plan( $member_id , $plan_id , 'cancelled' ) ;

            update_post_meta( $order_id , 'sumomemberships_check_for_order_placed' , '' ) ;
        }
    }

    public function revoke_membership_plan( $member_id , $plan_id , $status ) {

        $this_user = new WP_User( $member_id ) ;

        $post_id = sumo_get_member_post_id( $member_id ) ;

        $saved_plans = get_post_meta( $post_id , 'sumomemberships_saved_plans' , true ) ;

        $unique_id = sumo_get_plan_key( $post_id , $plan_id ) ;

        if( $unique_id && isset( $saved_plans[ $unique_id ][ 'choose_status' ] ) ) {

            sumo_clear_linked_plans_privilege_cron( $post_id , $unique_id , $member_id , $plan_id ) ;

            $saved_plans[ $unique_id ][ 'choose_status' ]        = $status ;
            $saved_plans[ $unique_id ][ 'to_date' ]              = '' ;
            $saved_plans[ $unique_id ][ 'available_duration' ]   = '' ;
            $saved_plans[ $unique_id ][ 'link_plans' ]           = array() ;
            $saved_plans[ $unique_id ][ 'scheduled_link_plans' ] = array() ;
            do_action( 'sumomemberships_plan_status_changed' , $post_id , $plan_id , $status ) ;
            update_post_meta( $post_id , 'sumomemberships_saved_plans' , $saved_plans ) ;

            do_action( 'sumomemberships_cron_expiry_plan_status_updation' , $plan_id , $status , $post_id , $unique_id ) ;
        }
    }

    public function save_membership_details( $product_id , $this_member_id , $order_id = '' , $plan_id_from_signup = '' ) {

        if( sumo_is_membership_product( $product_id ) && $this_member_id > 0 ) {

            $plan_ids = sumo_get_product_associated_plan_ids( $product_id ) ;

            if( $plan_id_from_signup ) {
                $this->sumo_common_function_for_save_plan_id_for_users( $product_id , $this_member_id , $order_id , $plan_id_from_signup ) ;
            } else {
                foreach( $plan_ids as $plan_id ) {
                    $this->sumo_common_function_for_save_plan_id_for_users( $product_id , $this_member_id , $order_id , $plan_id ) ;
                }
            }
        }
    }

    public function sumo_common_function_for_save_plan_id_for_users( $product_id , $this_member_id , $order_id , $plan_id ) {
        $order = new WC_Order( $order_id ) ;
        if( sumo_is_membership_post( $plan_id ) ) {

            $privileged_link_plans = sumo_get_privileged_link_plans( $plan_id , $this_member_id ) ;

            $scheduled_link_plans = sumo_get_schedule_link_plans( $plan_id , $this_member_id ) ;

            //Existing Plans Saved
            if( sumo_get_member_post_id( $this_member_id ) > 0 ) {

                $post_id = sumo_get_member_post_id( $this_member_id ) ;

                $saved_plans = sumo_member_saved_plans($post_id) ;

                $existing_saved_plans      = $saved_plans ;
                $previous_plan_information = $saved_plans ;

                if( sumo_membership_check_is_array( $saved_plans ) && sumo_get_plan_key( $post_id , $plan_id ) ) {

                    $unique_id = sumo_get_plan_key( $post_id , $plan_id ) ;

                    $existing_saved_plans[ $unique_id ] = array(
                        'choose_plan'              => $existing_saved_plans[ $unique_id ][ 'choose_plan' ] ,
                        'choose_status'            => apply_filters( 'sumomemberships_plan_status_management' , 'active' , $product_id , $order_id ) ,
                        'from_date'                => $existing_saved_plans[ $unique_id ][ 'from_date' ] ,
                        'to_date'                  => ! sumo_is_subcription_enabled( $product_id , $order_id ) ? $this->get_expire_duration_timestamp( $this_member_id , $plan_id , $existing_saved_plans[ $unique_id ][ 'from_date' ] ) : '' ,
                        'associated_product'       => ! sumo_is_subcription_enabled( $product_id , $order_id ) ? array_merge( ( array ) $product_id , $existing_saved_plans[ $unique_id ][ 'associated_product' ] ) : array() ,
                        'associated_subsc_product' => sumo_is_subcription_enabled( $product_id , $order_id ) ? $product_id : '' ,
                        'associated_subsc_id'      => $existing_saved_plans[ $unique_id ][ 'associated_subsc_id' ] ,
                        'plan_slug'                => get_post_meta( $plan_id , 'sumomemberships_plan_slug' , true ) ,
                        'scheduled_link_plans'     => $scheduled_link_plans ,
                        'link_plans'               => $privileged_link_plans ,
                        'available_duration'       => '' ,
                        'order_id'                 => array_merge( ( array ) $order_id , $existing_saved_plans[ $unique_id ][ 'order_id' ] )
                            //date could extend if same plan is placed
                            ) ;

                    do_action( 'sumomemberships_update_existing_plan_upon_order_status' , $previous_plan_information , $existing_saved_plans , $unique_id , $post_id ) ;
                    $new_data = $existing_saved_plans ;
                } else {
                    $parent = sumo_get_parent_order_id( $order ) ;
                    if( $parent > 0 && sumo_membership_check_is_array( $saved_plans ) ) {
                        $transfered_user_id = get_post_meta( $this_member_id , $plan_id . 'plan_switched_to' , true ) ;
                        $post_id            = sumo_get_member_post_id( $this_member_id ) ;
                        if( sumo_get_plan_key( $post_id , $plan_id ) ) {

                            $unique_id = sumo_get_plan_key( $post_id , $plan_id ) ;

                            $existing_saved_plans[ $unique_id ] = array(
                                'choose_plan'              => $existing_saved_plans[ $unique_id ][ 'choose_plan' ] ,
                                'choose_status'            => apply_filters( 'sumomemberships_plan_status_management' , 'active' , $product_id , $order_id ) ,
                                'from_date'                => $existing_saved_plans[ $unique_id ][ 'from_date' ] ,
                                'to_date'                  => ! sumo_is_subcription_enabled( $product_id , $order_id ) ? $this->get_expire_duration_timestamp( $transfered_user_id , $plan_id , $existing_saved_plans[ $unique_id ][ 'from_date' ] ) : '' ,
                                'associated_product'       => ! sumo_is_subcription_enabled( $product_id , $order_id ) ? array_merge( ( array ) $product_id , $existing_saved_plans[ $unique_id ][ 'associated_product' ] ) : array() ,
                                'associated_subsc_product' => sumo_is_subcription_enabled( $product_id , $order_id ) ? $product_id : '' ,
                                'associated_subsc_id'      => $existing_saved_plans[ $unique_id ][ 'associated_subsc_id' ] ,
                                'plan_slug'                => get_post_meta( $plan_id , 'sumomemberships_plan_slug' , true ) ,
                                'scheduled_link_plans'     => $scheduled_link_plans ,
                                'link_plans'               => $privileged_link_plans ,
                                'available_duration'       => '' ,
                                'order_id'                 => array_merge( ( array ) $order_id , $existing_saved_plans[ $unique_id ][ 'order_id' ] )
                                    //date could extend if same plan is placed
                                    ) ;

                            do_action( 'sumomemberships_update_existing_plan_upon_order_status' , $previous_plan_information , $existing_saved_plans , $unique_id , $post_id ) ;
                            $new_data = $existing_saved_plans ;
                        }
                    } else {
                        $newuniqid = uniqid() ;
                        if( $order_id ) {
                            $post       = get_post( $order_id ) ;
                            $order_date = ( $post->post_modified ) ;
                        } else {
                            $order_date = date( 'Y-m-d h:i:s' ) ;
                        }
                        $new_saved_plans = array(
                            $newuniqid => array(
                                'choose_plan'              => $plan_id ,
                                'choose_status'            => apply_filters( 'sumomemberships_plan_status_management' , 'active' , $product_id , $order_id ) ,
                                'from_date'                => $order_date ,
                                'to_date'                  => ! sumo_is_subcription_enabled( $product_id , $order_id ) ? $this->get_expire_duration_timestamp( $this_member_id , $plan_id , $order_date ) : '' ,
                                'associated_product'       => ! sumo_is_subcription_enabled( $product_id , $order_id ) ? ( array ) $product_id : array() ,
                                'associated_subsc_product' => sumo_is_subcription_enabled( $product_id , $order_id ) ? $product_id : '' ,
                                'associated_subsc_id'      => '' ,
                                'plan_slug'                => get_post_meta( $plan_id , 'sumomemberships_plan_slug' , true ) ,
                                'scheduled_link_plans'     => $scheduled_link_plans ,
                                'link_plans'               => $privileged_link_plans ,
                                'available_duration'       => '' ,
                                'order_id'                 => ( array ) $order_id
                            ) ) ;
                        $new_data        = $existing_saved_plans + $new_saved_plans ;

                        do_action( 'sumomemberships_add_new_plan_upon_order_status' , $new_saved_plans , $plan_id , $newuniqid , $post_id ) ;
                    }
                }
                do_action( 'sumomemberships_plan_status_changed' , $post_id , $plan_id , 'active' ) ;
                update_post_meta( $post_id , 'sumomemberships_saved_plans' , $new_data ) ;
            } else {
                //New Plan to Save
                $firstuniqid = uniqid() ;
                $args        = array(
                    'post_title'     => get_userdata( $this_member_id )->user_login ,
                    'post_type'      => "sumomembers" ,
                    'post_status'    => 'publish' ,
                    'posts_per_page' => -1
                        ) ;
                $post_id     = wp_insert_post( $args ) ;
                if( $order_id ) {
                    $post       = get_post( $order_id ) ;
                    $order_date = ( $post->post_modified ) ;
                } else {
                    $order_date = date( 'Y-m-d h:i:s' ) ;
                }
                $saved_plans = array(
                    $firstuniqid => array(
                        'choose_plan'              => $plan_id ,
                        'choose_status'            => apply_filters( 'sumomemberships_plan_status_management' , 'active' , $product_id , $order_id ) ,
                        'from_date'                => $order_date ,
                        'to_date'                  => ! sumo_is_subcription_enabled( $product_id , $order_id ) ? $this->get_expire_duration_timestamp( $this_member_id , $plan_id , $order_date ) : '' ,
                        'associated_product'       => ! sumo_is_subcription_enabled( $product_id , $order_id ) ? ( array ) $product_id : array() ,
                        'associated_subsc_product' => sumo_is_subcription_enabled( $product_id , $order_id ) ? $product_id : '' ,
                        'associated_subsc_id'      => '' ,
                        'plan_slug'                => get_post_meta( $plan_id , 'sumomemberships_plan_slug' , true ) ,
                        'scheduled_link_plans'     => $scheduled_link_plans ,
                        'link_plans'               => $privileged_link_plans ,
                        'available_duration'       => '' ,
                        'order_id'                 => ( array ) $order_id
                    ) ) ;
                do_action( 'sumomemberships_plan_status_changed' , $post_id , $plan_id , 'active' ) ;
                update_post_meta( $post_id , 'sumomemberships_userid' , $this_member_id ) ;
                update_post_meta( $post_id , 'sumomemberships_saved_plans' , $saved_plans ) ;

                add_post_meta( $post_id , 'sumomemberships_member_since_date' , time() ) ;

                do_action( 'sumomemberships_add_new_plan_upon_order_status' , $saved_plans , $plan_id , $firstuniqid , $post_id ) ;
            }
            //Can be helpful for other plugins to provide compatibility to this plugin
            sumo_add_capability_to_member( $this_member_id , $plan_id ) ;
        }
    }

    //Display Notice on Membership products

    public function display_notice_on_membership_products() {
        global $post ;

        if( isset( $post->ID ) ) {

            $product_level_id = $post->ID ;
            $get_plan_name    = array() ;

            $product_obj = sumo_get_product( $product_level_id ) ;

            if( is_product() && is_object( $product_obj ) ) {

                if( $product_obj->is_type( 'variable' ) ) {
                    ?>
                    <script type='text/javascript'>

                        jQuery( document ).ready( function() {

                            jQuery( '#sumo_display_notice_on_variation' ).hide() ;

                            jQuery( document ).on( 'change' , 'select' , function() {
                                var variation_id = jQuery( 'input:hidden[name=variation_id]' ).val() ;

                                if( variation_id === '' || variation_id === undefined ) {
                                    jQuery( '#sumo_display_notice_on_variation' ).hide() ;
                                    return false ;
                                } else {
                                    var dataparam = ( {
                                        action : 'sumo_display_notice_on_variation' ,
                                        variation_id : variation_id
                                    } ) ;
                                    jQuery.post( "<?php echo admin_url( 'admin-ajax.php' ) ; ?>" , dataparam , function( response ) {
                                        if( response != '' ) {
                                            jQuery( '#sumo_display_notice_on_variation' ).addClass( 'woocommerce-message' ) ;
                                            jQuery( '#sumo_display_notice_on_variation' ).show() ;
                                            jQuery( '#sumo_display_notice_on_variation' ).html( response ) ;
                                        } else {
                                            jQuery( '#sumo_display_notice_on_variation' ).hide() ;
                                        }
                                    } ) ;
                                }
                            } ) ;
                        } ) ;
                    </script>
                    <?php
                } else {
                    if( sumo_is_membership_product( $product_level_id ) ) {

                        $plan_associated_ids = sumo_get_product_associated_plan_ids( $product_level_id ) ;

                        foreach( $plan_associated_ids as $each_plan_id ) {
                            $get_plan_name[] = get_post_meta( $each_plan_id , 'sumomemberships_plan_name' , true ) ;
                        }

                        $plan_name = implode( ', ' , $get_plan_name ) ;

                        wc_add_notice( __( "Purchasing this product will provide access to " , 'sumomemberships' ) . '<b>' . $plan_name . '</b>' , 'success' ) ;
                    }
                }
            }
        }
    }

    //Display Notice on Membership Variable Product depends on variation

    public function display_notice_on_variation_product() {
        ?><div id='sumo_display_notice_on_variation'></div><?php
    }

    //Load Ajax to process notice on Membership variation product

    public function AJAX_notice_on_variation_product() {

        if( isset( $_POST[ 'variation_id' ] ) ) {

            $get_plan_name = array() ;

            if( sumo_is_membership_product( $_POST[ 'variation_id' ] ) ) {

                $plan_associated_ids = sumo_get_product_associated_plan_ids( $_POST[ 'variation_id' ] ) ;

                foreach( $plan_associated_ids as $each_plan_id ) {
                    $get_plan_name[] = get_post_meta( $each_plan_id , 'sumomemberships_plan_name' , true ) ;
                }

                $plan_name = implode( ', ' , $get_plan_name ) ;

                echo __( "Purchasing this Variation will provide access to " , "sumomemberships" ) . '<b>' . $plan_name . '</b>' ;
            } else {
                echo '' ;
            }
        }
        exit() ;
    }

    public function display_default_plans_on_user_signup_form() {

        $valid_plans   = array() ;
        $default_plans = get_option( 'sumomemberships_default_plans' ) ;
        if( $default_plans ) {

            $get_plans = is_array( $default_plans ) ? $default_plans : explode( ',' , $default_plans ) ;
            foreach( $get_plans as $plan_id ) {

                $membership_product_id = get_post_meta( $plan_id , 'sumomemberships_plan_associated_product' , true ) ;

                $product = sumo_get_product( $membership_product_id ) ;

                if( is_object( $product ) ) {
                    $valid_plans[] = $plan_id ;
                }
            }
        }
        update_option( 'sumomemberships_valid_default_plans' , $valid_plans ) ;

        if( ! empty( $valid_plans ) ) {
            $default_plan_msg = str_replace( '[default_membership_plan(s)]' , '[default_membership_plans]' , get_option( 'sumo_msg_for_default_plans_msg_on_user_signup' ) ) ;
            echo do_shortcode( $default_plan_msg ) ;
        }
    }

    //Activate Default Membership Plans for the New User while Signing up

    public function activate_default_plans_on_user_signing_up( $this_user_id ) {

        $default_valid_plans = get_option( 'sumomemberships_valid_default_plans' ) ;

        if( is_array( $default_valid_plans ) && ! empty( $default_valid_plans ) ) {

            foreach( $default_valid_plans as $plan_id ) {

                $membership_product_id = get_post_meta( $plan_id , 'sumomemberships_plan_associated_product' , true ) ;

                $product = sumo_get_product( $membership_product_id ) ;

                is_object( $product ) ? $this->save_membership_details( $membership_product_id , $this_user_id , NULL , $plan_id ) : "" ;
            }
        }
    }

    public function force_create_account_if_guest() {

        if( ! is_user_logged_in() && is_checkout() && $this->is_cart_contains_membership_product() ) {
            $_POST[ 'createaccount' ] = 1 ;
        }
    }

    public function force_enable_signup_if_guest( $checkout ) {

        if( ! is_user_logged_in() && is_checkout() && isset( $checkout->enable_signup ) && isset( $checkout->enable_guest_checkout ) && $this->is_cart_contains_membership_product() ) {
            $checkout->enable_signup         = true ;
            $checkout->enable_guest_checkout = false ;
        }
    }

    public function is_cart_contains_membership_product() {

        foreach( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {
            if( isset( $cart_item[ 'product_id' ] ) ) {
                $product_level_id = $cart_item[ 'product_id' ] ;
                $variation_id     = isset( $cart_item[ 'variation_id' ] ) ? $cart_item[ 'variation_id' ] : 0 ;

                if( sumo_is_membership_product( $variation_id > 0 ? $variation_id : $product_level_id  ) ) {
                    return true ;
                }
            }
        }
        return false ;
    }

    public function hide_post_from_front_feed_category_page( $where , $thid ) {
        global $wpdb ;
        $current_post_type = ($thid->query_vars[ 'post_type' ]) ;
        $custom_post_types = array( 'post' ) ;
        $post_types        = sumo_get_third_parties_cpt_exists() ;
        foreach( $post_types as $custom_post_type ) {
            if( get_option( "sumomemberships_$custom_post_type" ) == "yes" ) {
                $custom_post_types[] = $custom_post_type ;
            }
        }
        if( $current_post_type == 'product' ) {
            if( get_option( 'sumo_restriction_type' ) == '1' && get_option( 'sumo_hide_restricted_products_in_shop_page' ) == "yes" ) {
                $args            = array( 'post_type'              => 'product' ,
                    'fields'                 => 'ids' ,
                    'posts_per_page'         => '-1' ,
                    'meta_query'             => array(
                        array(
                            'key'     => 'sumomemberships_products_posts_pages_settings' ,
                            'value'   => '' ,
                            'compare' => 'NOT IN' ,
                        ) ,
                    ) ,
                    'no_found_rows'          => true ,
                    'update_post_term_cache' => false ,
                    'update_post_post_cache' => false ,
                    'cache_results'          => false ,
                        ) ;
                $products        = get_posts( $args ) ;
                $ids_to_restrict = array() ;

                if( is_array( $products ) && ! empty( $products ) ) {
                    foreach( $products as $eachproducts ) {
                        $check_return_access = $this->post_r_page_r_product_accessibility( $eachproducts , $eachproducts ) ;
                        if( $check_return_access == '' ) {
                            $ids_to_restrict[] = $eachproducts ;
                        }
                    }
                }
                if( ! empty( $ids_to_restrict ) ) {
                    $search                 = $wpdb->prefix . "posts.post_type = 'product' AND ((" . $wpdb->prefix . "posts.post_status = 'publish'))" ;
                    $ids_to_restrict_string = "(" . implode( ',' , $ids_to_restrict ) . ")" ;
                    $append                 = "AND " . $wpdb->prefix . "posts.ID NOT IN $ids_to_restrict_string" ;
                    $replace                = $search . $append ;
                    $where                  = str_replace( $search , $replace , $where ) ;
                }
            }
        } elseif( in_array( $current_post_type , $custom_post_types ) ) {
            $restriction_type = get_option( 'sumo_restriction_type_for_content' ) ;
            if( $restriction_type == '2' ) {
                $postids = array() ;
                $arg     = array( 'post_type' => $current_post_type , 'posts_per_page' => '-1' , 'post_status' => 'publish' , 'fields' => 'ids' , 'cache_results' => false ) ;
                $post    = get_posts( $arg ) ;
                foreach( $post as $post_id ) {
                    $variable         = $this->post_r_page_r_product_accessibility( $post_id , $post_id , $is_type_variable = false ) ;
                    if( $variable != '' ) {
                        $postids[] = $variable ;
                    }
                }
                if( is_array( $postids ) && ! empty( $postids ) ) {
                    $implode_ids = implode( ',' , $postids ) ;
                    $where       .= "AND " . $wpdb->prefix . "posts.ID IN (" . $implode_ids . ")" ;
                }
            }
        }

        return $where ;
    }

}

new SUMOMemberships_Restrictions() ;

function sumo_check_linked_users_had_privilege( $user_id ) {
    $args    = array( 'post_type'     => 'sumomembers' ,
        'numberposts'   => '-1' ,
        'post_status'   => 'published' ,
        'fields'        => 'ids' ,
        'cache_results' => false ) ;
    $members = get_posts( $args ) ;
    $arr     = array() ;
    foreach( $members as $member_post_id ) {
        $get_associated_plans = ( array ) get_post_meta( $member_post_id , 'sumomemberships_saved_plans' , true ) ;
        foreach( $get_associated_plans as $each_plan ) {
            if( isset( $each_plan[ 'choose_status' ] ) && $each_plan[ 'choose_status' ] == 'active' && $each_plan[ 'choose_plan' ] > 0 ) {
                $new_array = get_post_meta( $member_post_id , 'sumo_linked_users_of_' . $each_plan[ 'choose_plan' ] , true ) ;

                if( is_array( $new_array ) ) {
                    if( in_array( $user_id , $new_array ) && sm_check_user_purchase_history( $each_plan[ 'choose_plan' ] , $user_id ) ) {
                        $arr[] = $each_plan[ 'choose_plan' ] ;
                    }
                }
            }
        }
    }
    return $arr ;
}

function sumo_check_linked_users_of_member_since( $user_id ) {
    $args              = array( 'post_type'     => 'sumomembers' , 'numberposts'   => '-1' ,
        'post_status'   => 'published' ,
        'fields'        => 'ids' ,
        'cache_results' => false ) ;
    $members           = get_posts( $args ) ;
    $member_since_date = false ;
    foreach( $members as $member_post_id ) {
        $get_associated_plans = ( array ) get_post_meta( $member_post_id , 'sumomemberships_saved_plans' , true ) ;
        foreach( $get_associated_plans as $each_plan ) {
            if( isset( $each_plan[ 'choose_status' ] ) && $each_plan[ 'choose_status' ] == 'active' && $each_plan[ 'choose_plan' ] > 0 ) {
                $new_array = get_post_meta( $member_post_id , 'sumo_linked_users_of_' . $each_plan[ 'choose_plan' ] , true ) ;
                if( is_array( $new_array ) ) {
                    if( in_array( $user_id , $new_array ) ) {
                        $member_since_date = get_post_meta( $member_post_id , 'sumomemberships_member_since_date' , true ) ;
                        return $member_since_date ;
                    }
                }
            }
        }
    }
}

function sumo_check_linked_users_of_plan_activate_since( $user_id ) {
    $args            = array( 'post_type'     => 'sumomembers' , 'numberposts'   => '-1' ,
        'post_status'   => 'published' ,
        'fields'        => 'ids' ,
        'cache_results' => false ) ;
    $members         = get_posts( $args ) ;
    $plan_since_time = false ;
    foreach( $members as $member_post_id ) {
        $get_associated_plans = ( array ) get_post_meta( $member_post_id , 'sumomemberships_saved_plans' , true ) ;
        foreach( $get_associated_plans as $each_plan ) {
            if( isset( $each_plan[ 'choose_status' ] ) && $each_plan[ 'choose_status' ] == 'active' && $each_plan[ 'choose_plan' ] > 0 ) {
                $new_array = get_post_meta( $member_post_id , 'sumo_linked_users_of_' . $each_plan[ 'choose_plan' ] , true ) ;
                if( is_array( $new_array ) ) {
                    if( in_array( $user_id , $new_array ) ) {
                        $plan_since_time = sumo_get_member_plan_purchased_date( $member_post_id , $each_plan[ 'choose_plan' ] , true ) ;
                        return $plan_since_time ;
                    }
                }
            }
        }
    }
}

if( get_option( 'sumo_hide_apply_job_button_for_job_listing_pt' ) == 'yes' ) {
    add_filter( 'job_manager_candidates_can_apply' , 'sumo_membership_compatible_for_wp_job_manager_restriction_of_candidates' , 10 , 2 ) ;
}

function sumo_membership_compatible_for_wp_job_manager_restriction_of_candidates( $instance , $post ) {
    $object = new SUMOMemberships_Restrictions() ;
    $return = $object->post_r_page_r_product_accessibility( $post->ID , $post->ID ) ;
    return $return ? $instance : false ;
}
