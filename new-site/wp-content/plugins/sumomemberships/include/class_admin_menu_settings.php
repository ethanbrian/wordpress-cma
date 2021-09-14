<?php

class SUMOMemberships_Admin_Menu_Settings {

    public function __construct() {

        add_action( 'admin_menu' , array ( $this , 'add_sub_menus' ) ) ;

        include 'tabs/class_general_tab.php' ;
        include 'tabs/class_advanced_tab.php' ;
        include 'tabs/class_restricted_urls_tab.php' ;
        include 'tabs/class_content_restriction_tab.php' ;
        include 'tabs/class_email_tab.php' ;
        include 'tabs/class_message_tab.php' ;
        include 'tabs/class_transfer_plans.php' ;
        include 'tabs/class_reset_tab.php' ;
        include 'tabs/class_bulk_update_tab.php' ;
        include 'tabs/class_previous_order_tab.php' ;
        include 'tabs/class_help_tab.php' ;

    }

    // Add Submenu
    public function add_sub_menus() {
        global $submenu ;

        unset( $submenu[ 'edit.php?post_type=sumomembershipplans' ][ 10 ] ) ;

        add_submenu_page( 'edit.php?post_type=sumomembershipplans' , 'Settings' , 'Settings' , 'manage_options' , 'sumomemberships_settings' , array ( $this , 'admin_tab_settings' ) ) ;
        add_submenu_page( 'edit.php?post_type=sumomembershipplans' , 'Advanced' , 'Master Log' , 'manage_options' , 'edit.php?post_type=sumomem_masterlog' ) ;
    }

    public function admin_tab_settings() {

        global $woocommerce , $woocommerce_settings , $current_section , $current_tab ;

        do_action( 'woocommerce_sumomemberships_settings_start' ) ;

        $current_tab     = ( empty( $_GET[ 'tab' ] ) ) ? 'sumomembership_general_settings' : sanitize_text_field( urldecode( $_GET[ 'tab' ] ) ) ;
        $current_section = ( empty( $_REQUEST[ 'section' ] ) ) ? '' : sanitize_text_field( urldecode( $_REQUEST[ 'section' ] ) ) ;

        if ( ! empty( $_POST[ 'save' ] ) ) {
            if ( empty( $_REQUEST[ '_wpnonce' ] ) || ! wp_verify_nonce( $_REQUEST[ '_wpnonce' ] , 'woocommerce-settings' ) )
                die( __( 'Action failed. Please refresh the page and retry.' , 'sumomemberships' ) ) ;

            if ( ! $current_section ) {
                switch ( $current_tab ) {
                    default :
                        if ( isset( $woocommerce_settings[ $current_tab ] ) )
                            woocommerce_update_options( $woocommerce_settings[ $current_tab ] ) ;

                        // Trigger action for tab
                        do_action( 'woocommerce_update_options_' . $current_tab ) ;
                        break ;
                }

                do_action( 'woocommerce_update_options' ) ;
            } else {
                // Save section onlys
                do_action( 'woocommerce_update_options_' . $current_tab . '_' . $current_section ) ;
            }

            // Clear any unwanted data
            delete_transient( 'woocommerce_cache_excluded_uris' ) ;
            // Redirect back to the settings page
            $redirect = add_query_arg( array ( 'saved' => 'true' ) ) ;

            if ( isset( $_POST[ 'subtab' ] ) ) {
                wp_safe_redirect( $redirect ) ;
                exit ;
            }
        }
        // Get any returned messages
        $error   = ( empty( $_GET[ 'wc_error' ] ) ) ? '' : urldecode( stripslashes( $_GET[ 'wc_error' ] ) ) ;
        $message = ( empty( $_GET[ 'wc_message' ] ) ) ? '' : urldecode( stripslashes( $_GET[ 'wc_message' ] ) ) ;

        if ( $error || $message ) {

            if ( $error ) {
                echo '<div id="message" class="error fade"><p><strong>' . esc_html( $error ) . '</strong></p></div>' ;
            } else {
                echo '<div id="message" class="updated fade"><p><strong>' . esc_html( $message ) . '</strong></p></div>' ;
            }
        } elseif ( ! empty( $_GET[ 'saved' ] ) ) {

            echo '<div id="message" class="updated fade"><p><strong>' . __( 'Your settings have been saved.' , 'sumomemberships' ) . '</strong></p></div>' ;
        }
        ?>
        <div class="wrap woocommerce">
            <form method="post" id="mainform" action="" enctype="multipart/form-data">
                <div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br /></div><h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
                    <?php
                    $tabs = '' ;
                    $tabs = apply_filters( 'woocommerce_sumomemberships_settings_tabs_array' , $tabs ) ;

                    foreach ( $tabs as $name => $label ) {

                        //Display Advanced Tab only if Third Parties CPT exists
                        if ( ! sumo_is_third_parties_cpt_exists() && $name == "sumomembership_advanced_settings" ) {
                            continue ;
                        }

                        echo '<a href="' . admin_url( 'edit.php?post_type=sumomembershipplans&page=sumomemberships_settings&tab=' . $name ) . '" class="nav-tab ' ;
                        if ( $current_tab == $name )
                            echo 'nav-tab-active' ;
                        echo '">' . $label . '</a>' ;
                    }
                    do_action( 'woocommerce_sumomemberships_settings_tabs' ) ;
                    ?>
                </h2>

                <?php
                switch ( $current_tab ) :
                    default :
                        do_action( 'woocommerce_sumomemberships_settings_tabs_' . $current_tab ) ;
                        break ;
                endswitch ;
                ?>
                <p class="submit">
                    <?php if ( ! isset( $GLOBALS[ 'hide_save_button' ] ) ) : ?>
                        <input name="save" class="button-primary" type="submit" value="<?php _e( 'Save changes' , 'sumomemberships' ) ; ?>" />
                    <?php endif ; ?>
                    <input type="hidden" name="subtab" id="last_tab" />
                    <?php wp_nonce_field( 'woocommerce-settings' , '_wpnonce' , true , true ) ; ?>
                </p>
            </form>            

        </div>
        <?php
    }

}

new SUMOMemberships_Admin_Menu_Settings() ;
