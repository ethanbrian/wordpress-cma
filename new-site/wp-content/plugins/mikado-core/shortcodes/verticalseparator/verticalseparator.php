<?php
namespace Libero\Modules\Shortcodes\VerticalSeparator;

use Libero\Modules\Shortcodes\Lib\ShortcodeInterface;
/**
 * Class VerticalSeparator
 */
class VerticalSeparator implements ShortcodeInterface {

	/**
	 * @var string
	 */
	private $base;

	public function __construct() {
		$this->base = 'mkd_verticalsep';

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
	 * Maps shortcode to Visual Composer. Hooked on vc_before_init
	 *
	 */
	public function vcMap() {

		vc_map( array(
				'name' => esc_html__( 'Mikado Vertical Separator', 'mikado-core' ),
				'base' => $this->getBase(),
				'category' => esc_html__( 'by MIKADO', 'mikado-core' ),
				'icon' => 'icon-wpb-vert-sep extended-custom-icon',
				'allowed_container_element' => 'vc_row',
				'params' => array(
					array(
						"type" => "colorpicker",
						"holder" => "div",
						"class" => "",
						"heading" => esc_html__( "Color", 'mikado-core' ),
						"param_name" => "color",
						"value" => "",
						"description" => esc_html__( "Set the separator color. The default value is e8e8e8.", 'mikado-core' )
					),
					array(
						"type" => "textfield",
						"holder" => "div",
						"class" => "",
						"heading" => esc_html__( "Height (px)", 'mikado-core' ),
						"param_name" => "height",
						"value" => "",
						"description" => esc_html__( "Set the separator height. The default value is 15px.", 'mikado-core' )
					),
					array(
						"type" => "textfield",
						"holder" => "div",
						"class" => "",
						"heading" => esc_html__( "Margin Left (px)", 'mikado-core' ),
						"param_name" => "margin_left",
						"value" => ""
					),
					array(
						"type" => "textfield",
						"holder" => "div",
						"class" => "",
						"heading" => esc_html__( "Margin Right (px)", 'mikado-core' ),
						"param_name" => "margin_right",
						"value" => ""
					),
					array(
						"type" => "textfield",
						"holder" => "div",
						"class" => "",
						"heading" => esc_html__( "Bottom Offset (px)", 'mikado-core' ),
						"param_name" => "bottom_offset",
						"value" => "",
						"description" => esc_html__( "Set the bottom offset for better centering, if needed.", 'mikado-core' )
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
			'color' => '',
			'height' => '',
			'margin_left' => '',
			'margin_right' => '',
			'bottom_offset' => ''
		);

		$params = shortcode_atts($args, $atts);

		$params['separator_style'] = $this->getSeparatorStyle($params);

		//Get HTML from template
		$html = mkd_core_get_core_shortcode_template_part('templates/vertical-sep-template', 'verticalseparator', '', $params);

		return $html;

	}

	/**
	 * Return Style for Vertical Separator
	 *
	 * @param $params
	 * @return string
	 */
	private function getSeparatorStyle($params) {
		$sep_style = array();

		if ($params['color'] !== '') {
			$sep_style[] = 'border-right-color: '.$params['color'];
		}

		if ($params['height'] !== '') {
			$sep_style[] = 'height: '.libero_mikado_filter_px($params['height']).'px';
		}

		if ($params['margin_left'] !== '') {
			$sep_style[] = 'margin-left: '.libero_mikado_filter_px($params['margin_left']).'px';
		}

		if ($params['margin_right'] !== '') {
			$sep_style[] = 'margin-right: '.libero_mikado_filter_px($params['margin_right']).'px';
		}

		if ($params['bottom_offset'] !== '') {
			$sep_style[] = 'margin-bottom: '.libero_mikado_filter_px($params['bottom_offset']).'px';
		}

		return implode(';', $sep_style);
	}
}