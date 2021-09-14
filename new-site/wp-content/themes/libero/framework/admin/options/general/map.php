<?php

if ( ! function_exists('libero_mikado_general_options_map') ) {
    /**
     * General options page
     */
    function libero_mikado_general_options_map() {

        libero_mikado_add_admin_page(
            array(
                'slug'  => '',
                'title' => esc_html__( 'General', 'libero' ),
                'icon'  => 'icon_building'
            )
        );

        do_action('libero_mikado_before_general_options_map');

        $panel_design_style = libero_mikado_add_admin_panel(
            array(
                'page'  => '',
                'name'  => 'panel_design_style',
                'title' => esc_html__( 'Appearance', 'libero' )
            )
        );

        libero_mikado_add_admin_field(
            array(
                'name'          => 'google_fonts',
                'type'          => 'font',
                'default_value' => '-1',
                'label' => esc_html__( 'Font Family', 'libero' ),
                'description' => esc_html__( 'Choose a default Google font for your site', 'libero' ),
                'parent' => $panel_design_style
            )
        );

        libero_mikado_add_admin_field(
            array(
                'name'          => 'additional_google_fonts',
                'type'          => 'yesno',
                'default_value' => 'no',
                'label' => esc_html__( 'Additional Google Fonts', 'libero' ),
                'description'   => '',
                'parent'        => $panel_design_style,
                'args'          => array(
                    "dependence" => true,
                    "dependence_hide_on_yes" => "",
                    "dependence_show_on_yes" => "#mkd_additional_google_fonts_container"
                )
            )
        );

        $additional_google_fonts_container = libero_mikado_add_admin_container(
            array(
                'parent'            => $panel_design_style,
                'name'              => 'additional_google_fonts_container',
                'hidden_property'   => 'additional_google_fonts',
                'hidden_value'      => 'no'
            )
        );

        libero_mikado_add_admin_field(
            array(
                'name'          => 'additional_google_font1',
                'type'          => 'font',
                'default_value' => '-1',
                'label' => esc_html__( 'Font Family', 'libero' ),
                'description' => esc_html__( 'Choose additional Google font for your site', 'libero' ),
                'parent'        => $additional_google_fonts_container
            )
        );

        libero_mikado_add_admin_field(
            array(
                'name'          => 'additional_google_font2',
                'type'          => 'font',
                'default_value' => '-1',
                'label' => esc_html__( 'Font Family', 'libero' ),
                'description' => esc_html__( 'Choose additional Google font for your site', 'libero' ),
                'parent'        => $additional_google_fonts_container
            )
        );

        libero_mikado_add_admin_field(
            array(
                'name'          => 'additional_google_font3',
                'type'          => 'font',
                'default_value' => '-1',
                'label' => esc_html__( 'Font Family', 'libero' ),
                'description' => esc_html__( 'Choose additional Google font for your site', 'libero' ),
                'parent'        => $additional_google_fonts_container
            )
        );

        libero_mikado_add_admin_field(
            array(
                'name'          => 'additional_google_font4',
                'type'          => 'font',
                'default_value' => '-1',
                'label' => esc_html__( 'Font Family', 'libero' ),
                'description' => esc_html__( 'Choose additional Google font for your site', 'libero' ),
                'parent'        => $additional_google_fonts_container
            )
        );

        libero_mikado_add_admin_field(
            array(
                'name'          => 'additional_google_font5',
                'type'          => 'font',
                'default_value' => '-1',
                'label' => esc_html__( 'Font Family', 'libero' ),
                'description' => esc_html__( 'Choose additional Google font for your site', 'libero' ),
                'parent'        => $additional_google_fonts_container
            )
        );

        libero_mikado_add_admin_field(
            array(
                'name'          => 'first_color',
                'type'          => 'color',
                'label' => esc_html__( 'First Main Color', 'libero' ),
                'description' => esc_html__( 'Choose the most dominant theme color. Default color is #ff9800', 'libero' ),
                'parent'        => $panel_design_style
            )
        );

        libero_mikado_add_admin_field(
            array(
                'name'          => 'second_color',
                'type'          => 'color',
                'label' => esc_html__( 'Second Main Color', 'libero' ),
                'description' => esc_html__( 'Choose the second dominant theme color. Default color is #afc900', 'libero' ),
                'parent'        => $panel_design_style
            )
        );

        libero_mikado_add_admin_field(
            array(
                'name'          => 'page_background_color',
                'type'          => 'color',
                'label' => esc_html__( 'Page Background Color', 'libero' ),
                'description' => esc_html__( 'Choose the background color for page content. Default color is #ffffff', 'libero' ),
                'parent'        => $panel_design_style
            )
        );

        libero_mikado_add_admin_field(
            array(
                'name'          => 'selection_color',
                'type'          => 'color',
                'label' => esc_html__( 'Text Selection Color', 'libero' ),
                'description' => esc_html__( 'Choose the color users see when selecting text', 'libero' ),
                'parent'        => $panel_design_style
            )
        );

        libero_mikado_add_admin_field(
            array(
                'name'          => 'boxed',
                'type'          => 'yesno',
                'default_value' => 'no',
                'label' => esc_html__( 'Boxed Layout', 'libero' ),
                'description'   => '',
                'parent'        => $panel_design_style,
                'args'          => array(
                    "dependence" => true,
                    "dependence_hide_on_yes" => "",
                    "dependence_show_on_yes" => "#mkd_boxed_container"
                )
            )
        );

        $boxed_container = libero_mikado_add_admin_container(
            array(
                'parent'            => $panel_design_style,
                'name'              => 'boxed_container',
                'hidden_property'   => 'boxed',
                'hidden_value'      => 'no'
            )
        );

        libero_mikado_add_admin_field(
            array(
                'name'          => 'page_background_color_in_box',
                'type'          => 'color',
                'label' => esc_html__( 'Page Background Color', 'libero' ),
                'description' => esc_html__( 'Choose the page background color outside box.', 'libero' ),
                'parent'        => $boxed_container
            )
        );

        libero_mikado_add_admin_field(
            array(
                'name'          => 'boxed_background_image',
                'type'          => 'image',
                'label' => esc_html__( 'Background Image', 'libero' ),
                'description' => esc_html__( 'Choose an image to be displayed in background', 'libero' ),
                'parent'        => $boxed_container
            )
        );

        libero_mikado_add_admin_field(
            array(
                'name'          => 'boxed_pattern_background_image',
                'type'          => 'image',
                'label' => esc_html__( 'Background Pattern', 'libero' ),
                'description' => esc_html__( 'Choose an image to be used as background pattern', 'libero' ),
                'parent'        => $boxed_container
            )
        );

        libero_mikado_add_admin_field(
            array(
                'name'          => 'boxed_background_image_attachment',
                'type'          => 'select',
                'default_value' => 'fixed',
                'label' => esc_html__( 'Background Image Attachment', 'libero' ),
                'description' => esc_html__( 'Choose background image attachment', 'libero' ),
                'parent'        => $boxed_container,
                'options'       => array(
                    'fixed'     => esc_html__('Fixed', 'libero' ),
                    'scroll'    => esc_html__('Scroll', 'libero' )
                )
            )
        );

        libero_mikado_add_admin_field(
            array(
                'name'          => 'initial_content_width',
                'type'          => 'select',
                'default_value' => 'grid-1300',
                'label' => esc_html__( 'Initial Width of Content', 'libero' ),
                'description' => esc_html__( 'Choose the initial width of content which is in grid (Applies to pages set to Default Template and rows set to In Grid', 'libero' ),
                'parent'        => $panel_design_style,
                'options'       => array(
                    "grid-1300" => "1300px - default",
                    "grid-1100" => "1100px",
                    "grid-1200" => "1200px",
                    "grid-1000" => "1000px",
                    "grid-800"  => "800px"
                )
            )
        );

        libero_mikado_add_admin_field(
            array(
                'name'          => 'preload_pattern_image',
                'type'          => 'image',
                'label' => esc_html__( 'Preload Pattern Image', 'libero' ),
                'description' => esc_html__( 'Choose preload pattern image to be displayed until images are loaded ', 'libero' ),
                'parent'        => $panel_design_style
            )
        );

        libero_mikado_add_admin_field(
            array(
                'name' => 'element_appear_amount',
                'type' => 'text',
                'label' => esc_html__( 'Element Appearance', 'libero' ),
                'description' => esc_html__( 'For animated elements, set distance (related to browser bottom) to start the animation', 'libero' ),
                'parent' => $panel_design_style,
                'args' => array(
                    'col_width' => 2,
                    'suffix' => 'px'
                )
            )
        );

        $panel_settings = libero_mikado_add_admin_panel(
            array(
                'page'  => '',
                'name'  => 'panel_settings',
                'title' => esc_html__( 'Behavior', 'libero' )
            )
        );

        libero_mikado_add_admin_field(
            array(
                'name'          => 'smooth_scroll',
                'type'          => 'yesno',
                'default_value' => 'no',
                'label' => esc_html__( 'Smooth Scroll', 'libero' ),
                'description' => esc_html__( 'Enabling this option will perform a smooth scrolling effect on every page (except on Mac and touch devices)', 'libero' ),
                'parent'        => $panel_settings
            )
        );

        libero_mikado_add_admin_field(
            array(
                'name'          => 'smooth_page_transitions',
                'type'          => 'yesno',
                'default_value' => 'no',
                'label' => esc_html__( 'Page Transition Effect', 'libero' ),
                'description' => esc_html__( 'Enabling this option will perform a fade out transition between pages when clicking on links.', 'libero' ),
                'parent'        => $panel_settings
            )
        );

        libero_mikado_add_admin_field(
            array(
                'name'          => 'show_back_button',
                'type'          => 'yesno',
                'default_value' => 'yes',
                'label' => esc_html__( 'Show Back To Top Button', 'libero' ),
                'description' => esc_html__( 'Enabling this option will display a Back to Top button on every page', 'libero' ),
                'parent'        => $panel_settings
            )
        );

        libero_mikado_add_admin_field(
            array(
                'name'          => 'responsiveness',
                'type'          => 'yesno',
                'default_value' => 'yes',
                'label' => esc_html__( 'Responsiveness', 'libero' ),
                'description' => esc_html__( 'Enabling this option will make all pages responsive', 'libero' ),
                'parent'        => $panel_settings
            )
        );

        $panel_custom_code = libero_mikado_add_admin_panel(
            array(
                'page'  => '',
                'name'  => 'panel_custom_code',
                'title' => esc_html__( 'Custom Code', 'libero' )
            )
        );

        libero_mikado_add_admin_field(
            array(
                'name'          => 'custom_css',
                'type'          => 'textarea',
                'label' => esc_html__( 'Custom CSS', 'libero' ),
                'description' => esc_html__( 'Enter your custom CSS here', 'libero' ),
                'parent'        => $panel_custom_code
            )
        );

        libero_mikado_add_admin_field(
            array(
                'name'          => 'custom_js',
                'type'          => 'textarea',
                'label' => esc_html__( 'Custom JS', 'libero' ),
                'description' => esc_html__( 'Enter your custom Javascript here', 'libero' ),
                'parent'        => $panel_custom_code
            )
        );

        $panel_google_api = libero_mikado_add_admin_panel(
            array(
                'page'  => '',
                'name'  => 'panel_google_api',
                'title' => esc_html__( 'Google API', 'libero' )
            )
        );

        libero_mikado_add_admin_field(
            array(
                'name'        => 'google_maps_api_key',
                'type'        => 'text',
                'label' => esc_html__( 'Google Maps Api Key', 'libero' ),
                'description' => esc_html__( 'Insert your Google Maps API key here.', 'libero' ),
                'parent'      => $panel_google_api
            )
        );
    }

    add_action( 'libero_mikado_options_map', 'libero_mikado_general_options_map', 1);

}