<?php
/**
 * Created by PhpStorm.
 * User: My PC
 * Date: 04/04/2017
 * Time: 2:08 CH
 * @var $section_id
 * @var $name
 * @var $grid
 * @var $current_page
 * @var $source_type
 * @var $post_not_in
 */
global $category__in;

$grid_config = $grid['grid_config'];
$grid_data_source = $grid['grid_data_source'];
$grid_layout = $grid['grid_layout'];

$crop_image = isset($grid_config['crop_image']) ? $grid_config['crop_image'] : 'false';
$crop_image = 'true' == $crop_image ? true : false;
$disable_link = isset($grid_config['disable_link']) ? $grid_config['disable_link'] : 'false';
$layout_type = $grid_config['type'];
$columns = $grid_config['columns'];
$height_ratio = $grid_config['carousel_height_ratio'];
$width_ratio = $grid_config['carousel_width_ratio'];
$gutter = $grid_config['gutter'];
$total_item = isset($grid_config['total_item']) ? $grid_config['total_item'] : -1;
if(intval($total_item) == 0) {
    $total_item = -1;
}
$carousel_skin = isset($grid_config['main_skin']) ? $grid_config['main_skin'] : 'thumbnail';

$carousel_total_items = isset($grid['grid_config']['carousel_total_items']) ? $grid['grid_config']['carousel_total_items'] : 0;
$carousel_desktop_large_col = isset($grid['grid_config']['carousel_desktop_large_col']) ? $grid['grid_config']['carousel_desktop_large_col'] : 6;
$carousel_desktop_medium_col = isset($grid['grid_config']['carousel_desktop_medium_col']) ? $grid['grid_config']['carousel_desktop_medium_col'] : 5;
$carousel_desktop_small_col = isset($grid['grid_config']['carousel_desktop_small_col']) ? $grid['grid_config']['carousel_desktop_small_col'] : 4;
$carousel_tablet_col = isset($grid['grid_config']['carousel_tablet_col']) ? $grid['grid_config']['carousel_tablet_col'] : 3;
$carousel_tablet_small_col = isset($grid['grid_config']['carousel_tablet_small_col']) ? $grid['grid_config']['carousel_tablet_small_col'] : 2;
$carousel_mobile_col = isset($grid['grid_config']['carousel_mobile_col']) ? $grid['grid_config']['carousel_mobile_col'] : 1;

$carousel_desktop_large_width = isset($grid['grid_config']['carousel_desktop_large_width']) ? $grid['grid_config']['carousel_desktop_large_width'] : 1200;
$carousel_desktop_medium_width = isset($grid['grid_config']['carousel_desktop_medium_width']) ? $grid['grid_config']['carousel_desktop_medium_width'] : 992;
$carousel_desktop_small_width = isset($grid['grid_config']['carousel_desktop_small_width']) ? $grid['grid_config']['carousel_desktop_small_width'] : 768;
$carousel_tablet_width = isset($grid['grid_config']['carousel_tablet_width']) ? $grid['grid_config']['carousel_tablet_width'] : 600;
$carousel_tablet_small_width = isset($grid['grid_config']['carousel_tablet_small_width']) ? $grid['grid_config']['carousel_tablet_small_width'] : 480;
$carousel_mobile_width = isset($grid['grid_config']['carousel_mobile_width']) ? $grid['grid_config']['carousel_mobile_width'] : 320;

$nav_next_text = isset($grid['grid_config']['carousel_next_text']) ? $grid['grid_config']['carousel_next_text'] : '<i class=&quot;fa fa-angle-right&quot;></i>';
$nav_prev_text = isset($grid['grid_config']['carousel_prev_text']) ? $grid['grid_config']['carousel_prev_text'] : '<i class=&quot;fa fa-angle-left&quot;></i>';

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
if(count($categories)==0 && isset($show_category) && $show_category!='none'){
    $categories_info = Grid_Plus_Base::gf_get_categories_info_by_posttype($post_type);
    foreach($categories_info as $cat){
        $categories[] = $cat['term_id'];
    }
}

$args = array(
    'offset'         => 0,
    'posts_per_page' => -1,
    'post_type'      => $post_type,
    'post_status'    => 'publish'
);

