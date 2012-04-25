<script>
	$(function() {
		
		$(".file_upload").live("change" ,function() {
			var file_name = $(this).prev().val();
			$(this).attr("name",file_name);
			$(this).next().remove();
				
		});
		
	});

</script>

<input type="hidden" class="name_holder" value="<?= $field->tablename ?>_<?= $field->name ?>" />
<input type="file" name="" class="file_upload <?= $field->class ?>" />
<input type="hidden" name="<?= $field->tablename ?>_<?= $field->name ?>" value="<?= $field->value ?>" />



