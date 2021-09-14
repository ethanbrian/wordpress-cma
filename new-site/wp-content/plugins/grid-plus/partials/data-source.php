<?php
/**
 * Created by PhpStorm.
 * User: phuongth
 * Date: 12/15/2016
 * Time: 9:01 AM
 */
$post_types = Grid_Plus_Base::gf_get_posttypes();
$post_categories = Grid_Plus_Base::gf_get_categories();
$users = Grid_Plus_Base::gf_get_users();

$grid_id = isset($_GET['grid_id']) ? $_GET['grid_id'] : '';

$grid = get_option(G5PLUS_GRID_OPTION_KEY . '_' . $grid_id, array(
    'id'               => $grid_id,
    'name'             => '',
    'grid_config'      => '',
    'grid_data_source' => '',
    'grid_layout'      => ''
));
if (isset($grid['grid_data_source']['exclude_ids']) && $grid['grid_data_source']['exclude_ids'] !='') {
    $args = array(
        'post_type'      => $grid['grid_data_source']['post_type'],
        'posts_per_page' => -1,
        'post__in'       => $grid['grid_data_source']['exclude_ids']
    );
    $exclude_posts = new WP_Query($args);
}
if (isset($grid['grid_data_source']['include_ids']) && $grid['grid_data_source']['include_ids'] !='') {
    $args = array(
        'post_type'      => $grid['grid_data_source']['post_type'],
        'posts_per_page' => -1,
        'post__in'       => $grid['grid_data_source']['include_ids']
    );
    $include_ids = new WP_Query($args);
}
$source_type = isset($grid['grid_data_source']['source_type']) ? $grid['grid_data_source']['source_type'] : 'posts';
$post_type = isset($grid['grid_data_source']['post_type']) ? $grid['grid_data_source']['post_type'] : 'post';
$attachment_type = isset($grid['grid_data_source']['attachment_type']) ? $grid['grid_data_source']['attachment_type'] : 'choose_item';
$show_category = isset($grid['grid_data_source']['show_category']) ? $grid['grid_data_source']['show_category'] : 'none';
$order = isset($grid['grid_data_source']['order']) ? $grid['grid_data_source']['order'] : 'ASC';
$order_by = isset($grid['grid_data_source']['order_by'] ) ? $grid['grid_data_source']['order_by'] : 'date';
$authors = isset($grid['grid_data_source']['authors'] ) && is_array($grid['grid_data_source']['authors']) ? implode(',', $grid['grid_data_source']['authors']) : '';
$categories = isset($grid['grid_data_source']['categories']) && is_array($grid['grid_data_source']['categories']) ? implode(',', $grid['grid_data_source']['categories']) : '';
$cate_multi_line = (isset($grid['grid_data_source']['cate_multi_line']) && $grid['grid_data_source']['cate_multi_line'] == 'true');
$custom_urls = isset($grid['grid_data_source']['custom_urls']) ? $grid['grid_data_source']['custom_urls'] : '';
?>
<div class="grid-plus-container">
    <?php Grid_Plus_Base::gf_get_template('partials/bar/submit-bar'); ?>
        <div class="form-groups">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="source_type" class="col-xs-4 control-label"><?php esc_html_e('Source type', 'grid-plus'); ?></label>
                    <div class="col-xs-8">
                        <select id="source_type" class="form-control grid-col-md-6"
                                data-selected="<?php echo esc_attr($source_type); ?>">
                            <option value="posts" <?php echo esc_attr(($source_type === 'posts') ? 'selected' : '') ; ?>><?php esc_html_e('Post items', 'grid-plus'); ?></option>
                            <option value="taxonomies" <?php echo esc_attr(($source_type === 'taxonomies') ? 'selected' : ''); ?>><?php esc_html_e('Post taxonomies', 'grid-plus'); ?></option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="layout_source" class="col-xs-4 control-label"><?php esc_html_e('Post type', 'grid-plus'); ?></label>
                    <div class="col-xs-8">
                        <select id="layout_source" class="form-control grid-col-md-6"
                                data-selected="<?php echo esc_attr($post_type); ?>">
                            <?php foreach ($post_types as $key => $value) { ?>
                                <option
                                    value="<?php echo esc_attr($key); ?>" <?php if ($post_type == $key) {
                                    echo 'selected';
                                } ?> ><?php echo esc_attr($value); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group" data-depend-control="source_type" data-depend-value="posts">
                    <div class="form-group" data-depend-control="layout_source" data-depend-value="attachment">
                        <label for="attachment_type" class="col-xs-4 control-label"><?php esc_html_e('Attachment type', 'grid-plus'); ?></label>
                        <div class="col-xs-8">
                            <select id="attachment_type" class="form-control grid-col-md-6"
                                    data-selected="<?php echo esc_attr($attachment_type); ?>">
                                <option value="choose_item" <?php echo esc_attr(($attachment_type === 'choose_item') ? 'selected' : ''); ?>><?php esc_html_e('Choose items', 'grid-plus'); ?></option>
                                <option value="choose_source" <?php echo esc_attr(($attachment_type === 'choose_source') ? 'selected' : ''); ?>><?php esc_html_e('Choose source', 'grid-plus'); ?></option>
                            </select>
                        </div>
                    </div>
                </div>
                <?php
                $images = isset($grid['grid_data_source']['grid_gallery']) ? $grid['grid_data_source']['grid_gallery'] : '';
                $images_arr = explode('|', $images);
                ?>
                <div class="form-group" data-depend-control="source_type" data-depend-value="posts">
                    <div class="form-group" data-depend-control="attachment_type" data-depend-value="choose_item">
                        <div class="form-group" data-depend-control="layout_source" data-depend-value="attachment">
                            <label class="col-xs-4 control-label"><?php esc_html_e('Select Gallery', 'grid-plus'); ?></label>
                            <div class="col-xs-8">
                                <div class="sf-field-gallery-inner">
                                    <input type="hidden" name="grid_gallery" id="grid_gallery" value="<?php echo esc_attr($images); ?>" />
                                    <?php foreach ($images_arr as $image) : ?>
                                        <?php
                                        if (empty($image)) {
                                            continue;
                                        }
                                        $image_url = '';
                                        $image_attributes = wp_get_attachment_image_src($image);
                                        if (!empty($image_attributes) && is_array($image_attributes)) {
                                            $image_url = $image_attributes[0];
                                        }
                                        ?>
                                        <div class="sf-image-preview" data-id="<?php echo esc_attr($image); ?>">
                                            <div class="centered">
                                                <img src="<?php echo esc_url($image_url); ?>"/>
                                            </div>
                                            <span class="sf-gallery-remove dashicons dashicons dashicons-no-alt"></span>
                                        </div>
                                    <?php endforeach; ?>
                                    <div class="sf-gallery-add">
                                        <?php esc_html_e('+ Add Images', 'grid-plus'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="custom_urls" class="col-xs-4 control-label"><?php esc_html_e('Custom URLs', 'grid-plus'); ?></label>
                                <div class="col-xs-8">
                                    <textarea id="custom_urls" class="form-control grid-col-md-6"><?php echo esc_attr($custom_urls); ?></textarea>
                                    <span class="description"><?php esc_html_e('The urls are separated by semicolons', 'grid-plus'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group" data-depend-control="layout_source" data-depend-dif-value="attachment">
                    <div class="form-group">
                        <label for="layout_category" class="col-xs-4 control-label"><?php esc_html_e('Specific terms', 'grid-plus'); ?></label>
                        <div class="col-xs-8">
                            <select class="form-control manual" id="layout_category" name="choices-multiple-groups"
                                    data-selected="<?php echo esc_attr($categories); ?>"
                                    placeholder="<?php esc_attr_e('Select category','grid-plus'); ?>" multiple>
                            </select>
                            <select id="layout_category_filter" class="manual" style="display: none">
                                <?php foreach ($post_categories as $post_type => $category) {
                                    foreach ($category as $key => $value) {
                                        ?>
                                        <optgroup data-post-type="<?php echo esc_attr($post_type); ?>"
                                                  label="<?php echo sprintf('%s (%s)', $key, count($value)); ?>">
                                            <?php foreach ($value as $v) { ?>
                                                <option data-taxonomy="<?php echo $v['taxonomy']; ?>"
                                                        value="<?php echo esc_attr($v['term_id']); ?>"><?php echo sprintf('%s (%s)', $v['term_name'], $v['term_count']); ?></option>
                                            <?php } ?>
                                        </optgroup>
                                    <?php }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-4 control-label" for="layout_show_category"><?php esc_html_e('Show category', 'grid-plus'); ?></label>
                        <div class="col-xs-8">
                            <select id="layout_show_category" class="form-control grid-col-md-6"
                                    data-selected="<?php echo esc_attr($show_category); ?>">
                                <option value="none"><?php esc_html_e('None', 'grid-plus'); ?></option>
                                <option value="left"><?php esc_html_e('Left', 'grid-plus'); ?></option>
                                <option value="right"><?php esc_html_e('Right', 'grid-plus'); ?></option>
                                <option value="center"><?php esc_html_e('Center', 'grid-plus'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group" data-depend-control="layout_show_category" data-depend-value="left,right,center">
                        <label for="layout_cate_multi_line"
                               class="control-label col-xs-4"><?php esc_html_e('Category filter multi line?', 'grid-plus'); ?></label>
                        <div class=" col-xs-8">
                            <input type="checkbox" id="layout_cate_multi_line" <?php echo $cate_multi_line ? 'checked' : '';?> >
                        </div>
                    </div>
                </div>

                <!--Options for posts source-->
                <div class="form-group" data-depend-control="source_type" data-depend-value="posts">
                    <div class="form-group" data-depend-control="layout_source" data-depend-dif-value="attachment">
                        <div class="form-group">
                            <label for="layout_authors" class="col-xs-4 control-label"><?php esc_html_e('Author(s)', 'grid-plus'); ?></label>
                            <div class="col-xs-8">
                                <select class="form-control" id="layout_authors" name="choices-multiple-groups"
                                        data-selected="<?php echo esc_attr($authors); ?>"
                                        placeholder="<?php esc_attr_e('Select author','grid-plus'); ?>" multiple>
                                    <?php
                                    foreach ($users as $user) { ?>
                                        <option
                                            value="<?php echo esc_attr($user->ID); ?>"><?php echo esc_attr($user->user_nicename); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-xs-4"
                                   for="layout_include_ids"><?php esc_html_e('Include Post', 'grid-plus'); ?></label>
                            <div class="col-xs-8">
                                <select data-url="<?php echo esc_url(admin_url('admin-ajax.php')); ?>"
                                        class="form-control grid-col-md-6 manual ajax-search" id="layout_include_ids"
                                        placeholder="<?php esc_attr_e('Select post by input post title and press enter','grid-plus'); ?>" multiple
                                >
                                    <?php if (isset($include_ids)) {
                                        while ($include_ids->have_posts()) : $include_ids->the_post();
                                            ?>
                                            <option value="<?php the_ID(); ?>" selected><?php the_title(); ?></option>
                                        <?php endwhile;
                                    } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-xs-4"
                                   for="layout_exclude_ids"><?php esc_html_e('Exclude Post', 'grid-plus'); ?></label>
                            <div class="col-xs-8">
                                <select data-url="<?php echo esc_url(admin_url('admin-ajax.php')); ?>"
                                        class="form-control grid-col-md-6 manual ajax-search" id="layout_exclude_ids"
                                        placeholder="<?php esc_attr_e('Select post by input post title and press enter','grid-plus'); ?>" multiple
                                >
                                    <?php if (isset($exclude_posts)) {
                                        while ($exclude_posts->have_posts()) : $exclude_posts->the_post();
                                            ?>
                                            <option value="<?php the_ID(); ?>" selected><?php the_title(); ?></option>
                                        <?php endwhile;
                                    } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" data-depend-control="layout_source" data-depend-value="attachment">
                        <div class="form-group" data-depend-control="attachment_type" data-depend-value="choose_source">
                            <div class="form-group">
                                <label for="attachment_layout_authors" class="col-xs-4 control-label"><?php esc_html_e('Author(s)', 'grid-plus'); ?></label>
                                <div class="col-xs-8">
                                    <select class="form-control" id="attachment_layout_authors" name="choices-multiple-groups"
                                            data-selected="<?php echo esc_attr($authors); ?>"
                                            placeholder="<?php esc_attr_e('Select author','grid-plus'); ?>" multiple>
                                        <?php
                                        foreach ($users as $user) { ?>
                                            <option
                                                value="<?php echo esc_attr($user->ID); ?>"><?php echo esc_attr($user->user_nicename); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" data-depend-control="layout_source" data-depend-value="post">
                        <div class="form-group">
                            <label for="layout_source_filter" class="col-xs-4 control-label"><?php esc_html_e('Source Filter', 'grid-plus'); ?></label>
                            <div class="col-xs-8">
                                <select id="layout_source_filter" class="form-control grid-col-md-6" data-selected="all" >
                                    <option value="all"><?php esc_html_e('All', 'grid-plus'); ?></option>
                                    <option value="popular"><?php esc_html_e('Popular', 'grid-plus'); ?></option>
                                    <option value="recent"><?php esc_html_e('Recent', 'grid-plus'); ?></option>
                                    <option value="oldest"><?php esc_html_e('Oldest', 'grid-plus'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" data-depend-control="layout_source_filter" data-depend-value="all">
                            <div class="form-group">
                                <label for="layout_post_order" class="col-xs-4 control-label"><?php esc_html_e('Order', 'grid-plus'); ?></label>
                                <div class="col-xs-8">
                                    <select id="layout_post_order" class="form-control grid-col-md-6" data-selected="<?php echo esc_attr($order); ?>" >
                                        <option value="ASC"><?php esc_html_e('Ascending', 'grid-plus'); ?></option>
                                        <option value="DESC"><?php esc_html_e('Descending', 'grid-plus'); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="layout_post_order_by" class="col-xs-4 control-label"><?php esc_html_e('Order By', 'grid-plus'); ?></label>
                                <div class="col-xs-8">
                                    <select class="form-control" id="layout_post_order_by" data-selected="<?php echo esc_attr($order_by); ?>"
                                            placeholder="<?php esc_attr_e('Select Order','grid-plus'); ?>">
                                        <option value="ID"><?php esc_html_e('By post id', 'grid-plus'); ?></option>
                                        <option value="author"><?php esc_html_e('By author', 'grid-plus'); ?></option>
                                        <option value="title"><?php esc_html_e('By title', 'grid-plus'); ?></option>
                                        <option value="name"><?php esc_html_e('By post name (post slug)', 'grid-plus'); ?></option>
                                        <option value="date"><?php esc_html_e('By date', 'grid-plus'); ?></option>
                                        <option value="rand"><?php esc_html_e('Random order', 'grid-plus'); ?></option>
                                        <option value="menu_order"><?php esc_html_e('By Page Order (Menu Order)', 'grid-plus'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" data-depend-control="layout_source" data-depend-value="product">
                        <div class="form-group">
                            <label for="layout_product_source_filter" class="col-xs-4 control-label"><?php esc_html_e('Source Filter', 'grid-plus'); ?></label>
                            <div class="col-xs-8">
                                <select id="layout_product_source_filter" class="form-control grid-col-md-6" data-selected="all" >
                                    <option value="all"><?php esc_html_e('All', 'grid-plus'); ?></option>
                                    <option value="sale"><?php esc_html_e('Sale Off', 'grid-plus'); ?></option>
                                    <option value="featured"><?php esc_html_e('Featured', 'grid-plus'); ?></option>
                                    <option value="top-rated"><?php esc_html_e('Top Rated', 'grid-plus'); ?></option>
                                    <option value="best-selling"><?php esc_html_e('Best Selling', 'grid-plus'); ?></option>
                                    <option value="recent"><?php esc_html_e('Recent', 'grid-plus'); ?></option>
                                    <option value="oldest"><?php esc_html_e('Oldest', 'grid-plus'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" data-depend-control="layout_product_source_filter" data-depend-value="all,sale,featured">
                            <div class="form-group">
                                <label for="layout_product_order" class="col-xs-4 control-label"><?php esc_html_e('Order', 'grid-plus'); ?></label>
                                <div class="col-xs-8">
                                    <select id="layout_product_order" class="form-control grid-col-md-6" data-selected="<?php echo esc_attr($order); ?>" >
                                        <option value="ASC"><?php esc_html_e('Ascending', 'grid-plus'); ?></option>
                                        <option value="DESC"><?php esc_html_e('Descending', 'grid-plus'); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="layout_product_order_by" class="col-xs-4 control-label"><?php esc_html_e('Order By', 'grid-plus'); ?></label>
                                <div class="col-xs-8">
                                    <select class="form-control" id="layout_product_order_by" data-selected="<?php echo esc_attr($order_by); ?>"
                                            placeholder="<?php esc_attr_e('Select Order','grid-plus'); ?>">
                                        <option value="ID"><?php esc_html_e('By post id', 'grid-plus'); ?></option>
                                        <option value="author"><?php esc_html_e('By author', 'grid-plus'); ?></option>
                                        <option value="title"><?php esc_html_e('By title', 'grid-plus'); ?></option>
                                        <option value="name"><?php esc_html_e('By post name (post slug)', 'grid-plus'); ?></option>
                                        <option value="date"><?php esc_html_e('By date', 'grid-plus'); ?></option>
                                        <option value="rand"><?php esc_html_e('Random order', 'grid-plus'); ?></option>
                                        <option value="menu_order"><?php esc_html_e('By Page Order (Menu Order)', 'grid-plus'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" data-depend-control="layout_source" data-depend-dif-value="post,product">
                        <div class="form-group" data-depend-control="layout_source" data-depend-dif-value="attachment">
                            <div class="form-group">
                                <label for="layout_order" class="col-xs-4 control-label"><?php esc_html_e('Order', 'grid-plus'); ?></label>
                                <div class="col-xs-8">
                                    <select id="layout_order" class="form-control grid-col-md-6" data-selected="<?php echo esc_attr($order); ?>" >
                                        <option value="ASC"><?php esc_html_e('Ascending', 'grid-plus'); ?></option>
                                        <option value="DESC"><?php esc_html_e('Descending', 'grid-plus'); ?></option>
                                    </select>
                                    <span
                                        class="description"><?php esc_html_e('Don\'t apply when choose include posts', 'grid-plus'); ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="layout_order_by" class="col-xs-4 control-label"><?php esc_html_e('Order By', 'grid-plus'); ?></label>
                                <div class="col-xs-8">
                                    <select class="form-control" id="layout_order_by" data-selected="<?php echo esc_attr($order_by); ?>"
                                            placeholder="<?php esc_attr_e('Select Order','grid-plus'); ?>">
                                        <option value="ID"><?php esc_html_e('By post id', 'grid-plus'); ?></option>
                                        <option value="author"><?php esc_html_e('By author', 'grid-plus'); ?></option>
                                        <option value="title"><?php esc_html_e('By title', 'grid-plus'); ?></option>
                                        <option value="name"><?php esc_html_e('By post name (post slug)', 'grid-plus'); ?></option>
                                        <option value="date"><?php esc_html_e('By date', 'grid-plus'); ?></option>
                                        <option value="rand"><?php esc_html_e('Random order', 'grid-plus'); ?></option>
                                        <option value="menu_order"><?php esc_html_e('By Page Order (Menu Order)', 'grid-plus'); ?></option>
                                    </select>
                                    <span class="description"><?php esc_html_e('Don\'t apply when choose include posts', 'grid-plus'); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" data-depend-control="layout_source" data-depend-value="attachment">
                            <div class="form-group" data-depend-control="attachment_type" data-depend-value="choose_source">
                                <div class="form-group">
                                    <label for="attachment_layout_order" class="col-xs-4 control-label"><?php esc_html_e('Order', 'grid-plus'); ?></label>
                                    <div class="col-xs-8">
                                        <select id="attachment_layout_order" class="form-control grid-col-md-6" data-selected="<?php echo esc_attr($order); ?>" >
                                            <option value="ASC"><?php esc_html_e('Ascending', 'grid-plus'); ?></option>
                                            <option value="DESC"><?php esc_html_e('Descending', 'grid-plus'); ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="attachment_layout_order_by" class="col-xs-4 control-label"><?php esc_html_e('Order By', 'grid-plus'); ?></label>
                                    <div class="col-xs-8">
                                        <select class="form-control" id="attachment_layout_order_by" data-selected="<?php echo esc_attr($order_by); ?>"
                                                placeholder="<?php esc_attr_e('Select Order','grid-plus'); ?>">
                                            <option value="ID"><?php esc_html_e('By post id', 'grid-plus'); ?></option>
                                            <option value="author"><?php esc_html_e('By author', 'grid-plus'); ?></option>
                                            <option value="title"><?php esc_html_e('By title', 'grid-plus'); ?></option>
                                            <option value="name"><?php esc_html_e('By post name (post slug)', 'grid-plus'); ?></option>
                                            <option value="date"><?php esc_html_e('By date', 'grid-plus'); ?></option>
                                            <option value="rand"><?php esc_html_e('Random order', 'grid-plus'); ?></option>
                                            <option value="menu_order"><?php esc_html_e('By Page Order (Menu Order)', 'grid-plus'); ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    <div style="clear: both"></div>
</div>
<?php wp_reset_postdata(); ?>