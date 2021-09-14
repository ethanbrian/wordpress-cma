<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly
}

/**
 * Handle Subscription Ajax Event.
 * 
 * @class SUMOSubscriptions_Ajax
 */
class SUMOSubscriptions_Ajax {

	/**
	 * Init SUMOSubscriptions_Ajax.
	 */
	public static function init() {
		//Get Ajax Events.
		$ajax_events = array(
			'add_subscription_note'                             => false,
			'delete_subscription_note'                          => false,
			'get_subscribed_optional_plans_by_user'             => true,
			'subscriber_request'                                => false,
			'cancel_request'                                    => false,
			'checkout_order_subscription'                       => true,
			'get_subscription_variation_attributes_upon_switch' => false,
			'save_swapped_subscription_variation'               => false,
			'init_data_export'                                  => false,
			'handle_exported_data'                              => false,
			'bulk_update_product_meta'                          => false,
			'optimize_bulk_updation_of_product_meta'            => false,
			'get_subscription_as_regular_html_data'             => false,
			'json_search_subscription_products_and_variations'  => false,
			'json_search_downloadable_products_and_variations'  => false,
			'json_search_customers_by_email'                    => false,
				) ;

		foreach ( $ajax_events as $ajax_event => $nopriv ) {
			add_action( "wp_ajax_sumosubscription_{$ajax_event}", __CLASS__ . "::{$ajax_event}" ) ;

			if ( $nopriv ) {
				add_action( "wp_ajax_nopriv_sumosubscription_{$ajax_event}", __CLASS__ . "::{$ajax_event}" ) ;
			}
		}
	}

	/**
	 * Admin manually add subscription notes.
	 */
	public static function add_subscription_note() {
		check_ajax_referer( 'add-subscription-note', 'security' ) ;

		$posted = $_POST ;
		$note   = sumo_add_subscription_note( wc_clean( wp_unslash( $posted[ 'content' ] ) ), absint( wp_unslash( $posted[ 'post_id' ] ) ), 'processing', __( 'Admin Manually Added Note', 'sumosubscriptions' ) ) ;
		$note   = sumosubs_get_subscription_note( $note ) ;

		if ( $note ) {
			include 'admin/views/html-admin-subscription-note.php' ;
		}
		die() ;
	}

	/**
	 * Admin manually delete subscription notes.
	 */
	public static function delete_subscription_note() {
		check_ajax_referer( 'delete-subscription-note', 'security' ) ;
		$posted = $_POST ;
		wp_send_json( wp_delete_comment( absint( wp_unslash( $posted[ 'delete_id' ] ) ), true ) ) ;
	}

	/**
	 * Get optional Subscription plan subscribed by User in product page
	 */
	public static function get_subscribed_optional_plans_by_user() {
		check_ajax_referer( 'get-subscription-product-data', 'security' ) ;

		$posted         = $_POST ;
		$product_id     = absint( wp_unslash( $posted[ 'product_id' ] ) ) ;
		$selected_plans = wc_clean( wp_unslash( $posted[ 'selected_plans' ] ) ) ;
		if ( ! $product_id ) {
			die() ;
		}

		$subscription_plan = sumo_get_subscription_plan( 0, $product_id ) ;

		if ( in_array( 'set_trial', $selected_plans ) ) {
			$subscription_plan[ 'trial_status' ] = '1' ;
		}

		if ( in_array( 'set_signup_fee', $selected_plans ) ) {
			$subscription_plan[ 'signup_status' ] = '1' ;
		}

		wp_send_json( array(
			/* translators: 1: label 2: initial payment date */
			'next_payment_sync_on' => '1' === $subscription_plan[ 'synchronization_status' ] ? sprintf( '<p id="sumosubs_initial_synced_payment_date">%s<strong>%s</strong></p>', __( 'Next Payment on: ', 'sumosubscriptions' ), SUMO_Subscription_Synchronization::get_initial_payment_date( $product_id, true ) ) : '',
			'subscribed_plan'      => sumo_display_subscription_plan( 0, 0, 0, false, $subscription_plan )
		) ) ;
	}

