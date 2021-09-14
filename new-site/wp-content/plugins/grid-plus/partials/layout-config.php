<?php
/**
 * Created by PhpStorm.
 * User: phuongth
 * Date: 12/15/2016
 * Time: 9:01 AM
 */

$grid_id = isset($_GET['grid_id']) ? $_GET['grid_id'] : '';
$clone = isset($_GET['clone']) ? $_GET['clone'] : '';
$grid_name_clone = isset($_GET['grid_name']) ? $_GET['grid_name'] : '';


$grid = get_option(G5PLUS_GRID_OPTION_KEY . '_' . $grid_id, array(
    'id'               => $grid_id,
    'name'             => '',
    'grid_config'      => '',
    'grid_data_source' => '',
    'grid_layout'      => ''
));
$layout_type = array(
    'grid'    => esc_html__('Grid', 'grid-plus'),
    'masonry' => esc_html__('Masonry', 'grid-plus'),
    'metro'   => esc_html__('Metro', 'grid-plus'),
    'carousel'   => esc_html__('Carousel', 'grid-plus'),
    'justified'   => esc_html__('Justified', 'grid-plus')
);
$cols = array(
    '2' => esc_html__('2 Columns', 'grid-plus'),
    '3' => esc_html__('3 Columns', 'grid-plus'),
    '4' => esc_html__('4 Columns', 'grid-plus'),
    '5' => esc_html__('5 Columns', 'grid-plus'),
    '6' => esc_html__('6 Columns', 'grid-plus')
);
$layout_id = $clone == 'true' ? '' : $grid_id;
$grid_name = $clone == 'true' ? $grid_name_clone : $grid['name'];
$grid_name = $grid_name != '' ? $grid_name : esc_html__('New Grid', 'grid-plus');

