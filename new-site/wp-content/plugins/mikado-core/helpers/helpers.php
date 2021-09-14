<?php

if(!function_exists('mkd_core_version_class')) {
    /**
     * Adds plugins version class to body
     * @param $classes
     * @return array
     */
    function mkd_core_version_class($classes) {
        $classes[] = 'mkd-core-'.MIKADO_CORE_VERSION;

        return $classes;
    }

    add_filter('body_class', 'mkd_core_version_class');
}

if(!function_exists('mkd_core_theme_installed')) {
    /**
     * Checks whether theme is installed or not
     * @return bool
     */
    function mkd_core_theme_installed() {
        return defined('MIKADO_ROOT');
    }
}

if ( ! function_exists( 'mkd_core_is_revolution_slider_installed' ) ) {
    function mkd_core_is_revolution_slider_installed() {
        return class_exists( 'RevSliderFront' );
    }
}

if ( ! function_exists( 'mkd_core_is_woocommerce_installed' ) ) {
    function mkd_core_is_woocommerce_installed() {
        return function_exists( 'is_woocommerce' );
    }
}

if (!function_exists('mkd_core_get_carousel_slider_array')){
    /**
     * Function that returns associative array of carousels,
     * where key is term slug and value is term name
     * @return array
     */
    function mkd_core_get_carousel_slider_array() {
        $carousels_array = array();
        $terms = get_terms('carousels_category');

        if (is_array($terms) && count($terms)) {
            $carousels_array[''] = '';
            foreach ($terms as $term) {
                $carousels_array[$term->slug] = $term->name;
            }
        }

        return $carousels_array;
    }
}

if(!function_exists('mkd_core_get_carousel_slider_array_vc')) {
    /**
     * Function that returns array of carousels formatted for Visual Composer
     *
     * @return array array of carousels where key is term title and value is term slug
     *
     * @see mkd_core_get_carousel_slider_array
     */
    function mkd_core_get_carousel_slider_array_vc() {
        return array_flip(mkd_core_get_carousel_slider_array());
    }
}

if ( ! function_exists( 'mkd_core_ajax_status' ) ) {
    /**
     * Function that return status from ajax functions
     */
    function mkd_core_ajax_status( $status, $message, $data = null ) {
        $response = array(
            'status'  => $status,
            'message' => $message,
            'data'    => $data
        );

        $output = json_encode( $response );

        exit( $output );
    }
}

if ( ! function_exists( 'mkd_core_get_module_template_part' ) ) {
    /**
     * Loads module template part.
     *
     * @param string $module name of the module to load
     * @param string $template name of the template file
     * @param string $slug
     * @param array $params array of parameters to pass to template
     *
     * @return html
     */
    function mkd_core_get_module_template_part( $module, $template, $slug = '', $params = array() ) {

        //HTML Content from template
        $html          = '';
        $template_path = MIKADO_CORE_ABS_PATH . '/' . $module . '/templates';

        $temp = $template_path . '/' . $template;

        if ( is_array( $params ) && count( $params ) ) {
            extract( $params );
        }

        $template = '';

        if ( ! empty( $temp ) ) {
            if ( ! empty( $slug ) ) {
                $template = "{$temp}-{$slug}.php";

                if ( ! file_exists( $template ) ) {
                    $template = $temp . '.php';
                }
            } else {
                $template = $temp . '.php';
            }
        }

        if ( $template ) {
            ob_start();
            include( $template );
            $html = ob_get_clean();
        }

        return $html;
    }
}

