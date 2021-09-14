<?php

/**
 * Order Subscription Settings.
 * 
 * @class SUMOSubscriptions_Order_Subscription_Settings
 */
class SUMOSubscriptions_Order_Subscription_Settings extends SUMO_Abstract_Subscription_Settings {

	/**
	 * SUMOSubscriptions_Order_Subscription_Settings constructor.
	 */
	public function __construct() {
		$this->id            = 'order_subscription' ;
		$this->label         = __( 'Order Subscription', 'sumosubscriptions' ) ;
		$this->custom_fields = array(
			'get_duration_value_user_can_select',
			'get_include_product_selector',
			'get_exclude_product_selector',
			'get_include_product_category_selector',
			'get_exclude_product_category_selector', ) ;
		$this->settings      = $this->get_settings() ;
		$this->init() ;
	}

	/**
	 * Get settings array.
	 *
	 * @return array
	 */
	public function get_settings() {
		global $current_section ;

		return apply_filters( 'sumosubscriptions_get_' . $this->id . '_settings', array(
			array(
				'name' => __( 'Order Subscription Settings', 'sumosubscriptions' ),
				'type' => 'title',
				'id'   => 'sumo_order_subsc_settings'
			),
			array(
				'name'     => __( 'Enable Order Subscription as a Single Subscription', 'sumosubscriptions' ),
				'id'       => 'sumo_order_subsc_check_option',
				'newids'   => 'sumo_order_subsc_check_option',
				'type'     => 'checkbox',
				'std'      => 'no',
				'default'  => 'no',
				'desc_tip' => __( "When enabled, the 'order subscription' checkbox will be visible to the user on the checkout page for any of the products in the shop(excluding SUMO Subscriptions and SUMO Memberships plan access products). If the user places the order with checkbox enabled then entire order will be considered as the single subscription and payment will be charged accordingly.", 'sumosubscriptions' ),
			),
			array(
				'name'     => __( 'Order Subscription Checkbox Initial Value', 'sumosubscriptions' ),
				'id'       => 'sumo_order_subsc_checkout_option',
				'newids'   => 'sumo_order_subsc_checkout_option',
				'type'     => 'checkbox',
				'std'      => 'no',
				'default'  => 'no',
				'desc_tip' => __( "When enabled, the 'order subscription' checkbox will be enabled by default. If the user doesn't want to purchase the order as a subscription, they can uncheck the 'order subscription' checkbox and place the order.", 'sumosubscriptions' ),
			),
			array(
				'name'    => __( 'Select Products/Categories', 'sumosubscriptions' ),
				'id'      => 'sumo_order_subsc_get_product_selected_type',
				'newids'  => 'sumo_order_subsc_get_product_selected_type',
				'type'    => 'select',
				'std'     => 'all-products',
				'default' => 'all-products',
				'options' => array(
					'all-products'        => __( 'All Products', 'sumosubscriptions' ),
					'included-products'   => __( 'Include Products', 'sumosubscriptions' ),
					'excluded-products'   => __( 'Exclude Products', 'sumosubscriptions' ),
					'included-categories' => __( 'Include Categories', 'sumosubscriptions' ),
					'excluded-categories' => __( 'Exclude Categories', 'sumosubscriptions' ),
				),
			),
			array(
				'type' => $this->get_custom_field_type( 'get_include_product_selector' ),
			),
			array(
				'type' => $this->get_custom_field_type( 'get_exclude_product_selector' ),
			),
			array(
				'type' => $this->get_custom_field_type( 'get_include_product_category_selector' )
			),
			array(
				'type' => $this->get_custom_field_type( 'get_exclude_product_category_selector' )
			),
			array(
				'name'     => __( 'Sign up Fee', 'sumosubscriptions' ),
				'id'       => 'sumo_order_subsc_has_signup',
				'newids'   => 'sumo_order_subsc_has_signup',
				'type'     => 'checkbox',
				'std'      => 'no',
				'default'  => 'no',
				'desc_tip' => __( 'When enabled, Signup fee will be applied for the order subscription', 'sumosubscriptions' ),
			),
			array(
				/* translators: 1: currency symbol */
				'name'    => sprintf( __( 'Sign up Value(%s)', 'sumosubscriptions' ), get_woocommerce_currency_symbol() ),
				'id'      => 'sumo_order_subsc_signup_fee',
				'newids'  => 'sumo_order_subsc_signup_fee',
				'type'    => 'text',
				'std'     => '',
				'default' => '',
			),
			array(
				'name'     => __( 'Order Subscription Duration is Chosen by', 'sumosubscriptions' ),
				'id'       => 'sumo_order_subsc_chosen_by_option',
				'newids'   => 'sumo_order_subsc_chosen_by_option',
				'type'     => 'select',
				'std'      => 'admin',
				'default'  => 'admin',
				'options'  => array(
					'admin' => __( 'Admin', 'sumosubscriptions' ),
					'user'  => __( 'User', 'sumosubscriptions' ),
				),
				'desc_tip' => __( "If 'Admin' is selected Subscription Duration, Subscription Duration Value and Recurring Cycle will be set by the site admin for order subscription. If 'User' is selected Subscription Duration, Subscription Duration Value and Recurring Cycle will be displayed in the checkout page for user selection.", 'sumosubscriptions' ),
			),
			array(
				'name'     => __( 'Subscription Duration', 'sumosubscriptions' ),
				'id'       => 'sumo_order_subsc_duration_option',
				'newids'   => 'sumo_order_subsc_duration_option',
				'type'     => 'select',
				'std'      => 'D',
				'default'  => 'D',
				'options'  => sumosubs_get_duration_period_selector(),
				'desc_tip' => __( 'Select the duration of each order subscription', 'sumosubscriptions' ),
			),
			array(
				'name'    => __( 'Subscription Duration Value', 'sumosubscriptions' ),
				'id'      => 'sumo_order_subsc_duration_value_option',
				'newids'  => 'sumo_order_subsc_duration_value_option',
				'type'    => 'select',
				'std'     => '1',
				'default' => '1',
				'options' => sumo_get_subscription_duration_options( get_option( 'sumo_order_subsc_duration_option', 'D' ) ),
			),
			array(
				'name'     => __( 'Recurring Cycle', 'sumosubscriptions' ),
				'id'       => 'sumo_order_subsc_recurring_option',
				'newids'   => 'sumo_order_subsc_recurring_option',
				'type'     => 'select',
				'std'      => '0',
				'default'  => '0',
				'options'  => sumo_get_subscription_recurring_options(),
				'desc_tip' => __( 'Choose the recurring duration for an order subscription. If indefinite is chosen the subscription will not expire.', 'sumosubscriptions' ),
			),
			array(
				'name'    => __( 'Select Durations to Display', 'sumosubscriptions' ),
				'id'      => 'sumo_get_order_subsc_duration_period_selector_for_users',
				'newids'  => 'sumo_get_order_subsc_duration_period_selector_for_users',
				'class'   => 'wc-enhanced-select',
				'type'    => 'multiselect',
				'std'     => array( 'D', 'W', 'M', 'Y', ),
				'default' => array( 'D', 'W', 'M', 'Y', ),
				'options' => sumosubs_get_duration_period_selector(),
			),
			array(
				'type' => $this->get_custom_field_type( 'get_duration_value_user_can_select' )
			),
			array(
				'name'     => __( 'Enable Recurring Cycle', 'sumosubscriptions' ),
				'id'       => 'sumo_order_subsc_enable_recurring_cycle_option_for_users',
				'newids'   => 'sumo_order_subsc_enable_recurring_cycle_option_for_users',
				'type'     => 'checkbox',
				'std'      => 'yes',
				'default'  => 'yes',
				'desc_tip' => __( 'If disabled, Recurring Cycle will be considered as "Indefinite"', 'sumosubscriptions' ),
			),
			array(
				'name'    => __( 'Minimum Recurring Cycle User can Select', 'sumosubscriptions' ),
				'id'      => 'sumo_order_subsc_min_recurring_cycle_user_can_select',
				'newids'  => 'sumo_order_subsc_min_recurring_cycle_user_can_select',
				'type'    => 'select',
				'std'     => '1',
				'default' => '1',
				'options' => sumo_get_subscription_recurring_options( false ),
			),
			array(
				'name'    => __( 'Maximum Recurring Cycle User can Select', 'sumosubscriptions' ),
				'id'      => 'sumo_order_subsc_max_recurring_cycle_user_can_select',
				'newids'  => 'sumo_order_subsc_max_recurring_cycle_user_can_select',
				'type'    => 'select',
				'std'     => '0',
				'default' => '0',
				'options' => sumo_get_subscription_recurring_options( 'first' ),
			),
			array(
				'name'              => __( 'Minimum Order Total to Display Order Subscription', 'sumosubscriptions' ),
				'id'                => 'sumo_min_order_total_to_display_order_subscription',
				'newids'            => 'sumo_min_order_total_to_display_order_subscription',
				'type'              => 'number',
				'std'               => '',
				'default'           => '',
				'custom_attributes' => array(
					'step' => '0.01',
				),
			),
			array(
				'name'    => __( 'Display Order Subscription in Cart Page', 'sumosubscriptions' ),
				'id'      => 'sumo_display_order_subscription_in_cart',
				'newids'  => 'sumo_display_order_subscription_in_cart',
				'type'    => 'checkbox',
				'std'     => 'no',
				'default' => 'no',
			),
			array(
				'name'    => __( 'Order Subscription Position in Checkout Page', 'sumosubscriptions' ),
				'id'      => 'sumo_order_subsc_form_position',
				'newids'  => 'sumo_order_subsc_form_position',
				'type'    => 'select',
				'std'     => 'checkout_order_review',
				'default' => 'checkout_order_review',
				'options' => array(
					'checkout_order_review'           => 'Woocommerce Checkout Order Review',
					'checkout_after_customer_details' => 'Woocommerce Checkout After Customer Details',
					'before_checkout_form'            => 'Woocommerce Before Checkout Form',
					'checkout_before_order_review'    => 'Woocommerce Checkout Before Order Review',
				),
				'desc'    => __( 'Some themes do not support all the positions, if the positions is not supported then it might result in jquery conflict', 'sumosubscriptions' ),
			),
			array(
				'name'    => __( 'Order Subscription Checkbox Label', 'sumosubscriptions' ),
				'id'      => 'sumo_order_subsc_checkout_label_option',
				'newids'  => 'sumo_order_subsc_checkout_label_option',
				'type'    => 'textarea',
				'std'     => __( 'Enable Order Subscription', 'sumosubscriptions' ),
				'default' => __( 'Enable Order Subscription', 'sumosubscriptions' ),
			),
			array(
				'name'    => __( 'Customize "Subscription Duration" Label in Checkout Page for Order Subscription', 'sumosubscriptions' ),
				'id'      => 'sumo_order_subsc_duration_checkout_label_option',
				'newids'  => 'sumo_order_subsc_duration_checkout_label_option',
				'type'    => 'textarea',
				'std'     => __( 'Subscription Duration', 'sumosubscriptions' ),
				'default' => __( 'Subscription Duration', 'sumosubscriptions' ),
			),
			array(
				'name'    => __( 'Customize "Subscription Duration Value" Label in Checkout Page for Order Subscription', 'sumosubscriptions' ),
				'id'      => 'sumo_order_subsc_duration_value_checkout_label_option',
				'newids'  => 'sumo_order_subsc_duration_value_checkout_label_option',
				'type'    => 'textarea',
				'std'     => __( 'Subscription Duration Value', 'sumosubscriptions' ),
				'default' => __( 'Subscription Duration Value', 'sumosubscriptions' ),
			),
			array(
				'name'    => __( 'Customize "Recurring Cycle" Label in Checkout Page for Order Subscription', 'sumosubscriptions' ),
				'id'      => 'sumo_order_subsc_recurring_checkout_label_option',
				'newids'  => 'sumo_order_subsc_recurring_checkout_label_option',
				'type'    => 'textarea',
				'std'     => __( 'Recurring Cycle', 'sumosubscriptions' ),
				'default' => __( 'Recurring Cycle', 'sumosubscriptions' ),
			),
			array(
				'name'    => __( 'Custom CSS', 'sumosubscriptions' ),
				'id'      => 'sumo_order_subsc_custom_css',
				'newids'  => 'sumo_order_subsc_custom_css',
				'type'    => 'textarea',
				'css'     => 'height:200px;',
				'std'     => '',
				'default' => '',
			),
			array( 'type' => 'sectionend', 'id' => 'sumo_order_subsc_settings' ),
			array(
				'name' => __( 'Troubleshoot Settings', 'sumosubscriptions' ),
				'type' => 'title',
				'id'   => 'sumo_order_subsc_troubleshoot_settings'
			),
			array(
				'name'    => __( 'Load Ajax Synchronously', 'sumosubscriptions' ),
				'id'      => 'sumo_sync_ajax_for_order_subscription',
				'newids'  => 'sumo_sync_ajax_for_order_subscription',
				'type'    => 'checkbox',
				'std'     => 'no',
				'default' => 'no',
			),
			array( 'type' => 'sectionend', 'id' => 'sumo_order_subsc_troubleshoot_settings' ),
				) ) ;
	}

