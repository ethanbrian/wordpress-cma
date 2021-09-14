<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly
}

/**
 * Get HTML for the subscription order items to be shown in emails.
 *
 * @param int $subscription_id The Subscription post ID
 * @param WC_Order $order Order object.
 * @param array    $args Arguments.
 * @return string
 */
function sumosubs_get_email_order_items( $subscription_id, $order, $args = array(), $echo = true ) {
	$args = wp_parse_args( $args, array(
		'show_sku'        => false,
		'show_image'      => false,
		'image_size'      => array( 32, 32 ),
		'plain_text'      => false,
		'sent_to_admin'   => false,
		'pluck_subs_item' => false
			) ) ;

	if ( $args[ 'pluck_subs_item' ] ) {
		$items = sumo_pluck_subscription_order_item( $order, $subscription_id ) ;
	} else {
		$items = $order->get_items() ;
	}

	ob_start() ;
	$template = $args[ 'plain_text' ] ? 'emails/plain/email-order-items.php' : 'emails/email-order-items.php' ;
	wc_get_template( $template, apply_filters( 'woocommerce_email_order_items_args', array(
		'order'               => $order,
		'items'               => $items,
		'subs_id'             => $subscription_id,
		'show_download_links' => $order->is_download_permitted() && ! $args[ 'sent_to_admin' ],
		'show_purchase_note'  => $order->is_paid() && ! $args[ 'sent_to_admin' ],
		'show_sku'            => $args[ 'show_sku' ],
		'show_image'          => $args[ 'show_image' ],
		'image_size'          => $args[ 'image_size' ],
		'plain_text'          => $args[ 'plain_text' ],
		'sent_to_admin'       => $args[ 'sent_to_admin' ],
	) ) ) ;

	if ( $echo ) {
		ob_end_flush() ;
	} else {
		return apply_filters( 'woocommerce_email_order_items_table', ob_get_clean(), $order ) ;
	}
}

/**
 * Output Email Order items table
 *
 * @param object $order The Order object
 * @param int $subscription_id The Subscription post ID
 * @param string $email
 */
function sumosubs_display_email_order_items_table( $order, $subscription_id, $email ) {
	switch ( $email->name ) {
		case 'invoice':
		case 'overdue':
		case 'suspended':
		case 'auto-renewed':
		case 'automatic-to-manual-renewal':
		case 'preapproval-access-revoked':
		case 'automatic-charging-reminder':
		case 'pending-authorization':
			sumosubs_get_email_order_items( $subscription_id, $order ) ;
			break ;
		case 'new-order':
		case 'completed':
		case 'processing':
		case 'cancelled':
			if ( 1 === count( $order->get_items() ) || doing_action( 'woocommerce_order_status_changed' ) || SUMO_Order_Subscription::is_subscribed( $subscription_id ) ) {
				sumosubs_get_email_order_items( $subscription_id, $order ) ;
			} else {
				sumosubs_get_email_order_items( $subscription_id, $order, array( 'pluck_subs_item' => true ) ) ;
			}
			break ;
		case 'paused':
		case 'cancel-request-submitted':
		case 'cancel-request-revoked':
		case 'turnoff-automatic-payments-success':
		case 'expired':
			if ( 1 === count( $order->get_items() ) || SUMO_Order_Subscription::is_subscribed( $subscription_id ) ) {
				sumosubs_get_email_order_items( $subscription_id, $order ) ;
			} else {
				sumosubs_get_email_order_items( $subscription_id, $order, array( 'pluck_subs_item' => true ) ) ;
			}
			break ;
	}
}

add_action( 'sumosubscriptions_email_order_details', 'sumosubs_display_email_order_items_table', 10, 3 ) ;

/**
 * Output Email Order items totals.
 *
 * @param object $order The Order object
 * @param int $subscription_id The Subscription post ID
 * @param string $email
 * @param boolean $plain
 */
