<?php
/**
 * Created by PhpStorm.
 * User: phuongth
 * Date: 1/3/2017
 * Time: 9:33 AM
 * @var $section_id
 * @var $post_type
 * @var $categories
 * @var $show_category
 * @var $source_type
 * @var $cate_multi_line
 */
global $category__in, $taxonomy__in;;
$terms = $taxonomies = array();
if('posts' == $source_type) {
    $terms = Grid_Plus_Base::gf_get_categories_info($post_type, $categories);
} else {
    $taxonomies = $categories;
    $categories = array();
    foreach ($taxonomies as $tax) {
        $categories[] = $tax['name'];
    }
}
global $grid_plus_custom_css;
$spin_color = '#5d97af';
if (isset($grid_plus_custom_css) && is_array($grid_plus_custom_css)) {
    foreach ($grid_plus_custom_css as $section => $grid_config) {
        if (isset($grid_config['category_hover_color']) && $grid_config['category_hover_color'] != '') {
            $spin_color = $grid_config['category_hover_color'];
        }
    }
}
$category_active = '';
if (isset($category__in) && is_array($category__in) && count($category__in) > 0) {
    $category_active = $category__in[0];
}

$taxonomy_active = '';
if (isset($taxonomy__in) && is_array($taxonomy__in) && count($taxonomy__in) > 0) {
    $taxonomy_active = $taxonomy__in[0];
}

?>
<div class="grid-category <?php if('false' === $cate_multi_line): ?>hidden <?php endif; ?><?php echo esc_attr($show_category); ?>" data-section-id="<?php echo esc_attr($section_id); ?>">
    <a href="javascript:;" class="ladda-button<?php if(empty($category_active) && empty($taxonomy_active)): ?> active<?php endif; ?>"
       data-category="<?php echo implode(",", $categories); ?>" data-style="zoom-in"
       data-spinner-color="<?php echo esc_attr($spin_color); ?>">
        <?php esc_html_e('All', 'grid-plus'); ?>
    </a>
    <?php if('posts' == $source_type) : ?>
        <?php for($index = 0; $index < count($terms); $index++) { ?>
            <a href="javascript:;" class="ladda-button<?php if(!empty($category_active) && ($category_active == $terms[$index]['term_id'])): ?> active<?php endif; ?>"
               data-category="<?php echo esc_attr($terms[$index]['term_id']) ?>" data-style="zoom-in"
               data-spinner-color="<?php echo esc_attr($spin_color); ?>">
                <?php echo wp_kses_post($terms[$index]['name']) ?>
            </a>
        <?php } ?>
    <?php else: ?>
        <?php for($index = 0; $index < count($taxonomies); $index++) { ?>
            <a href="javascript:;" class="ladda-button<?php if(!empty($taxonomy_active) && ($taxonomy_active == $taxonomies[$index]['name'])): ?> active<?php endif; ?>"
               data-category="<?php echo esc_attr($taxonomies[$index]['name']) ?>" data-style="zoom-in"
               data-spinner-color="<?php echo esc_attr($spin_color); ?>">
                <?php echo wp_kses_post($taxonomies[$index]['label']) ?>
            </a>
        <?php } ?>
    <?php endif; ?>
    <div class="grid-cate-expanded hidden">
        <span class="grid-dropdown-toggle">+</span>
        <ul class="grid-dropdown-menu"></ul>
    </div>
</div>
