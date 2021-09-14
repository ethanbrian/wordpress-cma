<?php
namespace Libero\Modules\Accordion;

use Libero\Modules\Shortcodes\Lib\ShortcodeInterface;
/**
	* class Accordions
*/
class Accordion implements ShortcodeInterface{
	/**
	 * @var string
	 */
	private $base;

	function __construct() {
		$this->base = 'mkd_accordion';
		add_action('vc_before_init', array($this, 'vcMap'));
	}

	public function getBase() {
		return	$this->base;
	}

	public function vcMap() {

		vc_map( array(
			'name' =>  esc_html__( 'Mikado Accordion', 'mikado-core' ),
			'base' => $this->base,
			'as_parent' => array('only' => 'mkd_accordion_tab'),
			'content_element' => true,
			'category' => esc_html__( 'by MIKADO', 'mikado-core' ),
			'icon' => 'icon-wpb-accordion extended-custom-icon',
			'show_settings_on_create' => true,
			'js_view' => 'VcColumnView',
			'params' => array(
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Extra class name', 'mikado-core' ),
					'param_name' => 'el_class',
					'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'mikado-core' )
				),
				array(
					'type' => 'dropdown',
					'class' => '',
					'heading' => esc_html__( 'Style', 'mikado-core' ),
					'param_name' => 'style',
					'value' => array(
						esc_html__('Accordion', 'mikado-core' )             => 'accordion',
						esc_html__('Toggle', 'mikado-core' )                => 'toggle'
					),
					'save_always' => true,
					'description' => ''
				)
			)
		) );
	}
	public function render($atts, $content = null) {
		$default_atts=(array(
			'title' => '',
			'style' => 'accordion'
		));
		$params = shortcode_atts($default_atts, $atts);
		extract($params);
		
 		$acc_class = $this->getAccordionClasses($params);
		$params['acc_class'] = $acc_class;
		$params['content'] = $content;
		
		$output = '';
		
		$output .= mkd_core_get_core_shortcode_template_part('templates/accordion-holder-template','accordions', '', $params);

		return $output;
	}

	/**
	   * Generates accordion classes
	   *
	   * @param $params
	   *
	   * @return string
	*/
	private function getAccordionClasses($params){
		
		$acc_class = '';
		$style = $params['style'];
		switch($style) {
			case 'toggle':
				$acc_class .= 'mkd-toggle mkd-initial';
				break;
			default:
				$acc_class = 'mkd-accordion mkd-initial';
		}
		return $acc_class;
	}
}
