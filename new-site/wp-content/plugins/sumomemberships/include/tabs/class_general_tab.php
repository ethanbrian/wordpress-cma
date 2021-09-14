<?php

class SUMOGeneral_Settings_Tab {

    public function __construct() {

        add_action( 'init' , array( $this , 'load_default_settings' ) , 103 ) ; // update the default settings on page load

        add_filter( 'woocommerce_sumomemberships_settings_tabs_array' , array( $this , 'general_tab_setting' ) ) ; // Register a New Tab in a WooCommerce

        add_action( 'woocommerce_sumomemberships_settings_tabs_sumomembership_general_settings' , array( $this , 'register_admin_settings' ) ) ; // Call to register the admin settings in the Plugin Submenu with general settings tab

        add_action( 'woocommerce_update_options_sumomembership_general_settings' , array( $this , 'advance_update_settings' ) ) ; // call the woocommerce_update_options_{slugname} to update the values

        add_action( 'woocommerce_admin_field_sumomemberships_select_default_plans' , array( $this , 'select_default_plans' ) ) ;

        add_action( 'wp_ajax_sumo_search_membership_plans' , array( $this , 'AJAX_search_plans' ) ) ;

        add_action( 'admin_head' , array( $this , 'show_or_hide' ) ) ;
    }

    /*
     * Function to Define Name of the Tab
     */

    public static function general_tab_setting( $setting_tabs ) {
        if ( ! is_array( $setting_tabs ) )
            $setting_tabs                                      = ( array ) $setting_tabs ;
        $setting_tabs[ 'sumomembership_general_settings' ] = __( 'General' , 'sumomemberships' ) ;
        return array_filter( $setting_tabs ) ;
    }

    /*
     * Function label settings to Member Level Tab
     */

