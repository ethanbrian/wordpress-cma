<?php
namespace Libero\Modules\Counter;

use Libero\Modules\Shortcodes\Lib\ShortcodeInterface;
/**
 * Class Countdown
 */
class Countdown implements ShortcodeInterface {

	/**
	 * @var string
	 */
	private $base;

	public function __construct() {
		$this->base = 'mkd_countdown';

		add_action('vc_before_init', array($this, 'vcMap'));
	}

	/**
	 * Returns base for shortcode
	 * @return string
	 */
	public function getBase()
	{
		return $this->base;
	}

	/**
	 * Maps shortcode to Visual Composer. Hooked on vc_before_init
	 *
	 * @see mkd_core_get_carousel_slider_array_vc()
	 */
	public function vcMap() {

		vc_map( array(
			'name' => esc_html__( 'Mikado Countdown', 'mikado-core' ),
			'base' => $this->getBase(),
			'category' => esc_html__( 'by MIKADO', 'mikado-core' ),
			'admin_enqueue_css' => array(libero_mikado_get_skin_uri().'/assets/css/mkd-vc-extend.css'),
			'icon' => 'icon-wpb-countdown extended-custom-icon',
			'allowed_container_element' => 'vc_row',
			'params' => array(
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Year', 'mikado-core' ),
					'param_name' => 'year',
					'value' => array(
						'' => '',
						'2015' => '2015',
						'2016' => '2016',
						'2017' => '2017',
						'2018' => '2018',
						'2019' => '2019',
						'2020' => '2020'
					),
					'admin_label' => true,
					'save_always' => true
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Month', 'mikado-core' ),
					'param_name' => 'month',
					'value' => array(
						'' => '',
						esc_html__('January', 'mikado-core' ) => '1',
						esc_html__('February', 'mikado-core' ) => '2',
						esc_html__('March', 'mikado-core' ) => '3',
						esc_html__('April', 'mikado-core' ) => '4',
						esc_html__('May', 'mikado-core' ) => '5',
						esc_html__('June', 'mikado-core' ) => '6',
						esc_html__('July', 'mikado-core' ) => '7',
						esc_html__('August', 'mikado-core' ) => '8',
						esc_html__('September', 'mikado-core' ) => '9',
						esc_html__('October', 'mikado-core' ) => '10',
						esc_html__('November', 'mikado-core' ) => '11',
						esc_html__('December', 'mikado-core' ) => '12'
					),
					'admin_label' => true,
					'save_always' => true
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Day', 'mikado-core' ),
					'param_name' => 'day',
					'value' => array(
						'' => '',
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
						'7' => '7',
						'8' => '8',
						'9' => '9',
						'10' => '10',
						'11' => '11',
						'12' => '12',
						'13' => '13',
						'14' => '14',
						'15' => '15',
						'16' => '16',
						'17' => '17',
						'18' => '18',
						'19' => '19',
						'20' => '20',
						'21' => '21',
						'22' => '22',
						'23' => '23',
						'24' => '24',
						'25' => '25',
						'26' => '26',
						'27' => '27',
						'28' => '28',
						'29' => '29',
						'30' => '30',
						'31' => '31',
					),
					'admin_label' => true,
					'save_always' => true
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Hour', 'mikado-core' ),
					'param_name' => 'hour',
					'value' => array(
						'' => '',
						'0' => '0',
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
						'7' => '7',
						'8' => '8',
						'9' => '9',
						'10' => '10',
						'11' => '11',
						'12' => '12',
						'13' => '13',
						'14' => '14',
						'15' => '15',
						'16' => '16',
						'17' => '17',
						'18' => '18',
						'19' => '19',
						'20' => '20',
						'21' => '21',
						'22' => '22',
						'23' => '23',
						'24' => '24'
					),
					'admin_label' => true,
					'save_always' => true
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Minute', 'mikado-core' ),
					'param_name' => 'minute',
					'value' => array(
						'' => '',
						'0' => '0',
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
						'7' => '7',
						'8' => '8',
						'9' => '9',
						'10' => '10',
						'11' => '11',
						'12' => '12',
						'13' => '13',
						'14' => '14',
						'15' => '15',
						'16' => '16',
						'17' => '17',
						'18' => '18',
						'19' => '19',
						'20' => '20',
						'21' => '21',
						'22' => '22',
						'23' => '23',
						'24' => '24',
						'25' => '25',
						'26' => '26',
						'27' => '27',
						'28' => '28',
						'29' => '29',
						'30' => '30',
						'31' => '31',
						'32' => '32',
						'33' => '33',
						'34' => '34',
						'35' => '35',
						'36' => '36',
						'37' => '37',
						'38' => '38',
						'39' => '39',
						'40' => '40',
						'41' => '41',
						'42' => '42',
						'43' => '43',
						'44' => '44',
						'45' => '45',
						'46' => '46',
						'47' => '47',
						'48' => '48',
						'49' => '49',
						'50' => '50',
						'51' => '51',
						'52' => '52',
						'53' => '53',
						'54' => '54',
						'55' => '55',
						'56' => '56',
						'57' => '57',
						'58' => '58',
						'59' => '59',
						'60' => '60',
					),
					'admin_label' => true,
					'save_always' => true
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Month Label', 'mikado-core' ),
					'param_name' => 'month_label',
					'description' => ''
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Month Single Label', 'mikado-core' ),
					'param_name' => 'month_slabel',
					'description' => ''
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Day Label', 'mikado-core' ),
					'param_name' => 'day_label',
					'description' => ''
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Day Single Label', 'mikado-core' ),
					'param_name' => 'day_slabel',
					'description' => ''
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Hour Label', 'mikado-core' ),
					'param_name' => 'hour_label',
					'description' => ''
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Hour Single Label', 'mikado-core' ),
					'param_name' => 'hour_slabel',
					'description' => ''
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Minute Label', 'mikado-core' ),
					'param_name' => 'minute_label',
					'description' => ''
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Minute Single Label', 'mikado-core' ),
					'param_name' => 'minute_slabel',
					'description' => ''
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Second Label', 'mikado-core' ),
					'param_name' => 'second_label',
					'description' => ''
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Second Single Label', 'mikado-core' ),
					'param_name' => 'second_slabel',
					'description' => ''
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Digit Font Size (px)', 'mikado-core' ),
					'param_name' => 'digit_font_size',
					'description' => '',
					'group' => esc_html__( 'Design Options', 'mikado-core' )
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Label Font Size (px)', 'mikado-core' ),
					'param_name' => 'label_font_size',
					'description' => '',
					'group' => esc_html__( 'Design Options', 'mikado-core' )
				),
				array(
					'type' => 'colorpicker',
					'heading' => esc_html__( 'Digit & Label Color', 'mikado-core' ),
					'param_name' => 'color',
					'description' => '',
					'group' => esc_html__( 'Design Options', 'mikado-core' )
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
	public function render($atts, $content = null)
	{

		$args = array(
			'year' => '',
			'month' => '', 
			'day' => '',
			'hour' => '',
			'minute' => '',
			'month_label' => 'Months',
			'day_label' => 'Days',
			'hour_label' => 'Hours',
			'minute_label' => 'Minutes',
			'second_label' => 'Seconds',
			'month_slabel' => 'Month',
			'day_slabel' => 'Day',
			'hour_slabel' => 'Hour',
			'minute_slabel' => 'Minute',
			'second_slabel' => 'Second',
			'digit_font_size' => '',
			'label_font_size' => '',
			'color' => ''
		);

		$params = shortcode_atts($args, $atts);

		$params['id'] = mt_rand(1000, 9999);
		$params['countdown_style'] = $this->getCountdownStyle($params);

		//Get HTML from template
		$html = mkd_core_get_core_shortcode_template_part('templates/countdown-template', 'countdown', '', $params);

		return $html;
	}

	/**
	* Renders Countdown General Style
	* @param $params
	* @return string
	*
	*/

	private function getCountdownStyle($params) {
		$countdownStyle = array();

		if ($params['color'] !== '') {

			$countdownStyle[] = 'color: ' . $params['color'];

		}

		return implode(';', $countdownStyle);
	}
}