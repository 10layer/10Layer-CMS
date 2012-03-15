<?php
	link_js("/tlresources/file/js/publish/section_manager.js");
?>

  

<?php

if(isset($zones[0])){

?>

<div id="controlls">

<input type="hidden" id="active_zone" value="/publish/worker/subsection/<?= $section_urlid ?>/<?= $zones[0]->getData()->urlid ?>">

<div id="btn_submit">

<div id="config_container">
<!-- ===================================== -->
<div id="config_section_container">
	<a id="config_section"> Publishing to <?php echo $section_data->title; ?> </a> > <span id="active_zone_display" class="auto_<?= $zones[0]->getData()->auto ?>"><?php echo $zones[0]->getData()->title; ?></span> <div style="height:15px; width:15px; float:right;" id="config_section_options">Options</div>
</div>



<div id="section_config" class="shadow">

 <div id="section_automator">
 	Automate -> <a option="all" class="mass_selector">All</a> | <a option="none" class="mass_selector">None</a>
 </div>

	<div id="zone_automators">
	
	<table class="small_table">
		<tr>
			<th>Automate</th>
			<th>Zone</t>
		</tr>
		
		<?php 
		foreach($zones as $zone){
		?>
			
		<tr>
			<td align="center"><input type="checkbox" class="zone_automator" <?php echo ($zone->getData()->auto == "0") ? "" : "checked='checked'" ?> id="<?= $zone->getData()->content_id ?>"></td>
			<th><a class="zone_selector auto_<?= $zone->getData()->auto ?>" href="/publish/worker/subsection/<?= $section_urlid ?>/<?= $zone->urlid ?>"><?= $zone->getData()->title ?></a></t>
		</tr>
			
				
		<?php
		}
			
		?>
	
	</table>
	
	</div>


 </div>

</div>
<!-- ===================================== -->


</div>
	
	
	
	<div id="date_slider_container">
		<div id="date_slider_value"></div>
		<div id="date_slider"></div>
	</div>
	<div id="search">
		<input type="text" id="publishSearch" value="Search..." />
		<span id="loading_icon" style="display:none;">
			<img src="/tlresources/file/images/loader.gif" />
		</span>

	</div>
	
</div>
<br clear="both" />
<br />

<div id="the_display_panel">

	
	
</div>

<div class="message_box" id="message_box">  </div>

<div id="btn_submit">
		<button aria-disabled="false" role="button" class="ui-button-text-icons ui-button ui-widget ui-state-default ui-corner-all " id="doUpdate"><span class="ui-button-text"><span class="ui-button-icon-primary ui-icon ui-icon-check"></span>Update Content</span></button>
	</div>

<form method="post" id="update_form">
	<input type="hidden" id="section_id" name="section_id" value="<?= $section_id ?>" />
</form>



<?php

}else{
?>

<div class="big_error_message">
	The  system has found that there are no zones defined for the selected section, Please contact the System Administrator about this issue.
</div>

<?php

}

?>

