<?php

/**
 * Advanced Settings.
 * 
 * @class SUMOSubscriptions_Advance_Settings
 */
class SUMOSubscriptions_Advance_Settings extends SUMO_Abstract_Subscription_Settings {

	/**
	 * SUMOSubscriptions_Advance_Settings constructor.
	 */
	public function __construct() {
		$this->id            = 'advanced' ;
		$this->label         = __( 'Advanced', 'sumosubscriptions' ) ;
		$this->custom_fields = array(
			'set_subscription_as_regular_product',
			'get_status_and_notes_color',
				) ;
		$this->settings      = $this->get_settings() ;
		$this->init() ;
	}

	/**
	 * Get settings array.
	 *
	 * @return array
	 */
	public function get_settings() {
		global $current_section ;

		return apply_filters( 'sumosubscriptions_get_' . $this->id . '_settings', array(
			array(
				'name' => __( 'Advanced Settings', 'sumosubscriptions' ),
				'type' => 'title',
				'id'   => 'sumo_advance_setting'
			),
			array(
				'name'    => __( 'Apply Subscription Fee for Already Purchased Users', 'sumosubscriptions' ),
				'id'      => 'sumosubs_apply_subscription_fee_by',
				'newids'  => 'sumosubs_apply_subscription_fee_by',
				'type'    => 'select',
				'std'     => 'time_of_purchase_fee',
				'default' => 'time_of_purchase_fee',
				'options' => array(
					'time_of_purchase_fee' => __( 'Subscription Fee at the Time of Purchase', 'sumosubscriptions' ),
					'current_fee'          => __( 'Current Subscription Fee', 'sumosubscriptions' ),
				),
			),
			array(
				'name'     => __( 'Display Price Message for Variable Product in Shop and Single Product Page', 'sumosubscriptions' ),
				'id'       => 'sumosubs_apply_variable_product_price_msg_based_on',
				'newids'   => 'sumosubs_apply_variable_product_price_msg_based_on',
				'type'     => 'select',
				'std'      => 'subscription-message',
				'default'  => 'subscription-message',
				'options'  => array(
					'subscription-message'     => __( 'Subscription Message', 'sumosubscriptions' ),
					'woocommerce-message'      => __( 'WooCommerce Message', 'sumosubscriptions' ),
					'non-subscription-message' => __( 'Price Range for Non Subscription Products', 'sumosubscriptions' ),
				),
				'desc'     => __( 'Use this option to display the price message for variable products when the variable product has both subscription and non-subscription variations', 'sumosubscriptions' ),
				'desc_tip' => true,
			),
			array(
				'name'     => __( 'Activate Free Trial', 'sumosubscriptions' ),
				'id'       => 'sumosubs_activate_free_trial_by',
				'newids'   => 'sumosubs_activate_free_trial_by',
				'type'     => 'select',
				'std'      => 'auto',
				'default'  => 'auto',
				'options'  => array(
					'auto'           => __( 'Automatically', 'sumosubscriptions' ),
					'admin_approval' => __( 'After Admin Approval', 'sumosubscriptions' ),
				),
				'desc_tip' => __( 'If "After Admin Approval" option is chosen, admin needs to activate subscription with free trial in edit subscription page.', 'sumosubscriptions' )
			),
			array(
				'name'     => __( 'Activate Subscription', 'sumosubscriptions' ),
				'id'       => 'sumosubs_activate_subscription_by',
				'newids'   => 'sumosubs_activate_subscription_by',
				'type'     => 'select',
				'std'      => 'auto',
				'default'  => 'auto',
				'options'  => array(
					'auto'           => __( 'Automatically', 'sumosubscriptions' ),
					'admin_approval' => __( 'After Admin Approval', 'sumosubscriptions' ),
				),
				'desc_tip' => __( 'If "After Admin Approval" option is chosen, admin needs to activate subscription in edit subscription page. Note: This option is not applicable for Trial/Synchronized Subscriptions.', 'sumosubscriptions' )
			),
			array(
				'name'    => __( 'Disable WooCommerce Emails for Subscription Orders', 'sumosubscriptions' ),
				'id'      => 'sumosubs_disabled_wc_order_emails',
				'newids'  => 'sumosubs_disabled_wc_order_emails',
				'class'   => 'wc-enhanced-select',
				'type'    => 'multiselect',
				'options' => array(
					'new'        => __( 'New order', 'sumosubscriptions' ),
					'processing' => __( 'Processing order', 'sumosubscriptions' ),
					'completed'  => __( 'Completed order', 'sumosubscriptions' ),
					'cancelled'  => __( 'Cancelled order', 'sumosubscriptions' ),
				),
				'std'     => array(),
				'default' => array(),
			),
			array(
				'type' => $this->get_custom_field_type( 'set_subscription_as_regular_product' ),
			),
			array(
				'name'    => __( 'Date and Time Format', 'sumosubscriptions' ),
				'id'      => 'sumo_set_subscription_date_time_format_as',
				'newids'  => 'sumo_set_subscription_date_time_format_as',
				'type'    => 'select',
				'std'     => 'default',
				'default' => 'default',
				'options' => array(
					'default'   => __( 'Default', 'sumosubscriptions' ),
					'wordpress' => __( 'WordPress Format', 'sumosubscriptions' ),
				),
			),
			array(
				'name'    => __( 'Timezone', 'sumosubscriptions' ),
				'id'      => 'sumo_set_subscription_timezone_as',
				'newids'  => 'sumo_set_subscription_timezone_as',
				'type'    => 'select',
				'std'     => 'default',
				'default' => 'default',
				'options' => array(
					'default'   => __( 'UTC+0', 'sumosubscriptions' ),
					'wordpress' => __( 'WordPress Timezone', 'sumosubscriptions' ),
				),
				'desc'    => __( 'Note: Only for display purpose in frontend.', 'sumosubscriptions' )
			),
			array(
				'name'    => __( 'Show Timezone', 'sumosubscriptions' ),
				'id'      => 'sumo_show_subscription_timezone',
				'newids'  => 'sumo_show_subscription_timezone',
				'type'    => 'checkbox',
				'std'     => 'yes',
				'default' => 'yes',
				'desc'    => __( 'Note: Only for display purpose in frontend.', 'sumosubscriptions' )
			),
			array(
				'name'    => __( 'Display Time', 'sumosubscriptions' ),
				'id'      => 'sumosubs_show_time_in_frontend',
				'newids'  => 'sumosubs_show_time_in_frontend',
				'type'    => 'select',
				'std'     => 'disable',
				'default' => 'disable',
				'options' => array(
					'disable' => __( 'Disable', 'sumosubscriptions' ),
					'enable'  => __( 'Enable', 'sumosubscriptions' ),
				),
				'desc'    => __( 'If enabled, time will be displayed in single product page, cart page and checkout page.', 'sumosubscriptions' ),
			),
			array(
				'name'    => __( 'Email to be Sent for Old Subscribers when they Purchase New Subscriptions', 'sumosubscriptions' ),
				'id'      => 'sumosubs_new_subscription_order_template_for_old_subscribers',
				'newids'  => 'sumosubs_new_subscription_order_template_for_old_subscribers',
				'type'    => 'select',
				'std'     => 'default',
				'default' => 'default',
				'options' => array(
					'default'         => __( 'Subscription New Order', 'sumosubscriptions' ),
					'old-subscribers' => __( 'Subscription New Order - Old Subscribers', 'sumosubscriptions' ),
				),
			),
			array(
				'name'   => __( 'Show Payment Gateways if the Subscription Order Amount is 0', 'sumosubscriptions' ),
				'desc'   => __( 'If enabled, payment gateways will be displayed in checkout page even  if the order amount is 0 when the cart contains subscription product. In case of automatic payments, subscriber doesn’t need to visit the site during payment renewals if this option is enabled', 'sumosubscriptions' ),
				'id'     => 'sumosubscription_show_payment_gateways_when_order_amt_zero',
				'std'    => 'yes',
				'type'   => 'checkbox',
				'newids' => 'sumosubscription_show_payment_gateways_when_order_amt_zero',
			),
			array(
				'name'    => __( 'Hide Specific Payment Gateways', 'sumosubscriptions' ),
				'id'      => 'sumosubs_payment_gateways_to_hide_when_order_amt_zero',
				'class'   => 'wc-enhanced-select',
				'std'     => array(),
				'default' => array(),
				'type'    => 'multiselect',
				'newids'  => 'sumosubs_payment_gateways_to_hide_when_order_amt_zero',
				'options' => sumosubs_get_active_payment_gateways(),
			),
			array(
				'name'              => __( 'Renewal Order Delay Time', 'sumosubscriptions' ),
				'desc'              => __( 'in Minutes', 'sumosubscriptions' ),
				'desc_tip'          => __( 'Please enter a number > 0 in order to create a minimum time difference between the initial order and the renewal order. Example: 1 day subscription.', 'sumosubscriptions' ),
				'type'              => 'number',
				'id'                => 'sumo_renewal_order_delay',
				'newids'            => 'sumo_renewal_order_delay',
				'css'               => 'width:80px',
				'std'               => '10',
				'default'           => '10',
				'custom_attributes' => array(
					'min'      => 0,
					'required' => 'required'
				)
			),
			array(
				'name'              => __( 'Number of Attempts to charge Automatic Payment during Overdue status', 'sumosubscriptions' ),
				'desc'              => __( 'times per day', 'sumosubscriptions' ),
				'desc_tip'          => __( 'This option controls the number of times the SUMO Subcriptions will try to charge for subscription renewal in case of a payment failure when the subscription in overdue status.', 'sumosubscriptions' ),
				'type'              => 'number',
				'id'                => 'sumo_auto_payment_in_overdue',
				'newids'            => 'sumo_auto_payment_in_overdue',
				'css'               => 'width:80px',
				'std'               => '2',
				'default'           => '2',
				'custom_attributes' => array(
					'min'      => 0,
					'required' => 'required'
				)
			),
			array(
				'name'              => __( 'Number of Attempts to charge Automatic Payment during Suspended status', 'sumosubscriptions' ),
				'desc'              => __( 'times per day', 'sumosubscriptions' ),
				'desc_tip'          => __( 'This option controls the number of times the SUMO Subcriptions will try to charge for subscription renewal in case of a payment failure when the subscription in suspend status.', 'sumosubscriptions' ),
				'type'              => 'number',
				'id'                => 'sumo_auto_payment_in_suspend',
				'newids'            => 'sumo_auto_payment_in_suspend',
				'css'               => 'width:80px',
				'std'               => '2',
				'default'           => '2',
				'custom_attributes' => array(
					'min'      => 0,
					'required' => 'required'
				)
			),
			array(
				'name'    => __( 'Custom CSS', 'sumosubscriptions' ),
				'id'      => 'sumo_subsc_custom_css',
				'newids'  => 'sumo_subsc_custom_css',
				'type'    => 'textarea',
				'css'     => 'height:200px;',
				'std'     => '',
				'default' => '',
			),
			array( 'type' => 'sectionend', 'id' => 'sumo_advance_setting' ),
			array(
				'name' => __( 'Automatic Payment Failure - Preapproval Access Revoked', 'sumosubscriptions' ),
				'type' => 'title',
				'id'   => 'sumo_automatic_failed_payment_preapproval_access_revoked'
			),
			array(
				'name'     => __( class_exists( 'FPRewardSystem' ) ? 'When Subscriber revokes access to Preapproval Key/Billing ID (PayPal Adaptive Payment, PayPal Reference Transactions, SUMO Reward Points Payment) then' : 'When Subscriber revokes access to Preapproval Key/Billing ID (PayPal Adaptive Payment, PayPal Reference Transactions) then', 'sumosubscriptions' ),
				'id'       => 'sumo_user_cancel_preapprove_key',
				'newids'   => 'sumo_user_cancel_preapprove_key',
				'type'     => 'select',
				'std'      => '1',
				'options'  => array(
					'1' => __( 'Cancel Subscription', 'sumosubscriptions' ),
					'2' => __( 'From email allow subscribers to choose the payment method', 'sumosubscriptions' ),
				),
				'desc'     => __( 'This option controls how SUMO Subscriptions should behave when the user manually revokes access to the preapproval/billing.', 'sumosubscriptions' ),
				'desc_tip' => true
			),
			array(
				'name'              => __( 'Maximum Waiting Time During Manual Payment Mode(Preapproval Access Revoked) Before Subscription Becomes Cancelled', 'sumosubscriptions' ),
				'type'              => 'number',
				'id'                => 'sumo_min_waiting_time_after_switched_to_manual_pay_when_preapproval_revoked',
				'newids'            => 'sumo_min_waiting_time_after_switched_to_manual_pay_when_preapproval_revoked',
				'std'               => '5',
				'default'           => '5',
				'css'               => 'width:80px',
				'desc'              => __( 'in days', 'sumosubscriptions' ),
				'custom_attributes' => array(
					'min'      => 1,
					'required' => 'required'
				)
			),
			array(
				'name'    => __( 'Send Payment Reminder Email During Waiting Time', 'sumosubscriptions' ),
				'id'      => 'sumo_payment_reminder_interval_after_preapproval_revoked',
				'newids'  => 'sumo_payment_reminder_interval_after_preapproval_revoked',
				'type'    => 'text',
				'std'     => '3,2,1',
				'default' => '3,2,1',
				'desc'    => __( 'day(s) before due date', 'sumosubscriptions' ),
			),
			array( 'type' => 'sectionend', 'id' => 'sumo_automatic_failed_payment_preapproval_access_revoked' ),
			array(
				'name' => __( 'Automatic Payment Failure - Charging Not Successful', 'sumosubscriptions' ),
				'type' => 'title',
				'id'   => 'sumo_automatic_failed_payment_charging_not_success'
			),
			array(
				'name'     => __( 'When Automatic Subscription goes to Cancel because of failed Payment, then', 'sumosubscriptions' ),
				'type'     => 'select',
				'id'       => 'sumo_cancel_automatic_subscription_goes_to',
				'newids'   => 'sumo_cancel_automatic_subscription_goes_to',
				'std'      => '1',
				'default'  => '1',
				'options'  => array(
					'1' => __( 'Cancel Subscription', 'sumosubscriptions' ),
					'2' => __( 'From email allow subscribers to choose the payment method', 'sumosubscriptions' ),
				),
				'desc'     => __( 'This option controls how SUMO Subscriptions should behave when the subscription status reaches failed because of a payment failure.', 'sumosubscriptions' ),
				'desc_tip' => true
			),
			array(
				'name'              => __( 'Maximum Waiting Time During Manual Payment Mode(Automatic Payment Failure) Before Subscription Becomes Cancelled', 'sumosubscriptions' ),
				'type'              => 'number',
				'id'                => 'sumo_min_waiting_time_after_switched_to_manual_pay',
				'newids'            => 'sumo_min_waiting_time_after_switched_to_manual_pay',
				'std'               => '5',
				'default'           => '5',
				'css'               => 'width:80px',
				'desc'              => __( 'in days', 'sumosubscriptions' ),
				'custom_attributes' => array(
					'min'      => 1,
					'required' => 'required'
				)
			),
			array(
				'name'    => __( 'Send Payment Reminder Email During Waiting Time', 'sumosubscriptions' ),
				'id'      => 'sumo_payment_reminder_interval_for_auto_to_manual_switch',
				'newids'  => 'sumo_payment_reminder_interval_for_auto_to_manual_switch',
				'type'    => 'text',
				'std'     => '3,2,1',
				'default' => '3,2,1',
				'desc'    => __( 'day(s) before due date', 'sumosubscriptions' ),
			),
			array( 'type' => 'sectionend', 'id' => 'sumo_automatic_failed_payment_charging_not_success' ),
			array(
				'name' => __( 'Subscription Number Prefix Settings', 'sumosubscriptions' ),
				'type' => 'title',
				'id'   => 'sumo_subscription_number_prefix_setting'
			),
			array(
				'name'              => __( 'Prefix', 'sumosubscriptions' ),
				'id'                => 'sumo_subscription_number_custom_prefix',
				'newids'            => 'sumo_subscription_number_custom_prefix',
				'type'              => 'text',
				'std'               => '',
				'default'           => '',
				'custom_attributes' => array(
					'maxlength' => 30
				),
				'desc'              => __( 'Prefix can be alpha-numeric', 'sumosubscriptions' ),
			),
			array( 'type' => 'sectionend', 'id' => 'sumo_subscription_number_prefix_setting' ),
			array(
				'name' => __( 'My Account Page Endpoints', 'sumosubscriptions' ),
				'type' => 'title',
				'id'   => 'sumo_subscription_my_account_endpoints_setting'
			),
			array(
				'name'              => __( 'My Subscriptions', 'sumosubscriptions' ),
				'id'                => 'sumo_my_account_subscriptions_endpoint',
				'newids'            => 'sumo_my_account_subscriptions_endpoint',
				'type'              => 'text',
				'std'               => 'sumo-subscriptions',
				'default'           => 'sumo-subscriptions',
				'custom_attributes' => array(
					'required' => 'required'
				),
			),
			array(
				'name'              => __( 'View Subscription', 'sumosubscriptions' ),
				'id'                => 'sumo_my_account_view_subscription_endpoint',
				'newids'            => 'sumo_my_account_view_subscription_endpoint',
				'type'              => 'text',
				'std'               => 'view-subscription',
				'default'           => 'view-subscription',
				'custom_attributes' => array(
					'required' => 'required'
				),
			),
			array( 'type' => 'sectionend', 'id' => 'sumo_subscription_my_account_endpoints_setting' ),
			array(
				'name' => __( 'Subscription Background Color Settings', 'sumosubscriptions' ),
				'type' => 'title',
				'id'   => 'sumo_subscription_bgcolor_setting'
			),
			array(
				'type' => $this->get_custom_field_type( 'get_status_and_notes_color' ),
			),
			array( 'type' => 'sectionend', 'id' => 'sumo_subscription_bgcolor_setting' ),
			array(
				'name' => __( 'Experimental Settings', 'sumosubscriptions' ),
				'type' => 'title',
				'id'   => 'sumo_subscription_experimental_settings',
			),
			array(
				'name'     => __( 'Use Subscription Variation Form Template.', 'sumosubscriptions' ),
				'id'       => 'sumosubs_variation_data_template',
				'newids'   => 'sumosubs_variation_data_template',
				'type'     => 'select',
				'options'  => array(
					'from-woocommerce' => __( 'From WooCommerce', 'sumosubscriptions' ),
					'from-plugin'      => __( 'From Plugin', 'sumosubscriptions' ),
				),
				'std'      => 'from-woocommerce',
				'default'  => 'from-woocommerce',
				'desc'     => __( 'If the Subscription variations data not displaying in Single Product page, then try using "From Plugin" option.', 'sumosubscriptions' ),
				'desc_tip' => true,
			),
			array( 'type' => 'sectionend', 'id' => 'sumo_subscription_experimental_settings' ),
				) ) ;
	}

