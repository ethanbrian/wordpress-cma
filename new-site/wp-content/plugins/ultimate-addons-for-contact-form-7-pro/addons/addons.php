<?php

$uacf7_options = get_option( 'uacf7_option_name' );

if( isset($uacf7_options['uacf7_enable_product_auto_cart']) && $uacf7_options['uacf7_enable_product_auto_cart'] === 'on' )
{
    require_once( 'auto-add-to-cart/auto-add-to-cart.php' );
}
