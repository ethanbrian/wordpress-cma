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
if(count($taxonomies)==0 && isset($show_category) && $show_category!='none'){
    $taxonomies_info = Grid_Plus_Base::gf_get_categories_info_by_posttype($post_type);
    foreach($taxonomies_info as $tax){
        if(!isset($taxonomy__in) || (is_array($taxonomy__in) && count($taxonomy__in) <=0) || in_array($tax['taxonomy'], $taxonomy__in)) {
            $taxonomies[] = $tax['term_id'];
        }
    }
}
if('attachment' == $post_type || count($category_taxonomy) == 0 || count($taxonomies) == 0) {
    echo '<div class="grid-plus-empty">'.esc_html__('No item found!', 'grid-plus').'</div>';
    return;
}

$all_taxonomies = array();
foreach ($category_taxonomy as $tax) {
    $all_taxonomies[] = $tax['name'];
}

$grid_config = $grid['grid_config'];
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

if ($carousel_total_items > 0) {
    $args['posts_per_page'] = $carousel_total_items;
}


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
$terms = Grid_Plus_Base::gf_get_categories_info($post_type, $taxonomies);
?>
    <div class="grid-plus-container grid-<?php echo esc_attr($section_id); ?> <?php echo esc_attr($post_type); ?> <?php if('true' == $cate_multi_line): ?> grid-cate-multi-line<?php endif; ?>"
         id="<?php echo esc_attr($section_id); ?>"
         data-grid-name="<?php echo esc_attr($name); ?>"
         data-animation="<?php echo esc_attr($grid_config['animation_type']); ?>">
        <div class="grid-carousel-container grid-plus-inner"
             data-ajax-url="<?php echo esc_url(admin_url('admin-ajax.php')) ?>"
             data-grid-id="<?php echo esc_attr($grid['id']); ?>"
             data-current-category="<?php echo implode(",", $all_taxonomies); ?>"
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
                    'categories'    => $category_taxonomy,
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
                foreach ($terms as $term) {
                    $post_thumbnail_id = $width = $height = $width_crop = $height_crop = 0;
                    $img_origin = $thumbnail = '';
                    $title = $term['name'];
                    $excerpt = $term['description'];
                    $post_thumbnail_id = get_post_thumbnail_id(get_the_ID());
                    if(!empty($post_thumbnail_id)){
                        Grid_Plus_Base::gf_get_attachment_image($post_thumbnail_id, $crop_image, $crop_size, $width_crop, $height_crop, $width, $height, $img_origin, $thumbnail);
                    }
                    $cat_filter = $term['slug'];
                    $cat = $term['name'];

                    $ico_gallery = apply_filters('grid_plus_icon_gallery', 'fa fa-search');
                    $post_link = $term['link'];
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
                }
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