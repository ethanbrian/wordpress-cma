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
 */

global $taxonomy__in;
$grid_data_source = $grid['grid_data_source'];
$post_type = $grid_data_source['post_type'];

$category_taxonomy = Grid_Plus_Base::gf_get_category_taxonomy($post_type);

$taxonomies = isset($grid_data_source['categories']) && $grid_data_source['categories'] != '' ? $grid_data_source['categories'] : array();
$show_category = $grid_data_source['show_category'];
$cate_multi_line = 'false';
if(isset($grid_data_source['cate_multi_line']) && !empty($grid_data_source['cate_multi_line'])) {
    $cate_multi_line = $grid_data_source['cate_multi_line'];
}
if(count($taxonomies)==0){
    $taxonomies_info = Grid_Plus_Base::gf_get_categories_info_by_posttype($post_type);
    foreach($taxonomies_info as $tax){
        if(!isset($taxonomy__in) || (is_array($taxonomy__in) && count($taxonomy__in) <=0) || in_array($tax['taxonomy'], $taxonomy__in)) {
            $taxonomies[] = $tax['term_id'];
        }
    }
}
if('attachment' == $post_type || count($category_taxonomy) == 0 && count($taxonomies) == 0) {
    echo '<div class="grid-plus-empty">'.esc_html__('No item found!', 'grid-plus').'</div>';
    return;
}

$all_taxonomies = array();
foreach ($category_taxonomy as $tax) {
    $all_taxonomies[] = $tax['name'];
}

$grid_config = $grid['grid_config'];
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
$fix_item_height = isset($grid_config['fix_item_height']) ? $grid_config['fix_item_height'] : 0;
$crop_image = isset($grid_config['crop_image']) ? $grid_config['crop_image'] : 'false';
$crop_image = 'true' == $crop_image ? true : false;
$disable_link = isset($grid_config['disable_link']) ? $grid_config['disable_link'] : 'false';

$custom_content_enable = isset($grid_config['custom_content_enable']) ? $grid_config['custom_content_enable'] : 'false';
$custom_content = isset($grid_config['custom_content']) ? $grid_config['custom_content'] : '';
$custom_content_numb = isset($grid_config['custom_content_numb']) ? $grid_config['custom_content_numb'] : '0';

$item_per_page = count($grid_layout) > $item_per_page ? count($grid_layout) : $item_per_page;
if($layout_type == 'metro' && $custom_content_enable == 'true') {
    $item_per_page -= intval($custom_content_numb);
}

$item_per_page = ($total_item > 0 && ($item_per_page < 0 || $total_item < $item_per_page)) ? intval($total_item) : intval($item_per_page);