	public static function subscriber_request() {
		check_ajax_referer( 'subscriber-request', 'security' ) ;

		$posted              = $_POST ;
		$subscription_id     = absint( wp_unslash( $posted[ 'subscription_id' ] ) ) ;
		$requested_by        = wc_clean( wp_unslash( $posted[ 'requested_by' ] ) ) ;
		$next_payment_date   = get_post_meta( $subscription_id, 'sumo_get_next_payment_date', true ) ;
		$renewal_order_id    = absint( get_post_meta( $subscription_id, 'sumo_get_renewal_id', true ) ) ;
		$parent_order_id     = absint( get_post_meta( $subscription_id, 'sumo_get_parent_order_id', true ) ) ;
		$subscription_status = get_post_meta( $subscription_id, 'sumo_get_status', true ) ;

		try {
			switch ( wc_clean( wp_unslash( $posted[ 'request' ] ) ) ) {
				case 'pause':
					$auto_resume_on = wc_clean( wp_unslash( $posted[ 'auto_resume_on' ] ) ) ;

					if ( empty( $auto_resume_on ) || ( ! empty( $auto_resume_on ) && ! sumosubs_is_valid_date( $auto_resume_on, 'Y-m-d' ) ) ) {
						throw new Exception( __( 'Please enter the valid date to resume the subscription!!', 'sumosubscriptions' ) ) ;
					}

					sumo_pause_subscription( $subscription_id, '', $requested_by ) ;

					//Manage Automatic Resume
					if ( ! empty( $auto_resume_on ) ) {
						$cron_event = new SUMO_Subscription_Cron_Event( $subscription_id ) ;
						$cron_event->schedule_automatic_resume( $auto_resume_on ) ;
						add_post_meta( $subscription_id, 'sumo_subscription_auto_resume_scheduled_on', $auto_resume_on ) ;
					}

					do_action( 'sumosubscriptions_pause_subscription', $subscription_id, $parent_order_id ) ;
					break ;
				case 'resume':
					sumo_resume_subscription( $subscription_id, $requested_by ) ;
					do_action( 'sumosubscriptions_active_subscription', $subscription_id, $parent_order_id ) ;
					break ;
				case 'cancel-immediate':
					update_post_meta( $subscription_id, 'sumo_subscription_cancel_method_requested_by', $requested_by ) ;
					update_post_meta( $subscription_id, 'sumo_subscription_requested_cancel_method', 'immediate' ) ;
					sumo_cancel_subscription( $subscription_id, '', $requested_by ) ;

					do_action( 'sumosubscriptions_cancel_subscription', $subscription_id, $parent_order_id ) ;
					break ;
				case 'cancel-at-the-end-of-billing-cycle':
					if ( apply_filters( 'sumosubscriptions_schedule_cancel', true, $subscription_id, $parent_order_id ) ) {
						if ( in_array( $subscription_status, array( 'Trial', 'Active' ) ) ) {
							update_post_meta( $subscription_id, 'sumo_subscription_previous_status', $subscription_status ) ;
						}

						delete_post_meta( $subscription_id, 'sumo_subscription_cancellation_scheduled_on' ) ;
						update_post_meta( $subscription_id, 'sumo_get_status', 'Pending_Cancellation' ) ;
						update_post_meta( $subscription_id, 'sumo_subscription_cancel_method_requested_by', $requested_by ) ;
						update_post_meta( $subscription_id, 'sumo_subscription_requested_cancel_method', 'end_of_billing_cycle' ) ;

						sumo_add_subscription_note( __( 'Subscription cancel request submitted. And it is set to Cancel at the End of this Billing Cycle.', 'sumosubscriptions' ), $subscription_id, 'success', __( 'Cancelling at the End of Billing Cycle', 'sumosubscriptions' ) ) ;
						sumo_trigger_subscription_email( 'subscription_cancel_request_submitted', null, $subscription_id ) ;

						$cron_event = new SUMO_Subscription_Cron_Event( $subscription_id ) ;
						$cron_event->unset_events() ;
						$cron_event->schedule_cancel_notify( $renewal_order_id, 0, $next_payment_date, true ) ;
					}
					break ;
				case 'cancel-on-scheduled-date':
					$scheduled_date_to_cancel = wc_clean( wp_unslash( $posted[ 'scheduled_date_to_cancel' ] ) ) ;
					$scheduled_time           = sumo_get_subscription_timestamp( $scheduled_date_to_cancel ) ;
					$next_payment_time        = sumo_get_subscription_timestamp( $next_payment_date ) ;

					if ( $scheduled_time < sumo_get_subscription_timestamp() || $scheduled_time > $next_payment_time ) {
						wp_send_json( array(
							'result' => 'failure',
							'notice' => esc_html__( 'Selected date must be within current billing cycle !!', 'sumosubscriptions' ),
						) ) ;
					}

					if ( apply_filters( 'sumosubscriptions_schedule_cancel', true, $subscription_id, $parent_order_id ) ) {
						if ( in_array( $subscription_status, array( 'Trial', 'Active' ) ) ) {
							update_post_meta( $subscription_id, 'sumo_subscription_previous_status', $subscription_status ) ;
						}
						update_post_meta( $subscription_id, 'sumo_get_status', 'Pending_Cancellation' ) ;
						update_post_meta( $subscription_id, 'sumo_subscription_cancellation_scheduled_on', $scheduled_date_to_cancel ) ;
						update_post_meta( $subscription_id, 'sumo_subscription_cancel_method_requested_by', $requested_by ) ;
						update_post_meta( $subscription_id, 'sumo_subscription_requested_cancel_method', 'scheduled_date' ) ;

						/* translators: 1: scheduled date */
						sumo_add_subscription_note( sprintf( __( 'Subscription cancel request submitted. And it is set to Cancel on the Scheduled Date <b>%s</b>.', 'sumosubscriptions' ), $scheduled_date_to_cancel ), $subscription_id, 'success', __( 'Cancelling on the Scheduled Date', 'sumosubscriptions' ) ) ;
						sumo_trigger_subscription_email( 'subscription_cancel_request_submitted', null, $subscription_id ) ;

						$cron_event = new SUMO_Subscription_Cron_Event( $subscription_id ) ;
						$cron_event->unset_events() ;
						$cron_event->schedule_cancel_notify( $renewal_order_id, 0, $scheduled_date_to_cancel, true ) ;
					}
					break ;
				case 'cancel-revoke':
					sumosubs_revoke_cancel_request( $subscription_id, __( 'User has Revoked the Cancel request.', 'sumosubscriptions' ) ) ;
					break ;
				case 'turnoff-auto':
					if ( 'auto' === sumo_get_payment_type( $subscription_id ) && apply_filters( 'sumosubscriptions_revoke_automatic_subscription', true, $subscription_id, $parent_order_id ) ) {
						sumo_save_subscription_payment_info( $parent_order_id, array(
							'payment_type' => 'manual'
						) ) ;

						$cron_event = new SUMO_Subscription_Cron_Event( $subscription_id ) ;
						$cron_event->unset_events( array(
							'automatic_pay',
							'notify_invoice_reminder',
							'switch_to_manual_pay_mode',
							'retry_automatic_pay_in_overdue',
							'retry_automatic_pay_in_suspended',
						) ) ;

						if ( sumosubs_unpaid_renewal_order_exists( $subscription_id ) ) {
							$cron_event->schedule_next_eligible_payment_failed_status() ;
							$cron_event->schedule_reminders( $renewal_order_id, $next_payment_date ) ;
						}

						sumo_add_subscription_note( __( 'Subscriber turned off their automatic charging for subscription renewals.', 'sumosubscriptions' ), $subscription_id, 'success', __( 'Turnedoff Auto Payments', 'sumosubscriptions' ) ) ;
						sumo_trigger_subscription_email( 'subscription_turnoff_automatic_payments_success', null, $subscription_id ) ;

						do_action( 'sumosubscriptions_automatic_subscription_is_revoked', $subscription_id, $parent_order_id ) ;
						wp_send_json( array(
							'result'   => 'success',
							'redirect' => sumo_get_subscription_endpoint_url( $subscription_id ),
							'notice'   => esc_html__( 'You have successfully turned off your Automatic Subscription Renewal for this subscription!!', 'sumosubscriptions' ),
						) ) ;
					}
					break ;
				case 'resubscribe':
					$redirect = SUMO_Subscription_Resubscribe::do_resubscribe( $subscription_id ) ;
					wp_send_json( array(
						'result'   => 'success',
						'redirect' => $redirect,
					) ) ;
					break ;
				case 'quantity-change':
					$new_qty  = absint( wp_unslash( $posted[ 'quantity' ] ) ) ;

					if ( ! $new_qty ) {
						throw new Exception( __( 'Please enter the valid product quantity!!', 'sumosubscriptions' ) ) ;
					}

					$subscription_plan = ( array ) get_post_meta( $subscription_id, 'sumo_subscription_product_details', true ) ;
					$old_qty           = absint( $subscription_plan[ 'product_qty' ] ) ;

					if ( $new_qty !== $old_qty ) {
						$subscription_plan[ 'product_qty' ] = $new_qty ;
						update_post_meta( $subscription_id, 'sumo_subscription_product_details', $subscription_plan ) ;
						/* translators: 1: old qty 2: new qty */
						sumo_add_subscription_note( sprintf( __( 'Customer has changed the subscription quantity from <b>%1$s</b> to <b>%2$s</b>.', 'sumosubscriptions' ), $old_qty, $new_qty ), $subscription_id, 'success', __( 'Subscription Qty Changed', 'sumosubscriptions' ) ) ;
						do_action( 'sumosubscriptions_subscription_qty_changed', $new_qty, $subscription_id, $subscription_plan, $parent_order_id ) ;
					}
					break ;
			}

			wp_send_json( array(
				'result'   => 'success',
				'redirect' => sumo_get_subscription_endpoint_url( $subscription_id ),
			) ) ;
		} catch ( Exception $e ) {
			wp_send_json( array(
				'result' => 'failure',
				'notice' => esc_html( $e->getMessage() ),
			) ) ;
		}
	}

