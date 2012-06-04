<script>
	function leadingZeros(s) {
		if (s<10) {
			return '0'+s;
		}
		return s;
	}
	
	function sqlCurrentDate() {
		var d = new Date();
		return leadingZeros(d.getFullYear())+'-'+leadingZeros(d.getMonth()+1)+'-'+leadingZeros(d.getDate());
	}
	
	function sqlCurrentTime() {
		var d = new Date();
		return leadingZeros(sqlCurrentDate())+' '+leadingZeros(d.getHours())+':'+leadingZeros(d.getMinutes());
	}
</script>

<script type='text/template' id='edit-field-autocomplete'>
	<label class='<%= field.label_class %>'><%= field.label %></label>
	<input id='autocomplete_view_<%= field.tablename %>_<%= field.name %>' type='text' tablename='<%= field.tablename %>' contenttype='<%= field.contenttype %>' fieldname='<%= field.name %>' class="autocomplete <%= (field.multiple==1) ? 'multiple' : '' %> <%= field.class %>" value='' <%= (field.contenttype=='mixed') ? "mixed='mixed' contenttypes='"+field.contenttypes.join(",")+"'" : '' %> />
	<br clear="both" />
	
	<div class="aligner">
	<ul class="items_container">
	
	<%
		if (field.data) {
			var x=0;
			_.each(field.data, function(data) {
				var value=data.content_id;
				title=data.fields.title.value;
	%>
	
	<li class="autocomplete_item">
		<span class="ui-icon ui-icon-arrowthick-2-n-s float-left" style="margin:10px;"></span>
		<span class="remover ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button" aria-disabled="false">
			<span class="ui-button-icon-primary ui-icon ui-icon-circle-close"></span>
			<span class="ui-button-text">
				<%= title %>
			</span></span>
	<input id="autocomplete_<%= field.contenttype %>_<%= field.name %>_<%= value %>" type="hidden" name="<%= field.tablename %>_<%= field.name %><%= (field.multiple==1) ? '[]' : '' %>" value="<%= value %>"  />
	</li>
	
	<%
			});
		}
	%>

	</ul>
	</div>

	<% if (field.external==1) { %>
	<br clear="both"><br clear="both">
	<button style="margin-left: 110px" id="add_relation_<%= field.tablename %>_<%= field.name %>" contenttype="<%= field.contenttype %>" fieldname="<%= field.name %>" tablename="<%= field.tablename %>" class="add-relation"><span class="ui-button-text"><span class="ui-button-icon-primary ui-icon ui-icon-plusthick"></span>New</span></button>
	<br clear="both">
	<%
		}
	%>
</script>

<script type='text/template' id='create-field-autocomplete'>
	<label class='<%= field.label_class %>'><%= field.label %></label>
	<input id='autocomplete_view_<%= field.tablename %>_<%= field.name %>' type='text' tablename='<%= field.tablename %>' contenttype='<%= field.contenttype %>' fieldname='<%= field.name %>' class="autocomplete <%= (field.multiple==1) ? 'multiple' : '' %> <%= field.class %>" value='' <%= (field.contenttype=='mixed') ? "mixed='mixed' contenttypes='"+field.contenttypes.join(",")+"'" : '' %> />
	<br clear="both" />
	
	<div class="aligner">
	<ul class="items_container"></ul>
	</div>

	<% if (field.external==1) { %>
	<br clear="both"><br clear="both">
	<button style="margin-left: 110px" id="add_relation_<%= field.tablename %>_<%= field.name %>" contenttype="<%= field.contenttype %>" fieldname="<%= field.name %>" tablename="<%= field.tablename %>" class="add-relation"><span class="ui-button-text"><span class="ui-button-icon-primary ui-icon ui-icon-plusthick"></span>New</span></button>
	<br clear="both">
	<%
		}
	%>
</script>

<script type='text/template' id='edit-field-boolean'>
	<label class='<%= field.label_class %>'><%= field.label %></label>
	<input type='checkbox' name='<%= field.tablename %>_<%= field.name %>' value='1' class='<%= field.class %>' <%= (field.value==1) ? "checked='checked'" : '' %> />
	<br clear='both' />
</script>

<script type='text/template' id='create-field-boolean'>
	<label class='<%= field.label_class %>'><%= field.label %></label>
	<input type='checkbox' name='<%= field.tablename %>_<%= field.name %>' value='1' class='<%= field.class %>' <%= (field.value==1 || field.value==true) ? "checked='checked'" : '' %> />
	<br clear='both' />
</script>

