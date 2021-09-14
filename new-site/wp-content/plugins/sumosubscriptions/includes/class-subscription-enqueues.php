<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly
}

/**
 * Handle subscription enqueues.
 * 
 * @class SUMOSubscriptions_Enqueues
 */
class SUMOSubscriptions_Enqueues {

	/**
	 * Init SUMOSubscriptions_Enqueues
	 */
	public static function init() {
		add_action( 'admin_enqueue_scripts', __CLASS__ . '::admin_script', 11 ) ;
		add_action( 'admin_enqueue_scripts', __CLASS__ . '::admin_style', 11 ) ;
		add_action( 'wp_enqueue_scripts', __CLASS__ . '::frontend_script', 11 ) ;
		add_action( 'wp_enqueue_scripts', __CLASS__ . '::frontend_style', 11 ) ;
		add_filter( 'woocommerce_screen_ids', __CLASS__ . '::load_wc_enqueues', 1 ) ;
	}

	/**
	 * Register and enqueue a script for use.
	 *
	 * @uses   wp_enqueue_script()
	 * @param  string   $handle
	 * @param  string   $path
	 * @param  array   $localize_data
	 * @param  string[] $deps
	 * @param  string   $version
	 * @param  boolean  $in_footer
	 */
	public static function enqueue_script( $handle, $path = '', $localize_data = array(), $deps = array( 'jquery' ), $version = SUMO_SUBSCRIPTIONS_VERSION, $in_footer = false ) {
		wp_register_script( $handle, $path, $deps, $version, $in_footer ) ;

		$name = str_replace( '-', '_', $handle ) ;
		wp_localize_script( $handle, "{$name}_params", $localize_data ) ;
		wp_enqueue_script( $handle ) ;
	}

	/**
	 * Register and enqueue a styles for use.
	 *
	 * @uses   wp_enqueue_style()
	 * @param  string   $handle
	 * @param  string   $path
	 * @param  string[] $deps
	 * @param  string   $version
	 * @param  string   $media
	 * @param  boolean  $has_rtl
	 */
	public static function enqueue_style( $handle, $path = '', $deps = array(), $version = SUMO_SUBSCRIPTIONS_VERSION, $media = 'all', $has_rtl = false ) {
		wp_register_style( $handle, $path, $deps, $version, $media, $has_rtl ) ;
		wp_enqueue_style( $handle ) ;
	}

	/**
	 * Return asset URL.
	 *
	 * @param string $path
	 * @return string
	 */
	public static function get_asset_url( $path ) {
		return SUMO_SUBSCRIPTIONS_PLUGIN_URL . "/assets/{$path}" ;
	}

	/**
	 * Enqueue Product Variation switcher.
	 */
	public static function enqueue_variation_switcher() {
		self::enqueue_script( 'sumosubs-variation-switcher', self::get_asset_url( 'js/variation-switcher.js' ), array(
			'wp_ajax_url'                   => admin_url( 'admin-ajax.php' ),
			'switched_by'                   => is_admin() ? __( 'Admin', 'sumosubscriptions' ) : __( 'User', 'sumosubscriptions' ),
			'variation_switch_submit_nonce' => wp_create_nonce( 'save-swapped-variation' ),
			'variation_swapping_nonce'      => wp_create_nonce( 'variation-swapping' ),
			'i18n_default_attribute_select' => __( 'Select ', 'sumosubscriptions' ),
			'success_message'               => __( 'Subscription Variation has been Switched Successfully.', 'sumosubscriptions' ),
			'failure_message'               => __( 'Something went wrong.', 'sumosubscriptions' ),
			'notice_message'                => __( 'Please select the variation and try again.', 'sumosubscriptions' ),
		) ) ;
	}

	/**
	 * Enqueue Subscription styles.
	 */
	public static function enqueue_subscription_styles() {
		ob_start() ;
		sumosubscriptions_get_template( 'subscription-styles.php' ) ;
		$css = ob_get_clean() ;

		wp_register_style( 'sumosubs-styles', false, array(), SUMO_SUBSCRIPTIONS_VERSION ) ;
		wp_enqueue_style( 'sumosubs-styles' ) ;
		wp_add_inline_style( 'sumosubs-styles', $css ) ;
	}

