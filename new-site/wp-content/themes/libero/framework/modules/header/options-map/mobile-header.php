<?php

if ( ! function_exists('libero_mikado_mobile_header_options_map') ) {

	function libero_mikado_mobile_header_options_map() {

		$panel_mobile_header = libero_mikado_add_admin_panel(array(
			'title' => esc_html__( 'Mobile Header', 'libero' ),
			'name'  => 'panel_mobile_header',
			'page'  => '_header_page'
		));

		libero_mikado_add_admin_field(array(
			'name'        => 'mobile_header_height',
			'type'        => 'text',
			'label' => esc_html__( 'Mobile Header Height', 'libero' ),
			'description' => esc_html__( 'Enter height for mobile header in pixels', 'libero' ),
			'parent'      => $panel_mobile_header,
			'args'        => array(
				'col_width' => 3,
				'suffix'    => 'px'
			)
		));

		libero_mikado_add_admin_field(array(
			'name'        => 'mobile_header_background_color',
			'type'        => 'color',
			'label' => esc_html__( 'Mobile Header Background Color', 'libero' ),
			'description' => esc_html__( 'Choose color for mobile header', 'libero' ),
			'parent'      => $panel_mobile_header
		));

		libero_mikado_add_admin_field(array(
			'name'        => 'mobile_menu_background_color',
			'type'        => 'color',
			'label' => esc_html__( 'Mobile Menu Background Color', 'libero' ),
			'description' => esc_html__( 'Choose color for mobile menu', 'libero' ),
			'parent'      => $panel_mobile_header
		));

		libero_mikado_add_admin_field(array(
			'name'        => 'mobile_menu_separator_color',
			'type'        => 'color',
			'label' => esc_html__( 'Mobile Menu Item Separator Color', 'libero' ),
			'description' => esc_html__( 'Choose color for mobile menu horizontal separators', 'libero' ),
			'parent'      => $panel_mobile_header
		));

		libero_mikado_add_admin_field(array(
			'name'        => 'mobile_logo_height',
			'type'        => 'text',
			'label' => esc_html__( 'Logo Height For Mobile Header', 'libero' ),
			'description' => esc_html__( 'Define logo height for screen size smaller than 1000px', 'libero' ),
			'parent'      => $panel_mobile_header,
			'args'        => array(
				'col_width' => 3,
				'suffix'    => 'px'
			)
		));

		libero_mikado_add_admin_field(array(
			'name'        => 'mobile_logo_height_phones',
			'type'        => 'text',
			'label' => esc_html__( 'Logo Height For Mobile Devices', 'libero' ),
			'description' => esc_html__( 'Define logo height for screen size smaller than 480px', 'libero' ),
			'parent'      => $panel_mobile_header,
			'args'        => array(
				'col_width' => 3,
				'suffix'    => 'px'
			)
		));

		libero_mikado_add_admin_section_title(array(
			'parent' => $panel_mobile_header,
			'name'   => 'mobile_header_fonts_title',
			'title' => esc_html__( 'Typography', 'libero' )
		));

		libero_mikado_add_admin_field(array(
			'name'        => 'mobile_text_color',
			'type'        => 'color',
			'label' => esc_html__( 'Navigation Text Color', 'libero' ),
			'description' => esc_html__( 'Define color for mobile navigation text', 'libero' ),
			'parent'      => $panel_mobile_header
		));

		libero_mikado_add_admin_field(array(
			'name'        => 'mobile_text_hover_color',
			'type'        => 'color',
			'label' => esc_html__( 'Navigation Hover/Active Color', 'libero' ),
			'description' => esc_html__( 'Define hover/active color for mobile navigation text', 'libero' ),
			'parent'      => $panel_mobile_header
		));

		libero_mikado_add_admin_field(array(
			'name'        => 'mobile_font_family',
			'type'        => 'font',
			'label' => esc_html__( 'Navigation Font Family', 'libero' ),
			'description' => esc_html__( 'Define font family for mobile navigation text', 'libero' ),
			'parent'      => $panel_mobile_header
		));

		libero_mikado_add_admin_field(array(
			'name'        => 'mobile_font_size',
			'type'        => 'text',
			'label' => esc_html__( 'Navigation Font Size', 'libero' ),
			'description' => esc_html__( 'Define font size for mobile navigation text', 'libero' ),
			'parent'      => $panel_mobile_header,
			'args'        => array(
				'col_width' => 3,
				'suffix'    => 'px'
			)
		));

		libero_mikado_add_admin_field(array(
			'name'        => 'mobile_line_height',
			'type'        => 'text',
			'label' => esc_html__( 'Navigation Line Height', 'libero' ),
			'description' => esc_html__( 'Define line height for mobile navigation text', 'libero' ),
			'parent'      => $panel_mobile_header,
			'args'        => array(
				'col_width' => 3,
				'suffix'    => 'px'
			)
		));

		libero_mikado_add_admin_field(array(
			'name'        => 'mobile_text_transform',
			'type'        => 'select',
			'label' => esc_html__( 'Navigation Text Transform', 'libero' ),
			'description' => esc_html__( 'Define text transform for mobile navigation text', 'libero' ),
			'parent'      => $panel_mobile_header,
			'options'     => libero_mikado_get_text_transform_array(true)
		));

		libero_mikado_add_admin_field(array(
			'name'        => 'mobile_font_style',
			'type'        => 'select',
			'label' => esc_html__( 'Navigation Font Style', 'libero' ),
			'description' => esc_html__( 'Define font style for mobile navigation text', 'libero' ),
			'parent'      => $panel_mobile_header,
			'options'     => libero_mikado_get_font_style_array(true)
		));

		libero_mikado_add_admin_field(array(
			'name'        => 'mobile_font_weight',
			'type'        => 'select',
			'label' => esc_html__( 'Navigation Font Weight', 'libero' ),
			'description' => esc_html__( 'Define font weight for mobile navigation text', 'libero' ),
			'parent'      => $panel_mobile_header,
			'options'     => libero_mikado_get_font_weight_array(true)
		));

		libero_mikado_add_admin_section_title(array(
			'name' => 'mobile_opener_panel',
			'parent' => $panel_mobile_header,
			'title' => esc_html__( 'Mobile Menu Opener', 'libero' )
		));

		libero_mikado_add_admin_field(array(
			'name'        => 'mobile_icon_color',
			'type'        => 'color',
			'label' => esc_html__( 'Mobile Navigation Icon Color', 'libero' ),
			'description' => esc_html__( 'Choose color for icon header', 'libero' ),
			'parent'      => $panel_mobile_header
		));

		libero_mikado_add_admin_field(array(
			'name'        => 'mobile_icon_hover_color',
			'type'        => 'color',
			'label' => esc_html__( 'Mobile Navigation Icon Hover Color', 'libero' ),
			'description' => esc_html__( 'Choose hover color for mobile navigation icon ', 'libero' ),
			'parent'      => $panel_mobile_header
		));

	}

	add_action( 'libero_mikado_after_header_options_map', 'libero_mikado_mobile_header_options_map');

}