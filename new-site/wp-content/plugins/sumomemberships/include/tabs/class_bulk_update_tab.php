<?php

class SUMOBulkUpdate_Settings_Tab {

    public function __construct() {

        add_filter( 'woocommerce_sumomemberships_settings_tabs_array' , array ( $this , 'general_tab_setting' ) ) ; // Register a New Tab in a WooCommerce
        add_action( 'woocommerce_sumomemberships_settings_tabs_bulk_update' , array ( $this , 'register_admin_settings' ) ) ; // Call to register the admin settings in the Plugin Submenu with general settings tab
        add_action( 'woocommerce_admin_field_sumomemberships_bulk_update_content' , array ( $this , 'sm_bulkupdate_content' ) ) ;

        add_action( 'wp_ajax_sm_bulk_update_action' , array ( __CLASS__ , 'perform_bulk_process_action' ) ) ;

        add_action( 'wp_ajax_sm_json_update_product_restrictions' , array ( __CLASS__ , 'perform_final_bulk_process_action' ) ) ;

        add_action( 'admin_head' , array ( __CLASS__ , 'sumo_memberships_custom_search' ) ) ;

        add_action( 'wp_ajax_sumo_search_custom_post_types' , array ( $this , 'sumo_AJAX_search_custom_post_types' ) ) ;
    }

    /*
     * Function to Define Name of the Tab
     */

    public static function general_tab_setting( $setting_tabs ) {
        if ( ! is_array( $setting_tabs ) )
            $setting_tabs                = ( array ) $setting_tabs ;
        $setting_tabs[ 'bulk_update' ] = __( 'Bulk Update' , 'sumomemberships' ) ;
        return array_filter( $setting_tabs ) ;
    }

    public static function perform_bulk_process_action() {
        $products    = array () ;
        $post_type   = $_POST[ 'post_type' ] ;
        $offset      = $_POST[ 'offset' ] ;
        $product_ids = isset( $_POST[ 'select_products_for_restriction' ] ) ? $_POST[ 'select_products_for_restriction' ] : '' ;
        $categories  = isset( $_POST[ 'select_product_categories_for_restriction' ] ) ? $_POST[ 'select_product_categories_for_restriction' ] : "" ;
        $tags        = $_POST[ 'select_product_tags_for_restriction' ] ;
        if ( $_POST[ 'sm_bulk_product_restriction_select' ] == 'all_' . $post_type . 's' ) {
            $args     = array ( 'post_type' => $post_type , 'posts_per_page' => '10' , 'offset' => $offset , 'post_status' => 'publish' , 'fields' => 'ids' , 'cache_results' => false ) ;
            $products = get_posts( $args ) ;
        } elseif ( $_POST[ 'sm_bulk_product_restriction_select' ] == 'include_' . $post_type . 's' ) {
            $include = is_array( $product_ids ) ? $product_ids : explode( ',' , $product_ids ) ;
            $include = array_filter( $include ) ;
            if ( ! empty( $include ) ) {
                $args     = array ( 'post_type' => $post_type , 'posts_per_page' => '10' , 'offset' => $offset , 'post__in' => $include , 'post_status' => 'publish' , 'fields' => 'ids' , 'cache_results' => false ) ;
                $products = get_posts( $args ) ;
            }
        } elseif ( $_POST[ 'sm_bulk_product_restriction_select' ] == 'exclude_' . $post_type . 's' ) {
            $exclude = is_array( $product_ids ) ? $product_ids : explode( ',' , $product_ids ) ;
            $exclude = array_filter( $exclude ) ;
            if ( ! empty( $exclude ) ) {
                $args     = array ( 'post_type' => $post_type , 'posts_per_page' => '10' , 'offset' => $offset , 'post__not_in' => $exclude , 'post_status' => 'publish' , 'fields' => 'ids' , 'cache_results' => false ) ;
                $products = get_posts( $args ) ;
            }
        } elseif ( $_POST[ 'sm_bulk_product_restriction_select' ] == 'all_categories' ) {
            $tax_args       = array (
                'taxonomy'       => $post_type . '_cat' ,
                'posts_per_page' => '-1' ,
                'hide_empty'     => 0 ,
                'fields'         => 'slugs'
            ) ;
            $all_categories = get_categories( $tax_args ) ;
            $args           = array ( 'post_type' => $post_type , 'posts_per_page' => '10' , 'offset' => $offset , $post_type . '_cat' => implode( ',' , $all_categories ) , 'post_status' => 'publish' , 'fields' => 'ids' , 'cache_results' => false ) ;
            $products       = get_posts( $args ) ;
        } elseif ( $_POST[ 'sm_bulk_product_restriction_select' ] == 'include_categories' ) {
            $categories          = is_array( $categories ) ? $categories : explode( ',' , $categories ) ;
            $filtered_categories = array_filter( $categories ) ;
            if ( ! empty( $filtered_categories ) ) {
                $args     = array ( 'post_type'      => $post_type ,
                    'posts_per_page' => '10' ,
                    'offset'         => $offset ,
                    'post_status'    => 'publish' ,
                    'fields'         => 'ids' ,
                    'tax_query'      => array ( array (
                            'taxonomy' => $post_type . '_cat' ,
                            'field'    => 'ids' ,
                            'terms'    => $filtered_categories ,
                            'operator' => 'IN'
                        ) ) ,
                    'cache_results'  => false ) ;
                $products = get_posts( $args ) ;
            }
        } elseif ( $_POST[ 'sm_bulk_product_restriction_select' ] == 'exclude_categories' ) {
            $categories          = is_array( $categories ) ? $categories : explode( ',' , $categories ) ;
            $filtered_categories = array_filter( $categories ) ;
            if ( ! empty( $filtered_categories ) ) {
                $args     = array ( 'post_type'      => $post_type ,
                    'posts_per_page' => '10' ,
                    'offset'         => $offset ,
                    'post_status'    => 'publish' ,
                    'fields'         => 'ids' ,
                    'tax_query'      => array ( array (
                            'taxonomy' => $post_type . '_cat' ,
                            'field'    => 'ids' ,
                            'terms'    => $filtered_categories ,
                            'operator' => 'OUT'
                        ) ) ,
                    'cache_results'  => false ) ;
                $products = get_posts( $args ) ;
            }
        } elseif ( $_POST[ 'sm_bulk_product_restriction_select' ] == 'all_tags' ) {
            $tax_args = array (
                'taxonomy'       => $post_type . '_tag' ,
                'posts_per_page' => '-1' ,
                'hide_empty'     => 0 ,
                'fields'         => 'slugs'
            ) ;
            $all_tags = get_categories( $tax_args ) ;
            $args     = array ( 'post_type' => $post_type , 'posts_per_page' => '10' , 'offset' => $offset , $post_type . '_tag' => implode( ',' , $all_tags ) , 'post_status' => 'publish' , 'fields' => 'ids' , 'cache_results' => false ) ;
            $products = get_posts( $args ) ;
        } elseif ( $_POST[ 'sm_bulk_product_restriction_select' ] == 'include_tags' ) {
            $tags          = is_array( $tags ) ? $tags : explode( ',' , $tags ) ;
            $filtered_tags = array_filter( $tags ) ;
            if ( ! empty( $filtered_tags ) ) {
                $args     = array ( 'post_type'      => $post_type ,
                    'posts_per_page' => '10' ,
                    'offset'         => $offset ,
                    'post_status'    => 'publish' ,
                    'fields'         => 'ids' ,
                    'tax_query'      => array ( array (
                            'taxonomy' => $post_type . '_tag' ,
                            'field'    => 'ids' ,
                            'terms'    => $filtered_tags ,
                            'operator' => 'IN'
                        ) ) ,
                    'cache_results'  => false ) ;
                $products = get_posts( $args ) ;
            }
        } elseif ( $_POST[ 'sm_bulk_product_restriction_select' ] == 'exclude_tags' ) {
            $tags          = is_array( $tags ) ? $tags : explode( ',' , $tags ) ;
            $filtered_tags = array_filter( $tags ) ;
            if ( ! empty( $filtered_tags ) ) {
                $args     = array ( 'post_type'      => $post_type ,
                    'posts_per_page' => '10' ,
                    'offset'         => $offset ,
                    'post_status'    => 'publish' ,
                    'fields'         => 'ids' ,
                    'tax_query'      => array ( array (
                            'taxonomy' => $post_type . '_tag' ,
                            'field'    => 'ids' ,
                            'terms'    => $filtered_tags ,
                            'operator' => 'OUT'
                        ) ) ,
                    'cache_results'  => false ) ;
                $products = get_posts( $args ) ;
            }
        }
        $new_offset   = $offset + 10 ;
        $return_array = array ( 'ids' => $products , 'offset' => $new_offset ) ;
        echo json_encode( $return_array ) ;
        exit() ;
    }

