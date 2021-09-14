<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly
}

/**
 * Get Order ID
 *
 * @param object | int $order The Order post ID
 * @return int
 */
function sumosubs_get_order_id( $order ) {
	$order = sumosubs_maybe_get_order_instance( $order ) ;
	return $order ? $order->get_id() : 0 ;
}

/**
 * Get Order Currency
 *
 * @param object | int $order The Order post ID
 * @return string
 */
function sumosubs_get_order_currency( $order ) {
	$order = sumosubs_maybe_get_order_instance( $order ) ;
	return $order ? $order->get_currency() : '' ;
}

/**
 * Get Order Billing First Name
 *
 * @param object | int $order The Order post ID
 * @return string
 */
function sumosubs_get_order_billing_first_name( $order ) {
	$order = sumosubs_maybe_get_order_instance( $order ) ;
	return $order ? $order->get_billing_first_name() : '' ;
}

/**
 * Get Order Billing Last Name
 *
 * @param object | int $order The Order post ID
 * @return string
 */
function sumosubs_get_order_billing_last_name( $order ) {
	$order = sumosubs_maybe_get_order_instance( $order ) ;
	return $order ? $order->get_billing_last_name() : '' ;
}

/**
 * Get Order Date
 *
 * @param object | int $order The Order post ID
 * @param bool $date_i18n
 * @return string
 */
function sumosubs_get_order_date( $order, $date_i18n = false ) {
	$order = sumosubs_maybe_get_order_instance( $order ) ;
	if ( ! $order ) {
		return '' ;
	}

	return $date_i18n ? $order->get_date_created()->date_i18n( 'Y-m-d H:i:s' ) : $order->get_date_created() ;
}

/**
 * Check if the Order contains Subscription.
 *
 * @param mixed $order
 * @return bool
 */
function sumo_is_order_contains_subscriptions( $order ) {
	$order = sumosubs_maybe_get_order_instance( $order ) ;
	if ( ! $order ) {
		return false ;
	}

	$parent_order_id = sumosubs_get_parent_order_id( $order ) ;
	$bool            = sumo_order_contains_subscription( $order ) ;
	return apply_filters( 'sumosubscriptions_order_has_subscriptions', $bool, $order->get_id(), $parent_order_id ) ;
}