	/**
	 * Cancel request by Admin. Cancelling Subscription by Immediately/End of Billing Cycle/Scheduled Date 
	 */
	public static function cancel_request() {
		check_ajax_referer( 'subscription-cancel-request', 'security' ) ;

		$posted              = $_POST ;
		$subscription_id     = absint( wp_unslash( $posted[ 'subscription_id' ] ) ) ;
		$requested_method    = wc_clean( wp_unslash( $posted[ 'cancel_method_requested' ] ) ) ;
		$requested_by        = wc_clean( wp_unslash( $posted[ 'cancel_method_requested_by' ] ) ) ;
		$next_due_date       = get_post_meta( $subscription_id, 'sumo_get_next_payment_date', true ) ;
		$renewal_order_id    = absint( get_post_meta( $subscription_id, 'sumo_get_renewal_id', true ) ) ;
		$parent_order_id     = absint( get_post_meta( $subscription_id, 'sumo_get_parent_order_id', true ) ) ;
		$subscription_status = get_post_meta( $subscription_id, 'sumo_get_status', true ) ;

		switch ( $requested_method ) {
			case 'immediate':
				update_post_meta( $subscription_id, 'sumo_subscription_cancel_method_requested_by', $requested_by ) ;
				update_post_meta( $subscription_id, 'sumo_subscription_requested_cancel_method', $requested_method ) ;

				//Cancel Subscription
				sumo_cancel_subscription( $subscription_id, '', $requested_by ) ;
				//Trigger after Subscription gets Cancelled
				do_action( 'sumosubscriptions_cancel_subscription', $subscription_id, $parent_order_id ) ;
				wp_send_json( 'success' ) ;
				break ;
			case 'end_of_billing_cycle':
				if ( apply_filters( 'sumosubscriptions_schedule_cancel', true, $subscription_id, $parent_order_id ) ) {
					if ( in_array( $subscription_status, array( 'Trial', 'Active' ) ) ) {
						update_post_meta( $subscription_id, 'sumo_subscription_previous_status', $subscription_status ) ;
					}

					delete_post_meta( $subscription_id, 'sumo_subscription_cancellation_scheduled_on' ) ;
					update_post_meta( $subscription_id, 'sumo_get_status', 'Pending_Cancellation' ) ;
					update_post_meta( $subscription_id, 'sumo_subscription_cancel_method_requested_by', $requested_by ) ;
					update_post_meta( $subscription_id, 'sumo_subscription_requested_cancel_method', $requested_method ) ;

					sumo_add_subscription_note( __( 'Subscription cancel request submitted. And it is set to Cancel at the End of this Billing Cycle.', 'sumosubscriptions' ), $subscription_id, 'success', __( 'Cancelling at the End of Billing Cycle', 'sumosubscriptions' ) ) ;
					sumo_trigger_subscription_email( 'subscription_cancel_request_submitted', null, $subscription_id ) ;

					$cron_event = new SUMO_Subscription_Cron_Event( $subscription_id ) ;
					$cron_event->unset_events() ;
					$cron_event->schedule_cancel_notify( $renewal_order_id, 0, $next_due_date, true ) ;
				}
				wp_send_json( 'success' ) ;
				break ;
			case 'scheduled_date':
				$scheduled_date    = wc_clean( wp_unslash( $posted[ 'scheduled_date' ] ) ) ;
				$scheduled_time    = sumo_get_subscription_timestamp( $scheduled_date ) ;
				$next_payment_time = sumo_get_subscription_timestamp( $next_due_date ) ;

				if ( $scheduled_time < sumo_get_subscription_timestamp() || $scheduled_time > $next_payment_time ) {
					wp_send_json( esc_html__( 'Selected date must be within current billing cycle !!', 'sumosubscriptions' ) ) ;
				}

				if ( apply_filters( 'sumosubscriptions_schedule_cancel', true, $subscription_id, $parent_order_id ) ) {
					if ( in_array( $subscription_status, array( 'Trial', 'Active' ) ) ) {
						update_post_meta( $subscription_id, 'sumo_subscription_previous_status', $subscription_status ) ;
					}
					update_post_meta( $subscription_id, 'sumo_get_status', 'Pending_Cancellation' ) ;
					update_post_meta( $subscription_id, 'sumo_subscription_cancellation_scheduled_on', $scheduled_date ) ;
					update_post_meta( $subscription_id, 'sumo_subscription_cancel_method_requested_by', $requested_by ) ;
					update_post_meta( $subscription_id, 'sumo_subscription_requested_cancel_method', $requested_method ) ;

					/* translators: 1: scheduled date */
					sumo_add_subscription_note( sprintf( __( 'Subscription cancel request submitted. And it is set to Cancel on the Scheduled Date <b>%s</b>.', 'sumosubscriptions' ), $scheduled_date ), $subscription_id, 'success', __( 'Cancelling on the Scheduled Date', 'sumosubscriptions' ) ) ;
					sumo_trigger_subscription_email( 'subscription_cancel_request_submitted', null, $subscription_id ) ;

					$cron_event = new SUMO_Subscription_Cron_Event( $subscription_id ) ;
					$cron_event->unset_events() ;
					$cron_event->schedule_cancel_notify( $renewal_order_id, 0, $scheduled_date, true ) ;
				}
				wp_send_json( 'success' ) ;
				break ;
		}

		wp_send_json( 'failure' ) ;
	}

