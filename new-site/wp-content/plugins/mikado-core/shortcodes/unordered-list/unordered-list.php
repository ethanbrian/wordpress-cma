<?php
namespace Libero\Modules\UnorderedList;

use Libero\Modules\Shortcodes\Lib\ShortcodeInterface;

/**
 * Class unordered List
 */
class UnorderedList implements ShortcodeInterface{

	private $base;

	function __construct() {
		$this->base='mkd_unordered_list';
		
		add_action('vc_before_init', array($this, 'vcMap'));
	}

	/**\
	 * Returns base for shortcode
	 * @return string
	 */
	public function getBase() {
		return $this->base;
	}

	public function vcMap() {

		vc_map( array(
			'name' => esc_html__( 'Mikado List - Unordered', 'mikado-core' ),
			'base' => $this->base,
			'icon' => 'icon-wpb-unordered-list extended-custom-icon',
			'category' => esc_html__( 'by MIKADO', 'mikado-core' ),
			'allowed_container_element' => 'vc_row',
			'params' => array(
				array(
					'type' => 'dropdown',
					'admin_label' => true,
					'heading' => esc_html__( 'Style', 'mikado-core' ),
					'param_name' => 'style',
					'value' => array(
						esc_html__('Circle', 'mikado-core' ) => 'circle',
						esc_html__('Line', 'mikado-core' )	 => 'line',
						esc_html__('Arrow', 'mikado-core' )  => 'arrow'
					),
					'description' => ''
				),
				array(
					'type' => 'dropdown',
					'admin_label' => true,
					'heading' => esc_html__( 'Animate List', 'mikado-core' ),
					'param_name' => 'animate',
					'value' => array(
						esc_html__('No', 'mikado-core' ) => 'no',
						esc_html__('Yes', 'mikado-core' ) => 'yes'
					),
					'description' => ''
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Font Weight', 'mikado-core' ),
					'param_name' => 'font_weight',
					'value' => array(
						esc_html__('Default', 'mikado-core' ) => '',
						esc_html__('Light', 'mikado-core' ) => 'light',
						esc_html__('Normal', 'mikado-core' ) => 'normal',
						esc_html__('Bold', 'mikado-core' ) => 'bold'
					),
					'description' => ''
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Padding left (px)', 'mikado-core' ),
					'param_name' => 'padding_left',
					'value' => ''
				),
				array(
					'type' => 'textarea_html',
					'heading' => esc_html__( 'Content', 'mikado-core' ),
					'param_name' => 'content',
					'value' => '<ul><li>Lorem Ipsum</li><li>Lorem Ipsum</li><li>Lorem Ipsum</li></ul>',
					'description' => ''
				)
			)
		) );

	}


	public function render($atts, $content = null) {
		$args = array(
            'style' => '',
            'animate' => '',
            'font_weight' => '',
            'padding_left' => ''
        );
		$params = shortcode_atts($args, $atts);
		
		//Extract params for use in method
		extract($params);
		
		$list_item_classes = "";

        if ($style != '') {
			$list_item_classes .= ' mkd-'.$style;
        }

		if ($animate == 'yes') {
			$list_item_classes .= ' mkd-animate-list';
		}
		
		$list_style = '';
		if($padding_left != '') {
			$list_style .= 'padding-left: ' . $padding_left .'px;';
		}
		$html = '';
		
        $html .= '<div class="mkd-unordered-list '.$list_item_classes.'" '.  libero_mikado_get_inline_style($list_style).'>';
		$html .= libero_mikado_remove_wpautop($content, true);
		$html .= '</div>';
        return $html;
	}

}