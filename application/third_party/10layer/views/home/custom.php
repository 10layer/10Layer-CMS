<script type="text/javascript" src="/tlresources/file/js/underscore-1.1.6.js"></script>
<script type="text/javascript" src="/tlresources/file/js/backbone.js"></script>
<script>
	$(function() {
		Backbone.emulateJSON = true;
		Backbone.emulateHTTP = true;
		
		
		/*----------
		/ QUEUES   /
		----------*/
		var queuecount=0;
		
		queueModel=Backbone.Model.extend({
			defaults: function() {
				queuecount++;
				return {
					name: "Queue "+queuecount,
					order: queuecount,
				}
			},
			url: function() {
				return "/queues/content/queues/"+this.get("order");
			},
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
			queueid: 0,
			render: function() {
				var root=this;
				this.queueid=root.model.get("order");
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
        				//console.log($(this).parent());
        				$(this).parent().next(".options").toggle();
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
						model.set({ "queueid": root.model.get("order")}); 
					});
				}});
				this.contenttypes.bind("saved", function(e) { this.content.fetch() }, this);
				
				this.workflows=new workflowCollection;
				this.workflows.queueid=this.queueid;
				this.workflows.bind("reset", this.workflowsSetup, this);
				this.workflows.fetch({success: function() {
					root.workflows.each(function(model) {
						model.set({ "queueid": root.model.get("order")}); 
					});
				}});
				this.workflows.bind("saved", function(e) { this.content.fetch() }, this);
				$(this.el).bind("contentchange", function(e) { root.content.fetch() }, this);
				
				return this;
			},
			events: {
				"click .queue-name": "editName",
				"keypress .queuename-edit":"updateNameOnEnter",
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
				//console.log("contentRender");
				$("#queue_"+root.queueid).children(" .queue-content").empty();
				content.each(function(ct) {
					var view=new ContentItemView({model: ct});
					this.$("#queue_"+root.queueid+" .queue-content").append(view.render().el);
				});
			},
			
			contenttypesSetup: function(contenttypes) {
				root=this;
				//console.log("contenttypesSetup");
				//this.$("#queue_"+root.queueid+" .contenttypes").append("<div><span class='checkhelp select-all'>All</span> | <span class='checkhelp select-none'>None</span></div>");
				contenttypes.each(function(ct) {
					var view=new contenttypeView({model: ct});
					this.$("#queue_"+root.queueid+" .contenttypes").append(view.render().el);
				});
				if (!this.firstrun) {
					this.content.fetch();
				}
				this.firstrun=false;
			},
			
			workflowsSetup: function(wfs) {
				root=this;
				wfs.each(function(ct) {
					var view=new contenttypeView({model: ct});
					this.$("#queue_"+root.queueid+" .workflows").append(view.render().el);
				});
			},
			
		});
		
		queues=new queueCollection;
		
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
				var root=this;
				this.save({checked: true});
			},
			uncheck: function() {
				var root=this;
				this.save({checked: false});
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
				//console.log("toggleSelect");
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
				//content.bind('reset',   this.render, this);
				//content.bind("all", function(eventName) { console.log("Content: "+eventName)});
				//contenttypes.bind("reset", this.contenttypesSetup, this);
				//workflows.bind("reset", this.workflowsSetup, this);
				//contenttypes.fetch();
				//workflows.fetch();
				//content.fetch();
				//contenttypes.bind("all", function(eventName) { console.log("Contenttype: "+eventName)});
			},
			
			events: {
				".addqueue click": "newQueue"
			},
			
			init: function(queues) {
				var root=this;
				if (queues.length==0) { //No queues, make some
					this.newQueue();
				} else { //Queues exist, draw them
					//console.log(queues);
					queues.each(function(model) {
						//console.log(model);
						root.drawQueue(model);
					});
				}
				this.addqueuebutton=$(".addqueue");
				this.addqueuebutton.bind('click', _.bind(this.newQueue, this));
			},
			
			drawQueue: function(queue) {
				var view = new queueView({ model: queue });
				$("#homepage").append(view.render().el);
			},
			
			newQueue: function() {
				var model= new queueModel();
				this.drawQueue(model);
				model.save();
			},
			
			edited: function() {
				console.log("Caught edit");
			}
			
			/*drawContentItem: function(content) {
				var view=new ContentItemView({model: content});
				this.$("#content").append(view.render().el);
			},
			
			render: function() {
				//console.log("Render");
				$("#content").empty();
				content.each(this.drawContentItem);
			},
			
			refresh: function() {
				console.log("Refetching content");
				content.fetch();
			},
			
			contenttypesSetup: function(contenttypes) {
				contenttypes.each(function(ct) {
					var view=new contenttypeView({model: ct});
					this.$("#contenttypes").append(view.render().el);
				});
			},
			
			workflowsSetup: function(wfs) {
				wfs.each(function(ct) {
					var view=new contenttypeView({model: ct});
					this.$("#workflows").append(view.render().el);
				});
			},*/
		});
		
		window.App = new QueuesView;
		
	});
