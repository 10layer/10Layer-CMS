<?php
	$content_types=$this->model_content->get_content_types();
	foreach($content_types as $content_type) {
	?>
	<div class="menuitem"><?= anchor("edit/".$content_type->urlid,$content_type->name) ?></div>
	<?php
	}
?>