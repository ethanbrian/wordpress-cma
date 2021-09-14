<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
Function: uacf7_checked
Return: checked
*/
if( !function_exists('uacf7_checked') ){
    function uacf7_checked( $name ){
    
        //Get settings option
        $uacf7_options = get_option( apply_filters( 'uacf7_option_name', 'uacf7_option_name' ) );

        if( isset( $uacf7_options[$name] ) && $uacf7_options[$name] === 'on' ) {
            return 'checked';
        }
    }
}

/*
* Hook: uacf7_multistep_pro_features
* Multistep pro features demo
*/
add_action( 'uacf7_multistep_pro_features', 'uacf7_multistep_pro_features_demo', 5, 2 );
function uacf7_multistep_pro_features_demo( $all_steps, $form_id ){
    
    if( empty(array_filter($all_steps)) ) return;
    ?>
    <div class="multistep_fields_row">
    <?php
    $step_count = 1;
    foreach( $all_steps as $step ) {
        ?>
        <h3><strong>Step <?php echo $step_count; ?> <a style="color:red" target="_blank" href="https://live.themefic.com/ultimate-cf7/pro">(Pro)</a></strong></h3>
        <?php
        if( $step_count == 1 ){
            ?>
            <div>
               <p><label for="<?php echo 'next_btn_'.$step->name; ?>">Change next button text for this Step</label></p>
               <input id="<?php echo 'next_btn_'.$step->name; ?>" type="text" name="" value="" placeholder="<?php echo esc_html__('Next','ultimate-addons-cf7-pro') ?>">
            </div>
            <?php
        } else {

            if( count($all_steps) == $step_count ) {
                ?>
                <div>
                   <p><label for="<?php echo 'prev_btn_'.$step->name; ?>">Change previus button text for this Step</label></p>
                   <input id="<?php echo 'prev_btn_'.$step->name; ?>" type="text" name="" value="" placeholder="<?php echo esc_html__('Previous','ultimate-addons-cf7-pro') ?>">
                </div>
                <?php

            } else {
                ?>
                <div class="multistep_fields_row-">
                    <div class="multistep_field_column">
                       <p><label for="<?php echo 'prev_btn_'.$step->name; ?>">Change previus button text for this Step</label></p>
                       <input id="<?php echo 'prev_btn_'.$step->name; ?>" type="text" name="" value="" placeholder="<?php echo esc_html__('Previous','ultimate-addons-cf7-pro') ?>">
                    </div>

                    <div class="multistep_field_column">
                       <p><label for="<?php echo 'next_btn_'.$step->name; ?>">Change next button text for this Step</label></p>
                       <input id="<?php echo 'next_btn_'.$step->name; ?>" type="text" name="" value="" placeholder="<?php echo esc_html__('Next','ultimate-addons-cf7-pro') ?>">
                    </div>
                </div>
                <?php
            }

        }
        ?>
        <div class="uacf7_multistep_progressbar_image_row">
           <p><label for="<?php echo esc_attr('uacf7_progressbar_image_'.$step->name); ?>">Add pregressbar image for this step</label></p>
           <input class="uacf7_multistep_progressbar_image" id="<?php echo esc_attr('uacf7_progressbar_image_'.$step->name); ?>" type="url" name="" value=""> <a class="button-primary" href="#">Add or Upload Image</a>
        </div>
        <?php
        $step_count++;
    }
    ?>
    </div>
    <?php
}

/*
* Progressbar style
*/
add_action( 'uacf7_multistep_before_form', 'uacf7_multistep_progressbar_style', 10 );
function uacf7_multistep_progressbar_style( $form_id ) {
    $uacf7_multistep_circle_width = get_post_meta( $form_id, 'uacf7_multistep_circle_width', true ); 
    $uacf7_multistep_circle_height = get_post_meta( $form_id, 'uacf7_multistep_circle_height', true ); 
    $uacf7_multistep_circle_bg_color = get_post_meta( $form_id, 'uacf7_multistep_circle_bg_color', true ); 
    $uacf7_multistep_circle_font_color = get_post_meta( $form_id, 'uacf7_multistep_circle_font_color', true ); 
    $uacf7_multistep_circle_border_radious = get_post_meta( $form_id, 'uacf7_multistep_circle_border_radious', true ); 
    $uacf7_multistep_font_size = get_post_meta( $form_id, 'uacf7_multistep_font_size', true ); 
    ?>
    <style>
    .steps-form .steps-row .steps-step .btn-circle {
        <?php if(!empty($uacf7_multistep_circle_width)) echo 'width: '.esc_attr($uacf7_multistep_circle_width).'px;'; ?>
        <?php if(!empty($uacf7_multistep_circle_height)) echo 'height: '.esc_attr($uacf7_multistep_circle_height).'px;'; ?>
        <?php if($uacf7_multistep_circle_border_radious != '' ) echo 'border-radius: '.$uacf7_multistep_circle_border_radious.'px;'; ?>
        <?php if(!empty($uacf7_multistep_circle_height)) echo 'line-height: '.esc_attr($uacf7_multistep_circle_height).'px;'; ?>
        <?php if(!empty($uacf7_multistep_circle_bg_color)) echo 'background-color: '.esc_attr($uacf7_multistep_circle_bg_color).' !important;'; ?>
        <?php if(!empty($uacf7_multistep_circle_font_color)) echo 'color: '.esc_attr($uacf7_multistep_circle_font_color).';'; ?>
        <?php if(!empty($uacf7_multistep_font_size)) echo 'font-size: '.esc_attr($uacf7_multistep_font_size).'px;'; ?>
    }
	.steps-form .steps-row .steps-step .btn-circle img {
		<?php if( $uacf7_multistep_circle_border_radious != 0 ) echo 'border-radius: '.$uacf7_multistep_circle_border_radious.'px !important;'; ?>
	}
    .steps-form .steps-row .steps-step .btn-circle.uacf7-btn-active,
    .steps-form .steps-row .steps-step .btn-circle:hover,
    .steps-form .steps-row .steps-step .btn-circle:focus,
    .steps-form .steps-row .steps-step .btn-circle:active{
        <?php if(!empty($uacf7_multistep_circle_bg_color)) echo 'background-color: '.esc_attr($uacf7_multistep_circle_bg_color).' !important;'; ?>
        <?php if(!empty($uacf7_multistep_circle_font_color)) echo 'color: '.esc_attr($uacf7_multistep_circle_font_color).';'; ?>
    }
    .steps-form .steps-row .steps-step p {
        <?php if(!empty($uacf7_multistep_font_size)) echo 'font-size: '.esc_attr($uacf7_multistep_font_size).'px;'; ?>
    }
    .steps-form .steps-row::before {
        <?php if(!empty($uacf7_multistep_circle_height)) echo 'top: '.esc_attr($uacf7_multistep_circle_height / 2).'px;'; ?>
    }
    </style>
    <?php
}