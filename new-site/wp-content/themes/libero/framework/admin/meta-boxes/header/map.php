<?php

$header_meta_box = libero_mikado_create_meta_box(
    array(
        'scope' => array('page', 'portfolio-item', 'post'),
        'title' => esc_html__( 'Header', 'libero' ),
        'name' => 'header_meta'
    )
);
    libero_mikado_create_meta_box_field(
        array(
            'name' => 'mkd_header_style_meta',
            'type' => 'select',
            'default_value' => '',
            'label' => esc_html__( 'Header Skin', 'libero' ),
            'description' => esc_html__( 'Choose a header style to make header elements (logo, main menu, side menu button) in that predefined style', 'libero' ),
            'parent' => $header_meta_box,
            'options' => array(
                '' => '',
                'light-header' => esc_html__('Light', 'libero' ),
                'dark-header' => esc_html__('Dark', 'libero' )
            )
        )
    );

    libero_mikado_create_meta_box_field(
        array(
            'parent' => $header_meta_box,
            'type' => 'select',
            'name' => 'mkd_enable_header_style_on_scroll_meta',
            'default_value' => '',
            'label' => esc_html__( 'Enable Header Style on Scroll', 'libero' ),
            'description' => esc_html__( 'Enabling this option, header will change style depending on row settings for dark/light style', 'libero' ),
            'options' => array(
                '' => '',
                'no' => esc_html__('No', 'libero' ),
                'yes' => esc_html__('Yes', 'libero' )
            )
        )
    );

    libero_mikado_create_meta_box_field(
        array(
            'name' => 'mkd_top_menu_area_background_color_header_standard_meta',
            'type' => 'color',
            'label' => esc_html__( 'Top Menu Area Background Color', 'libero' ),
            'description' => esc_html__( 'Choose a background color for header top menu area', 'libero' ),
            'parent' => $header_meta_box
        )
    );

	libero_mikado_create_meta_box_field(
		array(
			'parent' => $header_meta_box,
			'type' => 'color',
			'name' => 'mkd_menu_area_background_color_header_standard_meta',
			'default_value' => '',
			'label' => esc_html__( 'Bottom Menu Area Background color', 'libero' ),
			'description' => esc_html__( 'Set background color for header bottom menu area', 'libero' )
		)
	);


	libero_mikado_create_meta_box_field(array(
		'name' => 'mkd_sticky_header_background_color_meta',
		'type' => 'color',
		'label' => esc_html__( 'Sticky Background Color', 'libero' ),
		'description' => esc_html__( 'Set background color for sticky header', 'libero' ),
		'parent' => $header_meta_box
	));

    libero_mikado_create_meta_box_field(
        array(
            'name' => 'mkd_sticky_header_transparency_meta',
            'type' => 'text',
            'label' => esc_html__( 'Sticky Header Transparency', 'libero' ),
            'description' => esc_html__( 'Choose a transparency for sticky header background color (0 = fully transparent, 1 = opaque)', 'libero' ),
            'parent' => $header_meta_box,
            'args' => array(
                'col_width' => 2
            )
        )
    );

    libero_mikado_create_meta_box_field(
        array(
            'name'            => 'mkd_scroll_amount_for_sticky_meta',
            'type'            => 'text',
            'label' => esc_html__( 'Scroll amount for sticky header appearance', 'libero' ),
            'description' => esc_html__( 'Define scroll amount for sticky header appearance', 'libero' ),
            'parent'          => $header_meta_box,
            'args'            => array(
                'col_width' => 2,
                'suffix'    => 'px'
            ),
            'hidden_property' => 'mkd_header_behaviour',
            'hidden_values'   => array("sticky-header-on-scroll-up", "fixed-on-scroll")
        )
    );
