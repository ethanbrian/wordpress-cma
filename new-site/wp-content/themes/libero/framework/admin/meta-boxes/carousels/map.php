<?php

//Carousels

$carousel_meta_box = libero_mikado_create_meta_box(
    array(
        'scope' => array('carousels'),
        'title' => esc_html__( 'Carousel', 'libero' ),
        'name' => 'carousel_meta'
    )
);

    libero_mikado_create_meta_box_field(
        array(
            'name'        => 'mkd_carousel_image',
            'type'        => 'image',
            'label' => esc_html__( 'Carousel Image', 'libero' ),
            'description' => esc_html__( 'Choose carousel image (min width needs to be 215px)', 'libero' ),
            'parent'      => $carousel_meta_box
        )
    );

    libero_mikado_create_meta_box_field(
        array(
            'name'        => 'mkd_carousel_hover_image',
            'type'        => 'image',
            'label' => esc_html__( 'Carousel Hover Image', 'libero' ),
            'description' => esc_html__( 'Choose carousel hover image (min width needs to be 215px)', 'libero' ),
            'parent'      => $carousel_meta_box
        )
    );

    libero_mikado_create_meta_box_field(
        array(
            'name'        => 'mkd_carousel_item_link',
            'type'        => 'text',
            'label' => esc_html__( 'Link', 'libero' ),
            'description' => esc_html__( 'Enter the URL to which you want the image to link to (e.g. http://www.example.com)', 'libero' ),
            'parent'      => $carousel_meta_box
        )
    );

    libero_mikado_create_meta_box_field(
        array(
            'name'        => 'mkd_carousel_item_target',
            'type'        => 'selectblank',
            'label' => esc_html__( 'Target', 'libero' ),
            'description' => esc_html__( 'Specify where to open the linked document', 'libero' ),
            'parent'      => $carousel_meta_box,
            'options' => array(
            	'_self' => esc_html__('Self', 'libero' ),
            	'_blank' => esc_html__('Blank', 'libero' )
        	)
        )
    );