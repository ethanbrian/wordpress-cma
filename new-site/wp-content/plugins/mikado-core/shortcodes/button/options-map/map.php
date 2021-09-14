<?php

if(!function_exists('libero_mikado_button_map')) {
    function libero_mikado_button_map() {
        $panel = libero_mikado_add_admin_panel(array(
            'title' => esc_html__( 'Button', 'mikado-core' ),
            'name'  => 'panel_button',
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
            'description' => esc_html__( 'Setup typography for all button types', 'mikado-core' ),
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
            'name'          => 'button_font_family',
            'default_value' => '',
            'label' => esc_html__( 'Font Family', 'mikado-core' ),
        ));

        libero_mikado_add_admin_field(array(
            'parent'        => $typography_row,
            'type'          => 'selectsimple',
            'name'          => 'button_text_transform',
            'default_value' => '',
            'label' => esc_html__( 'Text Transform', 'mikado-core' ),
            'options'       => libero_mikado_get_text_transform_array()
        ));

        libero_mikado_add_admin_field(array(
            'parent'        => $typography_row,
            'type'          => 'selectsimple',
            'name'          => 'button_font_style',
            'default_value' => '',
            'label' => esc_html__( 'Font Style', 'mikado-core' ),
            'options'       => libero_mikado_get_font_style_array()
        ));

        libero_mikado_add_admin_field(array(
            'parent'        => $typography_row,
            'type'          => 'textsimple',
            'name'          => 'button_letter_spacing',
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
            'name'          => 'button_font_weight',
            'default_value' => '',
            'label' => esc_html__( 'Font Weight', 'mikado-core' ),
            'options'       => libero_mikado_get_font_weight_array()
        ));

        //Outline type options
        libero_mikado_add_admin_section_title(array(
            'name' => 'type_section_title',
            'title' => esc_html__( 'Types', 'mikado-core' ),
            'parent' => $panel
        ));

        $outline_group = libero_mikado_add_admin_group(array(
            'name' => 'outline_group',
            'title' => esc_html__( 'Outline Type', 'mikado-core' ),
            'description' => esc_html__( 'Setup outline button type', 'mikado-core' ),
            'parent' => $panel
        ));

        $outline_row = libero_mikado_add_admin_row(array(
            'name' => 'outline_row',
            'next' => true,
            'parent' => $outline_group
        ));

        libero_mikado_add_admin_field(array(
            'parent'        => $outline_row,
            'type'          => 'colorsimple',
            'name'          => 'btn_outline_text_color',
            'default_value' => '',
            'label' => esc_html__( 'Text Color', 'mikado-core' ),
            'description'   => ''
        ));

        libero_mikado_add_admin_field(array(
            'parent'        => $outline_row,
            'type'          => 'colorsimple',
            'name'          => 'btn_outline_hover_text_color',
            'default_value' => '',
            'label' => esc_html__( 'Text Hover Color', 'mikado-core' ),
            'description'   => ''
        ));

        libero_mikado_add_admin_field(array(
            'parent'        => $outline_row,
            'type'          => 'colorsimple',
            'name'          => 'btn_outline_hover_bg_color',
            'default_value' => '',
            'label' => esc_html__( 'Hover Background Color', 'mikado-core' ),
            'description'   => ''
        ));

        libero_mikado_add_admin_field(array(
            'parent'        => $outline_row,
            'type'          => 'colorsimple',
            'name'          => 'btn_outline_border_color',
            'default_value' => '',
            'label' => esc_html__( 'Border Color', 'mikado-core' ),
            'description'   => ''
        ));

        $outline_row2 = libero_mikado_add_admin_row(array(
            'name' => 'outline_row2',
            'next' => true,
            'parent' => $outline_group
        ));

        libero_mikado_add_admin_field(array(
            'parent'        => $outline_row2,
            'type'          => 'colorsimple',
            'name'          => 'btn_outline_hover_border_color',
            'default_value' => '',
            'label' => esc_html__( 'Hover Border Color', 'mikado-core' ),
            'description'   => ''
        ));

        //Solid type options
        $solid_group = libero_mikado_add_admin_group(array(
            'name' => 'solid_group',
            'title' => esc_html__( 'Solid Type', 'mikado-core' ),
            'description' => esc_html__( 'Setup solid button type', 'mikado-core' ),
            'parent' => $panel
        ));

        $solid_row = libero_mikado_add_admin_row(array(
            'name' => 'solid_row',
            'next' => true,
            'parent' => $solid_group
        ));

        libero_mikado_add_admin_field(array(
            'parent'        => $solid_row,
            'type'          => 'colorsimple',
            'name'          => 'btn_solid_text_color',
            'default_value' => '',
            'label' => esc_html__( 'Text Color', 'mikado-core' ),
            'description'   => ''
        ));

        libero_mikado_add_admin_field(array(
            'parent'        => $solid_row,
            'type'          => 'colorsimple',
            'name'          => 'btn_solid_hover_text_color',
            'default_value' => '',
            'label' => esc_html__( 'Text Hover Color', 'mikado-core' ),
            'description'   => ''
        ));

        libero_mikado_add_admin_field(array(
            'parent'        => $solid_row,
            'type'          => 'colorsimple',
            'name'          => 'btn_solid_bg_color',
            'default_value' => '',
            'label' => esc_html__( 'Background Color', 'mikado-core' ),
            'description'   => ''
        ));

        libero_mikado_add_admin_field(array(
            'parent'        => $solid_row,
            'type'          => 'colorsimple',
            'name'          => 'btn_solid_hover_bg_color',
            'default_value' => '',
            'label' => esc_html__( 'Hover Background Color', 'mikado-core' ),
            'description'   => ''
        ));

        $solid_row2 = libero_mikado_add_admin_row(array(
            'name' => 'solid_row2',
            'next' => true,
            'parent' => $solid_group
        ));

        libero_mikado_add_admin_field(array(
            'parent'        => $solid_row2,
            'type'          => 'colorsimple',
            'name'          => 'btn_solid_border_color',
            'default_value' => '',
            'label' => esc_html__( 'Border Color', 'mikado-core' ),
            'description'   => ''
        ));

        libero_mikado_add_admin_field(array(
            'parent'        => $solid_row2,
            'type'          => 'colorsimple',
            'name'          => 'btn_solid_hover_border_color',
            'default_value' => '',
            'label' => esc_html__( 'Hover Border Color', 'mikado-core' ),
            'description'   => ''
        ));
    }

    add_action('libero_mikado_options_elements_map', 'libero_mikado_button_map',2);
}