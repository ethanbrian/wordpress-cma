/* global sumosubs_admin_upgrade_downgrade_params */

jQuery( function ( $ ) {

	if ( typeof sumosubs_admin_upgrade_downgrade_params === 'undefined' ) {
		return false;
	}

	$( '#sumosubs_allow_user_to' ).closest( 'tr' ).hide();
	$( '#sumosubs_allow_upgrade_r_downgrade_between' ).closest( 'tr' ).hide();
	$( '#sumosubs_upgrade_r_downgrade_based_on' ).closest( 'tr' ).hide();
	$( '#sumosubs_payment_for_upgrade_r_downgrade' ).closest( 'tr' ).hide();
	$( '#sumosubs_upgrade_r_downgrade_button_text' ).closest( 'tr' ).hide();
	$( '#sumosubs_prorate_recurring_payment' ).closest( 'tr' ).hide();
	$( '#sumosubs_charge_signup_fee' ).closest( 'tr' ).hide();
	$( '#sumosubs_prorate_subscription_recurring_cycle' ).closest( 'tr' ).hide();

	if ( $( '#sumosubs_allow_upgrade_r_downgrade' ).is( ':checked' ) ) {
		$( '#sumosubs_allow_user_to' ).closest( 'tr' ).show();
		$( '#sumosubs_allow_upgrade_r_downgrade_between' ).closest( 'tr' ).show();
		$( '#sumosubs_upgrade_r_downgrade_based_on' ).closest( 'tr' ).show();
		$( '#sumosubs_payment_for_upgrade_r_downgrade' ).closest( 'tr' ).show();
		$( '#sumosubs_upgrade_r_downgrade_button_text' ).closest( 'tr' ).show();
		$( '#sumosubs_charge_signup_fee' ).closest( 'tr' ).show();
		$( '#sumosubs_prorate_subscription_recurring_cycle' ).closest( 'tr' ).show();
		$( '#sumosubs_prorate_recurring_payment' ).closest( 'tr' ).hide();

		if ( 'prorate' === $( '#sumosubs_payment_for_upgrade_r_downgrade' ).val() ) {
			$( '#sumosubs_prorate_recurring_payment' ).closest( 'tr' ).show();
		}
	}

	$( '#sumosubs_allow_upgrade_r_downgrade' ).on( 'change', function () {
		$( '#sumosubs_allow_user_to' ).closest( 'tr' ).hide();
		$( '#sumosubs_allow_upgrade_r_downgrade_between' ).closest( 'tr' ).hide();
		$( '#sumosubs_upgrade_r_downgrade_based_on' ).closest( 'tr' ).hide();
		$( '#sumosubs_payment_for_upgrade_r_downgrade' ).closest( 'tr' ).hide();
		$( '#sumosubs_upgrade_r_downgrade_button_text' ).closest( 'tr' ).hide();
		$( '#sumosubs_prorate_recurring_payment' ).closest( 'tr' ).hide();
		$( '#sumosubs_charge_signup_fee' ).closest( 'tr' ).hide();
		$( '#sumosubs_prorate_subscription_recurring_cycle' ).closest( 'tr' ).hide();

		if ( this.checked ) {
			$( '#sumosubs_allow_user_to' ).closest( 'tr' ).show();
			$( '#sumosubs_allow_upgrade_r_downgrade_between' ).closest( 'tr' ).show();
			$( '#sumosubs_upgrade_r_downgrade_based_on' ).closest( 'tr' ).show();
			$( '#sumosubs_payment_for_upgrade_r_downgrade' ).closest( 'tr' ).show();
			$( '#sumosubs_upgrade_r_downgrade_button_text' ).closest( 'tr' ).show();
			$( '#sumosubs_charge_signup_fee' ).closest( 'tr' ).show();
			$( '#sumosubs_prorate_subscription_recurring_cycle' ).closest( 'tr' ).show();

			if ( 'prorate' === $( '#sumosubs_payment_for_upgrade_r_downgrade' ).val() ) {
				$( '#sumosubs_prorate_recurring_payment' ).closest( 'tr' ).show();
			}
		}
	} );

	$( '#sumosubs_payment_for_upgrade_r_downgrade' ).on( 'change', function () {
		$( '#sumosubs_prorate_recurring_payment' ).closest( 'tr' ).hide();

		if ( 'prorate' === $( this ).val() ) {
			$( '#sumosubs_prorate_recurring_payment' ).closest( 'tr' ).show();
		}
	} );
} );
