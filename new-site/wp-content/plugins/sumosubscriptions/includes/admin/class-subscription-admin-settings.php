<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly
}

/**
 * Handle Admin menus and post types.
 * 
 * @class SUMOSubscriptions_Admin_Settings
 */
class SUMOSubscriptions_Admin_Settings {

	/**
	 * Setting pages.
	 *
	 * @var array
	 */
	private static $settings = array() ;

	/**
	 * Init SUMOSubscriptions_Admin_Settings.
	 */
	public static function init() {
		add_action( 'init', __CLASS__ . '::register_post_types' ) ;
		add_action( 'admin_menu', __CLASS__ . '::settings_menu' ) ;
		add_filter( 'plugin_row_meta', __CLASS__ . '::plugin_row_meta', 10, 2 ) ;
		add_filter( 'plugin_action_links_' . SUMO_SUBSCRIPTIONS_PLUGIN_BASENAME, __CLASS__ . '::plugin_action_links' ) ;
		add_action( 'sumosubscriptions_reset_options', __CLASS__ . '::reset_options' ) ;
		add_filter( 'woocommerce_account_settings', __CLASS__ . '::add_note_to_subscription_order_data_retention_settings' ) ;
		add_filter( 'woocommerce_account_settings', __CLASS__ . '::add_wc_account_settings' ) ;

		include 'class-subscription-admin-welcome.php' ;
		include 'class-subscription-admin-exporter.php' ;
	}

	/**
	 * Show action links on the plugin screen.
	 *
	 * @param	mixed $links Plugin Action links
	 * @return	array
	 */
	public static function plugin_action_links( $links ) {
		$setting_page_link = '<a  href="' . esc_url( admin_url( 'admin.php?page=sumosubs-settings' ) ) . '">Settings</a>' ;
		array_unshift( $links, $setting_page_link ) ;
		return $links ;
	}

	/**
	 * Show row meta on the plugin screen.
	 *
	 * @param	mixed $links Plugin Row Meta
	 * @param	mixed $file  Plugin Base file
	 * @return	array
	 */
	public static function plugin_row_meta( $links, $file ) {
		if ( SUMO_SUBSCRIPTIONS_PLUGIN_BASENAME == $file ) {
			$row_meta = array(
				'about'   => '<a href="' . esc_url( admin_url( 'admin.php?page=sumosubs-welcome' ) ) . '" aria-label="' . esc_attr__( 'About', 'sumosubscriptions' ) . '">' . esc_html__( 'About', 'sumosubscriptions' ) . '</a>',
				'support' => '<a href="' . esc_url( 'http://fantasticplugins.com/support/' ) . '" aria-label="' . esc_attr__( 'Support', 'sumosubscriptions' ) . '">' . esc_html__( 'Support', 'sumosubscriptions' ) . '</a>',
					) ;

			return array_merge( $links, $row_meta ) ;
		}

		return ( array ) $links ;
	}

