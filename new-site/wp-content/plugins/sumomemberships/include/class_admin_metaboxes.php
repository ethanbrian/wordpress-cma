<?php

class SUMOMemberships_Admin_Meta_Boxes {

    public function __construct() {

        add_action( 'add_meta_boxes' , array( $this , 'admin_meta_boxes' ) ) ;
        add_action( 'save_post' , array( $this , 'save_metabox_values_on_submit' ) , 10 , 3 ) ;
        add_action( 'woocommerce_product_duplicate' , array( $this , 'duplicate_action_save_meta' ) , 10 , 2 ) ;
        add_action( 'post_updated_messages' , array( $this , 'display_admin_post_messages' ) ) ;
        add_action( 'admin_head' , array( $this , 'hide_move_to_trash' ) ) ;

        add_filter( 'redirect_post_location' , array( $this , 'redirect_post_location' ) , 10 , 2 ) ;
        add_action( 'restrict_manage_posts' , array( $this , 'add_filters_to_members' ) ) ;
        add_action( 'posts_where' , array( $this , 'pre_get_posts_sorting' ) , 10 , 2 ) ;
        add_action( 'edit_form_after_title' , array( $this , 'select_user_to_add_member' ) ) ;

        add_action( 'wp_ajax_sumo_json_search_products_and_variations' , array( $this , 'AJAX_search_products' ) ) ;
        add_action( 'wp_ajax_sumo_choose_user_to_add_as_member' , array( $this , 'AJAX_add_members_user_image' ) ) ;
        add_action( 'wp_ajax_sumo_save_membership_plan_action' , array( $this , 'AJAX_save_membership_plan_action' ) ) ;
        add_action( 'wp_ajax_sumo_update_plan_linking_table_row_count_on_click' , array( $this , 'AJAX_update_table_row_count_on_click' ) ) ;
        add_action( 'wp_ajax_sumo_checking_table_with_r_without_plan' , array( $this , 'AJAX_check_table_with_r_without_plan' ) ) ;
        add_action( 'wp_ajax_sumo_delete_current_plan_linking_table_row' , array( $this , 'AJAX_delete_current_table_row' ) ) ;
        add_action( 'wp_ajax_sumo_delete_membership_plan' , array( $this , 'AJAX_delete_membership_plan' ) ) ;
        add_action( 'wp_ajax_sumo_transfer_membership_plan' , array( $this , 'AJAX_transfer_membership_plan' ) ) ;
        add_action( 'wp_ajax_sumo_link_membership_plan' , array( $this , 'AJAX_link_membership_plan' ) ) ;
        add_action( 'wp_ajax_sumo_unlink_membership_plan' , array( $this , 'AJAX_unlink_membership_plan' ) ) ;
        add_action( 'wp_ajax_sumo_add_notes_for_members' , array( $this , 'AJAX_add_notes_manually' ) ) ;
        add_action( 'wp_ajax_sumo_delete_members_note' , array( $this , 'AJAX_delete_members_note' ) ) ;

        add_action( 'admin_enqueue_scripts' , array( $this , 'enqueue_script_for_alert_backend' ) ) ;
        add_action( 'wp_enqueue_scripts' , array( $this , 'enqueue_script_for_alert_frontend' ) ) ;
        add_action( 'admin_head' , array( $this , 'add_manual_notes' ) ) ;

        add_action( 'sumomemberships_before_saving_members_plan_data' , array( $this , 'do_some_action_on_manual_updations' ) , 10 , 2 ) ;
// add capabilities for manual plan updation.
        add_action( 'sumomemberships_plan_saved' , array( $this , 'add_capabilities_to_member_for_manual_updation' ) , 999 , 2 ) ;

//For Plan ID updation
        add_action( 'sumomemberships_manual_plan_updation' , array( $this , 'do_some_action_on_manual_plan_update' ) , 10 , 4 ) ;
//For plan status updation
        add_action( 'sumomemberships_manual_plan_status_updation' , array( $this , 'do_some_action_on_manual_plan_status_update' ) , 10 , 4 ) ;
//For plan expiry updation
        add_action( 'sumomemberships_manual_plan_expiry_date_updation' , array( $this , 'do_some_action_on_manual_plan_expiry_date_update' ) , 10 , 4 ) ;
        add_action( 'sumomemberships_delete_plan' , array( $this , 'clear_automatic_process_cron_events_on_manual_deletion' ) , 10 , 3 ) ;

        add_action( 'manage_posts_extra_tablenav' , array( $this , 'sumo_memberships_manage_posts_extra_table' ) ) ;
        add_action( 'admin_head' , array( $this , 'sumo_member_emails_export_csv' ) ) ;

        add_action( 'sm_save_values_on_ppm' , array( $this , 'sm_save_values_on_ppm_action' ) , 10 , 1 ) ;
    }

    public function admin_meta_boxes() {
        remove_meta_box( 'slugdiv' , 'sumomembershipplans' , 'normal' ) ;
        remove_meta_box( 'submitdiv' , 'sumomembers' , 'side' ) ;
        remove_meta_box( 'commentsdiv' , 'sumomembers' , 'normal' ) ;

        add_meta_box( 'sumomemberships_plan_action' , 'Plan Action' , array( $this , 'output_plan_action' ) , 'sumomembershipplans' , 'side' , 'low' ) ;
        add_meta_box( 'sumomemberships_general' , 'General' , array( $this , 'output_general' ) , 'sumomembershipplans' , 'normal' , 'high' ) ;
        add_meta_box( 'sumomemberships_link_additional_plans' , 'Link Additional Plans' , array( $this , 'output_link_additional_plans' ) , 'sumomembershipplans' , 'normal' , 'low' ) ;
        add_meta_box( 'sm_link_additional_plans_filter' , 'Filter for Linking Additional Plans' , array( $this , 'output_link_additional_plans_filter' ) , 'sumomembershipplans' , 'normal' , 'low' ) ;

        add_meta_box( 'sumomemberships_user_image' , 'Member Information' , array( $this , 'output_to_add_member' ) , 'sumomembers' , 'side' , 'high' ) ;
        add_meta_box( 'sumomemberships_create_member_plan' , 'Members Plan Details' , array( $this , 'output_to_add_plan' ) , 'sumomembers' , 'normal' , 'high' ) ;
        add_meta_box( 'sumomemberships_members_notes' , 'Activity Log' , array( $this , 'output_membership_notes' ) , 'sumomembers' , 'side' , 'default' ) ;

//Provide Restriction for Default Types
        $post_types = get_post_types() ;

        foreach( $post_types as $type ) {

            if( $type == 'post' || $type == 'product' || $type == 'page' ) {
                add_meta_box( 'sumomemberships_' . $type . '_settings' , 'SUMO Memberships ' . ucfirst( $type ) . ' Settings' , array( $this , 'output_restrictions_settings' ) , $type , 'normal' , 'low' ) ;
            }
        }
//Provide Restriction for Third Parties Custom Post Types
        $post_types = sumo_get_available_cpt_to_restrict() ;

        foreach( $post_types as $type ) {

            add_meta_box( 'sumomemberships_' . $type . '_settings' , 'SUMO Memberships ' . ucfirst( $type ) . ' Settings' , array( $this , 'output_restrictions_settings' ) , $type , 'normal' , 'low' ) ;
        }
    }

    public function enqueue_script_for_alert_backend() {
        $screen    = get_current_screen() ;
        $screen_id = $screen ? $screen->id : '' ;
        if( $screen_id == 'sumomembers' ) {
            self::sm_common_function_enqueue() ;
        }
    }

    public function enqueue_script_for_alert_frontend() {
        self::sm_common_function_enqueue() ;
    }

    public static function sm_common_function_enqueue() {
         $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' ;
        wp_enqueue_script( 'sweetalert2' , plugins_url( 'sumomemberships/assets/sweetalert2/sweetalert2.min.js') ) ;
        wp_enqueue_style( 'sweetalert2' , plugins_url( 'sumomemberships/assets/sweetalert2/sweetalert2'.$suffix.'.css' ) ) ;
    }

    public function display_admin_post_messages( $messages ) {
        $messages[ 'sumomembershipplans' ] = array(
            0  => '' , // Unused. Messages start at index 1.
            1  => sprintf( __( 'Plan updated successfully.' ) ) ,
            2  => __( 'Custom field updated.' , 'sumomemberships' ) ,
            3  => __( 'Custom field deleted.' , 'sumomemberships' ) ,
            4  => __( 'Plan updated successfully.' , 'sumomemberships' ) ,
            5  => '' ,
            6  => sprintf( __( 'Plan created successfully.' , 'sumomemberships' ) ) ,
            7  => __( 'Plan saved.' , 'sumomemberships' ) ,
            8  => sprintf( __( 'Plan submitted.' , 'sumomemberships' ) ) ,
            9  => '' ,
            10 => sprintf( __( 'Plan draft updated.' , 'sumomemberships' ) ) ,
                ) ;
        return $messages ;
    }

    public function hide_move_to_trash() {
        if( isset( $_GET[ 'post' ] ) ) {

            $plan_id                   = $_GET[ 'post' ] ;
            $is_plan_active_for_member = sumo_get_plan_purchased_members( $plan_id , true ) > 0 ? true : false ;

            if( $is_plan_active_for_member ) {
                echo '<style>.submitdelete{display:none;}</style>' ;
            }
        }
    }

    public function output_plan_action() {
        global $post ;
        $this_postid = $post->ID ;
        ?>
        <select name="sumomemberships_plan_status" id="sumomemberships_plan_status" style="width:50%">
            <?php if( sumo_is_global_plan_status_active( $this_postid ) ) {
                ?>
                <option value="disable"><?php _e( 'Disable' , 'sumomemberships' ) ; ?></option>
                <?php
            } else {
                ?>
                <option value="enable"><?php _e( 'Enable' , 'sumomemberships' ) ; ?></option>
            <?php } ?>
        </select>
        &nbsp;&nbsp;&nbsp;
        <input type="button" class="button-primary sumomemberships_save_plan_action" value="Save">
        <img src="<?php echo SUMO_MEMBERSHIPS_PLUGIN_URL . '/assets/images/loader.gif' ; ?>" id="sumomemberships_load_on_save" style="display:none;width:12%;"/>

        <script type="text/javascript">
            jQuery( document ).ready( function() {
                jQuery( ".sumomemberships_save_plan_action" ).click( function( event ) {

                    event.preventDefault() ;

                    jQuery( '#sumomemberships_load_on_save' ).show() ;

                    var action_to_take = jQuery( '#sumomemberships_plan_status' ).val() ;
                    var data = {
                        action : 'sumo_save_membership_plan_action' ,
                        this_postid : "<?php echo $this_postid ; ?>" ,
                        action_to_take : action_to_take
                    } ;

                    jQuery.post( "<?php echo admin_url( 'admin-ajax.php' ) ; ?>" , data , function( response ) {
                        console.log( response ) ;

                        if( response == "success" ) {
                            jQuery( '#sumomemberships_load_on_save' ).hide() ;

                            if( jQuery( '#sumomemberships_plan_status' ).val() == "enable" ) {
                                var o = new Option( "Disable" , "disable" ) ;
                                jQuery( o ).html( "Disable" ) ;
                                jQuery( "#sumomemberships_plan_status" ).append( o ) ;
                                jQuery( "#sumomemberships_plan_status option[value='enable']" ).remove() ;
                            } else {
                                var o = new Option( "Enable" , "enable" ) ;
                                jQuery( o ).html( "Enable" ) ;
                                jQuery( "#sumomemberships_plan_status" ).append( o ) ;
                                jQuery( "#sumomemberships_plan_status option[value='disable']" ).remove() ;
                            }
                        }
                    } ) ;
                } ) ;
            } ) ;
        </script>
        <?php
    }

