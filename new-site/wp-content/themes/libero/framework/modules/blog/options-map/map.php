<?php

if ( ! function_exists('libero_mikado_blog_options_map') ) {

	function libero_mikado_blog_options_map() {

		libero_mikado_add_admin_page(
			array(
				'slug' => '_blog_page',
				'title' => esc_html__( 'Blog', 'libero' ),
				'icon' => 'icon_book_alt'
			)
		);

		/**
		 * Blog Lists
		 */

		$custom_sidebars = libero_mikado_get_custom_sidebars();

		$panel_blog_lists = libero_mikado_add_admin_panel(
			array(
				'page' => '_blog_page',
				'name' => 'panel_blog_lists',
				'title' => esc_html__( 'Blog Lists', 'libero' )
			)
		);

		libero_mikado_add_admin_field(array(
			'name'        => 'blog_list_type',
			'type'        => 'select',
			'label' => esc_html__( 'Blog Layout for Archive Pages', 'libero' ),
			'description' => esc_html__( 'Choose a default blog layout', 'libero' ),
			'default_value' => 'standard',
			'parent'      => $panel_blog_lists,
			'options'     => array(
				'standard'				=> esc_html__('Blog: Standard', 'libero' ),
				'standard-whole-post' 	=> esc_html__('Blog: Standard Whole Post', 'libero' )
			)
		));

		libero_mikado_add_admin_field(array(
			'name'        => 'archive_sidebar_layout',
			'type'        => 'select',
			'label' => esc_html__( 'Archive and Category Sidebar', 'libero' ),
			'description' => esc_html__( 'Choose a sidebar layout for archived Blog Post Lists and Category Blog Lists', 'libero' ),
			'parent'      => $panel_blog_lists,
			'options'     => array(
				'default'			=> esc_html__('No Sidebar', 'libero' ),
				'sidebar-33-right'	=> esc_html__('Sidebar 1/3 Right', 'libero' ),
				'sidebar-25-right' 	=> esc_html__('Sidebar 1/4 Right', 'libero' ),
				'sidebar-33-left' 	=> esc_html__('Sidebar 1/3 Left', 'libero' ),
				'sidebar-25-left' 	=> esc_html__('Sidebar 1/4 Left', 'libero' ),
			)
		));


		if(count($custom_sidebars) > 0) {
			libero_mikado_add_admin_field(array(
				'name' => 'blog_custom_sidebar',
				'type' => 'selectblank',
				'label' => esc_html__( 'Sidebar to Display', 'libero' ),
				'description' => esc_html__( 'Choose a sidebar to display on Blog Post Lists and Category Blog Lists. Default sidebar is Sidebar Page', 'libero' ),
				'parent' => $panel_blog_lists,
				'options' => libero_mikado_get_custom_sidebars()
			));
		}

		libero_mikado_add_admin_field(
			array(
				'type' => 'yesno',
				'name' => 'pagination',
				'default_value' => 'yes',
				'label' => esc_html__( 'Pagination', 'libero' ),
				'parent' => $panel_blog_lists,
				'description' => esc_html__( 'Enabling this option will display pagination links on bottom of Blog Post List', 'libero' ),
				'args' => array(
					'dependence' => true,
					'dependence_hide_on_yes' => '',
					'dependence_show_on_yes' => '#mkd_mkd_pagination_container'
				)
			)
		);

		$pagination_container = libero_mikado_add_admin_container(
			array(
				'name' => 'mkd_pagination_container',
				'hidden_property' => 'pagination',
				'hidden_value' => 'no',
				'parent' => $panel_blog_lists,
			)
		);

		libero_mikado_add_admin_field(
			array(
				'parent' => $pagination_container,
				'type' => 'text',
				'name' => 'blog_page_range',
				'default_value' => '',
				'label' => esc_html__( 'Pagination Range limit', 'libero' ),
				'description' => esc_html__( 'Enter a number that will limit pagination to a certain range of links', 'libero' ),
				'args' => array(
					'col_width' => 3
				)
			)
		);

		libero_mikado_add_admin_field(
			array(
				'type' => 'text',
				'name' => 'number_of_chars',
				'default_value' => '',
				'label' => esc_html__( 'Number of Words in Excerpt', 'libero' ),
				'parent' => $panel_blog_lists,
				'description' => esc_html__( 'Enter a number of words in excerpt (article summary)', 'libero' ),
				'args' => array(
					'col_width' => 3
				)
			)
		);

		/**
		 * Blog Single
		 */
		$panel_blog_single = libero_mikado_add_admin_panel(
			array(
				'page' => '_blog_page',
				'name' => 'panel_blog_single',
				'title' => esc_html__( 'Blog Single', 'libero' )
			)
		);


		libero_mikado_add_admin_field(array(
			'name'        => 'blog_single_sidebar_layout',
			'type'        => 'select',
			'label' => esc_html__( 'Sidebar Layout', 'libero' ),
			'description' => esc_html__( 'Choose a sidebar layout for Blog Single pages', 'libero' ),
			'parent'      => $panel_blog_single,
			'options'     => array(
				'default'			=> esc_html__('No Sidebar', 'libero' ),
				'sidebar-33-right'	=> esc_html__('Sidebar 1/3 Right', 'libero' ),
				'sidebar-25-right' 	=> esc_html__('Sidebar 1/4 Right', 'libero' ),
				'sidebar-33-left' 	=> esc_html__('Sidebar 1/3 Left', 'libero' ),
				'sidebar-25-left' 	=> esc_html__('Sidebar 1/4 Left', 'libero' ),
			)
		));


		if(count($custom_sidebars) > 0) {
			libero_mikado_add_admin_field(array(
				'name' => 'blog_single_custom_sidebar',
				'type' => 'selectblank',
				'label' => esc_html__( 'Sidebar to Display', 'libero' ),
				'description' => esc_html__( 'Choose a sidebar to display on Blog Single pages. Default sidebar is Sidebar', 'libero' ),
				'parent' => $panel_blog_single,
				'options' => libero_mikado_get_custom_sidebars()
			));
		}
		libero_mikado_add_admin_field(array(
			'name'          => 'blog_single_comments',
			'type'          => 'yesno',
			'label' => esc_html__( 'Show Comments', 'libero' ),
			'description' => esc_html__( 'Enabling this option will show comments on your page.', 'libero' ),
			'parent'        => $panel_blog_single,
			'default_value' => 'yes'
		));
		libero_mikado_add_admin_field(
			array(
				'type' => 'yesno',
				'name' => 'blog_single_navigation',
				'default_value' => 'no',
				'label' => esc_html__( 'Enable Prev/Next Single Post Navigation Links', 'libero' ),
				'parent' => $panel_blog_single,
				'description' => esc_html__( 'Enable navigation links through the blog posts (left and right arrows will appear)', 'libero' ),
				'args' => array(
					'dependence' => true,
					'dependence_hide_on_yes' => '',
					'dependence_show_on_yes' => '#mkd_mkd_blog_single_navigation_container'
				)
			)
		);

		$blog_single_navigation_container = libero_mikado_add_admin_container(
			array(
				'name' => 'mkd_blog_single_navigation_container',
				'hidden_property' => 'blog_single_navigation',
				'hidden_value' => 'no',
				'parent' => $panel_blog_single,
			)
		);

		libero_mikado_add_admin_field(
			array(
				'type'        => 'yesno',
				'name' => 'blog_navigation_through_same_category',
				'default_value' => 'no',
				'label' => esc_html__( 'Enable Navigation Only in Current Category', 'libero' ),
				'description' => esc_html__( 'Limit your navigation only through current category', 'libero' ),
				'parent'      => $blog_single_navigation_container,
				'args' => array(
					'col_width' => 3
				)
			)
		);

		libero_mikado_add_admin_field(
			array(
				'type' => 'yesno',
				'name' => 'blog_author_info',
				'default_value' => 'no',
				'label' => esc_html__( 'Show Author Info Box', 'libero' ),
				'parent' => $panel_blog_single,
				'description' => esc_html__( 'Enabling this option will display author name and descriptions on Blog Single pages', 'libero' ),
				'args' => array(
					'dependence' => true,
					'dependence_hide_on_yes' => '',
					'dependence_show_on_yes' => '#mkd_mkd_blog_single_author_info_container'
				)
			)
		);

		$blog_single_author_info_container = libero_mikado_add_admin_container(
			array(
				'name' => 'mkd_blog_single_author_info_container',
				'hidden_property' => 'blog_author_info',
				'hidden_value' => 'no',
				'parent' => $panel_blog_single,
			)
		);

		libero_mikado_add_admin_field(
			array(
				'type'        => 'yesno',
				'name' => 'blog_author_info_email',
				'default_value' => 'no',
				'label' => esc_html__( 'Show Author Email', 'libero' ),
				'description' => esc_html__( 'Enabling this option will show author email', 'libero' ),
				'parent'      => $blog_single_author_info_container,
				'args' => array(
					'col_width' => 3
				)
			)
		);

	}

	add_action( 'libero_mikado_options_map', 'libero_mikado_blog_options_map',10);

}











