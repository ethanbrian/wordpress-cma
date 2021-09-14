<?php

/*** Audio Post Format ***/

$audio_post_format_meta_box = libero_mikado_create_meta_box(
	array(
		'scope' =>	array('post'),
		'title' => esc_html__( 'Audio Post Format', 'libero' ),
		'name' 	=> 'post_format_audio_meta'
	)
);

libero_mikado_create_meta_box_field(
	array(
		'name'        => 'mkd_post_audio_link_meta',
		'type'        => 'text',
		'label' => esc_html__( 'Link', 'libero' ),
		'description' => esc_html__( 'Enter audion link', 'libero' ),
		'parent'      => $audio_post_format_meta_box,

	)
);
