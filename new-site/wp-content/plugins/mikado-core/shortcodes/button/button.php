<?php
namespace Libero\Modules\Shortcodes\Button;

use Libero\Modules\Shortcodes\Lib\ShortcodeInterface;


/**
 * Class Button that represents button shortcode
 * @package Libero\Modules\Shortcodes\Button
 */
class Button implements ShortcodeInterface {
    /**
     * @var string
     */
    private $base;

    /**
     * Sets base attribute and registers shortcode with Visual Composer
     */
    public function __construct() {
        $this->base = 'mkd_button';

        add_action('vc_before_init', array($this, 'vcMap'));
    }

    /**
     * Returns base attribute
     * @return string
     */
    public function getBase() {
        return $this->base;
    }

    /**
     * Maps shortcode to Visual Composer
     */
    public function vcMap() {
        vc_map(array(
            'name' => esc_html__( 'Button', 'mikado-core' ),
            'base'                      => $this->base,
            'category' => esc_html__( 'by MIKADO', 'mikado-core' ),
            'icon'                      => 'icon-wpb-button extended-custom-icon',
            'allowed_container_element' => 'vc_row',
            'params'                    => array_merge(
                array(
                    array(
                        'type'        => 'dropdown',
                        'heading' => esc_html__( 'Size', 'mikado-core' ),
                        'param_name'  => 'size',
                        'value'       => array(
	                        esc_html__('Default', 'mikado-core' )                => '',
	                        esc_html__('Small', 'mikado-core' )                  => 'small',
	                        esc_html__('Medium', 'mikado-core' )                 => 'medium',
	                        esc_html__('Large', 'mikado-core' )                  => 'large',
	                        esc_html__('Extra Large', 'mikado-core' )            => 'huge'
                        ),
                        'save_always' => true,
                        'admin_label' => true
                    ),
                    array(
                        'type'        => 'dropdown',
                        'heading' => esc_html__( 'Type', 'mikado-core' ),
                        'param_name'  => 'type',
                        'value'       => array(
	                        esc_html__('Default', 'mikado-core' ) => '',
	                        esc_html__('Outline', 'mikado-core' ) => 'outline',
	                        esc_html__('Solid', 'mikado-core' )   => 'solid',
	                        esc_html__('White', 'mikado-core' )   => 'white'
                        ),
                        'save_always' => true,
                        'admin_label' => true
                    ),
                    array(
                        'type'        => 'textfield',
                        'heading' => esc_html__( 'Text', 'mikado-core' ),
                        'param_name'  => 'text',
                        'admin_label' => true
                    ),
                    array(
                        'type'        => 'textfield',
                        'heading' => esc_html__( 'Link', 'mikado-core' ),
                        'param_name'  => 'link',
                        'admin_label' => true
                    ),
                    array(
                        'type'        => 'dropdown',
                        'heading' => esc_html__( 'Link Target', 'mikado-core' ),
                        'param_name'  => 'target',
                        'value'       => array(
	                        esc_html__('Self', 'mikado-core' )  => '_self',
	                        esc_html__('Blank', 'mikado-core' ) => '_blank'
                        ),
                        'save_always' => true,
                        'admin_label' => true
                    ),
                    array(
                        'type'        => 'textfield',
                        'heading' => esc_html__( 'Custom CSS class', 'mikado-core' ),
                        'param_name'  => 'custom_class',
                        'admin_label' => true
                    )
                ),
                libero_mikado_icon_collections()->getVCParamsArray(array(), '', true),
                array(
                    array(
                        'type'        => 'colorpicker',
                        'heading' => esc_html__( 'Color', 'mikado-core' ),
                        'param_name'  => 'color',
                        'group' => esc_html__( 'Design Options', 'mikado-core' ),
                        'admin_label' => true
                    ),
                    array(
                        'type'        => 'colorpicker',
                        'heading' => esc_html__( 'Hover Color', 'mikado-core' ),
                        'param_name'  => 'hover_color',
                        'group' => esc_html__( 'Design Options', 'mikado-core' ),
                        'admin_label' => true
                    ),
                    array(
                        'type'        => 'colorpicker',
                        'heading' => esc_html__( 'Background Color', 'mikado-core' ),
                        'param_name'  => 'background_color',
                        'admin_label' => true,
                        'dependency'  => array('element' => 'type', 'value' => array('', 'solid', 'white')),
                        'group' => esc_html__( 'Design Options', 'mikado-core' )
                    ),
                    array(
                        'type'        => 'colorpicker',
                        'heading' => esc_html__( 'Hover Background Color', 'mikado-core' ),
                        'param_name'  => 'hover_background_color',
                        'admin_label' => true,
                        'group' => esc_html__( 'Design Options', 'mikado-core' )
                    ),
                    array(
                        'type'        => 'colorpicker',
                        'heading' => esc_html__( 'Border Color', 'mikado-core' ),
                        'param_name'  => 'border_color',
                        'admin_label' => true,
                        'group' => esc_html__( 'Design Options', 'mikado-core' )
                    ),
                    array(
                        'type'        => 'colorpicker',
                        'heading' => esc_html__( 'Hover Border Color', 'mikado-core' ),
                        'param_name'  => 'hover_border_color',
                        'admin_label' => true,
                        'group' => esc_html__( 'Design Options', 'mikado-core' )
                    ),
                    array(
                        'type'        => 'colorpicker',
                        'heading' => esc_html__( 'Between Border Color', 'mikado-core' ),
                        'param_name'  => 'icon_border_color',
                        'admin_label' => true,
                        'group' => esc_html__( 'Design Options', 'mikado-core' ),
                        'dependency'  => array('element' => 'icon_pack', 'not_empty' => true),
                        'description' => esc_html__( 'Choose color for border between text and icon', 'mikado-core' )
                    ),
                    array(
                        'type'        => 'colorpicker',
                        'heading' => esc_html__( 'Between Hover Border Color', 'mikado-core' ),
                        'param_name'  => 'icon_hover_border_color',
                        'admin_label' => true,
                        'group' => esc_html__( 'Design Options', 'mikado-core' ),
                        'dependency'  => array('element' => 'icon_pack', 'not_empty' => true),
                        'description' => esc_html__( 'Choose hover color for border between text and icon', 'mikado-core' )
                    ),
                    array(
                        'type'        => 'textfield',
                        'heading' => esc_html__( 'Font Size (px)', 'mikado-core' ),
                        'param_name'  => 'font_size',
                        'admin_label' => true,
                        'group' => esc_html__( 'Design Options', 'mikado-core' )
                    ),
                    array(
                        'type'        => 'dropdown',
                        'heading' => esc_html__( 'Font Weight', 'mikado-core' ),
                        'param_name'  => 'font_weight',
                        'value'       => array_flip(libero_mikado_get_font_weight_array(true)),
                        'admin_label' => true,
                        'group' => esc_html__( 'Design Options', 'mikado-core' )
                    ),
                    array(
                        'type'        => 'textfield',
                        'heading' => esc_html__( 'Margin', 'mikado-core' ),
                        'param_name'  => 'margin',
                        'description' => esc_html__('Insert margin in format: 0px 0px 1px 0px', 'mikado-core'),
                        'admin_label' => true,
                        'group' => esc_html__( 'Design Options', 'mikado-core' )
                    ),
                    array(
                        'type'        => 'dropdown',
                        'heading' => esc_html__( 'Text Transform', 'mikado-core' ),
                        'param_name'  => 'text_transform',
                        'admin_label' => true,
                        'group' => esc_html__( 'Design Options', 'mikado-core' ),
                        'value'       => array_flip(libero_mikado_get_text_transform_array(true))
                    )
                )
            ) //close array_merge
        ));
    }

