
<h2>Collections - <?= $collection->name ?></h2>

<div id="collection_manager">

	<?= $pagination ?>

	<table>
<?php
	$this->load->helper('string');
	foreach($collections as $collection) {
?>
		<tr class='<?= alternator('even', 'odd');?>' > 
			<td>
				<?= anchor("manage/collections/manage_item/".$collection->urlid, $collection->title) ?>
			</td>
		</tr>
<?php
	}
?>
	</table>
</div>