<script type='text/template' id='edit-field-cdn'>
	<label class='<%= field.label_class %>'><%= field.label %></label>
	<input type='text' name='<%= field.tablename %>_<%= field.name %>' value='<%= field.value %>' class='<%= field.class %>' readonly='readonly' />
	<br clear='both' />
</script>

<script type='text/template' id='create-field-cdn'>
</script>

<script type='text/template' id='edit-field-checkbox'>
	<label class='<%= field.label_class %>'><%= field.label %></label>
	<input type='checkbox' name='<%= field.tablename %>_<%= field.name %>' value='1' class='<%= field.class %>' <%= (field.value==1) ? "checked='checked'" : '' %> />
	<br clear='both' />
</script>

<script type='text/template' id='create-field-checkbox'>
	<label class='<%= field.label_class %>'><%= field.label %></label>
	<input type='checkbox' name='<%= field.tablename %>_<%= field.name %>' value='1' class='<%= field.class %>' <%= (field.value==1 || field.value==true) ? "checked='checked'" : '' %> />
	<br clear='both' />
</script>

<script type='text/template' id='edit-field-date'>
	<label class='<%= field.label_class %>'><%= field.label %></label>
	<input type='text' name='<%= field.tablename %>_<%= field.name %>' value='<%= field.value %>' class='datepicker <%= field.class %>' />
	<br clear='both' />
</script>

<script type='text/template' id='create-field-date'>
	<label class='<%= field.label_class %>'><%= field.label %></label>
	<input type='text' name='<%= field.tablename %>_<%= field.name %>' value='<%= (field.value=="now" || field.value=="Today") ? sqlCurrentDate() : field.value  %>' class='datepicker <%= field.class %>' />
	<br clear='both' />
</script>

<script type='text/template' id='edit-field-datetime'>
	<label class='<%= field.label_class %>'><%= field.label %></label>
	<input type='text' name='<%= field.tablename %>_<%= field.name %>' value='<%= field.value %>' class='datetimepicker <%= field.class %>' />
	<br clear='both' />
</script>

<script type='text/template' id='create-field-datetime'>
	<label class='<%= field.label_class %>'><%= field.label %></label>
	<input type='text' name='<%= field.tablename %>_<%= field.name %>' value='<%= (field.value=="now") ? sqlCurrentTime() : ''  %>' class='datetimepicker <%= field.class %>' />
	<br clear='both' />
</script>

<script type='text/template' id='edit-field-deepsearch'>
<div class="deepsearch">
	<label class='<%= field.label_class %>'><%= field.label %></label>
	<input id="deepsearch_view_<%= field.tablename %>_<%= field.name %>" type="text" tablename="<%= field.tablename %>" contenttype="<%= field.contenttype %>" fieldname="<%= field.name %>" class="deepsearch_input <%= (field.multiple==1) ? 'multiple' : '' %> <%= field.class %>" value="" <% (field.contenttype=='mixed') ? "mixed='mixed' contenttypes='"+field.contenttypes.join(",")+"'" : '' %> />
	<br clear="both" />
	<div class="deepsearch_results" style=" padding: 5px; background-color: #FFF; border: 1px #CCC solid; width:290px; height:300px; overflow:auto; float:left;"></div>
	<div class="selected_results" style=" padding: 5px; background-color: #FFF; border: 1px #CCC solid; width:290px; height:300px; overflow:auto; float:right;">
	<%
		if (field.data) {
			var x=0;
			_.each(field.data, function(data) {
				var value=data.content_id;
				var title=data.fields.title.value;
				var start_date=data.fields.start_date.value;
	%>
		<div class='deepsearch_selected_item'>
		<input id="deepsearch_<%= field.contenttype %>_<%= field.name %>_<%= value %>" type="hidden" name="<%= field.tablename %>_<%= field.name %><%= (field.multiple==1) ? '[]' : '' %>" value="<%= value %>"  />
		<span><%= title %> (<%= start_date %>)</span>
		</div>
	
<%
			});
		}
%>
	</div>
</div>
</script>

<script type='text/template' id='create-field-deepsearch'>
<div class="deepsearch">
	<label class='<%= field.label_class %>'><%= field.label %></label>
	<input id="deepsearch_view_<%= field.tablename %>_<%= field.name %>" type="text" tablename="<%= field.tablename %>" contenttype="<%= field.contenttype %>" fieldname="<%= field.name %>" class="deepsearch_input <%= (field.multiple==1) ? 'multiple' : '' %> <%= field.class %>" value="" <% (field.contenttype=='mixed') ? "mixed='mixed' contenttypes='"+field.contenttypes.join(",")+"'" : '' %> />
	<br clear="both" />
	<div class="deepsearch_results" style=" padding: 5px; background-color: #FFF; border: 1px #CCC solid; width:290px; height:300px; overflow:auto; float:left;"></div>
	<div class="selected_results" style=" padding: 5px; background-color: #FFF; border: 1px #CCC solid; width:290px; height:300px; overflow:auto; float:right;">
	</div>