	/**
	 * Save the custom options once.
	 */
	public function custom_types_add_options( $posted = null ) {
		add_option( 'sumo_order_subsc_min_subsc_duration_value_user_can_select', array(
			'D' => '1',
			'W' => '1',
			'M' => '1',
			'Y' => '1',
		) ) ;
		add_option( 'sumo_order_subsc_max_subsc_duration_value_user_can_select', array(
			'D' => '90',
			'W' => '52',
			'M' => '24',
			'Y' => '10',
		) ) ;
		add_option( 'sumo_order_subsc_get_included_products', array() ) ;
		add_option( 'sumo_order_subsc_get_excluded_products', array() ) ;
		add_option( 'sumo_order_subsc_get_included_categories', array() ) ;
		add_option( 'sumo_order_subsc_get_excluded_categories', array() ) ;
	}

	/**
	 * Delete the custom options.
	 */
	public function custom_types_delete_options( $posted = null ) {
		delete_option( 'sumo_order_subsc_min_subsc_duration_value_user_can_select' ) ;
		delete_option( 'sumo_order_subsc_max_subsc_duration_value_user_can_select' ) ;
		delete_option( 'sumo_order_subsc_get_included_products' ) ;
		delete_option( 'sumo_order_subsc_get_excluded_products' ) ;
		delete_option( 'sumo_order_subsc_get_included_categories' ) ;
		delete_option( 'sumo_order_subsc_get_excluded_categories' ) ;
	}

