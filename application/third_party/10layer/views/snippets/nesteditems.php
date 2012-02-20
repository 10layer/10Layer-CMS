<script>
	$(function() {
		
		$(".single_item_button").live("click" ,function() {
			var resultdiv=$(this).next();
			resultdiv.toggle();
			if($.trim(resultdiv.html())=="" )
			{
				$.get('/list/nested/<?= $field->contenttype ?>/1', function(data) {
  					resultdiv.html(data);
				});	
			}
						
		});
		
		$(".small_item").live("click", function(){
			var display_term = $(this).parentsUntil(".section_list").parent().prev().prev();
			display_term.html($(this).html());
			var value_holder = display_term.prev();
			value_holder.val($(this).attr("id"));
			display_term.next().next().hide();
			
		});
		
						
		
	});

</script>

<div class="single_item_actions">
	<input id="nestedselect_view_<?= $field->tablename ?>_<?= $field->name ?>" name="<?= $field->tablename ?>_<?= $field->name ?>" type="hidden" tablename="<?= $field->tablename ?>" contenttype="<?= $field->contenttype ?>" fieldname="<?= $field->name ?>" class="nestedselect <?= $field->class ?>" value="<?php if(isset($field->data)){ print $field->data->fields["id"]->value; } ?>" <?php if ($field->contenttype=='mixed') { ?> mixed='mixed' contenttypes='<?= implode(",",$field->contenttypes) ?>' <?php } ?> />

	<div class="single_item_label">
		<?php if(isset($field->data)){ print $field->data->fields["title"]->value; } else{ print "Click on change..."; }  ?>
	</div>
	<div class="single_item_button">
		Change...
	</div>
	<div class="section_list" >
	
	</div>
	
		
	<br clear="both"/><br clear="both"/>
	
	
	<button style="margin-left: 110px" id="add_relation_<?= $field->tablename ?>_<?= $field->name ?>" contenttype="<?= $field->contenttype ?>" fieldname="<?= $field->name ?>" tablename="<?= $field->tablename ?>" class="add-relation ui-button-text-icons ui-button ui-widget ui-state-default ui-corner-all " role="button" aria-disabled="false"><span class="ui-button-text"><span class="ui-button-icon-primary ui-icon ui-icon-plusthick"></span>New</span></button>



</div>

