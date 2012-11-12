
<table>
	<tr>
		<th>
			Title
		</th>
		<th>
			Creation date
		</th>
	</tr>
<?php
	foreach($items as $item) {
?>
	<tr>
		<td>
			<?= anchor("manage/collections/section/".$collectionurlid."/".$item->urlid, $item->title) ?>
		</td>
		<td>
			<?php echo $item->timestamp;?>
		</td>
	</tr>
	
<?php
	}
?>
</table>
