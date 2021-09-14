<?php

if ( ! function_exists('libero_mikado_call_to_action_options_map') ) {
	/**
	 * Add Call to Action options to elements page
	 */
	function libero_mikado_call_to_action_options_map() {

		$panel_call_to_action = libero_mikado_add_admin_panel(
			array(
				'page' => '_elements_page',
				'name' => 'panel_call_to_action',
				'title' => esc_html__( 'Call To Action', 'mikado-core' )
			)
		);

		$coloring_group = libero_mikado_add_admin_group(array(
			'name' => 'coloring_group',
			'title' => esc_html__( 'Color style', 'mikado-core' ),
			'description' => esc_html__( 'Choose default color style for Call to Action', 'mikado-core' ),
			'parent' => $panel_call_to_action
		));

		$coloring_row = libero_mikado_add_admin_row(array(
			'name' => 'coloring_row',
			'parent' => $coloring_group
		));

		libero_mikado_add_admin_field(array(
			'parent' => $coloring_row,
			'type' => 'colorsimple',
			'name' => 'call_to_action_bckg_color',
			'default_value' => '',
			'label' => esc_html__( 'Background Color', 'mikado-core' ),
		));

		libero_mikado_add_admin_field(array(
			'parent' => $coloring_row,
			'type' => 'colorsimple',
			'name' => 'call_to_action_border_color',
			'default_value' => '',
			'label' => esc_html__( 'Border Color', 'mikado-core' )
		));

	}

	add_action( 'libero_mikado_options_elements_map', 'libero_mikado_call_to_action_options_map',3);

}