if(!function_exists('mkd_core_get_shortcode_module_template_part')) {
	/**
	 * Loads module template part.
	 *
	 * @param string $shortcode name of the shortcode folder
	 * @param string $template name of the template to load
	 * @param string $slug
	 * @param array $params array of parameters to pass to template
	 *
	 * @see libero_mikado_get_template_part()
	 */
	function mkd_core_get_shortcode_module_template_part($shortcode,$template, $slug = '', $params = array()) {

		//HTML Content from template
		$html = '';
		$template_path = MIKADO_CORE_CPT_PATH.'/'.$shortcode.'/shortcodes/templates';
		
		$temp = $template_path.'/'.$template;
		if(is_array($params) && count($params)) {
			extract($params);
		}
		
		$template = '';

		if($temp !== '') {
			if($slug !== '') {
				$template = "{$temp}-{$slug}.php";
			}
			$template = $temp.'.php';
		}
		if($template) {
			ob_start();
			include($template);
			$html = ob_get_clean();
		}

		return $html;
	}
}

if(!function_exists('mkd_core_deregister_scripts')) {
	function mkd_core_deregister_scripts() {
		
		//Deregister FlexSlider from VisualComposer since we have our own
		wp_deregister_script('flexslider');
	}
	add_action('wp_enqueue_scripts', 'mkd_core_deregister_scripts');
}

if(!function_exists('mkd_core_set_portfolio_ajax_url')){
	/**
     * load themes ajax functionality
     * 
     */
	function mkd_core_set_portfolio_ajax_url() {
		echo '<script type="application/javascript">var mkdCoreAjaxUrl = "'.admin_url('admin-ajax.php').'"</script>';
	}
	add_action('wp_enqueue_scripts', 'mkd_core_set_portfolio_ajax_url');
	
}
/**
	 * Loads more function for portfolio.
	 *
	 */
if(!function_exists('mkd_core_portfolio_ajax_load_more')){
	
	function mkd_core_portfolio_ajax_load_more(){
	
	$return_obj = array();
	$shortcode_params = array();
	$activeFilterCat = '';
	if (!empty($_POST['type'])) {
        $shortcode_params['type'] = $_POST['type'];
    }
	if (!empty($_POST['columns'])) {
        $shortcode_params['columns'] = $_POST['columns'];
    }
	if (!empty($_POST['gridSize'])) {
        $shortcode_params['gridSize'] = $_POST['gridSize'];
    }
	if (!empty($_POST['orderBy'])) {
        $shortcode_params['order_by'] = $_POST['orderBy'];
    }
	if (!empty($_POST['order'])) {
        $shortcode_params['order'] = $_POST['order'];
    }
	if (!empty($_POST['number'])) {
        $shortcode_params['number'] = $_POST['number'];
    }
	if (!empty($_POST['imageSize'])) {
        $shortcode_params['image_size'] = $_POST['imageSize'];
    }
	if (!empty($_POST['filter'])) {
        $shortcode_params['filter'] = $_POST['filter'];
    }
	if (!empty($_POST['filterOrderBy'])) {
        $shortcode_params['filter_order_by'] = $_POST['filterOrderBy'];
    }
	if (!empty($_POST['category'])) {
        $shortcode_params['category'] = $_POST['category'];
    }
	if (!empty($_POST['selectedProjectes'])) {
        $shortcode_params['selected_projectes'] = $_POST['selectedProjectes'];
    }
	if (!empty($_POST['showLoadMore'])) {
        $shortcode_params['show_load_more'] = $_POST['showLoadMore'];
    }
	if (!empty($_POST['titleTag'])) {
        $shortcode_params['title_tag'] = $_POST['titleTag'];
    }
	if (!empty($_POST['nextPage'])) {
        $shortcode_params['next_page'] = $_POST['nextPage'];
    }
	if (!empty($_POST['activeFilterCat'])) {
        $shortcode_params['active_filter_cat'] = $_POST['activeFilterCat'];
    }
	
	$html = '';
	
	$port_list = new \MkdCore\CPT\Portfolio\Shortcodes\PortfolioList();
	$query_array = $port_list->getQueryArray($shortcode_params);
	$query_results = new \WP_Query($query_array);
	
	if($query_results->have_posts()):			
		while ( $query_results->have_posts() ) : $query_results->the_post(); 

			$shortcode_params['current_id'] = get_the_ID();
			$shortcode_params['thumb_size'] = $port_list->getImageSize($shortcode_params);
			$shortcode_params['icon_html'] = $port_list->getPortfolioIconsHtml($shortcode_params);
			$shortcode_params['category_html'] = $port_list->getItemCategoriesHtml($shortcode_params);
			$shortcode_params['categories'] = $port_list->getItemCategories($shortcode_params);
			$shortcode_params['article_masonry_size'] = $port_list->getMasonrySize($shortcode_params);
			$shortcode_params['title_link'] = $port_list->getPortfolioLink($shortcode_params);

			$html .= mkd_core_get_shortcode_module_template_part('portfolio',$shortcode_params['type'], '', $shortcode_params);
			
		endwhile;
		else: 			
			$html .= '<p>'. esc_html__('Sorry, no posts matched your criteria.', 'mikado-core') .'</p>';
		endif;
		
	$return_obj = array(
		'html' => $html,
	);

	
	echo json_encode($return_obj); exit;
}
}

