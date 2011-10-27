

<div id="contentlist" class="<?= $contenttype ?>-list boxed wide">
	<div id="listSearchContainer">
		<input type="text" id="listSearch" value="<?php
			if (empty($search)) {
				print "Search...";
			} else {
				print $search;
			}
		?>" />
		<span id="searchResultsCount"><?php
			if (isset($total_rows)) {
				print number_format($total_rows,0,".","&nbsp;")." result";
				if ($total_rows!=1) {
					print "s";
				}
			}
		?></span>
	</div>
	<?= $this->pagination->create_links(); ?>
	<table>
		<tr> 
			<th></th>
			<th>Title</th> 
			<th></th> 
			<th>Version</th> 
			<th></th> 
		</tr>
	<?php
		$class="odd";
		foreach($content as $item) {
			$version=$this->versions->get_version($item->urlid);
			$major_version=$this->versions->get_major_version($item->urlid);
	?>
	<tr class="<?= $class ?> <?= $contenttype ?>-item content-item" id="row_<?= $item->urlid ?>">
		<td>
		<?php
			//if ($this->tlpicture->hasPic($item->urlid,$contenttype)) {
			
		?>
			<img src="/workers/picture/display/<?= $item->urlid ?>/cropThumbnailImage/50/40?<?= $this->versions->get_minor_version($item->urlid) ?>" />
		<?php
			//}
		?>
		</td>
		<td class="content-workflow-<?= $major_version ?>"><a href="<?= base_url()."edit/$contenttype/".$item->urlid ?>"><?php
			if (!empty($item->title)) {
				 print $item->title;
			} else {
				print $item->urlid;
			}
		?></a></td>
		<td></td>
		<td><?= $version ?></td>
		<td class="lock_container">
			<span class="ui-icon ui-icon-locked" <?php 
			if (!$this->checkout->check($item->urlid)) {
			?> style="display:none" <?php
			}
		?>></span>
			</td>
	</tr>
	<?php
			if (empty($class)) {
				$class="odd";
			} else {
				$class="";
			}
		}
	?>
	</table>
	<?= $this->pagination->create_links(); ?>
</div>
<br clear="both" />