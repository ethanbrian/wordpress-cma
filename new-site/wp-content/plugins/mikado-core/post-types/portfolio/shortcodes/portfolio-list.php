<?php
namespace MkdCore\CPT\Portfolio\Shortcodes;

use MkdCore\Lib;

/**
 * Class PortfolioList
 * @package MkdCore\CPT\Portfolio\Shortcodes
 */
class PortfolioList implements Lib\ShortcodeInterface {
    /**
     * @var string
     */
    private $base;

    public function __construct() {
        $this->base = 'mkd_portfolio_list';

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
     * @see vc_map
     */
    public function vcMap() {
        if(function_exists('vc_map')) {

            vc_map( array(
                'name' => esc_html__( 'Portfolio List', 'mikado-core' ),
                'base' => $this->getBase(),
                'category' => esc_html__( 'by MIKADO', 'mikado-core' ),
                'icon' => 'icon-wpb-portfolio extended-custom-icon',
                'allowed_container_element' => 'vc_row',
                'params' => array(
						array(
							'type' => 'dropdown',								
							'heading' => esc_html__( 'Portfolio List Template', 'mikado-core' ),
							'param_name' => 'type',
							'value' => array(
								esc_html__('Standard', 'mikado-core' ) => 'standard',
								esc_html__('Gallery', 'mikado-core' ) => 'gallery'
							),
							'admin_label' => true,
							'description' => ''
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
							'admin_label' => true,
							'description' => ''
						),
						array(
							'type' => 'dropdown',
							'heading' => esc_html__( 'Image Proportions', 'mikado-core' ),
							'param_name' => 'image_size',
							'value' => array(
								esc_html__('Original', 'mikado-core' ) => 'full',
								esc_html__('Square', 'mikado-core' ) => 'square',
								esc_html__('Landscape', 'mikado-core' ) => 'landscape',
								esc_html__('Portrait', 'mikado-core' ) => 'portrait'
							),
							'save_always' => true,
							'admin_label' => true,
							'description' => '',
							'dependency' => array('element' => 'type', 'value' => array('standard', 'gallery'))
						),
						array(
							'type' => 'dropdown',
							'heading' => esc_html__( 'Show Load More', 'mikado-core' ),
							'param_name' => 'show_load_more',
							'value' => array(
								esc_html__('Yes', 'mikado-core' ) => 'yes',
								esc_html__('No', 'mikado-core' ) => 'no'
							),
							'save_always' => true,
							'admin_label' => true,
							'description' => esc_html__( 'Default value is Yes', 'mikado-core' )
						),
						array(
							'type' => 'dropdown',
							'heading' => esc_html__( 'Order By', 'mikado-core' ),
							'param_name' => 'order_by',
							'value' => array(
								esc_html__('Menu Order', 'mikado-core' ) => 'menu_order',
								esc_html__('Title', 'mikado-core' ) => 'title',
								esc_html__('Date', 'mikado-core' ) => 'date'
							),
							'admin_label' => true,
							'save_always' => true,
							'description' => '',
							'group' => esc_html__( 'Query and Layout Options', 'mikado-core' )
						),
						array(
							'type' => 'dropdown',
							'heading' => esc_html__( 'Order', 'mikado-core' ),
							'param_name' => 'order',
							'value' => array(
								esc_html__('ASC', 'mikado-core' ) => 'ASC',
								esc_html__('DESC', 'mikado-core' ) => 'DESC',
							),
							'admin_label' => true,
							'save_always' => true,
							'description' => '',
							'group' => esc_html__( 'Query and Layout Options', 'mikado-core' )
						),
						array(
							'type' => 'textfield',
							'heading' => esc_html__( 'One-Category Portfolio List', 'mikado-core' ),
							'param_name' => 'category',
							'value' => '',
							'admin_label' => true,
							'description' => esc_html__( 'Enter one category slug (leave empty for showing all categories)', 'mikado-core' ),
							'group' => esc_html__( 'Query and Layout Options', 'mikado-core' )
						),
						 array(
							'type' => 'textfield',
							'heading' => esc_html__( 'Number of Portfolios Per Page', 'mikado-core' ),
							'param_name' => 'number',
							'value' => '-1',
							'admin_label' => true,
							'description' => esc_html__( '(enter -1 to show all)', 'mikado-core' ),
							'group' => esc_html__( 'Query and Layout Options', 'mikado-core' )
						),
						array(
							'type' => 'dropdown',
							'heading' => esc_html__( 'Number of Columns', 'mikado-core' ),
							'param_name' => 'columns',
							'value' => array(
								'' => '',
								esc_html__('One', 'mikado-core' ) => 'one',
								esc_html__('Two', 'mikado-core' ) => 'two',
								esc_html__('Three', 'mikado-core' ) => 'three',
								esc_html__('Four', 'mikado-core' ) => 'four'
							),
							'admin_label' => true,
							'description' => esc_html__( 'Default value is Three', 'mikado-core' ),
							'dependency' => array('element' => 'type', 'value' => array('standard','gallery')),
							'group' => esc_html__( 'Query and Layout Options', 'mikado-core' )
						),
						array(
							'type' => 'dropdown',
							'heading' => esc_html__( 'Grid Size', 'mikado-core' ),
							'param_name' => 'grid_size',								
							'value' => array(
								esc_html__('Default', 'mikado-core' ) => '',
								esc_html__('3 Columns Grid', 'mikado-core' ) => 'three',
								esc_html__('4 Columns Grid', 'mikado-core' ) => 'four',
								esc_html__('5 Columns Grid', 'mikado-core' ) => 'five'
							),
							'admin_label' => true,
							'description' => esc_html__( 'This option is only for Full Width Page Template', 'mikado-core' ),
							'dependency' => array('element' => 'type', 'value' => array('pinterest')),
							'group' => esc_html__( 'Query and Layout Options', 'mikado-core' )
						),
						array(
							'type' => 'textfield',
							'heading' => esc_html__( 'Show Only Projects with Listed IDs', 'mikado-core' ),
							'param_name' => 'selected_projects',
							'value' => '',
							'admin_label' => true,
							'description' => esc_html__( 'Delimit ID numbers by comma (leave empty for all)', 'mikado-core' ),
							'group' => esc_html__( 'Query and Layout Options', 'mikado-core' )
						),
						array(
							'type' => 'dropdown',
							'heading' => esc_html__( 'Enable Category Filter', 'mikado-core' ),
							'param_name' => 'filter',
							'value' => array(
								esc_html__('No', 'mikado-core' ) => 'no',
								esc_html__('Yes', 'mikado-core' ) => 'yes'
							),
							'admin_label' => true,
							'save_always' => true,
							'description' => esc_html__( 'Default value is No', 'mikado-core' ),
							'group' => esc_html__( 'Query and Layout Options', 'mikado-core' )
						),
						array(
							'type' => 'dropdown',
							'heading' => esc_html__( 'Filter Order By', 'mikado-core' ),
							'param_name' => 'filter_order_by',
							'value' => array(
								esc_html__('Name', 'mikado-core' )  => 'name',
								esc_html__('Count', 'mikado-core' ) => 'count',
								esc_html__('Id', 'mikado-core' )    => 'id',
								esc_html__('Slug', 'mikado-core' )  => 'slug'
							),
							'admin_label' => true,
							'save_always' => true,
							'description' => esc_html__( 'Default value is Name', 'mikado-core' ),
							'dependency' => array('element' => 'filter', 'value' => array('yes')),
							'group' => esc_html__( 'Query and Layout Options', 'mikado-core' )
						)
						)
				)
			);
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
            'columns' => 'three',
            'grid_size' => 'three',
            'image_size' => 'full',
            'order_by' => 'date',
            'order' => 'ASC',
            'number' => '-1',
            'filter' => 'no',
            'filter_order_by' => 'name',
            'category' => '',
            'selected_projects' => '',
            'show_load_more' => 'yes',
            'title_tag' => 'h4',
			'next_page' => '',
			'portfolio_slider' => '',
			'next_page' => '',
			'portfolios_shown' => ''
        );

		$params = shortcode_atts($args, $atts);
		extract($params);
		
		$query_array = $this->getQueryArray($params);
		$query_results = new \WP_Query($query_array);		
		$params['query_results'] = $query_results;
		
		$classes = $this->getPortfolioClasses($params);
		$data_atts = $this->getDataAtts($params);
		$data_atts .= 'data-max-num-pages = '.$query_results->max_num_pages;
		$params['masonry_filter'] = '';
		
		$html = '';

		if($filter == 'yes' && ($type == 'masonry' || $type =='pinterest')){
			$params['filter_categories'] = $this->getFilterCategories($params);
			$params['masonry_filter'] = 'mkd-masonry-filter';
			$html .= mkd_core_get_shortcode_module_template_part('portfolio','portfolio-filter', '', $params);
		}
		
		$html .= '<div class = "mkd-portfolio-list-holder-outer '.$classes.'" '.$data_atts. '>';
		
		if($filter == 'yes' && ($type == 'standard' || $type =='gallery')){
			$params['filter_categories'] = $this->getFilterCategories($params);	
			$html .= mkd_core_get_shortcode_module_template_part('portfolio','portfolio-filter', '', $params);
		}
		
		$html .= '<div class = "mkd-portfolio-list-holder clearfix" >';
		if($type == 'masonry' || $type == 'pinterest'){
			$html .= '<div class="mkd-portfolio-list-masonry-grid-sizer"></div>';
			$html .= '<div class="mkd-portfolio-list-masonry-grid-gutter"></div>';
		}
		
		if($query_results->have_posts()):			
			while ( $query_results->have_posts() ) : $query_results->the_post(); 
			
				$params['current_id'] = get_the_ID();
				$params['thumb_size'] = $this->getImageSize($params);
				$params['icon_html'] = $this->getPortfolioIconsHtml($params);
				$params['category_html'] = $this->getItemCategoriesHtml($params);
				$params['categories'] = $this->getItemCategories($params);
				$params['article_masonry_size'] = $this->getMasonrySize($params);
				$params['title_link'] = $this->getPortfolioLink($params);
				
				$html .= mkd_core_get_shortcode_module_template_part('portfolio',$type, '', $params);
				
			endwhile;
		else: 
			
			$html .= '<p>'. esc_html_e( 'Sorry, no posts matched your criteria.', 'mikado-core' ) .'</p>';
		
		endif;
        $html .='<div class="mkd-gap"></div>';
        $html .='<div class="mkd-gap"></div>';
        $html .='<div class="mkd-gap"></div>';
        $html .='<div class="mkd-gap"></div>';


		$html .= '</div>'; //close mkd-portfolio-list-holder
		if($show_load_more == 'yes'){
			$html .= mkd_core_get_shortcode_module_template_part('portfolio','load-more-template', '', $params);
		}
		wp_reset_postdata();
		$html .= '</div>'; // close mkd-portfolio-list-holder-outer
        return $html;
	}
	
	/**
    * Generates portfolio list query attribute array
    *
    * @param $params
    *
    * @return array
    */
	public function getQueryArray($params){
		
		$query_array = array();
		
		$query_array = array(
			'post_type' => 'portfolio-item',
			'orderby' =>$params['order_by'],
			'order' => $params['order'],
			'posts_per_page' => $params['number']
		);
		
		if(!empty($params['category'])){
			$query_array['portfolio-category'] = $params['category'];
		}
		
		$project_ids = null;
		if (!empty($params['selected_projects'])) {
			$project_ids = explode(',', $params['selected_projects']);
			$query_array['post__in'] = $project_ids;
		}
		
		$paged = '';
		if(empty($params['next_page'])) {
            if(get_query_var('paged')) {
                $paged = get_query_var('paged');
            } elseif(get_query_var('page')) {
                $paged = get_query_var('page');
            }
        }
		
		if(!empty($params['next_page'])){
			$query_array['paged'] = $params['next_page'];
			
		}else{
			$query_array['paged'] = 1;
		}
		
		return $query_array;
	}

	/**
    * Generates portfolio icons html
    *
    * @param $params
    *
    * @return html
    */
	public function getPortfolioIconsHtml($params){

		$html ='';
		$id = $params['current_id'];
		$slug_list_ = 'pretty_photo_gallery';

		$featured_image_array = wp_get_attachment_image_src(get_post_thumbnail_id($id), 'full'); //original size
		$large_image = $featured_image_array[0];

		$html .= '<div class="mkd-item-icons-holder">';

		$html .= '<a class="mkd-portfolio-lightbox" title="' . get_the_title($id) . '" href="' . $large_image . '" data-rel="prettyPhoto[' . $slug_list_ . ']" rel="prettyPhoto[' . $slug_list_ . ']"></a>';


		if (function_exists('libero_mikado_like_portfolio_list')) {
			$html .= libero_mikado_like_portfolio_list($id);
		}

		$html .= '<a class="mkd-preview" title="'.esc_attr__('Go to Project','mikado-core').'" href="' . get_the_permalink($id) . '" data-type="portfolio_list"></a>';

		$html .= '</div>';

		return $html;

	}
	/**
    * Generates portfolio classes
    *
    * @param $params
    *
    * @return string
    */
	public function getPortfolioClasses($params){
		$classes = array();
		$type = $params['type'];
		$columns = $params['columns'];
		$grid_size = $params['grid_size'];
		switch($type):
			case 'standard':
				$classes[] = 'mkd-ptf-standard';
			break;
			case 'gallery':
				$classes[] = 'mkd-ptf-gallery';
			break;
			case 'masonry':
				$classes[] = 'mkd-ptf-masonry';
			break;
			case 'pinterest':
				$classes[] = 'mkd-ptf-pinterest';
			break;
		endswitch;
		
		if(empty($params['portfolio_slider'])){ // portfolio slider mustn't have this classes
			
			if($type == 'standard' || $type == 'gallery' ){
				switch ($columns):
					case 'one': 
						$classes[] = 'mkd-ptf-one-column';
					break;
					case 'two': 
						$classes[] = 'mkd-ptf-two-columns';
					break;
					case 'three': 
						$classes[] = 'mkd-ptf-three-columns';
					break;
					case 'four': 
						$classes[] = 'mkd-ptf-four-columns';
					break;
					case 'five': 
						$classes[] = 'mkd-ptf-five-columns';
					break;
					case 'six': 
						$classes[] = 'mkd-ptf-six-columns';
					break;
				endswitch;
			}
			if($params['show_load_more']== 'yes'){ 
				$classes[] = 'mkd-ptf-load-more';
			}
			
		}

		if($type == "pinterest"){
			switch ($grid_size):
				case 'three':
					$classes[] = 'mkd-ptf-pinterest-three-columns';
				break;
				case 'four':
					$classes[] = 'mkd-ptf-pinterest-four-columns';
				break;
				case 'five':
					$classes[] = 'mkd-ptf-pinterest-five-columns';
				break;
			endswitch;
		}
		if($params['filter'] == 'yes'){
			$classes[] = 'mkd-ptf-has-filter';
			if($params['type'] == 'masonry' || $params['type'] == 'pinterest'){
				if($params['filter'] == 'yes'){
					$classes[] = 'mkd-ptf-masonry-filter';
				}
			}
		}
		
		if(!empty($params['portfolio_slider']) && $params['portfolio_slider'] == 'yes'){
			$classes[] = 'mkd-portfolio-slider-holder';
		}
		
		return implode(' ',$classes);
        
	}
	/**
    * Generates portfolio image size
    *
    * @param $params
    *
    * @return string
    */
	public function getImageSize($params){
		
		$thumb_size = 'full';
		$type = $params['type'];
		
		if($type == 'standard' || $type == 'gallery'){
			if(!empty($params['image_size'])){
				$image_size = $params['image_size'];

				switch ($image_size) {
					case 'landscape':
						$thumb_size = 'libero_mikado_landscape';
						break;
					case 'portrait':
						$thumb_size = 'libero_mikado_portrait';
						break;
					case 'square':
						$thumb_size = 'libero_mikado_square';
						break;
					case 'full':
						$thumb_size = 'full';
						break;
				}
			}
		}
		elseif($type == 'masonry'){

			$id = $params['current_id'];
			$masonry_size = get_post_meta($id, 'portfolio_masonry_dimenisions',true);

			switch($masonry_size):
				case 'default' :
					$thumb_size = 'libero_mikado_portfolio_square';
				break;
				case 'large_width' :
					$thumb_size = 'libero_mikado_large_width';
				break;
				case 'large_height' :
					$thumb_size = 'libero_mikado_large_height';
				break;
				case 'large_width_height' :
					$thumb_size = 'libero_mikado_large_width_height';
				break;
			endswitch;
		}
		
		return $thumb_size;
	}
	/**
    * Generates portfolio item categories ids.This function is used for filtering
    *
    * @param $params
    *
    * @return array
    */
	public function getItemCategories($params){
		$id = $params['current_id'];
		$category_return_array = array();
		
		$categories = wp_get_post_terms($id, 'portfolio-category');
		
		foreach($categories as $cat){
			$category_return_array[] = 'portfolio_category_'.$cat->term_id;
		}
		return implode(' ', $category_return_array);
	}
	/**
    * Generates portfolio item categories html based on id
    *
    * @param $params
    *
    * @return html
    */
	public function getItemCategoriesHtml($params){
		$id = $params['current_id'];
		
		$categories = wp_get_post_terms($id, 'portfolio-category');
		$category_html = '<div class="mkd-ptf-category-holder">';
		$k = 1;
		foreach ($categories as $cat) {
			$category_html .= '<span>'.$cat->name.'</span>';
			if (count($categories) != $k) {
				$category_html .= ' | ';
			}
			$k++;
		}
		$category_html .= '</div>'; 
		return $category_html;
	}
	/**
    * Generates masonry size class for each article( based on id)
    *
    * @param $params
    *
    * @return string
    */
	public function getMasonrySize($params){
		$masonry_size_class = '';

		if($params['type'] == 'masonry'){

			$id = $params['current_id'];
			$masonry_size = get_post_meta($id, 'portfolio_masonry_dimenisions',true);
			switch($masonry_size):
				case 'default' :
					$masonry_size_class = 'mkd-default-masonry-item';
				break;
				case 'large_width' :
					$masonry_size_class = 'mkd-large-width-masonry-item';
				break;
				case 'large_height' :
					$masonry_size_class = 'mkd-large-height-masonry-item';
				break;
				case 'large_width_height' :
					$masonry_size_class = 'mkd-large-width-height-masonry-item';
				break;
			endswitch;
		}

		return $masonry_size_class;
	}
	/**
    * Generates filter categories array
    *
    * @param $params
    *
	 * 
	 * 
	 * 
	 * * @return array
    */
	public function getFilterCategories($params){
		
		$cat_id = 0;
		
		if(!empty($params['category'])){	
			
			$top_category = get_term_by('slug', $params['category'], 'portfolio-category');
			if(isset($top_category->term_id)){
				$cat_id = $top_category->term_id;
			}
			
		}
		
		$args = array(
			'child_of' => $cat_id,
			'order_by' => $params['filter_order_by']
		);
		
		$filter_categories = get_terms('portfolio-category',$args);
		
		return $filter_categories;
		
	}
	/**
    * Generates datta attributes array
    *
    * @param $params
    *
    * @return array
    */
	public function getDataAtts($params){
		
		$data_attr = array();
		$data_return_string = '';
		
		if(get_query_var('paged')) {
            $paged = get_query_var('paged');
        } elseif(get_query_var('page')) {
            $paged = get_query_var('page');
        } else {
            $paged = 1;
        }
		
		if(!empty($paged)) {
            $data_attr['data-next-page'] = $paged+1;
        }		
		if(!empty($params['type'])){
			$data_attr['data-type'] = $params['type'];
		}
		if(!empty($params['columns'])){
			$data_attr['data-columns'] = $params['columns'];
		}
		if(!empty($params['grid_size'])){
			$data_attr['data-grid-size'] = $params['grid_size'];
		}
		if(!empty($params['order_by'])){
			$data_attr['data-order-by'] = $params['order_by'];
		}
		if(!empty($params['order'])){
			$data_attr['data-order'] = $params['order'];
		}
		if(!empty($params['number'])){
			$data_attr['data-number'] = $params['number'];
		}
		if(!empty($params['image_size'])){
			$data_attr['data-image-size'] = $params['image_size'];
		}
		if(!empty($params['filter'])){
			$data_attr['data-filter'] = $params['filter'];
		}
		if(!empty($params['filter_order_by'])){
			$data_attr['data-filter-order-by'] = $params['filter_order_by'];
		}
		if(!empty($params['category'])){
			$data_attr['data-filter-order-by'] = $params['category'];
		}
		if(!empty($params['selected_projectes'])){
			$data_attr['data-selected-projects'] = $params['selected_projectes'];
		}
		if(!empty($params['show_load_more'])){
			$data_attr['data-show-load-more'] = $params['show_load_more'];
		}
		if(!empty($params['title_tag'])){
			$data_attr['data-title-tag'] = $params['title_tag'];
		}
		if(!empty($params['portfolio_slider']) && $params['portfolio_slider']=='yes'){
			$data_attr['data-items'] = $params['portfolios_shown'];
		}
		
		foreach($data_attr as $key => $value) {
			if($key !== '') {
				$data_return_string .= $key.'= '.esc_attr($value).' ';
			}
		}
		return $data_return_string;
	}

	/**
    * Gets portfolio link for each article( based on id)
    *
    * @param $params
    *
    * @return string
    */
	public function getPortfolioLink($params){

		$id = $params['current_id'];
		$portfolio_link = get_permalink($id);
		if (get_post_meta($id, 'portfolio_external_link',true) !== ''){
			$portfolio_link = get_post_meta($id, 'portfolio_external_link',true);
		}

		return $portfolio_link;
	}
}