<script language="javascript">
	document.domain=document.domain;
</script>
<br style="clear:both" />
<ul zone_name="<?= $zone->title ?>" id="selected_content_<?= $zone->urlid ?>" class="subsection ajax-content sortable" title="<?= $zone->title ?>" zone_id="<?= $zone->urlid ?>" >
				
			<?php
				if (is_array($published_articles)) {
					foreach($published_articles as $item) {
			?>		
					<li class="sectionrow" id="content=<?= $item->id ?>" urlid="<?= $item->urlid ?>">
						<img style="float: left; margin-right: 5px" src="/workers/picture/display/<?= $item->urlid ?>/cropThumbnailImage/50/40" /><?= $item->title ?> <?= anchor("edit/".$item->content_type_urlid."/".$item->urlid, "Edit", "target='_blank'") ?>
					</li>
			<?php
						}
				}
			?>
				<br style="clear:both" />
			</ul>
			
			<ul id="available_content" class="ajax-content sortable">
		<?php
			foreach($content as $item) {
				//print_r($item);
		?>
			<li class="sectionrow" id="content=<?= $item->id ?>" urlid="<?= $item->id ?>">
				<img style="float: left; margin-right: 5px" src="/workers/picture/display/<?= $item->id ?>/cropThumbnailImage/50/40" /><?= $item->title ?> <?= anchor("edit/".$item->contenttype."/".$item->id, "Edit", "target='_blank'") ?>
			</li>
		<?php
			}
		?>
		</ul>
		<br style="clear:both" />