<?php

if ( ! function_exists('libero_mikado_page_options_map') ) {

    function libero_mikado_page_options_map() {

        libero_mikado_add_admin_page(
            array(
                'slug'  => '_page_page',
                'title' => esc_html__( 'Page', 'libero' ),
                'icon'  => 'icon_document_alt'
            )
        );

        $custom_sidebars = libero_mikado_get_custom_sidebars();

        $panel_sidebar = libero_mikado_add_admin_panel(
            array(
                'page'  => '_page_page',
                'name'  => 'panel_sidebar',
                'title' => esc_html__( 'Design Style', 'libero' )
            )
        );

        libero_mikado_add_admin_field(array(
            'name'        => 'page_sidebar_layout',
            'type'        => 'select',
            'label' => esc_html__( 'Sidebar Layout', 'libero' ),
            'description' => esc_html__( 'Choose a sidebar layout for pages', 'libero' ),
            'default_value' => 'default',
            'parent'      => $panel_sidebar,
            'options'     => array(
                'default'			=> esc_html__('No Sidebar', 'libero' ),
                'sidebar-33-right'	=> esc_html__('Sidebar 1/3 Right', 'libero' ),
                'sidebar-25-right' 	=> esc_html__('Sidebar 1/4 Right', 'libero' ),
                'sidebar-33-left' 	=> esc_html__('Sidebar 1/3 Left', 'libero' ),
                'sidebar-25-left' 	=> esc_html__('Sidebar 1/4 Left', 'libero' )
            )
        ));


        if(count($custom_sidebars) > 0) {
            libero_mikado_add_admin_field(array(
                'name' => 'page_custom_sidebar',
                'type' => 'selectblank',
                'label' => esc_html__( 'Sidebar to Display', 'libero' ),
                'description' => esc_html__( 'Choose a sidebar to display on pages. Default sidebar is Sidebar', 'libero' ),
                'parent' => $panel_sidebar,
                'options' => $custom_sidebars
            ));
        }

        libero_mikado_add_admin_field(array(
            'name'        => 'page_show_comments',
            'type'        => 'yesno',
            'label' => esc_html__( 'Show Comments', 'libero' ),
            'description' => esc_html__( 'Enabling this option will show comments on your page', 'libero' ),
            'default_value' => 'yes',
            'parent'      => $panel_sidebar
        ));

        $panel_widgets = libero_mikado_add_admin_panel(
            array(
                'page'  => '_page_page',
                'name'  => 'panel_widgets',
                'title' => esc_html__( 'Sidebar', 'libero' )
            )
        );

        libero_mikado_add_admin_section_title(
            array(
                'parent' => $panel_widgets,
                'name' => 'logo_area_title',
                'title' => esc_html__( 'Widgets', 'libero' )
            )
        );

        libero_mikado_add_admin_field(array(
            'type'			=> 'color',
            'name'			=> 'sidebar_background_color',
            'default_value'	=> '',
            'label' => esc_html__( 'Sidebar Background Color', 'libero' ),
            'description' => esc_html__( 'Choose background color for sidebar', 'libero' ),
            'parent'		=> $panel_widgets
        ));

        $group_sidebar_padding = libero_mikado_add_admin_group(array(
            'name'		=> 'group_sidebar_padding',
            'title' => esc_html__( 'Padding', 'libero' ),
            'parent'	=> $panel_widgets
        ));

        $row_sidebar_padding = libero_mikado_add_admin_row(array(
            'name'		=> 'row_sidebar_padding',
            'parent'	=> $group_sidebar_padding
        ));

        libero_mikado_add_admin_field(array(
            'type'			=> 'textsimple',
            'name'			=> 'sidebar_padding_top',
            'default_value'	=> '',
            'label' => esc_html__( 'Top Padding', 'libero' ),
            'args'			=> array(
                'suffix'	=> 'px'
            ),
            'parent'		=> $row_sidebar_padding
        ));

        libero_mikado_add_admin_field(array(
            'type'			=> 'textsimple',
            'name'			=> 'sidebar_padding_right',
            'default_value'	=> '',
            'label' => esc_html__( 'Right Padding', 'libero' ),
            'args'			=> array(
                'suffix'	=> 'px'
            ),
            'parent'		=> $row_sidebar_padding
        ));

        libero_mikado_add_admin_field(array(
            'type'			=> 'textsimple',
            'name'			=> 'sidebar_padding_bottom',
            'default_value'	=> '',
            'label' => esc_html__( 'Bottom Padding', 'libero' ),
            'args'			=> array(
                'suffix'	=> 'px'
            ),
            'parent'		=> $row_sidebar_padding
        ));

        libero_mikado_add_admin_field(array(
            'type'			=> 'textsimple',
            'name'			=> 'sidebar_padding_left',
            'default_value'	=> '',
            'label' => esc_html__( 'Left Padding', 'libero' ),
            'args'			=> array(
                'suffix'	=> 'px'
            ),
            'parent'		=> $row_sidebar_padding
        ));

        libero_mikado_add_admin_field(array(
            'type'			=> 'select',
            'name'			=> 'sidebar_alignment',
            'default_value'	=> '',
            'label' => esc_html__( 'Text Alignment', 'libero' ),
            'description' => esc_html__( 'Choose text aligment', 'libero' ),
            'options'		=> array(
                'left' => esc_html__('Left', 'libero' ),
                'center' => esc_html__('Center', 'libero' ),
                'right' => esc_html__('Right', 'libero' )
            ),
            'parent'		=> $panel_widgets
        ));
    }

    add_action( 'libero_mikado_options_map', 'libero_mikado_page_options_map', 7);

}