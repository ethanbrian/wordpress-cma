/* global ajaxurl, sumosubs_admin_advanced_params */

jQuery( function ( $ ) {

	$( '#sumo_min_waiting_time_after_switched_to_manual_pay_when_preapproval_revoked' ).closest( 'tr' ).hide();
	$( '#sumo_payment_reminder_interval_after_preapproval_revoked' ).closest( 'tr' ).hide();
	$( '#sumo_min_waiting_time_after_switched_to_manual_pay' ).closest( 'tr' ).hide();
	$( '#sumo_payment_reminder_interval_for_auto_to_manual_switch' ).closest( 'tr' ).hide();
	$( '#sumosubs_payment_gateways_to_hide_when_order_amt_zero' ).closest( 'tr' ).hide();

	if ( $( '#sumo_user_cancel_preapprove_key' ).val() === '2' ) {
		$( '#sumo_min_waiting_time_after_switched_to_manual_pay_when_preapproval_revoked' ).closest( 'tr' ).show();
		$( '#sumo_payment_reminder_interval_after_preapproval_revoked' ).closest( 'tr' ).show();
	}
	if ( $( '#sumo_cancel_automatic_subscription_goes_to' ).val() === '2' ) {
		$( '#sumo_min_waiting_time_after_switched_to_manual_pay' ).closest( 'tr' ).show();
		$( '#sumo_payment_reminder_interval_for_auto_to_manual_switch' ).closest( 'tr' ).show();
	}
	if ( $( '#sumosubscription_show_payment_gateways_when_order_amt_zero' ).is( ':checked' ) ) {
		$( '#sumosubs_payment_gateways_to_hide_when_order_amt_zero' ).closest( 'tr' ).show();
	}

	$( '#sumosubscription_show_payment_gateways_when_order_amt_zero' ).change( function () {
		$( '#sumosubs_payment_gateways_to_hide_when_order_amt_zero' ).closest( 'tr' ).hide();

		if ( this.checked ) {
			$( '#sumosubs_payment_gateways_to_hide_when_order_amt_zero' ).closest( 'tr' ).show();
		}
	} );

	$( '#sumo_user_cancel_preapprove_key' ).change( function () {
		$( '#sumo_payment_reminder_interval_after_preapproval_revoked' ).closest( 'tr' ).hide();
		$( '#sumo_min_waiting_time_after_switched_to_manual_pay_when_preapproval_revoked' ).closest( 'tr' ).hide();
		$( '#sumo_min_waiting_time_after_switched_to_manual_pay_when_preapproval_revoked' ).val( '5' );

		if ( this.value === '2' ) {
			$( '#sumo_min_waiting_time_after_switched_to_manual_pay_when_preapproval_revoked' ).closest( 'tr' ).show();
			$( '#sumo_payment_reminder_interval_after_preapproval_revoked' ).closest( 'tr' ).show();
		}
	} );

	$( '#sumo_cancel_automatic_subscription_goes_to' ).change( function () {
		$( '#sumo_min_waiting_time_after_switched_to_manual_pay' ).closest( 'tr' ).hide();
		$( '#sumo_payment_reminder_interval_for_auto_to_manual_switch' ).closest( 'tr' ).hide();
		$( '#sumo_min_waiting_time_after_switched_to_manual_pay' ).val( '5' );

		if ( this.value === '2' ) {
			$( '#sumo_min_waiting_time_after_switched_to_manual_pay' ).closest( 'tr' ).show();
			$( '#sumo_payment_reminder_interval_for_auto_to_manual_switch' ).closest( 'tr' ).show();
		}
	} );

	$( '#sumosubscription_set_as_regular' ).on( 'click', 'a.add', function () {
		var rowID = $( '#sumosubscription_set_as_regular' ).find( 'tbody .defined_rule' ).length + 1;
		$( '.spinner' ).addClass( 'is-active' );

		$.ajax( {
			type: 'POST',
			url: ajaxurl,
			data: {
				action: 'sumosubscription_get_subscription_as_regular_html_data',
				security: sumosubs_admin_advanced_params.html_data_nonce,
				rowID: rowID
			},
			success: function ( data ) {
				if ( typeof data !== 'undefined' ) {
					$( '<tr class="defined_rule">\n\
                                            <td class="sort"></td>\
                                            <td>' + data.wc_product_search + '</td>\n\
                                            <td>' + data.wc_user_role_multiselect + '</td>\n\
                                            <td><a href="#" class="remove_row button">X</a></td>\
                                    </tr>' ).appendTo( '#sumosubscription_set_as_regular table tbody' );
					$( document.body ).trigger( 'wc-enhanced-select-init' );
				}
			},
			complete: function () {
				$( '.spinner' ).removeClass( 'is-active' );
			}
		} );
		return false;
	} );

	$( document ).on( 'click', '#sumosubscription_set_as_regular a.remove_row', function () {
		$( this ).closest( 'tr' ).remove();
		return false;
	} );

	// Color picker
	$( '.colorpick' )

			.iris( {
				change: function ( event, ui ) {
					$( this ).parent().find( '.colorpickpreview' ).css( { backgroundColor: ui.color.toString() } );
				},
		hide: true,
		border: true
			} )

			.on( 'click focus', function ( event ) {
				event.stopPropagation();
				$( '.iris-picker' ).hide();
				$( this ).closest( 'td' ).find( '.iris-picker' ).show();
				$( this ).data( 'originalValue', $( this ).val() );
			} )

			.on( 'change', function () {
				if ( $( this ).is( '.iris-error' ) ) {
					var original_value = $( this ).data( 'originalValue' );

					if ( original_value.match( /^\#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/ ) ) {
						$( this ).val( $( this ).data( 'originalValue' ) ).trigger( 'change' );
					} else {
						$( this ).val( '' ).trigger( 'change' );
					}
				}
			} );

	$( 'body' ).on( 'click', function () {
		$( '.iris-picker' ).hide();
	} );
} );
