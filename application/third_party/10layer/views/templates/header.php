<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"> 
<head> 
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" /> 
	<title>10Layer CMS</title> 
 
	<link rel="shortcut icon" href="/tlresources/file/images/favicon.ico" type="image/x-icon" /> 
	<link rel="icon" href="/tlresources/file/images/favicon.ico" type="image/x-icon" /> 
	
	<link rel="home" href="<?= base_url() ?>" title="Home" />
 
	
	<link rel="stylesheet" href="/tlresources/file/css/style.css" type="text/css" media="screen, projection" charset="utf-8" />
	<link rel="stylesheet" href="/tlresources/file/jquery/jquery-ui-1.8.18.custom/css/smoothness/jquery-ui-1.8.18.custom.css" type="text/css" media="screen, projection" charset="utf-8" />
	
	
	<?php print $this->autoloader->stylesheet(); ?>
	
	<?php
		if (isset($stylesheets) && is_array($stylesheets)) {
			foreach($stylesheets as $stylesheet) {
	?>
		<link rel="stylesheet" href="<?= $stylesheet ?>" type="text/css" media="screen, projection" charset="utf-8" />
	<?php
			}
		}
	?>
	<script type="text/javascript" src="/tlresources/file/jquery/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="/tlresources/file/jquery/jquery.tools.min.js"></script>
	<script type="text/javascript" src="/tlresources/file/jquery/jquery-ui-1.8.18.custom/development-bundle/ui/jquery-ui-1.8.18.custom.js"></script>
	<script type="text/javascript" src="/tlresources/file/jquery/jquery-ui-timepicker-addon.js"></script>

	<script type="text/javascript" src="/tlresources/file/js/heartcode-canvasloader-min-0.9.js"></script>
	
	
	<script type="text/javascript" src="/tlresources/file/js/default.js"></script>
	<script type="text/javascript" src="http://<?= $this->config->item("comet_server") ?>:<?= $this->config->item("comet_port") ?>/static/Orbited.js"></script>
	<script type="text/javascript" src="http://<?= $this->config->item("comet_server") ?>:<?= $this->config->item("comet_port") ?>/static/protocols/stomp/stomp.js"></script>
	<script type="text/javascript" src="/tlresources/file/js/messaging.js"></script>
	<script type="text/javascript">
		document.domain=document.domain;
		Orbited.settings.port = <?= $this->config->item("comet_port") ?>;
		TCPSocket = Orbited.TCPSocket;
	</script>
	<script language="javascript">
		<?php
			if (empty($menu1_active)) {
				$menu1_active="";
			}
			if (empty($menu2_active)) {
				$menu2_active="";
			}
		?>
		$(function() {
			$("#menu2 div").each(function() {
				if ($(this).find("a").first().attr("href")=="<?= base_url().$menu2_active ?>") {
					$(this).addClass("active");
				} else {
					$(this).removeClass("active");
				}
			});
			if ($("#menu2_container").width() > 768) {
				//$("#menu2").scrollable({});
				$("#menu2_scrollr").show();
				
				//$("#menu2_scrolll").show();
				/*$("#menu2_scrolll").button({
					icons: { primary:"ui-icon-triangle-1-w" },
					text: false
				});*/
				$("#menu2_scrollr").button({
					icons: { primary:"ui-icon-triangle-1-s" },
					text: false
				});
				var showMenu=false;
				$("#menu2_scrollr").click(function() {
					if (showMenu) {
						$("#menu2").animate({height:40}, 300, 
							function() {
								showMenu=false;
								$("#menu2_scrollr").button({
								icons: { primary:"ui-icon-triangle-1-s" },
							});
						});
						
					} else {
						$("#menu2").animate({height:80}, 300, 
						function() { 
							showMenu=true;
							$("#menu2_scrollr").button({
								icons: { primary:"ui-icon-triangle-1-n" },
							});
							
						});
					}
				});
			} else {
				
			}
			
			$("#menu2_container div a").each(function() { //Fix ridiculous Chrome bug
				if (!$(this).parent().hasClass("active")) {
					$(this).css("text-decoration", "none");
				}
				
			});
			
			
			$("#menu1 div").each(function() {
				if ($(this).find("a").first().attr("href")=="<?= base_url().$menu1_active ?>") {
					$(this).addClass("active");
				} else {
					$(this).removeClass("active");
				}
			});
			<?php
				$uid=$this->session->userdata("id");

				if (!empty($uid)) {
			?>
			cometInit(<?= $uid ?>, "localhost", <?= $this->config->item("stomp_port") ?>);
			<?php
				}
			?>
		});
	</script>
	<?php print $this->autoloader->javascript(); ?>
</head> 
<body>
	<div id="container">
	<div id="header">
		<div id="menu1">
		<?php
			if (isset($menu1)) {
				$this->load->view_if_exists("menus/main/".$menu1);
			} else {
				$this->load->view_if_exists("menus/main/default");
			}
		?>
		
	</div>
	<div id="menu2_scrolll" class="prev browse left">Left</div>
	<div id="menu2" class="redgradient_nohover smallshadow">
	
		<div id="menu2_container">
		<?php
			if (isset($menu2)) {
				$this->load->view_if_exists("menus/sub/".$menu2);
			} else {
				$this->load->view_by_uri("menus/sub/");
			}
		?>
		</div>
		
	</div>
	<div id="menu2_scrollr" class="next browse right">Click to see more items</div>
	
	<div id="logo">
		<img src="/tlresources/file/images/logo_square_90.png" />
	</div>
	</div>
	
	<div id="wrapper" class="shadow whitegradient">
		<div id="canvasloader-container" class="wrapper">
	
		</div>
		<script type="text/javascript" src="/tlresources/file/js/loader.js"></script>
		<div id="cookiecrumbs">
			
			<?= cookiecrumb() ?>
		</div>
		<?php
			if (!empty($msg)) {
		?>
		<div class="<?php if (!empty($msg["error"])) { print 'error '; } ?>message">
			<?= $msg["msg"] ?>
			<?php
				if (!empty($msg["info"])) {
					if (is_array($msg["info"])) {
						foreach($msg["info"] as $info) {
						?>
						<div class="info">
							<?= $info ?>
						</div>
						<?php
						}
					} else {
					?>
						<div class="info">
							<?= $msg["info"] ?>
						</div>
					<?php
					}
				}
			?>
		</div>
		<?php
			}
		?>