<?php
	link_js("/tlresources/file/js/publish/section_manager.js");
?>


<div class="message hidden" id="message"></div>
<div id="controlls">

<div id="btn_submit">
<select name="subsections" id="subsection_selector">
	
<?php
foreach($zones as $zone) {	
?>
	<option value="/publish/worker/subsection/<?= $section_urlid ?>/<?= $zone->urlid ?>" class="subsection_title"><?= $zone->getData()->title ?></option>
<?php
}
?>
</select>

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

<!--
<?php
		foreach($zones as $zone) {	
	?>
		<li class="subsection_title">
			<a id="<?= $section_urlid ?>_ajax_loader"  section="<?= $section_urlid ?>" subsection="<?= $zone->urlid ?>" class="ajax_loader" href="/publish/worker/subsection/<?= $section_urlid ?>/<?= $zone->urlid ?>"><?= $zone->getData()->title ?></a>
		</li>
	<?php
		}
	?>
	
-->
