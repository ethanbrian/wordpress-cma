<?php 
global $wp_roles;

if ( ! isset( $wp_roles ) ) { 
	$wp_roles = new WP_Roles();  
}

$roles = $wp_roles->roles;

$shipping_methods = WC()->shipping->get_shipping_methods();

$payment_gateways = WC()->payment_gateways->get_available_payment_gateways();



?>
<div class="panel-group panel panel-default checkoutfield pcfme_list_item" style="display:none;">
	<div class="panel-heading">

		<table class="heading-table">
			<tr>
				<td width="40%">
					<span class="glyphicon glyphicon-trash pcfme_trash_icon"></span>
					<a class="accordion-toggle pcfme_edit_icon_a" data-toggle="collapse" data-parent="#accordion" href="">
						<span class="glyphicon glyphicon-edit pcfme_edit_icon"></span>
					</a>
				</td>

				<td width="30%">
					<label  class="new-field-label"></label>
					
				</td>
				<td width="30%">
					
				</td>
				
				
			</tr>
		</table>

	</div>
	<div id="" class="panel-collapse collapse">
		<table class="table"> 
			
			
			
			<tr>
				<td width="25%"><label><?php echo esc_html__('Field Type','pcfme'); ?></label></td>
				<td width="75%">
					<select class="checkout_field_type_new" name="" >
						<option value="text"  ><?php echo esc_html__('Text','pcfme'); ?></option>
						<option value="heading"  ><?php echo esc_html__('Heading','pcfme'); ?></option>
						<option value="paragraph"  ><?php echo esc_html__('Paragraph','pcfme'); ?></option>
						<option value="email"  ><?php echo esc_html__('Email','pcfme'); ?></option>
						<option value="tel"  ><?php echo esc_html__('Telephone Number','pcfme'); ?></option>
						<option value="number"  ><?php echo esc_html__('Number','pcfme'); ?></option>
						<option value="password" ><?php echo esc_html__('Password','pcfme'); ?></option>
						<option value="textarea" ><?php echo esc_html__('Textarea','pcfme'); ?></option>
						<option value="checkbox" ><?php echo esc_html__('Checkbox','pcfme'); ?></option>
						<option value="pcfmeselect" ><?php echo esc_html__('Select','pcfme'); ?></option>
						<option value="multiselect"><?php echo esc_html__('Multi Select','pcfme'); ?></option>
						<option value="radio" ><?php echo esc_html__('Radio Select','pcfme'); ?></option>
						<option value="datepicker" ><?php echo esc_html__('Date Picker','pcfme'); ?></option>
						<option value="datetimepicker" ><?php echo esc_html__('Date Time Picker','pcfme'); ?></option>
						<option value="timepicker" ><?php echo esc_html__('Time Picker','pcfme'); ?></option>
						<option value="daterangepicker" ><?php echo esc_html__('Date Range Picker','pcfme'); ?></option>
						<option value="datetimerangepicker" ><?php echo esc_html__('Date Time Range Picker','pcfme'); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td width="25%"><label><?php  echo esc_html__('Label','pcfme'); ?></label></td>
				<td width="75%"><input type="text" class="checkout_field_label" name="" value="" size="100"></td>
			</tr>
			
			
			
			<tr>
				<td width="25%"><label><?php echo esc_html__('Class','pcfme'); ?></label></td>
				<td width="75%">
					<select class="checkout_field_width_new" name="">
						
						<option value="form-row-wide" ><?php echo esc_html__('Full Width','pcfme'); ?></option>
						<option value="form-row-first" ><?php echo esc_html__('First Half','pcfme'); ?></option>
						<option value="form-row-last" ><?php echo esc_html__('Second Half','pcfme'); ?></option>
						
						
						
					</select>
				</td>
			</tr>
			
			
			<tr>
				<td width="25%"><label ><?php  echo esc_html__('Required','pcfme'); ?></label></td>
				<td width="75%"><input class="checkout_field_required" type="checkbox" name=""  value="1"></td>
			</tr>
			
			
			<tr>
				<td width="25%"><label><?php  echo esc_html__('Clearfix','pcfme'); ?></label></td>
				<td width="75%"><input class="checkout_field_clear" type="checkbox" name="" value="1"></td>
			</tr>
			
			
			<tr>
				<td width="25%"><label><?php  echo esc_html__('Placeholder ','pcfme'); ?></label></td>
				<td width="75%"><input type="text" class="checkout_field_placeholder" name="" value="" size="35"></td>
			</tr>
			
			
			<tr class="add-field-extraclass" style="">
				<td width="25%">
					<label><?php echo esc_html__('Extra Class','pcfme'); ?></label>
				</td>
				<td width="75%">
					<input type="text" class="pcfme_checkout_field_extraclass_new" name="" value="" size="35">
					<?php echo esc_html__('Use space key or comma to separate class','pcfme'); ?>
				</td>
			</tr>
			
			<tr class="add-field-options" style="">
				<td width="25%">
					<label><?php echo esc_html__('Options','pcfme'); ?></label>
				</td>
				<td width="75%">
					<input type="text" class="pcfme_checkout_field_option_values_new" name="" value="" size="35">
					<ul>
						<li>
							<?php echo esc_html__('Use pipe key or comma to separate option.If you are using it for field specific conditional visibility replace space with underscore ( _ ) . For Example value for "Option 2" will be Option_2','pcfme','pcfme'); ?>
						</li>
					</ul>
				</td>
			</tr>
			
			
			
			<tr>
				<td width="25%"><label><?php  echo esc_html__('Visibility','pcfme'); ?></label></td>
				<td width="75%">
					<select class="checkout_field_visibility_new" name="" >
						<option value="always-visible"><?php echo esc_html__('Always Visibile','pcfme'); ?></option>
						<option value="product-specific"><?php echo esc_html__('Conditional - Product Specific','pcfme'); ?></option>
						<option value="category-specific"><?php echo esc_html__('Conditional - Category Specific','pcfme'); ?></option>
						<option value="field-specific"><?php echo esc_html__('Conditional - Field Specific','pcfme'); ?></option>
						<option value="role-specific"><?php echo esc_html__('Conditional - Role Specific','pcfme'); ?></option>
						<option value="total-quantity"><?php echo esc_html__('Conditional - Based on Total Cart Quantity','pcfme'); ?></option>
						<option value="cart-quantity-specific"><?php echo esc_html__('Conditional - Based on Cart Quantity of Specific Product','pcfme'); ?></option>
					 <!--
					 <option value="shipping-specific"><?php echo esc_html__('Conditional - Shipping Method Specific','pcfme'); ?></option>
					 <option value="payment-specific"><?php echo esc_html__('Conditional - Payment Gateway Specific','pcfme'); ?></option>
					-->

				</select>
			</td>
		</tr>
		
		<tr class="checkout_field_products_tr" style="display:none;">
			<td width="25%">
				<label><?php echo esc_html__('Select Products','pcfme'); ?></label>
			</td>
			<td width="75%">
				<select class="checkout_field_products_new" data-placeholder="<?php echo esc_html__('Choose Products','pcfme'); ?>" name="" multiple  style="width:600px">
					
					<option value="1">#1- sample post</option>
					
				</select>
			</td>
		</tr>
		<tr class="checkout_field_category_tr" style="display:none;" >
			<td width="25%">
				<label for="notice_category"><?php echo esc_html__('Select Categories','pcfme'); ?></label>
			</td>
			<td width="75%">
				<select class="checkout_field_category_new" data-placeholder="<?php echo esc_html__('Choose Categories','pcfme'); ?>" name=""  multiple style="width:600px">
					<?php foreach ($categories as $category) { ?>
						<option value="<?php echo $category->term_id; ?>">#<?php echo $category->term_id; ?>- <?php echo $category->name; ?></option>
					<?php } ?>
				</select>
			</td>
		</tr>
		<tr class="checkout_field_role_tr" style="display:none;" >
			<td width="25%">
				<label><?php echo esc_html__('Select Roles','pcfme'); ?></label>
			</td>
			<td width="75%">
				<select class="checkout_field_role_new" data-placeholder="<?php echo esc_html__('Choose Roles','pcfme'); ?>" name=""  multiple style="width:600px">
					<?php foreach ($roles as $key => $role) { ?>
						<option value="<?php echo $key; ?>"><?php echo $role['name']; ?></option>
					<?php } ?>
				</select>
			</td>
		</tr>

		<tr class="checkout_field_shipping_tr" style="display:none;" >
			<td width="25%">
				<label><?php echo esc_html__('Choose Shipping Method','pcfme'); ?></label>
			</td>
			<td width="75%">
				<select class="checkout_field_shipping_showhide_new" name="" style="width:100px">
					<option value="show"><?php echo esc_html__('show','pcfme'); ?></option>
					<option value="hide"><?php echo esc_html__('hide','pcfme'); ?></option>
				</select>&emsp;
				<span><?php echo esc_html__('by','pcfme'); ?></span>&emsp;
				<select class="checkout_field_shipping_new" data-placeholder="<?php echo esc_html__('Choose Shipping Method','pcfme'); ?>" name="" style="width:600px">
					<?php foreach ($shipping_methods as $key => $value) { ?>
						<option value="<?php echo $key; ?>"><?php echo $key; ?></option>
					<?php } ?>
				</select>
			</td>
		</tr>


		<tr class="checkout_field_payment_tr" style="display:none;" >
			<td width="25%">
				<label><?php echo esc_html__('Choose Payment Gateway','pcfme'); ?></label>
			</td>
			<td width="75%">
				<select class="checkout_field_payment_showhide_new" name="" style="width:100px">
					<option value="show"><?php echo esc_html__('show','pcfme'); ?></option>
					<option value="hide"><?php echo esc_html__('hide','pcfme'); ?></option>
				</select>&emsp;
				<span><?php echo esc_html__('by','pcfme'); ?></span>&emsp;
				<select class="checkout_field_payment_new" data-placeholder="<?php echo esc_html__('Choose Payment Gateway','pcfme'); ?>" name="" style="width:600px">
					<?php foreach ($payment_gateways as $key => $value) { ?>
						<option value="<?php echo $key; ?>"><?php echo $key; ?></option>
					<?php } ?>
				</select>
			</td>
		</tr>

		<tr class="checkout_field_total_quantity_tr" style="display:none;" >
			<td width="25%">
				<label><?php echo esc_html__('Total Cart Quantity','pcfme'); ?></label>
			</td>
			<td width="75%">
				<input type="number" placeholder="<?php echo esc_html__('Choose Quantity','pcfme'); ?>" class="checkout_field_total_quantity_new" value=""/>
			</td>
		</tr>


		<tr class="checkout_field_cart_quantity_specific_tr" style="display:none;" >
			<td width="25%">
				<label><?php echo esc_html__('Product and Quantity','pcfme'); ?></label>
			</td>
			<td width="75%">
				<select class="checkout_field_quantity_specific_product_new" data-placeholder="<?php echo esc_html__('Choose Product','pcfme'); ?>" style="width:600px">
					
				</select>
				<input type="number" class="checkout_field_cart_quantity_specific_new" placeholder="<?php echo esc_html__('Choose Quantity','pcfme'); ?>" value=""/>
			</td>
		</tr>
		<tr class="checkout_field_conditional_tr" style="display:none;" >
			<td width="25%">
				<label for="notice_category"><?php echo esc_html__('Set Rule','pcfme'); ?></label>
			</td>
			<td width="75%">
				
				<div class="conditional_fields_div_wrapper">
					
				</div>
				<ul>
					<li>
						<?php echo esc_html__('If parent field is checkbox leave blank for equal to field.','pcfme'); ?>
						
					</li>
					<li>
						<?php echo esc_html__('If parent field is radio/select and its value has space, replace space with underscore ( _ ) for example equal to field for "Option 3" will be Option_3.','pcfme'); ?>
						
					</li>
					<li>
						<?php echo esc_html__('You can also use text/textarea field as parent field.','pcfme'); ?>
					</li>
				</ul>
				<input type="button" keyno="" mnindex="1" class="btn button-primary add-condition-button" value="<?php echo esc_html__('Add Condition','pcfme'); ?>">
				
			</td>
		</tr>
		<?php if ($slug != 'pcfme_additional_settings') { ?>
			<tr>
				<td width="25%"><label><?php  echo esc_html__('Validate','pcfme'); ?></label></td>
				<td width="75%">
					<select name="" class="checkout_field_validate_new" multiple>
						<option value="state" ><?php echo esc_html__('state','pcfme'); ?></option>
						<option value="postcode" ><?php echo esc_html__('postcode','pcfme'); ?></option>
						<option value="email" ><?php echo esc_html__('email','pcfme'); ?></option>
						<option value="phone" ><?php echo esc_html__('phone','pcfme'); ?></option>
					</select>
				</td>
			<?php } ?>
		</tr>
		<tr>
			<td width="25%"><label for="<?php echo $key; ?>_clear"><?php  echo esc_html__('Chose Options','pcfme'); ?></label></td>
			<td  width="75%">
				<table>
					
					
					<tr class="disable_datepicker_tr" style="display:none;">
						<td><input class="checkout_field_disable_past_dates" type="checkbox" name=""  value="1"></td>
						<td><label ><?php  echo esc_html__('Disable Past Date Selection In Datepicker','pcfme'); ?></label></td>
					</tr>
					
					<tr>
						<td><input class="checkout_field_orderedition" type="checkbox" name=""  value="1"></td>
						<td><label ><?php  echo esc_html__('Show field detail along with orders','pcfme'); ?></label></td>
					</tr>
					
					
					
					<tr>
						<td><input class="checkout_field_emailfields" type="checkbox" name=""  value="1"></td>
						<td><label ><?php  echo esc_html__('Show field detail on woocommerce order emails','pcfme'); ?></label></td>
					</tr>
					
					<tr>
						<td><input class="checkout_field_pdfinvoice" type="checkbox" name=""  value="1"></td>
						<td><label ><?php  echo esc_html__('Show field detail on WooCommerce PDF Invoices & Packing Slips Invoice','pcfme'); ?></label></td>
					</tr>
					
					<tr>
						<td><input class="checkout_field_editaddress" type="checkbox" name=""  value="1"></td>
						<td><label ><?php  echo esc_html__('Add this field to myaccount/edit address page','pcfme'); ?></label></td>
					</tr>
					
				</table>
			</td>
		</tr>
		
		
	</table>
</div>
</div>