<?php
require ("inc/config.inc.php");
require ("inc/connectdb.inc.php");
require ("inc/function.inc.php");

        $target_dir1 = _UPLOAD_DIR_;
       	$target_dir2 = $target_dir1."temp";
        checkDir($target_dir1);
        checkDir($target_dir2); 

  		$now   = time();
		$day = 60 * 60 * 24 * 2;  // 2 days
		$dh  = opendir($target_dir2);
		while (false !== ($filename = readdir($dh))) {
			$file = $target_dir2.'/'.$filename;
			if (is_file($file)){
				if ($now - filemtime($file) >= $day){ 
					unlink($file);	
				}	
			}
		}

	logAccess('ออกจากระบบ');
	
	$sql = "DELETE FROM mod_user_online WHERE mod_user_online_pid =".intval($_SESSION['MEMBER_ID']);				
	$stmt = $conn->prepare($sql);
	$stmt->execute();	
	
	$_SESSION['MEMBER_ID'] = '';
	$_SESSION['MEMBER_NAME'] = '';
	$_SESSION['MEMBER_AVATAR'] = '';

	if(isset($_COOKIE['remember_user'])){
		setcookie('remember_user', null, -1, '/');
	}				
	
	session_destroy();
	CloseDB();		
	header('Location: index.php');
?>