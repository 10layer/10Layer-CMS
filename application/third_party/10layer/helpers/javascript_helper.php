<?php
	function link_js($filename) {
		print "<script type='text/javascript' src='$filename'></script>\n";
		return true;
	}
	
	function ckeditor() {
	?>
		<script type='text/javascript' src='/tlresources/file/ckeditor2/ckeditor.js'></script>
		<script type='text/javascript' src='/tlresources/file/ckeditor2/adapters/jquery.js'></script>
		<script type="text/javascript"> 
			$(function() {
				//initCKEditor();
			});
			function initCKEditor() {
				
				var config = { 
					toolbar: [
						['Source','-','Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink','-','Image','-','Maximize']
					],
					skin: 'kama',
					
					filebrowserImageBrowseUrl : '/workers/picturechooser/browse',
					
					filebrowserWindowWidth  : 1000,
					filebrowserWindowHeight : 600,
					on : { 'paste' : function(ev) {
						ev.data.html=parsePaste(ev.data.html);
					} }
					
				};
				$('.richedit').ckeditor(config);
				var editor = $('.richedit').ckeditorGet();		
				//console.log(editor);
			}
			
			function parsePaste(data) {
				data=data.replace(/<meta(?:.|\s)*?>/g,"");
				
				data=data.replace(/<span(?:.|\s)*?>/g,"");
				data=data.replace(/<\/span>/g,"");
				data=data.replace(/<div(?:.|\s)*?>/g,"");
				data=data.replace(/<\/div>/g,"");
				data=data.replace(/<font(?:.|\s)*?>/g,"");
				data=data.replace(/<\/font>/g,"");
				
				data=data.replace(/<iframe(?:.|\s)*?>/g, "");
				data=data.replace(/<\/iframe>/g,"");
				
				data=data.replace(/<fb:like(?:.|\s)*?>/g, "");
				data=data.replace(/<\/fb:like>/g,"");
				
				data=data.replace(/<br><br>/g,"<br>");
				data=data.replace(/<br(?:.|\s)*?>/g,"<p>");
				
				data=data.replace(/<p>$$/g,"");
  				//console.log(data);
				return data;
			}
			
		</script>
	<?php
	}
?>