<?php
require ("inc/config.inc.php");
require ("inc/connectdb.inc.php");
require ("inc/function.inc.php");
$language = language_data();

$sql = "SELECT * FROM mod_user WHERE mod_user_staus = 1 AND mod_user_id = ".intval($_COOKIE['remember_user']);				
$stmt = $conn->prepare($sql);
$stmt->execute();		
$count = $stmt->rowCount();
if($count == 0){
	setcookie('remember_user', null, -1, '/');
	header('Location: index.php');
}else{
	$row = $stmt->fetch();
	logAccess('รอ ปลดล็อคสกรีน');
	userOnline($row['mod_user_id']);
	
	//update
	$update = "";
	$update[] = "mod_user_lastdate 	= NOW()";
		
	$sql = "UPDATE mod_user SET  " . implode(",", $update) . " WHERE mod_user_id = ".$row['mod_user_id'];				
	$stmt = $conn->prepare($sql);
	$stmt->execute();		
				
	$path_avatar = _UPLOAD_DIR_.'avatar/user_'.$row['mod_user_id'].'/current';
	if(file_exists($path_avatar.'/'.$row['mod_user_avatar'])){
		$path_avatar = $path_avatar.'/'.$row['mod_user_avatar'];
	}else{
		$path_avatar = 'images/blank_avatar.svg';
	}
				
	$MEMBER_ID = $row['mod_user_id'];
	$MEMBER_NAME = $row['mod_user_name'];
	$MEMBER_AVATAR = $path_avatar;	
}
?>
<!DOCTYPE html>
<html>
<head>
<?php require ("inc/meta.inc.php"); ?>
</head>
<body class="hold-transition lockscreen">
<!-- Automatic element centering -->
<div class="lockscreen-wrapper">
  <div class="lockscreen-logo">
    <b>WEBENGINE</b>
  </div>
  <!-- User name -->
  <div class="lockscreen-name"><?php echo $MEMBER_NAME?></div>

  <!-- START LOCK SCREEN ITEM -->
  <div class="lockscreen-item">
    <!-- lockscreen image -->
    <div class="lockscreen-image">
      <img src="<?php echo $MEMBER_AVATAR?>" alt="User Image">
    </div>
    <!-- /.lockscreen-image -->

    <!-- lockscreen credentials (contains the form) -->
    <form class="lockscreen-credentials" method="post" action="lockscreen-action.php">
      <div class="input-group">
        <input type="password" name="pid" class="form-control" placeholder="<?php echo $language['password']?>" required="">
		<input type="hidden" value="<?php echo $MEMBER_ID?>" name="uid">
        <div class="input-group-btn">
          <button type="submit" class="btn"><i class="fa fa-arrow-right text-muted"></i></button>
        </div>
      </div>
    </form>
    <!-- /.lockscreen credentials -->

  </div>
  <!-- /.lockscreen-item -->
  <div class="help-block text-center">
    <?php echo $language['retrieve_your_session ']?>
  </div>
  <div class="text-center">
    <a href="lockscreen-action.php?other"><?php echo $language['different_user']?></a>
  </div>
  
  

</div>
<!-- /.center -->

<!-- REQUIRED JS SCRIPTS -->

<?php require ("inc/plugins.inc.php"); ?>  
<?php if(isset($_GET['error'])){ ?>
<style> 
.input-group{
	border-top: 1px solid #f00;
	border-right: 1px solid #f00;
	border-bottom: 1px solid #f00;
}	
</style> 
<?php } ?>
</body>
</html>
<?php 
CloseDB();
?>