    public static function perform_final_bulk_process_action() {
        if ( isset( $_POST[ 'ids' ] ) ) {
            $product_ids = is_array( $_POST[ 'ids' ] ) ? $_POST[ 'ids' ] : explode( ',' , $_POST[ 'ids' ] ) ;
            foreach ( $product_ids as $each_product_id ) {
                if ( isset( $_POST[ 'sumomemberships_bulk_product_settings' ] ) ) {
                    $object = new SUMOMemberships_Admin_Meta_Boxes() ;
                    update_post_meta( $each_product_id , 'sumomemberships_products_posts_pages_settings' , $_POST[ 'sumomemberships_bulk_product_settings' ] ) ;

                    if ( isset( $_POST[ 'sumomemberships_all_members_schedule_type' ] ) ) {
                        update_post_meta( $each_product_id , 'sumomemberships_all_members_schedule_type' , 'immediately' ) ;

                        if ( $_POST[ 'sumomemberships_all_members_schedule_type' ] == 'scheduled' && $_POST[ 'sumomemberships_all_members_scheduled_duration_value' ] > 0 ) {
                            $duration_value  = $_POST[ 'sumomemberships_all_members_scheduled_duration_value' ] ;
                            $duration_period = $_POST[ 'sumomemberships_all_members_scheduled_duration_period' ] ;

                            if ( $duration_value == 1 ) {

                                $duration_period = $duration_period == 'days' ? 'day' : ($duration_period == 'weeks' ? 'week' : ($duration_period == 'months' ? 'month' : 'year')) ;
                            }

                            update_post_meta( $each_product_id , 'sumomemberships_all_members_schedule_type' , 'scheduled' ) ;
                            update_post_meta( $each_product_id , 'sumomemberships_all_members_scheduled_duration_value' , $duration_value ) ;
                            update_post_meta( $each_product_id , 'sumomemberships_all_members_scheduled_duration_period' , $duration_period ) ;
                        }
                    }

                    $row_counts = 0 ;

//Check and Save Member With/Without Particular Plans Currently Edited.
                    if ( $_POST[ 'sumomemberships_bulk_product_settings' ] == 'with_particular_plans' ) {
                        $row_counts = isset( $_POST[ 'with_rowcount' ] ) ? $_POST[ 'with_rowcount' ] : 0 ;
                        update_option( 'sumomemberships_restrict_members_with_particular_plan_no_of_rules_added' . $each_product_id , $row_counts ) ;
                    } else if ( $_POST[ 'sumomemberships_bulk_product_settings' ] == 'without_particular_plans' ) {
                        $row_counts = isset( $_POST[ 'without_rowcount' ] ) ? $_POST[ 'without_rowcount' ] : 0 ;
                        update_option( 'sumomemberships_restrict_users_without_particular_plan_no_of_rules_added' . $each_product_id , $row_counts ) ;
                    }

                    for ( $i = 1 ; $i <= $row_counts ; $i ++  ) {

                        if ( isset( $_POST[ 'sumomemberships_restrict_members_with_particular_plan_schedule_type' . $i ] ) && $_POST[ 'sumomemberships_bulk_product_settings' ] == 'with_particular_plans' ) {

                            self::save_rules_added_to_product_by_bulk_actions( $each_product_id , $i , 'sumomemberships_restrict_members_with_particular_plan_purchased' , $_POST ) ;

                            update_post_meta( $each_product_id , 'sumomemberships_restrict_members_with_particular_plan_schedule_type' . $i , 'immediately' ) ;

                            if ( $_POST[ 'sumomemberships_restrict_members_with_particular_plan_schedule_type' . $i ] == 'scheduled' && $_POST[ 'sumomemberships_restrict_members_with_particular_plan_duration_value' . $i ] > 0 ) {

                                $duration_value  = $_POST[ 'sumomemberships_restrict_members_with_particular_plan_duration_value' . $i ] ;
                                $duration_period = $_POST[ 'sumomemberships_restrict_members_with_particular_plan_duration_period' . $i ] ;

                                if ( $duration_value == 1 ) {

                                    $duration_period = $duration_period == 'days' ? 'day' : ($duration_period == 'weeks' ? 'week' : ($duration_period == 'months' ? 'month' : 'year')) ;
                                }

                                update_post_meta( $each_product_id , 'sumomemberships_restrict_members_with_particular_plan_schedule_type' . $i , 'scheduled' ) ;
                                update_post_meta( $each_product_id , 'sumomemberships_restrict_members_with_particular_plan_duration_value' . $i , $duration_value ) ;
                                update_post_meta( $each_product_id , 'sumomemberships_restrict_members_with_particular_plan_duration_period' . $i , $duration_period ) ;
                            }
                        }

                        if ( isset( $_POST[ 'sumomemberships_restrict_users_without_particular_plan_purchased' . $i ] ) && $_POST[ 'sumomemberships_bulk_product_settings' ] == 'without_particular_plans' ) {

                            self::save_rules_added_to_product_by_bulk_actions( $each_product_id , $i , 'sumomemberships_restrict_users_without_particular_plan_purchased' , $_POST ) ;
                        }
                    }
                }
            }
        }
        exit() ;
    }