    public static function default_settings() {
        global $woocommerce ;
        $post_types       = sumo_get_third_parties_cpt_exists() ;
        $newcombinedarray = '' ;
        $categoryid       = array() ;
        $categoryname     = array() ;
        if ( function_exists( 'wc_get_order_statuses' ) ) {
            $orderstatus      = str_replace( 'wc-' , '' , array_keys( wc_get_order_statuses() ) ) ;
            $orderslugs       = array_values( wc_get_order_statuses() ) ;
            $newcombinedarray = array_combine( ( array ) $orderstatus , ( array ) $orderslugs ) ;
        } else {
            $taxonomy    = 'shop_order_status' ;
            $orderstatus = array() ;
            $orderslugs  = array() ;

            $term_args = array(
                'hide_empty' => false ,
                'orderby'    => 'date' ,
                    ) ;
            $tax_terms = get_terms( $taxonomy , $term_args ) ;
            foreach ( $tax_terms as $getterms ) {
                $orderstatus[] = $getterms->name ;
                $orderslugs[]  = $getterms->slug ;
            }
            $newcombinedarray = array_combine( ( array ) $orderslugs , ( array ) $orderstatus ) ;
        }
        unset( $newcombinedarray[ 'cancelled' ] ) ;
        unset( $newcombinedarray[ 'refunded' ] ) ;
        unset( $newcombinedarray[ 'failed' ] ) ;
        $job_listings = in_array( 'job_listing' , $post_types ) && get_option( 'sumomemberships_job_listing' ) == 'yes' ? 'checkbox' : 'hidden' ;
        return apply_filters( 'woocommerce_sumomemberships_general_settings' , array(
            array(
                'name' => __( 'Product Restriction Settings' , 'sumomemberships' ) ,
                'type' => 'title' ,
                'id'   => 'general_tab_setting'
            ) ,
            array(
                'name'     => __( 'Restriction Type' , 'sumomemberships' ) ,
                'type'     => 'select' ,
                'id'       => 'sumo_restriction_type' ,
                'newids'   => 'sumo_restriction_type' ,
                'class'    => 'sumo_restriction_type' ,
                'std'      => '1' ,
                'default'  => '1' ,
                'desc_tip' => __( 'Choose whether to make the product not purchasable or to apply the Content Restriction settings.' , 'sumomemberships' ) ,
                'options'  => array(
                    '1' => __( 'Product Should not be Purchasable' , 'sumomemberships' ) ,
                    '2' => __( 'Apply Content Restriction Settings' , 'sumomemberships' ) ,
                )
            ) ,
            array(
                'name'     => __( 'Hide Restricted Products in Shop Page and Category Page' , 'sumomemberships' ) ,
                'type'     => 'checkbox' ,
                'id'       => 'sumo_hide_restricted_products_in_shop_page' ,
                'newids'   => 'sumo_hide_restricted_products_in_shop_page' ,
                'class'    => 'sumo_hide_restricted_products_in_shop_page' ,
                'std'      => 'no' ,
                'default'  => 'no' ,
                'desc_tip' => __( 'When Enabled, the Restricted products will be hidden in Shop Page and Category Page' , 'sumomemberships' )
            ) ,
            array(
                'name'     => __( 'Restrict Multiple Plans' , 'sumomemberships' ) ,
                'type'     => 'checkbox' ,
                'id'       => 'sumo_restrict_multiple_plans' ,
                'newids'   => 'sumo_restrict_multiple_plans' ,
                'class'    => 'sumo_restrict_multiple_plans' ,
                'std'      => 'no' ,
                'default'  => 'no' ,
                'desc_tip' => __( 'Enable this option to restrict  the members to have and purchase multiple plans(allow only one plan  active at a time) if the member purchase a new plan then the old plan  should be removed.' , 'sumomemberships' )
            ) ,
            array( 'type' => 'sectionend' , 'id' => 'general_tab_setting' ) ,
            array(
                'name' => __( 'Content Restriction Settings' , 'sumomemberships' ) ,
                'type' => 'title' ,
                'id'   => 'general_tab_setting_for_content'
            ) ,
            array(
                'name'    => __( 'Give/Revoke access for Previously purchased members when a Plan is Linked/Unlinked with Another Plan' , 'sumomemberships' ) ,
                'type'    => 'checkbox' ,
                'id'      => 'sumo_allow_clp_access_to_oldmembers' ,
                'newids'  => 'sumo_allow_clp_access_to_oldmembers' ,
                'class'   => 'sumo_allow_clp_access_to_oldmembers' ,
                'std'     => 'no' ,
                'default' => 'no' ,
            ) ,
            array(
                'name'     => __( 'Restriction Type' , 'sumomemberships' ) ,
                'type'     => 'select' ,
                'id'       => 'sumo_restriction_type_for_content' ,
                'newids'   => 'sumo_restriction_type_for_content' ,
                'class'    => 'sumo_restriction_type_for_content' ,
                'std'      => '1' ,
                'default'  => '1' ,
                'options'  => array(
                    '1' => __( 'Limited Restriction' , 'sumomemberships' ) ,
                    '2' => __( 'Complete Restriction' , 'sumomemberships' ) ,
                    '3' => __( 'Redirection' , 'sumomemberships' ) ,
                ) ,
                'desc_tip' => __( 'If "Complete Restriction" is chosen, a 404 Page will be displayed when the restircted content is accessed. If "Limited Content" is chosen, only the title and few words of the of the original content will be displayed when the  restircted content is accessed. If "Redirection" is chosen, the User will be redirected to a custom page when the restircted content is accessed.' , 'sumomemberships' )
            ) ,
            array(
                'name'    => __( 'Display Excerpt Content' , 'sumomemberships' ) ,
                'type'    => 'checkbox' ,
                'id'      => 'sumo_display_excerpt_content' ,
                'newids'  => 'sumo_display_excerpt_content' ,
                'class'   => 'sumo_display_excerpt_content' ,
                'std'     => 'yes' ,
                'default' => 'yes' ,
            ) ,
            array(
                'name'              => __( 'Number of Words to Display' , 'sumomemberships' ) ,
                'type'              => 'number' ,
                'id'                => 'sumo_no_of_words_to_display' ,
                'newids'            => 'sumo_no_of_words_to_display' ,
                'class'             => 'sumo_no_of_words_to_display' ,
                'std'               => '50' ,
                'default'           => '50' ,
                'custom_attributes' => array(
                    'min' => 1 ,
                )
            ) ,
            array(
                'name'    => __( 'Hide "Apply for job" button for "job_listing" post type on frontend' , 'sumomemberships' ) ,
                'type'    => $job_listings ,
                'id'      => 'sumo_hide_apply_job_button_for_job_listing_pt' ,
                'newids'  => 'sumo_hide_apply_job_button_for_job_listing_pt' ,
                'class'   => 'sumo_hide_apply_job_button_for_job_listing_pt' ,
                'std'     => 'no' ,
                'default' => 'no' ,
            ) ,
            array(
                'name'    => __( 'Redirect to' , 'sumomemberships' ) ,
                'type'    => 'text' ,
                'id'      => 'sumo_redirect_to' ,
                'newids'  => 'sumo_redirect_to' ,
                'class'   => 'sumo_redirect_to' ,
                'std'     => '' ,
                'default' => '' ,
            ) ,
            array(
                'name'    => __( 'Hide Restricted Pages and Posts from Menu' , 'sumomemberships' ) ,
                'type'    => 'checkbox' ,
                'id'      => 'sumo_hide_restricted_posts_and_pages_from_menu' ,
                'newids'  => 'sumo_hide_restricted_posts_and_pages_from_menu' ,
                'class'   => 'sumo_hide_restricted_posts_and_pages_from_menu' ,
                'std'     => 'no' ,
                'default' => 'no' ,
            ) ,
            array( 'type' => 'sectionend' , 'id' => 'general_tab_setting_for_content' ) ,
            array(
                'name' => __( 'Default Membership Plans' , 'sumomemberships' ) ,
                'type' => 'title' ,
                'id'   => 'general_tab_default_membership_plans'
            ) ,
            array(
                'type' => 'sumomemberships_select_default_plans'
            ) ,
            array(
                'name'    => __( 'Disable Transfer Membership Plan option for Default Membership Plans' , 'sumomemberships' ) ,
                'type'    => 'checkbox' ,
                'id'      => 'sumo_restrict_trans_plan_for_dmp' ,
                'newids'  => 'sumo_restrict_trans_plan_for_dmp' ,
                'class'   => 'sumo_restrict_trans_plan_for_dmp' ,
                'std'     => 'no' ,
                'default' => 'no' ,
            ) ,
            array(
                'name'    => __( 'Disable Link Users option for Default Membership Plans' , 'sumomemberships' ) ,
                'type'    => 'checkbox' ,
                'id'      => 'sumo_restrict_link_users_for_dmp' ,
                'newids'  => 'sumo_restrict_link_users_for_dmp' ,
                'class'   => 'sumo_restrict_link_users_for_dmp' ,
                'std'     => 'no' ,
                'default' => 'no' ,
            ) ,
            array( 'type' => 'sectionend' , 'id' => 'general_tab_default_membership_plans' ) ,
            array(
                'name' => __( 'Frontend Membership Customization Settings' , 'sumomemberships' ) ,
                'type' => 'title' ,
                'id'   => 'sumomemberships_my_account_page_settings'
            ) ,
            array(
                'name'     => __( 'Enable Membership Menu in MyAccount Page' , 'sumomemberships' ) ,
                'id'       => 'sumomemberships_enable_customization_in_myaccount' ,
                'newids'   => 'sumomemberships_enable_customization_in_myaccount' ,
                'type'     => 'checkbox' ,
                'std'      => 'yes' ,
                'default'  => 'yes' ,
                'desc_tip' => __( 'When enabled, Memberships Customization is avail at MyAccount Page.' , 'sumomemberships' ) ,
            ) ,
            array(
                'name'     => __( 'Allow Members to Show Linked plans in My account Page' , 'sumomemberships' ) ,
                'type'     => 'checkbox' ,
                'id'       => 'sumo_allow_member_show_linked_plans' ,
                'newids'   => 'sumo_allow_member_show_linked_plans' ,
                'class'    => 'sumo_allow_member_show_linked_plans' ,
                'std'      => 'no' ,
                'default'  => 'no' ,
                'desc_tip' => __( 'Linked Plans can be displayed for each plans' , 'sumomemberships' )
            ) ,
            array(
                'name'     => __( 'Allow Members to Pause their Membership plans' , 'sumomemberships' ) ,
                'id'       => 'sumomemberships_pause_resume_option' ,
                'newids'   => 'sumomemberships_pause_resume_option' ,
                'type'     => 'checkbox' ,
                'std'      => 'no' ,
                'default'  => 'no' ,
                'desc_tip' => __( 'When enabled, members can pause their memberships plans from "My Memberships" section.' , 'sumomemberships' ) ,
            ) ,
            array(
                'name'     => __( 'Allow Members to Transfer their Membership plans' , 'sumomemberships' ) ,
                'id'       => 'sumomemberships_transfer_option' ,
                'newids'   => 'sumomemberships_transfer_option' ,
                'type'     => 'checkbox' ,
                'std'      => 'no' ,
                'default'  => 'no' ,
                'desc_tip' => __( 'When enabled, members can transfer their memberships plans from "My Memberships" section.' , 'sumomemberships' ) ,
            ) ,
            array(
                'name'     => __( 'Allow Members to link users with their plans' , 'sumomemberships' ) ,
                'type'     => 'select' ,
                'id'       => 'sumo_allow_member_link_users_with_plan' ,
                'newids'   => 'sumo_allow_member_link_users_with_plan' ,
                'class'    => 'sumo_allow_member_link_users_with_plan' ,
                'std'      => '1' ,
                'default'  => '1' ,
                'options'  => array(
                    '1' => __( 'Allow' , 'sumomemberships' ) ,
                    '2' => __( "Don't Allow" , "sumomemberships" ) ,
                ) ,
                'desc_tip' => __( 'When allowed, Members can link other users to access their plans' , 'sumomemberships' )
            ) ,
            array(
                'name'     => __( 'Transfer of Membership plans with linked users' , 'sumomemberships' ) ,
                'type'     => 'select' ,
                'id'       => 'sumo_allow_transfer_when_users_linked_with_plan' ,
                'newids'   => 'sumo_allow_transfer_when_users_linked_with_plan' ,
                'class'    => 'sumo_allow_transfer_when_users_linked_with_plan' ,
                'std'      => '1' ,
                'default'  => '1' ,
                'options'  => array(
                    '1' => __( 'Allow' , 'sumomemberships' ) ,
                    '2' => __( "Don't Allow" , "sumomemberships" ) ,
                ) ,
                'desc_tip' => __( 'When allowed, Members can Transfer their plans when the plan have linked users' , 'sumomemberships' )
            ) ,
            array( 'type' => 'sectionend' , 'id' => 'sumomemberships_my_account_page_settings' ) ,
            array(
                'name' => __( 'Order Status Settings' , 'sumomemberships' ) ,
                'type' => 'title' ,
                'id'   => 'sumomemberships_order_status_settings'
            ) ,
            array(
                'name'     => __( 'Membership Plan access will be available when Order Status becomes' , 'sumomemberships' ) ,
                'type'     => 'multiselect' ,
                'id'       => 'sumo_membership_plan_access_order_status' ,
                'newids'   => 'sumo_membership_plan_access_order_status' ,
                'class'    => 'sumo_membership_plan_access_order_status' ,
                'std'      => array( 'processing' , 'completed' ) ,
                'default'  => array( 'processing' , 'completed' ) ,
                'options'  => $newcombinedarray ,
                'desc_tip' => __( 'When allowed, Members can Transfer their plans when the plan have linked users' , 'sumomemberships' )
            ) ,
            array( 'type' => 'sectionend' , 'id' => 'sumomemberships_order_status_settings' ) ,
            array(
                'name' => __( 'Shortcodes' , 'sumomemberships' ) ,
                'type' => 'title' ,
                'id'   => 'sumomemberships_shortcodes'
            ) ,
            array(
                'desc' => sprintf( __( 'Use shortcode %s to display My Membership table in Frontend.' , 'sumomemberships' ) , '<b>[sumo_member_details]</b>' ) ,
                'type' => 'title' ,
                'id'   => 'sumomemberships_label'
            ) ,
            array(
                'desc' => sprintf( __( 'Use shortcode %s to display all the users who are associated with one/more membership plans.' , 'sumomemberships' ) , '<b>[sumo_members]</b>' ) ,
                'type' => 'title' ,
                'id'   => 'sumomemberships_label1'
            ) ,
            array(
                'desc' => sprintf( __( 'Use this shortcode %s to display all the Members with their details(including their plan(s) and status of the plan(s))' , 'sumomemberships' ) , '<b>[sumo_members_details]</b>' ) ,
                'type' => 'title' ,
                'id'   => 'sumomemberships_label3'
            ) ,
            array(
                'desc' => sprintf( __( 'Use shortcode %s to display all the users who are associated with the selected membership plans.'
                                . ' You can find the %s by the following steps %s. '
                                . ' Use the <b>plan id</b> between the double quotes <b>(” “)</b> in the shortcode. '
                                . ' Also, you can display one or more plans by using comma<b>(,)</b> separator to add another plan id in the shortcode.' , 'sumomemberships' ) , '<b>[sumo_members_based_on_plans plan_ids=" "]</b>' , '<b>Membership Plan ID</b>' , '<b>SUMO Memberships -> Membership Plans -> Membership Plan ID</b>' ) ,
                'type' => 'title' ,
                'id'   => 'sumomemberships_label2'
            ) ,
            array( 'type' => 'sectionend' , 'id' => 'sumomemberships_label' ) ,
                ) ) ;
    }

