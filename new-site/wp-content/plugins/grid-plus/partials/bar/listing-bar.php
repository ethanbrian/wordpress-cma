<?php
/**
 * Created by PhpStorm.
 * User: phuongth
 * Date: 12/19/2016
 * Time: 4:23 PM
 */

?>
<div class="layout-header">
    <span class="layout-name"><?php esc_html_e('Listing layout','grid-plus');?></span>
    <div class="action-groups" data-ajax-url="<?php echo esc_url(admin_url('admin-ajax.php')) ?>">
        <div class="search-wrap">
            <input id="grid_name" type="text">
        <a class="search-layout" href="javascript:;" title="<?php esc_html_e('Search grid', 'grid-plus'); ?>"><i class="fa fa-search"></i></a>
        </div>

        <a class="export-grid" href="javascript:;"><i class="fa fa-download"></i><?php esc_html_e('Export grid','grid-plus');?></a>
        <a class="import-grid" href="javascript:;"><i class="fa fa-upload"></i><?php esc_html_e('Import grid','grid-plus');?></a>
        <a class="refresh-layout" href="javascript:;"><i class="fa fa-refresh"></i><?php esc_html_e('Refresh grid','grid-plus');?> </a>
        <a class="add-layout" href="<?php menu_page_url('grid_plus_setting', true) ?>"><i class="fa fa-plus"></i><?php esc_html_e('Add grid','grid-plus');?> </a>
        <a class="remove-all-data" href="javascript:;"><i class="fa fa-trash"></i><?php esc_html_e('Remove all grid','grid-plus');?> </a>
    </div>
</div>
