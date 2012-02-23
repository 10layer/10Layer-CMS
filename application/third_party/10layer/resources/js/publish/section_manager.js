var date_slider_options=new Array("Forever","2 years ago","1 year ago","6 months ago","2 months ago","1 month ago","2 weeks ago","1 week ago","2 days ago","1 day ago","1 hour ago","Now");
var max_slider=11;
var min_slider=0;
var def_max_slider=11;
var def_min_slider=8;

function updateTab(skipLoad) {
	var d1=date_slider_options[$("#date_slider").slider( "values", 0 )];
	var d2=date_slider_options[$("#date_slider").slider( "values", 1 )];
	if (!d1) {
		d1=date_slider_options[def_min_slider];
	}
	if (!d2) {
		d2=date_slider_options[def_max_slider];
	}
	$("#date_slider_value").html(d1+" to "+d2);
	var tabIndex=$("#sectiontabs").tabs("option","selected");
	var searchstr="None";
	try {
		searchstr=$("#publishSearch").val();
		//console.log(searchstr);
	} catch(err) {

	}
	var section=$("#sectiontabs .ui-tabs-selected a").attr("section");
	var subsection=$("#sectiontabs .ui-tabs-selected a").attr("subsection");
	var url="/publish/worker/subsection/"+section+"/"+subsection+"/"+d1+"/"+d2+"/"+searchstr;
	//console.log("Create "+url);
	$("#sectiontabs").tabs("url", tabIndex, url);
	if (!skipLoad) {
		$("#sectiontabs").tabs("load", tabIndex);
	}
}


function get_selected(){
	selecteds = [];
	if ($("#selected_items").length > 0){
  		$('#selected_items').children('li').each(function(idx, elm) {
  			selecteds.push(elm.id.split('=')[1])
		});
		return selecteds;
	}
	else{
		return "";
	}
	
}

function update_panel(all){
	d1=date_slider_options[$("#date_slider").slider( "values", 0)];
	d2=date_slider_options[$("#date_slider").slider( "values", 1 )];
	
	if($("#subsection_selector").val() != ""){
		var searchstr = $("#publishSearch").val();
		var selecteds = get_selected();
		var url = $("#subsection_selector").val()+"/"+d1+"/"+d2+"/"+searchstr;
		var params = {'selecteds[]': selecteds, "all":all}
		
		$.get(url,params, function(data) {
			if ($("#selected_items").length > 0){
				$("#unselected_articles").html(data);
			}
			else{
				$("#the_display_panel").html(data);
			}
			$( "#selected_items" ).sortable();
			$( "#selected_items" ).disableSelection();
  		
		});

	}else{
		$("#the_display_panel").html("<h3 align='center'>Please select the zone...</h3>");
	}
	
}

function stage_changes(){
	var post_data = {"content":get_selected(), "zone_name":$("#zone_name").val(), "zone_id":$("#zone_id").val(),}
		
		$.post("/publish/worker/stage_rank_section",post_data,function(data) {
				$("#message_box").html(data).delay(2400);
				$("#message_box").html("You have staged changes, click update to save them");
		});

}


$(function() {
	
	$("#subsection_selector").live("change", function(){
		$("#the_display_panel").html("");
		update_panel(true);		
	});
	
	$("#publishSearch").live("keyup", function(){
		if ($("#selected_items").length > 0){
			update_panel(false);
		}else{
			update_panel(true);		
		}
		
	});
	
	
	$('.move_over').live('click',function(){
		$(this).attr("title", "Move out of Section List");
		$(this).children(":first").removeClass("ui-icon-circle-arrow-e").next().html("Move out of Section List");
		$(this).children(":first").addClass("ui-icon-circle-arrow-w");
		$(this).removeClass("move_over");
		$(this).addClass("move_back");
    	$("#selected_items").prepend($(this).parent());
    	$(this).parent().effect("pulsate", { times:3 }, 500);
    	if(!$(this).parent().parent().hasClass("staged")){
			$(this).parent().parent().parent().addClass("staged");
		}
    	stage_changes();
    	
	});
	
	$('.move_back').live('click',function(){
	
		if(!$(this).parent().parent().hasClass("staged")){
			$(this).parent().parent().parent().addClass("staged");
		}
		$(this).attr("title", "Move to Section List");
		$(this).children(":first").removeClass("ui-icon-circle-arrow-w").next().html("Move to Section List");
		$(this).children(":first").addClass("ui-icon-circle-arrow-e");
		$(this).removeClass("move_back");
		$(this).addClass("move_over");
    	$("#unselected_items").prepend($(this).parent());
    	$(this).parent().effect("pulsate", { times:3 }, 500);
    	stage_changes();
    	
    	
	});
	
	
	$("#doUpdate").click(function() {
		
		var post_data = {"content":get_selected(), "zone_name":$("#zone_name").val(), "zone_id":$("#zone_id").val(),}
		
		$.post("/publish/worker/rank_section",post_data,function(data) {
				$("#message_box").html(data).delay(2400).html();
				$("#selected_items").parent().removeClass("staged");
					
		});
	});
	
	
	
	
	
	$("#date_slider").slider({
	    range: true,
	    min: min_slider,
	    max: max_slider,
	    values: [ def_min_slider, def_max_slider ],
	    stop: function(event, ui) {
	    	if ($("#selected_items").length > 0){
				update_panel(false);
			}else{
				update_panel(true);		
			}
	    },
	    slide: function(event, ui) {
	    	$("#date_slider_value").html(date_slider_options[ui.values[0]]+" to "+date_slider_options[ui.values[1]]);
	    }
	});
	
	
	
	var d1=date_slider_options[$("#date_slider").slider( "values", 0 )];
	var d2=date_slider_options[$("#date_slider").slider( "values", 1 )];
	try {
		$("#date_slider_value").html(d1+" to "+d2);
	} catch(err) {

	}
	
	
	$(".btn-edit").live("click", function(){
		location.href="/edit/"+$(this).parent().parent().attr("contenttype")+"/"+$(this).parent().parent().attr("urlid");
	});
	
	$(".btn-workflowprev").live("click", function(){
		
	  $.getJSON("/workflow/change/revert/"+$(this).parent().parent().attr("contenttype")+"/"+$(this).parent().parent().attr("urlid"), function(result) {
			//to do something to reflect the change
	  });
	});
	
	$(".btn-workflownext").live("click", function(){
		
	  $.getJSON("/workflow/change/advance/"+$(this).parent().parent().attr("contenttype")+"/"+$(this).parent().parent().attr("urlid"), function(result) {
			//to do something to reflect the change

	  });
	});
	
	$(".btn-live").live("click", function(){
		
	$.getJSON("/workflow/change/togglelive/"+$(this).parent().parent().attr("contenttype")+"/"+$(this).parent().parent().attr("urlid"), function(result) {
			//to do something to reflect the change
	  });
	});

	
});