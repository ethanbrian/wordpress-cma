<article class="mkd-portfolio-item <?php echo esc_attr($categories)?>" >
	<a class ="mkd-portfolio-link" href="<?php echo esc_url(get_permalink()) ?>"></a>
	<div class = "mkd-item-image-holder">
	<?php
		echo get_the_post_thumbnail(get_the_ID(),$thumb_size);
	?>				
	</div>
	<div class="mkd-item-text-overlay">
		<div class="mkd-item-text-overlay-inner">
			<div class="mkd-item-text-holder">
				<<?php echo esc_attr($title_tag)?> class="mkd-item-title">
					<?php echo esc_attr(get_the_title()); ?>
				</<?php echo esc_attr($title_tag)?>>	
				<?php 
				echo libero_mikado_get_module_part($icon_html);
				echo libero_mikado_get_module_part($category_html);
				?>
			</div>
		</div>	
	</div>
</article>