	/**
	 * Perform script localization in backend.
	 */
	public static function admin_script() {
		global $post ;

		$screen    = get_current_screen() ;
		$screen_id = $screen ? $screen->id : '' ;

		switch ( $screen_id ) {
			case 'sumosubscriptions':
			case 'edit-sumosubscriptions':
				self::enqueue_script( 'sumosubs-admin', self::get_asset_url( 'js/admin/subscription-admin.js' ), array(
					'wp_ajax_url'                                       => admin_url( 'admin-ajax.php' ),
					'add_note_nonce'                                    => wp_create_nonce( 'add-subscription-note' ),
					'delete_note_nonce'                                 => wp_create_nonce( 'delete-subscription-note' ),
					'cancel_request_nonce'                              => wp_create_nonce( 'subscription-cancel-request' ),
					'is_synced'                                         => $post ? ( SUMO_Subscription_Synchronization::is_subscription_synced( $post->ID ) ? 'yes' : '' ) : '',
					'view_renewal_orders_text'                          => __( 'View Unpaid Renewal Order', 'sumosubscriptions' ),
					'display_dialog_upon_cancel'                        => 'yes' === get_option( 'sumo_display_dialog_upon_cancel' ),
					'display_dialog_upon_revoking_cancel'               => 'yes' === get_option( 'sumo_display_dialog_upon_revoking_cancel' ),
					'warning_message_upon_immediate_cancel'             => get_option( 'sumo_cancel_dialog_message' ),
					'warning_message_upon_at_the_end_of_billing_cancel' => get_option( 'sumo_cancel_at_the_end_of_billing_dialog_message' ),
					'warning_message_upon_on_the_scheduled_date_cancel' => get_option( 'sumo_cancel_on_the_scheduled_date_dialog_message' ),
					'warning_message_upon_revoking_cancel'              => get_option( 'sumo_revoking_cancel_confirmation_dialog_message' ),
					'warning_message_upon_invalid_date'                 => __( 'Please enter the Date and Try again !!', 'sumosubscriptions' ),
					'warning_message_before_pause'                      => __( 'This is a Synchronized Subscription and hence if you have paused this subscription, then the customer might not get the extended number of days based on the Pause duration once the subscription is resumed. Are you sure you want to Pause this subscription?', 'sumosubscriptions' ),
						), array( 'jquery-ui-datepicker' ) ) ;
				self::enqueue_variation_switcher() ;
				wp_dequeue_script( 'autosave' ) ;
				break ;
			case 'product':
				self::enqueue_script( 'sumosubs-admin-product', self::get_asset_url( 'js/admin/product-admin.js' ), array(
					'synchronize_mode'                                 => SUMO_Subscription_Synchronization::$sync_mode,
					'subscription_week_duration_options'               => sumo_get_subscription_duration_options( 'W', false ),
					'subscription_month_duration_options'              => sumo_get_subscription_duration_options( 'M', false ),
					'subscription_year_duration_options'               => sumo_get_subscription_duration_options( 'Y', false ),
					'subscription_day_duration_options'                => sumo_get_subscription_duration_options( 'D', false ),
					'synced_subscription_week_duration_options'        => SUMO_Subscription_Synchronization::get_duration_options( 'W' ),
					'synced_subscription_month_duration_options'       => SUMO_Subscription_Synchronization::get_duration_options( 'M', true ),
					'synced_subscription_year_duration_options'        => SUMO_Subscription_Synchronization::get_duration_options( 'Y', true ),
					'synced_subscription_month_duration_value_options' => SUMO_Subscription_Synchronization::get_duration_options( 'M' ),
					'synced_subscription_year_duration_value_options'  => SUMO_Subscription_Synchronization::get_duration_options( 'Y' ),
					'variations_per_page'                              => absint( apply_filters( 'woocommerce_admin_meta_boxes_variations_per_page', 15 ) ),
				) ) ;
				break ;
			case 'dashboard_page_sumosubs-welcome':
				self::enqueue_script( 'sumosubs-admin-welcome', self::get_asset_url( 'js/admin/welcome-admin.js' ) ) ;
				break ;
			case 'sumo-subscriptions_page_sumosubs-exporter':
				self::enqueue_script( 'sumosubs-admin-exporter', self::get_asset_url( 'js/admin/exporter-admin.js' ), array(
					'wp_ajax_url'    => admin_url( 'admin-ajax.php' ),
					'exporter_nonce' => wp_create_nonce( 'subscription-exporter' ),
						), array( 'jquery-ui-datepicker' ) ) ;
				break ;
		}

		if ( 'sumo-subscriptions_page_sumosubs-settings' === $screen_id ) {
			switch ( isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : '' ) {
				case 'order_subscription':
					self::enqueue_script( 'sumosubs-admin-ordersubscription', self::get_asset_url( 'js/admin/ordersubscription-admin.js' ), array(
						'subscription_week_duration_options'        => sumo_get_subscription_duration_options( 'W' ),
						'subscription_month_duration_options'       => sumo_get_subscription_duration_options( 'M' ),
						'subscription_year_duration_options'        => sumo_get_subscription_duration_options( 'Y' ),
						'subscription_day_duration_options'         => sumo_get_subscription_duration_options( 'D' ),
						'warning_message_upon_invalid_no_of_days'   => __( 'Please enter the valid number of days for Subscription Duration Value !!', 'sumosubscriptions' ),
						'warning_message_upon_invalid_no_of_weeks'  => __( 'Please enter the valid number of weeks for Subscription Duration Value !!', 'sumosubscriptions' ),
						'warning_message_upon_invalid_no_of_months' => __( 'Please enter the valid number of months for Subscription Duration Value !!', 'sumosubscriptions' ),
						'warning_message_upon_invalid_no_of_years'  => __( 'Please enter the valid number of years for Subscription Duration Value !!', 'sumosubscriptions' ),
						'warning_message_upon_max_recurring_cycle'  => __( 'Please select the valid number of maximum recurring cycle !!', 'sumosubscriptions' ),
					) ) ;
					break ;
				case 'synchronization':
					self::enqueue_script( 'sumosubs-admin-synchronization', self::get_asset_url( 'js/admin/synchronization-admin.js' ) ) ;
					break ;
				case 'upgrade_r_downgrade':
					self::enqueue_script( 'sumosubs-admin-upgrade-downgrade', self::get_asset_url( 'js/admin/upgrade-downgrade-admin.js' ) ) ;
					break ;
				case 'my_account':
					self::enqueue_script( 'sumosubs-admin-myaccount', self::get_asset_url( 'js/admin/myaccount-admin.js' ) ) ;
					break ;
				case 'bulk_action':
					self::enqueue_script( 'sumosubs-admin-bulk-action', self::get_asset_url( 'js/admin/bulk-action-admin.js' ), array(
						'wp_ajax_url'        => admin_url( 'admin-ajax.php' ),
						'update_nonce'       => wp_create_nonce( 'bulk-update-subscription' ),
						'optimization_nonce' => wp_create_nonce( 'bulk-update-optimization' ),
						'wp_create_nonce'    => wp_create_nonce( 'search-products' )
					) ) ;
					break ;
				case 'messages':
					self::enqueue_script( 'sumosubs-admin-messages', self::get_asset_url( 'js/admin/messages-admin.js' ) ) ;
					break ;
				case 'advanced':
					self::enqueue_script( 'sumosubs-admin-advanced', self::get_asset_url( 'js/admin/advanced-admin.js' ), array(
						'html_data_nonce' => wp_create_nonce( 'subscription-as-regular-html-data' ),
							), array( 'iris' ) ) ;
					break ;
				default:
					self::enqueue_script( 'sumosubs-admin-general', self::get_asset_url( 'js/admin/general-admin.js' ) ) ;
			}
		}

		if ( 'woocommerce_page_wc-settings' === $screen_id ) {
			switch ( isset( $_GET[ 'section' ] ) ? $_GET[ 'section' ] : '' ) {
				case 'sumo_paypal_preapproval':
					self::enqueue_script( 'sumosubs-admin-wc-payments-paypal-adaptive', self::get_asset_url( 'js/admin/wc-payments-paypal-adaptive-admin.js' ), array(
						'admin_notice' => __( 'Please do not leave any fields empty.', 'sumosubscriptions' )
					) ) ;
					break ;
				case 'sumo_paypal_reference_txns':
					self::enqueue_script( 'sumosubs-admin-wc-payments-paypal-reference', self::get_asset_url( 'js/admin/wc-payments-paypal-reference-admin.js' ), array(
						'wp_ajax_url'                    => admin_url( 'admin-ajax.php' ),
						'paypal_change_logo_button_text' => __( 'Change Logo', 'sumosubscriptions' ),
						'admin_notice'                   => __( 'Please upload the logo in valid image format, such as .gif, .jpg, or .png.', 'sumosubscriptions' )
					) ) ;
					wp_enqueue_media() ;
					break ;
				case 'sumo_stripe':
					self::enqueue_script( 'sumosubs-admin-wc-payments-stripe', self::get_asset_url( 'js/admin/wc-payments-stripe-admin.js' ) ) ;
					break ;
			}
		}
	}