	/**
	 * Register Custom Post Type.
	 */
	public static function register_post_types() {

		if ( ! post_type_exists( 'sumosubscriptions' ) ) {
			register_post_type( 'sumosubscriptions', array(
				'labels'          => array(
					'name'               => __( 'Subscriptions', 'sumosubscriptions' ),
					'singular_name'      => _x( 'Subscription', 'singular name', 'sumosubscriptions' ),
					'menu_name'          => _x( 'Subscriptions', 'admin menu', 'sumosubscriptions' ),
					'add_new'            => __( 'Add subscription', 'sumosubscriptions' ),
					'add_new_item'       => __( 'Add new subscription', 'sumosubscriptions' ),
					'new_item'           => __( 'New subscription', 'sumosubscriptions' ),
					'edit_item'          => __( 'Edit subscription', 'sumosubscriptions' ),
					'view_item'          => __( 'View subscription', 'sumosubscriptions' ),
					'search_items'       => __( 'Search subscriptions', 'sumosubscriptions' ),
					'not_found'          => __( 'No subscription found.', 'sumosubscriptions' ),
					'not_found_in_trash' => __( 'No subscription found in trash.', 'sumosubscriptions' )
				),
				'description'     => __( 'This is where store subscriptions are stored.', 'sumosubscriptions' ),
				'public'          => false,
				'show_ui'         => true,
				'capability_type' => 'post',
				'show_in_menu'    => 'sumosubscriptions',
				'rewrite'         => false,
				'has_archive'     => false,
				'supports'        => false,
				'map_meta_cap'    => true,
				'capabilities'    => array(
					'create_posts' => 'do_not_allow'
				),
			) ) ;
		}

		if ( ! post_type_exists( 'sumosubs_cron_events' ) ) {
			register_post_type( 'sumosubs_cron_events', array(
				'labels'              => array(
					'name'         => __( 'Cron events', 'sumosubscriptions' ),
					'menu_name'    => _x( 'Cron events', 'admin menu', 'sumosubscriptions' ),
					'search_items' => __( 'Search cron events', 'sumosubscriptions' ),
					'not_found'    => __( 'No cron event found.', 'sumosubscriptions' ),
				),
				'description'         => __( 'This is where scheduled cron events are stored.', 'sumosubscriptions' ),
				'public'              => false,
				'capability_type'     => 'post',
				'show_ui'             => apply_filters( 'sumosubscriptions_show_cron_events_post_type_ui', false ),
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'show_in_menu'        => 'sumosubscriptions',
				'hierarchical'        => false,
				'show_in_nav_menus'   => false,
				'rewrite'             => false,
				'query_var'           => false,
				'supports'            => false,
				'has_archive'         => false,
				'map_meta_cap'        => true,
				'capabilities'        => array(
					'create_posts' => 'do_not_allow'
				),
			) ) ;
		}

		if ( ! post_type_exists( 'sumomasterlog' ) ) {
			register_post_type( 'sumomasterlog', array(
				'labels'          => array(
					'name'         => __( 'Master log', 'sumosubscriptions' ),
					'menu_name'    => _x( 'Master log', 'admin menu', 'sumosubscriptions' ),
					'search_items' => __( 'Search log', 'sumosubscriptions' ),
					'not_found'    => __( 'No logs found.', 'sumosubscriptions' ),
				),
				'description'     => __( 'This is where subscription logs are stored.', 'sumosubscriptions' ),
				'public'          => false,
				'show_ui'         => true,
				'capability_type' => 'post',
				'show_in_menu'    => 'sumosubscriptions',
				'rewrite'         => false,
				'has_archive'     => false,
				'supports'        => false,
				'map_meta_cap'    => true,
				'capabilities'    => array(
					'create_posts' => 'do_not_allow'
				),
			) ) ;
		}
	}

	/**
	 * Add admin menu pages.
	 */
	public static function settings_menu() {
		add_menu_page( __( 'SUMO Subscriptions', 'sumosubscriptions' ), __( 'SUMO Subscriptions', 'sumosubscriptions' ), 'manage_woocommerce', 'sumosubscriptions', null, 'dashicons-backup', '56.6' ) ;
		add_submenu_page( 'sumosubscriptions', __( 'Settings', 'sumosubscriptions' ), __( 'Settings', 'sumosubscriptions' ), 'manage_woocommerce', 'sumosubs-settings', __CLASS__ . '::output' ) ;
		add_submenu_page( 'sumosubscriptions', __( 'Subscription Export', 'sumosubscriptions' ), __( 'Subscription Export', 'sumosubscriptions' ), 'manage_woocommerce', SUMO_Subscription_Exporter::$exporter_page, 'SUMO_Subscription_Exporter::render_exporter_html_fields' ) ;
	}

