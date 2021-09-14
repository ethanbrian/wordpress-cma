<?php if($query_results->max_num_pages>1){ ?>
	<div class="mkd-ptf-list-paging">
		<span class="mkd-ptf-list-load-more">
			<?php 
				echo libero_mikado_get_button_html(array(
					'link' => 'javascript: void(0)',
					'size' => 'large',
					'text' => 'Load more',
                    'icon_pack' => 'simple_line_icons',
                    'simple_line_icons' => 'icon-arrow-down-circle'
				));
			?>
		</span>
	</div>
<?php }