if ($carousel_total_items > 0) {
    $args['posts_per_page'] = $carousel_total_items;
}
$category_taxonomy = Grid_Plus_Base::gf_get_category_taxonomy($post_type);
$all_taxonomies = array();
foreach ($category_taxonomy as $tax) {
    $all_taxonomies[] = $tax['name'];
}
$terms_in = array();
if (count($category_taxonomy) > 0) {
    if (isset($category__in) && is_array($category__in) && count($category__in) > 0) {
        $terms_in = $category__in;
    } else {
        if (count($categories) > 0) {
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
        $args['posts_per_page'] = count($grid_gallery);
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
                        'terms' => $product_visibility_term_ids['featured'],
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
$grid_full_layout = array();
$ajax_nonce = wp_create_nonce("grid-plus-category");

$is_loop = isset($grid['grid_config']['loop']) && $grid['grid_config']['loop'] == 'true';
$is_center = isset($grid['grid_config']['center']) && $grid['grid_config']['center'] == 'true';
$is_show_nav = isset($grid['grid_config']['show_nav']) && $grid['grid_config']['show_nav'] == 'true';
$is_show_dot = isset($grid['grid_config']['show_dot']) && $grid['grid_config']['show_dot'] == 'true';
$is_rtl = isset($grid['grid_config']['carousel_rtl']) && $grid['grid_config']['carousel_rtl'] == 'true';
$autoplay = isset($grid['grid_config']['autoplay']) && $grid['grid_config']['autoplay'] == 'true';
$autoplay_time = isset($grid['grid_config']['autoplay_time']) ? $grid['grid_config']['autoplay_time'] : 3000;
$owl_options = array(
    'items'           => intval($carousel_desktop_large_col),
    'loop'            => $is_loop,
    'center'          => $is_center,
    'margin'          => intval($gutter),
    'dots'            => $is_show_dot,
    'nav'             => $is_show_nav,
    'autoplay'        => $autoplay,
    'autoplayTimeout' => intval($autoplay_time),
    'rtl'             => $is_rtl,
    'responsive'      => array(
        $carousel_desktop_large_width  => array(
            'items' => intval($carousel_desktop_large_col)
        ),
        $carousel_desktop_medium_width => array(
            'items' => intval($carousel_desktop_medium_col)
        ),
        $carousel_desktop_small_width  => array(
            'items' => intval($carousel_desktop_small_col)
        ),
        $carousel_tablet_width         => array(
            'items' => intval($carousel_tablet_col)
        ),
        $carousel_tablet_small_width   => array(
            'items' => intval($carousel_tablet_small_col)
        ),
        $carousel_mobile_width         => array(
            'items' => intval($carousel_mobile_col)
        )
    )
);
$grid_plus = new Grid_Plus();
$skin_css = $grid_plus->get_skin_css($carousel_skin);
if (isset($skin_css) && $skin_css != '') {
    wp_enqueue_style($carousel_skin, str_replace('\\"', '', $skin_css));
}
?>

    <div class="grid-plus-container grid-<?php echo esc_attr($section_id); ?> <?php echo esc_attr($post_type); ?> <?php if('true' == $cate_multi_line): ?> grid-cate-multi-line<?php endif; ?>"
         id="<?php echo esc_attr($section_id); ?>"
         data-grid-name="<?php echo esc_attr($name); ?>"
         data-animation="<?php echo esc_attr($grid_config['animation_type']); ?>">
        <div class="grid-carousel-container grid-plus-inner"
             data-ajax-url="<?php echo esc_url(admin_url('admin-ajax.php')) ?>"
             data-grid-id="<?php echo esc_attr($grid['id']); ?>"
             data-current-category="<?php echo implode(",", $categories); ?>"
             data-section-id="<?php echo esc_attr($section_id); ?>"
             data-gutter="<?php echo esc_attr($gutter); ?>"
             data-columns="<?php echo esc_attr($columns); ?>"
             data-nonce="<?php echo esc_attr($ajax_nonce); ?>"
             data-layout-type="<?php echo esc_attr($layout_type); ?>"
             data-source-type="<?php echo esc_attr($source_type); ?>"
             data-height-ratio="<?php echo esc_attr($height_ratio); ?>"
             data-width-ratio="<?php echo esc_attr($width_ratio); ?>"
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

            <div class="carousel-items grid-owl-carousel <?php echo esc_attr($grid['grid_config']['carousel_nav_position']); ?>
                <?php echo esc_attr($grid['grid_config']['carousel_nav_style']); ?>" data-owl-options='<?php echo json_encode($owl_options); ?>'
                <?php if ($is_show_nav) {
                    echo 'data-show-nav="1"';?>
                    data-nav-next-text="<?php echo esc_html($nav_next_text); ?>"
                    data-nav-prev-text="<?php echo esc_html($nav_prev_text); ?>"
                    <?php
                }; ?>
                <?php if ($is_show_dot) {
                    echo 'data-show-dot="1"';
                }; ?>
            >
                <?php
                $crop_size = 600;
                $grid_plus = new Grid_Plus();
                $url_index = 0;
                if(!empty($custom_urls)) {
                    $custom_urls = explode(';', $custom_urls);
                } else {
                    $custom_urls = array();
                }
                while ($posts->have_posts()) : $posts->the_post();
                    $post_thumbnail_id = $width = $height = $width_crop = $height_crop = 0;
                    $img_origin = $thumbnail = '';
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
                        $item_template = $grid_plus->get_skin_template($carousel_skin);
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
        </div>
    </div>

<?php

global $grid_plus_custom_css;
if (!isset($grid_plus_custom_css) || !is_array($grid_plus_custom_css)) {
    $grid_plus_custom_css = array();
}
$grid_plus_custom_css[$section_id] = $grid_config;