    /**
     * Renders HTML for button shortcode
     *
     * @param array $atts
     * @param null $content
     *
     * @return string
     */
    public function render($atts, $content = null) {
        $default_atts = array(
            'size'                    => '',
            'type'                    => '',
            'text'                    => '',
            'link'                    => '',
            'target'                  => '',
            'color'                   => '',
            'hover_color'             => '',
            'background_color'        => '',
            'hover_background_color'  => '',
            'border_color'            => '',
            'hover_border_color'      => '',
            'icon_border_color'       => '',
            'icon_hover_border_color' => '',
            'font_size'               => '',
            'font_weight'             => '',
            'margin'                  => '',
            'text_transform'          => '',
            'custom_class'            => '',
            'html_type'               => 'anchor',
            'input_name'              => '',
            'hover_animation'         => '',
            'custom_attrs'            => array()
        );

        $default_atts = array_merge($default_atts, libero_mikado_icon_collections()->getShortcodeParams());
        $params       = shortcode_atts($default_atts, $atts);

        if($params['html_type'] !== 'input') {
            $iconPackName        = libero_mikado_icon_collections()->getIconCollectionParamNameByKey($params['icon_pack']);
            $params['icon']      = $iconPackName ? $params[$iconPackName] : '';
            $params['show_icon'] = $params['icon'] !== '';
        }

        $params['size'] = !empty($params['size']) ? $params['size'] : 'medium';
        $params['type'] = !empty($params['type']) ? $params['type'] : 'solid';


        $params['link']   = !empty($params['link']) ? $params['link'] : '#';
        $params['target'] = !empty($params['target']) ? $params['target'] : '_self';

        //prepare params for template
        $params['button_classes']      = $this->getButtonClasses($params);
        $params['button_custom_attrs'] = !empty($params['custom_attrs']) ? $params['custom_attrs'] : array();
        $params['button_styles']       = $this->getButtonStyles($params);
        $params['text_holder_styles']  = $this->getTextHolderStyles($params);
        $params['button_data']         = $this->getButtonDataAttr($params);

        return mkd_core_get_core_shortcode_template_part('templates/'.$params['html_type'], 'button', $params['hover_animation'], $params);
    }

