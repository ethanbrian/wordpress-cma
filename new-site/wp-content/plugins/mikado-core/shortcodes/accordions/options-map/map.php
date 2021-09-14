<?php 
if(!function_exists('libero_mikado_accordions_map')) {
    /**
     * Add Accordion options to elements panel
     */
   function libero_mikado_accordions_map() {
		
       $panel = libero_mikado_add_admin_panel(array(
           'title' => esc_html__( 'Accordions', 'mikado-core' ),
           'name'  => 'panel_accordions',
           'page'  => '_elements_page'
       ));

       //Typography options
       libero_mikado_add_admin_section_title(array(
           'name' => 'typography_section_title',
           'title' => esc_html__( 'Typography', 'mikado-core' ),
           'parent' => $panel
       ));

       $typography_group = libero_mikado_add_admin_group(array(
           'name' => 'typography_group',
           'title' => esc_html__( 'Typography', 'mikado-core' ),
			'description' => esc_html__( 'Setup typography for accordions navigation', 'mikado-core' ),
           'parent' => $panel
       ));

       $typography_row = libero_mikado_add_admin_row(array(
           'name' => 'typography_row',
           'next' => true,
           'parent' => $typography_group
       ));

       libero_mikado_add_admin_field(array(
           'parent'        => $typography_row,
           'type'          => 'fontsimple',
           'name'          => 'accordions_font_family',
           'default_value' => '',
           'label' => esc_html__( 'Font Family', 'mikado-core' ),
       ));

       libero_mikado_add_admin_field(array(
           'parent'        => $typography_row,
           'type'          => 'selectsimple',
           'name'          => 'accordions_text_transform',
           'default_value' => '',
           'label' => esc_html__( 'Text Transform', 'mikado-core' ),
           'options'       => libero_mikado_get_text_transform_array()
       ));

       libero_mikado_add_admin_field(array(
           'parent'        => $typography_row,
           'type'          => 'selectsimple',
           'name'          => 'accordions_font_style',
           'default_value' => '',
           'label' => esc_html__( 'Font Style', 'mikado-core' ),
           'options'       => libero_mikado_get_font_style_array()
       ));

       libero_mikado_add_admin_field(array(
           'parent'        => $typography_row,
           'type'          => 'textsimple',
           'name'          => 'accordions_letter_spacing',
           'default_value' => '',
           'label' => esc_html__( 'Letter Spacing', 'mikado-core' ),
           'args'          => array(
               'suffix' => 'px'
           )
       ));

       $typography_row2 = libero_mikado_add_admin_row(array(
           'name' => 'typography_row2',
           'next' => true,
           'parent' => $typography_group
       ));		
		
       libero_mikado_add_admin_field(array(
           'parent'        => $typography_row2,
           'type'          => 'selectsimple',
           'name'          => 'accordions_font_weight',
           'default_value' => '',
           'label' => esc_html__( 'Font Weight', 'mikado-core' ),
           'options'       => libero_mikado_get_font_weight_array()
       ));
		
		//Initial Accordion Color Styles
		
		libero_mikado_add_admin_section_title(array(
           'name' => 'accordion_color_section_title',
           'title' => esc_html__( 'Accordions Color Styles', 'mikado-core' ),
           'parent' => $panel
       ));
       $accordions_general_color_group = libero_mikado_add_admin_group(array(
           'name' => 'accordions_general_color_group',
           'title' => esc_html__( 'Accordion Color Styles', 'mikado-core' ),
           'description' => esc_html__( 'Set color styles for accordion title', 'mikado-core' ),
           'parent' => $panel
       ));

       $accordions_general_color_row = libero_mikado_add_admin_row(array(
           'name' => 'accordions_color_row',
           'next' => true,
           'parent' => $accordions_general_color_group
       ));
       libero_mikado_add_admin_field(array(
           'parent'        => $accordions_general_color_row,
           'type'          => 'colorsimple',
           'name'          => 'accordions_background_color',
           'default_value' => '',
           'label' => esc_html__( 'Background Color', 'mikado-core' )
       ));
       libero_mikado_add_admin_field(array(
           'parent'        => $accordions_general_color_row,
           'type'          => 'colorsimple',
           'name'          => 'accordions_border_color',
           'default_value' => '',
           'label' => esc_html__( 'Border Color', 'mikado-core' )
       ));

		libero_mikado_add_admin_field(array(
           'parent'        => $accordions_general_color_row,
           'type'          => 'colorsimple',
           'name'          => 'accordions_title_color',
           'default_value' => '',
           'label' => esc_html__( 'Title Color', 'mikado-core' )
       ));
		libero_mikado_add_admin_field(array(
           'parent'        => $accordions_general_color_row,
           'type'          => 'colorsimple',
           'name'          => 'accordions_icon_color',
           'default_value' => '',
           'label' => esc_html__( 'Icon Color', 'mikado-core' )
       ));
		
		$active_accordions_color_group = libero_mikado_add_admin_group(array(
           'name' => 'active_accordions_color_group',
           'title' => esc_html__( 'Active and Hover Accordion Color Styles', 'mikado-core' ),
			'description' => esc_html__( 'Set color styles for active and hover accordions', 'mikado-core' ),
           'parent' => $panel
       ));
		$active_accordions_color_row = libero_mikado_add_admin_row(array(
           'name' => 'active_accordions_color_row',
           'next' => true,
           'parent' => $active_accordions_color_group
       ));
		libero_mikado_add_admin_field(array(
           'parent'        => $active_accordions_color_row,
           'type'          => 'colorsimple',
           'name'          => 'accordions_title_color_active',
           'default_value' => '',
           'label' => esc_html__( 'Title Color', 'mikado-core' )
       ));
		libero_mikado_add_admin_field(array(
           'parent'        => $active_accordions_color_row,
           'type'          => 'colorsimple',
           'name'          => 'accordions_icon_color_active',
           'default_value' => '',
           'label' => esc_html__( 'Icon Color', 'mikado-core' )
       ));
       
   }
   add_action('libero_mikado_options_elements_map', 'libero_mikado_accordions_map', 1);
}