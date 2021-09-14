<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
* Adding multiple attribute field to dropdown tag generator
*/
add_filter( 'uacf7_tag_generator_multiple_select_field', 'uacf7_tag_generator_field_allow_multiple', 10 );
function uacf7_tag_generator_field_allow_multiple() {
    ?>
    <tr>
        <th scope="row"></th>
        <td><label for="tag-generator-panel-select-multiple"><input id="tag-generator-panel-select-multiple" type="checkbox" name="multiple" class="option"> Allow Multiple Product Selection</label><br><br></td>
    </tr>
    <?php
}

add_filter( 'uacf7_tag_generator_product_by_field', 'uacf7_tag_generator_product_by_field', 10 );
function uacf7_tag_generator_product_by_field(){
    ob_start(); ?>
    <tr>
        <th scope="row"><label for="product_by">Show Product By</label></th>
        <td>
            <label for="byID"><input id="byID" name="product_by" class="option" type="radio" value="id" checked> Product ID</label>

            <label for="byCategory"><input id="byCategory" name="product_by" class="option" type="radio" value="category"> Category</label>
        </td>
    </tr>
    <?php 
    return ob_get_clean();
}

/*
* Adding category field to dropdown tag generator
*/
add_filter( 'uacf7_tag_generator_product_id_field', 'uacf7_tag_generator_product_id_field', 10 );
if( !function_exists('uacf7_tag_generator_product_id_field') ) {
    function uacf7_tag_generator_product_id_field() {
        ob_start(); ?>
        <tr class="tag-generator-panel-product-id">
            <th scope="row"><label for="tag-generator-panel-product-id">Product ID</label></th>
            <td>
                <textarea class="values" name="values" id="tag-generator-panel-product-id" cols="30" rows="10"></textarea><br>One ID per line. <a style="color:blue" target="_blank" href="https://live.themefic.com/ultimate-cf7/wp-content/uploads/sites/7/2021/05/Screenshot_21.png">Click here</a> to know how to find Product ID.
            </td>
        </tr>
        <?php 
        return ob_get_clean();
    }
}

/*
* Adding category field to dropdown tag generator
*/
add_filter( 'uacf7_tag_generator_product_category_field', 'uacf7_tag_generator_product_dropdown_categories', 10 );

if( !function_exists('uacf7_tag_generator_product_dropdown_categories') ) {
    
    function uacf7_tag_generator_product_dropdown_categories(){
        ob_start();
        ?>
        <tr class="tag-generator-panel-product-category">   
           <th><label for="tag-generator-panel-product-category">Product category</label></th>                     
            <td>
            <?php
            $taxonomies = get_terms( array(
                'taxonomy' => 'product_cat',
                'hide_empty' => true
            ) );

            if ( !empty(array_filter($taxonomies)) ) :
                $output = '<select class="values" name="values" id="tag-generator-panel-product-category">';
                $output .= '<option value="">All</option>';
                foreach( $taxonomies as $category ) {
                    $output.= '<option value="'. esc_attr( $category->slug ) .'">'. esc_html( $category->name ) .'</option>';
                }
                $output.='</select>';

                echo $output;

            endif;    
            ?>
            </td>
        </tr>
        <?php 
        $product_dropdown_html = ob_get_clean();

        echo $product_dropdown_html;
    }
}

/*
* Product dropdown query by category
*/
add_filter( 'uacf7_product_dropdown_query', 'uacf7_product_dropdown_query', 10, 3 );

if( !function_exists('uacf7_product_dropdown_query') ){
    function uacf7_product_dropdown_query($args, $values, $product_by){
        
        if( !empty( array_filter($values) ) ){
            $query_values = array();

            foreach ( $values as $key => $value ) {
                $query_values[] = $value;
            }
            
            if( $product_by == 'category' ){
                
                $args['tax_query'] = array(
                    array(
                        'taxonomy' => 'product_cat',
                        'field'    => 'slug',
                        'terms'    => $query_values,
                    ),
                 );
                
            }elseif( $product_by == 'id' ) {
                $args['post__in'] = $query_values;
            }
            
        }
        
        return $args;
    }
}

