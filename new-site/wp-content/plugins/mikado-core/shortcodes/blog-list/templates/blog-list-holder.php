<div class="mkd-blog-list-holder <?php echo esc_attr($holder_classes) ?>">
	<ul class="mkd-blog-list">
	<?php
	$html = '';
		if($query_result->have_posts()):
		while ($query_result->have_posts()) : $query_result->the_post();
			$html .= mkd_core_get_core_shortcode_template_part('templates/'.$type, 'blog-list', '', $params);
		endwhile;
		print libero_mikado_get_module_part($html);
		else: ?>
		<div class="mkd-blog-list-messsage">
			<p><?php esc_html_e('No posts were found.', 'mikado-core'); ?></p>
		</div>
		<?php endif;
		wp_reset_postdata();
	?>
	</ul>	
</div>
