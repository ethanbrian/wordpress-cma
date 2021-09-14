<div class="mkd-social-share-holder mkd-list">
	<ul>
		<?php
		$i = 0;
		foreach ($networks as $net) {
			$i++;
			if ($no_shown == ''){
				print libero_mikado_get_module_part($net);
			}
			elseif($i <= $no_shown) {
				print libero_mikado_get_module_part($net);
			}
			else{
				break;
			}
		} ?>
	</ul>
</div>