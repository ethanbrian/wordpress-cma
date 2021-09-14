<?php

$title_meta_box = libero_mikado_create_meta_box(
    array(
        'scope' => array('page', 'portfolio-item', 'post'),
        'title' => esc_html__( 'Title', 'libero' ),
        'name' => 'title_meta'
    )
);

    libero_mikado_create_meta_box_field(
        array(
            'name' => 'mkd_show_title_area_meta',
            'type' => 'select',
            'default_value' => '',
            'label' => esc_html__( 'Show Title Area', 'libero' ),
            'description' => esc_html__( 'Disabling this option will turn off page title area', 'libero' ),
            'parent' => $title_meta_box,
            'options' => array(
                '' => '',
                'no' => esc_html__('No', 'libero' ),
                'yes' => esc_html__('Yes', 'libero' )
            ),
            'args' => array(
                "dependence" => true,
                "hide" => array(
                    "" => "",
                    "no" => "#mkd_mkd_show_title_area_meta_container",
                    "yes" => ""
                ),
                "show" => array(
                    "" => "#mkd_mkd_show_title_area_meta_container",
                    "no" => "",
                    "yes" => "#mkd_mkd_show_title_area_meta_container"
                )
            )
        )
    );

    $show_title_area_meta_container = libero_mikado_add_admin_container(
        array(
            'parent' => $title_meta_box,
            'name' => 'mkd_show_title_area_meta_container',
            'hidden_property' => 'mkd_show_title_area_meta',
            'hidden_value' => 'no'
        )
    );

        libero_mikado_create_meta_box_field(
            array(
                'name' => 'mkd_title_area_type_meta',
                'type' => 'select',
                'default_value' => '',
                'label' => esc_html__( 'Title Area Type', 'libero' ),
                'description' => esc_html__( 'Choose title type', 'libero' ),
                'parent' => $show_title_area_meta_container,
                'options' => array(
                    '' => '',
                    'standard' => esc_html__('Standard', 'libero' ),
                    'breadcrumb' => esc_html__('Breadcrumb', 'libero' )
                ),
                'args' => array(
                    "dependence" => true,
                    "hide" => array(
                        "standard" => "",
                        "standard" => "",
                        "breadcrumb" => "#mkd_mkd_title_area_type_meta_container"
                    ),
                    "show" => array(
                        "" => "#mkd_mkd_title_area_type_meta_container",
                        "standard" => "#mkd_mkd_title_area_type_meta_container",
                        "breadcrumb" => ""
                    )
                )
            )
        );

        $title_area_type_meta_container = libero_mikado_add_admin_container(
            array(
                'parent' => $show_title_area_meta_container,
                'name' => 'mkd_title_area_type_meta_container',
                'hidden_property' => 'mkd_title_area_type_meta',
                'hidden_value' => '',
                'hidden_values' => array('breadcrumb'),
            )
        );

            libero_mikado_create_meta_box_field(
                array(
                    'name' => 'mkd_title_area_enable_breadcrumbs_meta',
                    'type' => 'select',
                    'default_value' => '',
                    'label' => esc_html__( 'Enable Breadcrumbs', 'libero' ),
                    'description' => esc_html__( 'This option will display Breadcrumbs in Title Area', 'libero' ),
                    'parent' => $title_area_type_meta_container,
                    'options' => array(
                        '' => '',
                        'no' => esc_html__('No', 'libero' ),
                        'yes' => esc_html__('Yes', 'libero' )
                    ),
                )
            );

        libero_mikado_create_meta_box_field(
            array(
                'name' => 'mkd_title_area_animation_meta',
                'type' => 'select',
                'default_value' => '',
                'label' => esc_html__( 'Animations', 'libero' ),
                'description' => esc_html__( 'Choose an animation for Title Area', 'libero' ),
                'parent' => $show_title_area_meta_container,
                'options' => array(
                    '' => '',
                    'no' => esc_html__('No Animation', 'libero' ),
                    'right-left' => esc_html__('Text right to left', 'libero' ),
                    'left-right' => esc_html__('Text left to right', 'libero' )
                )
            )
        );

        libero_mikado_create_meta_box_field(
            array(
                'name' => 'mkd_title_area_vertial_alignment_meta',
                'type' => 'select',
                'default_value' => '',
                'label' => esc_html__( 'Vertical Alignment', 'libero' ),
                'description' => esc_html__( 'Specify title vertical alignment', 'libero' ),
                'parent' => $show_title_area_meta_container,
                'options' => array(
                    '' => '',
                    'header_bottom' => esc_html__('From Bottom of Header', 'libero' ),
                    'window_top' => esc_html__('From Window Top', 'libero' )
                )
            )
        );

        libero_mikado_create_meta_box_field(
            array(
                'name' => 'mkd_title_area_content_alignment_meta',
                'type' => 'select',
                'default_value' => '',
                'label' => esc_html__( 'Horizontal Alignment', 'libero' ),
                'description' => esc_html__( 'Specify title horizontal alignment', 'libero' ),
                'parent' => $show_title_area_meta_container,
                'options' => array(
                    '' => '',
                    'left' => esc_html__('Left', 'libero' ),
                    'center' => esc_html__('Center', 'libero' ),
                    'right' => esc_html__('Right', 'libero' )
                )
            )
        );

        libero_mikado_create_meta_box_field(
            array(
                'name' => 'mkd_title_text_color_meta',
                'type' => 'color',
                'label' => esc_html__( 'Title Color', 'libero' ),
                'description' => esc_html__( 'Choose a color for title text', 'libero' ),
                'parent' => $show_title_area_meta_container
            )
        );

        libero_mikado_create_meta_box_field(
            array(
                'name' => 'mkd_title_text_fsize_meta',
                'type' => 'text',
                'default_value' => '',
                'label' => esc_html__( 'Title Font Size', 'libero' ),
                'description' => esc_html__( 'Enter title font size', 'libero' ),
                'parent' => $show_title_area_meta_container,
                'args' => array(
                    'col_width' => 3,
                    'suffix' => 'px'
                )
            )
        );

        libero_mikado_create_meta_box_field(
            array(
                'name' => 'mkd_title_breadcrumb_color_meta',
                'type' => 'color',
                'label' => esc_html__( 'Breadcrumb Color', 'libero' ),
                'description' => esc_html__( 'Choose a color for breadcrumb text', 'libero' ),
                'parent' => $show_title_area_meta_container
            )
        );

        libero_mikado_create_meta_box_field(
            array(
                'name' => 'mkd_title_area_background_color_meta',
                'type' => 'color',
                'label' => esc_html__( 'Background Color', 'libero' ),
                'description' => esc_html__( 'Choose a background color for Title Area', 'libero' ),
                'parent' => $show_title_area_meta_container
            )
        );

        libero_mikado_create_meta_box_field(
            array(
                'name' => 'mkd_hide_background_image_meta',
                'type' => 'yesno',
                'default_value' => 'no',
                'label' => esc_html__( 'Hide Background Image', 'libero' ),
                'description' => esc_html__( 'Enable this option to hide background image in Title Area', 'libero' ),
                'parent' => $show_title_area_meta_container,
                'args' => array(
                    "dependence" => true,
                    "dependence_hide_on_yes" => "#mkd_mkd_hide_background_image_meta_container",
                    "dependence_show_on_yes" => ""
                )
            )
        );

        $hide_background_image_meta_container = libero_mikado_add_admin_container(
            array(
                'parent' => $show_title_area_meta_container,
                'name' => 'mkd_hide_background_image_meta_container',
                'hidden_property' => 'mkd_hide_background_image_meta',
                'hidden_value' => 'yes'
            )
        );

        libero_mikado_create_meta_box_field(
            array(
                'name' => 'mkd_title_area_background_image_meta',
                'type' => 'image',
                'label' => esc_html__( 'Background Image', 'libero' ),
                'description' => esc_html__( 'Choose an Image for Title Area', 'libero' ),
                'parent' => $hide_background_image_meta_container
            )
        );

        libero_mikado_create_meta_box_field(
            array(
                'name' => 'mkd_title_area_background_as_pattern_meta',
                'type' => 'select',
                'default_value' => '',
                'label' => esc_html__( 'Background Image as Pattern', 'libero' ),
                'description' => esc_html__( 'Choose whether to make background image behave as pattern', 'libero' ),
                'parent' => $hide_background_image_meta_container,
                'options' => array(
                    "" => "",
                    "no" => esc_html__("No", 'libero' ),
                    "yes" => esc_html__("Yes", 'libero' )
                )
            )
        );

        libero_mikado_create_meta_box_field(
            array(
                'name' => 'mkd_title_area_background_image_responsive_meta',
                'type' => 'select',
                'default_value' => '',
                'label' => esc_html__( 'Background Responsive Image', 'libero' ),
                'description' => esc_html__( 'Enabling this option will make Title background image responsive', 'libero' ),
                'parent' => $hide_background_image_meta_container,
                'options' => array(
                    '' => '',
                    'no' => esc_html__('No', 'libero' ),
                    'yes' => esc_html__('Yes', 'libero' )
                ),
                'args' => array(
                    "dependence" => true,
                    "hide" => array(
                        "" => "",
                        "no" => "",
                        "yes" => "#mkd_mkd_title_area_background_image_responsive_meta_container, #mkd_mkd_title_area_height_meta"
                    ),
                    "show" => array(
                        "" => "#mkd_mkd_title_area_background_image_responsive_meta_container, #mkd_mkd_title_area_height_meta",
                        "no" => "#mkd_mkd_title_area_background_image_responsive_meta_container, #mkd_mkd_title_area_height_meta",
                        "yes" => ""
                    )
                )
            )
        );

        $title_area_background_image_responsive_meta_container = libero_mikado_add_admin_container(
            array(
                'parent' => $hide_background_image_meta_container,
                'name' => 'mkd_title_area_background_image_responsive_meta_container',
                'hidden_property' => 'mkd_title_area_background_image_responsive_meta',
                'hidden_value' => 'yes'
            )
        );

            libero_mikado_create_meta_box_field(
                array(
                    'name' => 'mkd_title_area_background_image_parallax_meta',
                    'type' => 'select',
                    'default_value' => '',
                    'label' => esc_html__( 'Background Image in Parallax', 'libero' ),
                    'description' => esc_html__( 'Enabling this option will make Title background image parallax', 'libero' ),
                    'parent' => $title_area_background_image_responsive_meta_container,
                    'options' => array(
                        '' => '',
                        'no' => esc_html__('No', 'libero' ),
                        'yes' => esc_html__('Yes', 'libero' ),
                        'yes_zoom' => esc_html__('Yes, with zoom out', 'libero' )
                    )
                )
            );

        libero_mikado_create_meta_box_field(array(
            'name' => 'mkd_title_area_height_meta',
            'type' => 'text',
            'label' => esc_html__( 'Height', 'libero' ),
            'description' => esc_html__( 'Set a height for Title Area', 'libero' ),
            'parent' => $show_title_area_meta_container,
            'args' => array(
                'col_width' => 2,
                'suffix' => 'px'
            )
        ));

        libero_mikado_create_meta_box_field(
            array(
                'name' => 'mkd_title_area_border_bottom_enable_meta',
                'type' => 'select',
                'default_value' => '',
                'label' => esc_html__( 'Enable Border Bottom', 'libero' ),
                'description' => esc_html__( 'Enable this option to add border bottom on title area', 'libero' ),
                'parent' => $show_title_area_meta_container,
                'options' => array(
                    '' => '',
                    'yes' => esc_html__('Yes', 'libero' ),
                    'no' => esc_html__('No', 'libero' )
                ),
                'args' => array(
                    "dependence" => true,
                    "show" => array("yes" => "#mkd_mkd_show_border_bottom_meta_container"),
                    "hide" => array("no" => "#mkd_mkd_show_border_bottom_meta_container")
                )
            )
        );

        $show_border_bottom_meta_container = libero_mikado_add_admin_container(
            array(
                'parent' => $show_title_area_meta_container,
                'name' => 'mkd_show_border_bottom_meta_container',
                'hidden_property' => 'mkd_title_area_border_bottom_enable_meta',
                'hidden_value' => 'no'
            )
        );

        libero_mikado_create_meta_box_field(
            array(
                'name' => 'mkd_title_area_border_bottom_meta',
                'type' => 'color',
                'default_value' => '',
                'label' => esc_html__( 'Border Bottom Color', 'libero' ),
                'description' => esc_html__( 'Choose border bottom color for title area', 'libero' ),
                'parent' => $show_border_bottom_meta_container
            )
        );

        libero_mikado_create_meta_box_field(array(
            'name' => 'mkd_title_area_subtitle_meta',
            'type' => 'text',
            'default_value' => '',
            'label' => esc_html__( 'Subtitle Text', 'libero' ),
            'description' => esc_html__( 'Enter your subtitle text', 'libero' ),
            'parent' => $show_title_area_meta_container,
            'args' => array(
                'col_width' => 6
            )
        ));

        libero_mikado_create_meta_box_field(
            array(
                'name' => 'mkd_subtitle_color_meta',
                'type' => 'color',
                'label' => esc_html__( 'Subtitle Color', 'libero' ),
                'description' => esc_html__( 'Choose a color for subtitle text', 'libero' ),
                'parent' => $show_title_area_meta_container
            )
        );


        libero_mikado_create_meta_box_field(
            array(
                'name' => 'mkd_title_icon_enable_meta',
                'type' => 'select',
                'default_value' => '',
                'label' => esc_html__( 'Enable Icon', 'libero' ),
                'description' => esc_html__( 'Enable icon to be rendered above title text', 'libero' ),
                'parent' => $show_title_area_meta_container,
                'options' => array(
                	'' => '',
                	'yes' => esc_html__('Yes', 'libero' ),
                	'no' => esc_html__('No', 'libero' )
            	),
                'args' => array(
                    "dependence" => true,
                    "show" => array("yes" => "#mkd_mkd_title_icon_meta_all_container"),
                    "hide" => array("no" => "#mkd_mkd_title_icon_meta_all_container")
                )
            )
        );

        $title_icon_meta_all_container = libero_mikado_add_admin_container(
            array(
                'parent' => $show_title_area_meta_container,
                'name' => 'mkd_title_icon_meta_all_container',
                'hidden_property' => 'mkd_title_icon_enable_meta',
                'hidden_value' => 'no'
            )
        );


