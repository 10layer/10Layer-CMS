<script>
	$(function() {
		$(".deepsearch_input").each(function() {
			$(this).css("width","200px");
		});
		
		var t;
		$(".deepsearch_input").live("keyup",function() {
			var resultdiv=$(this).next().next();
			var val=$(this).val();
			clearTimeout(t);
			t=setTimeout(function() {
				$.getJSON("/list/<?= $field->contenttype ?>/deepsearch?term="+escape(val), function(result) {
					resultdiv.html("");
					for(x=0; x<result.length; x++) {
						resultdiv.append("<div class='deepsearch_item'>"+result[x].value+"</div>");
					}
				});
			}, 1000);
			
		});
		
		
	});
</script>
<div class="deepsearch">
	<input id="deepsearch_view_<?= $field->tablename ?>_<?= $field->name ?>" type="text" tablename="<?= $field->tablename ?>" contenttype="<?= $field->contenttype ?>" fieldname="<?= $field->name ?>" class="deepsearch_input <?php if ($field->multiple) { ?>multiple<?php } ?> <?= $field->class ?>" value="<?php if (!$field->multiple) { print $field->data->fields["title"]->value; } ?>" <?php if ($field->contenttype=='mixed') { ?> mixed='mixed' contenttypes='<?= implode(",",$field->contenttypes) ?>' <?php } ?> />
	<br clear="both" />
	<div class="deepsearch_results" style="position: absolute; padding: 20px; background-color: #FFF; border: 1px #CCC solid; z-index: 1000">
		
	</div>
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
<?php
		if ($field->external) {
	?>
	<button style="margin-left: 110px" id="add_relation_<?= $field->tablename ?>_<?= $field->name ?>" contenttype="<?= $field->contenttype ?>" fieldname="<?= $field->name ?>" tablename="<?= $field->tablename ?>" class="add-relation ui-button-text-icons ui-button ui-widget ui-state-default ui-corner-all " role="button" aria-disabled="false"><span class="ui-button-text"><span class="ui-button-icon-primary ui-icon ui-icon-plusthick"></span>New</span></button>
	<?php
		}
	?>
</div>