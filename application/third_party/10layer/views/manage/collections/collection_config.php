<?php
	//$section_title=$section->getData()->title;
	link_js("/tlresources/file/jquery/jquery.form.js");
?>
<script>
	$(function() {
		$('#config_zone').button().click(function(){
			if($('#zone_selector').val() == ''){
				$('#zone_config_container').html("<h3>Select a zone to configure</h3>");
			}else{
				var urlid = $('#zone_selector').val();
				var url = '/manage/collections/configure_zone/'+urlid;
				$.get(url,function(data){
					$('#zone_config_container').html(data);
					$('.small_button').button();
				});
			}

		});
		$('#add_zone').button().click(function(){

			var url = '/manage/collections/configure_zone/';
			$.get(url,function(data){
				$('#zone_config_container').html(data);
				$('.small_button').button();
			});

		});


		$('#zone_config_container').delegate('#save_zone', 'click', function(){
			var data = $('#zone_config_form').serialize();
			var url = '/manage/collections/save_zone/'+$('#section_id').val();
			$.post(url, data, function(response){
				var results = $.parseJSON(response);
				if(results.title != ''){
					var option = "<option value='"+results.urlid+"'>"+results.title+"</option>";
					$('#zone_selector').append(option);
				}
				$('#zone_config_container').html(results.message);
			});

		});

	


		
	});
</script>
<h2>
	Configuring :  <?php echo $item->title; ?>
</h2>
<input id='section_id' value='<?php echo $item->id; ?>' type='hidden'> 
<div id='zone_container'>
	<label>Available zones</label>
	<select id='zone_selector' style='height:30px; border:1px solid #ccc; font-size:15px;'>
		<option value=''>Select zone to configure</option>
		<?php foreach ($zones as $zone) {
	?>
		<option value='<?php echo $zone->urlid;?>'><?php echo $zone->title;?></option>
	<?php } ?>
	</select>
	<span id="config_zone">Configure Zone</span>
	<span style='float:right;' id="add_zone">Add Zone</span>

</div>
<hr>

<div id="zone_config_container" style='height:300px; background:#fff; padding:10px;'>

</div>
