<?php
if (!defined('ABSPATH')) {
    exit;
}

class Grid_Plus_Base
{

    /**
     * GET Plugin template
     * *******************************************************
     */

    public static function gf_get_template($slug, $args = array())
    {
        if ($args && is_array($args)) {
            extract($args);
        }
        $located = G5PLUS_GRID_DIR . $slug . '.php';
        if (!file_exists($located)) {
            _doing_it_wrong(__FUNCTION__, sprintf('<code>%s</code> does not exist.', $slug), '1.0');
            return;
        }
        include($located);
    }

    /**
     * GET list post type
     * *******************************************************
     */

    public static function gf_get_posttypes()
    {
        $post_types = get_post_types(array(
            'public'             => true,
            'publicly_queryable' => true,
        ));
        foreach ($post_types as $key => $type) {
            $post_type_object = get_post_type_object($type);
            if (empty($post_type_object)) {
                $post_types[$key] = $type;
                continue;
            }
            $post_types[$key] = $post_type_object->labels->name;
        }
        return apply_filters('grid_plus_post_types', $post_types);
    }

    /**
     * GET list categories
     * *******************************************************
     */

    public static function gf_get_categories()
    {
        $post_categories = $terms = $obj_taxonomies = array();
        $post_types = Grid_Plus_Base::gf_get_posttypes();

        foreach ($post_types as $post_type => $value) {
            $obj_taxonomies = get_object_taxonomies(
                array('post_type' => $post_type),
                'objects'
            );

            foreach ($obj_taxonomies as $taxonomy_key => $taxonomy_values) {
                $terms = get_terms(array(
                    'taxonomy'   => $taxonomy_values->name,
                    'hide_empty' => false,
                ));
                if (isset($terms) && is_array($terms)) {
                    foreach ($terms as $term) {
                        $post_categories[$post_type][$taxonomy_values->labels->name][] = array(
                            'taxonomy' => $taxonomy_values->name,
                            'term_id'    => $term->term_id,
                            'term_name'  => $term->name,
                            'term_count' => $term->count
                        );
                    }
                }
            }
        }
        return $post_categories;
    }

    /**
     * GET categories taxonomy by post type
     * *******************************************************
     */

    public static function gf_get_category_taxonomy($post_type)
    {
        $obj_taxonomies = get_object_taxonomies(
            array('post_type' => $post_type),
            'objects'
        );
        $category_taxonomy = array();
        if (is_array($obj_taxonomies) && count($obj_taxonomies) > 0) {
            foreach ($obj_taxonomies as $taxonomy_key => $taxonomy_values) {
                $category_taxonomy[] = array(
                    'name' => $taxonomy_values->name,
                    'label' => $taxonomy_values->label
                );
            }
        }
        return $category_taxonomy;
    }

    /**
     * GET categories by post type
     * *******************************************************
     */
    public static function gf_get_categories_info($post_type, $category_ids)
    {
        $post_categories = $terms = array();
        $obj_taxonomies = get_object_taxonomies( array('post_type' => $post_type), 'objects' );
        foreach ($obj_taxonomies as $taxonomy_key => $taxonomy_values) {
            $terms = get_categories(array( 'taxonomy'   => $taxonomy_values->name, 'hide_empty' => '0' ));
            if (isset($terms) && is_array($terms)) {
                foreach ($terms as $term) {
                    if (in_array($term->term_id, $category_ids)) {
                        $link = get_term_link($term->term_id, $taxonomy_values->name);
                        $post_categories[array_search($term->term_id, $category_ids)] = array(
                            'term_id' => $term->term_id,
                            'slug'    => $term->slug,
                            'name'    => $term->name,
                            'count'   => $term->count,
                            'description' => $term->description,
                            'link' => $link,
                            'taxonomy' => $taxonomy_values->name
                        );
                    }
                }
            }
        }
        return $post_categories;
    }

