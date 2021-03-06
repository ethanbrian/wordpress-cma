<?php
defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

/**
 * The configuration file
 */
defined( 'HMW_REQUEST_TIME' ) || define( 'HMW_REQUEST_TIME', microtime( true ) );

if ( defined( 'NONCE_KEY' ) ) {
	defined( '_HMW_NONCE_ID_' ) || define( '_HMW_NONCE_ID_', NONCE_KEY );
} else {
	defined( '_HMW_NONCE_ID_' ) || define( '_HMW_NONCE_ID_', md5( date( 'Y-m-d' ) ) );
}

//force Hide My Wp to load right after initialization
defined( 'HMW_PRIORITY' ) || define( 'HMW_PRIORITY', false );
//Force not to write the rules in config file
defined( 'HMW_RULES_IN_CONFIG' ) || define( 'HMW_RULES_IN_CONFIG', true );
//add HMW Rules in WordPress rewrite definition in htaccess
defined( 'HMW_RULES_IN_WP_RULES' ) || define( 'HMW_RULES_IN_WP_RULES', true );

/* No path file? error ... */
require_once( dirname( __FILE__ ) . '/paths.php' );

/* Define the record name in the Option and UserMeta tables */
define( 'HMW_OPTION', 'hmw_options' );
define( 'HMW_OPTION_SAFE', 'hmw_options_safe' );