	/**
	 * Include the settings page classes.
	 */
	public static function get_settings_pages() {
		if ( empty( self::$settings ) ) {
			self::$settings[] = include( 'settings-page/class-general-settings.php' ) ;
			self::$settings[] = include( 'settings-page/class-order-subscription-settings.php' ) ;
			self::$settings[] = include( 'settings-page/class-synchronization-settings.php' ) ;
			self::$settings[] = include( 'settings-page/class-upgrade-or-downgrade-settings.php' ) ;
			self::$settings[] = include( 'settings-page/class-my-account-settings.php' ) ;
			self::$settings[] = include( 'settings-page/class-advance-settings.php' ) ;
			self::$settings[] = include( 'settings-page/class-bulk-action-settings.php' ) ;
			self::$settings[] = include( 'settings-page/class-message-settings.php' ) ;
			self::$settings[] = include( 'settings-page/class-help.php' ) ;
		}

		return self::$settings ;
	}

	/**
	 * Settings page.
	 *
	 * Handles the display of the main subscription settings page in admin.
	 */
	public static function output() {
		global $current_section, $current_tab ;

		do_action( 'sumosubscriptions_settings_start' ) ;

		$current_tab     = ( empty( $_GET[ 'tab' ] ) ) ? 'general' : urldecode( sanitize_text_field( $_GET[ 'tab' ] ) ) ;
		$current_section = ( empty( $_REQUEST[ 'section' ] ) ) ? '' : urldecode( sanitize_text_field( $_REQUEST[ 'section' ] ) ) ;

		// Include settings pages
		self::get_settings_pages() ;

		do_action( 'sumosubscriptions_add_options_' . $current_tab ) ;
		do_action( 'sumosubscriptions_add_options' ) ;

		if ( $current_section ) {
			do_action( 'sumosubscriptions_add_options_' . $current_tab . '_' . $current_section ) ;
		}

		if ( ! empty( $_POST[ 'save' ] ) ) {
			if ( empty( $_REQUEST[ '_wpnonce' ] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST[ '_wpnonce' ] ), 'sumosubscriptions-settings' ) ) {
				die( esc_html__( 'Action failed. Please refresh the page and retry.', 'sumosubscriptions' ) ) ;
			}

			// Save settings if data has been posted
			do_action( 'sumosubscriptions_update_options_' . $current_tab, $_POST ) ;
			do_action( 'sumosubscriptions_update_options', $_POST ) ;

			if ( $current_section ) {
				do_action( 'sumosubscriptions_update_options_' . $current_tab . '_' . $current_section, $_POST ) ;
			}

			wp_safe_redirect( esc_url_raw( add_query_arg( array( 'saved' => 'true' ) ) ) ) ;
			exit ;
		}
		if ( ! empty( $_POST[ 'reset' ] ) || ! empty( $_POST[ 'reset_all' ] ) ) {
			if ( empty( $_REQUEST[ '_wpnonce' ] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST[ '_wpnonce' ] ), 'sumosubscriptions-reset_settings' ) ) {
				die( esc_html__( 'Action failed. Please refresh the page and retry.', 'sumosubscriptions' ) ) ;
			}

			do_action( 'sumosubscriptions_reset_options_' . $current_tab, $_POST ) ;

			if ( ! empty( $_POST[ 'reset_all' ] ) ) {
				do_action( 'sumosubscriptions_reset_options', $_POST ) ;
			}

			if ( $current_section ) {
				do_action( 'sumosubscriptions_reset_options_' . $current_tab . '_' . $current_section, $_POST ) ;
			}

