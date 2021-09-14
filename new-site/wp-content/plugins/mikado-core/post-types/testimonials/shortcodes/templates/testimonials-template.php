<div id="mkd-testimonials<?php echo esc_attr($current_id) ?>" class="mkd-testimonial-content clearfix">
	<?php if (has_post_thumbnail($current_id)) { ?>
		<div class="mkd-testimonial-image-holder">
			<?php the_post_thumbnail($current_id) ?>
		</div>
	<?php } ?>
	<div class="mkd-testimonial-content-inner">
		<div class="mkd-testimonial-text-holder">
			<div class="mkd-testimonial-text-inner">
				<div class="mkd-testimonial-quote"><span aria-hidden="true" class="icon_quotations"></span></div>
				<p class="mkd-testimonial-text"><?php echo trim($text) ?></p>
				<?php if ($show_author == "yes") { ?>
					<div class = "mkd-testimonial-author">
						<p class="mkd-testimonial-author-text"><?php echo esc_attr($author)?>
							<?php if($show_position == "yes" && $job !== ''){ ?>
								<span class="mkd-testimonials-job"> | <?php echo esc_attr($job)?></span>
							<?php }?>
						</p>	
					</div>
				<?php } ?>				
			</div>
		</div>
	</div>	
</div>