    /**
     * Registering Custom Field Admin Settings
     */
    public static function register_admin_settings() {


        woocommerce_admin_fields( SUMOGeneral_Settings_Tab::default_settings() ) ;
    }

    /**
     * Update the Settings on Save Changes
     */
    public static function advance_update_settings() {
        $sumomemberships_default_plans = isset( $_POST[ 'sumomemberships_default_plans' ] ) ? $_POST[ 'sumomemberships_default_plans' ] : array() ;
        update_option( 'sumomemberships_default_plans' , $sumomemberships_default_plans ) ;
        woocommerce_update_options( SUMOGeneral_Settings_Tab::default_settings() ) ;
                
        // Update flush option for my memberships menu.  
         update_option( 'sumo_flush_rewrite_rules' , 1 ) ;
    }

    /**
     * Initialize the Default Settings by looping this function
     */
    public static function load_default_settings() {
        global $woocommerce ;
        foreach ( SUMOGeneral_Settings_Tab::default_settings() as $setting )
            if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
            }
        add_option( 'sumomemberships_default_plans' , "" ) ;
    }

    public static function select_default_plans() {
        ?>
        <tr>
            <th>
                <?php echo __( 'Select Default Membership Plans' ) ; ?>
            </th>
            <td>
                <?php if ( ( float ) WC()->version < ( float ) '3.0.0' ) { ?>
                    <input type="text" name="sumomemberships_default_plans" id="sumomemberships_default_plans" style="width:320px;"/>
                    <?php
                } else {
                    $plan_ids = get_option( 'sumomemberships_default_plans' ) ;
                    if ( ! is_array( $plan_ids ) ) {
                        $plan_ids = explode( ',' , $plan_ids ) ;
                    }
                    $plan_ids = array_filter( $plan_ids ) ;
                    ?>
                    <select name="sumomemberships_default_plans[]" multiple="multiple" id="sumomemberships_default_plans" style="width:320px;">
                        <?php
                        foreach ( $plan_ids as $plan_id ) {
                            echo '<option value="' . $plan_id . '"' . selected( 1 , 1 ) . '>' . get_the_title( $plan_id ) . '</option>' ;
                        }
                        ?>
                    </select>
                <?php }
                ?>
            </td>
        </tr>
        <?php
    }

    public static function AJAX_search_plans() {
        $found_plans = array() ;

        $args = array(
            'offset'    => 0 ,
            'orderby'   => 'post_date' ,
            'post_type' => 'sumomembershipplans' ,
            's'         => $_REQUEST[ 'term' ] ,
                ) ;

        $search_results = get_posts( $args ) ;

        foreach ( $search_results as $plan ) {
            $plan_id                 = $plan->ID ;
            $plan_name               = $plan->post_title ;
            $found_plans[ $plan_id ] = $plan_name ;
        }
        wp_send_json( $found_plans ) ;
        exit() ;
    }

    public static function show_or_hide() {
        if ( isset( $_GET[ 'post_type' ] ) && isset( $_GET[ 'page' ] ) ) {
            if ( $_GET[ 'post_type' ] == 'sumomembershipplans' && $_GET[ 'page' ] == 'sumomemberships_settings' ) {

                $json_ids       = array() ;
                $selected_plans = get_option( 'sumomemberships_default_plans' ) ;

                if ( ! is_array( $selected_plans ) && $selected_plans != '' ) {

                    $get_plans = array_filter( array_map( 'absint' , explode( ',' , $selected_plans ) ) ) ;

                    foreach ( $get_plans as $plan_id ) {
                        $plan_name            = get_post_meta( $plan_id , 'sumomemberships_plan_name' , true ) ;
                        $json_ids[ $plan_id ] = $plan_name ;
                    }
                }
                ?>
                <script type="text/javascript">
                    jQuery( document ).ready( function () {

                <?php if ( ( float ) WC()->version <= ( float ) ('2.2.0') ) { ?>
                            jQuery( '#sumo_membership_plan_access_order_status' ).chosen() ;
                <?php } else { ?>
                            jQuery( '#sumo_membership_plan_access_order_status' ).select2() ;
                <?php } ?>

                        if ( jQuery( '#sumo_restriction_type' ).val() == '2' ) {
                            jQuery( '#sumo_hide_restricted_products_in_shop_page' ).parent().parent().parent().parent().hide() ;
                        } else {
                            jQuery( '#sumo_hide_restricted_products_in_shop_page' ).parent().parent().parent().parent().show() ;
                        }

                        jQuery( '#sumo_restriction_type' ).change( function () {
                            if ( this.value == '2' ) {
                                jQuery( '#sumo_hide_restricted_products_in_shop_page' ).parent().parent().parent().parent().hide() ;
                            } else {
                                jQuery( '#sumo_hide_restricted_products_in_shop_page' ).parent().parent().parent().parent().show() ;
                            }
                        } ) ;


                        if ( jQuery( '#sumo_restriction_type_for_content' ).val() == '2' ) {
                            jQuery( '#sumo_display_excerpt_content' ).closest( 'tr' ).hide() ;
                            jQuery( '#sumo_no_of_words_to_display' ).closest( 'tr' ).hide() ;
                            jQuery( '#sumo_hide_apply_job_button_for_job_listing_pt' ).closest( 'tr' ).hide() ;
                            jQuery( '#sumo_redirect_to' ).closest( 'tr' ).hide() ;
                            jQuery( '#sumo_hide_restricted_posts_and_pages_from_menu' ).closest( 'tr' ).show() ;
                        } else if ( jQuery( '#sumo_restriction_type_for_content' ).val() == '1' ) {
                            jQuery( '#sumo_display_excerpt_content' ).closest( 'tr' ).show() ;
                            jQuery( '#sumo_redirect_to' ).closest( 'tr' ).hide() ;
                            jQuery( '#sumo_hide_restricted_posts_and_pages_from_menu' ).closest( 'tr' ).show() ;
                            if ( jQuery( '#sumo_display_excerpt_content' ).is( ':checked' ) ) {
                                jQuery( '#sumo_no_of_words_to_display' ).closest( 'tr' ).show() ;
                            } else {
                                jQuery( '#sumo_no_of_words_to_display' ).closest( 'tr' ).hide() ;
                            }

                            jQuery( '#sumo_display_excerpt_content' ).change( function () {
                                if ( jQuery( '#sumo_display_excerpt_content' ).is( ':checked' ) ) {
                                    jQuery( '#sumo_no_of_words_to_display' ).closest( 'tr' ).show() ;
                                } else {
                                    jQuery( '#sumo_no_of_words_to_display' ).closest( 'tr' ).hide() ;
                                }
                            } ) ;
                            jQuery( '#sumo_hide_apply_job_button_for_job_listing_pt' ).closest( 'tr' ).show() ;
                        } else {
                            jQuery( '#sumo_display_excerpt_content' ).closest( 'tr' ).hide() ;
                            jQuery( '#sumo_no_of_words_to_display' ).closest( 'tr' ).hide() ;
                            jQuery( '#sumo_redirect_to' ).closest( 'tr' ).show() ;
                            jQuery( '#sumo_hide_restricted_posts_and_pages_from_menu' ).closest( 'tr' ).show() ;
                            jQuery( '#sumo_hide_apply_job_button_for_job_listing_pt' ).closest( 'tr' ).hide() ;
                        }

                        jQuery( '#sumo_restriction_type_for_content' ).change( function () {
                            if ( jQuery( '#sumo_restriction_type_for_content' ).val() == '2' ) {
                                jQuery( '#sumo_display_excerpt_content' ).closest( 'tr' ).hide() ;
                                jQuery( '#sumo_no_of_words_to_display' ).closest( 'tr' ).hide() ;
                                jQuery( '#sumo_redirect_to' ).closest( 'tr' ).hide() ;
                                jQuery( '#sumo_hide_restricted_posts_and_pages_from_menu' ).closest( 'tr' ).show() ;
                                jQuery( '#sumo_hide_apply_job_button_for_job_listing_pt' ).closest( 'tr' ).hide() ;
                            } else if ( jQuery( '#sumo_restriction_type_for_content' ).val() == '1' ) {
                                jQuery( '#sumo_display_excerpt_content' ).closest( 'tr' ).show() ;
                                jQuery( '#sumo_redirect_to' ).closest( 'tr' ).hide() ;
                                jQuery( '#sumo_hide_restricted_posts_and_pages_from_menu' ).closest( 'tr' ).show() ;
                                if ( jQuery( '#sumo_display_excerpt_content' ).is( ':checked' ) ) {
                                    jQuery( '#sumo_no_of_words_to_display' ).closest( 'tr' ).show() ;
                                } else {
                                    jQuery( '#sumo_no_of_words_to_display' ).closest( 'tr' ).hide() ;
                                }

                                jQuery( '#sumo_display_excerpt_content' ).change( function () {
                                    if ( jQuery( '#sumo_display_excerpt_content' ).is( ':checked' ) ) {
                                        jQuery( '#sumo_no_of_words_to_display' ).closest( 'tr' ).show() ;
                                    } else {
                                        jQuery( '#sumo_no_of_words_to_display' ).closest( 'tr' ).hide() ;
                                    }
                                } ) ;
                                jQuery( '#sumo_hide_apply_job_button_for_job_listing_pt' ).closest( 'tr' ).show() ;
                            } else {
                                jQuery( '#sumo_display_excerpt_content' ).closest( 'tr' ).hide() ;
                                jQuery( '#sumo_no_of_words_to_display' ).closest( 'tr' ).hide() ;
                                jQuery( '#sumo_redirect_to' ).closest( 'tr' ).show() ;
                                jQuery( '#sumo_hide_restricted_posts_and_pages_from_menu' ).closest( 'tr' ).show() ;
                                jQuery( '#sumo_hide_apply_job_button_for_job_listing_pt' ).closest( 'tr' ).hide() ;
                            }
                        } ) ;



                        jQuery( "#mainform" ).submit( function () {

                            if ( jQuery( "#sumo_restriction_type_for_content" ).val() == '3' ) {

                                if ( jQuery( "#sumo_redirect_to" ).val() == '' ) {
                                    alert( 'Please enter the URL. And Try Again.' ) ;
                                    return false ;
                                } else if ( !isUrlValid( jQuery( "#sumo_redirect_to" ).val() ) ) {
                                    alert( 'Please enter the valid URL. And Try Again.' ) ;
                                    return false ;
                                }
                            }
                        } ) ;
                <?php if ( ( float ) WC()->version < ( float ) '3.0.0' ) { ?>
                            jQuery( "#sumomemberships_default_plans" ).select2( {
                                placeholder : "Enter atleast 3 characters" ,
                                allowClear : true ,
                                enable : false ,
                                readonly : false ,
                                initSelection : function ( data , callback ) {
                                    var newjson = '<?php echo json_encode( $json_ids ) ; ?>' ;
                                    newjson = JSON.parse( newjson )
                                    var data_show = [ ] ;
                                    jQuery.each( newjson , function ( index , item ) {

                                        data_show.push( { id : index , text : item } ) ;

                                    } ) ;
                                    callback( data_show ) ;
                                } ,
                                multiple : false ,
                                minimumInputLength : 3 ,
                                tags : [ ] ,
                                ajax : {
                                    url : '<?php echo admin_url( 'admin-ajax.php' ) ; ?>' ,
                                    dataType : 'json' ,
                                    type : "GET" ,
                                    quietMillis : 250 ,
                                    data : function ( term ) {
                                        return {
                                            term : term ,
                                            action : "sumo_search_membership_plans" ,
                                        } ;
                                    } ,
                                    results : function ( data ) {
                                        var terms = [ ] ;
                                        if ( data ) {
                                            jQuery.each( data , function ( id , text ) {
                                                terms.push( {
                                                    id : id ,
                                                    text : text
                                                } ) ;
                                            } ) ;
                                        }
                                        return { results : terms } ;
                                    } ,
                                } ,
                            } ).select2( 'val' , '1' ) ;
                <?php } else {
                    ?>
                            jQuery( "#sumomemberships_default_plans" ).select2( {
                                placeholder : "Enter atleast 3 characters" ,
                                allowClear : true ,
                                //                                    enable: false,
                                //                                    maximumSelectionSize: 1,
                                //                                    readonly: false,
                                //                                    multiple: false,
                                minimumInputLength : 3 ,
                                //                                    tags: [],
                                escapeMarkup : function ( m ) {
                                    return m ;
                                } ,
                                ajax : {
                                    url : '<?php echo admin_url( 'admin-ajax.php' ) ; ?>' ,
                                    dataType : 'json' ,
                                    quietMillis : 250 ,
                                    data : function ( params ) {
                                        return {
                                            term : params.term ,
                                            action : 'sumo_search_membership_plans'
                                        } ;
                                    } ,
                                    processResults : function ( data ) {
                                        var terms = [ ] ;
                                        if ( data ) {
                                            jQuery.each( data , function ( id , text ) {
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


                        function isUrlValid( url ) {
                            var res = url.match( /(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g ) ;
                            if ( res == null ) {
                                return false ;
                            } else {
                                return true ;
                            }
                        }

                    } ) ;
                </script>
                <?php
            }
        }
    }

}

new SUMOGeneral_Settings_Tab() ;

function sumomembership_general_settings() {
    foreach ( SUMOGeneral_Settings_Tab::default_settings() as $setting ) {
        if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
            delete_option( $setting[ 'newids' ] ) ;
            add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
        }
    }
    delete_option( 'sumomemberships_default_plans' ) ;
    add_option( 'sumomemberships_default_plans' , '' ) ;
}
