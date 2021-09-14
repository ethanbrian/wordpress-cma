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

$grid_plus = new Grid_Plus();
$skin_css = $grid_plus->get_skin_css($justified_skin);
if (isset($skin_css) && $skin_css != '') {
    wp_enqueue_style($justified_skin, str_replace('\\"', '', $skin_css));
}
$terms = Grid_Plus_Base::gf_get_categories_info($post_type, $taxonomies);
?>
    <div class="grid-plus-container grid-<?php echo esc_attr($section_id); ?> <?php echo esc_attr($post_type); ?><?php if('true' == $cate_multi_line): ?> grid-cate-multi-line<?php endif; ?>"
         id="<?php echo esc_attr($section_id); ?>"
         data-grid-name="<?php echo esc_attr($name); ?>"
         data-animation="<?php echo esc_attr($grid_config['animation_type']); ?>">
        <div class="justified-container grid-plus-inner"
             data-ajax-url="<?php echo esc_url(admin_url('admin-ajax.php')) ?>"
             data-grid-id="<?php echo esc_attr($grid['id']); ?>"
             data-current-category="<?php echo implode(",", $all_taxonomies); ?>"
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
                    'categories'    => $category_taxonomy,
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
                for($i = $offset; $i < $max_item_index; $i++) {
                    if ($total_item > 0 && ($offset + $index > ($total_item - 1))) {
                        break;
                    }
                    $term = $terms[$i];
                    $post_thumbnail_id = $width = $height = $width_crop = $height_crop = 0;
                    $thumbnail = $img_origin = '';

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
                        $item_template = $grid_plus->get_skin_template($justified_skin);
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
            <?php
            Grid_Plus_Base::gf_get_template('shortcodes/templates/infinite-scroll', array(
                    'item_per_page'   => $item_per_page,
                    'total_post'      => $total_tax,
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