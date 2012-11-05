
<h2>Collections - <?= $collection->name ?></h2>

<div id="collection_manager">
	<ul>
<?php
	//print_r($collections);
	foreach($collections as $collection) {
?>
		<li>
			<?php 
				echo anchor("manage/collections/manage_item/".$collection->urlid, $collection->title);
				if(isset($collection->children) && sizeof($collection->children) > 0){
					?>
					<ol>
						<?php
							foreach($collection->children as $item){
								?>
									<li><?= anchor("manage/collections/manage_item/".$item->urlid, $item->title) ?></li>
								<?php
							}
						?>
					</ol>
					<?php
				}
			?>
		</li>
<?php
	}
?>
	</ul>
</div>