<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UACF7_Admin_Menu {
    
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'uacf7_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'uacf7_page_init' ) );
	}

    /*
    * Admin menu
    */
	public function uacf7_add_plugin_page() {
		add_submenu_page(
            'wpcf7', //parent slug
			__('Ultimate Addons','ultimate-addons-cf7'), // page_title
			__('Ultimate Addons','ultimate-addons-cf7'), // menu_title
			'manage_options', // capability
			'ultimate-addons', // menu_slug
			array( $this, 'uacf7_create_admin_page' ) // function
		);
	}

    /*
    * Admin settings page
    */
	public function uacf7_create_admin_page() {
        ?>
		<div class="wrap">
			<h2><?php echo esc_html__( 'Ultimate Addons for Contact Form 7', 'ultimate-addons-cf7' ); ?></h2>
			<p><?php echo esc_html__( 'The only Contact form 7 addons plugin you should install to meet all your basic needs. Just tick the addon you need from the below settings Panel and click save. That addon will be activated. Then go to the Contact Form 7 settings Panel and start editing.', 'ultimate-addons-cf7' ); ?></p>
			<?php settings_errors(); ?>

            <!--Settings tab start-->
            
            <!--Tab buttons start-->
			<div class="uacf7-tab">
              <a class="tablinks active" onclick="uacf7_settings_tab(event, 'uacf7_addons')">Addons</a>
              <a class="tablinks" onclick="uacf7_settings_tab(event, 'uacf7_doc')">Documentation</a>
              
              <?php do_action('uacf7_admin_tab_button'); ?>
            </div>
            <!--Tab buttons end-->

            <!--Tab Addons start-->
            <div id="uacf7_addons" class="uacf7-tabcontent" style="display:block">
                <form method="post" action="options.php">
                    <?php
                        settings_fields( 'uacf7_option_group' );
                        do_settings_sections( 'ultimate-addons-admin' );
                        submit_button();
                    ?>
                </form>
            </div>
            <!--Tab Addons end-->
            
            <div id="uacf7_doc" class="uacf7-tabcontent">
                <p>Click <a href="https://themefic.com/docs/ultimate-addons-for-contact-form-7/" target="_blank">here</a> to learn about Ultimate addons for contact form 7.</p>
            </div>
            
            <?php do_action('uacf7_admin_tab_content'); ?>
            
            <!--Settings tab end-->
			
		</div>
	    <?php 
    }

    /*
    * Admin settings fields
    */
	public function uacf7_page_init() {
		register_setting(
			'uacf7_option_group', // option_group
			'uacf7_option_name', // option_name
			array( $this, 'uacf7_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'uacf7_setting_section', // id
			__( 'Addons Settings:', 'ultimate-addons-cf7' ), // title
			array( $this, 'uacf7_section_info' ), // callback
			'ultimate-addons-admin' // page
		);

		add_settings_field(
			'uacf7_enable_redirection', // id
			__( 'Enable Redirection', 'ultimate-addons-cf7' ), // title
			array( $this, 'uacf7_enable_redirection_callback' ), // callback
			'ultimate-addons-admin', // page
			'uacf7_setting_section' // section
		);
        
        add_settings_field(
			'uacf7_enable_conditional_field', // id
			__( 'Enable Conditional Field', 'ultimate-addons-cf7' ), // title
			array( $this, 'uacf7_enable_conditional_field_callback' ), // callback
			'ultimate-addons-admin', // page
			'uacf7_setting_section' // section
		);
        
        add_settings_field(
			'uacf7_enable_field_column', // id
			__( 'Enable Column or Grid', 'ultimate-addons-cf7' ), // title
			array( $this, 'uacf7_enable_field_column_callback' ), // callback
			'ultimate-addons-admin', // page
			'uacf7_setting_section' // section
		);
        
        add_settings_field(
			'uacf7_enable_placeholder', // id
			__( 'Enable Placeholder Styling', 'ultimate-addons-cf7' ), // title
			array( $this, 'uacf7_enable_placeholder_callback' ), // callback
			'ultimate-addons-admin', // page
			'uacf7_setting_section' // section
		);
        
        add_settings_field(
			'uacf7_enable_uacf7style', // id
			__( 'Enable Form Styling', 'ultimate-addons-cf7' ), // title
			array( $this, 'uacf7_enable_uacf7style_callback' ), // callback
			'ultimate-addons-admin', // page
			'uacf7_setting_section' // section
		);
        
        add_settings_field(
			'uacf7_enable_multistep', // id
			__( 'Enable Multistep', 'ultimate-addons-cf7' ), // title
			array( $this, 'uacf7_enable_multistep_callback' ), // callback
			'ultimate-addons-admin', // page
			'uacf7_setting_section' // section
		);
        
        add_settings_field(
			'uacf7_enable_post_submission', // id
			__( 'Enable Frontend Post Submission', 'ultimate-addons-cf7' ), // title
			array( $this, 'uacf7_enable_post_submission_callback' ), // callback
			'ultimate-addons-admin', // page
			'uacf7_setting_section' // section
		);

        add_settings_section(
			'uacf7_setting_section_fields', // id
			'', // title
			array( $this, 'uacf7_setting_section_fields_callback' ), // callback
			'ultimate-addons-admin' // page
		);
        
        add_settings_field(
            'uacf7_enable_star_rating', // id
            __( 'Star Rating', 'ultimate-addons-cf7' ), // title
            array( $this, 'uacf7_enable_star_rating_callback' ), // callback
            'ultimate-addons-admin', // page
            'uacf7_setting_section_fields' // section
        );

        add_settings_section(
			'uacf7_setting_section_woo', // id
			'', // title
			array( $this, 'uacf7_setting_section_woo_callback' ), // callback
			'ultimate-addons-admin' // page
		);
                
        add_settings_field(
            'uacf7_enable_product_dropdown', // id
            __( 'Product Dropdown Menu', 'ultimate-addons-cf7' ), // title
            array( $this, 'uacf7_enable_product_dropdown_callback' ), // callback
            'ultimate-addons-admin', // page
            'uacf7_setting_section_woo' // section
        );
        
        add_settings_field(
            'uacf7_enable_product_auto_cart', // id
            __( 'Auto Add to Cart & Checkout after Form Submission', 'ultimate-addons-cf7' ), // title
            array( $this, 'uacf7_enable_product_auto_cart_callback' ), // callback
            'ultimate-addons-admin', // page
            'uacf7_setting_section_woo' // section
        );
                
        do_action( 'uacf7_settings_field' );
	}

    /*
    * Sanitize fields
    */
	public function uacf7_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['uacf7_enable_redirection'] ) ) {
			$sanitary_values['uacf7_enable_redirection'] = $input['uacf7_enable_redirection'];
		}
        
        if ( isset( $input['uacf7_enable_conditional_field'] ) ) {
			$sanitary_values['uacf7_enable_conditional_field'] = $input['uacf7_enable_conditional_field'];
		}
        
        if ( isset( $input['uacf7_enable_field_column'] ) ) {
			$sanitary_values['uacf7_enable_field_column'] = $input['uacf7_enable_field_column'];
		}
        
        if ( isset( $input['uacf7_enable_placeholder'] ) ) {
			$sanitary_values['uacf7_enable_placeholder'] = $input['uacf7_enable_placeholder'];
		}
        
        if ( isset( $input['uacf7_enable_uacf7style'] ) ) {
			$sanitary_values['uacf7_enable_uacf7style'] = $input['uacf7_enable_uacf7style'];
		}
        
        if ( isset( $input['uacf7_enable_star_rating'] ) ) {
			$sanitary_values['uacf7_enable_star_rating'] = $input['uacf7_enable_star_rating'];
		}
        
        if ( isset( $input['uacf7_enable_multistep'] ) ) {
			$sanitary_values['uacf7_enable_multistep'] = $input['uacf7_enable_multistep'];
		}
        
        if ( isset( $input['uacf7_enable_product_dropdown'] ) ) {
			$sanitary_values['uacf7_enable_product_dropdown'] = $input['uacf7_enable_product_dropdown'];
		}

        return apply_filters( 'uacf7_save_admin_menu', $sanitary_values, $input );
	}
    
    public function uacf7_section_info() {
		//Nothing to say
	}
    
    /*
    * Section- Extra fields
    */
    public function uacf7_setting_section_fields_callback() {
		echo '<h3>Extra Fields</h3>';
	}
    
    /*
    * Section- WooCommerce Integration
    */
    public function uacf7_setting_section_woo_callback() {
		echo '<h3>WooCommerce Integration</h3>';
	}
    
    /*
    * Field - Enable redirection
    */
	public function uacf7_enable_redirection_callback() {
		printf(
			'<input type="checkbox" name="uacf7_option_name[uacf7_enable_redirection]" id="uacf7_enable_redirection" %s>', uacf7_checked('uacf7_enable_redirection')
		);
	}
    
    /*
    * Field - Enable conditional field
    */
    public function uacf7_enable_conditional_field_callback() {
		printf(
			'<input type="checkbox" name="uacf7_option_name[uacf7_enable_conditional_field]" id="uacf7_enable_conditional_field" %s>', uacf7_checked('uacf7_enable_conditional_field')
		);
	}
    
    /*
    * Field - Enable field column
    */
    public function uacf7_enable_field_column_callback() {
		printf(
			'<input type="checkbox" name="uacf7_option_name[uacf7_enable_field_column]" id="uacf7_enable_field_column" %s>', uacf7_checked('uacf7_enable_field_column')
		);
	}
    
    /*
    * Field - Enable Placeholder
    */
    public function uacf7_enable_placeholder_callback() {
		printf(
			'<input type="checkbox" name="uacf7_option_name[uacf7_enable_placeholder]" id="uacf7_enable_placeholder" %s>', uacf7_checked('uacf7_enable_placeholder')
		);
	}
    
    /*
    * Field - Enable Form Styler
    */
    public function uacf7_enable_uacf7style_callback() {
		printf(
			'<input type="checkbox" name="uacf7_option_name[uacf7_enable_uacf7style]" id="uacf7_enable_uacf7style" %s>', uacf7_checked('uacf7_enable_uacf7style')
		);
	}
    
    /*
    * Field - Enable Multistep
    */
    public function uacf7_enable_multistep_callback() {
		printf(
			'<input type="checkbox" name="uacf7_option_name[uacf7_enable_multistep]" id="uacf7_enable_multistep" %s>', uacf7_checked('uacf7_enable_multistep')
		);
	}
    
    /*
    * Field - Enable post submission
    */
    public function uacf7_enable_post_submission_callback() {
		printf(
			'<input type="checkbox" name="uacf7_option_name[uacf7_enable_post_submission]" id="uacf7_enable_post_submission" %s> <span class="uacf7-post-sub-pro-link"><a style="color:red" target="_blank" href="https://live.themefic.com/ultimate-cf7/pro">(Pro)</a></span>', uacf7_checked('uacf7_enable_post_submission')
		);
	}
    
    /*
    * Field - Enable star rating
    */
    public function uacf7_enable_star_rating_callback(){
        printf(
			'<input type="checkbox" name="uacf7_option_name[uacf7_enable_star_rating]" id="uacf7_enable_star_rating" %s>', uacf7_checked('uacf7_enable_star_rating')
		);
    }
    
    /*
    * Field - Enable product dropdown
    */
    public function uacf7_enable_product_dropdown_callback(){
        printf(
			'<input type="checkbox" name="uacf7_option_name[uacf7_enable_product_dropdown]" id="uacf7_enable_product_dropdown" %s>', uacf7_checked('uacf7_enable_product_dropdown')
		);
    }
    
    /*
    * Field - Enable product dropdown
    */
    public function uacf7_enable_product_auto_cart_callback(){
        printf(
			'<input type="checkbox" name="uacf7_option_name[uacf7_enable_product_auto_cart]" id="uacf7_enable_product_auto_cart" %s> <span class="uacf7-pro-link"><a style="color:red" target="_blank" href="https://live.themefic.com/ultimate-cf7/pro">(Pro)</a></span>', uacf7_checked('uacf7_enable_product_auto_cart')
		);
    }
}

/*
* Object - UACF7_Admin_Menu
*/
$uacf7_admin_menu = new UACF7_Admin_Menu();

// Link to settings page from plugins screen
add_filter( 'plugin_action_links_ultimate-addons-for-contact-form-7/ultimate-addons-for-contact-form-7.php', 'bafg_action_links' );
function bafg_action_links ( $links ) {
    $settings = esc_html__('Settings','ultimate-addons');
    $settings_link = array(
        '<a href="' . admin_url( 'admin.php?page=ultimate-addons' ) . '">'.$settings.'</a>',
    );
    return array_merge( $links, $settings_link );
}