	/**
	 * Save custom settings.
	 */
	public function custom_types_save( $posted ) {
		if ( isset( $posted[ 'min_subsc_duration_value' ] ) ) {
			update_option( 'sumo_order_subsc_min_subsc_duration_value_user_can_select', array_map( 'wc_clean', $posted[ 'min_subsc_duration_value' ] ) ) ;
		}
		if ( isset( $posted[ 'max_subsc_duration_value' ] ) ) {
			update_option( 'sumo_order_subsc_max_subsc_duration_value_user_can_select', array_map( 'wc_clean', $posted[ 'max_subsc_duration_value' ] ) ) ;
		}
		if ( isset( $posted[ 'get_included_products' ] ) ) {
			update_option( 'sumo_order_subsc_get_included_products', array_map( 'wc_clean', $posted[ 'get_included_products' ] ) ) ;
		}
		if ( isset( $posted[ 'get_excluded_products' ] ) ) {
			update_option( 'sumo_order_subsc_get_excluded_products', array_map( 'wc_clean', $posted[ 'get_excluded_products' ] ) ) ;
		}
		if ( isset( $posted[ 'get_included_categories' ] ) ) {
			update_option( 'sumo_order_subsc_get_included_categories', array_map( 'wc_clean', $posted[ 'get_included_categories' ] ) ) ;
		}
		if ( isset( $posted[ 'get_excluded_categories' ] ) ) {
			update_option( 'sumo_order_subsc_get_excluded_categories', array_map( 'wc_clean', $posted[ 'get_excluded_categories' ] ) ) ;
		}
	}

