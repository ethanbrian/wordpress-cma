<?php
namespace Libero\Modules\Shortcodes\ImageGallery;

use Libero\Modules\Shortcodes\Lib\ShortcodeInterface;

class ImageGallery implements ShortcodeInterface{

	private $base;

	/**
	 * Image Gallery constructor.
	 */
	public function __construct() {
		$this->base = 'mkd_image_gallery';

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
			'name' => esc_html__( 'Mikado Image Gallery', 'mikado-core' ),
			'base'                      => $this->getBase(),
			'category' => esc_html__( 'by MIKADO', 'mikado-core' ),
			'icon'                      => 'icon-wpb-image-gallery extended-custom-icon',
			'allowed_container_element' => 'vc_row',
			'params'                    => array(
				array(
					'type'			=> 'attach_images',
					'heading' => esc_html__( 'Images', 'mikado-core' ),
					'param_name'	=> 'images',
					'description' => esc_html__( 'Select images from media library', 'mikado-core' )
				),
				array(
					'type'			=> 'textfield',
					'heading' => esc_html__( 'Image Size', 'mikado-core' ),
					'param_name'	=> 'image_size',
					'description' => esc_html__( 'Enter image size. Example: thumbnail, medium, large, full or other sizes defined by current theme. Alternatively enter image size in pixels: 200x100 (Width x Height). Leave empty to use thumbnail size', 'mikado-core' )
				),
				array(
					'type'        => 'dropdown',
					'heading' => esc_html__( 'Gallery Type', 'mikado-core' ),
					'admin_label' => true,
					'param_name'  => 'type',
					'value'       => array(
						esc_html__('Slider', 'mikado-core' )		=> 'slider',
						esc_html__('Image Grid', 'mikado-core' )	=> 'image_grid'
					),
					'description' => esc_html__( 'Select gallery type', 'mikado-core' ),
					'save_always' => true
				),
				array(
					'type'			=> 'dropdown',
					'heading' => esc_html__( 'Slide duration', 'mikado-core' ),
					'admin_label'	=> true,
					'param_name'	=> 'autoplay',
					'value'			=> array(
						'3'			=> '3',
						'5'			=> '5',
						'10'		=> '10',
						esc_html__('Disable', 'mikado-core' )	=> 'disable'
					),
					'save_always'	=> true,
					'dependency'	=> array(
						'element'	=> 'type',
						'value'		=> array(
							'slider'
						)
					),
					'description' => esc_html__( 'Auto rotate slides each X seconds', 'mikado-core' ),
				),
				array(
					'type'			=> 'dropdown',
					'heading' => esc_html__( 'Slide Animation', 'mikado-core' ),
					'admin_label'	=> true,
					'param_name'	=> 'slide_animation',
					'value'			=> array(
						esc_html__('Slide', 'mikado-core' )		=> 'slide',
						esc_html__('Fade', 'mikado-core' )		=> 'fade',
						esc_html__('Fade Up', 'mikado-core' )	=> 'fadeUp',
						esc_html__('Back Slide', 'mikado-core' )=> 'backSlide',
						esc_html__('Go Down', 'mikado-core' )	=> 'goDown'
					),
					'save_always'	=> true,
					'dependency'	=> array(
						'element'	=> 'type',
						'value'		=> array(
							'slider'
						)
					)
				),
				array(
					'type'			=> 'dropdown',
					'heading' => esc_html__( 'Column Number', 'mikado-core' ),
					'param_name'	=> 'column_number',
					'value'			=> array(2, 3, 4, 5),
					'save_always'	=> true,
					'dependency'	=> array(
						'element' 	=> 'type',
						'value'		=> array(
							'image_grid'
						)
					),
				),
				array(
					'type'			=> 'dropdown',
					'heading' => esc_html__( 'Space Between Images', 'mikado-core' ),
					'param_name'	=> 'space_between',
					'value'			=> array(
						esc_html__('Yes', 'mikado-core' ) => 'yes',
						esc_html__('No', 'mikado-core' ) => 'no'
					),
					'save_always'	=> true,
					'dependency'	=> array(
						'element' 	=> 'type',
						'value'		=> array(
							'image_grid'
						)
					),
				),
				array(
					'type'			=> 'dropdown',
					'heading' => esc_html__( 'Open PrettyPhoto on click', 'mikado-core' ),
					'param_name'	=> 'pretty_photo',
					'value'			=> array(
						esc_html__('No', 'mikado-core' )		=> 'no',
						esc_html__('Yes', 'mikado-core' )		=> 'yes'
					),
					'save_always'	=> true,
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Grayscale Images', 'mikado-core' ),
					'param_name' => 'grayscale',
					'value' => array(
						esc_html__('No', 'mikado-core' ) => 'no',
						esc_html__('Yes', 'mikado-core' ) => 'yes'
					),
					'save_always'	=> true,
					'dependency'	=> array(
						'element'	=> 'type',
						'value'		=> array(
							'image_grid'
						)
					)
				),
				array(
					'type'			=> 'dropdown',
					'heading' => esc_html__( 'Show Navigation Arrows', 'mikado-core' ),
					'param_name' 	=> 'navigation',
					'value' 		=> array(
						esc_html__('Yes', 'mikado-core' )		=> 'yes',
						esc_html__('No', 'mikado-core' )		=> 'no'
					),
					'dependency' 	=> array(
						'element' => 'type',
						'value' => array(
							'slider'
						)
					),
					'save_always'	=> true
				),
				array(
					'type'			=> 'dropdown',
					'heading' => esc_html__( 'Show Pagination', 'mikado-core' ),
					'param_name' 	=> 'pagination',
					'value' 		=> array(
						esc_html__('Yes', 'mikado-core' ) 		=> 'yes',
						esc_html__('No', 'mikado-core' )		=> 'no'
					),
					'save_always'	=> true,
					'dependency'	=> array(
						'element' => 'type',
						'value' => array(
							'slider'
						)
					)
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
			'images'			=> '',
			'image_size'		=> 'thumbnail',
			'type'				=> 'slider',
			'autoplay'			=> '5000',
			'slide_animation'	=> 'slide',
			'pretty_photo'		=> '',
			'column_number'		=> '',
			'space_between'		=> 'yes',
			'grayscale'			=> '',
			'navigation'		=> 'yes',
			'pagination'		=> 'yes'
		);

		$params = shortcode_atts($args, $atts);
		$params['slider_data'] = $this->getSliderData($params);
		$params['image_size'] = $this->getImageSize($params['image_size']);
		$params['images'] = $this->getGalleryImages($params);
		$params['pretty_photo'] = ($params['pretty_photo'] == 'yes') ? true : false;
		$params['columns'] = 'mkd-gallery-columns-' . $params['column_number'];
		$params['gallery_classes'] = ($params['grayscale'] == 'yes') ? 'mkd-grayscale' : '';
		$params['gallery_classes'] .= ($params['space_between'] == 'no') ? ' mkd-image-no-space' : '';

		if ($params['type'] == 'image_grid') {
			$template = 'gallery-grid';
		} elseif ($params['type'] == 'slider') {
			$template = 'gallery-slider';
		}

		$html = mkd_core_get_core_shortcode_template_part('templates/' . $template, 'imagegallery', '', $params);

		return $html;

	}

	/**
	 * Return images for gallery
	 *
	 * @param $params
	 * @return array
	 */
	private function getGalleryImages($params) {

		$images = array();

		if ($params['images'] !== '') {

			$size = $params['image_size'];
			$image_ids = explode(',', $params['images']);

			foreach ($image_ids as $id) {

				$img = wp_get_attachment_image_src($id, $size);

				$image['url'] = $img[0];
				$image['width'] = $img[1];
				$image['height'] = $img[2];
				$image['title'] = get_the_title($id);

				$images[] = $image;

			}

		}

		return $images;

	}

	/**
	 * Return image size or custom image size array
	 *
	 * @param $image_size
	 * @return array
	 */
	private function getImageSize($image_size) {

		//Remove whitespaces
		$image_size = trim($image_size);
		//Find digits
		preg_match_all( '/\d+/', $image_size, $matches );
		if ( !empty($matches[0]) ) {
			return array(
				$matches[0][0],
				$matches[0][1]
			);
		} elseif ( in_array( $image_size, array('thumbnail', 'thumb', 'medium', 'large', 'full') )) {
			return $image_size;
		} else {
			return 'thumbnail';
		}

	}

	/**
	 * Return all configuration data for slider
	 *
	 * @param $params
	 * @return array
	 */
	private function getSliderData($params) {

		$slider_data = array();

		$slider_data['data-autoplay'] = ($params['autoplay'] !== '') ? $params['autoplay'] : '';
		$slider_data['data-animation'] = ($params['slide_animation'] !== '') ? $params['slide_animation'] : '';
		$slider_data['data-navigation'] = ($params['navigation'] !== '') ? $params['navigation'] : '';
		$slider_data['data-pagination'] = ($params['pagination'] !== '') ? $params['pagination'] : '';

		return $slider_data;

	}

}