    public static function gf_get_categories_info_by_posttype($post_type)
    {
        $post_categories = $terms = $obj_taxonomies = array();
        $obj_taxonomies = get_object_taxonomies(
            array('post_type' => $post_type),
            'objects'
        );
        foreach ($obj_taxonomies as $taxonomy_key => $taxonomy_values) {
            $terms = get_terms(array(
                'taxonomy'   => $taxonomy_values->name,
                'hide_empty' => true,
            ));
            if (isset($terms) && is_array($terms)) {
                foreach ($terms as $term) {
                    $post_categories[$term->slug] = array(
                        'term_id' => $term->term_id,
                        'slug'    => $term->slug,
                        'name'    => $term->name,
                        'count'   => $term->count,
                        'taxonomy' => $taxonomy_values->name
                    );
                }
            }
        }
        return $post_categories;
    }


    /**
     * GET list user
     * *******************************************************
     */
    public static function gf_get_users()
    {
        $users = get_users(array(
            'orderby' => 'display_name',
            'order'   => 'DESC',
            'fields'  => array('ID', 'user_nicename'),
        ));
        if ($users) {
            $array = array();
            foreach ($users as $user) {
                $array[$user->ID] = $user->user_nicename;
            }
        }
        return $users;
    }

    /**
     * GET grid by grid name
     * *******************************************************
     */
    public static function gf_get_grid_by_name($name)
    {
        $grids = get_option(G5PLUS_GRID_OPTION_KEY, array());
        if (is_array($grids)) {
            foreach ($grids as $grid) {
                if (strtolower($grid['name']) == strtolower($name)) {
                    return get_option(G5PLUS_GRID_OPTION_KEY . '_' . $grid['id'], array());
                }
            }
        }
        return null;
    }

    /**
     * GET post format
     * *******************************************************
     */
    public static function gf_get_post_format($post = null)
    {
        if (!$post = get_post($post))
            return false;

        $_format = get_the_terms($post->ID, 'post_format');

        if (empty($_format))
            return false;

        $format = reset($_format);

        return str_replace('post-format-', '', $format->slug);
    }

    /**
     * Enqueue custom css
     * *******************************************************
     */
    public static function gf_enqueue_custom_css($section, $grid_config)
    {
        if (!empty($section) && !empty($grid_config)) {
            $grid_custom_css = '';
            if(isset($grid_config['category_color']) && $grid_config['category_color']!=''){
                $grid_custom_css .= <<<CSS
                    .grid-{$section} .grid-category a,
                    .grid-{$section} .grid-cate-expanded > span {
                        color: {$grid_config['category_color']} !important;
                    }
CSS;
            }
            if(isset($grid_config['category_hover_color']) && $grid_config['category_hover_color']!=''){
                $grid_custom_css .= <<<CSS
                    .grid-{$section} .grid-category a.active,
                    .grid-{$section} .grid-category a:hover,
                    .grid-{$section} .grid-category a:focus,
                    .grid-{$section} .grid-category a:active,
                    .grid-{$section} .grid-cate-expanded > span:hover,
                    .grid-{$section} .grid-cate-expanded > span:active,
                    .grid-{$section} .grid-cate-expanded > span:focus {
                        color: {$grid_config['category_hover_color']} !important;
                    }
CSS;
            }

            if(isset($grid_config['no_image_background_color']) && $grid_config['no_image_background_color']!=''){
                $grid_custom_css .= <<<CSS
                    .grid-{$section} .grid-post-item .thumbnail-image {
                        background-color: {$grid_config['no_image_background_color']} !important;
                    }
CSS;
            }
            if(isset($grid_config['background_color']) && $grid_config['background_color']!=''){
                $grid_custom_css .= <<<CSS
                    .grid-{$section} .grid-post-item .hover-outer {
                        background-color: {$grid_config['background_color']} !important;
                    }
CSS;
            }
            if(isset($grid_config['icon_color']) && $grid_config['icon_color']!=''){
                $grid_custom_css .= <<<CSS
                    .grid-{$section} .icon-groups > a {
                        color: {$grid_config['icon_color']} !important;
                        border-color: {$grid_config['icon_color']} !important;
                    }
CSS;
            }
            if(isset($grid_config['icon_hover_color']) && $grid_config['icon_hover_color']!=''){
                $grid_custom_css .= <<<CSS
                    .grid-{$section} .icon-groups > a:hover {
                        color: {$grid_config['icon_hover_color']} !important;
                        border-color: {$grid_config['icon_hover_color']} !important;
                    }
CSS;
            }
            if(isset($grid_config['title_color']) && $grid_config['title_color']!=''){
                $grid_custom_css .= <<<CSS
                    .grid-{$section} .grid-plus-inner .title,
                    .grid-{$section} .grid-plus-inner .title a {
                        color:  {$grid_config['title_color']} !important;
                    }
CSS;
            }
            if(isset($grid_config['title_hover_color']) && $grid_config['title_hover_color']!=''){
                $grid_custom_css .= <<<CSS
                    .grid-{$section} .grid-plus-inner .title:hover,
                    .grid-{$section} .grid-plus-inner .title a:hover {
                        color:  {$grid_config['title_hover_color']} !important;
                    }
CSS;
            }
            if(isset($grid_config['excerpt_color']) && $grid_config['excerpt_color']!=''){
                $grid_custom_css .= <<<CSS
                    .grid-{$section} .grid-plus-inner .excerpt,
                    .grid-{$section} .grid-plus-inner .categories {
                         color:  {$grid_config['excerpt_color']} !important;
                    }
CSS;
            }
            grid_custom_css()->addCss($grid_custom_css);
        }
    }

