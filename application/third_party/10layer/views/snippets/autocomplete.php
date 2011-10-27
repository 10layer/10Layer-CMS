<input id="autocomplete_view_<?= $field->tablename ?>_<?= $field->name ?>" type="text" tablename="<?= $field->tablename ?>" contenttype="<?= $field->contenttype ?>" fieldname="<?= $field->name ?>" class="autocomplete <?php if ($field->multiple) { ?>multiple<?php } ?> <?= $field->class ?>" value="<?php if (!$field->multiple) { print $field->data->fields["title"]->value; } ?>" <?php if ($field->contenttype=='mixed') { ?> mixed='mixed' contenttypes='<?= implode(",",$field->contenttypes) ?>' <?php } ?> />
	<br clear="both" />
	<?php
		if (is_array($field->value)) {
			$x=0;
			foreach($field->data as $data) {
				$value=$data->content_id;
				$title=$data->fields["title"]->value;
	?>
	<button class="autocomplete_item">
		<?= $title ?>
	</button>
	<input id="autocomplete_<?= $field->contenttype ?>_<?= $field->name ?>_<?= $value ?>" type="hidden" name="<?= $field->tablename ?>_<?= $field->name ?><?php if ($field->multiple) { ?>[]<?php } ?>" value="<?= $value ?>"  />
<?php
			}
		}
?>