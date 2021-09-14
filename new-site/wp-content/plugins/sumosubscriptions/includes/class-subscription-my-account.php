<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly
}

/**
 * Handle subscriptions in My Account page.
 * 
 * @class SUMOSubscriptions_My_Account
 */
class SUMOSubscriptions_My_Account {

	/**
	 * Init SUMOSubscriptions_My_Account.
	 */
	public static function init() {
		add_filter( 'woocommerce_account_menu_items', __CLASS__ . '::set_my_account_menu_items' ) ;

		add_action( 'woocommerce_account_sumo-subscriptions_endpoint', __CLASS__ . '::my_subscriptions' ) ;
		add_shortcode( 'sumo_my_subscriptions', __CLASS__ . '::my_subscriptions', 10, 3 ) ;

		add_action( 'woocommerce_account_view-subscription_endpoint', __CLASS__ . '::view_subscription' ) ;
		add_action( 'sumosubscriptions_my_subscriptions_view-subscription_endpoint', __CLASS__ . '::view_subscription' ) ;

		add_filter( 'user_has_cap', __CLASS__ . '::customer_has_capability', 10, 3 ) ;
		add_filter( 'sumosubscriptions_my_subscription_table_pause_action', __CLASS__ . '::remove_pause_action', 10, 3 ) ;
		add_filter( 'sumosubscriptions_my_subscription_table_cancel_action', __CLASS__ . '::remove_cancel_action', 10, 3 ) ;

		if ( isset( $_GET[ 'pay_for_order' ] ) ) {
			add_filter( 'sumosubscriptions_need_payment_gateway', __CLASS__ . '::need_payment_gateway', 19, 2 ) ;
			add_filter( 'woocommerce_no_available_payment_methods_message', __CLASS__ . '::wc_gateway_notice' ) ;
			add_filter( 'woocommerce_pay_order_button_html', __CLASS__ . '::remove_place_order_button' ) ;
			add_action( 'before_woocommerce_pay', __CLASS__ . '::wc_checkout_notice' ) ;
		}
	}

	/**
	 * Set our menus under My account menu items
	 *
	 * @param array $items
	 * @return array
	 */
	public static function set_my_account_menu_items( $items ) {
		$endpoint = sumosubscriptions()->query->get_query_var( 'sumo-subscriptions' ) ;
		$menu     = array( $endpoint => apply_filters( 'sumosubscriptions_my_subscriptions_table_title', __( 'My Subscriptions', 'sumosubscriptions' ) ) ) ;
		$position = 2 ;
		$items    = array_slice( $items, 0, $position ) + $menu + array_slice( $items, $position, count( $items ) - 1 ) ;
		return $items ;
	}

	/**
	 * My Subscriptions template.
	 */
	public static function my_subscriptions( $atts = '', $content = '', $tag = '' ) {
		if ( is_admin() ) {
			return ;
		}

		global $wp ;
		if ( 'sumo_my_subscriptions' === $tag ) {
			if ( ! empty( $wp->query_vars ) ) {
				foreach ( $wp->query_vars as $key => $value ) {
					// Ignore pagename param.
					if ( 'pagename' === $key ) {
						continue ;
					}

					if ( has_action( 'sumosubscriptions_my_subscriptions_' . $key . '_endpoint' ) ) {
						do_action( 'sumosubscriptions_my_subscriptions_' . $key . '_endpoint', $value ) ;
						return ;
					}
				}
			}
		}

		$endpoint = sumosubscriptions()->query->get_query_var( 'sumo-subscriptions' ) ;
		if ( isset( $wp->query_vars[ $endpoint ] ) && ! empty( $wp->query_vars[ $endpoint ] ) ) {
			$current_page = absint( $wp->query_vars[ $endpoint ] ) ;
		} else {
			$current_page = 1 ;
		}

		$query = new WP_Query( apply_filters( 'woocommerce_my_account_my_sumo_subscriptions_query', array(
					'post_type'      => 'sumosubscriptions',
					'post_status'    => 'publish',
					'meta_key'       => 'sumo_get_user_id',
					'meta_value'     => get_current_user_id(),
					'fields'         => 'ids',
					'paged'          => $current_page,
					'posts_per_page' => 5,
				) ) ) ;


		$customer_subscriptions = ( object ) array(
					'subscriptions' => $query->posts,
					'max_num_pages' => $query->max_num_pages,
					'total'         => $query->found_posts,
				) ;

		sumosubscriptions_get_template( 'subscriptions.php', array(
			'current_page'           => absint( $current_page ),
			'customer_subscriptions' => $customer_subscriptions,
			'subscriptions'          => $customer_subscriptions->subscriptions,
			'has_subscription'       => 0 < $customer_subscriptions->total,
			'endpoint'               => $endpoint,
		) ) ;
	}

