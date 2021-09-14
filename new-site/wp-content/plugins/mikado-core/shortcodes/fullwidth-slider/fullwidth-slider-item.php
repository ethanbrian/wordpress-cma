<?php
namespace Libero\Modules\FullwidthSliderItem;

use Libero\Modules\Shortcodes\Lib\ShortcodeInterface;

class FullwidthSliderItem implements ShortcodeInterface{

    private $base;

    function __construct() {
        $this->base = 'mkd_fullwidth_slider_item';
        add_action('vc_before_init', array($this, 'vcMap'));
    }

    public function getBase() {
        return $this->base;
    }

    public function vcMap() {
        vc_map(array(
                'name' => esc_html__( 'Fullwidth Slider Item', 'mikado-core' ),
                'base' => $this->base,
                'allowed_container_element' => 'vc_row',
                'as_child' => array('only' => 'mkd_fullwidth_slider_holder'),
                'category' => esc_html__( 'by MIKADO', 'mikado-core' ),
                'icon' => 'icon-wpb-fullwidth-slider-item extended-custom-icon',
                'params' => array_merge(
                    array(
                        array(
                            'type'       => 'attach_image',
                            'heading' => esc_html__( 'Image', 'mikado-core' ),
                            'param_name' => 'image'
                        ),
                        array(
                            'type' => 'textfield',
                            'heading' => esc_html__( 'Title', 'mikado-core' ),
                            'param_name' => 'title',
                            'admin_label' => true,
                            'description' => ''
                        ),
                        array(
                            'type' => 'dropdown',
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
                            'type' => 'textfield',
                            'heading' => esc_html__( 'Text', 'mikado-core' ),
                            'param_name' => 'text',
                            'admin_label' => true,
                            'description' => ''
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
                            'dependency' => array('element' => 'show_button', 'value' => array('yes'))
                        ),
                        array(
                            'type' => 'dropdown',
                            'heading' => esc_html__( 'Button Type', 'mikado-core' ),
                            'param_name' => 'button_type',
                            'value' => array(
	                            esc_html__('Default', 'mikado-core' ) => '',
	                            esc_html__('Outline', 'mikado-core' ) => 'outline',
	                            esc_html__('Solid', 'mikado-core' )   => 'solid'
                            ),
                            'description' => '',
                            'dependency' => array('element' => 'show_button', 'value' => array('yes'))
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
                            'heading' => esc_html__( 'Show Button Icon', 'mikado-core' ),
                            'param_name' => 'button_icon',
                            'value' => array(
	                            esc_html__('Yes', 'mikado-core' ) => 'yes',
	                            esc_html__('No', 'mikado-core' )   => 'no'
                            ),
                            'description' => '',
                            'dependency' => array('element' => 'show_button', 'value' => array('yes'))
                        ),
                    ),
                    libero_mikado_icon_collections()->getVCParamsArray(array('element' => 'button_icon', 'value' => array('yes')))
                )
            )
        );
    }

    public function render($atts, $content = null) {

        $args = array(
            'image' => '',
            'title' => '',
            'title_tag' => 'h1',
            'text' => '',
            'show_button' => 'yes',
            'button_size' => '',
            'button_type' => '',
            'button_link' => '',
            'button_text' => 'button',
            'button_target' => '',
            'button_icon' => 'yes',
        );

        $args = array_merge($args, libero_mikado_icon_collections()->getShortcodeParams());
        $params = shortcode_atts($args, $atts);

        $params['button_parameters'] = $this->getButtonParameters($params);
        $params['image_src'] = $this->getImageSrc($params);
        $params['slide_holder_style'] = $this->getSlideHolderStyle($params);

        $html = mkd_core_get_core_shortcode_template_part('templates/fullwidth-slider-item-template', 'fullwidth-slider', '', $params);

        return $html;
    }

    private function getButtonParameters($params) {
        $button_params_array = array();

        if(!empty($params['button_link'])) {
            $button_params_array['link'] = $params['button_link'];
        }

        if(!empty($params['button_size'])) {
            $button_params_array['size'] = $params['button_size'];
        }

        if(!empty($params['button_type'])) {
            $button_params_array['type'] = $params['button_type'];
        }

        if($params['button_icon'] == 'yes') {
            $iconPackName = libero_mikado_icon_collections()->getIconCollectionParamNameByKey($params['icon_pack']);

            $button_params_array['icon_pack'] = $params['icon_pack'];
            $button_params_array[$iconPackName] = $params[$iconPackName];
        }

        if(!empty($params['button_target'])) {
            $button_params_array['target'] = $params['button_target'];
        }

        if(!empty($params['button_text'])) {
            $button_params_array['text'] = $params['button_text'];
        }

        return $button_params_array;
    }

    private function getImageSrc($params) {



        if (is_numeric($params['image'])) {
            $image_src = wp_get_attachment_url($params['image']);
        } else {
            $image_src = $params['image'];
        }

        return $image_src;

    }

    private function getSlideHolderStyle($params) {

        $slide_holder_style = array();

        $slide_holder_style[] = 'background-image: url(' . $params['image_src'] . ')';


        return implode(';', $slide_holder_style);
    }

}