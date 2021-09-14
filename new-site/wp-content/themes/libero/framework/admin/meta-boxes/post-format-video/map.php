<?php

/*** Video Post Format ***/

$video_post_format_meta_box = libero_mikado_create_meta_box(
	array(
		'scope' =>	array('post'),
		'title' => esc_html__( 'Video Post Format', 'libero' ),
		'name' 	=> 'post_format_video_meta'
	)
);

libero_mikado_create_meta_box_field(
	array(
		'name'        => 'mkd_video_type_meta',
		'type'        => 'select',
		'label' => esc_html__( 'Video Type', 'libero' ),
		'description' => esc_html__( 'Choose video type', 'libero' ),
		'parent'      => $video_post_format_meta_box,
		'default_value' => 'youtube',
		'options'     => array(
			'youtube' => esc_html__('Youtube', 'libero' ),
			'vimeo' => esc_html__('Vimeo', 'libero' ),
			'self' => esc_html__('Self Hosted', 'libero' )
		),
		'args' => array(
		'dependence' => true,
		'hide' => array(
			'youtube' => '#mkd_mkd_video_self_hosted_container',
			'vimeo' => '#mkd_mkd_video_self_hosted_container',
			'self' => '#mkd_mkd_video_embedded_container'
		),
		'show' => array(
			'youtube' => '#mkd_mkd_video_embedded_container',
			'vimeo' => '#mkd_mkd_video_embedded_container',
			'self' => '#mkd_mkd_video_self_hosted_container')
	)
	)
);

$mkd_video_embedded_container = libero_mikado_add_admin_container(
	array(
		'parent' => $video_post_format_meta_box,
		'name' => 'mkd_video_embedded_container',
		'hidden_property' => 'mkd_video_type_meta',
		'hidden_value' => 'self'
	)
);

$mkd_video_self_hosted_container = libero_mikado_add_admin_container(
	array(
		'parent' => $video_post_format_meta_box,
		'name' => 'mkd_video_self_hosted_container',
		'hidden_property' => 'mkd_video_type_meta',
		'hidden_values' => array('youtube', 'vimeo')
	)
);



libero_mikado_create_meta_box_field(
	array(
		'name'        => 'mkd_post_video_id_meta',
		'type'        => 'text',
		'label' => esc_html__( 'Video ID', 'libero' ),
		'description' => esc_html__( 'Enter Video ID', 'libero' ),
		'parent'      => $mkd_video_embedded_container,

	)
);


libero_mikado_create_meta_box_field(
	array(
		'name'        => 'mkd_post_video_image_meta',
		'type'        => 'image',
		'label' => esc_html__( 'Video Image', 'libero' ),
		'description' => esc_html__( 'Upload video image', 'libero' ),
		'parent'      => $mkd_video_self_hosted_container,

	)
);

libero_mikado_create_meta_box_field(
	array(
		'name'        => 'mkd_post_video_webm_link_meta',
		'type'        => 'text',
		'label' => esc_html__( 'Video WEBM', 'libero' ),
		'description' => esc_html__( 'Enter video URL for WEBM format', 'libero' ),
		'parent'      => $mkd_video_self_hosted_container,

	)
);

libero_mikado_create_meta_box_field(
	array(
		'name'        => 'mkd_post_video_mp4_link_meta',
		'type'        => 'text',
		'label' => esc_html__( 'Video MP4', 'libero' ),
		'description' => esc_html__( 'Enter video URL for MP4 format', 'libero' ),
		'parent'      => $mkd_video_self_hosted_container,

	)
);

libero_mikado_create_meta_box_field(
	array(
		'name'        => 'mkd_post_video_ogv_link_meta',
		'type'        => 'text',
		'label' => esc_html__( 'Video OGV', 'libero' ),
		'description' => esc_html__( 'Enter video URL for OGV format', 'libero' ),
		'parent'      => $mkd_video_self_hosted_container,

	)
);