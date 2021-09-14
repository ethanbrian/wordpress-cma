<?php

if ( ! function_exists('libero_mikado_search_options_map') ) {

	function libero_mikado_search_options_map() {

		libero_mikado_add_admin_page(
			array(
				'slug' => '_search_page',
				'title' => esc_html__( 'Search', 'libero' ),
				'icon' => 'icon_search'
			)
		);

		$search_panel = libero_mikado_add_admin_panel(
			array(
				'title' => esc_html__( 'Search', 'libero' ),
				'name' => 'search',
				'page' => '_search_page'
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $search_panel,
				'type'			=> 'select',
				'name'			=> 'search_type',
				'default_value'	=> 'search-covers-header',
				'label' => esc_html__( 'Select Search Type', 'libero' ),
				'description' => esc_html__( 'Does not work with transparent header', 'libero' ),
				'options' 		=> array(
					'search-slides-from-header-bottom' => esc_html__('Slide From Header Bottom', 'libero' ),
					'search-covers-header' => esc_html__('Search Covers Header', 'libero' ),
					'fullscreen-search' => esc_html__('Fullscreen Search', 'libero' ),
					'search-slides-from-window-top' => esc_html__('Slide from Window Top', 'libero' )
				),
				'args'			=> array(
					'dependence'=> true,
					'hide'		=> array(
						'search-slides-from-header-bottom' => '#mkd_search_animation_container',
						'search-covers-header' => '#mkd_search_height_container, #mkd_search_animation_container',
						'fullscreen-search' => '#mkd_search_height_container',
						'search-slides-from-window-top' => '#mkd_search_height_container, #mkd_search_animation_container'
					),
					'show'		=> array(
						'search-slides-from-header-bottom' => '#mkd_search_height_container',
						'search-covers-header' => '',
						'fullscreen-search' => '#mkd_search_animation_container',
						'search-slides-from-window-top' => ''
					)
				)
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $search_panel,
				'type'			=> 'select',
				'name'			=> 'search_icon_pack',
				'default_value'	=> 'ion_icons',
				'label' => esc_html__( 'Search Icon Pack', 'libero' ),
				'description' => esc_html__( 'Choose icon pack for search icon', 'libero' ),
				'options'		=> libero_mikado_icon_collections()->getIconCollectionsExclude(array('linea_icons', 'dripicons'))
			)
		);

		$search_height_container = libero_mikado_add_admin_container(
			array(
				'parent'			=> $search_panel,
				'name'				=> 'search_height_container',
				'hidden_property'	=> 'search_type',
				'hidden_value'		=> '',
				'hidden_values'		=> array(
					'search-covers-header',
					'fullscreen-search',
					'search-slides-from-window-top'
				)
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $search_height_container,
				'type'			=> 'text',
				'name'			=> 'search_height',
				'default_value'	=> '',
				'label' => esc_html__( 'Search bar height', 'libero' ),
				'description' => esc_html__( 'Set search bar height', 'libero' ),
				'args'			=> array(
					'col_width' => 3,
					'suffix'	=> 'px'
				)
			)
		);

		$search_animation_container = libero_mikado_add_admin_container(
			array(
				'parent'			=> $search_panel,
				'name'				=> 'search_animation_container',
				'hidden_property'	=> 'search_type',
				'hidden_value'		=> '',
				'hidden_values'		=> array(
					'search-covers-header',
					'search-slides-from-header-bottom',
					'search-slides-from-window-top'
				)
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $search_animation_container,
				'type'			=> 'select',
				'name'			=> 'search_animation',
				'default_value'	=> 'search-fade',
				'label' => esc_html__( 'Fullscreen Search Overlay Animation', 'libero' ),
				'description' => esc_html__( 'Choose animation for fullscreen search overlay', 'libero' ),
				'options'		=> array(
					'search-fade'			=> esc_html__('Fade', 'libero' ),
					'search-from-circle'	=> esc_html__('Circle appear', 'libero' )
				)
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $search_panel,
				'type'			=> 'yesno',
				'name'			=> 'search_in_grid',
				'default_value'	=> 'yes',
				'label' => esc_html__( 'Search area in grid', 'libero' ),
				'description' => esc_html__( 'Set search area to be in grid', 'libero' ),
			)
		);

		libero_mikado_add_admin_section_title(
			array(
				'parent' 	=> $search_panel,
				'name'		=> 'initial_header_icon_title',
				'title' => esc_html__( 'Initial Search Icon in Header', 'libero' )
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $search_panel,
				'type'			=> 'text',
				'name'			=> 'header_search_icon_size',
				'default_value'	=> '',
				'label' => esc_html__( 'Icon Size', 'libero' ),
				'description' => esc_html__( 'Set size for icon', 'libero' ),
				'args'			=> array(
					'col_width' => 3,
					'suffix'	=> 'px'
				)
			)
		);

		$search_icon_color_group = libero_mikado_add_admin_group(
			array(
				'parent'	=> $search_panel,
				'title' => esc_html__( 'Icon Colors', 'libero' ),
				'description' => esc_html__( 'Define color style for icon', 'libero' ),
				'name'		=> 'search_icon_color_group'
			)
		);

		$search_icon_color_row = libero_mikado_add_admin_row(
			array(
				'parent'	=> $search_icon_color_group,
				'name'		=> 'search_icon_color_row'
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'	=> $search_icon_color_row,
				'type'		=> 'colorsimple',
				'name'		=> 'header_search_icon_color',
				'label' => esc_html__( 'Color', 'libero' )
			)
		);
		libero_mikado_add_admin_field(
			array(
				'parent' => $search_icon_color_row,
				'type'		=> 'colorsimple',
				'name'		=> 'header_search_icon_hover_color',
				'label' => esc_html__( 'Hover Color', 'libero' )
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $search_panel,
				'type'			=> 'yesno',
				'name'			=> 'enable_search_icon_text',
				'default_value'	=> 'yes',
				'label' => esc_html__( 'Enable Search Icon Text', 'libero' ),
				'description' => esc_html__( "Enable this option to show Search text next to search icon in header", 'libero' ),
				'args'			=> array(
					'dependence' => true,
					'dependence_hide_on_yes' => '',
					'dependence_show_on_yes' => '#mkd_enable_search_icon_text_container'
				)
			)
		);

		$enable_search_icon_text_container = libero_mikado_add_admin_container(
			array(
				'parent'			=> $search_panel,
				'name'				=> 'enable_search_icon_text_container',
				'hidden_property'	=> 'enable_search_icon_text',
				'hidden_value'		=> 'no'
			)
		);

		$enable_search_icon_text_group = libero_mikado_add_admin_group(
			array(
				'parent'	=> $enable_search_icon_text_container,
				'title' => esc_html__( 'Search Icon Text', 'libero' ),
				'name'		=> 'enable_search_icon_text_group',
				'description' => esc_html__( 'Define Style for Search Icon Text', 'libero' )
			)
		);

		$enable_search_icon_text_row = libero_mikado_add_admin_row(
			array(
				'parent'	=> $enable_search_icon_text_group,
				'name'		=> 'enable_search_icon_text_row'
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $enable_search_icon_text_row,
				'type'			=> 'colorsimple',
				'name'			=> 'search_icon_text_color',
				'label' => esc_html__( 'Text Color', 'libero' ),
				'default_value'	=> ''
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $enable_search_icon_text_row,
				'type'			=> 'colorsimple',
				'name'			=> 'search_icon_text_color_hover',
				'label' => esc_html__( 'Text Hover Color', 'libero' ),
				'default_value'	=> ''
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $enable_search_icon_text_row,
				'type'			=> 'textsimple',
				'name'			=> 'search_icon_text_fontsize',
				'label' => esc_html__( 'Font Size', 'libero' ),
				'default_value'	=> '',
				'args'			=> array(
					'suffix'	=> 'px'
				)
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $enable_search_icon_text_row,
				'type'			=> 'textsimple',
				'name'			=> 'search_icon_text_lineheight',
				'label' => esc_html__( 'Line Height', 'libero' ),
				'default_value'	=> '',
				'args'			=> array(
					'suffix'	=> 'px'
				)
			)
		);

		$enable_search_icon_text_row2 = libero_mikado_add_admin_row(
			array(
				'parent'	=> $enable_search_icon_text_group,
				'name'		=> 'enable_search_icon_text_row2',
				'next'		=> true
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $enable_search_icon_text_row2,
				'type'			=> 'selectblanksimple',
				'name'			=> 'search_icon_text_texttransform',
				'label' => esc_html__( 'Text Transform', 'libero' ),
				'default_value'	=> '',
				'options'		=> libero_mikado_get_text_transform_array()
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $enable_search_icon_text_row2,
				'type'			=> 'fontsimple',
				'name'			=> 'search_icon_text_google_fonts',
				'label' => esc_html__( 'Font Family', 'libero' ),
				'default_value'	=> '-1',
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $enable_search_icon_text_row2,
				'type'			=> 'selectblanksimple',
				'name'			=> 'search_icon_text_fontstyle',
				'label' => esc_html__( 'Font Style', 'libero' ),
				'default_value'	=> '',
				'options'		=> libero_mikado_get_font_style_array(),
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $enable_search_icon_text_row2,
				'type'			=> 'selectblanksimple',
				'name'			=> 'search_icon_text_fontweight',
				'label' => esc_html__( 'Font Weight', 'libero' ),
				'default_value'	=> '',
				'options'		=> libero_mikado_get_font_weight_array(),
			)
		);

		$enable_search_icon_text_row3 = libero_mikado_add_admin_row(
			array(
				'parent'	=> $enable_search_icon_text_group,
				'name'		=> 'enable_search_icon_text_row3',
				'next'		=> true
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $enable_search_icon_text_row3,
				'type'			=> 'textsimple',
				'name'			=> 'search_icon_text_letterspacing',
				'label' => esc_html__( 'Letter Spacing', 'libero' ),
				'default_value'	=> '',
				'args'			=> array(
					'suffix'	=> 'px'
				)
			)
		);

		$search_icon_spacing_group = libero_mikado_add_admin_group(
			array(
				'parent'	=> $search_panel,
				'title' => esc_html__( 'Icon Spacing', 'libero' ),
				'description' => esc_html__( 'Define padding and margins for Search icon', 'libero' ),
				'name'		=> 'search_icon_spacing_group'
			)
		);

		$search_icon_spacing_row = libero_mikado_add_admin_row(
			array(
				'parent'	=> $search_icon_spacing_group,
				'name'		=> 'search_icon_spacing_row'
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $search_icon_spacing_row,
				'type'			=> 'textsimple',
				'name'			=> 'search_padding_left',
				'default_value'	=> '',
				'label' => esc_html__( 'Padding Left', 'libero' ),
				'args'			=> array(
					'suffix'	=> 'px'
				)
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $search_icon_spacing_row,
				'type'			=> 'textsimple',
				'name'			=> 'search_padding_right',
				'default_value'	=> '',
				'label' => esc_html__( 'Padding Right', 'libero' ),
				'args'			=> array(
					'suffix'	=> 'px'
				)
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $search_icon_spacing_row,
				'type'			=> 'textsimple',
				'name'			=> 'search_margin_left',
				'default_value'	=> '',
				'label' => esc_html__( 'Margin Left', 'libero' ),
				'args'			=> array(
					'suffix'	=> 'px'
				)
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $search_icon_spacing_row,
				'type'			=> 'textsimple',
				'name'			=> 'search_margin_right',
				'default_value'	=> '',
				'label' => esc_html__( 'Margin Right', 'libero' ),
				'args'			=> array(
					'suffix'	=> 'px'
				)
			)
		);

		libero_mikado_add_admin_section_title(
			array(
				'parent' 	=> $search_panel,
				'name'		=> 'search_form_title',
				'title' => esc_html__( 'Search Bar', 'libero' )
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $search_panel,
				'type'			=> 'color',
				'name'			=> 'search_background_color',
				'default_value'	=> '',
				'label' => esc_html__( 'Background Color', 'libero' ),
				'description' => esc_html__( 'Choose a background color for Select search bar', 'libero' )
			)
		);

		$search_input_text_group = libero_mikado_add_admin_group(
			array(
				'parent'	=> $search_panel,
				'title' => esc_html__( 'Search Input Text', 'libero' ),
				'description' => esc_html__( 'Define style for search text', 'libero' ),
				'name'		=> 'search_input_text_group'
			)
		);

		$search_input_text_row = libero_mikado_add_admin_row(
			array(
				'parent'	=> $search_input_text_group,
				'name'		=> 'search_input_text_row'
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $search_input_text_row,
				'type'			=> 'colorsimple',
				'name'			=> 'search_text_color',
				'default_value'	=> '',
				'label' => esc_html__( 'Text Color', 'libero' ),
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $search_input_text_row,
				'type'			=> 'colorsimple',
				'name'			=> 'search_text_disabled_color',
				'default_value'	=> '',
				'label' => esc_html__( 'Disabled Text Color', 'libero' ),
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $search_input_text_row,
				'type'			=> 'textsimple',
				'name'			=> 'search_text_fontsize',
				'default_value'	=> '',
				'label' => esc_html__( 'Font Size', 'libero' ),
				'args'			=> array(
					'suffix' => 'px'
				)
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $search_input_text_row,
				'type'			=> 'selectblanksimple',
				'name'			=> 'search_text_texttransform',
				'default_value'	=> '',
				'label' => esc_html__( 'Text Transform', 'libero' ),
				'options'		=> libero_mikado_get_text_transform_array()
			)
		);

		$search_input_text_row2 = libero_mikado_add_admin_row(
			array(
				'parent'	=> $search_input_text_group,
				'name'		=> 'search_input_text_row2'
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $search_input_text_row2,
				'type'			=> 'fontsimple',
				'name'			=> 'search_text_google_fonts',
				'default_value'	=> '-1',
				'label' => esc_html__( 'Font Family', 'libero' ),
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $search_input_text_row2,
				'type'			=> 'selectblanksimple',
				'name'			=> 'search_text_fontstyle',
				'default_value'	=> '',
				'label' => esc_html__( 'Font Style', 'libero' ),
				'options'		=> libero_mikado_get_font_style_array(),
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $search_input_text_row2,
				'type'			=> 'selectblanksimple',
				'name'			=> 'search_text_fontweight',
				'default_value'	=> '',
				'label' => esc_html__( 'Font Weight', 'libero' ),
				'options'		=> libero_mikado_get_font_weight_array()
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $search_input_text_row2,
				'type'			=> 'textsimple',
				'name'			=> 'search_text_letterspacing',
				'default_value'	=> '',
				'label' => esc_html__( 'Letter Spacing', 'libero' ),
				'args'			=> array(
					'suffix'	=> 'px'
				)
			)
		);

		$search_label_text_group = libero_mikado_add_admin_group(
			array(
				'parent'	=> $search_panel,
				'title' => esc_html__( 'Search Label Text', 'libero' ),
				'description' => esc_html__( 'Define style for search label text', 'libero' ),
				'name'		=> 'search_label_text_group'
			)
		);

		$search_label_text_row = libero_mikado_add_admin_row(
			array(
				'parent'	=> $search_label_text_group,
				'name'		=> 'search_label_text_row'
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $search_label_text_row,
				'type'			=> 'colorsimple',
				'name'			=> 'search_label_text_color',
				'default_value'	=> '',
				'label' => esc_html__( 'Text Color', 'libero' ),
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $search_label_text_row,
				'type'			=> 'textsimple',
				'name'			=> 'search_label_text_fontsize',
				'default_value'	=> '',
				'label' => esc_html__( 'Font Size', 'libero' ),
				'args'			=> array(
					'suffix'	=> 'px'
				)
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $search_label_text_row,
				'type'			=> 'selectblanksimple',
				'name'			=> 'search_label_text_texttransform',
				'default_value'	=> '',
				'label' => esc_html__( 'Text Transform', 'libero' ),
				'options'		=> libero_mikado_get_text_transform_array()
			)
		);

		$search_label_text_row2 = libero_mikado_add_admin_row(
			array(
				'parent'	=> $search_label_text_group,
				'name'		=> 'search_label_text_row2',
				'next'		=> true
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $search_label_text_row2,
				'type'			=> 'fontsimple',
				'name'			=> 'search_label_text_google_fonts',
				'default_value'	=> '-1',
				'label' => esc_html__( 'Font Family', 'libero' ),
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $search_label_text_row2,
				'type'			=> 'selectblanksimple',
				'name'			=> 'search_label_text_fontstyle',
				'default_value'	=> '',
				'label' => esc_html__( 'Font Style', 'libero' ),
				'options'		=> libero_mikado_get_font_style_array()
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $search_label_text_row2,
				'type'			=> 'selectblanksimple',
				'name'			=> 'search_label_text_fontweight',
				'default_value'	=> '',
				'label' => esc_html__( 'Font Weight', 'libero' ),
				'options'		=> libero_mikado_get_font_weight_array()
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $search_label_text_row2,
				'type'			=> 'textsimple',
				'name'			=> 'search_label_text_letterspacing',
				'default_value'	=> '',
				'label' => esc_html__( 'Letter Spacing', 'libero' ),
				'args'			=> array(
					'suffix'	=> 'px'
				)
			)
		);

		$search_icon_group = libero_mikado_add_admin_group(
			array(
				'parent'	=> $search_panel,
				'title' => esc_html__( 'Search Icon', 'libero' ),
				'description' => esc_html__( 'Define style for search icon', 'libero' ),
				'name'		=> 'search_icon_group'
			)
		);

		$search_icon_row = libero_mikado_add_admin_row(
			array(
				'parent'	=> $search_icon_group,
				'name'		=> 'search_icon_row'
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $search_icon_row,
				'type'			=> 'colorsimple',
				'name'			=> 'search_icon_color',
				'default_value'	=> '',
				'label' => esc_html__( 'Icon Color', 'libero' ),
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $search_icon_row,
				'type'			=> 'colorsimple',
				'name'			=> 'search_icon_hover_color',
				'default_value'	=> '',
				'label' => esc_html__( 'Icon Hover Color', 'libero' ),
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $search_icon_row,
				'type'			=> 'colorsimple',
				'name'			=> 'search_icon_disabled_color',
				'default_value'	=> '',
				'label' => esc_html__( 'Icon Disabled Color', 'libero' ),
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $search_icon_row,
				'type'			=> 'textsimple',
				'name'			=> 'search_icon_size',
				'default_value'	=> '',
				'label' => esc_html__( 'Icon Size', 'libero' ),
				'args'			=> array(
					'suffix'	=> 'px'
				)
			)
		);

		$search_close_icon_group = libero_mikado_add_admin_group(
			array(
				'parent'	=> $search_panel,
				'title' => esc_html__( 'Search Close', 'libero' ),
				'description' => esc_html__( 'Define style for search close icon', 'libero' ),
				'name'		=> 'search_close_icon_group'
			)
		);

		$search_close_icon_row = libero_mikado_add_admin_row(
			array(
				'parent'	=> $search_close_icon_group,
				'name'		=> 'search_icon_row'
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $search_close_icon_row,
				'type'			=> 'colorsimple',
				'name'			=> 'search_close_color',
				'label' => esc_html__( 'Icon Color', 'libero' ),
				'default_value'	=> ''
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $search_close_icon_row,
				'type'			=> 'colorsimple',
				'name'			=> 'search_close_hover_color',
				'label' => esc_html__( 'Icon Hover Color', 'libero' ),
				'default_value'	=> ''
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent'		=> $search_close_icon_row,
				'type'			=> 'textsimple',
				'name'			=> 'search_close_size',
				'label' => esc_html__( 'Icon Size', 'libero' ),
				'default_value'	=> '',
				'args'			=> array(
					'suffix'	=> 'px'
				)
			)
		);

	}

	add_action('libero_mikado_options_map', 'libero_mikado_search_options_map', 5);

}