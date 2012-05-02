<script type="text/javascript" src="/tlresources/file/js/underscore-1.1.6.js"></script>
<script type="text/javascript" src="/tlresources/file/js/backbone.js"></script>
<script>

jQuery.fn.center = function () {
	//Get the window height and width
	var winH = $(window).height();
	var winW = $(window).width();
	
	var top_pos = winH/2-$(this).height()/2;
	var left_pos = winW/2-$(this).width()/2;
              
		//Set the popup window to center
	this.css('top',  top_pos);
	this.css('left', left_pos);

    this.css("position","absolute");
    //this.css("top", ($(window).height() / 2) + "px");
    //this.css("left", ($(window).width()  / 2) + "px");
    return this;
}

	$(function() {
		Backbone.emulateJSON = true;
		Backbone.emulateHTTP = true;
		
		
		/*----------
		/ QUEUES   /
		----------*/
		var queuecount=0;
		
		queueModel=Backbone.Model.extend({
			/*
defaults: function() {
				queuecount++;
				id=Math.round(new Date().getTime()/1000);
				return {
					name: "Queue "+queuecount,
					order: queuecount,
					id: id,
				}
			},
			url: function() {
				return "/queues/content/queues/"+this.get("id");
			},
*/
		});
		
		queueCollection=Backbone.Collection.extend({
			model: queueModel,
			url: "/queues/content/queues",
		});
		
		queueView=Backbone.View.extend({
			tagName: "div",
			className: "queue",
			template: _.template($("#queue-template").html()),
			firstrun: true,
			queueid: function() { return this.model.get("id") },
			render: function() {
				this.queueid=this.model.get("id");
				var root=this;
				var preffered_width = (this.model.get("width") > 950 ) ? 950 : this.model.get("width");
				preffered_width = (this.model.get("width") <= 220 ) ? 220 : this.model.get("width");
				
    			this.model.set({"width":preffered_width});
    						
				$(this.el).html(this.template(this.model.toJSON()));
				this.nameinput=this.$(".queuename-edit");
				this.nameinput.bind('blur', _.bind(this.saveName, this));
				this.$(".options_dropdown").button({
	    	        icons: {
    	            	primary: "ui-icon-gear"
            		},
        	    	text: false
	    	    })
		        .click(
        			function() {
        				//needed to hack this here in order to center the popup
        				var container = $(this).parent().parent().parent();
        				var displayer = $(this).parent().next();
        				
        				if (displayer.css('display') == 'none'){
   							container.removeClass("ui-resizable");
   							displayer.toggle().center();
						}else{
							container.addClass("ui-resizable");
   							displayer.toggle();
						}
						
    	    		}
		        );
		        
		        this.$(".config_close").button({
		        	icons: {
        				primary: "ui-icon-close",
		        	},
        			text: false,
		        }).click(function(){
		        
		        	//needed to hack this here in order to center the popup
        				var container = $(this).parent().parent().parent();
        				var displayer = $(this).parent();
        				container.addClass("ui-resizable");
   						displayer.toggle();		        		
		        		
		        });
		        
		        this.$(".options_personalise").button({
		        	icons: {
        				primary: "ui-icon-person",
		        	},
        			text: false,
        		}).click(function(){
        			var this_queue = $(this).parent().parent();
        			var id = $(this).parent().parent().attr("id");
        								
					if(!this_queue.hasClass("personal")){
						$.post("queues/content/personalise/"+id+"/"+"on", function(data) {
							
						});
						this_queue.addClass("personal");
					}else{
						$.post("queues/content/personalise/"+id+"/"+"off", function(data) {
						
						});
						this_queue.removeClass("personal");
					}
					
        		});
		        
		        this.$(".options_close").button({
		        	icons: {
        				primary: "ui-icon-close",
		        	},
        			text: false,
		        })
		        .click(
		        	function() {
		        		$( "#dialog_confirm_queue_delete" ).dialog({
		        			resizable: false,
							height:140,
							modal: true,
							buttons: {
								"Delete": function() {
									root.model.destroy({success: function() {
		        						$(root.el).hide();
		    			    			queuecount--;
					        		}});
									$( this ).dialog( "close" );
								},
								Cancel: function() {
									$( this ).dialog( "close" );
								}
							}
						});
		        	}
		        );
		        
		        this.content=new contentCollection;
		        this.content.queueid=this.queueid;
		        this.content.bind("reset", this.contentRender, this);
		        this.content.fetch();
				
				this.contenttypes=new contenttypesCollection;
				this.contenttypes.queueid=this.queueid;
				this.contenttypes.bind("reset", this.contenttypesSetup, this);
				this.contenttypes.fetch({success: function() {
					root.contenttypes.each(function(model) {
						model.content=this.content;
						model.set({ "queueid": root.model.get("id")}); 
					});
				}});
				this.contenttypes.bind("saved", function(e) { this.content.fetch() }, this);
				
				this.workflows=new workflowCollection;
				this.workflows.queueid=this.queueid;
				this.workflows.bind("reset", this.workflowsSetup, this);
				this.workflows.fetch({success: function() {
					root.workflows.each(function(model) {
						model.set({ "queueid": root.model.get("id")}); 
					});
				}});
				this.workflows.bind("saved", function(e) { this.content.fetch() }, this);
				$(this.el).bind("contentchange", function(e) { root.content.fetch() }, this);
				
				return this;
			},
			events: {
				"click .queue-name": "editName",
				"keypress .queuename-edit":"updateNameOnEnter",
				"save_update":"saveUpdate",
			},
			
			saveUpdate: function(e) {
				var cts=this.contenttypes.toJSON();
				var root=this;
				$.post("queues/content/update/"+this.queueid, { contenttypes: JSON.stringify(cts) }, function(data) {
					root.content.fetch();
				});
			},
			
			editName: function(e) {
				el=e.currentTarget;
				var currentname=$(el).html();
			},
			
			saveName: function(e) {
				this.model.save({ name: this.nameinput.val() });
			},
			
			updateNameOnEnter: function(e) {
				if (e.keyCode == 13) this.saveName();
			},
			
			contentRender: function(content) {
				var root=this;
				$(this.el).find(".queue-content").empty();
				content.each(function(ct) {
					var view=new ContentItemView({model: ct});
					$(root.el).find(".queue-content").append(view.render().el);
				});
			},
			
			contenttypesSetup: function(contenttypes) {
				var root=this;
				contenttypes.each(function(ct) {
					var view=new contenttypeView({model: ct});
					$(root.el).find(".contenttypes").append(view.render().el);
				});
				if (!this.firstrun) {
					this.content.fetch();
				}
				this.firstrun=false;
			},
			
			workflowsSetup: function(wfs) {
				var root=this;
				wfs.each(function(ct) {
					var view=new contenttypeView({model: ct});
					$(root.el).find(".workflows").append(view.render().el);
				});
			},
			
		});
		
		 var queues=new queueCollection;
		 		
		/*----------
		/ CONTENT  /
		----------*/
		
		content=Backbone.Model.extend({});
		
		contentCollection=Backbone.Collection.extend({
			model: content,
			queueid: 0,
			url: function() {
				return "/queues/content/contentlist/"+this.queueid;
			},
		});
		
		content=new contentCollection;
		
		ContentItemView=Backbone.View.extend({
			tagName: "li",
			template: _.template($('#content-template').html()),
			render: function() {
				//root=this;
				$(this.el).html(this.template(this.model.toJSON()));
				//this.setText();
				
				this.$(".btn-send").button({
					icons: {
						primary: "ui-icon-transfer-e-w",
					},
					text: false,
				}).click(function(){
					var container = $(this).parent().next();
					if(container.html() == ""){
						$.get("/queues/content/load_recipients/", function(data) {
							container.html(data).toggle();
							$(".add_to").button({icons: {primary: "ui-icon-circle-plus",},text: false,}).click(function(){
								var container = $(this).parent().parent();
								var user_id = $(this).parent().attr("id");
								var item_id = $(this).parent().parent().prev().children(":first").attr("id");
								$.get("/queues/content/send_to/"+user_id+"/"+item_id, function(data) {
									alert(data); //container.html(data).slideToggle();
								});
					
								container.toggle();
							});
							$(".remove_from").button({icons: {primary: "ui-icon-circle-minus",},text: false,}).click(function(){
									var user_id = $(this).parent().attr("id");
									var item_id = $(this).parent().parent().prev().children(":first").attr("id");
									$.get("/queues/content/remove_from/"+user_id+"/"+item_id, function(data) {
										alert(data); //container.html(data).slideToggle();
									});
					
									$(this).parent().parent().toggle();
							});
						});
					}else{
						container.toggle();
					}
					

				});
				
				this.$(".btn-edit").button({
					icons: {
						primary: "ui-icon-pencil",
					},
					text: false,
				});
				
				this.$(".btn-workflownext").button({
					icons: {
						primary: "ui-icon-arrowthick-1-e",
					},
					text: false,
				});
				this.$(".btn-workflowprev").button({
					icons: {
						primary: "ui-icon-arrowthick-1-w",
					},
					text: false,
				});
				
				if (this.model.get("live")=="1") {
					liveicon="ui-icon-close";
				} else {
					liveicon="ui-icon-check";
				}
				this.$(".btn-live").button({
					icons: {
						primary: liveicon,
					},
					text: false,
				});
				//this.bind("all", function(e) { console.log(e); });
				return this;
			},
			events: {
				"dblclick .content": "edit",
				"click .btn-edit": "edit",
				"click .btn-workflownext": "workflownext",
				"click .btn-workflowprev": "workflowprev",
				"click .btn-live": "live",
				"click .btn-send" : "send",
			},
			edit: function() {
				location.href="/edit/"+this.model.get("content_type")+"/"+this.model.get("urlid");
			},
			workflownext: function() {
				var root=this;
				$.getJSON("/workflow/change/advance/"+this.model.get("content_type")+"/"+this.model.get("urlid"), function(result) {
					root.model.set({"major_version": result.major_version});
					root.render();
				});
			},
			workflowprev: function() {
				var root=this;
				$.getJSON("/workflow/change/revert/"+this.model.get("content_type")+"/"+this.model.get("urlid"), function(result) {
					root.model.set({"major_version": result.major_version});
					root.render();
				});
			},
			send:function(){
				var root=this;
				//$.getJSON("/queues/content/load_recipients/", function(results) {
					//alert(results);
					//root.model.set({"live": result.live});
					//root.render();
				//});
			},
			live: function() {
				var root=this;
				$.getJSON("/workflow/change/togglelive/"+this.model.get("content_type")+"/"+this.model.get("urlid"), function(result) {
					root.model.set({"live": result.live});
					root.render();
				});
			}
		});
		
		  /*----------------/
		 / CONTENTTYPES   /
		/---------------*/
		
		contenttype=Backbone.Model.extend({
			defaults: function() {
				return {
					checked: true,
					queueid: 0,
				}	
			},
			toggle: function() {
				var root=this;
				this.save({checked: !this.get("checked")}, { success: function() { root.trigger("saved") } } );
			},
			check: function() {
				this.set({ checked: true });
			},
			uncheck: function() {
				this.set({ checked: false });
			},
		});
		
		contenttypesCollection=Backbone.Collection.extend({
			model: contenttype,
			queueid: 0,
			url: function() {
				return "/queues/content/contenttypes/"+this.queueid;
			},
		});
		
		contenttypes=new contenttypesCollection;
		
		contenttypeView=Backbone.View.extend({
			tagName: "div",
			template: _.template($("#contenttypes-template").html()),
			events: {
				"click .contenttype_check": "toggleSelect",
			},
			render: function() {
				var root=this;
				$(this.el).html(this.template(this.model.toJSON()));
				$(this.el).bind("check", function() { root.model.check(); }, this);
				$(this.el).bind("uncheck", function() { root.model.uncheck(); }, this);
				return this;
			},
			toggleSelect: function(e) {
				this.model.toggle();
			},
		});
		
		/*----------
		/ WORKFLOW  /
		----------*/
		
		workflowModel=Backbone.Model.extend({
			defaults: function() {
				return {
					checked: true,
					queueid: 0,
				}	
			},
			toggle: function() {
				root=this;
				this.save({checked: !this.get("checked")}, {success: function() { root.trigger("saved") } } );
			},
		});
		
		workflowCollection=Backbone.Collection.extend({
			model: workflowModel,
			queueid: 0,
			url: function() {
				return "/queues/content/workflows/"+this.queueid;
			},
		});
		
		workflows=new workflowCollection;
		
		workflowView=Backbone.View.extend({
			tagName: "div",
			template: _.template($("#workflows-template").html()),
			events: {
				"click .workflow_check": "toggleSelect",
			},
			render: function() {
				$(this.el).html(this.template(this.model.toJSON()));
				return this;
			},
			toggleSelect: function(e) {
				this.model.toggle();
			},
		});
		
		/*----------
		/ DRAWIT   /
		----------*/
		
		window.QueuesView = Backbone.View.extend({
			el: $("#content"),
			initialize: function() {
				queues.bind("reset", this.init, this);
				queues.fetch();
				queues.bind("edited", this.edited, this);
			},
			
			events: {
				".addqueue click": "newQueue",
				"#tiles click":"retile"
			},
			
			init: function(queues) {
				//clean the container first
				$('#queues').html("");
				var root=this;
				if (queues.length==0) { //No queues, make some
					this.newQueue();
				} else { //Queues exist, draw them					
					queues.each(function(model) {
						root.drawQueue(model);
					});
				}
				this.addqueuebutton=$(".addqueue");
				this.addqueuebutton.bind('click', _.bind(this.newQueue, this));
				
				this.retile_button=$("#tiles");
				this.retile_button.bind('click', _.bind(this.retile, this));
				this.addBehaviour();

				$('#queues').append("<br clear='both'>");
								
				
				
			},
			
			drawQueue: function(queue, new_) {
				var view = new queueView({ model: queue });
				if(!new_){
					$("#queues").append(view.render().el);
				}else{
					$("#queues").prepend(view.render().el);
				}
				
			},
			
			newQueue: function() {
				var model= new queueModel();
				
				var queue_count = $("#queues").children().length + 1;
								
				var id=Math.round(new Date().getTime()/1000);
				model.set({"name": "Queue_"+queue_count,"order":queue_count,"height":160, "width":220, "id":id, "personal":""});
				this.drawQueue(model,true);
				queues.add(model);
				model.save();
				
				//we save all because we want to store the new order
				selecteds = [];
      			var items = $("#queues").children("div");
      			items.each(function(index){
      				the_id = $(this).children(":first").attr("id");
      				selecteds.push(the_id);
      			});
      					
      			var params = {'selecteds[]': selecteds}
      					
      			$.post("/queues/content/set_queue_order", params,function(data){ });

				
				//model.save();
			},
			
			addBehaviour:function(){
				$(".options").draggable();
				$('#queues').sortable({
					
					stop: function(event,ui){
      					var items = $("#queues").children("div");
      					//go through the list and update collection items
      					items.each(function(index){
      						the_id = $(this).children(":first").attr("id")
      						the_model = queues.get(the_id);
      						updates = {order:index+1,height:$(this).height(), width:$(this).width()};
      						the_model.set(updates);
      					});
      					
      					selecteds = [];
      					var items = $("#queues").children("div");
      					items.each(function(index){
      						the_id = $(this).children(":first").attr("id");
      						selecteds.push(the_id);
      					});
      					
      					var params = {'selecteds[]': selecteds}
      					
      					$.post("/queues/content/set_queue_order", params,function(data){
      						//alert(data);
      					});
      							
          			}
				});
      			$(".queue").resizable({minHeight: 200, minWidth: 240, maxHeight: 500, maxWidth: 950,
      			
      				resize:function(){
      					//adjust the size of the inner ones as well
      					var item = $(this);
      					var formatter = item.children(":first").find(".queue_formatter");
      					formatter.css("width",item.width()-10);
      					formatter.css("height",item.height()-40);
      			    	
						
      				}, 
      				stop: function(event,ui){
      					var items = $(this).parent().children("div");
      					items.each(function(index){
      						the_id = $(this).children(":first").attr("id"); //$(this).children(":first").attr("id");
      						the_model = queues.get(the_id);
      						updates = {order:index+1,height:$(this).height(), width:$(this).width()};
      						the_model.set(updates);
      						//the_model.save();
      						
      					});
      					
      		
      						the_queues = [];
      						var items = $("#queues").children("div");
      						items.each(function(index){
      							the_id = $(this).children(":first").attr("id");
      							var item = the_id+"|"+$(this).height()+"|"+$(this).width();
      							the_queues.push(item);
      						});
      						
      						var params = {'selecteds[]': the_queues}
      						
      						$.post("/queues/content/set_queue_size", params,function(data){
      							//alert(data);
      						});
	
          			}
          			
          			});
          		$( ".queue" ).resizable( "option", "grid", [5, 5] );
      			
			},
			
			edited: function() {
				//console.log("Caught edit");
			},
			resize: function(){
				the_queues = [];
      				var items = $("#queues").children("div");
      				items.each(function(index){
      				    the_id = $(this).children(":first").attr("id");
      				    var item = the_id+"|"+$(this).height()+"|"+$(this).width();
      				    the_queues.push(item);
      				});
      				
      				var params = {'selecteds[]': the_queues}
      				
      				$.post("/queues/content/set_queue_size", params,function(data){
      				    //alert(data);
      				});

			},
			retile:function(){
				//$("#queues").html("");
				var items = $("#queues").children("div");
      				var number;
      				items.each(function(index){
      					number = index+1;
      					the_id = $(this).children(":first").attr("id");
      					the_model = queues.get(the_id);
      					updates = {order:number,height:160, width:220};
      					the_model.set(updates);
      					//the_model.save();      					//console.log(index);
      				});
      				
      				 				
      				this.init(queues);
      				this.resize();
      				//window.location.reload();
      				//Backbone.history.navigate("/home", true);
			}
			
		});
		
		window.App = new QueuesView;
		
	});
