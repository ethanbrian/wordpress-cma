<?php
namespace Libero\Modules\CallToAction;

use Libero\Modules\Shortcodes\Lib\ShortcodeInterface;
/**
 * Class CallToAction
 */
class CallToAction implements ShortcodeInterface {

	/**
	 * @var string
	 */
	private $base;

	public function __construct() {
		$this->base = 'mkd_call_to_action';

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

		$call_to_action_button_icons_array = array();
		$call_to_action_button_IconCollections = libero_mikado_icon_collections()->iconCollections;
		foreach($call_to_action_button_IconCollections as $collection_key => $collection) {

			$call_to_action_button_icons_array[] = array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Button Icon', 'mikado-core' ),
				'param_name' => 'button_'.$collection->param,
				'value' => $collection->getIconsArray(),
				'save_always' => true,
				'dependency' => Array('element' => 'button_icon_pack', 'value' => array($collection_key))
			);

		}

		vc_map( array(
				'name' => esc_html__( 'Mikado Call to Action', 'mikado-core' ),
				'base' => $this->getBase(),
				'category' => esc_html__( 'by MIKADO', 'mikado-core' ),
				'icon' => 'icon-wpb-call-to-action extended-custom-icon',
				'allowed_container_element' => 'vc_row',
				'params' => array_merge(
					array(
						array(
							'type'          => 'dropdown',
							'heading' => esc_html__( 'Full Width', 'mikado-core' ),
							'param_name'    => 'full_width',
							'admin_label'	=> true,
							'value'         => array(
								esc_html__('Yes', 'mikado-core' )       => 'yes',
								esc_html__('No', 'mikado-core' )        => 'no'
							),
							'save_always' 	=> true,
							'description'   => '',
						),
						array(
							'type'          => 'dropdown',
							'heading' => esc_html__( 'Content in grid', 'mikado-core' ),
							'param_name'    => 'content_in_grid',
							'value'         => array(
								esc_html__('Yes', 'mikado-core' )       => 'yes',
								esc_html__('No', 'mikado-core' )        => 'no'
							),
							'save_always'	=> true,
							'description'   => '',
							'dependency'    => array('element' => 'full_width', 'value' => 'yes')
						),
						array(
							'type'          => 'dropdown',
							'heading' => esc_html__( 'Grid size', 'mikado-core' ),
							'param_name'    => 'grid_size',
							'value'         => array(
								'75/25'     => '75',
								'50/50'     => '50',
								'66/33'     => '66'
							),
							'save_always' 	=> true,
							'description'   => '',
							'dependency'    => array('element' => 'content_in_grid', 'value' => 'yes')
						),
						array(
							'type' 			=> 'dropdown',
							'heading' => esc_html__( 'Type', 'mikado-core' ),
							'param_name' 	=> 'type',
							'admin_label' 	=> true,
							'value' 		=> array(
								esc_html__('Normal', 'mikado-core' ) 	=> 'normal',
								esc_html__('With Icon', 'mikado-core' ) => 'with-icon',
							),
							'save_always' 	=> true,
							'description' 	=> ''
						)
					),
					libero_mikado_icon_collections()->getVCParamsArray(array('element' => 'type', 'value' => array('with-icon'))),
					array(
						array(
							'type' 			=> 'textfield',
							'heading' => esc_html__( 'Icon Size (px)', 'mikado-core' ),
							'param_name' 	=> 'icon_size',
							'description' 	=> '',
							'dependency' 	=> Array('element' => 'type', 'value' => array('with-icon')),
							'group' => esc_html__( 'Design Options', 'mikado-core' ),
						),
						array(
							'type' 			=> 'colorpicker',
							'heading' => esc_html__( 'Icon Color', 'mikado-core' ),
							'param_name' 	=> 'icon_color',
							'description' 	=> '',
							'dependency' 	=> Array('element' => 'type', 'value' => array('with-icon')),
							'group' => esc_html__( 'Design Options', 'mikado-core' ),
						),
						array(
							'type' 			=> 'textfield',
							'heading' => esc_html__( 'Box Padding (top right bottom left) px', 'mikado-core' ),
							'param_name' 	=> 'box_padding',
							'admin_label' 	=> true,
							'description' => esc_html__( 'Default padding is 22px on all sides', 'mikado-core' ),
							'group' => esc_html__( 'Design Options', 'mikado-core' )
						),
						array(
							'type' 			=> 'colorpicker',
							'heading' => esc_html__( 'Box Background Color', 'mikado-core' ),
							'param_name' 	=> 'background_color',
							'admin_label' 	=> true,
							'description' => esc_html__( 'Choose background color for Call to Action Box', 'mikado-core' ),
							'group' => esc_html__( 'Design Options', 'mikado-core' )
						),
						array(
							'type'			=> 'dropdown',
							'heading' => esc_html__( 'Box Border Showing', 'mikado-core' ),
							'param_name'	=> 'show_border',
							'value'			=> array(
								esc_html__('Default', 'mikado-core' ) => '',
								esc_html__('No', 'mikado-core' ) => 'no',
								esc_html__('Yes', 'mikado-core' ) => 'yes'
							),
							'group' => esc_html__( 'Design Options', 'mikado-core' )
						),
						array(
							'type' 			=> 'textfield',
							'heading' => esc_html__( 'Default Text Font Size (px)', 'mikado-core' ),
							'param_name' 	=> 'text_size',
							'description' => esc_html__( 'Font size for p tag', 'mikado-core' ),
							'group' => esc_html__( 'Design Options', 'mikado-core' ),
						),
						array(
							'type' 			=> 'dropdown',
							'heading' => esc_html__( 'Show Button', 'mikado-core' ),
							'param_name' 	=> 'show_button',
							'value' 		=> array(
								esc_html__('Yes', 'mikado-core' ) 		=> 'yes',
								esc_html__('No', 'mikado-core' ) 		=> 'no'
							),
							'admin_label' 	=> true,
							'save_always' 	=> true,
							'description' 	=> ''
						),
						array(
							'type' => 'dropdown',
							'heading' => esc_html__( 'Button Position', 'mikado-core' ),
							'param_name' => 'button_position',
							'value' => array(
								esc_html__('Default/right', 'mikado-core' ) => '',
								esc_html__('Center', 'mikado-core' ) => 'center',
								esc_html__('Left', 'mikado-core' ) => 'left'
							),
							'description' => '',
							'dependency' => array('element' => 'show_button', 'value' => array('yes'))
						),
						array(
							'type' => 'dropdown',
							'heading' => esc_html__( 'Button Size', 'mikado-core' ),
							'param_name' => 'button_size',
							'value' => array(
								esc_html__('Default', 'mikado-core' ) => '',
								esc_html__('Small', 'mikado-core' ) => 'small',
								esc_html__('Medium', 'mikado-core' ) => 'medium',
								esc_html__('Large', 'mikado-core' ) => 'large',
								esc_html__('Extra Large', 'mikado-core' ) => 'huge'
							),
							'description' => '',
							'dependency' => array('element' => 'show_button', 'value' => array('yes')),
							'group' => esc_html__( 'Design Options', 'mikado-core' ),
						),
						array(
							'type' => 'colorpicker',
							'heading' => esc_html__( 'Button Background Color', 'mikado-core' ),
							'param_name' => 'button_main_color',
							'value' => '',
							'description' => esc_html__( 'Choose the color to be used for button background.', 'mikado-core' ),
							'dependency' => array('element' => 'show_button', 'value' => array('yes')),
							'group' => esc_html__( 'Design Options', 'mikado-core' ),
						),
						array(
							'type' => 'colorpicker',
							'heading' => esc_html__( 'Button Border Color', 'mikado-core' ),
							'param_name' => 'button_border_color',
							'value' => '',
							'description' => esc_html__( 'Choose the color to be used for button border.', 'mikado-core' ),
							'dependency' => array('element' => 'show_button', 'value' => array('yes')),
							'group' => esc_html__( 'Design Options', 'mikado-core' ),
						),
						array(
							'type' => 'textfield',
							'heading' => esc_html__( 'Button Text', 'mikado-core' ),
							'param_name' => 'button_text',
							'admin_label' 	=> true,
							'description' => esc_html__( 'Default text is button', 'mikado-core' ),
							'dependency' => array('element' => 'show_button', 'value' => array('yes'))
						),
						array(
							'type' => 'textfield',
							'heading' => esc_html__( 'Button Link', 'mikado-core' ),
							'param_name' => 'button_link',
							'description' => '',
							'admin_label' 	=> true,
							'dependency' => array('element' => 'show_button', 'value' => array('yes'))
						),
						array(
							'type' => 'dropdown',
							'heading' => esc_html__( 'Button Target', 'mikado-core' ),
							'param_name' => 'button_target',
							'value' => array(
								'' => '',
								esc_html__('Self', 'mikado-core' ) => '_self',
								esc_html__('Blank', 'mikado-core' ) => '_blank'
							),
							'description' => '',
							'dependency' => array('element' => 'show_button', 'value' => array('yes'))
						),
						array(
							'type' => 'dropdown',
							'heading' => esc_html__( 'Button Icon Pack', 'mikado-core' ),
							'param_name' => 'button_icon_pack',
							'value' => array_merge(array('No Icon' => ''),libero_mikado_icon_collections()->getIconCollectionsVC()),
							'save_always' => true,
							'dependency' => array('element' => 'show_button', 'value' => array('yes'))
						)
					),
					$call_to_action_button_icons_array,
					array(
						array(
							'type' => 'textarea_html',
							'admin_label' => true,
							'heading' => esc_html__( 'Content', 'mikado-core' ),
							'param_name' => 'content',
							'value' => '<p>'.'I am test text for Call to action.'.'</p>',
							'description' => ''
						)
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
			'type' => 'normal',
			'full_width' => 'yes',
			'content_in_grid' => 'yes',
			'grid_size' => '66',
			'icon_size' => '',
			'icon_color' => '',
			'box_padding' => '22px',
			'background_color' => '',
			'show_border' => '',
			'text_size' => '',
			'show_button' => 'yes',
			'button_position' => 'right',
			'button_size' => 'medium',
			'button_main_color' => '',
			'button_border_color' => '',
			'button_link' => '',
			'button_text' => 'button',
			'button_target' => '',
			'button_icon_pack' => ''
		);

		$call_to_action_icons_form_fields = array();

		foreach (libero_mikado_icon_collections()->iconCollections as $collection_key => $collection) {

			$call_to_action_icons_form_fields['button_' . $collection->param ] = '';

		}

		$args = array_merge($args, libero_mikado_icon_collections()->getShortcodeParams(),$call_to_action_icons_form_fields);

		$params = shortcode_atts($args, $atts);

		$params['content'] = $content;
		$params['text_wrapper_classes'] = $this->getTextWrapperClasses($params);
		$params['content_styles'] = $this->getContentStyles($params);
		$params['call_to_action_styles'] = $this->getCallToActionStyles($params);
		$params['call_to_action_padding'] = $this->getCallToActionPadding($params);
		$params['icon'] = $this->getCallToActionIcon($params);
		$params['button_parameters'] = $this->getButtonParameters($params);

		//Get HTML from template
		$html = mkd_core_get_core_shortcode_template_part('templates/call-to-action-template', 'calltoaction', '', $params);

		return $html;

	}

	/**
	 * Return Classes for Call To Action text wrapper
	 *
	 * @param $params
	 * @return string
	 */
	private function getTextWrapperClasses($params) {
		return ( $params['show_button'] == 'yes') ? 'mkd-call-to-action-column1 mkd-call-to-action-cell' : '';
	}

	/**
	 * Return CSS styles for Call To Action Icon
	 *
	 * @param $params
	 * @return string
	 */
	private function getIconStyles($params) {
		$icon_style = array();

		if ($params['icon_size'] !== '') {
			$icon_style[] = 'font-size: ' . $params['icon_size'] . 'px; ';
		}
		if ($params['icon_color'] !== '') {
			$icon_style[] = 'color: ' . $params['icon_color']. '; ';
		}

		return implode(';', $icon_style);
	}

	/**
	 * Return CSS styles for Call To Action Content
	 *
	 * @param $params
	 * @return string
	 */
	private function getContentStyles($params) {
		$content_styles = array();

		if ($params['text_size'] !== '') {
			$content_styles[] = 'font-size: ' . $params['text_size'] . 'px';
		}

		return implode(';', $content_styles);
	}

	/**
	 * Return CSS styles for Call To Action shortcode
	 *
	 * @param $params
	 * @return string
	 */
	private function getCallToActionStyles($params) {
		$call_to_action_styles = array();

		if ($params['background_color'] != '') {
			$call_to_action_styles[] = 'background-color: ' . $params['background_color'];
		}

		if ($params['show_border'] == 'yes'){
			$call_to_action_styles[] = 'border: 1px solid #d8d8d8;';
		}

		return implode(';', $call_to_action_styles);
	}

	/**
	 * Return padding for Call To Action shortcode
	 *
	 * @param $params
	 * @return string
	 */
	private function getCallToActionPadding($params) {
		$call_to_action_padding = array();

		if ($params['box_padding'] != '') {
			$call_to_action_padding[] = 'padding: ' . $params['box_padding'] . ';';
		}

		return implode(';', $call_to_action_padding);
	}

	/**
	 * Return Icon for Call To Action Shortcode
	 *
	 * @param $params
	 * @return mixed
	 */
	private function getCallToActionIcon($params) {

		$icon = libero_mikado_icon_collections()->getIconCollectionParamNameByKey($params['icon_pack']);
		$iconStyles = array();
		$iconStyles['icon_attributes']['style'] = $this->getIconStyles($params);
		$call_to_action_icon = '';
		if(!empty($params[$icon])){			
			$call_to_action_icon = libero_mikado_icon_collections()->renderIcon( $params[$icon], $params['icon_pack'], $iconStyles );
		}
		return $call_to_action_icon;

	}
	
	private function getButtonParameters($params) {
		$button_params_array = array();
		
		if(!empty($params['button_link'])) {
			$button_params_array['link'] = $params['button_link'];
		}
		
		if(!empty($params['button_size'])) {
			$button_params_array['size'] = $params['button_size'];
		}
		
		if(!empty($params['button_icon_pack'])) {
			$button_params_array['icon_pack'] = $params['button_icon_pack'];
			$iconPackName = libero_mikado_icon_collections()->getIconCollectionParamNameByKey($params['button_icon_pack']);
			$button_params_array[$iconPackName] = $params['button_'.$iconPackName];		
		}
				
		if(!empty($params['button_target'])) {
			$button_params_array['target'] = $params['button_target'];
		}
		
		if(!empty($params['button_text'])) {
			$button_params_array['text'] = $params['button_text'];
		}

		if(!empty($params['button_main_color'])) {
			$button_params_array['background_color'] = $params['button_main_color'];
		}

		if(!empty($params['button_border_color'])) {
			$button_params_array['border_color'] = $params['button_border_color'];
			$button_params_array['icon_border_color'] = $params['button_border_color'];
		}

		return $button_params_array;
	}
}