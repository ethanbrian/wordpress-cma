<?php

$general_meta_box = libero_mikado_create_meta_box(
    array(
        'scope' => array('page', 'portfolio-item', 'post'),
        'title' => esc_html__( 'General', 'libero' ),
        'name' => 'general_meta'
    )
);


    libero_mikado_create_meta_box_field(
        array(
            'name' => 'mkd_page_background_color_meta',
            'type' => 'color',
            'default_value' => '',
            'label' => esc_html__( 'Page Background Color', 'libero' ),
            'description' => esc_html__( 'Choose background color for page content', 'libero' ),
            'parent' => $general_meta_box
        )
    );

    libero_mikado_create_meta_box_field(
        array(
            'name' => 'mkd_page_slider_meta',
            'type' => 'text',
            'default_value' => '',
            'label' => esc_html__( 'Slider Shortcode', 'libero' ),
            'description' => esc_html__( 'Paste your slider shortcode here', 'libero' ),
            'parent' => $general_meta_box
        )
    );

    libero_mikado_create_meta_box_field(
        array(
            'name'        => 'mkd_page_comments_meta',
            'type'        => 'selectblank',
            'label' => esc_html__( 'Show Comments', 'libero' ),
            'description' => esc_html__( 'Enabling this option will show comments on your page', 'libero' ),
            'parent'      => $general_meta_box,
            'options'     => array(
                'yes' => esc_html__('Yes', 'libero' ),
                'no' => esc_html__('No', 'libero' ),
            )
        )
    );