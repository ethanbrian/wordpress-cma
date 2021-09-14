<?php

if(!function_exists('mkd_map_portfolio_settings')) {
    function mkd_map_portfolio_settings() {
        $meta_box = libero_mikado_create_meta_box(array(
            'scope' => 'portfolio-item',
            'title' => esc_html__('Portfolio Settings','libero'),
            'name'  => 'portfolio_settings_meta_box'
        ));

        libero_mikado_create_meta_box_field(array(
            'name'        => 'mkd_portfolio_single_template_meta',
            'type'        => 'select',
            'label'       => esc_html__('Portfolio Type','libero'),
            'description' => esc_html__('Choose a default type for Single Project pages','libero'),
            'parent'      => $meta_box,
            'options'     => array(
                ''                  => esc_html__('Default','libero'),
                'small-images'      => esc_html__('Portfolio small images','libero'),
                'small-slider'      => esc_html__('Portfolio small slider','libero'),
                'big-images'        => esc_html__('Portfolio big images','libero'),
                'big-slider'        => esc_html__('Portfolio big slider','libero'),
                'custom'            => esc_html__('Portfolio custom','libero'),
                'full-width-custom' => esc_html__('Portfolio full width custom','libero'),
                'gallery'           => esc_html__('Portfolio gallery','libero')
            )
        ));

        $all_pages = array();
        $pages     = get_pages();
        foreach($pages as $page) {
            $all_pages[$page->ID] = $page->post_title;
        }

        libero_mikado_create_meta_box_field(array(
            'name'        => 'portfolio_single_back_to_link',
            'type'        => 'select',
            'label'       => esc_html__('Back To Link','libero'),
            'description' => esc_html__('Choose "Back To" page to link from portfolio Single Project page','libero'),
            'parent'      => $meta_box,
            'options'     => $all_pages
        ));

        libero_mikado_create_meta_box_field(array(
            'name'        => 'portfolio_external_link',
            'type'        => 'text',
            'label'       => esc_html__('Portfolio External Link','libero'),
            'description' => esc_html__('Enter URL to link from Portfolio List page','libero'),
            'parent'      => $meta_box,
            'args'        => array(
                'col_width' => 3
            )
        ));
    }

    add_action('libero_mikado_meta_boxes_map', 'mkd_map_portfolio_settings');
}