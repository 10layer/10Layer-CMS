
<form name='zone_config_form' id='zone_config_form' >

<label>Zone Title</label>

<?php
	$title = (isset($zone)) ? $zone->title : "";
	$urlid = (isset($zone)) ? $zone->urlid : "";
	$id = (isset($zone)) ? $zone->content_id : "";
	$auto = (isset($zone)) ? $zone->auto : "";
	$max_items = (isset($zone)) ? $zone->max_count : "";
	$min_items = (isset($zone)) ? $zone->min_count : "";
	$saved_content_types = (isset($zone)) ? explode(',', $zone->content_types) : array();
?>

<input type='text' value='<?= $title; ?>' name='zone_title' id='zone_title' style='margin-top:20px;width:500px; height:20px; border:1px solid #ccc; font-size:15px;'> <br clear='both'>

<input type='hidden' value='<?= $id; ?>' name='id' id='id' >

<div style='float:left; margin-right:20px;'>
	<label>Automatic Zone</label>
	<select id='zone_auto_selector' name='zone_auto_selector' style='margin-top:20px; height:30px; border:1px solid #ccc; font-size:15px;'>
		<option <?= ($auto) ? "checked='checked'" : "" ?> value='1'>Yes</option>
		<option <?= (!$auto) ? "checked='checked'" : "" ?> value='0'>No</option>
	</select> <br clear='both'>
</div>

<div style='float:left; margin-right:20px;'>
	<label>Max Items</label>
	<input value='<?= $max_items; ?>' type='text'  name='max_items' id='max_items' style='margin-top:20px; height:20px; border:1px solid #ccc; font-size:15px;'> <br clear='both'>
</div>


<div style='float:left; margin-right:20px;'>
	<label>Min Items</label>
	<input value='<?= $min_items; ?>' type='text' class='funcky_input' name='min_items' id='min_items' style='margin-top:20px; height:20px; border:1px solid #ccc; font-size:15px;'> <br clear='both'>
</div>
<br clear='both'>
<br />
<strong>Content Types</strong><br /><br />

<br clear='both'>
<?php
	foreach($content_types as $content_type) {
	?>
	<div style='float:left;'>
		<input style='margin-top:0;float:left;' <?= (in_array($content_type->urlid, $saved_content_types)) ? "checked='checked'" : "" ?> type='checkbox' name='content_types[]' value='<?php echo $content_type->urlid; ?>'> 
		<label style='border-top:none !important; width: 110px;float: left; margin-top: -6px; padding-top: 3px;'>
			<?php echo $content_type->name; ?>
		</label>
		
	</div>
	<?php
		}
	?>

</form>

<br clear='both' />
<br />

<span style='float:right;' class='small_button' id="save_zone">Save Zone</span>


<br clear='both'>