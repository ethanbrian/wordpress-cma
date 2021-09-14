<?php

namespace MkdCore\CPT\Testimonials\Shortcodes;


use MkdCore\Lib;

/**
 * Class Testimonials
 * @package MkdCore\CPT\Testimonials\Shortcodes
 */
class Testimonials implements Lib\ShortcodeInterface {
    /**
     * @var string
     */
    private $base;

    public function __construct() {
        $this->base = 'mkd_testimonials';

        add_action('vc_before_init', array($this, 'vcMap'));
    }

    /**
     * Returns base for shortcode
     * @return string
     */
    public function getBase() {
        return $this->base;
    }

    /**
     * Maps shortcode to Visual Composer
     *
     * @see vc_map()
     */
    public function vcMap() {
        if(function_exists('vc_map')) {
            vc_map( array(
                'name' => esc_html__( 'Testimonials', 'mikado-core' ),
                'base' => $this->base,
                'category' => esc_html__( 'by MIKADO', 'mikado-core' ),
                'icon' => 'icon-wpb-testimonials extended-custom-icon',
                'allowed_container_element' => 'vc_row',
                'params' => array(
                    array(
                        'type' => 'textfield',
						'admin_label' => true,
                        'heading' => esc_html__( 'Category', 'mikado-core' ),
                        'param_name' => 'category',
                        'value' => '',
                        'description' => esc_html__( 'Category Slug (leave empty for all)', 'mikado-core' )
                    ),
                    array(
                        'type' => 'textfield',
                        'admin_label' => true,
                        'heading' => esc_html__( 'Number', 'mikado-core' ),
                        'param_name' => 'number',
                        'value' => '',
                        'description' => esc_html__( 'Number of Testimonials. 3 per slide', 'mikado-core' )
                    ),
                    array(
                        'type' => 'dropdown',
                        'admin_label' => true,
                        'heading' => esc_html__( 'Show Author', 'mikado-core' ),
                        'param_name' => 'show_author',
                        'value' => array(
	                        esc_html__('Yes', 'mikado-core' ) => 'yes',
	                        esc_html__('No', 'mikado-core' ) => 'no'
                        ),
						'save_always' => true,
                        'description' => ''
                    ),
                    array(
                        'type' => 'dropdown',
                        'admin_label' => true,
                        'heading' => esc_html__( 'Show Author Job Position', 'mikado-core' ),
                        'param_name' => 'show_position',
                        'value' => array(
	                        esc_html__('Yes', 'mikado-core' ) => 'yes',
	                        esc_html__('No', 'mikado-core' ) => 'no',
                        ),
						'save_always' => true,
                        'dependency' => array('element' => 'show_author', 'value' => array('yes')),
                        'description' => ''
                    )
                )
            ) );
        }
    }

    /**
     * Renders shortcodes HTML
     *
     * @param $atts array of shortcode params
     * @param $content string shortcode content
     * @return string
     */
    public function render($atts, $content = null) {
        
        $args = array(
            'number' => '-1',
            'category' => '',
            'show_author' => 'yes',
            'show_position' => 'yes'
        );
		$params = shortcode_atts($args, $atts);
		
		//Extract params for use in method
		extract($params);

        $number = esc_attr($number);
        $category = esc_attr($category);
		
		$query_args = $this->getQueryParams($params);

		$html = '';
        $html .= '<div class="mkd-testimonials-holder clearfix">';
        $html .= '<div class="mkd-testimonials">';

        query_posts($query_args);
        if (have_posts()) :
            while (have_posts()) : the_post();
                $author = get_post_meta(get_the_ID(), 'mkd_testimonial_author', true);
                $text = get_post_meta(get_the_ID(), 'mkd_testimonial_text', true);
                $job = get_post_meta(get_the_ID(), 'mkd_testimonial_author_position', true);

				$params['author'] = $author;
				$params['text'] = $text;
				$params['job'] = $job;
				$params['current_id'] = get_the_ID();				
				
				$html .= mkd_core_get_shortcode_module_template_part('testimonials','testimonials-template', '', $params);
				
            endwhile;
        else:
            $html .= esc_html__('Sorry, no posts matched your criteria.', 'mikado-core');
        endif;

        wp_reset_query();
        $html .= '</div>';
		$html .= '</div>';
		
        return $html;
    }

	/**
    * Generates testimonials query attribute array
    *
    * @param $params
    *
    * @return array
    */
	private function getQueryParams($params){
		
		$args = array(
            'post_type' => 'testimonials',
            'orderby' => 'date',
            'order' => 'DESC',
            'posts_per_page' => $params['number']
        );

        if ($params['category'] != '') {
            $args['testimonials_category'] = $params['category'];
        }
		return $args;
	}
	 
}