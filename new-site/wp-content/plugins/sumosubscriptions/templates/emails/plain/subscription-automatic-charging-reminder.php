<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly
}

echo '= ' . wp_kses_post( $email_heading ) . ' =</br>' ;

if ( $order->has_status( 'pending' ) ) {

	/* translators: 1: subscription number 2: due date */
	echo sprintf( wp_kses_post( __( 'Hi, <br>This is to remind you that your subscription #%1$s will be automatically renewed on <b>%2$s</b> because you have already preapproved for automatic charging. <br>Kindly make sure you have sufficient funds in your account. ', 'sumosubscriptions' ) ), esc_html( sumo_get_subscription_number( $post_id ) ), esc_html( $payment_charging_date ) ) . "\n\n" ;
}

echo '=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=</br>' ;

do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text, $email ) ;

/* translators: 1: subscription number */
echo sprintf( esc_html__( 'Subscription #%s', 'sumosubscriptions' ), esc_html( sumo_get_subscription_number( $post_id ) ) ) . '</br>' ;

echo '(' . esc_html( date_i18n( __( 'jS F Y', 'sumosubscriptions' ), strtotime( $order->get_date_created() ) ) ) . ')</br>' ;

do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email ) ;

echo '</br>' ;

do_action( 'sumosubscriptions_email_order_details', $order, $post_id, $email ) ;

echo '==========</br>' ;

do_action( 'sumosubscriptions_email_order_meta', $order, $post_id, $email, true ) ;

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=</br>" ;

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) ) ;
