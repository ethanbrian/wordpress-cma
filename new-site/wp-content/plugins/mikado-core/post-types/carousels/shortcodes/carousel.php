<?php
namespace MkdCore\CPT\Carousels\Shortcodes;

use MkdCore\Lib;

/**
 * Class Carousel
 * @package MkdCore\CPT\Carousels\Shortcodes
 */
class Carousel implements Lib\ShortcodeInterface {
    /**
     * @var string
     */
    private $base;

    public function __construct() {
        $this->base = 'mkd_carousel';

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
            'name' => esc_html__( 'Mikado Carousel', 'mikado-core' ),
            'base' => $this->base,
            'category' => esc_html__( 'by MIKADO', 'mikado-core' ),
            'icon' => 'icon-wpb-carousel-slider extended-custom-icon',
            'allowed_container_element' => 'vc_row',
            'params' => array(
                array(
                    'type' => 'dropdown',
                    'heading' => esc_html__( 'Carousel Slider', 'mikado-core' ),
                    'param_name' => 'carousel',
                    'value' => mkd_core_get_carousel_slider_array_vc(),
                    'description' => esc_html__( 'Note: For best visual appearance, please use sliders with at least 6 carousel items set.', 'mikado-core' ),
                    'admin_label' => true
                ),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Carousel Type', 'mikado-core' ),
					'param_name' => 'type',
					'value' => array(
						esc_html__('Default', 'mikado-core' ) => '',
						esc_html__('Carousel Row', 'mikado-core' ) => 'row',
						esc_html__('Carousel Grid', 'mikado-core' ) => 'grid'
					)
				),
                array(
                    'type' => 'dropdown',
                    'heading' => esc_html__( 'Order By', 'mikado-core' ),
                    'param_name' => 'orderby',
                    'value' => array(
                        '' => '',
                        esc_html__('Title', 'mikado-core' ) => 'title',
                        esc_html__('Date', 'mikado-core' ) => 'date'
                    ),
                    'description' => ''
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => esc_html__( 'Order', 'mikado-core' ),
                    'param_name' => 'order',
                    'value' => array(
                        '' => '',
                        esc_html__('ASC', 'mikado-core' ) => 'ASC',
                        esc_html__('DESC', 'mikado-core' ) => 'DESC',
                    ),
                    'description' => ''
                ),
                array(
                	'type' => 'dropdown',
                	'heading' => esc_html__( 'Number of Items', 'mikado-core' ),
                	'param_name' => 'no_of_items',
                	'value' => array(
		                esc_html__('Default', 'mikado-core' ) => '',
                		'4' => '4',
                		'5' => '5',
                		'6' => '6'
            		),
            		'save_always' => true,
            		'description' => esc_html__( 'Choose number of items to show initialy (number will change on smaller screens due to responsiveness)', 'mikado-core' ),
            		'dependency' => array('element' => 'type', 'value' => array('row'))
            	),
            	array(
            		'type' => 'textfield',
            		'heading' => esc_html__( 'Item Padding (px)', 'mikado-core' ),
            		'param_name' => 'padding',
        		),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Pagination position', 'mikado-core' ),
					'param_name' => 'pag_position',
					'value' => array(
						esc_html__('Default', 'mikado-core' ) => '',
						esc_html__('Left', 'mikado-core' ) => 'left',
						esc_html__('Right', 'mikado-core' ) => 'right'
					),
					'dependency'  => array('element' => 'type', 'value' => array('','grid'))
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Autoplay', 'mikado-core' ),
					'param_name' => 'autoplay',
					'value' => array(
						esc_html__('Yes', 'mikado-core' ) => 'yes',
						esc_html__('No', 'mikado-core' ) => 'no'
					),
					'save_always' => true
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
            'carousel' => '',
            'type' => 'grid',
            'orderby' => 'date',
            'order' => 'ASC',
            'image_animation' => 'image-change',
            'no_of_items' => '',
            'padding' => '',
            'pag_position' => 'left',
            'autoplay' => ''
        );