	/**
	 * My Subscriptions > View Subscription template.
	 *
	 * @param int $subscription_id
	 */
	public static function view_subscription( $subscription_id ) {
		if ( ! current_user_can( 'view-subscription', $subscription_id ) ) {
			echo '<div class="woocommerce-error">' . esc_html__( 'Invalid subscription.', 'sumosubscriptions' ) . ' <a href="' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '" class="wc-forward">' . esc_html__( 'My account', 'sumosubscriptions' ) . '</a></div>' ;
			return ;
		}

		echo '<div class="sumo-view-subscription">' ;
		sumosubscriptions_get_template( 'view-subscription.php', array(
			'subscription_id' => absint( $subscription_id ),
		) ) ;
		echo '</div>' ;
	}

	/**
	 * Checks if a user has a certain capability.
	 *
	 * @param array $allcaps All capabilities.
	 * @param array $caps    Capabilities.
	 * @param array $args    Arguments.
	 *
	 * @return array The filtered array of all capabilities.
	 */
	public static function customer_has_capability( $allcaps, $caps, $args ) {
		if ( isset( $caps[ 0 ] ) ) {
			switch ( $caps[ 0 ] ) {
				case 'view-subscription':
					$user_id         = absint( $args[ 1 ] ) ;
					$subscription_id = absint( $args[ 2 ] ) ;

					if ( sumo_is_subscription_exists( $subscription_id ) && absint( get_post_meta( $subscription_id, 'sumo_get_user_id', true ) ) === $user_id ) {
						$allcaps[ 'view-subscription' ] = true ;
					}
					break ;
			}
		}

		return $allcaps ;
	}

	/**
	 * Hide Pause action from my Subscriptions table
	 *
	 * @param bool $action
	 * @param int $subscription_id
	 * @param int $parent_order_id
	 * @return bool
	 */
	public static function remove_pause_action( $action, $subscription_id, $parent_order_id ) {
		if ( 'Pending_Cancellation' === get_post_meta( $subscription_id, 'sumo_get_status', true ) ) {
			return false ;
		}

		return $action ;
	}

	/**
	 * Minimum waiting time for the User to get previlege to Cancel their Subscription.
	 * Show Cancel button only when the User has got the previlege
	 * 
	 * @param bool $action
	 * @param int $subscription_id
	 * @param int $parent_order_id
	 * @return bool
	 */
	public static function remove_cancel_action( $action, $subscription_id, $parent_order_id ) {
		$min_days_user_wait_to_cancel = absint( get_option( 'sumo_min_days_user_wait_to_cancel_their_subscription' ) ) ;

		if ( 0 === $min_days_user_wait_to_cancel ) {
			return $action ;
		}

		$order      = wc_get_order( $parent_order_id ) ;
		$order_date = $order ? $order->get_date_created()->date_i18n( 'Y-m-d H:i:s' ) : '' ;

		if ( $min_days_user_wait_to_cancel > 0 && '' !== $order_date ) {
			$order_time                   = sumo_get_subscription_timestamp( $order_date ) ;
			$min_time_user_wait_to_cancel = $order_time + ( $min_days_user_wait_to_cancel * 86400 ) ;

			if ( sumo_get_subscription_timestamp() >= $min_time_user_wait_to_cancel ) {
				return $action ;
			}
		}

		return false ;
	}

	/**
	 * Prevent the User placing Automatic Subscription renewal order from Pay for Order page.
	 * To do this, remove the Place Order button when Subscription status is in Active or Trial
	 * 
	 * @param html $button
	 * @return html
	 */
	public static function remove_place_order_button( $button ) {
		$renewal_order_id = sumosubs_get_subscription_renewal_order_in_pay_for_order() ;
		if ( ! $renewal_order_id ) {
			return $button ;
		}

		$renewal_order = wc_get_order( $renewal_order_id ) ;
		if ( ! $renewal_order || $renewal_order->has_status( 'failed' ) ) {
			return $button ;
		}

		$subscription_id = sumosubs_get_subscription_id_from_renewal_order( $renewal_order_id ) ;
		if ( 'auto' === sumo_get_payment_type( $subscription_id ) && in_array( get_post_meta( $subscription_id, 'sumo_get_status', true ), array( 'Trial', 'Active', 'Pending_Cancellation' ) ) ) {
			$button = '' ;
		}

		return $button ;
	}

