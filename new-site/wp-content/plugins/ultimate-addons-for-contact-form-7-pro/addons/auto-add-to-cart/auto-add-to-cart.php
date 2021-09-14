<?php
 
//Get settings option
add_action( 'wpcf7_editor_panels', 'uacf7_add_panel' );
add_filter( 'wpcf7_contact_form_properties', 'uacf7_properties', 10, 2 );

/*
* Function create tab panel
*/
function uacf7_add_panel( $panels ) {
    $panels['uacf7-auto-product-cart-panel'] = array(
        'title'    => __( 'Ultimate Auto Product Cart', 'ultimate-addons-cf7' ),
        'callback' => 'uacf7_create_auto_product_cart_panel_fields',
    );
    return $panels;
}

/*
* Function Auto product cart field
*/
function uacf7_create_auto_product_cart_panel_fields( $post ) {
    // get existing value
    $auto_cart = get_post_meta( $post->id(), 'uacf7_enable_product_auto_cart', true );

    ?>
    <fieldset>
       <div class="ultimate-product-dropdown-admin">
          <h3>Auto Add to Cart & Checkout after Form Submission</h3>
           <label for="uacf7_enable_product_auto_cart"> Enable for this form 
               <input id="uacf7_enable_product_auto_cart" type="checkbox" name="uacf7_enable_product_auto_cart" <?php checked( 'on', $auto_cart ); ?> >
           </label>
       </div>
    </fieldset>
    <?php
     wp_nonce_field( 'uacf7_auto_cart_nonce_action', 'uacf7_auto_cart_nonce' );
}

function uacf7_properties($properties, $cfform) {

    if (!is_admin() || (defined('DOING_AJAX') && DOING_AJAX)) { 

        $form = $properties['form'];

        $auto_cart = get_post_meta( $cfform->id(), 'uacf7_enable_product_auto_cart', true );

        if( $auto_cart == 'on' ) {

            ob_start();

            $auto_cart = 'uacf7_auto_cart_'.$cfform->id();

            echo '<div class="'.$auto_cart.'">'.$form.'</div>';

            $properties['form'] = ob_get_clean();
        }

    }

    return $properties;
}