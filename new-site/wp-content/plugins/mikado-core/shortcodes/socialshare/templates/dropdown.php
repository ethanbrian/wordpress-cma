<div class="mkd-social-share-holder mkd-dropdown">
	<a href="javascript:void(0)" target="_self" class="mkd-social-share-dropdown-opener">
		<i class="icon-share social_share"></i>
		<span class="mkd-social-share-title"><?php esc_html_e('Share', 'mikado-core') ?></span>
	</a>
	<div class="mkd-social-share-dropdown">
		<ul>
			<?php foreach ($networks as $net) {
				print libero_mikado_get_module_part($net);
			} ?>
		</ul>
	</div>
</div>