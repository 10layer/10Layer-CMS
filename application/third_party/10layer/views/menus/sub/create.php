<?php
// removed the logic for smart menus
	$content_types=$this->model_content->get_content_types();
	foreach($content_types as $ct) {

		if (isset($ct->urlid)) {
?>
		<div class="menuitem" id='menuitem_<?= $ct->urlid ?>'><?= anchor("create/".$ct->urlid,$ct->name) ?></div>
<?php
		}
	}
?>