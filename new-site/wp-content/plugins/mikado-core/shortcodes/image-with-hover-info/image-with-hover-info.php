<?php
namespace Libero\Modules\ImageWithHoverInfo;

use Libero\Modules\Shortcodes\Lib\ShortcodeInterface;
/**
 * Class ImageWithHoverInfo
 */
class ImageWithHoverInfo implements ShortcodeInterface {

	/**
	 * @var string
	 */
	private $base;

	public function __construct() {
		$this->base = 'mkd_image_with_hover_info';

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
				'name' => esc_html__( 'Mikado Image With Hover Info', 'mikado-core' ),
				'base' => $this->getBase(),
				'category' => esc_html__( 'by MIKADO', 'mikado-core' ),
				'icon' => 'icon-wpb-image-with-text-hover extended-custom-icon',
				'allowed_container_element' => 'vc_row',
				'params' => array(
					array(
						'type'       => 'attach_image',
						'heading' => esc_html__( 'Image', 'mikado-core' ),
						'param_name' => 'image'
					),
					array(
						"type" => "textarea_html",
						"heading" => esc_html__( "Hover Info", 'mikado-core' ),
						"param_name" => "content",
						"description" => esc_html__( "Add content to be displayed on hover. You can use shortcodes.", 'mikado-core' )
					),
					array(
						"type" => "textfield",
						"heading" => esc_html__( "Link", 'mikado-core' ),
						"param_name" => "link",
						"save_always" => true,
						"description" => esc_html__( "Add a URL to link to.", 'mikado-core' )
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
			'image' => '',
			'hover_info' => '',
			'link' => 'http://'
		);

		$params = shortcode_atts($args, $atts);

		$params['content'] = $content;

		//Get HTML from template
		$html = mkd_core_get_core_shortcode_template_part('templates/image-with-hover-info-template', 'image-with-hover-info', '', $params);

		return $html;

	}

}