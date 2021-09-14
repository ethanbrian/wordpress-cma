<?php
namespace MkdCore\CPT\Portfolio\Shortcodes;

use MkdCore\Lib;

/**
 * Class PortfolioSlider
 * @package MkdCore\CPT\Portfolio\Shortcodes
 */
class PortfolioSlider implements Lib\ShortcodeInterface {
    /**
     * @var string
     */
    private $base;

    public function __construct() {
        $this->base = 'mkd_portfolio_slider';

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
     * Maps shortcode to Visual Composer
     *
     * @see vc_map()
     */
    public function vcMap() {
        if(function_exists('vc_map')) {
            vc_map( array(
                'name' => esc_html__( 'Portfolio Slider', 'mikado-core' ),
                'base' => $this->base,
                'category' => esc_html__( 'by MIKADO', 'mikado-core' ),
                'icon' => 'icon-wpb-portfolio-slider extended-custom-icon',
                'allowed_container_element' => 'vc_row',
                'params' => array(
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__( 'Portfolio Slider Template', 'mikado-core' ),
                        'param_name' => 'type',
                        'value' => array(
	                        esc_html__('Standard', 'mikado-core' ) => 'standard',
	                        esc_html__('Gallery', 'mikado-core' ) => 'gallery'
                        ),
						'save_always' => true,
						'admin_label' => true,
                        'description' => '',
                    ),
                    array(
                        'type' => 'dropdown',
                        'admin_label' => true,
                        'heading' => esc_html__( 'Image size', 'mikado-core' ),
                        'param_name' => 'image_size',
                        'value' => array(
	                        esc_html__('Default', 'mikado-core' ) => '',
	                        esc_html__('Original Size', 'mikado-core' ) => 'full',
	                        esc_html__('Square', 'mikado-core' ) => 'square',
	                        esc_html__('Landscape', 'mikado-core' ) => 'landscape',
	                        esc_html__('Portrait', 'mikado-core' ) => 'portrait'
                        ),
                        'description' => ''
                    ),
                    array(
                        'type' => 'dropdown',
                        
                        'heading' => esc_html__( 'Order By', 'mikado-core' ),
                        'param_name' => 'order_by',
                        'value' => array(
                            '' => '',
                            esc_html__('Menu Order', 'mikado-core' ) => 'menu_order',
                            esc_html__('Title', 'mikado-core' ) => 'title',
                            esc_html__('Date', 'mikado-core' ) => 'date'
                        ),
						'admin_label' => true,
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
						'admin_label' => true,
                        'description' => ''
                    ),
                    array(
                        'type' => 'textfield',                        
                        'heading' => esc_html__( 'Number', 'mikado-core' ),
                        'param_name' => 'number',
                        'value' => '-1',
						'admin_label' => true,
                        'description' => esc_html__( 'Number of portolios on page (-1 is all)', 'mikado-core' )
                    ),
                    array(
                        'type' => 'dropdown',                        
                        'heading' => esc_html__( 'Number of Portfolios Shown', 'mikado-core' ),
                        'param_name' => 'portfolios_shown',
						'admin_label' => true,
                        'value' => array(
                            '' => '',
                            '3' => '3',
                            '4' => '4',
                            '5' => '5',
                            '6' => '6'
                        ),
                        'description' => esc_html__( 'Number of portfolios that are showing at the same time in full width (on smaller screens is responsive so there will be less items shown)', 'mikado-core' ),
                    ),
                    array(
                        'type' => 'textfield',
                        'heading' => esc_html__( 'Category', 'mikado-core' ),
                        'param_name' => 'category',
                        'value' => '',
						'admin_label' => true,
                        'description' => esc_html__( 'Category Slug (leave empty for all)', 'mikado-core' )
                    ),
                    array(
                        'type' => 'textfield',
                        'heading' => esc_html__( 'Selected Projects', 'mikado-core' ),
                        'param_name' => 'selected_projects',
                        'value' => '',
						'admin_label' => true,
                        'description' => esc_html__( 'Selected Projects (leave empty for all, delimit by comma)', 'mikado-core' )
                    ),
                    array(
                        'type' => 'dropdown',
                        'class' => '',
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
                    )
                )
            ) );
        }
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
            'type' => 'standard',
            'image_size' => 'full',
            'order_by' => 'date',
            'order' => 'ASC',
            'number' => '-1',
            'category' => '',
            'selected_projects' => '',
            'title_tag' => 'h4',
			'portfolios_shown' => '3',
			'portfolio_slider' => 'yes'
        );

        $args = array_merge($args, libero_mikado_icon_collections()->getShortcodeParams());
		$params = shortcode_atts($args, $atts);
		
		extract($params);
		
		$html ='';
		$html .= libero_mikado_execute_shortcode('mkd_portfolio_list', $params);
        return $html;
    }
	
	
}