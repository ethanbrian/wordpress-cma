<?php
switch ( $product->get_type() ) {
	case 'variable':
		?>
		<table>
			<tbody>
				<?php
				foreach ( $subscription_variation as $variation_id ) {
					$_variation = wc_get_product( $variation_id ) ;
					$selected   = get_post_meta( $variation_id, 'sumosubs_send_payment_reminder_email', true ) ;
					?>
					<tr>
						<th><?php echo wp_kses_post( $_variation->get_formatted_name() ) ; ?></th>
					</tr>
					<tr>
						<td>
							<div>
								<input type="checkbox" name="sumosubs_send_payment_reminder_email[<?php echo esc_attr( $variation_id ) ; ?>][auto]" value="yes" <?php checked( 'yes', ( '' === $selected || ( isset( $selected[ 'auto' ] ) && 'yes' === $selected[ 'auto' ] ) ? 'yes' : '' ) ) ; ?>/>
								<label>
									<a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=email&section=sumosubscriptions_automatic_charging_reminder_email' ) ) ; ?>"><?php esc_html_e( 'Subscription Automatic Renewal Reminder', 'sumosubscriptions' ) ; ?></a>
								</label>
							</div>
							<div>
								<input type="checkbox" name="sumosubs_send_payment_reminder_email[<?php echo esc_attr( $variation_id ) ; ?>][manual]" value="yes" <?php checked( 'yes', ( '' === $selected || ( isset( $selected[ 'manual' ] ) && 'yes' === $selected[ 'manual' ] ) ? 'yes' : '' ) ) ; ?>/>
								<label>
									<a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=email&section=sumosubscriptions_manual_invoice_order_email' ) ) ; ?>"><?php esc_html_e( 'Subscription Invoice - Manual', 'sumosubscriptions' ) ; ?></a>
								</label>
							</div>
							<input name="sumo_subscription_product_ids[]" type="hidden" value="<?php echo esc_attr( $variation_id ) ; ?>">
						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		<?php
		break ;
	default:
		$selected = get_post_meta( $product->get_id(), 'sumosubs_send_payment_reminder_email', true ) ;
		?>
		<div>
			<input type="checkbox" name="sumosubs_send_payment_reminder_email[<?php echo esc_attr( $product->get_id() ) ; ?>][auto]" value="yes" <?php checked( 'yes', ( '' === $selected || ( isset( $selected[ 'auto' ] ) && 'yes' === $selected[ 'auto' ] ) ? 'yes' : '' ) ) ; ?>/>
			<label>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=email&section=sumosubscriptions_automatic_charging_reminder_email' ) ) ; ?>"><?php esc_html_e( 'Subscription Automatic Renewal Reminder', 'sumosubscriptions' ) ; ?></a>
			</label>
		</div>
		<div>
			<input type="checkbox" name="sumosubs_send_payment_reminder_email[<?php echo esc_attr( $product->get_id() ) ; ?>][manual]" value="yes" <?php checked( 'yes', ( '' === $selected || ( isset( $selected[ 'manual' ] ) && 'yes' === $selected[ 'manual' ] ) ? 'yes' : '' ) ) ; ?>/>
			<label>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=email&section=sumosubscriptions_manual_invoice_order_email' ) ) ; ?>"><?php esc_html_e( 'Subscription Invoice - Manual', 'sumosubscriptions' ) ; ?></a>
			</label>
		</div>
		<input name="sumo_subscription_product_ids[]" type="hidden" value="<?php echo esc_attr( $product->get_id() ) ; ?>">
		<?php
		break ;
}