	/**
	 * Load style in backend.
	 */
	public static function admin_style() {
		$screen    = get_current_screen() ;
		$screen_id = $screen ? $screen->id : '' ;

		if ( 'dashboard_page_sumosubs-welcome' === $screen_id ) {
			self::enqueue_style( 'sumosubs-admin-welcome', self::get_asset_url( 'css/welcome-admin.css' ) ) ;
		}

		if ( in_array( $screen_id, array( 'sumosubscriptions', 'edit-sumosubscriptions', 'shop_order', 'edit-shop_order', 'product' ) ) ) {
			self::enqueue_style( 'sumosubs-admin', self::get_asset_url( 'css/admin.css' ) ) ;
			self::enqueue_subscription_styles() ;
		}
	}

	/**
	 * Perform script localization in frontend.
	 *
	 * @global object $post
	 */
	public static function frontend_script() {
		global $post ;

		$product = is_product() ? wc_get_product( $post ) : false ;
		self::enqueue_script( 'sumosubs-single-product', self::get_asset_url( 'js/frontend/single-product.js' ), array(
			'wp_ajax_url'              => admin_url( 'admin-ajax.php' ),
			'get_product_nonce'        => wp_create_nonce( 'get-subscription-product-data' ),
			'get_variation_nonce'      => wp_create_nonce( 'get-subscription-variation-data' ),
			'product_id'               => $product ? $product->get_id() : '',
			'product_type'             => $product ? $product->get_type() : '',
			'default_add_to_cart_text' => $product ? $product->single_add_to_cart_text() : __( 'Add to cart', 'sumosubscriptions' ),
			'variation_data_template'  => SUMOSubscriptions_Variation_Data::get_template(),
		) ) ;

		if ( ( is_cart() && SUMO_Order_Subscription::show_subscribe_form_in_cart() ) || is_checkout() ) {
			self::enqueue_script( 'sumosubs-checkout', self::get_asset_url( 'js/frontend/checkout.js' ), array(
				'wp_ajax_url'                                 => admin_url( 'admin-ajax.php' ),
				'is_user_logged_in'                           => is_user_logged_in(),
				'current_page'                                => is_checkout() ? 'checkout' : 'cart',
				'update_order_subscription_nonce'             => wp_create_nonce( 'update-order-subscription' ),
				'can_user_subscribe'                          => SUMO_Order_Subscription::can_user_subscribe(),
				'default_order_subscription_duration'         => SUMO_Order_Subscription::$get_option[ 'default_duration_period' ],
				'default_order_subscription_duration_value'   => SUMO_Order_Subscription::$get_option[ 'default_duration_length' ],
				'default_order_subscription_installment'      => SUMO_Order_Subscription::$get_option[ 'default_recurring_length' ],
				'can_user_select_plan'                        => SUMO_Order_Subscription::$get_option[ 'can_user_select_plan' ],
				'subscription_week_duration_options'          => sumo_get_subscription_duration_options( 'W', true, SUMO_Order_Subscription::$get_option[ 'min_duration_length_user_can_select' ][ 'W' ], SUMO_Order_Subscription::$get_option[ 'max_duration_length_user_can_select' ][ 'W' ] ),
				'subscription_month_duration_options'         => sumo_get_subscription_duration_options( 'M', true, SUMO_Order_Subscription::$get_option[ 'min_duration_length_user_can_select' ][ 'M' ], SUMO_Order_Subscription::$get_option[ 'max_duration_length_user_can_select' ][ 'M' ] ),
				'subscription_year_duration_options'          => sumo_get_subscription_duration_options( 'Y', true, SUMO_Order_Subscription::$get_option[ 'min_duration_length_user_can_select' ][ 'Y' ], SUMO_Order_Subscription::$get_option[ 'max_duration_length_user_can_select' ][ 'Y' ] ),
				'subscription_day_duration_options'           => sumo_get_subscription_duration_options( 'D', true, SUMO_Order_Subscription::$get_option[ 'min_duration_length_user_can_select' ][ 'D' ], SUMO_Order_Subscription::$get_option[ 'max_duration_length_user_can_select' ][ 'D' ] ),
				'sync_ajax'                                   => 'yes' === get_option( 'sumo_sync_ajax_for_order_subscription', 'no' ),
				'maybe_prevent_from_hiding_guest_signup_form' => 'yes' === get_option( 'woocommerce_enable_guest_checkout' ) && 'yes' !== get_option( 'woocommerce_enable_signup_and_login_from_checkout' ),
			) ) ;
		}

		if ( is_account_page() || sumo_is_my_subscriptions_page() ) {
			self::enqueue_script( 'sumosubs-myaccount', self::get_asset_url( 'js/frontend/myaccount.js' ), array(
				'wp_ajax_url'                                       => admin_url( 'admin-ajax.php' ),
				'current_user_id'                                   => get_current_user_id(),
				'show_more_notes_label'                             => __( 'Show More', 'sumosubscriptions' ),
				'show_less_notes_label'                             => __( 'Show Less', 'sumosubscriptions' ),
				'wp_nonce'                                          => wp_create_nonce( 'subscriber-request' ),
				'subscriber_has_single_cancel_method'               => 1 === count( sumosubs_get_subscription_cancel_methods() ),
				'display_dialog_upon_cancel'                        => 'yes' === get_option( 'sumo_display_dialog_upon_cancel' ),
				'display_dialog_upon_revoking_cancel'               => 'yes' === get_option( 'sumo_display_dialog_upon_revoking_cancel' ),
				'warning_message_upon_immediate_cancel'             => get_option( 'sumo_cancel_dialog_message' ),
				'warning_message_upon_at_the_end_of_billing_cancel' => get_option( 'sumo_cancel_at_the_end_of_billing_dialog_message' ),
				'warning_message_upon_on_the_scheduled_date_cancel' => get_option( 'sumo_cancel_on_the_scheduled_date_dialog_message' ),
				'warning_message_upon_revoking_cancel'              => get_option( 'sumo_revoking_cancel_confirmation_dialog_message' ),
				'warning_message_upon_invalid_date'                 => __( 'Please enter the Date and Try again !!', 'sumosubscriptions' ),
				'warning_message_upon_turnoff_automatic_payments'   => __( 'Are you sure you want to turn off Automatic Subscription Renewal for this subscription?', 'sumosubscriptions' ),
				'warning_message_before_pause'                      => __( 'This is a Synchronized Subscription and hence if you have paused this subscription, then you might not get the extended number of days based on the Pause duration once the subscription is resumed. Are you sure you want to Pause this subscription?', 'sumosubscriptions' ),
				'failure_message'                                   => __( 'Something went wrong !!', 'sumosubscriptions' ),
					), array( 'jquery-ui-datepicker' ) ) ;

			self::enqueue_variation_switcher() ;
			self::enqueue_subscription_styles() ;
		}
	}

	/**
	 * Load style in frontend.
	 */
	public static function frontend_style() {
		self::enqueue_style( 'jquery-ui-style', self::get_asset_url( 'css/jquery-ui.css' ) ) ;
		self::enqueue_style( 'sumosubs-frontend', self::get_asset_url( 'css/frontend.css' ) ) ;
	}

	/**
	 * Load WC enqueues.
	 *
	 * @param array $screen_ids
	 * @return array
	 */
	public static function load_wc_enqueues( $screen_ids ) {
		$screen    = get_current_screen() ;
		$screen_id = $screen ? $screen->id : '' ;

		if ( in_array( $screen_id, array( 'sumosubscriptions', 'edit-sumosubscriptions', 'sumo-subscriptions_page_sumosubs-settings', 'sumo-subscriptions_page_sumosubs-exporter' ) ) ) {
			$screen_ids[] = $screen_id ;
		}

		return $screen_ids ;
	}

}

SUMOSubscriptions_Enqueues::init() ;
