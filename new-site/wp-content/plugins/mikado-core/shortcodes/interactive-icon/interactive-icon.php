<?php
namespace Libero\Modules\Shortcodes\InteractiveIcon;

use Libero\Modules\Shortcodes\Lib\ShortcodeInterface;

class InteractiveIcon implements ShortcodeInterface{

	private $base;

	/**
	 * Interactive Icon constructor.
	 */
	public function __construct() {
		$this->base = 'mkd_interactive_icon';

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

		vc_map(array(
			'name' => esc_html__( 'Mikado Interactive Icon', 'mikado-core' ),
			'base'                      => $this->getBase(),
			'category' => esc_html__( 'by MIKADO', 'mikado-core' ),
			'icon'                      => 'icon-wpb-interactive-icon extended-custom-icon',
			'allowed_container_element' => 'vc_row',
			'params'                    => array(
				array(
					'type'			=> 'attach_image',
					'heading' => esc_html__( 'Icon', 'mikado-core' ),
					'param_name'	=> 'icon',
					'description' => esc_html__( 'Select icon image from media library.', 'mikado-core' )
				),
				array(
					'type'			=> 'textfield',
					'heading' => esc_html__( 'Title', 'mikado-core' ),
					'param_name'	=> 'title',
					'description' => esc_html__( 'Enter the title to be displayed.', 'mikado-core' )
				),
				array(
					'type'			=> 'textfield',
					'heading' => esc_html__( 'Text', 'mikado-core' ),
					'param_name'	=> 'text',
					'description' => esc_html__( 'Enter the text to be displayed.', 'mikado-core' )
				),
				array(
					'type' => 'colorpicker',
					'heading' => esc_html__( 'Title and Text Color', 'mikado-core' ),
					'param_name' => 'typography_color',
					'description' => ''
				),
				array(
					'type' => 'colorpicker',
					'heading' => esc_html__( 'Separator', 'mikado-core' ),
					'param_name' => 'separator_color',
					'description' => ''
				),
				array(
					'type'			=> 'textfield',
					'heading' => esc_html__( 'URL', 'mikado-core' ),
					'param_name'	=> 'url',
					'description' => esc_html__( 'Enter the URL to link to.', 'mikado-core' )
				),
				array(
					'type'			=>'dropdown',
					'heading' => esc_html__( 'Add Right Border', 'mikado-core' ),
					'param_name'	=> 'right_border',
					'value'         => array(
						esc_html__('Yes', 'mikado-core' )       => 'yes',
						esc_html__('No', 'mikado-core' )        => 'no'
					),
					'save_always'   => 'true',
					'description' => esc_html__( 'Sets right border for the entire element.', 'mikado-core' )
				),
				array(
					'type'			=> 'dropdown',
					'heading' => esc_html__( 'Hide Right Border Under', 'mikado-core' ),
					'param_name'	=> 'hide_right_border',
					'value'			=> array(
						esc_html__('Default', 'mikado-core' )			=> '',
						esc_html__('Below 1280px', 'mikado-core' )		=> '1280',
						esc_html__('Below 1024px', 'mikado-core' )		=> '1024',
						esc_html__('Below 768px', 'mikado-core' )		=> '768',
						esc_html__('Below 600px', 'mikado-core' )		=> '600',
						esc_html__('Below 480px', 'mikado-core' )		=> '480'
					),
					'description' => esc_html__( 'Choose width under whitch right border will be hidden', 'mikado-core' ),
					'dependency'	=> array('element' => 'right_border', 'value' => 'yes'),
					'group' => esc_html__( 'Responsive', 'mikado-core' )
				),
				array(
					'type'			=>'dropdown',
					'heading' => esc_html__( 'Add Bottom Border', 'mikado-core' ),
					'param_name'	=> 'bottom_border',
					'value'         => array(
						esc_html__('No', 'mikado-core' )        => 'no',
						esc_html__('Yes', 'mikado-core' )       => 'yes'
					),
					'save_always'   => 'true',
					'description' => esc_html__( 'Sets bottom border for the entire element.', 'mikado-core' )
				),
				array(
					'type'			=> 'dropdown',
					'heading' => esc_html__( 'Show Bottom Border Under', 'mikado-core' ),
					'param_name'	=> 'show_bottom_border',
					'value'			=> array(
						esc_html__('Default', 'mikado-core' )			=> '',
						esc_html__('Always', 'mikado-core' )			=> 'always',
						esc_html__('Below 1280px', 'mikado-core' )		=> '1280',
						esc_html__('Below 1024px', 'mikado-core' )		=> '1024',
						esc_html__('Below 768px', 'mikado-core' )		=> '768',
						esc_html__('Below 600px', 'mikado-core' )		=> '600',
						esc_html__('Below 480px', 'mikado-core' )		=> '480',
					),
					'description' => esc_html__( 'Choose width under whitch bottom border will be shown', 'mikado-core' ),
					'dependency'	=> array('element' => 'bottom_border', 'value' => 'yes'),
					'group'			=> 'Responsive'
				)
			)
		));

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
			'icon'					=> '',
			'title'					=> '',
			'text'					=> '',
			'typography_color'		=> '',
			'separator_color'		=> '',
			'url'					=> '',
			'right_border'			=> 'yes',
			'hide_right_border'		=> '',
			'bottom_border'			=> '',
			'show_bottom_border'	=> '768',

		);

		$params = shortcode_atts($args, $atts);

		$params['typography_styles'] = $this->getTypographyStyles($params);
		$params['separator_styles'] = $this->getSeparatorStyles($params);
		$params['interactive_icon_classes'] = $this->getInteractiveIconClasses($params);

		$html = mkd_core_get_core_shortcode_template_part('templates/interactive-icon-template', 'interactive-icon', '', $params);

		return $html;

	}

	/**
	 * Return CSS styles for Typography elements
	 *
	 * @param $params
	 * @return string
	 */
	private function getTypographyStyles($params) {
		$typography_styles = array();

		if ($params['typography_color'] !== '') {
			$typography_styles[] = 'color: ' . $params['typography_color'] ;
		}

		return implode(';', $typography_styles);
	}

	/**
	 * Return CSS styles for Separator element
	 *
	 * @param $params
	 * @return string
	 */
	private function getSeparatorStyles($params) {
		$separator_styles = array();

		if ($params['separator_color'] !== '') {
			$separator_styles[] = 'background-color: ' . $params['separator_color'] ;
		}

		return implode(';', $separator_styles);
	}


	/**
	 * Return Classes for Interactive Icon
	 *
	 * @param $params
	 * @return string
	 */
	private function getInteractiveIconClasses($params) {

		$interactive_icon_classes = '';

		if ($params['right_border'] == 'yes') {
			$interactive_icon_classes .= 'mkd-right-border-added';

			if ($params['hide_right_border'] !== ''){
				$interactive_icon_classes .= ' mkd-right-border-hide-'.$params['hide_right_border'];
			}
		}

		if ($params['bottom_border'] == 'yes') {
			$interactive_icon_classes .= ' mkd-bottom-border-added';

			if ($params['show_bottom_border'] !== ''){
				$interactive_icon_classes .= ' mkd-bottom-border-show-'.$params['show_bottom_border'];
			}
		}

		return $interactive_icon_classes;
	}

}