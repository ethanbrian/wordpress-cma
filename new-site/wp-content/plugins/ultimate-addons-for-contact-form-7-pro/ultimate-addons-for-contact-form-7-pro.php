<?php
/**
 * Plugin Name: Ultimate Addons for Contact Form 7 Pro
 * Plugin URI: https://live.themefic.com/ultimate-cf7/pro
 * Description: Extend the power of Ultimate Addons for Contact Form 7 with Pro. More advanced function crafted for your Website's needs.
 * Version: 1.1.1
 * Author: Themefic
 * Author URI: https://themefic.com/
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: ultimate-addons-cf7-pro
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once "C4BFEE022.php";
class UltimateAddonsforContactForm7Pro_M4BFEE022 {
    public $plugin_file=__FILE__;
    public $responseObj;
    public $licenseMessage;
    public $showMessage=false;
    public $slug="ultimate-addons-for-contact-form-7-pro";
    function __construct() {
        add_action('uacf7_admin_tab_button', [ $this, 'uacf7_admin_tab_button_license' ], 10);
        add_action( 'admin_print_styles', [ $this, 'SetAdminStyle' ] );
        $licenseKey=get_option("UltimateAddonsforContactForm7Pro_lic_Key","");
        $liceEmail=get_option( "UltimateAddonsforContactForm7Pro_lic_email","");
        C4BFEE022::addOnDelete(function(){
           delete_option("UltimateAddonsforContactForm7Pro_lic_Key");
        });
		
		add_action( 'admin_init', array( $this, 'uacf7_pro_license_notice_dismissed' ) );
		
        if(C4BFEE022::CheckWPPlugin($licenseKey,$liceEmail,$this->licenseMessage,$this->responseObj,__FILE__)){
            
            add_action('uacf7_admin_tab_content', [$this,'ActiveAdminMenu'], 10);
            
            add_action( 'admin_post_UltimateAddonsforContactForm7Pro_el_deactivate_license', [ $this, 'action_deactivate_license' ] );
            //$this->licenselMessage=$this->mess;

        }else{
			
			add_action( 'admin_notices',  array( $this, 'uacf7_pro_license_notice' ) );
			
            if(!empty($licenseKey) && !empty($this->licenseMessage)){
               $this->showMessage=true;
            }
            update_option("UltimateAddonsforContactForm7Pro_lic_Key","") || add_option("UltimateAddonsforContactForm7Pro_lic_Key","");
            add_action( 'admin_post_UltimateAddonsforContactForm7Pro_el_activate_license', [ $this, 'action_activate_license' ] );
            add_action('uacf7_admin_tab_content', [$this,'InactiveMenu'], 10);
        }
    }
    function uacf7_admin_tab_button_license(){
        ?>
        <a class="tablinks" onclick="uacf7_settings_tab(event, 'uacf7_pro_license')">License</a>
        <?php
    }
    function SetAdminStyle() {
        wp_register_style( "UltimateAddonsforContactForm7ProLic", plugins_url("_lic_style.css",$this->plugin_file),10);
        wp_enqueue_style( "UltimateAddonsforContactForm7ProLic" );
    }
    function ActiveAdminMenu(){        
        ?>
        <div id="uacf7_pro_license" class="uacf7-tabcontent">
            <?php $this->Activated(); ?>
        </div>
        <?php
    }
    function InactiveMenu() {
        ?>
        <div id="uacf7_pro_license" class="uacf7-tabcontent">
            <?php $this->LicenseForm(); ?>
        </div>
        <?php
        
    }
    function action_activate_license(){
        check_admin_referer( 'el-license' );
        $licenseKey=!empty($_POST['el_license_key'])?$_POST['el_license_key']:"";
        $licenseEmail=!empty($_POST['el_license_email'])?$_POST['el_license_email']:"";
        update_option("UltimateAddonsforContactForm7Pro_lic_Key",$licenseKey) || add_option("UltimateAddonsforContactForm7Pro_lic_Key",$licenseKey);
        update_option("UltimateAddonsforContactForm7Pro_lic_email",$licenseEmail) || add_option("UltimateAddonsforContactForm7Pro_lic_email",$licenseEmail);
        update_option('_site_transient_update_plugins','');
        wp_safe_redirect(admin_url( 'admin.php?page=ultimate-addons'));
    }
    function action_deactivate_license() {
        check_admin_referer( 'el-license' );
        $message="";
        if(C4BFEE022::RemoveLicenseKey(__FILE__,$message)){
            update_option("UltimateAddonsforContactForm7Pro_lic_Key","") || add_option("UltimateAddonsforContactForm7Pro_lic_Key","");
            update_option('_site_transient_update_plugins','');
        }
        wp_safe_redirect(admin_url( 'admin.php?page=ultimate-addons'));
    }
    function Activated(){
        ?>
        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <input type="hidden" name="action" value="UltimateAddonsforContactForm7Pro_el_deactivate_license"/>
            <div class="el-license-container">
                <h3 class="uacf7-el-license-title"><?php _e("Ultimate Addons for Contact Form 7 Pro License Info",$this->slug);?> </h3>
                <hr>
                <ul class="el-license-info">
                <li>
                    <div>
                        <span class="el-license-info-title"><?php _e("Status",$this->slug);?></span>

                        <?php if ( $this->responseObj->is_valid ) : ?>
                            <span class="el-license-valid"><?php _e("Valid",$this->slug);?></span>
                        <?php else : ?>
                            <span class="el-license-valid"><?php _e("Invalid",$this->slug);?></span>
                        <?php endif; ?>
                    </div>
                </li>

                <li>
                    <div>
                        <span class="el-license-info-title"><?php _e("License Type",$this->slug);?></span>
                        <?php echo $this->responseObj->license_title; ?>
                    </div>
                </li>

               <li>
                   <div>
                       <span class="el-license-info-title"><?php _e("License Expired on",$this->slug);?></span>
                       <?php echo $this->responseObj->expire_date;
                       if(!empty($this->responseObj->expire_renew_link)){
                           ?>
                           <a target="_blank" class="el-blue-btn" href="<?php echo $this->responseObj->expire_renew_link; ?>">Renew</a>
                           <?php
                       }
                       ?>
                   </div>
               </li>

               <li>
                   <div>
                       <span class="el-license-info-title"><?php _e("Support Expired on",$this->slug);?></span>
                       <?php
                           echo $this->responseObj->support_end;
                        if(!empty($this->responseObj->support_renew_link)){
                            ?>
                               <a target="_blank" class="el-blue-btn" href="<?php echo $this->responseObj->support_renew_link; ?>">Renew</a>
                            <?php
                        }
                       ?>
                   </div>
               </li>
                <li>
                    <div>
                        <span class="el-license-info-title"><?php _e("Your License Key",$this->slug);?></span>
                        <span class="el-license-key"><?php echo esc_attr( substr($this->responseObj->license_key,0,9)."XXXXXXXX-XXXXXXXX".substr($this->responseObj->license_key,-9) ); ?></span>
                    </div>
                </li>
                </ul>
                <div class="el-license-active-btn">
                    <?php wp_nonce_field( 'el-license' ); ?>
                    <?php submit_button('Deactivate'); ?>
                </div>
            </div>
        </form>
    <?php
    }

    function LicenseForm() {
        ?>
    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <input type="hidden" name="action" value="UltimateAddonsforContactForm7Pro_el_activate_license"/>
        <div class="el-license-container">
            <h3 class="uacf7-el-license-title"><?php _e("Ultimate Addons for Contact Form 7 Pro Licensing",$this->slug);?></h3>
            <hr>
            <?php
            if(!empty($this->showMessage) && !empty($this->licenseMessage)){
                ?>
                <div class="notice notice-error is-dismissible">
                    <p><?php echo _e($this->licenseMessage,$this->slug); ?></p>
                </div>
                <?php
            }
            ?>
            
            <div class="el-license-field">
                <label for="el_license_key"><?php _e("License code",$this->slug);?></label>
                <input type="text" class="regular-text code" name="el_license_key" size="50" placeholder="xxxxxxxx-xxxxxxxx-xxxxxxxx-xxxxxxxx" required="required">
            </div>
            <div class="el-license-field">
                <label for="el_license_key"><?php _e("Email Address",$this->slug);?></label>
                <?php
                    $purchaseEmail   = get_option( "UltimateAddonsforContactForm7Pro_lic_email", get_bloginfo( 'admin_email' ));
                ?>
                <input type="text" class="regular-text code" name="el_license_email" size="50" value="<?php echo $purchaseEmail; ?>" placeholder="" required="required">
                <div><small><?php _e("We will send update news of this product by this email address, don't worry, we hate spam",$this->slug);?></small></div>
            </div>
            <div class="el-license-active-btn">
                <?php wp_nonce_field( 'el-license' ); ?>
                <?php submit_button('Activate'); ?>
            </div>
        </div>
    </form>
        <?php
    }
	
	/*
    * Admin notice: Licanse activation error
    */
    function uacf7_pro_license_notice() {
		$user_id = get_current_user_id();
    		
		if ( get_user_meta( $user_id, 'uacf7_pro_license_notice_dismissed', true ) != 'closed' ) {
		?>
		<div class="wrap">
		<div class="notice notice-error">
			<p>
				<strong><?php echo esc_html__( 'Activate UACF7 Pro License.', 'bafg-pro' ); ?> </strong></p>
				
				<p><span>Please <a href="<?php echo admin_url('/admin.php?page=ultimate-addons'); ?>">activate your license</a> key to enable getting future updates for Ultimate addons for contact form 7 Pro.</span><p>
				
			<p><a class="button" href="<?php echo admin_url('?uacf7-license-dismissed'); ?>">Maybe later</a></p>
			
		</div>
		</div>

		<?php
		}
    }
	
	/*
	* Update license notice
	*/
	public function uacf7_pro_license_notice_dismissed() {
		$user_id = get_current_user_id();
		if ( isset($_GET['uacf7-license-dismissed']) ){
			update_user_meta( $user_id, 'uacf7_pro_license_notice_dismissed', 'closed' );
		}
	}
}