    /**
     * Returns array of button styles
     *
     * @param $params
     *
     * @return array
     */
    private function getButtonStyles($params) {
        $styles = array();

        if(!empty($params['color'])) {
            $styles[] = 'color: '.$params['color'];
        }

        if(!empty($params['background_color']) && $params['type'] !== 'outline') {
            $styles[] = 'background-color: '.$params['background_color'];
        }

        if(!empty($params['border_color'])) {
            $styles[] = 'border: 1px solid '.$params['border_color'];
        }

        if(!empty($params['font_size'])) {
            $styles[] = 'font-size: '.libero_mikado_filter_px($params['font_size']).'px';
        }

        if(!empty($params['font_weight'])) {
            $styles[] = 'font-weight: '.$params['font_weight'];
        }

        if(!empty($params['margin'])) {
            $styles[] = 'margin: '.$params['margin'];
        }

        if(!empty($params['text_transform'])) {
            $styles[] = 'text-transform: '.$params['text_transform'];
        }

        return $styles;
    }

    /**
     *
     * Returns array of button data attr
     *
     * @param $params
     *
     * @return array
     */
    private function getButtonDataAttr($params) {
        $data = array();

        if(!empty($params['hover_background_color'])) {
            $data['data-hover-bg-color'] = $params['hover_background_color'];
        }

        if(!empty($params['hover_color'])) {
            $data['data-hover-color'] = $params['hover_color'];
        }

        if(!empty($params['hover_color'])) {
            $data['data-hover-color'] = $params['hover_color'];
        }

        if(!empty($params['hover_border_color'])) {
            $data['data-hover-border-color'] = $params['hover_border_color'];
        }

        if(!empty($params['icon_hover_border_color'])) {
            $data['data-between-hover-border-color'] = $params['icon_hover_border_color'];
        }

        return $data;
    }

    /**
     * Returns array of HTML classes for button
     *
     * @param $params
     *
     * @return array
     */
    private function getButtonClasses($params) {
        $buttonClasses = array(
            'mkd-btn',
            'mkd-btn-'.$params['size'],
            'mkd-btn-'.$params['type']
        );

        if(!empty($params['hover_background_color'])) {
            $buttonClasses[] = 'mkd-btn-custom-hover-bg';
        }

        if(!empty($params['hover_border_color'])) {
            $buttonClasses[] = 'mkd-btn-custom-border-hover';
        }

        if(!empty($params['icon_hover_border_color'])) {
            $buttonClasses[] = 'mkd-btn-custom-icon-border-hover';
        }

        if(!empty($params['hover_color'])) {
            $buttonClasses[] = 'mkd-btn-custom-hover-color';
        }

        if(!empty($params['hover_color'])) {
            $buttonClasses[] = 'mkd-btn-custom-hover-color';
        }

		if ($params['html_type'] !== 'input'){
			if($params['icon'] !== '') {
				$buttonClasses[] = 'mkd-btn-icon';
			}
		}

        if(!empty($params['custom_class'])) {
            $buttonClasses[] = $params['custom_class'];
        }

        if(!empty($params['hover_animation'])) {
            $buttonClasses[] = 'mkd-btn-'.$params['hover_animation'];
        }

        return $buttonClasses;
    }

    /**
     * Returns array of text holder styles
     *
     * @param $params
     *
     * @return array
     */
    private function getTextHolderStyles($params) {
        $styles = array();

        if(!empty($params['icon_border_color'])) {
            $styles[] = 'border-color: '.$params['icon_border_color'];
        }

        return $styles;
    }
}