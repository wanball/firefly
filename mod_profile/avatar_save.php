<?php
require ("../inc/config.inc.php");
require ("../inc/connectdb.inc.php");
require ("../inc/function.inc.php");


if(isset($_POST['name'])){

		$data = $_POST['name'];

		$target_dir1 = '../'._UPLOAD_DIR_;
		$target_dir2 = $target_dir1."avatar";
		$target_dir3 = $target_dir2."/user_".$_SESSION['MEMBER_ID'];
		$target_dir4 = $target_dir3."/current";
		$target_dir5 = $target_dir3."/history";
		
		$fileName = time().'_'.rand(11111,99999).'.jpg';
		$targetFile = $target_dir4.'/'.$fileName;
		
		checkDir($target_dir1);
		checkDir($target_dir2);		
		checkDir($target_dir3);
		checkDir($target_dir4);
		checkDir($target_dir5);
		
		list($type, $data) = explode(';', $data);
		list(, $data)      = explode(',', $data);
		$data = base64_decode($data);
		
		file_put_contents($targetFile, $data);
		
		$sql = "SELECT * FROM mod_user WHERE mod_user_id = ".$_SESSION['MEMBER_ID'];
		$stmt = $conn->prepare($sql);
		$stmt->execute();	
		$row = $stmt->fetch();
		
		if($row['mod_user_avatar'] != ''){
			if(file_exists($target_dir4.'/'.$row['mod_user_avatar'])){
				$history_file = $target_dir5.'/'.$row['mod_user_avatar'];
				if (copy($target_dir4.'/'.$row['mod_user_avatar'], $history_file)) {
				    unlink($target_dir4.'/'.$row['mod_user_avatar']);
					chmod($history_file, 0755);
				}				
			}
		}	

			$update = "";
			$update[] = "mod_user_avatar 	= :avatar";
					
			$sql = "UPDATE mod_user SET " . implode(",", $update) . " WHERE mod_user_id = ".$_SESSION['MEMBER_ID'];
			$stmt = $conn->prepare($sql);
				
			$stmt->bindParam(':avatar', $fileName);
					
			$stmt->execute();	

						
		chmod($targetFile, 0755);
		chmod($target_dir5, 0755);
		chmod($target_dir4, 0755);
		chmod($target_dir3, 0755);
		chmod($target_dir2, 0755);
		chmod($target_dir1, 0755);
		
		$_SESSION['MEMBER_AVATAR'] = $targetFile;
	
		logAccess('เปลี่ยนรูปภาพ');

}

CloseDB(); 
?>