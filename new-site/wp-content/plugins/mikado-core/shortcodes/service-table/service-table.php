<?php
namespace Libero\Modules\Shortcodes\ServiceTable;

use Libero\Modules\Shortcodes\Lib\ShortcodeInterface;


/**
 * Class ServiceTable that represents service table shortcode
 * @package Libero\Modules\Shortcodes\ServiceTable
 */
class ServiceTable implements ShortcodeInterface {
	/**
	 * @var string
	 */
	private $base;

	/**
	 * Sets base attribute and registers shortcode with Visual Composer
	 */
	public function __construct() {
		$this->base = 'mkd_service_table';

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
			'name' => esc_html__( 'Service Table', 'mikado-core' ),
			'base'                      => $this->base,
			'category' => esc_html__( 'by MIKADO', 'mikado-core' ),
			'icon'                      => 'icon-wpb-service-table extended-custom-icon',
			'allowed_container_element' => 'vc_row',
			'params'                    => array_merge(
				libero_mikado_icon_collections()->getVCParamsArray(array(), '', true),
				array(
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Title', 'mikado-core' ),
						'param_name' => 'title',
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Subtitle', 'mikado-core' ),
						'param_name' => 'subtitle',
					),
					array(
						'type' => 'textarea_html',
						'holder' => 'div',
						'class' => '',
						'heading' => esc_html__( 'Content', 'mikado-core' ),
						'param_name' => 'content',
						'value' => '<li>content content content</li><li>content content content</li><li>content content content</li>',
						'description' => ''
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Link/Bottom Text', 'mikado-core' ),
						'param_name' => 'link_text',
						'value' => esc_html__('More details', 'mikado-core' ),
						'save_always' => true,
						'description' => esc_html__( 'Enter text to be displayed in bottom of service table (can be linked if chosen below)', 'mikado-core' )
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Link', 'mikado-core' ),
						'param_name' => 'link',
					),
					array(
						'type' => 'dropdown',
						'heading' => esc_html__( 'Link Target', 'mikado-core' ),
						'param_name' => 'link_target',
						'value' => array(
							esc_html__('Self', 'mikado-core' ) => '_self',
							esc_html__('Blank', 'mikado-core' ) => '_blank'
						),
						'save_always' => true,
						'dependency' => array('element' => 'link', 'not_empty' => true)
					)
				)
			) //close array_merge
		));
	}

	/**
	 * Renders HTML for service table shortcode
	 *
	 * @param array $atts
	 * @param null $content
	 *
	 * @return string
	 */
	public function render($atts, $content = null) {
		$default_atts = array(
			'title' => '',
			'subtitle' => '',
			'link' => '',
			'link_text' => '',
			'link_target' => ''
		);

		$default_atts = array_merge($default_atts, libero_mikado_icon_collections()->getShortcodeParams());
		$params       = shortcode_atts($default_atts, $atts);

		$params['content']= $content;
		$params['icon_params'] = $this->getIconParams($params);
		if (is_array($params['icon_params']) && count($params['icon_params'])){
			$params['show_icon'] = true;
			$params['service_class'] = 'mkd-service-has-icon';
		}
		else{
			$params['show_icon'] = false;
			$params['service_class'] = '';
		}

		return mkd_core_get_core_shortcode_template_part('templates/service-table-template', 'service-table', '', $params);
	}

	/**
	 * Returns array of icon parameters
	 *
	 * @param $params
	 *
	 * @return array
	 */
	private function getIconParams($params) {
		$icon_params = array();

		$icon_pack_name = libero_mikado_icon_collections()->getIconCollectionParamNameByKey($params['icon_pack']);

		$icon_params['icon_pack']   = $params['icon_pack'];
		$icon_params[$icon_pack_name] = $icon_pack_name !== false ? $params[$icon_pack_name] : '';

		$icon_params['type'] = 'circle';

		if ($icon_params[$icon_pack_name] == ''){
			$icon_params = array();
		}

		return $icon_params;
	}
}