<?php
require ("inc/config.inc.php");
require ("inc/connectdb.inc.php");
require ("inc/function.inc.php");
$language = language_data();

if(!isset($_SESSION['MEMBER_ID'])){
	echo '<script>
		window.close();
	</script>';
	exit();
}else{
	$target_dir1 = _UPLOAD_DIR_;
	$target_dir2 = $target_dir1."filemanager";
	checkDir($target_dir1);
	checkDir($target_dir2);
	$moduleKey = 'gallery';
	
	$level = 0;
	if(isset($_GET['l'])){
		$level = intval($_GET['l']);
	}	
}
if(isset($_GET['image'])){
	$path_inc = "sys_library/image.php";	
}else if(isset($_GET['preview'])){
	$path_inc = "sys_library/preview.php";	
	$preview = intval($_GET['preview']);

}else{
	$path_inc = "sys_library/dir.php";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require ( "inc/meta.inc.php"); ?>
    <link rel="stylesheet" href="plugins/style-library.css" />
</head>

<body data-level="<?=$level?>">
    <div class="wrapper">
        <section class="content">
		<?php require ($path_inc); ?>
        </section>
    </div>
    
<?php require ("inc/plugins.inc.php"); ?>
</body>
</html>
<?php
CloseDB();
chmod($target_dir2, 0755);
chmod($target_dir1, 0755);		
?>