//init icon pack hide and show array. It will be populated dinamically from collections array
		$title_above_icon_pack_hide_array = array();
		$title_above_icon_pack_show_array = array();

//do we have some collection added in collections array?
		if (is_array(libero_mikado_icon_collections()->iconCollections) && count(libero_mikado_icon_collections()->iconCollections)) {
			//get collections params array. It will contain values of 'param' property for each collection
			$title_above_icon_collections_params = libero_mikado_icon_collections()->getIconCollectionsParams();

			//foreach collection generate hide and show array
			foreach (libero_mikado_icon_collections()->iconCollections as $dep_collection_key => $dep_collection_object) {
				$title_above_icon_pack_hide_array[$dep_collection_key] = '';

				//we need to include only current collection in show string as it is the only one that needs to show
				$title_above_icon_pack_show_array[$dep_collection_key] = '#mkd_mkd_title_above_icon_' . $dep_collection_object->param . '_container';

				//for all collections param generate hide string
				foreach ($title_above_icon_collections_params as $title_above_icon_collections_param) {
					//we don't need to include current one, because it needs to be shown, not hidden
					if ($title_above_icon_collections_param !== $dep_collection_object->param) {
						$title_above_icon_pack_hide_array[$dep_collection_key] .= '#mkd_mkd_title_above_icon_' . $title_above_icon_collections_param . '_container,';
					}
				}

				//remove remaining ',' character
				$title_above_icon_pack_hide_array[$dep_collection_key] = rtrim($title_above_icon_pack_hide_array[$dep_collection_key], ',');
			}

		}

		libero_mikado_create_meta_box_field(
			array(
				'parent' => $title_icon_meta_all_container,
				'type' => 'selectblank',
				'name' => 'mkd_title_above_icon_pack_meta',
				'default_value' => '',
				'label' => esc_html__( 'Icon Pack', 'libero' ),
				'description' => esc_html__( 'Choose icon pack', 'libero' ),
				'options' => libero_mikado_icon_collections()->getIconCollections(),
				'args' => array(
					'dependence' => true,
					'hide' => $title_above_icon_pack_hide_array,
					'show' => $title_above_icon_pack_show_array
				)
			)
		);

		if (is_array(libero_mikado_icon_collections()->iconCollections) && count(libero_mikado_icon_collections()->iconCollections)) {
			//foreach icon collection we need to generate separate container that will have dependency set
			//it will have one field inside with icons dropdown
			foreach (libero_mikado_icon_collections()->iconCollections as $collection_key => $collection_object) {
				$icons_array = $collection_object->getIconsArray();

				//get icon collection keys (keys from collections array, e.g 'font_awesome', 'font_elegant' etc.)
				$icon_collections_keys = libero_mikado_icon_collections()->getIconCollectionsKeys();

				//unset current one, because it doesn't have to be included in dependency that hides icon container
				unset($icon_collections_keys[array_search($collection_key, $icon_collections_keys)]);

				$title_icon_hide_values = $icon_collections_keys;

				$title_icon_meta_container = libero_mikado_add_admin_container(
					array(
						'parent' => $title_icon_meta_all_container,
						'name' => 'mkd_title_above_icon_' . $collection_object->param . '_container',
						'hidden_property' => 'mkd_title_above_icon_pack_meta',
						'hidden_value' => '',
						'hidden_values' => $title_icon_hide_values
					)
				);

				libero_mikado_create_meta_box_field(
					array(
						'parent' => $title_icon_meta_container,
						'type' => 'select',
						'name' => 'mkd_title_above_icon_' . $collection_object->param.'_meta',
						'default_value' => '',
						'label' => esc_html__( 'Icon', 'libero' ),
						'description' => esc_html__( 'Choose icon to be displayed above title', 'libero' ),
						'options' => $icons_array,
					)
				);

			}

		}

		$group_page_title_icon_styles_meta = libero_mikado_add_admin_group(array(
			'name'			=> 'group_page_title_icon_styles_meta',
			'title' => esc_html__( 'Icon Style', 'libero' ),
			'description' => esc_html__( 'Define icon style', 'libero' ),
			'parent'		=> $title_icon_meta_all_container
		));

		$row_page_title_icon_styles_1_meta = libero_mikado_add_admin_row(array(
			'name'		=> 'row_page_title_icon_styles_1_meta',
			'parent'	=> $group_page_title_icon_styles_meta
		));

		libero_mikado_create_meta_box_field(
			array(
				'parent' => $row_page_title_icon_styles_1_meta,
				'type' => 'colorsimple',
				'name' => 'mkd_title_icon_color_meta',
				'default_value' => '',
				'label' => esc_html__( 'Color', 'libero' ),
			)
		);

		libero_mikado_create_meta_box_field(
			array(
				'parent' => $row_page_title_icon_styles_1_meta,
				'type' => 'colorsimple',
				'name' => 'mkd_title_icon_border_color_meta',
				'default_value' => '',
				'label' => esc_html__( 'Border Color', 'libero' ),
			)
		);

		$row_page_title_icon_styles_2_meta = libero_mikado_add_admin_row(array(
			'name'		=> 'row_page_title_icon_styles_2_meta',
			'parent'	=> $group_page_title_icon_styles_meta
		));

		libero_mikado_create_meta_box_field(
			array(
				'parent' => $row_page_title_icon_styles_2_meta,
				'type' => 'selectblanksimple',
				'name' => 'mkd_title_icon_size_meta',
				'default_value' => '',
				'label' => esc_html__( 'Size', 'libero' ),
				'options' => array(
					'' => esc_html__('Default', 'libero' ),
					'mkd-icon-tiny' => esc_html__('Tiny', 'libero' ),
					'mkd-icon-small' => esc_html__('Small', 'libero' ),
					'mkd-icon-medium' => esc_html__('Medium', 'libero' ),
					'mkd-icon-large' => esc_html__('Large', 'libero' ),
					'mkd-icon-huge' => esc_html__('Very Large', 'libero' ),
				)
			)
		);

		libero_mikado_create_meta_box_field(
			array(
				'parent' => $row_page_title_icon_styles_2_meta,
				'type' => 'textsimple',
				'name' => 'mkd_title_icon_custom_size_meta',
				'default_value' => '',
				'label' => esc_html__( 'Custom Size', 'libero' ),
				'args' => array(
					'suffix' => 'px',
				)
			)
		);

		libero_mikado_create_meta_box_field(
			array(
				'parent' => $row_page_title_icon_styles_2_meta,
				'type' => 'textsimple',
				'name' => 'mkd_title_icon_shape_size_meta',
				'default_value' => '',
				'label' => esc_html__( 'Shape Size', 'libero' ),
				'args' => array(
					'suffix' => 'px',
				)
			)
		);