    public static function gf_get_attachment_image($attachment_id, $crop, $crop_size, &$with_after_crop,
                                                   &$height_after_crop, &$with_origin, &$height_origin, &$attachment_url, &$crop_url){
        $orig_image = wp_get_attachment_image_src($attachment_id, 'full');
        if ($orig_image === false) {
            return;
        }
        $attachment_url = $crop_url = isset($orig_image[0]) ? $orig_image[0] : '' ;
        $with_origin = $with_after_crop = isset($orig_image[1]) ? $orig_image[1] : $with_origin;
        $height_origin = $height_after_crop = isset($orig_image[2]) ? $orig_image[2] : $height_origin;

        if($crop){
            if($with_after_crop>=$crop_size){
                $percent = floor($with_after_crop/$crop_size);
                $with_after_crop = $crop_size;
                $height_after_crop = floor($height_after_crop/$percent);
            }elseif($height_after_crop>=$crop_size){
                $percent = floor($height_after_crop/$crop_size);
                $height_after_crop = $crop_size;
                $with_after_crop = floor($with_after_crop/$percent);
            }
            $crop_url = G5Plus_Image_Resize::init()->resize(array(
                'image_id' => $attachment_id,
                'width' => $with_after_crop,
                'height' => $height_after_crop,
            ));
            $crop_url = isset($crop_url['url']) ? $crop_url['url'] : '';
        }
    }

    public static function content_block($id)
    {
        if (empty($id)) return '';
        $content = get_post_field('post_content', $id);
        $content = apply_filters('the_content', $content);
        $content = str_replace(']]>', ']]&gt;', $content);

        /**
         * Post Custom Css
         */
        $post_custom_css = get_post_meta($id, '_wpb_post_custom_css', true);
        if (!empty($post_custom_css)) {
            $post_custom_css = strip_tags($post_custom_css);
            grid_custom_css()->addCss($post_custom_css, "_wpb_post_custom_css_{$id}");
        }

        /**
         * Shortcodes Custom Css
         */
        $shortcodes_custom_css = get_post_meta($id, '_wpb_shortcodes_custom_css', true);
        if (!empty($shortcodes_custom_css)) {
            grid_custom_css()->addCss($shortcodes_custom_css, "_wpb_shortcodes_custom_css_{$id}");
        }
        add_action('wp_footer', 'gf_add_js_composer_front');
        return $content;
    }
    /**
     * Add VC style: js_composer.min.js
     * *******************************************************
     */
    public function gf_add_js_composer_front()
    {
        wp_enqueue_style('js_composer_front');
    }
}