<?php 

namespace Libero\Modules\AccordionTab;

use Libero\Modules\Shortcodes\Lib\ShortcodeInterface;
/**
	* class Accordions
*/
class AccordionTab implements ShortcodeInterface{
	/**
	 * @var string
	 */
	private $base;
	function __construct() {
		$this->base = 'mkd_accordion_tab';
		add_action('vc_before_init', array($this, 'vcMap'));
	}
	public function getBase() {
		return $this->base;
	}
	
	public function vcMap() {
		if(function_exists('vc_map')){
			vc_map( array(
				"name" =>  esc_html__( 'Mikado Accordion Tab', 'mikado-core' ),
				"base" => $this->base,
				"as_parent" => array('except' => 'vc_row'),
				"as_child" => array('only' => 'mkd_accordion'),
				'is_container' => true,
				"category" => esc_html__( 'by MIKADO', 'mikado-core' ),
				"icon" => "icon-wpb-accordion-section extended-custom-icon",
				"show_settings_on_create" => true,
				"js_view" => 'VcColumnView',
				"params" => array(
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Title', 'mikado-core' ),
						'param_name' => 'title',
						'admin_label' => true,
						'value' => esc_html__( 'Section', 'mikado-core' ),
						'description' => esc_html__( 'Enter accordion section title.', 'mikado-core' )
					),
					array(
						'type' => 'el_id',
						'heading' => esc_html__( 'Section ID', 'mikado-core' ),
						'param_name' => 'el_id',
						'description' => sprintf( esc_html__( 'Enter optional row ID. Make sure it is unique, and it is valid as w3c specification: %s (Must not have spaces)', 'mikado-core' ), '<a target="_blank" href="http://www.w3schools.com/tags/att_global_id.asp">' . esc_html__( 'link', 'mikado-core' ) . '</a>' ),
					),
					array(
						"type" => "dropdown",
						"class" => "",
						"heading" => esc_html__( "Title Tag", 'mikado-core' ),
						"param_name" => "title_tag",
						"value" => array(
							""   => "",
							"p"  => "p",
							"h2" => "h2",
							"h3" => "h3",
							"h4" => "h4",
							"h5" => "h5",
							"h6" => "h6",
						),
						"description" => ""
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Title Top/Bottom Padding (px)', 'mikado-core' ),
						'param_name' => 'title_padding',
						'admin_label' => true,
						'value' => '',
						'description' => ''
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Content Padding', 'mikado-core' ),
						'param_name' => 'content_padding',
						'admin_label' => true,
						'value' => '',
						'description' => esc_html__( 'Enter padding in format 0px 1px 0px 0px', 'mikado-core' )
					)
				)

			));
		}
	}	
	public function render($atts, $content = null) {

		$default_atts = (array(
			'title' => esc_html__( "Accordion Title", 'mikado-core' ),
			'title_tag' => 'h6',
			'title_padding' => '',
			'content_padding' => '',
			'el_id' => ''
		));
		
		$params = shortcode_atts($default_atts, $atts);
		extract($params);
		$params['content'] = $content;
		$params['content_style'] = $this->getContentStyle($params);
		$params['title_style'] = $this->getTitleStyle($params);
		
		$output = '';
		
		$output .= mkd_core_get_core_shortcode_template_part('templates/accordion-template','accordions', '',$params);

		return $output;
		
	}

	/**
	 * Returns array of content styles
	 *
	 * @param $params
	 *
	 * @return array
	 */
	private function getContentStyle($params){
		$content_style = array();

		if ($params['content_padding'] !== ''){
			$content_style[] = 'padding: '.$params['content_padding'];
		}

		return implode('; ', $content_style);
	}


	/**
	 * Returns array of title styles
	 *
	 * @param $params
	 *
	 * @return array
	 */
	private function getTitleStyle($params){
		$title_style = array();

		if ($params['title_padding'] !== ''){
			$title_style[] = 'padding: '.libero_mikado_filter_px($params['title_padding']).'px 0';
		}

		return implode('; ', $title_style);
	}

}