	/**
	 * Load Subscription Variation to be Switched in Admin Page and in My Account Page.
	 */
	public static function get_subscription_variation_attributes_upon_switch() {
		check_ajax_referer( 'variation-swapping', 'security' ) ;

		$posted                   = $_POST ;
		$subscription_id          = absint( wp_unslash( $posted[ 'post_id' ] ) ) ;
		$selected_attribute_key   = sanitize_title( wp_unslash( $posted[ 'selected_attribute_key' ] ) ) ;
		$selected_attribute_value = wc_clean( wp_unslash( $posted[ 'selected_attribute_value' ] ) ) ;
		$selected_attributes      = is_array( $posted[ 'selected_attributes' ] ) ? array_unique( $posted[ 'selected_attributes' ], SORT_REGULAR ) : array() ;
		$selected_attributes      = isset( $selected_attributes[ 0 ] ) ? $selected_attributes[ 0 ] : array() ;
		$matched_variation        = SUMO_Subscription_Variation_Switcher::get_matched_variation( $subscription_id, $selected_attributes ) ;

		if ( empty( $matched_variation ) ) {
			$altered_attributes                            = array() ;
			$altered_attributes[ $selected_attribute_key ] = $selected_attribute_value ;

			foreach ( $selected_attributes as $attribute_key => $attribute_value ) {
				if ( $attribute_key != $selected_attribute_key && $attribute_value != $selected_attribute_value ) {
					$altered_attributes[ $attribute_key ] = $attribute_value ;
				}
			}

			array_pop( $altered_attributes ) ;

			$matched_variation = SUMO_Subscription_Variation_Switcher::get_matched_variation( $subscription_id, $altered_attributes ) ;
			if ( empty( $matched_variation ) ) {
				$altered_attributes = array() ;
				$altered_attributes = array( $selected_attribute_key => $selected_attribute_value ) ;
				$matched_variation  = SUMO_Subscription_Variation_Switcher::get_matched_variation( $subscription_id, $altered_attributes ) ;
			}
		}

		wp_send_json( $matched_variation ) ;
	}

