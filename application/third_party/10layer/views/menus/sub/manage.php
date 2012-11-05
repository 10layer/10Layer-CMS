<div class="menuitem"><?= anchor("manage/users/my_account","My Account") ?></div>
<div class="menuitem"><?= anchor("manage/users/accounts","User Accounts") ?></div>
<!--<div class="menuitem"><?= anchor("manage/users/roles","User Roles") ?></div>-->
<div class="menuitem"><?= anchor("manage/users/permissions","User Permissions") ?></div>
<!--<div class="menuitem"><?= anchor("manage/sections","Sections") ?></div>-->
<?php
	$permissions = $this->session->userdata('permissions');
	$access = false;
	foreach($permissions as $permission){
		if($permission == 1 || $permission == 2){
			$access = true;
		}
	}
	if($access){
?>
	<div class="menuitem"><?= anchor("manage/collections","Collections") ?></div>
<?php
	}
?>
<!--<div class="menuitem"><?= anchor("manage/users/groups","User Groups") ?></div>-->
<!--<div class="menuitem"><?= anchor("manage/workflow/designer","Workflow") ?></div>-->
<!--<div class="menuitem"><?= anchor("manage/servers","Servers") ?></div>-->
<!--<div class="menuitem"><?= anchor("manage/files","Files") ?></div>-->
<!--<div class="menuitem"><?= anchor("manage/performance","Performance") ?></div>-->
<div class="menuitem"><?= anchor("manage/healthchecks","Health Checks") ?></div>