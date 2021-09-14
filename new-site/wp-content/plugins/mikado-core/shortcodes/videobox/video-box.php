<?php
namespace Libero\Modules\VideoBox;

use Libero\Modules\Shortcodes\Lib\ShortcodeInterface;
/**
 * Class VideoBox
 */
class VideoBox implements ShortcodeInterface {

	/**
	 * @var string
	 */
	private $base;

	public function __construct() {
		$this->base = 'mkd_video_box';

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
				'name' => esc_html__( 'Mikado Video Box', 'mikado-core' ),
				'base' => $this->getBase(),
				'category' => esc_html__( 'by MIKADO', 'mikado-core' ),
				'icon' => 'icon-wpb-video-box extended-custom-icon',
				'allowed_container_element' => 'vc_row',
				'params' => array(
					array(
						"type" => "textfield",
						"heading" => esc_html__( "Video Link", 'mikado-core' ),
						"param_name" => "video_link"
					),
                    array(
                        "type" => "attach_image",
                        "heading" => esc_html__( "Image", 'mikado-core' ),
                        "param_name" => "video_image"
                    ),
                    array(
                        "type" => "textfield",
                        "heading" => esc_html__( "Title", 'mikado-core' ),
                        "param_name" => "title"
                    ),
					array(
						"type" => "textfield",
						"heading" => esc_html__( "Text", 'mikado-core' ),
						"param_name" => "text"
					),

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
			'video_link' => '#',
			'title' => '',
            'text' => '',
            'video_image' => ''

		);

		$params = shortcode_atts($args, $atts);

        $params['title_holder_classes'] = $this->getTitleHolderClasses($params);

		//Get HTML from template
		$html = mkd_core_get_core_shortcode_template_part('templates/video-box-template', 'videobox', '', $params);

		return $html;

	}

    /**
     * Return Video title holder classes
     *
     * @param $params
     * @return false|string
     */
    private function getTitleHolderClasses($params) {
    	$title_classes = '';

        if ($params['text'] !== '') {
            $title_classes .= 'mkd-video-has-desc';
        }

        return $title_classes;
    }

}