<?php

if ( ! function_exists('libero_mikado_footer_options_map') ) {
	/**
	 * Add footer options
	 */
	function libero_mikado_footer_options_map() {

		libero_mikado_add_admin_page(
			array(
				'slug' => '_footer_page',
				'title' => esc_html__( 'Footer', 'libero' ),
				'icon' => 'icon_cone_alt'
			)
		);

		$footer_panel = libero_mikado_add_admin_panel(
			array(
				'title' => esc_html__( 'Footer', 'libero' ),
				'name' => 'footer',
				'page' => '_footer_page'
			)
		);

		libero_mikado_add_admin_field(
			array(
				'type' => 'yesno',
				'name' => 'uncovering_footer',
				'default_value' => 'yes',
				'label' => esc_html__( 'Uncovering Footer', 'libero' ),
				'description' => esc_html__( 'Enabling this option will make Footer gradually appear on scroll', 'libero' ),
				'parent' => $footer_panel,
			)
		);

		libero_mikado_add_admin_field(
			array(
				'type' => 'yesno',
				'name' => 'footer_in_grid',
				'default_value' => 'no',
				'label' => esc_html__( 'Footer in Grid', 'libero' ),
				'description' => esc_html__( 'Enabling this option will place Footer content in grid', 'libero' ),
				'parent' => $footer_panel,
			)
		);

		libero_mikado_add_admin_field(
			array(
				'type' => 'yesno',
				'name' => 'show_footer_top',
				'default_value' => 'yes',
				'label' => esc_html__( 'Show Footer Top', 'libero' ),
				'description' => esc_html__( 'Enabling this option will show Footer Top area', 'libero' ),
				'args' => array(
					'dependence' => true,
					'dependence_hide_on_yes' => '',
					'dependence_show_on_yes' => '#mkd_show_footer_top_container'
				),
				'parent' => $footer_panel,
			)
		);

		$show_footer_top_container = libero_mikado_add_admin_container(
			array(
				'name' => 'show_footer_top_container',
				'hidden_property' => 'show_footer_top',
				'hidden_value' => 'no',
				'parent' => $footer_panel
			)
		);

		libero_mikado_add_admin_field(
			array(
				'type' => 'select',
				'name' => 'footer_top_columns',
				'default_value' => '4',
				'label' => esc_html__( 'Footer Top Columns', 'libero' ),
				'description' => esc_html__( 'Choose number of columns for Footer Top area', 'libero' ),
				'options' => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'5' => '3(25%+25%+50%)',
					'6' => '3(50%+25%+25%)',
					'4' => '4'
				),
				'parent' => $show_footer_top_container,
			)
		);

		libero_mikado_add_admin_field(
			array(
				'type' => 'select',
				'name' => 'footer_top_columns_alignment',
				'default_value' => '',
				'label' => esc_html__( 'Footer Top Columns Alignment', 'libero' ),
				'description' => esc_html__( 'Text Alignment in Footer Columns', 'libero' ),
				'options' => array(
					'left' => esc_html__('Left', 'libero' ),
					'center' => esc_html__('Center', 'libero' ),
					'right' => esc_html__('Right', 'libero' )
				),
				'parent' => $show_footer_top_container,
			)
		);

		libero_mikado_add_admin_field(
			array(
				'type' => 'image',
				'name' => 'footer_top_background_image',
				'default_value' => '',
				'label' => esc_html__( 'Footer Top Background Image', 'libero' ),
				'description' => esc_html__( 'Choose background image', 'libero' ),
				'parent' => $show_footer_top_container,
			)
		);

		libero_mikado_add_admin_field(
			array(
				'type' => 'yesno',
				'name' => 'show_footer_bottom',
				'default_value' => 'yes',
				'label' => esc_html__( 'Show Footer Bottom', 'libero' ),
				'description' => esc_html__( 'Enabling this option will show Footer Bottom area', 'libero' ),
				'args' => array(
					'dependence' => true,
					'dependence_hide_on_yes' => '',
					'dependence_show_on_yes' => '#mkd_show_footer_bottom_container'
				),
				'parent' => $footer_panel,
			)
		);

		$show_footer_bottom_container = libero_mikado_add_admin_container(
			array(
				'name' => 'show_footer_bottom_container',
				'hidden_property' => 'show_footer_bottom',
				'hidden_value' => 'no',
				'parent' => $footer_panel
			)
		);


		libero_mikado_add_admin_field(
			array(
				'type' => 'select',
				'name' => 'footer_bottom_columns',
				'default_value' => '3',
				'label' => esc_html__( 'Footer Bottom Columns', 'libero' ),
				'description' => esc_html__( 'Choose number of columns for Footer Bottom area', 'libero' ),
				'options' => array(
					'1' => '1',
					'2' => '2',
					'3' => '3'
				),
				'parent' => $show_footer_bottom_container,
			)
		);

	}

	add_action( 'libero_mikado_options_map', 'libero_mikado_footer_options_map', 8);

}