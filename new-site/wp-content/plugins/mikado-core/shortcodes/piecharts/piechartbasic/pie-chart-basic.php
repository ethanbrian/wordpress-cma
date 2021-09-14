<?php
namespace Libero\Modules\PieCharts\PieChartBasic;

use Libero\Modules\Shortcodes\Lib\ShortcodeInterface;
/**
 * Class PieChartBasic
 */
class PieChartBasic implements ShortcodeInterface {

	/**
	 * @var string
	 */
	private $base;

	public function __construct() {
		$this->base = 'mkd_pie_chart';

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
			'name' => esc_html__( 'Mikado Pie Chart', 'mikado-core' ),
			'base' => $this->getBase(),
			'icon' => 'icon-wpb-pie-chart extended-custom-icon',
			'category' => esc_html__( 'by MIKADO', 'mikado-core' ),
			'allowed_container_element' => 'vc_row',
			'params' => array(
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Type of Central text', 'mikado-core' ),
					'param_name' => 'type_of_central_text',
					'value' => array(
						esc_html__('Percent', 'mikado-core' )  => 'percent',
						esc_html__('Title', 'mikado-core' ) => 'title'
					),
					'save_always' => true,
					'admin_label' => true
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Percentage', 'mikado-core' ),
					'param_name' => 'percent',
					'description' => '',
					'admin_label' => true,
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Title', 'mikado-core' ),
					'param_name' => 'title',
					'description' => '',
					'admin_label' => true
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
					'description' => '',
					'admin_label' => true
				),
                array(
                    'type' => 'textfield',
                    'heading' => esc_html__( 'Size(px)', 'mikado-core' ),
                    'param_name' => 'size',
                    'description' => '',
                    'admin_label' => true,
                    'group' => esc_html__( 'Design Options', 'mikado-core' ),
                ),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Margin below chart (px)', 'mikado-core' ),
					'param_name' => 'margin_below_chart',
					'description' => '',
					'group' => esc_html__( 'Design Options', 'mikado-core' ),
				),
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
			'size' => '',
			'type_of_central_text' => 'percent',
			'title' => '',
			'title_tag' => 'h3',
			'percent' => '',
			'text' => '',
			'margin_below_chart' => ''
		);

		$params = shortcode_atts($args, $atts);

		$params['title_tag'] = $this->getValidTitleTag($params, $args);
		$params['pie_chart_data'] = $this->getPieChartData($params);
		$params['pie_chart_style'] = $this->getPieChartStyle($params);
		$params['pie_chart_text_style'] = $this->getPieChartTextStyle($params);

		$html = mkd_core_get_core_shortcode_template_part('templates/pie-chart-basic', 'piecharts/piechartbasic', '', $params);

		return $html;


	}

	/**
	 * Return correct heading value. If provided heading isn't valid get the default one
	 *
	 * @param $params
	 * @param $args
	 */
	private function getValidTitleTag($params, $args) {

		$headings_array = array('h2', 'h3', 'h4', 'h5', 'h6');
		return (in_array($params['title_tag'], $headings_array)) ? $params['title_tag'] : $args['title_tag'];

	}

	/**
	 * Return data attributes for Pie Chart
	 *
	 * @param $params
	 * @return array
	 */
	private function getPieChartData($params) {

		$pieChartData = array();

		if( $params['size'] !== '' ) {
			$pieChartData['data-size'] = $params['size'];
		}
		if( $params['percent'] !== '' ) {
			$pieChartData['data-percent'] = $params['percent'];
		}

		return $pieChartData;

	}

	/**
	 * Return style for Pie Chart
	 *
	 * @param $params
	 * @return array
	 */
	private function getPieChartStyle($params) {

		$pieChartStyle = array();

		if( $params['size'] !== '' ) {
			$size = libero_mikado_filter_px($params['size']).'px';
			$pieChartStyle[] = 'height: '. $size;
			$pieChartStyle[] = 'line-height: '. $size;
			$pieChartStyle[] = 'width: '. $size;
		}

		return $pieChartStyle;

	}

	private function getPieChartTextStyle($params) {

		$pieChartTextStyle = array();

		if ($params['margin_below_chart'] !== '') {
			$pieChartTextStyle[] = 'margin-top: ' . $params['margin_below_chart'] . 'px';
		}

		return $pieChartTextStyle;

	}

}