new UltimateAddonsforContactForm7Pro_M4BFEE022();

/*
* Class Ultimate_Addons_CF7_PRO
*/
class Ultimate_Addons_CF7_PRO {
			    
    public function __construct() {
        define( 'UACF7_PRO_URL', plugin_dir_url( __FILE__ ) );
        define( 'UACF7_PRO_ADDONS', UACF7_PRO_URL.'addons' );
        define( 'UACF7_PRO_PATH', plugin_dir_path( __FILE__ ) );

        require_once 'functions/functions.php';
        require_once 'addons/addons.php';
        do_action( 'uacf7_pro_include_addons' );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
        
    }

    public function enqueue_scripts() {
        wp_enqueue_script( 'uacf7-pro-scripts', UACF7_PRO_URL . 'assets/js/uacf7-pro-scripts.js', array('jquery'), null, true );
        wp_enqueue_style( 'uacf7-pro', UACF7_PRO_URL . 'assets/css/uacf7-pro-styles.css' );
        
        $auto_cart = $product_dropdown = '';
        
        if( function_exists('uacf7_checked') ){
            $product_dropdown = uacf7_checked('uacf7_enable_product_dropdown');
            $auto_cart = uacf7_checked('uacf7_enable_product_auto_cart');
        }
		
		$checkout_url = '';
		if(function_exists('wc_get_checkout_url')){ 
			$checkout_url = wc_get_checkout_url();
		}
		
		$cart_url = '';
		if(function_exists('wc_get_cart_url')){ 
			$cart_url = wc_get_cart_url();
		}
        
        wp_localize_script( 'uacf7-pro-scripts', 'uacf7_pro_object',
            array( 
                'checkout_page' => $checkout_url,
                'cart_page' => $cart_url,
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'product_dropdown' => $product_dropdown,
                'auto_cart' => $auto_cart,
            )
        );
        
    }