	/**
	 * Save Swapped Subscription Variation in Admin Page and in My Account Page.
	 */
	public static function save_swapped_subscription_variation() {
		check_ajax_referer( 'save-swapped-variation', 'security' ) ;

		$posted                          = $_POST ;
		$subscription_id                 = absint( wp_unslash( $posted[ 'post_id' ] ) ) ;
		$subscription_meta               = sumo_get_subscription_meta( $subscription_id ) ;
		$parent_order_id                 = get_post_meta( $subscription_id, 'sumo_get_parent_order_id', true ) ;
		$parent_order_item_data          = get_post_meta( $subscription_id, 'sumo_subscription_parent_order_item_data', true ) ;
		$subscriptions_from_parent_order = get_post_meta( $parent_order_id, 'sumo_subsc_get_available_postids_from_parent_order', true ) ;
		$payment_info                    = get_post_meta( $parent_order_id, 'sumosubscription_payment_order_information', true ) ;
		$response_code                   = '0' ;

		if ( isset( $subscription_meta[ 'productid' ] ) && is_array( $posted[ 'plan_matched_attributes_key' ] ) && is_array( $posted[ 'attribute_value_to_switch' ] ) && ! empty( $posted[ 'plan_matched_attributes_key' ] ) && ! empty( $posted[ 'attribute_value_to_switch' ] ) ) {
			$switch_variation_from = $subscription_meta[ 'productid' ] ;
			$parent_order          = wc_get_order( $parent_order_id ) ;
			$swap_variation        = false ;
			$attributes            = array() ;

			foreach ( $posted[ 'attribute_value_to_switch' ] as $each_attribute ) {
				$attributes[] = 'attribute_' . $each_attribute ;
			}

			//Prevent if User/Admin not selecting Attribute values on Submit.
			if ( $attributes == $posted[ 'plan_matched_attributes_key' ] ) {
				wp_send_json( '2' ) ;
			}

			//Get Variation ID from Variation attributes selected to switch by Admin/User.
			$new_variations         = array_combine( $posted[ 'plan_matched_attributes_key' ], $posted[ 'attribute_value_to_switch' ] ) ;
			$matched_variation_id   = SUMO_Subscription_Variation_Switcher::get_matched_variation( $subscription_id, $new_variations, true ) ;
			$switch_variation_to    = isset( $matched_variation_id[ 0 ] ) ? $matched_variation_id[ 0 ] : 0 ;
			$_switched_to_product   = wc_get_product( $switch_variation_to ) ;
			$_switched_from_product = wc_get_product( $switch_variation_from ) ;

			if ( $switch_variation_to > 0 ) {
				foreach ( $parent_order->get_items() as $item_id => $items ) {
					//Update Parent Order Details
					if ( $items[ 'variation_id' ] == $switch_variation_from && is_array( $_switched_to_product->get_variation_attributes() ) ) {
						//Update New Variation.
						wc_update_order_item_meta( $item_id, '_variation_id', $switch_variation_to ) ;
						//Update New Variation Attributes.
						foreach ( $new_variations as $key => $value ) {
							wc_update_order_item_meta( $item_id, str_replace( 'attribute_', '', $key ), $value ) ;
						}
						//Is New Variation updated successfull in the Order item meta.
						$swap_variation = wc_get_order_item_meta( $item_id, '_variation_id' ) == $switch_variation_to ;
					}
				}

				//Is Valid to process Variation Swap.
				if ( $swap_variation ) {
					//Swap Variation.
					unset( $subscriptions_from_parent_order[ $subscription_meta[ 'productid' ] ] ) ;
					$subscriptions_from_parent_order[ $switch_variation_to ] = absint( $subscription_id ) ;

					$payment_info[ $switch_variation_to ] = $payment_info[ $subscription_meta[ 'productid' ] ] ;
					unset( $payment_info[ $subscription_meta[ 'productid' ] ] ) ;

					if ( is_array( $parent_order_item_data ) ) {
						foreach ( $parent_order_item_data as $order_item_id => $data ) {
							if ( ! isset( $data[ 'id' ] ) ) {
								continue ;
							}

							if ( $subscription_meta[ 'productid' ] == $data[ 'id' ] ) {
								$parent_order_item_data[ $order_item_id ][ 'id' ] = $switch_variation_to ;
							}
						}
					}

					$subscription_meta[ 'productid' ] = $switch_variation_to ;
					update_post_meta( $subscription_id, 'sumo_subscription_product_details', $subscription_meta ) ;
					update_post_meta( $subscription_id, 'sumo_subscription_parent_order_item_data', $parent_order_item_data ) ;
					update_post_meta( $subscription_id, 'sumo_product_name', wc_get_product( $switch_variation_to )->get_title() ) ;
					update_post_meta( $parent_order_id, 'sumo_subsc_get_available_postids_from_parent_order', $subscriptions_from_parent_order ) ;
					update_post_meta( $parent_order_id, 'sumosubscription_payment_order_information', $payment_info ) ;

					/* translators: 1: switched by 2: from product name 3: to product name */
					$note = sprintf( __( '%1$s switched the Variation Subscription from <b>%2$s</b> to <b>%3$s</b>.', 'sumosubscriptions' ), wc_clean( wp_unslash( $posted[ 'switched_by' ] ) ), $_switched_from_product->get_formatted_name(), $_switched_to_product->get_formatted_name() ) ;
					sumo_add_subscription_note( $note, $subscription_id, 'success', __( 'Subscription Variation Switch', 'sumosubscriptions' ) ) ;

					//Success
					$response_code = '1' ;
				}
			}
		}

		wp_send_json( $response_code ) ;
	}

	/**
	 * Init data export
	 */
	public static function init_data_export() {
		check_ajax_referer( 'subscription-exporter', 'security' ) ;

		$export_databy = array() ;
		$posted        = $_POST ;
		parse_str( $posted[ 'exportDataBy' ], $export_databy ) ;

		$json_args = array() ;
		$args      = array(
			'type'     => 'sumosubscriptions',
			'status'   => 'publish',
			'order_by' => 'DESC',
				) ;

		if ( ! empty( $export_databy ) ) {
			if ( ! empty( $export_databy[ 'subscription_from_date' ] ) ) {
				$to_date              = ! empty( $export_databy[ 'subscription_to_date' ] ) ? strtotime( $export_databy[ 'subscription_to_date' ] ) : strtotime( gmdate( 'Y-m-d' ) ) ;
				$args[ 'date_query' ] = array(
					array(
						'after'     => gmdate( 'Y-m-d', strtotime( $export_databy[ 'subscription_from_date' ] ) ),
						'before'    => array(
							'year'  => gmdate( 'Y', $to_date ),
							'month' => gmdate( 'm', $to_date ),
							'day'   => gmdate( 'd', $to_date ),
						),
						'inclusive' => true,
					),
						) ;
			}

			$meta_query = array() ;
			if ( ! empty( $export_databy[ 'subscription_statuses' ] ) ) {
				$meta_query[] = array(
					'key'     => 'sumo_get_status',
					'value'   => ( array ) $export_databy[ 'subscription_statuses' ],
					'compare' => 'IN'
						) ;
			}

			if ( ! empty( $export_databy[ 'subscription_buyers' ] ) ) {
				$meta_query[] = array(
					'key'     => 'sumo_buyer_email',
					'value'   => ( array ) $export_databy[ 'subscription_buyers' ],
					'compare' => 'IN'
						) ;
			}

			if ( ! empty( $meta_query ) ) {
				$args[ 'meta_query' ] = array( 'relation' => 'AND' ) + $meta_query ;
			}
		}

		$subscriptions = sumosubscriptions()->query->get( $args ) ;

		if ( count( $subscriptions ) <= 1 ) {
			if ( ! empty( $export_databy[ 'subscription_products' ] ) ) {
				foreach ( $subscriptions as $key => $subscription_id ) {
					$subscription = sumo_get_subscription( $subscription_id ) ;

					if ( $subscription && ! in_array( $subscription->get_subscribed_product(), ( array ) $export_databy[ 'subscription_products' ] ) ) {
						unset( $subscriptions[ $key ] ) ;
					}
				}
			}

			$json_args[ 'export' ]         = 'done' ;
			$json_args[ 'generated_data' ] = array_map( array( 'SUMO_Subscription_Exporter', 'generate_data' ), $subscriptions ) ;
			$json_args[ 'redirect_url' ]   = SUMO_Subscription_Exporter::get_download_url( $json_args[ 'generated_data' ] ) ;
		} else {
			$json_args[ 'export' ]        = 'processing' ;
			$json_args[ 'original_data' ] = $subscriptions ;
		}

		wp_send_json( wp_parse_args( $json_args, array(
			'export'         => '',
			'generated_data' => array(),
			'original_data'  => array(),
			'redirect_url'   => SUMO_Subscription_Exporter::get_exporter_page_url(),
		) ) ) ;
	}

