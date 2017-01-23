<?php
require ("inc/config.inc.php");
require ("inc/connectdb.inc.php");
require ("inc/function.inc.php");


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