	/**
	 * Prevent the User placing Automatic Subscription renewal order from Pay for Order page.
	 * To do this, display customer notice when Subscription status is in Active or Trial
	 * 
	 * @param string $gateway_notice
	 * @return string
	 */
	public static function wc_gateway_notice( $gateway_notice ) {
		$renewal_order_id = sumosubs_get_subscription_renewal_order_in_pay_for_order() ;
		if ( ! $renewal_order_id ) {
			return $gateway_notice ;
		}

		$renewal_order = wc_get_order( $renewal_order_id ) ;
		if ( ! $renewal_order || $renewal_order->has_status( 'failed' ) ) {
			return $gateway_notice ;
		}

		$subscription_id     = sumosubs_get_subscription_id_from_renewal_order( $renewal_order_id ) ;
		$next_due_date       = sumo_display_subscription_date( get_post_meta( $subscription_id, 'sumo_get_next_payment_date', true ) ) ;
		$display_err_message = 'yes' === get_option( 'sumo_show_hide_err_msg_pay_order_page' ) ;

		if ( $display_err_message && 'auto' === sumo_get_payment_type( $subscription_id ) && in_array( get_post_meta( $subscription_id, 'sumo_get_status', true ), array( 'Trial', 'Active', 'Pending_Cancellation' ) ) ) {
			$gateway_notice = str_replace( '#[subscription_number]', '<a href="' . esc_url( sumo_get_subscription_endpoint_url( $subscription_id ) ) . '">#' . esc_html( sumo_get_subscription_number( $subscription_id ) ) . '</a>', str_replace( '[next_payment_date]', '<b>' . esc_html( $next_due_date ) . '</b>', get_option( 'sumo_err_msg_if_user_paying_active_auto_subscription_renewal_order' ) ) ) ;
		}

		return $gateway_notice ;
	}

	/**
	 * Prevent the User placing Automatic Subscription renewal order from Pay for Order page.
	 * To do this, hide the payment gateways when Subscription status is in Active or Trial
	 * 
	 * @param bool $need
	 * @param string $gateway_id
	 * @return bool
	 */
	public static function need_payment_gateway( $need, $gateway_id ) {
		$renewal_order_id = sumosubs_get_subscription_renewal_order_in_pay_for_order() ;
		if ( ! $renewal_order_id ) {
			return $need ;
		}

		$renewal_order = wc_get_order( $renewal_order_id ) ;
		if ( ! $renewal_order || $renewal_order->has_status( 'failed' ) ) {
			return $need ;
		}

		$subscription_id = sumosubs_get_subscription_id_from_renewal_order( $renewal_order_id ) ;
		if ( 'auto' === sumo_get_payment_type( $subscription_id ) && in_array( get_post_meta( $subscription_id, 'sumo_get_status', true ), array( 'Trial', 'Active', 'Pending_Cancellation' ) ) ) {
			$need = false ;
		}

		return $need ;
	}

	/**
	 * Prevent the User placing Paused/Cancelled Subscription renewal order from Pay for Order page.
	 */
	public static function wc_checkout_notice() {
		$renewal_order_id = sumosubs_get_subscription_renewal_order_in_pay_for_order() ;
		if ( ! $renewal_order_id ) {
			return ;
		}

		$subscription_id = sumosubs_get_subscription_id_from_renewal_order( $renewal_order_id ) ;
		switch ( get_post_meta( $subscription_id, 'sumo_get_status', true ) ) {
			case 'Pause':
				if ( 'yes' === get_option( 'sumo_show_hide_err_msg_pay_order_page' ) ) {
					wc_add_notice( get_option( 'sumo_err_msg_for_paused_in_pay_for_order_page' ), 'error' ) ;
				}
				echo '<style>#order_review {display: none;}</style>' ;
				break ;
			case 'Pending_Cancellation':
				if ( 'yes' === get_option( 'sumo_show_hide_err_msg_pay_order_page' ) ) {
					wc_add_notice( get_option( 'sumo_err_msg_for_pending_cancellation_in_pay_for_order_page' ), 'error' ) ;
				}
				echo '<style>#order_review {display: none;}</style>' ;
		}
	}

}

SUMOSubscriptions_My_Account::init() ;
