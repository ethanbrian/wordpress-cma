<?php
namespace Libero\Modules\PricingTable;

use Libero\Modules\Shortcodes\Lib\ShortcodeInterface;

class PricingTable implements ShortcodeInterface{
	private $base;
	function __construct() {
		$this->base = 'mkd_pricing_table';
		add_action('vc_before_init', array($this, 'vcMap'));
	}
	public function getBase() {
		return $this->base;
	}
	
	public function vcMap() {
		vc_map( array(
			'name' => esc_html__( 'Mikado Pricing Table', 'mikado-core' ),
			'base' => $this->base,
			'icon' => 'icon-wpb-pricing-table extended-custom-icon',
			'category' => esc_html__( 'by MIKADO', 'mikado-core' ),
			'allowed_container_element' => 'vc_row',
			'as_child' => array('only' => 'mkd_pricing_tables'),
			'params' => array(
				array(
					'type' => 'textfield',
					'admin_label' => true,
					'heading' => esc_html__( 'Title', 'mikado-core' ),
					'param_name' => 'title',
					'value' => esc_html__('Basic Plan','mikado-core'),
					'description' => ''
				),
				array(
					'type' => 'textfield',
					'admin_label' => true,
					'heading' => esc_html__( 'Price', 'mikado-core' ),
					'param_name' => 'price',
					'description' => esc_html__( 'Default value is 100', 'mikado-core' )
				),
				array(
					'type' => 'textfield',
					'admin_label' => true,
					'heading' => esc_html__( 'Currency', 'mikado-core' ),
					'param_name' => 'currency',
					'description' => esc_html__( 'Default mark is $', 'mikado-core' )
				),
				array(
					'type' => 'textfield',
					'admin_label' => true,
					'heading' => esc_html__( 'Price Period', 'mikado-core' ),
					'param_name' => 'price_period',
					'description' => esc_html__( 'Default label is mo', 'mikado-core' )
				),
				array(
					'type' => 'dropdown',
					'admin_label' => true,
					'heading' => esc_html__( 'Show Button', 'mikado-core' ),
					'param_name' => 'show_button',
					'value' => array(
						esc_html__('Default', 'mikado-core' ) => '',
						esc_html__('Yes', 'mikado-core' ) => 'yes',
						esc_html__('No', 'mikado-core' ) => 'no'
					),
					'description' => ''
				),
				array(
					'type' => 'textfield',
					'admin_label' => true,
					'heading' => esc_html__( 'Button Text', 'mikado-core' ),
					'param_name' => 'button_text',
					'dependency' => array('element' => 'show_button',  'value' => 'yes') 
				),
				array(
					'type' => 'textfield',
					'admin_label' => true,
					'heading' => esc_html__( 'Button Link', 'mikado-core' ),
					'param_name' => 'link',
					'dependency' => array('element' => 'show_button',  'value' => 'yes')
				),
				array(
					'type' => 'dropdown',
					'admin_label' => true,
					'heading' => esc_html__( 'Active', 'mikado-core' ),
					'param_name' => 'active',
					'value' => array(
						esc_html__('No', 'mikado-core' ) => 'no',
						esc_html__('Yes', 'mikado-core' ) => 'yes'
					),
					'save_always' => true,
					'description' => ''
				),
				array(
					'type' => 'textfield',
					'admin_label' => true,
					'heading' => esc_html__( 'Active text', 'mikado-core' ),
					'param_name' => 'active_text',
					'description' => esc_html__( 'Best choice', 'mikado-core' ),
					'dependency' => array('element' => 'active', 'value' => 'yes')
				),
				array(
					'type' => 'textarea_html',
					'holder' => 'div',
					'class' => '',
					'heading' => esc_html__( 'Content', 'mikado-core' ),
					'param_name' => 'content',
					'value' => '<li>content content content</li><li>content content content</li><li>content content content</li>',
					'description' => ''
				)
			)
		));
	}

	public function render($atts, $content = null) {
	
		$args = array(
			'title' => esc_html__( 'Basic Plan', 'mikado-core' ),
			'price'         			   => '100',
			'currency'      			   => '$',
			'price_period'  			   => 'mo',
			'active'        			   => 'no',
			'active_text'   			   => 'Best',
			'show_button'				   => 'yes',
			'link'          			   => '',
			'button_text'   			   => 'Purchase'
		);
		$params = shortcode_atts($args, $atts);
		extract($params);

		$html						= '';
		$pricing_table_clasess		= '';
		
		if($active == 'yes') {
			$pricing_table_clasess .= ' mkd-active';
		}
		
		$params['pricing_table_classes'] = $pricing_table_clasess;
		$params['content']= $content;
		$params['button_params']= $this->buttonParams($params);
		
		$html .= mkd_core_get_core_shortcode_template_part('templates/pricing-table-template','pricing-table', '', $params);
		return $html;

	}


	/*
	* Returns Button Parameters
	* @param $params
	* @return array
	*/

	private function buttonParams($params){
		$buttonParams = array();

		if($params['button_text'] !== ''){
			$buttonParams['text'] = $params['button_text'];
		}

		if($params['link'] !== ''){
			$buttonParams['link'] = $params['link'];
		}

		$buttonParams['size'] = 'large';
		$buttonParams['icon_pack'] = 'simple_line_icons';
		$buttonParams['simple_line_icons'] = 'icon-arrow-right-circle';

		return $buttonParams;
	}

}