	/**
	 * Save the custom options once.
	 */
	public function custom_types_add_options( $posted = null ) {
		add_option( 'sumo_subscription_as_regular_product_defined_rules', array() ) ;
		add_option( 'sumo_subscription_custom_bgcolor', array() ) ;
	}

	/**
	 * Delete the custom options.
	 */
	public function custom_types_delete_options( $posted = null ) {
		delete_option( 'sumo_subscription_as_regular_product_defined_rules' ) ;
		delete_option( 'sumo_subscription_custom_bgcolor' ) ; //@since 4.9
		//backward compatibility
		delete_option( 'sumo_choose_subsc_status_color_trial' ) ;
		delete_option( 'sumo_choose_subsc_status_color_pause' ) ;
		delete_option( 'sumo_choose_subsc_status_color_active' ) ;
		delete_option( 'sumo_settings_choose_subs_status_color_overdue' ) ;
		delete_option( 'sumo_choose_subsc_status_color_pending' ) ;
		delete_option( 'sumo_choose_subsc_status_color_suspend' ) ;
		delete_option( 'sumo_choose_subsc_status_color_cancel' ) ;
		delete_option( 'sumo_choose_subsc_status_color_pending_cancellation' ) ;
		delete_option( 'sumo_settings_choose_subs_status_color_failed' ) ;
		delete_option( 'sumo_choose_subsc_status_color_expire' ) ;
		delete_option( 'sumo_choose_subsc_notes_color_processing' ) ;
		delete_option( 'sumo_choose_subsc_notes_color_success' ) ;
		delete_option( 'sumo_choose_subsc_notes_color_pending' ) ;
		delete_option( 'sumo_choose_subsc_notes_color_failure' ) ;
	}

