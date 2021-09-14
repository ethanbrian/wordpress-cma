<?php

if ( ! function_exists('libero_mikado_woocommerce_options_map') ) {

	/**
	 * Add Woocommerce options page
	 */
	function libero_mikado_woocommerce_options_map() {

		libero_mikado_add_admin_page(
			array(
				'slug' => '_woocommerce_page',
				'title' => esc_html__( 'Woocommerce', 'libero' ),
				'icon' => 'icon_cart_alt'
			)
		);

		/**
		 * Product List Settings
		 */
		$panel_product_list = libero_mikado_add_admin_panel(
			array(
				'page' => '_woocommerce_page',
				'name' => 'panel_product_list',
				'title' => esc_html__( 'Product List', 'libero' )
			)
		);

		libero_mikado_add_admin_field(array(
			'name'        	=> 'mkd_woo_products_list_full_width',
			'type'        	=> 'yesno',
			'label' => esc_html__( 'Enable Full Width Template', 'libero' ),
			'default_value'	=> 'no',
			'description' => esc_html__( 'Enabling this option will enable full width template for shop page', 'libero' ),
			'parent'      	=> $panel_product_list,
		));

		libero_mikado_add_admin_field(array(
			'name'        	=> 'mkd_woo_product_list_columns',
			'type'        	=> 'select',
			'label' => esc_html__( 'Product List Columns', 'libero' ),
			'default_value'	=> 'mkd-woocommerce-columns-3',
			'description' => esc_html__( 'Choose number of columns for product listing and related products on single product', 'libero' ),
			'options'		=> array(
				'mkd-woocommerce-columns-3' => esc_html__('3 Columns (2 with sidebar)', 'libero' ),
				'mkd-woocommerce-columns-4' => esc_html__('4 Columns (3 with sidebar)', 'libero' )
			),
			'parent'      	=> $panel_product_list,
		));

		libero_mikado_add_admin_field(array(
			'name'        	=> 'mkd_woo_products_per_page',
			'type'        	=> 'text',
			'label' => esc_html__( 'Number of products per page', 'libero' ),
			'default_value'	=> '',
			'description' => esc_html__( 'Set number of products on shop page', 'libero' ),
			'parent'      	=> $panel_product_list,
			'args' 			=> array(
				'col_width' => 3
			)
		));

		libero_mikado_add_admin_field(array(
			'name'        	=> 'mkd_products_list_title_tag',
			'type'        	=> 'select',
			'label' => esc_html__( 'Products Title Tag', 'libero' ),
			'default_value'	=> '',
			'description' 	=> '',
			'options'		=> array(
				'' => esc_html__('Default', 'libero' ),
				'h1' => 'h1',
				'h2' => 'h2',
				'h3' => 'h3',
				'h4' => 'h4',
				'h5' => 'h5',
				'h6' => 'h6',
			),
			'parent'      	=> $panel_product_list,
		));

	}

	add_action( 'libero_mikado_options_map', 'libero_mikado_woocommerce_options_map',16);

}