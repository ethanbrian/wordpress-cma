<?php
namespace Libero\Modules\Shortcodes\InteractiveImage;

use Libero\Modules\Shortcodes\Lib\ShortcodeInterface;

class InteractiveImage implements ShortcodeInterface {

	/**
	 * @var string
	 */
	private $base;

	/**
	 * Interactive image constructor.
	 */
	public function __construct() {
		$this->base = 'mkd_interactive_image';

		add_action('vc_before_init', array($this, 'vcMap'));
	}

	/**
	 * Returns base for shortcode
	 * @return string
	 */
	public function getBase()
	{
		return $this->base;
	}

	/**
	 * Maps shortcode to Visual Composer. Hooked on vc_before_init
	 *
	 * @see mkd_core_get_carousel_slider_array_vc()
	 */
	public function vcMap() {

		vc_map( array(
				'name' => esc_html__( 'Mikado Interactive Image', 'mikado-core' ),
				'base' => $this->getBase(),
				'category' => esc_html__( 'by MIKADO', 'mikado-core' ),
				'icon' => 'icon-wpb-interactive-image extended-custom-icon',
				'allowed_container_element' => 'vc_row',
				'params' => array(
					array(
						"type" => "attach_image",
						"heading" => esc_html__( "Image", 'mikado-core' ),
						"param_name" => "image"
					),
					array(
						"type" => "dropdown",
						"heading" => esc_html__( "Animate image on hover", 'mikado-core' ),
						"param_name" => "animate_image_on_hover",
						"value" => array(
							esc_html__("Yes", 'mikado-core' ) => "yes",
							esc_html__("No", 'mikado-core' ) => "no"
						),
						"save_always" => true
					),
					array(
						"type" => "dropdown",
						"heading" => esc_html__( "Add Checkmark", 'mikado-core' ),
						"param_name" => "add_checkmark",
						"value" => array(
							esc_html__("Yes", 'mikado-core' ) => "yes",
							esc_html__("No", 'mikado-core' ) => "no"
						),
						"save_always" => true
					),
					array(
						"type" => "textarea",
						"heading" => esc_html__( "Checkmark Left Offset", 'mikado-core' ),
						"param_name" => "left_offset",
						"value" => "",
						"save_always" => true,
						"dependency" => array('element' => 'add_checkmark', 'value' => 'yes')
					),
					array(
						"type" => "textarea",
						"heading" => esc_html__( "Checkmark Top Offset", 'mikado-core' ),
						"param_name" => "top_offset",
						"value" => "",
						"save_always" => true,
						"dependency" => array('element' => 'add_checkmark', 'value' => 'yes')
					),
					array(
						"type" => "textarea",
						"heading" => esc_html__( "Link", 'mikado-core' ),
						"param_name" => "link",
						"value" => "http://",
						"description" => esc_html__( "Enter an optional URL to link to.", 'mikado-core' )
					)
				)
		) );

	}


	/**
	 * Renders shortcodes HTML
	 *
	 * @param $atts array of shortcode params
	 * @return string
	 */
	public function render($atts, $content = null) {

		$args = array(
			'add_checkmark' => '',
			'animate_image_on_hover' => '',
			'link' => '',
			'image' => '',
			'top_offset' => '0',
			'left_offset' => '0'
		);

		$params = shortcode_atts($args, $atts);

		$params['checkmark_position'] = $this->getCheckmarkPosition($params);
		$params['classes'] = $this->getClasses($params);

		//Get HTML from template
		$html = mkd_core_get_core_shortcode_template_part('templates/interactive-image-template', 'interactive-image','',$params);

		return $html;
	}


	/**
	 * Return Position offset for Checkmark
	 *
	 * @param $params
	 * @return string
	 */
	private function getCheckmarkPosition($params) {
		$checkmark_position = array();

		if ($params['add_checkmark'] == 'yes'){
			if ($params['top_offset'] !== '') {
				if (!libero_mikado_string_ends_with($params['top_offset'],'px') && !libero_mikado_string_ends_with($params['top_offset'],'%')){
					$params['top_offset'] = $params['top_offset'].'px';
				}
				$checkmark_position[] = 'top: '.$params['top_offset'];
			}

			if ($params['left_offset'] !== '') {
				if (!libero_mikado_string_ends_with($params['left_offset'],'px') && !libero_mikado_string_ends_with($params['left_offset'],'%')){
					$params['left_offset'] = $params['left_offset'].'px';
				}
				$checkmark_position[] = 'left: '.$params['left_offset'];
			}
		}

		return implode(';', $checkmark_position);
	}

	/**
	 * Return classes
	 *
	 * @param $params
	 * @return false|string
	 */
	private function getClasses($params) {
		$classes = array();
		
		if($params['animate_image_on_hover'] == 'yes') {
			$classes[] = 'mkd-animate-image';
		}

		if($params['add_checkmark'] == 'yes') {
			$classes[] = 'mkd-checkmark';
		}

		if($params['link'] != '') {
			$classes[] = 'mkd-linked';
		}
		
		return implode(' ',$classes);
	}

}