/*
* Product add to cart after submiting form by ajax
*/
add_action( 'wp_ajax_uacf7_ajax_add_to_cart_product', 'uacf7_ajax_add_to_cart_product' );
add_action( 'wp_ajax_nopriv_uacf7_ajax_add_to_cart_product', 'uacf7_ajax_add_to_cart_product' );
function uacf7_ajax_add_to_cart_product() {
    
    $product_ids = $_POST['product_ids'];
    
    foreach( $product_ids as $product_id ) :
    
    $product_cart_id = WC()->cart->generate_cart_id( $product_id );

    if( ! WC()->cart->find_product_in_cart( $product_cart_id ) ){

        WC()->cart->add_to_cart( $product_id );

    }
    
    endforeach;
    
    die();
}

/*
* Adding 'multiple' attribure
*/
add_filter( 'uacf7_multiple_attribute', 'uacf7_multiple_attribute', 10 );
function uacf7_multiple_attribute(){
    return 'multiple';
}

/*
Admin menu- Save auto product cart
*/
add_filter( 'uacf7_save_admin_menu', 'uacf7_save_auto_product_cart', 10, 2 );
function uacf7_save_auto_product_cart( $sanitary_values, $input ){
    
    if ( isset( $input['uacf7_enable_product_auto_cart'] ) ) {
        $sanitary_values['uacf7_enable_product_auto_cart'] = $input['uacf7_enable_product_auto_cart'];
    }
    return $sanitary_values;
}

add_action( 'wpcf7_after_save', 'uacf7_save_contact_form_auto_cart' );
function uacf7_save_contact_form_auto_cart( $form ) {

    if ( ! isset( $_POST ) || empty( $_POST ) ) {
        return;
    }
    if ( ! wp_verify_nonce( $_POST['uacf7_auto_cart_nonce'], 'uacf7_auto_cart_nonce_action' ) ) {
        return;
    }

    update_post_meta( $form->id(), 'uacf7_enable_product_auto_cart', $_POST['uacf7_enable_product_auto_cart'] );

}

/*
* Hook: uacf7_multistep_pro_features
* Multistep pro features
*/

//Removed pro demo fields
add_action( 'after_setup_theme', 'remove_uacf7_multistep_pro_features_demo', 0 );
function remove_uacf7_multistep_pro_features_demo() {

    remove_action( 'uacf7_multistep_pro_features', 'uacf7_multistep_pro_features_demo', 5, 2 );
}

