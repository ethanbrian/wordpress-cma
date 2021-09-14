<div class="mkd-page-header page-header clearfix">

    <div class="mkd-theme-name pull-left" >
        <img src="<?php echo esc_url(libero_mikado_get_skin_uri() . '/assets/img/logo.png'); ?>"
             alt="<?php esc_attr_e('mkd_logo','libero')?>" class="mkd-header-logo pull-left"/>
        <h1 class="pull-left">
            <?php echo esc_html($themeName); ?>
            <small><?php echo esc_html($themeVersion); ?></small>
        </h1>
    </div>
    <div class="mkd-top-section-holder">
        <div class="mkd-top-section-holder-inner">
            <div class="mkd-notification-holder">
                <div class="mkd-input-change"><i class="icon_error-circle_alt"></i><?php esc_html_e( 'You should save your changes', 'libero' ); ?></div>
                <div class="mkd-changes-saved"><i class="icon_check_alt2"></i><?php esc_html_e( 'All your changes are successfully saved', 'libero' ); ?></div>
            </div>
            <div class="mkd-top-buttons-holder">
                <?php if($showSaveButton) { ?>
                    <input type="button" id="mkd_top_save_button" class="btn btn-info btn-sm" value="<?php esc_html_e('Save Changes', 'libero'); ?>"/>
                <?php } ?>
            </div>
        </div>
    </div>

</div> <!-- close div.mkd-page-header -->