	/**
	 * Save custom settings.
	 */
	public function custom_types_save( $posted ) {
		$data = array() ;

		if ( isset( $posted[ 'selected_subscription' ] ) ) {
			$selected_subscription = array_map( 'wc_clean', $posted[ 'selected_subscription' ] ) ;
			$selected_userrole     = array_map( 'wc_clean', $posted[ 'selected_userrole' ] ) ;

			foreach ( $selected_subscription as $i => $name ) {
				if ( ! isset( $selected_subscription[ $i ] ) || ! isset( $selected_userrole[ $i ] ) ) {
					continue ;
				}

				$data[] = array(
					'selected_subscription' => $selected_subscription[ $i ],
					'selected_userrole'     => $selected_userrole[ $i ],
						) ;
			}
		}
		update_option( 'sumo_subscription_as_regular_product_defined_rules', $data ) ;

		if ( isset( $posted[ 'subscription_bgcolor' ] ) ) {
			$colors        = array() ;
			$posted_colors = array_map( 'wc_clean', $posted[ 'subscription_bgcolor' ] ) ;
			foreach ( $posted_colors as $key => $color ) {
				$colors[ $key ] = str_replace( '#', '', $color ) ;
			}

			update_option( 'sumo_subscription_custom_bgcolor', $colors ) ;
		}
	}