</script>

<script type="text/template" id="content-template">
	<div class="content">
		
		<div class="content-tools">
			<div class="btn-send" id="<%= id %>">Send to</div>
			<div class="btn-edit">Edit</div>
			<div class="btn-workflowprev">Revert Workflow</div>
			<div class="btn-workflownext">Advance Workflow</div>
			<div class="btn-live"><%= live ? 'Make unlive' : 'Make live' %></div>
		</div>
		<div class="directory_container shadow"></div>
		<div class="content-title content-workflow-<%= major_version %>"><%= title %></div>
	</div>
</script>





<script type="text/template" id="contenttypes-template">
	<div class="contenttype">
		<input class="contenttype_check" type="checkbox" <%= checked ? 'checked="checked"' : '' %> value="<%= urlid %>" /> <%= name %>
	</div>
</script>

<script type="text/template" id="workflows-template">
	<div class="workflow">
		<input class="workflow_check" type="checkbox" <%= checked ? 'checked="checked"' : '' %> value="<%= urlid %>" /> <%= name %>
	</div>
</script>

<script type="text/template" id="queue-template">
	<div id="<%= id %>" class="<%= personal %>" >
		
		<div class="options_icons">
			<div class="queue-name"><input class="queuename-edit" name="queuename" value="<%= name %>" /></div>
			<div class="options_close">Delete queue</div>
			<div class="options_dropdown">Filter queue</div>
			<div class="options_personalise">Make this queue personal</div>
		</div>
		
				<div class="options shadow" style="z-index:100000;">
		
				<a class="config_close">close</a>
				
				<h4><%= name %> Configuarations...</h4>
				
				<div class="option">
					<div class="option_header">Content Types</div>
				</div>
				
				<div class="option_popout contenttypes">
				<div class="allnone"><span class="select-all">All</span> | <span class="select-none">None</span></div>
				
				</div>
				
				<div class="option">
					<div class="option_header">Workflow</div>
				</div>
				<div class="option_popout workflows"></div>
				<br clear="both">
			</div>
		
		
	
		<div class="queue_formatter" style="height:<%= height %>px; width:<%= width %>px">
		<div class="queue-content"></div>
		</div>
	</div>
