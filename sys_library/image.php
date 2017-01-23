<?php
$level = intval($_GET['l']);	
$_plugin_list = '
<link rel="stylesheet" href="plugins/mini-upload-form/style.css" />
<script src="plugins/jquery.knob.js"></script>
<script src="plugins/mini-upload-form/jquery.ui.widget.js"></script>
<script src="plugins/mini-upload-form/jquery.iframe-transport.js"></script>
<script src="plugins/mini-upload-form/jquery.fileupload.js"></script>
<script src="sys_library/upload.js"></script>
<script>
var level = '.$level.';
</script>';
?>	
<a href="library.php?l=<?=$level?>"class="btn btn-block btn-default btn-lg" style="width: 50px;" title="<?php echo $language['back']?>" id="btn-back" data-href="library.php?l=<?=$level?>">
	<i class="fa fa-chevron-circle-left"></i>	
</a>	
		<form id="upload" method="post" action="sys_library/upload.php?l=<?=$level?>" enctype="multipart/form-data">
			<div id="drop">
				Drop Here
				<br>
				<a>Browse</a>
				<input type="file" name="upload" multiple accept="image/*" />
			</div>

			<ul>
				<!-- The file uploads will be shown here  "-->
			</ul>

		</form>
