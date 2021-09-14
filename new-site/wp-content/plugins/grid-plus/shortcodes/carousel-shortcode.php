<?php
/**
 * Created by PhpStorm.
 * User: phuongth
 * Date: 2/14/2017
 * Time: 2:29 PM
 * @var $name
 */
$section_id = uniqid();
$grid = Grid_Plus_Base::gf_get_grid_by_name($name);

Grid_Plus_Base::gf_enqueue_custom_css($section_id, $grid['grid_config']);
$current_page = isset($current_page) ? $current_page : 1;
$post_not_in = isset($post_not_in) ? $post_not_in : '';
$post_not_in = explode(',', $post_not_in);
$source_type = 'posts';
if(isset($grid['grid_data_source']['source_type']) && !empty($grid['grid_data_source']['source_type'])) {
    $source_type = $grid['grid_data_source']['source_type'];
}
Grid_Plus_Base::gf_get_template('shortcodes/carousel-templates/carousel-'.$source_type, array(
    'section_id'    => $section_id,
    'name'     => $name,
    'grid'    => $grid,
    'current_page' => $current_page,
    'source_type' => $source_type,
    'post_not_in' => $post_not_in
));