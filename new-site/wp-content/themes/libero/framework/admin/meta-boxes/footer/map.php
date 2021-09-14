<?php

$footer_meta_box = libero_mikado_create_meta_box(
    array(
        'scope' => array('page', 'portfolio-item', 'post'),
        'title' => esc_html__( 'Footer', 'libero' ),
        'name' => 'footer_meta'
    )
);

    libero_mikado_create_meta_box_field(
        array(
            'name' => 'mkd_disable_footer_meta',
            'type' => 'yesno',
            'default_value' => 'no',
            'label' => esc_html__( 'Disable Footer for this Page', 'libero' ),
            'description' => esc_html__( 'Enabling this option will hide footer on this page', 'libero' ),
            'parent' => $footer_meta_box,
        )
    );