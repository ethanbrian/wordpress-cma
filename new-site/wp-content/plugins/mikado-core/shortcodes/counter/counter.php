<?php
namespace Libero\Modules\Counter;

use Libero\Modules\Shortcodes\Lib\ShortcodeInterface;
/**
 * Class Counter
 */
class Counter implements ShortcodeInterface {

	/**
	 * @var string
	 */
	private $base;

	public function __construct() {
		$this->base = 'mkd_counter';

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
	 * @see mkd_core_get_carousel_slider_array_vc()
	 */
	public function vcMap() {

		vc_map( array(
			'name' => esc_html__( 'Mikado Counter', 'mikado-core' ),
			'base' => $this->getBase(),
			'category' => esc_html__( 'by MIKADO', 'mikado-core' ),
			'admin_enqueue_css' => array(libero_mikado_get_skin_uri().'/assets/css/mkd-vc-extend.css'),
			'icon' => 'icon-wpb-counter extended-custom-icon',
			'allowed_container_element' => 'vc_row',
			'params' =>	array_merge(
				array(
					array(
						'type' => 'dropdown',
						'admin_label' => true,
						'heading' => esc_html__( 'Type', 'mikado-core' ),
						'param_name' => 'type',
						'value' => array(
							esc_html__('Zero Counter', 'mikado-core' ) => 'zero',
							esc_html__('Random Counter', 'mikado-core' ) => 'random'
						),
						'save_always' => true,
						'description' => ''
					),
					array(
						'type' => 'dropdown',
						'admin_label' => true,
						'heading' => esc_html__( 'Show Icon', 'mikado-core' ),
						'param_name' => 'show_icon',
						'value' => array(
							esc_html__('No', 'mikado-core' ) => 'no',
							esc_html__('Yes', 'mikado-core' ) => 'yes'
						),
						'save_always' => true
					)
				),
				libero_mikado_icon_collections()->getVCParamsArray(array('element' => 'show_icon', 'value' => array('yes'))),
				array(
					array(
						'type' => 'dropdown',
						'heading' => esc_html__( 'Position', 'mikado-core' ),
						'param_name' => 'position',
						'value' => array(
							esc_html__('Left', 'mikado-core' ) => 'left',
							esc_html__('Right', 'mikado-core' ) => 'right',
							esc_html__('Center', 'mikado-core' ) => 'center'
						),
						'save_always' => true
					),
					array(
						'type' => 'textfield',
						'admin_label' => true,
						'heading' => esc_html__( 'Digit', 'mikado-core' ),
						'param_name' => 'digit',
						'description' => ''
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Digit Font Size (px)', 'mikado-core' ),
						'param_name' => 'font_size',
						'description' => '',
						'group' => esc_html__( 'Design Options', 'mikado-core' ),
					),
					array(
						'type' => 'dropdown',
						'heading' => esc_html__( 'Digit Font Weight', 'mikado-core' ),
						'param_name' => 'font_weight',
						'value'       => array_flip(libero_mikado_get_font_weight_array(true)),
						'description' => '',
						'group' => esc_html__( 'Design Options', 'mikado-core' ),
					),
					array(
						'type' => 'colorpicker',
						'heading' => esc_html__( 'Digit Color', 'mikado-core' ),
						'param_name' => 'digit_color',
						'description' => '',
						'group' => esc_html__( 'Design Options', 'mikado-core' ),
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Currency', 'mikado-core' ),
						'param_name' => 'currency',
						'description' => esc_html__( 'Enter currency to be added before counter', 'mikado-core' ),
						'admin_label' => true
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Title', 'mikado-core' ),
						'param_name' => 'title',
						'admin_label' => true
					),
					array(
						'type' => 'colorpicker',
						'heading' => esc_html__( 'Title Color', 'mikado-core' ),
						'param_name' => 'title_color',
						'description' => '',
						'group' => esc_html__( 'Design Options', 'mikado-core' ),
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Text', 'mikado-core' ),
						'param_name' => 'text',
						'admin_label' => true,
						'description' => ''
					),
					array(
						'type' => 'colorpicker',
						'heading' => esc_html__( 'Text Color', 'mikado-core' ),
						'param_name' => 'text_color',
						'description' => '',
						'group' => esc_html__( 'Design Options', 'mikado-core' ),
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Padding Bottom(px)', 'mikado-core' ),
						'param_name' => 'padding_bottom',
						'description' => '',
						'group' => esc_html__( 'Design Options', 'mikado-core' ),
					),
					array(
						'type'       => 'textfield',
						'heading' => esc_html__( 'Custom Icon Size (px)', 'mikado-core' ),
						'param_name' => 'custom_icon_size',
						'group' => esc_html__( 'Design Options', 'mikado-core' ),
						'dependency' => array('element' => 'show_icon', 'value' => array('yes'))
					),
					array(
						'type'       => 'colorpicker',
						'heading' => esc_html__( 'Icon Color', 'mikado-core' ),
						'param_name' => 'icon_color',
						'group'      => 'Design Options',
						'dependency' => array('element' => 'show_icon', 'value' => array('yes'))
					),
				)
			)
		) );

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
			'type' => '',
			'position' => '',
			'digit' => '',
			'underline_digit' => '',
			'font_size' => '',
			'font_weight' => '',
			'digit_color' => '',
			'currency' => '',
			'title' => '',
			'title_color' => '',
			'text' => '',
			'text_color' => '',
			'padding_bottom' => '',
			'show_icon' => '',
			'custom_icon_size' => '',
			'icon_color' => ''
		);

		$args = array_merge($args, libero_mikado_icon_collections()->getShortcodeParams());
		$params = shortcode_atts($args, $atts);

		$params['counter_holder_styles'] = $this->getCounterHolderStyle($params);
		$params['counter_styles'] = $this->getCounterStyle($params);
		$params['counter_data'] = $this->getCounterData($params);
		$params['text_styles'] = $this->getCounterTextStyle($params);
		$params['title_styles'] = $this->getCounterTitleStyle($params);
		$params['icon_params'] = $this->getIconParameters($params);
		$params['counter_holder_classes'] = $this->getCounterHolderClasses($params);

		//Get HTML from template
		$html = mkd_core_get_core_shortcode_template_part('templates/counter-template', 'counter', '', $params);

		return $html;

	}

