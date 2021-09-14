<?php
/**
 * Created by PhpStorm.
 * User: phuongth
 * Date: 12/16/2016
 * Time: 3:37 PM
 */
$grid_id = isset($_GET['grid_id']) ? $_GET['grid_id'] : '';
$grid = get_option(G5PLUS_GRID_OPTION_KEY . '_' . $grid_id, array(
    'id'               => $grid_id,
    'name'             => '',
    'grid_config'      => '',
    'grid_data_source' => '',
    'grid_layout'      => ''
));
$autoplay_time = isset($grid['grid_config']['autoplay_time']) ? $grid['grid_config']['autoplay_time'] : 3000;
$carousel_total_item = isset($grid['grid_config']['carousel_total_item']) ? $grid['grid_config']['carousel_total_item'] : '';
$carousel_width_ratio = isset($grid['grid_config']['carousel_width_ratio']) ? $grid['grid_config']['carousel_width_ratio'] : 3;
$carousel_height_ratio = isset($grid['grid_config']['carousel_height_ratio']) ? $grid['grid_config']['carousel_height_ratio'] : 2;
$carousel_next_text = isset($grid['grid_config']['carousel_next_text']) ? $grid['grid_config']['carousel_next_text'] : '<i class=&quot;fa fa-angle-right&quot;></i>';
$carousel_prev_text = isset($grid['grid_config']['carousel_prev_text']) ? $grid['grid_config']['carousel_prev_text'] : '<i class=&quot;fa fa-angle-left&quot;></i>';

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
$carousel_mobile_width = isset($grid['grid_config']['carousel_mobile_col']) ? $grid['grid_config']['carousel_mobile_col'] : 0;

