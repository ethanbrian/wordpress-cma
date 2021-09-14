<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}

function sumo_memberships_plugin_get_default_privacy_content() {
    return
            '<p>' . __( 'This includes the basics of what personal data your store may be collecting, storing and sharing. Depending on what settings are enabled and which additional plugins are used, the specific information shared by your store will vary.' , 'sumomemberships' ) . '</p>' .
            '<h2>' . __( 'What the Plugin Does' , 'sumomemberships' ) . '</h2>' .
            '<p>' . __( "- Access to specific Pages, Posts, Products can be restricted to specific Membership Plans" , 'sumomemberships' ) . '</p>' .
            '<p>' . __( "- The user will not have access to the restricted content until they purchase the associated Membership plan" , 'sumomemberships' ) . '</p>' .
            '<p>' . __( "- Once the Membership Plan is purchased, the user will have access to the restricted content." , 'sumomemberships' ) . '</p>' .
            '<h2>' . __( 'What we collect and store' , 'sumomemberships' ) . '</h2>' .
            '<h3>' . __( "User ID" , 'sumomemberships' ) . '</h3>' .
            '<p>' . __( "We use the user id to " , 'sumomemberships' ) . '</p>' .
            '<p>' . __( "- Identify the user" , 'sumomemberships' ) . '</p>' .
            '<p>' . __( "- Associate the Purchased Membership Plan with the user" , 'sumomemberships' ) . '</p>' ;
}

/**
 * Add the suggested privacy policy text to the policy postbox.
 */
function sumo_memberships_plugin_add_suggested_privacy_content() {
    global $wp_version ;
    if ( function_exists( 'wp_add_privacy_policy_content' ) ) {
        $content = sumo_memberships_plugin_get_default_privacy_content() ;
        wp_add_privacy_policy_content( __( 'SUMO Memberships' , 'sumomemberships' ) , $content ) ;
    }
}

// Not sure why but core registers their default text at priority 15, so to be after them (which I think would be the idea, you need to be 20+.
add_action( 'admin_init' , 'sumo_memberships_plugin_add_suggested_privacy_content' , 20 ) ;
