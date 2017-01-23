
<!-- jQuery 2.2.3 -->
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script type="text/javascript">    if (!window.jQuery) document.write(unescape('%3Cscript src="plugins/jquery.min.js"%3E%3C/script%3E'))</script>
<!-- jQuery UI 1.11.4 -->
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.0/jquery-ui.min.js"></script>	
<script type="text/javascript">    if (!window.jQuery.ui) document.write(unescape('%3Cscript src="plugins/jquery-ui.min.js"%3E%3C/script%3E'))</script>	
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
  
<?php 
if(isset($real_mobile)){	
	echo $real_mobile;
	echo "\n";
}	
if(isset($menu_id)){
	echo 'var menu_id = '.intval($menu_id).';';
}else{
	echo 'var menu_id = 0;';
}

?>  
</script>
<!-- Bootstrap 3.3.6 -->
<script src="template/bootstrap/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="template/dist/js/app.min.js"></script>
<!-- Sweetalert -->
<link rel="stylesheet" href="plugins/sweetalert/sweetalert.css" />
<script src="plugins/sweetalert/sweetalert.min.js"></script>


<?php
if(isset($_plugin_list)){
	echo $_plugin_list;	
	echo "\n";
}
?>

<script src="plugins/script-addon.js"></script>
