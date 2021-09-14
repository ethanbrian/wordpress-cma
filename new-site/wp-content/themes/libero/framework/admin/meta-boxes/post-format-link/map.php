<?php

/*** Link Post Format ***/

$link_post_format_meta_box = libero_mikado_create_meta_box(
	array(
		'scope' => array('post'),
		'title' => esc_html__( 'Link Post Format', 'libero' ),
		'name' => 'post_format_link_meta'
	)
);

libero_mikado_create_meta_box_field(
	array(
		'name'        => 'mkd_post_link_link_meta',
		'type'        => 'text',
		'label' => esc_html__( 'Link', 'libero' ),
		'description' => esc_html__( 'Enter link', 'libero' ),
		'parent'      => $link_post_format_meta_box,

	)
);

