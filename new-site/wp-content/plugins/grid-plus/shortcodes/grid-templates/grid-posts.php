<?php
/**
 * Created by PhpStorm.
 * User: My PC
 * Date: 03/04/2017
 * Time: 2:49 CH
 * @var $section_id
 * @var $name
 * @var $grid
 * @var $current_page
 * @var $ajax
 * @var $source_type
 * @var $post_not_in
 */
global $category__in;

$grid_config = $grid['grid_config'];
$grid_data_source = $grid['grid_data_source'];
$grid_layout = $grid['grid_layout'];
$layout_type = $grid_config['type'];
$columns = $grid_config['columns'];
$height_ratio = $grid_config['height_ratio'];
$width_ratio = $grid_config['width_ratio'];
$gutter = $grid_config['gutter'];
$item_per_page = isset($grid_config['item_per_page']) ? $grid_config['item_per_page'] : 8;
$total_item = isset($grid_config['total_item']) ? $grid_config['total_item'] : -1;
if(intval($total_item) == 0) {
    $total_item = -1;
}
$pagination_type = $grid_config['pagination_type'];
if($pagination_type == 'show_all') {
    $item_per_page = -1;
}
$pagination_none = (empty($pagination_type) || $pagination_type == 'show_all') ? true : false;
if((intval($total_item) != -1 && intval($item_per_page) >= intval($total_item)) || empty($pagination_type)) {
    $pagination_type = 'show_all';
}
$skip_custom_content = false;
if($ajax == 3 || $ajax == 4) {
    $skip_custom_content = true;
}
$fix_item_height = isset($grid_config['fix_item_height']) ? $grid_config['fix_item_height'] : 0;
$crop_image = isset($grid_config['crop_image']) ? $grid_config['crop_image'] : 'false';
$crop_image = 'true' == $crop_image ? true : false;
$disable_link = isset($grid_config['disable_link']) ? $grid_config['disable_link'] : 'false';
$item_per_page = ($total_item > 0 && ($item_per_page < 0 || $total_item < $item_per_page)) ? intval($total_item) : intval($item_per_page);

$post_type = $grid_data_source['post_type'];
$categories = isset($grid_data_source['categories']) && $grid_data_source['categories'] != '' ? $grid_data_source['categories'] : array();
$show_category = $grid_data_source['show_category'];
$cate_multi_line = 'false';
if(isset($grid_data_source['cate_multi_line']) && !empty($grid_data_source['cate_multi_line'])) {
    $cate_multi_line = $grid_data_source['cate_multi_line'];
}

$offset = 0;

if(count($categories)==0 && isset($show_category) && $show_category!='none'){
    $categories_info = Grid_Plus_Base::gf_get_categories_info_by_posttype($post_type);
    foreach($categories_info as $cat){
        $categories[] = $cat['term_id'];
    }
}

$main_skin = isset($grid_config['main_skin']) ? $grid_config['main_skin'] : '';
$custom_content_enable = isset($grid_config['custom_content_enable']) ? $grid_config['custom_content_enable'] : 'false';
$custom_content = isset($grid_config['custom_content']) ? $grid_config['custom_content'] : '';
$custom_content_numb = isset($grid_config['custom_content_numb']) ? $grid_config['custom_content_numb'] : '0';
$item_per_page = count($grid_layout) > $item_per_page ? count($grid_layout) : $item_per_page;
if($layout_type == 'metro' && $custom_content_enable == 'true') {
    $item_per_page -= intval($custom_content_numb);
}
if ($item_per_page > 0) {
    $offset = ($current_page - 1) * $item_per_page;
}