$carousel_nav_position = isset($grid['grid_config']['carousel_nav_position']) ? $grid['grid_config']['carousel_nav_position'] : 'nav-center';
$carousel_nav_style = isset($grid['grid_config']['carousel_nav_style']) ? $grid['grid_config']['carousel_nav_style'] : 'nav-square';
?>
<div class="grid-plus-container">
    <?php Grid_Plus_Base::gf_get_template('partials/bar/submit-bar'); ?>
    <div class="form-groups" id="form_layout_pagination">
        <div class="col-md-6 col-xs-12">
            <div class="form-group" >
                <label for="layout_carousel_total_items" class="control-label col-xs-4"><?php esc_html_e('Total items', 'grid-plus'); ?></label>
                <div class="col-xs-8">
                    <input type="number" class="form-control" min="0" value="<?php echo esc_attr($carousel_total_item); ?>" id="layout_carousel_total_items">
                    <span class="description"><?php esc_html_e('Set empty for display all', 'grid-plus'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <label for="layout_carousel_desktop_large_col" class="control-label col-xs-4"><?php esc_html_e('Desktop large columns', 'grid-plus'); ?></label>
                <div class=" col-xs-8">
                    <input type="number" class="form-control" min="1" required value="<?php echo esc_attr($carousel_desktop_large_col); ?>" max="12" step="1" id="layout_carousel_desktop_large_col">
                    <?php esc_html_e('Min width (pixel):', 'grid-plus'); ?>
                    <input type="number" class="form-control" min="1" required value="<?php echo esc_attr($carousel_desktop_large_width); ?>" max="2000" step="1" id="layout_carousel_desktop_large_width">
                </div>
            </div>

            <div class="form-group">
                <label for="layout_carousel_desktop_medium_col" class="control-label col-xs-4"><?php esc_html_e('Desktop medium columns', 'grid-plus'); ?></label>
                <div class=" col-xs-8">
                    <input type="number" class="form-control" min="1" required value="<?php echo esc_attr($carousel_desktop_medium_col); ?>" max="12" step="1" id="layout_carousel_desktop_medium_col">
                    <?php esc_html_e('Min width (pixel):', 'grid-plus'); ?>
                    <input type="number" class="form-control" min="1" required value="<?php echo esc_attr($carousel_desktop_medium_width); ?>" max="2000" step="1" id="layout_carousel_desktop_medium_width">
                </div>
            </div>

            <div class="form-group">
                <label for="layout_carousel_desktop_small_col" class="control-label col-xs-4"><?php esc_html_e('Desktop small columns', 'grid-plus'); ?></label>
                <div class=" col-xs-8">
                    <input type="number" class="form-control" min="1" required value="<?php echo esc_attr($carousel_desktop_small_col); ?>" max="12" step="1" id="layout_carousel_desktop_small_col">
                    <?php esc_html_e('Min width (pixel):', 'grid-plus'); ?>
                    <input type="number" class="form-control" min="1" required value="<?php echo esc_attr($carousel_desktop_small_width); ?>" max="2000" step="1" id="layout_carousel_desktop_small_width">
                </div>
            </div>

            <div class="form-group">
                <label for="layout_carousel_tablet_col" class="control-label col-xs-4"><?php esc_html_e('Tablet columns', 'grid-plus'); ?></label>
                <div class=" col-xs-8">
                    <input type="number" class="form-control" min="1" required value="<?php echo esc_attr($carousel_tablet_col); ?>" max="12" step="1" id="layout_carousel_tablet_col">
                    <?php esc_html_e('Min width (pixel):', 'grid-plus'); ?>
                    <input type="number" class="form-control" min="1" required value="<?php echo esc_attr($carousel_tablet_width); ?>" max="2000" step="1" id="layout_carousel_tablet_width">
                </div>
            </div>

            <div class="form-group">
                <label for="layout_carousel_tablet_small_col" class="control-label col-xs-4"><?php esc_html_e('Tablet small columns', 'grid-plus'); ?></label>
                <div class=" col-xs-8">
                    <input type="number" class="form-control" min="1" required value="<?php echo esc_attr($carousel_tablet_small_col); ?>" max="12" step="1" id="layout_carousel_tablet_small_col">
                    <?php esc_html_e('Min width (pixel):', 'grid-plus'); ?>
                    <input type="number" class="form-control" min="1" required value="<?php echo esc_attr($carousel_tablet_small_width); ?>" max="2000" step="1" id="layout_carousel_tablet_small_width">
                </div>
            </div>

            <div class="form-group">
                <label for="layout_carousel_mobile_col" class="control-label col-xs-4"><?php esc_html_e('Mobile columns', 'grid-plus'); ?></label>
                <div class=" col-xs-8">
                    <input type="number" class="form-control" min="1" required value="<?php echo esc_attr($carousel_mobile_col); ?>" max="12" step="1" id="layout_carousel_mobile_col">
                    <?php esc_html_e('Min width (pixel):', 'grid-plus'); ?>
                    <input type="number" class="form-control" min="1" required value="<?php echo esc_attr($carousel_mobile_width); ?>" max="2000" step="1" id="layout_carousel_mobile_width">
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xs-12">
            <div class="form-group">
                <label for="layout_carousel_width_ratio" class="control-label col-xs-4"><?php esc_html_e('Image ratio (width x height)', 'grid-plus'); ?></label>
                <div class=" col-xs-8">
                    <input type="number" class="form-control" min="1" required value="<?php echo esc_attr($carousel_width_ratio); ?>" max="10" step="1" id="layout_carousel_width_ratio">
                    x
                    <input type="number" class="form-control" min="1" required value="<?php echo esc_attr($carousel_height_ratio); ?>" max="10" step="1" id="layout_carousel_height_ratio">
                </div>
            </div>

            <div class="form-group">
                <label for="layout_carousel_rtl" class="control-label col-xs-4"><?php esc_html_e('Is right to left', 'grid-plus'); ?></label>
                <div class=" col-xs-8">
                    <input type="checkbox" id="layout_carousel_rtl" <?php if(isset($grid['grid_config']['carousel_rtl']) && $grid['grid_config']['carousel_rtl']=='true' ){ echo 'checked';}?> >
                </div>
            </div>

            <div class="form-group">
                <label for="layout_loop" class="control-label col-xs-4"><?php esc_html_e('Loop', 'grid-plus'); ?></label>
                <div class=" col-xs-8">
                    <input type="checkbox" id="layout_loop" <?php if(isset($grid['grid_config']['loop']) && $grid['grid_config']['loop']=='true' ){ echo 'checked';}?> >
                </div>
            </div>

            <div class="form-group" >
                <label for="layout_center" class="control-label col-xs-4"><?php esc_html_e('Center', 'grid-plus'); ?></label>
                <div class=" col-xs-8">
                    <input type="checkbox" id="layout_center" <?php if(isset($grid['grid_config']['center']) && $grid['grid_config']['center']=='true' ){ echo 'checked';}?> >
                </div>
            </div>

            <div class="form-group">
                <label for="layout_autoplay" class="control-label col-xs-4"><?php esc_html_e('Autoplay', 'grid-plus'); ?></label>
                <div class=" col-xs-8">
                    <input type="checkbox" id="layout_autoplay" <?php if(isset($grid['grid_config']['autoplay']) && $grid['grid_config']['autoplay']=='true' ){ echo 'checked';}?> >
                </div>
            </div>

            <div class="form-group" data-depend-control="layout_autoplay" data-depend-value="true">
                <label for="layout_autoplay_hover_pause" class="control-label col-xs-4"><?php esc_html_e('Autoplay Hover Pause', 'grid-plus'); ?></label>
                <div class=" col-xs-8">
                    <input type="checkbox" id="layout_autoplay_hover_pause" <?php if(isset($grid['grid_config']['autoplay_hover_pause']) && $grid['grid_config']['autoplay_hover_pause']=='true' ){ echo 'checked';}?> >
                </div>
            </div>

            <div class="form-group" data-depend-control="layout_autoplay" data-depend-value="true">
                <label for="layout_autoplay_time" class="control-label col-xs-4"><?php esc_html_e('Autoplay Time', 'grid-plus'); ?></label>
                <div class=" col-xs-8">
                    <input type="number" class="form-control" min="1000" required value="<?php echo esc_attr($autoplay_time); ?>" max="10000" step="500" id="layout_autoplay_time">
                    <?php esc_html_e('( miliseconds )', 'grid-plus'); ?>
                </div>
            </div>

            <div class="form-group" >
                <label for="layout_show_dot" class="control-label col-xs-4"><?php esc_html_e('Show dot navigation', 'grid-plus'); ?></label>
                <div class=" col-xs-8">
                    <input type="checkbox" id="layout_show_dot" <?php if(isset($grid['grid_config']['show_dot']) && $grid['grid_config']['show_dot']=='true' ){ echo 'checked';}?> >
                </div>
            </div>

            <div class="form-group" >
                <label for="layout_show_nav" class="control-label col-xs-4"><?php esc_html_e('Show prev/next navigation', 'grid-plus'); ?></label>
                <div class=" col-xs-8">
                    <input type="checkbox" id="layout_show_nav" <?php if(isset($grid['grid_config']['show_nav']) && $grid['grid_config']['show_nav']=='true' ){ echo 'checked';}?> >
                </div>
            </div>

            <div class="form-group" data-depend-control="layout_show_nav" data-depend-value="true">
                <label for="layout_carousel_nav_position" class="col-xs-4 control-label"><?php esc_html_e('Navigation Position','grid-plus') ?></label>
                <div class="col-xs-8">
                    <select class="form-control" id="layout_carousel_nav_position" data-selected="<?php echo esc_attr($carousel_nav_position); ?>"
                            placeholder="<?php esc_attr_e('Select Navigation Position','grid-plus'); ?>">
                        <option value="nav-top-right"><?php esc_html_e('Top Right', 'grid-plus'); ?></option>
                        <option value="nav-center"><?php esc_html_e('Center', 'grid-plus'); ?></option>
                        <option value="nav-bottom-left"><?php esc_html_e('Bottom Left', 'grid-plus'); ?></option>
                        <option value="nav-bottom-center"><?php esc_html_e('Bottom Center', 'grid-plus'); ?></option>
                        <option value="nav-bottom-right"><?php esc_html_e('Bottom Right', 'grid-plus'); ?></option>
                    </select>
                </div>
            </div>

            <div class="form-group" data-depend-control="layout_show_nav" data-depend-value="true">
                <label for="layout_carousel_nav_style" class="col-xs-4 control-label"><?php esc_html_e('Navigation Style','grid-plus') ?></label>
                <div class="col-xs-8">
                    <select class="form-control" id="layout_carousel_nav_style" data-selected="<?php echo esc_attr($carousel_nav_style); ?>"
                            placeholder="<?php esc_attr_e('Select Navigation Style','grid-plus'); ?>">
                        <option value="nav-circle"><?php esc_html_e('Circle', 'grid-plus'); ?></option>
                        <option value="nav-square"><?php esc_html_e('Square', 'grid-plus'); ?></option>
                    </select>
                </div>
            </div>

            <div class="form-group "  data-depend-control="layout_show_nav" data-depend-value="true">
                <label class="control-label col-xs-4"><?php esc_html_e('Next Text', 'grid-plus'); ?></label>
                <div class="col-xs-8">
                    <input type="text"  id="layout_carousel_next_text" value="<?php echo esc_attr($carousel_next_text); ?>">
                </div>
            </div>

            <div class="form-group "  data-depend-control="layout_show_nav" data-depend-value="true">
                <label class="control-label col-xs-4"><?php esc_html_e('Previous Text', 'grid-plus'); ?></label>
                <div class="col-xs-8">
                    <input type="text"  id="layout_carousel_prev_text" value="<?php echo esc_attr($carousel_prev_text); ?>">
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>

