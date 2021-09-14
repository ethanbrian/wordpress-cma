<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly
}

/**
 * Handle subscription settings in product backend. 
 * 
 * @class SUMOSubscriptions_Product_Settings
 */
class SUMOSubscriptions_Product_Settings {

	protected static $subscription_fields = array(
		'susbcription_status'                     => 'select',
		'susbcription_trial_enable_disable'       => 'select',
		'susbcription_signusumoee_enable_disable' => 'select',
		'susbcription_period'                     => 'select',
		'trial_period'                            => 'select',
		'synchronize_period'                      => 'select',
		'susbcription_period_value'               => 'select',
		'trial_period_value'                      => 'select',
		'synchronize_period_value'                => 'select',
		'synchronize_start_year'                  => 'number',
		'subscribed_after_sync_date_type'         => 'select',
		'xtra_time_to_charge_full_fee'            => 'number',
		'cutoff_time_to_not_renew_nxt_subs_cycle' => 'number',
		'susbcription_fee_type_selector'          => 'select',
		'trial_price'                             => 'price',
		'signup_price'                            => 'price',
		'recurring_period_value'                  => 'select',
		'enable_additional_digital_downloads'     => 'checkbox',
		'choose_downloadable_products'            => 'search',
			) ;

	/**
	 * Init SUMOSubscriptions_Product_Settings.
	 */
	public static function init() {
		add_action( 'woocommerce_product_options_general_product_data', __CLASS__ . '::subscription_product_settings' ) ;
		add_action( 'woocommerce_process_product_meta', __CLASS__ . '::save_subscription_product_data' ) ;
		add_action( 'woocommerce_product_after_variable_attributes', __CLASS__ . '::subscription_variation_product_settings', 10, 3 ) ;
		add_action( 'woocommerce_save_product_variation', __CLASS__ . '::save_subscription_variation_data', 10, 2 ) ;
	}