    public static function sm_bulkupdate_content() {
        ?>
        <tbody>
            <tr valign="top" >
                <th class="titledesc" scope="row">
                    <label><?php _e( 'Post Type' , 'sumomemberships' ) ?></label>
                </th>
                <td class="forminp forminp-select">
                    <select id="sm_bulk_post_type_restriction_select" name="sm_bulk_post_type_restriction_select">
                        <?php
                        $post_type_array = array ( 'page' , 'post' , 'product' ) ;
                        $post_types      = sumo_get_third_parties_cpt_exists() ;
                        foreach ( $post_types as $type ) {
                            if ( get_option( "sumomemberships_$type" ) == "yes" ) {
                                $post_type_array[] = $type ;
                            }
                        }
                        foreach ( $post_type_array as $each_post_type ) {
                            ?>
                            <option value="<?php echo $each_post_type ?>"><?php _e( ucfirst( $each_post_type ) , 'sumomemberships' ) ; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <?php
            echo self::sumo_memberhships_product_dds_bu() ;
            echo self::sumo_memberhships_page_dds_bu() ;
            echo self::sumo_memberhships_post_dds_bu() ;
            foreach ( $post_types as $posttype ) {
                if ( get_option( "sumomemberships_$posttype" ) == "yes" ) {
                    echo self::sumo_memberhships_custom_dds_bu( $posttype ) ;
                }
            }
            ?>
            <tr valign="top">
                <th class="titledesc" scope="row">
                    <label><?php _e( 'Available for' , 'sumomemberships' ) ?></label>
                </th>
                <td class="forminp forminp-select">
                    <select id="sumomemberships_bulk_product_settings" name="sumomemberships_bulk_product_settings">
                        <option value="all_users">
                            <?php _e( 'Accessible by All Users' , 'sumomemberships' ) ; ?>
                        </option>
                        <option value="with_particular_plans">
                            <?php _e( 'Members with Particular Plans' , 'sumomemberships' ) ; ?>
                        </option>
                        <option value="without_particular_plans">
                            <?php _e( 'Users without Particular Plans' , 'sumomemberships' ) ; ?>
                        </option>
                        <option value="all_members">
                            <?php _e( 'All Members' , 'sumomemberships' ) ; ?>
                        </option>
                        <option value="all_non_members">
                            <?php _e( 'All Non Members' , 'sumomemberships' ) ; ?>
                        </option>
                    </select>
                </td>
            </tr>
            <tr>
                <th class="titledesc" scope="row"></th>
                <td class="titledesc sumomemberships_all_members_schedule_type" scope="row">
                    <label><?php _e( 'Any Plan :' , 'sumomemberships' ) ; ?> &nbsp;
                        <input type="radio" name="sumomemberships_all_members_schedule_type" id="sumomemberships_all_members_schedule_type" value="immediately" checked>
                        <?php _e( 'Immediately' , 'sumomemberships' ) ; ?>
                        <input type="radio" name="sumomemberships_all_members_schedule_type" id="sumomemberships_all_members_schedule_type" value="scheduled">
                        <?php _e( 'Scheduled' , 'sumomemberships' ) ; ?>
                    </label>
                </td>
                <td class="forminp forminp-select">
                    <input type="number" min="1" style="width: 15%" name="sumomemberships_all_members_scheduled_duration_value" id="sumomemberships_all_members_scheduled_duration_value" value="">
                    <select id="sumomemberships_all_members_scheduled_duration_period" name="sumomemberships_all_members_scheduled_duration_period">
                        <option value="days"><?php _e( 'Day' , 'sumomemberships' ) ; ?></option>
                        <option value="weeks"><?php _e( 'Week' , 'sumomemberships' ) ; ?></option>
                        <option value="months"><?php _e( 'Month' , 'sumomemberships' ) ; ?></option>
                        <option value="years"><?php _e( 'Year' , 'sumomemberships' ) ; ?></option>
                    </select>
                </td>
            </tr>
            <!--                        </tbody>
                                </table>-->
            <?php
            $with_particular_plan_meta_key = array (
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
            $object                        = new SUMOMemberships_Admin_Meta_Boxes() ;

            echo $object->display_add_rule_table( 'bulk_product' , $with_particular_plan_meta_key ) ;

            $without_particular_plan_meta_key = array (
                'table_name'        => 'sumo_restrict_users_without_particular_plan' ,
                'no_rules_added'    => 'sumo_restrict_users_without_particular_plan_no_rules_added' ,
                'add_rule'          => 'sumo_restrict_users_without_particular_plan_add_rule' ,
                'remove_rule'       => 'sumo_restrict_users_without_particular_plan_remove_rule' ,
                'no_of_rules_added' => 'sumomemberships_restrict_users_without_particular_plan_no_of_rules_added' ,
                'membership_plan'   => 'sumomemberships_restrict_users_without_particular_plan_purchased' ,
            ) ;

            echo $object->display_add_rule_table( 'bulk_product' , $without_particular_plan_meta_key ) ;
            ?>
        <input type="button" name="sm_bulk_process_submit" class="sm_bulk_process_submit button-primary" id="sm_bulk_process_submit" value="<?php _e( 'Bulk Update' , 'galaxyfunder' ) ; ?>"/>
        <img src="<?php echo SUMO_MEMBERSHIPS_PLUGIN_URL . '/assets/images/loader.gif' ; ?>" id="sumomemberships_load_on_save" style="display:none;width:40px;"/>
        <div id="sm_bulk_success_message" style="display: none"></div>

        <style type="text/css">
            p.submit{
                display: none;
            }
            #mainforms{
                display: none;
            }
        </style>
        <script type="text/javascript">
            jQuery( document ).ready( function () {
                jQuery( ".sm_bulk_process_submit" ).click( function () {
                    jQuery( '#sumomemberships_load_on_save' ).show() ;
                    var data = jQuery( '#mainform' ).serialize() ;
                    var with_rowcount = jQuery( '.sumo_restrict_members_with_particular_plan tr' ).length - 2 ;
                    var without_rowcount = jQuery( '.sumo_restrict_users_without_particular_plan tr' ).length - 2 ;
                    var post_type = jQuery( '#sm_bulk_post_type_restriction_select' ).val() ;
                    function sm_update_product_restrictions( ids ) {
                        data += '&action=sm_json_update_product_restrictions&ids=' + ids + '&with_rowcount=' + with_rowcount + '&without_rowcount=' + without_rowcount ;

                        jQuery.ajax( {
                            type : 'POST' ,
                            url : '<?php echo admin_url( 'admin-ajax.php' ) ; ?>' ,
                            data : data ,
                            dataType : 'json' ,
                            async : false
                        } ) ;
                    }
                    var offset = 0 ;
                    sumo_common_function_for_chunk( offset ) ;
                    var result_count = 0 ;
                    function sumo_common_function_for_chunk( offset ) {
                        var dataparam = ( {
                            action : 'sm_bulk_update_action' ,
                            offset : offset ,
                            post_type : post_type ,
                            sm_bulk_product_restriction_select : jQuery( '#sm_bulk_' + post_type + '_restriction_select' ).val() ,
                            select_products_for_restriction : jQuery( '#select_' + post_type + 's_for_restriction' ).val() ,
                            select_product_categories_for_restriction : jQuery( '#select_' + post_type + '_categories_for_restriction' ).val() ,
                            select_product_tags_for_restriction : jQuery( '#select_' + post_type + '_tags_for_restriction' ).val() ,
                        } ) ;

                        jQuery.post( "<?php echo admin_url( 'admin-ajax.php' ) ; ?>" , dataparam , function ( response ) {
                            result_count += response['ids'].length ;
                            if ( response['ids'].length > 0 ) {
                                sm_update_product_restrictions( response['ids'] ) ;
                                sumo_common_function_for_chunk( response['offset'] ) ;
                            } else {
                                jQuery( '#sumomemberships_load_on_save' ).hide() ;
                                jQuery( '#sm_bulk_success_message' ).html( 'Restrtiction applies to <b>' + result_count + '</b> ' + post_type + 's' ) ;
                                jQuery( '#sm_bulk_success_message' ).fadeIn() ;
                                jQuery( '#sm_bulk_success_message' ).fadeOut( 'slow' ) ;

                            }
                        } , 'json' ) ;
                    }
                } ) ;

                var post_type_selection = jQuery( '#sm_bulk_post_type_restriction_select' ).val() ;
                sumo_memberships_show_hide_posttype_section( post_type_selection ) ;
                jQuery( '#sm_bulk_post_type_restriction_select' ).change( function () {
                    sumo_memberships_show_hide_posttype_section( this.value ) ;
                } ) ;

                function sumo_memberships_show_hide_posttype_section( posttype_selection ) {
        <?php
        $post_type_array = array ( 'page' , 'post' , 'product' ) ;
        $post_types      = sumo_get_third_parties_cpt_exists() ;
        foreach ( $post_types as $type ) {
            if ( get_option( "sumomemberships_$type" ) == "yes" ) {
                $post_type_array[] = $type ;
            }
        }
        foreach ( $post_type_array as $each_post_type ) {
            ?>
                        if ( posttype_selection == '<?php echo $each_post_type ; ?>' ) {
                            jQuery( '#sm_bulk_<?php echo $each_post_type ; ?>_restriction_select' ).closest( 'tr' ).show() ;
                            if ( posttype_selection != 'product' ) {
                                var all_or_particular_post = jQuery( '#sm_bulk_' + posttype_selection + '_restriction_select' ).val() ;
                                sumo_memberships_show_hide_custom_section( all_or_particular_post , posttype_selection ) ;
                                jQuery( '#sm_bulk_' + posttype_selection + '_restriction_select' ).change( function () {
                                    sumo_memberships_show_hide_custom_section( this.value , posttype_selection ) ;
                                } ) ;
                            } else {
                                var product_selection1 = jQuery( '#sm_bulk_' + posttype_selection + '_restriction_select' ).val() ;
                                sumo_memberships_show_hide_product_section( product_selection1 ) ;
                                jQuery( '#sm_bulk_' + posttype_selection + '_restriction_select' ).change( function () {
                                    sumo_memberships_show_hide_product_section( this.value ) ;
                                } ) ;
                            }
                        } else {
                            jQuery( '#sm_bulk_<?php echo $each_post_type ; ?>_restriction_select' ).closest( 'tr' ).hide() ;
                            jQuery( '.sumo_show_hide_<?php echo $each_post_type ; ?>_bulkupdate' ).hide() ;
                        }
        <?php } ?>
                }

                var product_selection = jQuery( '#sm_bulk_product_restriction_select' ).val() ;
                sumo_memberships_show_hide_product_section( product_selection ) ;
                jQuery( '#sm_bulk_product_restriction_select' ).change( function () {
                    sumo_memberships_show_hide_product_section( this.value ) ;
                } ) ;

                function sumo_memberships_show_hide_product_section( product_selection ) {
                    if ( product_selection == 'all_products' || product_selection == 'all_categories' || product_selection == 'all_tags' ) {
                        jQuery( '#select_products_for_restriction' ).closest( 'tr' ).hide() ;
                        jQuery( '#select_product_categories_for_restriction' ).closest( 'tr' ).hide() ;
                        jQuery( '#select_product_tags_for_restriction' ).closest( 'tr' ).hide() ;
                    } else if ( product_selection == 'include_products' || product_selection == 'exclude_products' ) {
                        jQuery( '#select_products_for_restriction' ).closest( 'tr' ).show() ;
                        jQuery( '#select_product_categories_for_restriction' ).closest( 'tr' ).hide() ;
                        jQuery( '#select_product_tags_for_restriction' ).closest( 'tr' ).hide() ;
                    } else if ( product_selection == 'include_categories' || product_selection == 'exclude_categories' ) {
                        jQuery( '#select_products_for_restriction' ).closest( 'tr' ).hide() ;
                        jQuery( '#select_product_categories_for_restriction' ).closest( 'tr' ).show() ;
                        jQuery( '#select_product_tags_for_restriction' ).closest( 'tr' ).hide() ;
                    } else if ( product_selection == 'include_tags' || product_selection == 'exclude_tags' ) {
                        jQuery( '#select_products_for_restriction' ).closest( 'tr' ).hide() ;
                        jQuery( '#select_product_categories_for_restriction' ).closest( 'tr' ).hide() ;
                        jQuery( '#select_product_tags_for_restriction' ).closest( 'tr' ).show() ;
                    }
                }

                function sumo_memberships_show_hide_custom_section( post_selection , post_type ) {
                    if ( post_selection == 'all_' + post_type + 's' ) {
                        jQuery( '#select_' + post_type + 's_for_restriction' ).closest( 'tr' ).hide() ;
                    } else {
                        jQuery( '#select_' + post_type + 's_for_restriction' ).closest( 'tr' ).show() ;
                    }
                }


                var myval = jQuery( '#sumomemberships_display_content_when_restriction_enabled' ).val() ;

                if ( myval == 'show' ) {
                    jQuery( '#wp-sumo_content_for_reu-wrap' ).show() ;
                } else {
                    jQuery( '#wp-sumo_content_for_reu-wrap' ).hide() ;
                }
                jQuery( '#sumomemberships_display_content_when_restriction_enabled' ).change( function () {
                    if ( this.value == 'show' ) {
                        jQuery( '#wp-sumo_content_for_reu-wrap' ).show() ;
                    } else {
                        jQuery( '#wp-sumo_content_for_reu-wrap' ).hide() ;
                    }
                } ) ;

                function show_hide_input_fields() {

                    if ( jQuery( "select[name='sumomemberships_bulk_product_settings']" ).val() == 'with_particular_plans' ) {
                        jQuery( ".sumo_restrict_members_with_particular_plan" ).show() ;
                        jQuery( ".sumo_restrict_users_without_particular_plan" ).hide() ;
                        jQuery( ".sumomemberships_all_members_schedule_type" ).hide() ;
                        jQuery( "#sumomemberships_all_members_scheduled_duration_value" ).hide() ;
                        jQuery( "#sumomemberships_all_members_scheduled_duration_period" ).hide() ;
                    } else if ( jQuery( "select[name='sumomemberships_bulk_product_settings']" ).val() == 'without_particular_plans' ) {
                        jQuery( ".sumo_restrict_users_without_particular_plan" ).show() ;
                        jQuery( ".sumo_restrict_members_with_particular_plan" ).hide() ;
                        jQuery( ".sumomemberships_all_members_schedule_type" ).hide() ;
                        jQuery( "#sumomemberships_all_members_scheduled_duration_value" ).hide() ;
                        jQuery( "#sumomemberships_all_members_scheduled_duration_period" ).hide() ;
                    } else if ( jQuery( "select[name='sumomemberships_bulk_product_settings']" ).val() == 'all_members' ) {
                        jQuery( ".sumomemberships_all_members_schedule_type" ).show() ;
                        jQuery( ".sumo_restrict_members_with_particular_plan" ).hide() ;
                        jQuery( ".sumo_restrict_users_without_particular_plan" ).hide() ;
                        if ( jQuery( "input[name='sumomemberships_all_members_schedule_type']:checked" ).val() == "scheduled" ) {
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

                    if ( restriction_type == "with_particular_plans" || restriction_type == "without_particular_plans" ) {

                        var data = {
                            action : 'sumo_checking_table_with_r_without_plan' ,
                            this_table : restriction_type
                        } ;

                        jQuery.post( "<?php echo admin_url( 'admin-ajax.php' ) ; ?>" , data ,
                                function ( response , status ) {
                                    console.log( status ) ;
                                }
                        ) ;
                    }
                }

                show_hide_input_fields() ;

                ajax_check_table_with_r_without_plan( jQuery( "select[name='sumomemberships_bulk_product_settings']" ).val() ) ;

                jQuery( "input:radio[name='sumomemberships_all_members_schedule_type']" ).change( function () {

                    if ( this.checked && this.value == 'scheduled' ) {
                        jQuery( "#sumomemberships_all_members_scheduled_duration_value" ).show() ;
                        jQuery( "#sumomemberships_all_members_scheduled_duration_period" ).show() ;
                    } else {
                        jQuery( "#sumomemberships_all_members_scheduled_duration_value" ).hide() ;
                        jQuery( "#sumomemberships_all_members_scheduled_duration_period" ).hide() ;
                    }
                } ) ;

                jQuery( "select[name='sumomemberships_bulk_product_settings']" ).change( function () {

                    show_hide_input_fields() ;

                    ajax_check_table_with_r_without_plan( jQuery( "select[name='sumomemberships_bulk_product_settings']" ).val() ) ;
                } ) ;
            } ) ;
        </script>
        <?php
    }

    public static function sumo_memberhships_product_dds_bu() {
        ?>
        <tr valign="top" style="display:none" class="sumo_show_hide_product_bulkupdate" >
            <th class="titledesc" scope="row">
                <label><?php _e( 'Select Products' , 'sumomemberships' ) ?></label>
            </th>
            <td class="forminp forminp-select">
                <select id="sm_bulk_product_restriction_select" name="sm_bulk_product_restriction_select">
                    <option value="all_products"><?php _e( 'All Products' , 'sumomemberships' ) ; ?></option>
                    <option value="include_products"><?php _e( 'Include Products' , 'sumomemberships' ) ; ?></option>
                    <option value="exclude_products"><?php _e( 'Exclude Products' , 'sumomemberships' ) ; ?></option>
                    <option value="all_categories"><?php _e( 'All Categories' , 'sumomemberships' ) ; ?></option>
                    <option value="include_categories"><?php _e( 'Include Categories' , 'sumomemberships' ) ; ?></option>
                    <option value="exclude_categories"><?php _e( 'Exclude Categories' , 'sumomemberships' ) ; ?></option>
                    <option value="all_tags"><?php _e( 'All Tags' , 'sumomemberships' ) ; ?></option>
                    <option value="include_tags"><?php _e( 'Include Tags' , 'sumomemberships' ) ; ?></option>
                    <option value="exclude_tags"><?php _e( 'Exclude Tags' , 'sumomemberships' ) ; ?></option>
                </select>
            </td>
        </tr>
        <?php
        echo self::sm_select_products_for_restriction() ;
        echo self::sm_select_product_categories_for_restriction() ;
        echo self::sm_select_product_tags_for_restriction() ;
    }

    public static function sumo_memberhships_page_dds_bu() {
        ?>
        <tr valign="top" style="display:none" class="sumo_show_hide_page_bulkupdate" >
            <th class="titledesc" scope="row">
                <label><?php _e( 'Select Pages' , 'sumomemberships' ) ?></label>
            </th>
            <td class="forminp forminp-select">
                <select id="sm_bulk_page_restriction_select" name="sm_bulk_page_restriction_select">
                    <option value="all_pages"><?php _e( 'All Pages' , 'sumomemberships' ) ; ?></option>
                    <option value="include_pages"><?php _e( 'Include Pages' , 'sumomemberships' ) ; ?></option>
                    <option value="exclude_pages"><?php _e( 'Exclude Pages' , 'sumomemberships' ) ; ?></option>
                </select>
            </td>
        </tr>
        <?php
        echo self::sm_select_pages_for_restriction() ;
    }

    public static function sumo_memberhships_post_dds_bu() {
        ?>
        <tr valign="top" style="display:none" class="sumo_show_hide_post_bulkupdate" >
            <th class="titledesc" scope="row">
                <label><?php _e( 'Select Posts' , 'sumomemberships' ) ?></label>
            </th>
            <td class="forminp forminp-select">
                <select id="sm_bulk_post_restriction_select" name="sm_bulk_post_restriction_select">
                    <option value="all_posts"><?php _e( 'All Post' , 'sumomemberships' ) ; ?></option>
                    <option value="include_posts"><?php _e( 'Include Posts' , 'sumomemberships' ) ; ?></option>
                    <option value="exclude_posts"><?php _e( 'Exclude Posts' , 'sumomemberships' ) ; ?></option>
                </select>
            </td>
        </tr>
        <?php
        echo self::sm_select_posts_for_restriction() ;
    }

    public static function sumo_memberhships_custom_dds_bu( $post_type ) {
        ?>
        <tr valign="top" style="display:none" class="<?php echo 'sumo_show_hide_' . $post_type . '_bulkupdate' ; ?>" >
            <th class="titledesc" scope="row">
                <?php $label              = 'Select ' . ucfirst( $post_type ) ; ?>
                <label><?php _e( $label , 'sumomemberships' ) ?></label>
            </th>
            <td class="forminp forminp-select">
                <?php
                $all_post_lable     = "All " . ucfirst( $post_type ) ;
                $include_post_lable = "Include " . ucfirst( $post_type ) ;
                $exclude_post_lable = "Exclude " . ucfirst( $post_type ) ;
                ?>
                <select id="<?php echo 'sm_bulk_' . $post_type . '_restriction_select' ?>" name="<?php echo 'sm_bulk_' . $post_type . '_restriction_select' ?>">
                    <option value="<?php echo 'all_' . $post_type . 's' ?>"><?php _e( $all_post_lable , 'sumomemberships' ) ; ?></option>
                    <option value="<?php echo 'include' . $post_type . 's' ?>"><?php _e( $include_post_lable , 'sumomemberships' ) ; ?></option>
                    <option value="<?php echo 'exclude' . $post_type . 's' ?>"><?php _e( $exclude_post_lable , 'sumomemberships' ) ; ?></option>
                </select>
            </td>
        </tr>
        <?php
        echo self::sm_select_customposts_for_restriction( $post_type ) ;
    }

    /*
     * Function label settings to Member Level Tab
     */

    public static function default_settings() {
        global $woocommerce ;

        return apply_filters( 'woocommerce_sumomemberships_bulk_update' , array (
            array (
                'name' => __( 'Bulk Updation for Product Restrictions' , 'sumomemberships' ) ,
                'type' => 'title' ,
                'id'   => 'bulk_update_tab_setting' ,
            ) ,
            array (
                'type' => 'sumomemberships_bulk_update_content' ,
            ) ,
            array ( 'type' => 'sectionend' , 'id' => 'bulk_update_tab_setting' ) ,
        ) ) ;
    }

    /**
     * Registering Custom Field Admin Settings
     */
    public static function register_admin_settings() {


        woocommerce_admin_fields( SUMOBulkUpdate_Settings_Tab::default_settings() ) ;
    }

    /**
     * Update the Settings on Save Changes
     */
    public static function advance_update_settings() {

        woocommerce_update_options( SUMOBulkUpdate_Settings_Tab::default_settings() ) ;
    }

    public static function sm_select_products_for_restriction() {
        global $woocommerce ;
        ?>
        <tr valign="top" style="display:none" class="sumo_show_hide_product_bulkupdate">
            <th class="titledesc" scope="row">
                <label><?php _e( 'Select Products' , 'sumomemberships' ) ; ?>
                </label>
            </th>
            <td  class="forminp forminp-select">
                <?php
                if ( ( float ) $woocommerce->version > ( float ) ('2.2.0') && ( float ) $woocommerce->version < ( float ) ('3.0.0') ) {
                    ?>
                    <input type="hidden" class="wc-product-search" style="width: 350px;" id="select_products_for_restriction" name="select_products_for_restriction" data-placeholder="<?php _e( 'Search for a product&hellip;' , 'sumomemberships' ) ; ?>" data-action="woocommerce_json_search_products" data-multiple="true" value="">
                <?php } else { ?>
                    <select name="select_products_for_restriction" style='width:350px;' id='select_products_for_restriction' data-action="woocommerce_json_search_products" class="wc-product-search" multiple></select>
                <?php } ?>
            </td></tr>

        <?php
    }

    public static function sm_select_product_categories_for_restriction() {
        global $woocommerce ;
        ?>
        <tr valign="top" style="display:none" class="sumo_show_hide_product_bulkupdate">
            <th class="titledesc" scope="row">
                <label><?php _e( 'Select Categories' , 'sumomemberships' ) ; ?>
                </label>
            </th>
            <td  class="forminp forminp-select">
                <select id="select_product_categories_for_restriction" name="select_product_categories_for_restriction[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple">
                    <?php
                    $categories = get_terms( 'product_cat' , 'orderby=name&hide_empty=0' ) ;

                    if ( $categories ) {
                        foreach ( $categories as $cat ) {
                            echo '<option value="' . esc_attr( $cat->term_id ) . '">' . esc_html( $cat->name ) . '</option>' ;
                        }
                    }
                    ?>
                </select>
                <?php
                if ( ( float ) $woocommerce->version < ( float ) ('2.2.0') ) {
                    ?>
                    <script type="text/javascript">
                        jQuery( document ).ready( function () {
                            jQuery( '#select_product_categories_for_restriction' ).chosen() ;
                        } ) ;
                    </script>
                    <?php
                }
                ?>
            </td></tr>

        <?php
    }

    public static function sm_select_product_tags_for_restriction() {
        global $woocommerce ;
        ?>
        <tr valign="top" style="display:none" class="sumo_show_hide_product_bulkupdate">
            <th class="titledesc" scope="row">
                <label><?php _e( 'Select Tags' , 'sumomemberships' ) ; ?>
                </label>
            </th>
            <td  class="forminp forminp-select">
                <select id="select_product_tags_for_restriction" name="select_product_tags_for_restriction[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple">
                    <?php
                    $categories = get_terms( 'product_tag' , 'orderby=name&hide_empty=0' ) ;

                    if ( $categories ) {
                        foreach ( $categories as $cat ) {
                            echo '<option value="' . esc_attr( $cat->term_id ) . '">' . esc_html( $cat->name ) . '</option>' ;
                        }
                    }
                    ?>
                </select>
                <?php
                if ( ( float ) $woocommerce->version < ( float ) ('2.2.0') ) {
                    ?>
                    <script type="text/javascript">
                        jQuery( document ).ready( function () {
                            jQuery( '#select_product_tags_for_restriction' ).chosen() ;
                        } ) ;
                    </script>
                    <?php
                }
                ?>
            </td></tr>

        <?php
    }

    public static function sm_select_pages_for_restriction() {
        global $woocommerce ;
        ?>
        <tr valign="top" style="display:none" class="sumo_show_hide_page_bulkupdate">
            <th class="titledesc" scope="row">
                <label><?php _e( 'Select Pages' , 'sumomemberships' ) ; ?>
                </label>
            </th>
            <td  class="forminp forminp-select">
                <?php
                if ( ( float ) $woocommerce->version > ( float ) ('2.2.0') && ( float ) $woocommerce->version < ( float ) ('3.0.0') ) {
                    ?>
                    <input type="hidden" class="sumo-page-search" style="width: 350px;" id="select_pages_for_restriction" name="select_pages_for_restriction" data-placeholder="<?php _e( 'Search for a page&hellip;' , 'sumomemberships' ) ; ?>" data-multiple="true" value="">
                <?php } else { ?>
                    <select name="select_pages_for_restriction" style='width:350px;' id='select_pages_for_restriction' class="sumo-page-search" multiple></select>
                <?php } ?>
            </td></tr>

        <?php
    }

    public static function sm_select_posts_for_restriction() {
        global $woocommerce ;
        ?>
        <tr valign="top" style="display:none" class="sumo_show_hide_post_bulkupdate">
            <th class="titledesc" scope="row">
                <label><?php _e( 'Select Posts' , 'sumomemberships' ) ; ?>
                </label>
            </th>
            <td  class="forminp forminp-select">
                <?php
                if ( ( float ) $woocommerce->version > ( float ) ('2.2.0') && ( float ) $woocommerce->version < ( float ) ('3.0.0') ) {
                    ?>
                    <input type="hidden" class="sumo-post-search" style="width: 350px;" id="select_posts_for_restriction" name="select_posts_for_restriction" data-placeholder="<?php _e( 'Search for a post&hellip;' , 'sumomemberships' ) ; ?>" data-multiple="true" value="">
                <?php } else { ?>
                    <select name="select_posts_for_restriction" style='width:350px;' id='select_posts_for_restriction' class="sumo-post-search" multiple></select>
                <?php } ?>
            </td></tr>

        <?php
    }

    public static function sm_select_customposts_for_restriction( $posttype ) {
        global $woocommerce ;
        ?>
        <tr valign="top" style="display:none" class="<?php echo 'sumo_show_hide_' . $posttype . '_bulkupdate' ?>">
            <th class="titledesc" scope="row">
                <?php $label       = "Select " . ucfirst( $posttype ) ?>
                <label><?php _e( $label , 'sumomemberships' ) ; ?>
                </label>
            </th>
            <td  class="forminp forminp-select">
                <?php
                $placeholder = "Search for a " . $posttype . "&hellip;" ;
                if ( ( float ) $woocommerce->version > ( float ) ('2.2.0') && ( float ) $woocommerce->version < ( float ) ('3.0.0') ) {
                    ?>
                    <input type="hidden" class="<?php echo 'sumo-' . $posttype . '-search' ?>" style="width: 350px;" id="<?php echo 'select_' . $posttype . 's_for_restriction' ?>" name="<?php echo 'select_' . $posttype . 's_for_restriction' ?>" data-placeholder="<?php _e( $placeholder , 'sumomemberships' ) ; ?>" data-multiple="true" value="">
                <?php } else { ?>
                    <select name="<?php echo 'select_' . $posttype . 's_for_restriction' ?>" style='width:350px;' id="<?php echo 'select_' . $posttype . 's_for_restriction' ?>" class="<?php echo 'sumo-' . $posttype . '-search' ?>" multiple></select>
                <?php } ?>
            </td></tr>

        <?php
    }

    public static function save_rules_added_to_product_by_bulk_actions( $this_postid , $i , $key , $array ) {

        $object = new SUMOMemberships_Admin_Meta_Boxes() ;
        if ( $key == 'sumomemberships_restrict_members_with_particular_plan_purchased' ) {
            $meta_key = 'sumomemberships_saved_plans_to_restrict_members_for_with' ;
        } elseif ( $key == 'sumomemberships_restrict_users_without_particular_plan_purchased' ) {
            $meta_key = 'sumomemberships_saved_plans_to_restrict_users_for_without' ;
        } else {
            $meta_key = 'sumomemberships_saved_additional_linking_plans' ;
        }

        $saved_plans      = $check_previous_saved_values ;
        $saved_plan_value = get_post_meta( $this_postid , $key . $i , true ) ;

        if ( $saved_plan_value > 0 ) {
            unset( $saved_plans[ implode( array_keys( $saved_plans , $saved_plan_value ) ) ] ) ;
            update_post_meta( $this_postid , $meta_key , $saved_plans ) ;
        }

        update_post_meta( $this_postid , $key . $i , $array[ $key . $i ] ) ;

        $new_value_to_save = array ( $array[ $key . $i ] ) ;
        update_post_meta( $this_postid , $meta_key , $new_value_to_save ) ;
    }

    public static function sumo_memberships_custom_search() {
        if ( isset( $_GET[ 'page' ] ) && isset( $_GET[ 'tab' ] ) && 'sumomemberships_settings' === $_GET[ 'page' ] && 'bulk_update' === $_GET[ 'tab' ] ) {
            ?>
            <script type="text/javascript">
                jQuery( document ).ready( function () {
            <?php
            $post_type_array = array ( 'page' , 'post' , 'product' ) ;
            $post_types      = sumo_get_third_parties_cpt_exists() ;
            foreach ( $post_types as $type ) {
                if ( get_option( "sumomemberships_$type" ) == "yes" ) {
                    $post_type_array[] = $type ;
                }
            }
            foreach ( $post_type_array as $each_post ) {
                if ( ( float ) WC()->version < ( float ) '3.0.0' ) {
                    ?>
                            jQuery( "<?php echo '.sumo-' . $each_post . '-search' ?>" ).select2( {
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
                                            action : "sumo_search_custom_post_types" ,
                                            posttype : "<?php echo $each_post ?>"
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
                            jQuery( "<?php echo '.sumo-' . $each_post . '-search' ?>" ).select2( {
                                placeholder : "Enter atleast 3 characters" ,
                                allowClear : true ,
                                minimumInputLength : 3 ,
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
                                            action : 'sumo_search_custom_post_types' ,
                                            posttype : "<?php echo $each_post ?>"
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
                    <?php
                }
            }
            ?>
                } ) ;
            </script>
            <?php
        }
    }

    public static function sumo_AJAX_search_custom_post_types() {
        $found_plans = array () ;
        $args        = array (
            'offset'    => 0 ,
            'orderby'   => 'post_date' ,
            'post_type' => $_REQUEST[ 'posttype' ] ,
            's'         => $_REQUEST[ 'term' ] ,
        ) ;

        $search_results = get_posts( $args ) ;

        foreach ( $search_results as $plan ) {
            $plan_id               = $plan->ID ;
            $plan_name             = $plan->post_title ;
            $found_plans[ $plan_id ] = $plan_name ;
        }
        wp_send_json( $found_plans ) ;
        exit() ;
    }

}

new SUMOBulkUpdate_Settings_Tab() ;
