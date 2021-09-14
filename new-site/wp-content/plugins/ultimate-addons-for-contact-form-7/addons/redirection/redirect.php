<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UACF7_Redirection {
    
    /*
    * Construct function
    */
    public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_redirect_script' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_redirect_script' ) );
		add_action( 'wpcf7_editor_panels', array( $this, 'uacf7_add_panel' ) );
		add_action( 'wpcf7_after_save', array( $this, 'uacf7_save_meta' ) );
		add_action( 'wpcf7_submit', array( $this, 'uacf7_non_ajax_redirection' ) );
    }
    
    public function enqueue_redirect_script() {
        wp_enqueue_script( 'uacf7-redirect-script', UACF7_URL . 'addons/redirection/js/redirect.js', array(), null, true );
		wp_localize_script( 'uacf7-redirect-script', 'uacf7_redirect_object', $this->get_forms() );

		if ( isset( $this->enqueue_new_tab_script ) && $this->enqueue_new_tab_script ) {
			wp_add_inline_script( 'wpcf7-redirect-script', 'window.open("' . $this->redirect_url . '");' );
		}
    }
	
	public function admin_enqueue_redirect_script() {
        wp_enqueue_script( 'uacf7-redirect-script', UACF7_URL . 'addons/redirection/js/admin-redirect.js', array(), null, true );
        wp_enqueue_style( 'uacf7-redirect-style', UACF7_URL . 'addons/redirection/css/admin-redirect.css', array(), null, true );
    }
    
    public function get_forms() {
		$args  = array(
			'post_type'        => 'wpcf7_contact_form',
			'posts_per_page'   => -1,
		);
		$query = new WP_Query( $args );

		$forms = array();

		if ( $query->have_posts() ) :

			$fields = $this->fields();

			while ( $query->have_posts() ) :
				$query->the_post();

				$post_id = get_the_ID();

				foreach ( $fields as $field ) {
					$forms[ $post_id ][ $field['name'] ] = get_post_meta( $post_id, 'uacf7_redirect_' . $field['name'], true );
				}

				$forms[ $post_id ]['thankyou_page_url'] = $forms[ $post_id ]['page_id'] ? get_permalink( $forms[ $post_id ]['page_id'] ) : '';
			endwhile;
			wp_reset_postdata();
		endif;

		return $forms;
	}
    
    public function uacf7_get_options( $post_id ) {
		$fields = $this->fields();
		foreach ( $fields as $field ) {
			$values[ $field['name'] ] = get_post_meta( $post_id, 'uacf7_redirect_' . $field['name'], true );
		}
		return $values;
	}
    
    /*
    * Function create tab panel
    */
    public function uacf7_add_panel( $panels ) {
		$panels['uacf7-redirect-panel'] = array(
			'title'    => __( 'Ultimate Redirect', 'ultimate-addons-cf7' ),
			'callback' => array( $this, 'uacf7_create_redirect_panel_fields' ),
		);
		return $panels;
	}
    
    public function uacf7_non_ajax_redirection( $contact_form ) {
		$this->fields = $this->uacf7_get_options( $contact_form->id() );

		if ( isset( $this->fields ) && ! WPCF7_Submission::is_restful() ) {
			$submission = WPCF7_Submission::get_instance();

			if ( $submission->get_status() === 'mail_sent' ) {

				if ( 'to_url' === $this->fields['uacf7_redirect_to_type'] && $this->fields['external_url'] ) {
					$this->redirect_url = $this->fields['external_url'];
				}
				if( 'to_page' === $this->fields['uacf7_redirect_to_type'] && $this->fields['page_id'] ){
					$this->redirect_url = get_permalink( $this->fields['page_id'] );
				}

				// Open link in a new tab
				if ( isset( $this->redirect_url ) && $this->redirect_url ) {
					if ( 'on' === $this->fields['open_in_new_tab'] ) {
						$this->enqueue_new_tab_script = true;
					} else {
						wp_redirect( $this->redirect_url );
						exit;
					}
				}
			}
		}
	}
    
    /*
    * Function redirect fields
    */
    public function uacf7_create_redirect_panel_fields( $post ) {
        ?>
        <h2><?php echo esc_html__( 'Ultimate Redirect Settings', 'ultimate-addons-cf7' ); ?></h2>
                
        <p><?php echo esc_html__('This feature will help you to redirect contact form 7 after submission. You can Redirect users to a Thank you page or External page after user fills up the form. You can check this','ultimate-addons-cf7'); ?> <a target="_blank" href="<?php echo esc_url('https://youtu.be/mxcC1eQXxEI'); ?>"><?php echo esc_html__('video','ultimate-addons-cf7'); ?></a> <?php echo esc_html__('to learn more.','ultimate-addons-cf7'); ?></p>
        
        <fieldset>
          <?php
			$options = $this->uacf7_get_options($post->id());
			$uacf7_redirect_to_type = !empty($options['uacf7_redirect_to_type']) ? $options['uacf7_redirect_to_type'] : 'to_page';
			?>
           <p>
           	<label for="uacf7_redirect_to_page">
           		<input class="uacf7_redirect_to_type" id="uacf7_redirect_to_page" name="uacf7_redirect[uacf7_redirect_to_type]" type="radio" value="to_page" <?php checked( 'to_page', $uacf7_redirect_to_type, true ); ?>> <?php echo esc_html__('Redirect to page'); ?>
           	</label><br>
           	<label for="uacf7_redirect_to_url">
           		<input class="uacf7_redirect_to_type" id="uacf7_redirect_to_url" name="uacf7_redirect[uacf7_redirect_to_type]" type="radio" value="to_url" <?php checked( 'to_url', $uacf7_redirect_to_type, true ); ?>> <?php echo esc_html__('Redirect to external URL'); ?>
           	</label>
           </p>
            <p class="uacf7_redirect_to_page">
                <label for="uacf7-redirect-page">
					<?php esc_html_e( 'Select a page to redirect', 'ultimate-addons-cf7' ); ?>   
				</label><br>
				<?php
				$pages = get_posts(array(
                            'post_type'        => 'page',
                            'posts_per_page'   => -1,
                            'post_status'      => 'published',
                        ));
				?>
				<select name="uacf7_redirect[page_id]" id="uacf7-redirect-page">
					<option value="0" <?php selected( 0, $options['page_id'] ); ?> >
				<?php echo esc_html__( 'Choose Page', 'ultimate-addons-cf7' ); ?>
					</option>

					<?php foreach ( $pages as $page ) : ?>

						<option value="<?php echo esc_attr($page->ID); ?>" <?php selected( $page->ID, $options['page_id'] ); ?>>
							<?php echo esc_html($page->post_title); ?>
						</option>

					<?php endforeach; ?>
				</select>
            </p>
            <p class="uacf7_redirect_to_url">
                <input type="url" id="uacf7-external-url" name="uacf7_redirect[external_url]" class="large-text" value="<?php echo esc_url($options['external_url']); ?>" placeholder="<?php echo esc_html__( 'Enter an external URL', 'ultimate-addons-cf7' ); ?>">
            </p>
            <p>
                <input id="uacf7_tab_target" type="checkbox" name="uacf7_redirect[target]" <?php checked( $options['target'], 'on', true ); ?>>
                <label for="uacf7_tab_target"><?php echo esc_html__( 'Open page in a new tab', 'ultimate-addons-cf7' ); ?></label>
            </p>
        </fieldset>
        
        <?php
         wp_nonce_field( 'uacf7_redirection_nonce_action', 'uacf7_redirect_nonce' );
    }
    
    /*
    * Fields array
    */
    public function fields() {
        $fields = array(
            array(
                'name'  => 'uacf7_redirect_to_type',
                'type'  => 'radio',
            ),
			array(
                'name'  => 'page_id',
                'type'  => 'number',
            ),
            array(
                'name'  => 'external_url',
                'type'  => 'url',
            ),
            array(
                'name'  => 'target',
                'type'  => 'checkbox',
            ),
        );
        return $fields;
    }
    
    /*
    * Save meta value
    */
    public function uacf7_save_meta( $post ) {
        if ( ! isset( $_POST ) || empty( $_POST ) ) {
			return;
		}
        if ( ! wp_verify_nonce( $_POST['uacf7_redirect_nonce'], 'uacf7_redirection_nonce_action' ) ) {
            return;
        }
        
        $fields = $this->fields();
        $data = $_POST['uacf7_redirect'];
        
        foreach( $fields as $field ) {
            $value = isset($data[$field['name']]) ? $data[$field['name']] : '';
            
            switch( $field['type'] ) {
                    
                case 'radio':
                    $value = sanitize_text_field( $value );
                    break;
    
                case 'number':
                    $value = intval( $value );
                    break;

                case 'checkbox':
                    $value = sanitize_text_field( $value );
                    break;

                case 'url':
                    $value = esc_url_raw( $value );
                    break;
            }
            
            update_post_meta( $post->id(), 'uacf7_redirect_' . $field['name'], $value );
        }
        
    }
}
new UACF7_Redirection();