	/**
	 * Return Counter holder styles
	 *
	 * @param $params
	 * @return string
	 */
	private function getCounterHolderStyle($params) {
		$counterHolderStyle = array();

		if ($params['padding_bottom'] !== '') {

			$counterHolderStyle[] = 'padding-bottom: ' . $params['padding_bottom'] . 'px';

		}

		return implode(';', $counterHolderStyle);
	}

	/**
	 * Return Counter styles
	 *
	 * @param $params
	 * @return string
	 */
	private function getCounterStyle($params) {
		$counter_style = array();

		if ($params['font_size'] !== '') {
			$counter_style[] = 'font-size: ' . libero_mikado_filter_px($params['font_size']) . 'px';
		}
		if ($params['digit_color'] !== '') {
			$counter_style[] = 'color: ' . $params['digit_color'];
		}

		if ($params['font_weight'] !== '') {
			$counter_style[] = 'font-weight: ' . $params['font_weight'];
		}

		return implode(';', $counter_style);
	}

	/**
	 * Return Counter data
	 *
	 * @param $params
	 * @return string
	 */
	private function getCounterData($params) {
		$counter_data = array();

		if ($params['font_size'] !== '') {
			$counter_data[] = 'data-digit-size= ' . libero_mikado_filter_px($params['font_size']);
		}
		else{
			$counter_data[] = 'data-digit-size= 75';
		}

		return implode(' ', $counter_data);
	}

	/**
	 * Return Counter Text styles
	 *
	 * @param $params
	 * @return string
	 */
	private function getCounterTextStyle($params) {
		$text_style = array();

		if ($params['text_color'] !== '') {
			$text_style[] = 'color: ' . $params['text_color'];
		}

		return implode(';', $text_style);
	}


	/**
	 * Return Counter Title styles
	 *
	 * @param $params
	 * @return string
	 */
	private function getCounterTitleStyle($params) {
		$title_style = array();

		if ($params['title_color'] !== '') {
			$title_style[] = 'color: ' . $params['title_color'];
		}

		return implode(';', $title_style);
	}

	/**
	 * Return Counter Class
	 *
	 * @param $params, $icon_exist
	 * @return string
	 */
	private function getCounterHolderClasses($params) {
		$counter_holder_classes = array();

		if ($params['position'] !== '') {
			$counter_holder_classes[] = $params['position'];
		}

		return implode(' ', $counter_holder_classes);
	}

	/**
	 * Returns parameters for icon shortcode as a string
	 *
	 * @param $params
	 *
	 * @return array
	 */
	private function getIconParameters($params) {
		$params_array = array();

		if ($params['show_icon'] == 'yes'){

			$icon_pack_name = libero_mikado_icon_collections()->getIconCollectionParamNameByKey($params['icon_pack']);

			$params_array['icon_pack']   = $params['icon_pack'];
			$params_array[$icon_pack_name] = $params[$icon_pack_name];

			if($params_array[$icon_pack_name] !== ''){

				if(!empty($params['custom_icon_size'])) {
					$params_array['custom_size'] = $params['custom_icon_size'];
				}

				$params_array['type'] = 'normal';
				$params_array['icon_color'] = $params['icon_color'];
			}
		}

		return $params_array;
	}
}