add_action('wp_ajax_nopriv_mkd_core_portfolio_ajax_load_more', 'mkd_core_portfolio_ajax_load_more');
add_action( 'wp_ajax_mkd_core_portfolio_ajax_load_more', 'mkd_core_portfolio_ajax_load_more' );

if ( ! function_exists( 'mkd_core_is_revolution_slider_installed' ) ) {
	function mkd_core_is_revolution_slider_installed() {
		return class_exists( 'RevSliderFront' );
	}
}

if(!function_exists('mkd_core_init_shortcode_loader')) {
	function mkd_core_init_shortcode_loader() {
		
		include_once 'shortcode-loader.php';
	}
	
	add_action('libero_mikado_shortcode_loader', 'mkd_core_init_shortcode_loader');
}

/* Function for adding custom meta boxes hooked to default action */
if ( class_exists( 'WP_Block_Type' ) && defined( 'MIKADO_ROOT' ) ) {
	add_action( 'admin_head', 'libero_mikado_meta_box_add' );
} else {
	add_action( 'add_meta_boxes', 'libero_mikado_meta_box_add' );
}

if ( ! function_exists( 'libero_mikado_create_meta_box_handler' ) ) {
	function libero_mikado_create_meta_box_handler( $box, $key, $screen ) {
		add_meta_box(
			'mkd-meta-box-'.$key,
			$box->title,
			'libero_mikado_render_meta_box',
			$screen,
			'advanced',
			'high',
			array( 'box' => $box)
		);
	}
}

if ( ! function_exists( 'mkd_core_get_core_shortcode_template_part' ) ) {
	/**
	 * Loads module template part.
	 *
	 * @param string $template name of the template to load
	 * @param string $shortcode name of the shortcode folder
	 * @param string $slug
	 * @param array $params array of parameters to pass to template
	 *
	 * @return html
	 */
	function mkd_core_get_core_shortcode_template_part( $template, $shortcode, $slug = '', $params = array() ) {
		
		//HTML Content from template
		$html          = '';
		$template_path = MIKADO_CORE_SHORTCODES_PATH . '/' . $shortcode;
		
		$temp = $template_path . '/' . $template;
		
		if ( is_array( $params ) && count( $params ) ) {
			extract( $params );
		}
		
		$template = '';
		
		if ( ! empty( $temp ) ) {
			if ( ! empty( $slug ) ) {
				$template = "{$temp}-{$slug}.php";
				
				if ( ! file_exists( $template ) ) {
					$template = $temp . '.php';
				}
			} else {
				$template = $temp . '.php';
			}
		}
		
		if ( $template ) {
			ob_start();
			include( $template );
			$html = ob_get_clean();
		}
		
		return $html;
	}
}

add_filter('widget_text', 'do_shortcode');