        $params = shortcode_atts($args, $atts);

		extract($params);
		$carousel_classes = $this->getCarouselClasses($params);
		$carousel_data = $this->getCarouselData($params);

        $html = '';

        if ($carousel !== '') {

            $html .= '<div class="mkd-carousel-holder clearfix">';
            $html .= '<div class="mkd-carousel '.$carousel_classes.'" '.$carousel_data.'>';

            $args = array(
                'post_type' => 'carousels',
                'carousels_category' => $params['carousel'],
                'orderby' => $params['orderby'],
                'order' => $params['order'],
                'posts_per_page' => '-1'
            );

            $query = new \WP_Query($args);

            if ($query->have_posts()) {
                while($query->have_posts()) {
                    $query->the_post();
                    $carousel_item = $this->getCarouselItemData(get_the_ID(), $params);
                    $html .= mkd_core_get_shortcode_module_template_part('carousels', 'carousel-template', '', $carousel_item);
                }
            }

            wp_reset_postdata();


            $html .= '</div>';
            $html .= '</div>';

        }

        return $html;
    }

    /**
     * Return all data that carousel needs, images, titles, links, etc
     *
     * @param $params
     * @return array
     */
    private function getCarouselItemData($item_id, $params) {

        $carousel_item = array();

        if (get_post_meta($item_id, 'mkd_carousel_image', true) !== '') {
            $carousel_item['image'] = get_post_meta($item_id, 'mkd_carousel_image', true);
        } else {
            $carousel_item['image'] = '';
        }

        if ($params['image_animation'] == 'image-change' && get_post_meta($item_id, 'mkd_carousel_hover_image', true) !== '') {
            $carousel_item['hover_image'] = get_post_meta($item_id, 'mkd_carousel_hover_image', true);
            $carousel_item['hover_class'] = 'mkd-has-hover-image';
        }

        if (get_post_meta($item_id, 'mkd_carousel_item_link', true) != '') {
            $carousel_item['link'] = get_post_meta($item_id, 'mkd_carousel_item_link', true);
        } else {
            $carousel_item['link'] = '';
        }

        if (get_post_meta($item_id, 'mkd_carousel_item_target', true) != '') {
            $carousel_item['target'] = get_post_meta($item_id, 'mkd_carousel_item_target', true);
        } else {
            $carousel_item['target'] = '_self';
        }

        $carousel_item['title'] = get_the_title();

        $carousel_item['carousel_image_classes'] = $this->getCarouselImageClasses($params);

        $carousel_item['style'] = '';

        if ($params['padding'] !== ''){
        	$carousel_item['style'] .= 'padding: '.libero_mikado_filter_px($params['padding']).'px;';
        }

        return $carousel_item;

    }

	/**
	 * Return CSS classes for carousel image
	 *
	 * @param $params
	 * @return array
	 */
	private function getCarouselImageClasses($params) {

		$carousel_image_classes = array();
		if($params['image_animation'] !== '') {
			$carousel_image_classes[] = 'mkd-' . $params['image_animation'];
		}

		return implode(' ', $carousel_image_classes);

	}


	/**
	 * Return CSS classes for carousel
	 *
	 * @param $params
	 * @return array
	 */
	private function getCarouselClasses($params) {
		$carousel_classes = array();

		if($params['type'] !== '') {
			$carousel_classes[] =  'mkd-carousel-'.$params['type'];
		}

		if($params['pag_position'] !== '') {
			$carousel_classes[] =  'mkd-pag-'.$params['pag_position'];
		}

		return implode(' ', $carousel_classes);

	}

	/**
	 * Return data for carousel
	 *
	 * @param $params
	 * @return array
	 */
	private function getCarouselData($params) {
		$carousel_data = array();

		if($params['no_of_items'] !== '') {
			$carousel_data[] =  'data-items-shown='.$params['no_of_items'];
		}

		if($params['autoplay'] !== '') {
			$carousel_data[] =  'data-autoplay='.$params['autoplay'];
		}

		return implode(' ', $carousel_data);

	}

}