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
	
	$(".deepsearch_input").live("keypress",function(e) { 
    	if (e.which == 13) { 
      		return false; 
    	} 
  	});
  	
  	$(".deepsearch_input").live("keypress",function(e) {
  		if (e.which == 13) { 
			var resultdiv=$(this).next().next();
			var selected_container = resultdiv.next();
			var selected_items = selected_container.children('div');
			var items = new Array();
			var content_type=$(this).attr("contenttype");
			selected_items.each(function(index) {
				items[index] = $(this).children(":first").val();
			});
		
			var val = $(this).val();
      		$.getJSON("/list/"+content_type+"/deepsearch?term="+escape(val), {"selected[]":items}, function(result) {
				resultdiv.html("");
				for(x=0; x<result.length; x++) {
					resultdiv.append("<div class='deepsearch_item' id='"+result[x].id+"'>"+result[x].value+" ("+result[x].start_date+")</div>");
				}
			});	
    	}		
	});
	
	$(".deepsearch_item").live("click", function(){
		var selected_set = $(this).parent().next();
		var used_element = $(this).parent().prev().prev();
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
	
	$(document).on("click", '.nesteditems_item_button', function() {
		var resultdiv=$(this).next();
		var content_type=$(this).attr('contenttype');
		if(resultdiv.is(':hidden') ) {
    		$.getJSON('/list/jsonnested/'+content_type+'/1?jsoncallback=?', function(data) {
  				resultdiv.html(_.template($('#edit-field-nesteditems-list').html(), data));
	  			resultdiv.toggle();
			});
		} else {
			resultdiv.toggle();
		}
		return false;
	});

	$(".nested_section").live("click", function(){
		var content_id = $(this).attr("content_id");
		var value = $(this).attr('label');
		var display_el = $(this).parentsUntil(".section_list").parent().prev().prev();
		display_el.html(value);
		var value_el = display_el.prev();
		value_el.val(content_id);
		display_el.next().next().hide();
		return false;
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
	
	/*$("#sidebar_accordian").accordion({
		autoHeight: false
	});*/
	
	var content_type=$(document.body).data('content_type');
	var urlid=$(document.body).data('urlid');
	$("#workflows").load("/workflow/change/status/"+content_type+"/"+urlid);
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
	
	$(document.body).data('done_submit',false);
	$("#contentform").ajaxForm({
			delegation: true,
			dataType: "json",
			iframe: true,
			debug: true,
			iframeSrc: '/blank',
			success: function(data) {
				if (data.error) {
					$("#msgdialog").html("<div class='ui-state-error' style='padding: 5px'><p><span class='ui-icon ui-icon-alert' style='float: left; margin-right: .3em;'></span><strong>"+data.msg+"</strong><br /> "+data.info+"</p></div>");
					$("#msgdialog").dialog({
						modal: true,
						buttons: {
							Ok: function() {
								$(this).dialog("close");
							}
						}
					});
				} else {
					$("#msgdialog").html("<div class='ui-state-highlight' style='padding: 5px'><p><span class='ui-icon ui-icon-info' style='float: left; margin-right: .3em;'></span><strong>Saved</strong></p></div>");
					if ($(document.body).data('done_submit')) {
						content_type=$(document.body).data('content_type');
						urlid=$(document.body).data('urlid');
						$.ajax({ type: "GET", url: "<?= base_url() ?>/workflow/change/advance/"+content_type+"/"+urlid, async:false});
						location.href="/workers/content/unlock/"+content_type+"/"+urlid;
					} else {
						$("#msgdialog").dialog({
							modal: true,
							buttons: {
								Ok: function() {
									$(this).dialog("close");
								}
							}
						});
					}
				}
			},
			error: function(e) {
				$("#msgdialog").html("<div class='ui-state-error' style='padding: 5px'><p><span class='ui-icon ui-icon-alert' style='float: left; margin-right: .3em;'></span><strong>Error</strong><br /> Problem communicating with the server: "+e.error+"</p></div>");
				$("#msgdialog").dialog({
					modal: true,
					buttons: {
						Ok: function() {
							$(this).dialog("close");
						}
					}
				});
			}
		});
		
		
	/*$('.file_upload').each(function() {
		var el=$(this).parent();
		$(this).fileupload({
			dataType: 'json',
			start: function (e) {
				el.find('.fileupload-progress').show();
			},
			stop: function(e) {
				el.find('.fileupload-progress').hide();
			},
			add: function (e, data) {
				data.submit();
			},
			done: function (e, data) {
				$.each(data.result, function (index, file) {
					el.find('.fileupload-result').html('File uploaded: '+file.url).show();
				});
            },
            progress: function (e, data) {
				var progress = parseInt(data.loaded / data.total * 100, 10);
				el.find('.fileupload-progress .progress .bar').css('width', progress+'%');
				el.find('.fileupload-progress .progress-extended').html(progress+'%');
			}
		});
	});*/
}