$justified_row_height = isset($grid['grid_config']['justified_row_height']) ? $grid['grid_config']['justified_row_height'] : '100';
$gutter = isset($grid['grid_config']['gutter']) ? $grid['grid_config']['gutter'] : '0';
?>
<div class="grid-plus-container">
    <?php Grid_Plus_Base::gf_get_template('partials/bar/submit-bar'); ?>
        <div class="form-groups" id="form_layout_info">
            <div class="col-md-6">
                <div class="form-group  ">
                    <label class="control-label col-xs-4"><?php esc_html_e('Grid ID', 'grid-plus'); ?></label>
                    <div class="col-xs-8">
                        <input type="text" readonly id="layout_id" value="<?php echo esc_attr($layout_id); ?>">
                    </div>
                </div>
                <div class="form-group  ">
                    <label class="control-label col-xs-4"><?php esc_html_e('Grid Shortcode', 'grid-plus'); ?></label>
                    <div class="col-xs-8">
                        <div>
                            <span
                                id="layout_shortcode"><?php echo '[grid_plus name="' . $grid_name . '"]'; ?></span>
                            <a class="copy-clipboard" href="javascript:;"
                               title="<?php esc_attr_e('Copy to clipboard', 'grid-plus'); ?>"
                               data-clipboard-target="#layout_shortcode"><i
                                    class="fa fa-clipboard"></i></a>
                        </div>
                        <span
                            class="description"><?php esc_html_e('Click icon clipboard to copy and paste shortcode to page or anywhere', 'grid-plus'); ?></span>
                    </div>
                </div>
                <div class="form-group  ">
                    <label class="control-label col-xs-4"
                           for="layout_name"><?php esc_html_e('Grid name', 'grid-plus'); ?></label>
                    <div class="col-xs-8">
                        <input class="form-control" id="layout_name" value="<?php echo esc_attr($grid_name); ?>"
                               type="text" required>
                        <span class="description"><?php esc_html_e('Grid name is identity', 'grid-plus'); ?></span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="layout_type"
                           class="col-xs-4 control-label"><?php esc_html_e('Grid type', 'grid-plus'); ?></label>
                    <div class="col-xs-8">
                        <select id="layout_type" class="form-control ">
                            <?php foreach ($layout_type as $key => $val) { ?>
                                <option
                                    value="<?php echo esc_attr($key); ?>" <?php if (isset($grid['grid_config']['type']) && $grid['grid_config']['type'] == $key) {
                                    echo 'selected';
                                }; ?> >
                                    <?php echo esc_html($val); ?>
                                </option>
                            <?php }; ?>
                        </select>
                        <span
                            class="description"><?php esc_html_e('Please click generate layout after change layout type to update item width', 'grid-plus'); ?></span>
                    </div>
                </div>
                <div class="form-group" data-depend-control="layout_type" data-depend-value="grid,masonry,metro">
                    <label for="layout_col"
                           class="col-xs-4 control-label"><?php esc_html_e('Grid columns', 'grid-plus'); ?></label>
                    <div class="col-xs-8">
                        <select id="layout_col" class="form-control ">
                            <?php foreach ($cols as $key => $val) { ?>
                                <option
                                    value="<?php echo esc_attr($key); ?>" <?php if (isset($grid['grid_config']['columns']) && $grid['grid_config']['columns'] == $key) {
                                    echo 'selected';
                                }; ?> >
                                    <?php echo esc_html($val); ?>
                                </option>
                            <?php }; ?>
                        </select>
                        <span class="description"><?php esc_html_e('Please click generate layout after change layout type to update grid column', 'grid-plus'); ?></span>
                    </div>
                </div>

                <!-- for justified -->

                <div class="form-group" data-depend-control="layout_type" data-depend-value="justified">
                    <label for="layout_justified_row_height" class="control-label col-xs-4"><?php esc_html_e('Row height', 'grid-plus'); ?></label>
                    <div class=" col-xs-8">
                        <input type="number" class="form-control" min="50" required value="<?php echo esc_attr($justified_row_height); ?>" step="5" id="layout_justified_row_height">
                    </div>
                </div>

                <div class="form-group">
                    <label for="layout_gutter" class="control-label col-xs-4"><?php esc_html_e('Columns Gutter', 'grid-plus'); ?></label>
                    <div class="col-xs-8">
                        <input type="number" class="form-control" min="0" max="70" value="<?php echo esc_attr($gutter); ?>" step="5" id="layout_gutter">
                        <span class="description"><?php esc_html_e('Please just enter the numbers divisible by 5 (example: 0, 5, 10, ...)', 'grid-plus'); ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group" data-depend-control="layout_type" data-depend-value="grid,masonry,metro">
                    <label for="layout_fix_item_height"
                           class="control-label col-xs-4"><?php esc_html_e('Fixed item height', 'grid-plus'); ?></label>
                    <div class=" col-xs-8">
                        <input type="checkbox"
                               id="layout_fix_item_height" <?php if (isset($grid['grid_config']['fix_item_height']) && $grid['grid_config']['fix_item_height'] == 'true') {
                            echo 'checked';
                        } ?> >
                    </div>
                </div>

                <div class="form-group" >
                    <label for="layout_crop_image"
                           class="control-label col-xs-4"><?php esc_html_e('Dynamic Crop Image', 'grid-plus'); ?></label>
                    <div class=" col-xs-8">
                        <input type="checkbox"
                               id="layout_crop_image" <?php if (isset($grid['grid_config']['crop_image']) && $grid['grid_config']['crop_image'] == 'true') {
                            echo 'checked';
                        } ?> >
                    </div>
                </div>

                <div class="form-group" >
                    <label for="layout_disable_link"
                           class="control-label col-xs-4"><?php esc_html_e('Disable Link', 'grid-plus'); ?></label>
                    <div class=" col-xs-8">
                        <input type="checkbox"
                               id="layout_disable_link" <?php if (isset($grid['grid_config']['disable_link']) && $grid['grid_config']['disable_link'] == 'true') {
                            echo 'checked';
                        } ?> >
                    </div>
                </div>

                <div class="form-group" data-depend-control="layout_type" data-depend-value="metro">
                    <div class="form-group" >
                        <label for="layout_custom_content_enable"
                               class="control-label col-xs-4"><?php esc_html_e('Custom Content an item', 'grid-plus'); ?></label>
                        <div class="col-xs-8">
                            <input type="checkbox"
                                   id="layout_custom_content_enable" <?php if (isset($grid['grid_config']['custom_content_enable']) && $grid['grid_config']['custom_content_enable'] == 'true') {
                                echo 'checked';
                            } ?> >
                        </div>
                    </div>
                    <div class="form-group" data-depend-control="layout_custom_content_enable" data-depend-value="true">
                        <label for="layout_custom_content"
                               class="control-label col-xs-4 mg-bottom-20"><?php esc_html_e('Custom Content', 'grid-plus'); ?></label>
                        <div class="col-xs-8">
                            <?php /*$content = isset($grid['grid_config']['custom_content']) ? $grid['grid_config']['custom_content'] : '';
                            wp_editor(wp_kses_post($content), 'layout_custom_content'); */?>
                            <?php $post_type = apply_filters('grid_plus_content_post_type', 'page');
                            if (isset($grid['grid_config']['custom_content']) && $grid['grid_config']['custom_content'] !='') {
                                $args = array(
                                    'post_type'      => $post_type,
                                    'posts_per_page' => -1,
                                    'post__in'       => array($grid['grid_config']['custom_content'])
                                );
                                $custom_content = new WP_Query($args);
                            }?>
                            <select data-url="<?php echo esc_url(admin_url('admin-ajax.php')); ?>"
                                    class="form-control manual ajax-search" id="layout_custom_content" data-post-type="<?php echo esc_attr($post_type); ?>"
                                    placeholder="<?php esc_attr_e('Select content by input post title and press enter','grid-plus'); ?>"
                            >
                                <?php if (isset($custom_content)) {
                                    while ($custom_content->have_posts()) : $custom_content->the_post();
                                        ?>
                                        <option value="<?php the_ID(); ?>" selected><?php the_title(); ?></option>
                                    <?php endwhile;
                                    wp_reset_postdata();
                                } ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    <div style="clear: both"></div>
</div>