</script>

<style>

	#queues{
		background: #fff;
		padding: 5px;
		min-height: 500px;
	}
	
		
	.queue {
		float: left;
		margin: 5px;
		margin-bottom: 10px;
		border:1px solid #ccc;
		-moz-border-radius: 5px;
		border-radius: 5px;
		background: #fff;
		
	}
	
	.queue_formatter{
		overflow: auto;
		padding:5px;
	}
	
	
	.queue_header{
		    background-color: #CCCCCC;
    		color: #FFFFFF;
    		cursor: pointer;
    		height: 20px;
    		padding: 5px;
    		-moz-border-radius: 5px;
			border-radius: 5px;
	}
	
	.queue-name {
		width: 180px;
		float: left;
	}

	
	.options_close, .options_dropdown, .options_personalise{
		height: 14px;
		width:14px;
		float:right;
	}
	
	.options_icons {
		height: 20px;
		background-color: #ccc;
		color: #FFF;
		cursor: pointer;
		padding: 5px;
	}
	
	.options_icons input {
		border:1px solid #999;
		color: #999;
		padding: 2px;
		
	}
	
	.options_close, .options_dropdown{
		height: 14px;
		width:14px;
		float:right;
	}
	
	.options {
    	background-color: #FFFFFF;
    	border: 1px solid #CCCCCC;
    	border-radius: 5px 5px 5px 5px;
    	cursor: pointer;
    	display: none;
    	margin-top: 5px;
    	padding: 10px;
    	/* position: absolute; */
    	/* right: 41px; */
    	/* top: -15px; */
    	width: 600px;
    	z-index: 999;
	}
	
	.option_header {
    	background: none repeat scroll 0 0 #999999;
    	border: 1px solid #CCCCCC;
    	border-radius: 5px 5px 5px 5px;
    	clear: both;
    	color: #FFFFFF;
    	margin-top: 3px;
    	padding: 10px;
	}		
	
	.contenttype, .workflow {
    	border-bottom: 1px solid #CCCCCC;
    	float: left;
    	height: 16px;
    	line-height: 17px;
    	margin: 5px;
    	padding: 5px;
    	width: 177px;
	}
	
	.config_close {
		float: right;
		height:12px;
		width:12px;
	}
	
	.select-all, .select-none{
		margin: 0 5px;
		
	}
	
	.select-all:hover, .select-none:hover{
		text-decoration: underline;
	}
	
	.allnone {
    	background: none repeat scroll 0 0 #CCCCCC;
    	border: 1px solid #CCCCCC;
    	color: #FFFFFF;
    	margin-top: 5px;
    	padding: 5px;
    	text-align: center;
    	width: 100px;
	}
		
	.content-tools div {
		width: 12px;
		height: 12px;
	}
	
	
	.queue-content {
		padding:3px;
	}
	.queue-content li{
		list-style: none;
	}
	
	.queue-content li .content{
		border:1px solid #ccc;
		padding:2px;
		margin-top: 3px;
		-moz-border-radius: 5px;
		border-radius: 5px;
		background: #f1fcf8;
	}
	
	.queue-content li .content:hover{
		background: #c9f8e6;
	}
	
	.personal{
		background: #ffd9a2;
	}
	
	/*
.directory_container{
		border:1px solid #ffd9a2;
		padding: 5px;
		display: none;
		margin-bottom: 5px;
		background: #fff;
		-moz-border-radius: 5px;
		border-radius: 5px;
	}
*/
	
