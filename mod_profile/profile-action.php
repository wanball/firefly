<?php
require ("../inc/config.inc.php");
require ("../inc/connectdb.inc.php");
require ("../inc/function.inc.php");

$action = $_POST['action'];
$pid = intval($_SESSION['MEMBER_ID']); 	

if($pid == 0){
	//change other user	
	$pid = intval($_POST['MID']); 	
}

if($pid > 0){
	if($action == 'validate_pass'){
		$my_pass = crypt($_POST['pass'], '$6$rounds=5000$usesomesillystringforsalt$');
		
		$sql = 'SELECT mod_user_pass FROM mod_user WHERE mod_user_id = '.$pid.' AND mod_user_staus < 2';
		$stmt = $conn->prepare($sql);
		$stmt->execute();
		$row = $stmt->fetch();
		if (strcmp($row['mod_user_pass'], $my_pass) !== 0) {
			echo 0;
		}else{
			echo 1;
		}
		
	}else if($action == 'change_profile'){
			//update
			$update = "";
			
			$update[] = "mod_user_name 		= :name";
			$update[] = "mod_user_position 	= :position";
			$update[] = "mod_user_phone 	= :phone";
			$update[] = "mod_user_other 	= :other";
			$update[] = "mod_user_updatedate = NOW()";
			
			$sql = "UPDATE mod_user SET  " . implode(",", $update) . " WHERE mod_user_id = ".$pid;				
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(':name', $_POST['Profile_Name']);
			$stmt->bindParam(':position', $_POST['Profile_Postition']);
			$stmt->bindParam(':phone', $_POST['Profile_Phone']);
			$stmt->bindParam(':other', $_POST['Profile_Other']);
			
			$stmt->execute();			
			logAccess('แก้ไขโปรไฟล์');
			$staus = '&update';
		
			header('Location: ../home.php?profile'.$staus);		
					
	}else if($action == 'change_pass'){
		$staus = '';
		$my_pass1 = crypt($_POST['password1'], '$6$rounds=5000$usesomesillystringforsalt$');
		$my_pass2 = crypt($_POST['password2'], '$6$rounds=5000$usesomesillystringforsalt$');
		$my_pass3 = crypt($_POST['password3'], '$6$rounds=5000$usesomesillystringforsalt$');

		$sql = 'SELECT mod_user_pass FROM mod_user WHERE mod_user_id = '.$pid.' AND mod_user_staus < 2';
		$stmt = $conn->prepare($sql);
		$stmt->execute();
		$row = $stmt->fetch();
		if (strcmp($row['mod_user_pass'], $my_pass1) !== 0) {
			$staus = '&error1';
		}else if (strcmp($my_pass1, $my_pass2) == 0) {
			$staus = '&error2';
		}else if (strcmp($my_pass2, $my_pass3) !== 0) {
			$staus = '&error3';
		}else{
			//update
			$update = "";
			$update[] = "mod_user_pass 	= :pass";
			$update[] = "mod_user_updatedate = NOW()";
			$update[] = "mod_user_updatepass = NOW()";
			
			$sql = "UPDATE mod_user SET  " . implode(",", $update) . " WHERE mod_user_id = ".$pid;				
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(':pass', $my_pass3);
			
			$stmt->execute();			
			logAccess('เปลี่ยนรหัสผ่านสำเร็จ');
			$staus = '&success';
		}
		header('Location: ../home.php?profile'.$staus);			
	}
}
CloseDB();
?>