	/**
	 * Custom type field.
	 */
	public function get_duration_value_user_can_select() {
		$min_subsc_duration_value = get_option( 'sumo_order_subsc_min_subsc_duration_value_user_can_select', array() ) ;
		$max_subsc_duration_value = get_option( 'sumo_order_subsc_max_subsc_duration_value_user_can_select', array() ) ;
		$max                      = array(
			'D' => '90',
			'W' => '52',
			'M' => '24',
			'Y' => '10',
				) ;
		?>
		<tr valign="top">
			<th scope="row" class="titledesc"><?php esc_html_e( 'Subscription Duration Value User can Select', 'sumosubscriptions' ) ; ?></th>
			<td class="forminp">
				<table id="sumo_order_subsc_duration_value_user_can_select">
					<?php foreach ( sumosubs_get_duration_period_selector() as $duration_period => $duration_period_label ) : ?>
						<tr>
							<th><?php /* translators: 1: duration period label */ printf( esc_html__( '%s between', 'sumosubscriptions' ), esc_html( $duration_period_label ) ) ; ?></th>
							<td>
								<input id="min_subsc_duration_value_<?php echo esc_attr( $duration_period ) ; ?>" name="min_subsc_duration_value[<?php echo esc_attr( $duration_period ) ; ?>]" type="number" min="1" max="<?php echo esc_attr( $max[ $duration_period ] ) ; ?>" value="<?php echo esc_attr( $min_subsc_duration_value[ $duration_period ] ) ; ?>"></input>
								<input id="max_subsc_duration_value_<?php echo esc_attr( $duration_period ) ; ?>" name="max_subsc_duration_value[<?php echo esc_attr( $duration_period ) ; ?>]" type="number" min="1" max="<?php echo esc_attr( $max[ $duration_period ] ) ; ?>" value="<?php echo esc_attr( $max_subsc_duration_value[ $duration_period ] ) ; ?>"></input>
							</td>
						</tr>   
					<?php endforeach ; ?>
				</table>
			</td>
		</tr>
		<?php
	}

