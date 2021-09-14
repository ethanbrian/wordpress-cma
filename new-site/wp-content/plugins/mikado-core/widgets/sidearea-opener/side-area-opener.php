<?php

class LiberoSideAreaOpener extends LiberoWidget {
    public function __construct() {
        parent::__construct(
            'mkd_side_area_opener', // Base ID
            esc_html__('Mikado Side Area Opener', 'mikado-core' ) // Name
        );

        $this->setParams();
    }

    protected function setParams() {

		$this->params = array(
			array(
				'name'			=> 'side_area_opener_icon_color',
				'type'			=> 'textfield',
				'title' => esc_html__( 'Icon Color', 'mikado-core' ),
				'description' => esc_html__( 'Define color for Side Area opener icon', 'mikado-core' )
			),
            array(
                'name'        => 'show_label',
                'type'        => 'dropdown',
                'title' => esc_html__( 'Enable Side Area Icon Text', 'mikado-core' ),
                'description' => esc_html__( 'Enable this option to show Menu text bellow search icon in header', 'mikado-core' ),
                'options'     => array(
                    ''    => '',
                    'yes' => esc_html__('Yes', 'mikado-core' ),
                    'no'  => esc_html__('No', 'mikado-core' )
                )
            )
		);

    }


    public function widget($args, $instance) {
        global $libero_mikado_options;

		$sidearea_icon_styles = array();
        $show_side_area_text     = true;
        if (($instance['show_label'] == 'no') || ($instance['show_label'] == '' && $libero_mikado_options['enable_side_area_icon_text'] == 'no')){
            $show_side_area_text = false;
        }

		if ( !empty($instance['side_area_opener_icon_color']) ) {
			$sidearea_icon_styles[] = 'background-color: ' . $instance['side_area_opener_icon_color'];
		}
		
		?>
        <a class="mkd-side-menu-button-opener"  href="javascript:void(0)">
           	<span class="mkd-lines-holder">
				<span class="mkd-lines-holder-inner">
					<span class="mkd-lines line-1" <?php libero_mikado_inline_style($sidearea_icon_styles) ?>></span>
					<span class="mkd-lines line-2" <?php libero_mikado_inline_style($sidearea_icon_styles) ?>></span>
					<span class="mkd-lines line-3" <?php libero_mikado_inline_style($sidearea_icon_styles) ?>></span>
					<span class="mkd-lines line-4" <?php libero_mikado_inline_style($sidearea_icon_styles) ?>></span>
                    <span class="mkd-lines line-5" <?php libero_mikado_inline_style($sidearea_icon_styles) ?>></span>
				</span>
           	</span>
            <?php if($show_side_area_text) { ?>
                <span class="mkd-side-area-icon-text"><?php esc_html_e('Menu', 'mikado-core'); ?></span>
            <?php } ?>
        </a>

    <?php }

}