    public function output_general() {
        global $post ;
        $this_postid = $post->ID ;
        ?>
        <table class="form-table">
            <tr>
                <th>
                    <?php _e( 'Purchasing the following Product will provide access to this plan :' , 'sumomemberships' ) ; ?>
                </th>
                <td>
                    <?php if( WC()->version < 3.0 ) { ?>
                        <input type="hidden" class="wc-product-search" style="width: 350px;" id="sumomemberships_plan_associated_product" name="sumomemberships_plan_associated_product" data-placeholder="<?php _e( 'Search for a product&hellip;' , 'sumomemberships' ) ; ?>" data-action="sumo_json_search_products_and_variations" data-multiple="false"
                               data-selected="<?php
                               $product_name = '' ;
                               $product_id   = get_post_meta( $this_postid , 'sumomemberships_plan_associated_product' , true ) ;

                               $product = sumo_get_product( $product_id ) ;

                               if( is_object( $product ) ) {

                                   $product_name = wp_kses_post( $product->get_formatted_name() ) ;

                                   echo esc_attr( $product_name ) ;
                               }
                               ?>" value="<?php echo esc_attr( $product_name ) ; ?>">
                           <?php } else {
                               ?>
                        <select class="wc-product-search" style="width: 350px;" name="sumomemberships_plan_associated_product" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;' , 'woocommerce' ) ; ?>" data-action="sumo_json_search_products_and_variations">
                            <?php
                            $product_ids = get_post_meta( $this_postid , 'sumomemberships_plan_associated_product' , true ) ;
                            if( ! is_array( $product_ids ) ) {
                                $product_ids = $product_ids ? explode( ',' , $product_ids ) : array() ;
                            }
                            foreach( $product_ids as $product_id ) {
                                $product = wc_get_product( $product_id ) ;
                                if( is_object( $product ) ) {
                                    echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true , true , false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>' ;
                                }
                            }
                            ?>
                        </select>
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <th>
                    <?php _e( 'Membership Duration Type :' , 'sumomemberships' ) ; ?>
                </th>
                <td>
                    <select name="sumomemberships_duration_type" id="sumomemberships_duration_type">
                        <option value="unlimited_duration"
                                <?php if( get_post_meta( $this_postid , 'sumomemberships_duration_type' , true ) == 'unlimited_duration' ) { ?> selected="selected" <?php } ?>>
                                    <?php _e( 'Unlimited Duration' , 'sumomemberships' ) ; ?>
                        </option>
                        <option value="limited_duration"
                                <?php if( get_post_meta( $this_postid , 'sumomemberships_duration_type' , true ) == 'limited_duration' ) { ?> selected="selected" <?php } ?>>
                                    <?php _e( 'Limited Duration' , 'sumomemberships' ) ; ?>
                        </option>
                    </select>

                    <input type="number" min="1" name="sumomemberships_duration_value" id="sumomemberships_duration_value" value="<?php echo get_post_meta( $this_postid , 'sumomemberships_duration_value' , true ) ; ?>">

                    <select id="sumomemberships_duration_period" name="sumomemberships_duration_period">
                        <option value="days"
                                <?php if( get_post_meta( $this_postid , 'sumomemberships_duration_period' , true ) == 'day' || get_post_meta( $this_postid , 'sumomemberships_duration_period' , true ) == 'days' ) { ?> selected="selected" <?php } ?>>
                                    <?php get_post_meta( $this_postid , 'sumomemberships_duration_period' , true ) == 'day' ? _e( 'Day' , 'sumomemberships' ) : _e( 'Days' , 'sumomemberships' ) ; ?>
                        </option>
                        <option value="weeks"
                                <?php if( get_post_meta( $this_postid , 'sumomemberships_duration_period' , true ) == 'week' || get_post_meta( $this_postid , 'sumomemberships_duration_period' , true ) == 'weeks' ) { ?> selected="selected" <?php } ?>>
                                    <?php get_post_meta( $this_postid , 'sumomemberships_duration_period' , true ) == 'week' ? _e( 'Week' , 'sumomemberships' ) : _e( 'Weeks' , 'sumomemberships' ) ; ?>
                        </option>
                        <option value="months"
                                <?php if( get_post_meta( $this_postid , 'sumomemberships_duration_period' , true ) == 'month' || get_post_meta( $this_postid , 'sumomemberships_duration_period' , true ) == 'months' ) { ?> selected="selected" <?php } ?>>
                                    <?php get_post_meta( $this_postid , 'sumomemberships_duration_period' , true ) == 'month' ? _e( 'Month' , 'sumomemberships' ) : _e( 'Months' , 'sumomemberships' ) ; ?>
                        </option>
                        <option value="years"
                                <?php if( get_post_meta( $this_postid , 'sumomemberships_duration_period' , true ) == 'year' || get_post_meta( $this_postid , 'sumomemberships_duration_period' , true ) == 'years' ) { ?> selected="selected" <?php } ?>>
                                    <?php get_post_meta( $this_postid , 'sumomemberships_duration_period' , true ) == 'year' ? _e( 'Year' , 'sumomemberships' ) : _e( 'Years' , 'sumomemberships' ) ; ?>
                        </option>
                    </select>
                    <?php
                    if( class_exists( 'SUMOSubscriptions' ) ) {
                        echo '<br>(Will not be considered, if linked with a Subscription Product created using SUMO Subscriptions)' ;
                    }
                    ?>
                </td>
            </tr>
        </table>

        <script type="text/javascript">
            jQuery( document ).ready( function() {

                if( jQuery( "select[name=sumomemberships_duration_type]" ).val() == "limited_duration" ) {
                    jQuery( "#sumomemberships_duration_value" ).show() ;
                    jQuery( "#sumomemberships_duration_period" ).show() ;
                } else {
                    jQuery( "#sumomemberships_duration_value" ).hide() ;
                    jQuery( "#sumomemberships_duration_period" ).hide() ;
                }

                jQuery( "select[name='sumomemberships_duration_type']" ).change( function() {

                    if( this.value == 'limited_duration' ) {
                        jQuery( "#sumomemberships_duration_value" ).show() ;
                        jQuery( "#sumomemberships_duration_period" ).show() ;
                    } else {
                        jQuery( "#sumomemberships_duration_value" ).hide() ;
                        jQuery( "#sumomemberships_duration_period" ).hide() ;
                    }
                } ) ;

                jQuery( "#post" ).submit( function() {

                    if( jQuery( "#title" ).val() == '' ) {
                        alert( 'Please enter the Plan Name. And Try Again.' ) ;
                        return false ;
                    }
                } ) ;
            } ) ;
        </script>
        <?php
    }

    public function output_link_additional_plans() {
        global $post ;
        $this_postid = $post->ID ;

        $meta_key = array(
            'table_name'        => 'sumo_link_additional_plans' ,
            'no_rules_added'    => 'sumo_no_plan_links_added' ,
            'add_rule'          => 'sumo_add_plan_to_link' ,
            'remove_rule'       => 'sumo_remove_linking_plan' ,
            'no_of_rules_added' => 'sumomemberships_no_of_links_added' ,
            'membership_plan'   => 'sumomemberships_plan_to_link_with' ,
            'schedule_type'     => 'sumomemberships_linking_plan_schedule_type' ,
            'duration_value'    => 'sumomemberships_linking_plan_duration_value' ,
            'duration_period'   => 'sumomemberships_linking_plan_duration_period'
                ) ;

        echo $this->display_add_rule_table( $this_postid , $meta_key ) ;
    }

    public function output_link_additional_plans_filter() {
        global $post ;
        $this_postid                             = $post->ID ;
        $user_purchase_history_for_linking_plans = get_post_meta( $this_postid , 'user_purchase_history_for_linking_plans' , true ) ;
        $sm_user_purchase_history_period         = get_post_meta( $this_postid , 'sm_user_purchase_history_period' , true ) ;
        $sm_uph_from_period                      = get_post_meta( $this_postid , 'sm_uph_from_period' , true ) ;
        $sm_uph_to_period                        = get_post_meta( $this_postid , 'sm_uph_to_period' , true ) ;
        $sm_no_of_orders_placed                  = get_post_meta( $this_postid , 'sm_no_of_orders_placed' , true ) ;
        $sm_total_amount_spent_in_site           = get_post_meta( $this_postid , 'sm_total_amount_spent_in_site' , true ) ;
        ?>
        <table>
            <tr>
                <th>
                    <?php echo __( 'User Purchase History' , 'sumomemberships' ) ?>
                </th>
                <td>
                    <select  class="" id="user_purchase_history_for_linking_plans" name="user_purchase_history_for_linking_plans">
                        <option value="" <?php echo selected( '' , $user_purchase_history_for_linking_plans ) ; ?>><?php echo __( 'None' , 'sumomemberships' ) ; ?></option>
                        <option value="1" <?php echo selected( '1' , $user_purchase_history_for_linking_plans ) ; ?>><?php echo __( 'Minimum Number of Successful Orders' , 'sumomemberships' ) ; ?></option>
                        <option value="2" <?php echo selected( '2' , $user_purchase_history_for_linking_plans ) ; ?>><?php echo __( 'Minimum Amount spent on Site' , 'sumomemberships' ) ; ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th>
                    <?php echo __( 'User Purchase History Period' , 'sumomemberships' ) ?>
                </th>
                <td>
                    <select  class="" id="sm_user_purchase_history_period" name="sm_user_purchase_history_period">
                        <option value="" <?php echo selected( '' , $sm_user_purchase_history_period ) ; ?>><?php echo __( 'From Beginning' , 'sumomemberships' ) ; ?></option>
                        <option value="1" <?php echo selected( '1' , $sm_user_purchase_history_period ) ; ?>><?php echo __( 'Specific Period' , 'sumomemberships' ) ; ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th>
                    <?php echo __( 'From' , 'sumomemberships' ) ?>
                </th>
                <td>
                    <input type="text" placeholder="yy-mm-dd" class="sm__date" name="sm_uph_from_period"  value="<?php echo $sm_uph_from_period ?>" id="sm_uph_from_period"/>
                </td>
            </tr>
            <tr>
                <th>
                    <?php echo __( 'To' , 'sumomemberships' ) ?>
                </th>
                <td>
                    <input type="text" placeholder="yy-mm-dd" class="sm__date" name="sm_uph_to_period"  value="<?php echo $sm_uph_to_period ?>" id="sm_uph_to_period"/>
                </td>
            </tr>
            <tr>
                <th>
                    <?php echo __( 'No. of Orders Placed' , 'sumomemberships' ) ?>
                </th>
                <td>
                    <input type="number" step="1" name="sm_no_of_orders_placed"  value="<?php echo $sm_no_of_orders_placed ?>" id="sm_no_of_orders_placed"/>
                </td>
            </tr>
            <tr>
                <th>
                    <?php echo __( 'Total Amount Spent ' , 'sumomemberships' ) . get_woocommerce_currency_symbol() ?>
                </th>
                <td>
                    <input type="number" step="any" name="sm_total_amount_spent_in_site"  value="<?php echo $sm_total_amount_spent_in_site ?>" id="sm_total_amount_spent_in_site"/>
                </td>
            </tr>
        </table>
        <script type="text/javascript">
            jQuery( 'document' ).ready( function() {
                jQuery( '.sm__date' ).datepicker( {
                    dateFormat : "yy-mm-dd" ,
                    maxDate : new Date()

                } ) ;
                if( jQuery( '#user_purchase_history_for_linking_plans' ).val() == '' ) {
                    jQuery( "#sm_no_of_orders_placed" ).closest( 'tr' ).hide() ;
                    jQuery( "#sm_total_amount_spent_in_site" ).closest( 'tr' ).hide() ;
                    jQuery( '#sm_user_purchase_history_period' ).closest( 'tr' ).hide() ;
                    jQuery( '#sm_uph_from_period' ).closest( 'tr' ).hide() ;
                    jQuery( '#sm_uph_to_period' ).closest( 'tr' ).hide() ;
                } else if( jQuery( '#user_purchase_history_for_linking_plans' ).val() == '1' ) {
                    jQuery( "#sm_no_of_orders_placed" ).closest( 'tr' ).show() ;
                    jQuery( "#sm_total_amount_spent_in_site" ).closest( 'tr' ).hide() ;
                    jQuery( '#sm_user_purchase_history_period' ).closest( 'tr' ).show() ;
                    if( jQuery( '#sm_user_purchase_history_period' ).val() == '' ) {
                        jQuery( '#sm_uph_from_period' ).closest( 'tr' ).hide() ;
                        jQuery( '#sm_uph_to_period' ).closest( 'tr' ).hide() ;
                    } else {
                        jQuery( '#sm_uph_from_period' ).closest( 'tr' ).show() ;
                        jQuery( '#sm_uph_to_period' ).closest( 'tr' ).show() ;
                    }
                    jQuery( '#sm_user_purchase_history_period' ).change( function() {
                        if( jQuery( this ).val() == '' ) {
                            jQuery( '#sm_uph_from_period' ).closest( 'tr' ).hide() ;
                            jQuery( '#sm_uph_to_period' ).closest( 'tr' ).hide() ;
                        } else {
                            jQuery( '#sm_uph_from_period' ).closest( 'tr' ).show() ;
                            jQuery( '#sm_uph_to_period' ).closest( 'tr' ).show() ;
                        }
                    } ) ;
                } else if( jQuery( '#user_purchase_history_for_linking_plans' ).val() == '2' ) {
                    jQuery( "#sm_no_of_orders_placed" ).closest( 'tr' ).hide() ;
                    jQuery( "#sm_total_amount_spent_in_site" ).closest( 'tr' ).show() ;
                    jQuery( '#sm_user_purchase_history_period' ).closest( 'tr' ).show() ;
                    if( jQuery( '#sm_user_purchase_history_period' ).val() == '' ) {
                        jQuery( '#sm_uph_from_period' ).closest( 'tr' ).hide() ;
                        jQuery( '#sm_uph_to_period' ).closest( 'tr' ).hide() ;
                    } else {
                        jQuery( '#sm_uph_from_period' ).closest( 'tr' ).show() ;
                        jQuery( '#sm_uph_to_period' ).closest( 'tr' ).show() ;
                    }
                    jQuery( '#sm_user_purchase_history_period' ).change( function() {
                        if( jQuery( this ).val() == '' ) {
                            jQuery( '#sm_uph_from_period' ).closest( 'tr' ).hide() ;
                            jQuery( '#sm_uph_to_period' ).closest( 'tr' ).hide() ;
                        } else {
                            jQuery( '#sm_uph_from_period' ).closest( 'tr' ).show() ;
                            jQuery( '#sm_uph_to_period' ).closest( 'tr' ).show() ;
                        }
                    } ) ;
                }
                jQuery( '#user_purchase_history_for_linking_plans' ).change( function() {
                    if( jQuery( this ).val() == '' ) {
                        jQuery( "#sm_no_of_orders_placed" ).closest( 'tr' ).hide() ;
                        jQuery( "#sm_total_amount_spent_in_site" ).closest( 'tr' ).hide() ;
                        jQuery( '#sm_user_purchase_history_period' ).closest( 'tr' ).hide() ;
                        jQuery( '#sm_uph_from_period' ).closest( 'tr' ).hide() ;
                        jQuery( '#sm_uph_to_period' ).closest( 'tr' ).hide() ;
                    } else if( jQuery( this ).val() == '1' ) {
                        jQuery( "#sm_no_of_orders_placed" ).closest( 'tr' ).show() ;
                        jQuery( "#sm_total_amount_spent_in_site" ).closest( 'tr' ).hide() ;
                        jQuery( '#sm_user_purchase_history_period' ).closest( 'tr' ).show() ;
                        if( jQuery( '#sm_user_purchase_history_period' ).val() == '' ) {
                            jQuery( '#sm_uph_from_period' ).closest( 'tr' ).hide() ;
                            jQuery( '#sm_uph_to_period' ).closest( 'tr' ).hide() ;
                        } else {
                            jQuery( '#sm_uph_from_period' ).closest( 'tr' ).show() ;
                            jQuery( '#sm_uph_to_period' ).closest( 'tr' ).show() ;
                        }
                        jQuery( '#sm_user_purchase_history_period' ).change( function() {
                            if( jQuery( this ).val() == '' ) {
                                jQuery( '#sm_uph_from_period' ).closest( 'tr' ).hide() ;
                                jQuery( '#sm_uph_to_period' ).closest( 'tr' ).hide() ;
                            } else {
                                jQuery( '#sm_uph_from_period' ).closest( 'tr' ).show() ;
                                jQuery( '#sm_uph_to_period' ).closest( 'tr' ).show() ;
                            }
                        } ) ;
                    } else if( jQuery( this ).val() == '2' ) {
                        jQuery( "#sm_no_of_orders_placed" ).closest( 'tr' ).hide() ;
                        jQuery( "#sm_total_amount_spent_in_site" ).closest( 'tr' ).show() ;
                        jQuery( '#sm_user_purchase_history_period' ).closest( 'tr' ).show() ;
                        if( jQuery( '#sm_user_purchase_history_period' ).val() == '' ) {
                            jQuery( '#sm_uph_from_period' ).closest( 'tr' ).hide() ;
                            jQuery( '#sm_uph_to_period' ).closest( 'tr' ).hide() ;
                        } else {
                            jQuery( '#sm_uph_from_period' ).closest( 'tr' ).show() ;
                            jQuery( '#sm_uph_to_period' ).closest( 'tr' ).show() ;
                        }
                        jQuery( '#sm_user_purchase_history_period' ).change( function() {
                            if( jQuery( this ).val() == '' ) {
                                jQuery( '#sm_uph_from_period' ).closest( 'tr' ).hide() ;
                                jQuery( '#sm_uph_to_period' ).closest( 'tr' ).hide() ;
                            } else {
                                jQuery( '#sm_uph_from_period' ).closest( 'tr' ).show() ;
                                jQuery( '#sm_uph_to_period' ).closest( 'tr' ).show() ;
                            }
                        } ) ;
                    }
                } ) ;
            } ) ;
        </script>
        <?php
    }

    public function output_to_add_member() {
        global $post ;
        $default   = '' ;
        $args      = '' ;
        $username  = '' ;
        $useremail = '' ;
        $user_id   = get_post_meta( $post->ID , 'sumomemberships_userid' , true ) ;
        if( $user_id != '' ) {
            $user_info = get_userdata( $user_id ) ;
            $username  = $user_info->user_login ;
            $useremail = $user_info->user_email ;
        }
        $saved_plans = get_post_meta( $post->ID , 'sumomemberships_saved_plans' , true ) ;

        if( $user_id != '' && $username != '' ) {
            ?>
            <div class="sumomemberships_member_information">
                <?php
                echo get_avatar( $user_id , 250 , $default , $username , $args ) ;
                ?>
                <h1><?php echo $username ; ?></h1>
                <address><?php echo $useremail ; ?></address>
            </div>
        <?php } ?>
        <script type="text/javascript">
            jQuery( document ).ready( function() {
                jQuery( '#sumomemberships_add_user_as_member' ).click( function() {
                    var userid = jQuery( '#sumomemberships_select_user_to_add_member' ).val() ;
                    if( userid != '' ) {
                        var dataparam = ( {
                            action : 'sumo_choose_user_to_add_as_member' ,
                            userid : userid ,
                            postid :<?php echo $post->ID ; ?> ,
                        } ) ;
                        jQuery.post( "<?php echo admin_url( 'admin-ajax.php' ) ; ?>" , dataparam ,
                                function( response ) {
                                    window.location.replace( response ) ;
                                } ) ;
                    } else {
                        sweetAlert( '' , 'Please Choose any Member.' , 'error' ) ;
                        return false ;
                    }
                } ) ;
        <?php if( $user_id == '' ) { ?>
                    jQuery( '.sumomemberships_save_plan_button' ).click( function() {
                        sweetAlert( '' , 'Please Choose any Member.' , 'error' ) ;
                        return false ;
                    } ) ;
        <?php } ?>

                jQuery( '.sumomemberships_delete_plan_button' ).click( function() {
                    var uniqid = jQuery( this ).attr( 'data-uniqid' ) ;
                    swal( {
                        title : 'Are you sure?' ,
                        text : "You won't be able to revert this!" ,
                        type : 'warning' ,
                        showCancelButton : true ,
                        confirmButtonColor : '#3085d6' ,
                        cancelButtonColor : '#d33' ,
                        confirmButtonText : 'Yes, delete it!'
                    } ).then( function( isConfirm ) {
                        if( isConfirm ) {
                            var dataparam = ( {
                                action : 'sumo_delete_membership_plan' ,
                                uniqid : uniqid ,
                                postid :<?php echo $post->ID ; ?> ,
                            } ) ;
                            jQuery.post( "<?php echo admin_url( 'admin-ajax.php' ) ; ?>" , dataparam ,
                                    function( response ) {

                                    } ).then( function( response ) {
                                swal( {
                                    title : "Deleted!" ,
                                    text : "Membership Plan Deleted Successfully." ,
                                    type : "success" ,
                                    showConfirmButton : false
                                } ) ;
                                window.location.replace( response ) ;
                            } ) ;

                        } else if( isConfirm === false ) {
                            swal( {
                                title : "Cancelled!" ,
                                type : "error" ,
                            } ) ;
                        }
                    } ) ;


                } ) ;
                jQuery( '.sumomemberships_transfer_plan_button' ).click( function() {
                    jQuery( '.user_select_box_for_transfer_membership' ).toggle() ;
                    jQuery( '.user_select_box_for_link_membership' ).hide() ;
                } ) ;
                jQuery( '.sumomemberships_link_user_to_plan' ).click( function() {
                    jQuery( '.user_select_box_for_link_membership' ).toggle() ;
                    jQuery( '.user_select_box_for_transfer_membership' ).hide() ;
                } ) ;

                jQuery( '.sumomemberships_transfer_confirm' ).click( function() {
                    var uniqid = jQuery( this ).attr( 'data-uniqid' ) ;
                    var user_id = jQuery( '#sumomemberships_select_user_to_transfer_plan' + uniqid ).val() ;
                    var current_user_id = "<?php echo get_post_meta( $post->ID , 'sumomemberships_userid' , true ) ; ?>" ;
                    if( user_id !== "" ) {
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
                                        action : 'sumo_transfer_membership_plan' ,
                                        uniqid : uniqid ,
                                        userid : user_id ,
                                        postid : "<?php echo $post->ID ; ?>" ,
                                    } ) ;
                                    jQuery.post( "<?php echo admin_url( 'admin-ajax.php' ) ; ?>" , dataparam ,
                                            function( response ) {

                                            } ).then( function( response ) {
                                        if( response == 555 ) {
                                            swal( {
                                                title : "This User had this Plan Already!" ,
                                                type : "error" ,
                                            } ) ;
                                        } else {
                                            swal( {
                                                title : "Transfered!" ,
                                                text : "Membership Plan Transfered Successfully." ,
                                                type : "success" ,
                                                showConfirmButton : false
                                            } ) ;
                                            window.location.replace( response ) ;
                                        }
                                    } ) ;

                                } else if( isConfirm === false ) {
                                    swal( {
                                        title : "Cancelled!" ,
                                        type : "error"
                                    } ) ;
                                }
                            } ) ;
                        }
                    }
                } ) ;

                jQuery( '.sumomemberships_confirm_link_user_to_plan' ).click( function() {
                    var uniqid = jQuery( this ).attr( 'data-uniqid' ) ;
                    var users = jQuery( '#sumomemberships_select_user_to_link_plan' + uniqid ).val() ;
                    if( users ) {
                        var dataparam = ( {
                            action : 'sumo_link_membership_plan' ,
                            uniqid : uniqid ,
                            users : users ,
                            postid : "<?php echo $post->ID ; ?>"
                        } ) ;
                        jQuery.post( "<?php echo admin_url( 'admin-ajax.php' ) ; ?>" , dataparam , function( response ) {
                            if( response ) {
                                window.location.reload( true ) ;
                            }
                        } ) ;
                    }
                } ) ;

                jQuery( '.sumo_remove_linked_user_from_smp' ).click( function() {
                    var user_id = jQuery( this ).attr( 'data-user_id' ) ;
                    var plan_id = jQuery( this ).attr( 'data-plan_id' ) ;
                    var post_id = jQuery( this ).attr( 'data-post_id' ) ;
                    var dataparam = ( {
                        action : 'sumo_unlink_membership_plan' ,
                        user_id : user_id ,
                        plan_id : plan_id ,
                        post_id : post_id
                    } ) ;
                    jQuery.post( "<?php echo admin_url( 'admin-ajax.php' ) ; ?>" , dataparam , function( response ) {
                        if( response ) {
                            window.location.reload( true ) ;
                        }
                    } ) ;
                } ) ;
            } ) ;
        </script>
        <?php
    }

    public function AJAX_delete_membership_plan() {
        if( isset( $_POST[ 'uniqid' ] ) && isset( $_POST[ 'postid' ] ) ) {
            $postid  = $_POST[ 'postid' ] ;
            $uniqid  = $_POST[ 'uniqid' ] ;
            $getdata = get_post_meta( $postid , 'sumomemberships_saved_plans' , true ) ;
            $plan_id = $getdata[ $uniqid ][ 'choose_plan' ] ;
            update_post_meta( $postid , 'sumo_linked_users_of_' . $plan_id , array() ) ;
            if( isset( $getdata[ $uniqid ] ) ) {
                do_action( 'sumomemberships_delete_plan' , $uniqid , $getdata , $postid ) ;
                do_action( 'sumomemberships_plan_status_changed' , $postid , $getdata[ $uniqid ][ 'choose_plan' ] , 'delete' ) ;
                unset( $getdata[ $uniqid ] ) ;
            }
            update_post_meta( $postid , 'sumomemberships_saved_plans' , $getdata ) ;
            echo admin_url( 'post.php?post=' . $postid . '&action=edit&tab=add_plan_tab' ) ;
        }
        exit() ;
    }

    public function AJAX_transfer_membership_plan() {
        if( isset( $_POST[ 'uniqid' ] ) && isset( $_POST[ 'postid' ] ) && isset( $_POST[ 'userid' ] ) ) {
            $postid        = $_POST[ 'postid' ] ;
            $replace_array = array() ;
            $uniqid        = $_POST[ 'uniqid' ] ;
            $userid        = $_POST[ 'userid' ] ;
            $user_name     = get_userdata( $userid )->user_login ;
            $user_email    = get_userdata( $userid )->user_email ;
            $getdata       = get_post_meta( $postid , 'sumomemberships_saved_plans' , true ) ;
            $new_post_id   = sumo_get_member_post_id( $userid ) ;
            $plan_id       = $getdata[ $uniqid ][ 'choose_plan' ] ;
            $linked_users  = get_post_meta( $postid , 'sumo_linked_users_of_' . $plan_id , true ) ;
            $planname      = get_the_title( $plan_id ) ;
            if( $new_post_id > 0 ) {
                if( sumo_plan_is_already_had( $plan_id , $new_post_id ) ) {
                    $new_getdata = get_post_meta( $new_post_id , 'sumomemberships_saved_plans' , true ) ;
                    if( is_array( $new_getdata ) && ! empty( $new_getdata ) ) {
                        foreach( $new_getdata as $key => $new_eachdata ) {
                            $replace_array[ $key ] = $new_eachdata[ 'choose_plan' ] ;
                        }
                    }
                    if( ! empty( $replace_array ) ) {
                        if( in_array( $plan_id , $replace_array ) ) {
                            $search = array_search( $plan_id , $replace_array ) ;
                            unset( $new_getdata[ $search ] ) ;
                        }
                    }
                    $new_getdata[ $uniqid ] = $getdata[ $uniqid ] ;
                    do_action( 'sumomemberships_plan_status_changed' , $new_post_id , $plan_id , $getdata[ $uniqid ][ 'choose_status' ] ) ;
                    update_post_meta( $new_post_id , 'sumomemberships_saved_plans' , $new_getdata ) ;
                    update_post_meta( $new_post_id , 'sumo_linked_users_of_' . $plan_id , $linked_users ) ;
                    update_post_meta( $postid , 'sumo_linked_users_of_' . $plan_id , array() ) ;
                } else {
                    echo 555 ;
                    exit() ;
                }
            } else {
                $args        = array(
                    'post_title'     => $user_name ,
                    'post_type'      => "sumomembers" ,
                    'post_status'    => 'publish' ,
                    'posts_per_page' => -1
                        ) ;
                $new_post_id = wp_insert_post( $args ) ;
                $firstuniqid = uniqid() ;
                $saved_plans = array(
                    $firstuniqid => $getdata[ $uniqid ]
                        ) ;
                update_post_meta( $new_post_id , 'sumomemberships_userid' , $userid ) ;
                do_action( 'sumomemberships_plan_status_changed' , $new_post_id , $plan_id , $getdata[ $uniqid ][ 'choose_status' ] ) ;
                update_post_meta( $new_post_id , 'sumomemberships_saved_plans' , $saved_plans ) ;
                add_post_meta( $new_post_id , 'sumomemberships_member_since_date' , time() ) ;
                update_post_meta( $new_post_id , 'sumo_linked_users_of_' . $plan_id , $linked_users ) ;
                update_post_meta( $postid , 'sumo_linked_users_of_' . $plan_id , array() ) ;
                do_action( 'sumomemberships_add_new_plan_upon_order_status' , $saved_plans , $plan_id , $firstuniqid , $new_post_id ) ;
            }
            $sumo_email_for = 'sumo_plan_manual_transfer' ;
            $sender_user_id = get_post_meta( $postid , 'sumomemberships_userid' , true ) ;
            $to             = get_userdata( $sender_user_id )->user_email ;
            sumo_email_for_transfer_plans( $sumo_email_for , $to , $user_email , $planname ) ;
            update_post_meta( $sender_user_id , $plan_id . 'plan_switched_to' , $userid ) ;
            if( isset( $getdata[ $uniqid ] ) ) {
                do_action( 'sumomemberships_delete_plan' , $uniqid , $getdata , $postid ) ;
                do_action( 'sumomemberships_plan_status_changed' , $postid , $getdata[ $uniqid ][ 'choose_plan' ] , 'delete' ) ;
                unset( $getdata[ $uniqid ] ) ;
            }
            update_post_meta( $postid , 'sumomemberships_saved_plans' , $getdata ) ;
            echo admin_url( 'post.php?post=' . $postid . '&action=edit&tab=add_plan_tab' ) ;
        }
        exit() ;
    }

    public function AJAX_link_membership_plan() {
        if( isset( $_POST[ 'postid' ] ) ) {
            $post_id       = $_POST[ 'postid' ] ;
            $users         = is_array( $_POST[ 'users' ] ) ? $_POST[ 'users' ] : explode( ',' , $_POST[ 'users' ] ) ;
            $uniqid        = $_POST[ 'uniqid' ] ;
            $getdata       = get_post_meta( $post_id , 'sumomemberships_saved_plans' , true ) ;
            $plan_id       = $getdata[ $uniqid ][ 'choose_plan' ] ;
            $linked_users1 = get_post_meta( $post_id , 'sumo_linked_users_of_' . $plan_id , true ) ;
            $linked_users  = is_array( $linked_users1 ) ? $linked_users1 : array() ;
            $post_author   = get_post_meta( $post_id , 'sumomemberships_userid' , true ) ;
            foreach( $users as $user_id ) {
                $check = self::sumo_check_user_has_already_this_plan( $user_id , $plan_id ) ;
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

    public static function AJAX_unlink_membership_plan() {
        if( isset( $_POST[ 'user_id' ] ) && isset( $_POST[ 'post_id' ] ) && isset( $_POST[ 'plan_id' ] ) ) {
            $post_id = $_POST[ 'post_id' ] ;
            $plan_id = $_POST[ 'plan_id' ] ;
            $user_id = $_POST[ 'user_id' ] ;
            $array   = get_post_meta( $post_id , 'sumo_linked_users_of_' . $plan_id , true ) ;
            if( ($key     = array_search( $user_id , $array )) !== false ) {
                unset( $array[ $key ] ) ;
            }
            update_post_meta( $post_id , 'sumo_linked_users_of_' . $plan_id , $array ) ;
        }
    }

    public static function sumo_check_user_has_already_this_plan( $user_id , $plan_id ) {
        $new_post_id = sumo_get_member_post_id( $user_id ) ;
        if( $new_post_id > 0 ) {
            if( sumo_plan_is_already_had( $plan_id , $new_post_id ) ) {
                return true ;
            } else {
                return false ;
            }
        } else {
            return true ;
        }
    }

    public function clear_automatic_process_cron_events_on_manual_deletion( $uniqid , $getdata , $postid ) {
        if( is_array( $getdata ) && ! empty( $getdata ) && isset( $getdata[ $uniqid ][ 'choose_plan' ] ) ) {

            $plan_id = $getdata[ $uniqid ][ 'choose_plan' ] ;
            $user_id = get_post_meta( $postid , 'sumomemberships_userid' , true ) ;

            wp_clear_scheduled_hook( 'sumo_memberships_process_plan_duration_validity' , array( ( int ) $user_id , ( int ) $plan_id ) ) ;

            $scheduled_plans = $getdata[ $uniqid ][ 'scheduled_link_plans' ] ;

            if( is_array( $scheduled_plans ) && ! empty( $scheduled_plans ) ) {
                foreach( $scheduled_plans as $each_link_plan_id ) {
                    wp_clear_scheduled_hook( 'sumo_memberships_process_linked_plan_privilege' , array( ( int ) $user_id , ( int ) $each_link_plan_id , ( int ) $plan_id ) ) ;
                }
            }
        }
    }

    public function output_to_add_plan() {
        global $post , $woocommerce ;
        $membership_level = sumo_get_membership_levels() ;
        $oldmetavalue     = ( array ) get_post_meta( $post->ID , 'sumomemberships_saved_plans' , true ) ;
        $param            = array( 'tab' => 'add_plan_tab' ) ;
        $defaulturl       = add_query_arg( $param , $_SERVER[ 'REQUEST_URI' ] ) ;
        $saved_plans      = array() ;

        foreach( $oldmetavalue as $uniqueid => $data ) {
            if( isset( $data[ 'choose_plan' ] ) && $data[ 'choose_plan' ] != '' ) {
                $saved_plans[] = $data[ 'choose_plan' ] ;
            }
        }
        ?>
        <h2 class="nav-tab-wrapper">
            <?php
            if( is_array( $oldmetavalue ) ) {
                foreach( $oldmetavalue as $key => $value ) {
                    if( isset( $value[ 'choose_plan' ] ) && $value[ 'choose_plan' ] != '' ) {
                        $param = array( 'tab' => 'add_plan_tab' . $key ) ;
                        $url   = $_SERVER[ 'REQUEST_URI' ] ;
                        ?>
                        <a href = "<?php echo add_query_arg( $param , $url ) ; ?>" id = "add_plan_tab<?php echo $key ; ?>" class = "nav-tab <?php if( isset( $_GET[ 'tab' ] ) && ($_GET[ 'tab' ] == 'add_plan_tab' . $key) ) { ?>nav-tab-active<?php } ?>"><?php
                            if( ! empty( $membership_level ) ) {
                                foreach( $membership_level as $keys => $values ) {
                                    if( isset( $value[ 'choose_plan' ] ) && ($value[ 'choose_plan' ] == $keys) ) {
                                        echo $values ;
                                    }
                                }
                            }
                            ?></a>
                        <?php
                    }
                }
            }
            if( count( $saved_plans ) != count( $membership_level ) ) {
                ?>
                <a href = "<?php echo $defaulturl ; ?>" id = "add_plan_tab" class = "nav-tab <?php if( isset( $_GET[ 'tab' ] ) && ($_GET[ 'tab' ] == 'add_plan_tab') ) { ?>nav-tab-active<?php } ?>"><?php _e( 'Add Plan' , 'sumomemberships' ) ; ?></a>
                <?php
            }
            ?>
        </h2>
        <?php
        if( is_array( $oldmetavalue ) && ! empty( $oldmetavalue ) ) {
            foreach( $oldmetavalue as $key => $value ) {
                if( isset( $value[ 'choose_plan' ] ) && $value[ 'choose_plan' ] != '' ) {
                    ?>
                    <div id = "add_plan_tab<?php echo $key ; ?>" <?php if( isset( $_GET[ 'tab' ] ) && ($_GET[ 'tab' ] != 'add_plan_tab' . $key) ) { ?>class="hidden"<?php } ?>>
                        <table>
                            <tr>
                                <td>
                                    <h3><?php _e( 'Plan Name' , 'sumomemberships' ) ; ?></h3>
                                </td>
                                <td>
                                    <select name="sumomember_plan_meta[<?php echo $key ; ?>][choose_plan]" class="sumomemberships_choose_plan">
                                        <?php
                                        if( ! empty( $membership_level ) ) {
                                            foreach( $membership_level as $keys => $values ) {
                                                if( $keys == $value[ 'choose_plan' ] ) {
                                                    ?>
                                                    <option value="<?php echo $keys ; ?>" selected="selected"><?php echo $values ; ?></option>
                                                    <?php
                                                }
                                                if( ! in_array( $keys , $saved_plans ) ) {
                                                    ?>
                                                    <option value="<?php echo $keys ; ?>"><?php echo $values ; ?></option>
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <h3><?php _e( 'Plan Status' , 'sumomemberships' ) ; ?></h3>
                                </td>
                                <td>
                                    <?php
                                    if( isset( $value[ 'associated_subsc_id' ] ) && $value[ 'associated_subsc_id' ] != '' ) {
                                        $subscription_id     = $value[ 'associated_subsc_id' ] ;
                                        $subscription_number = get_post_meta( $subscription_id , 'sumo_get_subscription_number' , true ) ;
                                        $subscription_status = get_post_meta( $subscription_id , 'sumo_get_status' , true ) ;
                                        echo 'This Plan is currently linked with this Subscription <a href="' . admin_url( "post.php?post=$subscription_id&action=edit" ) . '">#' . $subscription_number . '</a>  and the Current Status is <b>' . $subscription_status . '</b>' ;
                                        ?>
                                        <input type="hidden" name="sumomember_plan_meta[<?php echo $key ; ?>][choose_status]" value="<?php echo $value[ 'choose_status' ] ; ?>"/>
                                        <?php
                                    } elseif( isset( $value[ 'choose_status' ] ) && $value[ 'choose_status' ] == 'paused' && ! sumo_is_global_plan_status_active( $value[ 'choose_plan' ] ) ) {
                                        echo 'Disabled' ;
                                        ?>
                                        <input type="hidden" name="sumomember_plan_meta[<?php echo $key ; ?>][choose_status]" value="<?php echo $value[ 'choose_status' ] ; ?>"/>
                                        <?php
                                    } else {
                                        ?>
                                        <select name="sumomember_plan_meta[<?php echo $key ; ?>][choose_status]" class="sumomemberships_choose_status">
                                            <?php
                                            if( isset( $_GET[ 'tab' ] ) ) {
                                                if( $value[ 'choose_status' ] == 'paused' ) {
                                                    ?>
                                                    <option value="paused" selected="selected"><?php _e( 'Paused' , 'sumomemberships' ) ?></option>
                                                    <option value="active">Resume</option>
                                                    <option value="cancelled">Cancelled</option>
                                                    <?php
                                                } elseif( $value[ 'choose_status' ] == 'active' ) {
                                                    ?>
                                                    <option value="active" selected="selected"><?php _e( 'Active' , 'sumomemberships' ) ?></option>
                                                    <option value="paused">Paused</option>
                                                    <option value="cancelled">Cancelled</option>
                                                    <?php
                                                } elseif( $value[ 'choose_status' ] == 'expired' ) {
                                                    ?>
                                                    <option value="expired" selected="selected"><?php _e( 'Expired' , 'sumomemberships' ) ?></option>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <option value="cancelled" selected="selected"><?php _e( 'Cancelled' , 'sumomemberships' ) ?></option>
                                                    <?php
                                                }
                                            } else {
                                                ?>
                                                <option></option>
                                            <?php } ?>
                                        </select>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <h3><?php _e( 'Member Since' , 'sumomemberships' ) ; ?></h3>
                                </td>
                                <td>
                                    <?php 
                                    $dateformat = get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ;
                                    $gmt_offset = (2 == get_option('sumomemberships_member_since_display_type_in_post_table',1) ) ? (int) ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ):0;
                                    $from_date = isset( $value[ 'from_date' ] ) ? $value[ 'from_date' ] : '' ;
                                    $member_since_in_time = strtotime($from_date)+ $gmt_offset;
                                    ?>
                                    <input type="text" readonly name="sumomember_plan_meta[<?php echo $key ; ?>][from_date]"  class="sumomemberships_select_from_date" value="<?php echo date($dateformat,$member_since_in_time) ; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <h3><?php _e( 'Expires On' , 'sumomemberships' ) ; ?></h3>
                                </td>
                                <td>
                                    <?php
                                    if( isset( $value[ 'associated_subsc_id' ] ) && $value[ 'associated_subsc_id' ] != '' ) {
                                        $subscription_id     = $value[ 'associated_subsc_id' ] ;
                                        $subscription_number = get_post_meta( $subscription_id , 'sumo_get_subscription_number' , true ) ;
                                        echo 'This Plan is currently linked with this Subscription <a href="' . admin_url( "post.php?post=$subscription_id&action=edit" ) . '">#' . $subscription_number . '</a>' ;
                                        ?>
                                        <input type="hidden" name="sumomember_plan_meta[<?php echo $key ; ?>][to_date]" value=""/>
                                        <?php
                                    } else {
                                        if( isset( $value[ 'choose_status' ] ) && $value[ 'choose_status' ] == 'expired' ) {
                                            echo 'Expired' ;
                                            ?>
                                            <input type="hidden" name="sumomember_plan_meta[<?php echo $key ; ?>][to_date]" value="<?php echo isset( $value[ 'to_date' ] ) ? $value[ 'to_date' ] : "" ; ?>"/>
                                            <?php
                                        } else if( isset( $value[ 'choose_status' ] ) && $value[ 'choose_status' ] == 'cancelled' ) {
                                            echo 'Cancelled' ;
                                            ?>
                                            <input type="hidden" name="sumomember_plan_meta[<?php echo $key ; ?>][to_date]" value="<?php echo isset( $value[ 'to_date' ] ) ? $value[ 'to_date' ] : "" ; ?>"/>
                                            <?php
                                        } else if( isset( $value[ 'to_date' ] ) ? $value[ 'to_date' ] == '--' : false ) {
                                            echo 'Paused' ;
                                            ?>
                                            <input type="hidden" name="sumomember_plan_meta[<?php echo $key ; ?>][to_date]" value="<?php echo isset( $value[ 'to_date' ] ) ? $value[ 'to_date' ] : "" ; ?>"/>
                                            <?php
                                        } else {
                                            ?>
                                            <input type="text" name="sumomember_plan_meta[<?php echo $key ; ?>][to_date]"  class="sumomemberships_select_to_date" value="<?php echo isset( $value[ 'to_date' ] ) && $value[ 'to_date' ] != '' && $value[ 'to_date' ] != '--' ? $value[ 'to_date' ] : '' ; ?>"/> <?php
                                        }
                                    }
                                    ?>
                                </td>
                            </tr>

                            <?php
                            $member_post_id = sumo_get_member_post_id( get_post_meta( $post->ID , 'sumomemberships_userid' , true ) ) ;

                            $user_available_linked_plans = sumo_get_available_linked_plans( $member_post_id ) ;

                            $plan_name = array() ;

                            if( is_array( $value [ "link_plans" ] ) && array_filter( $value [ "link_plans" ] ) ) {

                                foreach( $value [ "link_plans" ] as $plan_id ) {

                                    if( in_array( $plan_id , $user_available_linked_plans ) ) {

                                        $plan_name[] = get_post_meta( $plan_id , 'sumomemberships_plan_name' , true ) ;
                                    }
                                }
                                ?>

                                <tr>

                                    <td>
                                        <h3><?php ! empty( $plan_name ) ? esc_html_e( 'Linked Plans' , 'sumomemberships' ) : '' ; ?></h3>
                                    </td>

                                    <td>
                                        <?php echo ! empty( $plan_name ) ? esc_html( implode( ' , ' , $plan_name ) ) : '' ; ?>
                                    </td>

                                </tr>

                                <?php
                            }
                            ?>
                            <tr>
                                <td></td>
                                <td>
                                    <?php
                                    $hidden_elements = array( 'associated_product' , 'associated_subsc_id' , 'associated_subsc_product' , 'plan_slug' , 'scheduled_link_plans' , 'link_plans' , 'available_duration' , 'order_id' ) ;

                                    foreach( $hidden_elements as $each_elements ) {

                                        $arrvalues = isset( $value[ $each_elements ] ) ? $value[ $each_elements ] : '' ;

                                        if( is_array( $arrvalues ) ) {
                                            if( ! empty( $arrvalues ) ) {
                                                foreach( $arrvalues as $eachvalue ) {
                                                    ?>
                                                    <input type="hidden" name="sumomember_plan_meta[<?php echo $key ; ?>][<?php echo $each_elements ; ?>][]" value="<?php echo $eachvalue ; ?>"/>
                                                    <?php
                                                }
                                            } else {
                                                ?>
                                                <input type="hidden" name="sumomember_plan_meta[<?php echo $key ; ?>][<?php echo $each_elements ; ?>][]" value=""/>
                                                <?php
                                            }
                                        } else {
                                            ?>
                                            <input type="hidden" name="sumomember_plan_meta[<?php echo $key ; ?>][<?php echo $each_elements ; ?>]" value="<?php echo $arrvalues ; ?>"/>
                                            <?php
                                        }
                                    }
                                    if( isset( $value[ 'choose_status' ] ) && $value[ 'choose_status' ] != 'cancelled' && $value[ 'choose_status' ] != 'expired' ) {
                                        ?>
                                        <input type="submit" value="Update" id="sumomemberships_save_plan_button" class="sumomemberships_save_plan_button button-primary"/>
                                    <?php } ?>
                                    <input type="button" value="Delete Membership Plan" id="sumomemberships_delete_plan_button" data-uniqid='<?php echo $key ; ?>' class="sumomemberships_delete_plan_button button-primary"/>
                                    <?php
                                    $restrict_transfer = '' ;
                                    $restrict_linking  = '' ;
                                    $default_plans     = ! is_array( get_option( 'sumomemberships_default_plans' ) ) ? explode( ',' , get_option( 'sumomemberships_default_plans' ) ) : get_option( 'sumomemberships_default_plans' ) ;
                                    if( ! empty( $default_plans ) ) {
                                        if( in_array( $value[ 'choose_plan' ] , $default_plans ) ) {
                                            if( get_option( 'sumo_restrict_trans_plan_for_dmp' ) == 'yes' ) {
                                                $restrict_transfer = 'yes' ;
                                            }
                                            if( get_option( 'sumo_restrict_link_users_for_dmp' ) == 'yes' ) {
                                                $restrict_linking = 'yes' ;
                                            }
                                        }
                                    }
                                    if( isset( $value[ 'choose_status' ] ) && $value[ 'choose_status' ] == 'active' ) {
                                        if( $restrict_transfer != 'yes' ) {
                                            ?>
                                            <input type="button" value="Transfer Membership" id="sumomemberships_transfer_plan_button" data-uniqid='<?php echo $key ; ?>' class="sumomemberships_transfer_plan_button button-primary"/>
                                            <?php
                                        }
                                    }
                                    if( $restrict_linking != 'yes' ) {
                                        ?>
                                        <input type="button" value="Link Users" id="sumomemberships_link_user_to_plan" data-uniqid='<?php echo $key ; ?>' class="sumomemberships_link_user_to_plan button-primary"/>
                                    <?php }
                                    ?>
                                </td>
                            </tr>
                            <tr class="user_select_box_for_transfer_membership" style="display: none">
                                <td>
                                    <h3><?php _e( 'Transfer To' , 'sumomemberships' ) ; ?></h3>
                                </td>
                                <td>
                                    <?php
                                    if( ( float ) $woocommerce->version <= ( float ) ('2.2.0') ) {
                                        $args      = array(
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
                                            'orderby'                => 'login' ,
                                            'order'                  => 'ASC' ,
                                            'offset'                 => '' ,
                                            'search'                 => '' ,
                                            'number'                 => '' ,
                                            'count_total'            => false ,
                                            'fields'                 => 'all' ,
                                            'who'                    => '' ,
                                            'update_post_term_cache' => false ,
                                            'update_post_post_cache' => false ,
                                            'cache_results'          => false ,
                                                ) ;
                                        $get_users = get_users( $args ) ;
                                        ?>
                                        <select name='sumomemberships_select_user_to_transfer_plan' class='sumomemberships_select_user_to_transfer_plan' id='sumomemberships_select_user_to_transfer_plan<?php echo $key ; ?>'>
                                            <?php
                                            $json_ids  = array() ;
                                            foreach( $get_users as $user ) {
                                                $user_string           = esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email ) . ')' ;
                                                $json_ids[ $user->ID ] = $user_string ;
                                                echo '<option value="' . $user->ID . '" ' ;
                                                echo '>' . esc_html( $user_string ) . '</option>' ;
                                            }
                                            ?>
                                        </select>
                                        <?php
                                    } else {
                                        if( WC()->version < 3.0 ) {
                                            ?>
                                            <input type='hidden' class='wc-customer-search' name='sumomemberships_select_user_to_transfer_plan' class='sumomemberships_select_user_to_transfer_plan' id='sumomemberships_select_user_to_transfer_plan<?php echo $key ; ?>'  data-placeholder='<?php _e( 'Search Users' , 'sumomemberships' ) ; ?>' data-selected='' value='' data-allow_clear='true' />
                                            <?php
                                        } else {
                                            ?>
                                            <select class="wc-customer-search sumomemberships_select_user_to_transfer_plan" data-minimum_input_length="3" style="width:350px" id='sumomemberships_select_user_to_transfer_plan<?php echo $key ; ?>' name="sumomemberships_select_user_to_transfer_plan" data-placeholder="<?php esc_attr_e( 'Search for a customer&hellip;' , 'woocommerce' ) ; ?>" data-allow_clear="true"></select>
                                            <?php
                                        }
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr class="user_select_box_for_transfer_membership" style="display: none">
                                <td>
                                    <input type="button" value="Confirm Transfer" id="sumomemberships_transfer_confirm" data-uniqid='<?php echo $key ; ?>' class="sumomemberships_transfer_confirm button-primary"/>
                                </td>
                            </tr>
                            <tr class="user_select_box_for_link_membership" style="display: none">
                                <td>
                                    <h3><?php _e( 'Link Users' , 'sumomemberships' ) ; ?></h3>
                                </td>
                                <td>
                                    <?php
                                    if( ( float ) $woocommerce->version <= ( float ) ('2.2.0') ) {
                                        ?>
                                        <select name='sumomemberships_select_user_to_link_plan' multiple="true" class='sumomemberships_select_user_to_link_plan' id='sumomemberships_select_user_to_link_plan<?php echo $key ; ?>'>
                                            <?php
                                            $json_ids = array() ;
                                            foreach( $get_users as $user ) {
                                                $user_string           = esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email ) . ')' ;
                                                $json_ids[ $user->ID ] = $user_string ;
                                                echo '<option value="' . $user->ID . '" ' ;
                                                echo '>' . esc_html( $user_string ) . '</option>' ;
                                            }
                                            ?>
                                        </select>
                                        <?php
                                    } else {
                                        if( WC()->version < 3.0 ) {
                                            ?>
                                            <input type='hidden' class='wc-customer-search' data-multiple="true" name='sumomemberships_select_user_to_link_plan' class='sumomemberships_select_user_to_link_plan' id='sumomemberships_select_user_to_link_plan<?php echo $key ; ?>'  data-placeholder='<?php _e( 'Search Users' , 'sumomemberships' ) ; ?>' data-selected='' value='' data-allow_clear='true' />
                                            <?php
                                        } else {
                                            ?>
                                            <select class="wc-customer-search sumomemberships_select_user_to_link_plan" data-minimum_input_length="3" style="width:350px" id='sumomemberships_select_user_to_link_plan<?php echo $key ; ?>' name="sumomemberships_select_user_to_link_plan" data-placeholder="<?php esc_attr_e( 'Search for a customer&hellip;' , 'woocommerce' ) ; ?>" data-allow_clear="true"></select>
                                            <?php
                                        }
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr class="user_select_box_for_link_membership" style="display: none">
                                <td>
                                    <input type="button" value="Link Users" id="sumomemberships_confirm_link_user_to_plan" data-uniqid='<?php echo $key ; ?>' class="sumomemberships_confirm_link_user_to_plan button-primary"/>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?php
                                    $post_id      = $post->ID ;
                                    $plan_id      = $value[ 'choose_plan' ] ;
                                    $linked_users = get_post_meta( $post_id , 'sumo_linked_users_of_' . $plan_id , true ) ;
                                    if( is_array( $linked_users ) && ! empty( $linked_users ) ) {
                                        echo '<table border="1" class="table">'
                                        . '<thead>'
                                        . '<tr>'
                                        . '<th>' . __( 'Name' , '' ) . '</th>'
                                        . '<th>' . __( 'Email' , '' ) . '</th>'
                                        . '<th></th>'
                                        . '</tr>'
                                        . '</thead>'
                                        . '<tbody>' ;
                                        foreach( $linked_users as $eachlinkeduser ) {
                                            if( $eachlinkeduser ) {
                                                $userdata = get_userdata( $eachlinkeduser ) ;
                                                if( is_object( $userdata->data ) ) {
                                                    echo '<tr>'
                                                    . '<td>' . $userdata->data->user_login . '</td>'
                                                    . '<td>' . $userdata->data->user_email . '</td>'
                                                    . '<td><input type="button" class="sumo_remove_linked_user_from_smp" data-post_id="' . $post_id . '" data-plan_id="' . $plan_id . '" data-user_id="' . $eachlinkeduser . '" value="' . __( 'Remove' , '' ) . '"></td>'
                                                    . '</tr>' ;
                                                }
                                            }
                                        }
                                        echo '</tbody>'
                                        . '</table>' ;
                                    }
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <?php
                }
            }
        }
        $uniqid = uniqid() ;
        ?>
        <div id = "add_plan_tab" <?php if( isset( $_GET[ 'tab' ] ) && ($_GET[ 'tab' ] != 'add_plan_tab') ) { ?>class="hidden"<?php } ?>>
            <table>
                <tr>
                    <td>
                        <h3><?php _e( 'Plan Name' , 'sumomemberships' ) ; ?></h3>
                    </td>
                    <td>
                        <select name="sumomember_plan_metas[<?php echo $uniqid ; ?>][choose_plan]" class="sumomemberships_choose_plan">
                            <option value=""><?php _e( 'Choose Plan' , 'sumomemberships' ) ; ?></option>
                            <?php
                            if( ! empty( $membership_level ) ) {
                                foreach( $membership_level as $key => $value ) {
                                    if( ! in_array( $key , $saved_plans ) ) {
                                        ?>
                                        <option value="<?php echo $key ; ?>"><?php echo $value ; ?></option>
                                        <?php
                                    }
                                }
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h3><?php _e( 'Plan Status' , 'sumomemberships' ) ; ?></h3>
                    </td>
                    <td>
                        <select name="sumomember_plan_metas[<?php echo $uniqid ; ?>][choose_status]" class="sumomemberships_choose_status">
                            <option value="active"><?php _e( 'Active' , 'sumomemberships' ) ?></option>
                            <option value="paused"><?php _e( 'Paused' , 'sumomemberships' ) ?></option>
                            <option value="cancelled"><?php _e( 'Cancelled' , 'sumomemberships' ) ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h3><?php _e( 'Member Since' , 'sumomemberships' ) ; ?></h3>
                    </td>
                    <td>
                        <input type="text" readonly name="sumomember_plan_metas[<?php echo $uniqid ; ?>][from_date]"  class="sumomemberships_select_from_date"  value="<?php echo date( 'Y-m-d h:i:s' ) ; ?>" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <h3><?php _e( 'Expires On' , 'sumomemberships' ) ; ?></h3>
                    </td>
                    <td>
                        <input type="text" name="sumomember_plan_metas[<?php echo $uniqid ; ?>][to_date]"  class="sumomemberships_select_to_date"/>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>

                        <?php
                        $hidden_elements = array( 'associated_product' , 'associated_subsc_id' , 'associated_subsc_product' , 'plan_slug' , 'scheduled_link_plans' , 'link_plans' , 'available_duration' , 'order_id' ) ;

                        foreach( $hidden_elements as $each_elements ) {
                            ?>
                            <input type="hidden" name="sumomember_plan_metas[<?php echo $uniqid ; ?>][<?php echo $each_elements ; ?>]" value="<?php echo '' ; ?>"/>
                            <?php
                        }
                        ?>
                        <input type="submit" value="Save Plan" id="sumomemberships_save_plan_button" class="sumomemberships_save_plan_button button-primary"/>
                    </td>
                </tr>
            </table>
        </div>
        <?php
    }

    public function output_membership_notes() {
        global $post ;

        $args = array(
            'post_id' => $post->ID ,
            'orderby' => 'comment_ID' ,
            'order'   => 'DESC' ,
            'approve' => 'approve' ,
            'type'    => 'membership_note' ,
                ) ;

        $notes = get_comments( $args ) ;
        echo '<ul class="membership_notes">' ;

        if( is_array( $notes ) && ! empty( $notes ) ) {
            foreach( $notes as $eachnote ) {
                $note_classes = get_comment_meta( $eachnote->comment_ID , 'plan_status' , true ) ? get_comment_meta( $eachnote->comment_ID , 'plan_status' , true ) : "plan_default" ;
                ?>
                <li rel="<?php echo absint( $eachnote->comment_ID ) ; ?>" class="<?php echo $note_classes ; ?>">
                    <div class="note_content">
                        <?php echo wpautop( wptexturize( wp_kses_post( $eachnote->comment_content ) ) ) ; ?>
                    </div>
                    <p class="meta">
                        <abbr class="exact-date" title="<?php echo $eachnote->comment_date ; ?>"><?php echo $eachnote->comment_date ; ?></abbr>
                        <?php printf( ' ' . __( 'by %s' , 'sumomemberships' ) , $eachnote->comment_author ) ; ?>
                        <a href="#" class="sumomemberships_delete_note delete_note"><?php _e( 'Delete note' , 'sumomemberships' ) ; ?></a>
                    </p>
                </li>
                <?php
            }
        }
        echo "</ul>" ;
        ?>
        <div class="add_subscription_note">
            <h4><?php _e( 'Add note' , 'sumomemberships' ) ; ?></h4>
            <p>
                <textarea type="text" name="add_membership_note" id="add_membership_note" class="input-text" cols="20" rows="2"></textarea>
            </p>
            <p>
                <a href="#" class="sumomemberships_add_note_for_member button" data-id="<?php echo $post->ID ; ?>"><?php _e( 'Add' , 'sumomemberships' ) ; ?></a>
            </p>
        </div>
        <?php
    }

    public function add_manual_notes() {
        ?>
        <style type="text/css">
            ul.membership_notes li.processing .note_content {
                background: #dd9a48 none repeat scroll 0 0;
            }

            ul.membership_notes li.paused .note_content {
                background: blue none repeat scroll 0 0;
            }

            ul.membership_notes li.active .note_content {
                background: green none repeat scroll 0 0;
            }

            ul.membership_notes li .note_content {
                background: #ccc none repeat scroll 0 0;
            }

            ul.membership_notes li.cancelled .note_content {
                background: red none repeat scroll 0 0;
            }

            ul.membership_notes li.expired .note_content {
                background: red none repeat scroll 0 0;
            }

            ul.membership_notes li .note_content {
                padding: 10px;
                position: relative;
            }
            ul.membership_notes li .note_content::after {
                border-style: solid;
                border-width: 10px 10px 0 0;
                bottom: -10px;
                content: "";
                display: block;
                height: 0;
                left: 20px;
                position: absolute;
                width: 0;
            }

            ul.membership_notes {
                padding: 2px 0 0;
            }

            ul.membership_notes li {
                padding: 0 10px;
            }

            ul.membership_notes li .note_content p {
                margin: 0;
                padding: 0;
                word-wrap: break-word;
                color:#fff;
            }
            ul.membership_notes li p.meta {
                color: #999;
                font-size: 11px;
                margin: 0;
                padding: 10px;
            }
            ul.membership_notes li a.delete_note {
                color: #a00;
            }

            ul.membership_notes li.processing .note_content {
                background: #dd9a48 none repeat scroll 0 0;
            }
            ul.membership_notes li.processing .note_content::after {
                border-color: #dd9a48 transparent;
            }

            ul.membership_notes li.paused .note_content {
                background: blue none repeat scroll 0 0;
            }
            ul.membership_notes li.paused .note_content::after {
                border-color: blue transparent;
            }

            ul.membership_notes li.active .note_content {
                background: green none repeat scroll 0 0;
            }
            ul.membership_notes li.active .note_content::after {
                border-color: green transparent;
            }

            ul.membership_notes li.cancelled .note_content {
                background: red none repeat scroll 0 0;
            }
            ul.membership_notes li.cancelled .note_content::after {
                border-color: red transparent;
            }
            ul.membership_notes li.expired .note_content {
                background: red none repeat scroll 0 0;
            }
            ul.membership_notes li.expired .note_content::after {
                border-color: red transparent;
            }
        </style>
        <script type="text/javascript">
            jQuery( document ).ready( function() {
                jQuery( document ).on( 'click' , '.sumomemberships_add_note_for_member' , function() {
                    var current_content = jQuery( '#add_membership_note' ).val() ;
                    var post_id = jQuery( this ).attr( 'data-id' ) ;
                    var another_data = ( {
                        action : 'sumo_add_notes_for_members' ,
                        content : current_content ,
                        post_id : post_id ,
                    } ) ;
                    jQuery.post( "<?php echo admin_url( 'admin-ajax.php' ) ; ?>" , another_data , function( response ) {
                        // if(response==='1'){
                        jQuery( 'ul.membership_notes' ).prepend( response ) ;
                        jQuery( '#add_membership_note' ).val( '' ) ;
                        //  }
                    } ) ;
                } ) ;
                jQuery( document ).on( 'click' , '.sumomemberships_delete_note' , function() {
                    var current_value = jQuery( this ).parent().parent().attr( 'rel' ) ;
                    var dataparam = ( {
                        action : 'sumo_delete_members_note' ,
                        delete_id : current_value
                    } ) ;
                    var current_action = jQuery( this ) ;
                    jQuery.post( "<?php echo admin_url( 'admin-ajax.php' ) ; ?>" , dataparam , function( response ) {
                        if( response === '1' ) {
                            current_action.parent().parent().remove() ;
                        }
                    } ) ;
                    return false ;
                } ) ;
            } ) ;
        </script>
        <?php
    }

    public function AJAX_add_notes_manually() {
        if( isset( $_POST ) ) {
            $content      = $_POST[ 'content' ] ;
            $current_post = $_POST[ 'post_id' ] ;
            $current_id   = get_post_meta( $current_post , 'sumomemberships_userid' , true ) ;
            $comment_id   = self::add_membership_note( $content , $current_post , $current_id , 'processing' ) ;
            echo $this->add_note_html( $comment_id ) ;
        }
        exit() ;
    }

    public function add_note_html( $comment_id ) {
        ob_start() ;
        $eachnote     = get_comment( $comment_id ) ;
        $note_classes = get_comment_meta( $eachnote->comment_ID , 'plan_status' , true ) ? get_comment_meta( $eachnote->comment_ID , 'plan_status' , true ) : "sumo_default" ;
        ?>
        <li rel="<?php echo absint( $eachnote->comment_ID ) ; ?>" class="<?php echo $note_classes ; ?>">
            <div class="note_content">
                <?php echo wpautop( wptexturize( wp_kses_post( $eachnote->comment_content ) ) ) ; ?>
            </div>
            <p class="meta">
                <abbr class="exact-date" title="<?php echo $eachnote->comment_date ; ?>"><?php echo $eachnote->comment_date ; ?></abbr>
                <?php printf( ' ' . __( 'by %s' , 'sumomemberships' ) , $eachnote->comment_author ) ; ?>
                <a href="#" class="sumomemberships_delete_note delete_note"><?php _e( 'Delete note' , 'sumomemberships' ) ; ?></a>
            </p>
        </li>
        <?php
        return ob_get_clean() ;
    }

    public function AJAX_delete_members_note() {
        if( isset( $_POST ) ) {
            $delete_id = $_POST[ 'delete_id' ] ;
            echo wp_delete_comment( $delete_id , true ) ;
        }
        exit() ;
    }

    public function sumo_memberships_manage_posts_extra_table( $which ) {
        global $post ;
        if( ($which === 'top' ) && (((is_object( $post ) && $post->post_type == 'sumomembers')) || (isset( $_REQUEST[ 'post_type' ] ) && $_REQUEST[ 'post_type' ] == 'sumomembers')) ) {
            $query_arg  = isset( $_GET[ 'post_status' ] ) ? '&post_status=' . $_GET[ 'post_status' ] : '' ;
            $admin_url  = admin_url( 'edit.php?post_type=sumomembers' . $query_arg ) ;
            $export_url = wp_nonce_url( esc_url_raw( add_query_arg( array( 'action' => 'sumo_memberemails-export-csv' ) , $admin_url ) ) , 'sumo_memberemails-exportcsv' ) ;

            if( ! isset( $_GET[ 'post_status' ] ) || $_GET[ 'post_status' ] != 'trash' ) {

                echo '<input type="submit" id="post-query-submit" class="button button-primary" name="sumo_member_emails_export_csv_action" value="' . __( 'Export emails as CSV' , 'sumomemberships' ) . '">' ;
            }
        }
    }

    public function sumo_member_emails_export_csv() {
        if( isset( $_REQUEST[ 'sumo_member_emails_export_csv_action' ] ) && $_REQUEST[ 'sumo_member_emails_export_csv_action' ] ) {
            $args  = array(
                'posts_per_page' => -1 ,
                'post_type'      => 'sumomembers' ,
                'post_status'    => 'publish'
                    ) ;
            $posts = sumo_members_export_email_check_query_having_posts( $args ) ;
            $array = array() ;
            $i     = 0 ;
            if( is_array( $posts ) && ! empty( $posts ) ) {
                foreach( $posts as $post ) {
                    $useremail  = '' ;
                    $username   = '' ;
                    $first_name = '' ;
                    $last_name  = '' ;
                    $user_id    = get_post_meta( $post->ID , 'sumomemberships_userid' , true ) ;
                    if( $user_id != '' ) {
                        $user_info = get_userdata( $user_id ) ;
                        if( $user_info ) {
                            $useremail  = $user_info->user_email ;
                            $username   = $user_info->user_login ;
                            $first_name = $user_info->first_name;
                            $last_name  = $user_info->last_name;
                        }
                    }
                    
                    $customer         = ''!=$user_id ? new WC_Customer($user_id) : false;
                    $billing_address  = is_object($customer) ? implode( "\n" , array_filter( array_values( $customer->get_billing() ) ) ):'-';
                    $shipping_address = is_object($customer) ? implode( "\n" , array_filter( array_values( $customer->get_shipping() ) ) ):'-';
                    $plan_id          = isset( $_REQUEST[ 'sumomemberships_choose_plan' ] ) && $_REQUEST[ 'sumomemberships_choose_plan' ] ? $_REQUEST[ 'sumomemberships_choose_plan' ] : '' ;
                    $plan_status      = isset( $_REQUEST[ 'sumomemberships_choose_status' ] ) && $_REQUEST[ 'sumomemberships_choose_status' ] ? array( $_REQUEST[ 'sumomemberships_choose_status' ] ) : array( 'active' , 'paused' , 'cancelled' ) ;
                    $getdatatodisplay = get_post_meta( $post->ID , 'sumomemberships_saved_plans' , true ) ;
                    foreach( $getdatatodisplay as $datas ) {
                        if( isset( $datas[ 'choose_status' ] ) && in_array( $datas[ 'choose_status' ] , $plan_status ) ) {
                            $subscribtion_id = $datas[ 'associated_subsc_id' ] ? $datas[ 'associated_subsc_id' ] : '' ;
                            $end_date        = '' ;
                            if( $subscribtion_id ) {
                                $end_date = get_post_meta( $subscribtion_id , 'sumo_get_next_payment_date' , true ) ;
                            }
                            $expires_on = $end_date ? $end_date : $datas[ 'to_date' ] ;
                            $to_date    = $expires_on ? $expires_on : 'Never Expires' ;
                            $dateformat = get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ;
                            $gmt_offset = (2 == get_option('sumomemberships_member_since_display_type_in_post_table',1) ) ? (int) ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ):0;
                            $member_since_in_time = strtotime($datas[ 'from_date' ])+ $gmt_offset;
                            if( $plan_id ) {
                                if( $plan_id == $datas[ 'choose_plan' ] ) {
                                    $array[ $i ] = array( 
                                        'member_name'        => $username , 
                                        'first_name'         => $first_name,
                                        'last_name'          => $last_name,
                                        'email_id'           => $useremail ,
                                        'plan_name'          => get_the_title( $datas[ 'choose_plan' ] ) , 
                                        'member_since'       => date($dateformat,$member_since_in_time), 
                                        'expires_on'         => $to_date,
                                        'billing_address'    => $billing_address,
                                        'shipping_address'   => $shipping_address,
                                            ) ;
                                }
                            } else {
                                if( $datas[ 'choose_plan' ] ) {
                                    $array[ $i ] = array( 
                                        'member_name'        => $username , 
                                        'first_name'         => $first_name,
                                        'last_name'          => $last_name,
                                        'email_id'           => $useremail ,
                                        'plan_name'          => get_the_title( $datas[ 'choose_plan' ] ) , 
                                        'member_since'       => date($dateformat,$member_since_in_time) ,
                                        'expires_on'         => $to_date,
                                        'billing_address'    => $billing_address,
                                        'shipping_address'   => $shipping_address,
                                            ) ;
                                }
                            }
                        }
                        $i ++ ;
                    }
                }
            }
            ob_end_clean() ;
            header( "Content-type: text/csv" ) ;
            header( "Content-Disposition: attachment; filename=sumo_members_email" . date_i18n( "Y-m-d H:i:s" ) . ".csv" ) ;
            header( "Pragma: no-cache" ) ;
            header( "Expires: 0" ) ;

            $output = fopen( "php://output" , 'w' ) ;
            fputcsv( $output , array( 
                esc_html__( 'Member Name' , 'sumomemberships' ) ,
                esc_html__( 'First Name' , 'sumomemberships' ) ,
                esc_html__( 'Last Name' , 'sumomemberships' ) ,
                esc_html__( 'Email ids' , 'sumomemberships' ) ,
                esc_html__( 'Plan Name' , 'sumomemberships' ) ,
                esc_html__( 'Member Since' , 'sumomemberships' ) ,
                esc_html__( 'Expires on' , 'sumomemberships' ),
                esc_html__( 'Billing Address' , 'sumomemberships' ),
                esc_html__( 'Shipping Address' , 'sumomemberships' )) 
                    ) ;
            foreach( $array as $email_ids ) {
                fputcsv( $output , $email_ids ) ;
            }
            fclose( $output ) ;
            exit() ;
        }
    }

    public static function add_membership_note( $note , $id , $user_id , $status , $planid = '' ) {
        $user               = get_user_by( 'id' , $user_id ) ;
        $author             = $user->display_name ;
        $email              = $user->user_email ;
        $date               = date( 'Y/m/d h:i:s' ) ;
        $get_existing_notes = get_option( 'sumo_get_membership_notes' ) ;

        if( $planid != '' ) {
            $planname = get_post_meta( $planid , 'sumomemberships_plan_name' , true ) ;
            $planname = $planname . ": " ;
        } else {
            $planname = '' ;
        }

        $comment_args = array(
            'comment_post_ID'      => $id ,
            'comment_author'       => $author ,
            'comment_author_email' => $email ,
            'comment_author_url'   => '' ,
            'comment_content'      => $planname . $note ,
            'comment_type'         => 'membership_note' ,
            'comment_parent'       => 0 ,
            'comment_approved'     => 1 ,
                ) ;
        $comment_id   = wp_insert_comment( $comment_args ) ;

        $get_new_note[] = array( 'postid' => $comment_args[ 'comment_post_ID' ] , 'note' => $comment_args[ 'comment_content' ] , 'plan_status' => $status , 'user_name' => $author , 'date' => $date ) ;

        if( $comment_id > 0 ) {
            if( empty( $get_existing_notes ) ) {
                update_option( 'sumo_get_membership_notes' , $get_new_note ) ;
            } else {
                $all_notes = array_merge( $get_new_note , $get_existing_notes ) ;
                update_option( 'sumo_get_membership_notes' , $all_notes ) ;
            }
        }
        add_comment_meta( $comment_id , 'plan_status' , $status ) ;

        return $comment_id ;
    }

    public function output_restrictions_settings( $this_post ) {
        $this_postid = $this_post->ID ;
        ?>
        <select id="sumomemberships_products_posts_pages_settings" name="sumomemberships_products_posts_pages_settings">
            <option value="all_users" <?php if( get_post_meta( $this_postid , 'sumomemberships_products_posts_pages_settings' , true ) == 'all_users' ) { ?> selected="selected" <?php } ?>>
                <?php _e( 'Accessible by All Users' , 'sumomemberships' ) ; ?>
            </option>
            <option value="with_particular_plans" <?php if( get_post_meta( $this_postid , 'sumomemberships_products_posts_pages_settings' , true ) == 'with_particular_plans' ) { ?> selected="selected" <?php } ?>>
                <?php _e( 'Members with Particular Plans' , 'sumomemberships' ) ; ?>
            </option>
            <option value="without_particular_plans" <?php if( get_post_meta( $this_postid , 'sumomemberships_products_posts_pages_settings' , true ) == 'without_particular_plans' ) { ?> selected="selected" <?php } ?>>
                <?php _e( 'Users without Particular Plans' , 'sumomemberships' ) ; ?>
            </option>
            <option value="all_members" <?php if( get_post_meta( $this_postid , 'sumomemberships_products_posts_pages_settings' , true ) == 'all_members' ) { ?> selected="selected" <?php } ?>>
                <?php _e( 'All Members' , 'sumomemberships' ) ; ?>
            </option>
            <option value="all_non_members" <?php if( get_post_meta( $this_postid , 'sumomemberships_products_posts_pages_settings' , true ) == 'all_non_members' ) { ?> selected="selected" <?php } ?>>
                <?php _e( 'All Non Members' , 'sumomemberships' ) ; ?>
            </option>
        </select>
        <br><br>
        <p class="sumomemberships_all_members_schedule_type">
            <?php _e( 'Any Plan :' , 'sumomemberships' ) ; ?> &nbsp;
            <input type="radio" name="sumomemberships_all_members_schedule_type" id="sumomemberships_all_members_schedule_type" value="immediately" checked>
            <?php _e( 'Immediately' , 'sumomemberships' ) ; ?>
            <input type="radio" name="sumomemberships_all_members_schedule_type" id="sumomemberships_all_members_schedule_type" value="scheduled"
                   <?php if( get_post_meta( $this_postid , 'sumomemberships_all_members_schedule_type' , true ) == 'scheduled' ) { ?> checked = "checked" <?php } ?>>
                   <?php _e( 'Scheduled' , 'sumomemberships' ) ; ?>
        </p>
        <input type="number" min="1" style="width: 15%" name="sumomemberships_all_members_scheduled_duration_value" id="sumomemberships_all_members_scheduled_duration_value" value="<?php echo get_post_meta( $this_postid , 'sumomemberships_all_members_scheduled_duration_value' , true ) ; ?>">
        <select id="sumomemberships_all_members_scheduled_duration_period" name="sumomemberships_all_members_scheduled_duration_period">
            <option value="days"
                    <?php if( get_post_meta( $this_postid , 'sumomemberships_all_members_scheduled_duration_period' , true ) == 'day' || get_post_meta( $this_postid , 'sumomemberships_all_members_scheduled_duration_period' , true ) == 'days' ) { ?> selected="selected" <?php } ?>>
                        <?php get_post_meta( $this_postid , 'sumomemberships_all_members_scheduled_duration_period' , true ) == 'day' ? _e( 'Day' , 'sumomemberships' ) : _e( 'Days' , 'sumomemberships' ) ; ?>
            </option>
            <option value="weeks"
                    <?php if( get_post_meta( $this_postid , 'sumomemberships_all_members_scheduled_duration_period' , true ) == 'week' || get_post_meta( $this_postid , 'sumomemberships_all_members_scheduled_duration_period' , true ) == 'weeks' ) { ?> selected="selected" <?php } ?>>
                        <?php get_post_meta( $this_postid , 'sumomemberships_all_members_scheduled_duration_period' , true ) == 'week' ? _e( 'Week' , 'sumomemberships' ) : _e( 'Weeks' , 'sumomemberships' ) ; ?>
            </option>
            <option value="months"
                    <?php if( get_post_meta( $this_postid , 'sumomemberships_all_members_scheduled_duration_period' , true ) == 'month' || get_post_meta( $this_postid , 'sumomemberships_all_members_scheduled_duration_period' , true ) == 'months' ) { ?> selected="selected" <?php } ?>>
                        <?php get_post_meta( $this_postid , 'sumomemberships_all_members_scheduled_duration_period' , true ) == 'month' ? _e( 'Month' , 'sumomemberships' ) : _e( 'Months' , 'sumomemberships' ) ; ?>
            </option>
            <option value="years"
                    <?php if( get_post_meta( $this_postid , 'sumomemberships_all_members_scheduled_duration_period' , true ) == 'year' || get_post_meta( $this_postid , 'sumomemberships_all_members_scheduled_duration_period' , true ) == 'years' ) { ?> selected="selected" <?php } ?>>
                        <?php get_post_meta( $this_postid , 'sumomemberships_all_members_scheduled_duration_period' , true ) == 'year' ? _e( 'Year' , 'sumomemberships' ) : _e( 'Years' , 'sumomemberships' ) ; ?>
            </option>
        </select>
        <?php
        $with_particular_plan_meta_key = array(
            'table_name'        => 'sumo_restrict_members_with_particular_plan' ,
            'no_rules_added'    => 'sumo_restrict_members_with_particular_plan_no_rules_added' ,
            'add_rule'          => 'sumo_restrict_members_with_particular_plan_add_rule' ,
            'remove_rule'       => 'sumo_restrict_members_with_particular_plan_remove_rule' ,
            'no_of_rules_added' => 'sumomemberships_restrict_members_with_particular_plan_no_of_rules_added' ,
            'membership_plan'   => 'sumomemberships_restrict_members_with_particular_plan_purchased' ,
            'schedule_type'     => 'sumomemberships_restrict_members_with_particular_plan_schedule_type' ,
            'duration_value'    => 'sumomemberships_restrict_members_with_particular_plan_duration_value' ,
            'duration_period'   => 'sumomemberships_restrict_members_with_particular_plan_duration_period'
                ) ;

        echo $this->display_add_rule_table( $this_postid , $with_particular_plan_meta_key ) ;

        $without_particular_plan_meta_key = array(
            'table_name'        => 'sumo_restrict_users_without_particular_plan' ,
            'no_rules_added'    => 'sumo_restrict_users_without_particular_plan_no_rules_added' ,
            'add_rule'          => 'sumo_restrict_users_without_particular_plan_add_rule' ,
            'remove_rule'       => 'sumo_restrict_users_without_particular_plan_remove_rule' ,
            'no_of_rules_added' => 'sumomemberships_restrict_users_without_particular_plan_no_of_rules_added' ,
            'membership_plan'   => 'sumomemberships_restrict_users_without_particular_plan_purchased' ,
                ) ;

        echo $this->display_add_rule_table( $this_postid , $without_particular_plan_meta_key ) ;
        ?>
        <script type="text/javascript">
            jQuery( document ).ready( function() {

                var myval = jQuery( '#sumomemberships_display_content_when_restriction_enabled' ).val() ;

                if( myval == 'show' ) {
                    jQuery( '#wp-sumo_content_for_reu-wrap' ).show() ;
                } else {
                    jQuery( '#wp-sumo_content_for_reu-wrap' ).hide() ;
                }
                jQuery( '#sumomemberships_display_content_when_restriction_enabled' ).change( function() {
                    if( this.value == 'show' ) {
                        jQuery( '#wp-sumo_content_for_reu-wrap' ).show() ;
                    } else {
                        jQuery( '#wp-sumo_content_for_reu-wrap' ).hide() ;
                    }
                } ) ;

                function show_hide_input_fields() {

                    if( jQuery( "select[name='sumomemberships_products_posts_pages_settings']" ).val() == 'with_particular_plans' ) {
                        jQuery( ".sumo_restrict_members_with_particular_plan" ).show() ;
                        jQuery( ".sumo_restrict_users_without_particular_plan" ).hide() ;
                        jQuery( ".sumomemberships_all_members_schedule_type" ).hide() ;
                        jQuery( "#sumomemberships_all_members_scheduled_duration_value" ).hide() ;
                        jQuery( "#sumomemberships_all_members_scheduled_duration_period" ).hide() ;
                    } else if( jQuery( "select[name='sumomemberships_products_posts_pages_settings']" ).val() == 'without_particular_plans' ) {
                        jQuery( ".sumo_restrict_users_without_particular_plan" ).show() ;
                        jQuery( ".sumo_restrict_members_with_particular_plan" ).hide() ;
                        jQuery( ".sumomemberships_all_members_schedule_type" ).hide() ;
                        jQuery( "#sumomemberships_all_members_scheduled_duration_value" ).hide() ;
                        jQuery( "#sumomemberships_all_members_scheduled_duration_period" ).hide() ;
                    } else if( jQuery( "select[name='sumomemberships_products_posts_pages_settings']" ).val() == 'all_members' ) {
                        jQuery( ".sumomemberships_all_members_schedule_type" ).show() ;
                        jQuery( ".sumo_restrict_members_with_particular_plan" ).hide() ;
                        jQuery( ".sumo_restrict_users_without_particular_plan" ).hide() ;
                        if( jQuery( "input[name='sumomemberships_all_members_schedule_type']:checked" ).val() == "scheduled" ) {
                            jQuery( "#sumomemberships_all_members_scheduled_duration_value" ).show() ;
                            jQuery( "#sumomemberships_all_members_scheduled_duration_period" ).show() ;
                        } else {
                            jQuery( "#sumomemberships_all_members_scheduled_duration_value" ).hide() ;
                            jQuery( "#sumomemberships_all_members_scheduled_duration_period" ).hide() ;
                        }
                    } else {
                        jQuery( ".sumo_restrict_members_with_particular_plan" ).hide() ;
                        jQuery( ".sumo_restrict_users_without_particular_plan" ).hide() ;
                        jQuery( ".sumomemberships_all_members_schedule_type" ).hide() ;
                        jQuery( "#sumomemberships_all_members_scheduled_duration_value" ).hide() ;
                        jQuery( "#sumomemberships_all_members_scheduled_duration_period" ).hide() ;
                    }
                }

                function ajax_check_table_with_r_without_plan( restriction_type ) {

                    if( restriction_type == "with_particular_plans" || restriction_type == "without_particular_plans" ) {

                        var data = {
                            action : 'sumo_checking_table_with_r_without_plan' ,
                            this_table : restriction_type
                        } ;

                        jQuery.post( "<?php echo admin_url( 'admin-ajax.php' ) ; ?>" , data ,
                                function( response , status ) {
                                    console.log( status ) ;
                                }
                        ) ;
                    }
                }

                show_hide_input_fields() ;

                ajax_check_table_with_r_without_plan( jQuery( "select[name='sumomemberships_products_posts_pages_settings']" ).val() ) ;

                jQuery( "input:radio[name='sumomemberships_all_members_schedule_type']" ).change( function() {

                    if( this.checked && this.value == 'scheduled' ) {
                        jQuery( "#sumomemberships_all_members_scheduled_duration_value" ).show() ;
                        jQuery( "#sumomemberships_all_members_scheduled_duration_period" ).show() ;
                    } else {
                        jQuery( "#sumomemberships_all_members_scheduled_duration_value" ).hide() ;
                        jQuery( "#sumomemberships_all_members_scheduled_duration_period" ).hide() ;
                    }
                } ) ;

                jQuery( "select[name='sumomemberships_products_posts_pages_settings']" ).change( function() {

                    show_hide_input_fields() ;

                    ajax_check_table_with_r_without_plan( jQuery( "select[name='sumomemberships_products_posts_pages_settings']" ).val() ) ;
                } ) ;
            } ) ;
        </script>
        <?php
        $post_type = get_post_type( $this_postid ) ;
        if( $post_type == 'page' ) {
            ?>
            <p>
            <h3><?php _e( 'Display Alternate Content when this Page is Restricted' , 'sumomemberships' ) ?></h3>
            <select id="sumomemberships_display_content_when_restriction_enabled" name="sumomemberships_display_content_when_restriction_enabled">
                <option value="show" <?php if( get_post_meta( $this_postid , 'sumomemberships_display_content_when_restriction_enabled' , true ) == 'show' ) { ?> selected="selected" <?php } ?>>
                    <?php _e( 'Enable' , 'sumomemberships' ) ; ?>
                </option>
                <option value="" <?php if( get_post_meta( $this_postid , 'sumomemberships_display_content_when_restriction_enabled' , true ) == '' ) { ?> selected="selected" <?php } ?>>
                    <?php _e( 'Disable' , 'sumomemberships' ) ; ?>
                </option>
            </select>        </p>

            <?php
            $content_for_reu = get_post_meta( $this_postid , 'sumo_content_for_reu' , true ) ;
            wp_editor( $content_for_reu , 'sumo_content_for_reu' , array( 'media_buttons' => true ) ) ;
        }
    }

    public function AJAX_save_membership_plan_action() {

        if( isset( $_POST[ 'this_postid' ] ) && isset( $_POST[ 'action_to_take' ] ) ) {

            $plan_id               = ( int ) $_POST[ 'this_postid' ] ;
            $plan_status_to_change = $_POST[ 'action_to_take' ] ;

            $plan_purchased_members = sumo_get_plan_purchased_members( $plan_id ) ;

            update_post_meta( $plan_id , 'sumomemberships_plan_status' , $plan_status_to_change ) ;

            foreach( $plan_purchased_members as $each_member_post_id => $each_plan_key ) {

                if( $each_member_post_id > 0 && $each_plan_key != '' ) {

                    $member_id = get_post_meta( $each_member_post_id , 'sumomemberships_userid' , true ) ;

                    if( $plan_status_to_change == "disable" ) {

                        sumo_remove_capability_from_member( $member_id , $plan_id ) ;

                        sumo_pause_r_disable_plan( $plan_id , $each_member_post_id , $member_id ) ;
                    } else {
                        sumo_resume_plan_after_plan_paused_r_disabled( $plan_id , $each_member_post_id , $member_id ) ;

                        sumo_add_capability_to_member( $member_id , $plan_id ) ;
                    }
                }
            }
            echo 'success' ;
        }
        exit ;
    }

    public function AJAX_update_table_row_count_on_click() {
        if( isset( $_POST[ 'row_count' ] ) && isset( $_POST[ 'this_id' ] ) && isset( $_POST[ 'meta_key' ] ) ) {

            update_option( $_POST[ 'meta_key' ] . $_POST[ 'this_id' ] , ( int ) $_POST[ 'row_count' ] ) ;

            echo 'success' ;
        }
        exit ;
    }

    public function AJAX_check_table_with_r_without_plan() {
        if( isset( $_POST[ 'this_table' ] ) ) {

            update_option( 'sumomemberships_check_with_r_without_plan' , $_POST[ 'this_table' ] ) ;

            echo $_POST[ 'this_table' ] ;
        }
        exit ;
    }

    public function AJAX_search_products() {
        $data_store      = WC_Data_Store::load( 'product' ) ;
        $ids             = $data_store->search_products( $_REQUEST[ 'term' ] , '' , true ) ;
        $product_objects = array_filter( array_map( 'wc_get_product' , $ids ) , 'wc_products_array_filter_editable' ) ;
        $products        = array() ;
        if( ! empty( $_GET[ 'exclude' ] ) ) {
            $ids = array_diff( $ids , ( array ) $_GET[ 'exclude' ] ) ;
        }
        foreach( $product_objects as $product_object ) {
            if( function_exists( 'is_sumo_bookings_product' ) ) {
                if( ! is_sumo_bookings_product( $product_object->get_id() ) ) {
                    $products[ $product_object->get_id() ] = rawurldecode( $product_object->get_formatted_name() ) ;
                }
            } else {
                $products[ $product_object->get_id() ] = rawurldecode( $product_object->get_formatted_name() ) ;
            }
        }

        wp_send_json( apply_filters( 'woocommerce_json_search_found_products' , $products ) ) ;
    }

    public function AJAX_add_members_user_image() {

        if( isset( $_POST[ 'userid' ] ) && $_POST[ 'userid' ] != '' ) {

            $user_id  = $_POST[ 'userid' ] ;
            $postid   = $_POST[ 'postid' ] ;
            $args     = array( 'post_type' => 'sumomembers' , 'numberposts' => '-1' , 'meta_query' => array( array( 'key' => 'sumomemberships_userid' , 'compare' => 'EXISTS' , 'value' => $user_id ) ) , 'post_status' => 'published' , 'fields' => 'ids' , 'cache_results' => false ) ;
            $products = get_posts( $args ) ;

            if( empty( $products ) ) {
                $get_user_by = get_user_by( 'id' , $user_id ) ;
                $username    = $get_user_by->user_login ;
                $useremail   = $get_user_by->user_email ;
                update_post_meta( $postid , 'sumomemberships_userid' , $user_id ) ;
                echo admin_url( 'post.php?post=' . $postid . '&action=edit&tab=add_plan_tab' ) ;
            } else {
                wp_delete_post( $postid ) ;

                foreach( $products as $post_id ) {
                    $getpost_id = $post_id ;
                }
                update_post_meta( $getpost_id , 'sumomemberships_userid' , $user_id ) ;

                $oldmetavalue = get_post_meta( $getpost_id , 'sumomemberships_saved_plans' , true ) ;
                if( $oldmetavalue == '' ) {
                    echo admin_url( 'post.php?post=' . $getpost_id . '&action=edit&tab=add_plan_tab' ) ;
                } else {
                    foreach( $oldmetavalue as $uniqid => $values ) {
                        echo admin_url( 'post.php?post=' . $getpost_id . '&action=edit&tab=add_plan_tab' . $uniqid ) ;
                    }
                }
            }
        }
        exit() ;
    }

    public function AJAX_delete_current_table_row() {
        if( isset( $_POST[ 'rowid' ] ) && isset( $_POST[ 'meta_keys' ] ) && isset( $_POST[ 'this_table' ] ) && isset( $_POST[ 'this_id' ] ) ) {

            $meta_keys        = $_POST[ 'meta_keys' ] ;
            $saved_plan_value = get_post_meta( $_POST[ 'this_id' ] , $meta_keys[ 'membership_plan' ] . $_POST[ 'rowid' ] , true ) ;

            if( $_POST[ 'this_table' ] == 'restrict_with_particular_plan' ) {
                $saved_plans = ( array ) get_post_meta( $_POST[ 'this_id' ] , 'sumomemberships_saved_plans_to_restrict_members_for_with' , true ) ;
                unset( $saved_plans[ implode( array_keys( $saved_plans , $saved_plan_value ) ) ] ) ;
                update_post_meta( $_POST[ 'this_id' ] , 'sumomemberships_saved_plans_to_restrict_members_for_with' , $saved_plans ) ;
            } elseif( $_POST[ 'this_table' ] == 'restrict_without_particular_plan' ) {
                $saved_plans = ( array ) get_post_meta( $_POST[ 'this_id' ] , 'sumomemberships_saved_plans_to_restrict_users_for_without' , true ) ;
                unset( $saved_plans[ implode( array_keys( $saved_plans , $saved_plan_value ) ) ] ) ;
                update_post_meta( $_POST[ 'this_id' ] , 'sumomemberships_saved_plans_to_restrict_users_for_without' , $saved_plans ) ;
            } else {
                $saved_plans = ( array ) get_post_meta( $_POST[ 'this_id' ] , 'sumomemberships_saved_additional_linking_plans' , true ) ;
                unset( $saved_plans[ implode( array_keys( $saved_plans , $saved_plan_value ) ) ] ) ;
                update_post_meta( $_POST[ 'this_id' ] , 'sumomemberships_saved_additional_linking_plans' , $saved_plans ) ;
            }

            delete_post_meta( $_POST[ 'this_id' ] , $meta_keys[ 'membership_plan' ] . $_POST[ 'rowid' ] ) ;
            delete_post_meta( $_POST[ 'this_id' ] , $meta_keys[ 'schedule_type' ] . $_POST[ 'rowid' ] ) ;
            delete_post_meta( $_POST[ 'this_id' ] , $meta_keys[ 'duration_value' ] . $_POST[ 'rowid' ] ) ;
            delete_post_meta( $_POST[ 'this_id' ] , $meta_keys[ 'duration_period' ] . $_POST[ 'rowid' ] ) ;
        }
        exit ;
    }

    public function sm_save_values_on_ppm_action( $plan_id ) {
        $members = sumo_get_plan_purchased_members( $plan_id ) ;
        foreach( $members as $member_post_id => $unique_id ) {
            $privileged_link_plans                               = sumo_get_privileged_link_plans( $plan_id , $member_post_id ) ;
            $scheduled_link_plans                                = sumo_get_schedule_link_plans( $plan_id , $member_post_id ) ;
            $saved_plans                                         = get_post_meta( $member_post_id , 'sumomemberships_saved_plans' , true ) ;
            $saved_plans[ $unique_id ][ 'link_plans' ]           = $privileged_link_plans ;
            $saved_plans[ $unique_id ][ 'scheduled_link_plans' ] = $scheduled_link_plans ;
            update_post_meta( $member_post_id , 'sumomemberships_saved_plans' , $saved_plans ) ;
        }
    }
    
    /*
     *  Duplicate action save part.
     */
    public function duplicate_action_save_meta( $duplicate_product , $product ) {

        if( ! is_object( $duplicate_product ) || ! is_object( $product ) ) {
            return ;
        }

        $product_id = $product->get_id() ;

        $duplicate_product_id = $duplicate_product->get_id() ;

        $metas = array(
            'sumomemberships_plan_name' ,
            'sumomemberships_plan_slug' ,
            'sumomemberships_plan_associated_product' ,
            'sumomemberships_duration_type' ,
            'sumomemberships_duration_value' ,
            'sumomemberships_duration_period' ,
            'sumomemberships_saved_plans' ,
            'sumomemberships_products_posts_pages_settings' ,
            'sumomemberships_all_members_schedule_type' ,
            'sumomemberships_all_members_schedule_type' ,
            'sumomemberships_all_members_scheduled_duration_value' ,
            'sumomemberships_all_members_scheduled_duration_period' ,
            'sumomemberships_display_content_when_restriction_enabled' ,
            'sumo_content_for_reu' ,
            'user_purchase_history_for_linking_plans' ,
            'sm_user_purchase_history_period' ,
            'sm_uph_from_period' ,
            'sm_uph_to_period' ,
            'sm_no_of_orders_placed' ,
            'sm_total_amount_spent_in_site' ,
            'sumomemberships_plan_saved'
                ) ;

        foreach( $metas as $meta_name ) {
            $meta_value = get_post_meta( $product_id , $meta_name , true ) ;
            update_post_meta( $duplicate_product_id , $meta_name , $meta_value ) ;
        }

        $this->save_schedule_type_duplicate_action( $duplicate_product_id , $product_id ) ;
        
        $this->save_product_settings_rules_duplicate_action( $duplicate_product_id , $product_id ) ;
    }
    
    /*
     *  Save schedule type in duplicate action.
     */
    public function save_schedule_type_duplicate_action( $duplicate_product_id , $product_id ) {

        $row_counts = get_option( 'sumomemberships_no_of_links_added' . $product_id ) > 0 ? ( int ) get_option( 'sumomemberships_no_of_links_added' . $product_id ) : 0 ;

        for( $i = 1 ; $i <= $row_counts ; $i ++ ) {

            if( get_post_meta( $product_id , 'sumomemberships_linking_plan_schedule_type' . $i , true ) ) {

                update_post_meta( $duplicate_product_id , 'sumomemberships_linking_plan_schedule_type' . $i , 'immediately' ) ;

                if( get_post_meta( $product_id , 'sumomemberships_linking_plan_schedule_type' . $i , true ) == 'scheduled' && get_post_meta( $product_id , 'sumomemberships_linking_plan_duration_value' . $i , true ) > 0 ) {

                    $duration_value  = get_post_meta( $product_id , 'sumomemberships_linking_plan_duration_value' . $i , true ) ;
                    $duration_period = get_post_meta( $product_id , 'sumomemberships_linking_plan_duration_period' . $i , true ) ;

                    if( $duration_value == 1 ) {

                        $duration_period = $duration_period == 'days' ? 'day' : ($duration_period == 'weeks' ? 'week' : ($duration_period == 'months' ? 'month' : 'year')) ;
                    }
                    update_post_meta( $duplicate_product_id , 'sumomemberships_linking_plan_schedule_type' . $i , 'scheduled' ) ;
                    update_post_meta( $duplicate_product_id , 'sumomemberships_linking_plan_duration_value' . $i , $duration_value ) ;
                    update_post_meta( $duplicate_product_id , 'sumomemberships_linking_plan_duration_period' . $i , $duration_period ) ;
                }
            }
        }

        update_post_meta( $duplicate_product_id , 'sumomemberships_saved_additional_linking_plans' , get_post_meta( $product_id , 'sumomemberships_saved_additional_linking_plans' , true ) ) ;
        update_post_meta( $duplicate_product_id , 'sumomemberships_saved_plans_to_restrict_members_for_with' , get_post_meta( $product_id , 'sumomemberships_saved_plans_to_restrict_members_for_with' , true ) ) ;
        update_post_meta( $duplicate_product_id , 'sumomemberships_saved_plans_to_restrict_users_for_without' , get_post_meta( $product_id , 'sumomemberships_saved_plans_to_restrict_users_for_without' , true ) ) ;
    }
    
    /*
     *  Save product settings rules in duplicate action.
     */
    public function save_product_settings_rules_duplicate_action( $duplicate_product_id , $product_id ) {

        $row_counts = 0 ;

        //Check and Save Member With/Without Particular Plans Currently Edited.
        if( $this->check_with_r_without_plan() == 'with' ) {
            $rule_count = count( get_post_meta( $duplicate_product_id , 'sumomemberships_saved_plans_to_restrict_members_for_with' , true ) ) ;
            update_option( 'sumomemberships_restrict_members_with_particular_plan_no_of_rules_added' . $duplicate_product_id , $rule_count ) ;
            $row_counts = get_option( 'sumomemberships_restrict_members_with_particular_plan_no_of_rules_added' . $product_id ) > 0 ? ( int ) get_option( 'sumomemberships_restrict_members_with_particular_plan_no_of_rules_added' . $product_id ) : 0 ;
        } else if( $this->check_with_r_without_plan() == 'without' ) {
            $rule_count = count( get_post_meta( $duplicate_product_id , 'sumomemberships_saved_plans_to_restrict_users_for_without' , true ) ) ;
            update_option( 'sumomemberships_restrict_users_without_particular_plan_no_of_rules_added' . $duplicate_product_id , $rule_count ) ;
            $row_counts = get_option( 'sumomemberships_restrict_users_without_particular_plan_no_of_rules_added' . $product_id ) > 0 ? ( int ) get_option( 'sumomemberships_restrict_users_without_particular_plan_no_of_rules_added' . $product_id ) : 0 ;
        }

        for( $i = 1 ; $i <= $row_counts ; $i ++ ) {

            if( get_post_meta( $product_id , 'sumomemberships_restrict_members_with_particular_plan_schedule_type' . $i , true ) && $this->check_with_r_without_plan() == 'with' ) {

                update_post_meta( $duplicate_product_id , 'sumomemberships_restrict_members_with_particular_plan_schedule_type' . $i , 'immediately' ) ;

                if( get_post_meta( $product_id , 'sumomemberships_restrict_members_with_particular_plan_schedule_type' . $i , true ) == 'scheduled' && get_post_meta( $product_id , 'sumomemberships_restrict_members_with_particular_plan_duration_value' . $i , true ) > 0 ) {

                    $duration_value  = get_post_meta( $product_id , 'sumomemberships_restrict_members_with_particular_plan_duration_value' . $i , true ) ;
                    $duration_period = get_post_meta( $product_id , 'sumomemberships_restrict_members_with_particular_plan_duration_period' . $i , true ) ;

                    if( $duration_value == 1 ) {

                        $duration_period = $duration_period == 'days' ? 'day' : ($duration_period == 'weeks' ? 'week' : ($duration_period == 'months' ? 'month' : 'year')) ;
                    }

                    update_post_meta( $duplicate_product_id , 'sumomemberships_restrict_members_with_particular_plan_schedule_type' . $i , 'scheduled' ) ;
                    update_post_meta( $duplicate_product_id , 'sumomemberships_restrict_members_with_particular_plan_duration_value' . $i , $duration_value ) ;
                    update_post_meta( $duplicate_product_id , 'sumomemberships_restrict_members_with_particular_plan_duration_period' . $i , $duration_period ) ;
                }
            }
        }
    }

    public function save_metabox_values_on_submit( $this_postid , $this_post , $bool ) {
        //Saving Add New/Edit Memberships Plan Posts
        if( isset( $_POST[ 'post_title' ] ) && isset( $_POST[ 'sumomemberships_duration_type' ] ) ) {

            update_post_meta( $this_postid , 'sumomemberships_plan_name' , $_POST[ 'post_title' ] ) ;

            if( strchr( $_POST[ 'post_title' ] , ' ' ) ) {
                $plan_slug = strtolower( str_replace( ' ' , '_' , $_POST[ 'post_title' ] ) ) ;
            } elseif( strchr( $_POST[ 'post_title' ] , '-' ) ) {
                $plan_slug = strtolower( str_replace( '-' , '_' , $_POST[ 'post_title' ] ) ) ;
            } else {
                $plan_slug = strtolower( $_POST[ 'post_title' ] ) ;
            }

            update_post_meta( $this_postid , 'sumomemberships_plan_slug' , $plan_slug ) ;

            preg_match_all( '/\d+/' , $_POST[ 'sumomemberships_plan_associated_product' ] , $matches ) ;

            if( isset( $matches[ 0 ][ 0 ] ) ) {

                $product_id = $matches[ 0 ][ 0 ] ;

                update_post_meta( $this_postid , 'sumomemberships_plan_associated_product' , $product_id ) ;
            }

            update_post_meta( $this_postid , 'sumomemberships_duration_type' , 'unlimited_duration' ) ;

            if( $_POST[ 'sumomemberships_duration_type' ] == 'limited_duration' && $_POST[ 'sumomemberships_duration_value' ] > 0 ) {

                $duration_value  = $_POST[ 'sumomemberships_duration_value' ] ;
                $duration_period = $_POST[ 'sumomemberships_duration_period' ] ;

                if( $duration_value == 1 ) {

                    $duration_period = $duration_period == 'days' ? 'day' : ($duration_period == 'weeks' ? 'week' : ($duration_period == 'months' ? 'month' : 'year')) ;
                }

                update_post_meta( $this_postid , 'sumomemberships_duration_type' , 'limited_duration' ) ;
                update_post_meta( $this_postid , 'sumomemberships_duration_value' , $duration_value ) ;
                update_post_meta( $this_postid , 'sumomemberships_duration_period' , $duration_period ) ;
            }

            $row_counts = get_option( 'sumomemberships_no_of_links_added' . $this_postid ) > 0 ? ( int ) get_option( 'sumomemberships_no_of_links_added' . $this_postid ) : 0 ;

            for( $i = 1 ; $i <= $row_counts ; $i ++ ) {

                if( isset( $_POST[ 'sumomemberships_linking_plan_schedule_type' . $i ] ) ) {

                    $this->save_rules_added( $this_postid , $i , 'sumomemberships_plan_to_link_with' ) ;

                    update_post_meta( $this_postid , 'sumomemberships_linking_plan_schedule_type' . $i , 'immediately' ) ;

                    if( $_POST[ 'sumomemberships_linking_plan_schedule_type' . $i ] == 'scheduled' && $_POST[ 'sumomemberships_linking_plan_duration_value' . $i ] > 0 ) {

                        $duration_value  = $_POST[ 'sumomemberships_linking_plan_duration_value' . $i ] ;
                        $duration_period = $_POST[ 'sumomemberships_linking_plan_duration_period' . $i ] ;

                        if( $duration_value == 1 ) {

                            $duration_period = $duration_period == 'days' ? 'day' : ($duration_period == 'weeks' ? 'week' : ($duration_period == 'months' ? 'month' : 'year')) ;
                        }
                        update_post_meta( $this_postid , 'sumomemberships_linking_plan_schedule_type' . $i , 'scheduled' ) ;
                        update_post_meta( $this_postid , 'sumomemberships_linking_plan_duration_value' . $i , $duration_value ) ;
                        update_post_meta( $this_postid , 'sumomemberships_linking_plan_duration_period' . $i , $duration_period ) ;
                    }
                }
            }
            if( get_option( 'sumo_allow_clp_access_to_oldmembers' , 'no' ) == 'yes' ) {
                do_action( 'sm_save_values_on_ppm' , $this_postid ) ;
            }
        }

        //Saving Every Products/Posts/Pages Settings
        if( isset( $_POST[ 'sumomemberships_products_posts_pages_settings' ] ) ) {

            update_post_meta( $this_postid , 'sumomemberships_products_posts_pages_settings' , $_POST[ 'sumomemberships_products_posts_pages_settings' ] ) ;

            if( isset( $_POST[ 'sumomemberships_all_members_schedule_type' ] ) ) {
                update_post_meta( $this_postid , 'sumomemberships_all_members_schedule_type' , 'immediately' ) ;

                if( $_POST[ 'sumomemberships_all_members_schedule_type' ] == 'scheduled' && $_POST[ 'sumomemberships_all_members_scheduled_duration_value' ] > 0 ) {
                    $duration_value  = $_POST[ 'sumomemberships_all_members_scheduled_duration_value' ] ;
                    $duration_period = $_POST[ 'sumomemberships_all_members_scheduled_duration_period' ] ;

                    if( $duration_value == 1 ) {

                        $duration_period = $duration_period == 'days' ? 'day' : ($duration_period == 'weeks' ? 'week' : ($duration_period == 'months' ? 'month' : 'year')) ;
                    }

                    update_post_meta( $this_postid , 'sumomemberships_all_members_schedule_type' , 'scheduled' ) ;
                    update_post_meta( $this_postid , 'sumomemberships_all_members_scheduled_duration_value' , $duration_value ) ;
                    update_post_meta( $this_postid , 'sumomemberships_all_members_scheduled_duration_period' , $duration_period ) ;
                }
            }

            $row_counts = 0 ;

//Check and Save Member With/Without Particular Plans Currently Edited.
            if( $this->check_with_r_without_plan() == 'with' ) {
                $row_counts = get_option( 'sumomemberships_restrict_members_with_particular_plan_no_of_rules_added' . $this_postid ) > 0 ? ( int ) get_option( 'sumomemberships_restrict_members_with_particular_plan_no_of_rules_added' . $this_postid ) : 0 ;
            } else if( $this->check_with_r_without_plan() == 'without' ) {
                $row_counts = get_option( 'sumomemberships_restrict_users_without_particular_plan_no_of_rules_added' . $this_postid ) > 0 ? ( int ) get_option( 'sumomemberships_restrict_users_without_particular_plan_no_of_rules_added' . $this_postid ) : 0 ;
            }

            for( $i = 1 ; $i <= $row_counts ; $i ++ ) {

                if( isset( $_POST[ 'sumomemberships_restrict_members_with_particular_plan_schedule_type' . $i ] ) && $this->check_with_r_without_plan() == 'with' ) {

                    $this->save_rules_added( $this_postid , $i , 'sumomemberships_restrict_members_with_particular_plan_purchased' ) ;

                    update_post_meta( $this_postid , 'sumomemberships_restrict_members_with_particular_plan_schedule_type' . $i , 'immediately' ) ;

                    if( $_POST[ 'sumomemberships_restrict_members_with_particular_plan_schedule_type' . $i ] == 'scheduled' && $_POST[ 'sumomemberships_restrict_members_with_particular_plan_duration_value' . $i ] > 0 ) {

                        $duration_value  = $_POST[ 'sumomemberships_restrict_members_with_particular_plan_duration_value' . $i ] ;
                        $duration_period = $_POST[ 'sumomemberships_restrict_members_with_particular_plan_duration_period' . $i ] ;

                        if( $duration_value == 1 ) {

                            $duration_period = $duration_period == 'days' ? 'day' : ($duration_period == 'weeks' ? 'week' : ($duration_period == 'months' ? 'month' : 'year')) ;
                        }

                        update_post_meta( $this_postid , 'sumomemberships_restrict_members_with_particular_plan_schedule_type' . $i , 'scheduled' ) ;
                        update_post_meta( $this_postid , 'sumomemberships_restrict_members_with_particular_plan_duration_value' . $i , $duration_value ) ;
                        update_post_meta( $this_postid , 'sumomemberships_restrict_members_with_particular_plan_duration_period' . $i , $duration_period ) ;
                    }
                }

                if( isset( $_POST[ 'sumomemberships_restrict_users_without_particular_plan_purchased' . $i ] ) && $this->check_with_r_without_plan() == 'without' ) {

                    $this->save_rules_added( $this_postid , $i , 'sumomemberships_restrict_users_without_particular_plan_purchased' ) ;
                }
            }
        }

        if( isset( $_POST[ 'sumomemberships_display_content_when_restriction_enabled' ] ) ) {
            update_post_meta( $this_postid , 'sumomemberships_display_content_when_restriction_enabled' , $_POST[ 'sumomemberships_display_content_when_restriction_enabled' ] ) ;
            update_post_meta( $this_postid , 'sumo_content_for_reu' , $_POST[ 'sumo_content_for_reu' ] ) ;
        }

//Saving For Members Post Type Section
        if( isset( $_POST[ 'post_type' ] ) || (isset( $_POST[ 'sumomember_plan_meta' ] ) || isset( $_POST[ 'sumomember_plan_metas' ] )) ) {

            if( $_POST[ 'post_type' ] == 'sumomembers' ) {
                if( get_post_meta( $this_postid , 'sumomemberships_userid' , true ) != '' ) {
                    remove_action( 'save_post' , array( $this , 'save_metabox_values_on_submit' ) ) ;
                    $new_array   = isset( $_POST[ 'sumomember_plan_meta' ] ) ? $_POST[ 'sumomember_plan_meta' ] : array() ;
                    $add_array   = isset( $_POST[ 'sumomember_plan_metas' ] ) ? $_POST[ 'sumomember_plan_metas' ] : array() ;
                    $merge_array = ( array ) $new_array + $add_array ;

                    do_action( 'sumomemberships_before_saving_members_plan_data' , $merge_array , $this_postid ) ;
                    $key = substr( $_POST[ '_wp_http_referer' ] , strpos( $_POST[ '_wp_http_referer' ] , "add_plan_tab" ) + 12 ) ;
                    foreach( $add_array as $eacharray ) {
                        
                    }
                    $my_array = $key ? $new_array[ $key ] : $eacharray ;
                    if( $key ) {
                        $saved_plans = get_post_meta( $this_postid , 'sumomemberships_saved_plans' , true ) ;
                        if( is_array( $saved_plans ) && ! empty( $saved_plans ) && SUMOMemberships_Account_Page::check_is_data_available( $this_postid ) ) {
                            if( $saved_plans[ $key ][ 'choose_status' ] != $my_array[ 'choose_status' ] ) {
                                do_action( 'sumomemberships_plan_status_changed' , $this_postid , $my_array[ 'choose_plan' ] , $my_array[ 'choose_status' ] ) ;
                            }
                        }
                    } else {
                        if( $my_array[ 'choose_plan' ] ) {
                            do_action( 'sumomemberships_plan_status_changed' , $this_postid , $my_array[ 'choose_plan' ] , $my_array[ 'choose_status' ] ) ;
                        }
                    }

                    $newarray   = isset( $_POST[ 'sumomember_plan_meta' ] ) ? $_POST[ 'sumomember_plan_meta' ] : array() ;
                    $addarray   = isset( $_POST[ 'sumomember_plan_metas' ] ) ? $_POST[ 'sumomember_plan_metas' ] : array() ;
                    $mergearray = ( array ) $newarray + $addarray ;

                    update_post_meta( $this_postid , 'sumomemberships_saved_plans' , $mergearray ) ;

                    wp_update_post( array( 'ID' => $this_postid , 'post_status' => 'publish' ) ) ;

                    do_action( 'sumomemberships_plan_saved' , $merge_array , $this_postid ) ;
                }
            }
            if( $_POST[ 'post_type' ] == 'sumomembershipplans' ) {
                $user_purchase_history_for_linking_plans = isset( $_POST[ 'user_purchase_history_for_linking_plans' ] ) ? $_POST[ 'user_purchase_history_for_linking_plans' ] : '' ;
                $sm_user_purchase_history_period         = isset( $_POST[ 'sm_user_purchase_history_period' ] ) ? $_POST[ 'sm_user_purchase_history_period' ] : '' ;
                $sm_uph_from_period                      = isset( $_POST[ 'sm_uph_from_period' ] ) ? $_POST[ 'sm_uph_from_period' ] : '' ;
                $sm_uph_to_period                        = isset( $_POST[ 'sm_uph_to_period' ] ) ? $_POST[ 'sm_uph_to_period' ] : '' ;
                $sm_no_of_orders_placed                  = isset( $_POST[ 'sm_no_of_orders_placed' ] ) ? $_POST[ 'sm_no_of_orders_placed' ] : '' ;
                $sm_total_amount_spent_in_site           = isset( $_POST[ 'sm_total_amount_spent_in_site' ] ) ? $_POST[ 'sm_total_amount_spent_in_site' ] : '' ;
                update_post_meta( $this_postid , 'user_purchase_history_for_linking_plans' , $user_purchase_history_for_linking_plans ) ;
                update_post_meta( $this_postid , 'sm_user_purchase_history_period' , $sm_user_purchase_history_period ) ;
                update_post_meta( $this_postid , 'sm_uph_from_period' , $sm_uph_from_period ) ;
                update_post_meta( $this_postid , 'sm_uph_to_period' , $sm_uph_to_period ) ;
                update_post_meta( $this_postid , 'sm_no_of_orders_placed' , $sm_no_of_orders_placed ) ;
                update_post_meta( $this_postid , 'sm_total_amount_spent_in_site' , $sm_total_amount_spent_in_site ) ;
            }
        }
    }

//Saving Multiple Rules/Links Added.
    public function save_rules_added( $this_postid , $i , $key ) {
        if( $key == 'sumomemberships_restrict_members_with_particular_plan_purchased' ) {
            $meta_key = 'sumomemberships_saved_plans_to_restrict_members_for_with' ;
        } elseif( $key == 'sumomemberships_restrict_users_without_particular_plan_purchased' ) {
            $meta_key = 'sumomemberships_saved_plans_to_restrict_users_for_without' ;
        } else {
            $meta_key = 'sumomemberships_saved_additional_linking_plans' ;
        }

        $check_previous_saved_values = ( array ) get_post_meta( $this_postid , $meta_key , true ) ;

        $this->unset_if_membership_plan_trashed_from_saved_rules( $this_postid , $check_previous_saved_values , $meta_key ) ;

        if( ! in_array( $_POST[ $key . $i ] , $check_previous_saved_values ) ) {

            $saved_plans      = $check_previous_saved_values ;
            $saved_plan_value = get_post_meta( $this_postid , $key . $i , true ) ;

            if( $saved_plan_value > 0 ) {
                unset( $saved_plans[ implode( array_keys( $saved_plans , $saved_plan_value ) ) ] ) ;
                update_post_meta( $this_postid , $meta_key , $saved_plans ) ;
            }

            update_post_meta( $this_postid , $key . $i , $_POST[ $key . $i ] ) ;
        }

        $new_value_to_save     = array( $_POST[ $key . $i ] ) ;
        $previous_saved_values = get_post_meta( $this_postid , $meta_key , true ) ;

        if( is_array( $previous_saved_values ) ) {
            $merged_values = array_merge( $new_value_to_save , $previous_saved_values ) ;
            update_post_meta( $this_postid , $meta_key , array_unique( $merged_values ) ) ;
        } else {
            update_post_meta( $this_postid , $meta_key , $new_value_to_save ) ;
        }
    }

    public function unset_if_membership_plan_trashed_from_saved_rules( $this_postid , $saved_rules , $meta_key ) {
        $membership_levels = sumo_get_membership_levels() ;

        if( is_array( $saved_rules ) && ! empty( $saved_rules ) ) {

            foreach( $saved_rules as $each_key => $each_plan_id ) {

                if( ! in_array( $each_plan_id , array_keys( $membership_levels ) ) ) {
                    unset( $saved_rules[ $each_key ] ) ;
                }
            }
            update_post_meta( $this_postid , $meta_key , $saved_rules ) ;
        }
    }

//Checking On Save/Publish
    public function check_with_r_without_plan() {
        if( get_option( 'sumomemberships_check_with_r_without_plan' ) == 'with_particular_plans' ) {
            return 'with' ;
        } else if( get_option( 'sumomemberships_check_with_r_without_plan' ) == 'without_particular_plans' ) {
            return 'without' ;
        }
        return '' ;
    }

    public function is_table_row_saved( $this_postid , $meta_key , $this_row = '' ) {
        if( $this_row != '' ) {

            if( get_post_meta( $this_postid , $meta_key[ 'membership_plan' ] . $this_row , true ) != '' ) {
                return true ;
            }
            return false ;
        } else {

            $row_counts = get_option( $meta_key[ 'no_of_rules_added' ] . $this_postid ) ;

            for( $i = 1 ; $i <= $row_counts ; $i ++ ) {

                if( get_post_meta( $this_postid , $meta_key[ 'membership_plan' ] . $i , true ) != '' ) {
                    return true ;
                }
            }
            return false ;
        }
    }

    public function redirect_post_location( $location , $postid ) {
        if( $_POST[ 'post_type' ] == 'sumomembers' ) {
            $oldmetavalue = get_post_meta( $postid , 'sumomemberships_saved_plans' , true ) ;
            if( is_array( $oldmetavalue ) ) {
                foreach( $oldmetavalue as $key => $value ) {
                    if( isset( $value[ 'choose_plan' ] ) && $value[ 'choose_plan' ] != '' ) {
                        $location = admin_url( 'post.php?post=' . $postid . '&action=edit&tab=add_plan_tab' . $key ) ;
                    }
                }
            } else {
                $location = admin_url( 'post.php?post=' . $postid . '&action=edit&tab=add_plan_tab' ) ;
            }
            return $location ;
        } else {
            return $location ;
        }
    }

    public function options_to_filter_plans( $this_postid , $meta_key , $i , $each_level_key , $each_level_value , $previous_saved_values ) {
        if( get_post_meta( $this_postid , $meta_key[ 'membership_plan' ] . $i , true ) == $each_level_key ) {
            ?>
            <option value="<?php echo $each_level_key ; ?>" selected="selected">
                <?php _e( $each_level_value , 'sumomemberships' ) ; ?>
            </option>
        <?php } elseif( ! in_array( $each_level_key , $previous_saved_values ) ) { ?>
            <option value="<?php echo $each_level_key ; ?>">
                <?php _e( $each_level_value , 'sumomemberships' ) ; ?>
            </option>
            <?php
        }
    }

    public function is_membership_plan_exists( $this_postid , $meta_key , $i ) {
        $plan_id = get_post_meta( $this_postid , $meta_key[ 'membership_plan' ] . $i , true ) ;

        return array_key_exists( $plan_id , sumo_get_membership_levels() ) ;
    }

    public function display_add_rule_table( $this_postid , $meta_key ) {
        $membership_levels = sumo_get_membership_levels() ;

        if( $meta_key[ 'table_name' ] == 'sumo_restrict_members_with_particular_plan' ) {
            $is_table = 'restrict_with_particular_plan' ;
            $this->unset_if_membership_plan_trashed_from_saved_rules( $this_postid , get_post_meta( $this_postid , 'sumomemberships_saved_plans_to_restrict_members_for_with' , true ) , 'sumomemberships_saved_plans_to_restrict_members_for_with' ) ;
        } elseif( $meta_key[ 'table_name' ] == 'sumo_restrict_users_without_particular_plan' ) {
            $is_table = 'restrict_without_particular_plan' ;
            $this->unset_if_membership_plan_trashed_from_saved_rules( $this_postid , get_post_meta( $this_postid , 'sumomemberships_saved_plans_to_restrict_users_for_without' , true ) , 'sumomemberships_saved_plans_to_restrict_users_for_without' ) ;
        } else {
            $is_table = 'linking_additional_plans' ;
            $this->unset_if_membership_plan_trashed_from_saved_rules( $this_postid , get_post_meta( $this_postid , 'sumomemberships_saved_additional_linking_plans' , true ) , 'sumomemberships_saved_additional_linking_plans' ) ;
        }

        $saved_linking_plans          = get_post_meta( $this_postid , 'sumomemberships_saved_additional_linking_plans' , true ) == "" ? array() : get_post_meta( $this_postid , 'sumomemberships_saved_additional_linking_plans' , true ) ;
        $saved_plans_restrict_with    = get_post_meta( $this_postid , 'sumomemberships_saved_plans_to_restrict_members_for_with' , true ) == "" ? array() : get_post_meta( $this_postid , 'sumomemberships_saved_plans_to_restrict_members_for_with' , true ) ;
        $saved_plans_restrict_without = get_post_meta( $this_postid , 'sumomemberships_saved_plans_to_restrict_users_for_without' , true ) == "" ? array() : get_post_meta( $this_postid , 'sumomemberships_saved_plans_to_restrict_users_for_without' , true ) ;

        $row_counts = get_option( $meta_key[ 'no_of_rules_added' ] . $this_postid ) != '' ? ( int ) get_option( $meta_key[ 'no_of_rules_added' ] . $this_postid ) : 0 ;
        ?>
        <table class="widefat wc_input_table <?php echo $meta_key[ 'table_name' ] ; ?>" style="border-spacing: 10px;">
            <tbody>
                <?php
                if( ! $this->is_table_row_saved( $this_postid , $meta_key ) ) {
                    delete_option( $meta_key[ 'no_of_rules_added' ] . $this_postid ) ;
                }

                if( $row_counts > 0 ) {

                    for( $i = 1 ; $i <= $row_counts ; $i ++ ) {

                        if( $this->is_table_row_saved( $this_postid , $meta_key , $i ) && $this->is_membership_plan_exists( $this_postid , $meta_key , $i ) ) {
                            ?>
                            <tr>
                                <td></td>
                                <td>
                                    <select name="<?php echo $meta_key[ 'membership_plan' ] . $i ; ?>" id="<?php echo $meta_key[ 'membership_plan' ] . $i ; ?>">
                                        <?php
                                        foreach( $membership_levels as $each_level_key => $each_level_value ) {
                                            if( $is_table == 'restrict_with_particular_plan' ) {

                                                $this->options_to_filter_plans( $this_postid , $meta_key , $i , $each_level_key , $each_level_value , $saved_plans_restrict_with ) ;
                                            } else if( $is_table == 'restrict_without_particular_plan' ) {

                                                $this->options_to_filter_plans( $this_postid , $meta_key , $i , $each_level_key , $each_level_value , $saved_plans_restrict_without ) ;
                                            } else {
                                                if( $this_postid != $each_level_key ) {

                                                    $this->options_to_filter_plans( $this_postid , $meta_key , $i , $each_level_key , $each_level_value , $saved_linking_plans ) ;
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </td>
                                <?php if( $is_table != 'restrict_without_particular_plan' ) { ?>
                                    <td>
                                        <input type="radio" name="<?php echo $meta_key[ 'schedule_type' ] . $i ; ?>" id="<?php echo $meta_key[ 'schedule_type' ] . $i ; ?>" value="immediately" checked>
                                        <?php _e( 'Immediately' , 'sumomemberships' ) ; ?>
                                        <input type="radio" name="<?php echo $meta_key[ 'schedule_type' ] . $i ; ?>" id="<?php echo $meta_key[ 'schedule_type' ] . $i ; ?>" value="scheduled"
                                               <?php if( get_post_meta( $this_postid , $meta_key[ 'schedule_type' ] . $i , true ) == 'scheduled' ) { ?> checked = "checked" <?php } ?>>
                                               <?php _e( 'Scheduled' , 'sumomemberships' ) ; ?>
                                        &nbsp;
                                        <input type="number" min="1" style="width: 15%" name="<?php echo $meta_key[ 'duration_value' ] . $i ; ?>" id="<?php echo $meta_key[ 'duration_value' ] . $i ; ?>" value="<?php echo get_post_meta( $this_postid , $meta_key[ 'duration_value' ] . $i , true ) ; ?>">
                                        <select id="<?php echo $meta_key[ 'duration_period' ] . $i ; ?>" name="<?php echo $meta_key[ 'duration_period' ] . $i ; ?>">
                                            <option value="days"
                                                    <?php if( get_post_meta( $this_postid , $meta_key[ 'duration_period' ] . $i , true ) == 'day' || get_post_meta( $this_postid , $meta_key[ 'duration_period' ] . $i , true ) == 'days' ) { ?> selected="selected" <?php } ?>>
                                                        <?php get_post_meta( $this_postid , $meta_key[ 'duration_period' ] . $i , true ) == 'day' ? _e( 'Day' , 'sumomemberships' ) : _e( 'Days' , 'sumomemberships' ) ; ?>
                                            </option>
                                            <option value="weeks"
                                                    <?php if( get_post_meta( $this_postid , $meta_key[ 'duration_period' ] . $i , true ) == 'week' || get_post_meta( $this_postid , $meta_key[ 'duration_period' ] . $i , true ) == 'weeks' ) { ?> selected="selected" <?php } ?>>
                                                        <?php get_post_meta( $this_postid , $meta_key[ 'duration_period' ] . $i , true ) == 'week' ? _e( 'Week' , 'sumomemberships' ) : _e( 'Weeks' , 'sumomemberships' ) ; ?>
                                            </option>
                                            <option value="months"
                                                    <?php if( get_post_meta( $this_postid , $meta_key[ 'duration_period' ] . $i , true ) == 'month' || get_post_meta( $this_postid , $meta_key[ 'duration_period' ] . $i , true ) == 'months' ) { ?> selected="selected" <?php } ?>>
                                                        <?php get_post_meta( $this_postid , $meta_key[ 'duration_period' ] . $i , true ) == 'month' ? _e( 'Month' , 'sumomemberships' ) : _e( 'Months' , 'sumomemberships' ) ; ?>
                                            </option>
                                            <option value="years"
                                                    <?php if( get_post_meta( $this_postid , $meta_key[ 'duration_period' ] . $i , true ) == 'year' || get_post_meta( $this_postid , $meta_key[ 'duration_period' ] . $i , true ) == 'years' ) { ?> selected="selected" <?php } ?>>
                                                        <?php get_post_meta( $this_postid , $meta_key[ 'duration_period' ] . $i , true ) == 'year' ? _e( 'Year' , 'sumomemberships' ) : _e( 'Years' , 'sumomemberships' ) ; ?>
                                            </option>
                                        </select>
                                    </td>
                                <?php } ?>
                                <td>
                                    <input type="button" class="button-primary <?php echo $meta_key[ 'remove_rule' ] ; ?>" data-rowid="<?php echo $i ; ?>"
                                           value="<?php
                                           if( $is_table == 'linking_additional_plans' ) {
                                               echo 'Remove Link' ;
                                           } else {
                                               echo 'Remove Rule' ;
                                           }
                                           ?>">
                                </td>
                            </tr>
                            <?php
                        }
                    }
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4">
                        <?php
                        if( $is_table == 'restrict_with_particular_plan' ) {
                            if( count( $saved_plans_restrict_with ) == count( $membership_levels ) ) {
                                ?>
                                <input type="button" disabled="true" style="cursor:not-allowed" class="button-primary <?php echo $meta_key[ 'add_rule' ] ; ?>" value="Add Rule">
                            <?php } else { ?>
                                <input type="button" class="button-primary <?php echo $meta_key[ 'add_rule' ] ; ?>" value="Add Rule">
                                <?php
                            }
                        } elseif( $is_table == 'restrict_without_particular_plan' ) {
                            if( count( $saved_plans_restrict_without ) == count( $membership_levels ) ) {
                                ?>
                                <input type="button" disabled="true" style="cursor:not-allowed" class="button-primary <?php echo $meta_key[ 'add_rule' ] ; ?>" value="Add Rule">
                            <?php } else { ?>
                                <input type="button" class="button-primary <?php echo $meta_key[ 'add_rule' ] ; ?>" value="Add Rule">
                                <?php
                            }
                        } else {
                            if( count( $membership_levels ) === 0 || (count( $saved_linking_plans ) == (count( $membership_levels ) - 1)) ) {
                                ?>
                                <input type="button" disabled="true" style="cursor:not-allowed" class="button-primary <?php echo $meta_key[ 'add_rule' ] ; ?>" value="Add Linking">
                            <?php } else { ?>
                                <input type="button" class="button-primary <?php echo $meta_key[ 'add_rule' ] ; ?>" value="Add Linking">
                                <?php
                            }
                        }
                        ?>
                    </th>
                </tr>
            </tfoot>
        </table>
        <script type="text/javascript">
            jQuery( document ).ready( function() {

                if( !jQuery( ".<?php echo $meta_key[ 'remove_rule' ] ; ?>" ).is( ":visible" ) ) {

                    var addrow = jQuery(
                            '<tr>\n\
                               <td class="<?php echo $meta_key[ 'no_rules_added' ] ; ?>" style="width:20%;">Currently there are no Links.</td>\n\
                            </tr>' ) ;
                    jQuery( 'table.<?php echo $meta_key[ 'table_name' ] ; ?>' ).append( addrow ) ;

                }

                function show_hide_duration( i ) {

        <?php if( $is_table != 'restrict_without_particular_plan' ) { ?>

                        if( jQuery( "input[name='<?php echo $meta_key[ 'schedule_type' ] ; ?>" + i + "']:checked" ).val() == "scheduled" ) {
                            jQuery( "#<?php echo $meta_key[ 'duration_value' ] ; ?>" + i ).show() ;
                            jQuery( "#<?php echo $meta_key[ 'duration_period' ] ; ?>" + i ).show() ;
                        } else {
                            jQuery( "#<?php echo $meta_key[ 'duration_value' ] ; ?>" + i ).hide() ;
                            jQuery( "#<?php echo $meta_key[ 'duration_period' ] ; ?>" + i ).hide() ;
                        }

                        jQuery( "input:radio[name='<?php echo $meta_key[ 'schedule_type' ] ; ?>" + i + "']" ).change( function() {

                            if( this.checked && this.value == 'scheduled' ) {
                                jQuery( "#<?php echo $meta_key[ 'duration_value' ] ; ?>" + i ).show() ;
                                jQuery( "#<?php echo $meta_key[ 'duration_period' ] ; ?>" + i ).show() ;
                            } else {
                                jQuery( "#<?php echo $meta_key[ 'duration_value' ] ; ?>" + i ).hide() ;
                                jQuery( "#<?php echo $meta_key[ 'duration_period' ] ; ?>" + i ).hide() ;
                            }
                        } ) ;
        <?php } ?>
                }

                jQuery( ".<?php echo $meta_key[ 'table_name' ] ; ?> .<?php echo $meta_key[ 'remove_rule' ] ; ?>" ).click( function() {

                    jQuery( this ).parents( 'tr:first' ).remove() ;

                    var data = {
                        action : 'sumo_delete_current_plan_linking_table_row' ,
                        rowid : jQuery( this ).attr( 'data-rowid' ) ,
                        this_id : "<?php echo $this_postid ; ?>" ,
                        this_table : "<?php echo $is_table ; ?>" ,
                        meta_keys : <?php echo json_encode( $meta_key ) ; ?>
                    } ;

                    jQuery.post( "<?php echo admin_url( 'admin-ajax.php' ) ; ?>" , data ,
                            function( response , status ) {
                                console.log( status ) ;

                                if( status == 'success' ) {

                                    window.location.reload() ;

                                }
                            }
                    ) ;
                } ) ;

                var row_count = "<?php echo get_option( $meta_key[ 'no_of_rules_added' ] . $this_postid ) != '' ? ( int ) get_option( $meta_key[ 'no_of_rules_added' ] . $this_postid ) : 0 ; ?>" ;

                jQuery( ".<?php echo $meta_key[ 'add_rule' ] ; ?>" ).click( function( event ) {

                    event.preventDefault() ;
                    row_count++ ;
                    jQuery( ".<?php echo $meta_key[ 'no_rules_added' ] ; ?>" ).hide() ;

                    var data = {
                        action : 'sumo_update_plan_linking_table_row_count_on_click' ,
                        row_count : row_count ,
                        this_id : "<?php echo $this_postid ; ?>" ,
                        meta_key : "<?php echo $meta_key[ 'no_of_rules_added' ] ; ?>" ,
                    } ;

                    jQuery.post( "<?php echo admin_url( 'admin-ajax.php' ) ; ?>" , data ,
                            function( response ) {

                                console.log( response ) ;

                                if( response.trim() == 'success' ) {

                                    var add_rule = jQuery( '<tr>\n\
                        <td></td>\n\
                        <td>\n\
                            <select id="<?php echo $meta_key[ 'membership_plan' ] ; ?>' + row_count + '" name="<?php echo $meta_key[ 'membership_plan' ] ; ?>' + row_count + '">\n\
        <?php
        foreach( $membership_levels as $each_level_key => $each_level_value ) {
            if( $is_table == 'linking_additional_plans' ) {
                if( $this_postid != $each_level_key ) {
                    ?><option value="<?php echo esc_attr( $each_level_key ) ; ?>"><?php echo esc_html( $each_level_value ) ; ?></option>\n\
                    <?php
                }
            } else {
                ?><option value="<?php echo esc_attr($each_level_key) ; ?>"><?php echo esc_html( $each_level_value ) ; ?></option>\n\
                <?php
            }
        }
        ?> \n\
                            </select>\n\
                        </td>\n\
        <?php if( $is_table != 'restrict_without_particular_plan' ) { ?>\n\
                                                <td>\n\
                                                <input type="radio" name="<?php echo $meta_key[ 'schedule_type' ] ; ?>' + row_count + '" id="<?php echo $meta_key[ 'schedule_type' ] ; ?>' + row_count + '" value="immediately" checked>Immediately\n\
                                                <input type="radio" name="<?php echo $meta_key[ 'schedule_type' ] ; ?>' + row_count + '" id="<?php echo $meta_key[ 'schedule_type' ] ; ?>' + row_count + '" value="scheduled">Scheduled\n\
                                                &nbsp;\n\
                                                <input type="number" min="1" style="width: 15%" name="<?php echo $meta_key[ 'duration_value' ] ; ?>' + row_count + '" id="<?php echo $meta_key[ 'duration_value' ] ; ?>' + row_count + '" style="width: 28%;border:2px solid gainsboro;" value="">\n\
                                                <select id="<?php echo $meta_key[ 'duration_period' ] ; ?>' + row_count + '" name="<?php echo $meta_key[ 'duration_period' ] ; ?>' + row_count + '">\n\
                                                <option value="days">Days</option>\n\
                                                <option value="weeks">Weeks</option>\n\
                                                <option value="months">Months</option>\n\
                                                <option value="years">Years</option>\n\
                                                </select>\n\
                                                </td> \n\
        <?php } ?>\n\
                        <td>\n\
                            <input type="button" class="button-primary <?php echo $meta_key[ 'remove_rule' ] ; ?>" data-rowid="' + row_count + '" \n\
                                value="<?php
        if( $is_table == 'linking_additional_plans' ) {
            echo 'Remove Link' ;
        } else {
            echo 'Remove Rule' ;
        }
        ?>">\n\
                        </td>\n\
                    </tr>' ) ;
                                    jQuery( "table.<?php echo $meta_key[ 'table_name' ] ; ?>" ).append( add_rule ) ;

                                    jQuery( ".<?php echo $meta_key[ 'table_name' ] ; ?> .<?php echo $meta_key[ 'remove_rule' ] ; ?>" ).click( function() {

                                        jQuery( this ).parents( 'tr:first' ).remove() ;

                                        if( !jQuery( ".<?php echo $meta_key[ 'remove_rule' ] ; ?>" ).is( ":visible" ) ) {

                                            window.location.reload() ;
                                        }
                                    } ) ;

                                    show_hide_duration( row_count ) ;
                                }
                            } ) ;
                } ) ;

                for( var i = 1 ; i <= row_count ; i++ ) {

                    ( function( i ) {

                        show_hide_duration( i ) ;
                    }
                    ( i ) ) ;
                }
            } ) ;
        </script>
        <?php
    }

    public function add_filters_to_members( $post_type ) {
        $membership_level = sumo_get_membership_levels() ;

        if( $post_type == 'sumomembers' ) {
            ?>
            <select name="sumomemberships_choose_plan" class="sumomemberships_choose_plan">
                <option value="">Choose Plan</option>
                <?php
                foreach( $membership_level as $key => $value ) {
                    ?>
                    <option value="<?php echo $key ; ?>" <?php if( isset( $_GET[ 'sumomemberships_choose_plan' ] ) && $_GET[ 'sumomemberships_choose_plan' ] == $key ) { ?>selected="selected"<?php } ?>><?php echo $value ; ?></option>
                    <?php
                }
                ?>
            </select>
            <select name="sumomemberships_choose_status" class="sumomemberships_choose_status">
                <option value="">Choose Status</option>
                <option value="active" <?php if( isset( $_GET[ 'sumomemberships_choose_status' ] ) && $_GET[ 'sumomemberships_choose_status' ] == 'active' ) { ?>selected="selected"<?php } ?>><?php _e( 'Active' , 'sumomemberships' ) ?></option>
                <option value="paused" <?php if( isset( $_GET[ 'sumomemberships_choose_status' ] ) && $_GET[ 'sumomemberships_choose_status' ] == 'paused' ) { ?>selected="selected"<?php } ?>><?php _e( 'Paused' , 'sumomemberships' ) ?></option>
                <option value="cancelled" <?php if( isset( $_GET[ 'sumomemberships_choose_status' ] ) && $_GET[ 'sumomemberships_choose_status' ] == 'cancelled' ) { ?>selected="selected"<?php } ?>><?php _e( 'Cancelled' , 'sumomemberships' ) ?></option>
            </select>
            <?php
        }
    }

    public function pre_get_posts_sorting( $where , $wp_query ) {
        global $wpdb ;
        if( is_admin() ) {
            if( isset( $_REQUEST[ 'filter_action' ] ) && $_REQUEST[ 'post_type' ] == 'sumomembers' ) {
                $selectplan     = isset( $_REQUEST[ 'sumomemberships_choose_plan' ] ) ? $_REQUEST[ 'sumomemberships_choose_plan' ] : null ;
                $selectedstatus = isset( $_REQUEST[ 'sumomemberships_choose_status' ] ) ? $_REQUEST[ 'sumomemberships_choose_status' ] : null ;

                if( $wp_query->query_vars[ 'post_type' ] == 'sumomembers' ) {
                    if( $selectplan ) {
                        $tablename    = $wpdb->prefix . "posts" ;
                        $final_result = array() ;
                        if( is_array( $final_result ) ) {
                            $result = $wpdb->get_results( "SELECT ID from $tablename where post_type='sumomembers'" ) ;
                            if( is_array( $result ) && ! empty( $result ) ) {
                                foreach( $result as $post_id ) {
                                    $final_result[] = self::get_seleted_plan( $post_id->ID , $selectplan ) ;
                                }
                            }
                        }
                        $array          = array_filter( array_unique( $final_result ) ) ;
                        $imploded_array = implode( "','" , $array ) ;
                        $where          .= " AND $tablename.ID IN ('" . $imploded_array . "')" ;
                    }

                    if( $selectedstatus ) {
                        $tablename    = $wpdb->prefix . "posts" ;
                        $final_result = array() ;
                        if( is_array( $final_result ) ) {
                            $result = $wpdb->get_results( "SELECT ID from $tablename where post_type='sumomembers'" ) ;
                            if( is_array( $result ) && ! empty( $result ) ) {
                                foreach( $result as $post_id ) {
                                    $final_result[] = self::get_seleted_status( $post_id->ID , $selectedstatus ) ;
                                }
                            }
                        }

                        $array          = array_values(array_filter( array_unique( $final_result ) ) );
                        $imploded_array = implode( "','" , $array ) ;
                        $where          .= " AND $tablename.ID IN ('" . $imploded_array . "')" ;
                    }
                }
            }
        }
        return $where ;
    }

    public function get_seleted_plan( $post_id , $plan_id ) {
        $get_post_meta = get_post_meta( $post_id , 'sumomemberships_saved_plans' , true ) ;

        if( is_array( $get_post_meta ) ) {
            foreach( $get_post_meta as $key => $value ) {
                if( !empty($value['choose_plan']) && $plan_id == $value['choose_plan'] ) {
                    return $post_id ;
                }
            }
            return false ;
        }
    }

    public function get_seleted_status( $post_id , $plan_status ) {
        $get_post_meta = get_post_meta( $post_id , 'sumomemberships_saved_plans' , true ) ;

        if( is_array( $get_post_meta ) ) {
            foreach( $get_post_meta as $key => $value ) {
                if( !empty($value['choose_plan']) && $plan_status == $value['choose_status'] ) {
                    return $post_id ;
                }
            }
            return false ;
        }
    }

    public function select_user_to_add_member() {
        global $woocommerce ;
        if( isset( $_GET[ 'post_type' ] ) ) {
            if( $_GET[ 'post_type' ] == 'sumomembers' ) {
                ?>
                <table>
                    <?php
                    if( ( float ) $woocommerce->version <= ( float ) ('2.2.0') ) {
                        echo $this->get_user( 'sumomemberships_select_user_to_add_member' ) ;
                        ?>
                        <tr valign="top">
                            <th class="titledesc" scope="row">
                                <label for="sumomemberships_select_user_to_add_member"><?php _e( 'Choose User to Add Member' , 'sumomemberships' ) ; ?></label>
                            </th>
                            <td>
                                <select name='sumomemberships_select_user_to_add_member' style="width: 300px;" class='sumomemberships_select_user_to_add_member' id='sumomemberships_select_user_to_add_member'>
                                    <?php
                                    $json_ids = array() ;

                                    $getuser = get_option( 'sumomemberships_select_user_to_add_member' ) ;
                                    if( $getuser != '' ) {
                                        $listofuser = $getuser ;
                                        if( ! is_array( $listofuser ) ) {
                                            $userids = array_filter( array_map( 'absint' , ( array ) explode( ',' , $listofuser ) ) ) ;
                                        } else {
                                            $userids = $listofuser ;
                                        }
                                        foreach( $userids as $userid ) {
                                            $user = get_user_by( 'id' , $userid ) ;
                                            ?>
                                            <option value='<?php echo $userid ; ?>' selected='selected'><?php echo esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email ) . ')' ; ?></option>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <option value=''></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>
                                <input type="button" value="Add Member" id="sumomemberships_add_user_as_member" name="sumomemberships_add_user_as_member" class="sumomemberships_add_user_as_member"/>
                            </td>
                        </tr>
                        <?php
                    } else {
                        ?>
                        <tr valign="top">
                            <th class="titledesc" scope="row">
                                <label for="sumomemberships_select_user_to_add_member"><?php _e( 'Choose User to Add Member' , 'sumomemberships' ) ; ?></label>
                            </th>
                            <td>
                                <?php
                                $getuser = get_option( 'sumomemberships_select_user_to_add_member' ) ;
                                if( WC()->version < 3.0 ) {
                                    ?>
                                    <input type='hidden'style="width: 300px;" class='wc-customer-search' name='sumomemberships_select_user_to_add_member' id='sumomemberships_select_user_to_add_member'  data-placeholder='<?php _e( 'Search Users' , 'sumomemberships' ) ; ?>' data-selected='<?php
                                    $json_ids = array() ;
                                    if( $getuser != '' ) {
                                        $listofuser = $getuser ;
                                        if( ! is_array( $listofuser ) ) {
                                            $userids = array_filter( array_map( 'absint' , ( array ) explode( ',' , $listofuser ) ) ) ;
                                        } else {
                                            $userids = $listofuser ;
                                        }

                                        foreach( $userids as $userid ) {
                                            $user                  = get_user_by( 'id' , $userid ) ;
                                            $json_ids[ $user->ID ] = esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email ) . ')' ;
                                        }
                                        echo esc_attr( json_encode( $json_ids ) ) ;
                                    }
                                    ?>' value='<?php echo implode( ',' , array_keys( $json_ids ) ) ; ?>' data-allow_clear='true' />
                                           <?php
                                       } else {
                                           if( $getuser != '' ) {
                                               $listofuser = $getuser ;
                                               if( ! is_array( $listofuser ) ) {
                                                   $userids = array_filter( array_map( 'absint' , ( array ) explode( ',' , $listofuser ) ) ) ;
                                               } else {
                                                   $userids = $listofuser ;
                                               }
                                           }
                                           ?>
                                    <select class="wc-customer-search" data-minimum_input_length="3" id="sumomemberships_select_user_to_add_member" style="width: 300px;" name="sumomemberships_select_user_to_add_member" data-placeholder="<?php esc_attr_e( 'Search for a customer&hellip;' , 'woocommerce' ) ; ?>" data-allow_clear="true">
                                        <?php
                                        if( ! empty( $userids ) ) {
                                            foreach( $userids as $userid ) {
                                                $user        = get_user_by( 'id' , $userid ) ;
                                                $user_string = esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email ) . ')' ;
                                                ?>
                                                <option value="<?php echo esc_attr( $userid ) ; ?>" selected="selected"><?php echo htmlspecialchars( $user_string ) ; ?><option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                    </select>
                                <?php }
                                ?>
                            </td>
                            <td>
                                <input type="button" value="Add Member" id="sumomemberships_add_user_as_member" name="sumomemberships_add_user_as_member" class="sumomemberships_add_user_as_member"/>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
                <?php
            }
        }
    }

    public function get_user( $ajaxid ) {
        global $woocommerce ;
        ob_start() ;
        ?>
        <script type="text/javascript">
        <?php if( ( float ) $woocommerce->version <= ( float ) ('2.2.0') ) { ?>
                jQuery( function() {

                    jQuery( 'select.<?php echo $ajaxid ; ?>' ).ajaxChosen( {
                        method : 'GET' ,
                        url : '<?php echo admin_url( 'admin-ajax.php' ) ; ?>' ,
                        dataType : 'json' ,
                        afterTypeDelay : 100 ,
                        data : {
                            action : 'woocommerce_json_search_customers' ,
                            security : '<?php echo wp_create_nonce( "search-customers" ) ; ?>'
                        }
                    } , function( data ) {
                        var terms = { } ;

                        jQuery.each( data , function( i , val ) {
                            terms[i] = val ;
                        } ) ;
                        return terms ;
                    } ) ;
                } ) ;
        <?php } ?>
        </script>
        <?php
        return ob_get_clean() ;
    }

    public function do_some_action_on_manual_updations( $new_data , $postid ) {

        $existing_data     = get_post_meta( $postid , 'sumomemberships_saved_plans' , true ) ;
        $previous_value    = sumo_multidimensional_array_difference( $existing_data , $new_data ) ;
        $post_updated_plan = sumo_multidimensional_array_difference( $new_data , $existing_data ) ;
        $get_plan_keys     = array() ;

        if( is_array( $new_data ) && ! empty( $new_data ) ) {
            foreach( $new_data as $new_unqkey => $newvalue ) {
                if( $newvalue[ 'choose_plan' ] != '' ) {
                    $get_plan_keys[] = $new_unqkey ;
                }
            }
        }

        $new_plan_updation = array() ;



        if( ! empty( $previous_value ) && is_array( $previous_value ) ) {
            $previous_plan_get_keys = array_keys( ( array ) $previous_value ) ;
            $difference_keys        = array_diff( $get_plan_keys , $previous_plan_get_keys ) ;

            if( is_array( $difference_keys ) && ! empty( $difference_keys ) ) {
                do_action( 'sumomemberships_manual_new_plan_addition' , $difference_keys , $new_data , $postid ) ;
            }
            foreach( $previous_value as $each_unique_key => $each_val ) {
                if( is_array( $previous_value[ $each_unique_key ] ) && ! empty( $previous_value[ $each_unique_key ] ) ) {
                    if( is_array( $each_val ) && ! empty( $each_val ) ) {

                        if( isset( $each_val[ 'choose_plan' ] ) ) {

                            $previous_plan_id = ( int ) $each_val[ 'choose_plan' ] ;

                            $new_plan_id = ( int ) $post_updated_plan[ $each_unique_key ][ 'choose_plan' ] ;

                            do_action( 'sumomemberships_manual_plan_updation' , $previous_plan_id , $new_plan_id , $postid , $each_unique_key ) ;
                        }
                        if( isset( $each_val[ 'choose_status' ] ) ) {

                            $previous_plan_status = $each_val[ 'choose_status' ] ;

                            $new_plan_status = $post_updated_plan[ $each_unique_key ][ 'choose_status' ] ;

                            do_action( 'sumomemberships_manual_plan_status_updation' , $previous_plan_status , $new_plan_status , $postid , $each_unique_key ) ;
                        }
                        if( isset( $each_val[ 'to_date' ] ) ) {

                            $previous_plan_expiry_date = isset( $each_val[ 'to_date' ] ) ? $each_val[ 'to_date' ] : "" ;

                            $new_plan_expiry_date = isset( $post_updated_plan[ $each_unique_key ][ 'to_date' ] ) ? $post_updated_plan[ $each_unique_key ][ 'to_date' ] : "" ;

                            do_action( 'sumomemberships_manual_plan_expiry_date_updation' , $previous_plan_expiry_date , $new_plan_expiry_date , $postid , $each_unique_key ) ;
                        }
                    }
                }
            }
        } else {
// new plan updation
            do_action( 'sumomemberships_manual_first_new_plan_addition' , $get_plan_keys , $new_data , $postid ) ;
        }
    }

// add capabilities for manual plan updation.
    public function add_capabilities_to_member_for_manual_updation( $plan_id_array , $post_id ) {

        if( ! isset( $plan_id_array ) || ! isset( $post_id ) )
            return ;

        $member_id = ( int ) get_post_meta( $post_id , 'sumomemberships_userid' , true ) ;

        if( is_array( $plan_id_array ) ) {
            foreach( $plan_id_array as $plan_ids ) {
                if( ! empty( $plan_ids ) ) {
                    $plan_id   = $plan_ids[ 'choose_plan' ] ;
                    $plan_slug = get_post_meta( $plan_id , 'sumomemberships_plan_slug' , true ) ;
                    sumo_add_capability_to_member( $member_id , $plan_id , $plan_slug ) ;
                }
            }
        }

        return ;
    }

    public function do_some_action_on_manual_plan_update( $previous_plan_id , $new_plan_id , $postid , $unique_key ) {

        $saved_plans = get_post_meta( $postid , 'sumomemberships_saved_plans' , true ) ;
        $member_id   = ( int ) get_post_meta( $postid , 'sumomemberships_userid' , true ) ;
        $this_status = $_POST[ 'sumomember_plan_meta' ][ $unique_key ][ 'choose_status' ] ;
        if( $this_status == 'active' ) {

            sumo_clear_linked_plans_privilege_cron( $postid , $unique_key , $member_id , $previous_plan_id ) ;

            $_POST[ 'sumomember_plan_meta' ][ $unique_key ][ 'plan_slug' ]            = '' ;
            $_POST[ 'sumomember_plan_meta' ][ $unique_key ][ 'link_plans' ]           = array() ;
            $_POST[ 'sumomember_plan_meta' ][ $unique_key ][ 'scheduled_link_plans' ] = array() ;

            $plan_slug = get_post_meta( $new_plan_id , 'sumomemberships_plan_slug' , true ) ;

            $_POST[ 'sumomember_plan_meta' ][ $unique_key ][ 'plan_slug' ] = $plan_slug ;

            $new_privileged_link_plans = sumo_get_privileged_link_plans( $new_plan_id , $member_id ) ;
            $new_scheduled_link_plans  = sumo_get_schedule_link_plans( $new_plan_id , $member_id ) ;

            $_POST[ 'sumomember_plan_meta' ][ $unique_key ][ 'link_plans' ]           = $new_privileged_link_plans ;
            $_POST[ 'sumomember_plan_meta' ][ $unique_key ][ 'scheduled_link_plans' ] = $new_scheduled_link_plans ;

            $previous_expire_duration_timestamp = wp_next_scheduled( 'sumo_memberships_process_plan_duration_validity' , array( $member_id , ( int ) $previous_plan_id ) ) ;

            if( $previous_expire_duration_timestamp > 0 ) {
                if( ! wp_next_scheduled( 'sumo_memberships_process_plan_duration_validity' , array( $member_id , ( int ) $new_plan_id ) ) ) {
                    wp_schedule_single_event( $previous_expire_duration_timestamp , 'sumo_memberships_process_plan_duration_validity' , array( $member_id , ( int ) $new_plan_id ) ) ;

                    wp_clear_scheduled_hook( 'sumo_memberships_process_plan_duration_validity' , array( $member_id , ( int ) $previous_plan_id ) ) ;
                }
            }
        }
    }

    public function do_some_action_on_manual_plan_status_update( $previous_plan_status , $new_plan_status , $postid , $unique_key ) {
        $saved_plans = get_post_meta( $postid , 'sumomemberships_saved_plans' , true ) ;
        $member_id   = ( int ) get_post_meta( $postid , 'sumomemberships_userid' , true ) ;
        $plan_id     = $_POST[ 'sumomember_plan_meta' ][ $unique_key ][ 'choose_plan' ] ;

        if( $previous_plan_status == 'active' && $new_plan_status == 'paused' ) {

            sumo_remove_capability_from_member( $member_id , $plan_id ) ;

            $expire_duration = $_POST[ 'sumomember_plan_meta' ][ $unique_key ][ 'to_date' ] ;

            if( $expire_duration != '' ) {

                $timestamp = strtotime( $expire_duration ) > 0 ? strtotime( $expire_duration ) - time() : '' ;

                $_POST[ 'sumomember_plan_meta' ][ $unique_key ][ 'available_duration' ] = $timestamp ;
                $_POST[ 'sumomember_plan_meta' ][ $unique_key ][ 'to_date' ]            = '--' ;
            }
            wp_clear_scheduled_hook( 'sumo_memberships_process_plan_duration_validity' , array( $member_id , ( int ) $plan_id ) ) ;

            sumo_clear_linked_plans_privilege_cron( $postid , $unique_key , $member_id , $plan_id ) ;

            $_POST[ 'sumomember_plan_meta' ][ $unique_key ][ 'link_plans' ]           = array() ;
            $_POST[ 'sumomember_plan_meta' ][ $unique_key ][ 'scheduled_link_plans' ] = array() ;
        } else if( $previous_plan_status == 'paused' && $new_plan_status == 'active' ) {

            $available_duration = $_POST[ 'sumomember_plan_meta' ][ $unique_key ][ 'available_duration' ] ;

            if( $available_duration > 0 ) {
                $timestamp = ( int ) $available_duration + time() ;

                if( $timestamp > 0 ) {
                    if( ! wp_next_scheduled( 'sumo_memberships_process_plan_duration_validity' , array( $member_id , ( int ) $plan_id ) ) ) {
                        wp_schedule_single_event( $timestamp , 'sumo_memberships_process_plan_duration_validity' , array( $member_id , ( int ) $plan_id ) ) ;

                        $_POST[ 'sumomember_plan_meta' ][ $unique_key ][ 'to_date' ]            = date( 'Y-m-d h:i:s' , $timestamp ) ;
                        $_POST[ 'sumomember_plan_meta' ][ $unique_key ][ 'available_duration' ] = "" ;
                    }
                }
            } else {
                $_POST[ 'sumomember_plan_meta' ][ $unique_key ][ 'to_date' ] = '' ;
            }

            $new_privileged_link_plans = sumo_get_privileged_link_plans( $plan_id , $member_id ) ;
            $new_scheduled_link_plans  = sumo_get_schedule_link_plans( $plan_id , $member_id ) ;

            $_POST[ 'sumomember_plan_meta' ][ $unique_key ][ 'link_plans' ]           = $new_privileged_link_plans ;
            $_POST[ 'sumomember_plan_meta' ][ $unique_key ][ 'scheduled_link_plans' ] = $new_scheduled_link_plans ;

            $plan_slug = get_post_meta( $plan_id , 'sumomemberships_plan_slug' , true ) ;

            $_POST[ 'sumomember_plan_meta' ][ $unique_key ][ 'plan_slug' ] = $plan_slug ;

            sumo_add_capability_to_member( $member_id , $plan_id ) ;
        } else {
            sumo_remove_capability_from_member( $member_id , $plan_id ) ;

            $_POST[ 'sumomember_plan_meta' ][ $unique_key ][ 'to_date' ]            = '' ;
            $_POST[ 'sumomember_plan_meta' ][ $unique_key ][ 'available_duration' ] = '' ;

            wp_clear_scheduled_hook( 'sumo_memberships_process_plan_duration_validity' , array( $member_id , ( int ) $plan_id ) ) ;

            sumo_clear_linked_plans_privilege_cron( $postid , $unique_key , $member_id , $plan_id ) ;

            $_POST[ 'sumomember_plan_meta' ][ $unique_key ][ 'link_plans' ]           = array() ;
            $_POST[ 'sumomember_plan_meta' ][ $unique_key ][ 'scheduled_link_plans' ] = array() ;
        }
    }

    public function do_some_action_on_manual_plan_expiry_date_update( $previous_plan_expiry_date , $new_plan_expiry_date , $postid , $unique_key ) {
        $saved_plans      = get_post_meta( $postid , 'sumomemberships_saved_plans' , true ) ;
        $member_id        = ( int ) get_post_meta( $postid , 'sumomemberships_userid' , true ) ;
        $plan_id          = $_POST[ 'sumomember_plan_meta' ][ $unique_key ][ 'choose_plan' ] ;
        $this_plan_status = $_POST[ 'sumomember_plan_meta' ][ $unique_key ][ 'choose_status' ] ;

        if( $this_plan_status == 'active' ) {

            $modified_duration = $_POST[ 'sumomember_plan_meta' ][ $unique_key ][ 'to_date' ] ;

            if( $modified_duration != '' ) {

                $modified_duration_timestamp = strtotime( $modified_duration . ' ' . date( 'h:i:s' ) ) ;

                $timestamp = $modified_duration_timestamp > 0 ? ( int ) $modified_duration_timestamp : 0 ;

                if( $timestamp > 0 ) {

                    wp_clear_scheduled_hook( 'sumo_memberships_process_plan_duration_validity' , array( $member_id , ( int ) $plan_id ) ) ;

                    if( ! wp_next_scheduled( 'sumo_memberships_process_plan_duration_validity' , array( $member_id , ( int ) $plan_id ) ) ) {
                        wp_schedule_single_event( $timestamp , 'sumo_memberships_process_plan_duration_validity' , array( $member_id , ( int ) $plan_id ) ) ;

                        $_POST[ 'sumomember_plan_meta' ][ $unique_key ][ 'to_date' ] = date( 'Y-m-d h:i:s' , $timestamp ) ;

                        $new_scheduled_link_plans  = sumo_get_schedule_link_plans( $plan_id , $member_id ) ;
                        $new_privileged_link_plans = sumo_get_privileged_link_plans( $plan_id , $member_id ) ;

                        $_POST[ 'sumomember_plan_meta' ][ $eachkey ][ 'link_plans' ]           = $new_privileged_link_plans ;
                        $_POST[ 'sumomember_plan_meta' ][ $eachkey ][ 'scheduled_link_plans' ] = $new_scheduled_link_plans ;
                    }
                }
            }
        }
    }

}

new SUMOMemberships_Admin_Meta_Boxes() ;

function sumo_members_export_email_check_query_having_posts( $args ) {
    $post       = array() ;
    $query_post = new WP_Query( $args ) ;
    if( isset( $query_post->posts ) ) {
        if( is_array( $query_post->posts ) && ! empty( $query_post->posts ) ) {
            $post = $query_post->posts ;
        }
    }
    return $post ;
}
