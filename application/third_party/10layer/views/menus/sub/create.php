<?php
	$content_types=$this->model_content->get_content_types();
	$this->load->library("tluserprefs");
	$usermenus=$this->tluserprefs->get_menus_order();
	foreach($usermenus as $usermenu) {
		$content_type=false;
		foreach($content_types as $ct) {
			if ($ct->urlid==$usermenu) {
				$content_type=$ct;
			}
		}
		if (isset($content_type->urlid)) {
	?>
	<div class="menuitem"><?= anchor("create/".$content_type->urlid,$content_type->name) ?></div>
	<?php
		}
	}
?>