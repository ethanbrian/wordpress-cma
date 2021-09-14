<?php

if ( ! function_exists('libero_mikado_error_404_options_map') ) {

	function libero_mikado_error_404_options_map() {

		libero_mikado_add_admin_page(array(
			'slug' => '__404_error_page',
			'title' => esc_html__( '404 Error Page', 'libero' ),
			'icon' => 'icon_info_alt'
		));

		$panel_404_options = libero_mikado_add_admin_panel(array(
			'page' => '__404_error_page',
			'name'	=> 'panel_404_options',
			'title' => esc_html__( '404 Page Option', 'libero' )
		));

		libero_mikado_add_admin_field(array(
			'parent' => $panel_404_options,
			'type' => 'text',
			'name' => '404_title',
			'default_value' => '',
			'label' => esc_html__( 'Title', 'libero' ),
			'description' => esc_html__( 'Enter title for 404 page', 'libero' )
		));

		libero_mikado_add_admin_field(array(
			'parent' => $panel_404_options,
			'type' => 'image',
			'name' => '404_title_background_image',
			'default_value' => '',
			'label' => esc_html__( 'Title Background Image', 'libero' ),
			'description' => esc_html__( 'Choose background image for title area', 'libero' )
		));

		libero_mikado_add_admin_field(array(
			'parent' => $panel_404_options,
			'type' => 'text',
			'name' => '404_text',
			'default_value' => '',
			'label' => esc_html__( 'Text', 'libero' ),
			'description' => esc_html__( 'Enter text for 404 page', 'libero' )
		));

		libero_mikado_add_admin_field(array(
			'parent' => $panel_404_options,
			'type' => 'text',
			'name' => '404_back_to_home',
			'default_value' => '',
			'label' => esc_html__( 'Back to Home Button Label', 'libero' ),
			'description' => esc_html__( 'Enter label for Back to Home button', 'libero' )
		));

	}

	add_action( 'libero_mikado_options_map', 'libero_mikado_error_404_options_map',14);

}