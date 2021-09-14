<!--space for right ordering on the list- has to be here-->
<article class="mkd-portfolio-item mix <?php echo esc_attr($categories)?>">
	<div class = "mkd-item-image-holder">
		<a href="<?php echo esc_url($title_link); ?>">
			<?php
				echo get_the_post_thumbnail(get_the_ID(),$thumb_size);
			?>				
		</a>
	</div>
	<div class="mkd-item-text-holder">
		<<?php echo esc_attr($title_tag)?> class="mkd-item-title">
			<a href="<?php echo esc_url($title_link); ?>">
				<?php echo esc_attr(get_the_title()); ?>
			</a>	
		</<?php echo esc_attr($title_tag)?>>	
		<?php
		echo libero_mikado_get_module_part($category_html);
		?>
	</div>
</article>
<!--space for right ordering on the list- has to be here-->