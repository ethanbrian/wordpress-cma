<?php do_action('libero_mikado_before_mobile_navigation'); ?>
<?php if ( has_nav_menu('main-navigation') ) { ?>
<nav class="mkd-mobile-nav">
    <div class="mkd-grid">
        <?php wp_nav_menu(array(
            'theme_location' => 'main-navigation' ,
            'container'  => '',
            'container_class' => '',
            'menu_class' => '',
            'menu_id' => '',
            'fallback_cb' => 'top_navigation_fallback',
            'link_before' => '<span>',
            'link_after' => '</span>',
            'walker' => new LiberoMobileNavigationWalker()
        )); ?>
    </div>
</nav>
<?php } ?>
<?php do_action('libero_mikado_after_mobile_navigation'); ?>