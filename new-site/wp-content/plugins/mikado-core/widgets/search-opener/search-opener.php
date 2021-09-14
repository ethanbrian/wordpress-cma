<?php

/**
 * Widget that adds search icon that triggers opening of search form
 *
 * Class Mkd_Search_Opener
 */
class LiberoSearchOpener extends LiberoWidget {
    /**
     * Set basic widget options and call parent class construct
     */
    public function __construct() {
        parent::__construct(
            'mkd_search_opener', // Base ID
            esc_html__('Mikado Search Opener', 'mikado-core' ) // Name
        );

        $this->setParams();
    }

    /**
     * Sets widget options
     */
    protected function setParams() {
        $this->params = array(
            array(
                'name'        => 'search_icon_size',
                'type'        => 'textfield',
                'title' => esc_html__( 'Search Icon Size (px)', 'mikado-core' ),
                'description' => esc_html__( 'Define size for Search icon', 'mikado-core' )
            ),
            array(
                'name'        => 'search_icon_color',
                'type'        => 'textfield',
                'title' => esc_html__( 'Search Icon Color', 'mikado-core' ),
                'description' => esc_html__( 'Define color for Search icon', 'mikado-core' )
            ),
            array(
                'name'        => 'search_icon_hover_color',
                'type'        => 'textfield',
                'title' => esc_html__( 'Search Icon Hover Color', 'mikado-core' ),
                'description' => esc_html__( 'Define hover color for Search icon', 'mikado-core' )
            ),
            array(
                'name'        => 'show_label',
                'type'        => 'dropdown',
                'title' => esc_html__( 'Enable Search Icon Text', 'mikado-core' ),
                'description' => esc_html__( 'Enable this option to show Search text bellow search icon in header', 'mikado-core' ),
                'options'     => array(
                    ''    => '',
                    'yes' => esc_html__('Yes', 'mikado-core' ),
                    'no'  => esc_html__('No', 'mikado-core' )
                )
            ),
			array(
				'name'			=> 'close_icon_position',
				'type'			=> 'dropdown',
				'title' => esc_html__( 'Close icon stays on opener place', 'mikado-core' ),
				'description' => esc_html__( 'Enable this option to set close icon on same position like opener icon', 'mikado-core' ),
				'options'		=> array(
					'yes'	=> esc_html__('Yes', 'mikado-core' ),
					'no'	=> esc_html__('No', 'mikado-core' )
				)
			)
        );
    }

    /**
     * Generates widget's HTML
     *
     * @param array $args args from widget area
     * @param array $instance widget's options
     */
    public function widget($args, $instance) {
        global $libero_mikado_options, $libero_mikado_IconCollections;

        $search_type_class    = 'mkd-search-opener';
		$fullscreen_search_overlay = false;
        $search_opener_styles = array();
        $show_search_text     = true;
        if (($instance['show_label'] == 'no') || ($instance['show_label'] == '' && $libero_mikado_options['enable_search_icon_text'] == 'no')){
        	$show_search_text = false;
        }
		$close_icon_on_same_position = $instance['close_icon_position'] == 'yes' ? true : false;

		if (isset($libero_mikado_options['search_type']) && $libero_mikado_options['search_type'] == 'fullscreen-search') {
			if (isset($libero_mikado_options['search_animation']) && $libero_mikado_options['search_animation'] == 'search-from-circle') {
				$fullscreen_search_overlay = true;
			}
		}

        if(isset($libero_mikado_options['search_type']) && $libero_mikado_options['search_type'] == 'search_covers_header') {
            if(isset($libero_mikado_options['search_cover_only_bottom_yesno']) && $libero_mikado_options['search_cover_only_bottom_yesno'] == 'yes') {
                $search_type_class .= ' search_covers_only_bottom';
            }
        }

        if(!empty($instance['search_icon_size'])) {
            $search_opener_styles[] = 'font-size: '.$instance['search_icon_size'].'px';
        }

        if(!empty($instance['search_icon_color'])) {
            $search_opener_styles[] = 'color: '.$instance['search_icon_color'];
        }

        ?>

        <a <?php echo libero_mikado_get_inline_attr($instance['search_icon_hover_color'], 'data-hover-color'); ?>
			<?php if ( $close_icon_on_same_position ) {
				echo libero_mikado_get_inline_attr('yes', 'data-icon-close-same-position');
			} ?>
            <?php libero_mikado_inline_style($search_opener_styles); ?>
            <?php libero_mikado_class_attribute($search_type_class); ?> href="javascript:void(0)">
            <span class="mkd-search-opener-holder">
	            <?php if(isset($libero_mikado_options['search_icon_pack'])) {
	                $libero_mikado_IconCollections->getSearchIcon($libero_mikado_options['search_icon_pack'], false);
	            } ?>
	            <?php if($show_search_text) { ?>
	                <span class="mkd-search-icon-text"><?php esc_html_e('Search', 'mikado-core'); ?></span>
	            <?php } ?>
	        </span>
        </a>
		<?php if($fullscreen_search_overlay) { ?>
			<div class="mkd-fullscreen-search-overlay"></div>
		<?php } ?>
    <?php }
}