    public function admin_enqueue_scripts() {
        wp_enqueue_style( 'uacf7-pro-admin', UACF7_PRO_URL . 'assets/css/uacf7-pro-admin-styles.css' );
        wp_enqueue_script( 'uacf7-pro-admin-scripts', UACF7_PRO_URL . 'assets/js/uacf7-pro-admin.js', array('jquery'), null, true );
    }

}

//Initialize
add_action( 'plugins_loaded', 'ultimate_addons_pro_init' );
function ultimate_addons_pro_init(){
	
    if( class_exists('Ultimate_Addons_CF7')){

        new Ultimate_Addons_CF7_PRO();
        
        //Pro activated: yes
        update_option('uacf7_pro_activated', 'yes');

    }else {
        
        //Pro activated: no
        update_option('uacf7_pro_activated', 'no');
        add_action( 'admin_notices', 'uacf7_admin_notice_error' );
    }
}

function uacf7_admin_notice_error() {
    ?>
    <div class="notice notice-error is-dismissible">
        <p><?php echo esc_html__( 'Ultimate Addons For Contact Form 7 Pro requires', 'ultimate-addons-cf7-pro' );
        echo '<a href="'.admin_url('plugin-install.php?s=Ultimate+Addons+For+Contact+Form+7&tab=search&type=term').'"> Ultimate Addons For Contact Form 7 </a>'; echo esc_html__('to be installed and active.', 'ultimate-addons-cf7-pro' ); ?>
        </p>
    </div>
    <?php
}