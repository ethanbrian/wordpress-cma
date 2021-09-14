<?php
/**
 * Custom Css In Page
 *
 * Add custom css any where and render it on footer (wp-footer)
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

if (!class_exists('G5Plus_Custom_Css')) {
	class G5Plus_Custom_Css
	{
		/*
		 * instance framework
		 */
		public static $instance;

		private $_custom_css = array();

		/**
		 * Init G5Plus_Custom_Css
		 *
		 * @return G5Plus_Custom_Css
		 */
		public static function init()
		{
			if (self::$instance == NULL) {
				self::$instance = new self();
				self::$instance->afterInit();
			}

			return self::$instance;
		}

		/**
		 * Plugin construct
		 */
		public function afterInit()
		{
            add_action('wp_head', array($this, 'init_custom_css'),10);
			add_action('wp_footer', array($this, 'render_custom_css'),20);
		}

		/**
		 * Add custom css
		 *
		 * @param $css
		 * @param string $key (default: '')
		 */
		public function addCss($css, $key = '')
		{
			if ($key === '') {
				$this->_custom_css[] = $css;
			} else {
				$this->_custom_css[$key] = $css;
			}
		}

		/**
		 * Get Custom Css
		 *
		 * @return string
		 */
		public function getCss()
		{
			$css ='   ' . implode('', $this->_custom_css);
			return preg_replace('/\r\n|\n|\t/','',$css);
		}

		/**
		 * Render custom css in footer
		 */
		public function init_custom_css() {
			echo '<style type="text/css" id="grid-custom-css"></style>';
		}

        public function render_custom_css() {
            echo sprintf('<script>jQuery("style#grid-custom-css").append("%s");</script>',$this->getCss());
        }
	}

	if(!function_exists('grid_custom_css')) {
        function grid_custom_css()
        {
            return G5Plus_Custom_Css::init();
        }
    }
    grid_custom_css();
}