<?php

if ( ! function_exists('libero_mikado_portfolio_options_map') ) {

	function libero_mikado_portfolio_options_map() {

		libero_mikado_add_admin_page(array(
			'slug'  => '_portfolio',
			'title' => esc_html__( 'Portfolio', 'libero' ),
			'icon'  => 'icon_images'
		));

		$panel = libero_mikado_add_admin_panel(array(
			'title' => esc_html__( 'Portfolio Single', 'libero' ),
			'name'  => 'panel_portfolio_single',
			'page'  => '_portfolio'
		));

		libero_mikado_add_admin_field(array(
			'name'        => 'portfolio_single_template',
			'type'        => 'select',
			'label' => esc_html__( 'Portfolio Type', 'libero' ),
			'default_value'	=> 'small-images',
			'description' => esc_html__( 'Choose a default type for Single Project pages', 'libero' ),
			'parent'      => $panel,
			'options'     => array(
				'small-images' => esc_html__('Portfolio small images', 'libero' ),
				'small-slider' => esc_html__('Portfolio small slider', 'libero' ),
				'big-images' => esc_html__('Portfolio big images', 'libero' ),
				'big-slider' => esc_html__('Portfolio big slider', 'libero' ),
				'custom' => esc_html__('Portfolio custom', 'libero' ),
				'full-width-custom' => esc_html__('Portfolio full width custom', 'libero' ),
				'gallery' => esc_html__('Portfolio gallery', 'libero' )
			)
		));

		libero_mikado_add_admin_field(array(
			'name'          => 'portfolio_single_lightbox_images',
			'type'          => 'yesno',
			'label' => esc_html__( 'Lightbox for Images', 'libero' ),
			'description' => esc_html__( 'Enabling this option will turn on lightbox functionality for projects with images.', 'libero' ),
			'parent'        => $panel,
			'default_value' => 'yes'
		));

		libero_mikado_add_admin_field(array(
			'name'          => 'portfolio_single_lightbox_videos',
			'type'          => 'yesno',
			'label' => esc_html__( 'Lightbox for Videos', 'libero' ),
			'description' => esc_html__( 'Enabling this option will turn on lightbox functionality for YouTube/Vimeo projects.', 'libero' ),
			'parent'        => $panel,
			'default_value' => 'no'
		));

		libero_mikado_add_admin_field(array(
			'name'          => 'portfolio_single_hide_categories',
			'type'          => 'yesno',
			'label' => esc_html__( 'Hide Categories', 'libero' ),
			'description' => esc_html__( 'Enabling this option will disable category meta description on Single Projects.', 'libero' ),
			'parent'        => $panel,
			'default_value' => 'no'
		));

		libero_mikado_add_admin_field(array(
			'name'          => 'portfolio_single_hide_date',
			'type'          => 'yesno',
			'label' => esc_html__( 'Hide Date', 'libero' ),
			'description' => esc_html__( 'Enabling this option will disable date meta on Single Projects.', 'libero' ),
			'parent'        => $panel,
			'default_value' => 'no'
		));

        libero_mikado_add_admin_field(array(
            'name'          => 'portfolio_single_hide_related',
            'type'          => 'yesno',
            'label' => esc_html__( 'Hide Related Projects', 'libero' ),
            'description' => esc_html__( 'Enabling this option will disable related projects part on Single Projects.', 'libero' ),
            'parent'        => $panel,
            'default_value' => 'no'
        ));

		libero_mikado_add_admin_field(array(
			'name'          => 'portfolio_single_comments',
			'type'          => 'yesno',
			'label' => esc_html__( 'Show Comments', 'libero' ),
			'description' => esc_html__( 'Enabling this option will show comments on your page.', 'libero' ),
			'parent'        => $panel,
			'default_value' => 'no'
		));

		libero_mikado_add_admin_field(array(
			'name'          => 'portfolio_single_sticky_sidebar',
			'type'          => 'yesno',
			'label' => esc_html__( 'Sticky Side Text', 'libero' ),
			'description' => esc_html__( 'Enabling this option will make side text sticky on Single Project pages', 'libero' ),
			'parent'        => $panel,
			'default_value' => 'yes'
		));

		libero_mikado_add_admin_field(array(
			'name'          => 'portfolio_single_hide_pagination',
			'type'          => 'yesno',
			'label' => esc_html__( 'Hide Pagination', 'libero' ),
			'description' => esc_html__( 'Enabling this option will turn off portfolio pagination functionality.', 'libero' ),
			'parent'        => $panel,
			'default_value' => 'no',
			'args' => array(
				'dependence' => true,
				'dependence_hide_on_yes' => '#mkd_navigate_same_category_container'
			)
		));

		$container_navigate_category = libero_mikado_add_admin_container(array(
			'name'            => 'navigate_same_category_container',
			'parent'          => $panel,
			'hidden_property' => 'portfolio_single_hide_pagination',
			'hidden_value'    => 'yes'
		));

		libero_mikado_add_admin_field(array(
			'name'            => 'portfolio_single_nav_same_category',
			'type'            => 'yesno',
			'label' => esc_html__( 'Enable Pagination Through Same Category', 'libero' ),
			'description' => esc_html__( 'Enabling this option will make portfolio pagination sort through current category.', 'libero' ),
			'parent'          => $container_navigate_category,
			'default_value'   => 'no'
		));

		libero_mikado_add_admin_field(array(
			'name'        => 'portfolio_single_numb_columns',
			'type'        => 'select',
			'label' => esc_html__( 'Number of Columns', 'libero' ),
			'default_value' => 'three-columns',
			'description' => esc_html__( 'Enter the number of columns for Portfolio Gallery type', 'libero' ),
			'parent'      => $panel,
			'options'     => array(
				'two-columns' => esc_html__('2 columns', 'libero' ),
				'three-columns' => esc_html__('3 columns', 'libero' ),
				'four-columns' => esc_html__('4 columns', 'libero' )
			)
		));

		libero_mikado_add_admin_field(array(
			'name'        => 'portfolio_single_slug',
			'type'        => 'text',
			'label' => esc_html__( 'Portfolio Single Slug', 'libero' ),
			'description' => esc_html__( 'Enter if you wish to use a different Single Project slug (Note: After entering slug, navigate to Settings -> Permalinks and click Save in order for changes to take effect)', 'libero' ),
			'parent'      => $panel,
			'args'        => array(
				'col_width' => 3
			)
		));

	}

	add_action( 'libero_mikado_options_map', 'libero_mikado_portfolio_options_map',11);

}