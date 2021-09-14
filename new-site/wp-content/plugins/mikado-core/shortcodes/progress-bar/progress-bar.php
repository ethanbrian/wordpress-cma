<?php
namespace Libero\Modules\ProgressBar;

use Libero\Modules\Shortcodes\Lib\ShortcodeInterface;

class ProgressBar implements ShortcodeInterface{
	private $base;
	
	function __construct() {
		$this->base = 'mkd_progress_bar';
		add_action('vc_before_init', array($this, 'vcMap'));
	}
	public function getBase() {
		return $this->base;
	}
	
	public function vcMap() {

		vc_map( array(
			'name' => esc_html__( 'Mikado Progress Bar', 'mikado-core' ),
			'base' => $this->base,
			'icon' => 'icon-wpb-progress-bar extended-custom-icon',
			'category' => esc_html__( 'by MIKADO', 'mikado-core' ),
			'allowed_container_element' => 'vc_row',
			'params' => array(
				array(
					'type' => 'textfield',
					'admin_label' => true,
					'heading' => esc_html__( 'Title', 'mikado-core' ),
					'param_name' => 'title',
					'description' => ''
				),
				array(
					'type' => 'dropdown',
					'admin_label' => true,
					'heading' => esc_html__( 'Title Tag', 'mikado-core' ),
					'param_name' => 'title_tag',
					'value' => array(
						''   => '',
						'h2' => 'h2',
						'h3' => 'h3',
						'h4' => 'h4',	
						'h5' => 'h5',	
						'h6' => 'h6',	
					),
					'description' => ''
				),
                array(
                    'type' => 'colorpicker',
                    'admin_label' => true,
                    'heading' => esc_html__( 'Title Color', 'mikado-core' ),
                    'param_name' => 'title_color',
                    'description' => ''
                ),
				array(
					'type' => 'textfield',
					'admin_label' => true,
					'heading' => esc_html__( 'Percentage', 'mikado-core' ),
					'param_name' => 'percent',
					'description' => ''
				),	
				array(
					'type' => 'dropdown',
					'admin_label' => true,
					'heading' => esc_html__( 'Percentage Type', 'mikado-core' ),
					'param_name' => 'percentage_type',
					'value' => array(
						esc_html__('Floating', 'mikado-core' )  => 'floating',
						esc_html__('Static', 'mikado-core' ) => 'static'
					),
					'dependency' => Array('element' => 'percent', 'not_empty' => true)
				),	
				array(
					'type' => 'dropdown',
					'admin_label' => true,
					'heading' => esc_html__( 'Floating Type', 'mikado-core' ),
					'param_name' => 'floating_type',
					'value' => array(
						esc_html__('Outside Floating', 'mikado-core' )  => 'floating_outside',
						esc_html__('Inside Floating', 'mikado-core' ) => 'floating_inside'
					),
					'dependency' => array('element' => 'percentage_type', 'value' => array('floating'))
				),
                array(
                    'type' => 'colorpicker',
                    'admin_label' => true,
                    'heading' => esc_html__( 'Percentage Bar Active Color', 'mikado-core' ),
                    'param_name' => 'percentage_bar_color',
                    'description' => ''
                ),
                array(
                    'type' => 'colorpicker',
                    'admin_label' => true,
                    'heading' => esc_html__( 'Percentage Bar Inactive Color', 'mikado-core' ),
                    'param_name' => 'percentage_bar_inactive_color',
                    'description' => ''
                )
			)
		) );

	}

	public function render($atts, $content = null) {
		$args = array(
            'title' => '',
            'title_tag' => 'h6',
            'title_color' => '',
            'percent' => '100',
            'percentage_type' => 'floating',
            'floating_type' => 'floating_outside',
            'percentage_bar_inactive_color' => '',
            'percentage_bar_color' => ''
        );
		$params = shortcode_atts($args, $atts);
		
		//Extract params for use in method
		extract($params);
		$headings_array = array('h2', 'h3', 'h4', 'h5', 'h6');

        //get correct heading value. If provided heading isn't valid get the default one
        $title_tag = (in_array($title_tag, $headings_array)) ? $title_tag : $args['title_tag'];

        if(!empty($title_color)){
            $params['title_color'] = 'color:'.$title_color;
        }

        if(!empty($percentage_bar_color)){
            $params['percentage_bar_color'] = 'background-color:'.$percentage_bar_color;
        }

        if(!empty($percentage_bar_inactive_color)){
            $params['percentage_bar_inactive_color'] = 'background-color:'.$percentage_bar_inactive_color;
        }

		$params['percentage_classes'] = $this->getPercentageClasses($params);		

        //init variables
		$html = mkd_core_get_core_shortcode_template_part('templates/progress-bar-template', 'progress-bar', '', $params);
		
        return $html;
		
	}
	/**
    * Generates css classes for progress bar
    *
    * @param $params
    *
    * @return array
    */
	private function getPercentageClasses($params){
		
		$percentClassesArray = array();
		
		if(!empty($params['percentage_type']) !=''){
			
			if($params['percentage_type'] == 'floating'){
				
				$percentClassesArray[]= 'mkd-floating';
				
				if($params['floating_type'] == 'floating_outside'){
					
					$percentClassesArray[] = 'mkd-floating-outside';
					
				}
				
				elseif($params['floating_type'] == 'floating_inside'){
					
					$percentClassesArray[] = 'mkd-floating-inside';
				}

			}
			elseif($params['percentage_type'] == 'static'){
				
				$percentClassesArray[] = 'mkd-static';
				
			}
		}
		return implode(' ', $percentClassesArray);
	}
}