	/**
	 * Custom type field.
	 */
	public function get_include_product_selector() {
		sumosubs_wc_search_field( array(
			'class'       => 'wc-product-search',
			'id'          => 'get_included_products',
			'type'        => 'product',
			'action'      => 'woocommerce_json_search_products_and_variations',
			'title'       => __( 'Select Product(s) to Include', 'sumosubscriptions' ),
			'placeholder' => __( 'Search for a product&hellip;', 'sumosubscriptions' ),
			'options'     => get_option( 'sumo_order_subsc_get_included_products', array() ),
		) ) ;
	}

	/**
	 * Custom type field.
	 */
	public function get_exclude_product_selector() {
		sumosubs_wc_search_field( array(
			'class'       => 'wc-product-search',
			'id'          => 'get_excluded_products',
			'type'        => 'product',
			'action'      => 'woocommerce_json_search_products_and_variations',
			'title'       => __( 'Select Product(s) to Exclude', 'sumosubscriptions' ),
			'placeholder' => __( 'Search for a product&hellip;', 'sumosubscriptions' ),
			'options'     => get_option( 'sumo_order_subsc_get_excluded_products', array() ),
		) ) ;
	}

	/**
	 * Custom type field.
	 */
	public function get_include_product_category_selector() {
		?>
		<tr>
			<th>
				<?php esc_html_e( 'Select Categorie(s) to Include', 'sumosubscriptions' ) ; ?>
			</th>
			<td> 
				<?php
				sumosubs_wc_enhanced_select_field( array(
					'id'       => 'get_included_categories',
					'selected' => get_option( 'sumo_order_subsc_get_included_categories', array() ),
					'options'  => sumosubs_category_list()
				) ) ;
				?>
			</td>
		</tr>
		<?php
	}

	/**
	 * Custom type field.
	 */
	public function get_exclude_product_category_selector() {
		?>
		<tr>
			<th>
				<?php esc_html_e( 'Select Categorie(s) to Exclude', 'sumosubscriptions' ) ; ?>
			</th>
			<td>       
				<?php
				sumosubs_wc_enhanced_select_field( array(
					'id'       => 'get_excluded_categories',
					'selected' => get_option( 'sumo_order_subsc_get_excluded_categories', array() ),
					'options'  => sumosubs_category_list()
				) ) ;
				?>
			</td>
		</tr>
		<?php
	}

}

return new SUMOSubscriptions_Order_Subscription_Settings() ;
