<script>
	$(function() {
		$(".deepsearch_input").each(function() {
			$(this).css("width","200px");
		});
		
		var t;
		$(".deepsearch_input").live("keyup",function() {
			var resultdiv=$(this).next().next();
			var selected_container = resultdiv.next();
			
			//console.log(selected_container.attr("class"));
			
			var selected_items = selected_container.children('div');
			//console.log(selected_container.html());
			
			var items = new Array();
			
			selected_items.each(function(index) {
				items[index] = $(this).children(":first").val();
			});
								
	
			var val=$(this).val();
			clearTimeout(t);
			t=setTimeout(function() {
				$.getJSON("/list/<?= $field->contenttype ?>/deepsearch?term="+escape(val), {"selected[]":items}, function(result) {
					resultdiv.html("");
					for(x=0; x<result.length; x++) {
						resultdiv.append("<div class='deepsearch_item' id='"+result[x].id+"'>"+result[x].value+"</div>");
					}
				});
			}, 1000);
			
		});
		
		
		
	/*
	$.post("/list/<?= $field->contenttype ?>/deepsearch?term="+escape(val),{"selected[]":items, function(data) {
  					resultdiv.html("");
					for(x=0; x<result.length; x++) {
						resultdiv.append("<div class='deepsearch_item' id='"+result[x].id+"'>"+result[x].value+"</div>");
					}
				},1000);
*/
		
		$(".deepsearch_item").live("click", function(){
		
			var selected_set = $(this).parent().next();
			var used_element = $(this).parent().prev().prev();
			//console.log(selected_values.attr("class"));
			var label = $(this).html();
			var id = this.id;
			//var newdisp="<button class='autocomplete_item'>"+label+"</button>";
			var newdisp="<div class='deepsearch_selected_item'>"+
			"<input type='hidden' value='"+id+"' name='"+used_element.attr("tablename")+"_"+used_element.attr("fieldname")+"[]' value='' /><span class='label'>"+
			label+"</span></div>";
			selected_set.append(newdisp);
			$(this).remove();
			return false;
			
		});
		
		$(".deepsearch_selected_item").live("click", function(){
		
			var search_result_set = $(this).parent().prev();
			//var used_element = $(this).parent().prev().prev();
			//console.log(selected_values.attr("class"));
			var label = $(this).text();
			var id = $(this).children(":first").val();
			
			var newdisp="<button class='autocomplete_item'>"+label+"</button>";
			var newdisp="<div class='deepsearch_item' id='"+id+"'>"+label+"</div>";
			search_result_set.append(newdisp);
			$(this).remove();
			return false;
			
		});
		
		function get_selected(){
			var inputs = $()
		}
				
			
	
		
		
		
		
		
		
		
		
		
	});
</script>
<div class="deepsearch">
	<input id="deepsearch_view_<?= $field->tablename ?>_<?= $field->name ?>" type="text" tablename="<?= $field->tablename ?>" contenttype="<?= $field->contenttype ?>" fieldname="<?= $field->name ?>" class="deepsearch_input <?php if ($field->multiple) { ?>multiple<?php } ?> <?= $field->class ?>" value="<?php if (!$field->multiple) { print $field->data->fields["title"]->value; } ?>" <?php if ($field->contenttype=='mixed') { ?> mixed='mixed' contenttypes='<?= implode(",",$field->contenttypes) ?>' <?php } ?> />
	
	<br clear="both" />
	
	<div class="deepsearch_results" style=" padding: 5px; background-color: #FFF; border: 1px #CCC solid; width:290px; height:300px; overflow:auto; float:left;">
		
	</div>
	
	<div class="selected_results" style=" padding: 5px; background-color: #FFF; border: 1px #CCC solid; width:290px; height:300px; overflow:auto; float:right;">
	<?php
			if (is_array($field->value)) {
			$x=0;
			foreach($field->data as $data) {
				$value=$data->content_id;
				$title=$data->fields["title"]->value;
	?>
	
		<div class='deepsearch_selected_item'>
		<input id="deepsearch_<?= $field->contenttype ?>_<?= $field->name ?>_<?= $value ?>" type="hidden" name="<?= $field->tablename ?>_<?= $field->name ?><?php if ($field->multiple) { ?>[]<?php } ?>" value="<?= $value ?>"  />
		<span><?= $title ?></span>
		</div>
	
<?php
			}
		}
?>
<?php
		if ($field->external) {
	?>
	
	<div class="selected_results" style=" padding: 5px; background-color: #FFF; border: 1px #CCC solid; width:290px; height:300px; overflow:auto; float:right;">
		<input id="autocomplete_<?= $field->contenttype ?>_<?= $field->name ?>_<?= $value ?>" type="hidden" name="<?= $field->tablename ?>_<?= $field->name ?><?php if ($field->multiple) { ?>[]<?php } ?>" value="<?= $value ?>"  />
		<span><?= $title ?></span>
	</div>

	<?php
		}
	?>

		
	</div>
		
	</div>