	/**
	 * Handle exported data
	 */
	public static function handle_exported_data() {
		check_ajax_referer( 'subscription-exporter', 'security' ) ;

		$export_databy = array() ;
		$posted        = $_POST ;
		parse_str( $posted[ 'exportDataBy' ], $export_databy ) ;

		$subscriptions = array_filter( ( array ) $posted[ 'chunkedData' ] ) ;
		if ( ! empty( $export_databy[ 'subscription_products' ] ) ) {
			foreach ( $subscriptions as $key => $subscription_id ) {
				$subscription = sumo_get_subscription( $subscription_id ) ;

				if ( $subscription && ! in_array( $subscription->get_subscribed_product(), ( array ) $export_databy[ 'subscription_products' ] ) ) {
					unset( $subscriptions[ $key ] ) ;
				}
			}
		}

		$json_args                     = array() ;
		$pre_generated_data            = json_decode( stripslashes( $posted[ 'generated_data' ] ) ) ;
		$new_generated_data            = array_map( array( 'SUMO_Subscription_Exporter', 'generate_data' ), $subscriptions ) ;
		$json_args[ 'generated_data' ] = array_values( array_filter( array_merge( array_filter( ( array ) $pre_generated_data ), $new_generated_data ) ) ) ;

		if ( absint( wp_unslash( $posted[ 'originalDataLength' ] ) ) === absint( wp_unslash( $posted[ 'step' ] ) ) ) {
			$json_args[ 'export' ]       = 'done' ;
			$json_args[ 'redirect_url' ] = SUMO_Subscription_Exporter::get_download_url( $json_args[ 'generated_data' ] ) ;
		}

		wp_send_json( wp_parse_args( $json_args, array(
			'export'         => 'processing',
			'generated_data' => array(),
			'original_data'  => array(),
			'redirect_url'   => SUMO_Subscription_Exporter::get_exporter_page_url(),
		) ) ) ;
	}

	/**
	 * Save order subscription.
	 */
	public static function checkout_order_subscription() {
		check_ajax_referer( 'update-order-subscription', 'security' ) ;

		$posted = $_POST ;
		if ( 'yes' === wc_clean( wp_unslash( $posted[ 'subscribed' ] ) ) ) {
			WC()->session->set( 'sumo_is_order_subscription_subscribed', 'yes' ) ;
			WC()->session->set( 'sumo_order_subscription_duration_period', wc_clean( wp_unslash( $posted[ 'subscription_duration' ] ) ) ) ;
			WC()->session->set( 'sumo_order_subscription_duration_length', wc_clean( wp_unslash( $posted[ 'subscription_duration_value' ] ) ) ) ;
			WC()->session->set( 'sumo_order_subscription_recurring_length', wc_clean( wp_unslash( $posted[ 'subscription_recurring' ] ) ) ) ;
		} else {
			WC()->session->set( 'sumo_is_order_subscription_subscribed', 'no' ) ;
			WC()->session->set( 'sumo_order_subscription_duration_period', '' ) ;
			WC()->session->set( 'sumo_order_subscription_duration_length', '' ) ;
			WC()->session->set( 'sumo_order_subscription_recurring_length', '' ) ;
		}
		die() ;
	}

	/**
	 * Process bulk update.
	 */
	public static function bulk_update_product_meta() {
		check_ajax_referer( 'bulk-update-subscription', 'security' ) ;

		$posted = $_POST ;
		if ( 'true' === wc_clean( wp_unslash( $posted[ 'is_bulk_update' ] ) ) ) {
			$products = get_posts( array(
				'post_type'      => 'product',
				'posts_per_page' => '-1',
				'post_status'    => 'publish',
				'fields'         => 'ids',
				'cache_results'  => false
					) ) ;

			if ( ! is_array( $products ) || ! $products ) {
				die() ;
			}

			switch ( wc_clean( wp_unslash( $posted[ 'select_type' ] ) ) ) {
				case '1':
					//Every Products published in the Site.
					wp_send_json( $products ) ;
					break ;
				case '2':
					//Selected Products.
					$selected_products = is_array( $posted[ 'selected_products' ] ) ? $posted[ 'selected_products' ] : explode( ',', $posted[ 'selected_products' ] ) ;
					foreach ( $selected_products as $product_id ) {
						if ( $product_id ) {
							self::update_subscription_product_meta( $product_id, $posted ) ;
						}
					}

					wp_send_json( 'success' ) ;
					break ;
				case '3':
					//All Categories.
					foreach ( $products as $product_id ) {
						$_product = wc_get_product( $product_id ) ;
						if ( ! $_product ) {
							continue ;
						}

						switch ( $_product->get_type() ) {
							case 'variable':
								$terms = get_the_terms( $_product->get_id(), 'product_cat' ) ;
								if ( ! is_array( $terms ) || ! is_array( $_product->get_available_variations() ) ) {
									continue 2 ;
								}

								foreach ( $_product->get_available_variations() as $variation_data ) {
									if ( isset( $variation_data[ 'variation_id' ] ) ) {
										self::update_subscription_product_meta( $variation_data[ 'variation_id' ], $posted ) ;
									}
								}
								break ;
							default:
								$terms = get_the_terms( $product_id, 'product_cat' ) ;
								if ( is_array( $terms ) ) {
									self::update_subscription_product_meta( $product_id, $posted ) ;
								}
								break ;
						}
					}
					wp_send_json( 'success' ) ;
					break ;
				case '4':
					//Selected Categories.
					$selected_categories = $posted[ 'selected_category' ] ;
					if ( ! is_array( $selected_categories ) || ! $selected_categories ) {
						die() ;
					}

					foreach ( $products as $product_id ) {
						$_product = wc_get_product( $product_id ) ;
						if ( ! $_product ) {
							continue ;
						}

						switch ( $_product->get_type() ) {
							case 'variable':
								$is_in_category = false ;
								$terms          = get_the_terms( $_product->get_id(), 'product_cat' ) ;
								if ( ! is_array( $terms ) || ! is_array( $_product->get_available_variations() ) ) {
									continue 2 ;
								}

								foreach ( $terms as $term ) {
									if ( in_array( $term->term_id, $selected_categories ) ) {
										$is_in_category = true ;
									}
								}

								if ( $is_in_category ) {
									foreach ( $_product->get_available_variations() as $variation_data ) {
										if ( isset( $variation_data[ 'variation_id' ] ) ) {
											self::update_subscription_product_meta( $variation_data[ 'variation_id' ], $posted ) ;
										}
									}
								}
								break ;
							default:
								$terms = get_the_terms( $product_id, 'product_cat' ) ;
								if ( ! is_array( $terms ) ) {
									continue 2 ;
								}

								foreach ( $terms as $term ) {
									if ( in_array( $term->term_id, $selected_categories ) ) {
										self::update_subscription_product_meta( $product_id, $posted ) ;
									}
								}
								break ;
						}
					}
					wp_send_json( 'success' ) ;
					break ;
			}
		}
		die() ;
	}

