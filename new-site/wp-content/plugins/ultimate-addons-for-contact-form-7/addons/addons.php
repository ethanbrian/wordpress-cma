<?php

if( !function_exists( 'uacf7_addons_included' ) ) {

    function uacf7_addons_included(){
    
        //Addon - Ultimate redirect
        if( uacf7_checked('uacf7_enable_redirection') != '' ){
            require_once( 'redirection/redirect.php' );
        }

        //Addon - Ultimate conditional field
        if( uacf7_checked('uacf7_enable_conditional_field') != '' ){
            require_once( 'conditional-field/conditional-fields.php' );
        }

        //Addon - Ultimate field Column
        if( uacf7_checked('uacf7_enable_field_column') != '' ){
            require_once( 'column/column.php' );
        }

        //Addon - Ultimate Placeholder
        if( uacf7_checked('uacf7_enable_placeholder') != '' ){
            require_once( 'placeholder/placeholder.php' );
        }

        //Addon - Ultimate Placeholder
        if( uacf7_checked('uacf7_enable_multistep') != '' ){
            require_once( 'multistep/multistep.php' );
        }

        //Addon - Ultimate Style
        if( uacf7_checked('uacf7_enable_uacf7style') != '' ){
            require_once( 'styler/uacf7style.php' );
        }

        //Addon - Ultimate Product Dropdown
        if( uacf7_checked('uacf7_enable_product_dropdown') != '' ){
            require_once( 'product-dropdown/product-dropdown.php' );
        }

        //Addon - Ultimate Star Rating
        if( uacf7_checked('uacf7_enable_star_rating') != '' ){
            require_once( 'star-rating/star-rating.php' );
        }
		
    }
}

uacf7_addons_included();