<?php
/**
 * The template for displaying tabs.php
 *
 * @package WordPress
 * @subpackage g5theme-framework
 * @since g5theme-framework 1.0
 * @var $current_post_format
 * @var $post_formats
 * @var $post_format_views
 * @var $post_type
 */
?>
<div class="gf-post-formats-ui-tabs" id="gf-post-formats-ui-tabs">
	<div class="tab-content">
		<?php
		foreach ($post_format_views as $post_format) {
			if (!in_array($post_format,$post_formats) || ($post_format === 'standard')) continue;
			$class = ($post_format == $current_post_format)  ? ' active' : '';
			$format_hash = 'post-format-'.$post_format;
			?>
			<div class="postbox tab-pane<?php echo esc_attr($class); ?>" id="tab-<?php echo esc_attr($format_hash);?>">
				<?php gfPostFormatUi()->get_template_part("format-{$post_format}");?>
			</div>
			<?php
		}
		?>
	</div>
</div>
