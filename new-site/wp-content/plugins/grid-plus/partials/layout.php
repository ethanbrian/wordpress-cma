<?php
/**
 * Created by PhpStorm.
 * User: phuongth
 * Date: 12/14/2016
 * Time: 9:31 AM
 */

$skins = array('thumbnail', 'thumbnail-title','thumbnail-title-hover-top');
$args = array(
    'thumbnail' => G5PLUS_GRID_URL.'/assets/images/sample-03.jpg',
    'title' => esc_html__('The post title','grid-plus'),
    'excerpt' => esc_html__('In eam evertitur ullamcorper signiferumque','grid-plus')
)

?>
<div class="grid-plus-container">
    <div class="grid-plus-layout">
        <?php Grid_Plus_Base::gf_get_template('partials/bar/layout-bar'); ?>
        <div class="grid-stack-container" data-resource-url="<?php echo esc_attr(G5PLUS_GRID_URL) ;?>">
            <div class="grid-stack">
            </div>
        </div>
    </div>
</div>
