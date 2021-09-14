<?php
/**
 * Login form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/global/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @package 	WooCommerce/Templates
 * @version     3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( is_user_logged_in() ) {
	return;
}

?>
<form method="post" class="woocomerce-form woocommerce-form-login login" <?php if ( $hidden ) echo 'style="display:none;"'; ?>>

	<?php do_action( 'woocommerce_login_form_start' ); ?>

	<?php if ( $message ) echo wpautop( wptexturize( $message ) ); ?>
	<div class="mkd-woo-form-row">
		<p class="form-row form-row-first">
			<input type="text" placeholder="<?php esc_attr_e( 'Username or email', 'libero' ); ?>" class="input-text" name="username" id="username" />
		</p>
		<p class="form-row form-row-last">
			<input class="input-text" placeholder="<?php esc_attr_e( 'Password', 'libero' ); ?>" type="password" name="password" id="password" />
		</p>	
		<p class="lost_password">
			<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'libero' ); ?></a>
		</p>
	</div>
	<div class="clear"></div>

	<?php do_action( 'woocommerce_login_form' ); ?>
	
	<p class="form-row">
		<label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
			<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php esc_html_e( 'Remember me', 'libero' ); ?></span>
		</label>
		<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
		<input type="hidden" name="redirect" value="<?php echo esc_url( $redirect ) ?>" />
		<button type="submit" class="woocommerce-button button woocommerce-form-login__submit" name="login" value="<?php esc_attr_e( 'Login', 'libero' ); ?>"><?php esc_html_e( 'Login', 'libero' ); ?></button>
	</p>

	<div class="clear"></div>

	<?php do_action( 'woocommerce_login_form_end' ); ?>

</form>
