<?php

if ( ! function_exists('libero_mikado_logo_options_map') ) {

	function libero_mikado_logo_options_map() {

		$panel_logo = libero_mikado_add_admin_panel(
			array(
				'page' => '',
				'name' => 'panel_logo',
				'title' => esc_html__( 'Branding', 'libero' )
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $panel_logo,
				'type' => 'yesno',
				'name' => 'hide_logo',
				'default_value' => 'no',
				'label' => esc_html__( 'Hide Logo', 'libero' ),
				'description' => esc_html__( 'Enabling this option will hide logo image', 'libero' ),
				'args' => array(
					"dependence" => true,
					"dependence_hide_on_yes" => "#mkd_hide_logo_container",
					"dependence_show_on_yes" => ""
				)
			)
		);

		$hide_logo_container = libero_mikado_add_admin_container(
			array(
				'parent' => $panel_logo,
				'name' => 'hide_logo_container',
				'hidden_property' => 'hide_logo',
				'hidden_value' => 'yes'
			)
		);

		libero_mikado_add_admin_field(
			array(
				'name' => 'logo_image',
				'type' => 'image',
				'default_value' => MIKADO_ASSETS_ROOT."/img/logo.png",
				'label' => esc_html__( 'Logo Image - Default', 'libero' ),
				'description' => esc_html__( 'Choose a default logo image to display ', 'libero' ),
				'parent' => $hide_logo_container
			)
		);

		libero_mikado_add_admin_field(
			array(
				'name' => 'logo_image_dark',
				'type' => 'image',
				'default_value' => MIKADO_ASSETS_ROOT."/img/logo_black.png",
				'label' => esc_html__( 'Logo Image - Dark', 'libero' ),
				'description' => esc_html__( 'Choose a default logo image to display ', 'libero' ),
				'parent' => $hide_logo_container
			)
		);

		libero_mikado_add_admin_field(
			array(
				'name' => 'logo_image_light',
				'type' => 'image',
				'default_value' => MIKADO_ASSETS_ROOT."/img/logo_white.png",
				'label' => esc_html__( 'Logo Image - Light', 'libero' ),
				'description' => esc_html__( 'Choose a default logo image to display ', 'libero' ),
				'parent' => $hide_logo_container
			)
		);

		libero_mikado_add_admin_field(
			array(
				'name' => 'logo_image_sticky',
				'type' => 'image',
				'default_value' => MIKADO_ASSETS_ROOT."/img/logo_sticky.png",
				'label' => esc_html__( 'Logo Image - Sticky', 'libero' ),
				'description' => esc_html__( 'Choose a default logo image to display ', 'libero' ),
				'parent' => $hide_logo_container
			)
		);

		libero_mikado_add_admin_field(
			array(
				'name' => 'logo_image_mobile',
				'type' => 'image',
				'default_value' => MIKADO_ASSETS_ROOT."/img/logo.png",
				'label' => esc_html__( 'Logo Image - Mobile', 'libero' ),
				'description' => esc_html__( 'Choose a default logo image to display ', 'libero' ),
				'parent' => $hide_logo_container
			)
		);

	}

	add_action( 'libero_mikado_before_general_options_map', 'libero_mikado_logo_options_map');

}