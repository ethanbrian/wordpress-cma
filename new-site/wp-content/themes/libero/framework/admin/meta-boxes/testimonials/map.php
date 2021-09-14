<?php

//Testimonials

$testimonial_meta_box = libero_mikado_create_meta_box(
    array(
        'scope' => array('testimonials'),
        'title' => esc_html__( 'Testimonial', 'libero' ),
        'name' => 'testimonial_meta'
    )
);

    libero_mikado_create_meta_box_field(
        array(
            'name'        	=> 'mkd_testimonial_title',
            'type'        	=> 'text',
            'label' => esc_html__( 'Title', 'libero' ),
            'description' => esc_html__( 'Enter testimonial title', 'libero' ),
            'parent'      	=> $testimonial_meta_box,
        )
    );


    libero_mikado_create_meta_box_field(
        array(
            'name'        	=> 'mkd_testimonial_author',
            'type'        	=> 'text',
            'label' => esc_html__( 'Author', 'libero' ),
            'description' => esc_html__( 'Enter author name', 'libero' ),
            'parent'      	=> $testimonial_meta_box,
        )
    );

    libero_mikado_create_meta_box_field(
        array(
            'name'        	=> 'mkd_testimonial_author_position',
            'type'        	=> 'text',
            'label' => esc_html__( 'Job Position', 'libero' ),
            'description' => esc_html__( 'Enter job position', 'libero' ),
            'parent'      	=> $testimonial_meta_box,
        )
    );

    libero_mikado_create_meta_box_field(
        array(
            'name'        	=> 'mkd_testimonial_text',
            'type'        	=> 'text',
            'label' => esc_html__( 'Text', 'libero' ),
            'description' => esc_html__( 'Enter testimonial text', 'libero' ),
            'parent'      	=> $testimonial_meta_box,
        )
    );