<?php
	$this->load->library("cdn");
	
	if (empty($field->value)) {
	?>
	<div style="margin-top: 20px">Awaiting upload</div>
	<?php
	} else {
	?>
	<input type="text" value="<?= $field->value ?>" readonly="readonly" />
	<?php
	}
?>