if ($layout_type != 'metro' && $pagination_type == 'show_all' && !$pagination_none) {
    if($total_item <= 0) {
        $item_per_page = -1;
    } else {
        $item_per_page = $total_item;
    }
}
$authors = $grid_data_source['authors'];
$include_ids = $grid_data_source['include_ids'];
$exclude_ids = $grid_data_source['exclude_ids'];
$order = $grid_data_source['order'];
$order_by = $grid_data_source['order_by'];
$args = array(
    'offset' => $offset,
    'posts_per_page' => $item_per_page,
    'post_type' => $post_type,
    'post_status' => 'publish'
);

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
                'field' => 'term_id',
                'terms' => $terms_in,
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
$disable_source = false;
$exclude_ids = array_merge($exclude_ids, $post_not_in);
if (is_array($include_ids) && count($include_ids) > 0) {
    $args['post__in'] = $include_ids;
    $args['orderby'] = 'post__in';
    $disable_source = true;
}
if (is_array($exclude_ids) && count($exclude_ids) > 0) {
    $args['post__not_in'] = $exclude_ids;
}
$custom_urls = '';
if ($post_type == 'attachment') {
    $args['post_mime_type'] = 'image/jpeg,image/gif,image/jpg,image/png';
    $args['post_status'] = 'any';
    $attachment_type = isset($grid['grid_data_source']['attachment_type']) ? $grid['grid_data_source']['attachment_type'] : 'choose_source';
    if($attachment_type == 'choose_item' && isset($grid['grid_data_source']['grid_gallery']) && !empty($grid['grid_data_source']['grid_gallery'])) {
        $custom_urls = isset($grid_data_source['custom_urls']) ? $grid_data_source['custom_urls'] : '';
        $disable_source = true;
        $grid_gallery = $grid['grid_data_source']['grid_gallery'];
        $grid_gallery = explode('|', $grid_gallery);
        $args['post__in'] = $grid_gallery;
        $args['orderby'] = 'post__in';
        if ($pagination_type == 'show_all') {
            //$args['posts_per_page'] = count($grid_gallery);
            $item_per_page = count($grid_gallery);
        }
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
// limit total post by get total item from pagination config
if ($total_item > 0 && $total_item <= $total_post) {
    $total_post = $total_item;
}
$grid_full_layout = array();
$ajax_nonce = wp_create_nonce("grid-plus-category");
$grid_stack_class = ($layout_type == 'metro' && $columns != 5) ? 'grid-stack' : 'grid-stack grid-stack-' . $columns;
?>
<div class="grid-plus-container grid-<?php echo esc_attr($section_id); ?> <?php echo esc_attr($post_type); ?> <?php if('true' == $cate_multi_line): ?> grid-cate-multi-line<?php endif; ?>"
     id="<?php echo esc_attr($section_id); ?>"
     data-grid-name="<?php echo esc_attr($name); ?>"
     data-animation="<?php echo esc_attr($grid_config['animation_type']); ?>">
    <div class="grid-stack-container grid-plus-inner"
         data-ajax-url="<?php echo esc_url(admin_url('admin-ajax.php')) ?>"
         data-grid-id="<?php echo esc_attr($grid['id']); ?>"
         data-current-category="<?php echo implode(",", $categories); ?>"
         data-section-id="<?php echo esc_attr($section_id); ?>"
         data-gutter="<?php echo esc_attr($gutter); ?>"
         data-columns="<?php echo esc_attr($columns); ?>"
         data-height-ratio="<?php echo esc_attr($height_ratio); ?>"
         data-width-ratio="<?php echo esc_attr($width_ratio); ?>"
         data-desktop-columns="<?php echo esc_attr($columns); ?>"
         data-tablet-columns="2"
         data-mobile-columns="1"
         data-layout-type="<?php echo esc_attr($layout_type); ?>"
         data-source-type="<?php echo esc_attr($source_type); ?>"
         data-fix-item-height="<?php echo esc_attr($fix_item_height); ?>"
         data-nonce="<?php echo esc_attr($ajax_nonce); ?>"
    >
        <?php if (isset($show_category) && $show_category != '' && $show_category != 'none') { // && $post_type!='attachment'
            Grid_Plus_Base::gf_get_template('shortcodes/templates/category', array(
                'section_id'    => $section_id,
                'post_type'     => $post_type,
                'categories'    => $categories,
                'show_category' => $show_category,
                'source_type'   => $source_type,
                'cate_multi_line' => $cate_multi_line
            ));
        } ?>
        <div class="<?php echo esc_attr($grid_stack_class); ?>" data-layout="<?php echo esc_attr($layout_type); ?>"
             style="height: <?php echo esc_attr($grid_config['height']); ?>px">
        </div>
        <div class="grid-items">
            <?php
            $index = $diff_y = $current_y = $prev_y = 0;
            $url_index = $offset;
            $crop_size = 600;
            $grid_plus = new Grid_Plus();
            if(!empty($custom_urls)) {
                $custom_urls = explode(';', $custom_urls);
            } else {
                $custom_urls = array();
            }
            if($posts->have_posts() && $total_post > 0):
                while ($posts->have_posts()) : $posts->the_post();
                    $item_skin = isset($grid_layout[$index]['skin']) ? $grid_layout[$index]['skin'] : 'thumbnail';
                    while($layout_type == 'metro' && $custom_content_enable == 'true' && $item_skin == 'custom-content') :
                        if(!$skip_custom_content):?>
                            <div class="item" data-gs-x="<?php echo esc_attr($grid_layout[$index]['x']); ?>"
                                 data-gs-y="<?php echo esc_attr($grid_layout[$index]['y']); ?>"
                                 data-gs-width="<?php echo esc_attr($grid_layout[$index]['width']); ?>"
                                 data-gs-height="<?php echo esc_attr($grid_layout[$index]['height']); ?>"
                                 data-skin="<?php echo esc_attr($grid_layout[$index]['skin']); ?>"

                                 data-desktop-gs-x="<?php echo esc_attr($grid_layout[$index]['x']); ?>"
                                 data-desktop-gs-y="<?php echo esc_attr($grid_layout[$index]['y']); ?>"
                                 data-desktop-gs-width="<?php echo esc_attr($grid_layout[$index]['width']); ?>"
                                 data-desktop-gs-height="<?php echo esc_attr($grid_layout[$index]['height']); ?>"
                                 data-desktop-skin="<?php echo esc_attr($grid_layout[$index]['skin']); ?>"

                                 data-image-width="<?php echo esc_attr($grid_layout[$index]['width']); ?>"
                                 data-image-height="<?php echo esc_attr($grid_layout[$index]['height']); ?>"
                                 data-item-width-ratio="<?php echo esc_attr($grid_layout[$index]['item_width_ratio']); ?>"
                                 data-item-height-ratio="<?php echo esc_attr($grid_layout[$index]['item_height_ratio']); ?>"
                            >
                                <div class="grid-post-item thumbnail custom-content" data-thumbnail-only="1">
                                    <div class="thumbnail-image">
                                        <?php echo Grid_Plus_Base::content_block($custom_content); ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                            $index++;
                            $custom_content_numb--;
                            $item_skin = isset($grid_layout[$index]['skin']) ? $grid_layout[$index]['skin'] : 'thumbnail';
                        else:
                            $item_skin = $main_skin;
                        endif;
                    endwhile;
                    if ($total_item > 0 && ($offset + $index > ($total_item - 1))) {
                        break;
                    }
                    if($layout_type !== 'metro') {
                        if ($grid_layout[$index]['y'] > $prev_y) {
                            $diff_y = $grid_layout[$index]['y'] - $prev_y;
                            $prev_y = $grid_layout[$index]['y'];
                        }
                        if ($grid_layout[$index]['x'] == 0) {
                            $current_y += $diff_y;
                        }
                    } else {
                        $current_y = $grid_layout[$index]['y'];
                    }
                    $post_thumbnail_id = $width = $height = $width_crop = $height_crop = 0;
                    $img_origin = $thumbnail = '';
                    $title = get_the_title();
                    $excerpt = get_the_excerpt();
                    $post_thumbnail_id = $post_type != 'attachment' ? get_post_thumbnail_id(get_the_ID()) : get_the_ID();
                    if(!empty($post_thumbnail_id)){
                        Grid_Plus_Base::gf_get_attachment_image($post_thumbnail_id, $crop_image, $crop_size, $width_crop, $height_crop, $width, $height, $img_origin, $thumbnail);
                    }

                    $thumbnail = isset($thumbnail) && $thumbnail != '' ? $thumbnail : $img_origin;

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



                    $skin_css = $grid_plus->get_skin_css($item_skin);
                    if (isset($skin_css) && $skin_css != '') {
                        wp_enqueue_style($grid_layout[$index]['skin'], str_replace('\\"', '', $skin_css));
                    }
                    $post_link = isset($custom_urls[$url_index]) ? $custom_urls[$url_index] : $post_link;
                    $url_index++;
                    if($url_index == count($custom_urls)) $url_index = 0;
                    ?>
                    <div class="item" data-gs-x="<?php echo esc_attr($grid_layout[$index]['x']); ?>"
                         data-gs-y="<?php echo esc_attr($current_y); ?>"
                         data-gs-width="<?php echo esc_attr($grid_layout[$index]['width']); ?>"
                         data-gs-height="<?php echo esc_attr($grid_layout[$index]['height']); ?>"
                         data-skin="<?php echo esc_attr($grid_layout[$index]['skin']); ?>"

                         data-desktop-gs-x="<?php echo esc_attr($grid_layout[$index]['x']); ?>"
                         data-desktop-gs-y="<?php echo esc_attr($current_y); ?>"
                         data-desktop-gs-width="<?php echo esc_attr($grid_layout[$index]['width']); ?>"
                         data-desktop-gs-height="<?php echo esc_attr($grid_layout[$index]['height']); ?>"
                         data-desktop-skin="<?php echo esc_attr($grid_layout[$index]['skin']); ?>"

                         data-image-width="<?php echo esc_attr($width); ?>"
                         data-image-height="<?php echo esc_attr($height); ?>"
                         data-item-width-ratio="<?php echo esc_attr($grid_layout[$index]['item_width_ratio']); ?>"
                         data-item-height-ratio="<?php echo esc_attr($grid_layout[$index]['item_height_ratio']); ?>"
                    >
                        <?php
                        $item_template = $grid_plus->get_skin_template($item_skin);
                        if (file_exists($item_template)) {
                            include $item_template;
                        } else {
                            echo esc_html__('Could not find this template!', 'grid-plus');
                        }
                        ?>
                    </div>
                    <?php
                    $index++;

                    if (count($grid_layout) <= $index) {
                        $index = 0;
                    }
                endwhile;
                $item_skin = isset($grid_layout[$index]['skin']) ? $grid_layout[$index]['skin'] : 'thumbnail';
                if($layout_type == 'metro' && $custom_content_enable == 'true' && $item_skin == 'custom-content') :
                    while ($custom_content_numb > 0): ?>
                        <div class="item" data-gs-x="<?php echo esc_attr($grid_layout[$index]['x']); ?>"
                             data-gs-y="<?php echo esc_attr($grid_layout[$index]['y']); ?>"
                             data-gs-width="<?php echo esc_attr($grid_layout[$index]['width']); ?>"
                             data-gs-height="<?php echo esc_attr($grid_layout[$index]['height']); ?>"
                             data-skin="<?php echo esc_attr($grid_layout[$index]['skin']); ?>"

                             data-desktop-gs-x="<?php echo esc_attr($grid_layout[$index]['x']); ?>"
                             data-desktop-gs-y="<?php echo esc_attr($grid_layout[$index]['y']); ?>"
                             data-desktop-gs-width="<?php echo esc_attr($grid_layout[$index]['width']); ?>"
                             data-desktop-gs-height="<?php echo esc_attr($grid_layout[$index]['height']); ?>"
                             data-desktop-skin="<?php echo esc_attr($grid_layout[$index]['skin']); ?>"

                             data-image-width="<?php echo esc_attr($grid_layout[$index]['width']); ?>"
                             data-image-height="<?php echo esc_attr($grid_layout[$index]['height']); ?>"
                             data-item-width-ratio="<?php echo esc_attr($grid_layout[$index]['item_width_ratio']); ?>"
                             data-item-height-ratio="<?php echo esc_attr($grid_layout[$index]['item_height_ratio']); ?>"
                        >
                            <div class="grid-post-item thumbnail custom-content" data-thumbnail-only="1">
                                <div class="thumbnail-image">
                                    <?php echo Grid_Plus_Base::content_block($custom_content); ?>
                                </div>
                            </div>
                        </div>
                        <?php
                        $index++;
                        $custom_content_numb--;
                    endwhile;
                endif;
            elseif($layout_type == 'metro' && $custom_content_enable == 'true' && $custom_content_numb > 0):
                while ($custom_content_numb > 0): ?>
                    <div class="item" data-gs-x="<?php echo esc_attr($grid_layout[$index]['x']); ?>"
                         data-gs-y="<?php echo esc_attr($grid_layout[$index]['y']); ?>"
                         data-gs-width="<?php echo esc_attr($grid_layout[$index]['width']); ?>"
                         data-gs-height="<?php echo esc_attr($grid_layout[$index]['height']); ?>"
                         data-skin="<?php echo esc_attr($grid_layout[$index]['skin']); ?>"

                         data-desktop-gs-x="<?php echo esc_attr($grid_layout[$index]['x']); ?>"
                         data-desktop-gs-y="<?php echo esc_attr($grid_layout[$index]['y']); ?>"
                         data-desktop-gs-width="<?php echo esc_attr($grid_layout[$index]['width']); ?>"
                         data-desktop-gs-height="<?php echo esc_attr($grid_layout[$index]['height']); ?>"
                         data-desktop-skin="<?php echo esc_attr($grid_layout[$index]['skin']); ?>"

                         data-image-width="<?php echo esc_attr($grid_layout[$index]['width']); ?>"
                         data-image-height="<?php echo esc_attr($grid_layout[$index]['height']); ?>"
                         data-item-width-ratio="<?php echo esc_attr($grid_layout[$index]['item_width_ratio']); ?>"
                         data-item-height-ratio="<?php echo esc_attr($grid_layout[$index]['item_height_ratio']); ?>"
                    >
                        <div class="grid-post-item thumbnail custom-content" data-thumbnail-only="1">
                            <div class="thumbnail-image">
                                <?php echo Grid_Plus_Base::content_block($custom_content); ?>
                            </div>
                        </div>
                    </div>
                    <?php
                    $index++;
                    $custom_content_numb--;
                endwhile;
            endif;
            wp_reset_postdata();
            ?>
        </div>

        <?php
        if ($pagination_type != 'show_all' ) {
            Grid_Plus_Base::gf_get_template('shortcodes/templates/' . $pagination_type, array(
                    'item_per_page'      => $item_per_page,
                    'total_post'         => $total_post,
                    'current_page'       => $current_page,
                    'data_section_id'    => $section_id,
                    'page_next_text'     => $grid_config['page_next_text'],
                    'page_prev_text'     => $grid_config['page_prev_text'],
                    'page_loadmore_text' => $grid_config['page_loadmore_text'],
                    'gutter' => $gutter
                )
            );
        }
        ?>
    </div>
</div>