<?php
if(!defined('ABSPATH')) exit;

class MkdTwitterWidget extends WP_Widget {
    private $params;

    public function __construct() {
        parent::__construct(
            'mkd_twitter_widget',
            esc_html__('Mikado Twitter Widget', 'mikado-twitter-feed' ),
            array( 'description' => esc_html__( 'Display tweets', 'mikado-twitter-feed' ) )
        );

        $this->setParams();
    }

    private function setParams() {
        $this->params = array(
            array(
                'name' => 'title',
                'type' => 'textfield',
                'title' => esc_html__('Title', 'mikado-twitter-feed' )
            ),
            array(
                'name' => 'user_id',
                'type' => 'textfield',
                'title' => esc_html__('User ID', 'mikado-twitter-feed' )
            ),
            array(
                'name' => 'count',
                'type' => 'textfield',
                'title' => esc_html__('Number of tweets', 'mikado-twitter-feed' )
            ),
            array(
                'name' => 'show_tweet_time',
                'type' => 'dropdown',
                'title' => esc_html__('Show tweet time', 'mikado-twitter-feed' ),
                'options' => array(
                    'no' => esc_html__('No', 'mikado-twitter-feed' ),
                    'yes' => esc_html__('Yes', 'mikado-twitter-feed' )
                )
            ),
            array(
                'name' => 'transient_time',
                'type' => 'textfield',
                'title' => esc_html__('Tweets Cache Time', 'mikado-twitter-feed' )
            )
        );
    }

    public function form($instance) {
        foreach ($this->params as $param_array) {
            $param_name = $param_array['name'];
            ${$param_name} = isset( $instance[$param_name] ) ? esc_attr( $instance[$param_name] ) : '';
        }

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

    public function update($new_instance, $old_instance) {
        $instance = array();
        foreach ($this->params as $param) {
            $param_name = $param['name'];

            $instance[$param_name] = sanitize_text_field($new_instance[$param_name]);
        }

        return $instance;
    }

    public function widget($args, $instance) {
        extract($instance);

        print $args['before_widget'];

        if(!empty($title)) {
            print $args['before_title'].$title.$args['after_title'];
        }

        $user_id = !empty($user_id) ? $user_id : '';
        $count = !empty($count) ? $count : '';
        $transient_time = !empty($transient_time) ? $transient_time : 0;

        $twitter_api = MkdTwitterApi::getInstance();

        if($twitter_api->hasUserConnected()) {
            $response = $twitter_api->fetchTweets($user_id, $count, array(
                'transient_time' => $transient_time,
                'transient_id' => 'mkd_twitter_'.$args['widget_id']
            ));

            if($response->status) {
                if(is_array($response->data) && count($response->data)) { ?>
                    <ul class="mkd_twitter_widget">
                    <?php foreach($response->data as $tweet) { ?>
                        <li>
                            <div class="mkd_tweet_text">
                                <?php echo wp_kses_post($twitter_api->getHelper()->getTweetText($tweet)); ?>
                            </div>
                            <?php if($show_tweet_time == 'yes') { ?>
                                <div class="mkd_tweet_time">

                                    <a target="_blank" href="<?php echo esc_url($twitter_api->getHelper()->getTweetURL($tweet)); ?>">
                                        <?php echo wp_kses_post($twitter_api->getHelper()->getTweetTime($tweet)); ?>
                                    </a>
                                </div>
                            <?php } ?>
                        </li>
                    <?php } ?>
                    </ul>
                <?php }
            } else {
                echo esc_html($response->message);
            }
        } else {
            esc_html_e('It seams that you haven\'t connected with your Twitter account', 'mikado-twitter-feed');
        }

        print $args['after_widget'];
    }
}

function mkd_twitter_widget_load(){
    register_widget('MkdTwitterWidget');
}

add_action('widgets_init', 'mkd_twitter_widget_load');