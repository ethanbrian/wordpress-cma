<?php

if ( ! function_exists('libero_mikado_social_options_map') ) {

	function libero_mikado_social_options_map() {

		libero_mikado_add_admin_page(
			array(
				'slug'  => '_social_page',
				'title' => esc_html__( 'Social', 'libero' ),
				'icon'  => 'icon_group'
			)
		);

		/**
		 * Enable Social Share
		 */
		$panel_social_share = libero_mikado_add_admin_panel(array(
			'page'  => '_social_page',
			'name'  => 'panel_social_share',
			'title' => esc_html__( 'Enable Social Share', 'libero' )
		));

		libero_mikado_add_admin_field(array(
			'type'			=> 'yesno',
			'name'			=> 'enable_social_share',
			'default_value'	=> 'no',
			'label' => esc_html__( 'Enable Social Share', 'libero' ),
			'description' => esc_html__( 'Enabling this option will allow social share on networks of your choice', 'libero' ),
			'args'			=> array(
				'dependence' => true,
				'dependence_hide_on_yes' => '',
				'dependence_show_on_yes' => '#mkd_panel_social_networks, #mkd_panel_show_social_share_on'
			),
			'parent'		=> $panel_social_share
		));

		$panel_show_social_share_on = libero_mikado_add_admin_panel(array(
			'page'  			=> '_social_page',
			'name'  			=> 'panel_show_social_share_on',
			'title' => esc_html__( 'Show Social Share On', 'libero' ),
			'hidden_property'	=> 'enable_social_share',
			'hidden_value'		=> 'no'
		));

		libero_mikado_add_admin_field(array(
			'type'			=> 'yesno',
			'name'			=> 'enable_social_share_on_post',
			'default_value'	=> 'no',
			'label' => esc_html__( 'Posts', 'libero' ),
			'description' => esc_html__( 'Show Social Share on Blog Posts', 'libero' ),
			'parent'		=> $panel_show_social_share_on
		));

		libero_mikado_add_admin_field(array(
			'type'			=> 'yesno',
			'name'			=> 'enable_social_share_on_page',
			'default_value'	=> 'no',
			'label' => esc_html__( 'Pages', 'libero' ),
			'description' => esc_html__( 'Show Social Share on Pages', 'libero' ),
			'parent'		=> $panel_show_social_share_on
		));

		libero_mikado_add_admin_field(array(
			'type'			=> 'yesno',
			'name'			=> 'enable_social_share_on_attachment',
			'default_value'	=> 'no',
			'label' => esc_html__( 'Media', 'libero' ),
			'description' => esc_html__( 'Show Social Share for Images and Videos', 'libero' ),
			'parent'		=> $panel_show_social_share_on
		));

		libero_mikado_add_admin_field(array(
			'type'			=> 'yesno',
			'name'			=> 'enable_social_share_on_portfolio-item',
			'default_value'	=> 'no',
			'label' => esc_html__( 'Portfolio Item', 'libero' ),
			'description' => esc_html__( 'Show Social Share for Portfolio Items', 'libero' ),
			'parent'		=> $panel_show_social_share_on
		));

		if(libero_mikado_is_woocommerce_installed()){
			libero_mikado_add_admin_field(array(
				'type'			=> 'yesno',
				'name'			=> 'enable_social_share_on_product',
				'default_value'	=> 'no',
				'label' => esc_html__( 'Product', 'libero' ),
				'description' => esc_html__( 'Show Social Share for Product Items', 'libero' ),
				'parent'		=> $panel_show_social_share_on
			));
		}

		/**
		 * Social Share Networks
		 */
		$panel_social_networks = libero_mikado_add_admin_panel(array(
			'page'  			=> '_social_page',
			'name'				=> 'panel_social_networks',
			'title' => esc_html__( 'Social Networks', 'libero' ),
			'hidden_property'	=> 'enable_social_share',
			'hidden_value'		=> 'no'
		));

		/**
		 * Facebook
		 */
		libero_mikado_add_admin_section_title(array(
			'parent'	=> $panel_social_networks,
			'name'		=> 'facebook_title',
			'title' => esc_html__( 'Share on Facebook', 'libero' )
		));

		libero_mikado_add_admin_field(array(
			'type'			=> 'yesno',
			'name'			=> 'enable_facebook_share',
			'default_value'	=> 'no',
			'label' => esc_html__( 'Enable Share', 'libero' ),
			'description' => esc_html__( 'Enabling this option will allow sharing via Facebook', 'libero' ),
			'args'			=> array(
				'dependence' => true,
				'dependence_hide_on_yes' => '',
				'dependence_show_on_yes' => '#mkd_enable_facebook_share_container'
			),
			'parent'		=> $panel_social_networks
		));

		$enable_facebook_share_container = libero_mikado_add_admin_container(array(
			'name'		=> 'enable_facebook_share_container',
			'hidden_property'	=> 'enable_facebook_share',
			'hidden_value'		=> 'no',
			'parent'			=> $panel_social_networks
		));

		libero_mikado_add_admin_field(array(
			'type'			=> 'image',
			'name'			=> 'facebook_icon',
			'default_value'	=> '',
			'label' => esc_html__( 'Upload Icon', 'libero' ),
			'parent'		=> $enable_facebook_share_container
		));

		/**
		 * Twitter
		 */
		libero_mikado_add_admin_section_title(array(
			'parent'	=> $panel_social_networks,
			'name'		=> 'twitter_title',
			'title' => esc_html__( 'Share on Twitter', 'libero' )
		));

		libero_mikado_add_admin_field(array(
			'type'			=> 'yesno',
			'name'			=> 'enable_twitter_share',
			'default_value'	=> 'no',
			'label' => esc_html__( 'Enable Share', 'libero' ),
			'description' => esc_html__( 'Enabling this option will allow sharing via Twitter', 'libero' ),
			'args'			=> array(
				'dependence' => true,
				'dependence_hide_on_yes' => '',
				'dependence_show_on_yes' => '#mkd_enable_twitter_share_container'
			),
			'parent'		=> $panel_social_networks
		));

		$enable_twitter_share_container = libero_mikado_add_admin_container(array(
			'name'		=> 'enable_twitter_share_container',
			'hidden_property'	=> 'enable_twitter_share',
			'hidden_value'		=> 'no',
			'parent'			=> $panel_social_networks
		));

		libero_mikado_add_admin_field(array(
			'type'			=> 'image',
			'name'			=> 'twitter_icon',
			'default_value'	=> '',
			'label' => esc_html__( 'Upload Icon', 'libero' ),
			'parent'		=> $enable_twitter_share_container
		));

		libero_mikado_add_admin_field(array(
			'type'			=> 'text',
			'name'			=> 'twitter_via',
			'default_value'	=> '',
			'label' => esc_html__( 'Via', 'libero' ),
			'parent'		=> $enable_twitter_share_container
		));

		/**
		 * Google Plus
		 */
		libero_mikado_add_admin_section_title(array(
			'parent'	=> $panel_social_networks,
			'name'		=> 'google_plus_title',
			'title' => esc_html__( 'Share on Google Plus', 'libero' )
		));

		libero_mikado_add_admin_field(array(
			'type'			=> 'yesno',
			'name'			=> 'enable_google_plus_share',
			'default_value'	=> 'no',
			'label' => esc_html__( 'Enable Share', 'libero' ),
			'description' => esc_html__( 'Enabling this option will allow sharing via Google Plus', 'libero' ),
			'args'			=> array(
				'dependence' => true,
				'dependence_hide_on_yes' => '',
				'dependence_show_on_yes' => '#mkd_enable_google_plus_container'
			),
			'parent'		=> $panel_social_networks
		));

		$enable_google_plus_container = libero_mikado_add_admin_container(array(
			'name'		=> 'enable_google_plus_container',
			'hidden_property'	=> 'enable_google_plus_share',
			'hidden_value'		=> 'no',
			'parent'			=> $panel_social_networks
		));

		libero_mikado_add_admin_field(array(
			'type'			=> 'image',
			'name'			=> 'google_plus_icon',
			'default_value'	=> '',
			'label' => esc_html__( 'Upload Icon', 'libero' ),
			'parent'		=> $enable_google_plus_container
		));

		/**
		 * Linked In
		 */
		libero_mikado_add_admin_section_title(array(
			'parent'	=> $panel_social_networks,
			'name'		=> 'linkedin_title',
			'title' => esc_html__( 'Share on LinkedIn', 'libero' )
		));

		libero_mikado_add_admin_field(array(
			'type'			=> 'yesno',
			'name'			=> 'enable_linkedin_share',
			'default_value'	=> 'no',
			'label' => esc_html__( 'Enable Share', 'libero' ),
			'description' => esc_html__( 'Enabling this option will allow sharing via LinkedIn', 'libero' ),
			'args'			=> array(
				'dependence' => true,
				'dependence_hide_on_yes' => '',
				'dependence_show_on_yes' => '#mkd_enable_linkedin_container'
			),
			'parent'		=> $panel_social_networks
		));

		$enable_linkedin_container = libero_mikado_add_admin_container(array(
			'name'		=> 'enable_linkedin_container',
			'hidden_property'	=> 'enable_linkedin_share',
			'hidden_value'		=> 'no',
			'parent'			=> $panel_social_networks
		));

		libero_mikado_add_admin_field(array(
			'type'			=> 'image',
			'name'			=> 'linkedin_icon',
			'default_value'	=> '',
			'label' => esc_html__( 'Upload Icon', 'libero' ),
			'parent'		=> $enable_linkedin_container
		));

		/**
		 * Tumblr
		 */
		libero_mikado_add_admin_section_title(array(
			'parent'	=> $panel_social_networks,
			'name'		=> 'tumblr_title',
			'title' => esc_html__( 'Share on Tumblr', 'libero' )
		));

		libero_mikado_add_admin_field(array(
			'type'			=> 'yesno',
			'name'			=> 'enable_tumblr_share',
			'default_value'	=> 'no',
			'label' => esc_html__( 'Enable Share', 'libero' ),
			'description' => esc_html__( 'Enabling this option will allow sharing via Tumblr', 'libero' ),
			'args'			=> array(
				'dependence' => true,
				'dependence_hide_on_yes' => '',
				'dependence_show_on_yes' => '#mkd_enable_tumblr_container'
			),
			'parent'		=> $panel_social_networks
		));

		$enable_tumblr_container = libero_mikado_add_admin_container(array(
			'name'		=> 'enable_tumblr_container',
			'hidden_property'	=> 'enable_tumblr_share',
			'hidden_value'		=> 'no',
			'parent'			=> $panel_social_networks
		));

		libero_mikado_add_admin_field(array(
			'type'			=> 'image',
			'name'			=> 'tumblr_icon',
			'default_value'	=> '',
			'label' => esc_html__( 'Upload Icon', 'libero' ),
			'parent'		=> $enable_tumblr_container
		));

		/**
		 * Pinterest
		 */
		libero_mikado_add_admin_section_title(array(
			'parent'	=> $panel_social_networks,
			'name'		=> 'pinterest_title',
			'title' => esc_html__( 'Share on Pinterest', 'libero' )
		));

		libero_mikado_add_admin_field(array(
			'type'			=> 'yesno',
			'name'			=> 'enable_pinterest_share',
			'default_value'	=> 'no',
			'label' => esc_html__( 'Enable Share', 'libero' ),
			'description' => esc_html__( 'Enabling this option will allow sharing via Pinterest', 'libero' ),
			'args'			=> array(
				'dependence' => true,
				'dependence_hide_on_yes' => '',
				'dependence_show_on_yes' => '#mkd_enable_pinterest_container'
			),
			'parent'		=> $panel_social_networks
		));

		$enable_pinterest_container = libero_mikado_add_admin_container(array(
			'name'				=> 'enable_pinterest_container',
			'hidden_property'	=> 'enable_pinterest_share',
			'hidden_value'		=> 'no',
			'parent'			=> $panel_social_networks
		));

		libero_mikado_add_admin_field(array(
			'type'			=> 'image',
			'name'			=> 'pinterest_icon',
			'default_value'	=> '',
			'label' => esc_html__( 'Upload Icon', 'libero' ),
			'parent'		=> $enable_pinterest_container
		));

		/**
		 * VK
		 */
		libero_mikado_add_admin_section_title(array(
			'parent'	=> $panel_social_networks,
			'name'		=> 'vk_title',
			'title' => esc_html__( 'Share on VK', 'libero' )
		));

		libero_mikado_add_admin_field(array(
			'type'			=> 'yesno',
			'name'			=> 'enable_vk_share',
			'default_value'	=> 'no',
			'label' => esc_html__( 'Enable Share', 'libero' ),
			'description' => esc_html__( 'Enabling this option will allow sharing via VK', 'libero' ),
			'args'			=> array(
				'dependence' => true,
				'dependence_hide_on_yes' => '',
				'dependence_show_on_yes' => '#mkd_enable_vk_container'
			),
			'parent'		=> $panel_social_networks
		));

		$enable_vk_container = libero_mikado_add_admin_container(array(
			'name'				=> 'enable_vk_container',
			'hidden_property'	=> 'enable_vk_share',
			'hidden_value'		=> 'no',
			'parent'			=> $panel_social_networks
		));

		libero_mikado_add_admin_field(array(
			'type'			=> 'image',
			'name'			=> 'vk_icon',
			'default_value'	=> '',
			'label' => esc_html__( 'Upload Icon', 'libero' ),
			'parent'		=> $enable_vk_container
		));

        if(defined('MIKADO_TWITTER_FEED_VERSION')) {
            $twitter_panel = libero_mikado_add_admin_panel(array(
                'title' => esc_html__( 'Twitter', 'libero' ),
                'name'  => 'panel_twitter',
                'page'  => '_social_page'
            ));

            libero_mikado_add_admin_twitter_button(array(
                'name'   => 'twitter_button',
                'parent' => $twitter_panel
            ));
        }

		if(defined('MIKADO_INSTAGRAM_FEED_VERSION')) {
            $instagram_panel = libero_mikado_add_admin_panel(array(
                'title' => esc_html__( 'Instagram', 'libero' ),
                'name'  => 'panel_instagram',
                'page'  => '_social_page'
            ));

            libero_mikado_add_admin_instagram_button(array(
                'name'   => 'instagram_button',
                'parent' => $instagram_panel
            ));
        }
	}

	add_action( 'libero_mikado_options_map', 'libero_mikado_social_options_map',12);

}