<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly
}

echo '= ' . wp_kses_post( $email_heading ) . ' =</br>' ;

if ( $order->has_status( 'pending' ) ) {

	/* translators: 1: subscription number 2: subscription number 3: pay url 4: due date 5: pending status */
	echo sprintf( wp_kses_post( __( "Your Subscription #%1\$s has been changed to Manual Subscription Renewal because we couldn\'t charge for the Subscription Renewal using your preapproved Payment method. <br>An Invoice has been created for you to renew your Subscription #%2\$s. To pay for this invoice, please use the following link: %3\$s. Please make the payment on or before <b>%4\$s</b>. If payment is not made, Subscription will go to <b>%5\$s</b> status", 'sumosubscriptions' ) ), esc_html( sumo_get_subscription_number( $post_id ) ), esc_html( sumo_get_subscription_number( $post_id ) ), esc_url( $order->get_checkout_payment_url() ), esc_html( $upcoming_mail_date ), esc_html( $upcoming_mail_status ) ) . "\n\n" ;
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