	/**
	 * Custom type field.
	 */
	public function get_status_and_notes_color() {
		$custom_bgcolor = sumosubs_get_custom_bgcolor() ;

		$status_data = array(
			'_trial'                 => __( 'Trial', 'sumosubscriptions' ),
			'_pause'                 => __( 'Pause', 'sumosubscriptions' ),
			'_active'                => __( 'Active', 'sumosubscriptions' ),
			'_overdue'               => __( 'Overdue', 'sumosubscriptions' ),
			'_pending'               => __( 'Pending', 'sumosubscriptions' ),
			'_suspended'             => __( 'Suspended', 'sumosubscriptions' ),
			'_cancelled'             => __( 'Cancelled', 'sumosubscriptions' ),
			'_pending_cancel'        => __( 'Pending Cancellation', 'sumosubscriptions' ),
			'_pending_authorization' => __( 'Pending Authorization', 'sumosubscriptions' ),
			'_failed'                => __( 'Failed', 'sumosubscriptions' ),
			'_expired'               => __( 'Expired', 'sumosubscriptions' ),
				) ;
		$notes_data  = array(
			'n_processing' => __( 'Processing', 'sumosubscriptions' ),
			'n_success'    => __( 'Success', 'sumosubscriptions' ),
			'n_pending'    => __( 'Pending', 'sumosubscriptions' ),
			'n_failure'    => __( 'Failure', 'sumosubscriptions' ),
				) ;
		?>
		<tr valign="top">
			<th scope="row" class="titledesc"><?php esc_html_e( 'For Subscription Status', 'sumosubscriptions' ) ; ?></th>
			<td class="forminp">
				<table class="widefat">
					<?php
					$count       = 0 ;
					foreach ( $status_data as $id => $label ) {
						if ( 0 === $count % 4 ) {
							?>
							<tr></tr>
							<?php
						}
						?>
						<td style="font-weight: bold;"><?php echo esc_html( $label ) ; ?>:</td>
						<td><span class="colorpickpreview" style="background: #<?php echo esc_attr( $custom_bgcolor[ $id ] ) ; ?>">&nbsp;</span><input class="colorpick" style="width: 80px;" name="subscription_bgcolor[<?php echo esc_attr( $id ) ; ?>]" id="<?php echo esc_attr( $id ) ; ?>" type="text" value="<?php echo esc_attr( $custom_bgcolor[ $id ] ) ; ?>"></td>
						<?php
						$count ++ ;
					}
					?>
				</table>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc"><?php esc_html_e( 'For Subscription Notes', 'sumosubscriptions' ) ; ?></th>
			<td class="forminp">
				<table class="widefat">
					<tr>
						<?php foreach ( $notes_data as $id => $label ) : ?>
							<td style="font-weight: bold;"><?php echo esc_html( $label ) ; ?>:</td>
							<td><span class="colorpickpreview" style="background: #<?php echo esc_attr( $custom_bgcolor[ $id ] ) ; ?>">&nbsp;</span><input class="colorpick" style="width: 80px;" name="subscription_bgcolor[<?php echo esc_attr( $id ) ; ?>]" id="<?php echo esc_attr( $id ) ; ?>" type="text" value="<?php echo esc_attr( $custom_bgcolor[ $id ] ) ; ?>"></td>
						<?php endforeach ; ?>
					</tr>
				</table>
			</td>
		</tr>
		<?php
	}

