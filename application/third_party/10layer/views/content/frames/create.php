<?php 
	$data["menu1_active"]="create";
	$data["menu2_active"]="create/".$type;
	$this->load->view('templates/header',$data);
	link_js("/tlresources/file/js/forms.js");
	link_js("/tlresources/file/jquery/jquery.form.js?1");
	link_js("/tlresources/file/js/forms/default.js");
	ckeditor();
	//tinymce();
?>
<script src="/tlresources/file/js/underscore-min.js"></script>
<script src="/tlresources/file/js/jquery.pagination.js"></script>
<script src="/tlresources/file/js/davis.min.js"></script>
<script language="javascript">
	$(function() {
	
		//Router
		var app = Davis(function() {
			this.get('#', function(req) {});
			this.get('/create/:content_type', function(req) {
				$(document.body).data('content_type', req.params['content_type']);
				prepRouter();
				init_create();
			});
		});
		
		app.start();
		
		function prepRouter() {
			$('#dyncontent').children().find('.richedit').each(function() {
				var name=$(this).attr('name');
				var o=CKEDITOR.instances[name];
			    if (o) o.destroy();
			});
		}
		
		function init_create() {
			content_type=$(document.body).data('content_type');
			$(".menuitem").each(function() {
				$(this).removeClass('selected');
			});
			$('#menuitem_'+content_type).addClass('selected');
			$('#dyncontent').html("Loading...");
			$.getJSON("<?= base_url() ?>create/jsoncreate/"+content_type+"?jsoncallback=?", function(data) {
				$('#dyncontent').html(_.template($("#create-template").html(), { data:data, content_type: content_type }));
				init_form();
			});
		}
		
		$(document).on('click', '#dosubmit_right', function() {
			$("#contentform").submit();
		});
		
		$(document).ajaxError(function(e, xhr, settings, exception) { 
			//$("#dyncontent").html('<h1>Caught error</h1>'+xhr.responseText); 
		});
		
		$("#dyncontent").ajaxComplete(function() {
			cl.hide();
		});
		
		$("#dyncontent").ajaxStart(function() {
			cl.show();
		});
		
		/*$("#dyncontent").load("<?= base_url()."create/fullview/$type" ?>", function() {
			if ($(".richedit").length) {
//				init_tinymce();
				initCKEditor();
			}
			
			$(".datepicker").datepicker({dateFormat:"yy-mm-dd"});
		});*/
		
		$("#dyncontent").delegate("#contentform","submit",function() {
			
			$(this).ajaxSubmit({
				iframe: true,
				dataType: "json",
				beforeSubmit: function(a,f,o) {
					o.dataType = "json";
					
				},
				success: function(data) {
					if (data.error) {
						$("#msgdialog").html("<div class='ui-state-error' style='padding: 5px'><p><span class='ui-icon ui-icon-alert' style='float: left; margin-right: .3em;'></span><strong>"+data.msg+"</strong><br /><br /> "+data.info+"</p></div>");
						$("#msgdialog").dialog({
							modal: true,
							buttons: {
								Ok: function() {
									$( this ).dialog( "close" );
								}
							}
						});
					} else {
						$("#msgdialog").html("<div class='ui-state-highlight' style='padding: 5px'><p><span class='ui-icon ui-icon-info' style='float: left; margin-right: .3em;'></span><strong>Saved</strong></p></div>");
						$("#msgdialog").dialog({
							modal: true,
							buttons: {
								"Create another": function() {
									location.href="<?= base_url() ?>create/<?= $type ?>";
								},
								"Reuse info": function() {
									$(this).dialog( "close" );
								},
								"Edit": function() {
									location.href="<?= base_url() ?>edit/<?= $type ?>/"+data.data.urlid;
								}
							}
						});
					}
				},
			});
			return false;
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
					o.dataType = "json";
				},
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
					
				}
			});
			return false;
		});

	});
</script>

<?php
	$this->load->view("snippets/javascript_templates");
?>

<script type='text/template' id='create-template'>
	<div id="create-content" class="boxed wide">
		<h2>Create - <%= content_type %></h2>
		<form id='contentform' method='post' enctype='multipart/form-data' action='<?= base_url() ?>create/ajaxsubmit/<%= content_type %>'>
		<input type='hidden' name='action' value='submit' />
		<% _.each(data.fields, function(field) { %>
			<% if (!field.hidden) { %>
				<%= _.template($('#create-field-'+field.type).html(), { field: field, urlid: false, content_type: content_type  }) %>
			<% } %>
		<% }); %>
		</form>
	</div>
	<div id="sidebar" class="pin">
	<div id="sidebar_accordian">
		<h3><a href="#">Actions</a></h3>
		<div>
			<button id="dosubmit_right">Save</button><br />
			<br />
		</div>
	</div>
	</div>
</script>
<div id="msgdialog"></div>
<div id="createdialog"></div>
<div id="dyncontent">

</div>
<?php
	$this->load->view("templates/footer");

?>