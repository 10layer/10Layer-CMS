
//This javascript isn't run because it is loaded before teh dynamic content

$(function() {
	
	
	$("input, textarea").keyup(function() {
		var reqs=checkreqs();
	});
	
	$("#submit").click(function() {
		if (checkreqs()) {
			return true;
		}
		return false;
	});
	
	checkreqs();
	
	$("#title").keyup(function() {
		$("#smarturl-preview").load("/workers/ajax_edit/smarturl", {"url": $("#title").val()}, 
			function(response, status, xhr) {
				$("#urlid").val(response);
			}
		);
	});
	
	$('<div class="wordcount_container">Word count:&nbsp;<div class="wordcount_result"></div></div>').insertAfter(".wordcount");
	
	$(".wordcount").keyup(function() {
		get_wordcount($(this));
	});
	
	$(".wordcount").each(function() {
		get_wordcount($(this));
	});
	
	
	
	
	
			
	$(".autocomplete_add").live("click",function() {
		var viewfield=$("#autocomplete_view_"+$(this).attr("contenttype")+"_"+$(this).attr("fieldname"));
		alert(viewfield.val());
		return false;
	});
		
	
			
	$(".remover").live("click", function() {
		//$(this).next().remove();
		$(this).parent().remove();
		return false;
	});
	
	
	
	$(document).unbind('keydown').bind('keydown', function (event) {
		var doPrevent = false;
		if (event.keyCode === 8) {
			var d = event.srcElement || event.target;
			if ((d.tagName.toUpperCase() === 'INPUT' && d.type.toUpperCase() === 'TEXT') || d.tagName.toUpperCase() === 'TEXTAREA') {
            	doPrevent = d.readOnly || d.disabled;
        	} else {
            	doPrevent = true;
        	}
    	}

	    if (doPrevent) {
        	event.preventDefault();
    	}
	});
	
	$(".deepsearch_input").live("keypress",function(e) { 
    	if (e.which == 13) { 
      		return false; 
    	} 
  	});
  	
  	$(".deepsearch_input").live("keypress",function(e) {
  		if (e.which == 13) { 
			var resultdiv=$(this).next().next().next().next();
			var selected_container = resultdiv.next();
			var selected_items = selected_container.children('div');
			var items = new Array();
			var content_type=$(this).attr("contenttype");
			selected_items.each(function(index) {
				items[index] = $(this).children(":first").val();
			});

			var exact_match_setting = $('#exact_match_conf').is(":checked");
			var older_content_setting = $('#older_content_conf').is(':checked');
			
		
			var val = $(this).val();
      		$.getJSON("/list/"+content_type+"/deepsearch?term="+escape(val), {"selected[]":items, exact_match:exact_match_setting, older_content:older_content_setting }, function(result) {
				resultdiv.html("");
				for(x=0; x<result.length; x++) {
					resultdiv.append("<div class='deepsearch_item' id='"+result[x].id+"'>"+result[x].value+" ("+result[x].start_date+")</div>");
				}
			});	
    	}		
	});
	
	$(".deepsearch_item").live("click", function(){
		var selected_set = $(this).parent().next();
		var used_element = $(this).parent().prev().prev().prev().prev();
		var label = $(this).html();
		var id = this.id;
		var newdisp="<div class='deepsearch_selected_item'>"+"<input type='hidden' value='"+id+"' name='"+used_element.attr("tablename")+"_"+used_element.attr("fieldname")+"[]' value='' /><span class='label'>"+
		label+"</span></div>";
		selected_set.append(newdisp);
		$(this).remove();
		return false;
	});
		
	$(".deepsearch_selected_item").live("click", function(){
		var search_result_set = $(this).parent().prev();
		var label = $(this).text();
		var id = $(this).children(":first").val();
		var newdisp="<button class='autocomplete_item'>"+label+"</button>";
		var newdisp="<div class='deepsearch_item' id='"+id+"'>"+label+"</div>";
		search_result_set.append(newdisp);
		$(this).remove();
		return false;
	});
	
	$("#workflow_next").live("click", function() {
		$.getJSON("/workflow/change/advance/"+content_type+"/"+urlid, function() {
			$("#workflows").load("/workflow/change/status/"+content_type+"/"+urlid);
		});
	});
	
	$("#workflow_revert").live("click", function() {
		$.getJSON("/workflow/change/revert/"+content_type+"/"+urlid, function() {
			$("#workflows").load("/workflow/change/status/"+content_type+"/"+urlid);
		});
	});
	
	$(document).on("click", '.new-window', function() {
		window.open($(this).attr('href'), '_blank');
	});
	
});

function checkreqs() {
	var reqs=true;
	$(".required").each(function() {
		var val=$(this).val();
		if (val=="") {
			reqs=false;
		}
	});
	if (!reqs) {
		$("#submit").addClass("inactive");
	} else {
		$("#submit").removeClass("inactive");
	}
	return reqs;
}

