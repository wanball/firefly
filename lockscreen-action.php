<?php
require ("inc/config.inc.php");
require ("inc/connectdb.inc.php");
require ("inc/function.inc.php");

if(isset($_GET['other'])){
	setcookie('remember_user', null, -1, '/');
	header('Location: index.php');	
}else{
	$sql = "SELECT * FROM mod_user WHERE mod_user_staus = 1 AND mod_user_id = ".intval($_POST['uid']);				
	$stmt = $conn->prepare($sql);
	$stmt->execute();		
	$count = $stmt->rowCount();
	if($count == 0){
		header('Location: lockscreen.php?error');	
	}else{
		$row = $stmt->fetch();
		$my_pass = crypt($_POST['pid'], '$6$rounds=5000$usesomesillystringforsalt$');
		
		if (strcmp($row['mod_user_pass'], $my_pass) !== 0) {
			logAccess('รหัสผ่านไม่ถูกต้อง');
			header('Location: lockscreen.php?error');	
		}else{		
			logAccess('ล็อคอินสำเร็จ');
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
				
			$_SESSION['MEMBER_ID'] = $row['mod_user_id'];
			$_SESSION['MEMBER_NAME'] = $row['mod_user_name'];
			$_SESSION['MEMBER_AVATAR'] = $path_avatar;
			$_SESSION['MEMBER_POSITION'] = $row['mod_user_position'];
			$_SESSION['MEMBER_CREATE'] = $row['mod_user_createdate'];
				
			header('Location: home.php');
		}	
	}	
}

CloseDB();
?>