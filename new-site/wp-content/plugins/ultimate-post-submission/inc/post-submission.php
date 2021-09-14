<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UACF7_POST_SUBMISSION {
    
    private $hidden_fields = array();
    /*
    * Construct function
    */
    public function __construct() {
        add_action( 'wpcf7_init', array($this, 'add_shortcodes') );
        add_action( 'admin_init', array( $this, 'tag_generator' ) );
        
        add_action( 'wp_enqueue_scripts', array($this, 'enqueue_script' ) );
        
        add_action( 'wpcf7_editor_panels', array( $this, 'uacf7_cf_add_panel' ) );
		
        add_filter( 'wpcf7_validate_uacf7_post_title', array($this, 'uacf7_post_submission_fields_validation_filter'), 10, 2 );
        
        add_filter( 'wpcf7_validate_uacf7_post_title*', array($this,'uacf7_post_submission_fields_validation_filter'), 10, 2 );
        
        add_filter( 'wpcf7_validate_uacf7_post_taxonomy', array($this, 'uacf7_post_submission_fields_validation_filter'), 10, 2 );
        
        add_filter( 'wpcf7_validate_uacf7_post_taxonomy*', array($this,'uacf7_post_submission_fields_validation_filter'), 10, 2 );
        
        add_filter( 'wpcf7_validate_uacf7_post_content', array($this, 'uacf7_post_submission_fields_validation_filter'), 10, 2 );
        
        add_filter( 'wpcf7_validate_uacf7_post_content*', array($this,'uacf7_post_submission_fields_validation_filter'), 10, 2 );
        
        add_filter( 'wpcf7_validate_uacf7_post_thumbnail', array($this, 'uacf7_post_submission_thumbnail_validation_filter'), 10, 2 );
        
        add_filter( 'wpcf7_validate_uacf7_post_thumbnail*', array($this,'uacf7_post_submission_thumbnail_validation_filter'), 10, 2 );

        add_action( 'wpcf7_after_save', array( $this, 'uacf7_save_contact_form' ) );
        
		add_action('wpcf7_before_send_mail', array( $this, 'process_post_submit' ) );
		
        
    }
    
    public function add_shortcodes() {
        
        wpcf7_add_form_tag( array('uacf7_post_content', 'uacf7_post_content*'), array( $this, 'uacf7_post_content' ), true );
        
		wpcf7_add_form_tag( array('uacf7_post_thumbnail', 'uacf7_post_thumbnail*'), array( $this, 'uacf7_post_thumbnail' ), true );
        
		wpcf7_add_form_tag( array('uacf7_post_title', 'uacf7_post_title*'), array( $this, 'uacf7_post_title' ), true );
        
        wpcf7_add_form_tag( array( 'uacf7_post_taxonomy', 'uacf7_post_taxonomy*'),
        array( $this, 'uacf7_post_taxonomy' ), true );
    }
    
    /*
    * Generate tag
    */
    public function tag_generator() {
        if (! function_exists('wpcf7_add_tag_generator'))
            return;

        wpcf7_add_tag_generator('uacf7_post_title',
            __('Post title', 'ultimate-post-submission'),
            'uacf7-tg-pane-post-title',
            array($this, 'tg_pane_post_title')
        );
        
        wpcf7_add_tag_generator('uacf7_post_content',
            __('Post content', 'ultimate-post-submission'),
            'uacf7-tg-pane-post-content',
            array($this, 'tg_pane_post_content')
        );
        
        wpcf7_add_tag_generator('uacf7_post_thumbnail',
            __('Post thumbnail', 'ultimate-post-submission'),
            'uacf7-tg-pane-post-thumbnail',
            array($this, 'tg_pane_post_thumbnail')
        );

        wpcf7_add_tag_generator('uacf7_post_taxonomy',
            __('Post taxonomy/category', 'ultimate-post-submission'),
            'uacf7-tg-pane-post-category',
            array($this, 'tg_pane_post_taxonomy')
        );

    }
    
    /*
    * Enqueue scripts
    */
    public function enqueue_script() {
        
        wp_enqueue_style( 'uacf7-post-submission', plugin_dir_url( __FILE__ ) . '../assets/post-submission.css' );
        
        wp_enqueue_style( 'uacf7-select2-style', plugin_dir_url( __FILE__ ) . '../assets/select2.min.css' );
        
        wp_enqueue_script( 'uacf7-select2', plugin_dir_url( __FILE__ ) . '../assets/select2.js', array('jquery'), null, true );
        
        wp_enqueue_script( 'uacf7-post-submission-script', plugin_dir_url( __FILE__ ) . '../assets/script.js', array('jquery'), null, true );
    }
    
    /*
    * Create tab panel
    */
    public function uacf7_cf_add_panel( $panels ) {

		$panels['uacf7-post-submission-panel'] = array(
			'title'    => __( 'Ultimate Post Submission', 'ultimate-post-submission' ),
			'callback' => array( $this, 'uacf7_create_post_submission_panel_fields' ),
		);
		return $panels;
	}
    
    public function uacf7_create_post_submission_panel_fields($post) {
        
        $nonrequired_tags = $post->scan_form_tags(array('type'=>'uacf7_post_taxonomy'));
        
        $required_tags = $post->scan_form_tags(array('type'=>'uacf7_post_taxonomy*'));
        
        $all_tags = array_merge( $nonrequired_tags, $required_tags );
        
        //$tag->get_option( 'tabindex', 'signed_int', true );
        $tax_names = array();
        $name_and_taxoomy = array();
        foreach( $all_tags as $tag ) {
            
            $name_and_taxonomy[$tag['name']] = $tag->get_option('tax', '', true);
            
            $tax_names[] = $tag['name'];
            
        }
        
        update_post_meta( $post->id(), 'tax_names', $tax_names );
        update_post_meta( $post->id(), 'post_taxonomies', $name_and_taxonomy );
        
        ?>
        <h2>Post submission form</h2>
        <?php $enable_post_submission = get_post_meta( $post->id(), 'enable_post_submission', true ); ?>
        <input type="checkbox" name="enable_post_submission" value="yes" <?php checked('yes', $enable_post_submission); ?>> Enable
        
        <h2>Select post type</h2>
        
        <?php
        $post_types = get_post_types();
        $saved_post = !empty(get_post_meta( $post->id(), 'post_submission_post_type', true )) ? get_post_meta( $post->id(), 'post_submission_post_type', true ) : 'post';
        
        if ( $post_types ) { // If there are any custom public post types.
            ?>
            <select name="post_submission_post_type">
            <?php
            foreach ( $post_types  as $post_type ) {
                ?>
                <option value="<?php echo $post_type; ?>" <?php selected($post_type, $saved_post); ?>> <?php echo $post_type; ?> </option>
                <?php
            }
            ?>
            </select>
        <?php
        }
        
        wp_nonce_field( 'uacf7_post_submission_nonce_action', 'uacf7_post_submission_nonce' );
        
    }
    
    /*
    * Check validation for custom form fields
    */
    public function uacf7_post_submission_fields_validation_filter( $result, $tag ) {
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
    * Check validation for file uploading field
    */
    public function uacf7_post_submission_thumbnail_validation_filter( $result, $tag ) {
        $name = $tag->name;
        $id = $tag->get_id_option();

        $file = isset( $_FILES[$name] ) ? $_FILES[$name] : null;

        if ( $file['error'] and UPLOAD_ERR_NO_FILE !== $file['error'] ) {
            $result->invalidate( $tag, wpcf7_get_message( 'upload_failed_php_error' ) );
            return $result;
        }

        if ( empty( $file['tmp_name'] ) and $tag->is_required() ) {
            $result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
            return $result;
        }

        if ( ! is_uploaded_file( $file['tmp_name'] ) ) {
            return $result;
        }

        /* File type validation */

        $file_type_pattern = wpcf7_acceptable_filetypes(
            $tag->get_option( 'filetypes' ), 'regex' );

        $file_type_pattern = '/\.(' . $file_type_pattern . ')$/i';

        if ( ! preg_match( $file_type_pattern, $file['name'] ) ) {
            $result->invalidate( $tag,
                wpcf7_get_message( 'upload_file_type_invalid' ) );
            return $result;
        }

        /* File size validation */

        $allowed_size = $tag->get_limit_option();

        if ( $allowed_size < $file['size'] ) {
            $result->invalidate( $tag, wpcf7_get_message( 'upload_file_too_large' ) );
            return $result;
        }

        return $result;
    }
    
    /*
    * Save from fields
    */
    public function uacf7_save_contact_form( $form ) {
        
        if ( ! isset( $_POST ) || empty( $_POST ) ) {
			return;
		}
        if ( ! wp_verify_nonce( $_POST['uacf7_post_submission_nonce'], 'uacf7_post_submission_nonce_action' ) ) {
            return;
        }
        
        update_post_meta( $form->id(), 'post_submission_post_type', $_POST['post_submission_post_type'] );
        
        update_post_meta( $form->id(), 'enable_post_submission', $_POST['enable_post_submission'] );
    }
    
    /*
    * Field: Post taxonomy
    */
    public function uacf7_post_taxonomy( $tag ) {
        
        $validation_error = wpcf7_get_validation_error( $tag->name );

        $class = wpcf7_form_controls_class( $tag->type );

        if ( $validation_error ) {
            $class .= ' wpcf7-not-valid';
        }

        $atts = array();
        
        $class .= ' uacf7_post_taxonomy';
        
        $atts['class'] = $tag->get_class_option( $class );
        
        $atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );
        
        if ( $tag->is_required() ) {
            $atts['aria-required'] = 'true';
        }

        $atts['aria-invalid'] = $validation_error ? 'true' : 'false';

        $multiple = $tag->has_option( 'multiple' );
        
        if ( $multiple ) {
            
            $atts['multiple'] = 'multiple';
            
            $atts['data-placeholder'] = esc_attr__( 'Select category', 'ultimate-post-submission' );
            
        }
        
        //Field name
        $field_name = $tag->name;
        
        $taxonomy = $tag->get_option('tax', '', true);
        
        $atts = wpcf7_format_atts( $atts );
        
        $drop_down_category = '<span class="wpcf7-form-control-wrap uacf7_post_taxonomy_wraper '.$field_name.'">';
        
        $drop_down_category .= wp_dropdown_categories(
            array(
                'show_option_none' => __( '', 'ultimate-post-submission' ),
                'hierarchical'     => 1,
                'hide_empty'       => 0,
                'name'             => $tag->name.'[]',
                'id'               => $field_name,
                'taxonomy'         => $taxonomy,
                'echo'             => 0,
            )
        );
        
        $drop_down_category .= $validation_error . '</span>';
        
        $html = str_replace( '<select', '<select ' . $atts, $drop_down_category );
        
        return $html;
    }
    
    /*
    * Field: Post title
    */
	public function uacf7_post_title($tag){
        ob_start();
        
        $validation_error = wpcf7_get_validation_error( $tag->name );

        $class = wpcf7_form_controls_class( $tag->type );

        if ( $validation_error ) {
            $class .= ' wpcf7-not-valid';
        }

        $atts = array();

        $atts['class'] = $tag->get_class_option( $class );
        
        $atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );
        

        if ( $tag->is_required() ) {
            $atts['aria-required'] = 'true';
        }

        $atts['aria-invalid'] = $validation_error ? 'true' : 'false';

        $atts = wpcf7_format_atts( $atts );
        
        echo '<span class="wpcf7-form-control-wrap post_title">';
        
        echo '<input type="text" size="40" name="post_title" '.$atts.'>';
        
        echo $validation_error.'</span>';
		
        return ob_get_clean();
    }
	
    /*
    * Field: Post content
    */
	public function uacf7_post_content($tag){
        ob_start();
        
        $validation_error = wpcf7_get_validation_error( $tag->name );

        $class = wpcf7_form_controls_class( $tag->type );

        if ( $validation_error ) {
            $class .= ' wpcf7-not-valid';
        }
        
        $atts = array();

        $atts['class'] = $tag->get_class_option( $class );
        
        $atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );
        

        if ( $tag->is_required() ) {
            $atts['aria-required'] = 'true';
        }

        $atts['aria-invalid'] = $validation_error ? 'true' : 'false';

        $atts = wpcf7_format_atts( $atts );
        
        $args = array(
			'tinymce'	=> array(
				'toolbar1'      => 'bold,italic,underline,separator,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo',
                
			)
		);
        
        echo '<span class="wpcf7-form-control-wrap post_content">';
        
        //wp_editor( '', 'uacf7_post_content', array('textarea_name' => 'post_content','media_buttons' => false,'editor_height' => 250,'teeny' => true, 'editor_class' => $class) );
        
        echo '<textarea name="post_content" cols="30" rows="10" '.$atts.'></textarea>';
        
        echo $validation_error.'</span>';
        
        return ob_get_clean();
    }
    
    /*
    * Field: Post thumbnail
    */
	function uacf7_post_thumbnail($tag){
        ob_start();
        
        $validation_error = wpcf7_get_validation_error( $tag->name );

        $class = wpcf7_form_controls_class( $tag->type );

        if ( $validation_error ) {
            $class .= ' wpcf7-not-valid';
        }

        $atts = array();

        $atts['class'] = $tag->get_class_option( $class );
        
        $atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );
        
        if ( $tag->is_required() ) {
            $atts['aria-required'] = 'true';
        }

        $atts['aria-invalid'] = $validation_error ? 'true' : 'false';

        $atts = wpcf7_format_atts( $atts );
        
        echo '<span class="wpcf7-form-control-wrap post_thumbnail">';
        
        echo '<input type="file" size="40" name="post_thumbnail" '.$atts.'>';
        
        echo $validation_error.'</span>';

        return ob_get_clean();
    }
    
    /*
    * Tag generators
    */
    static function tg_pane_post_taxonomy( $contact_form, $args = '' ) {
        $args = wp_parse_args( $args, array() );
        $uacf7_field_type = 'uacf7_post_taxonomy';
        ?>
        <div class="control-box">
            <fieldset>                
                <table class="form-table">
                   <tbody>
                        <tr>
                            <th scope="row">Field type</th>
                            <td>
                                <fieldset>
                                <legend class="screen-reader-text">Field type</legend>
                                <label><input type="checkbox" name="required" value="on"> Required field</label>
                                </fieldset>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><label for="tag-generator-panel-text-name">Name</label></th>
                            <td><input type="text" name="name" class="tg-name oneline" value="post-taxonomy-<?php echo rand(100,999); ?>" id="tag-generator-panel-text-name"></td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><label for="tag-generator-panel-text-name">Taxonomy name</label></th>
                            <td><input type="text" name="tax" class="tg-name oneline option" value="category" id="tag-generator-panel-text-name"></td>
                        </tr>
                        
                        <tr>
                            <th scope="row"></th>
                            <td><label for="tag_generator_panel_select_multiple"><input id="tag_generator_panel_select_multiple" type="checkbox" name="multiple" class="option"> Allow Multiple Selection</label><br><br></td>
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
    
    static function tg_pane_post_title( $contact_form, $args = '' ) {
        $args = wp_parse_args( $args, array() );
        $uacf7_field_type = 'uacf7_post_title';
        ?>
        <div class="control-box">
            <fieldset>                
                <table class="form-table">
                   <tbody>
                        <tr>
                            <th scope="row">Field type</th>
                            <td>
                                <fieldset>
                                <legend class="screen-reader-text">Field type</legend>
                                <label><input type="checkbox" name="required" value="on"> Required field</label>
                                </fieldset>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><label for="tag-generator-panel-text-name">Name</label></th>
                            <td><input type="text" name="name" class="tg-name oneline" value="post_title" id="tag-generator-panel-text-name" readonly="readonly" disabled></td>
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
    
    static function tg_pane_post_content( $contact_form, $args = '' ) {
        $args = wp_parse_args( $args, array() );
        $uacf7_field_type = 'uacf7_post_content';
        ?>
        <div class="control-box">
            <fieldset>                
                <table class="form-table">
                   <tbody>
                        <tr>
                            <th scope="row">Field type</th>
                            <td>
                                <fieldset>
                                <legend class="screen-reader-text">Field type</legend>
                                <label><input type="checkbox" name="required" value="on"> Required field</label>
                                </fieldset>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><label for="tag-generator-panel-text-name">Name</label></th>
                            <td><input type="text" name="name" class="tg-name oneline" value="post_content" id="tag-generator-panel-text-name" readonly="readonly" disabled></td>
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
    
    static function tg_pane_post_thumbnail( $contact_form, $args = '' ) {
        $args = wp_parse_args( $args, array() );
        $uacf7_field_type = 'uacf7_post_thumbnail';
        ?>
        <div class="control-box">
            <fieldset>                
                <table class="form-table">
                   <tbody>
                        <tr>
                            <th scope="row">Field type</th>
                            <td>
                                <fieldset>
                                <legend class="screen-reader-text">Field type</legend>
                                <label><input type="checkbox" name="required" value="on"> Required field</label>
                                </fieldset>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><label for="tag-generator-panel-text-name">Name</label></th>
                            <td><input type="text" name="name" class="tg-name oneline" value="post_thumbnail" id="tag-generator-panel-text-name" readonly="readonly" disabled></td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><label for="tag-generator-panel-file-limit">File size limit (bytes)</label></th>
                            <td><input type="text" name="limit" class="filesize oneline option" id="tag-generator-panel-file-limit"></td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><label for="tag-generator-panel-file-filetypes">Acceptable file types</label></th>
                            <td><input type="text" name="filetypes" class="filetype oneline option" id="tag-generator-panel-file-filetypes"></td>
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

    /*
    * Process post submission
    */
	public function process_post_submit($cf7){
        
        $enable_post_submission = get_post_meta( $cf7->id(), 'enable_post_submission', true );
        
        if( $enable_post_submission == 'yes' ) :
        
		$submission = WPCF7_Submission::get_instance();
		if($submission) {
            
			$posted_data = $submission->get_posted_data();
			$title = $posted_data['post_title'];
			$meta_input = array();
            $tax_names = !empty(get_post_meta( $cf7->id(), 'tax_names', true )) ? get_post_meta( $cf7->id(), 'tax_names', true ) : array();
            
			foreach ($posted_data as $keyval => $posted) {

				if( $keyval != 'post_title' && $keyval != 'post_content' && $keyval != 'post_thumbnail' && $keyval != '_wpcf7' && $keyval != '_wpcf7_version' && $keyval != '_wpcf7_locale' && $keyval != '_wpcf7_unit_tag' && $keyval != '_wpcf7_container_post' ) {
                    
                    if( !in_array( $keyval, $tax_names ) ) {
                        
                        $meta_input[$keyval] = $posted;
                    }
					
				}
			}

            $post_type = !empty(get_post_meta( $cf7->id(), 'post_submission_post_type', true )) ? get_post_meta( $cf7->id(), 'post_submission_post_type', true ) : 'post';
            
			$post_data = array(
				'post_type'		=> $post_type,
				'post_title'    => wp_strip_all_tags( $title ),
				'post_content'  => $posted_data['post_content'],
				'post_status'   => 'publish',
				'post_author'   => 1,
				'meta_input'   	=> $meta_input,

			);
            
			// Insert the post into the database
			$post_id = wp_insert_post( $post_data );
            
            $post_taxonomies = get_post_meta( $cf7->id(), 'post_taxonomies', true );
            
            foreach( $post_taxonomies as $taxonomy_name=>$taxonomy ) {
                
                $category_ids = $posted_data[$taxonomy_name];
            
                if(is_array($category_ids)) {
                    if( !empty(array_filter( $category_ids )) ) {

                        $cats = array();

                        foreach( $category_ids as $key=>$category_id  ) {

                            $cats[] = intval($category_id);

                        }
                    }
                }else {
                    $cats = $category_ids;
                }

                wp_set_object_terms( $post_id, $cats, $taxonomy );
                
            }
                
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
            require_once( ABSPATH . 'wp-admin/includes/media.php' );
            
            $attachment_id = media_handle_upload( 'post_thumbnail', 0 );
     
            if ( is_wp_error( $attachment_id ) ) {
                // There was an error uploading the image.
            } else {
                // The image was uploaded successfully!
                set_post_thumbnail( $post_id, $attachment_id );
            }
                        
		}
        
        endif;
	}
}
new UACF7_POST_SUBMISSION();
