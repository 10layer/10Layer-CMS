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
		
			source: source,
			select: function( event, ui ) {
				if (ui.item) {
					if ($(this).hasClass("multiple")) {
						//console.log(ui.item);
						var newdisp="<button class='autocomplete_item'>"+ui.item.label+"</button>";
						var newobj="<input type='hidden' value='"+ui.item.id+"' name='"+$(this).attr("tablename")+"_"+$(this).attr("fieldname")+"[]' value='' />";
						$(this).after(newobj);
						$(newdisp).button({
							icons: {
            		    		primary: "ui-icon-circle-close"
							}
						});
						$(this).after($(newdisp).button({
							icons: {
    		    				primary: "ui-icon-circle-close"
							}
						}));
						$(this).val("");
						return false;
					} else {
						$("#autocomplete_"+$(this).attr("contenttype")+"_"+$(this).attr("fieldname")).val(ui.item.id);
						alert($("#autocomplete_"+$(this).attr("contenttype")+"_"+$(this).attr("fieldname")).val());
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
			
	$(".autocomplete_item").each(function() {
		$(this).button({
			icons: {
                primary: "ui-icon-circle-close"
			}
		});
	});
			
	$(".autocomplete_item").live("click", function() {
		$(this).next().remove();
		$(this).remove();
		return false;
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