.directory_container {
    background: none repeat scroll 0 0 #999;
    border: 1px solid #ccc;
    border-radius: 5px 5px 5px 5px;
    display: none;
    left: -5px;
    margin-top: -1px;
    min-height: 50px;
    padding: 5px;
    position: absolute;
    width: 248px;
    z-index: 2000;
    height:300px;
    overflow: auto;
}
	
	
	.user_item{
		padding: 5px;
		background: #fff;
		-moz-border-radius: 5px;
		border-radius: 5px;
		margin-top: 3px;
	}
	.user_item:hover{
		background: #70C046;
		cursor: pointer;
		color: #fff;
	}
	
	.user_item span{
		width:15px;
		height: 15px;
		float: right;
	}
		
	
</style>

<script>





	$(function() {
	
		
		
	
		$(".addqueue").button({
			icons: {
				primary: "ui-icon-circle-plus",
			}
		});
		$("#tiles").button({
			icons: {
				primary: "ui-icon-circle-plus",
			}
		});

		
				
		function update_content(contenttype,urlid) {
			alert("Changed: "+contenttype+"/"+urlid);
		}
		
		$(".select-all").live("click",function() {
			$(this).parent().parent().find("input:checkbox").each(function() { 
				$(this.el).trigger("check");
				$(this).attr("checked", true);
			});
			$(this).trigger("save_update");
			
		});
		
		$(".select-none").live("click",function() {
			$(this).parent().parent().find("input:checkbox").each(function() { 
				//console.log("testing");
				$(this.el).trigger("uncheck");
				$(this).attr("checked", false);
			});
			$(this).trigger("save_update");
		});
		
	});
	
	function edit(contenttype, urlid) {
		$(".queue").each(function() {
			$(this).trigger("contentchange");
		});
	}
	
	function create(contenttype, urlid) {
		$(".queue").each(function() {
			$(this).trigger("contentchange");
		});
	}
	
</script>

<div id="homepage">

	<div id="dialog_first_queue" style="display: none">
		<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>You don't have any queues. Create one now?</p>
	<div id="dialog_confirm_queue_delete" style="display: none">
		<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Delete this queue?</p>
	</div>
	</div>
	<div id="topbuttons">
		<div class="addqueue">Add a queue</div> <div id="tiles">Tile queues</div>
	</div>
	
	<div id="queues">
		
	</div>
</div>