	/**
	 * Optimize bulk update.
	 */
	public static function optimize_bulk_updation_of_product_meta() {
		check_ajax_referer( 'bulk-update-optimization', 'security' ) ;

		$posted = $_POST ;
		if ( is_array( $posted[ 'ids' ] ) && $posted[ 'ids' ] ) {
			$products = $posted[ 'ids' ] ;

			foreach ( $products as $product_id ) {
				$_product = wc_get_product( $product_id ) ;
				if ( ! $_product ) {
					continue ;
				}

				update_post_meta( $product_id, 'sumo_subscription_version', SUMO_SUBSCRIPTIONS_VERSION ) ;

				switch ( $_product->get_type() ) {
					case 'variable':
						if ( is_array( $_product->get_available_variations() ) ) {
							foreach ( $_product->get_available_variations() as $variation_data ) {
								if ( isset( $variation_data[ 'variation_id' ] ) ) {
									self::update_subscription_product_meta( $variation_data[ 'variation_id' ], $posted ) ;
								}
							}
						}
						break ;
					default:
						self::update_subscription_product_meta( $product_id, $posted ) ;
						break ;
				}
			}
		}
		die() ;
	}

	/**
	 * Get HTML fields of wc-product-search and wc-user-role-multiselect
	 */
	public static function get_subscription_as_regular_html_data() {
		check_ajax_referer( 'subscription-as-regular-html-data', 'security' ) ;

		include_once( 'admin/settings-page/class-advance-settings.php' ) ;

		$posted = $_POST ;
		wp_send_json( array(
			'wc_product_search'        => sumosubs_wc_search_field( array(
				'class'       => 'wc-product-search',
				'action'      => 'sumosubscription_json_search_subscription_products_and_variations',
				'id'          => 'selected_subscription_' . $posted[ 'rowID' ],
				'name'        => 'selected_subscription[' . $posted[ 'rowID' ] . ']',
				'type'        => 'product',
				'selected'    => false,
				'placeholder' => __( 'Search for a subscription product&hellip;', 'sumosubscriptions' ),
					), false ),
			'wc_user_role_multiselect' => sumosubs_wc_enhanced_select_field( array(
				'id'      => 'selected_userrole_' . $posted[ 'rowID' ],
				'name'    => 'selected_userrole[' . $posted[ 'rowID' ] . ']',
				'options' => sumosubs_user_roles( true )
					), false )
		) ) ;
	}

	/**
	 * Update Subscription Product post Meta.
	 *
	 * @param int $product_id The Product post ID Either Product ID or Variation ID
	 * @return int
	 */
	public static function update_subscription_product_meta( $product_id, $posted ) {
		if ( '1' === $posted[ 'update_enable_subscription' ] ) {
			update_post_meta( $product_id, 'sumo_susbcription_status', $posted[ 'enable_subscription' ] ) ;

			//Sets Default value.
			update_post_meta( $product_id, 'sumo_susbcription_period', 'D' ) ;
			update_post_meta( $product_id, 'sumo_susbcription_period_value', '1' ) ;
		}

		if ( '1' === $posted[ 'update_subscription_duration' ] ) {
			update_post_meta( $product_id, 'sumo_susbcription_period', $posted[ 'subscription_duration' ] ) ;

			if ( 'D' === $posted[ 'subscription_duration' ] ) {
				update_post_meta( $product_id, 'sumo_susbcription_period_value', $posted[ 'subscription_value_days' ] ) ;
			} elseif ( 'W' === $posted[ 'subscription_duration' ] ) {
				update_post_meta( $product_id, 'sumo_susbcription_period_value', $posted[ 'subscription_value_weeks' ] ) ;
			} elseif ( 'M' === $posted[ 'subscription_duration' ] ) {
				update_post_meta( $product_id, 'sumo_susbcription_period_value', $posted[ 'subscription_value_months' ] ) ;
			} else {
				update_post_meta( $product_id, 'sumo_susbcription_period_value', $posted[ 'subscription_value_years' ] ) ;
			}
		}
		if ( '1' === $posted[ 'update_trial_period' ] ) {
			update_post_meta( $product_id, 'sumo_susbcription_trial_enable_disable', $posted[ 'trial_period' ] ) ;
		}
		if ( '1' === $posted[ 'update_fee_type' ] ) {
			update_post_meta( $product_id, 'sumo_susbcription_fee_type_selector', $posted[ 'trial_fee_type' ] ) ;
		}
		if ( '1' === $posted[ 'update_fee_value' ] ) {
			update_post_meta( $product_id, 'sumo_trial_price', $posted[ 'trial_fee_value' ] ) ;
		}
		if ( '1' === $posted[ 'update_trial_duration' ] ) {
			update_post_meta( $product_id, 'sumo_trial_period', $posted[ 'trial_duration' ] ) ;

			if ( 'D' === $posted[ 'trial_duration' ] ) {
				update_post_meta( $product_id, 'sumo_trial_period_value', $posted[ 'trial_value_days' ] ) ;
			} elseif ( 'W' === $posted[ 'trial_duration' ] ) {
				update_post_meta( $product_id, 'sumo_trial_period_value', $posted[ 'trial_value_weeks' ] ) ;
			} elseif ( 'M' === $posted[ 'trial_duration' ] ) {
				update_post_meta( $product_id, 'sumo_trial_period_value', $posted[ 'trial_value_months' ] ) ;
			} else {
				update_post_meta( $product_id, 'sumo_trial_period_value', $posted[ 'trial_value_years' ] ) ;
			}
		}
		if ( '1' === $posted[ 'update_signup_fee' ] ) {
			update_post_meta( $product_id, 'sumo_susbcription_signusumoee_enable_disable', $posted[ 'signup_fee' ] ) ;
		}
		if ( '1' === $posted[ 'update_signup_fee_value' ] ) {
			update_post_meta( $product_id, 'sumo_signup_price', $posted[ 'signup_fee_value' ] ) ;
		}
		if ( '1' === $posted[ 'update_recurring_cycle' ] ) {
			update_post_meta( $product_id, 'sumo_recurring_period_value', $posted[ 'recurring_cycle' ] ) ;
		}
		return 1 ;
	}

