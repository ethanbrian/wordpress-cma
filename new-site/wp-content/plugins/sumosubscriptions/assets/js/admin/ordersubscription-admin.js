/* global sumosubs_admin_ordersubscription_params */

jQuery( function ( $ ) {

	// sumosubs_admin_ordersubscription_params is required to continue, ensure the object exists
	if ( typeof sumosubs_admin_ordersubscription_params === 'undefined' ) {
		return false;
	}

	$( '#sumo_order_subsc_get_product_selected_type' ).change( function () {
		$( '#get_included_products' ).closest( 'tr' ).hide();
		$( '#get_excluded_products' ).closest( 'tr' ).hide();
		$( '#get_included_categories' ).closest( 'tr' ).hide();
		$( '#get_excluded_categories' ).closest( 'tr' ).hide();

		switch ( this.value ) {
			case 'included-products':
				$( '#get_included_products' ).closest( 'tr' ).show();
				break;
			case 'excluded-products':
				$( '#get_excluded_products' ).closest( 'tr' ).show();
				break;
			case 'included-categories':
				$( '#get_included_categories' ).closest( 'tr' ).show();
				break;
			case 'excluded-categories':
				$( '#get_excluded_categories' ).closest( 'tr' ).show();
				break;
		}
	} );

	var getChosenBy = function ( $chosen_by ) {
		$chosen_by = $chosen_by || $( '#sumo_order_subsc_chosen_by_option' ).val();

		$( '#sumo_order_subsc_check_option' ).closest( 'tr' ).nextAll( 'tr' ).hide();
		$( '#sumo_order_subsc_get_product_selected_type' ).closest( 'tr' ).show();
		$( '#sumo_order_subsc_get_product_selected_type' ).change();
		$( '#sumo_order_subsc_chosen_by_option' ).closest( 'tr' ).show();
		$( '#sumo_order_subsc_checkout_option' ).closest( 'tr' ).show();
		$( '#sumo_min_order_total_to_display_order_subscription' ).closest( 'tr' ).show();
		$( '#sumo_order_subsc_checkout_label_option' ).closest( 'tr' ).show();
		$( '#sumo_order_subsc_form_position' ).closest( 'tr' ).show();
		$( '#sumo_order_subsc_min_recurring_cycle_user_can_select' ).closest( 'tr' ).hide();
		$( '#sumo_order_subsc_max_recurring_cycle_user_can_select' ).closest( 'tr' ).hide();
		$( '#sumo_order_subsc_duration_value_user_can_select' ).closest( 'tr' ).hide();
		$( '#sumo_order_subsc_custom_css' ).closest( 'tr' ).show();
		$( '#sumo_display_order_subscription_in_cart' ).closest( 'tr' ).show();
		$( '#sumo_order_subsc_has_signup' ).closest( 'tr' ).show();

		if ( $chosen_by === 'admin' ) {
			$( '#sumo_order_subsc_duration_option' ).closest( 'tr' ).show();
			$( '#sumo_order_subsc_duration_value_option' ).closest( 'tr' ).show();
			$( '#sumo_order_subsc_recurring_option' ).closest( 'tr' ).show();
		} else if ( $chosen_by === 'user' ) {
			$( '#sumo_get_order_subsc_duration_period_selector_for_users' ).closest( 'tr' ).show();
			$( '#sumo_order_subsc_enable_recurring_cycle_option_for_users' ).closest( 'tr' ).show();
			$( '#sumo_order_subsc_duration_checkout_label_option' ).closest( 'tr' ).show();
			$( '#sumo_order_subsc_duration_value_checkout_label_option' ).closest( 'tr' ).show();
			$( '#sumo_order_subsc_recurring_checkout_label_option' ).closest( 'tr' ).show();
			$( '#sumo_order_subsc_duration_value_user_can_select' ).closest( 'tr' ).show();

			if ( $( '#sumo_order_subsc_enable_recurring_cycle_option_for_users' ).is( ':checked' ) ) {
				$( '#sumo_order_subsc_min_recurring_cycle_user_can_select' ).closest( 'tr' ).show();
				$( '#sumo_order_subsc_max_recurring_cycle_user_can_select' ).closest( 'tr' ).show();
			}
		}

		if ( $( '#sumo_order_subsc_has_signup' ).is( ':checked' ) ) {
			$( '#sumo_order_subsc_signup_fee' ).closest( 'tr' ).show();
		}
	};

	$( '#mainform' ).submit( function ( ) {
		if ( !$( '#sumo_order_subsc_check_option' ).is( ':checked' ) ) {
			return true;
		}

		if ( 'user' !== $( '#sumo_order_subsc_chosen_by_option' ).val() ) {
			return true;
		}

		if ( parseInt( $( '#min_subsc_duration_value_D' ).val() ) > parseInt( $( '#max_subsc_duration_value_D' ).val() ) ) {
			alert( sumosubs_admin_ordersubscription_params.warning_message_upon_invalid_no_of_days );
			return false;
		} else if ( parseInt( $( '#min_subsc_duration_value_W' ).val() ) > parseInt( $( '#max_subsc_duration_value_W' ).val() ) ) {
			alert( sumosubs_admin_ordersubscription_params.warning_message_upon_invalid_no_of_weeks );
			return false;
		} else if ( parseInt( $( '#min_subsc_duration_value_M' ).val() ) > parseInt( $( '#max_subsc_duration_value_M' ).val() ) ) {
			alert( sumosubs_admin_ordersubscription_params.warning_message_upon_invalid_no_of_months );
			return false;
		} else if ( parseInt( $( '#min_subsc_duration_value_Y' ).val() ) > parseInt( $( '#max_subsc_duration_value_Y' ).val() ) ) {
			alert( sumosubs_admin_ordersubscription_params.warning_message_upon_invalid_no_of_years );
			return false;
		}
		return true;
	} );

	$( '#sumo_order_subsc_check_option' ).change( function () {
		$( this ).closest( 'tr' ).nextAll( 'tr' ).hide();

		if ( this.checked ) {
			getChosenBy();
		}
	} ).change();

	$( '#sumo_order_subsc_chosen_by_option' ).change( function () {
		getChosenBy( this.value );
	} );

	$( document ).on( 'change', '#sumo_order_subsc_duration_option', function () {
		var $elements = $( '#sumo_order_subsc_duration_value_option' );
		$elements.empty();
		var duration_options = { };

		switch ( $( this ).val() ) {
			case 'W':
				var duration_options = sumosubs_admin_ordersubscription_params.subscription_week_duration_options;
				break;
			case 'M':
				var duration_options = sumosubs_admin_ordersubscription_params.subscription_month_duration_options;
				break;
			case 'Y':
				var duration_options = sumosubs_admin_ordersubscription_params.subscription_year_duration_options;
				break;
			default :
				var duration_options = sumosubs_admin_ordersubscription_params.subscription_day_duration_options;
				break;
		}

		$.each( duration_options, function ( value, key ) {
			$elements.append( $( '<option></option>' ).attr( 'value', value ).text( key ) );
		} );
	} );

	$( document ).on( 'change', '#sumo_order_subsc_enable_recurring_cycle_option_for_users', function () {
		$( '#sumo_order_subsc_min_recurring_cycle_user_can_select' ).closest( 'tr' ).hide();
		$( '#sumo_order_subsc_max_recurring_cycle_user_can_select' ).closest( 'tr' ).hide();

		if ( this.checked ) {
			$( '#sumo_order_subsc_min_recurring_cycle_user_can_select' ).closest( 'tr' ).show();
			$( '#sumo_order_subsc_max_recurring_cycle_user_can_select' ).closest( 'tr' ).show();
		}
	} );

	$( document ).on( 'change', '#sumo_order_subsc_has_signup', function () {
		$( '#sumo_order_subsc_signup_fee' ).closest( 'tr' ).hide();

		if ( this.checked ) {
			$( '#sumo_order_subsc_signup_fee' ).closest( 'tr' ).show();
		}
	} );

	$( '#mainform' ).submit( function () {
		if ( $( '#sumo_order_subsc_check_option' ).is( ':checked' ) && 'user' === $( '#sumo_order_subsc_chosen_by_option' ).val() && $( '#sumo_order_subsc_enable_recurring_cycle_option_for_users' ).is( ':checked' ) ) {
			if ( '0' !== $( '#sumo_order_subsc_max_recurring_cycle_user_can_select' ).val() && parseInt( $( '#sumo_order_subsc_max_recurring_cycle_user_can_select' ).val() ) < parseInt( $( '#sumo_order_subsc_min_recurring_cycle_user_can_select' ).val() ) ) {
				alert( sumosubs_admin_ordersubscription_params.warning_message_upon_max_recurring_cycle );
				return false;
			}
		}
	} );
} );