</script>

<script type="text/template" id="content-template">
	<div class="content">
		<div class="content-tools">
			<div class="btn-edit">Edit</div>
			<div class="btn-workflowprev">Revert Workflow</div>
			<div class="btn-workflownext">Advance Workflow</div>
			<div class="btn-live"><%= live ? 'Make unlive' : 'Make live' %></div>
		</div>
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
	<div id="queue_<%= order %>">
		<div class="options_icons">
			<div class="queue-name"><input class="queuename-edit" name="queuename" value="<%= name %>" /></div>
			<div class="options_dropdown">
			</div>
		</div>
		<div class="options">
			<div class="option">
				<div class="option_header">Content Types</div>
			</div>
			<div class="option_popout contenttypes"></div>
			
			<div class="option">
				<div class="option_header">Workflow</div>
			</div>
			<div class="option_popout workflows"></div>
			
		</div>
		<div class="queue-content"></div>
	</div>
</script>

<style>
	
	#homepage .queue {
		width: 300px;
		float: left;
		margin-right: 10px;
	}
	
	#homepage .options {
		display: none;
		position: absolute;
		z-index: 50;
		background-color: #FFF;
		border: 1px #CCC solid;
		width: 200px;
		padding-right: 10px;
		padding-bottom: 10px;
		margin-left: 270px;
		margin-top: 5px;
	}
	
	#homepage .queue-content {
		background: #FFF;
		border: 1px #CCC solid;
		padding: 5px;
	}
	
	#homepage .queue-content li {
		list-style: none;
		padding: 5px;
		border: 1px #CCC solid;
		margin-bottom: 5px;
	}
	
	#homepage .options_icons {
		width: 290px;
		height: 20px;
		background-color: #999;
		/*text-align: right;*/
		color: #FFF;
		cursor: pointer;
		padding: 5px;
	}
	
	#homepage .option_popout {
		display: none;
		float: right;
	}
	
	#homepage .option {
		width: auto;
		padding: 10px;
	}
	
	#homepage .options_dropdown {
		float: right;
		width: 20px;
		height: 20px;
	}
	
	#homepage .queue-name {
		width: 200px;
		float: left;
	}
	
	#homepage .option_header {
		width: 100px;
		cursor: pointer;
		font-weight: bold;
		border-bottom: 1-x #444 solid;
	}
	
	#homepage .options_icons input {
		border: none;
		color: #fff;
		background-color: #999;
	}
	
	#homepage #topbuttons {
		clear: both;
		margin-bottom: 10px;
	}
	
	#homepage .content-tools {
	}
	
	#homepage .content-tools div {
		width: 12px;
		height: 12px;
	}
	
	
</style>

<script>
	$(function() {
		$(".addqueue").button({
			icons: {
				primary: "ui-icon-circle-plus",
			}
		});
		
		$(".options_dropdown").button({
            icons: {
                primary: "ui-icon-triangle-1-s"
            },
            text: false
        })
        .click(
        	function() { 
        		$(".options").toggle();
        	}
        );
        
        /*$(".options").position({
        	my: "left bottom",
			at: "left top",
			offset: "0 100",
			of: $(".options_dropdown"),
		});*/
		
		$(".option").live("click",
			function() {
				$(this).next(".option_popout").toggle();
			}
		);
		
		function update_content(contenttype,urlid) {
			alert("Changed: "+contenttype+"/"+urlid);
		}
		
		$(".select-all").live("click",function() {
			console.log($(this).parent().parent());
			$(this).parent().parent().find("input:checkbox").each(function() { 
				$(this).trigger("check");
			});
			
		});
		
		$(".select-none").live("click",function() {
			console.log($(this).parent().parent());
			$(this).parent().parent().find("input:checkbox").each(function() { 
				$(this).attr("checked", false);
			});
			$(this).parent().parent().next().trigger("contentchange");
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
	</div>
	<div id="topbuttons">
		<div class="addqueue">Add a queue</div>
	</div>
</div>