if ($layout_type != 'metro' && $pagination_type == 'show_all' && !$pagination_none) {
    if($total_item < 0) {
        $item_per_page = -1;
    } else {
        $item_per_page = $total_item;
    }
}
$offset = 0;
$total_tax = count($taxonomies);
// limit total post by get total item from pagination config
if ($total_item > 0 && $total_item <= $total_tax) {
    $total_tax = $total_item;
}
$max_item_index = $total_tax;
if ($item_per_page > 0) {
    $offset = ($current_page - 1) * $item_per_page;
    $max_item_index = ($total_item > 0 && $current_page * $item_per_page > $total_tax) ? $total_tax : $current_page * $item_per_page;
}
$grid_full_layout = array();
$ajax_nonce = wp_create_nonce("grid-plus-category");
$grid_stack_class = ($layout_type == 'metro' && $columns != 5) ? 'grid-stack' : 'grid-stack grid-stack-' . $columns;
$terms = Grid_Plus_Base::gf_get_categories_info($post_type, $taxonomies);
if($max_item_index > count($terms)) $max_item_index = count($terms);
?>
    <div class="grid-plus-container grid-<?php echo esc_attr($section_id); ?> <?php echo esc_attr($post_type); ?> <?php if('true' == $cate_multi_line): ?> grid-cate-multi-line<?php endif; ?>"
         id="<?php echo esc_attr($section_id); ?>" data-grid-name="<?php echo esc_attr($name); ?>"
         data-animation="<?php echo esc_attr($grid_config['animation_type']); ?>">
        <div class="grid-stack-container grid-plus-inner"
             data-ajax-url="<?php echo esc_url(admin_url('admin-ajax.php')) ?>"
             data-grid-id="<?php echo esc_attr($grid['id']); ?>"
             data-current-category="<?php echo implode(",", $all_taxonomies); ?>"
             data-section-id="<?php echo esc_attr($section_id); ?>"
             data-gutter="<?php echo esc_attr($gutter); ?>"
             data-columns="<?php echo esc_attr($columns); ?>"
             data-height-ratio="<?php echo esc_attr($height_ratio); ?>"
             data-width-ratio="<?php echo esc_attr($width_ratio); ?>"
             data-desktop-columns="<?php echo esc_attr($columns); ?>"
             data-tablet-columns="2" data-mobile-columns="1"
             data-layout-type="<?php echo esc_attr($layout_type); ?>"
             data-source-type="<?php echo esc_attr($source_type); ?>"
             data-fix-item-height="<?php echo esc_attr($fix_item_height); ?>"
             data-nonce="<?php echo esc_attr($ajax_nonce); ?>">
            <?php if (isset($show_category) && $show_category != '' && $show_category != 'none') { // && $post_type!='attachment'
                Grid_Plus_Base::gf_get_template('shortcodes/templates/category', array(
                    'section_id'    => $section_id,
                    'post_type'     => $post_type,
                    'categories'    => $category_taxonomy,
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
                $crop_size = 600;
                $grid_plus = new Grid_Plus();
                for($i = $offset; $i < $max_item_index; $i++) {
                    $item_skin = isset($grid_layout[$index]['skin']) ? $grid_layout[$index]['skin'] : 'thumbnail';
                    while($layout_type == 'metro' && $custom_content_enable == 'true' && $item_skin == 'custom-content') :?>
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
                    endwhile;
                    $item_skin = isset($grid_layout[$index]['skin']) ? $grid_layout[$index]['skin'] : 'thumbnail';
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
                    $term = $terms[$i];
                    $post_thumbnail_id = $width = $height = $width_crop = $height_crop = 0;
                    $img_origin = $thumbnail = '';
                    $title = $term['name'];
                    $excerpt = $term['description'];
                    $post_thumbnail_id = get_post_thumbnail_id($term['term_id']);
                    if(!empty($post_thumbnail_id)){
                        Grid_Plus_Base::gf_get_attachment_image($post_thumbnail_id, $crop_image, $crop_size, $width_crop, $height_crop, $width, $height, $img_origin, $thumbnail);
                    }

                    $thumbnail = isset($thumbnail) && $thumbnail != '' ? $thumbnail : $img_origin;
                    $cat_filter = $term['slug'];
                    $cat = $term['name'];
                    $ico_gallery = apply_filters('grid_plus_icon_gallery', 'fa fa-search');

                    $post_link = $term['link'];
                    $grid_full_layout[] = array(
                        'y'      => $grid_layout[$index]['y'],
                        'height' => $grid_layout[$index]['height']
                    );

                    $skin_css = $grid_plus->get_skin_css($item_skin);
                    if (isset($skin_css) && $skin_css != '') {
                        wp_enqueue_style($grid_layout[$index]['skin'], str_replace('\\"', '', $skin_css));
                    }
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

                    if (count($grid_layout) == $index) {
                        $max_y = intval($grid_full_layout[(count($grid_full_layout) - 1)]['y']) + intval($grid_full_layout[(count($grid_full_layout) - 1)]['height']);
                        $index = 0;
                    }
                }
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
                ?>
            </div>

            <?php
            if ($pagination_type != 'show_all') {
                Grid_Plus_Base::gf_get_template('shortcodes/templates/' . $pagination_type, array(
                        'item_per_page'      => $item_per_page,
                        'total_post'         => $total_tax,
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
<?php