</div>
</script>

<script type='text/template' id='edit-field-drilldown'>
	<%= _.template($("#old-fields-template").html(), { field: field, urlid: urlid, content_type: content_type }) %>
</script>

<script type='text/template' id='create-field-drilldown'>
	<%= _.template($("#old-fields-create-template").html(), { field: field, content_type: content_type }) %>
</script>

<script type='text/template' id='edit-field-embed'>
	<%= _.template($("#old-fields-template").html(), { field: field, urlid: urlid, content_type: content_type }) %>
</script>

<script type='text/template' id='create-field-embed'>
	<%= _.template($("#old-fields-create-template").html(), { field: field, content_type: content_type }) %>
</script>

<script type='text/template' id='edit-field-external'>
	<%= _.template($("#old-fields-template").html(), { field: field, urlid: urlid, content_type: content_type }) %>
</script>

<script type='text/template' id='create-field-external'>
	<%= _.template($("#old-fields-create-template").html(), { field: field, content_type: content_type }) %>
</script>

<script type='text/template' id='edit-field-file'>
	<label class='<%= field.label_class %>'><%= field.label %></label>
	<input type="file" name="<%= field.tablename %>_<%= field.name %>" class="file_upload <%= field.class %>" value="<%= field.value %>" />
	<input type="hidden" name="<%= field.tablename %>_<%= field.name %>" value="<%= field.value %>" />
</script>

<script type='text/template' id='create-field-file'>
	<label class='<%= field.label_class %>'><%= field.label %></label>
	<input type="file" name="<%= field.tablename %>_<%= field.name %>" class="file_upload <%= field.class %>" value="<%= field.value %>" />
</script>

<script type='text/template' id='edit-field-hidden'>
	<input type='hidden' name='<%= field.tablename %>_<%= field.name %>' value='<%= field.value %>' />
</script>

<script type='text/template' id='create-field-hidden'>
	<input type='hidden' name='<%= field.tablename %>_<%= field.name %>' value='' />
</script>

<script type='text/template' id='edit-field-image'>
	<label class='<%= field.label_class %>'><%= field.label %></label>
	<input type="file" name="<%= field.tablename %>_<%= field.name %>" class="file_upload <%= field.class %>" value="<%= field.value %>" data-url='/edit/fileupload/<%= content_type %>/<%= urlid %>/<%= field.tablename %>_<%= field.name %>' />
	<input type="hidden" name="<%= field.tablename %>_<%= field.name %>" value="<%= field.value %>" />
	<div class="fileupload-progress">
		<!-- The global progress bar -->
			<div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
				<div class="bar" style="width:0%;"></div>
			</div>
			<!-- The extended global progress information -->
			<div class="progress-extended">&nbsp;</div>
	</div>
	<div class='fileupload-result'></div>
<%
	if (urlid) {
%>
<div class="preview-image">
<img src="/workers/picture/display/<%= urlid %>/cropThumbnailImage/500/300?<%= Math.random() * 1000 %>" />
</div>
<%
	}
%>
<% if (field.linkformat) { %>
	<label>Download link</label>
	<div class='download_url'><input type='text' class='select_on_click' readonly='readonly' value='<%= field.linkformat.replace('{filename}', field.value) %>' /></div>
	<% } %>
<br clear='both' />
</script>

<script type='text/template' id='create-field-image'>
	<label class='<%= field.label_class %>'><%= field.label %></label>
	<input type="file" name="<%= field.tablename %>_<%= field.name %>" class="file_upload <%= field.class %>" />
	<br clear='both' />
</script>

<script type='text/template' id='edit-field-nesteditems'>
	<label class='<%= field.label_class %>'><%= field.label %></label>
	<div class="nesteditems_actions">
		<input id="nestedselect_view_<%= field.tablename %>_<%= field.name %>" name="<%= field.tablename %>_<%= field.name %>" type="hidden" tablename="<%= field.tablename %>" contenttype="<%= field.contenttype %>" fieldname="<%= field.name %>" class="nestedselect <%= field.class %>" value="<%= field.value %>" <%= (field.contenttype=='mixed') ? "mixed='mixed' contenttypes='"+field.contenttypes.join(",")+"'" : '' %> />
		<div class="nesteditems_item_label"><%= (field.data) ? field.data.fields.title.value : '' %></div>
		<button class="nesteditems_item_button" contenttype="<%= field.contenttype %>">Change...</button>
		<div class="section_list" ></div>
		<br clear="both"/><br clear="both"/>
		<button><span class="ui-button-text"><span class="ui-button-icon-primary ui-icon ui-icon-plusthick"></span>New</span></button>
	</div>