//Added pro fields
add_action( 'uacf7_multistep_pro_features', 'uacf7_multistep_pro_features', 10, 2 );
function uacf7_multistep_pro_features( $all_steps, $form_id ){
    
    if( empty(array_filter($all_steps)) ) return;
    ?>
    <div class="multistep_fields_row">
    <?php
    $step_count = 1;
    foreach( $all_steps as $step ) {
        ?>
        <h3><strong>Step <?php echo $step_count; ?></strong></h3>
        <?php
        if( $step_count == 1 ){
            ?>
            <div>
               <p><label for="<?php echo 'next_btn_'.$step->name; ?>">Change next button text for this Step</label></p>
               <input id="<?php echo 'next_btn_'.$step->name; ?>" type="text" name="<?php echo 'next_btn_'.$step->name; ?>" value="<?php echo esc_html( get_option('next_btn_'.$step->name) ); ?>" placeholder="<?php echo esc_html__('Next','ultimate-addons-cf7-pro') ?>">
            </div>
            <?php
        } else {

            if( count($all_steps) == $step_count ) {
                ?>
                <div>
                   <p><label for="<?php echo 'prev_btn_'.$step->name; ?>">Change previus button text for this Step</label></p>
                   <input id="<?php echo 'prev_btn_'.$step->name; ?>" type="text" name="<?php echo 'prev_btn_'.$step->name; ?>" value="<?php echo esc_html(get_option('prev_btn_'.$step->name)); ?>" placeholder="<?php echo esc_html__('Previous','ultimate-addons-cf7-pro') ?>">
                </div>
                <?php

            } else {
                ?>
                <div class="multistep_fields_row-">
                    <div class="multistep_field_column">
                       <p><label for="<?php echo 'prev_btn_'.$step->name; ?>">Change previus button text for this Step</label></p>
                       <input id="<?php echo 'prev_btn_'.$step->name; ?>" type="text" name="<?php echo 'prev_btn_'.$step->name; ?>" value="<?php echo esc_html(get_option('prev_btn_'.$step->name)); ?>" placeholder="<?php echo esc_html__('Previous','ultimate-addons-cf7-pro') ?>">
                    </div>

                    <div class="multistep_field_column">
                       <p><label for="<?php echo 'next_btn_'.$step->name; ?>">Change next button text for this Step</label></p>
                       <input id="<?php echo 'next_btn_'.$step->name; ?>" type="text" name="<?php echo 'next_btn_'.$step->name; ?>" value="<?php echo esc_html(get_option('next_btn_'.$step->name)); ?>" placeholder="<?php echo esc_html__('Next','ultimate-addons-cf7-pro') ?>">
                    </div>
                </div>
                <?php
            }

        }
        ?>
        <div class="uacf7_multistep_progressbar_image_row">
           <p><label for="<?php echo esc_attr('uacf7_progressbar_image_'.$step->name); ?>">Add pregressbar image for this step</label></p>
           <input class="uacf7_multistep_progressbar_image" id="<?php echo esc_attr('uacf7_progressbar_image_'.$step->name); ?>" type="url" name="<?php echo esc_attr('uacf7_progressbar_image_'.$step->name); ?>" value="<?php echo get_option('uacf7_progressbar_image_'.$step->name); ?>"> <a class="button-primary uacf7_multistep_image_upload" href="#">Add or Upload Image</a>
        </div>
        <?php
        $step_count++;
    }
    ?>
    </div>
    <?php
}

add_filter( 'uacf7_multistep_save_pro_feature', 'uacf7_multistep_save_pro_feature', 2, 10 );
function uacf7_multistep_save_pro_feature( $f, $form, $all_steps ){
    
    $step_titles = array();
    $step_names = array();
    foreach ($all_steps as $step) {
        $step_titles[] = (is_array($step->values) && !empty($step->values)) ? $step->values[0] : '';

        $step_names[] = !empty($step->name) ? $step->name : '';

        update_option( 'prev_btn_'.$step->name, $_POST['prev_btn_'.$step->name] );
        update_option( 'next_btn_'.$step->name, $_POST['next_btn_'.$step->name] );
        update_option( 'uacf7_progressbar_image_'.$step->name, $_POST['uacf7_progressbar_image_'.$step->name] );

    }

    update_post_meta( $form->id(), 'uacf7_multistep_steps', count($all_steps) );

    update_post_meta( $form->id(), 'uacf7_multistep_steps_title', $step_titles );

    update_post_meta( $form->id(), 'uacf7_multistep_steps_names', $step_names );

    update_post_meta( $form->id(), 'uacf7_enable_multistep_next_button', $_POST['uacf7_enable_multistep_next_button'] );

    update_post_meta( $form->id(), 'uacf7_multistep_previus_button', $_POST['uacf7_multistep_previus_button'] );
        
}

add_action( 'uacf7_progressbar_image', 'uacf7_progressbar_image', 10 );
function uacf7_progressbar_image($step_id) {
    $uacf7_progressbar_image = get_option('uacf7_progressbar_image_'.$step_id);
    if( $uacf7_progressbar_image != '' ){
        echo '<img src="'.esc_url( $uacf7_progressbar_image ).'">';
    }
}
