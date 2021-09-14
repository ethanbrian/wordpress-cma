<?php
/**
 * Created by PhpStorm.
 * User: My PC
 * Date: 04/04/2017
 * Time: 2:54 CH
 * @var $section_id
 * @var $name
 * @var $grid
 * @var $current_page
 * @var $carousel_skin
 * @var $source_type
 * @var $post_not_in
 */

global $category__in;
$grid = Grid_Plus_Base::gf_get_grid_by_name($name);
$grid_config = $grid['grid_config'];
$grid_data_source = $grid['grid_data_source'];

$layout_type = $grid_config['type'];
$justified_row_height = isset($grid['grid_config']['justified_row_height']) ? $grid['grid_config']['justified_row_height'] : '100';
$gutter = isset($grid['grid_config']['gutter']) ? $grid['grid_config']['gutter'] : '0';

$justified_skin = isset($grid_config['main_skin']) ? $grid_config['main_skin'] : '';

$crop_image = isset($grid_config['crop_image']) ? $grid_config['crop_image'] : 'false';
$crop_image = 'true' == $crop_image ? true : false;
$disable_link = isset($grid_config['disable_link']) ? $grid_config['disable_link'] : 'false';
$item_per_page = isset($grid_config['item_per_page']) ? $grid_config['item_per_page'] : 8;
$total_item = isset($grid_config['total_item']) ? $grid_config['total_item'] : -1;
if(intval($total_item) == 0) {
    $total_item = -1;
}

$gutter = $grid_config['gutter'];

$post_type = $grid_data_source['post_type'];
$categories = isset($grid_data_source['categories']) && $grid_data_source['categories'] != '' ? $grid_data_source['categories'] : array();
$show_category = $grid_data_source['show_category'];
$cate_multi_line = 'false';
if(isset($grid_data_source['cate_multi_line']) && !empty($grid_data_source['cate_multi_line'])) {
    $cate_multi_line = $grid_data_source['cate_multi_line'];
}
$authors = $grid_data_source['authors'];
$include_ids = $grid_data_source['include_ids'];
$exclude_ids = $grid_data_source['exclude_ids'];
$order = $grid_data_source['order'];
$order_by = $grid_data_source['order_by'];

$offset = 0;
$current_page = isset($current_page) ? $current_page : 1;
if ($item_per_page > 0) {
    $offset = ($current_page - 1) * $item_per_page;
}

if(count($categories)==0 && isset($show_category) && $show_category!='none'){
    $categories_info = Grid_Plus_Base::gf_get_categories_info_by_posttype($post_type);
    foreach($categories_info as $cat){
        $categories[] = $cat['term_id'];
    }
}

$args = array(
    'offset'         => $offset,
    'posts_per_page' => $item_per_page,
    'post_type'      => $post_type,
    'post_status'    => 'publish'
);

$category_taxonomy = Grid_Plus_Base::gf_get_category_taxonomy($post_type);
$all_taxonomies = array();
foreach ($category_taxonomy as $tax) {
    $all_taxonomies[] = $tax['name'];
}
$terms_in = array();

if (isset($category_taxonomy) && is_array($category_taxonomy) && count($category_taxonomy) > 0) {

    if (isset($category__in) && is_array($category__in) && count($category__in) > 0) {
        $terms_in = $category__in;
    } else {
        if (isset($categories) && is_array($categories) && count($categories) > 0) {
            $terms_in = $categories;
        }
    }

    if (!empty($terms_in)) {
        $taxonomy_query = array('relation' => 'OR');
        foreach ($category_taxonomy as $taxonomy) {
            $taxonomy_query[] = array(
                'taxonomy' => $taxonomy['name'],
                'field'    => 'term_id',
                'terms'    => $terms_in,
                'operator' => 'IN'
            );
        }
        $args['tax_query'] = $taxonomy_query;
    }
}

