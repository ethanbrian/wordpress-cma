<div class="mkd-carousel-item-holder" <?php libero_mikado_inline_style($style); ?>>
	<div class="mkd-carousel-item-holder-inner">
		<?php if ($link !== '') { ?>
		<a href="<?php echo esc_url($link)?>" target="<?php echo esc_attr($target)?>">
		<?php } ?>
			<?php if ($image !== '') { ?>
				<span class="mkd-carousel-first-image-holder <?php echo esc_attr($hover_class); ?> <?php echo esc_attr($carousel_image_classes); ?>">
					<img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>">
				</span>
			<?php } ?>
			<?php if ($hover_image !== '') { ?>
				<span class="mkd-carousel-second-image-holder <?php echo esc_attr($hover_class); ?> <?php echo esc_attr($carousel_image_classes); ?>">
					<img src="<?php echo esc_url($hover_image); ?>" alt="<?php echo esc_attr($title); ?>">
				</span>
			<?php } ?>
		<?php if ($link !== '') { ?>
		</a>
		<?php } ?>
	</div>
</div>