//This javascript isn't run because it is loaded before teh dynamic content

$(function() {
	$(".items_container").sortable();
	
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
						//console.log(ui.item);
						var newdisp= ui.item.label;
						var newobj="<input type='hidden' value='"+ui.item.id+"' name='"+$(this).attr("tablename")+"_"+$(this).attr("fieldname")+"[]' value='' />";

						var append_material = "<li class='autocomplete_item'><span class='ui-icon ui-icon-arrowthick-2-n-s float-left'></span><span class='remover'>" + newdisp + "</span>" + newobj + "</li>";
						
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
			
	$(".autocomplete_add").live("click",function() {
		var viewfield=$("#autocomplete_view_"+$(this).attr("contenttype")+"_"+$(this).attr("fieldname"));
		alert(viewfield.val());
		return false;
	});
			
	
			
	$(".autocomplete_item").live("click", function() {
		//$(this).next().remove();
		$(this).remove();
		return false;
	});
	
	$(".datetimepicker").each(function() {
		$(this).datetimepicker({
			dateFormat:"yy-mm-dd",
			timeFormat:"hh:mm",
		});
	});
	
	$(document).unbind('keydown').bind('keydown', function (event) {
		var doPrevent = false;
		if (event.keyCode === 8) {
			/*var d = event.srcElement || event.target;
			if ((d.tagName.toUpperCase() === 'INPUT' && d.type.toUpperCase() === 'TEXT') || d.tagName.toUpperCase() === 'TEXTAREA') {
            	doPrevent = d.readOnly || d.disabled;
        	} else {*/
            	doPrevent = true;
        	//}
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