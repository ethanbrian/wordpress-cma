<?php

if ( ! function_exists('libero_mikado_sidearea_options_map') ) {

	function libero_mikado_sidearea_options_map() {

		libero_mikado_add_admin_page(
			array(
				'slug' => '_side_area_page',
				'title' => esc_html__( 'Side Area', 'libero' ),
				'icon' => 'icon_menu'
			)
		);

		$side_area_panel = libero_mikado_add_admin_panel(
			array(
				'title' => esc_html__( 'Side Area', 'libero' ),
				'name' => 'side_area',
				'page' => '_side_area_page'
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $side_area_panel,
				'type' => 'select',
				'name' => 'side_area_type',
				'default_value' => 'side-menu-slide-from-right',
				'label' => esc_html__( 'Side Area Type', 'libero' ),
				'description' => esc_html__( 'Choose a type of Side Area', 'libero' ),
				'options' => array(
					'side-menu-slide-from-right' => esc_html__('Slide from Right Over Content', 'libero' ),
					'side-menu-slide-with-content' => esc_html__('Slide from Right With Content', 'libero' ),
					'side-area-uncovered-from-content' => esc_html__('Side Area Uncovered from Content', 'libero' )
				),
				'args' => array(
					'dependence' => true,
					'hide' => array(
						'side-menu-slide-from-right' => '#mkd_side_area_slide_with_content_container',
						'side-menu-slide-with-content' => '#mkd_side_area_width_container',
						'side-area-uncovered-from-content' => '#mkd_side_area_width_container, #mkd_side_area_slide_with_content_container'
					),
					'show' => array(
						'side-menu-slide-from-right' => '#mkd_side_area_width_container',
						'side-menu-slide-with-content' => '#mkd_side_area_slide_with_content_container',
						'side-area-uncovered-from-content' => ''
					)
				)
			)
		);

		$side_area_width_container = libero_mikado_add_admin_container(
			array(
				'parent' => $side_area_panel,
				'name' => 'side_area_width_container',
				'hidden_property' => 'side_area_type',
				'hidden_value' => '',
				'hidden_values' => array(
					'side-menu-slide-with-content',
					'side-area-uncovered-from-content'
				)
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $side_area_width_container,
				'type' => 'text',
				'name' => 'side_area_width',
				'default_value' => '',
				'label' => esc_html__( 'Side Area Width', 'libero' ),
				'description' => esc_html__( 'Enter a width for Side Area', 'libero' ),
				'args' => array(
					'col_width' => 3,
					'suffix' => '%'
				)
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $side_area_width_container,
				'type' => 'color',
				'name' => 'side_area_content_overlay_color',
				'default_value' => '',
				'label' => esc_html__( 'Content Overlay Background Color', 'libero' ),
				'description' => esc_html__( 'Choose a background color for a content overlay', 'libero' ),
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $side_area_width_container,
				'type' => 'text',
				'name' => 'side_area_content_overlay_opacity',
				'default_value' => '',
				'label' => esc_html__( 'Content Overlay Background Transparency', 'libero' ),
				'description' => esc_html__( 'Choose a transparency for the content overlay background color (0 = fully transparent, 1 = opaque)', 'libero' ),
				'args' => array(
					'col_width' => 3
				)
			)
		);

		$side_area_slide_with_content_container = libero_mikado_add_admin_container(
			array(
				'parent' => $side_area_panel,
				'name' => 'side_area_slide_with_content_container',
				'hidden_property' => 'side_area_type',
				'hidden_value' => '',
				'hidden_values' => array(
					'side-menu-slide-from-right',
					'side-area-uncovered-from-content'
				)
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $side_area_slide_with_content_container,
				'type' => 'select',
				'name' => 'side_area_slide_with_content_width',
				'default_value' => 'width-470',
				'label' => esc_html__( 'Side Area Width', 'libero' ),
				'description' => esc_html__( 'Choose width for Side Area', 'libero' ),
				'options' => array(
					'width-270' => '270px',
					'width-370' => '370px',
					'width-470' => '470px'
				)
			)
		);

//init icon pack hide and show array. It will be populated dinamically from collections array
		$side_area_icon_pack_hide_array = array();
		$side_area_icon_pack_show_array = array();

//do we have some collection added in collections array?
		if (is_array(libero_mikado_icon_collections()->iconCollections) && count(libero_mikado_icon_collections()->iconCollections)) {
			//get collections params array. It will contain values of 'param' property for each collection
			$side_area_icon_collections_params = libero_mikado_icon_collections()->getIconCollectionsParams();

			//foreach collection generate hide and show array
			foreach (libero_mikado_icon_collections()->iconCollections as $dep_collection_key => $dep_collection_object) {
				$side_area_icon_pack_hide_array[$dep_collection_key] = '';

				//we need to include only current collection in show string as it is the only one that needs to show
				$side_area_icon_pack_show_array[$dep_collection_key] = '#mkd_side_area_icon_' . $dep_collection_object->param . '_container';

				//for all collections param generate hide string
				foreach ($side_area_icon_collections_params as $side_area_icon_collections_param) {
					//we don't need to include current one, because it needs to be shown, not hidden
					if ($side_area_icon_collections_param !== $dep_collection_object->param) {
						$side_area_icon_pack_hide_array[$dep_collection_key] .= '#mkd_side_area_icon_' . $side_area_icon_collections_param . '_container,';
					}
				}

				//remove remaining ',' character
				$side_area_icon_pack_hide_array[$dep_collection_key] = rtrim($side_area_icon_pack_hide_array[$dep_collection_key], ',');
			}

		}

		$side_area_icon_style_group = libero_mikado_add_admin_group(
			array(
				'parent' => $side_area_panel,
				'name' => 'side_area_icon_style_group',
				'title' => esc_html__( 'Side Area Icon Style', 'libero' ),
				'description' => esc_html__( 'Define styles for Side Area icon', 'libero' )
			)
		);

		$side_area_icon_style_row1 = libero_mikado_add_admin_row(
			array(
				'parent'		=> $side_area_icon_style_group,
				'name'			=> 'side_area_icon_style_row1'
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $side_area_icon_style_row1,
				'type' => 'colorsimple',
				'name' => 'side_area_icon_color',
				'default_value' => '',
				'label' => esc_html__( 'Color', 'libero' ),
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $side_area_icon_style_row1,
				'type' => 'colorsimple',
				'name' => 'side_area_icon_hover_color',
				'default_value' => '',
				'label' => esc_html__( 'Hover Color', 'libero' ),
			)
		);

		$icon_spacing_group = libero_mikado_add_admin_group(
			array(
				'parent' => $side_area_panel,
				'name' => 'icon_spacing_group',
				'title' => esc_html__( 'Side Area Icon Spacing', 'libero' ),
				'description' => esc_html__( 'Define padding and margin for side area icon', 'libero' )
			)
		);

		$icon_spacing_row = libero_mikado_add_admin_row(
			array(
				'parent'		=> $icon_spacing_group,
				'name'			=> 'icon_spancing_row',
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $icon_spacing_row,
				'type' => 'textsimple',
				'name' => 'side_area_icon_padding_left',
				'default_value' => '',
				'label' => esc_html__( 'Padding Left', 'libero' ),
				'args' => array(
					'suffix' => 'px'
				)
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $icon_spacing_row,
				'type' => 'textsimple',
				'name' => 'side_area_icon_padding_right',
				'default_value' => '',
				'label' => esc_html__( 'Padding Right', 'libero' ),
				'args' => array(
					'suffix' => 'px'
				)
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $icon_spacing_row,
				'type' => 'textsimple',
				'name' => 'side_area_icon_margin_left',
				'default_value' => '',
				'label' => esc_html__( 'Margin Left', 'libero' ),
				'args' => array(
					'suffix' => 'px'
				)
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $icon_spacing_row,
				'type' => 'textsimple',
				'name' => 'side_area_icon_margin_right',
				'default_value' => '',
				'label' => esc_html__( 'Margin Right', 'libero' ),
				'args' => array(
					'suffix' => 'px'
				)
			)
		);

        libero_mikado_add_admin_field(
            array(
                'parent'		=> $side_area_panel,
                'type'			=> 'yesno',
                'name'			=> 'enable_side_area_icon_text',
                'default_value'	=> 'yes',
                'label' => esc_html__( 'Enable Side Area Icon Text', 'libero' ),
                'description' => esc_html__( "Enable this option to show Menu text next to search icon in header", 'libero' ),
                'args'			=> array(
                    'dependence' => true,
                    'dependence_hide_on_yes' => '',
                    'dependence_show_on_yes' => '#mkd_enable_side_area_icon_text_container'
                )
            )
        );

        $enable_side_area_icon_text_container = libero_mikado_add_admin_container(
            array(
                'parent'			=> $side_area_panel,
                'name'				=> 'enable_side_area_icon_text_container',
                'hidden_property'	=> 'enable_side_area_icon_text',
                'hidden_value'		=> 'no'
            )
        );

        $enable_side_area_icon_text_group = libero_mikado_add_admin_group(
            array(
                'parent'	=> $enable_side_area_icon_text_container,
                'title' => esc_html__( 'Side Area Icon Text', 'libero' ),
                'name'		=> 'enable_side_area_icon_text_group',
                'description' => esc_html__( 'Define Style for Side Area Icon Text', 'libero' )
            )
        );

        $enable_side_area_icon_text_row = libero_mikado_add_admin_row(
            array(
                'parent'	=> $enable_side_area_icon_text_group,
                'name'		=> 'enable_side_area_icon_text_row'
            )
        );

        libero_mikado_add_admin_field(
            array(
                'parent'		=> $enable_side_area_icon_text_row,
                'type'			=> 'colorsimple',
                'name'			=> 'side_area_icon_text_color',
                'label' => esc_html__( 'Text Color', 'libero' ),
                'default_value'	=> ''
            )
        );

        libero_mikado_add_admin_field(
            array(
                'parent'		=> $enable_side_area_icon_text_row,
                'type'			=> 'colorsimple',
                'name'			=> 'side_area_icon_text_color_hover',
                'label' => esc_html__( 'Text Hover Color', 'libero' ),
                'default_value'	=> ''
            )
        );

        libero_mikado_add_admin_field(
            array(
                'parent'		=> $enable_side_area_icon_text_row,
                'type'			=> 'textsimple',
                'name'			=> 'side_area_icon_text_fontsize',
                'label' => esc_html__( 'Font Size', 'libero' ),
                'default_value'	=> '',
                'args'			=> array(
                    'suffix'	=> 'px'
                )
            )
        );

        libero_mikado_add_admin_field(
            array(
                'parent'		=> $enable_side_area_icon_text_row,
                'type'			=> 'textsimple',
                'name'			=> 'side_area_icon_text_lineheight',
                'label' => esc_html__( 'Line Height', 'libero' ),
                'default_value'	=> '',
                'args'			=> array(
                    'suffix'	=> 'px'
                )
            )
        );

        $enable_side_area_icon_text_row2 = libero_mikado_add_admin_row(
            array(
                'parent'	=> $enable_side_area_icon_text_group,
                'name'		=> 'enable_side_area_icon_text_row2',
                'next'		=> true
            )
        );

        libero_mikado_add_admin_field(
            array(
                'parent'		=> $enable_side_area_icon_text_row2,
                'type'			=> 'selectblanksimple',
                'name'			=> 'side_area_icon_text_texttransform',
                'label' => esc_html__( 'Text Transform', 'libero' ),
                'default_value'	=> '',
                'options'		=> libero_mikado_get_text_transform_array()
            )
        );

        libero_mikado_add_admin_field(
            array(
                'parent'		=> $enable_side_area_icon_text_row2,
                'type'			=> 'fontsimple',
                'name'			=> 'side_area_icon_text_google_fonts',
                'label' => esc_html__( 'Font Family', 'libero' ),
                'default_value'	=> '-1',
            )
        );

        libero_mikado_add_admin_field(
            array(
                'parent'		=> $enable_side_area_icon_text_row2,
                'type'			=> 'selectblanksimple',
                'name'			=> 'side_area_icon_text_fontstyle',
                'label' => esc_html__( 'Font Style', 'libero' ),
                'default_value'	=> '',
                'options'		=> libero_mikado_get_font_style_array(),
            )
        );

        libero_mikado_add_admin_field(
            array(
                'parent'		=> $enable_side_area_icon_text_row2,
                'type'			=> 'selectblanksimple',
                'name'			=> 'side_area_icon_text_fontweight',
                'label' => esc_html__( 'Font Weight', 'libero' ),
                'default_value'	=> '',
                'options'		=> libero_mikado_get_font_weight_array(),
            )
        );

        $enable_side_area_icon_text_row3 = libero_mikado_add_admin_row(
            array(
                'parent'	=> $enable_side_area_icon_text_group,
                'name'		=> 'enable_side_area_icon_text_row3',
                'next'		=> true
            )
        );

        libero_mikado_add_admin_field(
            array(
                'parent'		=> $enable_side_area_icon_text_row3,
                'type'			=> 'textsimple',
                'name'			=> 'side_area_icon_text_letterspacing',
                'label' => esc_html__( 'Letter Spacing', 'libero' ),
                'default_value'	=> '',
                'args'			=> array(
                    'suffix'	=> 'px'
                )
            )
        );

		libero_mikado_add_admin_field(
			array(
				'parent' => $side_area_panel,
				'type' => 'selectblank',
				'name' => 'side_area_aligment',
				'default_value' => '',
				'label' => esc_html__( 'Text Aligment', 'libero' ),
				'description' => esc_html__( 'Choose text aligment for side area', 'libero' ),
				'options' => array(
					'center' => esc_html__('Center', 'libero' ),
					'left' => esc_html__('Left', 'libero' ),
					'right' => esc_html__('Right', 'libero' )
				)
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $side_area_panel,
				'type' => 'text',
				'name' => 'side_area_title',
				'default_value' => '',
				'label' => esc_html__( 'Side Area Title', 'libero' ),
				'description' => esc_html__( 'Enter a title to appear in Side Area', 'libero' ),
				'args' => array(
					'col_width' => 3,
				)
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $side_area_panel,
				'type' => 'color',
				'name' => 'side_area_background_color',
				'default_value' => '',
				'label' => esc_html__( 'Background Color', 'libero' ),
				'description' => esc_html__( 'Choose a background color for Side Area', 'libero' ),
			)
		);

        libero_mikado_add_admin_field(
            array(
                'parent' => $side_area_panel,
                'type' => 'image',
                'name' => 'side_area_background_image',
                'default_value' => '',
                'label' => esc_html__( 'Background Image', 'libero' ),
                'description' => esc_html__( 'Choose a background image for Side Area', 'libero' ),
            )
        );

		$padding_group = libero_mikado_add_admin_group(
			array(
				'parent' => $side_area_panel,
				'name' => 'padding_group',
				'title' => esc_html__( 'Padding', 'libero' ),
				'description' => esc_html__( 'Define padding for Side Area', 'libero' )
			)
		);

		$padding_row = libero_mikado_add_admin_row(
			array(
				'parent' => $padding_group,
				'name' => 'padding_row',
				'next' => true
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $padding_row,
				'type' => 'textsimple',
				'name' => 'side_area_padding_top',
				'default_value' => '',
				'label' => esc_html__( 'Top Padding', 'libero' ),
				'args' => array(
					'suffix' => 'px'
				)
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $padding_row,
				'type' => 'textsimple',
				'name' => 'side_area_padding_right',
				'default_value' => '',
				'label' => esc_html__( 'Right Padding', 'libero' ),
				'args' => array(
					'suffix' => 'px'
				)
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $padding_row,
				'type' => 'textsimple',
				'name' => 'side_area_padding_bottom',
				'default_value' => '',
				'label' => esc_html__( 'Bottom Padding', 'libero' ),
				'args' => array(
					'suffix' => 'px'
				)
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $padding_row,
				'type' => 'textsimple',
				'name' => 'side_area_padding_left',
				'default_value' => '',
				'label' => esc_html__( 'Left Padding', 'libero' ),
				'args' => array(
					'suffix' => 'px'
				)
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $side_area_panel,
				'type' => 'select',
				'name' => 'side_area_close_icon',
				'default_value' => 'light',
				'label' => esc_html__( 'Close Icon Style', 'libero' ),
				'description' => esc_html__( 'Choose a type of close icon', 'libero' ),
				'options' => array(
					'light' => esc_html__('Light', 'libero' ),
					'dark' => esc_html__('Dark', 'libero' )
				)
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $side_area_panel,
				'type' => 'text',
				'name' => 'side_area_close_icon_size',
				'default_value' => '',
				'label' => esc_html__( 'Close Icon Size', 'libero' ),
				'description' => esc_html__( 'Define close icon size', 'libero' ),
				'args' => array(
					'col_width' => 3,
					'suffix' => 'px'
				)
			)
		);

		$title_group = libero_mikado_add_admin_group(
			array(
				'parent' => $side_area_panel,
				'name' => 'title_group',
				'title' => esc_html__( 'Title', 'libero' ),
				'description' => esc_html__( 'Define Style for Side Area title', 'libero' )
			)
		);

		$title_row_1 = libero_mikado_add_admin_row(
			array(
				'parent' => $title_group,
				'name' => 'title_row_1',
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $title_row_1,
				'type' => 'colorsimple',
				'name' => 'side_area_title_color',
				'default_value' => '',
				'label' => esc_html__( 'Text Color', 'libero' ),
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $title_row_1,
				'type' => 'textsimple',
				'name' => 'side_area_title_fontsize',
				'default_value' => '',
				'label' => esc_html__( 'Font Size', 'libero' ),
				'args' => array(
					'suffix' => 'px'
				)
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $title_row_1,
				'type' => 'textsimple',
				'name' => 'side_area_title_lineheight',
				'default_value' => '',
				'label' => esc_html__( 'Line Height', 'libero' ),
				'args' => array(
					'suffix' => 'px'
				)
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $title_row_1,
				'type' => 'selectblanksimple',
				'name' => 'side_area_title_texttransform',
				'default_value' => '',
				'label' => esc_html__( 'Text Transform', 'libero' ),
				'options' => libero_mikado_get_text_transform_array()
			)
		);

		$title_row_2 = libero_mikado_add_admin_row(
			array(
				'parent' => $title_group,
				'name' => 'title_row_2',
				'next' => true
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $title_row_2,
				'type' => 'fontsimple',
				'name' => 'side_area_title_google_fonts',
				'default_value' => '-1',
				'label' => esc_html__( 'Font Family', 'libero' ),
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $title_row_2,
				'type' => 'selectblanksimple',
				'name' => 'side_area_title_fontstyle',
				'default_value' => '',
				'label' => esc_html__( 'Font Style', 'libero' ),
				'options' => libero_mikado_get_font_style_array()
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $title_row_2,
				'type' => 'selectblanksimple',
				'name' => 'side_area_title_fontweight',
				'default_value' => '',
				'label' => esc_html__( 'Font Weight', 'libero' ),
				'options' => libero_mikado_get_font_weight_array()
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $title_row_2,
				'type' => 'textsimple',
				'name' => 'side_area_title_letterspacing',
				'default_value' => '',
				'label' => esc_html__( 'Letter Spacing', 'libero' ),
				'args' => array(
					'suffix' => 'px'
				)
			)
		);


		$text_group = libero_mikado_add_admin_group(
			array(
				'parent' => $side_area_panel,
				'name' => 'text_group',
				'title' => esc_html__( 'Text', 'libero' ),
				'description' => esc_html__( 'Define Style for Side Area text', 'libero' )
			)
		);

		$text_row_1 = libero_mikado_add_admin_row(
			array(
				'parent' => $text_group,
				'name' => 'text_row_1',
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $text_row_1,
				'type' => 'colorsimple',
				'name' => 'side_area_text_color',
				'default_value' => '',
				'label' => esc_html__( 'Text Color', 'libero' ),
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $text_row_1,
				'type' => 'textsimple',
				'name' => 'side_area_text_fontsize',
				'default_value' => '',
				'label' => esc_html__( 'Font Size', 'libero' ),
				'args' => array(
					'suffix' => 'px'
				)
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $text_row_1,
				'type' => 'textsimple',
				'name' => 'side_area_text_lineheight',
				'default_value' => '',
				'label' => esc_html__( 'Line Height', 'libero' ),
				'args' => array(
					'suffix' => 'px'
				)
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $text_row_1,
				'type' => 'selectblanksimple',
				'name' => 'side_area_text_texttransform',
				'default_value' => '',
				'label' => esc_html__( 'Text Transform', 'libero' ),
				'options' => libero_mikado_get_text_transform_array()
			)
		);

		$text_row_2 = libero_mikado_add_admin_row(
			array(
				'parent' => $text_group,
				'name' => 'text_row_2',
				'next' => true
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $text_row_2,
				'type' => 'fontsimple',
				'name' => 'side_area_text_google_fonts',
				'default_value' => '-1',
				'label' => esc_html__( 'Font Family', 'libero' ),
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $text_row_2,
				'type' => 'fontsimple',
				'name' => 'side_area_text_google_fonts',
				'default_value' => '-1',
				'label' => esc_html__( 'Font Family', 'libero' ),
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $text_row_2,
				'type' => 'selectblanksimple',
				'name' => 'side_area_text_fontstyle',
				'default_value' => '',
				'label' => esc_html__( 'Font Style', 'libero' ),
				'options' => libero_mikado_get_font_style_array()
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $text_row_2,
				'type' => 'selectblanksimple',
				'name' => 'side_area_text_fontweight',
				'default_value' => '',
				'label' => esc_html__( 'Font Weight', 'libero' ),
				'options' => libero_mikado_get_font_weight_array()
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $text_row_2,
				'type' => 'textsimple',
				'name' => 'side_area_text_letterspacing',
				'default_value' => '',
				'label' => esc_html__( 'Letter Spacing', 'libero' ),
				'args' => array(
					'suffix' => 'px'
				)
			)
		);

		$widget_links_group = libero_mikado_add_admin_group(
			array(
				'parent' => $side_area_panel,
				'name' => 'widget_links_group',
				'title' => esc_html__( 'Link Style', 'libero' ),
				'description' => esc_html__( 'Define styles for Side Area widget links', 'libero' )
			)
		);

		$widget_links_row_1 = libero_mikado_add_admin_row(
			array(
				'parent' => $widget_links_group,
				'name' => 'widget_links_row_1',
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $widget_links_row_1,
				'type' => 'colorsimple',
				'name' => 'sidearea_link_color',
				'default_value' => '',
				'label' => esc_html__( 'Text Color', 'libero' ),
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $widget_links_row_1,
				'type' => 'textsimple',
				'name' => 'sidearea_link_font_size',
				'default_value' => '',
				'label' => esc_html__( 'Font Size', 'libero' ),
				'args' => array(
					'suffix' => 'px'
				)
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $widget_links_row_1,
				'type' => 'textsimple',
				'name' => 'sidearea_link_line_height',
				'default_value' => '',
				'label' => esc_html__( 'Line Height', 'libero' ),
				'args' => array(
					'suffix' => 'px'
				)
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $widget_links_row_1,
				'type' => 'selectblanksimple',
				'name' => 'sidearea_link_text_transform',
				'default_value' => '',
				'label' => esc_html__( 'Text Transform', 'libero' ),
				'options' => libero_mikado_get_text_transform_array()
			)
		);

		$widget_links_row_2 = libero_mikado_add_admin_row(
			array(
				'parent' => $widget_links_group,
				'name' => 'widget_links_row_2',
				'next' => true
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $widget_links_row_2,
				'type' => 'fontsimple',
				'name' => 'sidearea_link_font_family',
				'default_value' => '-1',
				'label' => esc_html__( 'Font Family', 'libero' ),
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $widget_links_row_2,
				'type' => 'selectblanksimple',
				'name' => 'sidearea_link_font_style',
				'default_value' => '',
				'label' => esc_html__( 'Font Style', 'libero' ),
				'options' => libero_mikado_get_font_style_array()
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $widget_links_row_2,
				'type' => 'selectblanksimple',
				'name' => 'sidearea_link_font_weight',
				'default_value' => '',
				'label' => esc_html__( 'Font Weight', 'libero' ),
				'options' => libero_mikado_get_font_weight_array()
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $widget_links_row_2,
				'type' => 'textsimple',
				'name' => 'sidearea_link_letter_spacing',
				'default_value' => '',
				'label' => esc_html__( 'Letter Spacing', 'libero' ),
				'args' => array(
					'suffix' => 'px'
				)
			)
		);

		$widget_links_row_3 = libero_mikado_add_admin_row(
			array(
				'parent' => $widget_links_group,
				'name' => 'widget_links_row_3',
				'next' => true
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $widget_links_row_3,
				'type' => 'colorsimple',
				'name' => 'sidearea_link_hover_color',
				'default_value' => '',
				'label' => esc_html__( 'Hover Color', 'libero' ),
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $side_area_panel,
				'type' => 'yesno',
				'name' => 'side_area_enable_bottom_border',
				'default_value' => 'no',
				'label' => esc_html__( 'Border Bottom on Elements', 'libero' ),
				'description' => esc_html__( 'Enable border bottom on elements in side area', 'libero' ),
				'args' => array(
					'dependence' => true,
					'dependence_hide_on_yes' => '',
					'dependence_show_on_yes' => '#mkd_side_area_bottom_border_container'
				)
			)
		);

		$side_area_bottom_border_container = libero_mikado_add_admin_container(
			array(
				'parent' => $side_area_panel,
				'name' => 'side_area_bottom_border_container',
				'hidden_property' => 'side_area_enable_bottom_border',
				'hidden_value' => 'no'
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $side_area_bottom_border_container,
				'type' => 'color',
				'name' => 'side_area_bottom_border_color',
				'default_value' => '',
				'label' => esc_html__( 'Border Bottom Color', 'libero' ),
				'description' => esc_html__( 'Choose color for border bottom on elements in sidearea', 'libero' )
			)
		);

	}

	add_action('libero_mikado_options_map', 'libero_mikado_sidearea_options_map', 4);

}