	/**
	 * Custom type field.
	 */
	public function set_subscription_as_regular_product() {
		?>
		<tr valign="top">
			<th scope="row" class="titledesc"><?php esc_html_e( 'Set Subscription Product as Regular Product for Specific Userrole(s)', 'sumosubscriptions' ) ; ?></th>
			<td class="forminp" id="sumosubscription_set_as_regular">
				<span class="woocommerce-help-tip" data-tip="<?php esc_attr_e( 'Here you can set rules for displaying subscription product(s) as regular product(s) for specific userrole(s). You can\'t add membership plan accessible subscription products created using SUMO Memberships plugin here.', 'sumosubscriptions' ) ; ?>"></span>
				<table class="widefat sortable striped">
					<thead>
						<tr>
							<th class="sort" style="width: 0.1%">&nbsp;</th>
							<th><?php esc_html_e( 'Select Subscription Product(s)', 'sumosubscriptions' ) ; ?></th>
							<th><?php esc_html_e( 'Select User Role(s)', 'sumosubscriptions' ) ; ?></th>
							<th>&nbsp;</th>
						</tr>
					</thead>
					<tbody class="defined_rules">
						<?php
						$i             = 0 ;
						$defined_rules = get_option( 'sumo_subscription_as_regular_product_defined_rules', array() ) ;

						if ( $defined_rules ) {
							foreach ( $defined_rules as $rule ) {
								if ( ! isset( $rule[ 'selected_subscription' ] ) ) {
									continue ;
								}

								$i ++ ;
								?>
								<tr class="defined_rule">
									<td class="sort"></td>
									<td>
										<?php
										sumosubs_wc_search_field( array(
											'class'       => 'wc-product-search',
											'action'      => 'sumosubscription_json_search_subscription_products_and_variations',
											'id'          => 'selected_subscription_' . $i,
											'name'        => 'selected_subscription[' . $i . ']',
											'type'        => 'product',
											'selected'    => empty( $rule[ 'selected_subscription' ] ) ? false : true,
											'options'     => $rule[ 'selected_subscription' ],
											'placeholder' => __( 'Search for a subscription product&hellip;', 'sumosubscriptions' ),
												), true ) ;
										?>
									</td>
									<td>
										<?php
										sumosubs_wc_enhanced_select_field( array(
											'id'       => 'selected_userrole_' . $i,
											'name'     => 'selected_userrole[' . $i . ']',
											'selected' => $rule[ 'selected_userrole' ],
											'options'  => sumosubs_user_roles( true )
												), true ) ;
										?>
									</td>
									<td><a href="#" class="remove_row button">X</a></td>    
								</tr>
								<?php
							}
						}
						?>
					</tbody>
					<tfoot>
						<tr>
							<th><span class="spinner"></span></th>
							<th colspan="3"><a href="#" class="add button"><?php esc_html_e( 'Add Rule', 'sumosubscriptions' ) ; ?></a></th>
						</tr>
					</tfoot>
				</table>                
			</td>
		</tr>
		<?php
	}

}

return new SUMOSubscriptions_Advance_Settings() ;