if(!isset($include_ids) || empty($include_ids)) {
    $include_ids = array();
}
if(!isset($exclude_ids) || empty($exclude_ids)) {
    $exclude_ids = array();
}
$exclude_ids = array_merge($exclude_ids, $post_not_in);
if (is_array($include_ids) && count($include_ids) > 0) {
    $args['post__in'] = $include_ids;
    $args['orderby'] = 'post__in';
}
if (is_array($exclude_ids) && count($exclude_ids) > 0) {
    $args['post__not_in'] = $exclude_ids;
}
$custom_urls = '';
$disable_source = false;
if ($post_type == 'attachment') {
    $args['post_mime_type'] = 'image/jpeg,image/gif,image/jpg,image/png';
    $args['post_status'] = 'any';
    if(isset($grid['grid_data_source']['grid_gallery']) && !empty($grid['grid_data_source']['grid_gallery'])) {
        $custom_urls = isset($grid_data_source['custom_urls']) ? $grid_data_source['custom_urls'] : '';
        $disable_source = true;
        $grid_gallery = $grid['grid_data_source']['grid_gallery'];
        $grid_gallery = explode('|', $grid_gallery);
        $args['post__in'] = $grid_gallery;
        $args['orderby'] = 'post__in';
    }
}

if (!$disable_source) {
    if (isset($authors) && is_array($authors) && count($authors) > 0) {
        $args['author__in'] = $authors;
    }
    $source_filter = isset($grid_data_source['source_filter']) ? $grid_data_source['source_filter'] : 'all';
    if (in_array($source_filter, array('all','sale','featured'))) {
        if ($order_by) {
            $args['orderby'] = $order_by;
            $args['order'] = $order;
        }
        if(class_exists('WooCommerce')) {
            $product_visibility_term_ids = wc_get_product_visibility_term_ids();
            switch ($source_filter) {
                case 'sale':
                    $product_ids_on_sale = wc_get_product_ids_on_sale();
                    $product_ids_on_sale[] = 0;
                    if (is_array($include_ids) && count($include_ids) > 0) {
                        $args['post__in'] = array_intersect($include_ids, $product_ids_on_sale);
                    } else {
                        $args['post__in'] = $product_ids_on_sale;
                    }
                    break;
                case 'featured':
                    $args['tax_query'][] = array(
                        'taxonomy' => 'product_visibility',
                        'field' => 'term_taxonomy_id',
                        'terms' => $product_visibility_term_ids['featured']
                    );
                    break;
            }
        }
    } else {
        switch ($source_filter) {
            case 'popular':
                $args['orderby'] = 'comment_count';
                $args['order'] = 'DESC';
                break;
            case 'recent':
                $args['orderby'] = 'post_date';
                $args['order'] = 'DESC';
                break;
            case 'top-rated':
                $args['meta_key'] = '_wc_average_rating';
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'DESC';
                $args['meta_query'] = WC()->query->get_meta_query();
                $args['tax_query'] = WC()->query->get_tax_query();
                break;
            case 'best-selling' :
                $args['meta_key'] = 'total_sales';
                $args['orderby'] = 'meta_value_num';
                break;
            case 'oldest':
            default:
                $args['orderby'] = 'post_date';
                break;
        }
    }
}
$posts = new WP_Query($args);
$total_post = $posts->found_posts;

// limit total post by get total item from pagination config
if ($total_item > 0 && $total_item <= $total_post) {
    $total_post = $total_item;
}

$grid_full_layout = array();
$ajax_nonce = wp_create_nonce("grid-plus-category");