</script>

<script type='text/template' id='edit-field-nesteditems-list'>
	<ul class='nested_tree'>
	<% _.each(tree, function(item) { %>
		<li label='<%= item.title %>' content_id='<%= (item.content_link_id) ? item.content_link_id : item.content_id %>' class='nested_section'><%= item.title %>
		<%= (item.children) ? _.template($('#edit-field-nesteditems-list').html(), {tree: item.children}) : '' %>
		</li>
	<% }); %>
	</ul>
</script>

<script type='text/template' id='create-field-nesteditems'>
	<label class='<%= field.label_class %>'><%= field.label %></label>
	<div class="nesteditems_actions">
		<input id="nestedselect_view_<%= field.tablename %>_<%= field.name %>" name="<%= field.tablename %>_<%= field.name %>" type="hidden" tablename="<%= field.tablename %>" contenttype="<%= field.contenttype %>" fieldname="<%= field.name %>" class="nestedselect <%= field.class %>" value="" <%= (field.contenttype=='mixed') ? "mixed='mixed' contenttypes='"+field.contenttypes.join(",")+"'" : '' %> />
		<div class="nesteditems_item_label"><%= (field.data) ? field.data.fields.title.value : 'None selected' %></div>
		<button class="nesteditems_item_button" contenttype="<%= field.contenttype %>">Change...</button>
		<div class="section_list" ></div>
		<br clear="both"/><br clear="both"/>
		<button><span class="ui-button-text"><span class="ui-button-icon-primary ui-icon ui-icon-plusthick"></span>New</span></button>
	</div>
</script>

<script type='text/template' id='edit-field-password'>
	<label class='<%= field.label_class %>'><%= field.label %></label>
	<input type='password' name='<%= field.tablename %>_<%= field.name %>' value='<%= field.value %>' class='<%= field.class %>' />
	<br clear='both' />
</script>

<script type='text/template' id='create-field-password'>
	<label class='<%= field.label_class %>'><%= field.label %></label>
	<input type='password' name='<%= field.tablename %>_<%= field.name %>' value='' class='<%= field.class %>' />
	<br clear='both' />
</script>

<script type='text/template' id='edit-field-radio'>
	<label class='<%= field.label_class %>'><%= field.label %></label>
	<div class='radiogroup'>
	<% _.each(field.options, function(option, key) { %>
		<div class='radio'>
			<input type='radio' name='<%= field.tablename %>_<%= field.name %>' value='<%= key %>' <%= (field.value==key) ? 'checked="checked"' : '' %> />
			<div class='radio_label'><%= option %></div>
		</div>
	<% }); %>
	</div>
	<br clear='both' />
</script>

<script type='text/template' id='create-field-radio'>
	<label class='<%= field.label_class %>'><%= field.label %></label>
	<div class='radiogroup'>
	<% _.each(field.options, function(option, key) { %>
		<div class='radio'>
			<input type='radio' name='<%= field.tablename %>_<%= field.name %>' value='<%= key %>' <%= (field.value==key) ? 'checked="checked"' : '' %> />
			<div class='radio_label'><%= option %></div>
		</div>
	<% }); %>
	</div>
	<br clear='both' />
</script>

<script type='text/template' id='edit-field-readonly'>
	<label class='<%= field.label_class %>'><%= field.label %></label>
	<input type='text' name='<%= field.tablename %>_<%= field.name %>' value='<%= field.value %>' class='<%= field.class %>' readonly='readonly' />
	<br clear='both' />
</script>

<script type='text/template' id='create-field-readonly'>
	<label class='<%= field.label_class %>'><%= field.label %></label>
	<input type='text' name='<%= field.tablename %>_<%= field.name %>' value='<%= (field.value!=false) ? field.value : '' %>' class='<%= field.class %>' readonly='readonly' />
	<br clear='both' />
</script>

<script type='text/template' id='edit-field-reverse'>
	<%= _.template($("#old-fields-template").html(), { field: field, urlid: urlid, content_type: content_type }) %>
</script>

<script type='text/template' id='create-field-reverse'>
	<%= _.template($("#old-fields-create-template").html(), { field: field, content_type: content_type }) %>