	/**
	 * JSON Search Product and Variations
	 *
	 * @param array $meta_query
	 */
	public static function json_search_products_and_variations( $meta_query = array() ) {
		check_ajax_referer( 'search-products', 'security' ) ;

		$get     = $_GET ;
		$term    = ( string ) wc_clean( stripslashes( isset( $get[ 'term' ] ) ? $get[ 'term' ] : '' ) ) ;
		$exclude = array() ;

		if ( isset( $get[ 'exclude' ] ) && ! empty( $get[ 'exclude' ] ) ) {
			$exclude = array_map( 'intval', explode( ',', $get[ 'exclude' ] ) ) ;
		}

		$args = array(
			'post_type'      => array( 'product', 'product_variation' ),
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'order'          => 'ASC',
			'orderby'        => 'parent title',
			'meta_query'     => is_array( $meta_query ) ? $meta_query : array(),
			's'              => $term,
			'exclude'        => $exclude
				) ;

		$posts          = get_posts( $args ) ;
		$found_products = array() ;

		if ( ! empty( $posts ) ) {
			foreach ( $posts as $post ) {
				if ( ! current_user_can( 'read_product', $post->ID ) ) {
					continue ;
				}

				if ( class_exists( 'SUMOMemberships' ) && function_exists( 'sumo_is_membership_product' ) && sumo_is_membership_product( $post->ID ) ) {
					continue ;
				}

				$product                     = wc_get_product( $post->ID ) ;
				$found_products[ $post->ID ] = $product->get_formatted_name() ;
			}
		}

		wp_send_json( $found_products ) ;
	}

	/**
	 * Search Subscription Products and Variations without SUMO Memberships products which are linked with.
	 */
	public static function json_search_subscription_products_and_variations() {
		self::json_search_products_and_variations( array(
			array(
				'key'     => 'sumo_susbcription_status',
				'value'   => '1',
				'type'    => 'numeric',
				'compare' => '='
			),
		) ) ;
	}

	/**
	 * Search Downloadable Non Subscription and Non Membership Products and Variations.
	 */
	public static function json_search_downloadable_products_and_variations() {
		self::json_search_products_and_variations( array(
			array(
				'key'   => '_downloadable',
				'value' => 'yes'
			),
			array(
				'key'     => 'sumo_susbcription_status',
				'value'   => '1',
				'compare' => '!='
			)
		) ) ;
	}

	/**
	 * Search for customers by email and return json.
	 */
	public static function json_search_customers_by_email() {
		ob_start() ;

		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			wp_die( -1 ) ;
		}

		$get   = $_GET ;
		$term  = wc_clean( wp_unslash( $get[ 'term' ] ) ) ;
		$limit = '' ;

		if ( empty( $term ) ) {
			wp_die() ;
		}

		$ids = array() ;
		// Search by ID.
		if ( is_numeric( $term ) ) {
			$customer = new WC_Customer( intval( $term ) ) ;

			// Customer does not exists.
			if ( 0 !== $customer->get_id() ) {
				$ids = array( $customer->get_id() ) ;
			}
		}

		// Usernames can be numeric so we first check that no users was found by ID before searching for numeric username, this prevents performance issues with ID lookups.
		if ( empty( $ids ) ) {
			$data_store = WC_Data_Store::load( 'customer' ) ;

			// If search is smaller than 3 characters, limit result set to avoid
			// too many rows being returned.
			if ( 3 > strlen( $term ) ) {
				$limit = 20 ;
			}
			$ids = $data_store->search_customers( $term, $limit ) ;
		}

		$found_customers = array() ;
		if ( ! empty( $get[ 'exclude' ] ) ) {
			$ids = array_diff( $ids, ( array ) $get[ 'exclude' ] ) ;
		}

		foreach ( $ids as $id ) {
			$customer                                  = new WC_Customer( $id ) ;
			$found_customers[ $customer->get_email() ] = sprintf(
					/* translators: 1: user display name 2: user ID 3: user email */
					esc_html__( '%1$s (#%2$s &ndash; %3$s)', 'sumosubscriptions' ), $customer->get_first_name() . ' ' . $customer->get_last_name(), $customer->get_id(), $customer->get_email()
					) ;
		}

		wp_send_json( $found_customers ) ;
	}

}

SUMOSubscriptions_Ajax::init() ;