function get_wordcount(sender) {
	sender.nextAll(".wordcount_container").first().find(".wordcount_result").load("/workers/ajax_edit/wordcount", {"str": sender.val()})
}

function init_form() {

	$($('.nested_container')).each(function(index) {
		var container = $(this);
		var content_type = $(this).attr("contenttype");
		var items = container.children().eq(1);
		    	
    	$.getJSON('/list/jsonnested/'+content_type+'/1?jsoncallback=?', function(data) {
  				items.html(_.template($('#edit-field-nesteditems-list').html(), data));
  				
  				$(".nested_section").live("click", function(){
					var content_id = $(this).attr("content_id");
					var indicator = $(this);
					$(".cool_t").removeClass("cool_t");
                	indicator.children().eq(0).addClass("cool_t");
					var value = $(this).attr('label');
					var display_el = $(this).parentsUntil(".nested_items").parent().prev();
					display_el.children().eq(1).html(value);
					var value_el = display_el.children().eq(0);
					value_el.val(content_id);
					return false;
				});				

		});
    	
    	
    	
    	
	});
	
	
	
	$('button').each(function() {
		$(this).button();
	});
	
	$(".datetimepicker").each(function() {
		$(this).datetimepicker({
			dateFormat:"yy-mm-dd",
			timeFormat:"hh:mm:ss",
		});
	});
	
	$( ".datepicker" ).datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: "yy-mm-dd",
	});
	
	$(".autocomplete").each(function() {
		var contenttype=$(this).attr("contenttype");
		var contenttypes=false;
		var source="/list/"+contenttype+"/suggest";
		if (contenttype=="mixed") {
			contenttypes=$(this).attr("contenttypes");
			contenttypes=contenttypes.replace(/,/g,"/");
			var source="/list/mixed/suggest/"+contenttypes;
		}
		if ($(this).hasClass("multiple")) {
			$(this).width(450);
		}
		$(this).autocomplete({
		
			source: function (request, response) {
				try {
					$.ajax({
						url: source,
						data: request,
						dataType: "json",
						success: function(data, status) {
							response(data);
						}
					});
				} catch(err) {
					//Do nothing
				}
			},
			select: function( event, ui ) {
				if (ui.item) {
					if ($(this).hasClass("multiple")) {
						//Check for repeats
						var isRepeat=false;
						$(this).next().next().find("input").each(function() {
							if ($(this).val()==ui.item.id) {
								isRepeat=true;
								return false;
							}
						});
						if (isRepeat) {
							return false;
						}
						//console.log(ui.item);
						var newdisp= ui.item.label;
						var newobj="<input type='hidden' value='"+ui.item.id+"' name='"+$(this).attr("tablename")+"_"+$(this).attr("fieldname")+"[]' />";

						var append_material = "<li class='autocomplete_item'><span class='ui-icon ui-icon-arrowthick-2-n-s float-left' style='margin:10px;'></span><span class='remover'>" + newdisp + "</span>" + newobj + "</li>";
						
						$(this).next().next().children(":first").append(append_material);
						
						$(".remover").button({
								icons: {
    		    					primary: "ui-icon-circle-close"
								}
						});
						
						$(".items_container").sortable();
						$(this).val("");
						
						return false;
					} else {
						//This looks incomplete
						//$("#autocomplete_"+$(this).attr("contenttype")+"_"+$(this).attr("fieldname")).val(ui.item.id);
						//alert($("#autocomplete_"+$(this).attr("contenttype")+"_"+$(this).attr("fieldname")).val());
					}
				}
			},
		});
	});
	
	
	
		
	$(".items_container").sortable();
	
	$(".countchars").each(function() {
		if ($(this).hasClass("countdown")) {
			var max=$(this).attr("max");
			var current=max-$(this).val().length;
			if (current>=0) {
				$(this).after("<div class='charcount'>"+current+" chars remaining</div>");
			} else {
				$(this).after("<div class='charcount red'>"+Math.abs(current)+" chars over</div>");
			}
			
			$(this).keyup(function() {
				var max=$(this).attr("max");
				var current=max-$(this).val().length;
				if (current>=0) {
					$(this).next().html(current+" chars remaining").removeClass("red");
				} else {
					$(this).next().html(Math.abs(current)+" chars over").addClass("red");
				}
			});
		} else {
			var current=$(this).val().length;
			$(this).after("<div class='charcount'>"+current+" chars</div>");
			$(this).keyup(function() {
				current=$(this).val().length;
				$(this).next().html(current+" chars");
			});
		}
		$(this).removeClass("countchars");
	});
	
	if ($(".richedit").length) {
		//init_tinymce();
		//clearCKEditor();
		initCKEditor();
	}
	
	var content_type=$(document.body).data('content_type');
	var urlid=$(document.body).data('urlid');
	$("#workflows").load("/workflow/change/status/"+content_type+"/"+urlid);
}