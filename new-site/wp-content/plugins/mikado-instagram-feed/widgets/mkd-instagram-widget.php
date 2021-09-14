<?php
if(!defined('ABSPATH')) exit;

class MkdInstagramWidget extends WP_Widget {

	protected $params;

	public function __construct() {
		parent::__construct(
			'mkd_instagram_widget',
			esc_html__('Mikado Instagram Widget', 'mikado-instagram-feed' ),
			array( 'description' => esc_html__( 'Display instagram images', 'mikado-instagram-feed' ) )
		);

		$this->setParams();
	}

	protected function setParams() {
		$this->params = array(
			array(
				'name' => 'title',
				'type' => 'textfield',
				'title' => esc_html__('Title', 'mikado-instagram-feed' )
			),
			array(
				'name' => 'number_of_photos',
				'type' => 'textfield',
				'title' => esc_html__('Number of photos', 'mikado-instagram-feed' )
			),
			array(
				'name' => 'number_of_cols',
				'type' => 'dropdown',
				'title' => esc_html__('Number of columns', 'mikado-instagram-feed' ),
				'options' => array(
					'2' => esc_html__('Two', 'mikado-instagram-feed' ),
					'3' => esc_html__('Three', 'mikado-instagram-feed' ),
					'4' => esc_html__('Four', 'mikado-instagram-feed' ),
					'6' => esc_html__('Six', 'mikado-instagram-feed' ),
					'9' => esc_html__('Nine', 'mikado-instagram-feed' ),
				)
			),
			array(
				'name' => 'image_size',
				'type' => 'dropdown',
				'title' => esc_html__('Image Size', 'mikado-instagram-feed' ),
				'options' => array(
					'thumbnail' => esc_html__('Small', 'mikado-instagram-feed' ),
					'low_resolution' => esc_html__('Medium', 'mikado-instagram-feed' ),
					'standard_resolution' => esc_html__('Large', 'mikado-instagram-feed' )
				)
			),
			array(
				'name' => 'transient_time',
				'type' => 'textfield',
				'title' => esc_html__('Images Cache Time', 'mikado-instagram-feed' )
			),
		);
	}
	public function getParams() {
		return $this->params;
	}

	public function widget($args, $instance) {
		extract($instance);

		echo $args['before_widget'];
		if(!empty($title)) {
			echo $args['before_title'].$title.$args['after_title'];
		}

		$instagram_api = MkdInstagramApi::getInstance();
		$images_array = $instagram_api->getImages($number_of_photos, $tag, array(
			'use_transients' => true,
			'transient_name' => $args['widget_id'],
			'transient_time' => $transient_time
		));

		$number_of_cols = $number_of_cols == '' ? 3 : $number_of_cols;

		if(is_array($images_array) && count($images_array)) { ?>
			<ul class="mkd-instagram-feed clearfix mkd-col-<?php echo $number_of_cols; ?>">
				<?php
				foreach ($images_array as $image) { ?>
					<li>
						<a href="<?php echo esc_url($instagram_api->getHelper()->getImageLink($image)); ?>" target="_blank">
							<?php echo libero_mikado_kses_img($instagram_api->getHelper()->getImageHTML($image, $image_size)); ?>
						</a>
					</li>
				<?php } ?>
			</ul>
		<?php }

		echo $args['after_widget'];
	}

	public function form($instance) {
		foreach ($this->params as $param_array) {
			$param_name = $param_array['name'];
			${$param_name} = isset( $instance[$param_name] ) ? esc_attr( $instance[$param_name] ) : '';
		}

		$instagram_api = MkdInstagramApi::getInstance();

		//user has connected with instagram. Show form
		if($instagram_api->hasUserConnected()) {
			foreach ($this->params as $param) {
				switch($param['type']) {
					case 'textfield':
						?>
						<p>
							<label for="<?php echo esc_attr($this->get_field_id( $param['name'] )); ?>"><?php echo
								esc_html($param['title']); ?></label>
							<input class="widefat" id="<?php echo esc_attr($this->get_field_id( $param['name'] )); ?>" name="<?php echo esc_attr($this->get_field_name( $param['name'] )); ?>" type="text" value="<?php echo esc_attr( ${$param['name']} ); ?>" />
						</p>
						<?php
						break;
					case 'dropdown':
						?>
						<p>
							<label for="<?php echo esc_attr($this->get_field_id( $param['name'] )); ?>"><?php echo
								esc_html($param['title']); ?></label>
							<?php if(isset($param['options']) && is_array($param['options']) && count($param['options'])) { ?>
								<select class="widefat" name="<?php echo esc_attr($this->get_field_name( $param['name'] )); ?>" id="<?php echo esc_attr($this->get_field_id( $param['name'] )); ?>">
									<?php foreach ($param['options'] as $param_option_key => $param_option_val) {
										$option_selected = '';
										if(${$param['name']} == $param_option_key) {
											$option_selected = 'selected';
										}
										?>
										<option <?php echo esc_attr($option_selected); ?> value="<?php echo esc_attr($param_option_key); ?>"><?php echo esc_attr($param_option_val); ?></option>
									<?php } ?>
								</select>
							<?php } ?>
						</p>

						<?php
						break;
				}
			}
		}
	}
}

function mkd_instagram_widget_load(){
	register_widget('MkdInstagramWidget');
}

add_action('widgets_init', 'mkd_instagram_widget_load');