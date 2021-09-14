<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UACF7_STAR_RATING {
    /*
    * Construct function
    */
    public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_script' ) );
        add_action('wpcf7_init', array($this, 'add_shortcodes'));
        add_action( 'admin_init', array( $this, 'tag_generator' ) );
    }
	
    /*
	* Enqueue scripts
	*/
    public function enqueue_frontend_script() {        
        wp_enqueue_style( 'uacf7-star-rating-style', UACF7_ADDONS . '/star-rating/assets/css/star-rating.css' );
        wp_enqueue_style( 'uacf7-fontawesome', UACF7_ADDONS . '/star-rating/assets/css/all.css' );
    }
    
    /*
    * Create form tag: uacf7_star_rating
    */
    public function add_shortcodes() {
        
		wpcf7_add_form_tag( array('uacf7_star_rating','uacf7_star_rating*'), array( $this, 'uacf7_star_rating_cb' ), true );
  
    }
    
    /*
    * Field: Post title
    */
	public function uacf7_star_rating_cb($tag){
        
        ob_start();
        $validation_error = wpcf7_get_validation_error( $tag->name );

        $class = wpcf7_form_controls_class( $tag->type );

        if ( $validation_error ) {
            $class .= ' wpcf7-not-valid';
        }
        
        $atts = array();
        
        $class .= ' uacf7-rating';
        
        $atts['class'] = $class;
        
        if ( $tag->is_required() ) {
            $atts['aria-required'] = 'true';
        }

        $atts['aria-invalid'] = $validation_error ? 'true' : 'false';

        $atts = wpcf7_format_atts( $atts );
        
        $selected = !empty($tag->get_option('selected', '', true)) ? $tag->get_option('selected', '', true) : '5';
        
        $star1 = !empty($tag->get_option('star1', '', true)) ? $tag->get_option('star1', '', true) : '1';
        $star2 = !empty($tag->get_option('star2', '', true)) ? $tag->get_option('star2', '', true) : '2';
        $star3 = !empty($tag->get_option('star3', '', true)) ? $tag->get_option('star3', '', true) : '3';
        $star4 = !empty($tag->get_option('star4', '', true)) ? $tag->get_option('star4', '', true) : '4';
        $star5 = !empty($tag->get_option('star5', '', true)) ? $tag->get_option('star5', '', true) : '5';
        
        $rating_icon = '<i class="fas fa-star"></i>';
        
        if( function_exists('uacf7_rating_icon') ) {
			
			if( !empty(uacf7_rating_icon($tag)) ) {

				$rating_icon = uacf7_rating_icon($tag);
			}
			
        }else {
			
			$get_icon = $tag->get_option('icon', '', true);
	
			switch ($get_icon) {
			  case 'star1':
				$rating_icon = '<i class="far fa-star"></i>';
				break;
			  case 'star2':
				$rating_icon = '✪';
				break;
			}
		}
        
        ?>
        
        <span class="wpcf7-form-control-wrap <?php echo esc_attr($tag->name); ?>">
        <span <?php echo $atts; ?>>
          <label>
            <input type="radio" name="<?php echo esc_attr($tag->name); ?>" value="<?php echo esc_attr($star1); ?>" <?php checked( $selected, '1', true ); ?> />
            <span class="icon"><?php echo $rating_icon; ?></span>
          </label>
          <label>
            <input type="radio" name="<?php echo esc_attr($tag->name); ?>" value="<?php echo esc_attr($star2); ?>" <?php checked( $selected, '2', true ); ?> />
            <span class="icon"><?php echo $rating_icon; ?></span>
            <span class="icon"><?php echo $rating_icon; ?></span>
          </label>
          <label>
            <input type="radio" name="<?php echo esc_attr($tag->name); ?>" value="<?php echo esc_attr($star3); ?>" <?php checked( $selected, '3', true ); ?> />
            <span class="icon"><?php echo $rating_icon; ?></span>
            <span class="icon"><?php echo $rating_icon; ?></span>
            <span class="icon"><?php echo $rating_icon; ?></span>   
          </label>
          <label>
            <input type="radio" name="<?php echo esc_attr($tag->name); ?>" value="<?php echo esc_attr($star4); ?>" <?php checked( $selected, '4', true ); ?> />
            <span class="icon"><?php echo $rating_icon; ?></span>
            <span class="icon"><?php echo $rating_icon; ?></span>
            <span class="icon"><?php echo $rating_icon; ?></span>
            <span class="icon"><?php echo $rating_icon; ?></span>
          </label>
          <label>
            <input type="radio" name="<?php echo esc_attr($tag->name); ?>" value="<?php echo esc_attr($star5); ?>" <?php checked( $selected, '5', true ); ?> />
            <span class="icon"><?php echo $rating_icon; ?></span>
            <span class="icon"><?php echo $rating_icon; ?></span>
            <span class="icon"><?php echo $rating_icon; ?></span>
            <span class="icon"><?php echo $rating_icon; ?></span>
            <span class="icon"><?php echo $rating_icon; ?></span>
          </label>
          
          </span>
        <?php
        echo $validation_error;
		?>
        </span>
        <?php
        return ob_get_clean();
    }
    
    /*
    * Check validation for custom form fields
    */
    public function uacf7_star_rating_fields_validation_filter( $result, $tag ) {
        $name = $tag->name;

        if ( isset( $_POST[$name] )
        and is_array( $_POST[$name] ) ) {
            foreach ( $_POST[$name] as $key => $value ) {
                if ( '' === $value ) {
                    unset( $_POST[$name][$key] );
                }
            }
        }

        $empty = ! isset( $_POST[$name] ) || empty( $_POST[$name] ) && '0' !== $_POST[$name];

        if ( $tag->is_required() and $empty ) {
            $result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
        }

        return $result;
    }
    
    /*
    * Generate tag
    */
    public function tag_generator() {
        if (! function_exists('wpcf7_add_tag_generator'))
            return;

        wpcf7_add_tag_generator('uacf7_star_rating',
            __('Star Rating', 'ultimate-star-rating'),
            'uacf7-tg-pane-star-rating',
            array($this, 'tg_pane_star_rating')
        );
    }
    
    static function tg_pane_star_rating( $contact_form, $args = '' ) {
        $args = wp_parse_args( $args, array() );
        $uacf7_field_type = 'uacf7_star_rating';
        ?>
        <div class="control-box">
            <fieldset>                
                <table class="form-table">
                   <tbody>
                        
                        <tr>
                            <th scope="row"><label for="tag-generator-panel-text-name">Name</label></th>
                            <td><input type="text" name="name" class="tg-name oneline" value="rating" id="tag-generator-panel-text-name"></td>
                        </tr>
                        
                        <?php
					    ob_start();
						?>
						<tr>
							<th scope="row"><br><label for="tag-generator-panel-text-star-style"><?php echo esc_html__('Rating Icon','ultimate-addons-cf7'); ?></label></th>
							<td>
							<br>
							<label for="star1"><input id="star1" name="icon" class="option" type="radio" value="star1"> <?php echo esc_html('Star 1'); ?></label>
							<label for="star2"><input id="star2" name="icon" class="option" type="radio" value="star2"> <?php echo esc_html('Star 2'); ?></label>
							<label for=""><input id="" name="" class="" type="radio" value="" disabled> <?php echo esc_html('Heart'); ?> <a href="https://live.themefic.com/ultimate-cf7/star-rating/pro" style="color:red">(Pro)</a></label>
							<label for=""><input id="" name="" class="" type="radio" value="" disabled> <?php echo esc_html('Thumbs Up'); ?> <a href="https://live.themefic.com/ultimate-cf7/star-rating/pro" style="color:red">(Pro)</a></label>
							<label for=""><input id="" name="" class="" type="radio" value="" disabled> <?php echo esc_html('Smile'); ?> <a href="https://live.themefic.com/ultimate-cf7/star-rating/pro" style="color:red">(Pro)</a></label>
							<label for=""><input id="" name="" class="" type="radio" value="" disabled> <?php echo esc_html('Ok'); ?> <a href="https://live.themefic.com/ultimate-cf7/star-rating/pro" style="color:red">(Pro)</a></label>
							<br>
							<br>
							</td>
						</tr>
						<tr>
							<th><label for="tag-generator-panel-text-star-class">Icon Class</label></th>
							<td><input id="tag-generator-panel-text-star-class" type="text" placeholder="e.g: fa fa-star" disabled><a href="https://live.themefic.com/ultimate-cf7/star-rating/pro" style="color:red">(Pro)</a></td>
						</tr>
						<?php
					    $icon_field = ob_get_clean();
						echo apply_filters( 'uacf7_star_rating_tg_field', $icon_field );
					    ?>
                       
                        <tr>
                            <th scope="row"><label for="tag-generator-panel-text-selected">Default Selected Star</label></th>
                            <td>
                            <input name="selected" id="tag-generator-panel-text-selected" class="tg-name oneline option" placeholder="5" />
                            <br>
                            <br>
                            <p>Change the values of star. Default value: 1,2,3,4,5</p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><label for="tag-generator-panel-text-star1">Star 1</label></th>
                            <td><input type="text" name="star1" class="tg-name oneline option" value="" id="tag-generator-panel-text-star1"></td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><label for="tag-generator-panel-text-star2">Star 2</label></th>
                            <td><input type="text" name="star2" class="tg-name oneline option" value="" id="tag-generator-panel-text-star2"></td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><label for="tag-generator-panel-text-star3">Star 3</label></th>
                            <td><input type="text" name="star3" class="tg-name oneline option" value="" id="tag-generator-panel-text-star3"></td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><label for="tag-generator-panel-text-star4">Star 4</label></th>
                            <td><input type="text" name="star4" class="tg-name oneline option" value="" id="tag-generator-panel-text-star3"></td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><label for="tag-generator-panel-text-star5">Star 5</label></th>
                            <td><input type="text" name="star5" class="tg-name oneline option" value="" id="tag-generator-panel-text-star4"></td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><label for="tag-generator-panel-text-class">Class attribute</label></th>
                            <td><input type="text" name="class" class="classvalue oneline option" id="tag-generator-panel-text-class"></td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>

        <div class="insert-box">
            <input type="text" name="<?php echo esc_attr($uacf7_field_type); ?>" class="tag code" readonly="readonly" onfocus="this.select()" />

            <div class="submitbox">
                <input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'ultimate-post-submission' ) ); ?>" />
            </div>
        </div>
        <?php
    }
}

new UACF7_STAR_RATING();