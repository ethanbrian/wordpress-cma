<?php
/**
 * Order Subscription Options.
 *
 * This template can be overridden by copying it to yourtheme/sumosubscriptions/order-subscription-form.php.
 */
defined( 'ABSPATH' ) || exit ;
?>
<table class="shop_table sumo_order_subscription">
	<tr class="sumo_order_subscription_users_status">
		<td>
			<label for="subscribe"><?php echo wp_kses_post( get_option( 'sumo_order_subsc_checkout_label_option' ) ) ; ?></label>
			<input type="checkbox" id="sumo_order_subscription_status" <?php checked( 'yes' === $chosen_plan[ 'subscribed' ] || $options[ 'default_subscribed' ], true, true ) ; ?> />
		</td>
	</tr>
	<?php
	if ( $options[ 'can_user_select_plan' ] ) {
		?>
		<tr class="sumo_order_subscription_users_duration">
			<td><label for="subscribe_duration_period"><?php echo wp_kses_post( get_option( 'sumo_order_subsc_duration_checkout_label_option' ) ) ; ?></label></td>
			<td>
				<select id="sumo_order_subscription_duration">
					<?php
					$default_period  = 'D' ;
					$duration_period = sumosubs_get_duration_period_selector() ;

					foreach ( $options[ 'duration_period_selector' ] as $index => $period ) :
						if ( 0 === $index ) {
							$default_period = $period ;
						}
						?>
						<option value="<?php echo esc_attr( $period ) ; ?>" <?php selected( $chosen_plan[ 'duration_period' ], $period, true ) ; ?>><?php echo esc_html( $duration_period[ $period ] ) ; ?></option>
					<?php endforeach ; ?>
				</select>
			</td>
		</tr>
		<tr class="sumo_order_subscription_users_duration_value">
			<td><label for="subscribe_duration_length"><?php echo wp_kses_post( get_option( 'sumo_order_subsc_duration_value_checkout_label_option' ) ) ; ?></label></td>
			<td>
				<select id="sumo_order_subscription_duration_value">
					<?php
					$selected_duration_period = $chosen_plan[ 'duration_period' ] ? $chosen_plan[ 'duration_period' ] : $default_period ;
					foreach ( sumo_get_subscription_duration_options( $selected_duration_period, true, $options[ 'min_duration_length_user_can_select' ][ $selected_duration_period ], $options[ 'max_duration_length_user_can_select' ][ $selected_duration_period ] ) as $duration_value_key => $label ) {
						?>
						<option value="<?php echo esc_attr( $duration_value_key ) ; ?>" <?php selected( $duration_value_key, $chosen_plan[ 'duration_length' ], true ) ; ?>><?php echo esc_html( $label ) ; ?></option>
					<?php } ?>
				</select>
			</td>
		</tr>
		<?php if ( $options[ 'can_user_select_recurring_length' ] ) : ?>
			<tr class="sumo_order_subscription_users_recurring">
				<td><label for="subscribe_length"><?php echo wp_kses_post( get_option( 'sumo_order_subsc_recurring_checkout_label_option' ) ) ; ?></label></td>
				<td>
					<select id="sumo_order_subscription_recurring">
						<?php
						if ( '0' === $options[ 'max_recurring_length_user_can_select' ] ) {
							foreach ( sumo_get_subscription_recurring_options( 'last', $options[ 'min_recurring_length_user_can_select' ] ) as $recurring_key => $label ) {
								?>
								<option value="<?php echo esc_attr( $recurring_key ) ; ?>" <?php selected( $recurring_key, $chosen_plan[ 'recurring_length' ], true ) ; ?>><?php echo esc_html( $label ) ; ?></option>
								<?php
							}
						} else {
							foreach ( sumo_get_subscription_recurring_options( false, $options[ 'min_recurring_length_user_can_select' ], $options[ 'max_recurring_length_user_can_select' ] ) as $recurring_key => $label ) {
								?>
								<option value="<?php echo esc_attr( $recurring_key ) ; ?>" <?php selected( $recurring_key, $chosen_plan[ 'recurring_length' ], true ) ; ?>><?php echo esc_html( $label ) ; ?></option>
								<?php
							}
						}
						?>
					</select>
				</td>
			</tr>
		<?php endif ; ?>
		<?php
	}
	?>
</table>