</script>

<script type='text/template' id='edit-field-rich'>
	<%= _.template($("#old-fields-template").html(), { field: field, urlid: urlid, content_type: content_type }) %>
</script>

<script type='text/template' id='create-field-rich'>
	<%= _.template($("#old-fields-create-template").html(), { field: field, content_type: content_type }) %>
</script>

<script type='text/template' id='edit-field-section'>
	<%= _.template($("#old-fields-template").html(), { field: field, urlid: urlid, content_type: content_type }) %>
</script>

<script type='text/template' id='create-field-section'>
	<%= _.template($("#old-fields-create-template").html(), { field: field, content_type: content_type }) %>
</script>

<script type='text/template' id='edit-field-select'>
	<label class='<%= field.label_class %>'><%= field.label %></label>
	<select class='<%= field.class %>' name='<%= field.tablename %>_<%= field.name %>'>
	<option value="0"></option>
	<% 
	var keyadjust=0;
	_.each(field.options, function(val, key) {
		if (key==0) {
			keyadjust=1;
		}
	});
	%>
	<% _.each(field.options, function(option, key) { %>
		<option value='<%= ( key + keyadjust) %>' <%= (field.value==( key + keyadjust)) ? 'selected="selected"' : '' %> ><%= option %></option>
	<% }); %>
	</select>
	<br clear='both' />
</script>

<script type='text/template' id='create-field-select'>
	<label class='<%= field.label_class %>'><%= field.label %></label>
	<select class='<%= field.class %>' name='<%= field.tablename %>_<%= field.name %>'>
	<option value="0"></option>
	<% 
	var keyadjust=0;
	_.each(field.options, function(val, key) {
		if (key==0) {
			keyadjust=1;
		}
	});
	%>
	<% _.each(field.options, function(option, key) { %>
		<option value='<%= (key + keyadjust) %>' <%= (field.value==( key + keyadjust) ) ? 'selected="selected"' : '' %> ><%= option %></option>
	<% }); %>
	</select>
	<br clear='both' />
</script>

<script type='text/template' id='edit-field-text'>
	<label class='<%= field.label_class %>'><%= field.label %></label>
	<input type='text' name='<%= field.tablename %>_<%= field.name %>' value='<%= field.value %>' class='<%= field.class %>' />
	<br clear='both' />
</script>

<script type='text/template' id='create-field-text'>
	<label class='<%= field.label_class %>'><%= field.label %></label>
	<input type='text' name='<%= field.tablename %>_<%= field.name %>' value='<%= (field.value!=false) ? field.value : '' %>' class='<%= field.class %>' />
	<br clear='both' />
</script>

<script type='text/template' id='edit-field-textarea'>
	<label class='<%= field.label_class %>'><%= field.label %></label>
	<textarea name='<%= field.tablename %>_<%= field.name %>' class='<%= field.class %> <%= (field.showcount!==false) ? 'countchars' : '' %> <%= (_.isNumber(field.showcount)) ? 'countdown' : '' %>' <%= (_.isNumber(field.showcount)) ? 'max="'+field.showcount+'"' : '' %>><%= field.value %></textarea>
	<br clear='both' />
</script>

<script type='text/template' id='create-field-textarea'>
	<label class='<%= field.label_class %>'><%= field.label %></label>
	<textarea name='<%= field.tablename %>_<%= field.name %>' class='<%= field.class %> <%= (field.showcount!==false) ? 'countchars' : '' %> <%= (_.isNumber(field.showcount)) ? 'countdown' : '' %>' <%= (_.isNumber(field.showcount)) ? 'max="'+field.showcount+'"' : '' %>><%= (field.value!=false) ? field.value : '' %></textarea>
	<br clear='both' />
</script>

<script type='text/template' id='old-fields-template'>
	<label class='<%= field.label_class %>'><%= field.label %></label>
	<% 
		var url='/edit/field/'+field.name+'/'+content_type+'/'+urlid;
		$.get(url, function(data) {
			$('#'+field.name+'-hook').html(data);
		}); 
	%>
	<div id='<%= field.name %>-hook'></div>
	<br clear='both' />
</script>

<script type='text/template' id='old-fields-create-template'>
	<label class='<%= field.label_class %>'><%= field.label %></label>
	<% 
		var url='/create/field/'+field.name+'/'+content_type;
		$.get(url, function(data) {
			$('#'+field.name+'-hook').html(data);
		}); 
	%>
	<div id='<%= field.name %>-hook'></div>
	<br clear='both' />
</script>