function sumosubs_display_email_order_items_totals( $order, $subscription_id, $email, $plain = false ) {
	switch ( $email->name ) {
		case 'invoice':
		case 'overdue':
		case 'suspended':
		case 'auto-renewed':
		case 'automatic-to-manual-renewal':
		case 'preapproval-access-revoked':
		case 'automatic-charging-reminder':
		case 'pending-authorization':
			sumosubs_get_email_order_items_totals( $order, false, $plain ) ;
			break ;
		case 'new-order':
		case 'completed':
		case 'processing':
		case 'cancelled':
			if ( 1 === count( $order->get_items() ) || doing_action( 'woocommerce_order_status_changed' ) || SUMO_Order_Subscription::is_subscribed( $subscription_id ) ) {
				sumosubs_get_email_order_items_totals( $order, false, $plain ) ;
			} else {
				sumosubs_get_email_order_items_totals( $order, $subscription_id, $plain ) ;
			}
			break ;
		case 'paused':
		case 'cancel-request-submitted':
		case 'cancel-request-revoked':
		case 'turnoff-automatic-payments-success':
		case 'expired':
			if ( 1 === count( $order->get_items() ) || SUMO_Order_Subscription::is_subscribed( $subscription_id ) ) {
				sumosubs_get_email_order_items_totals( $order, false, $plain ) ;
			} else {
				sumosubs_get_email_order_items_totals( $order, $subscription_id, $plain ) ;
			}
			break ;
	}
}

add_action( 'sumosubscriptions_email_order_meta', 'sumosubs_display_email_order_items_totals', 10, 4 ) ;

/**
 * Get Email Order items totals.
 *
 * @param object $order The Order object
 * @param int $subscription_id The Subscription post ID
 * @param boolean $plain
 */
function sumosubs_get_email_order_items_totals( $order, $subscription_id = false, $plain = false ) {
	$totals = $order->get_order_item_totals() ;
	if ( empty( $totals ) ) {
		return ;
	}

	if ( $subscription_id ) {
		$item = current( sumo_pluck_subscription_order_item( $order, $subscription_id ) ) ;
		if ( ! $item ) {
			return ;
		}

		foreach ( $totals as $item_key => $item_value ) {
			switch ( $item_key ) {
				case 'cart_subtotal':
					$totals[ $item_key ][ 'value' ] = $order->get_formatted_line_subtotal( $item ) ;
					break ;
				case 'discount':
					if ( $item->get_subtotal() !== $item->get_total() ) {
						$totals[ $item_key ][ 'value' ] = '-' . wc_price( $item->get_subtotal() - $item->get_total(), array( 'currency' => $order->get_currency() ) ) ;
					} else {
						unset( $totals[ $item_key ] ) ;
					}
					break ;
				case 'order_total':
					$totals[ $item_key ][ 'value' ] = wc_price( $order->get_line_total( $item, true ), array( 'currency' => $order->get_currency() ) ) ;
					break ;
				case 'tax':
					$totals[ $item_key ][ 'value' ] = wc_price( $order->get_line_tax( $item ), array( 'currency' => $order->get_currency() ) ) ;
					break ;
				case 'payment_method':
					continue 2 ;
					break ;
				default:
					foreach ( $order->get_tax_totals() as $code => $tax ) {
						if ( sanitize_title( $code ) === $item_key ) {
							$totals[ $item_key ][ 'value' ] = wc_price( $order->get_line_tax( $item ), array( 'currency' => $order->get_currency() ) ) ;
							continue 3 ;
						}
					}

					unset( $totals[ $item_key ] ) ;
			}
		}
	}

	ob_start() ;
	if ( $plain ) {
		foreach ( $totals as $total ) {
			echo wp_kses_post( $total[ 'label' ] ) . "\t " . wp_kses_post( $total[ 'value' ] ) . "\n" ;
		}
	} else {
		$text_align = is_rtl() ? 'right' : 'left' ;

		$i = 0 ;
		foreach ( $totals as $total ) {
			$i ++ ;
			?>
			<tr>
				<th class="td" scope="row" colspan="2" style="text-align:<?php echo esc_attr( $text_align ) ; ?>; <?php echo ( 1 === $i ) ? 'border-top-width: 4px;' : '' ; ?>"><?php echo wp_kses_post( $total[ 'label' ] ) ; ?></th>
				<td class="td" style="text-align:<?php echo esc_attr( $text_align ) ; ?>; <?php echo ( 1 === $i ) ? 'border-top-width: 4px;' : '' ; ?>"><?php echo wp_kses_post( $total[ 'value' ] ) ; ?></td>
			</tr>
			<?php
		}
	}
	ob_end_flush() ;
}
