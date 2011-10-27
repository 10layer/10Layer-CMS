<?php
	link_js("/tlresources/file/js/publish/section.js");
?>
<div class="message hidden" id="message"></div>
<div id="controlls">
	<div id="btn_submit">
		<button aria-disabled="false" role="button" class="ui-button-text-icons ui-button ui-widget ui-state-default ui-corner-all " id="doUpdate"><span class="ui-button-text"><span class="ui-button-icon-primary ui-icon ui-icon-check"></span>Update Content</span></button>
	</div>
	<div id="date_slider_container">
		<div id="date_slider_value"></div>
		<div id="date_slider"></div>
	</div>
	<div id="search">
		<input type="text" id="publishSearch" value="" />
	</div>
</div>
<br clear="both" />
<br />
<div id="sectiontabs">
	<ul >
	<?php
		foreach($zones as $zone) {
		
	?>
		<li class="subsection_title">
			<a id="<?= $section_urlid ?>_ajax_loader"  section="<?= $section_urlid ?>" subsection="<?= $zone->urlid ?>" class="ajax_loader" href="/publish/worker/subsection/<?= $section_urlid ?>/<?= $zone->urlid ?>"><?= $zone->getData()->title ?></a>
		</li>
	<?php
		}
	?>
	</ul>
	
</div>



<form method="post" id="update_form">
	<input type="hidden" id="section_id" name="section_id" value="<?= $section_id ?>" />
</form>
