<?php
require ("inc/config.inc.php");
require ("inc/connectdb.inc.php");
require ("inc/function.inc.php");
require ("inc/authentication.inc.php");
$language = language_data();

if(isset($_GET['profile'])){
	$page = 'mod_profile/index.php';
	$menu_id = 0;	
}else if(isset($_GET['m'])){
	$url = base64_decode($_GET['m']);
	$url = explode('|',$url);
	
	$menu_id = intval($url[0]);
	$page = $url[1].'/'.$url[2];
	$moduleKey = $url[3];
	unset($url);
	
	$sql = "SELECT sys_menu_permisson_staus FROM sys_menu LEFT JOIN sys_menu_permisson ON sys_menu_id = sys_menu_permisson_menu_id WHERE sys_menu_id = ".$menu_id;
	$stmt_permisson = $conn->prepare($sql);
	$stmt_permisson->execute();
	$row_permisson = $stmt_permisson->fetch();
	$permisson = $row_permisson['sys_menu_permisson_staus'];
	
	if($permisson == 0){
		header('Location: index.php');
	}
	
}else{
	$page = 'mod_dashboard/index.php';
	$menu_id = 0;
}	
$_plugin_list = '';

//mobile
require ("plugins/Mobile_Detect.php");
$detect = new Mobile_Detect;
if ( $detect->isMobile() ) { 	
    $real_mobile = 'var mobile = true;'; 
}else{
    $real_mobile = 'var mobile = false;'; 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php require ("inc/meta.inc.php"); ?>
</head>

<body class="hold-transition skin-green sidebar-mini">
<div class="wrapper">

  <?php /* Main Header */?>
  <header class="main-header">
  <?php require ("inc/header.inc.php"); ?>
  </header>
  <?php /* Left side column. contains the logo and sidebar */?>
  <aside class="main-sidebar">
  <?php require ("inc/main-sidebar.inc.php"); ?>
  </aside>

  <?php /* Content Wrapper. Contains page content */?>
  <div class="content-wrapper">
  <?php require ($page); ?>
  </div>
  <?php /* /.content-wrapper */?>

  <?php /* Main Footer */?>
<?php require ("inc/footer.inc.php"); ?>     

  <?php /* Control Sidebar */?>
  <?php require ("inc/setting-sidebar.inc.php"); ?>
</div>
<?php /* ./wrapper */?>

<?php /* REQUIRED JS SCRIPTS */?>

<?php require ("inc/plugins.inc.php"); ?>     
</body>
</html>
<?php CloseDB(); ?>