	/**
	 * Display subscription product setting fields.
	 *
	 * @global object $post The Product post ID
	 */
	public static function subscription_product_settings() {
		global $post ;

		$product = wc_get_product( $post ) ;
		if (
				! $product ||
				in_array( $product->get_type(), array( 'variable' ) ) ||
				is_array( get_post_meta( $post->ID, 'sumo_susbcription_status', true ) )
		) {
			return ;
		}

		$subscription_duration       = get_post_meta( $post->ID, 'sumo_susbcription_period', true ) ;
		$subscription_duration_value = get_post_meta( $post->ID, 'sumo_susbcription_period_value', true ) ;
		$trial_duration              = get_post_meta( $post->ID, 'sumo_trial_period', true ) ;
		$trial_duration_value        = get_post_meta( $post->ID, 'sumo_trial_period_value', true ) ;
		$synchronize_duration        = get_post_meta( $post->ID, 'sumo_synchronize_period', true ) ;
		$synchronize_duration_value  = get_post_meta( $post->ID, 'sumo_synchronize_period_value', true ) ;
		$synchronize_start_year      = get_post_meta( $post->ID, 'sumo_synchronize_start_year', true ) ;

		$bckwrd_optional_signup_status = 'yes' === get_post_meta( $post->ID, 'sumo_susbcription_signup_fee_is_optional_for_user', true ) ;
		$bckwrd_optional_trial_status  = 'yes' === get_post_meta( $post->ID, 'sumo_susbcription_trial_is_optional_for_user', true ) ;

		$signup_status = $bckwrd_optional_signup_status ? '3' : get_post_meta( $post->ID, 'sumo_susbcription_signusumoee_enable_disable', true ) ;
		$trial_status  = $bckwrd_optional_trial_status ? '3' : get_post_meta( $post->ID, 'sumo_susbcription_trial_enable_disable', true ) ;

		$xtra_time_to_charge_full_fee = get_post_meta( $post->ID, 'sumo_xtra_time_to_charge_full_fee', true ) ;
		$xtra_time_to_charge_full_fee = is_numeric( $xtra_time_to_charge_full_fee ) ? $xtra_time_to_charge_full_fee : get_post_meta( $post->ID, 'sumo_xtra_duration_to_charge_full_fee', true ) ; //BKWD CMPT

		woocommerce_wp_select( array(
			'id'            => 'sumo_susbcription_status',
			'label'         => __( 'SUMO Subscriptions', 'sumosubscriptions' ),
			'wrapper_class' => 'sumo_subscription_fields',
			'options'       => array(
				'2' => __( 'Disable', 'sumosubscriptions' ),
				'1' => __( 'Enable', 'sumosubscriptions' ),
			),
		) ) ;
		?>
		<p class="form-field sumo_susbcription_period_value_field sumo_subscription_fields sumosubscription_simple">
			<label for="sumo_susbcription_period_value"><?php esc_html_e( 'Subscription Duration', 'sumosubscriptions' ) ; ?></label>
			<select id="sumo_susbcription_period_value" name="sumo_susbcription_period_value" style="width: 35% !important">
				<?php foreach ( sumo_get_subscription_duration_options( $subscription_duration, false ) as $each_key => $each_value ) { ?>
					<option value="<?php echo esc_attr( $each_key ) ; ?>" <?php selected( $subscription_duration_value == $each_key, true, true ); ?>><?php echo esc_html( $each_value ) ; ?></option>
				<?php } ?>
			</select>
			<select id="sumo_susbcription_period" name="sumo_susbcription_period" style="width: 35% !important">
				<?php foreach ( sumosubs_get_duration_period_selector() as $each_key => $each_value ) { ?>
					<option value="<?php echo esc_attr( $each_key ) ; ?>" <?php selected( $subscription_duration == $each_key, true, true ); ?>><?php echo esc_html( $each_value ) ; ?></option>
				<?php } ?>
			</select>
		</p>
		<?php
		if ( SUMO_Subscription_Synchronization::$sync_enabled_site_wide ) {
			?>
			<p class="form-field sumo_synchronize_duration_fields sumo_subscription_fields sumosubscription_simple">
				<label for="sumo_synchronize_duration"><?php esc_html_e( 'Synchronize Renewals', 'sumosubscriptions' ) ; ?></label>
				<select id="sumo_synchronize_period_value" name="sumo_synchronize_period_value" class="sumosubscription_simple" style="width: 35% !important;">
					<?php foreach ( SUMO_Subscription_Synchronization::get_duration_options( $subscription_duration ) as $each_key => $each_value ) { ?>
						<option value="<?php echo esc_attr( $each_key ) ; ?>" <?php selected( $synchronize_duration_value == $each_key, true, true ); ?>><?php echo esc_html( $each_value ) ; ?></option>
					<?php } ?>
				</select>
				<select id="sumo_synchronize_period" name="sumo_synchronize_period" class="sumosubscription_simple" style="width: 35% !important;" >
					<?php foreach ( SUMO_Subscription_Synchronization::get_duration_options( $subscription_duration, true ) as $each_key => $each_value ) { ?>
						<option value="<?php echo esc_attr( $each_key ) ; ?>" <?php selected( $synchronize_duration == $each_key, true, true ); ?>><?php echo esc_html( $each_value ) ; ?></option>
					<?php } ?>
				</select>                    
			</p>
			<?php if ( '1' === SUMO_Subscription_Synchronization::$sync_mode ) { ?>
				<p class="form-field sumo_synchronize_start_year_fields sumo_subscription_fields sumosubscription_simple">
					<label for="sumo_synchronize_start_year"><?php esc_html_e( 'Synchronization Starting Year', 'sumosubscriptions' ) ; ?></label>
					<input type="number" style="width:35% !important;" min="2016" id="sumo_synchronize_start_year" name="sumo_synchronize_start_year" class="sumosubscription_simple" value="<?php echo ! empty( $synchronize_start_year ) ? esc_attr( $synchronize_start_year ) : '2017' ; ?>">
				</p>
			<?php } ?>
			<p class="form-field sumo_subscribed_after_sync_date_type_fields sumo_subscription_fields sumosubscription_simple">
				<label for="sumo_subscribed_after_sync_date_type"><?php esc_html_e( 'For Subscriptions Purchased After Sync Date', 'sumosubscriptions' ) ; ?></label>
				<select id="sumo_subscribed_after_sync_date_type" name="sumo_subscribed_after_sync_date_type" class="sumosubscription_simple" style="width: 60% !important;">
					<option value="xtra-time-to-charge-full-fee" <?php selected( get_post_meta( $post->ID, 'sumo_subscribed_after_sync_date_type', true ), 'xtra-time-to-charge-full-fee', true ) ; ?>><?php esc_html_e( 'Give Extra Duration to Charge Full Payment', 'sumosubscriptions' ) ; ?></option>
					<option value="cutoff-time-to-not-renew-nxt-subs-cycle" <?php selected( get_post_meta( $post->ID, 'sumo_subscribed_after_sync_date_type', true ), 'cutoff-time-to-not-renew-nxt-subs-cycle', true ) ; ?>><?php esc_html_e( 'Give Cut Off Time to not Renew during Next Subscription Cycle', 'sumosubscriptions' ) ; ?></option>
				</select>
			</p>
			<p class="form-field sumo_xtra_time_to_charge_full_fee_in_sync_fields sumo_subscription_fields sumosubscription_simple">
				<?php
				echo wc_help_tip( __( 'When the duration is set here and if the customer purchase even after the synchronization date/days, full payment will be charged from the customer during the extra duration. 
                                           After the extra duration, initial payment order will be created only on the next sync date and amount will be charged that time.
                                           <br><b>Note</b>: 1) If the subscription is placed between the extra duration and next sync date, status of the respective subscription will be Pending
                                           <br>2) If the extra duration is set, "Payment for Synchronized Period" settings configured in "Synchronization Settings" will not be considered.'
								, 'sumosubscriptions' ) ) ;
				?>
				<label for="sumo_xtra_time_to_charge_full_fee_in_sync"><?php esc_html_e( 'Extra Duration to Charge Full Payment (in days)', 'sumosubscriptions' ) ; ?></label>
				<input type="number" style="width:35% !important;" min="0" max="<?php echo esc_attr( SUMO_Subscription_Synchronization::get_xtra_duration_options( $subscription_duration, $subscription_duration_value ) ) ; ?>" id="sumo_xtra_time_to_charge_full_fee" name="sumo_xtra_time_to_charge_full_fee" class="sumosubscription_simple" value="<?php echo esc_attr( $xtra_time_to_charge_full_fee ) ; ?>">
			</p>
			<p class="form-field sumo_cutoff_time_to_not_renew_nxt_subs_cycle_in_sync_fields sumo_subscription_fields sumosubscription_simple">                
				<label for="sumo_cutoff_time_to_not_renew_nxt_subs_cycle_in_sync"><?php esc_html_e( 'Cut Off Time to not Renew during Next Subscription Cycle (in days)', 'sumosubscriptions' ) ; ?></label>
				<input type="number" style="width:35% !important;" min="0" max="<?php echo esc_attr( SUMO_Subscription_Synchronization::get_xtra_duration_options( $subscription_duration, $subscription_duration_value ) ) ; ?>" id="sumo_cutoff_time_to_not_renew_nxt_subs_cycle" name="sumo_cutoff_time_to_not_renew_nxt_subs_cycle" class="sumosubscription_simple" value="<?php echo esc_attr( get_post_meta( $post->ID, 'sumo_cutoff_time_to_not_renew_nxt_subs_cycle', true ) ) ; ?>">
			</p>
			<?php
		}
		woocommerce_wp_select( array(
			'id'            => 'sumo_susbcription_trial_enable_disable',
			'wrapper_class' => 'sumo_subscription_fields sumosubscription_simple',
			'label'         => __( 'Trial Period', 'sumosubscriptions' ),
			'options'       => array(
				'2' => __( 'Disable', 'sumosubscriptions' ),
				'1' => __( 'Forced Trial', 'sumosubscriptions' ),
				'3' => __( 'Optional Trial', 'sumosubscriptions' ),
			),
			'value'         => $trial_status
		) ) ;
		woocommerce_wp_select( array(
			'id'            => 'sumo_susbcription_fee_type_selector',
			'wrapper_class' => 'sumo_subscription_fields sumosubscription_simple',
			'label'         => __( 'Select Trial Type', 'sumosubscriptions' ),
			'options'       => array(
				'free' => __( 'Free Trial', 'sumosubscriptions' ),
				'paid' => __( 'Paid Trial', 'sumosubscriptions' ),
			),
		) ) ;
		woocommerce_wp_text_input( array(
			'id'            => 'sumo_trial_price',
			'label'         => __( 'Trial Fee', 'sumosubscriptions' ) . '(' . get_woocommerce_currency_symbol() . ')',
			'wrapper_class' => 'sumo_subscription_fields sumosubscription_simple',
			'placeholder'   => __( 'Enter the Trial Fee', 'sumosubscriptions' ),
			'data_type'     => 'price',
		) ) ;
		?>
		<p class="form-field sumo_trial_period_value_field sumo_subscription_fields sumosubscription_simple">
			<label for="sumo_trial_period_value"><?php esc_html_e( 'Trial Duration', 'sumosubscriptions' ) ; ?></label>
			<select id="sumo_trial_period_value" name="sumo_trial_period_value" style="width: 35% !important">
				<?php foreach ( sumo_get_subscription_duration_options( $trial_duration, false ) as $each_key => $each_value ) { ?>
					<option value="<?php echo esc_attr( $each_key ) ; ?>" <?php selected( $trial_duration_value == $each_key, true, true ); ?>><?php echo esc_html( $each_value ) ; ?></option>
				<?php } ?>
			</select>
			<select id="sumo_trial_period" name="sumo_trial_period" style="width: 35% !important">
				<?php foreach ( sumosubs_get_duration_period_selector() as $each_key => $each_value ) { ?>
					<option value="<?php echo esc_attr( $each_key ) ; ?>" <?php selected( $trial_duration == $each_key, true, true ); ?>><?php echo esc_html( $each_value ) ; ?></option>
				<?php } ?>
			</select>
		</p>
		<?php
		woocommerce_wp_select( array(
			'id'            => 'sumo_susbcription_signusumoee_enable_disable',
			'wrapper_class' => 'sumo_subscription_fields sumosubscription_simple',
			'label'         => __( 'Sign up Fee', 'sumosubscriptions' ),
			'options'       => array(
				'2' => __( 'Disable', 'sumosubscriptions' ),
				'1' => __( 'Forced Sign up', 'sumosubscriptions' ),
				'3' => __( 'Optional Sign up', 'sumosubscriptions' ),
			),
			'value'         => $signup_status,
		) ) ;
		woocommerce_wp_text_input( array(
			'id'            => 'sumo_signup_price',
			'label'         => __( 'Sign up Value', 'sumosubscriptions' ) . '(' . get_woocommerce_currency_symbol() . ')',
			'wrapper_class' => 'sumo_subscription_fields sumosubscription_simple',
			'placeholder'   => __( 'Enter the Sign up Fee', 'sumosubscriptions' ),
			'data_type'     => 'price',
		) ) ;
		woocommerce_wp_select( array(
			'id'            => 'sumo_recurring_period_value',
			'wrapper_class' => 'sumo_subscription_fields sumosubscription_simple',
			'label'         => __( 'Recurring Cycle', 'sumosubscriptions' ),
			'options'       => sumo_get_subscription_recurring_options()
		) ) ;

		if ( sumo_is_additional_digital_downloads_enabled_in_the_site() ) {
			woocommerce_wp_checkbox( array(
				'id'            => 'sumo_enable_additional_digital_downloads',
				'wrapper_class' => 'sumo_subscription_fields sumosubscription_simple',
				'label'         => __( 'Enable Additional Digital Downloads', 'sumosubscriptions' ),
			) ) ;
			?>
			<p class="form-field sumo_choose_downloadable_products_field sumo_subscription_fields sumosubscription_simple">
				<?php
				sumosubs_wc_search_field( array(
					'class'       => 'wc-product-search sumosubscription_simple',
					'id'          => 'sumo_choose_downloadable_products',
					'type'        => 'product',
					'action'      => 'sumosubscription_json_search_downloadable_products_and_variations',
					'title'       => __( 'Choose the Downloadable Product(s)', 'sumosubscriptions' ),
					'placeholder' => __( 'Search for a product&hellip;', 'sumosubscriptions' ),
					'options'     => ( array ) get_post_meta( $post->ID, 'sumo_choose_downloadable_products', true )
				) ) ;
				?>
			</p>
			<?php
		}
	}

	/**
	 * Display subscription variation product setting fields.
	 *
	 * @param int $loop
	 * @param mixed $variation_data
	 * @param object $variation The Variation post ID
	 */
	public static function subscription_variation_product_settings( $loop, $variation_data, $variation ) {
		$variation_data              = get_post_meta( $variation->ID ) ;
		$subscription_duration       = get_post_meta( $variation->ID, 'sumo_susbcription_period', true ) ;
		$subscription_duration_value = get_post_meta( $variation->ID, 'sumo_susbcription_period_value', true ) ;
		$trial_duration              = get_post_meta( $variation->ID, 'sumo_trial_period', true ) ;
		$trial_duration_value        = get_post_meta( $variation->ID, 'sumo_trial_period_value', true ) ;
		$synchronize_duration        = get_post_meta( $variation->ID, 'sumo_synchronize_period', true ) ;
		$synchronize_duration_value  = get_post_meta( $variation->ID, 'sumo_synchronize_period_value', true ) ;
		$synchronize_start_year      = get_post_meta( $variation->ID, 'sumo_synchronize_start_year', true ) ;

		$bckwrd_optional_signup_status = 'yes' === get_post_meta( $variation->ID, 'sumo_susbcription_signup_fee_is_optional_for_user', true ) ;
		$bckwrd_optional_trial_status  = 'yes' === get_post_meta( $variation->ID, 'sumo_susbcription_trial_is_optional_for_user', true ) ;

		$signup_status = $bckwrd_optional_signup_status ? '3' : get_post_meta( $variation->ID, 'sumo_susbcription_signusumoee_enable_disable', true ) ;
		$trial_status  = $bckwrd_optional_trial_status ? '3' : get_post_meta( $variation->ID, 'sumo_susbcription_trial_enable_disable', true ) ;

		$xtra_time_to_charge_full_fee = get_post_meta( $variation->ID, 'sumo_xtra_time_to_charge_full_fee', true ) ;
		$xtra_time_to_charge_full_fee = is_numeric( $xtra_time_to_charge_full_fee ) ? $xtra_time_to_charge_full_fee : get_post_meta( $variation->ID, 'sumo_xtra_duration_to_charge_full_fee', true ) ; //BKWD CMPT

		woocommerce_wp_select( array(
			'id'            => 'sumo_susbcription_status[' . $loop . ']',
			'label'         => __( 'SUMO Subscriptions', 'sumosubscriptions' ),
			'wrapper_class' => "sumo_subscription_fields{$loop} sumo_subscription_fields_wrapper",
			'value'         => isset( $variation_data[ 'sumo_susbcription_status' ][ 0 ] ) ? $variation_data[ 'sumo_susbcription_status' ][ 0 ] : '',
			'options'       => array(
				'2' => __( 'Disable', 'sumosubscriptions' ),
				'1' => __( 'Enable', 'sumosubscriptions' ),
			),
		) ) ;
		?>
		<p class="form-field sumo_susbcription_period_value[<?php echo esc_attr( $loop ) ; ?>]_field  sumo_subscription_fields<?php echo esc_attr( $loop ) ; ?> sumo_subscription_fields_wrapper sumosubscription_variable<?php echo esc_attr( $loop ) ; ?>">
			<label for="sumo_susbcription_period_value[<?php echo esc_attr( $loop ) ; ?>]"><?php esc_html_e( 'Subscription Duration', 'sumosubscriptions' ) ; ?></label>
			<select style="width:25% !important;float:left;" id="sumo_susbcription_period_value[<?php echo esc_attr( $loop ) ; ?>]" name="sumo_susbcription_period_value[<?php echo esc_attr( $loop ) ; ?>]">
				<?php foreach ( sumo_get_subscription_duration_options( $subscription_duration, false ) as $each_key => $each_value ) { ?>
					<option value="<?php echo esc_attr( $each_key ) ; ?>" <?php selected( $subscription_duration_value == $each_key, true, true ); ?>><?php echo esc_html( $each_value ) ; ?></option>
				<?php } ?>
			</select>
			<select  style="width:25% !important;float:left;margin-bottom:10px;" id="sumo_susbcription_period[<?php echo esc_attr( $loop ) ; ?>]" name="sumo_susbcription_period[<?php echo esc_attr( $loop ) ; ?>]">
				<?php foreach ( sumosubs_get_duration_period_selector() as $each_key => $each_value ) { ?>
					<option value="<?php echo esc_attr( $each_key ) ; ?>" <?php selected( $subscription_duration == $each_key, true, true ); ?>><?php echo esc_html( $each_value ) ; ?></option>
				<?php } ?>
			</select>
		</p>
		<?php
		if ( SUMO_Subscription_Synchronization::$sync_enabled_site_wide ) {
			?>
			<p class="form-field sumo_synchronize_duration_fields<?php echo esc_attr( $loop ) ; ?> sumo_subscription_fields<?php echo esc_attr( $loop ) ; ?> sumo_subscription_fields_wrapper sumosubscription_variable<?php echo esc_attr( $loop ) ; ?>">
				<label for="sumo_synchronize_duration[<?php echo esc_attr( $loop ) ; ?>]"><?php esc_html_e( 'Synchronize Renewals', 'sumosubscriptions' ) ; ?></label>
				<select style="width:25% !important;float:left;" id="sumo_synchronize_period_value[<?php echo esc_attr( $loop ) ; ?>]" name="sumo_synchronize_period_value[<?php echo esc_attr( $loop ) ; ?>]" class="sumosubscription_variable<?php echo esc_attr( $loop ) ; ?>">
					<?php foreach ( SUMO_Subscription_Synchronization::get_duration_options( $subscription_duration ) as $each_key => $each_value ) { ?>
						<option value="<?php echo esc_attr( $each_key ) ; ?>" <?php selected( $synchronize_duration_value == $each_key, true, true ); ?>><?php echo esc_html( $each_value ) ; ?></option>
					<?php } ?>
				</select>
				<select style="width:25% !important;float:left;margin-bottom:10px;" id="sumo_synchronize_period[<?php echo esc_attr( $loop ) ; ?>]" name="sumo_synchronize_period[<?php echo esc_attr( $loop ) ; ?>]" class="sumosubscription_variable<?php echo esc_attr( $loop ) ; ?>" >
					<?php foreach ( SUMO_Subscription_Synchronization::get_duration_options( $subscription_duration, true ) as $each_key => $each_value ) { ?>
						<option value="<?php echo esc_attr( $each_key ) ; ?>" <?php selected( $synchronize_duration == $each_key, true, true ); ?>><?php echo esc_html( $each_value ) ; ?></option>
					<?php } ?>
				</select>
			</p>
			<?php if ( '1' === SUMO_Subscription_Synchronization::$sync_mode ) { ?>
				<p class="form-field sumo_synchronize_start_year_fields<?php echo esc_attr( $loop ) ; ?> sumo_subscription_fields<?php echo esc_attr( $loop ) ; ?> sumo_subscription_fields_wrapper sumosubscription_variable<?php echo esc_attr( $loop ) ; ?>">
					<label for="sumo_synchronize_start_year[<?php echo esc_attr( $loop ) ; ?>]"><?php esc_html_e( 'Synchronization Starting Year', 'sumosubscriptions' ) ; ?></label>
					<input type="number" style="width:25% !important;float:left;margin-bottom:10px;" min="2016" id="sumo_synchronize_start_year<?php echo esc_attr( $loop ) ; ?>" name="sumo_synchronize_start_year[<?php echo esc_attr( $loop ) ; ?>]" class="sumosubscription_variable<?php echo esc_attr( $loop ) ; ?>" value="<?php echo ! empty( $synchronize_start_year ) ? esc_attr( $synchronize_start_year ) : '2017' ; ?>">
				</p>
			<?php } ?>
			<p class="form-field sumo_subscribed_after_sync_date_type_fields<?php echo esc_attr( $loop ) ; ?> sumo_subscription_fields<?php echo esc_attr( $loop ) ; ?> sumo_subscription_fields_wrapper sumosubscription_variable<?php echo esc_attr( $loop ) ; ?>">
				<label for="sumo_subscribed_after_sync_date_type[<?php echo esc_attr( $loop ) ; ?>]"><?php esc_html_e( 'For Subscriptions Purchased After Sync Date', 'sumosubscriptions' ) ; ?></label>
				<select style="width:25% !important;float:left;" id="sumo_subscribed_after_sync_date_type<?php echo esc_attr( $loop ) ; ?>" name="sumo_subscribed_after_sync_date_type[<?php echo esc_attr( $loop ) ; ?>]" class="sumosubscription_variable<?php echo esc_attr( $loop ) ; ?>">
					<option value="xtra-time-to-charge-full-fee" <?php selected( get_post_meta( $variation->ID, 'sumo_subscribed_after_sync_date_type', true ), 'xtra-time-to-charge-full-fee', true ) ; ?>><?php esc_html_e( 'Give Extra Duration to Charge Full Payment', 'sumosubscriptions' ) ; ?></option>
					<option value="cutoff-time-to-not-renew-nxt-subs-cycle" <?php selected( get_post_meta( $variation->ID, 'sumo_subscribed_after_sync_date_type', true ), 'cutoff-time-to-not-renew-nxt-subs-cycle', true ) ; ?>><?php esc_html_e( 'Give Cut Off Time to not Renew during Next Subscription Cycle', 'sumosubscriptions' ) ; ?></option>
				</select>
			</p>
			<p class="form-field sumo_xtra_time_to_charge_full_fee_in_sync_fields<?php echo esc_attr( $loop ) ; ?> sumo_subscription_fields<?php echo esc_attr( $loop ) ; ?> sumo_subscription_fields_wrapper sumosubscription_variable<?php echo esc_attr( $loop ) ; ?>">
				<?php
				echo wc_help_tip( __( 'When the duration is set here and if the customer purchase even after the synchronization date/days, full payment will be charged from the customer during the extra duration. 
                                           After the extra duration, initial payment order will be created only on the next sync date and amount will be charged that time.
                                           <br><b>Note</b>: 1) If the subscription is placed between the extra duration and next sync date, status of the respective subscription will be Pending
                                           <br>2) If the extra duration is set, "Payment for Synchronized Period" settings configured in "Synchronization Settings" will not be considered.'
								, 'sumosubscriptions' ) ) ;
				?>
				<label for="sumo_xtra_time_to_charge_full_fee_in_sync[<?php echo esc_attr( $loop ) ; ?>]"><?php esc_html_e( 'Extra Duration to Charge Full Payment (in days)', 'sumosubscriptions' ) ; ?></label>
				<input style="width:25% !important;float:left;margin-bottom:10px;" type="number" min="0" max="<?php echo esc_attr( SUMO_Subscription_Synchronization::get_xtra_duration_options( $subscription_duration, $subscription_duration_value ) ) ; ?>" id="sumo_xtra_time_to_charge_full_fee<?php echo esc_attr( $loop ) ; ?>" name="sumo_xtra_time_to_charge_full_fee[<?php echo esc_attr( $loop ) ; ?>]" class="sumosubscription_variable<?php echo esc_attr( $loop ) ; ?>" value="<?php echo esc_attr( $xtra_time_to_charge_full_fee ) ; ?>">
			</p>
			<p class="form-field sumo_cutoff_time_to_not_renew_nxt_subs_cycle_in_sync_fields<?php echo esc_attr( $loop ) ; ?> sumo_subscription_fields<?php echo esc_attr( $loop ) ; ?> sumo_subscription_fields_wrapper sumosubscription_variable<?php echo esc_attr( $loop ) ; ?>">               
				<label for="sumo_cutoff_time_to_not_renew_nxt_subs_cycle_in_sync[<?php echo esc_attr( $loop ) ; ?>]"><?php esc_html_e( 'Cut Off Time to not Renew during Next Subscription Cycle (in days)', 'sumosubscriptions' ) ; ?></label>
				<input style="width:25% !important;float:left;margin-bottom:10px;" type="number" min="0" max="<?php echo esc_attr( SUMO_Subscription_Synchronization::get_xtra_duration_options( $subscription_duration, $subscription_duration_value ) ) ; ?>" id="sumo_cutoff_time_to_not_renew_nxt_subs_cycle<?php echo esc_attr( $loop ) ; ?>" name="sumo_cutoff_time_to_not_renew_nxt_subs_cycle[<?php echo esc_attr( $loop ) ; ?>]" class="sumosubscription_variable<?php echo esc_attr( $loop ) ; ?>" value="<?php echo esc_attr( get_post_meta( $variation->ID, 'sumo_cutoff_time_to_not_renew_nxt_subs_cycle', true ) ) ; ?>">
			</p>
			<?php
		}
		woocommerce_wp_select( array(
			'id'            => 'sumo_susbcription_trial_enable_disable[' . $loop . ']',
			'wrapper_class' => "sumo_subscription_fields{$loop} sumosubscription_variable{$loop} sumo_subscription_fields_wrapper",
			'label'         => __( 'Trial Period', 'sumosubscriptions' ),
			'value'         => isset( $variation_data[ 'sumo_susbcription_trial_enable_disable' ][ 0 ] ) ? $variation_data[ 'sumo_susbcription_trial_enable_disable' ][ 0 ] : '',
			'options'       => array(
				'2' => __( 'Disable', 'sumosubscriptions' ),
				'1' => __( 'Forced Trial', 'sumosubscriptions' ),
				'3' => __( 'Optional Trial', 'sumosubscriptions' ),
			),
			'value'         => $trial_status
		) ) ;
		woocommerce_wp_select( array(
			'id'            => 'sumo_susbcription_fee_type_selector[' . $loop . ']',
			'wrapper_class' => "sumo_subscription_fields{$loop} sumosubscription_variable{$loop} sumo_subscription_fields_wrapper",
			'label'         => __( 'Select Trial Type', 'sumosubscriptions' ),
			'value'         => isset( $variation_data[ 'sumo_susbcription_fee_type_selector' ][ 0 ] ) ? $variation_data[ 'sumo_susbcription_fee_type_selector' ][ 0 ] : '',
			'options'       => array(
				'free' => __( 'Free Trial', 'sumosubscriptions' ),
				'paid' => __( 'Paid Trial', 'sumosubscriptions' ),
			),
		) ) ;
		woocommerce_wp_text_input( array(
			'id'            => 'sumo_trial_price[' . $loop . ']',
			'label'         => __( 'Trial Fee', 'sumosubscriptions' ) . '(' . get_woocommerce_currency_symbol() . ')',
			'wrapper_class' => "sumo_subscription_fields{$loop} sumosubscription_variable{$loop} sumo_subscription_fields_wrapper",
			'data_type'     => 'price',
			'placeholder'   => __( 'Enter the Trial Fee', 'sumosubscriptions' ),
			'value'         => isset( $variation_data[ 'sumo_trial_price' ][ 0 ] ) ? $variation_data[ 'sumo_trial_price' ][ 0 ] : '',
		) ) ;
		?>
		<p class="form-field sumo_trial_period_value[<?php echo esc_attr( $loop ) ; ?>]_field  sumo_subscription_fields<?php echo esc_attr( $loop ) ; ?> sumo_subscription_fields_wrapper sumosubscription_variable<?php echo esc_attr( $loop ) ; ?>">
			<label for="sumo_trial_period_value[<?php echo esc_attr( $loop ) ; ?>]"><?php esc_html_e( 'Trial Duration', 'sumosubscriptions' ) ; ?></label>
			<select style="width:25% !important;float:left;" id="sumo_trial_period_value[<?php echo esc_attr( $loop ) ; ?>]" name="sumo_trial_period_value[<?php echo esc_attr( $loop ) ; ?>]">
				<?php foreach ( sumo_get_subscription_duration_options( $trial_duration, false ) as $each_key => $each_value ) { ?>
					<option value="<?php echo esc_attr( $each_key ) ; ?>" <?php selected( $trial_duration_value == $each_key, true, true ); ?>><?php echo esc_html( $each_value ) ; ?></option>
				<?php } ?>
			</select>
			<select style="width:25% !important;float:left;margin-bottom:10px;" id="sumo_trial_period[<?php echo esc_attr( $loop ) ; ?>]" name="sumo_trial_period[<?php echo esc_attr( $loop ) ; ?>]">
				<?php foreach ( sumosubs_get_duration_period_selector() as $each_key => $each_value ) { ?>
					<option value="<?php echo esc_attr( $each_key ) ; ?>" <?php selected( $trial_duration == $each_key, true, true ); ?>><?php echo esc_html( $each_value ) ; ?></option>
				<?php } ?>
			</select>
		</p>
		<?php
		woocommerce_wp_select( array(
			'id'            => 'sumo_susbcription_signusumoee_enable_disable[' . $loop . ']',
			'wrapper_class' => "sumo_subscription_fields{$loop} sumosubscription_variable{$loop} sumo_subscription_fields_wrapper",
			'label'         => __( 'Sign up Fee', 'sumosubscriptions' ),
			'value'         => isset( $variation_data[ 'sumo_susbcription_signusumoee_enable_disable' ][ 0 ] ) ? $variation_data[ 'sumo_susbcription_signusumoee_enable_disable' ][ 0 ] : '',
			'options'       => array(
				'2' => __( 'Disable', 'sumosubscriptions' ),
				'1' => __( 'Forced Sign up', 'sumosubscriptions' ),
				'3' => __( 'Optional Sign up', 'sumosubscriptions' ),
			),
			'value'         => $signup_status,
		) ) ;
		woocommerce_wp_text_input( array(
			'id'            => 'sumo_signup_price[' . $loop . ']',
			'label'         => __( 'Sign up Value', 'sumosubscriptions' ) . '(' . get_woocommerce_currency_symbol() . ')',
			'wrapper_class' => "sumo_subscription_fields{$loop} sumosubscription_variable{$loop} sumo_subscription_fields_wrapper",
			'data_type'     => 'price',
			'placeholder'   => __( 'Enter the Sign up Fee', 'sumosubscriptions' ),
			'value'         => isset( $variation_data[ 'sumo_signup_price' ][ 0 ] ) ? $variation_data[ 'sumo_signup_price' ][ 0 ] : '',
		) ) ;
		woocommerce_wp_select( array(
			'id'            => 'sumo_recurring_period_value[' . $loop . ']',
			'wrapper_class' => "sumo_subscription_fields{$loop} sumosubscription_variable{$loop} sumo_subscription_fields_wrapper",
			'label'         => __( 'Recurring Cycle', 'sumosubscriptions' ),
			'value'         => isset( $variation_data[ 'sumo_recurring_period_value' ][ 0 ] ) ? $variation_data[ 'sumo_recurring_period_value' ][ 0 ] : '',
			'options'       => sumo_get_subscription_recurring_options()
		) ) ;

		if ( sumo_is_additional_digital_downloads_enabled_in_the_site() ) {
			woocommerce_wp_checkbox( array(
				'id'            => 'sumo_enable_additional_digital_downloads[' . $loop . ']',
				'wrapper_class' => "sumo_subscription_fields{$loop} sumosubscription_variable{$loop} sumo_subscription_fields_wrapper",
				'label'         => __( 'Enable Additional Digital Downloads', 'sumosubscriptions' ),
				'value'         => isset( $variation_data[ 'sumo_enable_additional_digital_downloads' ][ 0 ] ) ? $variation_data[ 'sumo_enable_additional_digital_downloads' ][ 0 ] : ''
			) ) ;
			?>
			<p class="form-field sumo_choose_downloadable_products_field<?php echo esc_attr( $loop ) ; ?> sumo_subscription_fields<?php echo esc_attr( $loop ) ; ?> sumo_subscription_fields_wrapper sumosubscription_variable<?php echo esc_attr( $loop ) ; ?>">
				<?php
				sumosubs_wc_search_field( array(
					'class'       => "wc-product-search sumosubscription_variable{$loop}",
					'id'          => "sumo_choose_downloadable_products[{$loop}]",
					'type'        => 'product',
					'action'      => 'sumosubscription_json_search_downloadable_products_and_variations',
					'title'       => __( 'Choose the Downloadable Product(s)', 'sumosubscriptions' ),
					'placeholder' => __( 'Search for a product&hellip;', 'sumosubscriptions' ),
					'options'     => ( array ) get_post_meta( $variation->ID, 'sumo_choose_downloadable_products', true )
				) ) ;
				?>
			</p>
			<?php
		}
	}

	/**
	 * Save subscription product data.
	 *
	 * @param int $product_id The Product post ID
	 */
	public static function save_subscription_product_data( $product_id ) {
		if ( empty( $_POST[ 'woocommerce_meta_nonce' ] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST[ 'woocommerce_meta_nonce' ] ) ), 'woocommerce_save_data' ) ) {
			return ;
		}

		global $sitepress ;
		if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) && is_object( $sitepress ) ) {
			$trid         = $sitepress->get_element_trid( $product_id ) ;
			$translations = $sitepress->get_element_translations( $trid ) ;

			if ( ! empty( $translations ) ) {
				foreach ( $translations as $translation ) {
					self::save_meta( $translation->element_id, '', $_POST ) ;
				}
			} else {
				self::save_meta( $product_id, '', $_POST ) ;
			}
		} else {
			self::save_meta( $product_id, '', $_POST ) ;
		}
	}

	/**
	 * Save subscription variation product data.
	 *
	 * @param int $variation_id The Variation post ID
	 * @param int $loop
	 */
	public static function save_subscription_variation_data( $variation_id, $loop ) {
		check_ajax_referer( 'save-variations', 'security' ) ;

		global $sitepress ;
		if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) && is_object( $sitepress ) ) {
			$trid         = $sitepress->get_element_trid( $variation_id ) ;
			$translations = $sitepress->get_element_translations( $trid ) ;

			if ( ! empty( $translations ) ) {
				foreach ( $translations as $translation ) {
					self::save_meta( $translation->element_id, $loop, $_POST ) ;
				}
			} else {
				self::save_meta( $variation_id, $loop, $_POST ) ;
			}
		} else {
			self::save_meta( $variation_id, $loop, $_POST ) ;
		}
	}

	/**
	 * Save subscription product meta.
	 *
	 * @param int $product_id The Product post ID
	 * @param int $loop The Variation loop
	 */
	public static function save_meta( $product_id, $loop = '', $props = array() ) {
		foreach ( self::$subscription_fields as $field_name => $type ) {
			$meta_key         = "sumo_{$field_name}" ;
			$posted_meta_data = isset( $props[ "$meta_key" ] ) ? $props[ "$meta_key" ] : '' ;

			update_post_meta( $product_id, 'sumo_subscription_version', SUMO_SUBSCRIPTIONS_VERSION ) ;

			if ( is_numeric( $loop ) ) {
				if ( in_array( $type, array( 'checkbox', 'search' ) ) ) {
					delete_post_meta( $product_id, "$meta_key" ) ;
				}

				if ( isset( $posted_meta_data[ $loop ] ) ) {
					if ( 'price' === $type ) {
						$posted_meta_data[ $loop ] = wc_format_decimal( $posted_meta_data[ $loop ] ) ;
					} else if ( 'search' === $type ) {
						$posted_meta_data[ $loop ] = ! is_array( $posted_meta_data[ $loop ] ) ? array_filter( array_map( 'absint', explode( ',', $posted_meta_data ) ) ) : $posted_meta_data[ $loop ] ;
					}
					update_post_meta( $product_id, "$meta_key", wc_clean( $posted_meta_data[ $loop ] ) ) ;

					//backward compatible
					switch ( $meta_key ) {
						case 'sumo_susbcription_signusumoee_enable_disable':
							delete_post_meta( $product_id, 'sumo_susbcription_signup_fee_is_optional_for_user' ) ;
							break ;
						case 'sumo_susbcription_trial_enable_disable':
							delete_post_meta( $product_id, 'sumo_susbcription_trial_is_optional_for_user' ) ;
							break ;
						case 'sumo_xtra_time_to_charge_full_fee':
							delete_post_meta( $product_id, 'sumo_xtra_duration_to_charge_full_fee' ) ;
							break ;
					}
				}
			} else {
				if ( 'price' === $type ) {
					$posted_meta_data = wc_format_decimal( $posted_meta_data ) ;
				} else if ( 'search' === $type ) {
					$posted_meta_data = ! is_array( $posted_meta_data ) ? array_filter( array_map( 'absint', explode( ',', $posted_meta_data ) ) ) : $posted_meta_data ;
				}
				update_post_meta( $product_id, "$meta_key", $posted_meta_data ) ;

				//backward compatible
				switch ( $meta_key ) {
					case 'sumo_susbcription_signusumoee_enable_disable':
						delete_post_meta( $product_id, 'sumo_susbcription_signup_fee_is_optional_for_user' ) ;
						break ;
					case 'sumo_susbcription_trial_enable_disable':
						delete_post_meta( $product_id, 'sumo_susbcription_trial_is_optional_for_user' ) ;
						break ;
					case 'sumo_xtra_time_to_charge_full_fee':
						delete_post_meta( $product_id, 'sumo_xtra_duration_to_charge_full_fee' ) ;
						break ;
				}
			}
		}

		$products      = isset( $props[ 'sumo_subscription_product_ids' ] ) ? wc_clean( wp_unslash( $props[ 'sumo_subscription_product_ids' ] ) ) : '' ;
		$email_actions = isset( $props[ 'sumosubs_send_payment_reminder_email' ] ) ? wc_clean( wp_unslash( $props[ 'sumosubs_send_payment_reminder_email' ] ) ) : '' ;

		if ( is_array( $products ) ) {
			foreach ( $products as $id ) {
				update_post_meta( $id, 'sumosubs_send_payment_reminder_email', array(
					'auto'   => 'no',
					'manual' => 'no',
				) ) ;

				if ( isset( $email_actions[ $id ] ) ) {
					update_post_meta( $id, 'sumosubs_send_payment_reminder_email', wp_parse_args( $email_actions[ $id ], array(
						'auto'   => 'no',
						'manual' => 'no',
					) ) ) ;
				}
			}
		}
	}

}

SUMOSubscriptions_Product_Settings::init() ;
