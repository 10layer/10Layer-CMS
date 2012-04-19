<?php 
	$data["menu1_active"]="edit";
	$data["menu2_active"]="edit/".$type;
	$this->load->view('templates/header',$data);
	link_js("/tlresources/file/js/forms.js");
	link_js("/tlresources/file/jquery/jquery.form.js?1");
	link_js("/tlresources/file/js/forms/default.js");
	//ckeditor();
	tinymce();
?>
<script language="javascript">
	
	var dirty=false;
	var autosaveTimer=false;
	var autosaving=false;
	
	function markDirty(e) {
		dirty=true;
		//console.log("Setting timer");
		clearTimeout(autosaveTimer);
		autosaveTimer=setTimeout("autosave()", 5000);
	}
	
	function autosave() {
		//console.log("Checking autosave");
		if (dirty) {
			autosaving=true;
			$("#contentform").ajaxSubmit({
				dataType: "json",
				iframe: true,
				debug: true,
				url: "<?= base_url()."edit/autosave/$type/$urlid" ?>",
				beforeSubmit: function(a,f,o) {
					o.dataType = "json";
				},
				success: function(result) {
					autosaving=false;
					if (result.changed) {
						$("#autosave").slideDown("slow");
					} else {
						$("#autosave").slideUp("slow");
					}
				},
				error: function() {
					autosaving=false;
				},
			});
		}
		dirty=false;
	}
	
	var autosaveTimer=false;
	
	
	
	$(function() {
		$(window).unload(function() {
			if (dirty) {
				autosave();
			}
		});
		
		$("#dyncontent").keypress(function(e) {
			markDirty(e);
		});
		
		$(document).ajaxError(function(e, xhr, settings, exception) { 
			$("#dyncontent").html(xhr.responseText); 
		});
		
		$("#dyncontent").ajaxComplete(function() {
			cl.hide();
		});
		
		$("#dyncontent").ajaxStart(function() {
			if (!autosaving) {
				cl.show();
			}
		});
		
		$("#dyncontent").load("<?= base_url()."edit/fullview/$type/$urlid" ?>", function() {
			$(".datepicker").datepicker({dateFormat:"yy-mm-dd"});
			if ($(".richedit").length) {
				//initCKEditor();
				init_tinymce();
			}
		});
		
		$("#dyncontent").delegate(".pagination > a","click",function() {
			var url=$(this).attr("href");
			$("#dyncontent").load("<?= base_url()?>"+url, function() {  });
			return false;
		});
		
		function search() {
			var s=$("#listSearch").val();
			$("#loading_icon").show();
			$("#dyncontent").load("/edit/fullview/<?= $type ?>/search/"+escape(s));
		}
		
		$("#dyncontent").delegate("#listSearch", "click", function() {
			if ($(this).val()=="Search...") {
				$(this).val("");
			}
		});
		
		$("#dyncontent").delegate("#listSearch","keypress",function() {
			clearTimeout($.data(this, 'timer'));
			var wait = setTimeout(search, 1000);
			$(this).data('timer', wait);
		});
		
		$("#dyncontent").delegate(".add-relation","click",function() {
		//Creates the popup box for adding a new item
			var fieldname=$(this).attr("contenttype")+"_"+$(this).attr("fieldname");
			$("#createdialog").dialog({ minWidth: 700, modal: true, }).load(
				"/create/fullview/"+$(this).attr("contenttype")+"/embed"
			);
			$("#createdialog").data("fieldname",fieldname);
			return false;
		});
		
		
		$("#createdialog").delegate("#contentform","submit",function() {
		//Handles the submit for a new item
			$("#createdialog #contentform").ajaxSubmit({
				dataType: "json",
				iframe: true,
				
				beforeSubmit: function(a,f,o) {
					o.iframe = true;
					
				},
				success: function(data) {
					if (data.error) {
						//console.log(data);
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
						var title=data.data.title;
						var id=data.data.id;
						var fieldname=$("#createdialog").data("fieldname");
						var newoption="<option value='"+id+"'>"+title+"</option>";
						$("."+fieldname).prepend(newoption);
						$("."+fieldname).val(id);
						//$("#dyncontent").find()
						$("#createdialog").dialog("close");
						$("#msgdialog").dialog({
							modal: true,
							buttons: {
								Ok: function() {
									$(this).dialog("close");
								}
							}
						});
					}
				},
				error: function(data) {
					//console.log("Caught error");
					//console.log(data);
				}
			});
			return false;
		});
		
		$("#dyncontent").delegate("#contentform","submit",function() {
			
			$(this).ajaxSubmit({
				dataType: "json",
				iframe: true,
				debug: true,
				beforeSubmit: function(a,f,o) {
					o.dataType = "json";
				},
				
				success: function(data) {
					//console.log(data);
					if (data.error) {
						//console.log(data);
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
			});
			return false;
		});

	});
</script>

<div id="msgdialog"></div>
<div id="dyncontent">
</div>
<div id="createdialog"></div>
<?php
	$this->load->view("templates/footer");

?>