$grid_plus = new Grid_Plus();
$skin_css = $grid_plus->get_skin_css($justified_skin);
if (isset($skin_css) && $skin_css != '') {
    wp_enqueue_style($justified_skin, str_replace('\\"', '', $skin_css));
}
?>
    <div class="grid-plus-container grid-<?php echo esc_attr($section_id); ?> <?php echo esc_attr($post_type); ?> <?php if('true' == $cate_multi_line): ?> grid-cate-multi-line<?php endif; ?>"
         id="<?php echo esc_attr($section_id); ?>"
         data-grid-name="<?php echo esc_attr($name); ?>"
         data-animation="<?php echo esc_attr($grid_config['animation_type']); ?>">
        <div class="justified-container grid-plus-inner"
             data-ajax-url="<?php echo esc_url(admin_url('admin-ajax.php')) ?>"
             data-grid-id="<?php echo esc_attr($grid['id']); ?>"
             data-current-category="<?php echo implode(",", $categories); ?>"
             data-section-id="<?php echo esc_attr($section_id); ?>"
             data-row-height="<?php echo esc_attr($justified_row_height); ?>"
             data-margin="<?php echo esc_attr($gutter); ?>"
             data-nonce="<?php echo esc_attr($ajax_nonce); ?>"
             data-layout-type="<?php echo esc_attr($layout_type); ?>"
             data-source-type="<?php echo esc_attr($source_type); ?>"
        >
            <?php if (isset($show_category) && $show_category != '' && $show_category != 'none') {
                Grid_Plus_Base::gf_get_template('shortcodes/templates/category', array(
                    'section_id'    => $section_id,
                    'post_type'     => $post_type,
                    'categories'    => $categories,
                    'show_category' => $show_category,
                    'source_type' => $source_type,
                    'cate_multi_line' => $cate_multi_line
                ));
            } ?>

            <div class="justified-items">
                <?php
                $index = 0;
                $crop_size = 600;
                $grid_plus = new Grid_Plus();
                $url_index = $offset;
                if(!empty($custom_urls)) {
                    $custom_urls = explode(';', $custom_urls);
                } else {
                    $custom_urls = array();
                }
                while ($posts->have_posts()) : $posts->the_post();
                    if ($total_item > 0 && ($offset + $index > ($total_item - 1))) {
                        break;
                    }
                    $post_thumbnail_id = $width = $height = $width_crop = $height_crop = 0;
                    $thumbnail = $img_origin = '';

                    $title = get_the_title();
                    $excerpt = get_the_excerpt();
                    $post_thumbnail_id = $post_type != 'attachment' ? get_post_thumbnail_id(get_the_ID()) : get_the_ID();
                    if(!empty($post_thumbnail_id)){
                        Grid_Plus_Base::gf_get_attachment_image($post_thumbnail_id, $crop_image, $crop_size, $width_crop, $height_crop, $width, $height, $img_origin, $thumbnail);
                    }

                    $terms = wp_get_post_terms(get_the_ID(), $all_taxonomies);
                    $cat = $cat_filter = '';
                    foreach ($terms as $term) {
                        $cat_filter .= $term->slug . ' ';
                        $cat .= $term->name . ', ';
                    }
                    $cat = rtrim($cat, ', ');


	                $ico_gallery = 'fa fa-search';
	                $post_format = Grid_Plus_Base::gf_get_post_format(get_the_ID());
	                if (isset($post_format) && $post_format == 'video') {
		                $videos = get_post_meta(get_the_ID(), 'gf_format_video_embed', true);
		                if ($videos !== '') {
			                $ico_gallery = 'fa fa-play';
		                }

	                }


	                if ($post_type === 'attachment') {
		                $video_url = get_post_meta(get_the_ID(),'gsf_photographer_video_url',true);
		                if ($video_url !== '') {
			                $img_origin = $video_url;
			                $ico_gallery = 'fa fa-play';
		                }
	                }

	                $ico_gallery = apply_filters('grid_plus_icon_gallery', $ico_gallery);

                    $post_link = get_permalink();
                    $custom_link = get_post_meta(get_the_ID(),'custom_link',true);
                    if(!empty($custom_link)) {
                        $post_link = $custom_link;
                    }
                    $post_link = isset($custom_urls[$url_index]) ? $custom_urls[$url_index] : $post_link;
                    $url_index++;
                    if($url_index == count($custom_urls)) $url_index = 0;

                    ?>
                    <div class="item">
                        <?php
                        $item_template = $grid_plus->get_skin_template($justified_skin);
                        if (file_exists($item_template)) {
                            include $item_template;
                        } else {
                            echo esc_html__('Could not find this template!', 'grid-plus');
                        }
                        ?>
                    </div>
                    <?php
                endwhile;
                wp_reset_postdata();
                ?>
            </div>
            <?php
            Grid_Plus_Base::gf_get_template('shortcodes/templates/infinite-scroll', array(
                    'item_per_page'   => $item_per_page,
                    'total_post'      => $total_post,
                    'current_page'    => $current_page,
                    'data_section_id' => $section_id,
                    'gutter' => $gutter
                )
            );
            ?>
        </div>
    </div>
<?php

global $grid_plus_custom_css;
if (!isset($grid_plus_custom_css) || !is_array($grid_plus_custom_css)) {
    $grid_plus_custom_css = array();
}
$grid_plus_custom_css[$section_id] = $grid_config;