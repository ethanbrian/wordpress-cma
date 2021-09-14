<?php

$custom_sidebars = libero_mikado_get_custom_sidebars();

$sidebar_meta_box = libero_mikado_create_meta_box(
    array(
        'scope' => array('page', 'portfolio-item', 'post'),
        'title' => esc_html__( 'Sidebar', 'libero' ),
        'name' => 'sidebar_meta'
    )
);

    libero_mikado_create_meta_box_field(
        array(
            'name'        => 'mkd_sidebar_meta',
            'type'        => 'select',
            'label' => esc_html__( 'Layout', 'libero' ),
            'description' => esc_html__( 'Choose the sidebar layout', 'libero' ),
            'parent'      => $sidebar_meta_box,
            'options'     => array(
						''					=> esc_html__('Default', 'libero' ),
						'no-sidebar'		=> esc_html__('No Sidebar', 'libero' ),
						'sidebar-33-right'	=> esc_html__('Sidebar 1/3 Right', 'libero' ),
						'sidebar-25-right' 	=> esc_html__('Sidebar 1/4 Right', 'libero' ),
						'sidebar-33-left' 	=> esc_html__('Sidebar 1/3 Left', 'libero' ),
						'sidebar-25-left' 	=> esc_html__('Sidebar 1/4 Left', 'libero' ),
					)
        )
    );

if(count($custom_sidebars) > 0) {
    libero_mikado_create_meta_box_field(array(
        'name' => 'mkd_custom_sidebar_meta',
        'type' => 'selectblank',
        'label' => esc_html__( 'Choose Widget Area in Sidebar', 'libero' ),
        'description' => esc_html__( 'Choose Custom Widget area to display in Sidebar', 'libero' ),
        'parent' => $sidebar_meta_box,
        'options' => $custom_sidebars
    ));
}
