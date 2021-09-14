<?php
/*
Plugin Name: Mikado Core
Description: Plugin that adds all post types needed by our theme
Author: Mikado Themes
Version: 1.3.1
*/

require_once 'load.php';

add_action('after_setup_theme', array(MkdCore\CPT\PostTypesRegister::getInstance(), 'register'));

if(!function_exists('mkd_core_activation')) {
    /**
     * Triggers when plugin is activated. It calls flush_rewrite_rules
     * and defines libero_mikado_core_on_activate action
     */
    function mkd_core_activation() {
        do_action('libero_mikado_core_on_activate');

        MkdCore\CPT\PostTypesRegister::getInstance()->register();
        flush_rewrite_rules();
    }

    register_activation_hook(__FILE__, 'mkd_core_activation');
}

if(!function_exists('mkd_core_text_domain')) {
    /**
     * Loads plugin text domain so it can be used in translation
     */
    function mkd_core_text_domain() {
        load_plugin_textdomain('mikado-core', false, MIKADO_CORE_REL_PATH.'/languages');
    }

    add_action('plugins_loaded', 'mkd_core_text_domain');
}

if(!function_exists('libero_mikado_theme_menu')) {
	/**
	 * Function that generates admin menu for options page.
	 * It generates one admin page per options page.
	 */
	function libero_mikado_theme_menu() {
        if (mkd_core_theme_installed()) {
			global $libero_mikado_Framework;
			libero_mikado_init_theme_options();
	
			$page_hook_suffix = add_menu_page(
				'Mikado Options',                   // The value used to populate the browser's title bar when the menu page is active
				'Mikado Options',                   // The text of the menu in the administrator's sidebar
				'administrator',                  // What roles are able to access the menu
				'libero_mikado_theme_menu',                // The ID used to bind submenu items to this menu
				array($libero_mikado_Framework->getSkin(), 'renderOptions'), // The callback function used to render this menu
				$libero_mikado_Framework->getSkin()->getMenuIcon('options'),             // Icon For menu Item
				$libero_mikado_Framework->getSkin()->getMenuItemPosition('options')            // Position
			);
	
			foreach ($libero_mikado_Framework->mkdOptions->adminPages as $key=>$value ) {
				$slug = "";
	
				if (!empty($value->slug)) {
					$slug = "_tab".$value->slug;
				}
	
				$subpage_hook_suffix = add_submenu_page(
					'libero_mikado_theme_menu',
					'Mikado Options - '.$value->title,                   // The value used to populate the browser's title bar when the menu page is active
					$value->title,                   // The text of the menu in the administrator's sidebar
					'administrator',                  // What roles are able to access the menu
					'libero_mikado_theme_menu'.$slug,                // The ID used to bind submenu items to this menu
					array($libero_mikado_Framework->getSkin(), 'renderOptions')
				);
	
				add_action('admin_print_scripts-'.$subpage_hook_suffix, 'libero_mikado_enqueue_admin_scripts');
				add_action('admin_print_styles-'.$subpage_hook_suffix, 'libero_mikado_enqueue_admin_styles');
			};
	
			add_action('admin_print_scripts-'.$page_hook_suffix, 'libero_mikado_enqueue_admin_scripts');
			add_action('admin_print_styles-'.$page_hook_suffix, 'libero_mikado_enqueue_admin_styles');
		}
	}

	add_action( 'admin_menu', 'libero_mikado_theme_menu');
}