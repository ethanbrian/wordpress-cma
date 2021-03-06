/* global sumosubs_admin_wc_payments_paypal_reference_params */

jQuery( function ( $ ) {

	if ( typeof sumosubs_admin_wc_payments_paypal_reference_params === 'undefined' ) {
		return false;
	}

	$( '#woocommerce_sumo_paypal_reference_txns_user_roles_for_dev' ).closest( 'tr' ).hide();

	if ( $( '#woocommerce_sumo_paypal_reference_txns_dev_debug_enabled' ).is( ':checked' ) ) {
		$( '#woocommerce_sumo_paypal_reference_txns_user_roles_for_dev' ).closest( 'tr' ).show();
	}

	$( '#woocommerce_sumo_paypal_reference_txns_dev_debug_enabled' ).change( function () {
		$( '#woocommerce_sumo_paypal_reference_txns_user_roles_for_dev' ).closest( 'tr' ).hide();

		if ( this.checked ) {
			$( '#woocommerce_sumo_paypal_reference_txns_user_roles_for_dev' ).closest( 'tr' ).show();
		}
	} );

	// Uploading files
	var file_frame;
	if ( $( '#logo_attachment_id' ).val() !== '' ) {
		$( '#upload_logo_button' ).val( sumosubs_admin_wc_payments_paypal_reference_params.paypal_change_logo_button_text );
	}

	$( document ).on( 'click', '#upload_logo_button', function ( event ) {
		var $el = $( this );

		event.preventDefault();

		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media( {
			title: $el.data( 'choose' ),
			button: {
				text: $( '#logo_attachment_id' ).val() !== '' ? $el.val() : $el.data( 'update' )
			},
			states: [
				new wp.media.controller.Library( {
					title: $el.data( 'choose' ),
					library: wp.media.query( { type: 'image' } ),
					filterable: 'all',
					editable: true,
					suggestedWidth: '90',
					suggestedHeight: '60'
				} )
			]
		} );

		// When an image is selected, run a callback.
		file_frame.on( 'select', function () {
			// We set multiple to false so only get one image from the uploader
			attachment = file_frame.state().get( 'selection' ).first().toJSON();

			if ( attachment.type !== 'image' ) {
				alert( sumosubs_admin_wc_payments_paypal_reference_params.admin_notice );
				return false;
			}

			if ( attachment.id && attachment.url ) {
				$( '#logo-preview' ).show();
				$( '#logo_attachment' ).hide();

				// Do something with attachment.id and/or attachment.url here
				$( '#logo-preview' ).attr( 'src', attachment.url ).css( 'width', 'auto' );
				$( '#logo_attachment_id' ).val( attachment.id );
			} else {
				$( '#logo-preview' ).hide();
				$( '#logo_attachment' ).show();
			}
		} );

		// Finally, open the modal
		file_frame.open();
	} );
} );