			wp_safe_redirect( esc_url_raw( add_query_arg( array( 'saved' => 'true' ) ) ) ) ;
			exit ;
		}
		// Get any returned messages
		$error   = ( empty( $_GET[ 'wc_error' ] ) ) ? '' : urldecode( stripslashes( sanitize_title( $_GET[ 'wc_error' ] ) ) ) ;
		$message = ( empty( $_GET[ 'wc_message' ] ) ) ? '' : urldecode( stripslashes( sanitize_title( $_GET[ 'wc_message' ] ) ) ) ;

		if ( $error || $message ) {
			if ( $error ) {
				echo '<div id="message" class="error fade"><p><strong>' . esc_html( $error ) . '</strong></p></div>' ;
			} else {
				echo '<div id="message" class="updated fade"><p><strong>' . esc_html( $message ) . '</strong></p></div>' ;
			}
		} elseif ( ! empty( $_GET[ 'saved' ] ) ) {
			echo '<div id="message" class="updated fade"><p><strong>' . esc_html__( 'Your settings have been saved.', 'sumosubscriptions' ) . '</strong></p></div>' ;
		}

		include 'views/html-admin-settings.php' ;
	}

	/**
	 * Default options.
	 *
	 * Sets up the default options used on the settings page.
	 */
	public static function save_default_options( $reset_all = false ) {
		if ( empty( self::$settings ) ) {
			self::get_settings_pages() ;
		}

		foreach ( self::$settings as $tab ) {
			if ( ! isset( $tab->settings ) || ! is_array( $tab->settings ) ) {
				continue ;
			}

			$tab->add_options( $reset_all ) ;
		}
	}

	/**
	 * Reset All settings
	 */
	public static function reset_options() {
		self::save_default_options( true ) ;
	}

	/**
	 * Add notice to admin when data retention in SUMO Subscription orders
	 *
	 * @param array $settings
	 * @return array
	 */
	public static function add_note_to_subscription_order_data_retention_settings( $settings ) {
		if ( is_array( $settings ) && ! empty( $settings ) ) {
			foreach ( $settings as $pos => $setting ) {
				if (
						isset( $setting[ 'id' ] ) &&
						isset( $setting[ 'type' ] ) &&
						'personal_data_retention' === $setting[ 'id' ] &&
						'title' === $setting[ 'type' ]
				) {
					$settings[ $pos ][ 'desc' ] .= __( '<br><strong>Note:</strong> This settings will not be applicable for SUMO Subscription orders.', 'sumosubscriptions' ) ;
				}
			}
		}

		return $settings ;
	}

	/**
	 * Add privacy settings under WooCommerce Privacy
	 *
	 * @param array $settings
	 * @return array
	 */
	public static function add_wc_account_settings( $settings ) {
		$original_settings = $settings ;

		if ( is_array( $original_settings ) && ! empty( $original_settings ) ) {
			$new_settings = array() ;

			foreach ( $original_settings as $pos => $setting ) {
				if ( ! isset( $setting[ 'id' ] ) ) {
					continue ;
				}

				switch ( $setting[ 'id' ] ) {
					case 'woocommerce_erasure_request_removes_order_data':
						$new_settings[ $pos + 1 ] = array(
							'title'         => __( 'Account erasure requests', 'sumosubscriptions' ),
							'desc'          => __( 'Remove personal data from SUMO Subscriptions and its related Orders', 'sumosubscriptions' ),
							/* Translators: %s URL to erasure request screen. */
							'desc_tip'      => sprintf( __( 'When handling an <a href="%s">account erasure request</a>, should personal data within SUMO Subscriptions be retained or removed?', 'sumosubscriptions' ), esc_url( admin_url( 'tools.php?page=remove_personal_data' ) ) ),
							'id'            => 'sumo_erasure_request_removes_subscription_data',
							'type'          => 'checkbox',
							'default'       => 'no',
							'checkboxgroup' => '',
							'autoload'      => false,
								) ;
						break ;
					case 'woocommerce_anonymize_completed_orders':
						$new_settings[ $pos + 1 ] = array(
							'title'       => __( 'Retain ended SUMO Subscription Orders', 'sumosubscriptions' ),
							'desc_tip'    => __( 'Retain ended SUMO Subscription Orders for a specified duration before anonymizing the personal data within them.', 'sumosubscriptions' ),
							'id'          => 'sumo_anonymize_ended_subscriptions',
							'type'        => 'relative_date_selector',
							'placeholder' => __( 'N/A', 'sumosubscriptions' ),
							'default'     => array(
								'number' => '',
								'unit'   => 'months',
							),
							'autoload'    => false,
								) ;
						break ;
				}
			}

			if ( ! empty( $new_settings ) ) {
				foreach ( $new_settings as $pos => $new_setting ) {
					array_splice( $settings, $pos, 0, array( $new_setting ) ) ;
				}
			}
		}

		return $settings ;
	}

}

SUMOSubscriptions_Admin_Settings::init() ;
