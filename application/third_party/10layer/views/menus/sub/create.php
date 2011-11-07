<?php
	$content_types=$this->model_content->get_content_types();
	$this->load->library("tluserprefs");
	$usermenus=$this->tluserprefs->get_menus();
	foreach($content_types as $content_type) {
	?>
	<div class="menuitem"><?= anchor("create/".$content_type->urlid,$content_type->name) ?></div>
	<?php
	}
?>